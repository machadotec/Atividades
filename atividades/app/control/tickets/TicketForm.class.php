<?php
/**
 * TicketForm Registration
 * @author  <your name here>
 */
class TicketForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_Ticket');
        $this->form->class = 'tform'; // CSS class
        
        $table = new TTable;
        $table->style = 'width: 600px';
        $tablePagamento = new TTable;
        $tablePagamento->style = 'width: 600px';
        
        $notebook = new TNotebook(600, 650);
        $notebook->appendPage('Ticket - Cadastramento', $table);
        $notebook->appendPage('Ticket - Orçamento / Pagamento', $tablePagamento);
       
        // create the form fields
        $id                             = new TEntry('id');
        $id->setEditable(FALSE);
        $titulo                         = new TEntry('titulo');
        $origem                         = new TCombo('origem');
        $combo_origem = array();
        $combo_origem['I'] = 'Interno';
        $combo_origem['E'] = 'Externo';
        $origem->addItems($combo_origem);
        $origem->setDefaultOption(FALSE);
        $solicitacao_descricao          = new TText('solicitacao_descricao');
        $providencia                    = new TText('providencia');
        $orcamento_horas                = new TEntry('orcamento_horas');
        $orcamento_horas->setMask('999999');
        $orcamento_valor_hora           = new TEntry('orcamento_valor_hora');
        $orcamento_valor_hora->setNumericMask(2, ',', '.');
        $valor_desconto                 = new TEntry('valor_desconto');
        $valor_desconto->setNumericMask(2, ',', '.');
        $valor_total                    = new TEntry('valor_total');
        $valor_total->setNumericMask(2, ',', '.');
        $valor_total->setEditable(FALSE);
        $forma_pagamento                = new TEntry('forma_pagamento');
        $data_ultimo_pgto               = new TEntry('data_ultimo_pgto');
        $data_ultimo_pgto->setEditable(FALSE);        
        $valor_ultimo_pgto              = new TEntry('valor_ultimo_pgto');
        $valor_ultimo_pgto->setNumericMask(2, ',', '.');
        $valor_ultimo_pgto->setEditable(FALSE);
        $valor_total_pago               = new TEntry('valor_total_pago');
        $valor_total_pago->setNumericMask(2, ',', '.');
        $valor_total_pago->setEditable(FALSE);        
        $data_pagamento                 = new TDate('data_pagamento');
        $data_pagamento->setMask('dd/mm/yyyy');
        $valor_pagamento                = new TEntry('valor_pagamento');
        $valor_pagamento->setNumericMask(2, ',', '.'); 
        $valor_total_parcial            = new TEntry('valor_total_parcial');
        $valor_total_parcial->setNumericMask(2, ',', '.');
        $valor_total_parcial->setEditable(FALSE);        
        $valor_saldo                    = new TEntry('valor_saldo');
        $valor_saldo->setNumericMask(2, ',', '.');
        $valor_saldo->setEditable(FALSE);        
        $data_cadastro                  = new TEntry('data_cadastro');
        $data_cadastro->setEditable(FALSE);        
        $data_cadastro->setMask('dd/mm/yyyy');        
        $data_cadastro->setValue(date('d/m/Y'));   
        $data_inicio                    = new TDate('data_inicio');
        $data_inicio->setMask('dd/mm/yyyy');
        $data_inicio_oculta             = new THidden('data_inicio_oculta');        
        $data_cancelamento                   = new TDate('data_cancelamento');
        $data_cancelamento->setMask('dd/mm/yyyy');        
        $data_encerramento                    = new TDate('data_encerramento');
        $data_encerramento->setMask('dd/mm/yyyy');              
        $data_prevista                  = new TDate('data_prevista');
        $data_prevista->setMask('dd/mm/yyyy');        
        $data_aprovacao                 = new TDate('data_aprovacao');
        $data_aprovacao->setMask('dd/mm/yyyy');
        $observacao                     = new TText('observacao');        
        $nome_dtr                       = new TEntry('nome_dtr');
        $nome_dtr->setEditable(FALSE);
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter("origem", "=", 1));
        $criteria->add(new TFilter("ativo", "=", 1));
        $criteria->add(new TFilter("codigo_cadastro_origem", "=", 100));
        $responsavel_id                 = new TDBCombo('responsavel_id', 'atividade', 'Pessoa', 'pessoa_codigo', 'pessoa_nome', 'pessoa_nome', $criteria);
        $tipo_ticket_id                 = new TDBCombo('tipo_ticket_id', 'atividade', 'TipoTicket', 'id', 'nome');
        $tipo_ticket_id->setDefaultOption(FALSE);
        $sistema_id                     = new TDBCombo('sistema_id', 'atividade', 'Sistema', 'id', 'nome');
        $status_ticket_id               = new TDBCombo('status_ticket_id', 'atividade', 'StatusTicket', 'id', 'nome');
        $status_ticket_id->setValue(2);
        $status_ticket_id->setEditable(FALSE);     
        $prioridade_id                  = new TDBCombo('prioridade_id', 'atividade', 'Prioridade', 'id', 'nome');
        $prioridade_id->setDefaultOption(FALSE);
        $prioridade_id->setValue(3);
        $combo_tipo_origens             = new TCombo('tipo_origens');
        $combo_tipo_origens->addItems(array(1 => 'Entidade', 2 => 'Estabelecimento', 3 => 'Empresa') );
        $combo_codigo_origem            = new TCombo('codigo_cadastro_origem');
        $combo_solicitante_id           = new TCombo('solicitante_id');
        
        try
        {
            TTransaction::open('atividade');
            $logado = Pessoa::retornaUsuario();
            TTransaction::close();
        }
        catch(Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
        }
        
        $logado_id                      = new THidden('logado_id');
        $logado_id->setValue($logado->pessoa_codigo);
        
        // define the sizes
        $id->setSize(100);
        $origem->setSize(200);
        $solicitacao_descricao->setSize(400, 180);
        $data_inicio->setSize(90);
        $data_encerramento->setSize(90);
        $data_cancelamento->setSize(90);       
        $providencia->setSize(400, 80);
        $orcamento_horas->setSize(100);
        $orcamento_valor_hora->setSize(100);
        $valor_desconto->setSize(100);
        $valor_total->setSize(100);
        $valor_saldo->setSize(121);
        $forma_pagamento->setSize(400);
        $data_ultimo_pgto->setSize(100);
        $data_pagamento->setSize(100);
        $valor_pagamento->setSize(121);
        $valor_ultimo_pgto->setSize(100);
        $valor_total_pago->setSize(100);
        $valor_total_parcial->setSize(121);
        $data_cadastro->setSize(100);
        $data_prevista->setSize(100);  
        $data_aprovacao->setSize(100);
        $observacao->setSize(400, 80);
        $nome_dtr->setSize(400);
        $titulo->setSize(390);
        $responsavel_id->setSize(390);
        $tipo_ticket_id->setSize(200);
        $sistema_id->setSize(200);
        $status_ticket_id->setSize(200);
        $prioridade_id->setSize(200);
        $combo_tipo_origens->setSize(135);
        $combo_codigo_origem->setSize(250);
        $combo_solicitante_id->setSize(390);
        
        // validações

        $titulo->addValidation('Titulo', new TRequiredValidator);
        $combo_solicitante_id->addValidation('Solicitante', new TRequiredValidator);
        $responsavel_id->addValidation('Responsável', new TRequiredValidator);
        $sistema_id->addValidation('Sistema', new TRequiredValidator);   
        $gerar_dr = TButton::create('gerar_dr', array('RequisitoDesenvolvimentoForm', 'onEdit'), 'Gerar DTR', 'ico_add.png');
        $editar_dr = TButton::create('editar_dr', array('RequisitoDesenvolvimentoForm', 'onEdit'), 'Editar DTR', 'ico_edit.png');
        $this->form->addField($gerar_dr);
        $this->form->addField($editar_dr);
        
        TButton::disableField('form_Ticket', 'gerar_dr');  
        TButton::disableField('form_Ticket', 'editar_dr');   
        
        // add one row for each form field
        // notebook Cadastramento
        $table->addRowSet( new TLabel('Ticket:'), array ($id,new TLabel('Data Cadastro' ),$data_cadastro) );

        $table->addRowSet( $label_combo_origem = new TLabel('Origem:'), array($combo_tipo_origens, $combo_codigo_origem) );
        $label_combo_origem->setFontColor('#FF0000'); 
        $table->addRowSet(  $label_solicitante = new TLabel('Solicitante:'), $combo_solicitante_id  );
        $label_solicitante->setFontColor('#FF0000'); 
        
        $table->addRowSet( $label_responsavel = new TLabel('Responsável:'), $responsavel_id );
        $label_responsavel->setFontColor('#FF0000');       
        $table->addRowSet( $label_titulo = new TLabel('Título:'), $titulo );
        $label_titulo->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('Data Inicio'), array ($data_inicio, $label_status = new TLabel('Status:') , $status_ticket_id ) );
        $label_status->setSize(70); 
        $table->addRowSet( new TLabel('Data Encerramento:'), array ($data_encerramento, $label_data_cancelamento = new TLabel('Data Cancelamento:'), $data_cancelamento ) );
        $label_data_cancelamento->setSize(160);
        $table->addRowSet( new TLabel('Prioridade:'), $prioridade_id );      
        $table->addRowSet( new TLabel('Origem:'), $origem );
        $table->addRowSet( new TLabel('Tipo Ticket:'), $tipo_ticket_id );
        $table->addRowSet( $label_sistema = new TLabel('Sistema:'), $sistema_id );
        $label_sistema->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('Descrição Solicitação:'), $solicitacao_descricao );
        $table->addRowSet( new TLabel('DR.:'), $nome_dtr );
        $table->addRowSet( new TLabel(''),  $gerar_dr );
        $table->addRowSet( new TLabel(''),  $data_inicio_oculta );
        
        // notebook Pagamento
        $tablePagamento->addRowSet( new TLabel('Data Prevista:'), $data_prevista );       
        $tablePagamento->addRowSet( new TLabel('Data Aprovação:'), $data_aprovacao );     
        $tablePagamento->addRowSet( new TLabel('Qte Horas:'), $orcamento_horas );
        $tablePagamento->addRowSet( new TLabel('Valor Hora:'), $orcamento_valor_hora );
        $tablePagamento->addRowSet( new TLabel('Valor Desconto:'), $valor_desconto );
        $tablePagamento->addRowSet( new TLabel('Valor Total:'), $valor_total );
        $tablePagamento->addRowSet( new TLabel('Forma de Pgto:'), $forma_pagamento );
        $tablePagamento->addRowSet( new TLabel('Descrição Providência:'), $providencia );
        $tablePagamento->addRowSet( new TLabel('Observação:'), $observacao );
      
        // creates a frame
        $frame = new TFrame;
        $frame->oid = 'frame-measures';
        $frame->setLegend('Pagamentos:');
        
        $row=$tablePagamento->addRow();
        $cell=$row->addCell($frame);
        $cell->colspan=2;
        
        $page2 = new TTable;
        $frame->add($page2);
       
        $page2->addRowSet( new TLabel('Valor Pgto:'), array( $valor_pagamento, $tamanho_label = new TLabel('Valor Ultimo Pgto:'), $valor_ultimo_pgto ) );
        $tamanho_label->setSize(150);
         
        $page2->addRowSet( new TLabel('Data Pgto:'), array( $data_pagamento, $tamanho_label = new TLabel('Data Ultimo Pgto:'), $data_ultimo_pgto ) );
        $tamanho_label->setSize(150);      
        
        $page2->addRowSet( new TLabel('Valor Total:'), array( $valor_total_parcial, $tamanho_label = new TLabel('Valor Total Pago: '), $valor_total_pago ) );
        $tamanho_label->setSize(150);
        
        $page2->addRowSet( new TLabel('Saldo a pagar:'), $valor_saldo);
        
        $tablePagamento->addRowSet( new TLabel(''), $logado_id );
        
        // Envia campos para o formulario
        $this->form->setFields(array($id,$titulo,$data_inicio,$data_inicio_oculta,$data_encerramento,$data_cancelamento,$origem,$solicitacao_descricao,$nome_dtr,
                                         $providencia,$orcamento_horas,$orcamento_valor_hora,$valor_desconto,$valor_total,$forma_pagamento,$data_ultimo_pgto,
                                         $valor_ultimo_pgto,$valor_total_pago,$data_cadastro,$data_prevista,$data_aprovacao,$observacao, $tipo_ticket_id,$sistema_id,
                                         $status_ticket_id,$prioridade_id,$responsavel_id, $valor_total_parcial, $valor_pagamento, $data_pagamento, $valor_saldo,
                                         $combo_tipo_origens,$combo_codigo_origem,$combo_solicitante_id, $logado_id));

        // create the form actions
        $save_button   = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $new_button    = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
        $del_button    = TButton::create('delete',  array($this, 'onDelete'), _t('Delete'),  'ico_delete.png');
        $list_button   = TButton::create('list', array('TicketList', 'onReload'), _t('List'), 'ico_datagrid.png');
        $enviar_email  = TButton::create('email', array($this, 'onEnviaEmail'), 'Enviar Email', 'ico_email.png');
        $sincronizar   = TButton::create('sincronizar', array($this, 'onSincronizarContatos'), 'Sincronizar Contatos', 'sincronizar.png');
        
        $this->form->addField($save_button);
        $this->form->addField($new_button);
        $this->form->addField($del_button);
        $this->form->addField($list_button);
        $this->form->addField($enviar_email);
        $this->form->addField($sincronizar);
                
        $subtable = new TTable;
        $row = $subtable->addRow();
        $row->addCell($save_button);
        $row->addCell($new_button);
        $row->addCell($del_button);
        $row->addCell($list_button);
        $row->addCell($enviar_email);
        $row->addCell($sincronizar);
        
        $pretable = new TTable;      
        $pretable->style = 'width: 100%';
        $row = $pretable->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Cadastro de Ticket') )->colspan = 2;
        
        $change_action = new TAction(array($this, 'onCalculaValorTotal'));
        $orcamento_horas->setExitAction($change_action);
        $orcamento_valor_hora->setExitAction($change_action);
        $valor_desconto->setExitAction($change_action);   
          
        $change_data_action = new TAction(array($this, 'onChangeDataAction'));
        $data_aprovacao->setExitAction($change_data_action); 
           
        $change_data_prev = new TAction(array($this, 'onChangeDataPrevista'));
        $data_prevista->setExitAction($change_data_prev);

        $change_data_pagamento = new TAction(array($this, 'onChangeDataPagamento'));
        $data_pagamento->setExitAction($change_data_pagamento);

        $change_valor = new TAction(array ($this, 'onCalculaValorParcial'));
        $valor_pagamento->setExitAction($change_valor);
        
        $change_status = new TAction(array ($this, 'onChangeDataInicio'));
        $data_inicio->setExitAction($change_status);
        
        $change_status = new TAction(array ($this, 'onChangeDataCancelamento'));
        $data_cancelamento->setExitAction($change_status);
        
        $change_status = new TAction(array ($this, 'onChangeDataEncerramento'));
        $data_encerramento->setExitAction($change_status);
        
        $change_origem = new TAction(array ($this, 'onChangeOrigem'));
        $combo_tipo_origens->setChangeAction($change_origem); 
        
        $change_tipo_origem = new TAction(array ($this, 'onChangeTipoOrigem'));
        $combo_codigo_origem->setChangeAction($change_tipo_origem); 
        
        $vbox = new TVBox;
        $vbox->add($pretable);
        $vbox->add($notebook);
        $vbox->add($subtable);
        
        $this->form->add($vbox);
                
        parent::add($this->form);
    }

    public static function onSetarValoresCombo($param)
    {
         
        $obj = new StdClass;
        
        $obj->tipo_origens                 = $param['tipo_origens'];     
    	$obj->codigo_cadastro_origem       = $param['codigo_cadastro_origem'];
        $obj->solicitante_id               = $param['solicitante_id'];
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
       
    }

    public static function onChangeTipoOrigem($param)
    {
        
        if($param['tipo_origens'] && $param['codigo_cadastro_origem'])
        {
            try
            {
                TTransaction::open('atividade');
                
                
                $repo = new TRepository('Pessoa');
                $criteria = new TCriteria;
                
                $criteria->add(new TFilter("ativo", "=", 1));
                
                $criteria->add(new TFilter('origem', '=', $param['tipo_origens']) );
                $criteria->add(new TFilter('codigo_cadastro_origem', '=', $param['codigo_cadastro_origem']) );
                
                $newparam['order'] = 'pessoa_nome';
                $newparam['direction'] = 'asc';
                $criteria->setProperties($newparam); // order, offset
                
                $pessoas = $repo->load($criteria);
               
                foreach($pessoas as $pessoa)
                {
                    $options [ $pessoa->pessoa_codigo ] = $pessoa->pessoa_nome;
                }
                
                TTransaction::close();
            
            }
            catch(Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
        
        }
        
        TCombo::reload('form_Ticket', 'solicitante_id', $options);
        
    }


    public static function onChangeOrigem($param)
    {
        
        if($param['tipo_origens'])
        {
            try
            {
                TTransaction::open('atividade');
                
                if($param['tipo_origens'] == 1)
                {
                    $repo = new TRepository('Entidade');
                    $criteria = new TCriteria;
                    $criteria->add( new TFilter('enttipent', '=', 1));
                    $newparam['order'] = 'entcodent';
                    $newparam['direction'] = 'asc';
                    $criteria->setProperties($newparam); // order, offset    
                    $entidades = $repo->load($criteria);
                    
                    $options[] = '--Selecione--';
                    foreach($entidades as $etd)
                    { 
                        $options [ $etd->entcodent.$selecao ] = str_pad($etd->entcodent, 4, "0", STR_PAD_LEFT).' - '.$etd->entnomfan;
                    }
                }
                
                if($param['tipo_origens'] == 2)
                {
                    $repo = new TRepository('Estabelecimento');
                    $criteria = new TCriteria;
                    $newparam['order'] = 'lojcodloj';
                    $newparam['direction'] = 'asc';
                    $criteria->setProperties($newparam); // order, offset    
                    $estabelecimentos = $repo->load($criteria);
                    
                    $options[] = '--Selecione--';
                    foreach($estabelecimentos as $ecs)
                    {
                        $options [ $ecs->lojcodloj ] = str_pad($ecs->lojcodloj, 4, "0", STR_PAD_LEFT).' - '.$ecs->lojnomfan;
                    }
                }
                
                if($param['tipo_origens'] == 3)
                {
                    $repo = new TRepository('Empresa');
                    $criteria = new TCriteria;
                    $newparam['order'] = 'id';
                    $newparam['direction'] = 'asc';
                    $criteria->setProperties($newparam); // order, offset    
                    $empresas = $repo->load($criteria);
                   
                    $options[] = '--Selecione--';
                    foreach($empresas as $emp)
                    {
                        $options [ $emp->id ] = str_pad($emp->id, 4, "0", STR_PAD_LEFT).' - '.$emp->razao_social;
                    }
                }
                
                TTransaction::close();
            
            }
            catch(Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
        
        }
        
        TCombo::reload('form_Ticket', 'solicitante_id', $opt);
        TCombo::reload('form_Ticket', 'codigo_cadastro_origem', $options);
        
    }

    public function onSincronizarContatos()
    {
        
        exec("php /var/www/vhosts/tecbiz.com.br/httpdocs/atividades/atualizacao.php");
        
        new TMessage('info', 'Contatos Sincronizados!');
        
        $this->onChangeOrigem();
        $this->onChangeTipoOrigem();
    
    }
    
    public function onEnviaEmail()
    {       
        try
        {
           TTransaction::open('atividade');
           $object = $this->form->getData('Ticket');
        
           $vars['tipo_origens']                 = $object->tipo_origens;
           $vars['codigo_cadastro_origem']       = $object->codigo_cadastro_origem;
           $vars['solicitante_id']               = $object->solicitante_id;
                
           $this->onChangeOrigem($vars);
           $this->onChangeTipoOrigem($vars);
           $this->onSetarValoresCombo($vars);
           $status = $object->status_ticket->nome;  
           $solicitante = new Pessoa($object->solicitante_id);
           $cliente = $solicitante->pessoa_nome;
           $email1 = $solicitante->email1;
           $empresa = $solicitante->origem_nome;
           $responsavel = new Pessoa($object->responsavel_id);
           $colaborador = $responsavel->pessoa_nome;
           $email2 = $responsavel->email1;
           $table = new TTable;
           $table->border = 0;
           $table1 = new TTable;
           $table1->border = 1;
           $table2 = new TTable;
           $table2->border = 1;
           $table3 = new TTable;
           $table3->border = 1;
           $table4 = new TTable;
           $table4->border = 1;
           
           $imagem = new TImage('app/images/tecbiz.jpg');
           $imagem->height=63;
           $imagem->width=96;
           
           $row = $table->addRow();
           $cell = $row->addCell( $imagem );
           $cell->style = 'width: 100px;';
           $cell = $row->addCell("Prezado <br /> {$cliente} do(a) {$empresa} <br /> Foi registrado um ticket com sua solicitação conforme os dados a seguir:");
           $cell->style = 'width: 700px;';
                  
           $row = $table->addRow();
           $row->addCell('<span style="color: DarkOliveGreen;"><b><u>Inicial:</b></u></span>');
           $row = $table1->addRow();
           $cell = $row->addCell('<b>No. Ticket:</b>');
           $cell->style = 'width: 200px;';
           $cell = $row->addCell($object->id);
           $cell->style = 'width: 600px;';
           $row = $table1->addRow();
           $row->addCell('<b>Título Ticket:</b>');
           $row->addCell($object->titulo);
           $row = $table1->addRow();
           $row->addCell('<b>Data/Hora:</b>');
           $row->addCell(date('d/m/Y H:i'));
           $row = $table1->addRow();
           $row->addCell('<b>Status:</b>');
           $row->addCell($status);
           $row = $table1->addRow();
           $row->addCell('<b>Solicitante:</b>');
           $row->addCell($cliente);
           $row = $table1->addRow();
           $row->addCell('<b>Colaborador TecBiz:</b>');
           $row->addCell($colaborador);
           
           $row = $table->addRow();
           $cell = $row->addCell($table1);
           $cell->colspan=2;
           
           $row = $table->addRow();
           $row->addCell('<span style="color: DarkOliveGreen;"><b><u>Solicitação:</b></u></span>');
           
           $row = $table2->addRow();
           $cell = $row->addCell('<b>Descrição:</b>');
           $cell->style = 'width: 200px;';
           $cell = $row->addCell($object->solicitacao_descricao);
           $cell->style = 'width: 520px;';
           $cell = $row->addCell($object->data_cadastro);
           $cell->style = 'width: 80px;';
           
           $row = $table->addRow();
           $cell = $row->addCell($table2);
           $cell->colspan=2;
           
           $row = $table->addRow();
           $row->addCell('<span style="color: DarkOliveGreen;"><b><u>Orçamento:</b></u></span>');
           $row = $table3->addRow();
           $cell = $row->addCell('<b>Horas orçadas:</b>');
           $cell->style = 'width: 200px;';
           $cell = $row->addCell($object->orcamento_horas);
           $cell->style = 'width: 600px;';
           $row = $table3->addRow();
           $row->addCell('<b>Valor Hora:</b>');
           $row->addCell('R$ '.$object->orcamento_valor_hora);
           $row = $table3->addRow();
           $row->addCell('<b>Valor Total:</b>');
           $row->addCell('R$ '.$object->valor_total);
           $row = $table3->addRow();
           $row->addCell('<b>Forma de pagamento:</b>');
           $row->addCell($object->forma_pagamento);
           $row = $table3->addRow();
                        
           $row = $table->addRow();
           $cell = $row->addCell($table3);
           $cell->colspan=2;
           
           $row = $table4->addRow();
           $cell = $row->addCell('<span style="color: red;"><b>Importante:</b></span> Para dar seguimento a esta solicitação será necessário a aprovação da descrição e orçamento deste Ticket');
           $cell->style = 'width: 800px;';
           $row = $table->addRow();
           $cell = $row->addCell($table4);
           $cell->colspan=2;
           TTransaction::close();
           
           $ini = parse_ini_file('app/config/email.ini');
           
           $mail = new TMail;
           $mail->setFrom($ini['from'], $ini['name']);
           $mail->setSubject('TecBiz criou um ticket para voce');
           $mail->setHtmlBody($table);
           $mail->addAddress($email1);
           $mail->addCC($email2);
           $mail->addBCC('suporte@tecbiz.com.br');
           // Se tiver anexo
           if (isset($target_file))
           {
           $mail->addAttach($target_file);
           }
           $mail->SetUseSmtp();
           $mail->SetSmtpHost($ini['host'], $ini['port']);
           $mail->SetSmtpUser($ini['user'], $ini['pass']);
           $mail->setReplyTo($ini['repl']);
           $mail->send();
           
           new TMessage('info', 'Email enviado com sucesso');
               
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
        
        $this->form->setData($object);
        
    }
    
     /**
     * Executed when user leaves the fields
     */

    public static function onChangeDataInicio($param)
    {
        $string = new StringsUtil;
        $obj = new StdClass;
       
        if(strlen($param['data_inicio']) == 10)
        {
          
            if($param['data_encerramento'])
            {
                $obj->data_inicio = '';
                $obj->data_inicio_oculta = '';
                new TMessage('error', 'Apague data de encerramento para inserir uma data de inicio');
            } 
            elseif($param['data_cancelamento'])
            {
                $obj->data_inicio = '';
                $obj->data_inicio_oculta = '';
                new TMessage('error', 'Apague data de cancelamento para inserir uma data de inicio');
            } 
            elseif( strtotime( $string->formatDate( $param['data_inicio'] ) ) > strtotime(date('Y-m-d') ) )
            {
        	     $obj->data_inicio = '';
        	     $obj->data_inicio_oculta = '';
        	     new TMessage('error', 'Data de inicio maior que data atual');
            } 
            elseif(strtotime($string->formatDate($param['data_cadastro'])) > strtotime($string->formatDate($param['data_inicio'])))
            {
        	     $obj->data_inicio = '';
        	     $obj->data_inicio_oculta = '';
        	     new TMessage('error', 'Data de inicio menor que data de cadastro');
            } 
            else 
            {
                 $obj->status_ticket_id = 1;     
                 $obj->data_inicio_oculta = $param['data_inicio'];
            }
        }
        elseif(!$param['data_inicio'])
        {
            if(!$param['data_encerramento'] && !$param['data_cancelamento'])
            {
                 $obj->status_ticket_id = 2;
                 $obj->data_inicio_oculta = '';                  
            }
            elseif($param['data_encerramento'] && !$param['data_cancelamento'])
            {
                $obj->data_inicio = $param['data_inicio_oculta']; 
                new TMessage('error', 'Não pode haver data de encerramento sem data de inicio');
            }
        }    
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);      
    }

   public static function onChangeDataEncerramento($param)
    {   
        $string = new StringsUtil;
        $obj = new StdClass;
           
        if(strlen($param['data_encerramento']) == 10)
        {
            if($param['responsavel_id'] == $param['logado_id'])
            {
                if($param['data_cancelamento'])
                {
                    $obj->status_ticket_id = 4; 
                    $obj->data_encerramento = '';
                    new TMessage('error', 'Apague data de cancelamento para inserir uma data de encerramento');
                }
                elseif(!$param['data_inicio'])
                {
                    $obj->status_ticket_id = 2; 
                    $obj->data_encerramento = '';
                    new TMessage('error', 'Deve haver uma data de inicio para inserir uma data de encerramento');
                }
                elseif( strtotime( $string->formatDate( $param['data_encerramento'] ) ) > strtotime(date('Y-m-d') ) )
                {
            	     $obj->status_ticket_id = 1;
            	     $obj->data_encerramento = '';
            	     new TMessage('error', 'Data de encerramento maior que data atual');
                } 
                elseif(strtotime($string->formatDate($param['data_inicio'])) > strtotime($string->formatDate($param['data_encerramento'])))
                {
            	     $obj->status_ticket_id = 1; 
            	     $obj->data_encerramento = '';
            	     new TMessage('error', 'Data de inicio maior que data de encerramento');
                }
                else
                {
                     $obj->status_ticket_id = 3; 
                }
            }
            else
            {
                $obj->data_encerramento = '';
                new TMessage('error', 'Somente responsavel pode encerrar Ticket');
            }
        }
        elseif(!$param['data_encerramento'])
        {
            if($param['data_cancelamento'])
            {
                $obj->status_ticket_id = 4; 
            }
            elseif($param['data_inicio'])
            {
                $obj->status_ticket_id = 1; 
            }
            else
            {
                $obj->status_ticket_id = 2; 
            }
        }
    
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
       
    }

   public static function onChangeDataCancelamento($param)
    {     
        $obj = new StdClass;
        $string = new StringsUtil;
        
        if(strlen($param['data_cancelamento']) == 10)
        {
            if($param['responsavel_id'] == $param['logado_id'])
            {
                if( strtotime( $string->formatDate( $param['data_cancelamento'] ) ) > strtotime(date('Y-m-d') ) )
                {
            	     $obj->data_cancelamento = '';
            	     $obj->status_ticket_id = 2;
            	     $param['data_inicio'] ? $obj->status_ticket_id = 1 : 2;
            	     $param['data_encerramento'] ? $obj->status_ticket_id = 3 : 2;   
            	     new TMessage('error', 'Data de cancelamento maior que data atual');
                }
                elseif( ($param['data_encerramento'] ) && ( strtotime( $string->formatDate( $param['data_cancelamento'] ) ) < strtotime( $string->formatDate( $param['data_encerramento'] ) )  )   )
                {
                     $obj->data_cancelamento = '';
                     $obj->status_ticket_id = 3; 
            	     new TMessage('error', 'Data de cancelamento maior que data de encerramento');
                }
                elseif( ($param['data_inicio'] ) && ( strtotime( $string->formatDate( $param['data_cancelamento'] ) ) < strtotime( $string->formatDate( $param['data_inicio'] ) )  )   )
                {
                     $obj->data_cancelamento = '';
                     $obj->status_ticket_id = 1; 
            	     new TMessage('error', 'Data de cancelamento maior que data de inicio');
                }
                elseif( strtotime( $string->formatDate( $param['data_cancelamento'] ) ) < strtotime( $string->formatDate( $param['data_cadastro'] ) ) )
                {
                     $obj->data_cancelamento = '';
                     $obj->status_ticket_id = 2; 
            	     new TMessage('error', 'Data de cancelamento maior que data de cadastro');
                }
                else
                {
                    $obj->status_ticket_id = 4;
                }
            }
            else
            {
                $obj->data_cancelamento = '';
                new TMessage('error', 'Somente responsavel pode cancelar Ticket');
            }
        }
        elseif(!$param['data_cancelamento'])
        {
            if($param['data_encerramento'])
            {
                $obj->status_ticket_id = 3;
            }
            elseif($param['data_inicio'])
            {
                $obj->status_ticket_id = 1;
            }
            else
            {
                $obj->status_ticket_id = 2;
            }
        }           
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
       
    }

    public static function onChangeDataPagamento($param)
    {
         
        $obj = new StdClass;
        $string = new StringsUtil;
        
        $hoje                           = date('d/m/Y');
        $data_pagamento                 = $param['data_pagamento'];
        
        if(strtotime($string->formatDate($data_pagamento)) > strtotime($string->formatDate($hoje)))
        {
    	     
    	     $obj->data_pagamento = '';
    	     new TMessage('error', 'Data pagamento maior que data atual');
        }
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
       
    }
    
    public static function onChangeDataPrevista($param)
    {
         
        $obj = new StdClass;
        $string = new StringsUtil;
        
        $hoje                           = date('d/m/Y');
        $data_prevista                  = $param['data_prevista'];
        
        if(strtotime($string->formatDate($data_prevista)) < strtotime($string->formatDate($hoje)))
        {
    	     $obj->data_prevista = ''; 
    	     new TMessage('error', 'Data prevista menor que data atual');
    	     
        }
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
       
    }
    
     
    public static function onChangeDataAction($param)
    {
        $obj = new StdClass;
        $string = new StringsUtil;
        
        $data_prevista                  = $param['data_prevista'];     
        $data_aprovacao                 = $param['data_aprovacao'];
        

      if ($data_aprovacao and $data_prevista)
        {
        
            if(strtotime($string->formatDate($data_aprovacao)) > strtotime($string->formatDate($data_prevista)))
            {
    	         $obj->data_aprovacao = '';
    	         new TMessage('error', 'Data de aprovação não pode ser maior que a data prevista');
            }
                              
        }
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
        
    }
    
    public static function onCalculaValorParcial($param)
    {
        $obj = new StdClass;
        $string = new StringsUtil;
        
        $data_pagamento       = $param['data_pagamento'];
        $valor_total          = $param['valor_total_pago'];
        $valor_total_parcial  = $param['valor_total_parcial'];
        
        if(isset($param['valor_pagamento']))
        {
            
            if($string->desconverteReais($param['valor_pagamento']) > $string->desconverteReais($param['valor_saldo']))
            {
                new TMessage('error', 'Valor de pagamento não pode ser superior ao saldo devedor');
                $obj->valor_pagamento = '';
            }
            else
            {
                $horas = $param['orcamento_horas'];
                $valor = $string->desconverteReais($valor_total) + $string->desconverteReais($param['valor_pagamento']);
                $saldo = $string->desconverteReais($param['valor_total']) - $valor;
                $obj->valor_total_parcial = number_format($valor, 2, ',', '.');
                $obj->valor_saldo         = number_format($saldo, 2, ',', '.');
            }
           
            TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
            
        }
        
    }
     
    public static function onCalculaValorTotal($param)
    {
        $obj = new StdClass;
        $string = new StringsUtil;
        
        $horas     = 0;
        $valor     = 0;
        $desconto  = 0;
        if(isset($param['orcamento_horas']))
        {
            $horas = $param['orcamento_horas'];
        }
        
        if(isset($param['orcamento_valor_hora']))
        {
            $valor = $string->desconverteReais($param['orcamento_valor_hora']);
        }
        
        if(isset($param['valor_desconto']))
        {
            $desconto = $string->desconverteReais($param['valor_desconto']);
        }
        
        if($desconto > ($horas * $valor))
        {
            new TMessage('info', 'Valor de desconto maior que o valor total do Ticket');
            $desconto             = 0;
            $obj->valor_desconto  = 0;
        }
        
        $valor_total = ($horas * $valor) - $desconto;
 
        $obj->valor_total = number_format($valor_total, 2, ',', '.');
        
        //calcular valor_saldo

        !$param['valor_total_pago'] ? $param['valor_total_pago'] = 0 : $string->desconverteReais($param['valor_total_pago']);
        
        $valor_saldo = $valor_total - $param['valor_total_pago'];
        
        $obj->valor_saldo = number_format($valor_saldo, 2, ',', '.');
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
      
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        $string = new StringsUtil;
        try
        {
            TTransaction::open('atividade'); // open a transaction
            
            // get the form data into an active record Ticket
            $object = $this->form->getData('Ticket');
                  
            $validador = new TUltiPgtoValidator;
            $validador->validate('Ultimo pagamento', '', array($object->valor_pagamento, $object->data_pagamento ));
            
            !$object->data_cadastro ? $object->data_cadastro = date('Y-m-d') : $object->data_cadastro = $string->formatDate($object->data_cadastro);
                        
            $object->data_prevista ? $object->data_prevista = $string->formatDate($object->data_prevista) : null;
            
            if($object->status_ticket_id == 5)
            {
                $object->data_inicio = $object->data_inicio_oculta;
            }
            
            $object->data_inicio ? $object->data_inicio = $string->formatDate($object->data_inicio) : null;
            $object->data_encerramento ? $object->data_encerramento = $string->formatDate($object->data_encerramento) : null;
            $object->data_cancelamento ? $object->data_cancelamento = $string->formatDate($object->data_cancelamento) : null;
            
            $object->data_aprovacao ? $object->data_aprovacao = $string->formatDate($object->data_aprovacao) : null;
            
            $object->data_ultimo_pgto ? $object->data_ultimo_pgto = $string->formatDate($object->data_ultimo_pgto) : null;
            $object->data_pagamento ? $object->data_ultimo_pgto = $string->formatDate($object->data_pagamento) : null;
                                                
            $object->orcamento_horas ? $object->orcamento_horas = $object->orcamento_horas.':00:00' : null;
            
            $object->orcamento_valor_hora ? $object->orcamento_valor_hora = $string->desconverteReais($object->orcamento_valor_hora) : null;
            $object->valor_desconto ? $object->valor_desconto = $string->desconverteReais($object->valor_desconto) : null;
            $object->valor_total ? $object->valor_total = $string->desconverteReais($object->valor_total) : null;
            
            $object->valor_ultimo_pgto ? $object->valor_ultimo_pgto = $string->desconverteReais($object->valor_ultimo_pgto) : null;
            $object->valor_total_pago ? $object->valor_total_pago = $string->desconverteReais($object->valor_total_pago) : null;
                        
            if($object->valor_pagamento)
            {
                $object->valor_ultimo_pgto = $string->desconverteReais($object->valor_pagamento);
                $object->valor_total_pago += $string->desconverteReais($object->valor_pagamento);
            }
            
            $vars['tipo_origens']                 = $object->tipo_origens;
            $vars['codigo_cadastro_origem']       = $object->codigo_cadastro_origem;
            $vars['solicitante_id']               = $object->solicitante_id;
                    
            $this->onChangeOrigem($vars);
            $this->onChangeTipoOrigem($vars);
            
            $this->onSetarValoresCombo($vars);
            
            $this->form->validate(); // form validation
            
            $object->store(); // stores the object
            
            $saldo = $object->valor_total - $object->valor_total_pago;
            $object->valor_saldo = number_format($saldo, 2, ',', '.');
            
            $object->data_cadastro ? $object->data_cadastro = $string->formatDateBR($object->data_cadastro) : null;
            $object->data_prevista ? $object->data_prevista = $string->formatDateBR($object->data_prevista) : null;

            $object->data_inicio ? $object->data_inicio = $string->formatDateBR($object->data_inicio) : null;
            $object->data_encerramento ? $object->data_encerramento = $string->formatDateBR($object->data_encerramento) : null;
            $object->data_cancelamento ? $object->data_cancelamento = $string->formatDateBR($object->data_cancelamento) : null;

            $object->data_aprovacao ? $object->data_aprovacao = $string->formatDateBR($object->data_aprovacao) : null;
            $object->data_ultimo_pgto ? $object->data_ultimo_pgto = $string->formatDateBR($object->data_ultimo_pgto) : null; 
            
            $object->orcamento_horas ? $object->orcamento_horas = strstr($object->orcamento_horas, ':', true) : null;
            
            $object->orcamento_valor_hora ? $object->orcamento_valor_hora = number_format($object->orcamento_valor_hora, 2, ',', '.') : null;
            $object->valor_desconto ? $object->valor_desconto = number_format($object->valor_desconto, 2, ',', '.') : null;
            $object->valor_total ? $object->valor_total = number_format($object->valor_total, 2, ',', '.') : null;
            
            $object->valor_ultimo_pgto ? $object->valor_ultimo_pgto = number_format($object->valor_ultimo_pgto, 2, ',', '.') : null;
            $object->valor_total_pago ? $object->valor_total_pago = number_format($object->valor_total_pago, 2, ',', '.') : null;
            
            $dtr = Ticket::getDesenvolvimentoTicket($object->id);
            if(!$dtr)
            {
                if($object->tipo_ticket_id == 4 or $object->tipo_ticket_id == 5 or $object->tipo_ticket_id == 6)
                {
                    TButton::enableField('form_Ticket', 'gerar_dr');
                }
                
            }
            else
            {
                TButton::enableField('form_Ticket', 'editar_dr');
            }
            
            TButton::disableField('form_Ticket', 'delete');
            if($object->status_ticket_id == 2 and !$object->data_aprovacao and !$object->getAtividades() and !$object->data_ultimo_pgto)
            {
                TButton::enableField('form_Ticket', 'delete');
            }        
                        
            if($object->status_ticket_id == 5)
            {
                TDate::disableField('form_Ticket', 'data_inicio');
                TDate::disableField('form_Ticket', 'data_encerramento');
                TDate::disableField('form_Ticket', 'data_cancelamento');
            }
            
            $this->form->setData($object); // keep form data
            TTransaction::close(); // close the transaction
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        $string = new StringsUtil;
        try
        {
            TTransaction::open('atividade'); // open a transaction
            if (isset($param['key']))
            {
                $key=$param['key'];  // get the parameter $key

                $object = new Ticket($key); // instantiates the Active Record
                $object->nome_dtr = Ticket::getDesenvolvimentoTicket($key);
                
                if($object->nome_dtr)
                {
                    TButton::disableField('form_Ticket', 'gerar_dr');
                    TButton::enableField('form_Ticket', 'editar_dr');
                }
                
                if($object->tipo_ticket_id == 4 or $object->tipo_ticket_id == 5 or $object->tipo_ticket_id == 6)
                {
                    if(!$object->nome_dtr)
                    {
                        TButton::enableField('form_Ticket', 'gerar_dr');
                    }
                }
                
                TButton::disableField('form_Ticket', 'delete');
                if($object->status_ticket_id == 2 and !$object->data_aprovacao and !$object->getAtividades())
                {
                   TButton::enableField('form_Ticket', 'delete');
                }
                
                if($object->status_ticket_id == 5)
                {
                    TDate::disableField('form_Ticket', 'data_inicio');
                    TDate::disableField('form_Ticket', 'data_encerramento');
                    TDate::disableField('form_Ticket', 'data_cancelamento');
                }
                                              
                $object->data_cadastro ? $object->data_cadastro = $string->formatDateBR($object->data_cadastro) : null;
                $object->data_prevista ? $object->data_prevista = $string->formatDateBR($object->data_prevista) : null;
 
                $object->data_inicio ? $object->data_inicio = $string->formatDateBR($object->data_inicio) : null;
                $object->data_inicio ? $object->data_inicio_oculta = $object->data_inicio : null;                
                $object->data_encerramento ? $object->data_encerramento = $string->formatDateBR($object->data_encerramento) : null;
                $object->data_cancelamento ? $object->data_cancelamento = $string->formatDateBR($object->data_cancelamento) : null;
 
                $object->data_aprovacao ? $object->data_aprovacao = $string->formatDateBR($object->data_aprovacao) : null;
                $object->data_ultimo_pgto ? $object->data_ultimo_pgto = $string->formatDateBR($object->data_ultimo_pgto) : null;
                
                $saldo = $object->valor_total - $object->valor_total_pago;
                $saldo ? $object->valor_saldo = number_format($saldo, 2, ',', '.') : null;
                
                $object->orcamento_valor_hora ? $object->orcamento_valor_hora = number_format($object->orcamento_valor_hora, 2, ',', '.') : null;
                $object->valor_desconto ? $object->valor_desconto = number_format($object->valor_desconto, 2, ',', '.') : null;
                $object->valor_total ? $object->valor_total = number_format($object->valor_total, 2, ',', '.') : null;
            
                $object->valor_ultimo_pgto ? $object->valor_ultimo_pgto = number_format($object->valor_ultimo_pgto, 2, ',', '.') : null;
                $object->valor_total_pago ? $object->valor_total_pago = number_format($object->valor_total_pago, 2, ',', '.') : null;
                
                $object->orcamento_horas ? $object->orcamento_horas = strstr($object->orcamento_horas, ':', true) : null;
              
                
                if($object->solicitante_id)
                {
                    $pessoa = new Pessoa($object->solicitante_id);
                    
                    $vars['tipo_origens']                 = $pessoa->origem;
                    $vars['codigo_cadastro_origem']       = $pessoa->codigo_cadastro_origem;
                    $vars['solicitante_id']               = $pessoa->pessoa_codigo;
                    
                    $this->onChangeOrigem($vars);
                    $this->onChangeTipoOrigem($vars);    
                }
                               
                $this->form->setData($object); // fill the form
                
                $this->onSetarValoresCombo($vars);
            }
            else
            {
                
                $object = new Ticket();
                $object->data_cadastro   = date('d/m/Y');
                $this->form->setData($object);
                
            }
            TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
     /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            $key=$param['id']; // get the parameter $key
            TTransaction::open('atividade'); // open a transaction with database
            $object = new Ticket($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $action = new TAction(array('TicketList', 'onReload'));
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
     
}
