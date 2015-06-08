<?php
class CommonPage extends TPage
{
    
    protected $form; // form
        
        
    public function __construct()
    {
       parent::__construct();
        
        $this->form = new TForm('form_Teste');
    
        // creates the notebook
        $notebook = new TNotebook(400,250);
        
        // creates the containers for each notebook page
        $page1 = new TTable;
        $page2 = new TPanel(370,180);
        $page3 = new TTable;
        
        $this->form->add($notebook);
        
        // adds two pages in the notebook
        $notebook->appendPage('Basic data', $page1);
        $notebook->appendPage('Other data', $page2);
        $notebook->appendPage('Other note', $page3);
        
        // create the form fields
        $field1 = new TEntry('field1');
        $field2 = new TEntry('field2');
        $field3 = new TEntry('field3');
        $field4 = new TEntry('field4');
        $field5 = new TEntry('field5');
        
        $field6 = new TEntry('field6');
        $field7 = new TEntry('field7');
        $field8 = new TEntry('field8');
        $field9 = new TEntry('field9');
        $field10= new TEntry('field10');
        
        // change the size for some fields
        $field1->setSize(100);
        $field2->setSize(80);
        $field3->setSize(150);
        
        $field6->setSize(80);
        $field7->setSize(80);
        $field8->setSize(80);
        $field9->setSize(80);
        $field10->setSize(80);
        
        ## fields for the page 1 ##
        
        // add a row for a label
        $row=$page1->addRow();
        $cell=$row->addCell(new TLabel('<b>Table Layout</b>'));
        $cell->valign = 'top';
        $cell->colspan=2;
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field1:'));
        $row->addCell($field1);
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field2:'));
        $cell = $row->addCell($field2);
        $cell->colspan=3;
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field3:'));
        $cell = $row->addCell($field3);
        $cell->colspan=3;
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field4:'));
        $row->addCell($field4);
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field5:'));
        $row->addCell($field5);
        
        
        ## fields for the page 2 ##
        
        $page2->put(new TLabel('<b>Panel Layout</b>'), 4, 4);
        $page2->put(new TLabel('Field6'),  20,  30);
        $page2->put(new TLabel('Field7'),  50,  60);
        $page2->put(new TLabel('Field8'),  80,  90);
        $page2->put(new TLabel('Field9'), 110, 120);
        $page2->put(new TLabel('Field10'),140, 150);
        
        $page2->put($field6, 120,  30);
        $page2->put($field7, 150,  60);
        $page2->put($field8, 180,  90);
        $page2->put($field9, 210, 120);
        $page2->put($field10,240, 150);
        
        
        ## fields for the page 3 ##
        
        // creates the notebook
        $subnotebook = new TNotebook(250, 160);
        $subnotebook->appendPage('new page1', new TLabel('test1'));
        $subnotebook->appendPage('new page2', new TText('test2'));
        
        $row = $page3->addRow();
        $row->addCell($subnotebook);
        
                // create the form actions
        $save_button   = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $delete_button = TButton::create('delete', array($this, 'onDelete'), _t('Delete'), 'ico_delete.png');
        $new_button    = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
//        $list_button   = TButton::create('list', array('FilmeList', 'onReload'), _t('List'), 'ico_datagrid.png');
        
        $this->form->addField($save_button);
        $this->form->addField($delete_button);
        $this->form->addField($new_button);
//        $this->form->addField($list_button);
        
        $subtable = new TTable;
        $row = $subtable->addRow();
        $row->addCell($save_button);
        $row->addCell($delete_button);
        $row->addCell($new_button);
//        $row->addCell($list_button);
         
        $vbox = new TVBox;
        $vbox->add($notebook);
        $vbox->add($subtable);    
  
  TButton::disableField('form_Teste', 'save');
            
        parent::add($vbox);
        
    }
    
        /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            TTransaction::open('filme'); // open a transaction
            
            // get the form data into an active record Filme
            $object = $this->form->getData('Filme');          
            
            $object->dataVisualizacao = $this->formatDate($object->dataVisualizacao);
            
            if($object->genero_list)
            {
                foreach($object->genero_list as $genero_id)
                {
                    $object->addGenero(new Genero($genero_id));
                }
            }
        
            $this->form->validate(); // form validation
            $object->store(); // stores the object
            $object->dataVisualizacao = $this->formatDateBR($object->dataVisualizacao);
            $this->form->setData($object); // keep form data
            
           // $this->upload($object->imagem, 'teste');
            TTransaction::close(); // close the transaction
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Erros encontrados:</b> <br />' . $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
        
        if ($object instanceof Filme)
        {
            $source_file   = 'tmp/'.$object->photo_path;
            $target_file   = 'app/images/foto/' . $object->id.'.png';
          
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            
            if (file_exists($source_file) AND ($finfo->file($source_file) == 'image/png' OR $finfo->file($source_file) == 'image/jpeg'))
            {
                // move to the target directory
                rename($source_file, $target_file);
               
            } 
            else
            {
                 if($object->photo_path)
                 {
                     new TMessage('error', '<b>Arquivo de imagem inálido</b> <br />'); // shows the exception error message
                 }                 
            }                    
        }
        
    }
    
        /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key=$param['key'];  // get the parameter $key
                TTransaction::open('filme'); // open a transaction
                $object = new Filme($key); // instantiates the Active Record
                
                if(!$object->dataVisualizacao)
                {
                    $object->dataVisualizacao = $object->datavisualizacao;
                }
                
                $object->dataVisualizacao = $this->formatDateBR($object->dataVisualizacao);
                
                $generos = $object->getGeneros();
                $genero_list = array();
                if($generos)
                {
                    foreach ($generos as $genero)
                    {
                        $genero_list[] = $genero->id;
                    }
                }
                $object->genero_list = $genero_list;
                
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $object = new StdClass;
                $object->nota = 1;
                
                $this->form->clear();
                $this->form->setData($object); // fill the form
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
       /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
     /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            $key=$param['id']; // get the parameter $key
            TTransaction::open('filme'); // open a transaction with database
            $object = new Filme($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $object = new StdClass;
            $object->nota = 1;
            $this->form->clear();
            $this->form->setData($object); // fill the form
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public function formatDate($date)
    {        
        $dt = explode('/', $date);
        $retorno = $dt[2].'-'.$dt[1].'-'.$dt[0];
        return $retorno;
    }
    public function formatDateBR($date)
    {        
        $dt = explode('-', $date);
        $retorno = $dt[2].'/'.$dt[1].'/'.$dt[0];
        return $retorno;
    }    
    
}
?>