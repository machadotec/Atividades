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
class THoraFimValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated in case of 00:00
     */
    public function validate($label, $value, $parameters = NULL)
    {
        if ((!trim($value))  OR (trim($value)=='00:00:00') )
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 is required', $label));
        }
    }
}
?>
