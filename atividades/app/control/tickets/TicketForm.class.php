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
        $table->style = 'width: 540px';
        $tablePagamento = new TTable;
        $tablePagamento->style = 'width: 540px';
        
        $notebook = new TNotebook(600, 650);
        $notebook->appendPage('Ticket - Cadastramento', $table);
        $notebook->appendPage('Ticket - Orçamento / Pagamento', $tablePagamento);
       
        // create the form fields
        $id                             = new TEntry('id');
        $id->setEditable(FALSE);
        $titulo                         = new TEntry('titulo');
        $origem                         = new TCombo('origem');
        $combo_origem = array();
        $combo_origem['I'] = 'INTERNO';
        $combo_origem['E'] = 'EXTERNO';
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
        
        $data_prevista                  = new TDate('data_prevista');
        $data_prevista->setMask('dd/mm/yyyy');
        $data_validade                  = new TDate('data_validade');
        $data_validade->setMask('dd/mm/yyyy');
        $data_aprovacao                 = new TDate('data_aprovacao');
        $data_aprovacao->setMask('dd/mm/yyyy');
        $observacao                     = new TText('observacao');
        
        $nome_dtr                       = new TEntry('nome_dtr');
        $nome_dtr->setEditable(FALSE);
        
        $criteria = new TCriteria;
        $newparam['order'] = 'pessoa_nome';
        $newparam['direction'] = 'asc';
        $criteria->setProperties($newparam); // order, offset
        
        $solicitante_id                 = new TDBSeekButton('solicitante_id', 'tecbiz','form_Ticket','Pessoa','pessoa_nome','solicitante_id', 'solicitante_nome', $criteria);
        $solicitante_nome               = new TEntry('solicitante_nome');
        $solicitante_nome->setEditable(FALSE);
                        
        $criteria = new TCriteria;
        $criteria->add(new TFilter("origem", "=", 1));
        $criteria->add(new TFilter("codigo_cadastro_origem", "=", 100));
        $responsavel_id                 = new TDBCombo('responsavel_id', 'tecbiz', 'Pessoa', 'pessoa_codigo', 'pessoa_nome', 'pessoa_nome', $criteria);

        $tipo_ticket_id                 = new TDBCombo('tipo_ticket_id', 'atividade', 'TipoTicket', 'id', 'nome');
        $tipo_ticket_id->setDefaultOption(FALSE);
        $sistema_id                     = new TDBCombo('sistema_id', 'atividade', 'Sistema', 'id', 'nome');
        $sistema_id->setDefaultOption(FALSE);
        $status_ticket_id               = new TDBCombo('status_ticket_id', 'atividade', 'StatusTicket', 'id', 'nome');
        $status_ticket_id->setDefaultOption(FALSE);
        $prioridade_id                  = new TDBCombo('prioridade_id', 'atividade', 'Prioridade', 'id', 'nome');
        $prioridade_id->setDefaultOption(FALSE);

        // define the sizes
        $id->setSize(100);
        $titulo->setSize(300);
        $origem->setSize(200);
        $solicitacao_descricao->setSize(400, 80);
        $providencia->setSize(400, 80);
        $orcamento_horas->setSize(100);
        $orcamento_valor_hora->setSize(100);
        $valor_desconto->setSize(100);
        $valor_total->setSize(100);
        $valor_saldo->setSize(121);
        $forma_pagamento->setSize(300);
        $data_ultimo_pgto->setSize(100);
        $data_pagamento->setSize(100);
        $valor_pagamento->setSize(121);
        $valor_ultimo_pgto->setSize(100);
        $valor_total_pago->setSize(100);
        $valor_total_parcial->setSize(121);
        $data_cadastro->setSize(100);
        $data_prevista->setSize(100);
        $data_validade->setSize(100);
        $data_aprovacao->setSize(100);
        $observacao->setSize(400, 80);
        $nome_dtr->setSize(400);
        $solicitante_id->setSize(40);
        $responsavel_id->setSize(300);
        $tipo_ticket_id->setSize(200);
        $sistema_id->setSize(200);
        $status_ticket_id->setSize(200);
        $prioridade_id->setSize(200);

        // validações
        $solicitante_id->addValidation('Solicitante', new TRequiredValidator);
        $titulo->addValidation('Titulo', new TRequiredValidator);
        $responsavel_id->addValidation('Responsável', new TRequiredValidator);
        
        $gerar_dr = TButton::create('gerar_dr', array('RequisitoDesenvolvimentoForm', 'onEdit'), 'Gerar DTR', 'ico_add.png');
        $editar_dr = TButton::create('editar_dr', array('RequisitoDesenvolvimentoForm', 'onEdit'), 'Editar DTR', 'ico_edit.png');
        $this->form->addField($gerar_dr);
        $this->form->addField($editar_dr);
        
        TButton::disableField('form_Ticket', 'gerar_dr');  
        TButton::disableField('form_Ticket', 'editar_dr');   
        
        // add one row for each form field
        // notebook Cadastramento
        $table->addRowSet( new TLabel('Ticket:'), $id );
        $table->addRowSet( $label_solicitante = new TLabel('Solicitante:'), array($solicitante_id, $solicitante_nome) );
        $label_solicitante->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('Data:'), $data_cadastro );
        $table->addRowSet( $label_titulo = new TLabel('Título:'), $titulo );
        $label_titulo->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('Origem:'), $origem );
        $table->addRowSet( new TLabel('Tipo Ticket:'), $tipo_ticket_id );
        $table->addRowSet( new TLabel('Sistema:'), $sistema_id );
        $table->addRowSet( new TLabel('Status:'), $status_ticket_id );
        $table->addRowSet( new TLabel('Prioridade:'), $prioridade_id );
        $table->addRowSet( new TLabel('Descrição Solicitação:'), $solicitacao_descricao );
        $table->addRowSet( new TLabel('Descrição Providência:'), $providencia );
        $table->addRowSet( new TLabel('Observação:'), $observacao );
        $table->addRowSet( new TLabel('DR.:'), $nome_dtr );
        $table->addRowSet( new TLabel(''),  $gerar_dr );
        
        // notebook Pagamento
        $tablePagamento->addRowSet( $label_responsavel = new TLabel('Responsável:'), $responsavel_id );
        $label_responsavel->setFontColor('#FF0000');
        $tablePagamento->addRowSet( new TLabel('Data Prevista:'), $data_prevista );
        $tablePagamento->addRowSet( new TLabel('Data Validade:'), $data_validade );
        $tablePagamento->addRowSet( new TLabel('Data Aprovação:'), $data_aprovacao );     
        $tablePagamento->addRowSet( new TLabel('Qte Horas:'), $orcamento_horas );
        $tablePagamento->addRowSet( new TLabel('Valor Hora:'), $orcamento_valor_hora );
        $tablePagamento->addRowSet( new TLabel('Valor Desconto:'), $valor_desconto );
        $tablePagamento->addRowSet( new TLabel('Valor Total:'), $valor_total );
        $tablePagamento->addRowSet( new TLabel('Forma de Pgto:'), $forma_pagamento );
      
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
        
        // Envia campos para o formulario
        $this->form->setFields(array($id,$titulo,$origem,$solicitacao_descricao,$nome_dtr,$providencia,$orcamento_horas,$orcamento_valor_hora,$valor_desconto,$valor_total,$forma_pagamento,$data_ultimo_pgto,$valor_ultimo_pgto,$valor_total_pago,$data_cadastro,$data_prevista,$data_validade,$data_aprovacao,$observacao,$solicitante_id,$solicitante_nome, $tipo_ticket_id,$sistema_id,$status_ticket_id,$prioridade_id,$responsavel_id, $valor_total_parcial, $valor_pagamento, $data_pagamento, $valor_saldo));

        // create the form actions
        $save_button   = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $new_button    = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
        $del_button    = TButton::create('delete',  array($this, 'onDelete'), _t('Delete'),  'ico_delete.png');
        $list_button   = TButton::create('list', array('TicketList', 'onReload'), _t('List'), 'ico_datagrid.png');
        $enviar_email  = TButton::create('email', array($this, 'onEnviaEmail'), 'Enviar Email', 'ico_email.png');
        
        $this->form->addField($save_button);
        $this->form->addField($new_button);
        $this->form->addField($del_button);
        $this->form->addField($list_button);
        $this->form->addField($enviar_email);
                
        $subtable = new TTable;
        $row = $subtable->addRow();
        $row->addCell($save_button);
        $row->addCell($new_button);
        $row->addCell($del_button);
        $row->addCell($list_button);
        $row->addCell($enviar_email);
        
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
        $data_validade->setExitAction($change_data_action);
        $data_aprovacao->setExitAction($change_data_action);
        
        $change_data_prev = new TAction(array($this, 'onChangeDataPrevista'));
        $data_prevista->setExitAction($change_data_prev);

        $change_data_pagamento = new TAction(array($this, 'onChangeDataPagamento'));
        $data_pagamento->setExitAction($change_data_pagamento);

        $change_valor = new TAction(array ($this, 'onCalculaValorParcial'));
        $valor_pagamento->setExitAction($change_valor);
        
        
        $vbox = new TVBox;
        $vbox->add($pretable);
        $vbox->add($notebook);
        $vbox->add($subtable);
        
        $this->form->add($vbox);
                
        parent::add($this->form);
    }

    public function onEnviaEmail()
    {
        
        try
        {
            
               $object = $this->form->getData('Ticket');
            
               TTransaction::open('atividade');
               $status = $object->status_ticket->nome;  
               TTransaction::close();
               
               TTransaction::open('tecbiz');
               $solicitante = new Pessoa($object->solicitante_id);
               $cliente = $solicitante->pessoa_nome;
               $email1 = $solicitante->email1;
               $empresa = $solicitante->origem_nome;
               $responsavel = new Pessoa($object->responsavel_id);
               $colaborador = $responsavel->pessoa_nome;
               $email2 = $responsavel->email1;
               TTransaction::close();
               
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
               $row->addCell('<b>Validade:</b>');
               $row->addCell($object->data_validade);
               $row = $table->addRow();
               $cell = $row->addCell($table3);
               $cell->colspan=2;
               
               $row = $table4->addRow();
               $cell = $row->addCell('<span style="color: red;"><b>Importante:</b></span> Para dar seguimento a esta solicitação será necessário a aprovação da descrição e orçamento deste Ticket');
               $cell->style = 'width: 800px;';
               $row = $table->addRow();
               $cell = $row->addCell($table4);
               $cell->colspan=2;
               
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
        $data_validade                  = $param['data_validade'];
        $data_aprovacao                 = $param['data_aprovacao'];
        
        if($data_prevista and $data_validade)
        {
        
            if(strtotime($string->formatDate($data_prevista)) < strtotime($string->formatDate($data_validade)))
            {
    	         $obj->data_validade = '';
    	         new TMessage('error', 'Data validade deve ser menor que a data previsa');
    	         
            }
                              
        }
        
        if($data_aprovacao and $data_validade)
        {
        
            if(strtotime($string->formatDate($data_aprovacao)) > strtotime($string->formatDate($data_validade)))
            {
    	         $obj->data_aprovacao = '';
    	         new TMessage('error', 'Data validade deve ser maior que a data de aprovação');
    	         
            }
                              
        } 
        elseif ($data_aprovacao and $data_prevista)
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
            $object->data_validade ? $object->data_validade = $string->formatDate($object->data_validade) : null;
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
            
            $this->form->validate(); // form validation
            $object->store(); // stores the object
            
            $saldo = $object->valor_total - $object->valor_total_pago;
            $object->valor_saldo = number_format($saldo, 2, ',', '.');
            
            $object->data_cadastro ? $object->data_cadastro = $string->formatDateBR($object->data_cadastro) : null;
            $object->data_prevista ? $object->data_prevista = $string->formatDateBR($object->data_prevista) : null;
            $object->data_validade ? $object->data_validade = $string->formatDateBR($object->data_validade) : null;
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
            if($object->status_ticket_id == 1 and !$object->data_aprovacao and !$object->getAtividades())
            {
                TButton::enableField('form_Ticket', 'delete');
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
            if (isset($param['key']))
            {
                $key=$param['key'];  // get the parameter $key
                TTransaction::open('atividade'); // open a transaction
                $object = new Ticket($key); // instantiates the Active Record
                $object->nome_dtr = Ticket::getDesenvolvimentoTicket($key);
                
                if($object->nome_dtr)
                {
                    TButton::disableField('form_Ticket', 'gerar_dr');
                    TButton::enableField('form_Ticket', 'editar_dr');
                }
                
                if($object->tipo_ticket_id == 4 or $object->tipo_ticket_id == 5 or $object->tipo_ticket_id == 6)
                {
                    TButton::enableField('form_Ticket', 'gerar_dr');
                }
                
                TButton::disableField('form_Ticket', 'delete');
                if($object->status_ticket_id == 1 and !$object->data_aprovacao and !$object->getAtividades())
                {
                   TButton::enableField('form_Ticket', 'delete');
                }
                                              
                $object->data_cadastro ? $object->data_cadastro = $string->formatDateBR($object->data_cadastro) : null;
                $object->data_prevista ? $object->data_prevista = $string->formatDateBR($object->data_prevista) : null;
                $object->data_validade ? $object->data_validade = $string->formatDateBR($object->data_validade) : null;
                $object->data_aprovacao ? $object->data_aprovacao = $string->formatDateBR($object->data_aprovacao) : null;
                $object->data_ultimo_pgto ? $object->data_ultimo_pgto = $string->formatDateBR($object->data_ultimo_pgto) : null;
                
                $saldo = $object->valor_total - $object->valor_total_pago;
                $saldo ? $object->valor_saldo = number_format($saldo, 2, ',', '.') : null;
                
                $object->orcamento_valor_hora ? $object->orcamento_valor_hora = number_format($object->orcamento_valor_hora, 2, ',', '.') : null;
                $object->valor_desconto ? $object->valor_desconto = number_format($object->valor_desconto, 2, ',', '.') : null;
                $object->valor_total ? $object->valor_total = number_format($object->valor_total, 2, ',', '.') : null;
            
                $object->valor_ultimo_pgto ? $object->valor_ultimo_pgto = number_format($object->valor_ultimo_pgto, 2, ',', '.') : null;
                $object->valor_total_pago ? $object->valor_total_pago = number_format($object->valor_total_pago, 2, ',', '.') : null;
                
                if($object->solicitante_id)
                {
                    TTransaction::open('tecbiz');
                    
                    $pessoa = new Pessoa($object->solicitante_id);
                    $object->solicitante_nome = $pessoa->pessoa_nome;
                    
                    TTransaction::close();    
                }
                
                $object->orcamento_horas ? $object->orcamento_horas = strstr($object->orcamento_horas, ':', true) : null;
                
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                
                $object = new Ticket();
                $object->data_cadastro   = date('d/m/Y');
                $this->form->setData($object);
                
            }
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
