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

header('Content-type: application/json');
require('../../../config/config.inc.php');
require('../chronopost.php');

include_once dirname(__FILE__).'/../libraries/webservicesHelper.php';
$wsHelper = new webservicesHelper();
$module_instance = new Chronopost();

$return = array();

/* Check secret */
if (!Tools::getIsset('shared_secret') || Tools::getValue('shared_secret') != Configuration::get('CHRONOPOST_SECRET')) {
    $return['error'] = 'Secret does not match.';
}

if (!Tools::getIsset('code') || !Tools::getIsset('contract')) {
    $return['error'] = 'Parameter Error';
}

$contract = Tools::getValue('contract');

if (!is_numeric($contract) || $contract <= 0) {
    $return['error'] = $module_instance->l('Please choose a contract', 'createcarrier');
}

$carrier_code = Tools::getValue('code');

// Check if we can create this carrier
$available_products = $wsHelper->getMethodsForContract(Tools::getValue('contract'));
if (!isset($return['error']) && !in_array(Chronopost::$carriers_definitions[$carrier_code]['product_code'], $available_products)) {
    $return['error'] = $module_instance->l('Product not available : you can\'t create this carrier with this contract', 'createcarrier');
}

if (!isset($return['error'])) {
    $carrier = Chronopost::createCarrier($carrier_code);
    if ($carrier) {
        Configuration::updateValue('CHRONOPOST_'.Tools::strtoupper($carrier_code).'_ACCOUNT', $contract);
        $return['success'] = true;
    } else {
        $return['error'] = $module_instance->l('An error occurred while creating the carrier. Please check your settings (contract and addresses).', 'createcarrier');
    }
}



echo json_encode($return);
exit;
