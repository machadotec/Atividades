<?php
namespace Adianti\Wrapper;

use Adianti\Widget\Wrapper\TQuickForm;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TButton;

/**
 * Bootstrap form decorator for Adianti Framework
 *
 * @version    2.0
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class BootstrapFormWrapper
{
    private $decorated;
    
    /**
     * Constructor method
     */
    public function __construct(TQuickForm $form)
    {
        $this->decorated = $form;
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    
    /**
     * Shows the decorated form
     */
    public function show()
    {
        $element = new TElement('form');
        $element->{'class'}   = "form-horizontal";
        $element->{'enctype'} = "multipart/form-data";
        $element->{'method'}  = 'post';
        $element->{'name'}    = $this->decorated->getName();
        $element->{'id'}      = $this->decorated->getName();
        
        foreach ($this->decorated->getFields() as $field)
        {
            if (!$field instanceof TButton)
            {
                $group = new TElement('div');
                $group->{'class'} = 'form-group';
                
                $label = new TElement('label');
                
                $label->{'class'} = 'col-sm-2 control-label';
                $label->add($field->getLabel());
                $group->add($label);
                $col = new TElement('div');
                $col->{'class'} = 'col-sm-10';
                $col->add($field);
                
                $field->{'class'} = 'form-control input-sm '.$field->class;
                $field->{'style'} = $field->style . ' float:left'; 
                
                $group->add($col);
                $element->add($group);
            }
        }
        
        if ($this->decorated->getActionButtons())
        {
            $group = new TElement('div');
            $group->{'class'} = 'form-group';
            $col = new TElement('div');
            $col->{'class'} = 'col-sm-offset-2 col-sm-10"';
            
            $i = 0;
            foreach ($this->decorated->getActionButtons() as $action)
            {
                $col->add($action);
                $i ++;
            }
            $group->add($col);
            $element->add($group);
        }
        
        $element->{'width'} = '100%';
        $element->show();
    }
}
