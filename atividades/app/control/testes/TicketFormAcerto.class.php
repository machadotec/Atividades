<?php 
class TicketFormAcerto extends TPage
{ 
    public function __construct() 
    { 
        parent::__construct();        
        //---------------------Insere Ticket id+200---------------------------------
        try 
        { 
            TTransaction::open('atividade'); 
            $criteria = new TCriteria; 
            $repository = new TRepository('Ticket'); 
            $criteria->add(new TFilter('id', '>', 100)); 
            $criteria->add(new TFilter('id','NOT IN', array(135,136,137))); 
            $tickets = $repository->load($criteria); 
           
            foreach ($tickets as $ticket) 
            {                   
                  
               echo $ticket->id." <br> ";
                     
                $ticket->id = $ticket->id+200;                       
                $ticket->store(); 
            } 
            
            TTransaction::close(); 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        } 
    
    //-------------------atividade---------------------------------
   

   try 
        { 
            TTransaction::open('atividade'); 
            
            $criteria = new TCriteria; 
            $repository = new TRepository('Atividade'); 
            $criteria->add(new TFilter('ticket_id', '>', 100)); 
            $criteria->add(new TFilter('ticket_id','NOT IN', array(135,136,137))); 
            $atividades = $repository->load($criteria); 
           
            foreach ($atividades as $atividade) 
            {                            
                $atividade->ticket_id= $atividade->ticket_id+200;                             
                $atividade->store(); 
            } 
            
            TTransaction::close(); 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        } 
       
   //--------------------RequisitoDesenvolvimento--------------------------
      
      try 
        { 
            TTransaction::open('atividade'); 
            $criteria = new TCriteria; 
            $repository = new TRepository('RequisitoDesenvolvimento'); 
            $criteria->add(new TFilter('ticket_id', '>', 100)); 
            $criteria->add(new TFilter('ticket_id','NOT IN', array(135,136,137)));
            $requisitos = $repository->load($criteria); 
           
            foreach ($requisitos as $requisito) 
            {                             
               $requisito->ticket_id= $requisito->ticket_id+200;                               
               $requisito->store(); 
            } 
            TTransaction::close(); 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }    
  // -------------Deleta tickets maior que 100 e menor que 200--------------
   try 
        { 
            TTransaction::open('atividade'); 
            $criteria = new TCriteria; 
            $criteria->add(new TFilter('id', '>', 100)); 
            $criteria->add(new TFilter('id', '<', 200));
            $criteria->add(new TFilter('id','NOT IN', array(135,136,137))); 
            $repository = new TRepository('Ticket'); 
            $repository->delete($criteria);       
            new TMessage('info', 'Processamento Efetuado'); 
            TTransaction::close(); 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        } 
   
   //-----------------------------------------------------------------------
   
    } 
} 
?>