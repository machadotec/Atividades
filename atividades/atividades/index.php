<?php
require_once 'init.php';

$uri = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$template = 'theme1';

new TSession;

$menu_string = '';

if ( TSession::getValue('logged') )
{
    $content = file_get_contents("app/templates/{$template}/layout.html");
  
    ob_start();
    $callback = array('SystemPermission', 'checkPermission');
    $xml = new SimpleXMLElement(file_get_contents('menu.xml'));
    $menu = new TMenu($xml, $callback, 1, 'nav collapse', '');
    $menu->class = 'nav';
    $menu->id    = 'side-menu';
    $menu->show();
    $menu_string = ob_get_clean();
}
else
{
    $content = file_get_contents("app/templates/{$template}/login.html");
}

$content  = TApplicationTranslator::translateTemplate($content);
$content  = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$template}/libraries.html"), $content);
$content  = str_replace('{URI}', $uri, $content);
$content  = str_replace('{class}', isset($_REQUEST['class']) ? $_REQUEST['class'] : '', $content);
$content  = str_replace('{template}', $template, $content);
$content  = str_replace('{MENU}', $menu_string, $content);
$content  = str_replace('{username}', TSession::getValue('username'), $content);
$content  = str_replace('{frontpage}', TSession::getValue('frontpage'), $content);
$css      = TPage::getLoadedCSS();
$js       = TPage::getLoadedJS();
$content  = str_replace('{HEAD}', $css.$js, $content);

if (isset($_REQUEST['class']) AND TSession::getValue('logged'))
{
    $url = http_build_query($_REQUEST);
    $content = str_replace('//#javascript_placeholder#', "__adianti_load_page('engine.php?{$url}');", $content);
}
echo $content;
