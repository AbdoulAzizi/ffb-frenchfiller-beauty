<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from MigrationPro MMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the MigrationPro MMC is strictly forbidden.
 * In order to obtain a license, please contact us: migrationprommc@gmail.com
 *
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe MigrationPro MMC
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la MigrationPro MMC est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter la MigrationPro MMC a l'adresse: migrationprommc@gmail.com
 *
 * @author    Edgar I.
 * @copyright Copyright (c) 2012-2016 MigrationPro MMC
 * @license   Commercial license
 * @package   MigrationPro: Prestashop To PrestaShop
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/loggers/MigrationProDBWarningLogger.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/loggers/MigrationProDBErrorLogger.php');

class AdminMigrationProController extends AdminController
{
    // --- response vars:

    public $errors;
    protected $response;

    // --- request vars:

    protected $stepNumber;
    protected $toStepNumber;

    // -- dynamic vars:

    protected $forceIds = true;
    protected $forceManufacturerIds = false;
    protected $forceCategoryIds = false;
    protected $forceCarrierIds = false;
    protected $forceProductIds = false;
    protected $forceCatalogPriceRuleIds = false;
    protected $forceCustomerIds = false;
    protected $forceOrderIds = false;
    protected $forceCMSIds = false;

    protected $ps_validation_errors = true;

    protected $truncate = false;

    // --- source cart vars:

    protected $url_cart;
    protected $server_path = '/modules/migrationproserver/server.php';
//    protected $server_path = '/migration_pro/server.php';
    protected $token_cart;
    protected $cms;
    protected $image_category;
    protected $image_carrier;
    protected $image_product;
    protected $image_manufacturer;
    protected $image_supplier;
    protected $image_employee;
    protected $table_prefix;
    protected $version;
    protected $charset;
    protected $blowfish_key;
    protected $cookie_key;
    protected $mapping;
    protected $languagesForQuery;
    protected $speed;
    protected $module;

    // --- helper objects

    protected $query;
    protected $client;
    protected $recent_data;
    public function __construct()
    {
        $this->display = 'edit';
        parent::__construct();
        $this->controller_type = 'moduleadmin'; //instead of AdminController’s admin
        $tab = new Tab($this->id); // an instance with your tab is created; if the tab is not attached to the module, the exception will be thrown
        if (!$tab->module) {
            throw new PrestaShopException('Admin tab ' . get_class($this) . ' is not a module tab');
        }
        $this->module = Module::getInstanceByName($tab->module);
        if (!$this->module->id) {
            throw new PrestaShopException("Module {$tab->module} not found");
        }
        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminMigrationPro'));

        $this->stepNumber = (int)Tools::getValue('step_number');
        $this->toStepNumber = (int)Tools::getValue('to_step_number');

        $this->initParams();
        if ($this->stepNumber > 1 || self::isEmpty($this->stepNumber)) {
            $this->mapping = MigrationProMapping::listMapping(true, true);

            if (isset($this->mapping['languages'])) {
                // -- unset language where value = 0
                if (($key = array_search(0, $this->mapping['languages'])) !== false) {
                    unset($this->mapping['languages'][$key]);
                }
                $keys = array_keys($this->mapping['languages']);
                $this->languagesForQuery = implode(',', $keys);
            }

            if (!self::isEmpty($this->url_cart) && !self::isEmpty($this->token_cart)) {
                $this->client = new EDClient($this->url_cart . $this->server_path, $this->token_cart);
                if (Configuration::get($this->module->name . '_debug_mode')) {
                    $this->client->debugOn();
                } else {
                    $this->client->debugOff();
                }
            }
            $this->query = new EDQuery();
            $this->query->setVersion($this->version);
            $this->query->setCart($this->cms);
            $this->query->setPrefix($this->table_prefix);
            $this->query->setLanguages($this->languagesForQuery);
            $this->query->setRowCount($this->speed);
            $this->query->setRecentData($this->recent_data);
        }

        if (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP) {
            $allShops = Shop::getCompleteListOfShopsID();
            Shop::setContext(Shop::CONTEXT_SHOP, $allShops[0]);
        }
    }


    private function initHelperObjects()
    {
        $this->mapping = MigrationProMapping::listMapping(true, true);

        if (isset($this->mapping['languages'])) {
            // -- unset language where value = 0
            if (($key = array_search(0, $this->mapping['languages'])) !== false) {
                unset($this->mapping['languages'][$key]);
            }
            $keys = array_keys($this->mapping['languages']);
            $this->languagesForQuery = implode(',', $keys);
        }

        if (!self::isEmpty($this->url_cart) && !self::isEmpty($this->token_cart)) {
            $this->client = new EDClient($this->url_cart . $this->server_path, $this->token_cart);
            if (Configuration::get($this->module->name . '_debug_mode')) {
                $this->client->debugOn();
            } else {
                $this->client->debugOff();
            }
        }
        $this->query = new EDQuery();
        $this->query->setVersion($this->version);
        $this->query->setCart($this->cms);
        $this->query->setPrefix($this->table_prefix);
        $this->query->setLanguages($this->languagesForQuery);
        $this->query->setRowCount($this->speed);
        $this->query->setRecentData($this->recent_data);
    }

    // --- request processes
    public function postProcess()
    {
        parent::postProcess();
    }

    public function clearSmartyCache()
    {
        Tools::enableCache();
        Tools::clearCache($this->context->smarty);
        Tools::restoreCacheSettings();
    }

