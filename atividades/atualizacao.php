<?php

require_once 'init.php';

try
{
    TTransaction::open('atividade');
    
    //atualiza pessoas
    $file = "http://www2.tecbiz.com.br/tecbiz/tecbiz.php?a=21f7b2&acs=1";
    $json = file_get_contents($file);
    $lista = json_decode($json, true);
    foreach ($lista as $value) {
        
        $pessoa = new Pessoa;
        $pessoa->fromArray($value);
        $pessoa->store();
        
    }
    
    //atualiza empresas
    $file = "http://www2.tecbiz.com.br/tecbiz/tecbiz.php?a=21f7b2&acs=2";
    $json = file_get_contents($file);
    $lista = json_decode($json, true);
    foreach ($lista as $value) {
        
        $empresa = new Empresa;
        $empresa->fromArray($value);
        $empresa->store();
        
    }
    
    //atualiza entidades
    $file = "http://www2.tecbiz.com.br/tecbiz/tecbiz.php?a=21f7b2&acs=3";
    $json = file_get_contents($file);
    $lista = json_decode($json, true);
    foreach ($lista as $value) {
        
        $entidade = new Entidade;
        $entidade->fromArray($value);
        $entidade->store();
        
    }
    
    //atualiza entidades
    $file = "http://www2.tecbiz.com.br/tecbiz/tecbiz.php?a=21f7b2&acs=4";
    $json = file_get_contents($file);
    $lista = json_decode($json, true);
    foreach ($lista as $value) {
        
        $estabelecimento = new Estabelecimento;
        $estabelecimento->fromArray($value);
        $estabelecimento->store();
        
    }
    
    TTransaction::close();
}
catch (Exception $e)
{
    print $e->getMessage();
}
?>