<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;

/**
 * Html Editor
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class THtmlEditor extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $size;
    private   $height;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->id = 'THtmlEditor_'.mt_rand(1000000000, 1999999999);
        
        // creates a tag
        $this->tag = new TElement('div');
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
        if ($height)
        {
            $this->height = $height;
        }
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " thtmleditor_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " thtmleditor_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        TScript::create( " thtmleditor_clear_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        // check if the field is not editable
        if (parent::getEditable())
        {
            $tag = new TElement('textarea');
            $tag->{'id'} = $this->id;
            $tag->{'class'} = 'thtmleditor';       // CSS
            $tag-> name  = $this->name;   // tag name
            $this->setProperty('style', "width:{$this->size}px", FALSE); //aggregate style info
            $this->tag->add($tag);
            if ($this->height)
            {
                $tag-> style .=  "height:{$this->height}px";
            }
            
            // add the content to the textarea
            $tag->add(htmlspecialchars($this->value));
            TScript::create(" thtmleditor_start( '{$tag->{'id'}}', '{$this->size}', '{$this->height}' ); ");
        }
        else
        {
            $this->tag-> style = "width:{$this->size}px;";
            $this->tag-> style.= "height:{$this->height}px;";
            $this->tag-> style.= "background-color:#FFFFFF;";
            $this->tag-> style.= "border: 1px solid #000000;";
            $this->tag-> style.= "padding: 5px;";
            $this->tag-> style.= "overflow: auto;";
            
            // add the content to the textarea
            $this->tag->add($this->value);
        }
        // show the tag
        $this->tag->show();
    }
}
