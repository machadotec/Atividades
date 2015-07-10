<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class SystemUserForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        // creates the form
        $this->form = new TForm('form_System_user');
        $this->form->class = 'tform';

        // creates the table container
        $table = new TTable;
        $table->style = 'width: 100%';
        
        $table->addRowSet( new TLabel(_t('User')), '', '','' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        $frame_groups = new TFrame(NULL, 280);
        $frame_groups->setLegend(_t('Groups'));
        $frame_groups->style .= ';margin: 4px';
        $frame_programs = new TFrame(NULL, 280);
        $frame_programs->setLegend(_t('Programs'));
        $frame_programs->style .= ';margin: 15px';


        // create the form fields
        $id                  = new TEntry('id');
        $name                = new TEntry('name');
        $login               = new TEntry('login');
        $password            = new TPassword('password');
        $repassword          = new TPassword('repassword');
        $email               = new TEntry('email');
        $multifield_programs = new TMultiField('programs');
        $program_id          = new TDBSeekButton('program_id', 'permission', 'form_System_user', 'SystemProgram', 'name', 'programs_id', 'programs_name');
        $program_name        = new TEntry('program_name');
        $groups              = new TDBCheckGroup('groups','permission','SystemGroup','id','name');
        $frontpage_id        = new TDBSeekButton('frontpage_id', 'permission', 'form_System_user', 'SystemProgram', 'name', 'frontpage_id', 'frontpage_name');
        $frontpage_name      = new TEntry('frontpage_name');
        
        $scroll = new TScroll;
        $scroll->setSize(290, 230);
        $scroll->add( $groups );
        $frame_groups->add( $scroll );
        $frame_programs->add( $multifield_programs );

        // define the sizes
        $id->setSize(100);
        $name->setSize(200);
        $login->setSize(150);
        $password->setSize(150);
        $email->setSize(200);
        $frontpage_id->setSize(100);
        $multifield_programs->setHeight(140);
        
        // outros
        $id->setEditable(false);
        $program_name->setEditable(false);
        $frontpage_name->setEditable(false);
        
        // validations
        $name->addValidation(_t('Name'), new TRequiredValidator);
        $login->addValidation('Login', new TRequiredValidator);
        $email->addValidation('Email', new TEmailValidator);
        $program_id->setSize(50);
        $program_name->setSize(200);
        
        // configuracoes multifield
        $multifield_programs->setClass('SystemProgram');
        $multifield_programs->addField('id', 'ID',  $program_id, 60);
        $multifield_programs->addField('name',_t('Name'), $program_name, 250);
        $multifield_programs->setOrientation('horizontal');
        
        // add a row for the field id
        $table->addRowSet(new TLabel('ID:'),                 $id,           new TLabel(_t('Name').': '), $name);
        $table->addRowSet(new TLabel(_t('Login').': ' ),     $login,        new TLabel(_t('Email').': '), $email);
        $table->addRowSet(new TLabel(_t('Password').': '),   $password,     new TLabel(_t('Password confirmation').': '), $repassword);
        $table->addRowSet(new TLabel(_t('Front page').': '), $frontpage_id, new TLabel(_t('Page name') . ': '), $frontpage_name);
        
        $row=$table->addRow();
        $cell = $row->addCell($frame_groups);
        $cell->colspan = 2;
        
        $cell = $row->addCell($frame_programs);
        $cell->colspan = 2;

        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('fa:plus-square green');
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('SystemUserList','onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        // define the form fields
        $this->form->setFields(array($id,$name,$login,$password,$repassword,$multifield_programs,$frontpage_id, $frontpage_name, $groups,$email,$save_button,$new_button,$list_button));
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $buttons );
        $cell->colspan = 4;

        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'SystemUserList'));
        $container->addRow()->addCell($this->form);

        // add the form to the page
        parent::add($container);
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open('permission');
            
            // get the form data into an active record System_user
            $object = $this->form->getData('SystemUser');
            
            // form validation
            $this->form->validate();
            
            $senha = $object->password;
            
            if( ! $object->id )
            {
                if( ! $object->password )
                    throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Password')));
            }
            
            if( $object->password )
            {
                if( $object->password != $object->repassword )
                    throw new Exception(_t('The passwords do not match'));
                
                $object->password = md5($object->password);
            }
            else
                unset($object->password);
            
            $object->store(); // stores the object
            $object->clearParts();
            
            if( $object->groups )
            {
                foreach( $object->groups as $group )
                {
                    $object->addSystemUserGroup( new SystemGroup($group) );
                }
            }
            
            if( $object->programs )
            {
                foreach( $object->programs as $program )
                {
                    $object->addSystemUserProgram( $program );
                }
            }
            
            $object->password = $senha;
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            // reload the listing
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
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'permission'
                TTransaction::open('permission');
                
                // instantiates object System_user
                $object = new SystemUser($key);
                
                unset($object->password);
                
                $groups = array();
                
                if( $groups_db = $object->getSystemUserGroups() )
                {
                    foreach( $groups_db as $grup )
                    {
                        $groups[] = $grup->id;
                    }
                }
                
                $object->programs = $object->getSystemUserPrograms();
                
                $object->groups = $groups;
                
                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
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
?>