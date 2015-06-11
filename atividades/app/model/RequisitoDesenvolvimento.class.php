<?php
/**
 * RequisitoDesenvolvimento Active Record
 * @author  <your-name-here>
 */
class RequisitoDesenvolvimento extends TRecord
{
    const TABLENAME = 'requisito_desenvolvimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    private $ticket;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('rotina');
        parent::addAttribute('objetivo');
        parent::addAttribute('entrada');
        parent::addAttribute('processamento');
        parent::addAttribute('saida');
        parent::addAttribute('ticket_id');
    }

    
    /**
     * Method set_ticket
     * Sample of usage: $requisito_desenvolvimento->ticket = $object;
     * @param $object Instance of Ticket
     */
    public function set_ticket(Ticket $object)
    {
        $this->ticket = $object;
        $this->ticket_id = $object->id;
    }
    
    /**
     * Method get_ticket
     * Sample of usage: $requisito_desenvolvimento->ticket->attribute;
     * @returns Ticket instance
     */
    public function get_ticket()
    {
        // loads the associated object
        if (empty($this->ticket))
            $this->ticket = new Ticket($this->ticket_id);
    
        // returns the associated object
        return $this->ticket;
    }
    
}
