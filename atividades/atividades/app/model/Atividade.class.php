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
    }

    public function retornaUltimaAtividade($user)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query('SELECT id FROM atividade WHERE colaborador_id = '.$user.' order by data_atividade desc, id desc limit 1');
        
        foreach ($result as $row)
        {
            $data = $row['id'];
        }
        
        if(!$data)
        {
            $action = new TAction(array('AtividadeList', 'onReload'));
            new TMessage('info', 'Nenhuma atividade cadastrada!', $action); // success message
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
    


}