    public function ajaxProcessGenerateZip()
    {
        if (!Configuration::get('migrationpro_token_is_generated')) {
            if (!Configuration::get('migrationpro_token')) {
                $token = md5(Configuration::get('PS_SHOP_DOMAIN') . end(explode('\\', _PS_ADMIN_DIR_)));
                Configuration::updateValue('migrationpro_token', $token);
            }

            $txt_file = Tools::file_get_contents(_PS_MODULE_DIR_ . '/migrationpro/views/templates/admin/server.tpl');
            $txt_file = str_replace("[[[[[[sample_token]]]]]]]", Configuration::get('migrationpro_token'), $txt_file);
            $txt_file = str_replace("[[[[[[old_domain]]]]]]]", Configuration::get('PS_SHOP_DOMAIN'), $txt_file);
            file_put_contents(_PS_MODULE_DIR_ . '/migrationpro/assets/server.php', $txt_file);

            $zip = new ZipArchive;
            if ($zip->open(_PS_MODULE_DIR_ . 'migrationpro/assets/migrationproserver.zip')
            ) {
                // Add file to the zip file
                $zip->addFile(_PS_MODULE_DIR_ . '/migrationpro/assets/server.php', 'migrationproserver/server.php');

                // All files are added, so close the zip file.
                $zip->close();
            }
            Configuration::updateValue('migrationpro_token_is_generated', '1');
        }
    }


    public function ajaxProcessValidateStep()
    {
        $this->response = array('has_error' => false, 'has_warning' => false);

        if (!$this->tabAccess['edit']) {
            $this->errors[] = EDImport::displayError($this->module->l('You do not have permission to use this wizard.'));
        } else {
            // -- reset old data and response url to start new migration
            if (Tools::getIsset('resume')) {
                $this->response['step_form'] = $this->module->renderStepThree();
            } elseif (Tools::getValue('to_step_number') >= Tools::getValue('step_number')) {
                $this->validateStepFieldsValue();
            }
        }

        if (count($this->errors)) {
            $this->response['has_error'] = true;
            $this->response['errors'] = $this->errors;
        }
        if (count($this->warnings)) {
            $this->response['has_warning'] = true;
            $this->response['warnings'] = $this->warnings;
        }

        die(Tools::jsonEncode($this->response));
    }

    // --- validate functions

    private function validateStepFieldsValue()
    {
        if ($this->stepNumber == 1) {
            // validate url
            if (!Tools::getValue('source_shop_url')) {
                $this->errors[] = $this->module->l('Source PrestaShop URL is required.');
            } elseif (!Validate::isAbsoluteUrl(Tools::getValue('source_shop_url'))) {
                $this->errors[] = $this->module->l('Please enter a valid source PrestaShop URL.');
            }

            if ($this->truncateModuleTables()) {
                $this->errors[] = $this->module->l('Permission access to truncate module tables is denied! Please contact with support team!');
            }

            if (self::isEmpty($this->errors)) {
                $sourceURL = Tools::getValue('source_shop_url') . $this->server_path;
                $this->client = new EDClient($sourceURL, Configuration::get('migrationpro_token'));
                if (Configuration::get($this->module->name . '_debug_mode')) {
                    $this->client->debugOn();
                } else {
                    $this->client->debugOff();
                }
                if ($this->client->check()) {
                    $content = $this->client->getContent();
                    if (isset($content['cms']) && !self::isEmpty($content['cms'])) {
                        $this->saveParamsToConfiguration($content);
                        $this->initHelperObjects();
                        if ($this->requestToCartDetails()) {
                            $this->response['step_form'] = $this->module->renderStepTwo();
                        }
                    } else {
                        $context = Context::getContext();
                        $module_path = Configuration::get('migrationpro_module_path');
                        $context->smarty->assign('sourceURL', $sourceURL);
                        $error_msg = $context->smarty->fetch($module_path . 'views/templates/admin/connection_error.tpl');
                        $this->errors[] = EDImport::displayError($error_msg);
                    }
                } else {
                    $error_msg = $this->client->getMessage();
                    if (self::isEmpty($error_msg)) {
                        $context = Context::getContext();
                        $module_path = Configuration::get('migrationpro_module_path');
                        $context->smarty->assign('sourceURL', $sourceURL);
                        $error_msg = $context->smarty->fetch($module_path . 'views/templates/admin/connection_error.tpl');
                        $this->errors[] = EDImport::displayError($error_msg);
                    } else {
                        $this->errors[] = EDImport::displayError($this->module->l('Please check the URL ') . ' - ' . $this->client->getMessage());
                    }
                }
            }
        } elseif ($this->stepNumber == 2) {
            $maps = Tools::getValue('map');
            $languageSumValue = array_sum($maps['languages']);
            $languageDiffResArray = array_diff_assoc($maps['languages'], array_unique($maps['languages']));
            if (!($languageSumValue > 0 && self::isEmpty($languageDiffResArray))) {
                $this->errors[] = $this->module->l('Select a different language for each source language.');
            }

            $shopSumValue = array_sum($maps['multi_shops']);
            $shopDiffResArray = array_diff_assoc($maps['multi_shops'], array_unique($maps['multi_shops']));
            if (!($shopSumValue > 0 && self::isEmpty($shopDiffResArray))) {
                $this->errors[] = $this->module->l('Select a different shop for each source shop.');
            }
            Configuration::deleteByName('migrationpro_last_migrated_cat_id');
            Configuration::deleteByName('migrationpro_last_migrated_parent_id');
            if (self::isEmpty($this->errors)) {
                $this->initHelperObjects();
                Configuration::updateValue($this->module->name . '_force_manufacturer_ids', Tools::getValue('force_manufacturer_ids'));
                Configuration::updateValue($this->module->name . '_force_category_ids', Tools::getValue('force_category_ids'));
                Configuration::updateValue($this->module->name . '_force_carrier_ids', Tools::getValue('force_carrier_ids'));
                Configuration::updateValue($this->module->name . '_force_product_ids', Tools::getValue('force_product_ids'));
                Configuration::updateValue($this->module->name . '_force_catalogPriceRule_ids', Tools::getValue('force_catalogPriceRule_ids'));
                Configuration::updateValue($this->module->name . '_force_customer_ids', Tools::getValue('force_customer_ids'));
                Configuration::updateValue($this->module->name . '_force_order_ids', Tools::getValue('force_order_ids'));
                Configuration::updateValue($this->module->name . '_force_cms_ids', Tools::getValue('force_cms_ids'));
                Configuration::updateValue($this->module->name . '_ps_validation_errors', Tools::getValue('ps_validation_errors'));
                Configuration::updateValue($this->module->name . '_clear_data', Tools::getValue('clear_data'));
                Configuration::updateValue($this->module->name . '_migrate_recent_data', Tools::getValue('migrate_recent_data'));
                Configuration::updateValue($this->module->name . '_query_row_count', self::convertSpeedNameToNumeric(Tools::getValue('speed')));
                if ($this->createMapping($maps) && $this->createProcess()) {
                    $this->saveMappingValues(MigrationProMapping::listMapping(true, true));
                    // turn on allow html iframe on
                    if (!Configuration::get('PS_ALLOW_HTML_IFRAME')) {
                        Configuration::updateValue('PS_ALLOW_HTML_IFRAME', 1);
                        Configuration::updateValue($this->module->name . '_allow_html_iframe', 1);
                    }

                    $this->response['step_form'] = $this->module->renderStepThree();
                } else {
                    $this->errors[] = $this->module->l('Select a minimum of one data type to start migrating.');
                }
            }
        }
    }


