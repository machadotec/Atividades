<?php
/**
 * Empresa Active Record
 * @author  <your-name-here>
 */
class Empresa extends TRecord
{
    const TABLENAME = 'public.tbz_empresa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('razao_social');
        parent::addAttribute('endereco');
        parent::addAttribute('fone');
        parent::addAttribute('cnpj');
        parent::addAttribute('email');
        parent::addAttribute('segmento');
        parent::addAttribute('observacao');
    }


}
