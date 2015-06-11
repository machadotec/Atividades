<?php
class TestePage extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        //parent::add(new TLabel('TestePage'));
        
        //TTransaction::open('tecbiz'); // open a transaction      
        /*
            $x = new Pessoa(2);
            echo $x->cidade_id.'<br />';
            echo $x->cidade_nome->cidade_nome; 
         
        
        $pessoa = new Pessoa(2);
        
        $municipio = $pessoa->cidade_nome->cidade_nome;
        
        $tipo = $pessoa->descricao_tipo->descricao_tipo;
        
        echo $municipio.'<br>' ;
        
        echo $tipo;
       
        TTransaction::close();
         */
        // creates one datagrid
        $this->datagrid = new TQuickGrid;
        $this->datagrid->setHeight(320);
        
        // add the columns
        $this->datagrid->addQuickColumn('Code',    'code',    'right', 70);
        $this->datagrid->addQuickColumn('Name',    'name',    'left', 180);
        $this->datagrid->addQuickColumn('Address', 'address', 'left', 180);
        $this->datagrid->addQuickColumn('Phone',   'fone',    'left', 160);
        $this->datagrid->addQuickColumn('Valor1',   'valor1',    'left', 70);
        $this->datagrid->addQuickColumn('Valor2',   'valor2',    'left', 70);
        
        $this->datagrid->addQuickAction('View', new TDataGridAction(array($this, 'onShowDetail')), 'code', 'ico_view.png');
        // creates the datagrid model
        $this->datagrid->createModel();
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->datagrid);

        parent::add($vbox);
        
    }
    
    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '1';
        $item->name     = 'Fábio Locatelli';
        $item->address  = 'Rua Expedicionario';
        $item->fone     = '1111-1111';
        $item->valor1    = '111';
        $item->valor2    = '222';
        
        $row = $this->datagrid->addItem($item);
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '2';
        $item->name     = 'Julia Haubert';
        $item->address  = 'Rua Expedicionarios';
        $item->fone     = '2222-2222';
        $item->valor1    = '111';
        $item->valor2    = $item->valor1 * 4;
        $row = $this->datagrid->addItem($item);
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '3';
        $item->name     = 'Carlos Ranzi';
        $item->address  = 'Rua Oliveira';
        $item->fone     = '3333-3333';
        $item->valor1    = '111';
        $item->valor2    = '222';
        $row = $this->datagrid->addItem($item);
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '4';
        $item->name     = 'Daline DallOglio';
        $item->address  = 'Rua Oliveira';
        $item->fone     = '4444-4444';
        $item->valor1    = '111';
        $item->valor2    = '222';
        $row = $this->datagrid->addItem($item);
    }
    
    /**
     * Show record detail
     */
    public function onShowDetail( $param )
    {
        // get row position
        $pos = $this->datagrid->getRowIndex('code', $param['key']);
        
        // get row by position
        $current_row = $this->datagrid->getRow($pos);
        $current_row->style = "background-color: #8D8BC8; color:white; text-shadow:none";
        
        // create a new row
        $row = new TTableRow;
        $row->style = "background-color: #E0DEF8";
        $row->addCell('');
        $cell = $row->addCell('In this space, you can add any detail<br> content about the selected record');
        $cell->colspan = 4;
        $cell->style='padding:10px;';
        
        // insert the new row
        $this->datagrid->insert($pos +1, $row);
    }
    
    /**
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();
    }
    
}