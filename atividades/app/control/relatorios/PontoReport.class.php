<?php
/**
 * PontoReport Report
 * @author  <your name here>
 */
class PontoReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
    private $string;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_Ponto_report');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 500px';
        $this->string = new StringsUtil;
        
        // creates the table container
        $table = new TTable;
        $table->width = '100%';
        
        // add the table inside the form
        $this->form->add($table);

        // define the form title
        $row = $table->addRow();
        $row->class = 'tformtitle';
        $cell = $row->addCell(new TLabel('Indicador de produtividade por colaborador'));
        $cell->colspan=2;
        
        // create the form fields
        $mes_atividade                  = new TCombo('mes_atividade');
        $mes_atividade->addItems($this->string->array_meses());
        $mes_atividade->setDefaultOption(FALSE);
        $mes_atividade->setValue(date('m'));
        $mes_atividade->setSize(250);
        
        $output_type                    = new TRadioGroup('output_type');
        $output_type->setSize(100);

        // validations
        $output_type->addValidation('Saida', new TRequiredValidator);

        // add one row for each form field
        $table->addRowSet( new TLabel('Mês referencia:'), $mes_atividade );
        $table->addRowSet( $label_output_type = new TLabel('Saida:'), $output_type );
        $label_output_type->setFontColor('#FF0000');

        $this->form->setFields(array($mes_atividade,$output_type));
        
        $output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));;
        $output_type->setValue('html');
        $output_type->setLayout('horizontal');
        
        $generate_button = TButton::create('generate', array($this, 'onGenerate'), _t('Generate'), 'ico_apply.png');
        $this->form->addField($generate_button);
        
        // add a row for the form action
        $table->addRowSet( $generate_button, '' )->class = 'tformaction';
        
        parent::add($this->form);
    }

    function calculaPercentualProdutividade($user, $dia)
    {
        $ponto = $this->retornaPonto($user, $dia);
        $horas = Ponto::horaPreenchidas($dia, $user);        
        if($ponto)
        {            
            $perc = round($this->string->time_to_sec($horas) * 100 / $this->string->time_to_sec($ponto) );
            
        }
        $campo = $perc.'%';        
        if($perc < 50)
        {
            $campo = "<span style='color:#FF0000'><b>".round($perc, 2)."%</b></span>";
        }
        return $campo;             
    }
    
    function calculaPercentualProdutividadeTotalColaborador($totalAtividades, $totalPonto)
    {
        $perc = round($totalAtividades * 100 / $totalPonto);
        $campo = "<b>".$perc."%</b>"; 
        if($perc < 50)
        {
            $campo = "<span style='color:#FF0000'><b>".round($perc, 2)."%</b></span>";
        }
        return $campo;             
    }
    
    function retornaPonto($user, $dia)
    {
    
        $ponto = Ponto::retornaTempoPonto($user, $dia);
                        
        $total = new DateTime($ponto);
        $almoco = new DateTime('01:00:00');
        $limite = new DateTime('06:00:00');
        if($total > $limite)
        {
            $ponto = $total->diff($almoco)->format('%H:%I:%S');
        }
    
        return $ponto;
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
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $dataInicial = date('Y').'-'.str_pad($formdata->mes_atividade, 2, 0, STR_PAD_LEFT).'-01';
            $dataFinal   = date('Y').'-'.str_pad($formdata->mes_atividade, 2, 0, STR_PAD_LEFT).'-'.cal_days_in_month(CAL_GREGORIAN, $formdata->mes_atividade, date('Y'));
            
            $dias = Atividade::retornaDiasAtividades($dataInicial, $dataFinal);
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter("origem", "=", 1));
            $criteria->add(new TFilter("codigo_cadastro_origem", "=", 100));
            $criteria->add(new TFilter("ativo", "=", 1));
            $criteria->add(new TFilter("pessoa_codigo", "IN", "(SELECT colaborador_id FROM atividade WHERE data_atividade between '{$dataInicial}' and '{$dataFinal}')"));
            $newparam['order'] = 'pessoa_nome';
            $newparam['direction'] = 'asc';
            $criteria->setProperties($newparam); // order, offset
               
            $repo = new TRepository('Pessoa');
            $pessoas = $repo->load($criteria);
            
            $format  = $formdata->output_type;
            
            if ($dias)
            {
                $widths = array(50);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        $break = '<br />';
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        $break = '';
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new TTableWriterRTF($widths);
                        $break = '<br />';
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#ffffff', '#6B6B6B');
                $tr->addStyle('datap', 'Arial', '10', '',    '#000000', '#E5E5E5');
                $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                $tr->addStyle('totais', 'Arial', '10', '',    '#000000', '#C0D3E9');
                $tr->addStyle('header', 'Times', '16', 'B',  '#4A5590', '#C0D3E9');
                $tr->addStyle('footer', 'Times', '12', 'BI', '#4A5590', '#C0D3E9');
                
                // add a header row
                $tr->addRow();
                
                $tr->addCell(utf8_decode('Indicador de produtividade de: ').strtoupper($this->string->array_meses()[$formdata->mes_atividade]), 'center', 'header', 33);
                
                $tr->addRow();
                $tr->addCell('Seq.', 'center', 'title');
                $tr->addCell('Nome', 'center', 'title');    
                foreach($dias as $dia)
                {
                    $tr->addCell(substr($dia['dias'], -2), 'center', 'title');
                    $arrayDias[substr($dia['dias'], -2)] = $dia['dias']; 
                }
                $tr->addCell('TOTAL', 'center', 'title');
                                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                $count = 1;
                
                foreach ($pessoas as $pessoa)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($count, 'left', $style);
                    $tr->addCell(utf8_decode($pessoa->pessoa_nome), 'left', $style, 33);
                    
                    $tr->addRow();
                    $tr->addCell('', 'left', $style);
                    $tr->addCell('Ponto', 'left', $style);
                    $totalPonto = null;
                    foreach($arrayDias as $dia)
                    {
                        $ponto = $this->retornaPonto($pessoa->pessoa_codigo, $dia);
                        $totalPonto += $this->string->time_to_sec($ponto);
                        $tr->addCell(substr($ponto, 0, -3), 'center', $style);
                        $arrayPonto[$dia] += $this->string->time_to_sec($ponto);                     
                    }
                    $arrayPonto['total'] += $totalPonto;
                    $tr->addCell(substr($this->string->sec_to_time($totalPonto), 0, -3), 'center', $style);
                    
                    $tr->addRow();
                    $tr->addCell('', 'left', $style);
                    $tr->addCell('Atividades', 'left', $style);
                    $totalAtividades = null;
                    foreach($arrayDias as $dia)
                    {
                        $horas = Ponto::horaPreenchidas($dia, $pessoa->pessoa_codigo);
                        if(!$horas)
                        {
                            $horas = '00:00:00';
                        }
                        
                        $x = $this->retornaPonto($pessoa->pessoa_codigo, $dia);
               
                        if($this->string->time_to_sec($x) > 0){
                               $totalAtividades += $this->string->time_to_sec($horas);
                               $arrayAtividades[$dia] += $this->string->time_to_sec($horas); 
                        }
                        
                        $tr->addCell(substr($horas, 0, -3), 'center', $style);

                    }
                    $arrayAtividades['total'] += $totalAtividades;
                    $tr->addCell(substr($this->string->sec_to_time($totalAtividades), 0, -3), 'center', $style);
                    
                    $tr->addRow();
                    $tr->addCell('', 'left', $style);
                    $tr->addCell('Produtividade', 'left', $style);
                    foreach($arrayDias as $dia)
                    {
                        $campo = $this->calculaPercentualProdutividade($pessoa->pessoa_codigo, $dia);
                        $tr->addCell($campo, 'center', $style); 
                    }

                    $tr->addCell($this->calculaPercentualProdutividadeTotalColaborador($totalAtividades, $totalPonto), 'center', $style);
                    
                    $tr->addRow();
                    $tr->addCell($break, 'left', $style, 33);
                    
                    $count++;
                    $colour = !$colour;
                }
                
                // totais row
                $tr->addRow();
                $tr->addCell($count, 'left', 'totais');
                $tr->addCell('TOTAIS', 'left', 'totais', 33);
                
                $tr->addRow();
                $tr->addCell('', 'left', 'totais');
                $tr->addCell('Ponto', 'left', 'totais');
                foreach($arrayDias as $dia)
                {
                    $tr->addCell(substr($this->string->sec_to_time($arrayPonto[$dia]), 0, -3), 'center', 'totais');                     
                }
                $tr->addCell(substr($this->string->sec_to_time($arrayPonto['total']), 0, -3), 'center', 'totais');                     
                
                $tr->addRow();
                $tr->addCell('', 'left', 'totais');
                $tr->addCell('Atividades', 'left', 'totais');
                foreach($arrayDias as $dia)
                {
                    $tr->addCell(substr($this->string->sec_to_time($arrayAtividades[$dia]), 0, -3), 'center', 'totais');                     
                }
                $tr->addCell(substr($this->string->sec_to_time($arrayAtividades['total']), 0, -3), 'center', 'totais');                     
                
                $tr->addRow();
                $tr->addCell('', 'left', 'totais');
                $tr->addCell('Produtividade', 'left', 'totais');
                foreach($arrayDias as $dia)
                {                    
                    $tr->addCell($this->calculaPercentualProdutividadeTotalColaborador($arrayAtividades[$dia], $arrayPonto[$dia]), 'center', 'totais');
                }
                $tr->addCell($this->calculaPercentualProdutividadeTotalColaborador($arrayAtividades['total'], $arrayPonto['total']), 'center', 'totais');
                
                // footer row
                $tr->addRow();
                
                $var = rand(0, 1000);
                
                $tr->addCell(date('d/m/Y H:i:s'), 'center', 'footer', 33);
                // stores the file
                if (!file_exists("app/output/Ponto_{$var}.{$format}") OR is_writable("app/output/Ponto_{$var}.{$format}"))
                {
                    $tr->save("app/output/Ponto_{$var}.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Ponto_{$var}.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Ponto_{$var}.{$format}");
                
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
