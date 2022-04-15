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

class MdGiftRuleCondition extends ObjectModel
{
    public $id_mdgift_rule_condition;
    public $id_mdgift_rule;
    public $condition_type;
    public $id_customer;
    public $restriction_product;
    public $restriction_attribute;
    public $restriction_feature;
    public $restriction_category;
    public $restriction_supplier;
    public $restriction_manufacturer;
    public $restriction_price;
    
    public $customer_default_group;
    public $customer_birthday;
    public $cart_amount_operator;
    public $cart_amount;
    public $cart_amount_currency;
    public $cart_amount_tax = 1;
    public $cart_amount_shipping;
    public $cart_amount_discount;
    public $cart_weight_operator;
    public $cart_weight;
    public $products_operator;
    public $products_amount;
    public $products_amount_currency;
    public $products_amount_tax = 1;
    public $products_nb_operator;
    public $products_nb;
    public $products_nb_same;
    public $products_nb_same_attributes = true;
    public $product_price_from;
    public $product_price_from_currency;
    public $product_price_from_tax = 1;
    public $product_price_to;
    public $product_price_to_currency;
    public $product_price_to_tax = 1;
    public $apply_discount_to_special = 1;
    public $schedule;
    public $age_from;
    public $age_to;
    
    
    public static $definition = array(
        'table' => 'mdgift_rule_condition',
        'primary' => 'id_mdgift_rule_condition',
        'fields' => array(
            'id_mdgift_rule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'condition_type'  => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'customer_default_group' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'customer_birthday' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_amount_operator' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_amount'=> array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'cart_amount_currency'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_amount_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_amount_shipping' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_amount_discount' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_weight_operator' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cart_weight'  => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'products_operator' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'products_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'products_amount_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'products_amount_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'products_nb_operator' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'products_nb' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'products_nb_same' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'products_nb_same_attributes' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_price_from' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'product_price_from_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price_from_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price_to' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'product_price_to_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price_to_tax'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'apply_discount_to_special' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'restriction_product' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'restriction_attribute' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'restriction_feature' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'restriction_category' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'restriction_supplier' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'restriction_manufacturer' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'restriction_price' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'schedule' => array('type' => self::TYPE_STRING),
            'age_from' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'age_to' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
        $context = Context::getContext();
        if ($context->employee && $context->controller instanceof AdminGiftProductRulesController) {
            $this->attribute = $this->getRelatedRestrictions('attribute', false, true);
            if (Feature::isFeatureActive()) {
                $this->feature = $this->getRelatedRestrictions('feature', false, true);
            }
            $this->category = $this->getRelatedRestrictions('category', false, true);
            if (Group::isFeatureActive()) {
                $this->group = $this->getRelatedRestrictions('group', false, true);
            }
            $this->manufacturer = $this->getRelatedRestrictions('manufacturer', false, false);
            $this->supplier = $this->getRelatedRestrictions('supplier', false, false);
            $this->product = $this->getRelatedRestrictions('product', false, true);
            $this->gender = $this->getRelatedRestrictions('gender', false, true);
            $this->customer_filter = '';
            if (Validate::isUnsignedId($this->id_customer) &&
                ($customer = new Customer($this->id_customer)) &&
                Validate::isLoadedObject($customer)) {
                $this->customer_filter = $customer->firstname.' '.$customer->lastname.' ('.$customer->email.')';
            }
        }
    }
    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, $null_values);
    }
    public function getId()
    {
        return (int)$this->id;
    }

    public function getNewCondition($condition_id = 1)
    {
        $condition = new QuantityDiscountRuleCondition();
        $condition->id_mdgift_rule_condition = $condition_id;
        return $condition;
    }

    public function getRelatedRestrictions($type, $active_only, $multilang)
    {
        $cache_key = 'MdGiftRuleCondition::getRelatedRestrictions_'.(int)$this->id.'_'.$type;
        $context = Context::getContext();
        $current_lang = (int)$context->language->id;
        if (!Cache::isStored($cache_key)) {
            $returned_array = array('selected' => [], 'unselected' => [],'old_selected'=>[]);
            $column1 = $type == 'feature' ? 'fv.`id_feature_value` as id_feature' : 't.`id_'.$type.'`';
            $column2 = $multilang ? ($type == 'attribute' ? ', CONCAT(agl.`name`, " - ", tl.`name`) as name' : ($type == 'feature' ? ', CONCAT(tl.`name`, " - ", fvl.`value`) as name' : ', tl.name')) : ', t.name';
            $column3 = $type == 'product' ? ', reference' : '';
            if (!Validate::isLoadedObject($this)) {
                $sql = 'SELECT '.$column1.$column2.$column3.', 1 as selected
                FROM `'._DB_PREFIX_.$type.'` t
                '.($multilang ? ' LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' 
				AND tl.id_lang = '.$current_lang.(in_array($type, array('product', 'category')) ? '
				AND id_shop = '.(int)$context->shop->id : '').')' : '').
                    ($type == 'attribute' ?
                ' LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl 
					ON (t.id_attribute_group = agl.id_attribute_group 
						AND agl.id_lang = '.$current_lang.')' : '').
                    ($type == 'feature' ?
                    ' LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON (tl.id_feature = fv.id_feature) 
						LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl 
							ON (fv.id_feature_value = fvl.id_feature_value 
								AND fvl.id_lang = '.$current_lang.')' : '').'
						WHERE 1
                    '.($active_only ? 'AND t.active = 1' : '').
                    ($type == 'carrier' ? ' AND t.deleted = 0' : '').
                    ' ORDER BY name ASC';
                $returned_array['unselected'] = Db::getInstance()->executeS($sql);

            /*if (in_array($type, array('attribute', 'feature', 'manufacturer', 'supplier'))) {
                array_unshift($returned_array['unselected'], array('id_'.$type => '999999', 'name' => '- None '.$type.' -', 'selected' => '1'));
            }*/
            } else {
                $sql = 'SELECT '.$column1.$column2.$column3.', IF(mdgrt.id_'.$type.' IS NULL, 0, 1) as selected
                    FROM `'._DB_PREFIX_.$type.'` t
                    '.($multilang ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl 
						ON (t.id_'.$type.' = tl.id_'.$type.' 
						AND tl.id_lang = '.$current_lang.
                            (in_array($type, array('product', 'category')) ?
                                ' AND id_shop = '.(int)$context->shop->id : '').')' : '').
                    ($type == 'feature' ? ' LEFT JOIN `'._DB_PREFIX_.'feature_value` fv 
						ON (tl.id_feature = fv.id_feature)
						LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'` 
							WHERE id_mdgift_rule_condition = '.(int)$this->id.') mdgrt 
							ON fv.id_'.$type.'_value = mdgrt.id_'.$type.' 
					LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl 
					ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = '.$current_lang.')' :
                    'LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'` 
						WHERE id_mdgift_rule_condition = '.(int)$this->id.') mdgrt 
							ON t.id_'.$type.' = mdgrt.id_'.$type).
                    ($type == 'attribute' ? ' LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl 
						ON (t.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '.(int)$current_lang.')'
                        : '').'
                    WHERE 1 '.($active_only ? ' AND t.active = 1' : '').
                    ' ORDER BY name ASC LIMIT 50';

                $resource = Db::getInstance()->query($sql, false);
                while ($row = Db::getInstance()->nextRow($resource)) {
                    $returned_array[($row['selected']) ? 'selected' : 'unselected'][] = $row;
                    if ($row['selected']) {
                        $returned_array['old_selected'][] = $row['id_'.$type];
                    }
                }
            }
            $result = $returned_array;
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    public function getSelectedRelatedRestrictions($type)
    {
        $cache_key = 'MdGiftRuleCondition::getSelectedRelatedRestrictions_'.(int)$this->id.'_'.$type;

        if (!Cache::isStored($cache_key)) {
            $sql = 'SELECT id_'.$type.'
                FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'`
                WHERE `id_mdgift_rule_condition` = '.(int)$this->id;

            $return = array();
            $return['selected'] = Db::getInstance()->executeS($sql);

            Cache::store($cache_key, $return);
        } else {
            $return = Cache::retrieve($cache_key);
        }

        return $return;
    }

    protected function getNbProducts()
    {
        $sql = 'SELECT count(*)
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p');

        return (int)Db::getInstance()->getValue($sql);
    }
    
    public static function getConditions($id_mdgift_rule)
    {
        $sql = 'SELECT id_mdgift_rule_condition
                FROM `'._DB_PREFIX_.'mdgift_rule_condition`
                WHERE `id_mdgift_rule` = '.(int)$id_mdgift_rule;
        $results = Db::getInstance()->executeS($sql);
        $return = array_map(function ($item) {
            return $item['id_mdgift_rule_condition'];
        }, $results);
        return $return;
    }
     
    public function getAssociatedSelection($type)
    {
        $cache_key = 'MdGiftRuleCondition::getAssociatedSelection_'.(int)$this->id.'_'.$type;

        if (!Cache::isStored($cache_key)) {
            $sql = 'SELECT id_'.$type.'
                FROM `'._DB_PREFIX_.'mdgift_rule_condition_'.$type.'`
                WHERE `id_mdgift_rule_condition` = '.(int)$this->id;
            $result = Db::getInstance()->executeS($sql);

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }
}
