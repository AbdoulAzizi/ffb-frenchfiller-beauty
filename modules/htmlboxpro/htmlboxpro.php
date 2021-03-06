<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

require_once _PS_MODULE_DIR_ . 'htmlboxpro/models/hbox.php';

class htmlboxpro extends Module
{
    public $smartyTemplatesManager;
    public $searchTool;

    function __construct()
    {
        ini_set("display_errors", 0);
        error_reporting(0); //E_ALL
        $this->name = 'htmlboxpro';
        $this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/html-box-pro.html';
        $this->version = '3.7.1';
        $this->dir = '/modules/htmlboxpro/';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('HTML Box Pro');
        $this->description = $this->l('With this module you can put the HTML/JavaScript/CSS code anywhere you want');
        $this->allhooks = array(
            'displayHeader',
            'displayBanner',
            'displayNav1',
            'displayNav2',
            'displayNavFullWidth',
            'displayTop',
            'displayHome',
            'displayFooterBefore',
            'displayFooter',
            'displayFooterAfter',
            'displayMyAccountBlock',
            'displayBeforeBodyClosingTag',
            'displayLeftColumn',
            'displayLeftColumnProduct',
            'displayRightColumn',
            'displayRightColumnProduct',
            'displayCustomerAccount',
            'displayCustomerAccountForm',
            'displayProductAdditionalInfo',
            'displayReassurance',
            'displayFooterProduct',
            'displayAfterProductThumbs',
            'displayProductListReviews',
            'actionProductOutOfStock',
            'displayShoppingCart',
            'displayCartExtraProductActions',
            'displayShoppingCartFooter',
            'displayExpressCheckout',
            'displayCustomerLoginFormAfter',
            'displayBeforeCarrier',
            'displayAfterCarrier',
            'displayPaymentTop',
            'displayPaymentByBinaries'
        );
        $this->checkforupdates();
    }

