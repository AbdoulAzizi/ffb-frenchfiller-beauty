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
 *  @author    Edgar I.
 * @copyright Copyright (c) 2012-2016 MigrationPro MMC
 * @license   Commercial license
 * @package   MigrationPro: Prestashop To PrestaShop
 */

require_once 'EDClient.php';
require_once "loggers/Logger.php";
require_once "Validator.php";
class EDImport
{
    const UNFRIENDLY_ERROR = false;
    // --- Objects, Option & response vars:

    private $logger;
    private $validator;

    protected $obj;
    protected $module;
    protected $process;
    protected $client;
    protected $query;
    protected $url;
    protected $force_ids;
    protected $regenerate;
    protected $image_path;
    protected $image_supplier_path;
    protected $version;
    protected $shop_is_feature_active;
    protected $mapping;
    protected $ps_validation_errors = true;
    protected $migrate_recent_data = false;

    protected $error_msg;
    protected $warning_msg;
    protected $response;

    // --- Constructor / destructor:

    public function __construct(
        MigrationProProcess $process,
        $version,
        $url_cart,
        $force_ids,
        Module $module,
        EDClient $client = null,
        EDQuery $query = null
    ) {
        $this->regenerate = false; //@TODO dynamic from step two
        $this->process = $process;
        $this->version = $version;
        $this->url = $url_cart;
        $this->force_ids = $force_ids;
        $this->module = $module;
        $this->client = $client;
        $this->query = $query;
        $this->mapping = MigrationProMapping::listMapping(true, true);
        $this->shop_is_feature_active = Shop::isFeatureActive();
        $this->logger = new Logger();
        $this->validator = new Validator();
    }

    // --- Configuration methods:

    public function setImagePath($string)
    {
        $this->image_path = $string;
    }

    public function setImageSupplierPath($string)
    {
        $this->image_supplier_path = $string;
    }


    public function setRecentData($bool)
    {
        $this->migrate_recent_data = $bool;
    }

    public function setPsValidationErrors($bool)
    {
        $this->ps_validation_errors = $bool;
        $this->validator->allowSettingDefaultValue(!$bool);
    }

    public function preserveOn()
    {
        $this->force_ids = true;
    }

    public function preserveOff()
    {
        $this->force_ids = false;
    }

    // --- After object methods:

    public function getErrorMsg()
    {
        return $this->error_msg;
    }


    public function getWarningMsg()
    {
        return $this->warning_msg;
    }

    public function getResponse()
    {
        return $this->response;
    }

    // --- Import methods:

