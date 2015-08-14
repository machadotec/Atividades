<?php
/**
 * Atividade Active Record
 * @author  <your-name-here>
 */
class Atividade extends TRecord
{
    const TABLENAME = 'atividade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $tipo_atividade;
    private $ticket;
    private $sistema;
    private $pessoa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_atividade');
        parent::addAttribute('hora_inicio');
        parent::addAttribute('hora_fim');
        parent::addAttribute('descricao');
        parent::addAttribute('colaborador_id');
        parent::addAttribute('tipo_atividade_id');
        parent::addAttribute('ticket_id');
        parent::addAttribute('sistema_id');
    }

    public function retornaTotalAtividadesColaborador($colaborador, $mes, $ano, $tickets)
    {
        
        $tic = "";
        if($tickets)
        {
            $tic = " and a.ticket_id IN ({$tickets}) ";
        }
        
        $col = "";
        if($colaborador > 0)
        {
            $col = " and a.colaborador_id = {$colaborador} ";
 
        }
        
        $conn = TTransaction::get();
        
        $result = $conn->query("select sum((a.hora_fim - a.hora_inicio)) as total from atividade as a
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.ticket_id <> 328 {$col} {$tic}");
        
        foreach ($result as $row)
        {
            $data = $row['total'];
        }

        return $data;
        
    }

    public function retornaAtividadesColaborador($colaborador, $mes, $ano, $tickets)
    {
        
        $tic = "";
        if($tickets)
        {
            $tic = " and a.ticket_id IN ({$tickets}) ";
        }
        
        $col = "";
        if($colaborador > 0)
        {
            $col = " and a.colaborador_id = {$colaborador} ";
 
        }
        
        $conn = TTransaction::get();
        $result = $conn->query("select a.tipo_atividade_id,t.nome, sum((a.hora_fim - a.hora_inicio)) as total from atividade as a
                                inner join tipo_atividade as t on a.tipo_atividade_id = t.id
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.ticket_id <> 328 {$col} {$tic}
                                group by tipo_atividade_id, nome
                                order by nome
                                ");
        
        return $result;
        
    }
    
    public function retornaAtestadosMedicos($colaborador, $mes, $ano)
    {
        
        $col = "";
        if($colaborador > 0)
        {
            $col = " and a.colaborador_id = {$colaborador} ";
 
        }
        
        $conn = TTransaction::get();
        $result = $conn->query("select sum((a.hora_fim - a.hora_inicio)) as total from atividade as a
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.ticket_id = 328 {$col} ");
        
        return $result;    
        
        
    }
    
    public function retornaAtividadesSistemaColaborador($colaborador, $mes, $ano, $tickets)
    {
        
        $tic = "";
        if($tickets)
        {
            $tic = " and a.ticket_id IN ({$tickets}) ";
        }
                
        $col = "";
        if($colaborador > 0)
        {
            $col = " and a.colaborador_id = {$colaborador} ";
 
        }
        
        $conn = TTransaction::get();
        $result = $conn->query("select a.sistema_id, s.nome, sum((a.hora_fim - a.hora_inicio)) as total from atividade as a 
                                inner join sistema as s on a.sistema_id = s.id
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.ticket_id <> 328 {$col} {$tic}
                                group by a.sistema_id, s.nome
                                order by s.nome
                                ");
        
        return $result;
        
    }
    
    public function retornaAtividadesClienteColaborador($colaborador, $mes, $ano, $tickets)
    {
                
        $tic = "";
        if($tickets)
        {
            $tic = " and a.ticket_id IN ({$tickets}) ";
        }
        
        $col = "";
        if($colaborador > 0)
        {
            $col = " and a.colaborador_id = {$colaborador} ";
 
        }
        
        $conn = TTransaction::get();
        $result = $conn->query("select t.solicitante_id, sum((a.hora_fim - a.hora_inicio)) as total from atividade as a 
                                inner join ticket as t on a.ticket_id = t.id
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.ticket_id <> 328 {$col} {$tic}
                                group by solicitante_id
                                ");
        
        return $result;
        
    }
    
    public function retornaUltimaAtividade($user)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query('SELECT id FROM atividade WHERE colaborador_id = '.$user.' order by data_atividade desc, id desc limit 1');
        
        foreach ($result as $row)
        {
            $data = $row['id'];
        }
        
        return $data;
        
    }
    
    /**
     * Method set_tipo_atividade
     * Sample of usage: $atividade->tipo_atividade = $object;
     * @param $object Instance of TipoAtividade
     */
    public function set_tipo_atividade(TipoAtividade $object)
    {
        $this->tipo_atividade = $object;
        $this->tipo_atividade_id = $object->id;
    }
    
    /**
     * Method get_tipo_atividade
     * Sample of usage: $atividade->tipo_atividade->attribute;
     * @returns TipoAtividade instance
     */
    public function get_tipo_atividade()
    {
        // loads the associated object
        if (empty($this->tipo_atividade))
            $this->tipo_atividade = new TipoAtividade($this->tipo_atividade_id);
    
        // returns the associated object
        return $this->tipo_atividade;
    }
    
    
    /**
     * Method set_ticket
     * Sample of usage: $atividade->ticket = $object;
     * @param $object Instance of Ticket
     */
    public function set_ticket(Ticket $object)
    {
        $this->ticket = $object;
        $this->ticket_id = $object->id;
    }
    
    /**
     * Method get_ticket
     * Sample of usage: $atividade->ticket->attribute;
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
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->colaborador_id = $object->pessoa_codigo;
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $ticket->pessoa->pessoa_nome;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->colaborador_id);
    
        // returns the associated object
        return $this->pessoa;
    }


}
