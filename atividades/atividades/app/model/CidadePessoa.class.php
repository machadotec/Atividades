<?php
/**
 * TipoAtividade Active Record
 * @author  <your-name-here>
 */
class CidadePessoa extends TRecord
{
    const TABLENAME = 'tbz_cidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
       
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cidade_nome');
        parent::addAttribute('uf_id');
    }

}
