<?php
/**
 * TipoAtividade Active Record
 * @author  <your-name-here>
 */
class TipoAtividade extends TRecord
{
    const TABLENAME = 'tipo_atividade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }

    
    /**
     * Method getAtividades
     */
    public function getAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipoatividade_id', '=', $this->id));
        return Atividade::getObjects( $criteria );
    }
    


}
