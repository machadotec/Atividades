<?php
class CommonPage extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        
        //exec("php /var/www/html/atividades/atualizacao.php");

        
        /*
        TTransaction::open('atividade');
        
        //atualiza pessoas
        $file = "http://192.168.1.2/tecbiz/tecbiz.php?a=21f7b2&acs=1";
        $json = file_get_contents($file);
        $lista = json_decode($json, true);
        foreach ($lista as $value) {
            
            $pessoa = new Pessoa;
            $pessoa->fromArray($value);
            $pessoa->store();
            
        }
        
        //atualiza empresas
        $file = "http://192.168.1.2/tecbiz/tecbiz.php?a=21f7b2&acs=2";
        $json = file_get_contents($file);
        $lista = json_decode($json, true);
        foreach ($lista as $value) {
            
            $empresa = new Empresa;
            $empresa->fromArray($value);
            $empresa->store();
            
        }
        
        //atualiza entidades
        $file = "http://192.168.1.2/tecbiz/tecbiz.php?a=21f7b2&acs=3";
        $json = file_get_contents($file);
        $lista = json_decode($json, true);
        foreach ($lista as $value) {
            
            $entidade = new Entidade;
            $entidade->fromArray($value);
            $entidade->store();
            
        }
        
        //atualiza entidades
        $file = "http://192.168.1.2/tecbiz/tecbiz.php?a=21f7b2&acs=4";
        $json = file_get_contents($file);
        $lista = json_decode($json, true);
        foreach ($lista as $value) {
            
            $estabelecimento = new Estabelecimento;
            $estabelecimento->fromArray($value);
            $estabelecimento->store();
            
        }
        
        TTransaction::close();
        */
        parent::add(new TLabel('Common page'));
    }
}
?>