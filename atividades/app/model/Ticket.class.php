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
    private $pessoa;

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
        parent::addAttribute('data_aprovacao');
        parent::addAttribute('observacao');
        parent::addAttribute('solicitante_id');
        parent::addAttribute('responsavel_id');
        parent::addAttribute('tipo_ticket_id');
        parent::addAttribute('sistema_id');
        parent::addAttribute('status_ticket_id');
        parent::addAttribute('prioridade_id');
        parent::addAttribute('forma_pagamento');
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_cancelamento');
        parent::addAttribute('data_encerramento');
    }

    public function relatorioAnalitico($ticket_id, $where)
    {
        $conn = TTransaction::get();
        $result = $conn->query("select 
                                	a.data_atividade, 
                                	(a.hora_fim - a.hora_inicio) as tempo,
                                	a.hora_inicio,
                                	a.hora_fim, 
                                	a.colaborador_id,
                                	a.tipo_atividade_id,
                                	ta.nome as tipo_atividade
                                from atividade as a
                                inner join ticket as t on t.id = a.ticket_id
                                inner join tipo_atividade as ta on a.tipo_atividade_id = ta.id
                                where a.ticket_id = {$ticket_id}
                                {$where}
                                order by a.data_atividade, a.hora_inicio");
         
        return $result;
    
    }
 		 
    public function relatorioSintetico($where)
    {
        $conn = TTransaction::get();
        
        $result = $conn->query("select t.id,
                                       t.status_ticket_id,
                                       s.nome as status,
                                       t.prioridade_id,
                                       p.nome as prioridade,
                                       t.orcamento_horas,
                                       coalesce(sum(a.hora_fim - a.hora_inicio), '00:00:00') as horas_atividade,
                                       (coalesce(t.orcamento_horas, '00:00:00') - coalesce(sum(a.hora_fim - a.hora_inicio), '00:00:00')) as horas_saldo,
                                       t.data_prevista,
                                       t.titulo,
                                       t.responsavel_id,
                                       t.origem,
                                       t.solicitante_id,
                                       coalesce(t.valor_total,0) as valor_total,
                                       coalesce(t.valor_total_pago,0) as valor_total_pago,
                                       (coalesce(t.valor_total,0) - coalesce(t.valor_total_pago,0)) as saldo
                                from ticket as t
                                left join atividade as a on t.id = a.ticket_id
                                inner join status_ticket as s on t.status_ticket_id = s.id
                                inner join prioridade as p on t.prioridade_id = p.id
                                where t.id = t.id
                                {$where}
                                group by t.id, s.nome, p.nome
                                order by t.id");
                                                                
         return $result;
    
    }
   
    public function getTicketsCliente($cliente)
    {
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('solicitante_id', 'IN', $cliente) );
            
        $repository = new TRepository('Ticket');
        $tickets = $repository->load($criteria);
        
        $retorno[] = '0';
            
        foreach ($tickets as $row)
        {
            if($row->id <> 328)
            {
                $retorno[] = $row->id;
            }
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
     * Method set_pessoa
     * Sample of usage: $ticket->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa_solicitante(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->solicitante_id = $object->pessoa_codigo;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $ticket->pessoa->pessoa_nome;
     * @returns Pessoa instance
     */
    public function get_pessoa_solicitante()
    {
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->solicitante_id);
    
        // returns the associated object
        return $this->pessoa;
    }
    
    /**
     * Method set_pessoa
     * Sample of usage: $ticket->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa_responsavel(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->responsavel_id = $object->pessoa_codigo;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $ticket->pessoa->pessoa_nome;
     * @returns Pessoa instance
     */
    public function get_pessoa_responsavel()
    {
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->responsavel_id);
    
        // returns the associated object
        return $this->pessoa;
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
