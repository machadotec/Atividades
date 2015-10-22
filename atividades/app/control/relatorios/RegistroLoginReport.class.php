<?php
/**
 * RegistroLoginReport Report
 * @author  <your name here>
 */
class RegistroLoginReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_RegistroLogin_report');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 500px';
        
        // creates the table container
        $table = new TTable;
        $table->width = '100%';
        
        // add the table inside the form
        $this->form->add($table);

        // define the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Registro de acessos ao sistema') )->colspan = 2;
        
        // create the form fields
        //$name                           = new TEntry('name');
        $name                           = new TDBCombo('login', 'atividade', 'SystemUser', 'login', 'name');        
        $data_ponto                     = new TDate('data_ponto');
        $data_ponto->setMask('dd/mm/yyyy');
        $output_type                    = new TRadioGroup('output_type');

        // define the sizes        
        $data_ponto->setSize(100);
        $output_type->setSize(100);

        // add one row for each form field
        $table->addRowSet( new TLabel('Nome:'), $name );
        $table->addRowSet( new TLabel('Data:'), $data_ponto );
        $table->addRowSet( new TLabel('Saida:'), $output_type );
        
        $this->form->setFields(array($name,$data_ponto,$output_type));

        $output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));;
        $output_type->setValue('html');
        $output_type->setLayout('horizontal');
        
        $generate_button = TButton::create('generate', array($this, 'onGenerate'), _t('Generate'), 'ico_apply.png');
        $this->form->addField($generate_button);
        
        // add a row for the form action
        $table->addRowSet( $generate_button, '' )->class = 'tformaction';
        
        parent::add($this->form);
    }

    /**
     * method onGenerate()
     * Executed whenever the user clicks at the generate button
     */
    function onGenerate()
    {
        try
        {
            // open a transaction with database 'atividade'
            TTransaction::open('atividade');
            $string = new StringsUtil;
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $repository = new TRepository('RegistroLogin');
            $criteria   = new TCriteria;
            
            if ($formdata->login)
            {
                $criteria->add(new TFilter('login', '=', "{$formdata->login}"));
            }
            if ($formdata->data_ponto)
            {
                $criteria->add(new TFilter('data_ponto', '>=', "{$string->formatDate($formdata->data_ponto)}"));
            }

            $newparam['order'] = 'data_ponto';
            $newparam['direction'] = 'desc';
            $criteria->setProperties($newparam); // order, offset

            $objects = $repository->load($criteria);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $widths = array(20,200,50,80,80);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new TTableWriterRTF($widths);
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#ffffff', '#6B6B6B');
                $tr->addStyle('datap', 'Arial', '10', '',    '#000000', '#E5E5E5');
                $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Times', '16', 'B',  '#4A5590', '#C0D3E9');
                $tr->addStyle('footer', 'Times', '12', 'BI', '#4A5590', '#C0D3E9');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('Registro de acessos ao sistema', 'center', 'header', 5);
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Seq.', 'center', 'title');
                $tr->addCell('Nome', 'center', 'title');
                $tr->addCell('Data', 'center', 'title');
                $tr->addCell('Hora inicial', 'center', 'title');
                $tr->addCell('Hora final', 'center', 'title');

                
                // controls the background filling
                $colour= FALSE;
                $seq   =1;
                // data rows
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($seq++, 'right', $style);
                    $tr->addCell($object->name, 'left', $style);
                    $tr->addCell($string->formatDateBR($object->data_ponto), 'center', $style);
                    $tr->addCell($object->hora_inicial, 'center', $style);
                    $tr->addCell($object->hora_final, 'center', $style);
                    
                    $colour = !$colour;
                }
                
                // footer row
                $tr->addRow();
                $tr->addCell(date('d/m/Y h:i:s'), 'center', 'footer', 5);
                // stores the file
                if (!file_exists("app/output/RegistroLogin.{$format}") OR is_writable("app/output/RegistroLogin.{$format}"))
                {
                    $tr->save("app/output/RegistroLogin.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/RegistroLogin.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/RegistroLogin.{$format}");
                
                // shows the success message
                new TMessage('info', 'Relatorio gerado. Por favor, habilite popups no navegador (somente para web).');
            }
            else
            {
                new TMessage('error', 'No records found');
            }
    
            // fill the form with the active record data
            $this->form->setData($formdata);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
