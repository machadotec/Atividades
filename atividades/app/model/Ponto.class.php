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
    
    public function retornaTempoPonto($user, $data)
    {

        $conn = TTransaction::get();
        $result = $conn->query("select (hora_saida - hora_entrada) as horario from ponto where data_ponto = '{$data}' and colaborador_id = ".$user);
        
        $data = '00:00:00';
        foreach ($result as $row)
        {
            $data = $row['horario'];
        }
        
        if(!$data)
        {
            $data = '00:00:00';
        }

        return $data;
        
    }
    
    public function retornaUltimoPonto($user)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query("SELECT data_ponto, id FROM ponto WHERE colaborador_id = {$user} order by data_ponto desc, id desc limit 1");
        
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
        
    public function retornaHoraInicio($data, $user)
    {
        
        if($data)
        {
            
            $conn = TTransaction::get();
            
            // busca ultima hora da atividade do dia
            $result = $conn->query("SELECT MAX(hora_fim) as hora_fim FROM atividade WHERE data_atividade = '{$data}' AND colaborador_id = {$user} LIMIT 1");
            foreach ($result as $row)
            {
                $hora = $row['hora_fim'];
            }
            
            // busca hora entrada ponto do dia
            if (!$hora) 
            {
                $result = $conn->query("SELECT MAX(hora_entrada) as hora_entrada FROM ponto WHERE data_ponto = '{$data}' AND colaborador_id = {$user} LIMIT 1");
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
    
    public function saldoHorasMes($user)
    {
        $string = new StringsUtil;        
        $mes = date('m');
        $conn = TTransaction::get();
        $result = $conn->query("select (hora_saida - hora_entrada) as horario from ponto 
                                where colaborador_id = {$user} and extract('month' from data_ponto) = {$mes} and hora_saida is not null and hora_entrada is not null");
        
        $almoco       = new DateTime('01:00:00');
        $limite       = new DateTime('06:00:00');
        $cargaHoraria = $string->time_to_sec('08:48:00');
        
        foreach ($result as $row)
        {
            $total = new DateTime($row['horario']);
            if($total > $limite)
            {
               $total = $total->diff($almoco)->format('%H:%I:%S');
            }            
            $saldo += $string->time_to_sec($total) - $cargaHoraria;
        }
        return $string->sec_to_time($saldo);
    }
    
    public function horaPreenchidas($data, $user)
    {

        $conn = TTransaction::get();
        $result = $conn->query("select sum((hora_fim - hora_inicio)) as intervalo from atividade where data_atividade = '{$data}' and colaborador_id = {$user} and ticket_id <> 328");
        
        foreach ($result as $row)
        {
            $intervalo = $row['intervalo'];
        }
        
        return $intervalo;
        
    }
    
}