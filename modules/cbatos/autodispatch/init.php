<?php
require_once implode(DIRECTORY_SEPARATOR, array(dirname(dirname(dirname(dirname(__FILE__)))), 'config', 'config.inc.php'));

function cbatos_registerRequestValue($name, $value)
{
	$_GET[$name] = $value;
	$_POST[$name] = $value;
	$_REQUEST[$name] = $value;
}

function cbatos_autodispatch($file)
{
	$controller = basename($file, '.pub.php');
    //PrestaShop 1.5+
    foreach (array('controller' => $controller, 'fc' => 'module', 'module' => 'cbatos') as $name => $value)
        cbatos_registerRequestValue($name, $value);
    Dispatcher::getInstance()->dispatch();
}
