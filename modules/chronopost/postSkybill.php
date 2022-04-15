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

if(!isset($_SESSION))
{
    session_start();
}
require('../../config/config.inc.php');
$errors = array();

if (!Tools::getIsset('orderid') && !Tools::getIsset('orders')) {
    $errors[] = '<h1>Informations de commande non transmises</h1>';
}

require_once('chronopost.php');
include('libraries/ShippingServiceWSService.php');
include_once('libraries/PointRelaisServiceWSService.php');
include('libraries/QuickcostServiceWSService.php');
include_once('libraries/checkColis.php');
include_once('libraries/webservicesHelper.php');

if (Shop::isFeatureActive()) {
    Shop::setContext(Shop::CONTEXT_ALL);
}

$accounts = array();
if(Tools::getIsset('orderid')){
    $accounts[Tools::getValue('orderid')] = Tools::getValue(('account'));

    $dimensions[Tools::getValue('orderid')] = array(
        "weights" => Tools::getValue('weight'),
        "heights" => Tools::getValue('height'),
        "widths"  => Tools::getValue('width'),
        "lengths" => Tools::getValue('length')
    );

}
$multi = array();

$massActions = false;
if (Tools::getIsset('weights', 'heights')) {
    $massActions = true;
    $dimensionsRaw = array(
        "weights" => json_decode(Tools::getValue('weights'), true),
        "heights" => json_decode(Tools::getValue('heights'), true),
        "widths" => json_decode(Tools::getValue('widths'), true),
        "lengths" => json_decode(Tools::getValue('lengths'), true)
    );
    $dimensions = array();
    foreach ($dimensionsRaw as $dimension => $orderDimensions) {
        foreach ($orderDimensions as $orderId => $values) {
            if (!isset($dimensions[$orderId])) {
                $dimensions[$orderId] = array();
            }
            $dimensions[$orderId][$dimension] = $values;
        }
    }
}

if (!Tools::getIsset('shared_secret') || Tools::getValue('shared_secret') != Configuration::get('CHRONOPOST_SECRET')) {
    $errors[] = 'Secret does not match.';
}

// Check dimensions (In case JS verification passed due to user modifications)
// MassExport is handled in AdminExportChronopostController
if (Tools::getIsset('orderid')) {
    $checkColis = json_decode(checkColis::check(
        Tools::getValue('orderid'),
        $dimensions[Tools::getValue('orderid')]['weights'],
        $dimensions[Tools::getValue('orderid')]['widths'],
        $dimensions[Tools::getValue('orderid')]['heights'],
        $dimensions[Tools::getValue('orderid')]['lengths']), true);
    if($checkColis['error'] != 0){
        $errors[] = 'Problème rencontré avec la dimensions d\'un ou plusieurs colis';
    }
}

$return = false;

if (Tools::getIsset('multi')) {
    $multi = Tools::getValue('multi');
    $multi = json_decode($multi, true);
} else {
    $multi = array();
}

if (Tools::getIsset('orders')) {
    $orders = Tools::getValue('orders');
    $orders = explode(';', $orders);
} else {
    $orders = array(Tools::getValue('orderid'));
    if (Tools::getIsset('return')) {
        foreach ($orders as $order){
            $numberOfTracking = count(Chronopost::getAllTrackingNumbers($order));
            if($numberOfTracking <= 0){
                $errors[] = 'Impossible de créer une étiquette de retour avant d\'éditer celle de l\'aller';
            }

        }
        $return = true;
    }
    if (Tools::getIsset('multiOne')) {
        $multi = array($orders[0]=>Tools::getValue('multiOne'));
    }
}

if (Tools::getIsset('accounts')) {
    $accounts = Tools::getValue('accounts');
    $accounts = json_decode($accounts, true);
}

