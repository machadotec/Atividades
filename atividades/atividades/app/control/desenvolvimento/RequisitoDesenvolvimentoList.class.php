<?php
/**
 * RequisitoDesenvolvimentoList Listing
 * @author  <your name here>
 */
class RequisitoDesenvolvimentoList extends TPage
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
        $this->form = new TForm('form_search_RequisitoDesenvolvimento');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Cadastro de DRs') )->colspan = 2;
     
        // create the form fields
        $id                             = new TEntry('ticket_id');
        $titulo                         = new TEntry('titulo');
        $data_cadastro                  = new TDate('data_cadastro');
        $data_cadastro->setMask('dd/mm/yyyy');

        // define the sizes
        $id->setSize(50);
        $titulo->setSize(200);
        $data_cadastro->setSize(100);

        // add one row for each form field
        $table->addRowSet( new TLabel('Ticket:'), $id );
        $table->addRowSet( new TLabel('Título:'), $titulo );
        $table->addRowSet( new TLabel('Data:'), $data_cadastro );

        $this->form->setFields(array($id,$titulo,$data_cadastro));

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('RequisitoDesenvolvimento_filter_data') );
        
        // create two action buttons to the form
        $find_button = TButton::create('find', array($this, 'onSearch'), _t('Find'), 'ico_find.png');
        $clean_button  = TButton::create('clean',  array($this, 'onClean'), 'Limpar', 'ico_close.png');
        
        $this->form->addField($find_button);
        $this->form->addField($clean_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($find_button);
        $buttons_box->add($clean_button);
          
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $id   = new TDataGridColumn('ticket_id', 'ID', 'right', 20);
        $data_cadastro   = new TDataGridColumn('data_cadastro', 'Data', 'left', 80);
        $titulo   = new TDataGridColumn('titulo', 'Título', 'left', 300);
        $ticket_id   = new TDataGridColumn('ticket->titulo', 'Ticket', 'right', 300);
        
        $data_cadastro->setTransformer(array('StringsUtil', 'formatDateBR'));

        // add the columns to the DataGrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($data_cadastro);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($ticket_id);

        // creates the datagrid column actions
        $order_id= new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $id->setAction($order_id);

        $order_titulo= new TAction(array($this, 'onReload'));
        $order_titulo->setParameter('order', 'titulo');
        $titulo->setAction($order_titulo);

        $order_data_cadastro= new TAction(array($this, 'onReload'));
        $order_data_cadastro->setParameter('order', 'data_cadastro');
        $data_cadastro->setAction($order_data_cadastro);

        $order_ticket_id= new TAction(array($this, 'onReload'));
        $order_ticket_id->setParameter('order', 'ticket->titulo');
        $ticket_id->setAction($order_ticket_id);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array('RequisitoDesenvolvimentoForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
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
            $object = new RequisitoDesenvolvimento($key); // instantiates the Active Record
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
        
        // clear session filters
        TSession::setValue('RequisitoDesenvolvimentoList_filter_id',   NULL);
        TSession::setValue('RequisitoDesenvolvimentoList_filter_titulo',   NULL);
        TSession::setValue('RequisitoDesenvolvimentoList_filter_data_cadastro',   NULL);

        if (isset($data->ticket_id) AND ($data->ticket_id)) {
            $filter = new TFilter('ticket_id', '=', "$data->ticket_id"); // create the filter
            TSession::setValue('RequisitoDesenvolvimentoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->titulo) AND ($data->titulo)) {
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%"); // create the filter
            TSession::setValue('RequisitoDesenvolvimentoList_filter_titulo',   $filter); // stores the filter in the session
        }


        if (isset($data->data_cadastro) AND ($data->data_cadastro)) {
            $filter = new TFilter('data_cadastro', '>=', "$data->data_cadastro"); // create the filter
            TSession::setValue('RequisitoDesenvolvimentoList_filter_data_cadastro',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('RequisitoDesenvolvimento_filter_data', $data);
        
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
            
            // creates a repository for RequisitoDesenvolvimento
            $repository = new TRepository('RequisitoDesenvolvimento');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            $newparam = $param; // define new parameters
            if (isset($newparam['order']) AND $newparam['order'] == 'ticket->titulo')
            {
                $newparam['order'] = '(select titulo from ticket where ticket_id = id)';
            }
            
            // default order
            if (empty($newparam['order']))
            {
                $newparam['order'] = 'titulo';
                $newparam['direction'] = 'asc';
            }
            $criteria->setProperties($newparam); // order, offset
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('RequisitoDesenvolvimentoList_filter_id')) {
                $criteria->add(TSession::getValue('RequisitoDesenvolvimentoList_filter_id')); // add the session filter
            }

            if (TSession::getValue('RequisitoDesenvolvimentoList_filter_titulo')) {
                $criteria->add(TSession::getValue('RequisitoDesenvolvimentoList_filter_titulo')); // add the session filter
            }

            if (TSession::getValue('RequisitoDesenvolvimentoList_filter_data_cadastro')) {
                $criteria->add(TSession::getValue('RequisitoDesenvolvimentoList_filter_data_cadastro')); // add the session filter
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
            $object = new RequisitoDesenvolvimento($key, FALSE); // instantiates the Active Record
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
        TSession::setValue('RequisitoDesenvolvimentoList_filter_id',   NULL);
        TSession::setValue('RequisitoDesenvolvimentoList_filter_titulo',   NULL);
        TSession::setValue('RequisitoDesenvolvimentoList_filter_data_cadastro',   NULL);
         
        $this->form->clear();

        $this->onReload( );
         
    }
    
}
