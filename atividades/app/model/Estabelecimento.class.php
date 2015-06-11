<?php
/**
 * Car800 Active Record
 * @author  <your-name-here>
 */
class Estabelecimento extends TRecord
{
    const TABLENAME = 'public.car800';
    const PRIMARYKEY= 'lojcodloj';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('lojrazsoc');
        parent::addAttribute('lojcgcest');
        parent::addAttribute('lojlogend');
        parent::addAttribute('lojlognum');
        parent::addAttribute('lojlogcmp');
        parent::addAttribute('lojlogbai');
        parent::addAttribute('lojlogfon');
        parent::addAttribute('lojlogema');
        parent::addAttribute('lojlogfax');
        parent::addAttribute('lojendsit');
        parent::addAttribute('lojdatcad');
        parent::addAttribute('lojobsest');
        parent::addAttribute('lojcidlog');
        parent::addAttribute('lojsenlog');
        parent::addAttribute('lojnomfan');
        parent::addAttribute('lojinsest');
        parent::addAttribute('lojceploj');
        parent::addAttribute('lojcpfest');
        parent::addAttribute('lojtipjur');
        parent::addAttribute('lojramati');
        parent::addAttribute('segcodseg');
        parent::addAttribute('lojbanco');
        parent::addAttribute('lojagencia');
        parent::addAttribute('lojconta');
        parent::addAttribute('lojmatloj');
        parent::addAttribute('lojcodmat');
        parent::addAttribute('lojsenext');
        parent::addAttribute('lojdattxa');
        parent::addAttribute('lojvaltxa');
        parent::addAttribute('lojobstxa');
        parent::addAttribute('lojnomdir');
        parent::addAttribute('lojcodpes');
        parent::addAttribute('lojtipcta');
        parent::addAttribute('lojmovpar');
        parent::addAttribute('lojdtapar');
        parent::addAttribute('lojmovloj');
        parent::addAttribute('lojmovnro');
        parent::addAttribute('lojblqtxa');
        parent::addAttribute('lojmotcbo');
        parent::addAttribute('lojstacob');
        parent::addAttribute('lojdtvtxa');
        parent::addAttribute('lojbolloj');
        parent::addAttribute('lojlogfon2');
        parent::addAttribute('lojtemdup');
        parent::addAttribute('lojstaman');
        parent::addAttribute('lojtefloj');
        parent::addAttribute('lojacesso');
    }


}
