<?php
/**
* MODULE PRESTASHOP OFFICIEL CHRONOPOST
*
* LICENSE : All rights reserved - COPY AND REDISTRIBUTION FORBIDDEN WITHOUT PRIOR CONSENT FROM OXILEO
* LICENCE : Tous droits réservés, le droit d'auteur s'applique -
* COPIE ET REDISTRIBUTION INTERDITES SANS ACCORD EXPRES D'OXILEO
*
* @author    Oxileo SAS <contact@oxileo.eu>
* @copyright 2001-2018 Oxileo SAS
* @license   Proprietary - no redistribution without authorization
*/
if (defined('__PS_VERSION_')) {
    exit('Restricted Access');
}

include(dirname(__FILE__).'/../../chronopost.php');
include(dirname(__FILE__).'/../../libraries/checkColis.php');

class AdminExportChronopostController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->bootstrap = true;
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->context = Context::getContext();

        $this->list_no_link = true; // so you can't click on rows. Ignore Prestashop docs.

        $this->_select = '
			a.id_order AS id_pdf,
			a.id_order AS account,
			a.id_order AS weight,
			a.id_order AS width,
			a.id_order AS height,
			a.id_order AS length,
			CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
			osl.`name` AS `osname`,
			os.`color`,
			IF((SELECT COUNT(so.id_order) FROM `'._DB_PREFIX_.'orders` so 
			    WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new,
			country_lang.name as cname,
			IF(a.valid, 1, 0) badge_success';

        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
			INNER JOIN `'._DB_PREFIX_.'carrier` ca ON (ca.`id_carrier` = a.`id_carrier`)
			INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
			INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
			INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` 
			    AND country_lang.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
			    AND osl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';

        $this->addJquery();
        $this->addJS(_MODULE_DIR_."chronopost/views/js/exportMenu.js");

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }
        
        $this->_where = Chronopost::buildControllerWhereQuery();
        parent::__construct();

        // fields_lists *HAS* to be initiated in constructor, not later
        $this->fields_list = array(
            'id_order' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'customer' => array(
                'title' => $this->l('Customer'),
                'widthColumn' => 160,
                'width' => 140,
                'filter_key' => 'customer',
                'tmpTableFilter' => true
            ),
            'payment' => array('title' => $this->l('Payment'), 'width' => 100),
            'osname' => array(
                'title'       => $this->l('Status'),
                'type'        => 'select',
                'color'       => 'color',
                'list'        => $this->statuses_array,
                'filter_key'  => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key'   => 'osname'
            ),
            'date_add' => array('title'      => $this->l('Date'),
                'width'      => 35,
                'align'      => 'right',
                'type'       => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'id_pdf' => array(
                'title' => $this->l('Waybills'),
                'align' => 'text-center',
                'callback' => 'nbWaybillsInput',
                'orderby' => false,
                'search' => false
            ),
            'account' => array(
                'title' => $this->l('Account to use'),
                'align' => 'text-center',
                'callback' => 'accountInput',
                'orderby' => false,
                'search' => false
            ),
            'weight' => array(
                'title' => $this->l('Weight'),
                'align' => 'text-center',
                'callback' =>'weightInput',
                'orderby' => false,
                'search' => false
            ),
            'length' => array(
                'title' => $this->l('Length'),
                'align' => 'text-center',
                'callback' =>'lengthInput',
                'orderby' => false,
                'search' => false
            ),
            'height' => array(
                'title' => $this->l('Height'),
                'align' => 'text-center',
                'callback' =>'heightInput',
                'orderby' => false,
                'search' => false
            ),
            'width' => array(
                'title' => $this->l('Width'),
                'align' => 'text-center',
                'callback' =>'widthInput',
                'orderby' => false,
                'search' => false
            ),
        );

        $this->bulk_actions = array(
//            'csoexport' => array(
//                'text' => $this->l('CSO export '),
//                'icon' => 'icon-save'
//            ),
            'cssexport' => array(
                'text' => $this->l('CSS export '),
                'icon' => 'icon-save'
            ),
            'waybills' => array(
                'text' => $this->l('Print all waybills'),
                'icon' => 'icon-print'
            ),
        );

        $this->displayInformation(
            $this->l(
                'For an export, select orders, then in the "Bulk Actions" menu, '
                .'select the type of export wanted.'
            )
        );

        if (Tools::getIsset('dlfile')) {
            $url = urldecode(Tools::getValue('dlfile'));
            $message = '<a target="_blank" href="'. $url .'">' . $this->l("If the download doesn't start 
            automatically, click here to download your file") .'</a>';
            $message .= '<meta http-equiv="refresh" content="2;url='. $url .'" />';
            $this->displayInformation($message);
        }
    }

    public function weightInput($id_order)
    {
        $order = new Order($id_order);
        $weight = $order->getTotalWeight();
        $this->context->smarty->assign(array(
            'weight' => $weight,
            'id_order' => $id_order
        ));
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/weight_input.tpl');
    }

    public function widthInput($id_order)
    {
        $this->context->smarty->assign(array(
            'id_order' => $id_order,
        ));
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/width_input.tpl');
    }

    public function heightInput($id_order)
    {
        $this->context->smarty->assign(array(
            'id_order' => $id_order,
        ));
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/height_input.tpl');
    }

    public function lengthInput($id_order)
    {
        $this->context->smarty->assign(array(
            'id_order' => $id_order,
        ));
        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/length_input.tpl');
    }

    public function accountInput($id_order){

        $order = new Order($id_order);
        $ltHistory = DB::getInstance()->executeS(
            'SELECT lt, account_number, product, zipcode, country, insurance, city  FROM '
            ._DB_PREFIX_.'chrono_lt_history WHERE id_order = ('.$id_order.') AND cancelled IS NULL'
        );
        $disable = (!empty($ltHistory));
        $carrier = new Carrier($order->id_carrier);
        $product_code = Chronopost::getCodeFromCarrier($carrier->id_reference);
        $defaultAccount = Chronopost::getAccountInformationByAccountNumber(Configuration::get('CHRONOPOST_'.$product_code.'_ACCOUNT'));
        $accountUsed = false;
        if (is_array($ltHistory) && isset($ltHistory[0]['account_number'])) {
            $accountUsed = $ltHistory[0]['account_number'];
        }

        $wsHelper = Chronopost::getWsHelper();
        $availContracts = $wsHelper->getContractsForMethod(Chronopost::$carriers_definitions[$product_code]['product_code']);

        $this->context->smarty->assign(array(
            'id_order' => $id_order,
            'disable' => $disable,
            'default_account' => $defaultAccount,
            'available_accounts' => $availContracts,
            'account_used' => $accountUsed
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/account_input.tpl');
    }

    public function nbWaybillsInput($id_order)
    {
        $this->context->smarty->assign(array(
            'id_order' => $id_order,
            'nbwb' => Chronopost::minNumberOfPackages($id_order),
        ));

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/nb_waybill_input.tpl');
    }

    public function processBulkcsoexport()
    {
        $order_box = Tools::getValue('orderBox');

        if (empty($order_box)) {
            $this->displayWarning($this->l('You must selected orders for the export'));
            return;
        }

        $url = '../modules/chronopost/importExport.php?shared_secret='
            .Configuration::get('CHRONOPOST_SECRET')
            .'&cible=CSO&orders='.implode(';', Tools::getValue('orderBox'))
            .'&multi='.urlencode(Tools::jsonEncode(Tools::getValue('multi')));
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminExportChronopost')
            . '&dlfile=' . urlencode($url));
    }

    public function processBulkcssexport()
    {
        $order_box = Tools::getValue('orderBox');

        if (empty($order_box)) {
            $this->displayWarning($this->l('You must selected orders for the export'));
            return;
        }

        $url = '../modules/chronopost/importExport.php?shared_secret='
            .Configuration::get('CHRONOPOST_SECRET')
            .'&cible=CSS&orders='.implode(';', Tools::getValue('orderBox'))
            .'&multi='.urlencode(Tools::jsonEncode(Tools::getValue('multi')));
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminExportChronopost')
            . '&dlfile=' . urlencode($url));
    }


    public function processBulkwaybills()
    {
        $order_box = Tools::getValue('orderBox');

        if (empty($order_box)) {
            $this->errors[] = sprintf(Tools::displayError($this->l('You must selected orders for the export')));
            return;
        }

        foreach ($order_box as $order){
            $ltHistory = DB::getInstance()->executeS(
                'SELECT lt, account_number, product, zipcode, country, insurance, city  FROM '
                ._DB_PREFIX_.'chrono_lt_history WHERE id_order = ('.$order.') AND cancelled IS NULL'
            );
            $ltFound = !empty($ltHistory);
            $result = json_decode(checkColis::check(
                new Order($order),
                Tools::getValue('weight')[$order],
                Tools::getValue('width')[$order] ,
                Tools::getValue('height')[$order],
                Tools::getValue('length')[$order]
            ),1);
            if($result['error'] !== 0){
                $this->errors[] = sprintf(Tools::displayError($this->l($result['message'])));
                return;
            }
            if($ltFound){
                $this->errors[] = sprintf(Tools::displayError($this->l('Order was already shipped')));
            }
        }

        Tools::redirectAdmin(
            '../modules/chronopost/postSkybill.php?shared_secret='
            .Configuration::get('CHRONOPOST_SECRET')
            .'&orders='.implode(';', Tools::getValue('orderBox'))
            .'&multi='.addslashes(Tools::jsonEncode(Tools::getValue('multi')))
            .'&accounts='.addslashes(Tools::jsonEncode(Tools::getValue('account')))
            .'&weights=' .addslashes(Tools::jsonEncode(Tools::getValue('weight')))
            .'&widths=' .addslashes(Tools::jsonEncode(Tools::getValue('width')))
            .'&lengths=' .addslashes(Tools::jsonEncode(Tools::getValue('length')))
            .'&heights=' .addslashes(Tools::jsonEncode(Tools::getValue('height')))
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        // Remove "Add" button from toolbar
        unset($this->toolbar_btn['new']);
        unset($this->toolbar_btn['export']);
    }

    public function initContent()
    {
        if(isset($_SESSION['chronopost_errors'])){
            foreach ($_SESSION['chronopost_errors'] as $message) {
                $this->errors[] = $message;
            }
            unset($_SESSION['chronopost_errors']);
        }
        return parent::initContent();
    }

    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation(
            'chronopost',
            $string,
            Tools::substr(get_class($this), 0, -10),
            null,
            false
        );
    }
}
