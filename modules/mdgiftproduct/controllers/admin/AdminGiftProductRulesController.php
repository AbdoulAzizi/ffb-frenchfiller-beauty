<?php
/**
 * 2021-2022
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize the module for your
 * needs please refer to
 * http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
 * for more information.
 *
 * @author    Digincube <digincubeagency@gmail.com>
 * @copyright 2021-2022 Digincube
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'mdgiftproduct/mdgiftproduct.php';
if (!class_exists('MdGiftRule')) {
    require_once(_PS_MODULE_DIR_.'mdgiftproduct/classes/models/MdGiftRule.php');
    require_once(_PS_MODULE_DIR_.'mdgiftproduct/classes/models/MdGiftRuleCondition.php');
    require_once(_PS_MODULE_DIR_.'mdgiftproduct/classes/models/MdGiftRuleProduct.php');
}

class AdminGiftProductRulesController extends ModuleAdminController
{
    protected $isShopSelected = true;
    public $module;
    public $context;
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = MdGiftRule::$definition['table'];
        $this->tabClassName = 'AdminGiftProductRules';
        $this->className = MdGiftRule::class;
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_orderWay = 'DESC';
        parent::__construct();
 
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items ?'),
                'icon' => 'icon-trash'
            ),
        );
        
        $this->fields_list = array(
            'id_mdgift_rule' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'filter_key' => 'b!name',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'type' => 'bool',
                'callback' => 'printIconStatus',
                'align' => 'center',
                'orderby' => false,
            ),
            'date_from' => array(
                'title' => $this->l('From date'),
                'type' => 'datetime'
            ),
            'date_to' => array(
                'title' => $this->l('To date'),
                'type' => 'datetime'
            )
        );
        if (Shop::isFeatureActive() &&
            (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)) {
            $this->isShopSelected = false;
        }

        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        } else {
            $this->shopLinkType = 'shop';
        }
        $this->condition_restrictions = array('group', 'product', 'category',
        'attribute', 'feature', 'manufacturer', 'supplier', 'gender');
    }
    
    
    public function printIconStatus($value, $mdGiftRule)
    {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?'.htmlspecialchars('tab='.$this->controller_name.'&id_mdgift_rule='.(int)$mdGiftRule['id_mdgift_rule'].'&changeStatus&token='.Tools::getAdminTokenLite('AdminGiftProductRules')).'">'.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').'</a>';
    }
    
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_gift_rule'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new rule', null, null, false),
                'icon' => 'process-icon-new'
            );

            $this->page_header_toolbar_btn['desc-module-translate'] = array(
                'href' => '#',
                'desc' => $this->l('Translate'),
                'modal_target' => '#moduleTradLangSelect',
                'icon' => 'process-icon-flag'
            );
        }

        parent::initPageHeaderToolbar();

        $this->context->smarty->clearAssign('help_link', '');
    }
    
    public function initToolbar()
    {
        parent::initToolbar();
    }
    
    public function initModal()
    {
        parent::initModal();

        $languages = Language::getLanguages(false);
        $translateLinks = array();

        if (version_compare(_PS_VERSION_, '1.7.2.1', '>=')) {
            $isNewTranslateSystem = $this->module->isUsingNewTranslationSystem();
            $link = Context::getContext()->link;
            foreach ($languages as $lang) {
                if ($isNewTranslateSystem) {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslationSf', true, array(
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $this->module->name,
                        'locale' => $lang['locale'],
                    ));
                } else {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslations', true, array(), array(
                        'type' => 'modules',
                        'module' => $this->module->name,
                        'lang' => $lang['iso_code'],
                    ));
                }
            }
        }

        $this->context->smarty->assign(array(
            'trad_link' => 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&module='.$this->module->name.'&lang=',
            'module_languages' => $languages,
            'module_name' => $this->module->name,
            'translateLinks' => $translateLinks,
        ));

        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');

        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );
    }
    public function renderForm()
    {
        if (Tools::getValue('magic')) {
            return $this->module->getContent();
        }

        if (!$this->isShopSelected && $this->display == 'add') {
            $this->errors[] = $this->l('Please select a shop.');
            return;
        }

        $this->toolbar_btn['save-and-stay'] = array(
            'href' => '#',
            'desc' => $this->l('Save and Stay')
        );
        if (!$current_object = $this->loadObject(true)) {
            return;
        }

        $ruleConditions = $ruleProducts = array();
        if (Validate::isLoadedObject($current_object)) {
            $conditions = $current_object->getConditions();
            $products = $current_object->getProducts();
            foreach ($conditions as $condition) {
                $ruleConditions[] = new MdGiftRuleCondition((int)$condition['id_mdgift_rule_condition']);
            }
            
            foreach ($products as $product) {
                $ruleProducts[] = new MdGiftRuleProduct((int)$product['id_mdgift_rule_product']);
            }
        } else {
            if (Tools::getValue('condition_condition_type')) {
                $ruleConditions = $this->getConditionsFromPost();
            }
        }
        if (!array_filter($ruleConditions)) {
            $mdGiftRuleCondition = new MdGiftRuleCondition();
            $mdGiftRuleCondition->id_mdgift_rule_condition = 1;
            $ruleConditions[1] = $mdGiftRuleCondition;
        }
        if (!array_filter($ruleProducts)) {
            $mdGiftRuleProduct = new MdGiftRuleProduct();
            $mdGiftRuleProduct->id_mdgift_rule_product = 1;
            $ruleProducts[1] = $mdGiftRuleProduct;
        }
        
        $times_used = Db::getInstance()->getValue(
            "SELECT count(distinct(o.`id_order`))
            FROM `"._DB_PREFIX_."orders` o
            LEFT JOIN "._DB_PREFIX_."order_cart_rule od ON o.id_order = od.id_order
            LEFT JOIN "._DB_PREFIX_."mdgift_rule_cart mdgrc ON od.id_cart_rule = mdgrc.id_cart_rule
            WHERE mdgrc.id_mdgift_rule = ".(int)$current_object->id."
            AND ".(int)Configuration::get('PS_OS_ERROR')." != o.current_state"
        );
        $this->context->controller->addCSS($this->module->getPathUri().'views/css/jquery.datetimepicker.min.css');
        $this->context->controller->addCSS($this->module->getPathUri().'views/css/jquery.businessHours.css');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/jquery.datetimepicker.full.min.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/jquery.businessHours.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/admin.js');

        $this->context->smarty->assign(
            array(
                'show_toolbar'          => true,
                'toolbar_btn'           => $this->toolbar_btn,
                'toolbar_scroll'        => $this->toolbar_scroll,
                'defaultDateFrom'       => date('Y-m-d H:00:00'),
                'defaultDateTo'         => date('Y-m-d H:00:00', strtotime('+10 year')),
                'conditions'            => $ruleConditions,
                'rproducts'            => $ruleProducts,
                'show_button'           => true,
                'defaultCurrency'       => Configuration::get('PS_CURRENCY_DEFAULT'),
                'display_language'      => Configuration::get('PS_LANG_DEFAULT'),
                'languages'             => Language::getLanguages(false),
                'currencies'            => Currency::getCurrencies(false, false, true),
                'currentIndex'          => self::$currentIndex,
                'tpl_dir'               => $this->getTemplatePath(),
                'module_path'           => $this->module->getPathUri(),
                'currentToken'          => $this->token,
                'currentObject'         => $current_object,
                'currentTab'            => $this,
                'path_css'              => _THEME_CSS_DIR_,
                'ajax'                  => false,
                'times_used'            => $times_used,
                'currentText' => $this->l('Now'),
                'closeText' => $this->l('Done'),
                'timeOnlyTitle' => $this->l('Choose Time'),
                'timeText' => $this->l('Time'),
                'hourText' => $this->l('Hour'),
                'minuteText' => $this->l('Minute'),
            )
        );
        
        $this->content .= $this->createTemplate('form.tpl')->fetch();
        $this->addJqueryPlugin(array('jscroll', 'typewatch'));
        

        return parent::renderForm();
    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin(array('autocomplete'));
        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryUI('ui.button');
        $this->addJqueryUI('ui.sortable');
        $this->addJqueryUI('ui.droppable');
        
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->addCSS('https://fonts.googleapis.com/icon?family=Material+Icons');
        }
        $this->addCSS(_MODULE_DIR_.'mdgiftproduct/views/css/admin.css');
    }
    public function ajaxProcessFindProducts()
    {
        $mdGiftRuleProduct = new MdGiftRuleProduct();
        $array = $mdGiftRuleProduct->findProducts(Tools::getValue('product_search'));
        die(trim(Tools::jsonEncode($array)));
    }
    
    public function ajaxProcess()
    {
        if (Tools::isSubmit('newCondition')) {
            $giftProductRuleCondition = new MdGiftRuleCondition();
            $giftProductRuleCondition->id_mdgift_rule_condition = time();
            $this->context->smarty->assign(
                array(
                    'defaultCurrency'   => Configuration::get('PS_CURRENCY_DEFAULT'),
                    'currencies'        => Currency::getCurrencies(false, true),
                    'condition'         => $giftProductRuleCondition,
                    'unique_id'         => time(),
                    'ajax'              => true,
                    'tpl_dir'               => $this->getTemplatePath(),
                )
            );

            die($this->createTemplate('condition.tpl')->fetch());
        }
        if (Tools::isSubmit('newProduct')) {
            $rproduct = new MdGiftRuleProduct();
            $rproduct->id_mdgift_rule_product = time();
            $this->context->smarty->assign(
                array(
                    'rproduct'         => $rproduct,
                    'unique_id'         => time(),
                    'ajax'              => true,
                    'tpl_dir'               => $this->getTemplatePath(),
                )
            );

            die($this->createTemplate('signleproduct.tpl')->fetch());
        }
        if (Tools::isSubmit('findCustomer')) {
            $search = trim(Tools::getValue('q'));
            $result_customers = Db::getInstance()->executeS('
            SELECT `id_customer`, `email`, CONCAT(`firstname`, \' \', `lastname`) as fullname
            FROM `'._DB_PREFIX_.'customer`
            WHERE `deleted` = 0 AND is_guest = 0 AND active = 1
            AND (
                `id_customer` = '.(int)$search.'
                OR `email` LIKE "%'.pSQL($search).'%"
                OR `firstname` LIKE "%'.pSQL($search).'%"
                OR `lastname` LIKE "%'.pSQL($search).'%"
            )
            ORDER BY `firstname`, `lastname` ASC
            LIMIT 65');
            die(Tools::jsonEncode($result_customers));
        }
    }
    public function initProcess()
    {
        parent::initProcess();

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if (Tools::isSubmit('changeStatus') && $this->id_object) {
                if ($this->tabAccess['edit'] === '1') {
                    $this->action = 'change_status';
                } else {
                    $this->errors[] = $this->l('You do not have permission to edit this.');
                }
            } elseif (Tools::getIsset('duplicate'.$this->table)) {
                if ($this->tabAccess['add'] === '1') {
                    $this->action = 'duplicate';
                } else {
                    $this->errors[] = $this->l('You do not have permission to add this.');
                }
            }
        } else {
            if (Tools::isSubmit('changeStatus') && $this->id_object) {
                if ($this->access('edit')) {
                    $this->action = 'change_status';
                } else {
                    $this->errors[] = $this->l('You do not have permission to edit this.');
                }
            } elseif (Tools::getIsset('duplicate'.$this->table)) {
                if ($this->access('add')) {
                    $this->action = 'duplicate';
                } else {
                    $this->errors[] = $this->l('You do not have permission to add this.');
                }
            }
        }
    }
    
    
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddmdgift_rule') || Tools::isSubmit('submitAddmdgift_ruleAndStay')) {
            if (strtotime(Tools::getValue('date_from')) > strtotime(Tools::getValue('date_to'))) {
                $this->errors[] = $this->module->l('End date must be great than start date.', 'AdminGiftProductRulesController');
            }

            //Validate conditions
            $form_values = array();
            $definition = ObjectModel::getDefinition('mdGiftRuleCondition');
            foreach (array_keys($definition['fields']) as $condition_var) {
                $form_values[$condition_var] = Tools::getValue('cdt_'.$condition_var);
            }
            if ($form_values['condition_type'] && array_filter($form_values['condition_type'])) {
                foreach (array_keys($form_values['condition_type']) as $id_mdgift_rule_condition) {
                    $condition_type = $form_values['condition_type'][$id_mdgift_rule_condition];
                    if ($condition_type) {
                        switch ($condition_type) {
                            // total amount
                            case "total_cart_amount":
                                if (isset($form_values['cart_amount'][$id_mdgift_rule_condition])
                                    && $form_values['cart_amount'][$id_mdgift_rule_condition] <= 0) {
                                    $this->errors[] = $this->module->l('Please enter correct total cart amount in tab "Conditions"', 'AdminGiftProductRulesController');
                                }
                                break;
                            //Products in the cart
                            case "products_cart":
                                if (isset($form_values['restriction_product'][$id_mdgift_rule_condition])
                                    && $form_values['restriction_product'][$id_mdgift_rule_condition]
                                    && empty(Tools::getValue('cdt_selected_product')[$id_mdgift_rule_condition])) {
                                    $this->errors[] = $this->module->l('If you filter by product, you have to select at least 1 product', 'AdminGiftProductRulesController');
                                }
                                
                                if (isset($form_values['restriction_attribute'][$id_mdgift_rule_condition])
                                    && $form_values['restriction_attribute'][$id_mdgift_rule_condition]
                                    && empty(Tools::getValue('cdt_selected_attribute')[$id_mdgift_rule_condition])) {
                                    $this->errors[] = $this->module->l('If you filter by attribute, you have to select at least 1 attribute', 'AdminGiftProductRulesController');
                                }
                                
                                if (isset($form_values['restriction_feature'][$id_mdgift_rule_condition])
                                    && $form_values['restriction_feature'][$id_mdgift_rule_condition]
                                    && empty(Tools::getValue('cdt_selected_feature')[$id_mdgift_rule_condition])) {
                                    $this->errors[] = $this->module->l('If you filter by feature, you have to select at least 1 feature', 'AdminGiftProductRulesController');
                                }
                                
                                if (isset($form_values['restriction_category'][$id_mdgift_rule_condition])
                                    && $form_values['restriction_category'][$id_mdgift_rule_condition]
                                    && empty(Tools::getValue('cdt_selected_category')[$id_mdgift_rule_condition])) {
                                    $this->errors[] = $this->module->l('If you filter by category, you have to select at least 1 category', 'AdminGiftProductRulesController');
                                }
                                
                                if (isset($form_values['restriction_supplier'][$id_mdgift_rule_condition])
                                    && $form_values['restriction_supplier'][$id_mdgift_rule_condition]
                                    && empty(Tools::getValue('cdt_selected_supplier')[$id_mdgift_rule_condition])) {
                                    $this->errors[] = $this->module->l('If you filter by supplier, you have to select at least 1 supplier', 'AdminGiftProductRulesController');
                                }
                                if (isset($form_values['restriction_manufacturer'][$id_mdgift_rule_condition])
                                    && $form_values['restriction_manufacturer'][$id_mdgift_rule_condition]
                                    && empty(Tools::getValue('cdt_selected_manufacturer')[$id_mdgift_rule_condition])) {
                                    $this->errors[] = $this->module->l('If you filter by manufacturer, you have to select at least 1 manufacturer', 'AdminGiftProductRulesController');
                                }
                                
                                break;
                            //Customer age
                            case "customer_age":
                                if (isset($form_values['age_from'][$id_mdgift_rule_condition])
                                    && !(int)$form_values['age_from'][$id_mdgift_rule_condition]) {
                                    $this->errors[] = $this->module->l('Please enter correct age', 'AdminGiftProductRulesController');
                                }
                                
                                if (isset($form_values['age_to'][$id_mdgift_rule_condition])
                                &&!(int)$form_values['age_to'][$id_mdgift_rule_condition]) {
                                    $this->errors[] = $this->module->l('Please enter correct age', 'AdminGiftProductRulesController');
                                }
                                break;
                            case "single_customer":
                                if (isset($form_values['id_customer'][$id_mdgift_rule_condition])
                                    && !(int)$form_values['id_customer'][$id_mdgift_rule_condition]) {
                                    $this->errors[] = $this->module->l('Please select a customer', 'AdminGiftProductRulesController');
                                }
                                break;
                        }
                    }
                }
            }
        }
        return parent::postProcess();
    }
    
    public function processAdd()
    {
        $gift_rule = parent::processAdd();
        if (isset($gift_rule->id)) {
            $this->handleGiftRuleConditions($gift_rule->id);
            $this->handleGiftRuleProducts($gift_rule->id);
        }
        
         
        return $gift_rule;
    }
 
    public function processUpdate()
    {
        $id_mdgift_rule = (int) Tools::getValue('id_mdgift_rule');
        parent::processUpdate();
        $this->handleGiftRuleConditions($id_mdgift_rule);
        
        $this->handleGiftRuleProducts($id_mdgift_rule);
    }
    
    public function handleGiftRuleProducts($id_mdgift_rule)
    {
        Db::getInstance()->delete('mdgift_rule_product_attribute', '`id_mdgift_rule` = '.(int)$id_mdgift_rule);
        $rule_products_db = MdGiftRuleProduct::getProducts($id_mdgift_rule);
        $posted_id_products = Tools::getValue('id_product');
        $posted_attributes = Tools::getValue('id_product_attribute');
        if (!empty($posted_id_products)) {
            $posted_id_products = array_filter($posted_id_products);
            $product_to_delete = array_diff($rule_products_db, $posted_id_products);
            if (!empty($product_to_delete)) {
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mdgift_rule_product` WHERE `id_mdgift_rule_product` IN ('.implode(',', $product_to_delete).')');
            }
            foreach ($posted_id_products as $id_mdgift_rule_product => $id_product) {
                if (in_array($id_mdgift_rule_product, $rule_products_db)) {
                    $mdGiftRuleProduct = new MdGiftRuleProduct($id_mdgift_rule_product);
                } else {
                    $mdGiftRuleProduct = new MdGiftRuleProduct();
                }
                
                $mdGiftRuleProduct->id_mdgift_rule = $id_mdgift_rule;
                $mdGiftRuleProduct->id_product = $id_product;
                if ($mdGiftRuleProduct->save()) {
                    $loop_attributes = isset($posted_attributes[$id_mdgift_rule_product][$id_product]) ?
                            $posted_attributes[$id_mdgift_rule_product][$id_product] :[];
                    
                    if (!empty($loop_attributes)) {
                        $values = array();
                        foreach ($loop_attributes as $id_product_attribute) {
                            $values[] = '('.(int)$id_mdgift_rule.','.(int)$id_product.','.(int)$id_product_attribute.')';
                        }
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'mdgift_rule_product_attribute` (`id_mdgift_rule`, `id_product`, `id_product_attribute`) VALUES '.implode(',', $values));
                    }
                }
            }
        } else {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mdgift_rule_product` WHERE `id_mdgift_rule` = '.(int) $id_mdgift_rule);
        }
    }
    
    public function handleGiftRuleConditions($id_mdgift_rule)
    {
        $conditions_ids = MdGiftRuleCondition::getConditions($id_mdgift_rule);
        $form_values = array();
        $definition = ObjectModel::getDefinition('mdGiftRuleCondition');
        foreach (array_keys($definition['fields']) as $condition_var) {
            $form_values[$condition_var] = Tools::getValue('cdt_'.$condition_var);
        }
        $values = array();
        $delete_conditions = array_diff($conditions_ids, array_keys($form_values['condition_type']));
        if (!empty($delete_conditions)) {
            foreach ($delete_conditions as $id_mdgift_rule_condition) {
                $mdGiftRuleCondition = new MdGiftRuleCondition($id_mdgift_rule_condition);
                $mdGiftRuleCondition->delete();
                foreach ($this->condition_restrictions as $type) {
                    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'` WHERE `id_mdgift_rule` = '.(int)$id_mdgift_rule.' AND `id_mdgift_rule_condition` = '.(int) $mdGiftRuleCondition->id);
                }
            }
        }
        if ($form_values['condition_type'] && !empty(array_filter($form_values['condition_type']))) {
            foreach (array_keys($form_values['condition_type']) as $id_mdgift_rule_condition) {
                if (in_array($id_mdgift_rule_condition, $conditions_ids)) {
                    $mdGiftRuleCondition = new MdGiftRuleCondition($id_mdgift_rule_condition);
                } elseif (!in_array($id_mdgift_rule_condition, $conditions_ids) &&
                    (!empty($conditions_ids) && $id_mdgift_rule_condition > max($conditions_ids) || empty($conditions_ids))) {
                    $mdGiftRuleCondition = new MdGiftRuleCondition();
                }
                
                foreach ($definition['fields'] as $field => $fieldDefinition) {
                    if (array_key_exists($field, $form_values)) {
                        $mdGiftRuleCondition->$field = $form_values[$field][$id_mdgift_rule_condition];
                        if ($fieldDefinition['type'] == ObjectModel::TYPE_DATE && !$mdGiftRuleCondition->$field) {
                            $mdGiftRuleCondition->$field = date('Y-m-d H:i:s', 0);
                        }
                    }
                }
                $mdGiftRuleCondition->id_mdgift_rule = $id_mdgift_rule;
                if ($mdGiftRuleCondition->save()) {
                    foreach ($this->condition_restrictions as $type) {
                        if (!isset($form_values['filter_'.$type][$id_mdgift_rule_condition]) || (isset($form_values['filter_'.$type][$id_mdgift_rule_condition])
                                    && $form_values['filter_'.$type][$id_mdgift_rule_condition])) {
                            $values = array();
                            $selected = Tools::getValue('cdt_selected_'.$type)[$id_mdgift_rule_condition];
                            $old_selected = Tools::getValue('cdt_old_'.$type)[$id_mdgift_rule_condition];
                            
                            if (!empty($old_selected)) {
                                $old_selected = explode(',', $old_selected);
                            } else {
                                $old_selected = [];
                            }
                            
                            $to_delete = array_diff($old_selected, !empty($selected) ? $selected : []);
                            $to_add = array_diff(!empty($selected) ? $selected : [], $old_selected);
                            if (!empty($to_add)) {
                                foreach ($to_add as $id) {
                                    $values[] = '('.(int)$mdGiftRuleCondition->id.','.(int)$id_mdgift_rule.','.(int)$id.')';
                                }
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'` (`id_mdgift_rule_condition`, `id_mdgift_rule`, `id_'.$type.'`) VALUES '.implode(',', $values));
                            }
                            if (!empty($to_delete)) {
                                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'` WHERE `id_mdgift_rule` = '.(int)$id_mdgift_rule.' AND `id_mdgift_rule_condition` = '.(int)$mdGiftRuleCondition->id.' AND `id_'.$type.'` IN ('.implode(',', $to_delete).')');
                            }
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Unexpected error when saving condition.');
                }
            }
        } elseif (!empty($conditions_ids)) {
            foreach ($conditions_ids as $id_mdgift_rule_condition) {
                $mdGiftRuleCondition = new MdGiftRuleCondition($id_mdgift_rule_condition);
                $mdGiftRuleCondition->delete();
                foreach ($this->condition_restrictions as $type) {
                    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'` WHERE `id_mdgift_rule` = '.(int)$id_mdgift_rule.' AND `id_mdgift_rule_condition` = '.(int) $mdGiftRuleCondition->id);
                }
            }
        }
    }
    
    public function processChangeStatus()
    {
        $mdGiftRule = new MdGiftRule($this->id_object);
        if (!Validate::isLoadedObject($mdGiftRule)) {
            $this->errors[] = $this->l('An error occurred while updating gift rule.');
        }

        $mdGiftRule->active = $mdGiftRule->active ? 0 : 1;
        if (!$mdGiftRule->active) {
            MdGiftRule::deleteRulesNotUsed((int)$this->id_object);
        }
        if (!$mdGiftRule->update()) {
            $this->errors[] = $this->l('An error occurred while updating gift rule.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }
    
    public function processDelete()
    {
        $object = $this->loadObject();
        if (Validate::isLoadedObject($object)) {
            MdGiftRule::deleteRulesNotUsed((int)$object->id);
            parent::processDelete();
        }

        return $object;
    }
}
