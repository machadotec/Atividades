<?php
class StringsUtil
{
    
    public function formatDate($date)
    {        
        if($date)
        {
            $dt = explode('/', $date);
            $retorno = $dt[2].'-'.$dt[1].'-'.$dt[0];
            return $retorno;    
        }
    }
    
    public function formatDateBR($date)
    {        
        if($date)
        {
            $dt = explode('-', $date);
            $retorno = $dt[2].'/'.$dt[1].'/'.$dt[0];
            return $retorno;    
        }
    }
    
    
    public function formatHoras($campo, $object, $row)
    {        
        $object->orcamento_horas ? $campo = strstr($object->orcamento_horas, ':', true) : null;
        return $campo;
    }
    
    public function desconverteReais($valor)
    {
        $valor = str_replace('.',  '', $valor);
        $valor = str_replace(',', '.', $valor);
        return $valor;
    }
    
    function time_to_sec($time) 
    {
        $hours = substr($time, 0, -6);
        $minutes = substr($time, -5, 2);
        $seconds = substr($time, -2);
    
        return $hours * 3600 + $minutes * 60 + $seconds;
    }
    
    function sec_to_time($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);
        
        return str_pad($hours, 2, "0", STR_PAD_LEFT).':'.str_pad($mins, 2, "0", STR_PAD_LEFT).':'.str_pad($secs, 2, "0", STR_PAD_LEFT);
    }
    
    function array_meses()
    {
        $meses = array(
                            1 => 'Janeiro',
                            'Fevereiro',
                            'Março',
                            'Abril',
                            'Maio',
                            'Junho',
                            'Julho',
                            'Agosto',
                            'Setembro',
                            'Outubro',
                            'Novembro',
                            'Dezembro'
                        );
       
       return $meses;
    }
    
    function subtrair_datas($data_inicial, $data_final)
    {
        // Usa a função strtotime() e pega o timestamp das duas datas:
        $time_inicial = strtotime($data_inicial);
        $time_final = strtotime($data_final);
        
        // Calcula a diferença de segundos entre as duas datas:
        $diferenca = $time_final - $time_inicial; 
        
        // Calcula a diferença de dias
        $dias = (int)floor( $diferenca / (60 * 60 * 24)); 
    
        return $dias;
    }
    
    function retira_segundos($horario)
    {
        return substr($horario, 0, -3);
    }
    
}

?>