<?php
/**
 * MODULE PRESTASHOP OFFICIEL CHRONOPOST
 *
 * LICENSE : All rights reserved - COPY AND REDISTRIBUTION FORBIDDEN WITHOUT PRIOR CONSENT FROM OXILEO
 * LICENCE : Tous droits réservés, le droit d'auteur s'applique - COPIE ET REDISTRIBUTION INTERDITES
 * SANS ACCORD EXPRES D'OXILEO
 *
 * @author    Oxileo SAS <contact@oxileo.eu>
 * @copyright 2001-2018 Oxileo SAS
 * @license   Proprietary - no redistribution without authorization
 */

header('Content-type: text/plain');
require('../../../config/config.inc.php');
include_once '../libraries/TrackingServiceWSService.php';
include_once '../chronopost.php';

/** @var Chronopost $chronopostInstance */
$chronopostInstance = Module::getInstanceByName('chronopost');

/* Check secret */
if (!Tools::getIsset('shared_secret') || Tools::getValue('shared_secret') != Configuration::get('CHRONOPOST_SECRET')) {
    die('Secret does not match.');
}

if (!Tools::getIsset('skybill') || !Tools::getIsset('id_order')) {
    die('Parameter Error');
}

$LTRequest = DB::getInstance()->executeS(
    'SELECT lt, account_number FROM '
    ._DB_PREFIX_.'chrono_lt_history WHERE id_order = ' . (int)Tools::getValue('id_order') . ' AND `cancelled` IS NULL'
);

foreach ($LTRequest as $LT) {
    $ws = new TrackingServiceWSService();
    $params = new cancelSkybill();
    $params->language = 'fr_FR';
    $params->skybillNumber = $LT['lt'];

    $order = new Order((int)Tools::getValue('id_order'));
    $chronopostInstance->setWsShippingNumber($order, '');

    $account = Chronopost::getAccountInformationByAccountNumber($LT['account_number']);
    $params->accountNumber = $account['account'];
    $params->password = $account['password'];

    $return = $ws->cancelSkybill($params)->return;

    if ($return) {
        DB::getInstance()->executeS(
            'UPDATE '._DB_PREFIX_.'chrono_lt_history
          SET cancelled = 1
          WHERE lt = \'' . $LT['lt'] . '\''
        );
    }
}


echo Tools::jsonEncode($return);
