<?php
class GeradorDescricao extends TPage
{
    private $form;
    
    public function __construct()
    {
       parent::__construct();
        
       $this->form = new TQuickForm;
       $this->form->class = 'tform';
       $this->form->setFormTitle ( 'Relatorio' );
       
       $relatorio = new TAction(array($this, 'onGenerateKambam'));
       $this->form->addQuickAction('Report', $relatorio, 'ico_new.png');   
       
       parent::add($this->form);
        
    }
    
    public function onGenerateKambam()
    {
        
        try
        {
            TTransaction::open('atividade');
            $object = $this->form->getData();
            
            $desenvolvimento = new RequisitoDesenvolvimento(1);
            
            $cliente_id         = $desenvolvimento->ticket->solicitante_id;
            $responsavel_id     = $desenvolvimento->ticket->responsavel_id;

            $pessoa = new Pessoa($cliente_id);
            $cliente = $pessoa->pessoa_nome;
            
            $pessoa = new Pessoa($responsavel_id);
            $responsavel = $pessoa->pessoa_nome;

            $string = new StringsUtil;
            
            $data = $desenvolvimento->data_cadastro;
            $data = explode('-', $data);
            
            $data_prevista = '___/___/___';
            if($desenvolvimento->ticket->data_prevista)
            {
                $data_prevista = $string->formatDateBR($desenvolvimento->ticket->data_prevista);
            }    
            
            $designer = new TPDFDesigner;
            $designer->fromXml('app/reports/kanban.pdf.xml');
            $designer->replace('{ID_DTR}', $desenvolvimento->id.'/'.$data[0]);
            $designer->replace('{CADASTRO}', $string->formatDateBR($desenvolvimento->data_cadastro));
            $designer->replace('{INICIO}', date('d/m/Y'));
            $designer->replace('{PREVISTA}', $data_prevista);
            $designer->replace('{SISTEMA}', $desenvolvimento->ticket->sistema->nome);
            $designer->replace('{TICKET}', $desenvolvimento->ticket_id);
            $designer->replace('{TITULO}', $desenvolvimento->titulo);
            $designer->replace('{SOLICITANTE}', $cliente);
            $designer->replace('{RESPONSAVEL}', $responsavel);
            
            $designer->generate();
            
            $file = 'app/output/DTR011-'.$desenvolvimento->id.'-'.$data[0].'.pdf';
            $designer->save($file);
            parent::openFile($file); 
            
            new TMessage('info', 'Cartão kambam gerado com sucesso!');
            
            TTransaction::close();
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage);
        }
    }
    
    
    public function onGenerate()
    {
        try
        {
            TTransaction::open('atividade');
            $object = $this->form->getData();
            
            $desenvolvimento = new RequisitoDesenvolvimento(1);
            
            $cliente_id         = $desenvolvimento->ticket->solicitante_id;
            $responsavel_id     = $desenvolvimento->ticket->responsavel_id;

            $pessoa = new Pessoa($cliente_id);
            $cliente = $pessoa->pessoa_nome;
            
            $pessoa = new Pessoa($responsavel_id);
            $responsavel = $pessoa->pessoa_nome;
                
            if (!class_exists('PHPRtfLite_Autoloader'))
            {
                PHPRtfLite::registerAutoloader();
            }
            $tr = new TTableWriterRTF(array(500));
            
            $tr->addStyle('title', 'Arial', '10', 'BI', '#000000', '#ffffff');
            $tr->addStyle('datap', 'Arial', '10', '',   '#000000', '#ffffff');
            
            $string = new StringsUtil;
            
            $data = $desenvolvimento->data_cadastro;
            $data = explode('-', $data);
            
            $desenvolvimento->ticket->data_prevista ? $data_prevista = $string->formatDateBR($desenvolvimento->ticket->data_prevista) : '___/___/___';
            
            $cabecalho = 'DTR010 - Solicitação de Desenvolvimento
Número: '.$desenvolvimento->id.'/'.$data[0].' Data: '.$string->formatDateBR($desenvolvimento->data_cadastro).' Prazo de entrega: '.$data_prevista.' Qtde de Horas: '.strstr($desenvolvimento->ticket->orcamento_horas, ':', true).' Ticket: '.$desenvolvimento->ticket_id.'
Benefício: ( )+Receita ( )-Despesa ( )+Eficiência ( )-NDA
Título: '.$desenvolvimento->titulo.'
Sistema: '.$desenvolvimento->ticket->sistema->nome.'      Módulo:                                   Rotina: '.$desenvolvimento->rotina.'
Cliente: '.$cliente.' Solicitante/Dpto: '.$responsavel;
            
            $tr->addRow();
            $tr->addCell($cabecalho, 'left', 'title');
           
            $tr->addRow();
            $tr->addCell('<br /><b>Objetivo:</b> <br />'.$desenvolvimento->objetivo, 'left', 'datap');
            
            $tr->addRow();
            $tr->addCell('<br /><b>Entrada: </b><br />'.$desenvolvimento->entrada, 'left', 'datap');
                         
            $tr->addRow();
            $tr->addCell('<br /><b>Processamento: </b><br />'.$desenvolvimento->processamento, 'left', 'datap');
            
            $tr->addRow();
            $tr->addCell('<br /><b>Saida: </b><br />'.$desenvolvimento->saida, 'left', 'datap');
            
            $nome = 'DTR010 '.$desenvolvimento->id .' - '.$data[0].' - '.$desenvolvimento->titulo;
            
            $tr->save("app/output/{$nome}.rtf");
            parent::openFile("app/output/{$nome}.rtf");
            
            new TMessage('info', 'DTR gerado com sucesso!');
            
            TTransaction::close();
            
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage);
        }
        
    }
    
}