
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
           
           foreach ($objects as $row)
           {
               $cliente = new Pessoa($row['solicitante_id']);
               
               if($cliente->origem == 1)
               {
                   $ind = $cliente->codigo_cadastro_origem;
               }
               else
               {
                   $ind = 999;
               }
               
               $array[$ind] += $string->time_to_sec($row['total']); 
           
           }
           
           asort($array);
           
           foreach($array as $key => $value)
           {
               if($key < 999)
               {
                   $etd = new Entidade($key);
                   $nome = $etd->entnomfan;
                   
                   print $key.' - '.$nome.' '.$value.'<br/>';
               }
               else
               {
                   print $key.' - ECS '.$value;
               }
               
           }
           TTransaction::close();
           
                       
           parent::add($hello);
            
        }
                
    }
    ?>


