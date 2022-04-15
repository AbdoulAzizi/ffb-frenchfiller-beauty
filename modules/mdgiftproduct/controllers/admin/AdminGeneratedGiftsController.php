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

class AdminGeneratedGiftsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->display = 'view';
        
        parent::__construct();
    }

    public function renderView()
    {
        $output_filters = '';
        $this->setFiltersData();
        if (Tools::isSubmit('submitFiltermdgift_rule') && (int)Tools::getValue('submitFiltermdgift_rule') > 0) {
            if (!empty($this->_listfilters)) {
                foreach ($this->_listfilters as $_field => $_value) {
                    if ($_value != '') {
                        $field = str_replace('filter_', '', $_field);
                        if ($_field == 'filter_c.id_customer') {
                            $output_filters .= ' AND '.$field.' LIKE "%'.pSQL($_value).'%" OR cs.firstname LIKE "%'.pSQL($_value).'%" OR cs.lastname LIKE "%'.pSQL($_value).'%"';
                        } elseif ($_field != 'filter_o.date_add') {
                            $output_filters .= ' AND '.$field.' LIKE "%'.pSQL($_value).'%"';
                        } else {
                            $output_filters .= (!empty($_value[0]) ? ' AND '.$field.'>= "'.$_value[0].'"' : '') .
                            (!empty($_value[1]) ? ' AND '.$field.'<= "'.$_value[1].'"' : '');
                        }
                    }
                }
            }
        }
        
        $fields_list = array(
            'id_cart_rule' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs', 'callback' => 'findCartRuleLink'),
            'id_cart' => array('title' => $this->l('Cart'), 'align' => 'center', 'class' => 'fixed-width-xs', 'callback' => 'findCartLink'),
            'id_order' => array('title' => $this->l('Order'), 'align' => 'center', 'class' => 'fixed-width-xs', 'callback' => 'findOrderLink'),
            'id_customer' => array('title' => $this->l('Customer'), 'align' => 'center', 'callback' => 'findCustomerLink'),
            'id_mdgift_rule' => array('title' => $this->l('Free gift'), 'callback' => 'findGiftRuleLink'),
            'date_add' => array('title' => $this->l('Order date'),'type'=>'date'),
            'id_order_state' => array('title' => $this->l('Order status'),'callback'=>'findOrderStateLink'),
        );
        
        $sql = 'SELECT distinct(cr.`id_cart_rule`), mdgrc.`id_cart`, mdgr.`id_mdgift_rule`, o.`id_order`, o.`date_add`, c.`id_customer`, cs.`firstname`, cs.`lastname`, os.`id_order_state`
                FROM `'._DB_PREFIX_ .'mdgift_rule_cart` mdgrc
                LEFT JOIN `'._DB_PREFIX_ .'cart_rule` cr ON (mdgrc.`id_cart_rule` = cr.`id_cart_rule`)
                LEFT JOIN `'._DB_PREFIX_ .'mdgift_rule` mdgr ON (mdgrc.`id_mdgift_rule` = mdgr.`id_mdgift_rule`)
                LEFT JOIN `'._DB_PREFIX_ .'mdgift_rule_lang` mdgl ON (mdgr.`id_mdgift_rule` = mdgl.`id_mdgift_rule`)
                LEFT JOIN `'._DB_PREFIX_ .'orders` o ON (mdgrc.id_cart = o.id_cart)
                LEFT JOIN `'._DB_PREFIX_ .'order_state` os ON (os.id_order_state = o.current_state)
				LEFT JOIN `'._DB_PREFIX_ .'cart` c ON (mdgrc.id_cart = c.id_cart)
                LEFT JOIN `'._DB_PREFIX_ .'customer` cs ON (c.id_customer = cs.id_customer)
				WHERE mdgl.`id_lang` = '.(int)$this->context->language->id.
                    $output_filters.
                    (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP) ? '' : ' AND mdgr.`id_shop` = '.(int)Context::getContext()->shop->id)
                .' ORDER BY cr.`id_cart_rule` DESC;';

        $generated_rules =  Db::getInstance()->executeS($sql);
        
        $helper_list = new HelperList();
        $helper_list->module = $this->module;
        $helper_list->title = $this->l('Statistic - Generated free gift rules').' - '.count($generated_rules);
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->show_toolbar = true;
        $helper_list->simple_header = false;
        $helper_list->identifier = 'id_mdgift_rule';
        $helper_list->table = 'mdgift_rule';
        $helper_list->token = Tools::getAdminTokenLite('AdminGeneratedGifts');
        $helper_list->currentIndex = AdminController::$currentIndex;
        $this->_helperlist = $helper_list;
        $helper_list->listTotal = count($generated_rules);
        $helper_list->_default_pagination = $this->_default_pagination;
        $helper_list->_pagination = array(10, 50, 100);
        $page = ($page = Tools::getValue('submitFilter'.$helper_list->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper_list->table.'_pagination')) ? $pagination : $this->_default_pagination;
        $generated_rules = $this->renderPaginate($generated_rules, $page, $pagination);
        return $helper_list->generateList($generated_rules, $fields_list);
    }
    
    public function setFiltersData()
    {
        if (Tools::isSubmit('submitFiltermdgift_rule') && (int)Tools::getValue('submitFiltermdgift_rule') > 0) {
            $this->_listfilters = array(
                'filter_cr.id_cart_rule' => (string)Tools::getValue('mdgift_ruleFilter_id_cart_rule'),
                'filter_mdgrc.id_cart' => (string)Tools::getValue('mdgift_ruleFilter_id_cart'),
                'filter_mdgl.name' => (string)Tools::getValue('mdgift_ruleFilter_id_mdgift_rule'),
                'filter_o.id_order' => (string)Tools::getValue('mdgift_ruleFilter_id_order'),
                'filter_o.date_add' => Tools::getValue('mdgift_ruleFilter_date_add'),
                'filter_c.id_customer' => (string)Tools::getValue('mdgift_ruleFilter_id_customer'),
                'filter_osl.name' => (string)Tools::getValue('mdgift_ruleFilter_order_state'),
            );
        }
    }


    public function renderPaginate($array_items, $page = 1, $pagination = 5)
    {
        if (count($array_items) > $pagination) {
            $array_items = array_slice($array_items, $pagination * ($page - 1), $pagination);
        }

        return $array_items;
    }
    
    public function findCartRuleLink($id)
    {
        if ((int)$id) {
            return '<a href="'.$this->context->link->getAdminLink('AdminCartRules').'&id_cart_rule='.(int)$id.'&updatecart_rule">'.(int)$id.'</a>';
        }
    }

    public function findCartLink($id)
    {
        if ((int)$id) {
            return '<a target="_blank" href="'.$this->context->link->getAdminLink('AdminCarts').'&id_cart='.(int)$id.'&viewcart">'.(int)$id.'</a>';
        }
    }

    public function findOrderLink($id)
    {
        if ((int)$id) {
            return '<a target="_blank" href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.(int)$id.'&vieworder">'.(int)$id.'</a>';
        }
    }

    public function findCustomerLink($id)
    {
        if ((int)$id) {
            $customer = new Customer($id);
            return '<a target="_blank" href="'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id.'&viewcustomer">'.(int)$id.' - '.$customer->firstname.' '.$customer->lastname.'</a>';
        }
    }
 
    public function findGiftRuleLink($id)
    {
        if ($id) {
            $rule = new MdGiftRule($id, (int)$this->context->language->id);
            return '<a target="_blank" href="'.$this->context->link->getAdminLink('AdminGiftProductRules').'&id_mdgift_rule='.(int)$id.'&updatemdgift_rule">'.$rule->name.'</a>';
        }
    }
    
    public function findOrderStateLink($id_order_state)
    {
        if ((int) $id_order_state) {
            $order_state = new OrderState($id_order_state, (int)$this->context->language->id);
            return '<span style="background:'.$order_state->color.';color: #fff; padding: 2px 11px; border-radius: 5px;">'.$order_state->name.'</span>';
        }
    }
    
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_MODULE_DIR_.'mdgiftproduct/views/css/admin.css');
    }
}
