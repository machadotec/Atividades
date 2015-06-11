<?php
/**
 * Ticket Active Record
 * @author  <your-name-here>
 */
class Ticket extends TRecord
{
    const TABLENAME = 'ticket';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    private $tipo_ticket;
    private $sistema;
    private $status_ticket;
    private $prioridade;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('origem');
        parent::addAttribute('solicitacao_descricao');
        parent::addAttribute('providencia');
        parent::addAttribute('orcamento_horas');
        parent::addAttribute('orcamento_valor_hora');
        parent::addAttribute('valor_desconto');
        parent::addAttribute('valor_total');
        parent::addAttribute('data_ultimo_pgto');
        parent::addAttribute('valor_ultimo_pgto');
        parent::addAttribute('valor_total_pago');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('data_prevista');
        parent::addAttribute('data_validade');
        parent::addAttribute('data_aprovacao');
        parent::addAttribute('observacao');
        parent::addAttribute('solicitante_id');
        parent::addAttribute('responsavel_id');
        parent::addAttribute('tipo_ticket_id');
        parent::addAttribute('sistema_id');
        parent::addAttribute('status_ticket_id');
        parent::addAttribute('prioridade_id');
        parent::addAttribute('forma_pagamento');
    }


    public function getDesenvolvimentoTicket($ticket_id)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ticket_id', '=', $ticket_id));
        
        $repo = new TRepository('RequisitoDesenvolvimento');
        $DRs   = $repo->load($criteria);
        
        $retorno = '';
        
        foreach ($DRs as $dr)
        {
            $retorno = $dr->titulo;
        }
        
        return $retorno;
        
    }

    public function getTicketsSolicitante($solicitante_id)
    {
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('solicitante_id', '=', $solicitante_id) );
            
        $repository = new TRepository('Ticket');
        $tickets = $repository->load($criteria);
        
        $retorno[] = '0';
            
        foreach ($tickets as $row)
        {
            $retorno[] = $row->id;
        }
            
        return $retorno; 
        
    }
    public function getNaoUsados()
    {
        $conn = TTransaction::get();
        $result = $conn->query("SELECT id FROM ticket WHERE id NOT IN (SELECT ticket_id FROM requisito_desenvolvimento)");
        
        foreach ($result as $row)
        {
            $retorno[] = $row[0];
        }
        
        if(!$retorno)
        {
            $action = new TAction(array('TicketForm', 'onEdit'));
            new TMessage('info', 'Cadastre um ticket antes de Gerar DR', $action); // success message
        }
        
        return $retorno;
    }
    
    /**
     * Method set_tipo_ticket
     * Sample of usage: $ticket->tipo_ticket = $object;
     * @param $object Instance of TipoTicket
     */
    public function set_tipo_ticket(TipoTicket $object)
    {
        $this->tipo_ticket = $object;
        $this->tipo_ticket_id = $object->id;
    }
    
    /**
     * Method get_tipo_ticket
     * Sample of usage: $ticket->tipo_ticket->attribute;
     * @returns TipoTicket instance
     */
    public function get_tipo_ticket()
    {
        // loads the associated object
        if (empty($this->tipo_ticket))
            $this->tipo_ticket = new TipoTicket($this->tipo_ticket_id);
    
        // returns the associated object
        return $this->tipo_ticket;
    }
    
    
    /**
     * Method set_sistema
     * Sample of usage: $ticket->sistema = $object;
     * @param $object Instance of Sistema
     */
    public function set_sistema(Sistema $object)
    {
        $this->sistema = $object;
        $this->sistema_id = $object->id;
    }
    
    /**
     * Method get_sistema
     * Sample of usage: $ticket->sistema->attribute;
     * @returns Sistema instance
     */
    public function get_sistema()
    {
        // loads the associated object
        if (empty($this->sistema))
            $this->sistema = new Sistema($this->sistema_id);
    
        // returns the associated object
        return $this->sistema;
    }
    
    
    /**
     * Method set_status_ticket
     * Sample of usage: $ticket->status_ticket = $object;
     * @param $object Instance of StatusTicket
     */
    public function set_status_ticket(StatusTicket $object)
    {
        $this->status_ticket = $object;
        $this->status_ticket_id = $object->id;
    }
    
    /**
     * Method get_status_ticket
     * Sample of usage: $ticket->status_ticket->attribute;
     * @returns StatusTicket instance
     */
    public function get_status_ticket()
    {
        // loads the associated object
        if (empty($this->status_ticket))
            $this->status_ticket = new StatusTicket($this->status_ticket_id);
    
        // returns the associated object
        return $this->status_ticket;
    }
    
    
    /**
     * Method set_prioridade
     * Sample of usage: $ticket->prioridade = $object;
     * @param $object Instance of Prioridade
     */
    public function set_prioridade(Prioridade $object)
    {
        $this->prioridade = $object;
        $this->prioridade_id = $object->id;
    }
    
    /**
     * Method get_prioridade
     * Sample of usage: $ticket->prioridade->attribute;
     * @returns Prioridade instance
     */
    public function get_prioridade()
    {
        // loads the associated object
        if (empty($this->prioridade))
            $this->prioridade = new Prioridade($this->prioridade_id);
    
        // returns the associated object
        return $this->prioridade;
    }
    
    /**
     * Method getAtividades
     */
    public function getAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ticket_id', '=', $this->id));
        return Atividade::getObjects( $criteria );
    }
    
    /**
     * Method getRequisitoDesenvolvimentos
     */
    public function getRequisitoDesenvolvimentos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ticket_id', '=', $this->id));
        return RequisitoDesenvolvimento::getObjects( $criteria );
    }
    
}