    /**
     * @param $taxRulesGroups
     * @param $taxRules
     * @param $taxLangCountryLangState
     */
    public function taxes($taxRulesGroups, $taxRules, $taxLangCountryLangState)
    {
        // import country
        foreach ($taxLangCountryLangState['country'] as $country) {
            if ($countryObject = $this->createObjectModel('Country', $country['id_country'])) {
                $countryObject->id_zone = $country['id_zone'];
                $countryObject->id_currency = self::getCurrencyID($country['id_currency']);
                $countryObject->call_prefix = $country['call_prefix'];
                $countryObject->iso_code = $country['iso_code'];
                $countryObject->active = $country['active'];
                $countryObject->contains_states = $country['contains_states'];
                $countryObject->need_identification_number = $country['need_identification_number'];
                $countryObject->need_zip_code = $country['need_zip_code'];
                $countryObject->zip_code_format = $country['zip_code_format'];
                $countryObject->display_tax_label = (isset($country['display_tax_label'])) ? (bool)$country['display_tax_label'] : true;
                //language fields
                foreach ($taxLangCountryLangState['country_lang'] as $lang) {
                    if ($lang['id_country'] == $country['id_country']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $countryObject->name[$lang['id_lang']] = $lang['name'];
                    }
                }

                // Add to _shop relations
                $countriesShopsRelations = $this->getChangedIdShop($taxLangCountryLangState['country_shop'], 'id_country');
                if (array_key_exists($country['id_country'], $countriesShopsRelations)) {
                    $countryObject->id_shop_list = array_values($countriesShopsRelations[$country['id_country']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($countryObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($countryObject->id && Country::existsInDatabase($countryObject->id, 'country')) {
                        try {
                            $res = $countryObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $countryObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $err_tmp), 'Country');
                    } else {
                        self::addLog('Country', $country['id_country'], $countryObject->id);
                    }
                } else {
//                    $error_tmp = $error_tmp[0];
                    $this->showMigrationMessageAndLog($error_tmp, 'Country');
                }
            }
        }



        // import state
        foreach ($taxLangCountryLangState['state'] as $state) {
            if ($stateObject = $this->createObjectModel('State', $state['id_state'])) {
                $stateObject->id_country = self::getLocalID('country', $state['id_country'], 'data');
                $stateObject->id_zone = $state['id_zone'];
                $stateObject->iso_code = $state['iso_code'];
                $stateObject->active = $state['active'];
                $stateObject->name = $state['name'];

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($stateObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($stateObject->id && State::existsInDatabase($stateObject->id, 'state')) {
                        try {
                            $res = $stateObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $stateObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('State (ID: %1$s) can not be saved. %2$s')), (isset($state['id_state']) && !self::isEmpty($state['id_state'])) ? Tools::safeOutput($state['id_state']) : 'No ID', $err_tmp), 'State');
                    } else {
                        self::addLog('State', $state['id_state'], $stateObject->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'State');
                }
            }
        }
        // import tax
        foreach ($taxLangCountryLangState['tax'] as $tax) {
            if ($taxObject = $this->createObjectModel('Tax', $tax['id_tax'])) {
                $taxObject->rate = $tax['rate'];
                $taxObject->active = $tax['active'];
                if ($this->version >= 1.5) {
                    $taxObject->deleted = $tax['deleted'];
                }
                foreach ($taxLangCountryLangState['tax_lang'] as $lang) {
                    if ($lang['id_tax'] == $tax['id_tax']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $taxObject->name[$lang['id_lang']] = $lang['name'];
                    }
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($taxObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($taxObject->id && Tax::existsInDatabase($taxObject->id, 'tax')) {
                        try {
                            $res = $taxObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $taxObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Tax (ID: %1$s) can not be saved. %2$s')), (isset($tax['id_tax']) && !self::isEmpty($tax['id_tax'])) ? Tools::safeOutput($tax['id_tax']) : 'No ID', $err_tmp), 'Tax');
                    } else {
                        self::addLog('Tax', $tax['id_tax'], $taxObject->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Tax');
                }
            }
        }
        // import tax rules group
        foreach ($taxRulesGroups['tax_rules_group'] as $taxRulesGroup) {
            if ($taxRulesGroupModel = $this->createObjectModel('TaxRulesGroup', $taxRulesGroup['id_tax_rules_group'])) {
                $taxRulesGroupModel->name = $taxRulesGroup['name'];
                if (self::isEmpty($taxRulesGroupModel->date_add) || $taxRulesGroupModel->date_add == '0000-00-00 00:00:00') {
                    $taxRulesGroupModel->date_add = date('Y-m-d H:i:s');
                }
                if (self::isEmpty($taxRulesGroupModel->date_upd) || $taxRulesGroupModel->date_upd == '0000-00-00 00:00:00') {
                    $taxRulesGroupModel->date_upd = date('Y-m-d H:i:s');
                }
                $taxRulesGroupModel->active = $taxRulesGroup['active'];

                // Add to _shop relations
                $taxRulesShopsRelations = $this->getChangedIdShop($taxRulesGroups['tax_rules_group_shop'], 'id_tax_rules_group');
                if (array_key_exists($taxRulesGroup['id_tax_rules_group'], $taxRulesShopsRelations)) {
                    $taxRulesGroupModel->id_shop_list = array_values($taxRulesShopsRelations[$taxRulesGroup['id_tax_rules_group']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($taxRulesGroupModel);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($taxRulesGroupModel->id && TaxRulesGroup::existsInDatabase($taxRulesGroupModel->id, 'tax_rules_group')) {
                        try {
                            $res = $taxRulesGroupModel->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $taxRulesGroupModel->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Tax Rules Group (ID: %1$s) can not be saved. %2$s')), (isset($taxRulesGroup['id_tax_rules_group']) && !self::isEmpty($taxRulesGroup['id_tax_rules_group'])) ? Tools::safeOutput($taxRulesGroup['id_tax_rules_group']) : 'No ID', $err_tmp), 'TaxRulesGroup');
                    } else {
                        // import tax rules for this group
                        foreach ($taxRules as $taxRule) {
                            if ($taxRuleModel = $this->createObjectModel('TaxRule', $taxRule['id_tax_rule'])) {
                                $taxRuleModel->id_tax_rules_group = $taxRule['id_tax_rules_group'];
                                $taxRuleModel->id_country = $taxRule['id_country'];
                                $taxRuleModel->id_state = $taxRule['id_state'];
                                $taxRuleModel->id_tax = $taxRule['id_tax'];
                                $taxRuleModel->zipcode_from = 0;
                                $taxRuleModel->zipcode_to = 0;
                                $taxRuleModel->behavior = 0;

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($taxRuleModel);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($taxRuleModel->id && TaxRule::existsInDatabase($taxRuleModel->id, 'tax_rule')) {
                                        try {
                                            $res = $taxRuleModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $taxRuleModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Tax Rule (ID: %1$s) can not be saved. %2$s')), (isset($taxRule['id_tax_rule']) && !self::isEmpty($taxRule['id_tax_rule'])) ? Tools::safeOutput($taxRule['id_tax_rule']) : 'No ID', $err_tmp), 'TaxRule');
                                    } else {
                                        self::addLog('TaxRule', $taxRule['id_tax_rule'], $taxRuleModel->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'TaxRule');
                                }
                            }
                        }
                        if (count($this->error_msg) == 0) {
                            self::addLog('TaxRulesGroup', $taxRulesGroup['id_tax_rules_group'], $taxRulesGroupModel->id);
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'TaxRulesGroup');
                }
            }
        }

        $this->updateProcess(count($taxRulesGroups['tax_rules_group']));
    }

    /**
     * @param $manufacturers
     * @param $manufacturersAdditionalSecond
     */
    public function manufacturers($manufacturers, $manufacturersAdditionalSecond)
    {
        foreach ($manufacturers as $manufacturer) {
            if ($manufacturerObj = $this->createObjectModel('Manufacturer', $manufacturer['id_manufacturer'])) {
                $manufacturerObj->name = $manufacturer['name'];
                $manufacturerObj->date_add = $manufacturer['date_add'];
                $manufacturerObj->date_upd = $manufacturer['date_upd'];
                $manufacturerObj->active = $manufacturer['active'];
                foreach ($manufacturersAdditionalSecond['manufactures_lang'] as $lang) {
                    if ($lang['id_manufacturer'] == $manufacturer['id_manufacturer']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $manufacturerObj->description[$lang['id_lang']] = $lang['description'];
                        $manufacturerObj->short_description[$lang['id_lang']] = $lang['short_description'];
                        $manufacturerObj->meta_title[$lang['id_lang']] = $lang['meta_title'];
                        $manufacturerObj->meta_description[$lang['id_lang']] = $lang['meta_description'];
                        $manufacturerObj->meta_keywords[$lang['id_lang']] = $lang['meta_keywords'];
                    }
                }

                // Add to _shop relations
                $manufacturersShopsRelations = $this->getChangedIdShop($manufacturersAdditionalSecond['manufactures_shop'], 'id_manufacturer');
                if (array_key_exists($manufacturer['id_manufacturer'], $manufacturersShopsRelations)) {
                    $manufacturerObj->id_shop_list = array_values($manufacturersShopsRelations[$manufacturer['id_manufacturer']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($manufacturerObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($manufacturerObj->id && $manufacturerObj->manufacturerExists($manufacturerObj->id)) {
                        try {
                            $res = $manufacturerObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $manufacturerObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Manufacturer (ID: %1$s) can not be saved. %2$s')), (isset($manufacturer['id_manufacturer']) && !self::isEmpty($manufacturer['id_manufacturer'])) ? Tools::safeOutput($manufacturer['id_manufacturer']) : 'No ID', $err_tmp), 'Manufacturer');
                    } else {
                        $url = $this->url . $this->image_path . $manufacturer['id_manufacturer'] . '.jpg';
                        if (self::imageExits($url) && !(EDImport::copyImg($manufacturerObj->id, null, $url, 'manufacturers', $this->regenerate))) {
                            $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'Manufacturer', true);
                        }

                        //@TODO Associate manufacturers to shop
                        self::addLog('Manufacturer', $manufacturer['id_manufacturer'], $manufacturerObj->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Manufacturer');
                }
            }
        }

        $this->updateProcess(count($manufacturers));
    }

    /**
     * @param $categories
     * @param $categoriesAdditionalSecond
     * @param bool $innerMethodCall
     */
    public function categories($categories, $categoriesAdditionalSecond, $innerMethodCall = false)
    {
        foreach ($categories as $category) {
            $categories_home_root = array(
                Configuration::get('migrationpro_source_root_cat'),
                Configuration::get('migrationpro_source_home_cat')
            );

            if (version_compare($this->version, '1.5', '<')) {
                if ($this->force_ids) {
                    if ($category['id_category'] == Configuration::get('PS_HOME_CATEGORY')) {
                        $cat_id = Configuration::get('migrationpro_source_max_cat') + 1;
                        $category['id_category'] = $cat_id;
                    }

                    if ($category['id_parent'] == Configuration::get('PS_HOME_CATEGORY')) {
                        $cat_id = Configuration::get('migrationpro_source_max_cat') + 1;
                        $category['id_parent'] = $cat_id;
                    }
                }
            }

            $lastMigratedCategoryId = Configuration::get('migrationpro_last_migrated_cat_id');
            if (!self::isEmpty($lastMigratedCategoryId)) {
                if ($category['id_parent'] == Configuration::get('migrationpro_last_migrated_cat_id') && $category['id_category'] == Configuration::get('migrationpro_last_migrated_parent_id')) {
                    $category['id_parent'] = Configuration::get('PS_HOME_CATEGORY');
                }
            }

            Configuration::updateValue('migrationpro_last_migrated_cat_id', $category['id_category']);
            Configuration::updateValue('migrationpro_last_migrated_parent_id', $category['id_parent']);

            if (isset($category['id_category']) && in_array((int)$category['id_category'], $categories_home_root)) {
                $this->showMigrationMessageAndLog(self::displayError($this->module->l('The category ID can not be the same as the Root category ID or the Home category ID.')), 'Category');
                continue;
            }

            if ($categoryObj = $this->createObjectModel('Category', $category['id_category'])) {
                $categoryObj->active = $category['active'];

                if (isset($category['id_parent']) && !in_array((int)$category['id_parent'], $categories_home_root) && (int)$category['id_parent'] != 0) {
                    if (!Category::categoryExists(self::getLocalId('category', (int)$category['id_parent'], 'data'))) {
                        // -- if parent category not exist create it
                        $this->client->serializeOff();
                        $this->client->setPostData($this->query->singleCategory((int)$category['id_parent']));
                        if ($this->client->query()) {
                            $parentCategory = $this->client->getContent();
                            $this->client->serializeOn();
                            $this->client->setPostData($this->query->categorySqlSecond(AdminMigrationProController::getCleanIDs($parentCategory, 'id_category')));
                            if ($this->client->query()) {
                                $parentCategoryLang = $this->client->getContent();
                                $import = new EDImport($this->process, $this->version, $this->url, $this->force_ids, $this->module, $this->client, $this->query);
                                $import->setImagePath($this->image_path);
                                $import->setPsValidationErrors($this->ps_validation_errors);
                                $import->categories($parentCategory, $parentCategoryLang, true);
                                $this->error_msg = $import->getErrorMsg();
                                $this->warning_msg = $import->getWarningMsg();
                                $this->response = $import->getResponse();
                            }
                        } else {
                            $this->showMigrationMessageAndLog(self::displayError('Can\'t execute query to source Shop. ' . $this->client->getMessage()), 'Category');
                        }
                    }
                    $categoryObj->id_parent = self::getLocalId('category', (int)$category['id_parent'], 'data');
                } else {
                    $categoryObj->id_parent = Configuration::get('PS_HOME_CATEGORY');
                }

                $categoryObj->id_parent = $categoryObj->id_parent ? $categoryObj->id_parent : Configuration::get('PS_HOME_CATEGORY');

                $categoryObj->position = $category['position'];
                $categoryObj->date_add = $category['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $category['date_add'];
                $categoryObj->date_upd = $category['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $category['date_upd'];

                foreach ($categoriesAdditionalSecond['category_lang'] as $lang) {
                    if (version_compare($this->version, '1.5', '<')) {
                        if ($this->force_ids) {
                            if ($lang['id_category'] == Configuration::get('PS_HOME_CATEGORY')) {
                                $cat_id = Configuration::get('migrationpro_source_max_cat') + 1;
                                $lang['id_category'] = $cat_id;
                            }
                        }
                    }

                    if ($lang['id_category'] == $category['id_category']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $categoryObj->name[$lang['id_lang']] = $lang['name'];
                        $categoryObj->link_rewrite[$lang['id_lang']] = $lang['link_rewrite'];

                        if (isset($categoryObj->link_rewrite[$lang['id_lang']]) && !self::isEmpty($categoryObj->link_rewrite[$lang['id_lang']])) {
                            $valid_link = Validate::isLinkRewrite($categoryObj->link_rewrite[$lang['id_lang']]);
                        } else {
                            $valid_link = false;
                        }
                        if (!$valid_link) {
                            $categoryObj->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($categoryObj->name[$lang['id_lang']]);

                            if ($categoryObj->link_rewrite[$lang['id_lang']] == '') {
                                $categoryObj->link_rewrite[$lang['id_lang']] = 'friendly-url-autogeneration-failed';
                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('URL rewriting failed to auto-generate a friendly URL for: %s')), $categoryObj->name[$lang['id_lang']]), 'Category');
                            }

                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('The link for %1$s (ID: %2$s) was re-written as %3$s.')), $lang['link_rewrite'], (isset($category['id_category']) && !self::isEmpty($category['id_category'])) ? $category['id_category'] : 'null', $categoryObj->link_rewrite[$lang['id_lang']]), 'Category', true);
                        }

                        $categoryObj->description[$lang['id_lang']] = $lang['description'];
                        $categoryObj->meta_title[$lang['id_lang']] = $lang['meta_title'];
                        $categoryObj->meta_description[$lang['id_lang']] = $lang['meta_description'];
                        $categoryObj->meta_keywords[$lang['id_lang']] = $lang['meta_keywords'];
                    }
                }

                // Add to _shop relations
                $categoriesShopsRelations = $this->getChangedIdShop($categoriesAdditionalSecond['category_shop'], 'id_category');
                if (array_key_exists($category['id_category'], $categoriesShopsRelations)) {
                    $categoryObj->id_shop_list = array_values($categoriesShopsRelations[$category['id_category']]);
                }

                //@TODO get shop id from step-2
//                if (!$this->shop_is_feature_active) {
//                    $categoryObj->id_shop_default = 1;
//                } else {
//                    $categoryObj->id_shop_default = $category['id_category'];
//                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($categoryObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($categoryObj->id && $categoryObj->id == $categoryObj->id_parent) {
                        $this->showMigrationMessageAndLog(self::displayError($this->module->l('A category can not be its own parent category.')), 'Category');
                        continue;
                    }

                    if ($categoryObj->id == Configuration::get('PS_ROOT_CATEGORY')) {
                        $this->showMigrationMessageAndLog(self::displayError($this->module->l('The root category can not be modified.')), 'Category');
                        continue;
                    }

                    /* No automatic nTree regeneration for import */
                    $categoryObj->doNotRegenerateNTree = true;
                    // If id category AND id category already in base, trying to update
                    if ($categoryObj->id && $categoryObj->categoryExists($categoryObj->id) && !in_array($categoryObj->id, $categories_home_root)) {
                        try {
                            $res = $categoryObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    // If no id_category or update failed
                    if (!$res) {
                        try {
                            $res = $categoryObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp,'Category');
                }

                // If both failed, mysql error
                if (!$res) {
                    $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Category (ID: %1$s) can not be saved. %2$s')), (isset($category['id_category']) && !self::isEmpty($category['id_category'])) ? Tools::safeOutput($category['id_category']) : 'No ID', $err_tmp), 'Category');
                } else {
                    $url = $this->url . $this->image_path . $category['id_category'] . '.jpg';
                    if (self::imageExits($url) && !(EDImport::copyImg($categoryObj->id, null, $url, 'categories', $this->regenerate))) {
                        $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'Category', true);
                    }

                    //import Category_Group
                    $sql_values = array();
                    foreach ($categoriesAdditionalSecond['category_group'] as $group) {
                        if ($group['id_category'] == $category['id_category']) {
                            if (self::getCustomerGroupID($group['id_group']) != "0") {
                                $sql_values[] = '(' . $categoryObj->id . ', ' . self::getCustomerGroupID($group['id_group']) . ')';
                            }
                        }
                    }
                    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'category_group` WHERE id_category = ' . $categoryObj->id);
                    if (!self::isEmpty($sql_values)) {
                        $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'category_group` (`id_category`, `id_group`) VALUES ' . implode(',', $sql_values));
                        if (!$result) {
                            $this->showMigrationMessageAndLog(self::displayError($this->module->l('Can\'t add category_group. ' . Db::getInstance()->getMsgError())), 'Category');
                        }
                    }

                    //@TODO Associate category to shop

                    self::addLog('Category', $category['id_category'], $categoryObj->id);

                    //update multistore language fields
                    if (!version_compare($this->version, '1.5', '<')) {
                        if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                            foreach ($categoriesAdditionalSecond['category_lang'] as $lang) {
                                if ($lang['id_category'] == $category['id_category']) {
                                    $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                    $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                    $lang['id_category'] = $categoryObj->id;
                                    self::updateMultiStoreLang('category', $lang);
                                }
                            }
                        }
                    }
                }

                if (self::isEmpty($categoriesShopsRelations[$category['id_category']])) {
                    continue;
                }
                if ($category['id_category'] != Configuration::get('PS_HOME_CATEGORY') && $category['id_category'] != Configuration::get('PS_ROOT_CATEGORY')) {
                    Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'category_shop WHERE id_category = ' . (int)$category['id_category'] . ' AND id_shop NOT IN (' . implode(',', array_values($categoriesShopsRelations[$category['id_category']])) . ')');
                }
            }
        }

        if (!$innerMethodCall) {
            $this->updateProcess(count($categories));
        }
        Category::regenerateEntireNtree();
    }

    /**
     * @param $carriers
     * @param $carriersAdditionalSecond
     */
    public function carriers($carriers, $carriersAdditionalSecond)
    {
        foreach ($carriers as $carrier) {
            if ($carrierObj = $this->createObjectModel('Carrier', $carrier['id_carrier'])) {
                $carrierObj->id_tax_rules_group = self::getLocalID('taxRulesGroup', $carrier['id_tax_rules_group'], 'data');
                $carrierObj->name = $carrier['name'];
                $carrierObj->url = $carrier['url'];
                $carrierObj->active = $carrier['active'];
                $carrierObj->deleted = $carrier['deleted'];
                $carrierObj->shipping_handling = $carrier['shipping_handling'];
                $carrierObj->range_behavior = $carrier['range_behavior'];
                $carrierObj->is_module = $carrier['is_module'];
                $carrierObj->is_free = $carrier['is_free'];
                $carrierObj->shipping_external = $carrier['shipping_external'];
                $carrierObj->need_range = $carrier['need_range'];
                $carrierObj->external_module_name = $carrier['external_module_name'];
                $carrierObj->shipping_method = $carrier['shipping_method'];
                $carrierObj->position = $carrier['position'];
                $carrierObj->max_width = $carrier['max_width'];
                $carrierObj->max_height = $carrier['max_height'];
                $carrierObj->max_depth = $carrier['max_depth'];
                $carrierObj->max_weight = $carrier['max_weight'];
                $carrierObj->grade = $carrier['grade'];
                foreach ($carriersAdditionalSecond['carrier_lang'] as $lang) {
                    if ($lang['id_carrier'] == $carrier['id_carrier']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $carrierObj->delay[$lang['id_lang']] = $lang['delay'];
                        if (self::isEmpty($carrierObj->delay[$lang['id_lang']])) {
                            $carrierObj->delay[$lang['id_lang']] = 'Empty';
                        }
                    }
                }

                // Add to _shop relations
                $carriersShopsRelations = $this->getChangedIdShop($carriersAdditionalSecond['carrier_shop'], 'id_carrier');
                if (array_key_exists($carrier['id_carrier'], $carriersShopsRelations)) {
                    $carrierObj->id_shop_list = array_values($carriersShopsRelations[$carrier['id_carrier']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($carrierObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($carrierObj->id && Carrier::existsInDatabase($carrierObj->id, 'carrier')) {
                        try {
                            $res = $carrierObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $carrierObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Carrier (ID: %1$s) can not be saved. %2$s')), (isset($carrier['id_carrier']) && !self::isEmpty($carrier['id_carrier'])) ? Tools::safeOutput($carrier['id_carrier']) : 'No ID', $err_tmp), 'Carrier');
                    } else {
                        // Import Carrier Group
                        $sql_values = array();
                        foreach ($carriersAdditionalSecond['carrier_group'] as $carrierGroup) {
                            if ($carrierGroup['id_carrier'] == $carrier['id_carrier']) {
                                $sql_values[] = '(' . (int)$carrierObj->id . ', ' . self::getCustomerGroupID($carrierGroup['id_group']) . ')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'carrier_group` (`id_carrier`, `id_group`) VALUES ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add carrier_group. ' . Db::getInstance()->getMsgError()), 'Carrier');
                            }
                        }

                        // Add zones
                        foreach ($carriersAdditionalSecond['all_zones'] as $all_zone) {
                            if ($zoneObject = $this->createObjectModel('Zone', $all_zone['id_zone'])) {
                                $zoneObject->active = $all_zone['active'];
                                $zoneObject->name = $all_zone['name'];

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($zoneObject);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($zoneObject->id && Zone::existsInDatabase($zoneObject->id, 'zone')) {
                                        try {
                                            $res = $zoneObject->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $zoneObject->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError('Zone (ID: %1$s) can not be saved. %2$s'), (isset($all_zone['id_zone']) && !self::isEmpty($all_zone['id_zone'])) ? Tools::safeOutput($all_zone['id_zone']) : 'No ID', $err_tmp), 'Zone');
                                    } else {
                                        MigrationProData::import('Zone', $all_zone['id_zone'], $zoneObject->id);
                                        MigrationProMigratedData::import('Zone', $all_zone['id_zone'], $zoneObject->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'Zone');
                                }
                            }
                        }

                        // Range_price
                        foreach ($carriersAdditionalSecond['range_price'] as $rangePrice) {
                            if ($rangePriceObject = $this->createObjectModel('RangePrice', $rangePrice['id_range_price'])) {
                                $rangePriceObject->id_carrier = self::getLocalID('carrier', $rangePrice['id_carrier'], 'data');
                                $rangePriceObject->delimiter1 = $rangePrice['delimiter1'];
                                $rangePriceObject->delimiter2 = $rangePrice['delimiter2'];

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($rangePriceObject);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($rangePriceObject->id && RangePrice::existsInDatabase($rangePriceObject->id, 'range_price')) {
                                        try {
                                            $res = $rangePriceObject->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $rangePriceObject->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError('Range price (ID: %1$s) can not be saved. %2$s'), (isset($rangePrice['id_range_price']) && !self::isEmpty($rangePrice['id_range_price'])) ? Tools::safeOutput($rangePrice['id_range_price']) : 'No ID', $err_tmp), 'RangePrice');
                                    } else {
                                        MigrationProData::import('RangePrice', $rangePrice['id_range_price'], $rangePriceObject->id);
                                        MigrationProMigratedData::import('RangePrice', $rangePrice['id_range_price'], $rangePriceObject->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'RangePrice');
                                }
                            }
                        }

//                        // Range_weight
                        foreach ($carriersAdditionalSecond['range_weight'] as $rangeWeight) {
                            if ($rangeWeightObject = $this->createObjectModel('RangeWeight', $rangeWeight['id_range_weight'])) {
                                $rangeWeightObject->id_carrier = self::getLocalID('carrier', $rangeWeight['id_carrier'], 'data');
                                $rangeWeightObject->delimiter1 = $rangeWeight['delimiter1'];
                                $rangeWeightObject->delimiter2 = $rangeWeight['delimiter2'];
                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($rangeWeightObject);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($rangeWeightObject->id && RangeWeight::existsInDatabase($rangeWeightObject->id, 'range_weight')) {
                                        try {
                                            $res = $rangeWeightObject->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $rangeWeightObject->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError('Range weight (ID: %1$s) can not be saved. %2$s'), (isset($rangeWeight['id_range_weight']) && !self::isEmpty($rangeWeight['id_range_weight'])) ? Tools::safeOutput($rangeWeight['id_range_weight']) : 'No ID', $err_tmp), 'RangeWeight');
                                    } else {
                                        MigrationProData::import('RangeWeight', $rangeWeight['id_range_weight'], $rangeWeightObject->id);
                                        MigrationProMigratedData::import('RangeWeight', $rangeWeight['id_range_weight'], $rangeWeightObject->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'RangeWeight');
                                }
                            }
                        }

//                        // Delivery
                        foreach ($carriersAdditionalSecond['carrier_delivery'] as $delivery) {
                            if ($deliveryObject = $this->createObjectModel('Delivery', $delivery['id_delivery'])) {
                                $deliveryObject->id_carrier = self::getLocalID('carrier', $delivery['id_carrier'], 'data');
                                $deliveryObject->id_shop = 0;
                                $deliveryObject->id_shop_group = 0;
                                $deliveryObject->id_range_price = self::isEmpty($delivery['id_range_price']) ? 0 : $delivery['id_range_price'];
                                $deliveryObject->id_range_weight = self::isEmpty($delivery['id_range_weight']) ? 0 : $delivery['id_range_weight'];
                                $deliveryObject->id_zone = $delivery['id_zone'];
                                $deliveryObject->price = $delivery['price'];

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($deliveryObject);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($deliveryObject->id && Delivery::existsInDatabase($deliveryObject->id, 'delivery')) {
                                        try {
                                            $res = $deliveryObject->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $deliveryObject->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError('Delivery (ID: %1$s) can not be saved. %2$s'), (isset($delivery['id_delivery']) && !self::isEmpty($delivery['id_delivery'])) ? Tools::safeOutput($delivery['id_delivery']) : 'No ID', $err_tmp), 'Delivery');
                                    } else {
                                        if ($deliveryObject->id_range_price == 0) {
                                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'delivery` set id_shop_group = null, id_shop = null, id_range_price = null WHERE id_range_price = 0');
                                        } else {
                                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'delivery` set id_shop_group = null, id_shop = null, id_range_weight = null WHERE id_range_weight = 0');
                                        }
                                        MigrationProData::import('Delivery', $delivery['id_delivery'], $deliveryObject->id);
                                        MigrationProMigratedData::import('Delivery', $delivery['id_delivery'], $deliveryObject->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'Delivery');
                                }
                            }
                        }

                        // Import Carrier Tax Rules Group Shop
                        $sql_values = array();
                        foreach ($carriersAdditionalSecond['carrier_tax_rules_group_shop'] as $carrierTaxRulesGroupShop) {
                            if ($carrierTaxRulesGroupShop['id_carrier'] == $carrier['id_carrier']) {
                                $sql_values[] = '(' . (int)$carrierObj->id . ', ' . self::getLocalID('taxRulesGroup', ($carrierTaxRulesGroupShop['id_tax_rules_group']), 'data') . ', ' . self::getShopID($carrierTaxRulesGroupShop['id_shop']) . ')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop` (`id_carrier`, `id_tax_rules_group`, `id_shop`) VALUES
                                 ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add carrier_tax_rules_group_shop. ' . Db::getInstance()->getMsgError()), 'Carrier');
                            }
                        }

                        // Import Carrier Zone
                        $sql_values = array();
                        foreach ($carriersAdditionalSecond['carrier_zone'] as $carrierZone) {
                            if ($carrierZone['id_carrier'] == $carrier['id_carrier']) {
                                $sql_values[] = '(' . (int)$carrierObj->id . ', ' . (int)$carrierZone['id_zone'] . ')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'carrier_zone` (`id_carrier`, `id_zone`) VALUES ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add carrier_zone. ' . Db::getInstance()->getMsgError()), 'Carrier');
                            }
                        }

                        $url = $this->url . $this->image_path . $carrier['id_carrier'] . '.jpg';
                        if (self::imageExits($url) && !(EDImport::copyImg($carrierObj->id, null, $url, 'carriers', $this->regenerate))) {
                            $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'Carrier', true);
                        }

                        self::addLog('Carrier', $carrier['id_carrier'], $carrierObj->id);

                        //update multistore language fields
                        if (!version_compare($this->version, '1.5', '<')) {
                            if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                                foreach ($carriersAdditionalSecond['carrier_lang'] as $lang) {
                                    if ($lang['id_carrier'] == $carrier['id_carrier']) {
                                        $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                        $lang['id_carrier'] = $carrierObj->id;
                                        self::updateMultiStoreLang('carrier', $lang);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Carrier');
                }
            }
        }

        $this->updateProcess(count($carriers));
    }


//    public function warehouses($warehouses, $warehousesAdditionalSecond, $countryState)
//    {
//        // Import Country
//        foreach ($countryState['countries'] as $country) {
//            if ($countryModel = $this->createObjectModel('Country', $country['id_country'])) {
//                $countryModel->id_zone = $country['id_zone'];
//                $countryModel->id_currency = self::getCurrencyID($country['id_currency']);
//                $countryModel->call_prefix = $country['call_prefix'];
//                $countryModel->iso_code = $country['iso_code'];
//                $countryModel->active = $country['active'];
//                $countryModel->contains_states = $country['contains_states'];
//                $countryModel->need_identification_number = $country['need_identification_number'];
//                $countryModel->need_zip_code = $country['need_zip_code'];
//                $countryModel->zip_code_format = $country['zip_code_format'];
//                $countryModel->display_tax_label = (isset($country['display_tax_label'])) ? (bool)$country['display_tax_label'] : true;
//
//                // Add to _shop relations
//                $countriesShopsRelations = $this->getChangedIdShop($countryState['country_shop'], 'id_country');
//                if (array_key_exists($country['id_country'], $countriesShopsRelations)) {
//                    $countryModel->id_shop_list = array_values($countriesShopsRelations[$country['id_country']]);
//                }
//
//
//                // Language fields
//                foreach ($countryState['country_lang'] as $lang) {
//                    if ($lang['id_country'] == $country['id_country']) {
//                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
//                        $countryModel->name[$lang['id_lang']] = $lang['name'];
//                    }
//                }
//
//                $res = false;
//                $err_tmp = '';
//                if (($field_error = $countryModel->validateFields(self::UNFRIENDLY_ERROR, true)) === true && ($lang_field_error = $countryModel->validateFieldsLang(self::UNFRIENDLY_ERROR, true)) === true) {
//                    if ($countryModel->id && Country::existsInDatabase($countryModel->id, 'country')) {
//                        try {
//                            $res = $countryModel->update();
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//                    if (!$res) {
//                        try {
//                            $res = $countryModel->add(false);
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//
//                    if (!$res) {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $err_tmp);
//                    } else {
//                        self::addLog('Country', $country['id_country'], $countryModel->id);
//                    }
//                } else {
//                    $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
//                    if ($error_tmp != '') {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $error_tmp);
//                    }
//                }
//            }
//        }
//        // Import State
//        foreach ($countryState['states'] as $state) {
////                            if ($state['id_state'] == $address['id_state']) {
//            if ($stateModel = $this->createObjectModel('State', $state['id_state'])) {
//                $stateModel->id_country = self::getLocalId('country', $state['id_country'], 'data');
//                $stateModel->id_zone = $state['id_zone'];
//                $stateModel->iso_code = $state['iso_code'];
//                $stateModel->active = $state['active'];
//                $stateModel->name = $state['name'];
//
//
//                $res = false;
//                $err_tmp = '';
//                if (($field_error = $stateModel->validateFields(self::UNFRIENDLY_ERROR, true)) === true && ($lang_field_error = $stateModel->validateFieldsLang(self::UNFRIENDLY_ERROR, true)) === true) {
//                    if ($stateModel->id && State::existsInDatabase($stateModel->id, 'state')) {
//                        try {
//                            $res = $stateModel->update();
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//                    if (!$res) {
//                        try {
//                            $res = $stateModel->add(false);
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//
//                    if (!$res) {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('State (ID: %1$s) can not be saved. %2$s')), (isset($state['id_state']) && !self::isEmpty($state['id_state'])) ? Tools::safeOutput($state['id_state']) : 'No ID', $err_tmp);
//                    } else {
//                        self::addLog('State', $state['id_state'], $stateModel->id);
//                    }
//                } else {
//                    $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
//                    if ($error_tmp != '') {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('State (ID: %1$s) can not be saved. %2$s')), (isset($state['id_state']) && !self::isEmpty($state['id_state'])) ? Tools::safeOutput($state['id_state']) : 'No ID', $error_tmp);
//                    }
//                }
////                                }
//            }
//        }
//        // Import Address
//        foreach ($warehousesAdditionalSecond['address'] as $address) {
//            if ($addressObject = $this->createObjectModel('Address', $address['id_address'])) {
//                $addressObject->id_customer = $address['id_customer'];
//                $addressObject->id_manufacturer = self::getLocalId('manufacturer', $address['id_manufacturer'], 'data');
//                $addressObject->id_supplier = self::getLocalId('supplier', $address['id_supplier'], 'data');
//                $addressObject->id_country = self::getLocalId('country', $address['id_country'], 'data');
//                $addressObject->id_state = self::getLocalId('state', $address['id_state'], 'data');
//                $addressObject->alias = $address['alias'];
//                $addressObject->company = $address['company'];
//                $addressObject->lastname = $address['lastname'];
//                $addressObject->firstname = $address['firstname'];
//                $addressObject->vat_number = $address['vat_number'];
//                $addressObject->address1 = $address['address1'];
//                $addressObject->address2 = $address['address2'];
//                $addressObject->postcode = $address['postcode'];
//                $addressObject->city = $address['city'];
//                $addressObject->other = $address['other'];
//                $addressObject->phone = $address['phone'];
//                $addressObject->phone_mobile = $address['phone_mobile'];
//                $addressObject->dni = $address['dni'];
//                $addressObject->deleted = $address['deleted'];
//                $addressObject->date_add = $address['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $address['date_add'];
//                $addressObject->date_upd = $address['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $address['date_upd'];
////                                    if ($this->version >= 1.5) {
//                $addressObject->id_warehouse = (isset($address['id_warehouse']) && !self::isEmpty($address['id_warehouse'])) ? $address['id_warehouse'] : null;
////                                    }
//
//                $res = false;
//                $err_tmp = '';
//                if (($field_error = $addressObject->validateFields(self::UNFRIENDLY_ERROR, true)) === true && ($lang_field_error = $addressObject->validateFieldsLang(self::UNFRIENDLY_ERROR, true)) === true) {
//                    if ($addressObject->id && Address::existsInDatabase($addressObject->id, 'address')) {
//                        try {
//                            $res = $addressObject->update();
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//                    if (!$res) {
//                        try {
//                            $res = $addressObject->add(false);
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//
//                    if (!$res) {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Address (ID: %1$s) can not be saved. %2$s')), (isset($address['id_address']) && !self::isEmpty($address['id_address'])) ? Tools::safeOutput($address['id_address']) : 'No ID', $err_tmp);
//                    } else {
//                        self::addLog('Address', $address['id_address'], $addressObject->id);
//                    }
//                } else {
//                    $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
//                    if ($error_tmp != '') {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Address (ID: %1$s) can not be saved. %2$s')), (isset($address['id_address']) && !self::isEmpty($address['id_address'])) ? Tools::safeOutput($address['id_address']) : 'No ID', $error_tmp);
//                    }
//                }
//            }
//        }
//
//
//        foreach ($warehouses as $warehouse) {
//            if ($warehouseObj = $this->createObjectModel('Warehouse', $warehouse['id_warehouse'])) {
//                $warehouseObj->id_address = $warehouse['id_address'];
//                $warehouseObj->reference = $warehouse['reference'];
//                $warehouseObj->name = $warehouse['name'];
//                $warehouseObj->id_employee = $warehouse['id_employee'];
//                $warehouseObj->id_currency = $warehouse['id_currency'];
//                $warehouseObj->deleted = $warehouse['deleted'];
//                $warehouseObj->management_type = $warehouse['management_type'];
//
//
//
//                // Add to _shop relations
//                $warehouseShopsRelations = $this->getChangedIdShop($warehousesAdditionalSecond['warehouse_shop'], 'id_warehouse');
//                if (array_key_exists($warehouse['id_warehouse'], $warehouseShopsRelations)) {
//                    $warehouseObj->id_shop_list = array_values($warehouseShopsRelations[$warehouse['id_warehouse']]);
//                }
//
//                $res = false;
//                $err_tmp = '';
//                if (($field_error = $warehouseObj->validateFields(self::UNFRIENDLY_ERROR, true)) === true) {
//                    if ($warehouseObj->id && Warehouse::existsInDatabase($warehouseObj->id, 'warehouse')) {
//                        try {
//                            $res = $warehouseObj->update();
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//
//                    if (!$res) {
//                        try {
//                            $res = $warehouseObj->add(false);
//                        } catch (PrestaShopException $e) {
//                            $err_tmp = $e->getMessage();
//                        }
//                    }
//                    if (!$res) {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Warehouse (ID: %1$s) can not be saved. %2$s')), (isset($warehouse['id_warehouse']) && !self::isEmpty($warehouse['id_warehouse'])) ? Tools::safeOutput($warehouse['id_warehouse']) : 'No ID', $err_tmp);
//                    } else {
//                        // Import warehouse_carrier
//                        $sql_values = array();
//                        foreach ($warehousesAdditionalSecond['warehouse_carrier'] as $warehouseCarrier) {
//                            if ($warehouseCarrier['id_warehouse'] == $warehouse['id_warehouse']) {
//                                $sql_values[] = '(' . MigrationProData::getLocalID('Carrier', $warehouseCarrier['id_carrier']) . ', ' . (int)$warehouseObj->id . ')';
//                            }
//                        }
//                        if (!self::isEmpty($sql_values)) {
//                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'warehouse_carrier` (`id_carrier`, `id_warehouse`) VALUES ' . implode(',', $sql_values));
//                            if (!$result) {
//                                if (!$this->ps_validation_errors) {
//                                    continue;
//                                }
//
//                                $this->error_msg[] = self::displayError('Can\'t add warehouse_carrier. ' . Db::getInstance()->getMsgError());
//                                $this->error_msg[] = self::displayError('Can\'t add warehouse_carrier. ' . Db::getInstance()->getMsgError());
//                            }
//                        }
//
//                        self::addLog('Warehouse', $warehouse['id_warehouse'], $warehouseObj->id);
//                    }
//                } else {
//                    $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
//                    if ($error_tmp != '') {
//                        if (!$this->ps_validation_errors) {
//                            continue;
//                        }
//
//                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Warehouse (ID: %1$s) can not be saved. %2$s')), (isset($warehouse['id_warehouse']) && !self::isEmpty($warehouse['id_warehouse'])) ? Tools::safeOutput($warehouse['id_warehouse']) : 'No ID', $error_tmp);
//                    }
//                }
//            }
//        }
//
//        $this->updateProcess(count($warehouses));
//    }

    /**
     * @param $products
     * @param $product2AdditionalSecond
     * @param $productAdditionalThird
     * @param $productAdditionalFourth
     */

    public function products($products, $productAdditionalSecond, $productAdditionalThird, $productAdditionalFourth, $innerMethodCall = false)
    {
        Module::setBatchMode(true);

        //@TODO create import function for each data type
        // import supplier
        foreach ($productAdditionalThird['supplier'] as $supplier) {
            if ($supplierObj = $this->createObjectModel('Supplier', $supplier['id_supplier'])) {
                $supplierObj->name = $supplier['name'];
                if (!Validate::isCatalogName($supplierObj->name)) {
                    $supplierObj->name = 'Empty supplier name';
                    $this->showMigrationMessageAndLog('Name of supplier with ID ' . $supplier['id_supplier'] . ' is empty. For that reason, the module set default name to this supplier', 'Supplier', true);
                }
                $supplierObj->active = $supplier['active'];
                $supplierObj->date_add = $supplier['date_add'];
                $supplierObj->date_upd = $supplier['date_upd'];
                //language fields
                foreach ($productAdditionalThird['supplier_lang'] as $lang) {
                    if ($lang['id_supplier'] == $supplier['id_supplier']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $supplierObj->description[$lang['id_lang']] = $lang['description'];
                        if (!ValidateCore::isCleanHtml($supplierObj->description[$lang['id_lang']])) {
                            $supplierObj->description[$lang['id_lang']] = '';
                        }
                        $supplierObj->meta_title[$lang['id_lang']] = $lang['meta_title'];
                        $supplierObj->meta_description[$lang['id_lang']] = $lang['meta_description'];
                        $supplierObj->meta_keywords[$lang['id_lang']] = $lang['meta_keywords'];
                    }
                }

                // Add to _shop relations
                $suppliersShopsRelations = $this->getChangedIdShop($productAdditionalThird['supplier_shop'], 'id_supplier');
                if (array_key_exists($supplier['id_supplier'], $suppliersShopsRelations)) {
                    $supplierObj->id_shop_list = array_values($suppliersShopsRelations[$supplier['id_supplier']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($supplierObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($supplierObj->id && Supplier::existsInDatabase($supplierObj->id, 'supplier')) {
                        try {
                            $res = $supplierObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $supplierObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Supplier (ID: %1$s) can not be saved. %2$s')), (isset($supplier['id_supplier']) && !self::isEmpty($supplier['id_supplier'])) ? Tools::safeOutput($supplier['id_supplier']) : 'No ID', $err_tmp), 'Supplier');
                    } else {
                        $url = $this->url . $this->image_supplier_path . $supplier['id_supplier'] . '.jpg';
                        if (self::imageExits($url) && !(EDImport::copyImg($supplierObj->id, null, $url, 'suppliers', $this->regenerate))) {
                            $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'Supplier', true);
                        }

                        self::addLog('Supplier', $supplier['id_supplier'], $supplierObj->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Supplier');
                }
            }
        }
        // import attribute group
        foreach ($productAdditionalFourth['attribute_group'] as $attributeGroup) {
            if ($attributeGroupObj = $this->createObjectModel('AttributeGroup', $attributeGroup['id_attribute_group'])) {
                $attributeGroupObj->is_color_group = $attributeGroup['is_color_group'];
                $attributeGroupObj->group_type = ($attributeGroup['is_color_group']) ? 'color' : 'select';
                foreach ($productAdditionalFourth['attribute_group_lang'] as $lang) {
                    if ($attributeGroup['id_attribute_group'] == $lang['id_attribute_group']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $attributeGroupObj->name[$lang['id_lang']] = $lang['name'];
                        $attributeGroupObj->public_name[$lang['id_lang']] = $lang['public_name'];
                    }
                }

                // Add to _shop relations
                $attributeGroupsShopsRelations = $this->getChangedIdShop($productAdditionalFourth['attribute_group_shop'], 'id_attribute_group');
                if (array_key_exists($attributeGroup['id_attribute_group'], $attributeGroupsShopsRelations)) {
                    $attributeGroupObj->id_shop_list = array_values($attributeGroupsShopsRelations[$attributeGroup['id_attribute_group']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($attributeGroupObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($attributeGroupObj->id && AttributeGroup::existsInDatabase($attributeGroupObj->id, 'attribute_group')) {
                        try {
                            $res = $attributeGroupObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $attributeGroupObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('AttributeGroup (ID: %1$s) can not be saved. %2$s')), (isset($attributeGroup['id_attribute_group']) && !self::isEmpty($attributeGroup['id_attribute_group'])) ? Tools::safeOutput($attributeGroup['id_attribute_group']) : 'No ID', $err_tmp), 'AttributeGroup');
                    } else {
                        self::addLog('AttributeGroup', $attributeGroup['id_attribute_group'], $attributeGroupObj->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'AttributeGroup');
                }
            }
        }
        // import attribute
        foreach ($productAdditionalFourth['attribute'] as $attribute) {
            if ($attributeObj = $this->createObjectModel('Attribute', $attribute['id_attribute'])) {
                $attributeObj->id_attribute_group = self::getLocalID('attributegroup', (int)$attribute['id_attribute_group'], 'data');
                $attributeObj->color = $attribute['color'];
                foreach ($productAdditionalFourth['attribute_lang'] as $lang) {
                    if ($attribute['id_attribute'] == $lang['id_attribute']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $attributeObj->name[$lang['id_lang']] = $lang['name'];
                    }
                }

                // Add to _shop relations
                $attributesShopsRelations = $this->getChangedIdShop($productAdditionalFourth['attribute_shop'], 'id_attribute');
                if (array_key_exists($attribute['id_attribute'], $attributesShopsRelations)) {
                    $attributeObj->id_shop_list = array_values($attributesShopsRelations[$attribute['id_attribute']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($attributeObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($attributeObj->id && Attribute::existsInDatabase($attributeObj->id, 'attribute')) {
                        try {
                            $res = $attributeObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $attributeObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Attribute (ID: %1$s) can not be saved. %2$s')), (isset($attribute['id_attribute']) && !self::isEmpty($attribute['id_attribute'])) ? Tools::safeOutput($attribute['id_attribute']) : 'No ID', $err_tmp), 'Attribute');
                    } else {
                        self::addLog('Attribute', $attribute['id_attribute'], $attributeObj->id);
                        // import attribute texture image
                        if (self::imageExits($this->url . '/img/co/' . $attribute['id_attribute'] . '.jpg')) {
                            self::copyImg($attributeObj->id, null, $this->url . '/img/co/' . $attribute['id_attribute'] . '.jpg', 'attributes');
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Attribute');
                }
            }
        }
        // import feature
        foreach ($productAdditionalSecond['feature_product'] as $feature) {
            if ($featureObj = $this->createObjectModel('Feature', $feature['id_feature'])) {
                if (isset($feature['position']) && !self::isEmpty($feature['position'])) {
                    $featureObj->position = (int)$feature['position'];
                } else {
                    $featureObj->position = Feature::getHigherPosition() + 1;
                }

                foreach ($productAdditionalThird['feature_lang'] as $lang) {
                    if ($lang['id_feature'] == $feature['id_feature']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $featureObj->name[$lang['id_lang']] = $lang['name'];
                        if (self::isEmpty($featureObj->name[$lang['id_lang']])) {
                            $featureObj->name[$lang['id_lang']] = 'empty';
                        }
                    }
                }

                // Add to _shop relations
                $featuresShopsRelations = $this->getChangedIdShop($productAdditionalThird['feature_shop'], 'id_feature');
                if (array_key_exists($feature['id_feature'], $featuresShopsRelations)) {
                    $featureObj->id_shop_list = array_values($featuresShopsRelations[$feature['id_feature']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($featureObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($featureObj->id && Feature::existsInDatabase($featureObj->id, 'feature')) {
                        try {
                            $res = $featureObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $featureObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Feature (ID: %1$s) can not be saved. %2$s')), (isset($feature['id_feature']) && !self::isEmpty($feature['id_feature'])) ? Tools::safeOutput($feature['id_feature']) : 'No ID', $err_tmp), 'Feature');
                    } else {
                        self::addLog('Feature', $feature['id_feature'], $featureObj->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Feature');
                }
            }
        }

        // import feature value
        foreach ($productAdditionalThird['feature_value'] as $featureValue) {
            if ($featureValueObj = $this->createObjectModel('FeatureValue', $featureValue['id_feature_value'])) {
                $featureValueObj->id_feature = self::getLocalId('feature', $featureValue['id_feature'], 'data');
                $featureValueObj->custom = $featureValue['custom'];
                foreach ($productAdditionalThird['feature_value_lang'] as $lang) {
                    if ($lang['id_feature_value'] == $featureValue['id_feature_value']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $featureValueObj->value[$lang['id_lang']] = (!self::isEmpty($lang['value']) ? $lang['value'] : ' ');
                    }
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($featureValueObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($featureValueObj->id && FeatureValue::existsInDatabase($featureValueObj->id, 'feature_value')) {
                        try {
                            $res = $featureValueObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $featureValueObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('FeatureValue (ID: %1$s) can not be saved. %2$s')), (isset($featureValue['id_feature_value']) && !self::isEmpty($featureValue['id_feature_value'])) ? Tools::safeOutput($featureValue['id_feature_value']) : 'No ID', $err_tmp), 'FeatureValue');
                    } else {
                        self::addLog('FeatureValue', $featureValue['id_feature_value'], $featureValueObj->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'FeatureValue');
                }
            }
        }
        // import Tag
        foreach ($productAdditionalThird['tag'] as $tag) {
            //id-lang for version PS 1.4
            $tagPS14 = '';
            $tagPS14[$tag['id_tag']] = self::getLocalId('tag', $tag['id_lang'], 'data');
            if ($tagObject = $this->createObjectModel('Tag', $tag['id_tag'])) {
                $tagObject->id_lang = self::getLanguageID($tag['id_lang']);
                $tagObject->name = $tag['name'];

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($tagObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($tagObject->id && Tag::existsInDatabase($tagObject->id, 'tag')) {
                        try {
                            $res = $tagObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $tagObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Tag (ID: %1$s) can not be saved. %2$s')), (isset($tag['id_tag']) && !self::isEmpty($tag['id_tag'])) ? Tools::safeOutput($tag['id_tag']) : 'No ID', $err_tmp), 'Tag');
                    } else {
                        self::addLog('Tag', $tag['id_tag'], $tagObject->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Tag');
                }
            }
        }
        // import Products
        foreach ($products as $product) {
            if ($productObj = $this->createObjectModel('Product', $product['id_product'])) {
                $productObj->id_manufacturer = self::getLocalId('manufacturer', $product['id_manufacturer'], 'data');
                $productObj->id_supplier = self::getLocalId('supplier', $product['id_supplier'], 'data');
                $productObj->reference = $product['reference'];
                $productObj->supplier_reference = $product['supplier_reference'];
                $productObj->location = $product['location'];
                $productObj->width = $product['width'];
                $productObj->height = $product['height'];
                $productObj->depth = $product['depth'];
                $productObj->weight = $product['weight'];
                $productObj->quantity_discount = $product['quantity_discount'];
                $productObj->ean13 = $product['ean13'];
                $productObj->upc = $product['upc'];
                $productObj->cache_is_pack = $product['cache_is_pack'];
                $productObj->cache_has_attachments = $product['cache_has_attachments'];
                if ($product['id_category_default'] == 1 || $product['id_category_default'] == 0 || $product['id_category_default'] == 2) {
                    $productObj->id_category_default = Configuration::get('PS_HOME_CATEGORY');
                } else {
                    $localDefaultCategoryId = self::getLocalId('category', $product['id_category_default'], 'data');
                    if (self::isEmpty($localDefaultCategoryId)) {
                        $productObj->id_category_default = Configuration::get('PS_HOME_CATEGORY');
                    } else {
                        $productObj->id_category_default = self::getLocalId('category', $product['id_category_default'], 'data');
                    }
                }
                $productObj->id_tax_rules_group = self::getLocalId('taxrulesgroup', $product['id_tax_rules_group'], 'data');
                $productObj->on_sale = $product['on_sale'];
                $productObj->online_only = $product['online_only'];
                $productObj->ecotax = $product['ecotax'];
                $productObj->minimal_quantity = $product['minimal_quantity'];
                $productObj->price = $product['price'];
                $productObj->wholesale_price = $product['wholesale_price'];
                $productObj->unity = $product['unity'];
                $productObj->unit_price_ratio = $product['unit_price_ratio'];
                $productObj->additional_shipping_cost = $product['additional_shipping_cost'];
                $productObj->customizable = $product['customizable'];
                $productObj->text_fields = $product['text_fields'];
                $productObj->uploadable_files = $product['uploadable_files'];
                $productObj->active = $product['active'];
                $productObj->available_for_order = $product['available_for_order'];
                $productObj->condition = $product['condition'];
                $productObj->show_price = $product['show_price'];
                $productObj->indexed = 0; // always zero for new PS $product['indexed'];
                $productObj->cache_default_attribute = $product['cache_default_attribute'];
                $productObj->date_add = $product['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $product['date_add'];
                $productObj->date_upd = $product['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $product['date_upd'];
                $productObj->out_of_stock = $product['out_of_stock'];
//                $productObj->id_color_default = $product['id_color_default']; // @deprecated 1.5.0
                $productObj->quantity = $product['quantity'];
                if ($this->version >= 1.5) {
                    $productObj->id_shop_default = self::getShopID($product['id_shop_default']);
//                    $productObj->isbn = $product['isbn'];
                    $productObj->is_virtual = $product['is_virtual'];
                    $productObj->redirect_type = $product['redirect_type'];
                    $productObj->id_product_redirected = isset($product['id_product_redirected']) ? $product['id_product_redirected'] : 0;
                    $productObj->available_date = $product['available_date'];
//                    $productObj->show_condition = $product['show_condition'];
                    $productObj->visibility = $product['visibility'];
                    $productObj->advanced_stock_management = $product['advanced_stock_management'];
                }

                // Add to _shop relations
                $productsShopsRelations = $this->getChangedIdShop($productAdditionalSecond['product_shop'], 'id_product');
                if (array_key_exists($product['id_product'], $productsShopsRelations)) {
                    $productObj->id_shop_list = array_values($productsShopsRelations[$product['id_product']]);
                }

                //language fields
                foreach ($productAdditionalSecond['product_lang'] as $lang) {
                    if ($lang['id_product'] == $product['id_product']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $productObj->meta_description[$lang['id_lang']] = $lang['meta_description'];
                        $productObj->meta_keywords[$lang['id_lang']] = $lang['meta_keywords'];
                        $productObj->meta_title[$lang['id_lang']] = $lang['meta_title'];
                        $productObj->name[$lang['id_lang']] = $lang['name'];
                        $productObj->link_rewrite[$lang['id_lang']] = $lang['link_rewrite'];
                        if (isset($productObj->link_rewrite[$lang['id_lang']]) && !self::isEmpty($productObj->link_rewrite[$lang['id_lang']])) {
                            $valid_link = Validate::isLinkRewrite($productObj->link_rewrite[$lang['id_lang']]);
                        } else {
                            $valid_link = false;
                        }
                        if (!$valid_link) {
                            $productObj->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($productObj->name[$lang['id_lang']]);

                            if ($productObj->link_rewrite[$lang['id_lang']] == '') {
                                $productObj->link_rewrite[$lang['id_lang']] = 'friendly-url-autogeneration-failed';
                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('URL rewriting failed to auto-generate a friendly URL for: %s')), $productObj->name[$lang['id_lang']]), 'Product');
                            }

                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('The link for %1$s (ID: %2$s) was re-written as %3$s.')), $lang['link_rewrite'], (isset($product['id_product']) && !self::isEmpty($product['id_product'])) ? $product['id_product'] : 'null', $productObj->link_rewrite[$lang['id_lang']]), 'Product');
                        }
                        $productObj->description[$lang['id_lang']] = $lang['description'];
                        $productObj->description_short[$lang['id_lang']] = $lang['description_short'];
                        $productObj->available_now[$lang['id_lang']] = $lang['available_now'];
                        $productObj->available_later[$lang['id_lang']] = $lang['available_later'];
                    }
                }

                //@TODO get shop id from step-2
                if (!$this->shop_is_feature_active) {
                    $productObj->id_shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');
                } else {
                    $productObj->id_shop_default = (int)Context::getContext()->shop->id;
                }

                $res = false;
                $err_tmp = '';



                $this->validator->setObject($productObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($productObj->id && Product::existsInDatabase((int)$productObj->id, 'product')) {
                        try {
                            $res = $productObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $productObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Product (ID: %1$s) can not be saved. %2$s')), (isset($product['id_product']) && !self::isEmpty($product['id_product'])) ? Tools::safeOutput($product['id_product']) : 'No ID', $err_tmp), 'Product');
                    } else {
                        foreach ($productAdditionalSecond['product_carrier'] as $productCarrier) {
                            if ($productCarrier['id_product'] == $product['id_product']) {
                                $id_product = $productObj->id;
                                $id_carrier_reference = self::getCarrierReference($productCarrier['id_carrier']);
                                $id_shop = self::getShopID($productCarrier['id_shop']);

                                $result = Db::getInstance()->execute('INSERT IGNORE INTO ' . _DB_PREFIX_ . 'product_carrier (`id_product`, `id_carrier_reference`, `id_shop`) VALUES (' . (int)$id_product . ', ' . (int)$id_carrier_reference . ', ' . (int)$id_shop . ')');
                                if (!$result) {
                                    if (!$this->ps_validation_errors) {
                                        continue;
                                    }

                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t update product_carrier. ' . Db::getInstance()->getMsgError()), 'Product');
                                }
                            }
                        }

                        // set quantity to StockAvailable
                        if ($this->version >= 1.5) {
                            foreach ($productAdditionalSecond['stock_available'] as $stock_available) {
                                if ($product['id_product'] == $stock_available['id_product'] && $stock_available['id_product_attribute'] == 0) {
                                    StockAvailable::setQuantity($productObj->id, $stock_available['id_product_attribute'], $stock_available['quantity']);
                                    StockAvailable::setProductDependsOnStock($productObj->id, $stock_available['depends_on_stock']);
                                    StockAvailable::setProductOutOfStock($productObj->id, $stock_available['out_of_stock']);
                                }
                            }
                        } else {
                            StockAvailable::setQuantity($productObj->id, 0, $product['quantity']);
                        }

                        //update product activity for each shop
                        foreach ($productAdditionalSecond['product_shop'] as $productShop) {
                            if ($productShop['id_product'] == $product['id_product']) {
                                $result = Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product_shop SET active = ' . (int)$productShop['active'] . ' WHERE id_product = ' . (int)$productObj->id . ' AND id_shop = ' . (int)self::getShopID($productShop['id_shop']));
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t update product_shop. ' . Db::getInstance()->getMsgError()), 'Product');
                                }
                            }
                        }

                        //import Category_Product
                        $sql_values = array();
                        foreach ($productAdditionalSecond['category_product'] as $categoryProduct) {
                            if ($categoryProduct['id_product'] == $product['id_product']) {
                                if ((int)$categoryProduct['id_category'] == 2) {
                                    if (version_compare($this->version, '1.5', '<')) {
                                        $sql_values[] = '(' . self::getLocalID('category', (int)$categoryProduct['id_category'], 'data') . ', ' . (int)$productObj->id . ', ' . (int)$categoryProduct['position'] . ')';
                                    } else {
                                        $sql_values[] = '(' . (int)$categoryProduct['id_category'] . ', ' . (int)$productObj->id . ', ' . (int)$categoryProduct['position'] . ')';
                                    }
                                } else {
                                    $sql_values[] = '(' . self::getLocalID('category', (int)$categoryProduct['id_category'], 'data') . ', ' . (int)$productObj->id . ', ' . (int)$categoryProduct['position'] . ')';
                                }
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'category_product` (`id_category`, `id_product`, `position`) VALUES ' . implode(',', $sql_values));

                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add category_product. ' . Db::getInstance()->getMsgError()), 'Product');
                            }
                        }

                        //import images
                        foreach ($productAdditionalSecond['image'] as $image) {
                            if ($product['id_product'] == $image['id_product']) {
                                if ($imageObject = $this->createObjectModel('Image', $image['id_image'])) {
                                    $imageObject->id_product = $productObj->id;
                                    $imageObject->position = $image['position'];
                                    $imageObject->cover = $image['cover'];
                                    //language fields
                                    foreach ($productAdditionalThird['image_lang'] as $lang) {
                                        if ($lang['id_image'] == $image['id_image']) {
                                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                            $imageObject->legend[$lang['id_lang']] = $lang['legend'];
                                        }
                                    }
                                    // Add to _shop relations
                                    $imagesShopsRelations = $this->getChangedIdShop($productAdditionalThird['image_shop'], 'id_image');
                                    if (array_key_exists($image['id_image'], $imagesShopsRelations)) {
                                        $imageObject->id_shop_list = array_values($imagesShopsRelations[$image['id_image']]);
                                    }

                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($imageObject);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($imageObject->id && Image::existsInDatabase($imageObject->id, 'image')) {
                                            try {
                                                $res = $imageObject->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            try {
                                                $res = $imageObject->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError('Image (ID: %1$s) can not be saved. Product (ID: %2$s). %3$s'), (isset($image['id_image']) && !self::isEmpty($image['id_image'])) ? Tools::safeOutput($image['id_image']) : 'No ID', $productObj->id, $err_tmp), 'Image');
                                        } else {
                                            $url = $this->url . $this->image_path . Image::getImgFolderStatic($image['id_image']) . (int)$image['id_image'] . '.jpg';

                                            if (!self::imageExits($url)) {
                                                $url = $this->url . $this->image_path . $product['id_product'] . '-' . $image['id_image'] . '.jpg';
                                            }

                                            if (self::imageExits($url) && !(EDImport::copyImg($productObj->id, $imageObject->id, $url, 'products', $this->regenerate))) {
                                                $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'Image', true);
                                            }

                                            self::addLog('Image', $image['id_image'], $imageObject->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'Image');
                                    }
                                }
                            }
                        }

                        //import Product Attribute
                        foreach ($productAdditionalSecond['product_attribute'] as $productAttribute) {
                            if ($productAttribute['id_product'] == $product['id_product']) {
                                if ($combinationModel = $this->createObjectModel('Combination', $productAttribute['id_product_attribute'])) {
                                    $combinationModel->id_product = $productObj->id;
                                    $combinationModel->location = $productAttribute['location'];
                                    $combinationModel->ean13 = $productAttribute['ean13'];
                                    $combinationModel->upc = $productAttribute['upc'];
                                    $combinationModel->quantity = $productAttribute['quantity'];
                                    $combinationModel->reference = $productAttribute['reference'];
                                    $combinationModel->supplier_reference = $productAttribute['supplier_reference'];
                                    $combinationModel->wholesale_price = $productAttribute['wholesale_price'];
                                    $combinationModel->price = $productAttribute['price'];
                                    $combinationModel->ecotax = $productAttribute['ecotax'];
                                    $combinationModel->weight = $productAttribute['weight'];
                                    $combinationModel->unit_price_impact = $productAttribute['unit_price_impact'];
                                    $combinationModel->minimal_quantity = (isset($productAttribute['minimal_quantity']) && !self::isEmpty($productAttribute['minimal_quantity'])) ? $productAttribute['minimal_quantity'] : 1;
                                    $combinationModel->default_on = $productAttribute['default_on'];
                                    if ($this->version >= 1.5) {
//                                        $combinationModel->isbn = $productAttribute['isbn'];
                                        $combinationModel->available_date = $productAttribute['available_date'];
                                    }

                                    // Add to _shop relations
                                    $productAttrShopsRelations = $this->getChangedIdShop($productAdditionalThird['product_attribute_shop'], 'id_product_attribute');
                                    if (array_key_exists($productAttribute['id_product_attribute'], $productAttrShopsRelations)) {
                                        $combinationModel->id_shop_list = array_values($productAttrShopsRelations[$productAttribute['id_product_attribute']]);
                                    }

                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($combinationModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($combinationModel->id && Combination::existsInDatabase($combinationModel->id, 'product_attribute')) {
                                            try {
                                                $res = $combinationModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            try {
                                                $res = $combinationModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Product attribute (ID: %1$s) can not be saved. %2$s')), (isset($productAttribute['id_product_attribute']) && !self::isEmpty($productAttribute['id_product_attribute'])) ? Tools::safeOutput($combinationModel->id) : 'No ID', $err_tmp), 'Combination');
                                        } else {
                                            self::addLog('Combination', $productAttribute['id_product_attribute'], $combinationModel->id);
                                            // set quantity for Combination to StockAvailable
                                            if ($this->version >= 1.5) {
                                                foreach ($productAdditionalSecond['stock_available'] as $stock_available) {
                                                    if ($stock_available['id_product'] == $productAttribute['id_product'] && $stock_available['id_product_attribute'] == $productAttribute['id_product_attribute']) {
                                                        StockAvailable::setQuantity($combinationModel->id_product, $combinationModel->id, $stock_available['quantity']);
                                                        StockAvailable::setProductDependsOnStock($combinationModel->id_product, $stock_available['depends_on_stock'], null, $combinationModel->id);
                                                        StockAvailable::setProductOutOfStock($combinationModel->id_product, $stock_available['out_of_stock'], null, $combinationModel->id);
                                                    }
                                                }
                                            } else {
                                                StockAvailable::setQuantity($combinationModel->id_product, $combinationModel->id, $productAttribute['quantity']);
                                            }
                                            //import product_attribute_combination
                                            $sql_values = array();
                                            foreach ($productAdditionalThird['product_attribute_combination'] as $productAttributeCombination) {
                                                if ($productAttributeCombination['id_product_attribute'] == $productAttribute['id_product_attribute']) {
                                                    $sql_values[] = '(' . self::getLocalID('attribute', (int)$productAttributeCombination['id_attribute'], 'data') . ', ' . self::getLocalID('combination', $productAttributeCombination['id_product_attribute'], 'data') . ')';
                                                }
                                            }
                                            if (!self::isEmpty($sql_values)) {
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'product_attribute_combination` (`id_attribute`, `id_product_attribute`) VALUES ' . implode(',', $sql_values));
                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add product_attribute_combination. ' . Db::getInstance()->getMsgError()), 'Combination');
                                                }
                                            }

                                            //import product_attribute_image
                                            $sql_values = array();
                                            foreach ($productAdditionalThird['product_attribute_image'] as $productAttributeImage) {
                                                if ($productAttributeImage['id_product_attribute'] == $productAttribute['id_product_attribute']) {
                                                    $sql_values[] = '(' . (int)$combinationModel->id . ', ' . self::getLocalID('image', (int)$productAttributeImage['id_image'], 'data') . ')';
                                                }
                                            }
                                            if (!self::isEmpty($sql_values)) {
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'product_attribute_image` (`id_product_attribute`, `id_image`) VALUES ' . implode(',', $sql_values));
                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add product_attribute_image. ' . Db::getInstance()->getMsgError()), 'Combination');
                                                }
                                            }
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'Combination');
                                    }
                                }
                            }
                        }

//                        if ($this->version >= 1.5) {
//                            foreach ($productAdditionalSecond['stock'] as $stock) {
//                                if ($stock['id_product'] == $product['id_product']) {
//                                    $stockObj = new Stock();
//                                    $stockObj->id_warehouse = MigrationProData::getLocalID('Warehouse', $stock['id_warehouse']);
//                                    $stockObj->id_product = $productObj->id;
//                                    $stockObj->id_product_attribute = self::getLocalID('combination', $stock['id_product_attribute'], 'data');
//                                    $stockObj->reference = $stock['reference'];
//                                    $stockObj->upc = $stock['upc'];
//                                    $stockObj->physical_quantity = $stock['physical_quantity'];
//                                    $stockObj->usable_quantity = $stock['usable_quantity'];
//                                    $stockObj->price_te = $stock['price_te'];
//                                    try {
//                                        $res = $stockObj->add(false);
//                                    } catch (PrestaShopException $e) {
//                                        $err_tmp = $e->getMessage();
//                                    }
//                                    if (!$res) {
//                                        if (!$this->ps_validation_errors) {
//                                            continue;
//                                        }
//
//                                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Stock (ID: %1$s) can not be saved. %2$s')), (isset($stock['id_stock']) && !self::isEmpty($stock['id_stock'])) ? Tools::safeOutput($stock['id_stock']) : 'No ID', $err_tmp);
//                                    } else {
//                                        self::addLog('Stock', $stock['id_stock'], $stockObj->id);
//                                    }
//                                }
//                            }
//
//                            foreach ($productAdditionalSecond['warehouse_product_location'] as $warehouseProductLocation) {
//                                if ($warehouseProductLocation['id_product'] == $product['id_product']) {
//                                    $warehouseProductLocationObj = new WarehouseProductLocation();
//                                    $warehouseProductLocationObj->id_warehouse = MigrationProData::getLocalID('Warehouse', $warehouseProductLocation['id_warehouse']);
//                                    $warehouseProductLocationObj->id_product = $productObj->id;
//                                    $warehouseProductLocationObj->id_product_attribute = self::getLocalID('combination', $warehouseProductLocation['id_product_attribute'], 'data');
//                                    $warehouseProductLocationObj->location = $warehouseProductLocation['location'];
//                                    try {
//                                        $res = $warehouseProductLocationObj->add(false);
//                                    } catch (PrestaShopException $e) {
//                                        $err_tmp = $e->getMessage();
//                                    }
//                                    if (!$res) {
//                                        if (!$this->ps_validation_errors) {
//                                            continue;
//                                        }
//                                        $this->error_msg[] = sprintf(self::displayError($this->module->l('Stock (ID: %1$s) can not be saved. %2$s')), (isset($warehouseProductLocation['id_warehouse_product_location']) && !self::isEmpty($warehouseProductLocation['id_warehouse_product_location'])) ? Tools::safeOutput($warehouseProductLocation['id_warehouse_product_location']) : 'No ID', $err_tmp);
//                                    } else {
//                                        self::addLog('warehouseproductlocation', $warehouseProductLocation['id_warehouse_product_location'], $warehouseProductLocationObj->id);
//                                    }
//                                }
//                            }
//                        }

                        //import specific price
                        $second = 10;
                        foreach ($productAdditionalSecond['specific_price'] as $specificPrice) {
                            if ($product['id_product'] == $specificPrice['id_product']) {
                                if ($specificPriceObj = $this->createObjectModel('SpecificPrice', $specificPrice['id_specific_price'])) {
                                    $specificPriceObj->id_shop = (int)self::getShopID($specificPrice['id_shop']);
                                    $specificPriceObj->id_product = $productObj->id;
                                    $specificPriceObj->id_currency = self::getCurrencyID($specificPrice['id_currency']);
                                    $specificPriceObj->id_country = self::getLocalId('country', $specificPrice['id_country'], 'data');
                                    $specificPriceObj->id_group = self::getCustomerGroupID($specificPrice['id_group']);
//                                    $specificPriceObj->price = ((int)$specificPrice['price'] == 0) ? -1 : $specificPrice['price'];
                                    $specificPriceObj->price = ($specificPrice['price'] <= 0) ? '-1' : (float)$specificPrice['price'];
                                    $specificPriceObj->from_quantity = $specificPrice['from_quantity'];
                                    $specificPriceObj->reduction = $specificPrice['reduction'];
                                    $specificPriceObj->reduction_type = $specificPrice['reduction_type'];
                                    $specificPriceObj->from = $specificPrice['from'];
                                    $specificPriceObj->to = $specificPrice['to'];
//                                    $specificPriceObj->id_customer = (isset($specificPrice['id_customer']) && !self::isEmpty($specificPrice['id_customer'])) ? self::getLocalId('customer', $specificPrice['id_customer'], 'data') : 0;
                                    $specificPriceObj->id_customer = (isset($specificPrice['id_customer']) && !self::isEmpty($specificPrice['id_customer'])) ? $specificPrice['id_customer'] : 0;
                                    if ($this->version >= 1.5) {
                                        $specificPriceObj->id_shop = self::getShopID($specificPrice['id_shop']);
                                        $specificPriceObj->id_shop_group = Shop::getGroupFromShop($specificPriceObj->id_shop);

                                        $specificPriceObj->id_cart = $specificPrice['id_cart'];
                                        $specificPriceObj->id_product_attribute = self::getLocalId('combination', $specificPrice['id_product_attribute'], 'data');
                                        $specificPriceObj->id_specific_price_rule = $specificPrice['id_specific_price_rule'];

                                        $specificPriceObj->reduction_tax = (isset($specificPrice['reduction_tax']) && !self::isEmpty($specificPrice['reduction_tax'])) ? $specificPrice['reduction_tax'] : 1;
                                    }

                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($specificPriceObj);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($specificPriceObj->id && SpecificPrice::existsInDatabase($specificPriceObj->id, 'specific_price')) {
                                            try {
                                                $res = $specificPriceObj->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            try {
                                                $res = $specificPriceObj->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        // check duplicate entry

                                        if (Db::getInstance()->getNumberError() == 1062) {
                                            $second++;
                                            if ($second > 59) {
                                                $second = 10;
                                            }
                                            $specificPriceObj->from = substr_replace($specificPrice['from'], $second, Tools::strlen($specificPrice['from']) - 2, Tools::strlen($specificPrice['from']));
                                            $res = $specificPriceObj->add(false);
                                        }

                                        if (!$res) {
                                            $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError() . DB::getInstance()->getNumberError();
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('SpecificPrice (ID: %1$s) can not be saved. %2$s')), (isset($specificPrice['id_specific_price']) && !self::isEmpty($specificPrice['id_specific_price'])) ? Tools::safeOutput($specificPrice['id_specific_price']) : 'No ID', $err_tmp . ' ' . $error_tmp), 'SpecificPrice');
                                        } else {
                                            self::addLog('SpecificPrice', $specificPrice['id_specific_price'], $specificPriceObj->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'SpecificPrice');
                                    }
                                }
                            }
                        }

                        // import product_download
                        foreach ($productAdditionalSecond['product_download'] as $productDownload) {
                            $changeDateExpiration = false;
                            if ($product['id_product'] == $productDownload['id_product']) {
                                if ($productDownloadObject = $this->createObjectModel('ProductDownload', $productDownload['id_product_download'])) {
                                    $productDownloadObject->id_product = $productObj->id;
                                    $productDownloadObject->display_filename = $productDownload['display_filename'];
                                    $productDownloadObject->filename = $productDownload['filename'];
                                    $productDownloadObject->date_add = $productDownload['date_add'];
                                    if ($productDownload['date_expiration'] == '0000-00-00 00:00:00') {
                                        $productDownloadObject->date_expiration = date('Y-m-d H:i:s');
                                        $changeDateExpiration = true;
                                    } else {
                                        $productDownloadObject->date_expiration = $productDownload['date_expiration'];
                                    }
                                    $productDownloadObject->nb_days_accessible = $productDownload['nb_days_accessible'];
                                    $productDownloadObject->nb_downloadable = $productDownload['nb_downloadable'];
                                    $productDownloadObject->active = $productDownload['active'];
                                    $productDownloadObject->is_shareable = $productDownload['is_shareable'];
                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($productDownloadObject);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($productDownloadObject->id && ProductDownload::existsInDatabase($productDownloadObject->id, 'product_download')) {
                                            try {
                                                $res = $productDownloadObject->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            try {
                                                $res = $productDownloadObject->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError('ProductDownload (ID: %1$s) can not be saved. Product (ID: %2$s). %3$s'), (isset($productDownload['id_product_download']) && !self::isEmpty($productDownload['id_product_download'])) ? Tools::safeOutput($productDownload['id_product_download']) : 'No ID', $productObj->id, $err_tmp), 'ProductDownload');
                                        } else {
                                            $client = new EDClient($this->url . '/migration_pro/server.php', Configuration::get('migrationpro_token'));
                                            $client->setPostData('download/' . $productDownload['filename']);
                                            $client->setTimeout(999);
                                            $client->query('file');
                                            file_put_contents(getcwd() . '/../download/' . $productDownload['filename'], $client->getContent());

                                            if ($changeDateExpiration) {
                                                Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product_download SET date_expiration = \'' . pSQL($productDownload['date_expiration']) . '\' WHERE id_product_download = ' . (int)$productDownloadObject->id);
                                            }

                                            self::addLog('ProductDownload', $productDownload['id_product_download'], $productDownloadObject->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'ProductDownload');
                                    }
                                }
                            }
                        }

                        // import attachments
                        $sql_values = array();
                        foreach ($productAdditionalSecond['product_attachment'] as $productAttachment) {
                            if ($product['id_product'] == $productAttachment['id_product']) {
                                foreach ($productAdditionalThird['attachment'] as $attachment) {
                                    if ($attachment['id_attachment'] == $productAttachment['id_attachment']) {
                                        if ($attachmentObject = $this->createObjectModel('Attachment', $attachment['id_attachment'])) {
                                            $attachmentObject->file = $attachment['file'];
                                            $fileName = "";
                                            if (isset($attachment['filename'])) {
                                                $fileName = $attachment['filename'];
                                            } elseif (isset($attachment['file_name'])) {
                                                $fileName = $attachment['file_name'];
                                            }
                                            $attachmentObject->file_name = $fileName;

                                            $fileSize = 0;
                                            if (isset($attachment['filesize'])) {
                                                $fileSize = $attachment['filesize'];
                                            } elseif (isset($attachment['file_size'])) {
                                                $fileSize = $attachment['file_size'];
                                            }
                                            $attachmentObject->file_size = $fileSize;
                                            $attachmentObject->mime = $attachment['mime'];
                                            //language fields
                                            foreach ($productAdditionalThird['attachment_lang'] as $lang) {
                                                if ($lang['id_attachment'] == $attachment['id_attachment']) {
                                                    $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                                    $attachmentObject->name[$lang['id_lang']] = $lang['name'];
                                                    $attachmentObject->description[$lang['id_lang']] = $lang['description'];
                                                }
                                            }
                                            $res = false;
                                            $err_tmp = '';

                                            $this->validator->setObject($attachmentObject);
                                            $this->validator->checkFields();
                                            $error_tmp = $this->validator->getValidationMessages();
                                            if (self::isEmpty($error_tmp)) {
                                                if ($attachmentObject->id && Attachment::existsInDatabase($attachmentObject->id, 'attachment')) {
                                                    try {
                                                        $res = $attachmentObject->update();
                                                    } catch (PrestaShopException $e) {
                                                        $err_tmp = $e->getMessage();
                                                    }
                                                }

                                                if (!$res) {
                                                    try {
                                                        $res = $attachmentObject->add(false);
                                                    } catch (PrestaShopException $e) {
                                                        $err_tmp = $e->getMessage();
                                                    }
                                                }

                                                if (!$res) {
                                                    $this->showMigrationMessageAndLog(sprintf(self::displayError('Attachment (ID: %1$s) can not be saved. Product (ID: %2$s). %3$s'), (isset($attachment['id_attachment']) && !self::isEmpty($attachment['id_attachment'])) ? Tools::safeOutput($attachment['id_attachment']) : 'No ID', $productObj->id, $err_tmp), 'Attachment');
                                                } else {
                                                    $client = new EDClient($this->url . '/modules/migrationproserver/server.php', Configuration::get('migrationpro_token'));
                                                    $client->setPostData('download/' . $attachment['file']);
                                                    $client->setTimeout(999);
                                                    $client->query('file');
                                                    $fileName = getcwd() . '/../download/' . $attachment['file'];
                                                    file_put_contents($fileName, $client->getContent());

                                                    if ($attachmentObject->file_size == 0) {
                                                        $fileSize = filesize($fileName);
                                                        $attachmentObject->file_size = self::isEmpty($fileSize) ? 0 : $fileSize;
                                                        $attachmentObject->update();
                                                    }

                                                    self::addLog('Attachment', $attachment['id_attachment'], $attachmentObject->id);
                                                }
                                            } else {
                                                $this->showMigrationMessageAndLog($error_tmp, 'Attachment');
                                            }
                                        }
                                    }
                                }
                                //import product_attachments
                                $sql_values[] = '(' . $productObj->id . ', ' . self::getLocalID('attachment', $productAttachment['id_attachment'], 'data') . ')';

                                if (!self::isEmpty($sql_values)) {
                                    $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'product_attachment` (`id_product`, `id_attachment`) VALUES ' . implode(',', $sql_values));
                                    if (!$result) {
                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add product_attachment. ' . Db::getInstance()->getMsgError()), 'Attachment');
                                    }
                                }
                            }
                        }

                        //import product_supplier
                        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                            if ($productSupplierObject = $this->createObjectModel('ProductSupplier', $product['id_product'])) {
                                $productSupplierObject->id_product = $productObj->id;
                                $productSupplierObject->id_product_attribute = 0;
                                $productSupplierObject->id_supplier = self::getLocalID('supplier', $product['id_supplier'], 'data');
                                $productSupplierObject->product_supplier_price_te = 0;
                                $productSupplierObject->id_currency = 0;

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($productSupplierObject);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($productSupplierObject->id && ProductSupplier::existsInDatabase($productSupplierObject->id, 'product_supplier')) {
                                        try {
                                            $res = $productSupplierObject->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $productSupplierObject->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Product_Supplier (ID: %1$s) can not be saved. %2$s')), (isset($product['id_product']) && !self::isEmpty($product['id_product'])) ? Tools::safeOutput($product['id_product']) : 'No ID', $err_tmp), 'ProductSupplier');
                                    } else {
                                        self::addLog('ProductSupplier', $product['id_product'], $productSupplierObject->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'ProductSupplier');
                                }
                            }
                        } else {
                            foreach ($productAdditionalSecond['product_supplier'] as $productSupplier) {
                                if ($productSupplier['id_product'] == $product['id_product']) {
                                    if ($productSupplierObject = $this->createObjectModel('ProductSupplier', $productSupplier['id_product_supplier'])) {
                                        $productSupplierObject->id_product = $productObj->id;
                                        $productSupplierObject->id_product_attribute = self::getLocalID('combination', $productSupplier['id_product_attribute'], 'data');
                                        $productSupplierObject->id_supplier = self::getLocalID('supplier', $productSupplier['id_supplier'], 'data');
                                        $productSupplierObject->product_supplier_price_te = $productSupplier['product_supplier_price_te'];
                                        $productSupplierObject->id_currency = self::getCurrencyID($productSupplier['id_currency']);

                                        $res = false;
                                        $err_tmp = '';

                                        $this->validator->setObject($productSupplierObject);
                                        $this->validator->checkFields();
                                        $error_tmp = $this->validator->getValidationMessages();
                                        if (self::isEmpty($error_tmp)) {
                                            if ($productSupplierObject->id && ProductSupplier::existsInDatabase($productSupplierObject->id, 'product_supplier')) {
                                                try {
                                                    $res = $productSupplierObject->update();
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }
                                            if (!$res) {
                                                try {
                                                    $res = $productSupplierObject->add(false);
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }

                                            if (!$res) {
                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Product_Supplier (ID: %1$s) can not be saved. %2$s')), (isset($productSupplier['id_product_supplier']) && !self::isEmpty($productSupplier['id_product_supplier'])) ? Tools::safeOutput($productSupplier['id_product_supplier']) : 'No ID', $err_tmp), 'ProductSupplier');
                                            } else {
                                                self::addLog('ProductSupplier', $productSupplier['id_product_supplier'], $productSupplierObject->id);
                                            }
                                        } else {
                                            $this->showMigrationMessageAndLog($error_tmp, 'ProductSupplier');
                                        }
                                    }
                                }
                            }
                        }
                        //import feature_product
                        $sql_values = array();
                        foreach ($productAdditionalSecond['feature_product'] as $featureProduct) {
                            if ($featureProduct['id_product'] == $product['id_product']) {
                                Product::addFeatureProductImport($productObj->id, self::getLocalId('feature', (int)$featureProduct['id_feature'], 'data'), self::getLocalId('featurevalue', (int)$featureProduct['id_feature_value'], 'data'));
                            }
                        }
                        //import customization_field
                        foreach ($productAdditionalSecond['customization_field'] as $customizationField) {
                            if ($customizationField['id_product'] == $product['id_product']) {
                                if ($customizationFieldModel = $this->createObjectModel('CustomizationField', $customizationField['id_customization_field'])) {
                                    $customizationFieldModel->id_product = $productObj->id;
                                    $customizationFieldModel->type = $customizationField['type'];
                                    $customizationFieldModel->required = $customizationField['required'];
                                    foreach ($productAdditionalThird['customization_field_lang'] as $lang) {
                                        if ($lang['id_customization_field'] == $customizationField['id_customization_field']) {
                                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                            $customizationFieldModel->name[$lang['id_lang']] = $lang['name'];
                                            if (self::isEmpty($customizationFieldModel->name[$lang['id_lang']])) {
                                                $customizationFieldModel->name[$lang['id_lang']] = 'Empty';
                                            }
                                        }
                                    }

                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($customizationFieldModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($customizationFieldModel->id && CustomizationField::existsInDatabase($customizationFieldModel->id, 'customization_field')) {
                                            try {
                                                $res = $customizationFieldModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $customizationFieldModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError('CustomizationField (ID: %1$s) from Product (ID: %2$s) can not be saved. %3$s'), $productObj->id, (isset($customizationField['id_customization_field']) && !self::isEmpty($customizationField['id_customization_field'])) ? Tools::safeOutput($customizationField['id_customization_field']) : 'No ID', $err_tmp), 'CustomizationField');
                                        } else {
                                            self::addLog('CustomizationField', $customizationField['id_customization_field'], $customizationFieldModel->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'CustomizationField');
                                    }
                                }
                            }
                        }
                        //import product_tag
//                        Tag::deleteTagsForProduct($productObj->id);
                        $sql_values = array();
                        foreach ($productAdditionalSecond['product_tag'] as $productTag) {
                            if ($productTag['id_product'] == $product['id_product']) {
//                                $idLangTag = (isset($tagPS14[$productTag['id_tag']]) && !self::isEmpty
//                                    ($tagPS14[$productTag['id_tag']])) ? self::getLocalID('tag', (int)$tagPS14[$productTag['id_tag']], 'data') : 0; //@TODO not id lang field on PS 1.4
                                $sql_values[] = '(' . (int)$productObj->id . ', ' . self::getLocalID('tag', (int)$productTag['id_tag'], 'data') . ', ' . self::getLanguageID($productTag['id_lang']) . ')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'product_tag` (`id_product`, `id_tag`, `id_lang`)
                                VALUES ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add product_tag. ' . Db::getInstance()->getMsgError()), 'Product');
                            }
                        }


                        if (count($this->error_msg) == 0) {
                            self::addLog('Product', $product['id_product'], $productObj->id);

                            //update multistore language fields
                            if (!version_compare($this->version, '1.5', '<')) {
                                if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                                    foreach ($productAdditionalSecond['product_lang'] as $lang) {
                                        if ($lang['id_product'] == $product['id_product']) {
                                            $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                            $lang['id_product'] = $productObj->id;
                                            self::updateMultiStoreLang('product', $lang);
                                        }
                                    }
                                }
                            }

                            Module::processDeferedFuncCall();
                            Module::processDeferedClearCache();
                            Tag::updateTagCount();
                        }

                        // import pack products
//                        foreach ($productAdditionalSecond['product_pack'] as $productPack) {
//                            if ($productPack['id_product_pack'] != $product['id_product']) {
//                                continue;
//                            }
//                            if (!self::isEmpty(self::getLocalID('product', $productPack['id_product_item'], 'data'))) {
//                                Pack::addItem($productObj->id, self::getLocalID('product', $productPack['id_product_item'], 'data'), $productPack['quantity'],
//                                    self::getLocalID('combination', $productPack['id_product_attribute_item'], 'data'));
//                            } else {
//                                $this->client->serializeOff();
//                                $this->client->setPostData($this->query->singleProduct($productPack['id_product_item']));
//                                if ($this->client->query()) {
//                                    $product2 = $this->client->getContent();
//                                    $product2Id = AdminMigrationProController::getCleanIDs($product2, 'id_product');
//                                    $this->client->serializeOn();
//                                    $this->client->setPostData($this->query->productSqlSecond($product2Id));
//                                    if ($this->client->query()) {
//                                        $product2AdditionalSecond = $this->client->getContent();
//                                        $id_product_attribute = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['product_attribute'], 'id_product_attribute');
//                                        $id_feature = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['feature_product'], 'id_feature');
//                                        $id_feature_value = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['feature_product'], 'id_feature_value');
//                                        $id_supplier = AdminMigrationProController::getCleanIDs($product2, 'id_supplier');
//                                        $id_customization_field = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['customization_field'], 'id_customization_field');
//                                        $id_tag = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['product_tag'], 'id_tag');
//                                        $id_image = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['image'], 'id_image');
//                                        $id_attachment = AdminMigrationProController::getCleanIDs($product2AdditionalSecond['product_attachment'], 'id_attachment');
//                                        $this->client->setPostData($this->query->productSqlThird($id_product_attribute, $id_feature, $id_feature_value, $id_supplier, $id_customization_field, $id_tag,
//                                            $id_image, $id_attachment));
//                                        if ($this->client->query()) {
//                                            $product2AdditionalThird = $this->client->getContent();
//                                            $id_attribute_group = AdminMigrationProController::getCleanIDs($product2AdditionalThird['product_attribute_combination'], 'id_attribute_group');
//                                            $id_attribute = AdminMigrationProController::getCleanIDs($product2AdditionalThird['product_attribute_combination'], 'id_attribute');
//                                            $this->client->setPostData($this->query->productSqlFourth($id_attribute_group, $id_attribute));
//                                            if ($this->client->query()) {
//                                                $product2AdditionalFourth = $this->client->getContent();
//
//                                                $import = new EDImport($this->process, $this->version, $this->url, $this->force_ids, $this->client, $this->query);
//                                                $import->setImagePath($this->image_path);
//                                                $import->setImageSupplierPath($this->image_supplier_path);
//
//                                                $import->products($product2, $product2AdditionalSecond, $product2AdditionalThird, $product2AdditionalFourth, true);
//                                                $this->errors = $import->getErrorMsg();
//                                                $this->warnings = $import->getWarningMsg();
//                                                $this->response = $import->getResponse();
//                                            } else {
//                                                if (!$this->ps_validation_errors) {
//                                                    continue;
//                                                }
//                                                $this->errors[] = 'Can\'t execute query to source Shop.';
//                                            }
//                                        } else {
//                                            if (!$this->ps_validation_errors) {
//                                                continue;
//                                            }
//                                            $this->errors[] = 'Can\'t execute query to source Shop.';
//                                        }
//                                    } else {
//                                        if (!$this->ps_validation_errors) {
//                                            continue;
//                                        }
//                                        $this->errors[] = 'Can\'t execute query to source Shop.';
//                                    }
//                                } else {
//                                    if (!$this->ps_validation_errors) {
//                                        continue;
//                                    }
//                                    $this->errors[] = 'Can\'t execute query to source Shop.';
//                                }
//                                Pack::addItem($productObj->id, self::getLocalID('product', $productPack['id_product_item'], 'data'), $productPack['quantity'],
//                                    self::getLocalID('combination', $productPack['id_product_attribute_item'], 'data'));
//                            }
//                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Product');
                }
            }
        }

        if (!$innerMethodCall) {
            $this->updateProcess(count($products));
        }
    }

    /**
     * @param $accessories
     */
    public function accessories($accessories)
    {
        foreach ($accessories as $accessory) {
            $accessory_1 = self::getLocalID('product', $accessory['id_product_1'], 'data');
            $accessory_2 = self::getLocalID('product', $accessory['id_product_2'], 'data');


            if (!self::isEmpty($accessory_1) && !self::isEmpty($accessory_2)) {
                $res = self::importAccessories($accessory);

                if (!$res) {
                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add accessory. ' . Db::getInstance()->getMsgError()), 'Product');
                }
            } else {
                if (self::isEmpty($accessory_1)) {
                    $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Accessory (ID: %1$s) not found in source store')), (isset($accessory['id_product_1']) && !self::isEmpty($accessory['id_product_1'])) ? Tools::safeOutput($accessory['id_product_1']) : 'No ID'), 'Product');
                }
                if (self::isEmpty($accessory_2)) {
                    $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Accessory (ID: %1$s) not found in source store')), (isset($accessory['id_product_2']) && !self::isEmpty($accessory['id_product_2'])) ? Tools::safeOutput($accessory['id_product_2']) : 'No ID'), 'Product');
                }
            }
        }
        $this->updateProcess(count($accessories));
    }

    /**
     * @param $specificPriceRules
     * @param $specificPriceRuleConditionGroups
     * @param $specificPriceRuleConditions
     */
    public function catalogPriceRules($specificPriceRules, $specificPriceRuleCountries, $specificPriceRuleConditionGroups, $specificPriceRuleConditions)
    {
        // Import Country
        foreach ($specificPriceRuleCountries['country'] as $country) {
            if ($countryModel = $this->createObjectModel('Country', $country['id_country'])) {
                $countryModel->id_zone = $country['id_zone'];
                $countryModel->id_currency = self::getCurrencyID($country['id_currency']);
                $countryModel->call_prefix = $country['call_prefix'];
                $countryModel->iso_code = $country['iso_code'];
                $countryModel->active = $country['active'];
                $countryModel->contains_states = $country['contains_states'];
                $countryModel->need_identification_number = $country['need_identification_number'];
                $countryModel->need_zip_code = $country['need_zip_code'];
                $countryModel->zip_code_format = $country['zip_code_format'];
                $countryModel->display_tax_label = (isset($country['display_tax_label'])) ? (bool)$country['display_tax_label'] : true;

                // Add to _shop relations
                $countriesShopsRelations = $this->getChangedIdShop($specificPriceRuleCountries['country_shop'], 'id_country');
                if (array_key_exists($country['id_country'], $countriesShopsRelations)) {
                    $countryModel->id_shop_list = array_values($countriesShopsRelations[$country['id_country']]);
                }


                // Language fields
                foreach ($specificPriceRuleCountries['country_lang'] as $lang) {
                    if ($lang['id_country'] == $country['id_country']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $countryModel->name[$lang['id_lang']] = $lang['name'];
                    }
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($countryModel);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($countryModel->id && Country::existsInDatabase($countryModel->id, 'country')) {
                        try {
                            $res = $countryModel->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $countryModel->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $err_tmp), 'Country');
                    } else {
                        self::addLog('Country', $country['id_country'], $countryModel->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Country');
                }
            }
        }

        foreach ($specificPriceRules as $specificPriceRule) {
            if ($specificPriceRuleObj = $this->createObjectModel('SpecificPriceRule', $specificPriceRule['id_specific_price_rule'])) {
                $specificPriceRuleObj->name = $specificPriceRule['name'];
                $specificPriceRuleObj->id_shop = self::getShopID($specificPriceRule['id_shop']);
                $specificPriceRuleObj->id_currency = self::getCurrencyID($specificPriceRule['id_currency']);
                $specificPriceRuleObj->id_country = self::getLocalID('country', $specificPriceRule['id_country'], 'data');
                $specificPriceRuleObj->id_group = self::getCustomerGroupID($specificPriceRule['id_group']);
                $specificPriceRuleObj->from_quantity = $specificPriceRule['from_quantity'];
                $specificPriceRuleObj->price = $specificPriceRule['price'];
                $specificPriceRuleObj->reduction = $specificPriceRule['reduction'];
                if (self::isEmpty($specificPriceRule['reduction_tax'])) {
                    $specificPriceRuleObj->reduction_tax = 0;
                } else {
                    $specificPriceRuleObj->reduction_tax = $specificPriceRule['reduction_tax'];
                }
                $specificPriceRuleObj->reduction_type = $specificPriceRule['reduction_type'];
                $specificPriceRuleObj->from = $specificPriceRule['from'];
                $specificPriceRuleObj->to = $specificPriceRule['to'];

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($specificPriceRuleObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($specificPriceRuleObj->id && SpecificPriceRule::existsInDatabase($specificPriceRuleObj->id, 'specific_price_rule')) {
                        try {
                            $res = $specificPriceRuleObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $specificPriceRuleObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Specific price rule(ID: %1$s) can not be saved. %2$s')), (isset($specificPriceRule['id_specific_price_rule']) && !self::isEmpty($specificPriceRule['id_specific_price_rule'])) ? Tools::safeOutput($specificPriceRule['id_specific_price_rule']) : 'No ID', $err_tmp), 'SpecificPriceRule');
                    } else {
                        // Import Specific Price Rule Condition Groups
                        foreach ($specificPriceRuleConditionGroups as $specificPriceRuleConditionGroup) {
                            $sql_value = '';
                            if ($specificPriceRuleConditionGroup['id_specific_price_rule'] == $specificPriceRule['id_specific_price_rule']) {
                                $sql_value = '(' . (int)$specificPriceRuleObj->id . ')';
                            }
                            if (!self::isEmpty($sql_value)) {
                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'specific_price_rule_condition_group` (`id_specific_price_rule`)
                                VALUES ' . $sql_value);
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add specific_price_rule_condition_group. ' . Db::getInstance()->getMsgError()), 'SpecificPriceRule');
                                } else {
                                    $id_specific_price_rule_condition_group = Db::getInstance()->Insert_ID();

                                    // Import Specific Price Rule Conditions
                                    foreach ($specificPriceRuleConditions as $specificPriceRuleCondition) {
                                        $sql_value = '';
                                        if ($specificPriceRuleCondition['id_specific_price_rule_condition_group'] == $specificPriceRuleConditionGroup['id_specific_price_rule_condition_group']) {
                                            if (preg_match('|category|', $specificPriceRuleCondition['type'])) {
                                                $value = self::getLocalID('category', $specificPriceRuleCondition['value'], 'data');
                                            } elseif (preg_match('|manufacturer|', $specificPriceRuleCondition['type'])) {
                                                $value = self::getLocalID('manufacturer', $specificPriceRuleCondition['value'], 'data');
                                            } elseif (preg_match('|supplier|', $specificPriceRuleCondition['type'])) {
                                                $value = self::getLocalID('supplier', $specificPriceRuleCondition['value'], 'data');
                                            } elseif (preg_match('|attribute|', $specificPriceRuleCondition['type'])) {
                                                $value = self::getLocalID('attribute', $specificPriceRuleCondition['value'], 'data');
                                            } elseif (preg_match('|feature|', $specificPriceRuleCondition['type'])) {
                                                $value = self::getLocalID('feature', $specificPriceRuleCondition['value'], 'data');
                                            }

                                            $sql_value = '(' . (int)$id_specific_price_rule_condition_group . ', \'' . pSQL($specificPriceRuleCondition['type']) . '\', ' . (int)$value . ')';
                                        }
                                        if (!self::isEmpty($sql_value)) {
                                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'specific_price_rule_condition` (`id_specific_price_rule_condition_group`, `type`, `value`)
                                VALUES ' . $sql_value);
                                            if (!$result) {
                                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add specific_price_rule_condition. ' . Db::getInstance()->getMsgError()), 'specificPriceRuleCondition');
                                            } else {
                                                $d_specific_price_rule_condition = Db::getInstance()->Insert_ID();
                                                self::addLog('specificPriceRuleCondition', $specificPriceRuleCondition['id_specific_price_rule_condition'], $d_specific_price_rule_condition);
                                            }
                                        }
                                    }
                                    self::addLog('specificPriceRuleConditionGroup', $specificPriceRuleConditionGroup['id_specific_price_rule_condition_group'], $id_specific_price_rule_condition_group);
                                }
                            }
                        }

                        if (count($this->error_msg) == 0) {
                            self::addLog('SpecificPriceRule', $specificPriceRule['id_specific_price_rule'], $specificPriceRuleObj->id);
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'SpecificPriceRule');
                }
            }
        }

        $this->updateProcess(count($specificPriceRules));
    }

    /**
     * @param $employees
     * @param $employeesShop
     */
    public function employees($employees, $employeesShop)
    {
        foreach ($employees as $employee) {
            if ($employeeObject = $this->createObjectModel('Employee', $employee['id_employee'])) {
                $employeeObject->id_profile = $employee['id_profile'];
                $employeeObject->id_lang = self::getLanguageID($employee['id_lang']);
                $employeeObject->lastname = $employee['lastname'];
                $employeeObject->firstname = $employee['firstname'];
                $employeeObject->email = $employee['email'];
                $employeeObject->passwd = $employee['passwd'];
                $employeeObject->last_passwd_gen = $employee['last_passwd_gen'];
                $employeeObject->stats_date_from = $employee['stats_date_from'];
                $employeeObject->stats_date_to = $employee['stats_date_to'];
                $employeeObject->bo_color = $employee['bo_color'];
                $employeeObject->bo_theme = $employee['bo_theme'];
                $employeeObject->default_tab = $employee['default_tab'];
                $employeeObject->bo_width = $employee['bo_width'];
                $employeeObject->active = $employee['active'];
                $employeeObject->id_last_order = self::getLocalID('order', $employee['id_last_order'], 'data');
                $employeeObject->id_last_customer_message = self::getLocalID('customerMessage', $employee['id_last_customer_message'], 'data');
                $employeeObject->id_last_customer = self::getLocalID('customer', $employee['id_last_customer'], 'data');
                $employeeObject->preselect_date_range = $employee['preselect_date_range'];
                $employeeObject->bo_css = $employee['bo_css'];
                $employeeObject->bo_menu = $employee['bo_menu'];
                $employeeObject->optin = $employee['optin'];
                $employeeObject->last_connection_date = $employee['last_connection_date'];

                // Add to _shop relations
                $employeesShopsRelations = $this->getChangedIdShop($employeesShop, 'id_employee');
                if (array_key_exists($employee['id_employee'], $employeesShopsRelations)) {
                    $employeeObject->id_shop_list = array_values($employeesShopsRelations[$employee['id_employee']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($employeeObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($employeeObject->id && Employee::existsInDatabase($employeeObject->id, 'employee')) {
                        try {
                            $res = $employeeObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $employeeObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Employee (ID: %1$s) can not be saved. %2$s')), (isset($employee['id_employee']) && !self::isEmpty($employee['id_employee'])) ? Tools::safeOutput($employee['id_employee']) : 'No ID', $err_tmp), 'Employee');
                    } else {
                        $url = $this->url . $this->image_path . $employee['id_employee'] . '.jpg';
                        if (self::imageExits($url) && !(EDImport::copyImg($employeeObject->id, null, $url, 'employees', $this->regenerate))) {
                            $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'Employee', true);
                        }

                        self::addLog('Employee', $employee['id_employee'], $employeeObject->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Employee');
                }
            }
        }
        $this->updateProcess(count($employees));
    }

    /**
     * @param $customers
     * @param $addresses
     * @param $carts
     * @param $cartProducts
     * @param $countryState
     */
    public function customers($customers, $addresses, $carts, $cartProductCartRules, $countryState, $cartRules, $cartRuleAdditionalSecond, $cartRuleCountries, $cartRuleProductRules, $cartRuleProductRuleValues, $importCartRules)
    {
        foreach ($customers as $customer) {
            if ($customerObject = $this->createObjectModel('Customer', $customer['id_customer'])) {
                $customerObject->secure_key = $customer['secure_key'];
                $customerObject->lastname = $customer['lastname'];
                $customerObject->firstname = $customer['firstname'];
                $customerObject->email = $customer['email'];
                $customerObject->passwd = $customer['passwd'];
                $customerObject->last_passwd_gen = $customer['last_passwd_gen'];
                $customerObject->id_gender = $customer['id_gender'];
                $customerObject->birthday = $customer['birthday'];
                $customerObject->newsletter = $customer['newsletter'];
                $customerObject->newsletter_date_add = $customer['newsletter_date_add'];
                $customerObject->optin = $customer['optin'];
                $customerObject->active = $customer['active'];
                $customerObject->deleted = $customer['deleted'];
                $customerObject->note = $customer['note'];
                $customerObject->is_guest = $customer['is_guest'];
                $customerObject->id_default_group = self::getCustomerGroupID($customer['id_default_group']);
                $customerObject->date_add = $customer['date_add'];
                $customerObject->date_upd = $customer['date_upd'];
                if ($this->version >= 1.5) {
                    $customerObject->ip_registration_newsletter = $customer['ip_registration_newsletter'];
                    $customerObject->website = $customer['website'];
                    $customerObject->company = $customer['company'];
                    $customerObject->siret = $customer['siret'];
                    $customerObject->ape = $customer['ape'];
                    $customerObject->outstanding_allow_amount = $customer['outstanding_allow_amount'];
                    $customerObject->show_public_prices = $customer['show_public_prices'];
                    $customerObject->id_risk = $customer['id_risk'];
                    $customerObject->max_payment_days = $customer['max_payment_days'];
                    $customerObject->id_shop = self::getShopID($customer['id_shop']);
                    $customerObject->id_shop_group = $customer['id_shop_group'];
                    $customerObject->id_lang = self::getLanguageID($customer['id_lang']);
                    $customerObject->reset_password_token = $customer['reset_password_token'];
                    $customerObject->reset_password_validity = $customer['reset_password_validity'];
                }
                Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);
                $res = false;
                $err_tmp = '';

                $this->validator->setObject($customerObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($customerObject->id && Customer::existsInDatabase($customerObject->id, 'customer')) {
                        try {
                            $res = $customerObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $customerObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Customer (ID: %1$s) can not be saved. %2$s')), (isset($customer['id_customer']) && !self::isEmpty($customer['id_customer'])) ? Tools::safeOutput($customer['id_customer']) : 'No ID', $err_tmp), 'Customer');
                    } else {
//                      import customer_groups
                        $sql_values = array();
                        foreach ($addresses['customer_group'] as $customerGroup) {
                            if ($customer['id_customer'] == $customerGroup['id_customer']) {
                                $sql_values[] = '(' . (int)$customerObject->id . ', ' . self::getCustomerGroupID($customerGroup['id_group']) . ')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'customer_group` (`id_customer`, `id_group`) VALUES ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add customer_group. ' . Db::getInstance()->getMsgError()), 'Customer');
                            }
                        }
                        // Import Country
                        foreach ($countryState['countries'] as $country) {
                            if ($countryModel = $this->createObjectModel('Country', $country['id_country'])) {
                                $countryModel->id_zone = $country['id_zone'];
                                $countryModel->id_currency = self::getCurrencyID($country['id_currency']);
                                $countryModel->call_prefix = $country['call_prefix'];
                                $countryModel->iso_code = $country['iso_code'];
                                $countryModel->active = $country['active'];
                                $countryModel->contains_states = $country['contains_states'];
                                $countryModel->need_identification_number = $country['need_identification_number'];
                                $countryModel->need_zip_code = $country['need_zip_code'];
                                $countryModel->zip_code_format = $country['zip_code_format'];
                                $countryModel->display_tax_label = (isset($country['display_tax_label'])) ? (bool)$country['display_tax_label'] : true;

                                // Add to _shop relations
                                $countriesShopsRelations = $this->getChangedIdShop($countryState['country_shop'], 'id_country');
                                if (array_key_exists($country['id_country'], $countriesShopsRelations)) {
                                    $countryModel->id_shop_list = array_values($countriesShopsRelations[$country['id_country']]);
                                }


                                // Language fields
                                foreach ($countryState['country_lang'] as $lang) {
                                    if ($lang['id_country'] == $country['id_country']) {
                                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                        $countryModel->name[$lang['id_lang']] = $lang['name'];
                                    }
                                }

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($countryModel);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($countryModel->id && Country::existsInDatabase($countryModel->id, 'country')) {
                                        try {
                                            $res = $countryModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $countryModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $err_tmp), 'Country');
                                    } else {
                                        self::addLog('Country', $country['id_country'], $countryModel->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'Country');
                                }
                            }
                        }
                        // Import State
                        foreach ($countryState['states'] as $state) {
//                            if ($state['id_state'] == $address['id_state']) {
                            if ($stateModel = $this->createObjectModel('State', $state['id_state'])) {
                                $stateModel->id_country = self::getLocalId('country', $state['id_country'], 'data');
                                $stateModel->id_zone = $state['id_zone'];
                                $stateModel->iso_code = $state['iso_code'];
                                $stateModel->active = $state['active'];
                                $stateModel->name = $state['name'];


                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($stateModel);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($stateModel->id && State::existsInDatabase($stateModel->id, 'state')) {
                                        try {
                                            $res = $stateModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $stateModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('State (ID: %1$s) can not be saved. %2$s')), (isset($state['id_state']) && !self::isEmpty($state['id_state'])) ? Tools::safeOutput($state['id_state']) : 'No ID', $err_tmp), 'State');
                                    } else {
                                        self::addLog('State', $state['id_state'], $stateModel->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'State');
                                }
//                                }
                            }
                        }
                        // Import Address
                        foreach ($addresses['address'] as $address) {
                            if ($address['id_customer'] == $customer['id_customer']) {
                                if ($addressObject = $this->createObjectModel('Address', $address['id_address'])) {
                                    $addressObject->id_customer = $customerObject->id;
                                    $addressObject->id_manufacturer = self::getLocalId('manufacturer', $address['id_manufacturer'], 'data');
                                    $addressObject->id_supplier = self::getLocalId('supplier', $address['id_supplier'], 'data');
                                    $addressObject->id_country = self::getLocalId('country', $address['id_country'], 'data');
                                    $addressObject->id_state = self::getLocalId('state', $address['id_state'], 'data');
                                    $addressObject->alias = $address['alias'];
                                    $addressObject->company = $address['company'];
                                    $addressObject->lastname = $address['lastname'];
                                    $addressObject->firstname = $address['firstname'];
                                    $addressObject->vat_number = $address['vat_number'];
                                    $addressObject->address1 = $address['address1'];
                                    $addressObject->address2 = $address['address2'];
                                    $addressObject->postcode = $address['postcode'];
                                    $addressObject->city = $address['city'];
                                    $addressObject->other = $address['other'];
                                    $addressObject->phone = $address['phone'];
                                    $addressObject->phone_mobile = $address['phone_mobile'];
                                    $addressObject->dni = $address['dni'];
                                    $addressObject->deleted = $address['deleted'];
                                    $addressObject->date_add = $address['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $address['date_add'];
                                    $addressObject->date_upd = $address['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $address['date_upd'];
//                                    if ($this->version >= 1.5) {
                                    $addressObject->id_warehouse = (isset($address['id_warehouse']) && !self::isEmpty($address['id_warehouse'])) ? $address['id_warehouse'] : null;
//                                    }

                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($addressObject);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($addressObject->id && Address::existsInDatabase($addressObject->id, 'address')) {
                                            try {
                                                $res = $addressObject->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $addressObject->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Address (ID: %1$s) can not be saved. %2$s')), (isset($address['id_address']) && !self::isEmpty($address['id_address'])) ? Tools::safeOutput($address['id_address']) : 'No ID', $err_tmp), 'Address');
                                        } else {
                                            self::addLog('Address', $address['id_address'], $addressObject->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'Address');
                                    }
                                }
                            }
                        }
                        if (count($this->error_msg) == 0) {
                            self::addLog('Customer', $customer['id_customer'], $customerObject->id);
                            MigrationProPassLog::storeCustomerPass($customerObject->id, $customer['email'], $customer['passwd']);
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Customer');
                }
            }
        }
        // Import Cart Rule
        if ($importCartRules) {
            foreach ($cartRules as $cartRule) {
                if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                    $cartRuleid = $cartRule['id_discount'];
                } else {
                    $cartRuleid = $cartRule['id_cart_rule'];
                }
                if ($cartRuleObj = $this->createObjectModel('CartRule', $cartRuleid)) {
                    $cartRuleObj->id_customer = self::getLocalID('customer', $cartRule['id_customer'], 'data');
                    $cartRuleObj->date_from = $cartRule['date_from'];
                    $cartRuleObj->date_to = $cartRule['date_to'];
                    $cartRuleObj->description = isset($cartRule['description']) ? $cartRule['description'] : '';
                    $cartRuleObj->quantity = $cartRule['quantity'];
                    $cartRuleObj->quantity_per_user = $cartRule['quantity_per_user'];
                    $cartRuleObj->priority = isset($cartRule['priority']) ? $cartRule['priority'] : 1;
                    $cartRuleObj->partial_use = isset($cartRule['partial_use']) ? $cartRule['partial_use'] : 0;
                    $cartRuleObj->code = isset($cartRule['code']) ? $cartRule['code'] : 0;
                    $cartRuleObj->minimum_amount = isset($cartRule['minimum_amount']) ? $cartRule['minimum_amount'] : 0;
                    $cartRuleObj->minimum_amount_tax = isset($cartRule['minimum_amount_tax']) ? $cartRule['minimum_amount_tax'] : 0;
                    $cartRuleObj->minimum_amount_currency = isset($cartRule['minimum_amount_currency']) ? self::getCurrencyID($cartRule['minimum_amount_currency']) : 0;
                    $cartRuleObj->minimum_amount_shipping = isset($cartRule['minimum_amount_shipping']) ? $cartRule['minimum_amount_shipping'] : 0;
                    $cartRuleObj->country_restriction = isset($cartRule['country_restriction']) ? $cartRule['country_restriction'] : 0;
                    $cartRuleObj->carrier_restriction = isset($cartRule['carrier_restriction']) ? $cartRule['carrier_restriction'] : 0;
                    $cartRuleObj->group_restriction = isset($cartRule['group_restriction']) ? $cartRule['group_restriction'] : 0;
                    $cartRuleObj->cart_rule_restriction = isset($cartRule['cart_rule_restriction']) ? $cartRule['cart_rule_restriction'] : 0;
                    $cartRuleObj->product_restriction = isset($cartRule['product_restriction']) ? $cartRule['product_restriction'] : 0;
                    $cartRuleObj->shop_restriction = isset($cartRule['shop_restriction']) ? $cartRule['shop_restriction'] : 0;
                    $cartRuleObj->free_shipping = isset($cartRule['free_shipping']) ? $cartRule['free_shipping'] : 0;
                    $cartRuleObj->reduction_percent = isset($cartRule['reduction_percent']) ? $cartRule['reduction_percent'] : 0;
                    $cartRuleObj->reduction_amount = isset($cartRule['reduction_amount']) ? $cartRule['reduction_amount'] : 0;
                    $cartRuleObj->reduction_tax = isset($cartRule['reduction_tax']) ? $cartRule['reduction_tax'] : 0;
                    $cartRuleObj->reduction_currency = isset($cartRule['reduction_currency']) ? self::getCurrencyID($cartRule['reduction_currency']) : 0;
                    $cartRuleObj->reduction_product = isset($cartRule['reduction_product']) ? self::getLocalID('product', $cartRule['reduction_product'], 'data') : 0;
                    $cartRuleObj->gift_product = isset($cartRule['gift_product']) ? self::getLocalID('product', $cartRule['gift_product'], 'data') : 0;
                    $cartRuleObj->gift_product_attribute = isset($cartRule['gift_product_attribute']) ? self::getLocalID('combination', $cartRule['gift_product_attribute'], 'data') : 0;
                    $cartRuleObj->highlight = isset($cartRule['highlight']) ? $cartRule['highlight'] : 0;
                    $cartRuleObj->active = isset($cartRule['active']) ? $cartRule['active'] : 0;
                    $cartRuleObj->date_add = $cartRule['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $cartRule['date_add'];
                    $cartRuleObj->date_upd = $cartRule['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $cartRule['date_upd'];
                    if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                        foreach ($cartRuleAdditionalSecond['cart_rule_langs'] as $lang) {
                            if ($lang['id_discount'] == $cartRule['id_discount']) {
                                $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                $cartRuleObj->name[$lang['id_lang']] = $lang['description'];
                            }
                        }
                    } else {
                        foreach ($cartRuleAdditionalSecond['cart_rule_langs'] as $lang) {
                            if ($lang['id_cart_rule'] == $cartRule['id_cart_rule']) {
                                $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                $cartRuleObj->name[$lang['id_lang']] = $lang['name'];
                            }
                        }
                    }

                    if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                        $cartRuleObj->priority = 1;
                        $cartRuleObj->partial_use = 1;
                        $cartRuleObj->code = $cartRule['name'];
                        $cartRuleObj->minimum_amount = $cartRule['minimal'];
                        $cartRuleObj->minimum_amount_tax = 0;
                        $cartRuleObj->minimum_amount_currency = Configuration::get('PS_CURRENCY_DEFAULT');
                        $cartRuleObj->minimum_amount_shipping = 0;
                        $cartRuleObj->country_restriction = 0;
                        $cartRuleObj->carrier_restriction = 0;
                        $cartRuleObj->group_restriction = 0;
                        $cartRuleObj->cart_rule_restriction = 0;
                        $cartRuleObj->product_restriction = 0;
                        $cartRuleObj->shop_restriction = 0;
                        $cartRuleObj->free_shipping = 0;
                        $cartRuleObj->reduction_percent = 0;
                        $cartRuleObj->reduction_amount = 0;
                        $cartRuleObj->reduction_tax = $cartRule['include_tax'];
                        if ($cartRule['id_discount_type'] == 3) {
                            $cartRuleObj->free_shipping = 1;
                        } elseif ($cartRule['id_discount_type'] == 1) {
                            $cartRuleObj->reduction_percent = $cartRule['value'];
                        } elseif ($cartRule['id_discount_type'] == 2) {
                            $cartRuleObj->reduction_amount = $cartRule['value'];
                        }
                        if ($cartRule['id_discount_type'] == 2 && $cartRule['include_tax'] != 0) {
                        } else {
                            $cartRuleObj->reduction_tax = 0;
                        }
                        $cartRuleObj->reduction_currency = self::getCurrencyID($cartRule['id_currency']);
                        $cartRuleObj->reduction_product = 0;
                        $cartRuleObj->gift_product = 0;
                        $cartRuleObj->gift_product_attribute = 0;
                        $cartRuleObj->highlight = 0;
                    }

                    $res = false;
                    $err_tmp = '';

                    $this->validator->setObject($cartRuleObj);
                    $this->validator->checkFields();
                    $error_tmp = $this->validator->getValidationMessages();
                    if (self::isEmpty($error_tmp)) {
                        if ($cartRuleObj->id && CartRule::existsInDatabase($cartRuleObj->id, 'cart_rule')) {
                            try {
                                $res = $cartRuleObj->update();
                            } catch (PrestaShopException $e) {
                                $err_tmp = $e->getMessage();
                            }
                        }

                        if (!$res) {
                            try {
                                $res = $cartRuleObj->add(false);
                            } catch (PrestaShopException $e) {
                                $err_tmp = $e->getMessage();
                            }
                        }
                        if (!$res) {
                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Cart rule (ID: %1$s) can not be saved. %2$s')), (isset($cartRuleid) && !self::isEmpty($cartRuleid)) ? Tools::safeOutput($cartRuleid) : 'No ID', $err_tmp), 'CartRule');
                        } else {
                            // Import Cart Rule Carrier
                            $sql_values = array();
                            foreach ($cartRuleAdditionalSecond['cart_rule_carriers'] as $cartRuleCarrier) {
                                if ($cartRuleCarrier['id_cart_rule'] == $cartRuleid) {
                                    $sql_values[] = '(' . (int)$cartRuleObj->id . ', ' . self::getLocalID('carrier', $cartRuleCarrier['id_carrier'], 'data') . ')';
                                }
                            }
                            if (!self::isEmpty($sql_values)) {
                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_carrier` (`id_cart_rule`, `id_carrier`) VALUES ' . implode(',', $sql_values));
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_carrier. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                }
                            }

                            // Import Cart Rule Country
                            $sql_values = array();
                            foreach ($cartRuleAdditionalSecond['cart_rule_countries'] as $cartRuleCountry) {
                                if ($cartRuleCountry['id_cart_rule'] == $cartRuleid) {
                                    // Import Country
                                    foreach ($cartRuleCountries['country'] as $country) {
                                        if ($country['id_country'] == $cartRuleCountry['id_country']) {
                                            if ($countryModel = $this->createObjectModel('Country', $country['id_country'])) {
                                                $countryModel->id_zone = $country['id_zone'];
                                                $countryModel->id_currency = self::getCurrencyID($country['id_currency']);
                                                $countryModel->call_prefix = $country['call_prefix'];
                                                $countryModel->iso_code = $country['iso_code'];
                                                $countryModel->active = $country['active'];
                                                $countryModel->contains_states = $country['contains_states'];
                                                $countryModel->need_identification_number = $country['need_identification_number'];
                                                $countryModel->need_zip_code = $country['need_zip_code'];
                                                $countryModel->zip_code_format = $country['zip_code_format'];
                                                $countryModel->display_tax_label = (isset($country['display_tax_label'])) ? (bool)$country['display_tax_label'] : true;

                                                // Add to _shop relations
                                                $countriesShopsRelations = $this->getChangedIdShop($cartRuleCountries['country_shop'], 'id_country');
                                                if (array_key_exists($country['id_country'], $countriesShopsRelations)) {
                                                    $countryModel->id_shop_list = array_values($countriesShopsRelations[$country['id_country']]);
                                                }


                                                //language fields
                                                foreach ($cartRuleCountries['country_lang'] as $lang) {
                                                    if ($lang['id_country'] == $country['id_country']) {
                                                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                                        $countryModel->name[$lang['id_lang']] = $lang['name'];
                                                    }
                                                }

                                                $res = false;
                                                $err_tmp = '';

                                                $this->validator->setObject($countryModel);
                                                $this->validator->checkFields();
                                                $error_tmp = $this->validator->getValidationMessages();
                                                if (self::isEmpty($error_tmp)) {
                                                    if ($countryModel->id && Country::existsInDatabase($countryModel->id, 'country')) {
                                                        try {
                                                            $res = $countryModel->update();
                                                        } catch (PrestaShopException $e) {
                                                            $err_tmp = $e->getMessage();
                                                        }
                                                    }
                                                    if (!$res) {
                                                        try {
                                                            $res = $countryModel->add(false);
                                                        } catch (PrestaShopException $e) {
                                                            $err_tmp = $e->getMessage();
                                                        }
                                                    }

                                                    if (!$res) {
                                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $err_tmp), 'Country');
                                                    } else {
                                                        self::addLog('Country', $country['id_country'], $countryModel->id);
                                                    }
                                                } else {
                                                    $this->showMigrationMessageAndLog($error_tmp, 'Country');
                                                }
                                            }
                                        }
                                    }
                                    $sql_values[] = '(' . (int)$cartRuleObj->id . ', ' . self::getLocalID('country', $cartRuleCountry['id_country'], 'data') . ')';
                                }
                            }
                            if (!self::isEmpty($sql_values)) {
                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_country` (`id_cart_rule`, `id_country`) VALUES ' . implode(',', $sql_values));
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_country. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                }
                            }

                            // Import Cart Rule Group
                            $sql_values = array();
                            foreach ($cartRuleAdditionalSecond['cart_rule_groups'] as $cartRuleGroup) {
                                if ($cartRuleGroup['id_cart_rule'] == $cartRuleid) {
                                    $sql_values[] = '(' . (int)$cartRuleObj->id . ', ' . self::getCustomerGroupID($cartRuleGroup['id_group']) . ')';
                                }
                            }
                            if (!self::isEmpty($sql_values)) {
                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_group` (`id_cart_rule`, `id_group`) VALUES ' . implode(',', $sql_values));
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_group. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                }
                            }

                            // Import Cart Rule Product Rule Group
                            foreach ($cartRuleAdditionalSecond['cart_rule_product_rule_groups'] as $cartRuleProductRuleGroup) {
                                $sql_value = '';
                                if ($cartRuleProductRuleGroup['id_cart_rule'] == $cartRuleid) {
                                    $sql_value = '(' . (int)$cartRuleObj->id . ', ' . (int)$cartRuleProductRuleGroup['quantity'] . ')';
                                }
                                if (!self::isEmpty($sql_value)) {
                                    $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`,
                                    `quantity`)
                                VALUES ' . $sql_value);
                                    if (!$result) {
                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_product_rule_group. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                    } else {
                                        $id_product_rule_group = Db::getInstance()->Insert_ID();

                                        // Import Cart Rule Product Rule
                                        foreach ($cartRuleProductRules as $cartRuleProductRule) {
                                            $sql_value = '';
                                            if ($cartRuleProductRule['id_product_rule_group'] == $cartRuleProductRuleGroup['id_product_rule_group']) {
                                                $sql_value = '(' . (int)$id_product_rule_group . ', \'' . pSQL($cartRuleProductRule['type']) . '\')';
                                                if (!self::isEmpty($sql_value)) {
                                                    $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`)
                                VALUES ' . $sql_value);
                                                    if (!$result) {
                                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_product_rule. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                                    } else {
                                                        $id_product_rule = Db::getInstance()->Insert_ID();

                                                        // Import Cart Rule Product Rule Value
                                                        $sql_values = array();
                                                        foreach ($cartRuleProductRuleValues as $cartRuleProductRuleValue) {
                                                            if ($cartRuleProductRuleValue['id_product_rule'] == $cartRuleProductRule['id_product_rule']) {
                                                                $id_item = 0;
                                                                if (preg_match('|products|', $cartRuleProductRule['type'])) {
                                                                    $id_item = self::getLocalID('product', $cartRuleProductRuleValue['id_item'], 'data');
                                                                } elseif (preg_match('|attributes|', $cartRuleProductRule['type'])) {
                                                                    $id_item = self::getLocalID('attribute', $cartRuleProductRuleValue['id_item'], 'data');
                                                                } elseif (preg_match('|categories|', $cartRuleProductRule['type'])) {
                                                                    $id_item = self::getLocalID('category', $cartRuleProductRuleValue['id_item'], 'data');
                                                                } elseif (preg_match('|manufacturers|', $cartRuleProductRule['type'])) {
                                                                    $id_item = self::getLocalID('manufacturer', $cartRuleProductRuleValue['id_item'], 'data');
                                                                } elseif (preg_match('|suppliers|', $cartRuleProductRule['type'])) {
                                                                    $id_item = self::getLocalID('supplier', $cartRuleProductRuleValue['id_item'], 'data');
                                                                }
                                                                $sql_values[] = '(' . (int)$id_product_rule . ', ' . (int)$id_item . ')';
                                                            }
                                                            if (!self::isEmpty($sql_values)) {
                                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`)
                                VALUES ' . implode(',', $sql_values));
                                                                if (!$result) {
                                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_product_rule_value. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                                                }
                                                            }
                                                        }
                                                        self::addLog('CARTRULEPRODUCTRULE', $cartRuleProductRule['id_product_rule'], $id_product_rule);
                                                    }
                                                }
                                            }
                                        }

                                        self::addLog('CARTRULEPRODUCTRULEGROUP', $cartRuleProductRuleGroup['id_product_rule_group'], $id_product_rule_group);
                                    }
                                }
                            }

                            // Import Cart Rule Shop
                            $sql_values = array();
                            foreach ($cartRuleAdditionalSecond['cart_rule_shops'] as $cartRuleShop) {
                                if ($cartRuleShop['id_cart_rule'] == $cartRuleid) {
                                    $sql_values[] = '(' . (int)$cartRuleObj->id . ', ' . self::getShopID($cartRuleShop['id_shop']) . ')';
                                }
                            }
                            if (!self::isEmpty($sql_values)) {
                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_shop` (`id_cart_rule`, `id_shop`) VALUES ' . implode(',', $sql_values));
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_shop. ' . Db::getInstance()->getMsgError()), 'CartRule');
                                }
                            }

                            if (count($this->error_msg) == 0) {
                                self::addLog('CartRule', $cartRuleid, $cartRuleObj->id);
                            }
                        }
                    } else {
                        $this->showMigrationMessageAndLog($error_tmp, 'CartRule');
                    }
                }
            }

            // Import Cart Rule Combination
            $sql_values = array();
            foreach ($cartRuleAdditionalSecond['cart_rule_combinations'] as $cartRuleCombination) {
                $sql_values[] = '(' . self::getLocalID('cartRule', $cartRuleCombination['id_cart_rule_1'], 'data') . ',
                        ' . self::getLocalID('cartRule', $cartRuleCombination['id_cart_rule_2'], 'data') . ')';
            }
            if (!self::isEmpty($sql_values)) {
                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) VALUES ' . implode(',', $sql_values));
                if (!$result) {
                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_rule_combination. ' . Db::getInstance()->getMsgError()), 'CartRule');
                }
            }
        }
        // Import Cart
        foreach ($carts as $cart) {
            if ($cartObject = $this->createObjectModel('Cart', $cart['id_cart'])) {
                $cartObject->id_shop = self::getShopID($cart['id_shop']);
                $cartObject->id_shop_group = Shop::getGroupFromShop($cartObject->id_shop);
                $cartObject->id_carrier = $cart['id_carrier'];   //  fix it after carrier import
                $cartObject->delivery_option = $cart['delivery_option'];
                $cartObject->id_lang = self::getLanguageID($cart['id_lang']);
                $cartObject->id_address_delivery = self::getLocalID('address', $cart['id_address_delivery'], 'data');
                $cartObject->id_address_invoice = self::getLocalID('address', $cart['id_address_invoice'], 'data');
                $cartObject->id_currency = self::getCurrencyID($cart['id_currency']);
                $cartObject->id_customer = self::getLocalID('customer', $cart['id_customer'], 'data');
                $cartObject->id_guest = $cart['id_guest'];
                if (!self::isEmpty($cartObject->id_customer)) {
                    $customerObj = new Customer($cartObject->id_customer);
                    $cartObject->secure_key = $customerObj->secure_key;
                } else {
                    if (!self::isEmpty($cart['secure_key'])) {
                        $cartObject->secure_key = $cart['secure_key'];
                    } else {
                        $cartObject->secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
                        $this->showMigrationMessageAndLog('Secure key of cart with ID ' . $cart['id_cart'] ? $cart['id_cart'] : 'No ID' . ' is empty. For that reason, the module set default value as a secure key.', 'Cart', true);
                    }
                }
                $cartObject->recyclable = $cart['recyclable'];
                $cartObject->gift = $cart['gift'];
                $cartObject->gift_message = $cart['gift_message'];
                $cartObject->mobile_theme = isset($cart['mobile_theme']) ? $cart['mobile_theme'] : null;
                $cartObject->allow_seperated_package = isset($cart['allow_seperated_package ']) ? $cart['allow_seperated_package '] : null;
                $cartObject->date_add = $cart['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $cart['date_add'];
                $cartObject->date_upd = $cart['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $cart['date_upd'];

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($cartObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($cartObject->id && Cart::existsInDatabase($cartObject->id, 'cart')) {
                        try {
                            $res = $cartObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $cartObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Cart (ID: %1$s) can not be saved. %2$s')), (isset($cart['id_cart']) && !self::isEmpty($cart['id_cart'])) ? Tools::safeOutput($cart['id_cart']) : 'No ID', $err_tmp), 'Cart');
                    } else {
                        // Import Cart Product
                        $sql_values = array();
                        foreach ($cartProductCartRules['cart_product'] as $cartProduct) {
                            if ($cartProduct['id_cart'] == $cart['id_cart']) {
                                if (!self::isEmpty($cartProduct['id_shop'])) {
                                    $shopIdOfCartProduct = self::getShopID($cartProduct['id_shop']);
                                } else {
                                    $shopIdOfCartProduct = (int)Configuration::get('PS_SHOP_DEFAULT');
                                }
                                $sql_values[] = '(' . (int)$cartObject->id . ', ' . self::getLocalID('product', $cartProduct['id_product'], 'data') . ', ' . self::getLocalID('address', $cartProduct['id_address_delivery'], 'data') . ', ' . $shopIdOfCartProduct . ', ' . self::getLocalID('combination', $cartProduct['id_product_attribute'], 'data') . ' , ' . (int)$cartProduct['quantity'] . ', \'' . pSQL($cartProduct['date_add']) . '\')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_product` (`id_cart`, `id_product`,
                                                    `id_address_delivery`, `id_shop`, `id_product_attribute`,
                                                    `quantity`, `date_add`)
                                                    VALUES ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_product. ' . Db::getInstance()->getMsgError()), 'Cart');
                            }
                        }

                        // Import Cart Cart_Rule
                        $sql_values = array();
                        foreach ($cartProductCartRules['cart_cart_rule'] as $cartCartRule) {
                            if ($cartCartRule['id_cart'] == $cart['id_cart']) {
                                $sql_values[] = '(' . (int)$cartObject->id . ', ' . self::getLocalID('cartRule', $cartCartRule['id_cart_rule'], 'data') . ')';
                            }
                        }
                        if (!self::isEmpty($sql_values)) {
                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cart_cart_rule` (`id_cart`, `id_cart_rule`)
                                                    VALUES ' . implode(',', $sql_values));
                            if (!$result) {
                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add cart_cart_rule. ' . Db::getInstance()->getMsgError()), 'Cart');
                            }
                        }
                        if (count($this->error_msg) == 0) {
                            self::addLog('Cart', $cart['id_cart'], $cartObject->id);
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Cart');
                }
            }
        }
        $this->updateProcess(count($customers));
    }

    /**
     * @param $orders
     * @param $ordersAdditionalSecond
     * @param $ordersAdditionalThird
     */
    public function orders($orders, $ordersAdditionalSecond, $ordersAdditionalThird)
    {
        $isMigrateRecentData = (int)ConfigurationCore::get('_migrate_recent_data');
        foreach ($orders as $order) {
            if ($orderModel = $this->createObjectModel('Order', $order['id_order'], 'orders')) {
                $orderModel->id_address_delivery = $order['id_address_delivery'];
                $orderModel->id_address_invoice = $order['id_address_invoice'];
                $orderModel->id_cart = $order['id_cart'];
                $orderModel->id_currency = self::getCurrencyID($order['id_currency']);
                $orderModel->id_lang = self::getLanguageID($order['id_lang']);
                $orderModel->id_customer = self::getLocalId('customer', $order['id_customer'], 'data');
                $orderModel->id_carrier = $order['id_carrier'];
                if (!self::isEmpty($orderModel->id_customer)) {
                    $customerObj = new Customer($orderModel->id_customer);
                    $orderModel->secure_key = $customerObj->secure_key;
                } else {
                    if (!self::isEmpty($order['secure_key'])) {
                        $orderModel->secure_key = $order['secure_key'];
                    } else {
                        $orderModel->secure_key = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
                        $this->showMigrationMessageAndLog('Secure key of order with ID ' . $order['id_order'] ? $order['id_order'] : 'No ID' . ' is empty. For that reason, the module set default value as a secure key.', 'Order', true);
                    }
                }
                $orderModel->payment = $order['payment'];
                $orderModel->module = (self::isEmpty($order['module'])) || !Validate::isModuleName($order['module']) ? 'cheque' : $order['module'];
                $orderModel->recyclable = $order['recyclable'];
                $orderModel->gift = $order['gift'];
                $orderModel->gift_message = $order['gift_message'];
                $orderModel->total_discounts = $order['total_discounts'];
                $orderModel->total_paid = $order['total_paid'];
                $orderModel->total_paid_real = $order['total_paid_real'];
                $orderModel->total_products = $order['total_products'];
                $orderModel->total_products_wt = $order['total_products_wt'];
                $orderModel->total_shipping = $order['total_shipping'];
                $orderModel->carrier_tax_rate = $order['carrier_tax_rate'];
                $orderModel->total_wrapping = $order['total_wrapping'];
                $orderModel->shipping_number = Validate::isTrackingNumber($order['shipping_number']) ? $order['shipping_number'] : 0;
                $orderModel->conversion_rate = self::defaultValue($order['conversion_rate'], 0);
                $orderModel->invoice_number = $order['invoice_number'];
                $orderModel->delivery_number = $order['delivery_number'];
                $orderModel->invoice_date = $order['invoice_date'];
                $orderModel->delivery_date = $order['delivery_date'];
                $orderModel->valid = $order['valid'];
                $orderModel->date_add = $order['date_add'];
                $orderModel->date_upd = $order['date_upd'];
                $orderTaxRate = 0;
                $taxName = '';
                foreach ($ordersAdditionalSecond['order_detail'] as $orderDetail) {
                    if ($orderDetail['id_order'] == $order['id_order']) {
                        $orderTaxRate = $orderDetail['tax_rate'];
                        $taxName = $orderDetail['tax_name'];
                        break;
                    }
                }
                if ($this->version >= 1.5) {
                    $orderModel->id_shop = self::getShopID($order['id_shop']);
                    $orderModel->id_shop_group = Shop::getGroupFromShop($orderModel->id_shop);
                    $orderModel->current_state = $order['current_state'];
                    $orderModel->mobile_theme = $order['mobile_theme'];
                    $orderModel->total_discounts_tax_incl = (float)Tools::ps_round($order['total_discounts_tax_incl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_discounts_tax_excl = (float)Tools::ps_round($order['total_discounts_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_paid_tax_incl = (float)Tools::ps_round($order['total_paid_tax_incl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_paid_tax_excl = (float)Tools::ps_round($order['total_paid_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_shipping_tax_incl = (float)Tools::ps_round($order['total_shipping_tax_incl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_shipping_tax_excl = (float)Tools::ps_round($order['total_shipping_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_wrapping_tax_incl = (float)Tools::ps_round($order['total_wrapping_tax_incl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->total_wrapping_tax_excl = (float)Tools::ps_round($order['total_wrapping_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
                    $orderModel->round_mode = $order['round_mode'];
                    $orderModel->round_type = $order['round_type'];
                    $orderModel->reference = $order['reference'];
                } else {
                    $orderModel->total_discounts_tax_incl = $orderModel->total_discounts;
                    $orderModel->total_discounts_tax_excl = $orderModel->total_discounts - ($orderTaxRate * $orderModel->total_discounts)/100;
                    $orderModel->reference = Order::generateReference();
                    $orderModel->id_shop = (int)$this->mapping['multi_shops'];
                    $current_shop = Shop::getShop((int)$this->mapping['multi_shops']);
                    $orderModel->id_shop_group = $current_shop['id_shop_group'];
                    $orderModel->total_shipping_tax_incl = $order['total_shipping'];
                    $total_shipping_tax_excl = $order['total_shipping'] / (1 + $order['carrier_tax_rate']/100);
                    $orderModel->total_shipping_tax_excl = (float)Tools::ps_round($total_shipping_tax_excl, _PS_PRICE_COMPUTE_PRECISION_);
                    $orderModel->total_paid_tax_incl = $order['total_products_wt'] + $orderModel->total_shipping_tax_incl - $orderModel->total_discounts;
                    $orderModel->total_paid_tax_excl = $order['total_products'] + $orderModel->total_shipping_tax_excl - $orderModel->total_discounts_tax_excl;
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($orderModel);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($orderModel->id && self::existsInDatabase($orderModel->id, 'orders', 'order')) {
                        try {
                            $res = $orderModel->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $orderModel->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order (ID: %1$s) can not be saved. %2$s')), (isset($order['id_order']) && !self::isEmpty($order['id_order'])) ? Tools::safeOutput($order['id_order']) : 'No ID', $err_tmp), 'Order');
                    } else {
                        // import Payment
                        $paymentIds = array();

                        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                            if ($orderPaymentModel = $this->createObjectModel('OrderPayment', $order['id_order'])) {
                                $orderPaymentModel->order_reference = $orderModel->reference;
                                $orderPaymentModel->id_currency = $orderModel->id_currency;
                                $orderPaymentModel->amount = $orderModel->total_paid;
                                $orderPaymentModel->payment_method = $orderModel->payment;
                                $orderPaymentModel->conversion_rate = $orderModel->conversion_rate;
                                $orderPaymentModel->transaction_id = null;
                                $orderPaymentModel->card_number = null;
                                $orderPaymentModel->card_brand = null;
                                $orderPaymentModel->card_expiration = null;
                                $orderPaymentModel->card_holder = null;
                                $orderPaymentModel->date_add = $order['invoice_date'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $order['invoice_date'];

                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($orderPaymentModel);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($orderPaymentModel->id && OrderPayment::existsInDatabase($orderPaymentModel->id, 'order_payment')) {
                                        try {
                                            $res = $orderPaymentModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $orderPaymentModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Payment (ID: %1$s) can not be saved. %2$s')), (isset($order['id_order']) && !self::isEmpty($order['id_order'])) ? Tools::safeOutput($order['id_order']) : 'No ID', $err_tmp), 'OrderPayment');
                                    } else {
                                        self::addLog('OrderPayment', $order['id_order'], $orderPaymentModel->id);
                                        $paymentIds[] = $orderPaymentModel->id;
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'OrderPayment');

                                }
                            }
                        } else {
                            foreach ($ordersAdditionalSecond['order_payment'] as $orderPayment) {
                                if ($orderPayment['order_reference'] == $order['reference']) {
                                    if ($orderPaymentModel = $this->createObjectModel('OrderPayment', $orderPayment['id_order_payment'])) {
                                        $orderPaymentModel->order_reference = isset($orderPayment['order_reference']) ? $orderPayment['order_reference'] : $order['payment'];
                                        $orderPaymentModel->id_currency = self::getCurrencyID($orderPayment['id_currency']);
                                        $orderPaymentModel->amount = $orderPayment['amount'];
                                        $orderPaymentModel->payment_method = isset($orderPayment['payment_method']) ? $orderPayment['payment_method'] : $order['payment'];
                                        $orderPaymentModel->conversion_rate = $orderModel->conversion_rate;
                                        $orderPaymentModel->transaction_id = $orderPayment['transaction_id'];
                                        $orderPaymentModel->card_number = $orderPayment['card_number'];
                                        $orderPaymentModel->card_brand = $orderPayment['card_brand'];
                                        $orderPaymentModel->card_expiration = $orderPayment['card_expiration'];
                                        $orderPaymentModel->card_holder = $orderPayment['card_holder'];
                                        $orderPaymentModel->date_add = $orderPayment['date_add'];


                                        $res = false;
                                        $err_tmp = '';

                                        $this->validator->setObject($orderPaymentModel);
                                        $this->validator->checkFields();
                                        $error_tmp = $this->validator->getValidationMessages();
                                        if (self::isEmpty($error_tmp)) {
                                            if ($orderPaymentModel->id && OrderPayment::existsInDatabase($orderPaymentModel->id, 'order_payment')) {
                                                try {
                                                    $res = $orderPaymentModel->update();
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }
                                            if (!$res) {
                                                try {
                                                    $res = $orderPaymentModel->add(false);
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }

                                            if (!$res) {
                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Payment (ID: %1$s) can not be saved. %2$s')), (isset($orderPayment['id_order_payment']) && !self::isEmpty($orderPayment['id_order_payment'])) ? Tools::safeOutput($orderPayment['id_order_payment']) : 'No ID', $err_tmp), 'OrderPayment');
                                            } else {
                                                self::addLog('OrderPayment', $orderPayment['id_order_payment'], $orderPaymentModel->id);
                                                $paymentIds[] = $orderPaymentModel->id;
                                            }
                                        } else {
                                            $this->showMigrationMessageAndLog($error_tmp, 'OrderPayment');
                                        }
                                    }
                                }
                            }
                        }
                        // import Invoice
                        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                            if ($orderInvoiceModel = $this->createObjectModel('OrderInvoice', $order['id_order'])) {
                                $orderInvoiceModel->id_order = $orderModel->id;
                                $orderInvoiceModel->number = $order['invoice_number'];
                                $orderInvoiceModel->delivery_number = $order['delivery_number'];
                                $orderInvoiceModel->delivery_date = $order['delivery_date'];
                                $orderInvoiceModel->total_discount_tax_excl = $orderModel->total_discounts_tax_excl;
                                $orderInvoiceModel->total_discount_tax_incl = $orderModel->total_discounts_tax_incl;
                                $orderInvoiceModel->total_paid_tax_excl = $orderModel->total_paid_tax_excl;
                                $orderInvoiceModel->total_paid_tax_incl = $orderModel->total_paid_tax_incl;
                                $orderInvoiceModel->total_products = $orderModel->total_products;
                                $orderInvoiceModel->total_products_wt = $orderModel->total_products_wt;
                                $orderInvoiceModel->total_shipping_tax_excl = $orderModel->total_shipping_tax_excl;
                                $orderInvoiceModel->total_shipping_tax_incl = $orderModel->total_shipping_tax_incl;
                                $orderInvoiceModel->shipping_tax_computation_method = 0;
                                $orderInvoiceModel->total_wrapping_tax_excl = $orderModel->total_wrapping_tax_excl;
                                $orderInvoiceModel->total_wrapping_tax_incl = $orderModel->total_wrapping_tax_incl;
                                $orderInvoiceModel->invoice_date = $order['invoice_date'];
                                $orderInvoiceModel->invoice_address = $order['id_address_invoice'];
                                $orderInvoiceModel->delivery_address = $order['id_address_delivery'];
                                $orderInvoiceModel->note = '';
                                $orderInvoiceModel->date_add = date('Y-m-d H:i:s');


                                $res = false;
                                $err_tmp = '';

                                $this->validator->setObject($orderInvoiceModel);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($orderInvoiceModel->id && OrderInvoice::existsInDatabase($orderInvoiceModel->id, 'order_invoice')) {
                                        try {
                                            $res = $orderInvoiceModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $orderInvoiceModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Invoice (ID: %1$s) can not be saved. %2$s')), (isset($order['id_order']) && !self::isEmpty($order['id_order'])) ? Tools::safeOutput($order['id_order']) : 'No ID', $err_tmp), 'OrderInvoice');
                                    } else {
                                        self::addLog('OrderInvoice', $order['id_order'], $orderInvoiceModel->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'OrderInvoice');
                                }
                                //import Invoice_Tax
                                $invoice_taxsql_values = array();
                                $taxId = (int)Tax::getTaxIdByName($taxName);
                                $invoice_taxsql_values[] = '(' . (int)$orderInvoiceModel->id . ', \'' . 'tax' . '\', ' . (int)$taxId . ', ' . (float)($orderModel->total_paid_tax_incl - $orderModel->total_paid_tax_excl) . ')';
                                if (!self::isEmpty($invoice_taxsql_values)) {
                                    $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'order_invoice_tax` (`id_order_invoice`, `type`, `id_tax`, `amount`) VALUES ' . implode(',', $invoice_taxsql_values));
                                    if (!$result) {
                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_invoice_tax. ' . Db::getInstance()->getMsgError()), 'OrderInvoice');
                                    }
                                }
                                //import Invoice_Payment
                                $sql_values = array();
                                foreach ($paymentIds as $invoicePaymentId) {
                                    $sql_values[] = '(' . (int)$orderInvoiceModel->id . ', ' . (int)$invoicePaymentId . ', ' . (int)$orderModel->id . ')';
                                }
                                if (!self::isEmpty($sql_values)) {
                                    $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'order_invoice_payment` (`id_order_invoice`, `id_order_payment`, `id_order`) VALUES ' . implode(',', $sql_values));

                                    if (!$result) {
                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_invoice_payment. ' . Db::getInstance()->getMsgError()), 'OrderInvoice');
                                    }
                                }
                            }
                        } else {
                            foreach ($ordersAdditionalSecond['order_invoice'] as $orderInvoice) {
                                if ($orderInvoice['id_order'] == $order['id_order']) {
                                    if ($orderInvoiceModel = $this->createObjectModel('OrderInvoice', $orderInvoice['id_order_invoice'])) {
                                        $orderInvoiceModel->id_order = $orderModel->id;
                                        $orderInvoiceModel->number = $orderModel->invoice_number;
                                        $orderInvoiceModel->delivery_number = $orderModel->delivery_number;
                                        $orderInvoiceModel->delivery_date = $orderModel->delivery_date;
                                        $orderInvoiceModel->total_discount_tax_excl = $orderModel->total_discounts_tax_excl;
                                        $orderInvoiceModel->total_discount_tax_incl = $orderModel->total_discounts_tax_incl;
                                        $orderInvoiceModel->total_paid_tax_excl = $orderModel->total_paid_tax_excl;
                                        $orderInvoiceModel->total_paid_tax_incl = $orderModel->total_paid_tax_incl;
                                        $orderInvoiceModel->total_products = $orderModel->total_products;
                                        $orderInvoiceModel->total_products_wt = $orderModel->total_products_wt;
                                        $orderInvoiceModel->total_shipping_tax_excl = $orderModel->total_shipping_tax_excl;
                                        $orderInvoiceModel->total_shipping_tax_incl = $orderModel->total_shipping_tax_incl;
                                        $orderInvoiceModel->shipping_tax_computation_method = $orderInvoice['shipping_tax_computation_method'];
                                        $orderInvoiceModel->total_wrapping_tax_excl = $orderModel->total_wrapping_tax_excl;
                                        $orderInvoiceModel->total_wrapping_tax_incl = $orderModel->total_wrapping_tax_incl;
                                        $orderInvoiceModel->invoice_date = $orderInvoice['invoice_date'];
                                        $orderInvoiceModel->invoice_address = $orderInvoice['invoice_address'];
                                        $orderInvoiceModel->delivery_address = $orderInvoice['delivery_address'];
                                        $orderInvoiceModel->note = $orderInvoice['note'];
                                        $orderInvoiceModel->date_add = $orderInvoice['date_add'];


                                        $res = false;
                                        $err_tmp = '';

                                        $this->validator->setObject($orderInvoiceModel);
                                        $this->validator->checkFields();
                                        $error_tmp = $this->validator->getValidationMessages();
                                        if (self::isEmpty($error_tmp)) {
                                            if ($orderInvoiceModel->id && OrderInvoice::existsInDatabase($orderInvoiceModel->id, 'order_invoice')) {
                                                try {
                                                    $res = $orderInvoiceModel->update();
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }
                                            if (!$res) {
                                                try {
                                                    $res = $orderInvoiceModel->add(false);
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }

                                            if (!$res) {
                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Invoice (ID: %1$s) can not be saved. %2$s')), (isset($orderInvoice['id_order_invoice']) && !self::isEmpty($orderInvoice['id_order_invoice'])) ? Tools::safeOutput($orderInvoice['id_order_invoice']) : 'No ID', $err_tmp), 'OrderInvoice');
                                            } else {
                                                self::addLog('OrderInvoice', $orderInvoice['id_order_invoice'], $orderInvoiceModel->id);
                                            }
                                        } else {
                                            $this->showMigrationMessageAndLog($error_tmp, 'OrderInvoice');
                                        }
                                        //import Invoice_Tax
                                        $sql_values = array();
                                        foreach ($ordersAdditionalThird['invoice_tax'] as $invoiceTax) {
                                            if ($invoiceTax['id_order_invoice'] == $orderInvoice['id_order_invoice']) {
                                                $sql_values[] = '(' . (int)$orderInvoiceModel->id . ', "' . pSQL($invoiceTax['type']) . '", ' . self::getLocalID('tax', $invoiceTax['id_tax'], 'data') . ',' . (float)$invoiceTax['amount'] . ')';
                                            }
                                        }
                                        if (!self::isEmpty($sql_values)) {
                                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'order_invoice_tax` (`id_order_invoice`, `type`, `id_tax`, `amount`) VALUES ' . implode(',', $sql_values));
                                            if (!$result) {
                                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_invoice_tax. ' . Db::getInstance()->getMsgError()), 'OrderInvoice');
                                            }
                                        }
                                        //import Invoice_Payment
                                        $sql_values = array();
                                        foreach ($ordersAdditionalSecond['invoice_payment'] as $invoicePayment) {
                                            if ($invoicePayment['id_order'] == $order['id_order']) {
                                                $sql_values[] = '(' . (int)$orderInvoiceModel->id . ', ' . self::getLocalID('orderpayment', (int)$invoicePayment['id_order_payment'], 'data') . ', ' . (int)$orderModel->id . ')';
                                            }
                                        }
                                        if (!self::isEmpty($sql_values)) {
                                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'order_invoice_payment` (`id_order_invoice`, `id_order_payment`, `id_order`) VALUES ' . implode(',', $sql_values));

                                            if (!$result) {
                                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_invoice_payment. ' . Db::getInstance()->getMsgError()), 'OrderInvoice');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // import Order Detail
                        foreach ($ordersAdditionalSecond['order_detail'] as $orderDetail) {
                            if ($orderDetail['id_order'] == $order['id_order']) {
                                if ($orderDetailModel = $this->createObjectModel('OrderDetail', $orderDetail['id_order_detail'])) {
                                    $orderDetailModel->id_order = $orderModel->id;
                                    $orderDetailModel->product_id = self::getLocalID('product', $orderDetail['product_id'], 'data');
                                    $orderDetailModel->product_attribute_id = self::getLocalID('combination', $orderDetail['product_attribute_id'], 'data');
                                    $orderDetailModel->product_name = $orderDetail['product_name'];
                                    $orderDetailModel->product_quantity = $orderDetail['product_quantity'];
                                    $orderDetailModel->product_quantity_in_stock = $orderDetail['product_quantity_in_stock'];
                                    $orderDetailModel->product_quantity_return = $orderDetail['product_quantity_return'];
                                    $orderDetailModel->product_quantity_refunded = $orderDetail['product_quantity_refunded'];
                                    $orderDetailModel->product_quantity_reinjected = $orderDetail['product_quantity_reinjected'];
                                    $orderDetailModel->product_price = $orderDetail['product_price'];
                                    $orderDetailModel->reduction_percent = $orderDetail['reduction_percent'];
                                    $orderDetailModel->reduction_amount = $orderDetail['reduction_amount'];
                                    $orderDetailModel->group_reduction = $orderDetail['group_reduction'];
                                    $orderDetailModel->product_quantity_discount = $orderDetail['product_quantity_discount'];
                                    $orderDetailModel->product_ean13 = $orderDetail['product_ean13'];
                                    $orderDetailModel->product_upc = $orderDetail['product_upc'];
                                    $orderDetailModel->product_reference = $orderDetail['product_reference'];
                                    $orderDetailModel->product_supplier_reference = $orderDetail['product_supplier_reference'];
                                    $orderDetailModel->product_weight = $orderDetail['product_weight'];
                                    $orderDetailModel->tax_name = $orderDetail['tax_name'];
                                    $orderDetailModel->tax_rate = $orderDetail['tax_rate'];
                                    $orderDetailModel->ecotax = $orderDetail['ecotax'];
                                    $orderDetailModel->ecotax_tax_rate = $orderDetail['ecotax_tax_rate'];
                                    $orderDetailModel->discount_quantity_applied = $orderDetail['discount_quantity_applied'];
                                    $orderDetailModel->download_hash = $orderDetail['download_hash'];
                                    $orderDetailModel->download_nb = $orderDetail['download_nb'];
                                    $orderDetailModel->download_deadline = $orderDetail['download_deadline'];

                                    $orderDetailModel->id_warehouse = (isset($orderDetail['id_warehouse']) && !self::isEmpty($orderDetail['id_warehouse'])) ? $orderDetail['id_warehouse'] : 0;

                                    $orderDetailModel->id_shop = (isset($orderDetail['id_shop']) && !self::isEmpty($orderDetail['id_shop'])) ? self::getShopID($orderDetail['id_shop']) : Context::getContext()->shop->id;
                                    if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                        $orderDetailModel->id_order_invoice = self::getLocalID('orderinvoice', $order['id_order'], 'data');
                                        $orderDetailModel->reduction_amount_tax_incl = (float)Tools::ps_round($orderDetail['reduction_amount'], _PS_PRICE_DISPLAY_PRECISION_);
                                        $orderDetailModel->reduction_amount_tax_excl = (float)Tools::ps_round(($orderDetail['reduction_amount'] * 100 / (100 + $orderDetail['tax_rate'])), 6);
                                        $orderDetailModel->product_isbn = $orderDetail['product_isbn'];
                                        $orderDetailModel->tax_computation_method = $orderDetail['tax_computation_method'];
                                        $orderDetailModel->id_tax_rules_group = self::getLocalId('taxrulesgroup', $orderDetail['id_tax_rules_group'], 'data');
                                        $orderDetailModel->unit_price_tax_incl = (float)Tools::ps_round((($orderDetail['product_price'] *  $orderDetail['tax_rate'])/100 + $orderDetail['product_price']) - $orderDetailModel->reduction_amount_tax_incl, _PS_PRICE_DISPLAY_PRECISION_);
                                        $orderDetailModel->unit_price_tax_excl = $orderDetail['product_price'] - $orderDetailModel->reduction_amount_tax_excl;
                                        $orderDetailModel->total_price_tax_incl = $orderDetailModel->unit_price_tax_incl * $orderDetail['product_quantity'];
                                        $orderDetailModel->total_price_tax_excl = $orderDetailModel->unit_price_tax_excl * $orderDetail['product_quantity'];
                                        $orderDetailModel->total_shipping_price_tax_excl = $orderDetail['total_shipping_price_tax_excl'];
                                        $orderDetailModel->total_shipping_price_tax_incl = $orderDetail['total_shipping_price_tax_incl'];
                                        $orderDetailModel->purchase_supplier_price = $orderDetail['purchase_supplier_price'];
                                        $orderDetailModel->original_product_price = $orderDetail['product_price'];
                                        $orderDetailModel->original_wholesale_price = $orderDetail['original_wholesale_price'];
                                    } else {
                                        $orderDetailModel->id_order_invoice = self::getLocalID('orderinvoice', $orderDetail['id_order_invoice'], 'data');
                                        $orderDetailModel->reduction_amount_tax_incl = $orderDetail['reduction_amount_tax_incl'];
                                        $orderDetailModel->reduction_amount_tax_excl = $orderDetail['reduction_amount_tax_excl'];
                                        $orderDetailModel->product_isbn = $orderDetail['product_isbn'];
                                        $orderDetailModel->tax_computation_method = $orderDetail['tax_computation_method'];
                                        $orderDetailModel->id_tax_rules_group = self::getLocalId('taxrulesgroup', $orderDetail['id_tax_rules_group'], 'data');
                                        $orderDetailModel->unit_price_tax_incl = $orderDetail['unit_price_tax_incl'];
                                        $orderDetailModel->unit_price_tax_excl = $orderDetail['unit_price_tax_excl'];
                                        $orderDetailModel->total_price_tax_incl = $orderDetail['total_price_tax_incl'];
                                        $orderDetailModel->total_price_tax_excl = $orderDetail['total_price_tax_excl'];
                                        $orderDetailModel->total_shipping_price_tax_excl = $orderDetail['total_shipping_price_tax_excl'];
                                        $orderDetailModel->total_shipping_price_tax_incl = $orderDetail['total_shipping_price_tax_incl'];
                                        $orderDetailModel->purchase_supplier_price = $orderDetail['purchase_supplier_price'];
                                        $orderDetailModel->original_product_price = $orderDetail['original_product_price'];
                                        $orderDetailModel->original_wholesale_price = $orderDetail['original_wholesale_price'];
                                    }


                                    $res = false;
                                    $err_tmp = '';


                                    $this->validator->setObject($orderDetailModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($orderDetailModel->id && OrderDetail::existsInDatabase($orderDetailModel->id, 'order_detail')) {
                                            try {
                                                $res = $orderDetailModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $orderDetailModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Detail (ID: %1$s) can not be saved. %2$s')), (isset($orderDetail['id_order_detail']) && !self::isEmpty($orderDetail['id_order_detail'])) ? Tools::safeOutput($orderDetail['id_order_detail']) : 'No ID', $err_tmp), 'OrderDetail');
                                        } else {
                                            self::addLog('OrderDetail', $orderDetail['id_order_detail'], $orderDetailModel->id);
                                            //import Order Detail tax
                                            if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                                $sql_values = array();
                                                $sql_values[] = '('  . (int)$orderDetailModel->id . ', ' . (int)Tax::getTaxIdByName($taxName) . ', ' . (float)($orderDetailModel->unit_price_tax_incl - $orderDetailModel->unit_price_tax_excl) . ', ' . (float)($orderDetailModel->total_price_tax_incl - $orderDetailModel->total_price_tax_excl) . ')';
                                            } else {
                                                $sql_values = array();
                                                foreach ($ordersAdditionalThird['order_detail_tax'] as $orderDetailTax) {
                                                    if ($orderDetailTax['id_order_detail'] == $orderDetail['id_order_detail']) {
                                                        $sql_values[] = '(' . (int)$orderDetailModel->id . ', ' . (int)self::getLocalID('tax', $orderDetailTax['id_tax'], 'data') . ', ' . (float)$orderDetailTax['unit_amount'] . ', ' . (float)$orderDetailTax['total_amount'] . ')';
                                                    }
                                                }
                                            }
                                            if (!self::isEmpty($sql_values)) {
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'order_detail_tax` (`id_order_detail`, `id_tax`, `unit_amount`, `total_amount`) VALUES ' . implode(',', $sql_values));

                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_detail_tax. ' . Db::getInstance()->getMsgError()), 'OrderDetail');
                                                }
                                            }

                                            if ($isMigrateRecentData) {
                                                $productsOldQuantity = Db::getInstance()->getValue('SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . $orderDetailModel->product_id . ' AND id_product_attribute = ' . $orderDetailModel->product_attribute_id . ' AND id_shop = ' . $orderDetailModel->id_shop);
                                                $productsNewQuantityAfterOrder = $productsOldQuantity - $orderDetail['product_quantity'];
                                                Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = ' . (int)$productsNewQuantityAfterOrder . ' WHERE id_product = ' . (int)$orderDetailModel->product_id . ' AND id_product_attribute = ' . (int)$orderDetailModel->product_attribute_id . ' AND id_shop = ' . (int)$orderDetailModel->id_shop);
                                                Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product SET quantity = ' . (int)$productsNewQuantityAfterOrder . ' WHERE id_product = ' . (int)$orderDetailModel->product_id);
                                                if ($orderDetailModel->product_attribute_id != 0) {
                                                    $mainProductsCount = (int)Db::getInstance()->getValue('SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = ' . (int)$orderDetailModel->product_id . ' AND id_product_attribute = 0 AND id_shop = ' . (int)$orderDetailModel->id_shop) - (int)$orderDetail['product_quantity'];
                                                    Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = ' . (int)$mainProductsCount . ' WHERE id_product = ' . (int)$orderDetailModel->product_id . ' AND id_product_attribute = 0 AND id_shop = ' . (int)$orderDetailModel->id_shop);
                                                }
                                            }
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'OrderDetail');
                                    }
                                }
                            }
                        }
                        // import Order Slip
                        $insertIdOrderSlipDetail = 1;
                        foreach ($ordersAdditionalSecond['order_slip'] as $orderSlip) {
                            if ($orderSlip['id_order'] == $order['id_order']) {
                                if ($orderSlipModel = $this->createObjectModel('OrderSlip', $orderSlip['id_order_slip'])) {
                                    if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                        $orderSlipModel->conversion_rate = self::isEmpty($orderSlip['conversion_rate']) ? 1 : $orderSlip['conversion_rate'];
                                        $orderSlipModel->id_customer = self::getLocalID('customer', $orderSlip['id_customer'], 'data');
                                        $orderSlipModel->id_order = $orderModel->id;
                                        $orderSlipModel->shipping_cost = $orderSlip['shipping_cost'];
                                        $importedOrderSlipDetails = array();
                                        foreach ($ordersAdditionalThird['order_slip_detail'] as $orderSlipDetails) {
                                            if ($orderSlipDetails['id_order_slip'] == $orderSlip['id_order_slip']) {
                                                if (in_array($orderSlipDetails['id_order_detail'], $importedOrderSlipDetails)) {
                                                    continue;
                                                }
                                                $orderDetailOfSlip = new OrderDetail(self::getLocalID('orderdetail', (int)$orderSlipDetails['id_order_detail'], 'data'));
//                                            if ($orderDetailOfSlip) {
                                                $importedOrderSlipDetails[] = $orderSlipDetails['id_order_detail'];
//                                            }
                                                $orderSlipModel->amount = $orderDetailOfSlip->product_price * $orderSlipDetails['product_quantity'];
                                                $orderSlipModel->total_products_tax_excl = $orderDetailOfSlip->unit_price_tax_excl;
                                                $orderSlipModel->total_products_tax_incl = $orderDetailOfSlip->total_price_tax_incl;
                                                $orderSlipModel->total_shipping_tax_excl = $orderDetailOfSlip->total_shipping_price_tax_excl;
                                                $orderSlipModel->total_shipping_tax_incl = $orderDetailOfSlip->total_shipping_price_tax_incl;
                                                $orderSlipModel->shipping_cost_amount = $orderDetailOfSlip->total_shipping_price_tax_incl;
                                                $orderSlipModel->partial = 0;
                                                $orderSlipModel->order_slip_type = 0;
                                            }
                                        }
                                        if (self::isEmpty($orderSlipModel->total_products_tax_excl) || self::isEmpty($orderSlipModel->total_products_tax_incl)) {
                                            continue;
                                        }
                                        $orderSlipModel->date_add = $orderSlip['date_add'];
                                        $orderSlipModel->date_upd = $orderSlip['date_upd'];
                                    } else {
                                        $orderSlipModel->conversion_rate = $orderSlip['conversion_rate'];
                                        $orderSlipModel->id_customer = self::getLocalID('customer', $orderSlip['id_customer'], 'data');
                                        $orderSlipModel->id_order = $orderModel->id;
                                        $orderSlipModel->shipping_cost = $orderSlip['shipping_cost'];
                                        $orderSlipModel->amount = $orderSlip['amount'];
                                        $orderSlipModel->total_products_tax_excl = $orderSlip['total_products_tax_excl'];
                                        $orderSlipModel->total_products_tax_incl = $orderSlip['total_products_tax_incl'];
                                        $orderSlipModel->total_shipping_tax_excl = $orderSlip['total_shipping_tax_excl'];
                                        $orderSlipModel->total_shipping_tax_incl = $orderSlip['total_shipping_tax_incl'];
                                        $orderSlipModel->shipping_cost_amount = $orderSlip['shipping_cost_amount'];
                                        $orderSlipModel->partial = $orderSlip['partial'];
                                        $orderSlipModel->order_slip_type = $orderSlip['order_slip_type'];
                                        $orderSlipModel->date_add = $orderSlip['date_add'];
                                        $orderSlipModel->date_upd = $orderSlip['date_upd'];
                                    }

                                    if (self::isEmpty($orderSlipModel->total_products_tax_excl)) {
                                        $orderSlipModel->total_products_tax_excl = $orderSlipModel->amount;
                                    }
                                    if (self::isEmpty($orderSlipModel->total_products_tax_incl)) {
                                        $orderSlipModel->total_products_tax_incl = $orderSlipModel->amount;
                                    }
                                    if (self::isEmpty($orderSlipModel->total_shipping_tax_excl)) {
                                        $orderSlipModel->total_shipping_tax_excl = $orderSlipModel->shipping_cost;
                                    }
                                    if (self::isEmpty($orderSlipModel->total_shipping_tax_incl)) {
                                        $orderSlipModel->total_shipping_tax_incl = $orderSlipModel->shipping_cost;
                                    }

                                    $res = false;
                                    $err_tmp = '';


                                    $this->validator->setObject($orderSlipModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($orderSlipModel->id && orderSlip::existsInDatabase($orderSlipModel->id, 'order_slip')) {
                                            try {
                                                $res = $orderSlipModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $orderSlipModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Slip (ID: %1$s) can not be saved. %2$s')), (isset($orderSlip['id_order_slip']) && !self::isEmpty($orderSlip['id_order_slip'])) ? Tools::safeOutput($orderSlip['id_order_slip']) : 'No ID', $err_tmp), 'orderSlip');
                                        } else {
                                            self::addLog('orderSlip', $orderSlip['id_order_slip'], $orderSlipModel->id);
                                            //import Order Slip Detail
                                            $sql_values = array();
                                            $importedOrderSlipDetails = array();
                                            if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                                foreach ($ordersAdditionalThird['order_slip_detail'] as $invoiceSlipDetail) {
                                                    if (in_array($invoiceSlipDetail['id_order_detail'], $importedOrderSlipDetails)) {
                                                        continue;
                                                    } else {
                                                        $importedOrderSlipDetails[] = $invoiceSlipDetail['id_order_detail'];
                                                    }
                                                    $slipIdOrderDetail = self::getLocalID('orderdetail', (int)$invoiceSlipDetail['id_order_detail'], 'data');
                                                    $slipOrderDetailObj = new OrderDetail($slipIdOrderDetail);
                                                    $sql_values = array();
                                                    if ($invoiceSlipDetail['id_order_slip'] == $orderSlip['id_order_slip']) {
                                                        $sql_values[] = '(' .
                                                            $orderSlipModel->id . ', ' .
                                                            $slipOrderDetailObj->id . ', ' .
                                                            $invoiceSlipDetail['product_quantity']. ', ' .
                                                            $slipOrderDetailObj->unit_price_tax_excl .  ', ' .
                                                            $slipOrderDetailObj->unit_price_tax_incl .   ', ' .
                                                            $slipOrderDetailObj->total_price_tax_excl . ', ' .
                                                            $slipOrderDetailObj->total_price_tax_incl .  ', ' .
                                                            $slipOrderDetailObj->total_price_tax_excl .   ', ' .
                                                            $slipOrderDetailObj->total_price_tax_incl . ')';
                                                    }
                                                    if (!self::isEmpty($sql_values)) {
                                                        $dbInstance = Db::getInstance();
                                                        $result = false;
                                                        $result = $dbInstance->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'order_slip_detail` (
                                                `id_order_slip`,
                                                `id_order_detail`,
                                                `product_quantity`,
                                                `unit_price_tax_excl`,
                                                `unit_price_tax_incl`,
                                                `total_price_tax_excl`,
                                                `total_price_tax_incl`,
                                                `amount_tax_excl`,
                                                `amount_tax_incl`)
                                                VALUES ' . implode(',', $sql_values));
                                                        if (!$result) {
                                                            $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_slip_detail. ' . Db::getInstance()->getMsgError()), 'orderSlip');
                                                        } else {
                                                            $orderSlipDetailTaxName = Tax::getTaxIdByName($slipOrderDetailObj->tax_name);
                                                            Db::getInstance()->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'order_slip_detail_tax` (id_order_slip_detail, id_tax, unit_amount, total_amount) VALUES (' .
                                                                (int)$insertIdOrderSlipDetail++ . ', ' .
                                                                self::isEmpty($orderSlipDetailTaxName) ? $orderSlipDetailTaxName : 0 . ', ' . (float)$slipOrderDetailObj->unit_price_tax_incl . ', ' . (float)$slipOrderDetailObj->total_price_tax_incl . ')');
                                                        }
                                                    }
                                                }
                                            } else {
                                                $sql_values = array();
                                                foreach ($ordersAdditionalThird['order_slip_detail'] as $invoiceSlipDetail) {
                                                    if (self::isEmpty($invoiceSlipDetail['product_quantity']) || self::isEmpty($invoiceSlipDetail['unit_price_tax_excl']) || self::isEmpty($invoiceSlipDetail['unit_price_tax_incl']) || self::isEmpty($invoiceSlipDetail['total_price_tax_excl']) || $invoiceSlipDetail['total_price_tax_incl']) {
                                                        $slipIdOrderDetail = self::getLocalID('orderdetail', (int)$invoiceSlipDetail['id_order_detail'], 'data');
                                                        $slipOrderDetailObj = new OrderDetail($slipIdOrderDetail);
                                                        $sql_values[] = '(' .
                                                            $orderSlipModel->id . ', ' .
                                                            $slipOrderDetailObj->id . ', ' .
                                                            $invoiceSlipDetail['product_quantity']. ', ' .
                                                            $slipOrderDetailObj->unit_price_tax_excl .  ', ' .
                                                            $slipOrderDetailObj->unit_price_tax_incl .   ', ' .
                                                            $slipOrderDetailObj->total_price_tax_excl . ', ' .
                                                            $slipOrderDetailObj->total_price_tax_incl .  ', ' .
                                                            $slipOrderDetailObj->total_price_tax_excl .   ', ' .
                                                            $slipOrderDetailObj->total_price_tax_incl . ')';
                                                    } else {
                                                        $sql_values[] = '(' . (int)$orderSlipModel->id . ', ' . self::getLocalID('orderDetail', $invoiceSlipDetail['id_order_detail'], 'data') . ', ' . (int)$invoiceSlipDetail['product_quantity'] . ', ' . (float)$invoiceSlipDetail['unit_price_tax_excl'] . ', ' . (float)$invoiceSlipDetail['unit_price_tax_incl'] . ', ' . (float)$invoiceSlipDetail['total_price_tax_excl'] . ', ' . (float)$invoiceSlipDetail['total_price_tax_incl'] . ', ' .
                                                            (float)$invoiceSlipDetail['amount_tax_excl'] .
                                                        ', ' . (float)$invoiceSlipDetail['amount_tax_incl'] . ')';
                                                    }
                                                }
                                                if (!self::isEmpty($sql_values)) {
                                                    $dbInstance = Db::getInstance();
                                                    $result = false;
                                                    $result = $dbInstance->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'order_slip_detail` (
                                                `id_order_slip`,
                                                `id_order_detail`,
                                                `product_quantity`,
                                                `unit_price_tax_excl`,
                                                `unit_price_tax_incl`,
                                                `total_price_tax_excl`,
                                                `total_price_tax_incl`,
                                                `amount_tax_excl`,
                                                `amount_tax_incl`)
                                                VALUES ' . implode(',', $sql_values));
                                                    if (!$result) {
                                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_slip_detail. ' . Db::getInstance()->getMsgError()), 'orderSlip');
                                                    } else {
                                                        $sql_values = array();
                                                        foreach ($ordersAdditionalThird['order_slip_detail_tax'] as $invoiceSlipDetailtax) {
                                                            $sql_values[] = '(' . (int)$invoiceSlipDetailtax['id_order_slip_detail'] . ', ' . self::getLocalID('tax', $invoiceSlipDetailtax['id_tax'], 'data') . ', ' . (float)$invoiceSlipDetailtax['unit_amount'] . ', ' . (float)$invoiceSlipDetailtax['total_amount'] . ') ';
                                                        }

                                                        if (!self::isEmpty($sql_values)) {
                                                            $dbInstance = Db::getInstance();
                                                            $result = false;
                                                            $result = $dbInstance->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'order_slip_detail_tax` (
                                                            `id_order_slip_detail`,
                                                            `id_tax`,
                                                            `unit_amount`,
                                                            `total_amount`)
                                                            VALUES ' . implode(',', $sql_values));
                                                        }
                                                        if (!$result) {
                                                            $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_slip_detail_tax. ' . Db::getInstance()->getMsgError()), 'orderSlip');
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'orderSlip');
                                    }
                                }
                            }
                        }
                        // import Order History
                        foreach ($ordersAdditionalSecond['order_history'] as $orderHistory) {
                            if ($orderHistory['id_order'] == $order['id_order']) {
                                if ($orderHistoryModel = $this->createObjectModel('OrderHistory', $orderHistory['id_order_history'])) {
                                    $orderHistoryModel->id_order = $orderModel->id;
                                    $orderHistoryModel->id_order_state = self::getOrderStateID($orderHistory['id_order_state']);
                                    $orderHistoryModel->id_customer_thread = self::getLocalID('employee', $orderHistory['id_employee'], 'data');
                                    $orderHistoryModel->date_add = $orderHistory['date_add'];


                                    $res = false;
                                    $err_tmp = '';


                                    $this->validator->setObject($orderHistoryModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($orderHistoryModel->id && OrderHistory::existsInDatabase($orderHistoryModel->id, 'order_history')) {
                                            try {
                                                $res = $orderHistoryModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $orderHistoryModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order History (ID: %1$s) can not be saved. %2$s')), (isset($orderHistory['id_order_history']) && !self::isEmpty($orderHistory['id_order_history'])) ? Tools::safeOutput($orderHistory['id_order_history']) : 'No ID', $err_tmp), 'OrderHistory');
                                        } else {
                                            self::addLog('OrderHistory', $orderHistory['id_order_history'], $orderHistoryModel->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'OrderHistory');
                                    }
                                }
                            }
                        }
                        // import Order Return
                        foreach ($ordersAdditionalSecond['order_return'] as $orderReturn) {
                            if ($orderReturn['id_order'] == $order['id_order']) {
                                if ($orderReturnModel = $this->createObjectModel('OrderReturn', $orderReturn['id_order_return'])) {
                                    $orderReturnModel->id_order = $orderModel->id;
                                    $orderReturnModel->id_customer = self::getLocalID('customer', $orderReturn['id_customer'], 'data');
                                    $orderReturnModel->date_add = $orderReturn['date_add'];


                                    $res = false;
                                    $err_tmp = '';


                                    $this->validator->setObject($orderReturnModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($orderReturnModel->id && OrderReturn::existsInDatabase($orderReturnModel->id, 'order_history')) {
                                            try {
                                                $res = $orderReturnModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $orderReturnModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Return (ID: %1$s) can not be saved. %2$s')), (isset($orderReturn['id_order_return']) && !self::isEmpty($orderReturn['id_order_return'])) ? Tools::safeOutput($orderReturn['id_order_return']) : 'No ID', $err_tmp), 'OrderReturn');
                                        } else {
                                            self::addLog('OrderReturn', $orderReturn['id_order_return'], $orderReturnModel->id);
                                            $sql_values = array();
                                            foreach ($ordersAdditionalThird['order_return_detail'] as $orderDetailReturn) {
                                                if ($orderDetailReturn['id_order_return'] == $orderReturn['id_order_return']) {
                                                    $sql_values[] = '(' . (int)$orderReturnModel->id . ', ' . (int)self::getLocalID('orderDetail', $orderDetailReturn['id_order_detail'], 'data') . ', ' . (int)$orderDetailReturn['id_customization'] . ', ' . (int)$orderDetailReturn['product_quantity'] . ')';
                                                }
                                            }
                                        }
                                        if (!self::isEmpty($sql_values)) {
                                            $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'order_return_detail` (`id_order_return`, `id_order_detail`, `id_customization`, `product_quantity`) VALUES ' . implode(',', $sql_values));

                                            if (!$result) {
                                                $this->showMigrationMessageAndLog(self::displayError('Can\'t add order_return_detail. ' . Db::getInstance()->getMsgError()), 'OrderReturn');
                                            }
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'OrderReturn');
                                    }
                                }
                            }
                        }
                        // import Order Carrier
                        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                            if ($orderCarrierModel = $this->createObjectModel('OrderCarrier', $order['id_order'])) {
                                $orderCarrierModel->id_order = $orderModel->id;
                                $orderCarrierModel->id_carrier = self::getLocalID('carrier', $order['id_carrier'], 'data');
                                $orderCarrierModel->id_order_invoice = self::getLocalID('orderInvoice', $order['id_order'], 'data');
//                                $orderCarrierModel->weight = $orderCarrier['weight'];
                                $orderCarrierModel->shipping_cost_tax_excl = $orderModel->total_shipping_tax_excl;
                                $orderCarrierModel->shipping_cost_tax_incl = $orderModel->total_shipping_tax_incl;
                                $orderCarrierModel->tracking_number = $orderModel->shipping_number;
                                $orderCarrierModel->date_add = $order['date_add'];

                                $res = false;
                                $err_tmp = '';


                                $this->validator->setObject($orderCarrierModel);
                                $this->validator->checkFields();
                                $error_tmp = $this->validator->getValidationMessages();
                                if (self::isEmpty($error_tmp)) {
                                    if ($orderCarrierModel->id && OrderCarrier::existsInDatabase($orderCarrierModel->id, 'order_carrier')) {
                                        try {
                                            $res = $orderCarrierModel->update();
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }
                                    if (!$res) {
                                        try {
                                            $res = $orderCarrierModel->add(false);
                                        } catch (PrestaShopException $e) {
                                            $err_tmp = $e->getMessage();
                                        }
                                    }

                                    if (!$res) {
                                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Carrier of Order (ID: %1$s) can not be saved. %2$s')), (isset($order['id_order']) && !self::isEmpty($order['id_order'])) ? Tools::safeOutput($order['id_order']) : 'No ID', $err_tmp), 'OrderCarrier');
                                    } else {
                                        self::addLog('OrderCarrier', $order['id_order'], $orderCarrierModel->id);
                                    }
                                } else {
                                    $this->showMigrationMessageAndLog($error_tmp, 'OrderCarrier');
                                }
                            }
                        } else {
                            foreach ($ordersAdditionalSecond['order_carrier'] as $orderCarrier) {
                                if ($orderCarrier['id_order'] == $order['id_order']) {
                                    if ($orderCarrierModel = $this->createObjectModel('OrderCarrier', $orderCarrier['id_order_carrier'])) {
                                        $orderCarrierModel->id_order = $orderModel->id;
                                        $orderCarrierModel->id_carrier = self::getLocalID('carrier', $orderCarrier['id_carrier'], 'data');
                                        $orderCarrierModel->id_order_invoice = self::getLocalID('orderInvoice', $orderCarrier['id_order_invoice'], 'data');
                                        $orderCarrierModel->weight = $orderCarrier['weight'];
                                        $orderCarrierModel->shipping_cost_tax_excl = $orderCarrier['shipping_cost_tax_excl'];
                                        $orderCarrierModel->shipping_cost_tax_incl = $orderCarrier['shipping_cost_tax_incl'];
                                        $orderCarrierModel->tracking_number = $orderCarrier['tracking_number'];
                                        $orderCarrierModel->date_add = $orderCarrier['date_add'];


                                        $res = false;
                                        $err_tmp = '';


                                        $this->validator->setObject($orderCarrierModel);
                                        $this->validator->checkFields();
                                        $error_tmp = $this->validator->getValidationMessages();
                                        if (self::isEmpty($error_tmp)) {
                                            if ($orderCarrierModel->id && OrderCarrier::existsInDatabase($orderCarrierModel->id, 'order_carrier')) {
                                                try {
                                                    $res = $orderCarrierModel->update();
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }
                                            if (!$res) {
                                                try {
                                                    $res = $orderCarrierModel->add(false);
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }

                                            if (!$res) {
                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Carrier (ID: %1$s) can not be saved. %2$s')), (isset($orderCarrier['id_order_carrier']) && !self::isEmpty($orderCarrier['id_order_carrier'])) ? Tools::safeOutput($orderCarrier['id_order_carrier']) : 'No ID', $err_tmp), 'OrderCarrier');
                                            } else {
                                                self::addLog('OrderCarrier', $orderCarrier['id_order_carrier'], $orderCarrierModel->id);
                                            }
                                        } else {
                                            $this->showMigrationMessageAndLog($error_tmp, 'OrderCarrier');
                                        }
                                    }
                                }
                            }
                        }
                        // import Order Cart Rule
                        foreach ($ordersAdditionalSecond['order_cart_rule'] as $orderCartRule) {
                            if ($orderCartRule['id_order'] == $order['id_order']) {
                                if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                    $orderCartRuleId = 'id_order_discount';
                                } else {
                                    $orderCartRuleId = 'id_order_cart_rule';
                                }
                                if ($orderCartRuleModel = $this->createObjectModel('OrderCartRule', $orderCartRule[$orderCartRuleId])) {
                                    $orderCartRuleModel->id_order = $orderModel->id;
                                    $orderCartRuleModel->name = $orderCartRule['name'];
                                    if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                                        $orderCartRuleModel->id_cart_rule = self::getLocalID('cartRule', $orderCartRule['id_discount'], 'data');
                                        $orderCartRuleModel->id_order_invoice = self::getLocalID('orderInvoice', $orderCartRule['id_order'], 'data');
                                        $orderCartRuleModel->free_shipping = 0;
                                        $orderCartRuleModel->value = $orderModel->total_discounts_tax_incl;
                                        $orderCartRuleModel->value_tax_excl = $orderModel->total_discounts_tax_excl;
                                    } else {
                                        $orderCartRuleModel->id_cart_rule = self::getLocalID('cartRule', $orderCartRule['id_cart_rule'], 'data');
                                        $orderCartRuleModel->id_order_invoice = self::getLocalID('orderInvoice', $orderCartRule['id_order_invoice'], 'data');
                                        $orderCartRuleModel->free_shipping = $orderCartRule['free_shipping'];
                                        $orderCartRuleModel->value = $orderCartRule['value'];
                                        $orderCartRuleModel->value_tax_excl = $orderCartRule['value_tax_excl'];
                                    }

                                    $res = false;
                                    $err_tmp = '';


                                    $this->validator->setObject($orderCartRuleModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($orderCartRuleModel->id && OrderCartRule::existsInDatabase($orderCartRuleModel->id, 'order_cart_rule')) {
                                            try {
                                                $res = $orderCartRuleModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $orderCartRuleModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Cart Rule (ID: %1$s) can not be saved. %2$s')), (isset($orderCartRule[$orderCartRuleId]) && !self::isEmpty($orderCartRule[$orderCartRuleId])) ? Tools::safeOutput($orderCartRule[$orderCartRuleId]) : 'No ID', $err_tmp), 'OrderCartRule');
                                        } else {
                                            self::addLog('OrderCartRule', $orderCartRule[$orderCartRuleId], $orderCartRuleModel->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'OrderCartRule');
                                    }
                                }
                            }
                        }
                        // import Address
                        foreach ($ordersAdditionalSecond['address'] as $address) {
                            if ($address['id_address'] == $order['id_address_delivery']) {
                                if ($addressModel = $this->createObjectModel('Address', $address['id_address'])) {
                                    $addressModel->id_customer = self::getLocalId('customer', $address['id_customer'], 'data');
                                    $addressModel->id_manufacturer = self::getLocalId('manufacturer', $address['id_manufacturer'], 'data');
                                    $addressModel->id_supplier = self::getLocalId('supplier', $address['id_supplier'], 'data');
                                    $addressModel->id_country = self::getLocalId('country', $address['id_country'], 'data');
                                    $addressModel->id_state = self::getLocalId('state', $address['id_state'], 'data');
                                    $addressModel->alias = $address['alias'];
                                    $addressModel->company = $address['company'];
                                    $addressModel->lastname = $address['lastname'];
                                    $addressModel->firstname = $address['firstname'];
                                    $addressModel->vat_number = $address['vat_number'];
                                    $addressModel->address1 = $address['address1'];
                                    $addressModel->address2 = $address['address2'];
                                    $addressModel->postcode = $address['postcode'];
                                    $addressModel->city = $address['city'];
                                    $addressModel->other = $address['other'];
                                    $addressModel->phone = $address['phone'];
                                    $addressModel->phone_mobile = $address['phone_mobile'];
                                    $addressModel->dni = $address['dni'];
                                    $addressModel->deleted = $address['deleted'];
                                    $addressModel->date_add = $address['date_add'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $address['date_add'];
                                    $addressModel->date_upd = $address['date_upd'] == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s') : $address['date_upd'];
                                    $addressModel->id_warehouse = $address['id_warehouse'];

                                    $res = false;
                                    $err_tmp = '';


                                    $this->validator->setObject($addressModel);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($addressModel->id && Address::existsInDatabase($addressModel->id, 'address')) {
                                            try {
                                                $res = $addressModel->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $addressModel->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Address (ID: %1$s) can not be saved. %2$s')), (isset($address['id_address']) && !self::isEmpty($address['id_address'])) ? Tools::safeOutput($address['id_address']) : 'No ID', $err_tmp), 'Address');
                                        } else {
                                            // import country
                                            foreach ($ordersAdditionalThird['country'] as $country) {
                                                if ($country['id_country'] == $address['id_country']) {
                                                    if ($countryModel = $this->createObjectModel('Country', $country['id_country'])) {
                                                        $countryModel->id_zone = $country['id_zone'];
                                                        $countryModel->id_currency = self::getCurrencyID($country['id_currency']);
                                                        $countryModel->call_prefix = $country['call_prefix'];
                                                        $countryModel->iso_code = $country['iso_code'];
                                                        $countryModel->active = $country['active'];
                                                        $countryModel->contains_states = $country['contains_states'];
                                                        $countryModel->need_identification_number = $country['need_identification_number'];
                                                        $countryModel->need_zip_code = $country['need_zip_code'];
                                                        $countryModel->zip_code_format = $country['zip_code_format'];
                                                        $countryModel->display_tax_label = (isset($country['display_tax_label'])) ? (bool)$country['display_tax_label'] : true;

                                                        // Add to _shop relations
                                                        $countriesShopsRelations = $this->getChangedIdShop($ordersAdditionalThird['country_shop'], 'id_country');
                                                        if (array_key_exists($country['id_country'], $countriesShopsRelations)) {
                                                            $countryModel->id_shop_list = array_values($countriesShopsRelations[$country['id_country']]);
                                                        }


                                                        //language fields
                                                        foreach ($ordersAdditionalThird['country_lang'] as $lang) {
                                                            if ($lang['id_country'] == $country['id_country']) {
                                                                $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                                                $countryModel->name[$lang['id_lang']] = $lang['name'];
                                                            }
                                                        }

                                                        $res = false;
                                                        $err_tmp = '';


                                                        $this->validator->setObject($countryModel);
                                                        $this->validator->checkFields();
                                                        $error_tmp = $this->validator->getValidationMessages();
                                                        if (self::isEmpty($error_tmp)) {
                                                            if ($countryModel->id && Country::existsInDatabase($countryModel->id, 'country')) {
                                                                try {
                                                                    $res = $countryModel->update();
                                                                } catch (PrestaShopException $e) {
                                                                    $err_tmp = $e->getMessage();
                                                                }
                                                            }
                                                            if (!$res) {
                                                                try {
                                                                    $res = $countryModel->add(false);
                                                                } catch (PrestaShopException $e) {
                                                                    $err_tmp = $e->getMessage();
                                                                }
                                                            }

                                                            if (!$res) {
                                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Country (ID: %1$s) can not be saved. %2$s')), (isset($country['id_country']) && !self::isEmpty($country['id_country'])) ? Tools::safeOutput($country['id_country']) : 'No ID', $err_tmp), 'Country');
                                                            } else {
                                                                self::addLog('Country', $country['id_country'], $countryModel->id);
                                                            }
                                                        } else {
                                                            $this->showMigrationMessageAndLog($error_tmp, 'Country');
                                                        }
                                                    }
                                                }
                                            }

                                            // import State
                                            foreach ($ordersAdditionalThird['state'] as $state) {
                                                if ($state['id_state'] == $address['id_state']) {
                                                    if ($stateModel = $this->createObjectModel('State', $state['id_state'])) {
                                                        $stateModel->id_country = self::getLocalId('country', $state['id_country'], 'data');
                                                        $stateModel->id_zone = $state['id_zone'];
                                                        $stateModel->iso_code = $state['iso_code'];
                                                        $stateModel->active = $state['active'];
                                                        $stateModel->name = $state['name'];

                                                        $res = false;
                                                        $err_tmp = '';


                                                        $this->validator->setObject($stateModel);
                                                        $this->validator->checkFields();
                                                        $error_tmp = $this->validator->getValidationMessages();
                                                        if (self::isEmpty($error_tmp)) {
                                                            if ($stateModel->id && State::existsInDatabase($stateModel->id, 'state')) {
                                                                try {
                                                                    $res = $stateModel->update();
                                                                } catch (PrestaShopException $e) {
                                                                    $err_tmp = $e->getMessage();
                                                                }
                                                            }
                                                            if (!$res) {
                                                                try {
                                                                    $res = $stateModel->add(false);
                                                                } catch (PrestaShopException $e) {
                                                                    $err_tmp = $e->getMessage();
                                                                }
                                                            }

                                                            if (!$res) {
                                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('State (ID: %1$s) can not be saved. %2$s')), (isset($state['id_state']) && !self::isEmpty($state['id_state'])) ? Tools::safeOutput($state['id_state']) : 'No ID', $err_tmp), 'State');
                                                            } else {
                                                                self::addLog('State', $state['id_state'], $stateModel->id);
                                                            }
                                                        } else {
                                                            $this->showMigrationMessageAndLog($error_tmp, 'State');
                                                        }
                                                    }
                                                }
                                            }
                                            if (count($this->error_msg) == 0) {
                                                self::addLog('Address', $address['id_address'], $addressModel->id);
                                            }
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'Address');
                                    }
                                }
                            }
                        }

                        foreach ($ordersAdditionalSecond['message'] as $message) {
                            if ($message['id_order'] == $order['id_order']) {
                                if ($messageObject = $this->createObjectModel('Message', $message['id_message'])) {
                                    $messageObject->id_cart = self::getLocalID('cart', $message['id_cart'], 'data');
                                    $messageObject->id_customer = self::getLocalID('customer', $message['id_customer'], 'data');
                                    $messageObject->id_employee = self::getLocalID('employee', $message['id_employee'], 'data');
                                    $messageObject->id_order = $orderModel->id;
                                    $messageObject->message = $message['message'];
                                    $messageObject->private = $message['private'];
                                    $messageObject->date_add = $message['date_add'];
                                    if (self::isEmpty($message['date_add']) || $message['date_add'] == '0000-00-00 00:00:00') {
                                        $messageObject->date_add = date('Y-m-d H:i:s');
                                    }

                                    if (self::isEmpty($messageObject->message)) {
                                        $this->showMigrationMessageAndLog('Message with ID ' . $message['id_message'] . ' has not a message text and it is not allowed in PrestaShop. For that reason, the module skipped this unvalid message.', 'Message', true);
                                        continue;
                                    }

                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($messageObject);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($messageObject->id && Address::existsInDatabase($messageObject->id, 'message')) {
                                            try {
                                                $res = $messageObject->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $messageObject->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Message (ID: %1$s) can not be saved. %2$s')), (isset($message['id_address']) && !self::isEmpty($message['id_address'])) ? Tools::safeOutput($message['id_address']) : 'No ID', $err_tmp), 'Message');
                                        } else {
                                            //import Message Readed
                                            $sql_values = array();
                                            foreach ($ordersAdditionalThird as $messageReaded) {
                                                if ($messageReaded['id_message'] == $message['id_message']) {
                                                    $sql_values[] = '(' . (int)$messageObject->id . ', ' . (int)self::getLocalID('employee', $messageReaded['id_employee'], 'data') . ', ' . pSQL($messageReaded['date_add']) . ')';
                                                }
                                            }
                                            if (!self::isEmpty($sql_values)) {
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'ps_message_readed` (`id_message`, `id_employee`, `date_add`) VALUES ' . implode(',', $sql_values));

                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add message_readed. ' . Db::getInstance()->getMsgError()), 'Message');
                                                }
                                            }
                                            self::addLog('Message', $message['id_message'], $messageObject->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'Message');
                                    }
                                }
                            }
                        }

                        if (count($this->error_msg) == 0) {
                            self::addLog('Order', $order['id_order'], $orderModel->id);
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'Order');
                }
            }
        }

        // Import Order Messages
        foreach ($ordersAdditionalSecond['order_message'] as $orderMessage) {
            if ($orderMessageModel = $this->createObjectModel('OrderMessage', $orderMessage['id_order_message'])) {
                $orderMessageModel->date_add = $orderMessage['date_add'];
                foreach ($ordersAdditionalThird['order_message_lang'] as $lang) {
                    if ($lang['id_order_message'] == $orderMessage['id_order_message']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $orderMessageModel->name[$lang['id_lang']] = $lang['name'];
                        $orderMessageModel->message[$lang['id_lang']] = $lang['message'];
                    }
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($orderMessageModel);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($orderMessageModel->id && OrderMessage::existsInDatabase($orderMessageModel->id, 'order_message')) {
                        try {
                            $res = $orderMessageModel->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $orderMessageModel->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Order Message (ID: %1$s) can not be saved. %2$s')), (isset($orderMessage['id_order_message']) && !self::isEmpty($orderMessage['id_order_message'])) ? Tools::safeOutput($orderMessage['id_order_message']) : 'No ID', $err_tmp), 'OrderMessage');
                    } else {
                        self::addLog('OrderMessage', $orderMessage['id_order_message'], $orderMessageModel->id);
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'OrderMessage');
                }
            }
        }
        $this->updateProcess(count($orders));
    }

    /**
     * @param $customerMessages
     * @param $customerThreads
     */
    public function customerMessages($customerThreads, $customerMessages)
    {
        // Import Customer Threads
        foreach ($customerThreads as $customerThread) {
//            if ($order['id_order'] == $customerThread['id_order']) {
//                if (version_compare($this->version, '1.6', '<')) {
//                    $objId = $customerThread['id_order'];
//                } else {
                    $objId = $customerThread['id_customer_thread'];
//                }
                $id_customer= self::getLocalID('customer', $customerThread['id_customer'], 'data');
//                $customer= new Customer($id_customer);
            if ($customerThreadObject = $this->createObjectModel('CustomerThread', $objId)) {
                if (version_compare($this->version, '1.6', '<')) {
                    $customerThreadObject->id_shop = Configuration::get('PS_SHOP_DEFAULT');
                    $customerThreadObject->id_lang = Configuration::get('PS_LANG_DEFAULT');
                    $customerThreadObject->id_contact = 0;
                    $customerThreadObject->id_customer = $id_customer;
                    $customerThreadObject->id_order = self::getLocalID('order', $customerThread['id_order'], 'data');
                    $customerThreadObject->id_product = self::getLocalID('product', $customerThread['id_product'], 'data');
//                        $customerThreadObject->status = $customer->active;
                    $customerThreadObject->status = $customerThread['status'];
//                        $customerThreadObject->email = $customer->email;
                    $customerThreadObject->email = $customerThread['email'];
//                        $customerThreadObject->token = md5($customerThread['id_message']);
                    $customerThreadObject->token = $customerThread['token'];
                    $customerThreadObject->date_add = $customerThread['date_add'];
                    $customerThreadObject->date_upd = $customerThread['date_upd'];
                } else {
                    $customerThreadObject->id_shop = self::getShopID($customerThread['id_shop']);
                    $customerThreadObject->id_lang = self::getLanguageID($customerThread['id_lang']);
                    $customerThreadObject->id_contact = $customerThread['id_contact'];
                    $customerThreadObject->id_customer = $id_customer;
                    $customerThreadObject->id_order = self::getLocalID('order', $customerThread['id_order'], 'data');
                    $customerThreadObject->id_product = self::getLocalID('product', $customerThread['id_product'], 'data');
                    $customerThreadObject->status = $customerThread['status'];
                    $customerThreadObject->email = $customerThread['email'];
                    $customerThreadObject->token = $customerThread['token'];
                    $customerThreadObject->date_add = $customerThread['date_add'];
                    $customerThreadObject->date_upd = $customerThread['date_upd'];
                }


                $res = false;
                $err_tmp = '';

                $this->validator->setObject($customerThreadObject);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($customerThreadObject->id && CustomerThread::existsInDatabase($customerThreadObject->id, 'customer_thread')) {
                        try {
                            $res = $customerThreadObject->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $customerThreadObject->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Customer Thread (ID: %1$s) can not be saved. %2$s')), (isset($objId) && !self::isEmpty($objId)) ? Tools::safeOutput($objId) : 'No ID', $err_tmp), 'CustomerThread');
                    } else {
                        foreach ($customerMessages as $customerMessage) {
//                                if (version_compare($this->version, '1.6', '<')) {
//                                    $key = 'id_order';
//                                    $objId= $customerMessage['id_message'];
//                                } else {
                            $key = 'id_customer_thread';
                            $msgObjId = $customerMessage['id_customer_message'];
//                                }
                            if ($customerMessage[$key] == $customerThread[$key]) {
                                if ($customerMessageObject = $this->createObjectModel('CustomerMessage', $msgObjId)) {
                                    if (version_compare($this->version, '1.6', '<')) {
                                        $customerMessageObject->id_customer_thread = $customerThreadObject->id;
                                        $customerMessageObject->id_employee = self::getLocalID('employee', $customerMessage['id_employee'], 'data');
                                        $customerMessageObject->message = html_entity_decode($customerMessage['message']) ?: Tools::htmlentitiesDecodeUTF8($customerMessage['message']);
                                        $customerMessageObject->file_name = $customerMessage['file_name'];
                                        $customerMessageObject->ip_address = $customerMessage['ip_address'];
                                        $customerMessageObject->user_agent = $customerMessage['user_agent'];
                                        $customerMessageObject->date_add = $customerMessage['date_add'];
                                        if (self::isEmpty($customerMessage['date_upd']) || $customerMessage['date_upd'] == '0000-00-00 00:00:00') {
                                            $customerMessageObject->date_upd = date('Y-m-d H:i:s');
                                        } else {
                                            $customerMessageObject->date_upd = $customerMessage['date_upd'];
                                        }
                                        $customerMessageObject->private = $customerMessage['private'];
                                    } else {
                                        $customerMessageObject->id_customer_thread = $customerThreadObject->id;
                                        $customerMessageObject->id_employee = self::getLocalID('employee', $customerMessage['id_employee'], 'data');
                                        $customerMessageObject->message = $customerMessage['message'];
                                        $customerMessageObject->file_name = $customerMessage['file_name'];
                                        $customerMessageObject->ip_address = $customerMessage['ip_address'];
                                        $customerMessageObject->user_agent = $customerMessage['user_agent'];
                                        $customerMessageObject->date_add = $customerMessage['date_add'];
                                        if (self::isEmpty($customerMessage['date_upd']) || $customerMessage['date_upd'] == '0000-00-00 00:00:00') {
                                            $customerMessageObject->date_upd = date('Y-m-d H:i:s');
                                        } else {
                                            $customerMessageObject->date_upd = $customerMessage['date_upd'];
                                        }
                                    }


                                    $res = false;
                                    $err_tmp = '';

                                    $this->validator->setObject($customerMessageObject);
                                    $this->validator->checkFields();
                                    $error_tmp = $this->validator->getValidationMessages();
                                    if (self::isEmpty($error_tmp)) {
                                        if ($customerMessageObject->id && Address::existsInDatabase($customerMessageObject->id, 'customer_message')) {
                                            try {
                                                $res = $customerMessageObject->update();
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }
                                        if (!$res) {
                                            try {
                                                $res = $customerMessageObject->add(false);
                                            } catch (PrestaShopException $e) {
                                                $err_tmp = $e->getMessage();
                                            }
                                        }

                                        if (!$res) {
                                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Customer message (ID: %1$s) can not be saved. %2$s')), (isset($objId) && !self::isEmpty($objId)) ? Tools::safeOutput($objId) : 'No ID', $err_tmp), 'CustomerMessage');
                                        } else {
                                            self::addLog('CustomerMessage', $msgObjId, $customerMessageObject->id);
                                        }
                                    } else {
                                        $this->showMigrationMessageAndLog($error_tmp, 'CustomerMessage');
                                    }
                                }
                            }
                        }
                        if (count($this->error_msg) == 0) {
                            self::addLog('CustomerThread', $objId, $customerThreadObject->id);
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'CustomerThread');
                }
            }
//            }
        }

        $this->updateProcess(count($customerThreads));
    }

    /**
     * @param $cmses
     * @param $cmsAdditionalSecond
     * @param $cmsAdditionalThird
     */
    public function cmses($cmses, $cmsAdditionalSecond, $cmsAdditionalThird)
    {
        // Import CMS Category
        foreach ($cmsAdditionalSecond['cms_category'] as $cmsCategory) {
            if ($cmsCategoryModel = $this->createObjectModel('CMSCategory', $cmsCategory['id_cms_category'])) {
                $cmsCategoryModel->id_parent = self::getLocalID('cmscategory', $cmsCategory['id_parent'], 'data');
                if (self::isEmpty($cmsCategoryModel->id_parent)) {
                    $cmsCategoryModel->id_parent = 0;
                }
                $cmsCategoryModel->active = $cmsCategory['active'];
                $cmsCategoryModel->date_add = $cmsCategory['date_add'];
                $cmsCategoryModel->date_upd = $cmsCategory['date_upd'];


                foreach ($cmsAdditionalThird['cms_category_lang'] as $lang) {
                    if ($lang['id_cms_category'] == $cmsCategory['id_cms_category']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $cmsCategoryModel->name[$lang['id_lang']] = $lang['name'];
                        $cmsCategoryModel->description[$lang['id_lang']] = $lang['description'];
                        $cmsCategoryModel->meta_title[$lang['id_lang']] = $lang['meta_title'];
                        $cmsCategoryModel->meta_keywords[$lang['id_lang']] = $lang['meta_keywords'];
                        $cmsCategoryModel->meta_description[$lang['id_lang']] = $lang['meta_description'];
                        $cmsCategoryModel->link_rewrite[$lang['id_lang']] = $lang['link_rewrite'];

                        if (isset($cmsCategoryModel->link_rewrite[$lang['id_lang']]) && !self::isEmpty($cmsCategoryModel->link_rewrite[$lang['id_lang']])) {
                            $valid_link = Validate::isLinkRewrite($cmsCategoryModel->link_rewrite[$lang['id_lang']]);
                        } else {
                            $valid_link = false;
                        }
                        if (!$valid_link) {
                            $cmsCategoryModel->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($cmsCategoryModel->name[$lang['id_lang']]);

                            if ($cmsCategoryModel->link_rewrite[$lang['id_lang']] == '') {
                                $cmsCategoryModel->link_rewrite[$lang['id_lang']] = 'friendly-url-autogeneration-failed';
                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('URL rewriting failed to auto-generate a friendly URL for: %s')), $cmsCategoryModel->name[$lang['id_lang']]), 'CMSCategory');
                            }

                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('The link for %1$s (ID: %2$s) was re-written as %3$s.')), $lang['link_rewrite'], (isset($cmsCategory['id_cms_category']) && !self::isEmpty($cmsCategory['id_cms_category'])) ? $cmsCategory['id_cms_category'] : 'null', $cmsCategoryModel->link_rewrite[$lang['id_lang']]), 'CMSCategory');
                        }
                    }
                }

                // Add to _shop relations
                if (self::isEmpty($cmsAdditionalThird['cms_category_shop'])) {
                    $cmsCategoryModel->id_shop_list = array((int)Configuration::get('PS_SHOP_DEFAULT'));
                } else {
                    $cmsCategorysShopsRelations = $this->getChangedIdShop($cmsAdditionalThird['cms_category_shop'], 'id_cms_category');
                    if (array_key_exists($cmsCategory['id_cms_category'], $cmsCategorysShopsRelations)) {
                        $cmsCategoryModel->id_shop_list = array_values($cmsCategorysShopsRelations[$cmsCategory['id_cms_category']]);
                    }
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($cmsCategoryModel);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($cmsCategoryModel->id && CMSCategory::existsInDatabase($cmsCategoryModel->id, 'cms_category')) {
                        try {
                            $res = $cmsCategoryModel->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        try {
                            $res = $cmsCategoryModel->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('CMS Category(ID: %1$s) can not be saved. %2$s')), (isset($cmsCategory['id_cms_category']) && !self::isEmpty($cmsCategory['id_cms_category'])) ? Tools::safeOutput($cmsCategory['id_cms_category']) : 'No ID', $err_tmp), 'CMSCategory');
                    } else {
                        // Import CMS Block
                        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                            foreach ($cmsAdditionalSecond['cms_block'] as $cmsBlock) {
                                $sql_value = '';
                                if ($cmsBlock['id_cms_category'] == $cmsCategory['id_cms_category']) {
                                    $sql_value = '(' . (int)$cmsCategoryModel->id . ', ' . pSQL($cmsBlock['location']) . ', ' . (int)$cmsBlock['position'] . ', ' . pSQL($cmsBlock['display_store']) . ')';
                                }
                                if (!self::isEmpty($sql_value)) {
                                    $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cms_block` (`id_cms_category`, `location`,
                                    `position`, `display_store`)
                                VALUES ' . $sql_value);
                                    if (!$result) {
                                        $this->showMigrationMessageAndLog(self::displayError('Can\'t add cms_block. ' . Db::getInstance()->getMsgError()), 'CMS');
                                    } else {
                                        $id_cms_block = Db::getInstance()->Insert_ID();

                                        // Import CMS Block Lang
                                        foreach ($cmsAdditionalThird['cms_block_lang'] as $cmsBlockLang) {
                                            $sql_value = '';
                                            if ($cmsBlockLang['id_cms_block'] == $cmsBlock['id_cms_block']) {
                                                $sql_value = '(' . (int)$id_cms_block . ', ' . self::getLanguageID($cmsBlockLang['id_lang']) . ', \'' . pSQL($cmsBlockLang['name']) . '\')';
                                            }
                                            if (!self::isEmpty($sql_value)) {
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cms_block_lang` (`id_cms_block`, `id_lang`, `name`)
                                                                VALUES ' . $sql_value);
                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cms_block_lang. ' . Db::getInstance()->getMsgError()), 'CMS');
                                                }
                                            }
                                        }

                                        // Import CMS Block Shop
                                        foreach ($cmsAdditionalThird['cms_block_shop'] as $cmsBlockShop) {
                                            $sql_value = '';
                                            if ($cmsBlockShop['id_cms_block'] == $cmsBlock['id_cms_block']) {
                                                $sql_value = '(' . (int)$id_cms_block . ', ' . self::getShopID($cmsBlockShop['id_shop']) . ')';
                                            }
                                            if (!self::isEmpty($sql_value)) {
                                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cms_block_shop` (`id_cms_block`, `id_shop`)
                                                                VALUES ' . $sql_value);
                                                if (!$result) {
                                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cms_block_shop. ' . Db::getInstance()->getMsgError()), 'CMS');
                                                }
                                            }
                                        }
                                        self::addLog('CMSBLOCK', $cmsBlock['id_cms_block'], $id_cms_block);
                                    }
                                }
                            }
                        }
                        if (count($this->error_msg) == 0) {
                            self::addLog('CMSCategory', $cmsCategory['id_cms_category'], $cmsCategoryModel->id);

                            //update multistore language fields
                            if (!version_compare($this->version, '1.6', '<')) {
                                if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                                    foreach ($cmsAdditionalThird['cms_category_lang'] as $lang) {
                                        if ($lang['id_cms_category'] == $cmsCategory['id_cms_category']) {
                                            $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                            $lang['id_cms_category'] = $cmsCategoryModel->id;
                                            self::updateMultiStoreLang('cms_category', $lang);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'CMSCategory');
                }
            }
        }
        foreach ($cmses as $cms) {
            if ($cmsObj = $this->createObjectModel('CMS', $cms['id_cms'])) {
                $cmsObj->id_cms_category = self::getLocalID('CMSCategory', $cms['id_cms_category'], 'data');
                if (self::isEmpty($cmsObj->id_cms_category)) {
                    $cmsObj->id_cms_category = 1;
                }
                $cmsObj->position = $cms['position'];
                $cmsObj->active = $cms['active'];
                $cmsObj->indexation = $cms['indexation'];
                foreach ($cmsAdditionalSecond['cms_lang'] as $lang) {
                    if ($lang['id_cms'] == $cms['id_cms']) {
                        $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                        $cmsObj->meta_title[$lang['id_lang']] = $lang['meta_title'];
                        $cmsObj->meta_description[$lang['id_lang']] = $lang['meta_description'];
                        $cmsObj->meta_keywords[$lang['id_lang']] = $lang['meta_title'];
                        $cmsObj->content[$lang['id_lang']] = $lang['content'];
                        $cmsObj->link_rewrite[$lang['id_lang']] = $lang['link_rewrite'];

                        if (isset($cmsObj->link_rewrite[$lang['id_lang']]) && !self::isEmpty($cmsObj->link_rewrite[$lang['id_lang']])) {
                            $valid_link = Validate::isLinkRewrite($cmsObj->link_rewrite[$lang['id_lang']]);
                        } else {
                            $valid_link = false;
                        }
                        if (!$valid_link) {
                            $cmsObj->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($cmsObj->name[$lang['id_lang']]);

                            if ($cmsObj->link_rewrite[$lang['id_lang']] == '') {
                                $cmsObj->link_rewrite[$lang['id_lang']] = 'friendly-url-autogeneration-failed';
                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('URL rewriting failed to auto-generate a friendly URL for: %s')), $cmsObj->name[$lang['id_lang']]), 'CMS');
                            }

                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('The link for %1$s (ID: %2$s) was re-written as %3$s.')), $lang['link_rewrite'], (isset($cms['id_cms']) && !self::isEmpty($cms['id_cms'])) ? $cms['id_cms'] : 'null', $cmsObj->link_rewrite[$lang['id_lang']]), 'CMS');
                        }
                    }
                }

                // Add to _shop relations
                $cmsShopsRelations = $this->getChangedIdShop($cmsAdditionalSecond['cms_shop'], 'id_cms');
                if (array_key_exists($cms['id_cms'], $cmsShopsRelations)) {
                    $cmsObj->id_shop_list = array_values($cmsShopsRelations[$cms['id_cms']]);
                }

                $res = false;
                $err_tmp = '';

                $this->validator->setObject($cmsObj);
                $this->validator->checkFields();
                $error_tmp = $this->validator->getValidationMessages();
                if (self::isEmpty($error_tmp)) {
                    if ($cmsObj->id && CMS::existsInDatabase($cmsObj->id, 'cms')) {
                        try {
                            $res = $cmsObj->update();
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }

                    if (!$res) {
                        try {
                            $res = $cmsObj->add(false);
                        } catch (PrestaShopException $e) {
                            $err_tmp = $e->getMessage();
                        }
                    }
                    if (!$res) {
                        $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('CMS (ID: %1$s) can not be saved. %2$s')), (isset($cms['id_cms']) && !self::isEmpty($cms['id_cms'])) ? Tools::safeOutput($cms['id_cms']) : 'No ID', $err_tmp), 'CMS');
                    } else {
                        // Import CMS Role
                        if (!($this->version < 1.6)) {
                            foreach ($cmsAdditionalSecond['cms_role'] as $cmsRole) {
                                if ($cmsRole['id_cms'] == $cms['id_cms']) {
                                    if ($cmsRoleModel = $this->createObjectModel('CMSRole', $cmsRole['id_cms_role'])) {
                                        foreach ($cmsAdditionalThird['cms_role_lang'] as $lang) {
                                            if ($lang['id_cms_role'] == $cmsRole['id_cms_role']) {
                                                $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                                $cmsObj->name[$lang['id_lang']] = $lang['name'];
                                                $cmsObj->id_cms = $cmsObj->id;
                                            }
                                        }
                                        $cmsRoleModel->name = $cmsRole['name'];
                                        $cmsRoleModel->id_cms = $cmsObj->id;

                                        $res = false;
                                        $err_tmp = '';

                                        $this->validator->setObject($cmsRoleModel);
                                        $this->validator->checkFields();
                                        $error_tmp = $this->validator->getValidationMessages();
                                        if (self::isEmpty($error_tmp)) {
                                            if ($cmsRoleModel->id && CMSRole::existsInDatabase($cmsRoleModel->id, 'cms_role')) {
                                                try {
                                                    $res = $cmsRoleModel->update();
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }
                                            if (!$res) {
                                                try {
                                                    $res = $cmsRoleModel->add(false);
                                                } catch (PrestaShopException $e) {
                                                    $err_tmp = $e->getMessage();
                                                }
                                            }

                                            if (!$res) {
                                                $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('CMS Role(ID: %1$s) can not be saved. %2$s')), (isset($cmsRole['id_cms_role']) && !self::isEmpty($cmsRole['id_cms_role'])) ? Tools::safeOutput($cmsRole['id_cms_role']) : 'No ID', $err_tmp), 'CMSRole');
                                            } else {
                                                self::addLog('CMSRole', $cmsRole['id_cms_role'], $cmsRoleModel->id);
                                                //update multistore language fields
                                                if (!version_compare($this->version, '1.6', '<')) {
                                                    if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                                                        foreach ($cmsAdditionalThird['cms_role_lang'] as $lang) {
                                                            if ($lang['id_cms_role'] == $cmsRole['id_cms_role']) {
                                                                $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                                                $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                                                $lang['id_cms_role'] = $cmsRoleModel->id;
                                                                self::updateMultiStoreLang('cms_role', $lang);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $this->showMigrationMessageAndLog($error_tmp, 'CMSRole');
                                        }
                                    }
                                }
                                self::addLog('CMSRole', $cms['id_cms'], $cmsObj->id);
                            }
                        }

                        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                            // Import CMS Block Page
                            $sql_values = array();
                            foreach ($cmsAdditionalThird['cms_block_page'] as $cmsBlockPage) {
                                if ($cmsBlockPage['id_cms'] == $cms['id_cms']) {
                                    $sql_values[] = '(' . self::getLocalID('cmsBlock', $cmsBlockPage['id_cms_block'], 'data') . ', ' . (int)$cmsObj->id . ', ' . $cmsBlockPage['is_category'] . ')';
                                }
                            }
                            if (!self::isEmpty($sql_values)) {
                                $result = Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'cms_block_page` (`id_cms_block`, `id_cms`,
                                                                `is_category`)
                                                                VALUES ' . implode(',', $sql_values));
                                if (!$result) {
                                    $this->showMigrationMessageAndLog(self::displayError('Can\'t add cms_block_page. ' . Db::getInstance()->getMsgError()), 'CMS');
                                }
                            }
                        }

                        if (count($this->error_msg) == 0) {
                            self::addLog('CMS', $cms['id_cms'], $cmsObj->id);
                            //update multistore language fields
                            if (!version_compare($this->version, '1.6', '<')) {
                                if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                                    foreach ($cmsAdditionalSecond['cms_lang'] as $lang) {
                                        if ($lang['id_cms'] == $cms['id_cms']) {
                                            $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                            $lang['id_cms'] = $cmsObj->id;
                                            self::updateMultiStoreLang('cms', $lang);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $this->showMigrationMessageAndLog($error_tmp, 'CMS');
                }
            }
        }

        $this->updateProcess(count($cmses));
    }

    /**
     * @param $metas
     * @param $metaLang
     */
    public function metas($metas, $metaLang)
    {
        foreach ($metas as $meta) {
            if (in_array($meta['page'], Meta::getpages())) {
                if ($metaObj = $this->createObjectModel('Meta', $meta['id_meta'])) {
                    $metaObj->page = $meta['page'];
                    $metaObj->configurable = $meta['configurable'];
                    if (self::isEmpty($metaObj->configurable)) {
                        $metaObj->configurable = 1;
                    }
                    foreach ($metaLang as $lang) {
                        if ($lang['id_meta'] == $meta['id_meta']) {
                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                            $metaObj->title[$lang['id_lang']] = $lang['title'];
                            $metaObj->description[$lang['id_lang']] = $lang['description'];
                            $metaObj->keywords[$lang['id_lang']] = $lang['keywords'];
                            $metaObj->url_rewrite[$lang['id_lang']] = $lang['url_rewrite'];

                            if (!ValidateCore::isLinkRewrite($metaObj->url_rewrite[$lang['id_lang']])) {
                                $metaObj->url_rewrite[$lang['id_lang']] = Tools::link_rewrite($lang['title']);
                            }
                        }
                    }

                    $res = false;
                    $err_tmp = '';

                    $this->validator->setObject($metaObj);
                    $this->validator->checkFields();
                    $error_tmp = $this->validator->getValidationMessages();
                    if (self::isEmpty($error_tmp)) {
                        if (Db::getInstance()->getValue('SELECT * FROM ' . _DB_PREFIX_ . 'meta WHERE page = \'' . $meta['page'] . '\'') != 0) {
                            try {
                                $res = $metaObj->update();
                            } catch (PrestaShopException $e) {
                                $err_tmp = $e->getMessage();
                            }
                        }

                        if (!$res) {
                            try {
                                $res = $metaObj->add(false);
                            } catch (PrestaShopException $e) {
                                $err_tmp = $e->getMessage();
                            }
                        }
                        if (!$res) {
                            $this->showMigrationMessageAndLog(sprintf(self::displayError($this->module->l('Meta (ID: %1$s) can not be saved. %2$s')), (isset($meta['id_meta']) && !self::isEmpty($meta['id_meta'])) ? Tools::safeOutput($meta['id_meta']) : 'No ID', $err_tmp), 'meta');
                        } else {
                            $url = $this->url . $this->image_path . $meta['id_meta'] . '.jpg';
                            if (self::imageExits($url) && !(EDImport::copyImg($metaObj->id, null, $url, 'metas', $this->regenerate))) {
                                $this->showMigrationMessageAndLog($url . ' ' . self::displayError($this->module->l('can not be copied.')), 'meta', true);
                            }
                            self::addLog('meta', $meta['id_meta'], $metaObj->id);

                            //update multistore language fields
                            if (!version_compare($this->version, '1.5', '<')) {
                                if (MigrationProMapping::getMapTypeCount('multi_shops') > 1) {
                                    foreach ($metaLang as $lang) {
                                        if ($lang['id_meta'] == $meta['id_meta']) {
                                            $lang['id_shop'] = self::getShopID($lang['id_shop']);
                                            $lang['id_lang'] = self::getLanguageID($lang['id_lang']);
                                            $lang['id_meta'] = $metaObj->id;
                                            self::updateMultiStoreLang('meta', $lang);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $this->showMigrationMessageAndLog($error_tmp, 'meta');
                    }
                }
            }
        }

        $this->updateProcess(count($metas));
    }

    // --- Internal helper methods:

    private function createObjectModel($className, $objectID, $table_name = '')
    {
        if (!MigrationProData::exist($className, $objectID)) {
            // -- if keep old IDs and if exists in DataBase
            // -- else  isset($objectID) 1&& (int)$objectID

            if (!self::isEmpty($table_name)) {
                $existInDataBase = self::existsInDatabase((int)$objectID, Tools::strtolower($table_name), Tools::strtolower($className));
            } else {
                $existInDataBase = $className::existsInDatabase((int)$objectID, $className::$definition['table']);
                // [For PrestaShop Team] - This code call class definition attribute extended from ObjectModel class
                // like Order::$definition
            }

            if ($existInDataBase && $this->force_ids) {
                $this->obj = new $className((int)$objectID);
            } else {
                $this->obj = new $className();
            }

            if ($this->force_ids) {
                $this->obj->force_id = true;
                $this->obj->id = $objectID;
            }

//            if ($this->force_ids) {
//                if (!$existInDataBase) {
//                    $this->obj = new $className();
//                    $this->obj->force_id = true;
//                    $this->obj->id = $objectID;
//                } else {
//                    $this->obj = new $className((int)$objectID);
//                    /*if (Validate::isLoadedObject($this->obj) && (method_exists($this->obj, 'isUsed') && $this->obj->isUsed())
//                    ) {
//                        $this->obj->delete();
//                        $this->obj->force_id = true;
//                        $this->obj->id = $objectID;
//                    }*/
//                }
//
//            } else {
//                $this->obj = new $className();
//            }

            return $this->obj;
        }
//        return false;
    }

    private function updateProcess($count)
    {
        if (!count($this->error_msg) && $count > 0) {
            $this->process->imported += $count;//@TODO count of item
//            $this->process->id_source = $source_id;
            if ($this->process->total <= $this->process->imported) {
                $this->process->finish = 1;
                $this->response['execute_time'] = number_format((time() - strtotime($this->process->time_start)), 3, '.', '');
            }
            $this->response['type'] = $this->process->type;
            $this->response['total'] = (int)$this->process->total;
            $this->response['imported'] = (int)$this->process->imported;
            if ($this->process->finish == 1) {
                $this->response['process'] = 'finish';
                $allWarningMessages = $this->logger->getAllWarnings();
                $this->warning_msg = $allWarningMessages;
            } else {
                $this->response['process'] = 'continue';
            }
            $this->process->save();
        } else {
            if (!$this->ps_validation_errors) {
                $this->error_msg[] = self::displayError($this->module->l('Something went wrong. Source server return with null'));
            }
        }
    }

    private static function existsInDatabase($id_entity, $table, $entity_name)
    {
        $row = Db::getInstance()->getRow('
			SELECT `id_' . bqSQL($entity_name) . '` as id
			FROM `' . _DB_PREFIX_ . bqSQL($table) . '` e
			WHERE e.`id_' . bqSQL($entity_name) . '` = ' . (int)$id_entity, false);

        return isset($row['id']);
    }

    private static function copyImg($id_entity, $id_image, $url, $entity = 'products', $regenerate = false)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));
        if (self::isEmpty($id_image)) {
            $id_image = null;
        }
        switch ($entity) {
            default:
            case 'carriers':
                $path = _PS_SHIP_IMG_DIR_ . (int)$id_entity;
                break;
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int)$id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int)$id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int)$id_entity;
                break;
            case 'employees':
                $path = _PS_EMPLOYEE_IMG_DIR_ . (int)$id_entity;
                break;
            case 'attributes':
                $path = _PS_COL_IMG_DIR_ . (int)$id_entity;
                break;
        }

        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/' . implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_ . 'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parced_url);
        // [For PrestaShop Team] Before called require_once(_PS_TOOL_DIR_ . 'http_build_url/http_build_url.php');

        $orig_tmpfile = $tmpfile;

        $opts = array(
            "http" => array(
                "header" => "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
            )
        );

        $context = stream_context_create($opts);

        if (self::copy($url, $tmpfile, $context)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);

                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
            if ($regenerate) { //@TODO add to step-2 regenerate images after import
                $images_types = ImageType::getImagesTypes($entity, true);
//                $previous_path = null;
                $path_infos = array();
                $path_infos[] = array($tgt_width, $tgt_height, $path . '.jpg');
                foreach ($images_types as $image_type) {
                    $tmpfile = self::getBestPath($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize($tmpfile, $path . '-' . Tools::stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height'], 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height)) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array(
                                $tgt_width,
                                $tgt_height,
                                $path . '-' . Tools::stripslashes($image_type['name']) . '.jpg'
                            );
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '_' . (int)Context::getContext()->shop->id . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_entity . '_' . (int)Context::getContext()->shop->id . '.jpg');
                            }
                        }
                    }
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);

            return false;
        }
        unlink($orig_tmpfile);

        return true;
    }

    private static function getBestPath($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }

        return $path;
    }

    private static function imageExits($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
        ));
        curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response_code === 200) {
            return true;
        } else {
            return false;
        }
    }

    public static function copy($source, $destination, $stream_context = null)
    {
        if (is_null($stream_context) && !preg_match('/^https?:\/\//', $source)) {
            return @copy($source, $destination);
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return @file_put_contents($destination, Tools::file_get_contents($source, false, $stream_context));
        } else {
            return @file_put_contents($destination, self::fileGetContentsCurl($source, 5, $stream_context));
        }
    }

    private static function fileGetContentsCurl(
        $url,
        $curl_timeout,
        $opts
    ) {
        $content = false;

        if (function_exists('curl_init')) {
            Tools::refreshCACertFile();
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

            if ($opts != null) {
                if (isset($opts['http']['method']) && Tools::strtolower($opts['http']['method']) == 'post') {
                    curl_setopt($curl, CURLOPT_POST, true);
                    if (isset($opts['http']['content'])) {
                        parse_str($opts['http']['content'], $post_data);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                    }
                }
            }
            $content = curl_exec($curl);
            curl_close($curl);
        }

        return $content;
    }

    private function getLocalID($map_type, $sourceID, $table_type = 'map')
    {
        if ($table_type === "map") {
            $result = (isset($this->mapping[$map_type][$sourceID]) && !self::isEmpty($this->mapping[$map_type][$sourceID])) ? $this->mapping[$map_type][$sourceID] : 0;
        } else {
            $result = MigrationProData::getLocalID($map_type, $sourceID);
            if (self::isEmpty($result)) {
                $result = MigrationProMigratedData::getLocalID($map_type, $sourceID);
            }
        }

        return (int)$result;
    }

    private function getCarrierReference($id_carrier)
    {
        return Db::getInstance()->getValue('SELECT id_reference FROM ' . _DB_PREFIX_ . 'carrier WHERE id_carrier = ' . (int)$id_carrier . '');
    }

    private function getLanguageID($source_lang_id)
    {
        return $this->getLocalID('languages', $source_lang_id);
    }

    private function getShopID($source_shop_id)
    {
        return $this->getLocalID('multi_shops', $source_shop_id);
    }

    private function getCurrencyID($source_currency_id)
    {
        return $this->getLocalID('currencies', $source_currency_id);
    }

    private function getOrderStateID($source_order_state_id)
    {
        return $this->getLocalID('order_states', $source_order_state_id);
    }

    private function getCustomerGroupID($source_customer_group_id)
    {
        return $this->getLocalID('customer_groups', $source_customer_group_id);
    }

    private static function defaultValue($input, $default)
    {
        if (isset($input) && !self::isEmpty($input)) {
            return $input;
        } else {
            return $default;
        }
    }

    private function getChangedIdShop($dataFromSourceCart, $idKeyName)
    {
        $result = array();

        foreach ($dataFromSourceCart as $data) {
            if (self::getShopID($data['id_shop']) != 0) {
                $result[$data[$idKeyName]][] = self::getShopID($data['id_shop']);
            }
        }

        return $result;
    }

    public static function displayError($string = 'Fatal error', $htmlentities = false)
    {
        return $htmlentities ? Tools::htmlentitiesUTF8(Tools::stripslashes($string)) : $string;
    }

    public static function addLog($entity_type, $source_id, $local_id)
    {
        MigrationProData::import((string)$entity_type, (int)$source_id, (int)$local_id);
        MigrationProMigratedData::import((string)$entity_type, (int)$source_id, (int)$local_id);
    }

    public static function isEmpty($field)
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            return ($field === '' || $field === null || $field === array() || $field === 0 || $field === '0');
        } else {
            return empty($field);
        }
    }

    public function updateMultiStoreLang($entity, $properties)
    {
        $keys = self::quotaToProperty(array_keys($properties));
        $values = self::quotaToProperty(array_values($properties));
        $result = Db::getInstance()->execute("REPLACE INTO " . _DB_PREFIX_ . $entity . "_lang (" . implode(', ', $keys) . ") VALUES  ('" . implode("','", $values) . "')");
        return $result;
    }

    public function quotaToProperty($properties)
    {
        $result = array();

        foreach ($properties as $value) {
            $result[] = pSQL($value, true);
        }

        return $result;
    }

    public static function importAccessories($accessory)
    {
        $result = Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'accessory (id_product_1, id_product_2) VALUES (' . (int)$accessory["id_product_1"] . ', ' . (int)$accessory["id_product_2"] . ')');
        return $result;
    }

    private function showMigrationMessageAndLog($log, $entityType, $showOnlyWarning = false)
    {
        if ($this->ps_validation_errors) {
            if ($showOnlyWarning) {
                if (is_array($log)) {
                    foreach ($log as $logIndex => $logText) {
                        $this->logger->addWarningLog($logText, $entityType);
                    }
                } else {
                    $this->logger->addWarningLog($log, $entityType);
                }
            } else {
                if (is_array($log)) {
                    foreach ($log as $logIndex => $logText) {
                        $this->logger->addErrorLog($logText, $entityType);
                        $this->error_msg[] = $logText;
                    }
                } else {
                    $this->logger->addErrorLog($log, $entityType);
                    $this->error_msg[] = $log;
                }
            }
        } else {
            if (is_array($log)) {
                foreach ($log as $logIndex => $logText) {
                    $this->logger->addWarningLog($logText, $entityType);
                }
            } else {
                $this->logger->addWarningLog($log, $entityType);
            }
        }
    }
}
