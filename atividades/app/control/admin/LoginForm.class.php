<?php
/**
 * LoginForm Registration
 * @author  <your name here>
 */
class LoginForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();

        $table = new TTable;
        $table->width = '100%';
        // creates the form
        $this->form = new TForm('form_User');
        $this->form->class = 'tform';
        $this->form->style = 'width: 450px;margin:auto; margin-top:120px;';

        // add the notebook inside the form
        $this->form->add($table);

        // create the form fields
        $login = new TEntry('login');
        $password = new TPassword('password');

        // define the sizes
        $login->setSize(320, 40);
        $password->setSize(320, 40);

        $login->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $password->style = 'height:35px;margin-bottom: 15px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';

        $row=$table->addRow();
        $row->addCell( new TLabel('Login') )->colspan = 2;
        $row->class='tformtitle';

        $login->placeholder = _t('User');
        $password->placeholder = _t('Password');

        $user = '<span style="float:left;width:35px;margin-left:45px;height:35px;" class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>';
        $locker = '<span style="float:left;width:35px;margin-left:45px;height:35px;" class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>';

        $container1 = new TElement('div');
        $container1->add($user);
        $container1->add($login);

        $container2 = new TElement('div');
        $container2->add($locker);
        $container2->add($password);

        $row=$table->addRow();
        $row->addCell($container1)->colspan = 2;

        // add a row for the field password
        $row=$table->addRow();        
        $row->addCell($container2)->colspan = 2;
        
        // create an action button (save)
        $save_button=new TButton('save');
        // define the button action
        $save_button->setAction(new TAction(array($this, 'onLogin')), _t('Log in'));
        $save_button->class = 'btn btn-success btn-defualt';
        $save_button->style = 'margin-left:32px;width:355px;height:40px;border-radius:6px;font-size:18px';

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $save_button );
        $cell->colspan = 2;

        $this->form->setFields(array($login, $password, $save_button));

        // add the form to the page
        parent::add($this->form);
    }

    /**
     * Autenticates the User
     */
    function onLogin()
    {
        try
        {
            TTransaction::open('permission');
            $data = $this->form->getData('StdClass');
            $this->form->validate();
            $user = SystemUser::autenticate( $data->login, $data->password );
            if ($user)
            {
                $programs = $user->getPrograms();
                $programs['LoginForm'] = TRUE;
                
                TSession::setValue('logged', TRUE);
                TSession::setValue('login', $data->login);
                TSession::setValue('username', $user->name);
                TSession::setValue('frontpage', '');
                TSession::setValue('programs',$programs);
                
                $frontpage = $user->frontpage;
                
                if ($frontpage instanceof SystemProgram AND $frontpage->controller)
                {
                    TApplication::gotoPage($frontpage->controller); // reload
                    TSession::setValue('frontpage', $frontpage->controller);
                }
                else
                {
                    TApplication::gotoPage('EmptyPage'); // reload
                    TSession::setValue('frontpage', 'EmptyPage');
                }
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            TSession::setValue('logged', FALSE);
            TTransaction::rollback();
        }
    }
    
    /**
     * Logout
     */
    function onLogout()
    {
        TSession::freeSession();
        TApplication::gotoPage('LoginForm', '');
    }
}
?>