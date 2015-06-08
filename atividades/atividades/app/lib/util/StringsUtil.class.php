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
}

?>