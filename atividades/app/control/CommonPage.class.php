<?php
class CommonPage extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        TTransaction::open('tecbiz');
        
        $repository = new TRepository('Pessoa');
        $repo = $repository->load();
        
           
        foreach ($repo as $row)
        {
            $pessoa[$row->pessoa_codigo] = $row->pessoa_nome;
        }
        
        print_r($pessoa);
        
        TTransaction::close();
        
        parent::add(new TLabel('Common page'));
    }
}
?>