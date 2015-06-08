<?php
/**
 * System_group Active Record
 * @author  <your-name-here>
 */
class SystemGroup extends TRecord
{
    const TABLENAME = 'system_group';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_programs = array();

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
    }

    /**
     * Method addSystem_program
     * Add a System_program to the System_group
     * @param $object Instance of System_program
     */
    public function addSystemProgram(SystemProgram $object)
    {
        $this->system_programs[] = $object;
    }
    
    /**
     * Method getSystem_programs
     * Return the System_group' System_program's
     * @return Collection of System_program
     */
    public function getSystemPrograms()
    {
        return $this->system_programs;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->system_programs = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        // load the related System_program objects
        $repository = new TRepository('SystemGroupProgram');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_group_id', '=', $id));
        $system_group_system_programs = $repository->load($criteria);
        if ($system_group_system_programs)
        {
            foreach ($system_group_system_programs as $system_group_system_program)
            {
                $system_program = new SystemProgram( $system_group_system_program->system_program_id );
                $this->addSystemProgram($system_program);
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
    
        // delete the related System_groupSystem_program objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_group_id', '=', $this->id));
        $repository = new TRepository('SystemGroupProgram');
        $repository->delete($criteria);
        // store the related System_groupSystem_program objects
        if ($this->system_programs)
        {
            foreach ($this->system_programs as $system_program)
            {
                $system_group_system_program = new SystemGroupProgram;
                $system_group_system_program->system_program_id = $system_program->id;
                $system_group_system_program->system_group_id = $this->id;
                $system_group_system_program->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related System_groupSystem_program objects
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('SystemGroupProgram');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_group_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the object itself
        parent::delete($id);
    }
}
?>