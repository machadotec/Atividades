<?php
/**
 * System_program Active Record
 * @author  <your-name-here>
 */
class SystemProgram extends TRecord
{
    const TABLENAME = 'system_program';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('controller');
    }
    
    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        SystemChangeLog::register($this, $this->toArray(), array());
        // delete the object itself
        parent::delete($id);
    }
    
    public function onBeforeStore($object)
    {
        $this->lastState = array();
        if (self::exists($object->id))
        {
            $this->lastState = parent::load($object->id)->toArray();
        }
        // file_put_contents('/tmp/log.txt', "Before Store ".print_r($object, TRUE), FILE_APPEND);
    }
    
    public function onAfterStore($object)
    {
        SystemChangeLog::register($this, $this->lastState, (array) $object);
        // file_put_contents('/tmp/log.txt', "After Store ".print_r($object, TRUE), FILE_APPEND);
    }
}
