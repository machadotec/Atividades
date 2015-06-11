<?php
namespace Adianti\Widget\Menu;

use Adianti\Widget\Menu\TMenu;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Util\TImage;

/**
 * MenuItem Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage menu
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMenuItem extends TElement
{
    private $label;
    private $action;
    private $image;
    private $menu;
    private $level;
    
    /**
     * Class constructor
     * @param $label  The menu label
     * @param $action The menu action
     * @param $image  The menu image
     */
    public function __construct($label, $action, $image = NULL, $level = 0)
    {
        parent::__construct('li');
        $this->label  = $label;
        $this->action = $action;
        $this->level  = $level;
        
        if ($image)
        {
            $this->image  = $image;
        }
    }
    
    /**
     * Define the submenu for the item
     * @param $menu A TMenu object
     */
    public function setMenu(TMenu $menu)
    {
        $this->{'class'} = 'dropdown-submenu';
        $this->menu = $menu;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $link = new TElement('a');
        
        if ($this->action)
        {
            //$url['class'] = $this->action;
            //$url_str = http_build_query($url);
            $action = str_replace('#', '&', $this->action);
            if (substr($action,0,7) == 'http://')
            {
                $link-> href = $action;
                $link-> target = '_blank';
            }
            else
            {
                $link-> href = "index.php?class={$action}";
                $link-> generator = 'adianti';
            }
        }
        else
        {
            $link-> href = '#';
        }
        
        if (isset($this->image))
        {
            $image = new TImage($this->image);
            $link->add($image);
        }
        
        $link->add(' '.$this->label); // converts into ISO
        $this->add($link);
        
        if ($this->menu instanceof TMenu)
        {
            $link->{'class'} = "dropdown-toggle";
            $link->{'data-toggle'} = "dropdown";
            
            if ($this->level == 0)
            {
                $caret = new TElement('b');
                $caret->{'class'} = 'caret';
                $caret->add('');
                $link->add($caret);
            }
            parent::add($this->menu);
        }
        
        parent::show();
    }
}
