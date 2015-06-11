<?php
/**
 * AtividadeList Listing
 * @author  <your name here>
 */
class AtividadeList extends TPage
{
    private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_search_Atividade');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Atividade') )->colspan = 2;
        
        // create the form fields
        $id                             = new THidden('id');
        $data_atividade                 = new TDate('data_atividade');
        $data_atividade->setMask('dd/mm/yyyy');
        $criteria = new TCriteria;
        $criteria->add(new TFilter("origem", "=", 1));
        $criteria->add(new TFilter("codigo_cadastro_origem", "=", 100));
        $colaborador_id                 = new TDBCombo('colaborador_id', 'tecbiz', 'Pessoa', 'pessoa_codigo', 'pessoa_nome', 'pessoa_nome', $criteria);
        $tipo_atividade_id              = new TDBCombo('tipo_atividade_id', 'atividade', 'TipoAtividade', 'id', 'nome', 'nome');
        $ticket_id                      = new TDBCombo('ticket_id', 'atividade', 'Ticket', 'id', 'titulo', 'id');
        
        $criteria = new TCriteria;
        $newparam['order'] = 'pessoa_nome';
        $newparam['direction'] = 'asc';
        $criteria->setProperties($newparam); // order, offset
        $solicitante_id                 = new TDBSeekButton('solicitante_id', 'tecbiz','form_search_Atividade','Pessoa','pessoa_nome','solicitante_id', 'solicitante_nome',$criteria);
        $solicitante_nome               = new TEntry('solicitante_nome');
        $solicitante_nome->setEditable(FALSE);
        
        // define the sizes
        $id->setSize(50);
        $data_atividade->setSize(100);
        $colaborador_id->setSize(265);
        $tipo_atividade_id->setSize(265);
        $ticket_id->setSize(265);
        $solicitante_id->setSize(40);
        
        // add one row for each form field
        $table->addRowSet( new TLabel('Solicitante:'), array($solicitante_id, $solicitante_nome) );
        $table->addRowSet( new TLabel('Colaborador:'), $colaborador_id );
        $table->addRowSet( new TLabel('Data:'), $data_atividade );
        $table->addRowSet( new TLabel('Atividade:'), $tipo_atividade_id );
        $table->addRowSet( new TLabel('Ticket:'), $ticket_id );
        $table->addRowSet( new TLabel(''), $id );
        
        $this->form->setFields(array($id,$data_atividade,$colaborador_id,$tipo_atividade_id,$ticket_id, $solicitante_id, $solicitante_nome));
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Atividade_filter_data') );
        
        // create two action buttons to the form
        $find_button = TButton::create('find', array($this, 'onSearch'), _t('Find'), 'ico_find.png');
        $new_button  = TButton::create('new',  array('AtividadeForm', 'onEdit'), _t('New'), 'ico_new.png');
        $clean_button  = TButton::create('clean',  array($this, 'onClean'), 'Limpar', 'ico_close.png');
        
        $this->form->addField($find_button);
        $this->form->addField($new_button);
        $this->form->addField($clean_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($find_button);
        $buttons_box->add($new_button);
        $buttons_box->add($clean_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
                
        // creates the datagrid columns
        $data_atividade      = new TDataGridColumn('data_atividade', 'Data', 'right', 75);
        $hora_inicio         = new TDataGridColumn('hora_inicio', 'Inicio', 'right', 80);
        $hora_fim            = new TDataGridColumn('hora_fim', 'Fim', 'right', 80);
        $hora_qte            = new TDataGridColumn('hora_qte', 'Qtde', 'right', 80);
        $colaborador_id      = new TDataGridColumn('colaborador_id', 'Colaborador', 'right', 200);
        $tipo_atividade_id   = new TDataGridColumn('tipo_atividade->nome', 'Atividade', 'right', 100); //get_tipo_atividade()->nome
        $ticket_id           = new TDataGridColumn('ticket->titulo', 'Ticket', 'right', 200); // get_ticket()->titulo
        
        // transformers
        $colaborador_id->setTransformer(array($this, 'retornaPessoa'));
        
        $hora_qte->setTransformer(array($this, 'calculaDiferenca'));
        $data_atividade->setTransformer(array('StringsUtil', 'formatDateBR'));
        //exemplo de uso de classe para jogar funcoes
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($data_atividade);
        $this->datagrid->addColumn($hora_inicio);
        $this->datagrid->addColumn($hora_fim);
        $this->datagrid->addColumn($hora_qte);
        $this->datagrid->addColumn($colaborador_id);
        $this->datagrid->addColumn($tipo_atividade_id);
        $this->datagrid->addColumn($ticket_id);
        
        // creates the datagrid column actions  
        $order_data_atividade= new TAction(array($this, 'onReload'));
        $order_data_atividade->setParameter('order', 'data_atividade');
        $data_atividade->setAction($order_data_atividade);
        
        $order_colaborador_id= new TAction(array($this, 'onReload'));
        $order_colaborador_id->setParameter('order', 'colaborador_id');
        $colaborador_id->setAction($order_colaborador_id);
        
        $order_tipo_atividade_id= new TAction(array($this, 'onReload'));
        $order_tipo_atividade_id->setParameter('order', 'tipo_atividade->nome');
        $tipo_atividade_id->setAction($order_tipo_atividade_id);
        
        $order_ticket_id= new TAction(array($this, 'onReload'));
        $order_ticket_id->setParameter('order', 'ticket->titulo');
        $ticket_id->setAction($order_ticket_id);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array('AtividadeForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
       
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create the page container
        $container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        
        $container->style = 'width: 100%;max-width: 1200px;';
        $this->datagrid->style = '  width: 100%;  max-width: 1200px;';
        
        parent::add($container);
    }
    
    /**
     * method onInlineEdit()
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content
     */
    function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('atividade'); // open a transaction with database
            $object = new Atividade($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        $string = new StringsUtil;
        
        // clear session filters
        TSession::setValue('AtividadeList_filter_id',   NULL);
        TSession::setValue('AtividadeList_filter_data_atividade',   NULL);
        TSession::setValue('AtividadeList_filter_solicitante_id',   NULL);
        TSession::setValue('AtividadeList_filter_colaborador_id',   NULL);
        TSession::setValue('AtividadeList_filter_tipo_atividade_id',   NULL);
        TSession::setValue('AtividadeList_filter_ticket_id',   NULL);
        
        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('AtividadeList_filter_id',   $filter); // stores the filter in the session
        }
        
        if (isset($data->data_atividade) AND ($data->data_atividade)) {
            $data->data_atividade = $string->formatDate($data->data_atividade);
            $filter = new TFilter('data_atividade', '>=', "$data->data_atividade"); // create the filter
            $data->data_atividade = $string->formatDateBR($data->data_atividade);
            TSession::setValue('AtividadeList_filter_data_atividade',   $filter); // stores the filter in the session
        }
        
        if (isset($data->solicitante_id) AND ($data->solicitante_id)) {
            
             try
             {
                TTransaction::open('atividade');
                $tickets = Ticket::getTicketsSolicitante($data->solicitante_id);
                TTransaction::close();
             }
             catch(Exception $e)
             {
                new TMessage('error', $e->getMessage());
             }
            
            
            $filter = new TFilter('ticket_id', 'IN', ($tickets)); // create the filter
            TSession::setValue('AtividadeList_filter_solicitante_id',   $filter); // stores the filter in the session
            
        }
        
        if (isset($data->colaborador_id) AND ($data->colaborador_id)) {
            $filter = new TFilter('colaborador_id', '=', "$data->colaborador_id"); // create the filter
            TSession::setValue('AtividadeList_filter_colaborador_id',   $filter); // stores the filter in the session
        }
        
        if (isset($data->tipo_atividade_id) AND ($data->tipo_atividade_id)) {
            $filter = new TFilter('tipo_atividade_id', '=', "$data->tipo_atividade_id"); // create the filter
            TSession::setValue('AtividadeList_filter_tipo_atividade_id',   $filter); // stores the filter in the session
        }
        
        if (isset($data->ticket_id) AND ($data->ticket_id)) {
            $filter = new TFilter('ticket_id', '=', "$data->ticket_id"); // create the filter
            TSession::setValue('AtividadeList_filter_ticket_id',   $filter); // stores the filter in the session
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Atividade_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        
        try
        {
            // open a transaction with database 'atividade'
            TTransaction::open('atividade');
                      
            // creates a repository for Atividade
            $repository = new TRepository('Atividade');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            $newparam = $param; // define new parameters
            if (isset($newparam['order']) AND $newparam['order'] == 'ticket->titulo')
            {
                $newparam['order'] = '(select titulo from ticket where ticket_id = id)';
            }
            
            if (isset($newparam['order']) AND $newparam['order'] == 'tipo_atividade->nome')
            {
                $newparam['order'] = '(select nome from tipo_atividade where tipo_atividade_id = id)';
            }
                        
            // default order
            $newparam['direction'] = 'asc';
            if (empty($newparam['order']))
            {
                $newparam['order'] = 'data_atividade desc, id ';
                $newparam['direction'] = 'desc';
            }
            
            if($newparam['order'] == 'data_atividade')
            {
                $newparam['order'] = 'data_atividade desc, id ';
                $newparam['direction'] = 'desc';
            }
            
            $criteria->setProperties($newparam); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('AtividadeList_filter_id')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_id')); // add the session filter
            }
            
            if (TSession::getValue('AtividadeList_filter_data_atividade')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_data_atividade')); // add the session filter
            }
            
            if (TSession::getValue('AtividadeList_filter_solicitante_id')) {
                
                $criteria->add(TSession::getValue('AtividadeList_filter_solicitante_id')); // add the session filter
            }
            
            if (TSession::getValue('AtividadeList_filter_colaborador_id')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_colaborador_id')); // add the session filter
            }
            
            if (TSession::getValue('AtividadeList_filter_tipo_atividade_id')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_tipo_atividade_id')); // add the session filter
            }
            
            if (TSession::getValue('AtividadeList_filter_ticket_id')) {
                $criteria->add(TSession::getValue('AtividadeList_filter_ticket_id')); // add the session filter
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            
             try
             {
                TTransaction::open('tecbiz');
             
                if ($objects)
                {
                    // iterate the collection of active records
                    foreach ($objects as $object)
                    {
                        // add the object inside the datagrid
                        $this->datagrid->addItem($object);
                    }
                }
                
                TTransaction::close();
             }
             catch(Exception $e)
             {
                 new TMessage('error', '<b>Error</b> ' . $e->getMessage());
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
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
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
            $key=$param['key']; // get the parameter $key
            TTransaction::open('atividade'); // open a transaction with database
            $object = new Atividade($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
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
     * method show()
     * Shows the page
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
    
    public function onClean()
    {
         
        // clear session filters
        TSession::setValue('AtividadeList_filter_id',   '');
        TSession::setValue('AtividadeList_filter_data_atividade',   '');
        TSession::setValue('AtividadeList_filter_solicitante_id',   '');
        TSession::setValue('AtividadeList_filter_colaborador_id',   '');
        TSession::setValue('AtividadeList_filter_tipo_atividade_id',   '');
        TSession::setValue('AtividadeList_filter_ticket_id',   '');
         
        $this->form->clear();

        $this->onReload( );
         
    }
    
    public function retornaPessoa($campo, $object, $row)
    {
        
        $cliente = new Pessoa($object->colaborador_id);
        $campo = $cliente->pessoa_nome;
            
        return $campo;
         
    }
    
    public function calculaDiferenca($campo, $object, $row)
    {
        // criar metodo de preenchimento de horas
        $HoraEntrada = new DateTime($object->hora_inicio);
        $HoraSaida   = new DateTime($object->hora_fim);
        $campo = $HoraSaida->diff($HoraEntrada)->format('%H:%I:%S');
                    
        return $campo;     
    }
    
}