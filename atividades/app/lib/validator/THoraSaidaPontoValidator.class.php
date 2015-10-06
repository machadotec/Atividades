<?php
/**
 * Time validation
 *
 * @version    1.0
 * @package    validator
 * @author     Gustavo R. Emmel
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class THoraSaidaPontoValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated in case of 00:00
     */
    public function validate($label, $value, $parameters = NULL)
    {
        if ((trim($value)) )
        {
            
            $ultima = Atividade::retornaHoraUltimaAtividade($parameters['user'], $parameters['data']);
            
            if($ultima)
            {
            
                $HoraSaida       = new DateTime($value);
                $HoraAtividade   = new DateTime($ultima);
                
                if($HoraAtividade > $HoraSaida)
                {
                    throw new Exception("Horário inválido");
                }
            
            }
                        
        }
    }
}
?>