// Test accounts
if ($accounts && is_array($accounts)) {
    foreach ($orders as $orderid) {
        if (!isset($accounts[$orderid])) {
            $errors[] = 'Erreur : veuillez configurer le module avant de procéder à l\'édition des étiquettes.';
        }

        $account = Chronopost::getAccountInformationByAccountNumber($accounts[$orderid]);
        if (Tools::strlen($account['account']) < 8) {
            $errors[] = 'Erreur : veuillez configurer le module avant de procéder à l\'édition des étiquettes.';
        }

        $service = new QuickcostServiceWSService();
        $quick = new quickCost();
        $quick->accountNumber = $account['account'];
        $quick->password = $account['password'];
        $quick->depCode = '92500';
        $quick->arrCode = '75001';
        $quick->weight = '1';
        $quick->productCode = '1';
        $quick->type = 'D';

        $result = $service->quickCost($quick);

        $loginValid = true;
        if ($result->return->errorCode == 3){
            $loginValid = false;
        }

        if (!$loginValid){
            $errors[] = 'Erreur : le contrat Chronopost utilisé n\'est pas valide.';
        }
    }
}


// Check if product is available for the chosen contract
if(Tools::getIsset('orderid') && Tools::getIsset('orders')){
    $wsHelper = new webservicesHelper();
    foreach ($orders as $order) {
        $order = new Order($order);
        $details = Chronopost::getSkybillDetails($order, Tools::getIsset('return'));
        $available_products = $wsHelper->getMethodsForContract($accounts[$order->id]);
        if (!in_array($details['productCode'], $available_products)) {
            $errors[] = 'Erreur : Le contrat sélectionné pour une ou plusieurs commandes ne disposent pas du transporteur.' .
                'Veuillez choisir un autre contrat pour imprimer l\'étiquette.';
            break;
        }
    }
}


if (count($orders) == 0) {
    $errors[] = '<h1>Aucune commande sélectionnée</h1>';
}

$allowedReferers = array('postSkybill.php', 'controller=AdminExportChronopost', 'controller=AdminOrders&id_order');
if (count($errors) > 0) {
    echo 'Une erreur est survenue, veuillez patienter pendant la redirection ...';
    $_SESSION['chronopost_errors'] = $errors;
    if(isset($_SERVER['HTTP_REFERER'])) {
        foreach ($allowedReferers as $allowedReferer) {
            if (strpos($_SERVER['HTTP_REFERER'], $allowedReferer) !== false) {
                header('Refresh: 0; url=' . $_SERVER['HTTP_REFERER']);
            }
        }
    }
    else
        header('Refresh: 0; url=http://' . $_SERVER['HTTP_HOST']);
        exit;

}

require_once('libraries/PDFMerger.php');
@$pdf = new PDFMerger;

foreach ($orders as $orderid) {
    if (is_array($multi) && array_key_exists($orderid, $multi)) {
        $nb = $multi[$orderid];
    } else {
        $nb = 1;
    }

    $totalnb = $nb;

    if($totalnb > 1){
        $ltInfo = createLTMultiColis($orderid, $totalnb, Chronopost::getAccountInformationByAccountNumber($accounts[$orderid]) , $return, $dimensions[$orderid]);
        $service = new ShippingServiceWSService();
        $params = new getReservedSkybill();
        $params->reservationNumber = $ltInfo->reservationNumber;
        $r = $service->getReservedSkybill($params);
        $lt = new stdClass();
        if($r->return->errorCode == 0 && $r->return->skybill){
            $lt->pdfEtiquette = base64_decode($r->return->skybill);
            $lt->skybillNumber = $ltInfo->resultParcelValue[0]->skybillNumber;
        }
    }
    else{
        $lt = createLT($orderid, Chronopost::getAccountInformationByAccountNumber($accounts[$orderid]) , $return, $dimensions[$orderid]);
    }

    if ($lt === null) {
        $errors[] = 'Erreur : Impossible d\'imprimer l\'étiquette';
        echo 'Redirection ...';
        $_SESSION['chronopost_errors'] = $errors;
        if(isset($_SERVER['HTTP_REFERER'])) {
            foreach ($allowedReferers as $allowedReferer) {
                if (strpos($_SERVER['HTTP_REFERER'], $allowedReferer) !== false) {
                    header('Refresh: 0; url=' . $_SERVER['HTTP_REFERER']);
                }
            }
        }
        else
            header('Refresh: 0; url=http://' . $_SERVER['HTTP_HOST']);
        exit;
    }

    $file = 'skybills/'.$lt->skybillNumber.'.pdf';
    $fp = fopen($file, 'w');
    fwrite($fp, $lt->pdfEtiquette);
    fclose($fp);
    @$pdf->addPDF($file, 'all');

}

