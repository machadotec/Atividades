<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;

/**
 * ComboBox Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Matheus Agnes Dias
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMultiSearch extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $items;
    protected $size;
    protected $height;
    protected $minLength;
    protected $maxSize;
    protected $initialItems;
    
    /**
     * Class Constructor
     * @param  $name Widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tmultisearch'.mt_rand(1000000000, 1999999999);

        $this->height = 100;
        $this->minLength = 5;
        $this->maxSize = 0;
        
        if (LANG !== 'en')
        {
            TPage::include_js('lib/adianti/include/tmultisearch/select2_locale_'.LANG.'.js');
        }
        
        // creates a <select> tag
        $this->tag = new TElement('input');
        $this->tag->{'type'} = 'hidden';
        $this->tag->{'component'} = 'multisearch';
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
     * Define the minimum length for search
     */
    public function setMinLength($length)
    {
        $this->minLength = $length;
    }

    /**
     * Define the maximum number of items that can be selected
     */
    public function setMaxSize($maxsize)
    {
        $this->maxSize = $maxsize;
    }
    
    /**
     * Add items to the combo box
     * @param $items An indexed array containing the combo options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            $val = $_POST[$this->name];
            
            if ($val)
            {
                $rows = explode('||', $val);
                $data = array();
    
                if (is_array($rows))
                {
                    foreach ($rows as $row)
                    {
                        $columns = explode('::', $row);
                        
                        if (is_array($columns))
                        {
                            $data[ $columns[0] ] = $columns[1];
                        }
                    }
                }
                return $data;
            }
            return '';
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tmultisearch_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tmultisearch_disable_field('{$form_name}', '{$field}'); " );
    }

    /**
     * Define the field's value
     * @param $value An array the field's values
     */
    public function setValue($value)
    {
        $this->initialItems = $value;
        $this->value = $value;
    }
    /**
     * Shows the widget
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  = $this->name;    // tag name
        $this->tag-> id  = $this->id;    // tag name
        $this->setProperty('style', "width:{$this->size}px", FALSE); //aggregate style info
        $multiple = $this->maxSize == 1 ? 'false' : 'true';
        
        $load_items = 'undefined';
        
        if ($this->initialItems)
        {
            $new_items = array();
            foreach ($this->initialItems as $key => $item)
            {
                $new_item = array('id' => $key, 'text' => $item);
                $new_items[] = $new_item;
            }
            
            if ($multiple == 'true')
            {
                $load_items = json_encode($new_items);
            }
            else
            {
                $load_items = json_encode($new_item);
            }
        }

        $preitems_json = 'undefined';
        if ($this->items)
        {
            $preitems = array();
            foreach ($this->items as $key => $item)
            {
                $new_item = array('id' => $key, 'text' => $item);
                $preitems[] = $new_item;
            }
            $preitems_json = json_encode($preitems);
        }
        
        $search_word = AdiantiCoreTranslator::translate('Search');
        
        // shows the component
        $this->tag->show();
        
        TScript::create(" tmultisearch_start( '{$this->id}', '{$this->minLength}', '{$this->maxSize}', '{$search_word}', $multiple, {$preitems_json}, '{$this->size}px', '{$this->height}px', {$load_items} ); ");
    }
}
