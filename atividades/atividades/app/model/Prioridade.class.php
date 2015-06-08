<?php
/**
 * Prioridade Active Record
 * @author  <your-name-here>
 */
class Prioridade extends TRecord
{
    const TABLENAME = 'prioridade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }

    
    /**
     * Method getTickets
     */
    public function getTickets()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('prioridade_id', '=', $this->id));
        return Ticket::getObjects( $criteria );
    }
    


}