try {
    if (isset($_SERVER['HTTP_REFERER']) && preg_match('#AdminOrders#', $_SERVER['HTTP_REFERER'])) {
        header('Refresh: 0; url=' . $_SERVER['HTTP_REFERER']);
    }

    $pdf->merge('download', 'Chronopost-LT-'.date('Ymd-Hi').'.pdf');
} catch (Exception $e) {
    echo '<p>Le fichier généré est invalide.</p>';
    echo '<p>Vérifiez la configuration du module et que les commandes visées disposent d\'adresses de livraison 
valides.</p>';
}


function createLT($orderid, $account = false, $isReturn = false, $dimensions = array())
{
    $module_instance = new Chronopost();
    $o = new Order($orderid);
    $a = new Address($o->id_address_delivery);
    $cust = new Customer($o->id_customer);

    // at least 2 skybills for orders >= 30kg
    $o = new Order($orderid);

    $recipient = new recipientValue();
    $recipient->recipientAdress1 = Tools::substr($a->address1, 0, 35);
    $recipient->recipientAdress2 = Tools::substr($a->address2, 0, 35);
    $recipient->recipientCity = Tools::substr($a->city, 0, 30);
    $recipient->recipientCivility = 'M';
    $recipient->recipientContactName = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
    $c = new Country($a->id_country);
    $recipient->recipientCountry = $c->iso_code;
    $recipient->recipientName = Tools::substr($a->company, 0, 35);
    $recipient->recipientName2 = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
    $recipient->recipientZipCode = $a->postcode;
    $recipient->recipientPhone = $a->phone_mobile == null ? $a->phone : $a->phone_mobile;
    $recipient->recipientMobilePhone = $a->phone_mobile;
    $recipient->recipientEmail = $cust->email;

    if ($isReturn) {
        if (Tools::getValue('return_address') == chronopost::$RETURN_ADDRESS_RETURN) {
            $addressKey = 'RETURN';
        } elseif (Tools::getValue('return_address') == chronopost::$RETURN_ADDRESS_INVOICE) {
            $addressKey = 'CUSTOMER';
        } elseif (Tools::getValue('return_address') == chronopost::$RETURN_ADDRESS_SHIPPING) {
            $addressKey = 'SHIPPER';
        }
        $recipient->recipientAdress1 = Configuration::get('CHRONOPOST_'. $addressKey .'_ADDRESS');
        $recipient->recipientAdress2 = Configuration::get('CHRONOPOST_'. $addressKey .'_ADDRESS2');
        $recipient->recipientCity = Configuration::get('CHRONOPOST_'. $addressKey .'_CITY');
        $recipient->recipientCivility = Configuration::get('CHRONOPOST_'. $addressKey .'_CIVILITY');
        $recipient->recipientContactName = Configuration::get('CHRONOPOST_'. $addressKey .'_CONTACTNAME');
        $recipient->recipientCountry = Configuration::get('CHRONOPOST_'. $addressKey .'_COUNTRY');
        $recipient->recipientName = Configuration::get('CHRONOPOST_'. $addressKey .'_NAME');
        $recipient->recipientName2 = Configuration::get('CHRONOPOST_'. $addressKey .'_NAME2');
        $recipient->recipientZipCode = Configuration::get('CHRONOPOST_'. $addressKey .'_ZIPCODE');
    }


    $esd = new esdValue();
    $esd->specificInstructions = 'aucune';
    
    $esd->height = '';
    $esd->width = '';
    $esd->length = '';

    $header = new headerValue();
    $params = new shippingV7();
    $skybill = new skybillValue();
    $skybill->evtCode = 'DC';
    $skybill->objectType = 'MAR';

    // Ships with Chrono 13 by default
    $skybill->productCode = Chronopost::$carriers_definitions['CHRONO13']['product_code'];
    // Service code 0 by default
    $skybill->service = '0';


    if (Tools::getIsset('advalorem') && Tools::getValue('advalorem') == 'yes') {
        $skybill->insuredValue = (int) round((float)Tools::getValue('advalorem_value')*100);
    }

    if (Tools::getIsset('orders') && Configuration::get('CHRONOPOST_ADVALOREM_ENABLED') == 1) {
        $skybill->insuredValue = (int) round((float)chronopost::amountToInsure($orderid)*100);
    }

    $header->accountNumber = $account['account'];
    $header->subAccount = $account['subaccount'];
    $params->password = $account['password'];

    $header->idEmit = 'PREST';

    $shipper = new shipperValue();
    $shipper->shipperAdress1 = Configuration::get('CHRONOPOST_SHIPPER_ADDRESS');
    $shipper->shipperAdress2 = Configuration::get('CHRONOPOST_SHIPPER_ADDRESS2');
    $shipper->shipperCity = Configuration::get('CHRONOPOST_SHIPPER_CITY');
    $shipper->shipperCivility = Configuration::get('CHRONOPOST_SHIPPER_CIVILITY');
    $shipper->shipperContactName = Configuration::get('CHRONOPOST_SHIPPER_CONTACTNAME');
    $shipper->shipperCountry = Configuration::get('CHRONOPOST_SHIPPER_COUNTRY');
    $shipper->shipperName = Configuration::get('CHRONOPOST_SHIPPER_NAME');
    $shipper->shipperName2 = Configuration::get('CHRONOPOST_SHIPPER_NAME2');
    $shipper->shipperZipCode = Configuration::get('CHRONOPOST_SHIPPER_ZIPCODE');

    if ($isReturn) {
        $shipper = new shipperValue();
        $shipper->shipperAdress1 = Tools::substr($a->address1, 0, 35);
        $shipper->shipperAdress2 = Tools::substr($a->address2, 0, 35);
        $shipper->shipperCity = Tools::substr($a->city, 0, 30);
        $shipper->shipperCivility = 'M';
        $shipper->shipperContactName = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
        $c = new Country($a->id_country);
        $shipper->shipperCountry = $c->iso_code;
        $shipper->shipperPhone =  $a->phone_mobile == null ? $a->phone : $a->phone_mobile;
        $shipper->shipperName = Tools::substr($a->company, 0, 35);
        $shipper->shipperName2 = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
        $shipper->shipperZipCode = $a->postcode;
    }

    $customer = new customerValue();
    $customer->customerAdress1 = Configuration::get('CHRONOPOST_CUSTOMER_ADDRESS');
    $customer->customerAdress2 = Configuration::get('CHRONOPOST_CUSTOMER_ADDRESS2');
    $customer->customerCity = Configuration::get('CHRONOPOST_CUSTOMER_CITY');
    $customer->customerCivility = Configuration::get('CHRONOPOST_CUSTOMER_CIVILITY');
    $customer->customerContactName = Configuration::get('CHRONOPOST_CUSTOMER_CONTACTNAME');
    $customer->customerCountry = Configuration::get('CHRONOPOST_CUSTOMER_COUNTRY');
    $customer->customerName = Configuration::get('CHRONOPOST_CUSTOMER_NAME');
    $customer->customerName2 = Configuration::get('CHRONOPOST_CUSTOMER_NAME2');
    $customer->customerZipCode = Configuration::get('CHRONOPOST_CUSTOMER_ZIPCODE');

    $ref = new refValue();
    $ref->recipientRef = $a->postcode;

    // Skybill details per carrier
    $skybill_details = Chronopost::getSkybillDetails($o, $isReturn);
    $skybill->productCode = $skybill_details['productCode'];
    $skybill->service = $skybill_details['service'];
    if (isset($skybill_details['as'])) {
        $skybill->as = $skybill_details['as'];
    }

    if (array_key_exists('recipientRef', $skybill_details)) {
        $ref->recipientRef = $skybill_details['recipientRef'];
    }

    if (array_key_exists('timeSlot', $skybill_details)) {
        $params->scheduledValue = new scheduledValue();
        $params->scheduledValue->appointmentValue = new appointmentValue();
        $params->scheduledValue->appointmentValue->timeSlotStartDate = $skybill_details['timeSlotStartDate'];
        $params->scheduledValue->appointmentValue->timeSlotEndDate = $skybill_details['timeSlotEndDate'];
        $params->scheduledValue->appointmentValue->timeSlotTariffLevel = $skybill_details['timeSlotTariffLevel'];
    }
    // end carrier-specific part

    $ref->shipperRef = sprintf('%06d', $orderid);

    $skybill->shipDate = date('Y-m-d\TH:i:s');
    $skybill->shipHour = date('H');
    if(!empty($dimensions['weights'][0])){
        $skybill->weight = $dimensions['weights'][0];
    }
    else{
        $skybill->weight = 0;
    }

    if(!empty($dimensions['widths'][0]) && !empty($dimensions['heights'][0]) && !empty($dimensions['lengths'][0])){
        $skybill->height = $dimensions['heights'][0];
        $skybill->length = $dimensions['lengths'][0];
        $skybill->width = $dimensions['widths'][0];
    }
    else{
        $skybill->height = 22.9;
        $skybill->length = 16.2;
        $skybill->width = 0;
    }

    $skybillParams = new skybillParamsValue();
    $skybillParams->mode = Configuration::get('CHRONOPOST_GENERAL_PRINTMODE');

    $skybillParams->withReservation = 0;

    $params->esdValue = $esd;
    $params->headerValue = $header;
    $params->shipperValue = $shipper;
    $params->customerValue = $customer;
    $params->recipientValue = $recipient;
    $params->refValue = $ref;
    $params->skybillValue = $skybill;

    $params->skybillParamsValue = $skybillParams;

    $service = new ShippingServiceWSService();
    $r = $service->shippingV7($params)->return;

    if ($r->errorCode != 0) {
        return null;
    }

    // MAIL::SEND is bugged in 1.5 !
    // http://forge.prestashop.com/browse/PNM-754 (Unresolved as of 2013-04-15)
    // Context fix (it's that easy)
    Context::getContext()->link = new Link();

    if ($isReturn) {
        $customer = new Customer($o->id_customer);
        $template_path = '/modules/chronopost/mails/';
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            $template_path = 'mails/';
        }
        Mail::Send(
            $o->id_lang,
            'return',
            'Lettre de transport Chronopost pour le retour de votre commande',
            array(
                '{id_order}' => $o->id,
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname
            ),
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            array(
                'content' => $r->pdfEtiquette,
                'mime' => 'application/pdf',
                'name' => $r->skybillNumber.'.pdf'
            ),
            null,
            $template_path,
            true
        );
    } else {
        // Store LT for history
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'chrono_lt_history` VALUES (
				'.(int)$o->id.', 
				"'.pSQL($r->skybillNumber).'", 
				"'.pSQL($skybill->productCode).'",
				"'.pSQL($recipient->recipientZipCode).'",
				"'.pSQL($recipient->recipientCountry).'",
				"'.(isset($skybill->insuredValue) ? (float) $skybill->insuredValue / 100 : 0).'",
				"'.pSQL($recipient->recipientCity).'",
				"'.pSQL($account['account']).'",
				NULL
			)');

        Chronopost::trackingStatus($o->id, $r->skybillNumber);
    }

    return $r;
}

