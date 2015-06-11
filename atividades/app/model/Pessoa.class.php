<?php
/**
 * Ponto Active Record
 * @author  <your-name-here>
 */
class Pessoa extends TRecord
{
    const TABLENAME = 'tbz_pessoas';
    const PRIMARYKEY= 'pessoa_codigo';
    const IDPOLICY =  'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    private $descricao_tipo;
    private $cidade_nome;
    private $origem_nome;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_nome');
        parent::addAttribute('pessoa_apelido');
        parent::addAttribute('data_nascimento');
        parent::addAttribute('cpf');
        parent::addAttribute('cargo');
        parent::addAttribute('departamento');
        parent::addAttribute('origem');
        parent::addAttribute('codigo_cadastro_origem');
        parent::addAttribute('end_logradouro');
        parent::addAttribute('end_cep');
        parent::addAttribute('fone_celular_nro');
        parent::addAttribute('fone_comercial_nro');
        parent::addAttribute('email1');
        parent::addAttribute('email2');
        parent::addAttribute('data_criacao');
        parent::addAttribute('observacao');
        parent::addAttribute('cidade_id');
        parent::addAttribute('tipo_pessoa_id');
        parent::addAttribute('user');
        
    }
   
    public function get_origem_nome()
    {
        // loads the associated object
        if (empty($this->origem_nome))
        {
            $this->origem_nome = '';
            if($this->origem > 0)
            {
                $origens = array(1 => 'Entidade', 2 => 'Estabelecimento', 3 => 'Empresa');
                $campos  = array(1 => 'entrazsoc', 2 => 'lojrazsoc', 3 => 'razao_social');
                $origen  = new $origens[$this->origem]($this->codigo_cadastro_origem);
                $this->origem_nome = $origen->$campos[$this->origem]; 
            }
        }
        
        // returns the associated object
        return $this->origem_nome;
    }
    
    public function get_descricao_tipo()
    {
        // loads the associated object
        if (empty($this->descricao_tipo))
        $this->descricao_tipo = new TipoPessoa($this->tipo_pessoa_id);
        
        // returns the associated object
        return $this->descricao_tipo;
    }
    
    public function get_cidade_nome()
    {
        // loads the associated object
        if (empty($this->cidade_nome))
        $this->cidade_nome = new CidadePessoa($this->cidade_id);
        
        // returns the associated object
        return $this->cidade_nome;
    }
    
    public function retornaUsuario()
    {
        $login = TSession::getValue('login');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('usuario', '=', $login));
        
        $repository = new TRepository('Pessoa');
        $pessoas = $repository->load($criteria);
        
        foreach($pessoas as $pessoa)
        {
            $retorno = $pessoa;
        }
        
        return $retorno;
        
    }
    
}
