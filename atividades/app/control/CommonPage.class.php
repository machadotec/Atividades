<?php
class CommonPage extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        TTransaction::open('atividade');

         $criteria = new TCriteria;
         $criteria->add(new TFilter("origem", "=", 1));
                       
               
        TTransaction::close();


        
        parent::add(new TLabel('Common page'));
    }
}
?>