    public function saveMappingValues($mapping_values)
    {
        if (Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_save_mapping`')) {
            foreach ($mapping_values as $mappingType => $mappingObject) {
                foreach ($mappingObject as $source_id => $local_id) {
                    $mapping = new MigrationProSaveMapping();
                    $mapping->type = $mappingType;
                    $mapping->source_id = $source_id;
                    $mapping->source_name = $mappingType;
                    $mapping->local_id = $local_id;
                    if (!$mapping->save()) {
                        $this->errors[] = $this->module->l('Can\'t save to database mapping information. ');
                    }
                }
            }
        }
    }

    public function ajaxProcessClearCache()
    {
        if (Tools::getValue('clear_cache')) {
            ini_set('max_execution_time', 0);
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
            Tools::generateIndex();
            Search::indexation(true);

            $this->response['has_error'] = false;
            $this->response['has_warning'] = false;
            if (count($this->errors)) {
                $this->response['has_error'] = true;
                $this->response['errors'] = $this->errors;
            }
            if (count($this->warnings)) {
                $this->response['has_warning'] = true;
                $this->response['warnings'] = $this->warnings;
            }

            die(Tools::jsonEncode($this->response));
        }
    }

    public function ajaxProcessDebugOn()
    {
        if (Tools::getValue('turn') == 1) {
            Configuration::updateValue($this->module->name . '_debug_mode', 1);
        } else {
            Configuration::updateValue($this->module->name . '_debug_mode', 0);
        }
    }

    public function ajaxProcessSpeedUp()
    {
        if (!empty(Tools::getValue('speed'))) {
            Configuration::updateValue($this->module->name . '_query_row_count', Tools::getValue('speed'));
        }
    }

    public function ajaxProcessImportProcess($die = true)
    {
        $this->response = array('has_error' => false, 'has_warning' => false);

        if (!$this->tabAccess['edit']) {
            $this->errors[] = EDImport::displayError($this->module->l('You do not have permission to use this wizard.'));
        } else {
            $activeProcess = MigrationProProcess::getActiveProcessObject();
            if (Validate::isLoadedObject($activeProcess)) {
                $this->query->setOffset($activeProcess->imported);
                if ($activeProcess->imported == 0) {
                    if ($this->truncate && !$this->truncateTables($activeProcess->type)) {
                        $this->errors[] = $this->module->l('Can\'t clear current data on Target shop ') . Db::getInstance()->getMsgError();
                    }

                    $activeProcess->time_start = date('Y-m-d H:i:s', time());
                    $activeProcess->save();
                }

                if ($activeProcess->type == 'taxes') {
                    $this->importTaxes($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'manufacturers') {
                    $this->importManufacturers($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'categories') {
                    $this->importCategories($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'carriers') {
                    $this->importCarriers($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'products') {
                    $this->importProducts($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'accessories') {
                    $this->importAccessories($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'catalog price rules') {
                    $this->importCatalogPriceRules($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'employees') {
                    $this->importEmployees($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'customers') {
                    $this->importCustomers($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'orders') {
                    $this->importOrders($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'message_threads') {
                    $this->importMessageThreads($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'cms') {
                    $this->importCMS($activeProcess);
                    $this->clearSmartyCache();
                } elseif ($activeProcess->type == 'seo') {
                    $this->importMeta($activeProcess);
                    $this->clearSmartyCache();
                }
            } else {
                die('no process.');
            }
            $this->response['percent'] = MigrationProProcess::calculateImportedDataPercent();
            if ($this->response['percent'] == 100) {
                // turn off allow html iframe feature
                if (Configuration::get($this->module->name . '_allow_html_iframe')) {
                    Configuration::updateValue('PS_ALLOW_HTML_IFRAME', 0, null, 0, 0);
                    Configuration::updateValue($this->module->name . '_allow_html_iframe', 0, null, 0, 0);
                }
            }
        }

        if (count($this->errors)) {
            $this->response['has_error'] = true;
            $this->response['errors'] = $this->errors;
        }
        if (count($this->warnings)) {
            $this->response['has_warning'] = true;
            $this->response['warnings'] = $this->warnings;
        }

        if ($die) {
            die(Tools::jsonEncode($this->response));
        }
    }

    // --- import functions

    private function importTaxes($process)
    {
        $this->client->serializeOn();
        $this->client->setPostData($this->query->taxRulesGroup());
        if ($this->client->query()) {
            $taxRulesGroups = $this->client->getContent();
            $taxRulesGroupIds = self::getCleanIDs($taxRulesGroups['tax_rules_group'], 'id_tax_rules_group');
            $this->client->serializeOff();
            $this->client->setPostData($this->query->taxRules($taxRulesGroupIds));
            if ($this->client->query()) {
                $taxRules = $this->client->getContent();
                $taxIds = self::getCleanIDs($taxRules, 'id_tax');
                $countryIds = self::getCleanIDs($taxRules, 'id_country');
                $stateIds = self::getCleanIDs($taxRules, 'id_state');
                $this->client->setPostData($this->query->taxLangCountryLangState($taxIds, $countryIds, $stateIds));
                $this->client->serializeOn();
                if ($this->client->query()) {
                    $taxLangCountryLangState = $this->client->getContent();
                    $import = new EDImport($process, $this->version, $this->url_cart, $this->forceIds, $this->module);
                    $import->setPsValidationErrors($this->ps_validation_errors);
                    $import->taxes($taxRulesGroups, $taxRules, $taxLangCountryLangState);
                    $this->errors = $import->getErrorMsg();
                    $this->warnings = $import->getWarningMsg();
                    $this->response = $import->getResponse();
                } else {
                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importManufacturers($process)
    {
        $this->client->setPostData($this->query->manufactures());
        if ($this->client->query()) {
            $manufacturers = $this->client->getContent();
            $this->client->serializeOn();
            $this->client->setPostData($this->query->manufacturesSqlSecond(self::getCleanIDs($manufacturers, 'id_manufacturer')));
            if ($this->client->query()) {
                $manufacturersAdditionalSecond = $this->client->getContent();
                $import = new EDImport($process, $this->version, $this->url_cart, $this->forceManufacturerIds, $this->module);
                $import->setImagePath($this->image_manufacturer);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->manufacturers($manufacturers, $manufacturersAdditionalSecond);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importCategories($process)
    {
        //@TODO find fix for PS 1.4 for category id 2 WHERE is ID 2 standart category from list
        $this->client->serializeOff();
        $this->client->setPostData($this->query->category());
        if ($this->client->query()) {
            $categories = $this->client->getContent();
            $this->client->serializeOn();
            $this->client->setPostData($this->query->categorySqlSecond(self::getCleanIDs($categories, 'id_category')));
            if ($this->client->query()) {
                $categoriesAdditionalSecond = $this->client->getContent();
                $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCategoryIds, $this->module, $this->client, $this->query);
                $import->setImagePath($this->image_category);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->categories($categories, $categoriesAdditionalSecond);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importCarriers($process)
    {
        $this->client->setPostData($this->query->carrier());
        if ($this->client->query()) {
            $carriers = $this->client->getContent();
            $this->client->serializeOn();
            $this->client->setPostData($this->query->carrierSqlSecond(self::getCleanIDs($carriers, 'id_carrier')));
            if ($this->client->query()) {
                $carriersAdditionalSecond = $this->client->getContent();

                $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCarrierIds, $this->module, $this->client, $this->query);
                $import->setImagePath($this->image_carrier);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->carriers($carriers, $carriersAdditionalSecond);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

//    private function importWarehouse($process,$recent_data)
//    {
//        $this->client->setPostData($this->query->warehouses());
//        if ($this->client->query()) {
//            $warehouses = $this->client->getContent();
//
//            $this->client->serializeOn();
//            $this->client->setPostData($this->query->warehousesSqlSecond(self::getCleanIDs($warehouses, 'id_warehouse'), self::getCleanIDs($warehouses, 'id_address')));
//            if ($this->client->query()) {
//                $warehousesAdditionalSecond = $this->client->getContent();
//                $this->client->setPostData($this->query->countryState(self::getCleanIDs($warehousesAdditionalSecond['address'], 'id_country'), self::getCleanIDs($warehousesAdditionalSecond['address'], 'id_state')));
//                if ($this->client->query()) {
//                    $countryState = $this->client->getContent();
//                    $this->client->serializeOff();
//                    $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCarrierIds, $this->module, $this->client, $this->query);
//                    $import->setPsValidationErrors($this->ps_validation_errors);
//                    $import->setRecentData($recent_data);
//                    $import->warehouses($warehouses, $warehousesAdditionalSecond, $countryState);
//                    $this->errors = $import->getErrorMsg();
//                    $this->warnings = $import->getWarningMsg();
//                    $this->response = $import->getResponse();
//                } else {
//                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
//                }
//            }
//        } else {
//            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
//        }
//    }

    private function importProducts($process)
    {
        $this->client->setPostData($this->query->product());
        if ($this->client->query()) {
            $products = $this->client->getContent();
            $productIds = self::getCleanIDs($products, 'id_product');
            $this->client->serializeOn();
            $this->client->setPostData($this->query->productSqlSecond($productIds));
            if ($this->client->query()) {
                $productAdditionalSecond = $this->client->getContent();
                $id_product_attribute = self::getCleanIDs($productAdditionalSecond['product_attribute'], 'id_product_attribute');
                $id_feature = self::getCleanIDs($productAdditionalSecond['feature_product'], 'id_feature');
                $id_feature_value = self::getCleanIDs($productAdditionalSecond['feature_product'], 'id_feature_value');
                $id_supplier = self::getCleanIDs($productAdditionalSecond['product_supplier'], 'id_supplier');
                $id_customization_field = self::getCleanIDs($productAdditionalSecond['customization_field'], 'id_customization_field');
                $id_tag = self::getCleanIDs($productAdditionalSecond['product_tag'], 'id_tag');
                $id_image = self::getCleanIDs($productAdditionalSecond['image'], 'id_image');
                $id_attachment = self::getCleanIDs($productAdditionalSecond['product_attachment'], 'id_attachment');
                $this->client->setPostData($this->query->productSqlThird($id_product_attribute, $id_feature, $id_feature_value, $id_supplier, $id_customization_field, $id_tag, $id_image, $id_attachment));
                if ($this->client->query()) {
                    $productAdditionalThird = $this->client->getContent();
                    $id_attribute_group = self::getCleanIDs($productAdditionalThird['product_attribute_combination'], 'id_attribute_group');
                    $id_attribute = self::getCleanIDs($productAdditionalThird['product_attribute_combination'], 'id_attribute');
                    $this->client->setPostData($this->query->productSqlFourth($id_attribute_group, $id_attribute));
                    if ($this->client->query()) {
                        $productAdditionalFourth = $this->client->getContent();

                        $import = new EDImport($process, $this->version, $this->url_cart, $this->forceProductIds, $this->module, $this->client, $this->query);
                        $import->setImagePath($this->image_product);
                        $import->setImageSupplierPath($this->image_supplier);
                        $import->setPsValidationErrors($this->ps_validation_errors);
                        $import->products($products, $productAdditionalSecond, $productAdditionalThird, $productAdditionalFourth);
                        $this->errors = $import->getErrorMsg();
                        $this->warnings = $import->getWarningMsg();
                        $this->response = $import->getResponse();
                    } else {
                        $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                    }
                } else {
                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importAccessories($process)
    {
        $this->client->setPostData($this->query->accessories());
        if ($this->client->query()) {
            $accessories = $this->client->getContent();
            $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCatalogPriceRuleIds, $this->module, $this->client, $this->query);
            $import->setPsValidationErrors($this->ps_validation_errors);
            $import->accessories($accessories);
            $this->errors = $import->getErrorMsg();
            $this->warnings = $import->getWarningMsg();
            $this->response = $import->getResponse();
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importCatalogPriceRules($process)
    {
        $this->client->setPostData($this->query->specificPriceRule());
        if ($this->client->query()) {
            $specificPriceRules = $this->client->getContent();
            $this->client->serializeOn();
            $this->client->setPostData($this->query->specificPriceRuleCountry(self::getCleanIDs($specificPriceRules, 'id_country')));
            if ($this->client->query()) {
                $specificPriceRuleCountries = $this->client->getContent();
                $this->client->serializeOff();
                $this->client->setPostData($this->query->specificPriceRuleConditionGroup(self::getCleanIDs($specificPriceRules, 'id_specific_price_rule')));
                if ($this->client->query()) {
                    $specificPriceRuleConditionGroups = $this->client->getContent();
                    $this->client->setPostData($this->query->specificPriceRuleCondition(self::getCleanIDs($specificPriceRuleConditionGroups, 'id_specific_price_rule_condition_group')));
                    if ($this->client->query()) {
                        $specificPriceRuleConditions = $this->client->getContent();

                        $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCatalogPriceRuleIds, $this->module, $this->client, $this->query);
                        $import->setPsValidationErrors($this->ps_validation_errors);
                        $import->catalogPriceRules($specificPriceRules, $specificPriceRuleCountries, $specificPriceRuleConditionGroups, $specificPriceRuleConditions);
                        $this->errors = $import->getErrorMsg();
                        $this->warnings = $import->getWarningMsg();
                        $this->response = $import->getResponse();
                    }
                } else {
                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importEmployees($process)
    {
        $this->client->setPostData($this->query->employee());
        if ($this->client->query()) {
            $employees = $this->client->getContent();
            $this->client->setPostData($this->query->employeeShop(self::getCleanIDs($employees, 'id_employee')));
            if ($this->client->query()) {
                $employeesShop = $this->client->getContent();

                $import = new EDImport($process, $this->version, $this->url_cart, false, $this->module, $this->client, $this->query);
                $import->setImagePath($this->image_employee);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->employees($employees, $employeesShop);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importCustomers($process)
    {
        $this->client->setPostData($this->query->customers());
        $this->client->serializeOff();
        if ($this->client->query()) {
            $customers = $this->client->getContent();
            $this->client->setPostData($this->query->address(self::getCleanIDs($customers, 'id_customer')));
            $this->client->serializeOn();
            if ($this->client->query()) {
                $addresses = $this->client->getContent();
                $this->client->setPostData($this->query->countryState(self::getCleanIDs($addresses['address'], 'id_country'), self::getCleanIDs($addresses['address'], 'id_state')));
                if ($this->client->query()) {
                    $countryState = $this->client->getContent();
                    $this->client->serializeOff();
                    $this->client->setPostData($this->query->cart(self::getCleanIDs($customers, 'id_customer')));
                    if ($this->client->query()) {
                        $carts = $this->client->getContent();
                        $this->client->serializeOn();
                        $this->client->setPostData($this->query->cartProductCartRule(self::getCleanIDs($carts, 'id_cart')));
                        if ($this->client->query()) {
                            $cartProductCartRules = $this->client->getContent();
                            $this->client->serializeOff();
                            $this->client->setPostData($this->query->cartRule(self::getCleanIDs($customers, 'id_customer')));
                            if ($this->client->query()) {
                                $cartRules = $this->client->getContent();
                                $this->client->serializeOn();
                                if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                    $cartRuleColumnName = 'id_discount';
                                } else {
                                    $cartRuleColumnName = 'id_cart_rule';
                                }
                                $this->client->setPostData($this->query->cartRuleSqlSecond(self::getCleanIDs($cartRules, $cartRuleColumnName)));
                                if ($this->client->query()) {
                                    $cartRuleAdditionalSecond = $this->client->getContent();
                                    $this->client->setPostData($this->query->cartRuleCountry(self::getCleanIDs($cartRuleAdditionalSecond['cart_rule_countries'], 'id_country')));
                                    if ($this->client->query()) {
                                        $cartRuleCountries = $this->client->getContent();
                                        $this->client->serializeOff();
                                        $this->client->setPostData($this->query->cartRuleProductRule(self::getCleanIDs($cartRuleAdditionalSecond['cart_rule_product_rule_groups'], 'id_product_rule_group')));
                                        if ($this->client->query()) {
                                            $cartRuleProductRules = $this->client->getContent();
                                            $this->client->setPostData($this->query->cartRuleProductRuleValue(self::getCleanIDs($cartRuleProductRules, 'id_product_rule')));
                                            if ($this->client->query()) {
                                                $cartRuleProductRuleValues = $this->client->getContent();

                                                $importCartRules = Configuration::get('importCartRules');
                                                $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCustomerIds, $this->module);
                                                $import->setPsValidationErrors($this->ps_validation_errors);
                                                $import->customers($customers, $addresses, $carts, $cartProductCartRules, $countryState, $cartRules, $cartRuleAdditionalSecond, $cartRuleCountries, $cartRuleProductRules, $cartRuleProductRuleValues, $importCartRules);
                                                $this->errors = $import->getErrorMsg();
                                                $this->warnings = $import->getWarningMsg();
                                                $this->response = $import->getResponse();
                                            } else {
                                                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                                            }
                                        } else {
                                            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                                        }
                                    } else {
                                        $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                                    }
                                } else {
                                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                                }
                            } else {
                                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                            }
                        } else {
                            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                        }
                    } else {
                        $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                    }
                } else {
                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importOrders($process)
    {
        $this->client->setPostData($this->query->order());
        if ($this->client->query()) {
            $orders = $this->client->getContent();
            $id_order = self::getCleanIDs($orders, 'id_order');
            $id_address_delivery = self::getCleanIDs($orders, 'id_address_delivery');
            $id_currency = self::getCleanIDs($orders, 'id_currency');
            $order_reference = self::getCleanIDs($orders, 'reference');
            $this->client->serializeOn();
            $this->client->setPostData($this->query->orderSqlSecond($id_order, $id_address_delivery, $id_currency, $order_reference));
            if ($this->client->query()) {
                $ordersAdditionalSecond = $this->client->getContent();
                $id_order_detail = self::getCleanIDs($ordersAdditionalSecond['order_detail'], 'id_order_detail');
                $id_order_return = self::getCleanIDs($ordersAdditionalSecond['order_return'], 'id_order_return');
                $id_order_invoice = self::getCleanIDs($ordersAdditionalSecond['order_invoice'], 'id_order_invoice');
                $id_country = self::getCleanIDs($ordersAdditionalSecond['address'], 'id_country');
                $id_state = self::getCleanIDs($ordersAdditionalSecond['address'], 'id_state');
                $id_customer_thread = self::getCleanIDs($ordersAdditionalSecond['customer_thread'], 'id_customer_thread');
                $id_order_slip = self::getCleanIDs($ordersAdditionalSecond['order_slip'], 'id_order_slip');
                $id_message = self::getCleanIDs($ordersAdditionalSecond['message'], 'id_message');
                $orderMessageIds = self::getCleanIDs($ordersAdditionalSecond['order_message'], 'id_order_message');
                $this->client->setPostData($this->query->orderSqlThird($id_order_detail, $id_order_return, $id_order_invoice, $id_country, $id_state, $id_customer_thread, $orderMessageIds, $id_order_slip, $id_message));
                if ($this->client->query()) {
                    $ordersAdditionalThird = $this->client->getContent();
                    $this->client->serializeOff();



                        $import = new EDImport($process, $this->version, $this->url_cart, $this->forceOrderIds, $this->module);
                        $import->setPsValidationErrors($this->ps_validation_errors);
                        $import->orders($orders, $ordersAdditionalSecond, $ordersAdditionalThird);
                        $this->errors = $import->getErrorMsg();
                        $this->warnings = $import->getWarningMsg();
                        $this->response = $import->getResponse();
                } else {
                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importMessageThreads($process)
    {
        $customerThreads = null;
        $customerMessages = null;
        $this->client->serializeOff();
        $this->client->setPostData($this->query->customerThreads());
        if ($this->client->query()) {
            $customerThreads = $this->client->getContent();
//            if (version_compare($this->version, '1.6', '<')) {
//                $customerThreads = $customerThreads;
//            } else {
            $this->client->setPostData($this->query->customerMessages(self::getCleanIDs($customerThreads, 'id_customer_thread')));
            if ($this->client->query()) {
                $customerMessages = $this->client->getContent();
                $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCatalogPriceRuleIds, $this->module, $this->client, $this->query);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->customerMessages($customerThreads, $customerMessages);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                return;
            }
//            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importCMS($process)
    {
        $this->client->setPostData($this->query->cms());
        if ($this->client->query()) {
            $cmses = $this->client->getContent();
            $this->client->serializeOn();
            $this->client->setPostData($this->query->cmsSqlSecond(self::getCleanIDs($cmses, 'id_cms'), self::getCleanIDs($cmses, 'id_cms_category')));
            if ($this->client->query()) {
                $cmsAdditionalSecond = $this->client->getContent();
                $this->client->setPostData($this->query->cmsSqlThird(self::getCleanIDs($cmsAdditionalSecond['cms_role'], 'id_cms_role'), self::getCleanIDs($cmsAdditionalSecond['cms_category'], 'id_cms_category'), self::getCleanIDs($cmsAdditionalSecond['cms_block'], 'id_cms_block')));
                if ($this->client->query()) {
                    $cmsAdditionalThird = $this->client->getContent();

                    $import = new EDImport($process, $this->version, $this->url_cart, $this->forceCMSIds, $this->module, $this->client, $this->query);
                    $import->setPsValidationErrors($this->ps_validation_errors);
                    $import->cmses($cmses, $cmsAdditionalSecond, $cmsAdditionalThird);
                    $this->errors = $import->getErrorMsg();
                    $this->warnings = $import->getWarningMsg();
                    $this->response = $import->getResponse();
                } else {
                    $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    private function importMeta($process)
    {
        $this->client->setPostData($this->query->meta());
        if ($this->client->query()) {
            $metas = $this->client->getContent();
            $this->client->setPostData($this->query->metaLang(self::getCleanIDs($metas, 'id_meta')));
            if ($this->client->query()) {
                $metaLang = $this->client->getContent();

                $import = new EDImport($process, $this->version, $this->url_cart, $this->forceIds, $this->module, $this->client, $this->query);
                $import->setPsValidationErrors($this->ps_validation_errors);
                $import->metas($metas, $metaLang);
                $this->errors = $import->getErrorMsg();
                $this->warnings = $import->getWarningMsg();
                $this->response = $import->getResponse();
            } else {
                $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
            }
        } else {
            $this->errors[] = $this->module->l('Can\'t execute query to source PrestaShop. ' . $this->client->getMessage());
        }
    }

    // --- Internal helper methods:

    private function truncateModuleTables()
    {
        $res = Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_data`');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_process`');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_mapping`');

        if (!$res) {
            return false;
        }
    }

    private function createMapping($maps)
    {
        $res = true;
        foreach ($maps as $map) {
            foreach ($map as $key => $val) {
                $mapping = new MigrationProMapping($key);
                $mapping->local_id = $val;
                $res &= $mapping->save();
            }
        }

        return $res;
    }

    private function createProcess()
    {
        $res = Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_process`');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_data`');
        MigrationProDBErrorLogger::removeErrorLogs();
        MigrationProDBWarningLogger::removeWarningLogs();
        MigrationProDBWarningLogger::removeLogFile();
        $this->client->setPostData($this->query->getCountInfo());
        $this->client->serializeOn();
        if ($this->client->query()) {
            $processes = $this->client->getContent();

            if (isset($processes['cart_rules'])) {
                unset($processes['cart_rules']);
                Configuration::updateValue('importCartRules', 1);
            } else {
                Configuration::updateValue('importCartRules', 0);
            }
            foreach ($processes as $processKey => $processCount) {
                if (isset($processCount[0]['c']) && !self::isEmpty($processCount[0]['c'])) {
                    $process = new MigrationProProcess();
                    if ($processKey == 'catalog_price_rules') {
                        $process->type = 'catalog price rules';
                    } else {
                        $process->type = $processKey;
                    }
                    $process->total = (int)$processCount[0]['c'];
                    $process->imported = 0;
                    $process->id_source = 0;
                    $process->error = 0;
                    $process->point = 0;
                    $process->time_start = 0;
                    $process->finish = 0;
                    $res &= $process->add();
                }
            }

            return $res;
        }

