<?php
/**
 * TicketReport Report
 * @author  <your name here>
 */
class TicketReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
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
        $this->form->style = 'width: 500px';
        
        // creates the table container
        $table = new TTable;
        $table->width = '110%';
        
        // add the table inside the form
        $this->form->add($table);
        
        // define the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Tickets - Resumo e atividades') )->colspan = 3;
        
        // create the form fields
        $ticket                         = new TEntry('ticket_id');
        $ticket->setMask('99999');
        $solicitante_id                 = new TSeekButton('solicitante_id');
        $solicitante_nome               = new TEntry('solicitante_nome');
		$obj                            = new TicketPessoaSeek;
        $action                         = new TAction(array($obj, 'onReload'));
        $solicitante_id->setAction($action);      
        $solicitante_nome->setEditable(FALSE);
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter("origem", "=", 1));
        $criteria->add(new TFilter("ativo", "=", 1));
        $criteria->add(new TFilter("codigo_cadastro_origem", "=", 100));
        $responsavel_id                 = new TDBCombo('responsavel_id', 'tecbiz', 'Pessoa', 'pessoa_codigo', 'pessoa_nome', 'pessoa_nome', $criteria);
        
        $criteria = new TCriteria;
        $criteria->add( new TFilter('enttipent', '=', 1));
        $entcodent                      = new TDBComboMultiValue('entcodent', 'tecbiz', 'Entidade', 'entcodent', array(0 => 'entcodent', 1 => 'entrazsoc'), 'entcodent', $criteria);
        
        $status_ticket_id               = new TDBCombo('status_ticket_id', 'atividade', 'StatusTicket', 'id', 'nome');
        $prioridade_id                  = new TDBCombo('prioridade_id', 'atividade', 'Prioridade', 'id', 'nome');
        
        $data_prevista                  = new TDate('data_prevista');
        $data_prevista->setMask('dd/mm/yyyy');
        $dataAtividadeInicio            = new TDate('data_atividade_inicio');
        $dataAtividadeInicio->setMask('dd/mm/yyyy');
        $dataAtividadeInicio->setValue('01/'.date('m/Y'));
        $dataAtividadeFinal             = new TDate('data_atividade_final');
        $dataAtividadeFinal->setMask('dd/mm/yyyy');
        $dataAtividadeFinal->setValue(date('d/m/Y'));
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter("origem", "=", 1));
        $criteria->add(new TFilter("ativo", "=", 1));
        $criteria->add(new TFilter("codigo_cadastro_origem", "=", 100));
        $colaborador_id                 = new TDBCombo('colaborador_id', 'tecbiz', 'Pessoa', 'pessoa_codigo', 'pessoa_nome', 'pessoa_nome', $criteria);
        
        $tipo                           = new TRadioGroup('tipo');
        $output_type                    = new TRadioGroup('output_type');

        // define the sizes
        $ticket->setSize(100);
        $solicitante_id->setSize(30);
        $solicitante_nome->setSize(245);
        $responsavel_id->setSize(300);
        $colaborador_id->setSize(300);
        $entcodent->setSize(300);
        $status_ticket_id->setSize(100);
        $prioridade_id->setSize(100);
        $data_prevista->setSize(100);
        $dataAtividadeInicio->setSize(100);
        $dataAtividadeFinal->setSize(100);
        $tipo->setSize(100);
        $output_type->setSize(100);


        // validations
        $output_type->addValidation('Output', new TRequiredValidator);

        // add one row for each form field
        $table->addRowSet( new TLabel('Ticket inicial:'), $ticket );
        $table->addRowSet( new TLabel('Solicitante:'), array($solicitante_id, $solicitante_nome)  );
        $table->addRowSet( new TLabel('Responsável:'), $responsavel_id );
        $table->addRowSet( new TLabel('Cliente:'), $entcodent );
        $table->addRowSet( new TLabel('Status:'), $status_ticket_id );
        $table->addRowSet( new TLabel('Prioridade:'), $prioridade_id );
        $table->addRowSet( new TLabel('Dt. Prevista limite:'), $data_prevista );
        $table->addRowSet( new TLabel('Dt. Atividades inicio:'), array($dataAtividadeInicio, $label_data_fim = new TLabel('Fim:'), $dataAtividadeFinal) );
        $label_data_fim->setSize(48);
        $table->addRowSet( new TLabel('Atividades colaborador:'), $colaborador_id);
        $table->addRowSet( new TLabel('Relatório'), $tipo);
        $table->addRowSet( new TLabel('Output:'), $output_type );

        $this->form->setFields(array($ticket,
                                     $solicitante_id,
                                     $solicitante_nome,
                                     $responsavel_id,
                                     $entcodent,
                                     $status_ticket_id,
                                     $prioridade_id,
                                     $data_prevista,
                                     $dataAtividadeInicio,
                                     $dataAtividadeFinal,
                                     $colaborador_id,
                                     $tipo,
                                     $output_type));

        $tipo->addItems(array('s' => 'Sintético', 'a' => 'Analitico'));
        $tipo->setValue('s');
        $tipo->setLayout('horizontal');
        //$output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));
        $output_type->addItems(array('html'=>'HTML'));
        $output_type->setValue('html');
        $output_type->setLayout('horizontal');
        
        $generate_button = TButton::create('generate', array($this, 'onGenerate'), _t('Generate'), 'ico_apply.png');
        $this->form->addField($generate_button);
        
        $change_data = new TAction(array($this, 'onChangeData'));
        $dataAtividadeInicio->setExitAction($change_data);
        $dataAtividadeFinal->setExitAction($change_data);
        
        // add a row for the form action
        $table->addRowSet( $generate_button, '' )->class = 'tformaction';
        
        parent::add($this->form);
    }

    public static function onChangeData($param)
    {
         
        $obj = new StdClass;
        $string = new StringsUtil;
        
        if(strlen($param['data_atividade_inicio']) == 10 && strlen($param['data_atividade_final']) == 10)
        {
        
            if(strtotime($string->formatDate($param['data_atividade_final'])) < strtotime($string->formatDate($param['data_atividade_inicio'])))
            {
    	        $obj->data_atividade_final = ''; 
    	        new TMessage('error', 'Data de atividade final menor que data de atividade inicial'); 
            }
        
        }
        
        TForm::sendData('form_Ticket', $obj, FALSE, FALSE);
       
    }


    /**
     * method onGenerate()
     * Executed whenever the user clicks at the generate button
     */
    function onGenerate()
    {
        try
        {
            $string = new StringsUtil;
            
            // open a transaction with database 'atividade'
            TTransaction::open('atividade');
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            if($formdata->ticket_id)
            {
                $where .= " and t.id >= {$formdata->ticket_id} ";
            }
            
            if ($formdata->solicitante_id)
            {
                $where .= " and t.solicitante_id = {$formdata->solicitante_id} ";
            }
            if ($formdata->responsavel_id)
            {
                $where .= " and t.responsavel_id = {$formdata->responsavel_id} ";
            }
            if ($formdata->entcodent)
            {
                TTransaction::open('tecbiz');
                $solicitantes = Pessoa::getPessoasEntidade($formdata->entcodent);
                $comma_separated = implode(",", $solicitantes);
                TTransaction::close();
                $where .= " and t.solicitante_id in ( {$comma_separated} )";
            }
            if ($formdata->status_ticket_id)
            {
                $where .= " and t.status_ticket_id = {$formdata->status_ticket_id} ";
            }
            if ($formdata->prioridade_id)
            {
                $where .= " and t.prioridade_id = {$formdata->prioridade_id} ";
            }
            if ($formdata->data_prevista)
            {
                $where .= " and t.data_prevista <= '{$string->formatDate($formdata->data_prevista)}' ";
            }
            if ($formdata->data_atividade_inicio)
            {
                $where .= " and a.data_atividade >= '{$string->formatDate($formdata->data_atividade_inicio)}' ";
            }
            if ($formdata->data_atividade_final)
            {
                $where .= " and a.data_atividade <= '{$string->formatDate($formdata->data_atividade_final)}' ";
            }
            if ($formdata->colaborador_id)
            {
                $where .= " and a.colaborador_id = {$formdata->colaborador_id} ";
            }

            $format  = $formdata->output_type;
            
            $objects = Ticket::relatorioSintetico($where);
            
            if ($objects)
            {
              //  $widths = array(30,30,30,30,70,70,70,80,30,250,250,30,250,50,50,50);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths, 'L');
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new TTableWriterRTF($widths);
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '12', 'B',   '#ffffff', '#6B6B6B');
                $tr->addStyle('datap', 'Arial', '10', '',    '#000000', '#E5E5E5');
                $tr->addStyle('datapa', 'Arial', '9', '',    '#000000', '#E5E5E5');
                $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                $tr->addStyle('dataia', 'Arial', '9', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Times', '16', 'B',  '#4A5590', '#C0D3E9');
                $tr->addStyle('footer', 'Times', '12', 'BI', '#4A5590', '#C0D3E9');

                $tr->addStyle('valpos', 'Arial', '12', '',    '#000000', '#0DC13A');
                $tr->addStyle('valneg', 'Arial', '12', '',    '#000000', '#FF0000');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('Ticket - Resumo atividades', 'center', 'header', 16);
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Seq', 'center', 'title'); 
                $tr->addCell('ID', 'center', 'title'); 
                $tr->addCell('ST', 'center', 'title'); 
                $tr->addCell('PR', 'center', 'title'); 
                $tr->addCell('H.O.', 'center', 'title'); 
                $tr->addCell('H.A.', 'center', 'title'); 
                $tr->addCell('H.S.', 'center', 'title'); 
                $tr->addCell('Prevista', 'center', 'title'); 
                $tr->addCell('Dias', 'center', 'title'); 
                $tr->addCell(utf8_decode('Título'), 'left', 'title'); 
                $tr->addCell(utf8_decode('Responsável'), 'left', 'title'); 
                $tr->addCell('T', 'center', 'title'); 
                $tr->addCell('Cliente', 'left', 'title'); 
                $tr->addCell(utf8_decode('Orçado'), 'right', 'title'); 
                $tr->addCell('Pago', 'right', 'title'); 
                $tr->addCell('Saldo', 'right', 'title');

                // controls the background filling
                $colour= FALSE;
                
                TTransaction::open('tecbiz');
                
                $repository = new TRepository('Pessoa');
                $repo = $repository->load();
                foreach ($repo as $row)
                {
                    $pessoa[$row->pessoa_codigo] = $row->pessoa_nome;
                }
                
                $seq                     = 1;
                $totalOrcado             = 0;
                $totalPago               = 0;
                $totalSaldo              = 0;
                
                // data rows
                foreach ($objects as $object)
                {
                    
                    //$responsavel = new Pessoa($object['responsavel_id']);
                    $cliente     = new Pessoa($object['solicitante_id']);
                                        
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($seq++, 'center', $style);
                    $tr->addCell($object['id'], 'center', $style);
                    $tr->addCell(substr($object['status'], 0, 1), 'center', $style);
                    $tr->addCell(substr($object['prioridade'], 0, 1), 'center', $style);
                    $tr->addCell(substr($object['orcamento_horas'], 0, -3), 'center', $style);
                    $tr->addCell(substr($object['horas_atividade'], 0, -3), 'center', $style);
                    $tr->addCell(substr($object['horas_saldo'], 0, -3), 'center', substr($object['horas_saldo'], 0, 1) == '-' ? 'valneg' : 'valpos');
                    $tr->addCell($object['data_prevista'] ? $data_prevista = $string->formatDateBR($object['data_prevista']) : null, 'center', $style);
                    $tr->addCell($object['data_prevista'] ? $dias = $string->subtrair_datas(date('Y-m-d'), $object['data_prevista']) : null, 'center', substr($dias, 0, 1) == '-' ? 'valneg' : 'valpos');                    
                    $tr->addCell(utf8_decode($object['titulo']), 'left', $style);
                    $tr->addCell(utf8_decode($pessoa[$object['responsavel_id']]), 'left', $style);
                    $tr->addCell($object['origem'], 'center', $style);
                    $tr->addCell(utf8_decode($cliente->origem_nome), 'left', $style);
                    $tr->addCell($object['valor_total'], 'right', $style);
                    $tr->addCell($object['valor_total_pago'], 'right', $style);
                    $tr->addCell($object['saldo'], 'right', $style);
                    
                    $totalDias             += $dias;
                    $totalOrcado           += $object['valor_total'];
                    $totalPago             += $object['valor_total_pago'];
                    $totalSaldo            += $object['saldo'];
                    
                    if($formdata->tipo == 'a')
                    {
                        
                        TTransaction::open('atividade');
                        
                        $atividades = Ticket::relatorioAnalitico($object['id'], $where);
                        
                        if($atividades)
                        {
                            
                            $seqA = 1;
                            
                            foreach($atividades as $atividade)
                            {
                                $stylea = $colour ? 'datapa' : 'dataia';
                                
                                $tr->addRow();
                                $tr->addCell('', 'center', $stylea);
                                $tr->addCell($seqA++, 'center', $stylea);
                                
                                $tr->addCell($string->formatDateBR($atividade['data_atividade']), 'center', $stylea, 3);
                                //$tr->addCell(substr($object['prioridade'], 0, 1), 'center', $style);
                                //$tr->addCell($object['orcamento_horas'], 'center', $style);
                                
                                $tr->addCell(substr($atividade['tempo'], 0, -3), 'center', $stylea);
                                
                                $tr->addCell('', 'center', $stylea);
                                
                                $tr->addCell($object['data_prevista'] ? $data_prevista = $string->formatDateBR($object['data_prevista']) : null, 'center', $stylea);
                                
                                $tr->addCell('', 'center', $stylea);
                                
                                $tr->addCell('das '.substr($atividade['hora_inicio'], 0, -3).' as '.substr($atividade['hora_fim'], 0, -3), 'left', $stylea);
                                
                                $tr->addCell(utf8_decode($pessoa[$atividade['colaborador_id']]), 'left', $stylea);
                                
                                $tr->addCell('', 'center', $stylea);
                                $tr->addCell(utf8_decode($atividade['tipo_atividade']), 'left', $stylea);
                                $tr->addCell('', 'right', $stylea);
                                $tr->addCell('', 'right', $stylea);
                                $tr->addCell('', 'right', $stylea);
                                
                            }
                        
                        }
                        
                        $tr->addRow();
                        $tr->addCell('&nbsp; ', 'center', $style, 16);
                        
                        TTransaction::close();
                    }
                    
                    $colour = !$colour;
                }
                
                TTransaction::close();
                
                // footer row
                
                $tr->addRow();
                $tr->addCell('Totais:', 'center', 'footer', 4);
                //$tr->addCell('', 'center', 'footer');
                //$tr->addCell('', 'center', 'footer');
                //$tr->addCell('', 'center', 'footer');
                $tr->addCell('', 'center', 'footer');
                $tr->addCell('', 'center', 'footer');
                $tr->addCell('', 'center', 'footer');
                $tr->addCell('', 'center', 'footer');
                $tr->addCell('', 'center', 'footer');                    
                $tr->addCell('', 'left', 'footer');
                $tr->addCell('', 'left', 'footer');
                $tr->addCell('', 'center', 'footer');
                $tr->addCell('', 'left', 'footer');
                $tr->addCell($totalOrcado, 'right', 'footer');
                $tr->addCell($totalPago, 'right', 'footer');
                $tr->addCell($totalSaldo, 'right', 'footer');
                
                
                $tr->addRow();
                $tr->addCell(date('d/m/Y H:i:s'), 'center', 'footer', 16);
                // stores the file
                if (!file_exists("app/output/Ticket.{$format}") OR is_writable("app/output/Ticket.{$format}"))
                {
                    $tr->save("app/output/Ticket.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Ticket.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Ticket.{$format}");
                
                // shows the success message
                new TMessage('info', 'Relatorio gerado. Por favor, habilite popups no navegador (somente para web).');
            }
            else
            {
                new TMessage('error', 'Não foram encontrados registros!');
            }
    
            // fill the form with the active record data
            $this->form->setData($formdata);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