function createLTMultiColis($orderid, $totalnb = 1, $account = false, $isReturn = false, $dimensions = array()){

    $o = new Order($orderid);
    $a = new Address($o->id_address_delivery);
    $cust = new Customer($o->id_customer);
    $o = new Order($orderid);

    // ESD PARAMETERS //
    $esd = new esdValue();
    $esd->specificInstructions = 'aucune';
    $esd->ltAImprimerParChronopost = false;
    $esd->nombreDePassageMaximum = 1;
    $esd->height = '';
    $esd->width = '';
    $esd->length = '';

    // RECIPIENT PARAMETERS //
    $recipient = new recipientValue();
    $recipient->recipientAdress1 = Tools::substr($a->address1, 0, 35);
    $recipient->recipientAdress2 = Tools::substr($a->address2, 0, 35);
    $recipient->recipientCity = Tools::substr($a->city, 0, 30);
    $recipient->recipientCivility = 'M';
    $recipient->recipientContactName = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
    $c = new Country($a->id_country);
    $recipient->recipientCountry = $c->iso_code;
    $recipient->recipientName = Tools::substr($a->company, 0, 35);
    $recipient->recipientName2 = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
    $recipient->recipientZipCode = $a->postcode;
    $recipient->recipientPhone = $a->phone_mobile == null ? $a->phone : $a->phone_mobile;
    $recipient->recipientMobilePhone = $a->phone_mobile;
    $recipient->recipientEmail = $cust->email;

    if ($isReturn) {
        if (Tools::getValue('return_address') == chronopost::$RETURN_ADDRESS_RETURN) {
            $addressKey = 'RETURN';
        } elseif (Tools::getValue('return_address') == chronopost::$RETURN_ADDRESS_INVOICE) {
            $addressKey = 'CUSTOMER';
        } elseif (Tools::getValue('return_address') == chronopost::$RETURN_ADDRESS_SHIPPING) {
            $addressKey = 'SHIPPER';
        }
        $recipient->recipientAdress1 = Configuration::get('CHRONOPOST_'. $addressKey .'_ADDRESS');
        $recipient->recipientAdress2 = Configuration::get('CHRONOPOST_'. $addressKey .'_ADDRESS2');
        $recipient->recipientCity = Configuration::get('CHRONOPOST_'. $addressKey .'_CITY');
        $recipient->recipientCivility = Configuration::get('CHRONOPOST_'. $addressKey .'_CIVILITY');
        $recipient->recipientContactName = Configuration::get('CHRONOPOST_'. $addressKey .'_CONTACTNAME');
        $recipient->recipientCountry = Configuration::get('CHRONOPOST_'. $addressKey .'_COUNTRY');
        $recipient->recipientName = Configuration::get('CHRONOPOST_'. $addressKey .'_NAME');
        $recipient->recipientName2 = Configuration::get('CHRONOPOST_'. $addressKey .'_NAME2');
        $recipient->recipientZipCode = Configuration::get('CHRONOPOST_'. $addressKey .'_ZIPCODE');
    }

    // SHIPPER PARAMETERS //
    $shipper = new shipperValue();
    $shipper->shipperAdress1 = Configuration::get('CHRONOPOST_SHIPPER_ADDRESS');
    $shipper->shipperAdress2 = Configuration::get('CHRONOPOST_SHIPPER_ADDRESS2');
    $shipper->shipperCity = Configuration::get('CHRONOPOST_SHIPPER_CITY');
    $shipper->shipperCivility = Configuration::get('CHRONOPOST_SHIPPER_CIVILITY');
    $shipper->shipperContactName = Configuration::get('CHRONOPOST_SHIPPER_CONTACTNAME');
    $shipper->shipperCountry = Configuration::get('CHRONOPOST_SHIPPER_COUNTRY');
    $shipper->shipperName = Configuration::get('CHRONOPOST_SHIPPER_NAME');
    $shipper->shipperName2 = Configuration::get('CHRONOPOST_SHIPPER_NAME2');
    $shipper->shipperZipCode = Configuration::get('CHRONOPOST_SHIPPER_ZIPCODE');

    if ($isReturn) {
        $shipper = new shipperValue();
        $shipper->shipperAdress1 = Tools::substr($a->address1, 0, 35);
        $shipper->shipperAdress2 = Tools::substr($a->address2, 0, 35);
        $shipper->shipperCity = Tools::substr($a->city, 0, 30);
        $shipper->shipperCivility = 'M';
        $shipper->shipperContactName = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
        $shipper->shipperCountry = Configuration::get('CHRONOPOST_SHIPPER_COUNTRY');
        $shipper->shipperPhone = Configuration::get('CHRONOPOST_SHIPPER_PHONE');
        $shipper->shipperName = Tools::substr($a->company, 0, 35);
        $shipper->shipperName2 = Tools::substr($a->firstname.' '.$a->lastname, 0, 35);
        $shipper->shipperZipCode = $a->postcode;
    }

    // CUSTOMER PARAMETERS //
    $customer = new customerValue();
    $customer->customerAdress1 = Configuration::get('CHRONOPOST_CUSTOMER_ADDRESS');
    $customer->customerAdress2 = Configuration::get('CHRONOPOST_CUSTOMER_ADDRESS2');
    $customer->customerCity = Configuration::get('CHRONOPOST_CUSTOMER_CITY');
    $customer->customerCivility = Configuration::get('CHRONOPOST_CUSTOMER_CIVILITY');
    $customer->customerContactName = Configuration::get('CHRONOPOST_CUSTOMER_CONTACTNAME');
    $customer->customerCountry = Configuration::get('CHRONOPOST_CUSTOMER_COUNTRY');
    $customer->customerName = Configuration::get('CHRONOPOST_CUSTOMER_NAME');
    $customer->customerName2 = Configuration::get('CHRONOPOST_CUSTOMER_NAME2');
    $customer->customerZipCode = Configuration::get('CHRONOPOST_CUSTOMER_ZIPCODE');

    // HEADER PARAMETERS //
    $header = new headerValue();
    $header->accountNumber = $account['account'];
    $header->subAccount = $account['subaccount'];
    $header->idEmit = 'PREST';

    // SKYBILL PARAMS
    $skybill_details = Chronopost::getSkybillDetails($o, $isReturn);
    $skybills = array();
    for($i = 1; $i <= $totalnb; $i++){
        $skybill = new skybillValue();
        $skybill->evtCode = 'DC';
        $skybill->objectType = 'MAR';
        $skybill->bulkNumber = $totalnb;
        $skybill->skybillRank = $i;
        // Ships with Chrono 13 by default
        $skybill->productCode = Chronopost::$carriers_definitions['CHRONO13']['product_code'];
        // Service code 0 by default
        $skybill->service = '0';

        $skybill->productCode = $skybill_details['productCode'];
        $skybill->service = $skybill_details['service'];
        if (isset($skybill_details['as'])) {
            $skybill->as = $skybill_details['as'];
        }
        $skybill->shipDate = date('Y-m-d\TH:i:s');
        $skybill->shipHour = date('H');


        $skybill->weightUnit = 'KGM';
        if(!empty($dimensions['widths'][$i-1]) && !empty($dimensions['heights'][$i-1]) && !empty($dimensions['lengths'][$i-1])){
            $skybill->height = $dimensions['heights'][$i-1];
            $skybill->length = $dimensions['lengths'][$i-1];
            $skybill->width = $dimensions['widths'][$i-1];
        }
        else{
            $skybill->height = 22.9;
            $skybill->length = 16.2;
            $skybill->width = 0;
        }

        if(!empty($dimensions['weights'][$i-1])){
            $skybill->weight = $dimensions['weights'][$i-1];
        }
        else{
            $skybill->weight = 0;
        }
        // Pushing skybill to array
        array_push($skybills, $skybill);
    }

    $skybillParams = new skybillParamsValue();
    $skybillParams->mode = Configuration::get('CHRONOPOST_GENERAL_PRINTMODE');
    $skybillParams->withReservation = 0;

    // REF PARAMETERS
    $refs = array();
    for($i = 0; $i < $totalnb; $i++){
        $ref = new refValue();
        $ref->recipientRef = $a->postcode;
        if (array_key_exists('recipientRef', $skybill_details)) {
            $ref->recipientRef = $skybill_details['recipientRef'];
        }
        $ref->shipperRef = sprintf('%06d', $orderid);

        // Pushing skybill to array
        array_push($refs, $ref);
    }

    // PREPARING WS PARAMETERS //
    $params = new shippingMultiParcelWithReservationV3();

    if (array_key_exists('timeSlot', $skybill_details)) { // Specific to Chrono Precise
        $params->scheduledValue = new scheduledValue();
        $params->scheduledValue->appointmentValue = new appointmentValue();
        $params->scheduledValue->appointmentValue->timeSlotStartDate = $skybill_details['timeSlotStartDate'];
        $params->scheduledValue->appointmentValue->timeSlotEndDate = $skybill_details['timeSlotEndDate'];
        //$params->scheduledValue->appointmentValue->timeSlotTariffLevel = $skybill_details['timeSlotTariffLevel'];
    }

    $params->esdValue = $esd;
    $params->password = $account['password'];
    $params->headerValue = $header;
    $params->shipperValue = $shipper;
    $params->customerValue = $customer;
    $params->recipientValue = $recipient;
    $params->refValue = $refs;
    $params->skybillValue = $skybills;
    $params->skybillParamsValue = $skybillParams;
    $params->version = '3.0';
    $params->numberOfParcel = $totalnb;


    // CALL WS //
    $service = new ShippingServiceWSService();
    $r = $service->shippingMultiParcelWithReservationV3($params)->return;

    // MAIL::SEND is bugged in 1.5 !
    // http://forge.prestashop.com/browse/PNM-754 (Unresolved as of 2013-04-15)
    // Context fix (it's that easy)
    Context::getContext()->link = new Link();

    if ($isReturn) {
        $customer = new Customer($o->id_customer);
        $service = new ShippingServiceWSService();
        $params = new getReservedSkybill();
        $params->reservationNumber = $r->reservationNumber;
        $result = $service->getReservedSkybill($params);
        $lt = new stdClass();
        if($result->return->errorCode == 0 && $result->return->skybill){
            $lt->pdfEtiquette = base64_decode($result->return->skybill);
            $lt->skybillNumber = $r->resultParcelValue[0]->skybillNumber;
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            Mail::Send(
                $o->id_lang,
                'return',
                'Lettre de transport Chronopost pour le retour de votre commande',
                array(
                    '{id_order}' => $o->id,
                    '{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname
                ),
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                null,
                null,
                array(
                    'content' => $lt->pdfEtiquette,
                    'mime' => 'application/pdf',
                    'name' => $lt->skybillNumber.'.pdf'
                ),
                null,
                'mails/',
                true
            );
        } else {
            Mail::Send(
                $o->id_lang,
                'return',
                'Lettre de transport Chronopost pour le retour de votre commande',
                array(
                    '{id_order}' => $o->id,
                    '{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname
                ),
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                null,
                null,
                array(
                    'content' => $lt->pdfEtiquette,
                    'mime' => 'application/pdf',
                    'name' => $lt->skybillNumber.'.pdf'
                ),
                null,
                '/modules/chronopost/mails/',
                true
            );
        }
    } else {
        // Store LT for history
       foreach ($r->resultParcelValue as $item){
           Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'chrono_lt_history` VALUES (
				'.(int)$o->id.', 
				"'.pSQL($item->skybillNumber).'", 
				"'.pSQL($skybills[0]->productCode).'",
				"'.pSQL($recipient->recipientZipCode).'",
				"'.pSQL($recipient->recipientCountry).'",
				"'.(isset($skybills[0]->insuredValue) ? (int)$skybills[0]->insuredValue : 0).'",
				"'.pSQL($recipient->recipientCity).'",
				"'.pSQL($account['account']).'",
				NULL
			)');

           Chronopost::trackingStatus($o->id, $item->skybillNumber);
       }
    }

    return $r;
}
