<?php
/**
 * StatusTicket Active Record
 * @author  <your-name-here>
 */
class StatusTicket extends TRecord
{
    const TABLENAME = 'status_ticket';
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
        $criteria->add(new TFilter('statusticket_id', '=', $this->id));
        return Ticket::getObjects( $criteria );
    }
    


}
