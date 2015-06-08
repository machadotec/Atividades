<?php
/**
 * System_user Active Record
 * @author  <your-name-here>
 */
class SystemUser extends TRecord
{
    const TABLENAME = 'system_user';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $frontpage;
    private $system_user_groups = array();
    private $system_user_programs = array();

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('login');
        parent::addAttribute('password');
        parent::addAttribute('email');
        parent::addAttribute('frontpage_id');
    }

    /**
     * Returns the frontpage name
     */
    public function get_frontpage_name()
    {
        // loads the associated object
        if (empty($this->frontpage))
            $this->frontpage = new SystemProgram($this->frontpage_id);
    
        // returns the associated object
        return $this->frontpage->name;
    }
    
    /**
     * Returns the frontpage
     */
    public function get_frontpage()
    {
        // loads the associated object
        if (empty($this->frontpage))
            $this->frontpage = new SystemProgram($this->frontpage_id);
    
        // returns the associated object
        return $this->frontpage;
    }
    
    /**
     * Method addSystem_user_group
     * Add a System_user_group to the System_user
     * @param $object Instance of System_group
     */
    public function addSystemUserGroup(SystemGroup $object)
    {
        $this->system_user_groups[] = $object;
    }
    
    /**
     * Method getSystem_user_groups
     * Return the System_user' System_user_group's
     * @return Collection of System_user_group
     */
    public function getSystemUserGroups()
    {
        return $this->system_user_groups;
    }
    
    /**
     * Method addSystem_user_program
     * Add a System_user_program to the System_user
     * @param $object Instance of System_program
     */
    public function addSystemUserProgram(SystemProgram $object)
    {
        $this->system_user_programs[] = $object;
    }
    
    /**
     * Method getSystem_user_programs
     * Return the System_user' System_user_program's
     * @return Collection of System_user_program
     */
    public function getSystemUserPrograms()
    {
        return $this->system_user_programs;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->system_user_groups = array();
        $this->system_user_programs = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        // load the related System_user_group objects
        $repository = new TRepository('SystemUserGroup');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', $id));
        $system_user_system_user_groups = $repository->load($criteria);
        if ($system_user_system_user_groups)
        {
            foreach ($system_user_system_user_groups as $system_user_system_user_group)
            {
                $system_user_group = new SystemGroup( $system_user_system_user_group->system_group_id );
                $this->addSystemUserGroup($system_user_group);
            }
        }
    
        // load the related System_user_program objects
        $repository = new TRepository('SystemUserProgram');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', $id));
        $system_user_system_user_programs = $repository->load($criteria);
        if ($system_user_system_user_programs)
        {
            foreach ($system_user_system_user_programs as $system_user_system_user_program)
            {
                $system_user_program = new SystemProgram( $system_user_system_user_program->system_program_id );
                $this->addSystemUserProgram($system_user_program);
            }
        }
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related System_userSystem_user_group objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', $this->id));
        $repository = new TRepository('SystemUserGroup');
        $repository->delete($criteria);
        // store the related System_userSystem_user_group objects
        if ($this->system_user_groups)
        {
            foreach ($this->system_user_groups as $system_user_group)
            {
                $system_user_system_user_group = new SystemUserGroup;
                $system_user_system_user_group->system_group_id = $system_user_group->id;
                $system_user_system_user_group->system_user_id = $this->id;
                $system_user_system_user_group->store();
            }
        }
        // delete the related System_userSystem_user_program objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', $this->id));
        $repository = new TRepository('SystemUserProgram');
        $repository->delete($criteria);
        // store the related System_userSystem_user_program objects
        if ($this->system_user_programs)
        {
            foreach ($this->system_user_programs as $system_user_program)
            {
                $system_user_system_user_program = new SystemUserProgram;
                $system_user_system_user_program->system_program_id = $system_user_program->id;
                $system_user_system_user_program->system_user_id = $this->id;
                $system_user_system_user_program->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related System_userSystem_user_group objects
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('SystemUserGroup');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the related System_userSystem_user_program objects
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('SystemUserProgram');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }
    
    /**
     * Autenticate the user
     * @param $login String with user login
     * @param $password String with user password
     * @returns TRUE if the password matches, otherwise throw Exception
     */
    public static function autenticate($login, $password)
    {
        $user = self::newFromLogin($login);
        
        if ($user instanceof SystemUser)
        {
            if (isset( $user->password ) AND ($user->password == md5($password)) )
            {
                return $user;
            }
            else
            {
                throw new Exception(_t('Wrong password'));
            }
        }
        else
        {
            throw new Exception(_t('User not found'));
        }
    }
    
    /**
     * Returns a SystemUser object based on its login
     * @param $login String with user login
     */
    static public function newFromLogin($login)
    {
        $repos = new TRepository('SystemUser');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('login', '=', $login));
        $objects = $repos->load($criteria);
        if (isset($objects[0]))
        {
            return $objects[0];
        }
    }
    
    /**
     * Return the programs the user has permission to run
     */
    public function getPrograms()
    {
        $programs = array();
        
        foreach( $this->getSystemUserGroups() as $group )
        {
            foreach( $group->getSystemPrograms() as $prog )
            {
                $programs[$prog->controller] = true;
            }
        }
                
        foreach( $this->getSystemUserPrograms() as $prog )
        {
            $programs[$prog->controller] = true;
        }
        
        return $programs;
    }
    
    /**
     * Check if the user is within a group
     */
    public function checkInGroup( SystemGroup $group )
    {
        $user_groups = array();
        foreach( $this->getSystemUserGroups() as $group )
        {
            $user_groups[] = $group->id;
        }
    
        return in_array($group->id, $user_groups);
    }
}
?>