
    <?php
    class CommonPage extends TPage
    {
        
            
        public function __construct()
        {
           parent::__construct();
           $hello = 'Ola Mjundo <br />';
           TTransaction::open('atividade');
           $objects = Atividade::retornaAtividadesClienteColaborador(1, 6, 2015);
           TTransaction::close();
           
                      $string = new StringsUtil;
           
           TTransaction::open('tecbiz');
           
           $repo = new TRepository('Entidade');
           $entidades = $repo->load();
           
           foreach($entidades as $etd)
           {
               print $etd->entnomfan.'<br />';
           }
           
           TTransaction::close();
           
                       
           parent::add($hello);
            
        }
                
    }
    ?>


