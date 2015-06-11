<?php
/**
 * TipoAtividade Active Record
 * @author  <your-name-here>
 */
class TipoPessoa extends TRecord
{
    const TABLENAME = 'tbz_tipo_pessoa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
       
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao_tipo');
    }

}