        return false;
    }

    private function saveParamsToConfiguration($content)
    {
        Configuration::updateValue($this->module->name . '_url', Tools::getValue('source_shop_url'));
        Configuration::updateValue($this->module->name . '_cms', $content['cms']);
        Configuration::updateValue($this->module->name . '_image_category', $content['image_category']);
        Configuration::updateValue($this->module->name . '_image_carrier', $content['image_carrier']);
        Configuration::updateValue($this->module->name . '_image_product', $content['image_product']);
        Configuration::updateValue($this->module->name . '_image_manufacturer', $content['image_manufacturer']);
        Configuration::updateValue($this->module->name . '_image_supplier', $content['image_supplier']);
        Configuration::updateValue($this->module->name . '_image_employee', $content['image_employee']);
        Configuration::updateValue($this->module->name . '_table_prefix', $content['table_prefix']);
        Configuration::updateValue($this->module->name . '_version', $content['version']);
        Configuration::updateValue($this->module->name . '_charset', $content['charset']);
        Configuration::updateValue($this->module->name . '_blowfish_key', $content['blowfish_key']);
        Configuration::updateValue($this->module->name . '_cookie_key', $content['cookie_key']);

        $this->initParams();
    }

    private function initParams()
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP) {
            //fix the osl php version issue
            $allShops = Shop::getCompleteListOfShopsID();
            Shop::setContext(Shop::CONTEXT_SHOP, $allShops[0]);
        }

        $this->url_cart = Configuration::get($this->module->name . '_url');
        $this->token_cart = Configuration::get($this->module->name . '_token');
        $this->cms = Configuration::get($this->module->name . '_cms');
        $this->image_category = Configuration::get($this->module->name . '_image_category');
        $this->image_carrier = Configuration::get($this->module->name . '_image_carrier');
        $this->image_product = Configuration::get($this->module->name . '_image_product');
        $this->image_manufacturer = Configuration::get($this->module->name . '_image_manufacturer');
        $this->image_supplier = Configuration::get($this->module->name . '_image_supplier');
        $this->image_employee = Configuration::get($this->module->name . '_image_employee');
        $this->table_prefix = Configuration::get($this->module->name . '_table_prefix');
        $this->version = Configuration::get($this->module->name . '_version');
        $this->charset = Configuration::get($this->module->name . '_charset');
        $this->blowfish_key = Configuration::get($this->module->name . '_blowfish_key');
        $this->cookie_key = Configuration::get($this->module->name . '_cookie_key');
        $this->forceManufacturerIds = Configuration::get($this->module->name . '_force_manufacturer_ids');
        $this->forceCategoryIds = Configuration::get($this->module->name . '_force_category_ids');
        $this->forceCarrierIds = Configuration::get($this->module->name . '_force_carrier_ids');
        $this->forceProductIds = Configuration::get($this->module->name . '_force_product_ids');
        $this->forceCatalogPriceRuleIds = Configuration::get($this->module->name . '_force_catalogPriceRule_ids');
        $this->forceCustomerIds = Configuration::get($this->module->name . '_force_customer_ids');
        $this->forceOrderIds = Configuration::get($this->module->name . '_force_order_ids');
        $this->forceCMSIds = Configuration::get($this->module->name . '_force_cms_ids');
        $this->ps_validation_errors = Configuration::get($this->module->name . '_ps_validation_errors');
        $this->truncate = Configuration::get($this->module->name . '_clear_data');
        $this->speed = Configuration::get($this->module->name . '_query_row_count');
        $this->recent_data = Configuration::get($this->module->name . '_migrate_recent_data');
    }

    private function requestToCartDetails()
    {
        // --- get default values from source cart

        $this->client->setPostData($this->query->getDefaultShopValues());
        $this->client->serializeOn();
        $this->client->query();
        $resultDefaultShopValues = $this->client->getContent();
        $this->query->setVersion($this->version);
        $this->client->setPostData($this->query->getMappingInfo($resultDefaultShopValues['default_lang'][0]['value']));
        $this->client->query();
        $mappingInformation = $this->client->getContent();
        $mapping_value = MigrationProSaveMapping::listMapping(true, true);
        $source_default_lang_id = $resultDefaultShopValues['default_lang'][0]['value'];
        $target_default_lang_id = Configuration::get('PS_LANG_DEFAULT');
        Configuration::updateValue('migrationpro_source_root_cat', $resultDefaultShopValues['root_home'][0]['root']);
        Configuration::updateValue('migrationpro_source_home_cat', $resultDefaultShopValues['root_home'][0]['home']);
        Configuration::updateValue('migrationpro_source_max_cat', $resultDefaultShopValues['get_max_cat'][0]['max_cat_id']);

        if (is_array($mappingInformation)) {
            if (Db::getInstance()->execute('TRUNCATE TABLE  `' . _DB_PREFIX_ . 'migrationpro_mapping`')) {
                foreach ($mappingInformation as $mappingType => $mappingObject) {
                    foreach ($mappingObject as $value) {
                        $mapping = new MigrationProMapping();
                        $mapping->type = $mappingType;
                        $mapping->source_id = $value['source_id'];
                        $mapping->source_name = $value['source_name'];
                        if ($mappingType == 'languages' && $value['source_id'] == $source_default_lang_id) {
                            $mapping->local_id = $target_default_lang_id;
                        } else {
                            if (!empty($mapping_value)) {
                                $mapping->local_id = $mapping_value[$mappingType][$value['source_id']];
                            }
                        }
                        if (!$mapping->save()) {
                            $this->errors[] = $this->module->l('Can\'t save to database mapping information. ');
                        }
                    }
                }
            } else {
                $this->errors[] = $this->module->l('Can\'t truncate mapping table');
            }
        }
        if (self::isEmpty($this->errors)) {
            return true;
        }

        return false;
    }

    protected function checkMappingIsEmpty($mapping)
    {
        if (self::isEmpty($mapping)) {
            return true;
        }

        return false;
    }

    protected function truncateTables($case)
    {
        $res = true;
        switch ($case) {
            case 'taxes':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_rules_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'tax_rules_group_shop`');
                break;
            case 'manufacturers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_shop`');
                foreach (scandir(_PS_MANU_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_MANU_IMG_DIR_ . $d);
                    }
                }
                break;
            case 'categories':
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category`
					WHERE id_category NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_lang`
					WHERE id_category NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_shop`  
					WHERE `id_category` NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
                $res &= Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_group`  
					WHERE `id_category` NOT IN (' . (int)Configuration::get('PS_HOME_CATEGORY') . ', ' . (int)Configuration::get('PS_ROOT_CATEGORY') . ')');
                $res &= Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'category` AUTO_INCREMENT = 3');
                foreach (scandir(_PS_CAT_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_CAT_IMG_DIR_ . $d);
                    }
                }
                break;
            case 'carriers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'carrier_zone`');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'module_carrier`');
                }
                foreach (scandir(_PS_SHIP_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SHIP_IMG_DIR_ . $d);
                    }
                }
                break;
            case 'warehouse':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_product_location`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_shop`');
                break;
            case 'products':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'category_product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_tag`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_priority`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_product`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'pack`');
                if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'favorite_product\' '))) { //check if table exist
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'favorite_product`');
                }
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attachment`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'accessory`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_country_tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_download`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_group_reduction_cache`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_sale`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_supplier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_product_location`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock_available`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock_mvt`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization_field`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization_field_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supply_order_detail`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_impact`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'pack`');
                Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }
