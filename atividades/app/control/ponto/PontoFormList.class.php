<?php
/**
 * PontoFormList Registration
 * @author  <your name here>
 */
class PontoFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Ponto');
        $this->form->class = 'tform'; // CSS class
        $this->form->setFormTitle('Ponto'); // define the form title
        
        // create the form fields
        $id                             = new THidden('id');
        $data_ponto                     = new TDate('data_ponto');
        $data_ponto->setMask('dd/mm/yyyy');
        
        $change_data_action = new TAction(array($this, 'onChangeDataAction'));
        $data_ponto->setExitAction($change_data_action);
        
        $hora_entrada                   = new THidden('hora_entrada');
        $qtde_horas                     = new TCombo('qtde_horas');
        $qtde_minutos                   = new TCombo('qtde_minutos');
        $colaborador_id                 = new THidden('colaborador_id');
        TTransaction::open('atividade');
        $logado = Pessoa::retornaUsuario();
        TTransaction::close();
        $colaborador_id->setValue($logado->pessoa_codigo);
        $colaborador_nome               = new TEntry('colaborador_nome');
        $colaborador_nome->setEditable(FALSE);
        $colaborador_nome->setValue($logado->pessoa_nome);

        // cria combos de horas e minutos
        $combo_horas = array();
        for($i = 8; $i <= 18; $i++)
        {
             $combo_horas[$i] = str_pad($i, 2, 0, STR_PAD_LEFT) ;
        }
        $qtde_horas->addItems($combo_horas);
        $qtde_horas->setValue(8);
        $qtde_horas->setSize(60);
        $qtde_horas->setDefaultOption(FALSE);
        
        $combo_minutos = array();
        for($i = 0; $i <= 59; $i++)
        {
             $combo_minutos[$i] = str_pad($i, 2, 0, STR_PAD_LEFT) ;     
        }
        $qtde_minutos->addItems($combo_minutos);
        $qtde_minutos->setValue(0);
        $qtde_minutos->setSize(60);
        $qtde_minutos->setDefaultOption(FALSE);
        
        // validations
        $data_ponto->addValidation('Data', new TRequiredValidator);
        
        // add the fields
        $this->form->addQuickField('Colaborador', $colaborador_nome,  200);
        $this->form->addQuickField('Data', $data_ponto,  100);
        $this->form->addQuickFields('Hora entrada', array($qtde_horas, $qtde_minutos));
        $this->form->addQuickField('', $hora_entrada,  200);
        $this->form->addQuickField('', $colaborador_id,  100);
        $this->form->addQuickField('', $id,  100);
      
        // create the form actions
        $this->form->addQuickAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'ico_new.png');
        $this->form->addQuickAction('Excluir',  new TAction(array($this, 'onDelete')), 'ico_delete.png');
        
        TButton::disableField('form_Ponto', 'salvar');
        TButton::disableField('form_Ponto', 'excluir');
        
        // creates a DataGrid
        $this->datagrid = new TQuickGrid;
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $data_ponto = $this->datagrid->addQuickColumn('Data', 'data_ponto', 'left', 100);
        $hora_entrada = $this->datagrid->addQuickColumn('Hora', 'hora_entrada', 'left', 80);
        $intervalo = $this->datagrid->addQuickColumn('Atividades', 'intervalo', 'right', 80);
        
        // transformers
        $intervalo->setTransformer(array($this, 'retornaIntervalo'));
        
        // create the datagrid actions
        $edit_action   = new TDataGridAction(array($this, 'onEdit'));
        $delete_action = new TDataGridAction(array($this, 'onDelete'));
        
        // add the actions to the datagrid
        $this->datagrid->addQuickAction(_t('Edit'), $edit_action, 'id', 'ico_edit.png');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create the page container
        $container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        parent::add($container);
    }
    
    public static function onChangeDataAction($param)
    {
        
        $string = new StringsUtil;
        $data1 = date('Y-m-d');
        $data2 = $string->formatDate($param['data_ponto']);
        
        if(strtotime($data2) > strtotime($data1))
        {
	         new TMessage('error', 'Data do ponto maior que a data atual!');
	         TButton::disableField('form_Ponto', 'salvar');
        }    
        else
        {
        
            $dataLimite = date('Y-m-d', strtotime("-125 days"));
            
            if(strtotime($dataLimite) > strtotime($data2))
            {
                 new TMessage('error', 'Data do ponto menor que data limite permitida ['.$string->formatDateBR($dataLimite).']' );
	             TButton::disableField('form_Ponto', 'salvar');
            }
            else
            {
                
                try
                    {
                        
                        TTransaction::open('atividade');
                        
                        $ultimaData = Ponto::retornaUltimaData($param['colaborador_id']);
                        
                        if(strtotime($ultimaData) >= strtotime($data2))
                        {
                             new TMessage('error', 'Existe data posterior ou igual cadastrada!');
	                         TButton::disableField('form_Ponto', 'salvar');
                        }
                        else
                        {
                            TButton::enableField('form_Ponto', 'salvar');
                        }
                        
                        TTransaction::close();
                        
                    }
                catch(Exception $e)
                {
                    new TMessage('error', '<b>Error</b> ' . $e->getMessage());
                }
                
            }
            
        }
                
    }

    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        $string = new StringsUtil;
        try
        {
            // open a transaction with database 'atividade'
            TTransaction::open('atividade');

            $logado = Pessoa::retornaUsuario();            
                       
            // creates a repository for Ponto
            $repository = new TRepository('Ponto');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('colaborador_id', '=', $logado->pessoa_codigo));
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_ponto';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('Ponto_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('Ponto_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $object->data_ponto ? $object->data_ponto = $string->formatDateBR($object->data_ponto) : null;
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
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
            // get the parameter $key
            $key=$param['id'];
        
            TTransaction::open('atividade'); // open the transaction
            $object = new Ponto($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object
            TTransaction::close(); // close the transaction
            
            $this->onReload( $param ); // reload the listing
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        $string = new StringsUtil;
        try
        {
            TTransaction::open('atividade'); // open a transaction with database
            
            // get the form data into an active record Ponto
            $object = $this->form->getData('Ponto');
          
            $object->data_ponto ? $object->data_ponto = $string->formatDate($object->data_ponto) : null;
           
            $object->hora_entrada = $object->qtde_horas.':'.$object->qtde_minutos.':00';
                        
            $this->form->validate(); // form validation
            $object->store(); // stores the object
            
            $object->data_ponto ? $object->data_ponto = $string->formatDateBR($object->data_ponto) : null;
             
            $this->form->setData($object); // fill the form with the active record data
            TTransaction::close(); // close the transaction
            
            $action = new TAction(array($this, 'onReload'));
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $action); // success message
            $this->onReload(); // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        $string = new StringsUtil;
        try
        {
            TTransaction::open('atividade'); // open a transaction with the database
            if (isset($param['key']))
            {
                
                $key=$param['key']; // get the parameter $key

                $object = new Ponto($key); // instantiates the Active Record
             
                $ultimoPonto = Ponto::retornaUltimoPonto($object->colaborador_id);
 
                $intervalo = Ponto::horaPreenchidas($object->data_ponto, $object->colaborador_id);
                
                if(!$intervalo)
                {
                    
                    if($ultimoPonto == $key)
                    {
                        TButton::enableField('form_Ponto', 'salvar');
                        TButton::enableField('form_Ponto', 'excluir');   
                    }
                    
                }
            
                $object->data_ponto ? $object->data_ponto = $string->formatDateBR($object->data_ponto) : null;
                
                $horario = explode(':', $object->hora_entrada);
                
                $object->qtde_horas = $horario[0];
                $object->qtde_minutos = $horario[1];
                
                $this->form->setData($object); // fill the form with the active record data

            }
            else
            {
                $object = new Ponto();
                $object->colaborador_id   = $param['colaborador_id'];
                $object->colaborador_nome = $param['colaborador_nome'];
                $this->form->setData($object);
            }
            TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page e seu conteÃºdo
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
    
    public function retornaIntervalo($campo, $object, $row)
    {
         
         try
         {
             TTransaction::open('atividade');
             
             $intervalo = Ponto::horaPreenchidas($object->data_ponto, $object->colaborador_id);
             
             TTransaction::close();
             
             return $intervalo;
         }
         catch(Exception $e)
         {
             new TMessage('error', '<b>Error</b> ' . $e->getMessage());
         }
         
    }
    
}
