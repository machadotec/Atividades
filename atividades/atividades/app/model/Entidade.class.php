<?php
/**
 * Entidade Active Record
 * @author  <your-name-here>
 */
class Entidade extends TRecord
{
    const TABLENAME = 'public.car200';
    const PRIMARYKEY= 'entcodent';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('entrazsoc');
        parent::addAttribute('entnumcgc');
        parent::addAttribute('entlogend');
        parent::addAttribute('entendnum');
        parent::addAttribute('entendcmp');
        parent::addAttribute('entendcep');
        parent::addAttribute('ententbai');
        parent::addAttribute('entendfon');
        parent::addAttribute('entendema');
        parent::addAttribute('entmunend');
        parent::addAttribute('entcaddta');
        parent::addAttribute('entcadobs');
        parent::addAttribute('endfaxnum');
        parent::addAttribute('entendsit');
        parent::addAttribute('entsenent');
        parent::addAttribute('entdiacor');
        parent::addAttribute('entdiades');
        parent::addAttribute('entopccor');
        parent::addAttribute('entnomfan');
        parent::addAttribute('enttiplim');
        parent::addAttribute('entcontato');
        parent::addAttribute('enttippro');
        parent::addAttribute('entmsgecs');
        parent::addAttribute('entmsgass');
        parent::addAttribute('entusatef');
        parent::addAttribute('entlcttbz');
        parent::addAttribute('etdlctrecs');
        parent::addAttribute('entdtaold');
        parent::addAttribute('entempent');
        parent::addAttribute('enttipent');
    }


}