//                break;
//            case 'combinations':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_impact`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_shop`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image`');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE`' . _DB_PREFIX_ . 'attribute`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE`' . _DB_PREFIX_ . 'attribute_group`');
                } else {
                    $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'attribute`');
                    $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'attribute_group`');
                }
                $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'stock_available` WHERE id_product_attribute != 0');
//            case 'suppliers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier_shop`');
                foreach (scandir(_PS_SUPP_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SUPP_IMG_DIR_ . $d);
                    }
                }
//                break;
                break;
            case 'catalog price rules':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_rule_condition_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_rule_condition`');
                break;
            case 'customers':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer_group`');
//                break;
//            case 'addresses':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'address`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_product`');

                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_combination`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_country`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_product_rule_group`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_product_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_product_rule_value`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_rule_shop`');
                break;
            case 'orders':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'orders`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_detail`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_detail_tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_history`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_invoice`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_cart_rule`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_payment`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_invoice_payment`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_invoice_tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_carrier`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_message`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_message_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_slip`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_slip_detail`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_slip_detail_tax`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_return`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'order_return_detail`');
                break;
            case 'cms':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_lang`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_shop`');
                $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cms_category` WHERE id_cms_category != 1');
                $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cms_category_lang` WHERE id_cms_category != 1');
                $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cms_category_shop` WHERE id_cms_category != 1');
                if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block_lang`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block_page`');
                    $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_block_shop`');
                }
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_role`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cms_role_lang`');
                break;
            case 'seo':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'meta`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'meta_lang`');
                break;
            case 'message_threads':
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer_message`');
                $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer_thread`');
                break;
        }
        Image::clearTmpDir();

        return $res;
    }

    // --- Static utility methods:

    public static function getCleanIDs($rows, $key)
    {
        $result = array();
        if (is_array($rows) && !self::isEmpty($rows)) {
            foreach ($rows as $row) {
                if (is_numeric($row[$key])) {
                    $result[] = $row[$key];
                } else {
                    $result[] = '\'' . $row[$key] . '\'';
                }
            }
            $result = array_unique($result);
            $result = implode(',', $result);

            return $result;
        } else {
            return 'null';
        }
    }

    public static function convertSpeedNameToNumeric($speed)
    {
        switch ((string)$speed) {
            case 'VerySlow':
                $row_count = 2;
                break;
            case 'Slow':
                $row_count = 5;
                break;
            case 'Normal':
                $row_count = 10;
                break;
            case 'Fast':
                $row_count = 25;
                break;
            case 'VeryFast':
                $row_count = 85;
                break;
            case 'MigrationProSpeed':
                $row_count = 100;
                break;
            default:
                $row_count = 10;
                break;
        }

        return $row_count;
    }

    public static function isEmpty($field)
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            return ($field === '' || $field === null || $field === array() || $field === 0 || $field === '0');
        } else {
            return empty($field);
        }
    }
}
