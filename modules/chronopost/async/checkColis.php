<?php

header('Content-type: text/plain');
require('../../../config/config.inc.php');
require('../chronopost.php');
require('../libraries/checkColis.php');

error_reporting(E_ALL);

$order = new Order(Tools::getValue('orderId'));

$result = checkColis::check(
    $order,
    Tools::getValue('weight'),
    Tools::getValue('width'),
    Tools::getValue('height'),
    Tools::getValue('length')
);

echo $result;
return;