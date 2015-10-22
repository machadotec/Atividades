<?php
/**
 * RegistroLogin Active Record
 * @author  <your-name-here>
 */
class RegistroLogin extends TRecord
{
    const TABLENAME = 'public.registro_login';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('login');
        parent::addAttribute('data_ponto');
        parent::addAttribute('hora_inicial');
        parent::addAttribute('hora_final');
    }


}