    public function hookactionAdminControllerSetMedia($params)
    {
        if (Tools::getValue('configure') == 'htmlboxpro') {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin('autocomplete');
        }
        //for update feature purposes
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = htmlboxproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (htmlboxproUpdate::version($this->version) < htmlboxproUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = htmlboxproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (htmlboxproUpdate::version($this->version) < htmlboxproUpdate::version(htmlboxproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $statements = array();

        $statements[] = "
        CREATE TABLE IF NOT EXISTS `${prefix}hbp_block` (
         `id` INT(10) NOT NULL AUTO_INCREMENT,
         `position` INT(10) NOT NULL DEFAULT '1',
         `hook` VARCHAR(50) NULL DEFAULT NULL,
         `active` INT(11) NOT NULL DEFAULT '0',
         `logged` INT(11) NOT NULL DEFAULT '0',
         `name` VARCHAR(150) NULL DEFAULT NULL,
         INDEX `indek1` (`id`)
        ) COLLATE='utf8_general_ci'";

        $statements[] = "
        CREATE TABLE IF NOT EXISTS `${prefix}hbp_customhook` (
         `id` INT(10) NOT NULL AUTO_INCREMENT,
         `hook` VARCHAR(70) NULL DEFAULT NULL,
         INDEX `indekch1` (`id`)
        ) COLLATE='utf8_general_ci'";


        $statements[] = "
        CREATE TABLE IF NOT EXISTS `${prefix}hbp_block_lang` (
        `id` INT(10) NULL DEFAULT NULL,
        `id_lang` INT(10) NULL DEFAULT NULL,
        `body` TEXT NULL
        ) COLLATE='utf8_general_ci'";


        foreach ($statements as $statement) {
            if (@!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }
        $this->inconsistency(0);
        return true;
    }

    private function maybeUpdateDatabase($table, $column, $type = "int(8)", $default = "1", $null = "NULL", $onUpdate = '', $drop = false, $wtd = 'ADD')
    {
        $sql = 'DESCRIBE ' . _DB_PREFIX_ . $table;
        $columns = Db::getInstance()->executeS($sql);
        $found = false;
        foreach ($columns as $col) {
            if ($col['Field'] == $column) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            if ($drop == false) {
                if (!Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ' . $wtd . ' `' . $column . '` ' . $type . ' DEFAULT ' . $default . ' ' . $null . ' ' . $onUpdate)) {
                    return false;
                }
            }
        } else {
            if ($wtd == 'MODIFY') {
                if (!Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ' . $wtd . ' `' . $column . '` ' . $type . ' DEFAULT ' . $default . ' ' . $null . ' ' . $onUpdate)) {
                    return false;
                }
            }

            if ($drop == true) {
                if (!Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` DROP COLUMN `' . $column . '`')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function inconsistency($return_report = 1)
    {
        $this->maybeUpdateDatabase('hbp_block', 'position', "INT(9)", 1, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'hook', "VARCHAR(255)", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'active', "INT(9)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'logged', "INT(9)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'name', "VARCHAR(150)", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'bssl', "INT(9)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'shop', "INT(4)", 1, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'homeonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'specialsonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'productsonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selectedproducts', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'cmsonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selectedcms', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'productscat', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_pcats', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'productsman', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_pmanufs', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'catsonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_cats', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'manufsonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_manufs', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'date', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'datefrom', "VARCHAR(60)", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'dateto', "VARCHAR(60)", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'urlonly', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'url', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'cgroup', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'cgroup', "VARCHAR(250)", '', "NULL", '', false, 'MODIFY');
        $this->maybeUpdateDatabase('hbp_block', 'hcgroup', "INT(5)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'hcgroup', "INT(5)", '', "NULL", '', false, 'MODIFY');
        $this->maybeUpdateDatabase('hbp_block', 'search', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'query', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'oconfirmation', "VARCHAR(1)", '0', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'cmscatsonly', "VARCHAR(1)", '0', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_cmscats', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'supponly', "VARCHAR(1)", '0', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_supp', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'tim', "VARCHAR(1)", '0', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'timfrom', "VARCHAR(60)", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'timto', "VARCHAR(60)", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'poos', "VARCHAR(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'pins', "VARCHAR(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'onmobile', "INT(1)", 1, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'ontablet', "INT(1)", 1, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'onpc', "INT(1)", 1, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'pminprice', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'pminpricev', "VARCHAR(12)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'pmaxprice', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'pmaxpricev', "VARCHAR(12)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'excats', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_excats', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'exproductsall', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'exproducts', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_exproducts', "TEXT", '', "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'daytype', "VARCHAR(20)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'daytype_on', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'currency_on', "INT(1)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'currency', "INT(4)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'geoip', "VARCHAR(5)", 0, "NULL");
        $this->maybeUpdateDatabase('hbp_block', 'selected_geoip', "TEXT", '', "NULL");
        return;
    }

    public function renderGeoIp($block = false)
    {
        return '<div class="alert alert-warning">' .
            $this->l('Module to identify customer country uses geolocation.') . ' ' .
            $this->l('In order to use Geolocation, please download') . ' ' .
            '<a href="https://mypresta.eu/prestashop-17/geolite2-city-geolocation-download.html">' .
            $this->l('this file') . '</a> ' .
            $this->l('and extract it (using Winrar or Gzip) into the /app/Resources/geoip/ directory.') .
            $this->l('Please note that geolocation feature does not work on localhost environment') .
            '</div><label>' . $this->l('Countries selection', 'forms') . '</label><div class="margin-form">' . $this->countriesSelection($block == false ? false : $block->selected_geoip) . '</div>';
    }

    public function countriesSelection($selected = false)
    {
        if ($selected) {
            $selected_array = explode(';', $selected);
        } else {
            $selected_array = array();
        }
        $form = '
            <div class="well" style="height: 300px; overflow-y: auto;">
			<table class="table" style="border-spacing : 0; border-collapse : collapse;">
				<thead>
					<tr>
						<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, \'countries[]\', this.checked)"></th>
						<th>' . $this->l('Name') . '</th>
					</tr>
				</thead>
				<tbody>';

        foreach (Country::getCountries($this->context->language->id) AS $key => $country) {
            $element_selected = "";
            foreach ($selected_array AS $item => $selected) {
                if ($selected == $country['iso_code']) {
                    $element_selected = "checked";
                }
            }
            $form .= '
                    <tr>
                        <td><input type="checkbox" name="countries[]" value="' . $country['iso_code'] . '" ' . $element_selected . '></td>
                        <td>' . $country['name'] . '</td>
                    </tr>';
        }
        $form .= '
			    </tbody>
			</table>
		</div>';

        return $form;
    }

    public function returnUserCountry()
    {
        $record = false;
        if (!in_array($_SERVER['SERVER_NAME'], array(
            'localhost',
            '127.0.0.1'
        ))
        ) {
            /* Check if Maxmind Database exists */
            if (@filemtime(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_)) {
                $reader = new GeoIp2\Database\Reader(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_);
                try {
                    $record = $reader->city(Tools::getRemoteAddr());
                } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                    $record = null;
                }

                if (isset($record->country->isoCode)) {
                    return $record->country->isoCode;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    static function remove_doublewhitespace($s = null)
    {
        return $ret = preg_replace('/([\s])\1+/', ' ', $s);
    }

    static function remove_whitespace($s = null)
    {
        $ret = preg_replace('/[\s]+/', '', $s);
        $ret = Db::getInstance()->escape($ret, true);
        return $ret;
    }

    static function remove_whitespace_feed($s = null)
    {
        return $s;
        //return $ret = preg_replace('/[\t\n\r\0\x0B]/', ' ', $s);
    }

    static function prepare($v)
    {
        $value = preg_replace("/\s+/", ' ', $v);
        $value = preg_replace('/(\v|\s)+/', ' ', $v);
        $value = preg_replace("/[\r\n]+/", "", $v);
        $value = str_replace(CHR(13) . CHR(10), "", $v);
        $value = str_replace("  ", " ", $v);
        $value = str_replace("'", "\'", $v);
        $value = str_replace(array("\rn", "\r", "\n", "\t"), array(' ', ' ', ' '), $v);
        $value = str_replace(array("\\'", "\\\\", "\\\""), array('\'', "\""), $v);
        return $value;
    }

    static function smart_clean($s = null)
    {
        return $ret = trim(self::prepare(self::remove_doublewhitespace(self::remove_whitespace_feed($s))));
    }

    public static function currentPageURL()
    {
        $pageURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
        $pageReplacedURL = preg_replace('/rand\=([0-9]*)/', '', $pageURL);
        $pageReplacedURL = trim($pageReplacedURL, "?");
        return $pageReplacedURL;
    }

    public function install()
    {
        if (parent::install() == false OR $this->installdb() == false OR $this->installHooks() == false) {
            return false;
        }
        return true;
    }

    public function installHooks()
    {
        $this->registerHook('actionAdminControllerSetMedia');
        foreach ($this->allhooks AS $hook) {
            $this->registerHook($hook);
        }
        return true;
    }

    public function prepare_variables($hook, $body, $params)
    {
        if (Tools::getValue('controller', 'false') != 'false') {
            if (Tools::getValue('controller') != 'AdminModules') {
                //GLOBAL HOOKS
                $currency = new Currency($this->context->currency->id);
                $body = str_replace('{currency_iso}', $currency->iso_code, $body);

                if ($this->context->customer->islogged()) {
                    $body = str_replace('{id_customer}', $this->context->customer->id, $body);
                    $body = str_replace('{email_customer}', $this->context->customer->email, $body);
                    $body = str_replace('{customer_firstname}', $this->context->customer->firstname, $body);
                    $body = str_replace('{customer_lastname}', $this->context->customer->lastname, $body);
                }

                // PRODUCT PAGE
                if (Tools::getValue('controller', 'false') != "false") {
                    if (Tools::getValue('controller') == "product") {
                        $body = str_replace('{id_product}', Tools::getValue('id_product'), $body);
                        $body = str_replace('{id_product_attribute}', (Tools::getValue('id_product_attribute', 0) != 0 ? Tools::getValue('id_product_attribute') : ''), $body);
                        if (!isset($params['product']['id_product'])) {
                            $params['product'] = (array)new Product(Tools::getValue('id_product'), true, $this->context->language->id);
                            $params['product']['id_product'] = $params['product']['id'];
                        }
                    }
                }

                // ISSET PRODUCT
                if (isset($params['product']['id_product']) || isset($params['product']['id'])) {
                    if (Tools::version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
                        $params['product'] = (array)$params['product'];
                    }

                    if (!isset($params['product']['id_product'])) {
                        if (isset($params['product']['id'])) {
                            $params['product']['id_product'] = $params['product']['id'];
                        }
                    } else {
                        if (isset($params['product']['id_produt'])) {
                            $params['product']['id'] = $params['product']['id_product'];
                        }
                    }

                    if (!isset($params['product']['id_product_attribute'])) {
                        $params['product']['id_product_attribute'] = 0;
                    }

                    if (isset($params['product']['id_manufacturer'])) {
                        $manufacturer = Manufacturer::getnamebyid($params['product']['id_manufacturer']);
                        $body = str_replace('{manufacturer_name}', $manufacturer, $body);
                    } else {
                        $body = str_replace('{manufacturer_name}', '', $body);
                    }


                    $body = str_replace('{product_ean13}', $params['product']['ean13'], $body);
                    $body = str_replace('{product_isbn}', $params['product']['isbn'], $body);

                    $body = str_replace('{id_product}', $params['product']['id_product'], $body);
                    $body = str_replace('{product_name}', $params['product']['name'], $body);
                    $body = str_replace('{id_product_attribute}', ($params['product']['id_product_attribute'] != 0 ? $params['product']['id_product_attribute'] : ''), $body);

                    preg_match_all('/\{product_price_tax_incl\*[(0-9\.)]+\}/i', $body, $matches);
                    foreach ($matches[0] as $index => $match) {
                        $explode = explode("*", $match);
                        $explode[1] = str_replace("}", "", $explode[1]);
                        $body = str_replace($match, Tools::displayPrice(Product::getPriceStatic($params['product']['id_product'], true, 0, 6, null, false, false, 1, false, null) * $explode[1]), $body);
                    }
                    preg_match_all('/\{product_price_tax_excl\*[(0-9\.)]+\}/i', $body, $matches);
                    foreach ($matches[0] as $index => $match) {
                        $explode = explode("*", $match);
                        $explode[1] = str_replace("}", "", $explode[1]);
                        $body = str_replace($match, Tools::displayPrice(Product::getPriceStatic($params['product']['id_product'], false, 0, 6, null, false, false, 1, false, null) * $explode[1]), $body);
                    }
                    $body = str_replace('{product_price_tax_incl}', Tools::displayPrice(Product::getPriceStatic($params['product']['id_product'], true, 0, 6, null, false, false, 1, false, null)), $body);
                    $body = str_replace('{product_price_tax_excl}', Tools::displayPrice(Product::getPriceStatic($params['product']['id_product'], false, 0, 6, null, false, false, 1, false, null)), $body);
                    $body = str_replace('{product_price_tax_incl_no_currency}', (Product::getPriceStatic($params['product']['id_product'], true, 0, 6, null, false, false, 1, false, null)), $body);
                    $body = str_replace('{product_price_tax_excl_no_currency}', (Product::getPriceStatic($params['product']['id_product'], false, 0, 6, null, false, false, 1, false, null)), $body);
                }

                // CATEGORY PAGE
                if (Tools::getValue('controller', 'false') != "false") {
                    if (Tools::getValue('controller') == "category") {
                        $category = new Category(Tools::getValue('id_category'), $this->context->language->id);
                        $parents = $category->getParentsCategories($this->context->language->id);
                        $parents = array_reverse($parents);
                        $parents_array = '';
                        foreach ($parents as $parent => $parent_category) {
                            $parents_array .= "'" . $parent_category['name'] . "',";
                        }
                        $body = str_replace('{id_category}', Tools::getValue('id_category'), $body);
                        $body = str_replace('{name_category}', $category->name, $body);
                        $body = str_replace('{path_array_category}', rtrim($parents_array, ','), $body);
                    }

                    if (isset($this->context->cart)) {
                        if (isset($this->context->cart->id)) {
                            if ($this->context->cart->id != null) {
                                $cartProducts = $this->context->cart->getProducts();
                                if ($cartProducts != false) {
                                    $cpa = array();
                                    foreach ($cartProducts AS $cp => $cpv) {
                                        $cpa[] = $cpv['id_product'];
                                    }
                                    $body = str_replace('{cart_products_id}', implode(',', $cpa), $body);
                                }

                                $taxCalculationMethod = Group::getPriceDisplayMethod((int)Group::getCurrent()->id);
                                $useTax = !($taxCalculationMethod == PS_TAX_EXC);
                                $totalToPay = $this->context->cart->getOrderTotal($useTax);
                                $c_decimals = $this->context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
                                $body = str_replace('{cart_total}', Tools::ps_round($totalToPay, $c_decimals), $body);
                                $body = str_replace('{cart_total_with_currency}', Tools::displayPrice($totalToPay), $body);
                            }
                        }
                    }

                    if (Tools::getValue('controller') == "orderconfirmation" || strtolower($hook) == 'displayorderconfirmation' || strtolower($hook) == 'orderconfirmation') {
                        if (Tools::getValue('id_order', 'false') != 'false') {
                            $order = new Order((int)Tools::getValue('id_order'));
                            $ora = array();
                            foreach ($order->getProducts(false, false, false) as $or => $orv) {
                                $ora[] = $orv['id_product'];
                            }

                            $currency = new Currency($order->id_currency);
                            $body = str_replace('{order_products_id}', implode(',', $ora), $body);
                            $body = str_replace('{order_id}', Tools::getValue('id_order'), $body);
                            $body = str_replace('{order_currency_iso_code}', $currency->iso_code, $body);
                            $body = str_replace('{order_total_paid}', number_format($order->total_paid, 2, ".", ""), $body);
                            $body = str_replace('{order_total_paid_tax_excl}', number_format($order->total_paid_tax_excl, 2, ".", ""), $body);
                            $body = str_replace('{order_total_paid_tax_incl}', number_format($order->total_paid_tax_incl, 2, ".", ""), $body);
                            $body = str_replace('{order_total_products_tax_included}', number_format($order->total_products_wt, 2, ".", ""), $body);
                            $body = str_replace('{order_total_products_tax_excluded}', number_format($order->total_products, 2, ".", ""), $body);
                        }
                    }
                }

                /** HOOK RUN EXACT MODULE **/
                preg_match_all('/\{HOOK\:[(A-Za-z0-9\_)]+\:[(A-Za-z0-9\_)]+\}/i', $body, $matches);
                foreach ($matches[0] as $index => $match) {
                    $explode = explode(":", $match);
                    $body = str_replace($match, Hook::exec(str_replace("}", "", $explode[1]), array(), Module::getModuleIdByName(str_replace("}", "", $explode[2]))), $body);

                }

                /** HOOK RUN **/
                preg_match_all('/\{HOOK\:[(A-Za-z0-9\_)]+\}/i', $body, $matches);
                foreach ($matches[0] as $index => $match) {
                    $explode = explode(":", $match);
                    $body = str_replace($match, (strtolower($hook) != strtolower(str_replace("}", "", $explode[1])) ? Hook::exec(str_replace("}", "", $explode[1])) : ''), $body);
                }

                /** LOAD SMARTY TEMPLATE **/
                preg_match_all('/\{smartyTemplate\:[(A-Za-z0-9)]+\}/i', $body, $matches);
                foreach ($matches[0] as $index => $match) {
                    $explode = explode(":", $match);
                    $body = str_replace($match, $this->loadSmartyTemplateFromManager(str_replace("}", "", $explode[1])), $body);
                }
            }
        }

        // GLOBAL VARIABLES
        $actual_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $body = str_replace('{current_url}', $actual_url, $body);

        // HIDE VARIABLES IF NOT REPLACED
        $body = str_replace('{id_customer}', '', $body);
        $body = str_replace('{email_customer}', '', $body);
        $body = str_replace('{customer_firstname}', '', $body);
        $body = str_replace('{customer_lastname}', '', $body);
        $body = str_replace('{order_id}', '', $body);
        $body = str_replace('{order_currency_iso_code}', '', $body);
        $body = str_replace('{order_total_paid}', '', $body);
        $body = str_replace('{order_total_products_tax_included}', '', $body);
        $body = str_replace('{order_total_products_tax_excluded}', '', $body);
        $body = str_replace('{id_category}', '', $body);
        $body = str_replace('{name_category}', '', $body);
        $body = str_replace('{path_array_category}', '', $body);
        $body = str_replace('{order_products_id}', '', $body);
        $body = str_replace('{currency_iso}', '', $body);
        $body = str_replace('{cart_products_id}', '', $body);
        $body = str_replace('{cart_total}', '', $body);
        $body = str_replace('{cart_total_with_currency}', '', $body);
        $body = str_replace('{id_product}', '', $body);
        $body = str_replace('{product_name}', '', $body);
        $body = str_replace('{id_product_attribute}', '', $body);
        $body = str_replace('{product_price_tax_incl}', '', $body);
        $body = str_replace('{product_price_tax_incl_no_currency}', '', $body);
        $body = str_replace('{product_price_tax_excl}', '', $body);
        $body = str_replace('{product_price_tax_excl_no_currency}', '', $body);
        $body = str_replace('{manufacturer_name}', '', $body);
        $body = str_replace('{product_isbn}', '', $body);
        $body = str_replace('{product_ean13}', '', $body);
        return $body;
    }

    public function loadSmartyTemplateFromManager($file)
    {
        $smarty_file = _PS_MODULE_DIR_ . 'htmlboxpro/lib/smartyTemplatesManager/tpl/' . $file . '.tpl';
        if (file_exists($smarty_file)) {
            return $this->context->smarty->fetch($smarty_file);
        } else {
            return '<div class="alert alert-warning">' . $this->l('Unable to load smarty file:') . ' ' . $file . '.tpl </div>';
        }
    }

    public static function returnAssociatedProductSuppliers()
    {
        if (Tools::getValue('id_product')) {
            $product = new Product(Tools::getValue('id_product'));
            return $product->id_supplier;
        }
    }

    public static function returnAssociatedProductManufacturer()
    {
        if (Tools::getValue('id_product')) {
            $product = new Product(Tools::getValue('id_product'));
            return $product->id_manufacturer;
        }
    }

    public function get_blocks($hook, $active = null, $lang = null, $params = null, $front = false)
    {
        $innerjoin = '';
        $whereactive = '';
        $wherelang = '';
        $whereshop = '';

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1) {
            $whereshop = 'AND (shop="' . $this->context->shop->id . '" OR shop=0)';
        }


        if ($active == 1) {
            $whereactive = "AND active=1";
        }

        if (!is_null($lang)) {
            $innerjoin = 'INNER JOIN `' . _DB_PREFIX_ . 'hbp_block_lang` AS b ON a.id=b.id';
            $wherelang = "AND b.id_lang=" . $lang;
        }
        $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'hbp_block` AS a ' . $innerjoin . ' WHERE a.hook="' . $hook . '" ' . $whereactive . ' ' . $wherelang . ' ' . $whereshop . ' ORDER BY a.position');

        if (count($query) > 0) {
            foreach ($query as $blck => $key) {
                if (isset($query[$blck]['selectedproducts'])) {
                    $query[$blck]['selectedproducts'] = explode(',', $query[$blck]['selectedproducts']);
                }
                if (isset($query[$blck]['selectedcms'])) {
                    $query[$blck]['selectedcms'] = explode(',', $query[$blck]['selectedcms']);
                }

                if (isset($query[$blck]['selected_cmscats'])) {
                    $query[$blck]['selected_cmscats'] = explode(',', $query[$blck]['selected_cmscats']);
                }

                if (isset($query[$blck]['selected_pcats'])) {
                    $query[$blck]['selected_pcats'] = explode(',', $query[$blck]['selected_pcats']);
                }

                if (isset($query[$blck]['selected_pmanufs'])) {
                    $query[$blck]['selected_pmanufs'] = explode(',', $query[$blck]['selected_pmanufs']);
                }

                if (isset($query[$blck]['selected_supp']) && $query[$blck]['supponly'] == 1) {
                    $query[$blck]['selected_supp'] = explode(',', $query[$blck]['selected_supp']);
                }

                if (isset($query[$blck]['cgroup'])) {
                    $query[$blck]['cgroup'] = explode(',', $query[$blck]['cgroup']);
                    if (isset($query[$blck]['cgroup'][0])) {
                        if ($query[$blck]['cgroup'][0] == "") {
                            $query[$blck]['cgroup'] = 0;
                        }
                    }
                }

                $query[$blck]['pcurrentprice'] = 0;
                if (isset($query[$blck]['pmaxprice']) || isset($query[$blck]['pminprice'])) {
                    if ($query[$blck]['pmaxprice'] == 1 || $query[$blck]['pminprice'] == 1) {
                        if ((Tools::getValue('controller', 'false') == "product") || (isset($params['product']['id_product']) || isset($params['product']['id']))) {
                            if (isset($params['product']['id_product'])) {
                                $id_product = $params['product']['id_product'];
                            } elseif (isset($params['product']['id'])) {
                                $id_product = $params['product']['id'];
                            } elseif (Tools::getValue('controller', 'false') == "product") {
                                $id_product = Tools::getValue('id_product');
                            }
                            $product = new Product($id_product, true, Context::getContext()->language->id, Context::getContext()->shop->id);
                            if (!isset($params['product']['id_product_attribute'])) {
                                $groups = Tools::getValue('group');
                                if (!empty($groups)) {
                                    $requestedIdProductAttribute = (int)Product::getIdProductAttributesByIdAttributes($product->id, $groups);
                                } else {
                                    $requestedIdProductAttribute = Tools::getValue('id_product_attribute', 0);
                                }
                            } else {
                                $requestedIdProductAttribute = $params['product']['id_product_attribute'];
                            }
                            $query[$blck]['pcurrentprice'] = Product::getPriceStatic($id_product, true, $requestedIdProductAttribute);
                        }
                    }
                }

                if (isset($query[$blck]['poos']) || isset($query[$blck]['pins'])) {
                    if ($query[$blck]['poos'] == 1 || $query[$blck]['pins'] == 1) {
                        if ((Tools::getValue('controller', 'false') == "product") || (isset($params['product']['id_product']) || isset($params['product']['id']))) {
                            if (isset($params['product']['id_product'])) {
                                $id_product = $params['product']['id_product'];
                            } elseif (isset($params['product']['id'])) {
                                $id_product = $params['product']['id'];
                            } elseif (Tools::getValue('controller', 'false') == "product") {
                                $id_product = Tools::getValue('id_product');
                            }

                            $product = new Product($id_product, true, Context::getContext()->language->id, Context::getContext()->shop->id);
                            if (!isset($params['product']['id_product_attribute'])) {
                                $groups = Tools::getValue('group');
                                if (!empty($groups)) {
                                    $requestedIdProductAttribute = (int)Product::getIdProductAttributesByIdAttributes($product->id, $groups);
                                } else {
                                    $requestedIdProductAttribute = Tools::getValue('id_product_attribute', 0);
                                }
                            } else {
                                $requestedIdProductAttribute = $params['product']['id_product_attribute'];
                            }


                            $attribute_stock = StockAvailable::getQuantityAvailableByProduct($product->id, $requestedIdProductAttribute);

                            $query[$blck]['poos_stock'] = $attribute_stock;
                            $query[$blck]['pins_stock'] = $attribute_stock;
                        }

                    }
                }

                if (isset($query[$blck]['url'])) {
                    $urls = explode(',', $query[$blck]['url']);
                    if (count($urls) > 0) {
                        foreach ($urls AS $url) {
                            $query[$blck]['urls'][] = trim($url);
                        }
                    }
                }

                if (isset($query[$blck]['query'])) {
                    $keywords = explode(',', $query[$blck]['query']);
                    foreach ($keywords AS $keyword) {
                        $query[$blck]['keywords'][] = trim($keyword);
                    }
                }

                if (isset($query[$blck]['selected_cats'])) {
                    if ($query[$blck]['selected_cats'] != "") {
                        $query[$blck]['selected_cats'] = explode(',', $query[$blck]['selected_cats']);
                    } else {
                        $query[$blck]['selected_cats'] = "-";
                    }
                }

                if (isset($query[$blck]['daytype'])) {
                    $query[$blck]['daytype'] = explode(',', $query[$blck]['daytype']);
                }

                if (isset($query[$blck]['selected_geoip'])) {
                    $query[$blck]['selected_geoip'] = explode(';', $query[$blck]['selected_geoip']);
                    $query[$blck]['user_geoip'] = $this->returnUserCountry();
                }

                /* EXCLUSIONS */

                if (isset($query[$blck]['selected_excats'])) {
                    if ($query[$blck]['selected_excats'] != "") {
                        $query[$blck]['selected_excats'] = explode(',', $query[$blck]['selected_excats']);
                    } else {
                        $query[$blck]['selected_excats'] = "-";
                    }
                }

                if (isset($query[$blck]['selected_exproducts'])) {
                    if ($query[$blck]['selected_exproducts'] != "") {
                        $query[$blck]['selected_exproducts'] = explode(',', $query[$blck]['selected_exproducts']);
                    } else {
                        $query[$blck]['selected_exproducts'] = "-";
                    }
                }


                $query[$blck]['selected_manufs'] = explode(',', $query[$blck]['selected_manufs']);
                if (isset($query[$blck]['body'])) {
                    $query[$blck]['body'] = $this->prepare_variables($hook, $query[$blck]['body'], $params);
                }

                if ($front == true) {
                    //TIME FROM
                    if ($query[$blck]['tim'] == 1 && $query[$blck]['timfrom'] != '') {
                        $time_now = str_replace(":", "", date("H:i:s"));
                        $time_from = str_replace(":", "", $query[$blck]['timfrom']);
                        if ((int)$time_from <= (int)$time_now) {
                            $time_from_ver = 1;
                        } else {
                            $time_from_ver = 0;
                        }
                    } else {
                        $time_from_ver = 1;
                    }


                    //TIME TO
                    if ($query[$blck]['tim'] == 1 && $query[$blck]['timto'] != '') {
                        $time_now = ltrim(str_replace(":", "", date("H:i:s")), 0);
                        $time_to = ltrim(str_replace(":", "", $query[$blck]['timto']), 0);

                        if ((int)$time_to >= (int)$time_now) {
                            $time_to_ver = 1;
                        } else {
                            $time_to_ver = 0;
                        }
                    } else {
                        $time_to_ver = 1;
                    }


                    if ($query[$blck]['tim'] == 1 && ($time_to_ver == 0 || $time_from_ver == 0)) {
                        if (isset($query[$blck])) {
                            unset($query[$blck]);
                        }
                    }
                }

            }
        }
        return $query;
    }

    public static function get_block($id)
    {
        $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'hbp_block` AS a WHERE a.id="' . $id . '"');
        $lang = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'hbp_block_lang` AS a WHERE a.id="' . $id . '"');
        foreach ($lang as $k => $v) {
            $query[0]['body'][$v['id_lang']] = $v['body'];
        }
        return $query;
    }

    public function lastslide()
    {
        $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id FROM `' . _DB_PREFIX_ . 'hbp_block` ORDER BY id DESC LIMIT 1');
        if (isset($query[0]['id'])) {
            return $query[0]['id'];
        } else {
            return false;
        }
    }

    public function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function generateGroups($selected = null)
    {
        $selectedd = "";
        $return = '';
        foreach (Group::getGroups(Configuration::get('PS_LANG_DEFAULT')) as $key => $value) {
            if ($selected) {
                if ($value['id_group'] == $selected) {
                    $selectedd = "selected='yes'";
                } else {
                    $selectedd = "";
                }
            }
            $return .= '<option value="' . $value['id_group'] . '" ' . $selectedd . '>(' . $value['id_group'] . ') ' . $value['name'] . '</option>';
        }
        return $return;
    }

    public function checkHookInModuleFile($hook)
    {
        if (strpos(file_get_contents("../modules/htmlboxpro/htmlboxpro.php"), "function hook" . $hook . "(") !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllHooks()
    {
        $hook = array();
        $default_hooks = array();
        foreach ($this->getListNewHook() AS $h => $value) {
            $hook[]['hook'] = $value['hook'];
        }
        foreach ($this->allhooks AS $hh => $vvalue) {
            $default_hooks[]['hook'] = $vvalue;
        }
        return array_merge($hook, $default_hooks);
    }

    public function getContent()
    {
        $this->searchTool = new searchToolHtmlBoxPro($this->name, $this->tab);
        $this->smartyTemplatesManager = new htmlboxprosmartyTemplatesManager($this->name);
        $output = "";
        if (Tools::getValue('submitAddNewHbox')) {
            $hbox = new hbox();
            $hbox->shop = Tools::getValue('hbp_shop');
            $hbox->name = Tools::getValue('hbp_name');
            $hbox->hook = Tools::getValue('hbp_hook');
            $hbox->active = Tools::getValue('hbp_active');
            $hbox->bssl = Tools::getValue('hbp_bssl');
            $hbox->homeonly = Tools::getValue('hbp_homeonly');
            $hbox->specialsonly = Tools::getValue('hbp_specialsonly');
            $hbox->oconfirmation = Tools::getValue('hbp_oconfirmation');
            $hbox->productsonly = Tools::getValue('hbp_productsonly');
            $hbox->selectedproducts = Tools::getValue('hbp_selectedproducts');
            $hbox->productscat = Tools::getValue('hbp_productscat');
            $hbox->selected_pcats = Tools::getValue('hbp_selected_pcats');
            $hbox->productsman = Tools::getValue('hbp_productsman');
            $hbox->selected_pmanufs = Tools::getValue('hbp_selected_pmanufs');
            $hbox->catsonly = Tools::getValue('hbp_catsonly');
            $hbox->selected_cats = Tools::getValue('hbp_selected_cats');
            $hbox->cmscatsonly = Tools::getValue('hbp_cmscatsonly');
            $hbox->selected_cmscats = Tools::getValue('hbp_selected_cmscats');
            $hbox->cmsonly = Tools::getValue('hbp_cmsonly');
            $hbox->selectedcms = Tools::getValue('hbp_selectedcms');
            $hbox->manufsonly = Tools::getValue('hbp_manufsonly');
            $hbox->selected_manufs = Tools::getValue('hbp_selected_manufs');
            $hbox->urlonly = Tools::getValue('hbp_urlonly');
            $hbox->url = Tools::getValue('hbp_selected_url');
            $hbox->search = Tools::getValue('hbp_search');
            $hbox->query = Tools::getValue('hbp_query');
            $hbox->logged = Tools::getValue('hbp_logged');
            $hbox->hcgroup = Tools::getValue('hbp_hcgroup');
            $hbox->cgroup = (Tools::getValue('groupBox') != false ? implode(",", (Tools::getValue('groupBox'))) : '');
            $hbox->date = Tools::getValue('hbp_date');
            $hbox->datefrom = Tools::getValue('hbp_datefrom');
            $hbox->dateto = Tools::getValue('hbp_dateto');
            $hbox->supponly = Tools::getValue('hbp_supponly');
            $hbox->selected_supp = Tools::getValue('hbp_selected_supp');
            $hbox->tim = Tools::getValue('hbp_tim');
            $hbox->timfrom = Tools::getValue('hbp_timfrom');
            $hbox->timto = Tools::getValue('hbp_timto');
            $hbox->poos = Tools::getValue('hbp_poos');
            $hbox->pins = Tools::getValue('hbp_pins');
            $hbox->onmobile = Tools::getValue('hbp_onmobile', 1);
            $hbox->ontablet = Tools::getValue('hbp_ontablet', 1);
            $hbox->onpc = Tools::getValue('hbp_onpc', 1);
            $hbox->pminprice = Tools::getValue('hbp_pminprice', 0);
            $hbox->pminpricev = Tools::getValue('hbp_pminpricev', 0);
            $hbox->pmaxprice = Tools::getValue('hbp_pmaxprice', 0);
            $hbox->pmaxpricev = Tools::getValue('hbp_pmaxpricev', 0);
            $hbox->excats = Tools::getValue('hbp_excats');
            $hbox->selected_excats = Tools::getValue('hbp_selected_excats');
            $hbox->exproductsall = Tools::getValue('hbp_exproductsall');
            $hbox->exproducts = Tools::getValue('hbp_exproducts');
            $hbox->selected_exproducts = Tools::getValue('hbp_selected_exproducts');
            $hbox->currency_on = Tools::getValue('hbp_currency_on');
            $hbox->currency = Tools::getValue('hbp_currency');
            $hbox->selected_geoip = implode(";", Tools::getValue('countries', array()));
            $hbox->geoip = Tools::getValue('hbp_geoip');

            foreach (language::getLanguages(false) AS $key => $value) {
                $hbox->body[$value['id_lang']] = addslashes(self::smart_clean(Tools::getValue('hbp_body_' . $value['id_lang'])));
            }
            $daytypes = array();
            for ($i = 0; $i <= 6; $i++) {
                if (Tools::isSubmit('hbp_daytype_' . $i)) {
                    $daytypes[$i] = $i;
                }
            }
            $hbox->daytype = implode(",", $daytypes);
            $hbox->daytype_on = Tools::getValue('hbp_daytype_on');


            $hbox->add();
            $this->context->smarty->assign('message', $this->l('Block added properly'));
            $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
        }

        if (Tools::getValue('submitEditHbox')) {
            $hbox = new hbox(Tools::getValue('editblock'));
            $hbox->shop = Tools::getValue('hbp_shop');
            $hbox->name = Tools::getValue('hbp_name');
            $hbox->hook = Tools::getValue('hbp_hook');
            $hbox->active = Tools::getValue('hbp_active');
            $hbox->bssl = Tools::getValue('hbp_bssl');
            $hbox->homeonly = Tools::getValue('hbp_homeonly');
            $hbox->specialsonly = Tools::getValue('hbp_specialsonly');
            $hbox->oconfirmation = Tools::getValue('hbp_oconfirmation');
            $hbox->productsonly = Tools::getValue('hbp_productsonly');
            $hbox->selectedproducts = Tools::getValue('hbp_selectedproducts');
            $hbox->productscat = Tools::getValue('hbp_productscat');
            $hbox->selected_pcats = Tools::getValue('hbp_selected_pcats');
            $hbox->productsman = Tools::getValue('hbp_productsman');
            $hbox->selected_pmanufs = Tools::getValue('hbp_selected_pmanufs');
            $hbox->catsonly = Tools::getValue('hbp_catsonly');
            $hbox->selected_cats = Tools::getValue('hbp_selected_cats');
            $hbox->cmscatsonly = Tools::getValue('hbp_cmscatsonly');
            $hbox->selected_cmscats = Tools::getValue('hbp_selected_cmscats');
            $hbox->cmsonly = Tools::getValue('hbp_cmsonly');
            $hbox->selectedcms = Tools::getValue('hbp_selectedcms');
            $hbox->manufsonly = Tools::getValue('hbp_manufsonly');
            $hbox->selected_manufs = Tools::getValue('hbp_selected_manufs');
            $hbox->urlonly = Tools::getValue('hbp_urlonly');
            $hbox->url = Tools::getValue('hbp_selected_url');
            $hbox->search = Tools::getValue('hbp_search');
            $hbox->query = Tools::getValue('hbp_query');
            $hbox->logged = Tools::getValue('hbp_logged');
            $hbox->hcgroup = Tools::getValue('hbp_hcgroup');
            $hbox->cgroup = (Tools::getValue('groupBox') != false ? implode(",", (Tools::getValue('groupBox'))) : '');
            $hbox->date = Tools::getValue('hbp_date');
            $hbox->datefrom = Tools::getValue('hbp_datefrom');
            $hbox->dateto = Tools::getValue('hbp_dateto');
            $hbox->supponly = Tools::getValue('hbp_supponly');
            $hbox->selected_supp = Tools::getValue('hbp_selected_supp');
            $hbox->tim = Tools::getValue('hbp_tim');
            $hbox->timfrom = Tools::getValue('hbp_timfrom');
            $hbox->timto = Tools::getValue('hbp_timto');
            $hbox->poos = Tools::getValue('hbp_poos');
            $hbox->pins = Tools::getValue('hbp_pins');
            $hbox->onmobile = Tools::getValue('hbp_onmobile', 1);
            $hbox->ontablet = Tools::getValue('hbp_ontablet', 1);
            $hbox->onpc = Tools::getValue('hbp_onpc', 1);
            $hbox->pminprice = Tools::getValue('hbp_pminprice', 0);
            $hbox->pminpricev = Tools::getValue('hbp_pminpricev', 0);
            $hbox->pmaxprice = Tools::getValue('hbp_pmaxprice', 0);
            $hbox->pmaxpricev = Tools::getValue('hbp_pmaxpricev', 0);
            $hbox->excats = Tools::getValue('hbp_excats');
            $hbox->selected_excats = Tools::getValue('hbp_selected_excats');
            $hbox->exproductsall = Tools::getValue('hbp_exproductsall');
            $hbox->exproducts = Tools::getValue('hbp_exproducts');
            $hbox->selected_exproducts = Tools::getValue('hbp_selected_exproducts');
            $hbox->currency_on = Tools::getValue('hbp_currency_on');
            $hbox->currency = Tools::getValue('hbp_currency');
            $hbox->selected_geoip = implode(";", Tools::getValue('countries', array()));
            $hbox->geoip = Tools::getValue('hbp_geoip');

            foreach (language::getLanguages(false) AS $key => $value) {
                $hbox->body[$value['id_lang']] = addslashes(self::smart_clean(Tools::getValue('hbp_body_' . $value['id_lang'])));
            }
            $daytypes = array();
            for ($i = 0; $i <= 6; $i++) {
                if (Tools::isSubmit('hbp_daytype_' . $i)) {
                    $daytypes[$i] = $i;
                }
            }
            $hbox->daytype = implode(",", $daytypes);
            $hbox->daytype_on = Tools::getValue('hbp_daytype_on');

            $hbox->update();
            $this->context->smarty->assign('message', $this->l('Block saved properly'));
            $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
        }

        $firsttime = "";

        if (Configuration::get("firsttime_hbp") != 1) {
            $firsttime = "
            <form name=\"firsttimeform\" method=\"POST\" id=\"firsttimeform\"/>
                <input type=\"hidden\" name=\"firsttime_hbp\" value=\"1\" />
            </form>
                            <div class=\"bootstrap\" style=\"margin-top:20px;\">
                                <div class=\"alert alert-info\">
             			            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" onclick=\"firsttimeform.submit();\">[close]</button> 
                     			    " . $this->l('First time with HTML BOX PRO? Problems with configuration? Watch this video: ') . "<a href=\"https://www.youtube.com/watch?v=uxzhSr5TrF4\" target=\"_blank\">https://www.youtube.com/watch?v=uxzhSr5TrF4</a>
                          		</div>
                            </div>";
        }

        if (Tools::isSubmit('firsttime_hbp')) {
            Configuration::updateValue('firsttime_hbp', "1");
        }

        if (Tools::isSubmit('selecttab')) {
            Configuration::updateValue('hbp_lasttab', Tools::getValue('selecttab'));
        }

        if (Tools::isSubmit('hbp_tiny')) {
            Configuration::updateValue('hbp_tiny', Tools::getValue('hbp_tiny'));
        }

        if (Tools::isSubmit('hbp_notinyjs')) {
            Configuration::updateValue('hbp_notinyjs', Tools::getValue('hbp_notinyjs'));
        }

        if (Tools::isSubmit('hbp_noajax')) {
            Configuration::updateValue('hbp_noajax', Tools::getValue('hbp_noajax'));
        }

        if (Tools::isSubmit('hbp_forceurls')) {
            Configuration::updateValue('hbp_forceurls', Tools::getValue('hbp_forceurls'));
        }

        if (Tools::isSubmit('hbp_rh_submit')) {
            if ($this->rebuildModuleFile() == true) {
                $this->context->smarty->assign('message', $this->l('Main module file rebuilded properly. Module should support custom hooks now.'));
                $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            } else {
                $this->context->smarty->assign('message', $this->l('Main module file is not writable. Please check permissions (CHMOD) to /modules/htmlboxpro/htmlboxpro.php file'));
                $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            }
        }

        if (Tools::isSubmit('togglehook')) {
            $this->context->smarty->assign('message', $this->l('Visibility of hook changed properly'));
            $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            if (Tools::isSubmit('status')) {
                $status = Tools::getValue('status');
            } else {
                $status = 0;
            }
            Configuration::updateValue('hbp_' . Tools::getValue('togglehook'), $status);
            $this->registerHook(Tools::getValue('togglehook'));
        }

        if (Tools::isSubmit('activate_block')) {
            $this->context->smarty->assign('message', $this->l('Visibility of block changed properly'));
            $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            if (Tools::isSubmit('status')) {
                $status = Tools::getValue('status');
            } else {
                $status = 0;
            }

            $hbox = new hbox(Tools::getValue('activate_block'));
            $hbox->active = $status;
            $hbox->update();

            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'hbp_block` SET active="' . $status . '" WHERE id=' . Tools::getValue('activate_block') . ' ');
        }

        if (Tools::isSubmit('removeblock')) {
            $hbox = new hbox(Tools::getValue('removeblock'));
            if ($hbox->delete()) {
                $this->context->smarty->assign('message', $this->l('Block removed properly'));
                $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            }
        }

        if (Tools::isSubmit('duplicateblock')) {
            $hbox = new hbox(Tools::getValue('duplicateblock'));
            $hbox2 = clone $hbox;
            $hbox2->name = "[" . $this->l('duplicate') . "] " . $hbox2->name;
            $hbox2->add();
            $this->context->smarty->assign('message', $this->l('Block duplicated properly'));
            $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
        }

        if (Tools::isSubmit('removehook')) {
            $this->context->smarty->assign('message', $this->l('Custom hook removed properly'));
            $output .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'hbp_customhook` WHERE hook="' . Tools::getValue('removehook') . '" ');
            if (Hook::getIdByName(Tools::getValue('removehook')) != false) {
                $hook = new Hook(Hook::getIdByName(Tools::getValue('removehook')));
                $hook->delete();
            }
            $this->rebuildModuleFile();
        }

        $customhook_conf = "";

        if (Tools::isSubmit('hbp_nh_hook')) {
            $this->context->smarty->assign('message', $this->l('New hook added properly'));
            if (Hook::getIdByName(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook')))) == false) {
                $newhook = new Hook();
                $newhook->name = preg_replace("/[^\da-z]/i", '', trim(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook')))));
                $newhook->live_edit = 1;
                $newhook->position = 1;
                $newhook->add();
                $newhook_verify = Hook::getIdByName(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook'))));
                $customhook_conf = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
                $output = $customhook_conf;
                $this->registerHook(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook'))));
                $this->verifyNewHook(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook'))));
                $this->rebuildModuleFile();
            } else {
                if ($this->checkHookInModuleFile(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook')))) == false) {
                    $this->registerHook(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook'))));
                    $this->verifyNewHook(preg_replace("/[^\da-z]/i", '', trim(Tools::getValue('hbp_nh_hook'))));
                    $this->rebuildModuleFile();
                    $customhook_conf = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
                } else {
                    $this->context->smarty->assign('message', $this->l('Hook already exist in shop database'));
                    $customhook_conf = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
                }

                $output = $customhook_conf;
            }
        }
        return $this->searchTool->initTool() . $firsttime . $output . $this->displayForm();
    }

    public static function runStatement($statement)
    {
        if (@!Db::getInstance()->Execute($statement)) {
            return false;
        }
        return true;
    }

    public function displayForm()
    {
        if (Configuration::get('hbp_lasttab') == false) {
            Configuration::updateValue('hbp_lasttab', 1);
        }
        // GLOBAL DISPLAYFORM VARIABLES
        $customhook_conf = "";

        if (Configuration::get('hbp_lasttab') == 1) {
            $selected1 = "active";
        } else {
            $selected1 = "";
        }
        if (Configuration::get('hbp_lasttab') == 2) {
            $selected2 = "active";
        } else {
            $selected2 = "";
        }
        if (Configuration::get('hbp_lasttab') == 3) {
            $selected3 = "active";
        } else {
            $selected3 = "";
        }
        if (Configuration::get('hbp_lasttab') == 1) {

            $this->context->smarty->assign(array(
                'ps_base_uri' => __PS_BASE_URI__,
            ));
            $iso = Language::getIsoById((int)($this->context->language->id));
            $isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en');
            $ad = dirname($_SERVER["PHP_SELF"]);
            $this->context->smarty->assign(array(
                'psversion' => $this->psversion(),
                'ps_base_uri' => __PS_BASE_URI__,
                'isoTinyMCE' => $isoTinyMCE,
                'theme_css_dir' => _THEME_CSS_DIR_,
                'ad' => $ad
            ));

            $form = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/scripts.tpl');
            $returntotal = $form . $this->hbox_global_settings() . $this->checkforupdates(0, true);
        }
        if (Configuration::get('hbp_lasttab') == 2) {
            $this->context->smarty->assign('message', $this->l('Please select blocks from list available on the left hand side. Or create new block.'));
            $form2 = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/messages.tpl');
            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

            $iso = Language::getIsoById((int)($this->context->language->id));
            $isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en');
            $ad = dirname($_SERVER["PHP_SELF"]);


            $this->context->controller->addJqueryUI('ui.sortable');

            $this->context->smarty->assign(array(
                'psversion' => $this->psversion(),
                'ps_base_uri' => __PS_BASE_URI__,
                'isoTinyMCE' => $isoTinyMCE,
                'theme_css_dir' => _THEME_CSS_DIR_,
                'ad' => $ad
            ));

            $form = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/scripts.tpl');


            $radio = "";
            $body = "";
            foreach ($this->allhooks as $key => $value) {
                $blocks = '';

                $hook = $value;
                foreach ($this->get_blocks($value) as $k => $v) {
                    $blocks .= "
                    <li id=\"elements{$value}_{$v['id']}\">" . ($v['name'] != '' ? '[#'. $v['id'] .'] '.  $v['name'] : '#' . $v['id'] . ' (' . $this->l('no internal block name') . ')') . "
                    <span class=\"activate\" onclick=\"activate{$v['id']}.submit();\">
                        <form id=\"activate{$v['id']}\" name=\"activate{$v['id']}\" method=\"post\"/>
                            <input type=\"hidden\" name=\"activate_block\" value=\"{$v['id']}\" />
                            <input type=\"checkbox\" name=\"status\" value=\"1\" " . ($v['active'] == 1 ? 'checked="checked"' : '') . " />
                        </form>
                    </span>

                    <span class=\"label-tooltip hbpremove\" onclick=\"removeblock{$v['id']}.submit();\" data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Remove this block') . "\">
                        <form id=\"removeblock{$v['id']}\" name=\"removeblock{$v['id']}\" method=\"post\"/>
                            <input type=\"hidden\" name=\"removeblock\" value=\"{$v['id']}\" />
                        </form>
                    </span>

                    <span class=\"label-tooltip hbpedit\" onclick=\"editblock{$v['id']}.submit();\" data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Edit this block') . "\">
                        <form id=\"editblock{$v['id']}\" name=\"editblock{$v['id']}\" method=\"post\"/>
                            <input type=\"hidden\" name=\"editblock\" value=\"{$v['id']}\" />
                        </form>
                    </span>

                    <span class=\"label-tooltip hbpduplicate\" onclick=\"duplicateblock{$v['id']}.submit();\"  data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Duplicate this block') . "\">
                        <form id=\"duplicateblock{$v['id']}\" name=\"duplicateblock{$v['id']}\" method=\"post\"/>
                            <input type=\"hidden\" name=\"duplicateblock\" value=\"{$v['id']}\" />
                        </form>
                    </span>
                    
                    </li>";
                }
                if ($blocks == '') {
                    $blocks .= '<div class="info">' . $this->l('no blocks defined') . ' ' . $this->l('for this shop') . '</div>';
                } else {
                    $blocks = "<ul class=\"hbpslides\" id=\"elements$value\">$blocks</ul>" . '<script type="text/javascript">
                            $(function() {
                                var $mySlides' . $value . ' = $("#elements' . $value . '");
                                $mySlides' . $value . '.sortable({
                                    opacity: 0.6,
                                    cursor: "move",
                                    update: function() {
                                        var order = $(this).sortable("serialize") + "&hook=' . $value . '&action=updateSlidesPosition";
                                        $.post("../modules/' . $this->name . '/ajax_' . $this->name . '.php", order);
                                        }
                                    });
                                $mySlides' . $value . '.hover(function() {
                                    $(this).css("cursor","move");
                                    },
                                    function() {
                                    $(this).css("cursor","auto");
                                });
                            });
                            </script>';
                }

                $radio .= "
    				<tr class='hookslist " . (Configuration::get('hbp_' . $value) == 1 ? 'active' : 'inactive') . "'>
                        <td class=\"checkbx\">
                            <form id=\"togglehook$value\" name=\"togglehook$value\" method=\"post\"><input type=\"hidden\" name=\"togglehook\" value=\"" . $value . "\"><input type=\"checkbox\" name=\"status\" onchange=\"togglehook$value.submit();\" value=\"1\" " . (Configuration::get('hbp_' . $value) == 1 ? 'checked="checked"' : '') . "/></form>
                        </td>
    					<td class=\"hname\"><a style=\"display:inline-block; float:left; \" target=\"_blank\" href=\"http://mypresta.eu/prestashop/hook/" . strtolower($hook) . "/\">$hook</a><span style=\"width:20px; height:20px; cursor:pointer; text-align:right; display:inline-block; float:right;\" onclick=\"newblock$value.submit();\"><form id=\"newblock$value\" name=\"newblock$value\" method=\"post\"><input type=\"hidden\" name=\"newblock\" value=\"$value\"/></form><img style=\"opacity:0.3; filter:alpha(opacity=30);\" class=\"editbutton\" src=\"../" . $this->dir . "views/img/add-icon.png\" alt=\"{$hook}_{$id_lang_default}\" /></span></td>
    					<td class=\"hoptions\">
                            <span style=\"cursor:pointer; text-align:center;\"><img style=\"opacity:0.3; filter:alpha(opacity=30); padding:0px;\" class=\"accordion\" src=\"../" . $this->dir . "/views/img/br_down.png\" alt=\"{$hook}\" /></span>
                        </td>
    				</tr>
                    <tr class='hookslist hook_blocks hook_$value'>
                        <td colspan=\"3\" style=\"border-left:1px solid #c0c0c0; background:#FFF;\" class=\"checkboxx hoptions hname\">
                        " . $blocks . "
                        </td>
                    </tr>
    			";
            }

            $customHooks = $this->getListNewHook();
            if (count($customHooks) > 0) {
                $this->customhooks = array();
                foreach ($customHooks as $key => $value) {
                    $this->customhooks[$key] = $value['hook'];
                }
            }

            $radio2 = "";
            $body2 = "";
            if (isset($this->customhooks)) {
                if (count($this->customhooks) > 0) {
                    foreach ($this->customhooks as $key => $value) {
                        $blocks2 = '';
                        $hook = $value;
                        foreach ($this->get_blocks($value) as $k => $v) {
                            $blocks2 .= "
                            <li id=\"elements{$value}_{$v['id']}\">" . ($v['name'] != '' ? '[#'. $v['id'] .'] '.  $v['name'] : '#' . $v['id'] . ' (' . $this->l('no internal block name') . ')') . "

                          
                          <span class=\"activate\" onclick=\"activate{$v['id']}.submit();\">
                            <form id=\"activate{$v['id']}\" name=\"activate{$v['id']}\" method=\"post\"/>
                              <input type=\"hidden\" name=\"activate_block\" value=\"{$v['id']}\" />
                              <input type=\"checkbox\" name=\"status\" value=\"1\" " . ($v['active'] == 1 ? 'checked="checked"' : '') . " />
                            </form>
                          </span>
                          
                          <span class=\"hbpremove\" onclick=\"removeblock{$v['id']}.submit();\">
                            <form id=\"removeblock{$v['id']}\" name=\"removeblock{$v['id']}\" method=\"post\"/>
                              <input type=\"hidden\" name=\"removeblock\" value=\"{$v['id']}\" />
                            </form>
                          </span>
                          
                          <span class=\"hbpedit\" onclick=\"editblock{$v['id']}.submit();\">
                              <form id=\"editblock{$v['id']}\" name=\"editblock{$v['id']}\" method=\"post\"/>
                                  <input type=\"hidden\" name=\"editblock\" value=\"{$v['id']}\" />
                              </form>
                          </span>
                          
                          <span class=\"label-tooltip hbpduplicate\" onclick=\"duplicateblock{$v['id']}.submit();\"  data-toggle=\"tooltip\" data-original-title=\"" . $this->l('Duplicate this block') . "\">
                            <form id=\"duplicateblock{$v['id']}\" name=\"duplicateblock{$v['id']}\" method=\"post\"/>
                                <input type=\"hidden\" name=\"duplicateblock\" value=\"{$v['id']}\" />
                            </form>
                          </span>
                          
                          </li>
                          ";
                        }
                        if ($blocks2 == '') {
                            $blocks2 .= '<div class="info">' . $this->l('no blocks defined') . '</div>';
                        } else {
                            $blocks2 = "<ul class=\"hbpslides\" id=\"elements$value\">$blocks2</ul>" . '<script type="text/javascript">
            			$(function() {
            				var $mySlides' . $value . ' = $("#elements' . $value . '");
            				$mySlides' . $value . '.sortable({
            					opacity: 0.6,
            					cursor: "move",
            					update: function() {
            						var order = $(this).sortable("serialize") + "&hook=' . $value . '&action=updateSlidesPosition";
            						$.post("../modules/' . $this->name . '/ajax_' . $this->name . '.php", order);
            						}
            					});
            				$mySlides' . $value . '.hover(function() {
            					$(this).css("cursor","move");
            					},
            					function() {
            					$(this).css("cursor","auto");
            				});
            			});
            		    </script>';
                        }

                        $radio2 .= "
            				<tr class='hookslist " . (Configuration::get('hbp_' . $value) == 1 ? 'active' : 'inactive') . "'>
                                <td class=\"checkbx\">
                                    <form id=\"togglehook$value\" name=\"togglehook$value\" method=\"post\"><input type=\"hidden\" name=\"togglehook\" value=\"" . $value . "\"><input type=\"checkbox\" name=\"status\" onchange=\"togglehook$value.submit();\" value=\"1\" " . (Configuration::get('hbp_' . $value) == 1 ? 'checked="checked"' : '') . "/></form>
                                </td>
            					<td class=\"hname\">$hook<span style=\"width:20px; height:20px; cursor:pointer; text-align:right; display:inline-block; float:right;\" onclick=\"newblock$value.submit();\"><form id=\"newblock$value\" name=\"newblock$value\" method=\"post\"><input type=\"hidden\" name=\"newblock\" value=\"$value\"/></form><img style=\"opacity:0.3; filter:alpha(opacity=30);\" class=\"editbutton\" src=\"../" . $this->dir . "/views/img/add-icon.png\" alt=\"{$hook}_{$id_lang_default}\" /></span>
                                <span class=\"hbpremove\" onclick=\"removehook{$value}.submit();\" style=\"position:relative; top:-1px;\">
                                    <form id=\"removehook{$value}\" name=\"removehook{$value}\" method=\"post\"/>
                                    <input type=\"hidden\" name=\"removehook\" value=\"{$value}\" />
                                    </form>
                                </span>
                                </td>
            					<td class=\"hoptions\">
                                    <span style=\"cursor:pointer; text-align:center\"><img style=\"opacity:0.3; filter:alpha(opacity=30); padding:0px;\" class=\"accordion\" src=\"../" . $this->dir . "/views/img/br_down.png\" alt=\"{$hook}\" /></span>
                                </td>
            				</tr>
                            <tr class='hookslist hook_blocks hook_$value'>
                                <td colspan=\"3\" style=\"border-left:1px solid #c0c0c0; background:#FFF;\" class=\"checkboxx hoptions hname\">
                                " . $blocks2 . "
                                </td>
                            </tr>		
            			";
                    }
                }
            }

            $dfrom = '';

            if (Tools::getValue('newblock', 'false' != 'false')) {
                $this->context->smarty->assign(array(
                    'tpl_form' => $this->hbox_add()
                ));
                $form2 = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/form-add-new.tpl');
            }

            if (Tools::getValue('editblock', 'false') != 'false') {
                $this->context->smarty->assign(array(
                    'tpl_form' => $this->hbox_add(Tools::getValue('editblock')),
                    'custom_hook' => $this->returnCustomHookInfo(Tools::getValue('editblock'))
                ));
                $form2 = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/form-edit.tpl');
            }

            $howto = "
                    <tr class='hookslist active'>
    					<td class=\"hname\" style=\"width:100%; text-align:center;\">
                            <a href=\"http://mypresta.eu/en/art/news/html-content-box-custom-hooks.html\" target=\"_blank\">" . $this->l('How to use custom hooks?') . "</a>
                        </td>
    				</tr>";

            $customhook_addnew = "
            <div style=\"display:block; background:#FFF; clear:both; overflow:hidden;\" >
                <form method=\"post\" name=\"hbp_nh\" id=\"hbp_nh\">
                    " . $this->l('Hook name') . ":
                    <input type=\"text\" name=\"hbp_nh_hook\" /><br/>
                    <table style=\"width:100%; margin-bottom:30px;\">
                        <tr>
                            <td style=\"text-align:center;\">
                                <input type=\"submit\" class=\"button btn btn-default\" value=\"" . $this->l('Add new hook') . "\" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>";

            $customization = '
                <table style="width:100%; border-bottom:1px solid #c0c0c0;  clear:both; vertical-align:top;" cellspacing="0" cellpadding="0">
    			    ' . $radio2 . '
    		    </table>
                <table style="width:100%; border-bottom:1px solid #c0c0c0;  clear:both; vertical-align:top;" cellspacing="0" cellpadding="0">
    			    ' . $howto . '
    			</table>

                        ' . $customhook_conf . "
                        <div class='panel'>
                            <h3>" . $this->l('Add support of new hook') . "</h3>
                            " . $customhook_addnew . "
                        </div>

                        <div class='panel'>
                            <h3>" . $this->l('Regenerate hooks') . "</h3>
                            <div style=\"margin-top:10px; margin-bottom:10px; display:block; text-align:center;\">
                                <form method=\"post\" name=\"hbp_rh\" id=\"hbp_rh\">
                                    <input class=\"button btn btn-default\" type=\"submit\" name=\"hbp_rh_submit\" value=\"" . $this->l('regenerate now!') . "\"/>
                                </form>
                                <div class=\"bootstrap\" style=\"margin-top:10px; width:100%;  padding:0px 10px;\">
                                    <div class=\"alert alert-warning\">
                         			    " . $this->l('use this option to rebuild module file if custom hooks doesnt want to work') . "
                              		</div>
                                </div>
                                
                            </div>
                        </div>
                        ";


            $returntotal = $form . '
                    <div class="col-lg-3">
                        <div class="panel">
                            <h3><i class="icon-anchor"></i> ' . $this->l('Default positions') . '</h3>
                            <table style="width:100%">
                            ' . $radio . '
                            </table>
                        </div>
                        <div class="panel">
                            <h3><i class="icon-anchor"></i> ' . $this->l('Custom positions') . '</h3>
                            ' . $customization . '
                        </div>
                    </div>
                    <div class="col-lg-9">
                        ' . $form2 . '
                    </div>';
        }

        return '
        <div style="clear:both; overflow:hidden;" id=\'htmlboxproConfiguration\'>
            <form name="selectform1" id="selectform1" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="selecttab" value="1"></form>
            <form name="selectform2" id="selectform2" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="selecttab" value="2"></form>
            <form name="selectform3" id="selectform3" action="' . $_SERVER['REQUEST_URI'] . '" method="post"><input type="hidden" name="selecttab" value="3"></form>
            ' . "
                <div id='cssmenu'>
                    <ul>
                       <li class='bgver'><a><span>v" . $this->version . "</span></a></li>
                       <li class='$selected1' onclick=\"selectform1.submit()\"><a href=\"#\"><span>" . $this->l('Settings') . "</span></a></li>
                       <li class='$selected2' onclick=\"selectform2.submit()\"><a href=\"#\"><span>" . $this->l('Boxes') . "</span></a></li>
                       <li style='position:relative; display:inline-block; float:right; '><a href='http://mypresta.eu' target='_blank' title='prestashop modules'><img src='../modules/htmlboxpro/logo-white.png' alt='prestashop modules' style=\"position:absolute; top:17px; right:16px;\"/></a></li>
                       <li style='position:relative; display:inline-block; float:right;' class=''><a href='http://mypresta.eu/contact.html' target='_blank'><span>" . $this->l('Support') . "</span></a></li>
                       <li style='position:relative; display:inline-block; float:right;' class=''><a href='http://mypresta.eu/modules/front-office-features/html-box-pro.html' target='_blank'><span>" . $this->l('Updates') . "</span></a></li>
                    </ul>
                </div>" . $returntotal . '</div>';
    }

    public function returnCustomHookInfo($block)
    {
        $block = new hbox($block);
        if (!in_array($block->hook, $this->allhooks)) {
            $this->context->smarty->assign('custom_hook_name', $block->hook);
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'htmlboxpro/views/custom_hook_info.tpl');
        } else {
            return false;
        }

    }

    public function hbox_global_settings()
    {
        $inputs = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Use internal module\'s  rich text editor'),
                'name' => 'hbp_tiny',
                'desc' => $this->l('Module allows to use extended rich text editor delivered with this module or internal prestashop\'s editor') . ' <a href="https://mypresta.eu/modules/administration-tools/tinymce-pro-extended-rich-text-editor.html" target="_blank"> <strong>(' . $this->l('also tinymce pro') . ')</strong></a>. ' . $this->l('Turn this option on to use module internal extended rich text editor. Turn it off if you want to use prestashop\'s editor.'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Prevent removing URLs from images'),
                'name' => 'hbp_forceurls',
                'desc' => $this->l('This option when enabled will not create relative urls. All urls used in the module will have domain address.'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Don\'t load (duplicate) boxes with product page  ajax queries'),
                'name' => 'hbp_noajax',
                'desc' => $this->l('This feature prevents duplicate process of contents on product page when you change attribute / increase or decrease quantities'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Do not load tinyMCE libraries'),
                'name' => 'hbp_notinyjs',
                'desc' => $this->l('This feature is useful when you use some extended rich text editor module. To avoid conflicts between various tinyMCE libraries just activate this option.') . ' <br/>' . $this->l('Do it, if you do not see the editor when you create or edit blocks'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Global module settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = 'hbp_global_settings';
        $helper->submit_action = 'submit_global_settings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getBlockFieldsGlobal(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function hbox_add($id = false)
    {
        if ($id != false) {
            $editObject = new hbox($id);
        } else {
            $editObject = new hbox();
        }

        $this->select_daytypes = array(
            array(
                'id_option' => 0,
                'name' => $this->l('Sunday', 'forms')
            ),
            array(
                'id_option' => 1,
                'name' => $this->l('Monday', 'forms')
            ),
            array(
                'id_option' => 2,
                'name' => $this->l('Tuesday', 'forms')
            ),
            array(
                'id_option' => 3,
                'name' => $this->l('Wednesday', 'forms')
            ),
            array(
                'id_option' => 4,
                'name' => $this->l('Thursday', 'forms')
            ),
            array(
                'id_option' => 5,
                'name' => $this->l('Friday', 'forms')
            ),
            array(
                'id_option' => 6,
                'name' => $this->l('Saturday', 'forms')
            )
        );

        $lang = new Language($this->context->language->id);
        $langs = Language::getLanguages();
        $id_shop = (int)$this->context->shop->id;

        $options_visibility_users = array(
            array(
                'id_option' => 0,
                'name' => $this->l('All users'),
            ),
            array(
                'id_option' => 1,
                'name' => $this->l('Logged only'),
            ),
            array(
                'id_option' => 2,
                'name' => $this->l('Unlogged only'),
            ),
        );
        $options = array();
        foreach (Group::getGroups($this->context->language->id) as $gr => $group) {
            $options[] = array(
                'id_option' => $group['id_group'],
                'name' => $group['name'],
            );
        }
        $options[] = array(
            'id_option' => 0,
            'name' => $this->l('Show for all groups')
        );
        $options_visibility_users_groups = $options;

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'), $this->context->language->id);
        $inputs = array(
            array(
                'type' => 'textareaSwitcher',
                'label' => $this->l('Contents'),
                'name' => 'hbp_body',
                'class' => 'rte',
                'desc' => $this->smartyTemplatesManager->generateSmartyTemplatesManagerButton(),
                'lang' => 'true'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Name'),
                'name' => 'hbp_name',
                'desc' => $this->l('Internal block name visible for your eye only, used for distinction purposes'),
            ),
            array(
                'type' => 'html',
                'label' => "",
                'name' => 'html1',
                'html_content' => $edit . "<hr/><h1>" . $this->l('Visibility conditions') . "</h1><div class='alert alert-info'>" . $this->l('You can decide where, when and for what users module will appear. Below you can find options to decide about it.') . "</div>",
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Shop'),
                'name' => 'hbp_shop',
                'desc' => $this->l('Select shop where the module will display this block'),
                'options' => array(
                    'query' => array_merge(array(array('id_shop' => 0, 'name' => $this->l('Show in all shops'))), Shop::getShops(false)),
                    'id' => 'id_shop',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Position'),
                'name' => 'hbp_hook',
                'desc' => $this->l('Select position where you want to show this block'),
                'options' => array(
                    'query' => $this->getAllHooks(),
                    'id' => 'hook',
                    'name' => 'hook'
                ),
            ),
            ($id != false ? array(
                'type' => 'hidden',
                'name' => 'editblock',
                'value' => $id
            ) : null),

            array(
                'type' => 'switch',
                'label' => $this->l('Active'),
                'name' => 'hbp_active',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on secured (SSL) pages'),
                'name' => 'hbp_bssl',
                'desc' => $this->l('Option if enabled will show the block only if customer will browse your page with secured connection (ssl)'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on homepage'),
                'name' => 'hbp_homeonly',
                'desc' => $this->l('Option if enabled will show block on shop\'s homepage only'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on "Specials" page'),
                'name' => 'hbp_specialsonly',
                'desc' => $this->l('Option if enabled will show block on "offers" page (products with discounts)'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on order confirmation page'),
                'name' => 'hbp_oconfirmation',
                'desc' => $this->l('Option if enabled will show block on order confirmation page only'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Exclude from selected product pages'),
                'name' => 'hbp_exproducts',
                'desc' => $this->l('Option if enabled will exclude block from selected product(s) page(s)'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Products to exclude'),
                'name' => 'hbp_selected_exproducts',
                'desc' => $this->l('Products ID, separate by commas') . $this->searchTool->searchTool('product', 'hbp_selected_exproducts', '', true, $editObject->selected_exproducts),
                'prefix' => $this->searchTool->searchTool('product', 'hbp_selected_exproducts', ''),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Exclude from all product pages'),
                'name' => 'hbp_exproductsall',
                'desc' => $this->l('Option if enabled will exclude block from selected product(s) page(s)'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected product pages'),
                'name' => 'hbp_productsonly',
                'desc' => $this->l('Option if enabled will show block on selected product(s) page(s) only'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Products'),
                'name' => 'hbp_selectedproducts',
                'prefix' => $this->searchTool->searchTool('product', 'hbp_selectedproducts', ''),
                'desc' => $this->l('Products ID, separate by commas. This can be used also for display contents on list of products for selected products only.') . $this->searchTool->searchTool('product', 'hbp_selectedproducts', '', true, $editObject->selectedproducts),
            ),

            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is out of stock'),
                'name' => 'hbp_poos',
                'desc' => $this->l('Turn this option on only if viewed product page is out of stock'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is in stock'),
                'name' => 'hbp_pins',
                'desc' => $this->l('Turn this option on only if viewed product page is in stock'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is worth more (or equal) than defined amount'),
                'name' => 'hbp_pminprice',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Min value of product'),
                'name' => 'hbp_pminpricev',
                'prefix' => $currency->iso_code,
                'desc' => $this->l('If you use various currencies module will calculate value to other currencies automatically based on shop currency exchange rates'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is worth less (or equal) than defined amount'),
                'name' => 'hbp_pmaxprice',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Max value of product'),
                'name' => 'hbp_pmaxpricev',
                'prefix' => $currency->iso_code,
                'desc' => $this->l('If you use various currencies module will calculate value to other currencies automatically based on shop currency exchange rates'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is associated with category'),
                'name' => 'hbp_productscat',
                'desc' => $this->l('Option if enabled will show block on selected product(s) page(s) if these products are associated with defined categories'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Categories'),
                'name' => 'hbp_selected_pcats',
                'desc' => $this->l('Categories ID, separate them by commas') . $this->searchTool->searchTool('category', 'hbp_selected_pcats', '', true, $editObject->selected_pcats),
                'prefix' => $this->searchTool->searchTool('category', 'hbp_selected_pcats', ''),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is associated with manufacturers'),
                'name' => 'hbp_productsman',
                'desc' => $this->l('Option if enabled will show block on selected product(s) page(s) if these products are associated with defined manufacturers'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Manufacturers'),
                'name' => 'hbp_selected_pmanufs',
                'desc' => $this->l('Manufacturers ID, separate by commas') . $this->searchTool->searchTool('manufacturer', 'hbp_selected_pmanufs', '', true, $editObject->selected_pmanufs),
                'prefix' => $this->searchTool->searchTool('manufacturer', 'hbp_selected_pmanufs', ''),
            ),

            array(
                'type' => 'switch',
                'label' => $this->l('Only if viewed product is associated with suppliers'),
                'name' => 'hbp_supponly',
                'desc' => $this->l('Option if enabled will show block on selected product(s) page(s) if these products are associated with defined suppliers'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Suppliers'),
                'name' => 'hbp_selected_supp',
                'desc' => $this->l('Suppliers ID, separate by commas') . $this->searchTool->searchTool('supplier', 'hbp_selected_supp', '', true, $editObject->selected_supp),
                'prefix' => $this->searchTool->searchTool('supplier', 'hbp_selected_supp', ''),
            ),


            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected Category pages'),
                'name' => 'hbp_catsonly',
                'desc' => $this->l('Option if enabled will show block only on selected category(ies) page(s)'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Categories'),
                'name' => 'hbp_selected_cats',
                'desc' => $this->l('Categories ID, separate by commas') . $this->searchTool->searchTool('category', 'hbp_selected_cats', '', true, $editObject->selected_cats),
                'prefix' => $this->searchTool->searchTool('category', 'hbp_selected_cats', ''),

            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Exclude from selected Category pages'),
                'name' => 'hbp_excats',
                'desc' => $this->l('Option if enabled will exclude block from selected category(ies) page(s)'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Categories to exclude'),
                'name' => 'hbp_selected_excats',
                'desc' => $this->l('Categories ID, separate by commas') . $this->searchTool->searchTool('category', 'hbp_selected_excats', '', true, $editObject->selected_excats),
                'prefix' => $this->searchTool->searchTool('category', 'hbp_selected_excats', ''),
            ),


            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected CMS Category pages'),
                'name' => 'hbp_cmscatsonly',
                'desc' => $this->l('Option if enabled will show block only on selected CMS category pages'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Cms Categories'),
                'name' => 'hbp_selected_cmscats',
                'desc' => $this->l('Cms Categories ID, separate by commas') . $this->searchTool->searchTool('cms_category', 'hbp_selected_cmscats', '', true, $editObject->selected_cmscats),
                'prefix' => $this->searchTool->searchTool('cms_category', 'hbp_selected_cmscats', ''),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected CMS page'),
                'name' => 'hbp_cmsonly',
                'desc' => $this->l('Option if enabled will show block only on selected CMS pages'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Cms pages'),
                'name' => 'hbp_selectedcms',
                'desc' => $this->l('Cms pages ID, separate by commas') . $this->searchTool->searchTool('cms', 'hbp_selectedcms', '', true, $editObject->selectedcms),
                'prefix' => $this->searchTool->searchTool('cms', 'hbp_selectedcms', ''),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected Manufacturers page'),
                'name' => 'hbp_manufsonly',
                'desc' => $this->l('Option if enabled will show block only on selected manufacturer(s) page'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Manufacturer\'s ID'),
                'name' => 'hbp_selected_manufs',
                'desc' => $this->l('Manufacturer\'s ID, separate by commas') . $this->searchTool->searchTool('manufacturer', 'hbp_selected_manufs', '', true, $editObject->selected_manufs),
                'prefix' => $this->searchTool->searchTool('manufacturer', 'hbp_selected_manufs', ''),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected url'),
                'name' => 'hbp_urlonly',
                'desc' => $this->l('Option if enabled will show block only on selected url'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Url'),
                'name' => 'hbp_selected_url',
                'desc' => $this->l('Enter here full url of page(s) where you want to display block. Separate urls by commas.'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only when customer search'),
                'name' => 'hbp_search',
                'desc' => $this->l('Option if enabled will show block on search results page if customer will search for something'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Keyword'),
                'name' => 'hbp_query',
                'desc' => $this->l('search query, module will display block when someone will search for this in your shop. Separate keywords by commas'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only for selected currency'),
                'name' => 'hbp_currency_on',
                'desc' => $this->l('Option when active allows to show contents when customer browses shop in defined currency'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Select currency'),
                'name' => 'hbp_currency',
                'desc' => $this->l('If option to show contents for defined currency is active, module will display contents only if shop is browsed in selected currency'),
                'options' => array(
                    'query' => Currency::getCurrencies(false, false, $this->context->shop->id),
                    'id' => 'id_currency',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Visibility for (users)'),
                'name' => 'hbp_logged',
                'desc' => $this->l('Define who exactly will see this block'),
                'options' => array(
                    'query' => $options_visibility_users,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'group',
                'label' => $this->l('Visibility for (users\' groups)'),
                'name' => 'hbp_cgroup',
                'desc' => $this->l('Define what groups of customers will see this block'),
                'values' => Group::getGroups($this->context->language->id, true),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Hide for (users\' groups)'),
                'name' => 'hbp_hcgroup',
                'desc' => $this->l('Define what groups of customers will don\'t see the block '),
                'options' => array(
                    'query' => $options_visibility_users_groups,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only for selected countries'),
                'name' => 'hbp_geoip',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'html',
                'label' => '',
                'name' => 'popup_preview',
                'html_content' => $this->renderGeoIp($editObject),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Active on mobile devices'),
                'desc' => $this->l('this option identifies device type, not screen size.'),
                'name' => 'hbp_onmobile',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Active on tablet devices'),
                'desc' => $this->l('this option identifies device type, not screen size.'),
                'name' => 'hbp_ontablet',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Active on PC devices'),
                'desc' => $this->l('this option identifies device type, not screen size.'),
                'name' => 'hbp_onpc',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display date'),
                'name' => 'hbp_date',
                'desc' => $this->l('Select this option only if you want to specify visibility of the block depending on date '),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'date',
                'label' => $this->l('Date from'),
                'name' => 'hbp_datefrom',
                'desc' => $this->l('Block will start to appear on this day. Date format: 2017-04-29.'),

            ),
            array(
                'type' => 'date',
                'label' => $this->l('Date to'),
                'name' => 'hbp_dateto',
                'desc' => $this->l('Block will be hidden after this day. Date format: 2017-04-30.'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Only on selected day type'),
                'name' => 'hbp_daytype_on',
                'desc' => $this->l('Option allows to display block on selected day like monday, saturday, sunday etc.'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'checkbox',
                'label' => $this->l('Only on selected day type'),
                'name' => 'hbp_daytype',
                'desc' => $this->l('Select days'),
                'values' => array(
                    'query' => $this->select_daytypes,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display time'),
                'name' => 'hbp_tim',
                'desc' => $this->l('Select this option if you want to display contents during defined hours'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('On')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Off')
                    )
                ),
            ),
            array(
                'type' => 'time',
                'label' => $this->l('Time from'),
                'name' => 'hbp_timfrom',
                'desc' => $this->l('Block will start to appear from this hour. Time format: HH:ii:ss'),

            ),
            array(
                'type' => 'time',
                'label' => $this->l('Time to'),
                'name' => 'hbp_timto',
                'desc' => $this->l('Block will be hidden after this hour. Date format: Time format: HH:ii:ss'),
            ),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => ($id != false ? $this->l('Edit existing block') : $this->l('Create new block')),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = ($id != false ? 'editHbox' : 'addHbox');
        $helper->submit_action = ($id != false ? 'submitEditHbox' : 'submitAddNewHbox');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getBlockFields($id),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getBlockFieldsGlobal()
    {
        return array(
            'hbp_notinyjs' => Configuration::get('hbp_notinyjs'),
            'hbp_tiny' => Configuration::get('hbp_tiny'),
            'hbp_forceurls' => Configuration::get('hbp_forceurls'),
            'hbp_noajax' => Configuration::get('hbp_noajax'),
        );
    }

    public function getBlockFields($id = false)
    {
        $langs = array();
        foreach (Language::getLanguages() AS $key => $value) {
            $langs[$value['id_lang']] = '';
        };

        $array = array();
        if ($id != false) {
            $array['editblock'] = $id;
            $block = new hbox($id);
            foreach ($block as $object => $value) {
                if ($object == 'url') {
                    $object = 'selected_url';
                }
                $array['hbp_' . $object] = $value;
            }
            $array['hbp_hook'] = $block->hook;
            $explode_daytypes = explode(",", $block->daytype);
            foreach ($explode_daytypes AS $dt) {
                $array["hbp_daytype_" . $dt] = 1;
            }

            foreach (Group::getGroups(Configuration::get('PS_LANG_DEFAULT')) as $key => $value) {
                $array['groupBox_' . $value['id_group']] = false;
            }
            $array['hbp_cgroup'] = explode(",", $block->cgroup);
            foreach ($array['hbp_cgroup'] AS $h => $v) {
                $array['groupBox_' . $v] = true;
            }


            return $array;
        } else {
            $block = new hbox();
            foreach ($block as $object => $value) {
                if ($object == 'url') {
                    $object = 'selected_url';
                }
                $array['hbp_' . $object] = '';
            }
            $array['hbp_hook'] = Tools::getValue('newblock');
            $array['hbp_onmobile'] = true;
            $array['hbp_ontablet'] = true;
            $array['hbp_onpc'] = true;
            $array['hbp_cgroup'] = explode(",", $block->cgroup);


            foreach (Group::getGroups(Configuration::get('PS_LANG_DEFAULT')) as $key => $value) {
                $array['groupBox_' . $value['id_group']] = false;
            }
            foreach ($array['hbp_cgroup'] AS $h => $v) {
                $array['groupBox_' . $v] = true;
            }

            $array['hbp_body'] = $langs;
            return $array;
        }
    }

    public static function getIdByName($hook_name)
    {
        $hook_ids = array();
        $result = Db::getInstance()->ExecuteS('
           SELECT `id_hook`, `name`
           FROM `' . _DB_PREFIX_ . 'hook`
           UNION
           SELECT `id_hook`, ha.`alias` as name
           FROM `' . _DB_PREFIX_ . 'hook_alias` ha
           INNER JOIN `' . _DB_PREFIX_ . 'hook` h ON ha.name = h.name');
        foreach ($result as $row) {
            $hook_ids[strtolower($row['name'])] = $row['id_hook'];
        }


        return (isset($hook_ids[$hook_name]) ? $hook_ids[$hook_name] : false);
    }

    public function displayMyFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
    {
        if (count($languages) == 1) {
            return false;
        }

        $output = '
        <div id="languages_' . $id . '" class="my_language_flags">';
        foreach ($languages as $language) {
            $output .= '<span id="langbutton' . $id . '_' . $language['id_lang'] . '" class="langbutton' . $id . ' ' . ($language['id_lang'] == $default_language ? 'preselected' : 'button-outline') . ' pointer" alt="' . $language['name'] . '" title="' . $language['name'] . '" onclick="changeLanguageMine(\'' . $id . '\', \'' . $ids . '\', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');">' . $language['iso_code'] . '</span>';
        }
        $output .= '</div>';

        if ($return) {
            return $output;
        }
        echo $output;
    }

    public function installNewHook($name)
    {
        return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'hbp_customhook` (hook) VALUES ("' . $name . '")');
    }

    public function verifyNewHook($name)
    {
        $return = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'hbp_customhook` WHERE hook="' . $name . '"');
        if ($return == false) {
            $this->installNewHook($name);
        }
    }

    public function getListNewHook()
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'hbp_customhook`');
    }

    public function rebuildModuleFile()
    {
        if (is_writable("../modules/htmlboxpro/htmlboxpro.php")) {
            $functions_code = $this->regenerateFunctions();
            $content = file_get_contents("../modules/htmlboxpro/htmlboxpro.php");
            $part1 = "//~~";
            $part2 = "explode";
            $part3 = "~~//";
            $m = explode("//~~explode~~//", $content);
            $full_content = $m[0] . $part1 . $part2 . $part3 . $m[1] . "\n" . $part1 . $part2 . $part3 . "\n" . $functions_code . "\n" . $part1 . $part2 . $part3 . "\n" . $m[3];
            file_put_contents("../modules/htmlboxpro/htmlboxpro.php", $full_content);
            return true;
        } else {
            return false;
        }
    }

    public function regenerateFunctions()
    {
        $functions = "";

        /**
         * foreach ($this->allhooks as $k => $v)
         * {
         * $functions .= '
         * function hook' . $v . '($params){
         * return $this->generateHookContents("' . $v . '", $params);
         * }
         * ';
         * }
         * return $functions;
         **/

        foreach ($this->getListNewHook() as $k => $v) {
            $functions .= '
           	function hook' . $v['hook'] . '($params){ 
                return $this->generateHookContents("' . $v['hook'] . '", $params);
            }
            ';
        }
        return $functions;
    }


    /** FRONT OFFICE HOOK FUNCTIONS */

    public function generateHookContents($hook, $params)
    {
        if (Tools::getValue('ajax') == 1 && Tools::getValue('action') == 'refresh' && Configuration::get('hbp_noajax') == 1) {
            return;
        }

        if (Configuration::get('hbp_' . $hook) == 1) {
            if (isset($params['product'])) {
                if (Tools::version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
                    $params['product'] = (array)$params['product'];
                }
            }

            if (Tools::getValue('id_product', 'false') != 'false' && (!isset($params['product']['id']) || !isset($params['product']['id_product']))) {
                $product = new Product(Tools::getValue('id_product', 0), false, $this->context->language->id);
                if (Tools::version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
                    $params['product'] = (array)$product;
                }
            }

            $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'), $this->context->language->id);
            $blocks = $this->get_blocks($hook, 1, $this->context->cookie->id_lang, $params, true);
            $this->context->smarty->assign('page_name', isset($this->context->controller->php_self) ? $this->context->controller->php_self : '');
            $this->context->smarty->assign('currency_default', $currency_default);
            $this->context->smarty->assign('hook_params', $params);
            $this->context->smarty->assign('device_type', Context::getContext()->getDevice());
            $this->context->smarty->assign(array('blocks' => $blocks));
            $this->context->smarty->assign('customer_popup', $this->context->cookie);
            $this->context->smarty->assign('logged', $this->context->customer->isLogged());
            $this->context->smarty->assign(array('is_https' => (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on" ? 1 : 0)));
            return $this->display(__file__, 'html.tpl');
        }
    }

    public function hookdisplayHeader($params)
    {
        return $this->generateHookContents("displayHeader", $params);
    }

    public function hookdisplayBanner($params)
    {
        return $this->generateHookContents("displayBanner", $params);
    }

    public function hookdisplayNav1($params)
    {
        return $this->generateHookContents("displayNav1", $params);
    }

    public function hookdisplayNav2($params)
    {
        return $this->generateHookContents("displayNav2", $params);
    }

    public function hookdisplayNavFullWidth($params)
    {
        return $this->generateHookContents("displayNavFullWidth", $params);
    }

    public function hookdisplayTop($params)
    {
        return $this->generateHookContents("displayTop", $params);
    }

    public function hookdisplayHome($params)
    {
        return $this->generateHookContents("displayHome", $params);
    }

    public function hookdisplayFooterBefore($params)
    {
        return $this->generateHookContents("displayFooterBefore", $params);
    }

    public function hookdisplayFooter($params)
    {
        return $this->generateHookContents("displayFooter", $params);
    }

    public function hookdisplayFooterAfter($params)
    {
        return $this->generateHookContents("displayFooterAfter", $params);
    }

    public function hookdisplayMyAccountBlock($params)
    {
        return $this->generateHookContents("displayMyAccountBlock", $params);
    }

    public function hookdisplayBeforeBodyClosingTag($params)
    {
        return $this->generateHookContents("displayBeforeBodyClosingTag", $params);
    }

    public function hookdisplayLeftColumn($params)
    {
        return $this->generateHookContents("displayLeftColumn", $params);
    }

    public function hookdisplayRightColumn($params)
    {
        return $this->generateHookContents("displayRightColumn", $params);
    }

    public function hookdisplayCustomerAccount($params)
    {
        return $this->generateHookContents("displayCustomerAccount", $params);
    }

    public function hookdisplayCustomerAccountForm($params)
    {
        return $this->generateHookContents("displayCustomerAccountForm", $params);
    }

    public function hookdisplayProductAdditionalInfo($params)
    {
        return $this->generateHookContents("displayProductAdditionalInfo", $params);
    }

    public function hookdisplayReassurance($params)
    {
        return $this->generateHookContents("displayReassurance", $params);
    }

    public function hookdisplayFooterProduct($params)
    {
        return $this->generateHookContents("displayFooterProduct", $params);
    }

    public function hookdisplayAfterProductThumbs($params)
    {
        return $this->generateHookContents("displayAfterProductThumbs", $params);
    }

    public function hookdisplayProductListReviews($params)
    {
        return $this->generateHookContents("displayProductListReviews", $params);
    }

    public function hookactionProductOutOfStock($params)
    {
        return $this->generateHookContents("actionProductOutOfStock", $params);
    }

    public function hookdisplayShoppingCart($params)
    {
        return $this->generateHookContents("displayShoppingCart", $params);
    }

    public function hookdisplayCartExtraProductActions($params)
    {
        return $this->generateHookContents("displayCartExtraProductActions", $params);
    }

    public function hookdisplayShoppingCartFooter($params)
    {
        return $this->generateHookContents("displayShoppingCartFooter", $params);
    }

    public function hookdisplayExpressCheckout($params)
    {
        return $this->generateHookContents("displayExpressCheckout", $params);
    }

    public function hookdisplayCustomerLoginFormAfter($params)
    {
        return $this->generateHookContents("displayCustomerLoginFormAfter", $params);
    }

    public function hookdisplayBeforeCarrier($params)
    {
        return $this->generateHookContents("displayBeforeCarrier", $params);
    }

    public function hookdisplayAfterCarrier($params)
    {
        return $this->generateHookContents("displayAfterCarrier", $params);
    }

    public function hookdisplayPaymentTop($params)
    {
        return $this->generateHookContents("displayPaymentTop", $params);
    }

    public function hookdisplayPaymentByBinaries($params)
    {
        return $this->generateHookContents("displayPaymentByBinaries", $params);
    }

    public function hookdisplayLeftColumnProduct($params)
    {
        return $this->generateHookContents('displayLeftColumnProduct', $params);
    }

    public function hookdisplayRightColumnProduct($params)
    {
        return $this->generateHookContents('displayRightColumnProduct', $params);
    }

    /** CUSTOM HOOKS **/








//~~explode~~//

//~~explode~~//








}

class htmlboxproUpdate extends htmlboxpro
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

if (file_exists(_PS_MODULE_DIR_ . 'htmlboxpro/lib/smartyTemplatesManager/smartyTemplatesManager.php')) {
    require_once _PS_MODULE_DIR_ . 'htmlboxpro/lib/smartyTemplatesManager/smartyTemplatesManager.php';
}

if (file_exists(_PS_MODULE_DIR_ . 'htmlboxpro/lib/searchTool/searchTool.php')) {
    require_once _PS_MODULE_DIR_ . 'htmlboxpro/lib/searchTool/searchTool.php';
}
?>