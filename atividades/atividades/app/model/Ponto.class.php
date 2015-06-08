<?php
/**
 * Ponto Active Record
 * @author  <your-name-here>
 */
class Ponto extends TRecord
{
    const TABLENAME = 'ponto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_ponto');
        parent::addAttribute('hora_entrada');
        parent::addAttribute('hora_saida');
        parent::addAttribute('colaborador_id');
    }
    
    public function retornaUltimoPonto($user)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query('SELECT data_ponto, id FROM ponto WHERE colaborador_id = '.$user.' order by data_ponto desc, id desc limit 1');
        
        foreach ($result as $row)
        {
            $data = $row['id'];
        }
        
        if(!$data)
        {
            $data = 0;
        }
        
        return $data;
        
    }
    
    public function retornaUltimaData($user, $atividade = null)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query('SELECT MAX(data_ponto) as data_ponto FROM ponto WHERE colaborador_id = '.$user.' LIMIT 1');
        
        foreach ($result as $row)
        {
            $data = $row['data_ponto'];
        }
        
        if($atividade)
        {
            if(!$data)
            {
                $action = new TAction(array('PontoFormList', 'onReload'));
                new TMessage('info', 'Cadastre um horario para o ponto', $action); // success message
            }
        }
        
        return $data;
        
    }
    
    public function retornaHoraInicio($data, $user)
    {
        
        if($data)
        {
            
            $conn = TTransaction::get();
            
            // busca ultima hora da atividade do dia
            $result = $conn->query("SELECT MAX(hora_fim) as hora_fim FROM atividade WHERE data_atividade = "."'$data'"." AND colaborador_id = ".$user." LIMIT 1");
            foreach ($result as $row)
            {
                $hora = $row['hora_fim'];
            }
            
            // busca hora entrada ponto do dia
            if (!$hora) 
            {
                $result = $conn->query("SELECT MAX(hora_entrada) as hora_entrada FROM ponto WHERE data_ponto = "."'$data'"." AND colaborador_id = ".$user." LIMIT 1");
                foreach ($result as $row)
                {
                    $hora = $row['hora_entrada'];
                }
            }
            
            if(!$hora)
            {
                $action = new TAction(array('PontoFormList', 'onReload'));
                new TMessage('info', 'Nesta data não existe ponto cadastrado', $action); // success message
            }
            
            return $hora;
            
        }
        else
        {
            return '08:00:00';
        }
        
    }
    
    public function horaPreenchidas($data, $user)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query("select sum((hora_fim - hora_inicio)) as intervalo from atividade where data_atividade = "."'$data'"." and colaborador_id = ".$user."");
        
        foreach ($result as $row)
        {
            $intervalo = $row['intervalo'];
        }
        
        return $intervalo;
        
    }
    
}