<?php
/**
 * Dois campos
 *
 * @version    1.0
 * @package    validator
 * @author     Gustavo Emmel
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TUltiPgtoValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation (ex: mask)
     */
    public function validate($label, $value, $parameters = NULL)
    {
        
        //$parameters[0] = valor
        //$parameters[1] = data
        
        if($parameters[0] AND !$parameters[1])
        {
            
            if($parameters[0] <> '0,00')
            {
            $erro = "Deve existir data para o valor pago";
            }
        }
        
        if(!$parameters[0] AND $parameters[1])
        {
            $erro = "Deve existir valor para a data de pagamento";
        }
        
        
        if(isset($erro))
        {
            throw new Exception($erro);
        }
        
    }
}
?>