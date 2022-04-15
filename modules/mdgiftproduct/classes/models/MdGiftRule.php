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
 
require_once(_PS_MODULE_DIR_.'mdgiftproduct/classes/models/MdGiftRuleProduct.php');

class MdGiftRule extends ObjectModel
{
    public $id_mdgift_rule;
    public $name;
    public $active;
    public $title;
    public $description;
    public $date_from;
    public $date_to;
    public $compatible_cart_rules;
    public $apply_products_already_discounted;
    public $quantity_per_user;
    public $quantity;
    public $date_add;
    public $date_upd;
    public $id_shop;
    public $nb_product_gift;
    public static $definition = array(
        'table' => 'mdgift_rule',
        'primary' => 'id_mdgift_rule',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'compatible_cart_rules' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'apply_products_already_discounted' => array('type' => self::TYPE_BOOL,
                'validate' => 'isBool', 'required' => false),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'quantity_per_user' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'nb_product_gift' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            // Lang fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true,
                    'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
            'title' => array('type' => self::TYPE_STRING, 'lang' => true,
                    'validate' => 'isCleanHtml', 'size' => 254),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true,
                    'validate' => 'isCleanHtml', 'size' => 254),
        )
    );

    public function __construct($id = null, $lang = null)
    {
        $this->context = Context::getContext();
        $this->module = Module::getInstanceByName('mdgiftproduct');
        parent::__construct($id, $lang);
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;

        return parent::add($autodate, $null_values);
    }

    public function getConditions($hydrate = false)
    {
        $result = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'mdgift_rule_condition`
            WHERE `id_mdgift_rule` = '.(int)$this->id_mdgift_rule.'
            ORDER BY `id_mdgift_rule_condition` ASC'
        );
        if ($hydrate) {
            return ObjectModel::hydrateCollection('MdGiftRuleCondition', $result);
        }

        return $result;
    }

    public function getProducts($hydrate = false)
    {
        $result = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'mdgift_rule_product`
            WHERE `id_mdgift_rule` = '.(int)$this->id_mdgift_rule.'
            ORDER BY `id_mdgift_rule_product` ASC'
        );

        if ($hydrate) {
            return ObjectModel::hydrateCollection('MdGiftRuleProduct', $result);
        }

        return $result;
    }

    public function findValidGiftRules()
    {
        $context = Context::getContext();
        if (!Validate::isLoadedObject($context->cart)) {
            return;
        }
        
        $id_lang = (int)$context->cart->id_lang;

        $resultGifts = [];
        $giftRules = $this->getGifRules();
        if (is_array($giftRules) && !empty($giftRules)) {
            foreach ($giftRules as $giftRuleItem) {
                $giftRule = new MdGiftRule((int)$giftRuleItem['id_mdgift_rule']);
                if (!$giftRule->compatibleCartRules()) {
                    continue;
                }
                
                
                if (!$giftRule->isGiftRuleValid()) {
                    continue;
                }

                if (!$giftRule->validateGiftRuleConditions()) {
                    continue;
                }
                
                if (!$giftRule->apply_products_already_discounted) {
                    $cartProducts = $this->context->cart->getProducts();
                    $reduction_applies = array_column($cartProducts, 'reduction_applies');
                    if (in_array(true, $reduction_applies)) {
                        continue;
                    }
                }
                $giftProducts = $giftRule->getProducts();
                $resultGifts[$giftRule->id]['id'] = (int) $giftRule->id;
                $resultGifts[$giftRule->id]['nb_product_gift'] = (int) $giftRule->nb_product_gift;
                $resultGifts[$giftRule->id]['description'] = $giftRule->description[$id_lang];
                $resultGifts[$giftRule->id]['title'] = $giftRule->title[$id_lang];
                if (!empty($giftProducts)) {
                    foreach ($giftProducts as $giftProduct) {
                        $productObj = new Product((int)$giftProduct['id_product'], false, $id_lang);
                        $selectedAttributs = MdGiftRuleProduct::getProductsAttribut(
                            $giftProduct['id_mdgift_rule'],
                            $giftProduct['id_product']
                        );
                        $attributes = $this->getAttributesGroups(
                            (int)$giftProduct['id_product'],
                            $id_lang,
                            $selectedAttributs
                        );
                        $combinations = array();
                        foreach ($attributes as $attr) {
                            if (!isset($combinations[$attr['id_product_attribute']]['attributes'])) {
                                $combinations[$attr['id_product_attribute']]['attributes'] = '';
                            }
                            $id_product_attribute = $attr['id_product_attribute'];
                            $combinations[$id_product_attribute]['attributes'] .= $attr['attribute_name'].' - ';
                            $combinations[$id_product_attribute]['id_product_attribute'] = $id_product_attribute;
                            $combinations[$id_product_attribute]['default_on'] = $attr['default_on'];
                        }
                        foreach ($combinations as &$combination) {
                            $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                        }

                        $productObj->selectedCombinations = $combinations;
                        $productObj->combinationImages = $productObj->getCombinationImages($id_lang);
                        $cover_image = Product::getCover((int)$giftProduct['id_product']);
                        $productObj->id_image = $cover_image['id_image'];

                        $productObj->image = $context->link->getImageLink(
                            $productObj->link_rewrite,
                            $productObj->id_image,
                            ImageType::getFormatedName('home')
                        );
                        $resultGifts[$giftRule->id]['products'][] = $productObj;
                    }
                }
            }
        }
        return $resultGifts;
    }

    public static function getGifRules()
    {
        $sql = "SELECT *
            FROM `"._DB_PREFIX_ .MdGiftRule::$definition['table']."` mgr
            LEFT JOIN `"._DB_PREFIX_."mdgift_rule_lang` mgrl
                ON (mgr.`id_mdgift_rule` = mgrl.`id_mdgift_rule` 
			    AND mgrl.`id_lang` = ".(int)Context::getContext()->cart->id_lang.")
            WHERE mgr.`active` = 1
            AND mgr.`id_shop` = ".(int)Context::getContext()->shop->id.
            " ORDER BY mgr.`id_mdgift_rule` ASC";

        $result = Db::getInstance()->ExecuteS($sql);
        return $result;
    }

    public function compatibleCartRules()
    {
        if (!$this->compatible_cart_rules) {
            $cartRules = $this->context->cart->getCartRules();
            $giftRulesInCart = self::getGiftRulesAtCart((int)$this->context->cart->id);
            if (count($cartRules) > count($giftRulesInCart)) {
                return false;
            }
        }

        return true;
    }
    
    public function checkCompatibleWhenAddCart()
    {
        $giftRulesInCart = self::getGiftRulesAtCart((int)$this->context->cart->id);
        foreach ($giftRulesInCart as $giftRuleInCart) {
            if (!$giftRuleInCart['compatible_cart_rules']) {
                return false;
            }
        }
        if (!$this->compatible_cart_rules) {
            $giftRulesInCart = self::getGiftRulesAtCart((int)$this->context->cart->id);
            if (count($giftRulesInCart) > 0) {
                return false;
            }
        } else {
        }

        return true;
    }

    public static function getGiftRulesAtCart($id_cart, $id_mdgift_rule = null, $id_cart_rule = null, $single = false)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT mgr.`id_mdgift_rule`, mgrc.`id_cart_rule`, mgr.`compatible_cart_rules`
            FROM `'._DB_PREFIX_.'mdgift_rule_cart` mgrc
            LEFT JOIN `'._DB_PREFIX_.'mdgift_rule` mgr ON (mgr.`id_mdgift_rule` = mgrc.`id_mdgift_rule`)
            WHERE `id_cart` = '.(int)$id_cart.
                ($id_mdgift_rule ? ' AND mgr.`id_mdgift_rule` = '.(int)$id_mdgift_rule : '').
                ($id_cart_rule ? ' AND mgrc.`id_cart_rule` = '.(int)$id_cart_rule : '');
        if (!$single) {
            return Db::getInstance()->executeS($sql);
        } else {
            return Db::getInstance()->getRow($sql);
        }
    }
    public static function getCartCartRule($id_cart, $id_cart_rule)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT `id_cart_rule`
            FROM `'._DB_PREFIX_.'cart_cart_rule`
            WHERE `id_cart` = '.(int)$id_cart.' AND id_cart_rule = '.(int)$id_cart_rule;
            
        return Db::getInstance()->getValue($sql);
    }
    public static function getGiftProductAtCart($id_cart, $id_mdgift_rule = null)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }
        $sql = 'SELECT *
            FROM `'._DB_PREFIX_.'mdgift_rule_cart_product`
            WHERE `id_cart` = '.(int)$id_cart.
            ($id_mdgift_rule ? ' AND id_mdgift_rule = '.(int)$id_mdgift_rule : '');
            
        return Db::getInstance()->executeS($sql);
    }
    
    public static function getGiftProductAddedToCart($id_cart)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }
        $sql = 'SELECT *
            FROM `'._DB_PREFIX_.'mdgift_rule_cart_product`
            WHERE `id_cart` = '.(int)$id_cart;
        $rows = Db::getInstance()->executeS($sql);
        $result = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['id_product'])) {
                    $result[$row['id_mdgift_rule']][$row['id_product']]['id_product'] = $row['id_product'];
                }
                if (!empty($id_product_attribute = $row['id_product_attribute'])) {
                    $result[$id_product_attribute][$row['id_product']]['id_product_attribute'] = $id_product_attribute;
                }
            }
        }
        return $result;
    }

    public static function getExistingGiftRulesInCart($id_cart, $id_mdgift_rule = null, $id_cart_rule = null, $single = false)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT mgr.`id_mdgift_rule`, mgrc.`id_cart_rule`
            FROM `'._DB_PREFIX_.'mdgift_rule_cart` mgrc
            LEFT JOIN `'._DB_PREFIX_.'mdgift_rule` mgr ON (mgr.`id_mdgift_rule` = mgrc.`id_mdgift_rule`)
            LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr on (mgrc.`id_cart_rule` = cr.`id_cart_rule`)
			WHERE `id_cart` = '.(int)$id_cart.
                ($id_mdgift_rule ? ' AND mgr.`id_mdgift_rule` = '.(int)$id_mdgift_rule : '').
                ($id_cart_rule ? ' AND mgrc.`id_cart_rule` = '.(int)$id_cart_rule : '');
        if (!$single) {
            return Db::getInstance()->executeS($sql);
        } else {
            return Db::getInstance()->getRow($sql);
        }
    }
    
    public static function getGiftRulesByProductAtCart($id_cart, $id_product, $id_product_attribute)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'mdgift_rule_cart` mgrc 
			WHERE id_mdgift_rule in ( SELECT id_mdgift_rule from `'._DB_PREFIX_.'mdgift_rule_cart_product` mgrcp
				WHERE mgrcp.`id_product` = '.(int) $id_product.'
				AND mgrcp.`id_product_attribute` = ' .(int) $id_product_attribute.'
				AND mgrc.`id_cart` = '.(int)$id_cart.' )';

        return Db::getInstance()->getRow($sql);
    }
    
    public function isGiftRuleValid()
    {
        $now = date('Y-m-d H:i:s');
        if (strtotime($now) <= strtotime($this->date_from)
            || strtotime($now) >= strtotime($this->date_to)) {
            return false;
        }

        $times_used = Db::getInstance()->getValue(
            "SELECT count(distinct(o.`id_order`))
            FROM `"._DB_PREFIX_."orders` o
            LEFT JOIN "._DB_PREFIX_."order_cart_rule od ON o.id_order = od.id_order
            LEFT JOIN "._DB_PREFIX_."mdgift_rule_cart mdgrc ON od.id_cart_rule = mdgrc.id_cart_rule
            WHERE mdgrc.id_mdgift_rule = ".(int)$this->id."
            AND ".(int)Configuration::get('PS_OS_ERROR')." != o.current_state"
        );

        if ($this->quantity != 0 && ($times_used >= $this->quantity)) {
            return false;
        }

        if ((int)$this->id_shop != (int)$this->context->shop->id) {
            return false;
        }

        return true;
    }
    
    public function validateGiftRuleConditions()
    {
        if (!isset($this->context->cart)) {
            return false;
        }
        
        $checkValidaty = false;
        $conditions = $this->getConditions();
        foreach ($conditions as $condition) {
            $checkValidaty = false;
            $condition = new MdGiftRuleCondition($condition['id_mdgift_rule_condition']);
            switch ($condition->condition_type) {
                case "total_cart_amount":
                    if ($condition->cart_amount > 0) {
                        $cartAmount = $condition->cart_amount;
                        if ((int)$condition->cart_amount_currency != $this->context->currency->id) {
                            $cartAmount = self::switchPrice(
                                $cartAmount,
                                new Currency((int)$condition->cart_amount_currency),
                                $this->context->currency,
                                false
                            );
                        }

                        if (!(int)$condition->apply_discount_to_special) {
                            $cartProducts = $this->context->cart->getProducts();
                            $cartAmountTotal = 0;
                            
                            foreach ($cartProducts as $cartProduct) {
                                $product_price = Product::getPriceStatic(
                                    $cartProduct['id_product'],
                                    (int)$condition->cart_amount_tax,
                                    (isset($cartProduct['id_product_attribute']) ?
                                        (int)$cartProduct['id_product_attribute'] : null),
                                    6,
                                    null,
                                    true,
                                    true,
                                    $cartProduct['cart_quantity']
                                );
                                if (!($cartProduct['on_sale'] || $product_price > 0)) {
                                    $cartAmountTotal += $product_price * $cartProduct['cart_quantity'];
                                }
                            }
                        } else {
                            $cartAmountTotal = $this->context->cart->getOrderTotal((int)$condition->cart_amount_tax, Cart::ONLY_PRODUCTS);
                        }

                        if ((int)$condition->cart_amount_shipping) {
                            $cartAmountTotal += $this->context->cart->getOrderTotal($condition->cart_amount_tax, Cart::ONLY_SHIPPING);
                        }

                        if (!(int)$condition->cart_amount_discount) {
                            $cartAmountTotal -= $this->context->cart->getOrderTotal($condition->cart_amount_tax, Cart::ONLY_DISCOUNTS);
                        }

                        $cartAmountTotal -= $this->getGiftProductsValue($condition->cart_amount_tax);

                        $checkValidaty = $this->compare((int)$condition->cart_amount_operator, $cartAmountTotal, $cartAmount);
                    }

                    break;
                case "cart_weight":
                    if ($condition->cart_weight > 0) {
                        $cart_weight = $this->context->cart->getTotalWeight();
                        $checkValidaty = $this->compare((int)$condition->cart_weight_operator, $cart_weight, $condition->cart_weight);
                    }

                    break;
                case "products_cart":
                    $cartProducts = $this->context->cart->getProducts();
                    $cartProductsFiltered = $this->filterProducts($cartProducts, $condition);

                    if (!$cartProductsFiltered) {
                        break;
                    } else {
                        $checkValidaty = true;
                    }

                    if ((int)$condition->products_amount) {
                        $cartAmount = 0;

                        foreach ($cartProductsFiltered as $cartProductFiltered) {
                            $cartAmount += Product::getPriceStatic($cartProductFiltered['id_product'], (int)$condition->products_amount_tax, (isset($cartProductFiltered['id_product_attribute']) ? (int)$cartProductFiltered['id_product_attribute'] : null), 6, null, false, true, $cartProductFiltered['cart_quantity'])*$cartProductFiltered['cart_quantity'];
                        }

                        if ((int)$condition->products_amount_currency != $this->context->currency->id) {
                            $conditionProductsAmount = self::switchPrice($condition->products_amount, new Currency((int)$condition->products_amount_currency), $this->context->currency, false);
                        } else {
                            $conditionProductsAmount = $condition->products_amount;
                        }

                        $checkValidaty &= $this->compare((int)$condition->products_operator, $cartAmount, $conditionProductsAmount);
                    }
                    break;
                case "customer_single":
                    if ((int)$this->context->cart->id_customer == (int)$condition->id_customer) {
                        $checkValidaty = true;
                    }
                    break;
                case "customer_group":
                    $condition_groups = $condition->getSelectedRelatedRestrictions('group');
                    if (count($condition_groups['selected'])) {
                        $condition_id_groups = array_column($condition_groups['selected'], 'id_group');
                        if ((int)$this->context->cart->id_customer && $condition->customer_default_group) {
                            $customer = new Customer((int)$this->context->cart->id_customer);
                            if (in_array((int)$customer->id_default_group, $condition_id_groups)) {
                                $checkValidaty = true;
                                break;
                            }
                        } else {
                            $customer_groups = Customer::getGroupsStatic((int)$this->context->cart->id_customer);
                            foreach ($customer_groups as $customer_group) {
                                if (in_array($customer_group, $condition_id_groups)) {
                                    $checkValidaty = true;
                                    break;
                                }
                            }
                        }
                    }


                    break;
                case "customer_birthday":
                    if ((int)$this->context->cart->id_customer) {
                        $current_date = date('m-d');
                        $customer = new Customer((int)$this->context->cart->id_customer);

                        if ($condition->customer_birthday && $current_date == date('m-d', strtotime($customer->birthday))) {
                            $checkValidaty = true;
                        } elseif (!$condition->customer_birthday && $current_date != date('m-d', strtotime($customer->birthday))) {
                            $checkValidaty = true;
                        }
                    }

                    break;
                case "customer_gender":
                    if ((int)$this->context->cart->id_customer) {
                        $customer = new Customer((int)$this->context->cart->id_customer);
                        $condition_genders = $condition->getSelectedRelatedRestrictions('gender');

                        if ($customer->id_gender) {
                            if (is_array($condition_genders['selected']) && !empty($condition_genders['selected'])) {
                                if (in_array($customer->id_gender, array_column($condition_genders['selected'], 'id_gender'))) {
                                    $checkValidaty = true;
                                }
                            }
                        }
                    }

                    break;
                case "customer_age":
                    if ((int)$this->context->cart->id_customer) {
                        $customer = new Customer((int)$this->context->cart->id_customer);
                        if ($customer->birthday && $customer->birthday != '0000-00-00') {
                            $cusomer_age = date_diff(date_create($customer->birthday), date_create('now'))->y;
                            if ($cusomer_age <= $condition->age_to && $cusomer_age >= $condition->age_from) {
                                $checkValidaty = true;
                            }
                        }
                    }

                    break;
                case "day_week":
                    $schedule_params = Tools::jsonDecode($condition->schedule);
                    $day_week = date('w') - 1;
                    if ($day_week < 0) {
                        $day_week = 6;
                    }
                    if (is_array($schedule_params)) {
                        if (is_object($schedule_params[$day_week]) && $schedule_params[$day_week]->isActive === true) {
                            if ($schedule_params[$day_week]->timeFrom <= date('H:i') && $schedule_params[$day_week]->timeTill > date('H:i')) {
                                $checkValidaty = true;
                            }
                        }
                    }

                    break;
            }
            if (!$checkValidaty) {
                break;
            }
        }
        if ($checkValidaty) {
            return true;
        }
        return false;
    }
    
    public function getGiftProductsValue($with_taxes)
    {
        $products = $this->context->cart->getProducts();
        $cartRules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT);
        $amount = 0;
        foreach ($cartRules as $cartRule) {
            if ($cartRule['gift_product']) {
                foreach ($products as $product) {
                    if (empty($product['gift']) && $product['id_product'] == $cartRule['gift_product'] && $product['id_product_attribute'] == $cartRule['gift_product_attribute']) {
                        $amount += Tools::ps_round($product[$with_taxes ? 'price_wt' : 'price'], (int)$this->context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                    }
                }
            }
        }

        return $amount;
    }

    public static function switchPrice($amount, Currency $currency_from = null, Currency $currency_to = null, $round = true)
    {
        if ($currency_from === null) {
            $currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_to === null) {
            $currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_from === $currency_to) {
            return $amount;
        }

        if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
            $amount *= $currency_to->conversion_rate;
        } else {
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
            $amount = $amount / $conversion_rate;
            $amount *= $currency_to->conversion_rate;
        }
        if ($round) {
            $amount = Tools::ps_round($amount, _PS_PRICE_DISPLAY_PRECISION_);
        }

        return $amount;
    }

    protected function filterProducts($cartProducts, $object)
    {
        if (!is_array($cartProducts) || !is_object($object)) {
            return;
        }
        if ($object->restriction_product) {
            $associatedProducts = $object->getAssociatedSelection('product');
        }

        if ($object->restriction_category) {
            $restrictionCategories = $object->getAssociatedSelection('category');
        }
        
        if ($object->restriction_feature) {
            $restrictionFeatures = $object->getAssociatedSelection('feature');
        }
        
        if ($object->restriction_attribute) {
            $restrictionAttributes = $object->getAssociatedSelection('attribute');
        }
        
        if ($object->restriction_manufacturer) {
            $restrictionManufacturers = $object->getAssociatedSelection('manufacturer');
        }
        
        if ($object->restriction_supplier) {
            $restrictionSuppliers = $object->getAssociatedSelection('supplier');
        }

        foreach ($cartProducts as $key => $cartProduct) {
            //check if has no promotion
            
            if (!$this->apply_products_already_discounted) {
            }
            
            
            // Check product
            if ($object->restriction_product && (!isset($associatedProducts) || !in_array((int)$cartProduct['id_product'], array_column($associatedProducts, 'id_product')))) {
                unset($cartProducts[$key]);
                continue;
            }

            // Check categories
            if ($object->restriction_category) {
                $productIsInCategory = false;
                $productCategories = Product::getProductCategories($cartProduct['id_product']);
                foreach ($productCategories as $productCategory) {
                    if (isset($restrictionCategories) && in_array((int)$productCategory, array_column($restrictionCategories, 'id_category'))) {
                        $productIsInCategory = true;
                        break;
                    }
                }

                if (!$productIsInCategory) {
                    unset($cartProducts[$key]);
                    continue;
                }
            }

            // Check attributes

            if ($object->restriction_attribute) {
                $product = new Product((int)$cartProduct['id_product']);

                $productHasCombination = false;
                if (isset($cartProduct['id_product_attribute'])) {
                    if ($combinations = $product->getAttributeCombinationsById((int)$cartProduct['id_product_attribute'], (int)$this->context->cart->id_lang)) {
                        foreach ($combinations as $combination) {
                            if ((int)$combination['id_attribute'] && in_array((int)$combination['id_attribute'], array_column($restrictionAttributes, 'id_attribute'))) {
                                $productHasCombination = true;
                                break;
                            }
                        }
                    } elseif (in_array(999999, array_column($restrictionAttributes, 'id_attribute'))) {
                        $productHasCombination = true;
                    }
                }

                if (!$productHasCombination) {
                    unset($cartProducts[$key]);
                    continue;
                }
            }


            // Check features
            if ($object->restriction_feature) {
                $productFeatures = Product::getFeaturesStatic((int)$cartProduct['id_product']);

                $productHasFeature = false;
                if (isset($productFeatures)) {
                    foreach ($productFeatures as $productFeature) {
                        if ((int)$productFeature['id_feature_value'] && in_array((int)$productFeature['id_feature_value'], array_column($restrictionFeatures, 'id_feature'))) {
                            $productHasFeature = true;
                            break;
                        }
                    }
                } elseif (in_array(999999, array_column($restrictionFeatures, 'id_feature'))) {
                    $productHasFeature = true;
                }

                if (!$productHasFeature) {
                    unset($cartProducts[$key]);
                    continue;
                }
            }



            // Check supplier

            if ($object->restriction_supplier) {
                if ((!(int)$cartProduct['id_supplier']  && !in_array(999999, array_column($restrictionSuppliers, 'id_supplier')))
                    || ((int)$cartProduct['id_supplier'] && !in_array((int)$cartProduct['id_supplier'], array_column($restrictionSuppliers, 'id_supplier')))) {
                    unset($cartProducts[$key]);
                    continue;
                }
            }


            // Check manufacturer
            if ($object->restriction_manufacturer) {
                if ((!(int)$cartProduct['id_manufacturer'] && !in_array(999999, array_column($restrictionManufacturers, 'id_manufacturer')))
                    || ((int)$cartProduct['id_manufacturer'] && !in_array((int)$cartProduct['id_manufacturer'], array_column($restrictionManufacturers, 'id_manufacturer')))) {
                    unset($cartProducts[$key]);
                    continue;
                }
            }

            // Discard products with special price if configured
            if (!(int)$object->apply_discount_to_special && Product::getPriceStatic($cartProduct['id_product'], false, (isset($cartProduct['id_product_attribute']) ? (int)$cartProduct['id_product_attribute'] : null), 6, null, true, true, $cartProduct['cart_quantity']) > 0) {
                unset($cartProducts[$key]);
                continue;
            }

            // Filter by price
            if ($object->restriction_price) {
                if ((int)$object->product_price_from_tax) {
                    $price = $cartProduct['price_with_reduction'];
                } else {
                    $price = $cartProduct['price_with_reduction_without_tax'];
                }
                $price = self::switchPrice($price, $this->context->currency, new Currency($object->product_price_from_currency), true);
                if (!$this->compare(0, $price, $object->product_price_from)
                        || !$this->compare(2, $price, $object->product_price_to)) {
                    unset($cartProducts[$key]);
                    continue;
                }
            }
        }

        $result = $cartProducts;
        return $result;
    }
    
    protected function compare($operator, $a, $b)
    {
        switch ((int)$operator) {
            case 0:
                if ($a < $b) {
                    return false;
                }
                break;
            case 1:
                if ($a != $b) {
                    return false;
                }
                break;
            case 2:
                if ($a > $b) {
                    return false;
                }
                break;
        }

        return true;
    }

    public function getAttributesGroups($id_product, $id_lang, $attributeIds)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }
        
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
                    a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
                    IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
                    product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
                    product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                ' . Product::sqlStock('pa', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                ' . Shop::addSqlAssociation('attribute', 'a') . '
                WHERE pa.`id_product` = ' . (int) $id_product .
                (!empty($attributeIds) ? ' AND pac.`id_product_attribute` IN ('.implode(',', $attributeIds).') ' : '').
                    ' AND al.`id_lang` = ' . (int) $id_lang . '
                    AND agl.`id_lang` = ' . (int) $id_lang . '
                GROUP BY id_attribute_group, id_product_attribute
                ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';

        return Db::getInstance()->executeS($sql);
    }

    
    public function addGiftToCart($products = null, $auto = false)
    {
        $cart = $this->context->cart;
        $cart = new Cart($cart->id);
        $voucher_product_quantity = 1;
        $nb_product = $this->getTotalProduct();
        if (!$products) {
            $products = Tools::getValue('products');
        }
        if (!$this->checkCompatibleWhenAddCart()) {
            die(Tools::jsonEncode(
                array(
                    'status' => false,
                    'result' => $this->module->l('You cannot add this rule to the cart, because it is not cumulative with other rules in the cart', 'mdgiftrule')
                )
            ));
            return false;
        }
        $old_voucher = $this->getGiftRulesAtCart($cart->id, $this->id, null, true);
        $old_gift_products = $this->getGiftProductAtCart($cart->id, $this->id, null, true);
        
        if (!empty($old_gift_products)) {
            foreach ($old_gift_products as $old_product) {
                $cart->updateQty((int) $old_product['quantity'], $old_product['id_product'], $old_product['id_product_attribute'], false, 'down', 0, null, false, true);
            }
        }
        $cart_cart_rule = null;
        
        if (!empty($old_voucher) && isset($old_voucher['id_cart_rule'])) {
            $cart_cart_rule = $this->getCartCartRule($cart->id, $old_voucher['id_cart_rule']);
        }
        
        $voucher_amount = 0;
        $languages = Language::getLanguages();
        $insert_product_discount = [];
        if (!empty($products)) {
            foreach ($products as $productItem) {
                $productItemAttribut = isset($productItem['id_product_attribute']) ? $productItem['id_product_attribute'] : 0;
                if ($auto) {
                    $cart->updateQty((int)$this->nb_product_gift, $productItem['id_product'], $productItemAttribut, false, 'up', 0, null, true, true);
                } else {
                    $cart->updateQty($nb_product == 1 ? (int)$this->nb_product_gift : 1, $productItem['id_product'], $productItemAttribut, false, 'up', 0, null, true, true);
                }
                $insert_product_discount[] = '('.(int)$cart->id.','.(int)$this->id.','.(int)$productItem['id_product'].','.$productItemAttribut.','.($nb_product == 1 ? (int)$this->nb_product_gift : 1).')';
                $cart_products = $cart->getProducts($productItem['id_product']);
                foreach ($cart_products as $cart_product) {
                    if ($cart_product['id_product_attribute'] == $productItemAttribut
                        && $cart_product['id_product'] == $productItem['id_product']) {
                        $voucher_amount += self::switchPrice($cart_product['price_with_reduction'] * ($nb_product == 1 ? $this->nb_product_gift : 1), Context::getContext()->currency, Currency::getDefaultCurrency(), false);
                        break;
                    }
                }
            }
        }
        
        $cart_rule = !isset($old_voucher['id_cart_rule']) ? new CartRule() : new CartRule($old_voucher['id_cart_rule']);
        $cart_rule->id_customer = (int)$cart->id_customer;
        $cart_rule->date_from = $this->date_from;
        $cart_rule->date_to = $this->date_to;
        $cart_rule->description = '';
        $cart_rule->quantity = $voucher_product_quantity;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->priority = 1;
        $cart_rule->partial_use = 0;
        $cart_rule->code = $this->createCode('MDG-', 4);
        $cart_rule->reduction_amount = (float)$voucher_amount;
        $cart_rule->reduction_currency = (int)$cart->id_customer;
        $cart_rule->reduction_tax = true;
        $cart_rule->date_add = date('Y-m-d H:i:s');
        $cart_rule->date_upd = date('Y-m-d H:i:s');

        $name_suffix = '';
        if ($voucher_product_quantity > 1) {
            $name_suffix = ' x '.$voucher_product_quantity;
        }
    
        foreach ($languages as $language) {
            $name = $this->name[$language['id_lang']];
            if ($name == '') {
                $cart_rule->name[$language['id_lang']] = $this->module->l('Free Gift', 'MdGiftRule', $language['locale']);
            } else {
                $cart_rule->name[$language['id_lang']] = $name . $name_suffix;
            }
        }
        
        if (!isset($old_voucher['id_cart_rule'])) {
            $cart_rule->add();
        } else {
            $cart_rule->save();
        }



        
        if ($cart_rule->id > 0) {
            Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."mdgift_rule_cart_product`
						  WHERE `id_cart` = ".(int)$cart->id." AND `id_mdgift_rule` =".(int)$this->id);
            
            if (!empty($insert_product_discount)) {
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'mdgift_rule_cart_product` (`id_cart`, `id_mdgift_rule`, `id_product`, `id_product_attribute`, `quantity`)
                   VALUES '.implode(",", $insert_product_discount)
                );
            }
            // Add the cart rule to the cart
            if ($cart_cart_rule == null) {
                Db::getInstance()->insert('cart_cart_rule', array(
                  'id_cart_rule' => (int)$cart_rule->id,
                  'id_cart' => (int)$cart->id
                ));
            }
              
            if (!isset($old_voucher['id_cart_rule'])) {
                Db::getInstance()->insert('mdgift_rule_cart', array(
                    'id_cart' => (int)$cart->id,
                    'id_mdgift_rule' => (int)$this->id,
                    'id_cart_rule' => (int)$cart_rule->id
                ));
            } else {
                Db::getInstance()->update('mdgift_rule_cart', array(
                        'id_cart_rule' => (int)$cart_rule->id,
                    ), 'id_cart = '.(int)$cart->id.' AND id_mdgift_rule = '.(int)$this->id);
            }
        }
        return true;
    }
    
    public function deleteFromCart()
    {
        $id_product = Tools::getValue('id_product');
        $nb_product = $this->getTotalProduct();
        
        $id_product_attribute = version_compare(_PS_VERSION_, '1.7.0', '<') ? Tools::getValue('ipa') : Tools::getValue('id_product_attribute');
        $cart = Context::getContext()->cart;
        $id_cart = $cart->id;
        $giftRules = $this->getGiftRulesByProductAtCart($id_cart, $id_product, $id_product_attribute);
        if (!empty($giftRules)) {
            $id_mdgift_rule = (int) $giftRules['id_mdgift_rule'];
            $id_cart_rule = (int) $giftRules['id_cart_rule'];
            $this->deleteFromCartProduct($id_cart, $id_mdgift_rule, $id_product, $id_product_attribute);
            $this->calculateCartDiscount($id_cart, $id_mdgift_rule, $id_cart_rule);
            return true;
        } else {
            $validGifts = $this->findValidGiftRules();
            $giftIncart = $this->getExistingGiftRulesInCart($id_cart);
            $giftIdsIncart = array_column($giftIncart, 'id_mdgift_rule');
            $gift_to_delete = array_diff($giftIdsIncart, array_keys($validGifts));
            if (!empty($gift_to_delete)) {
                foreach ($gift_to_delete as $key => $id_mdgift_rule) {
                    $id_cart_rule = $giftIncart[$key]['id_cart_rule'];
                    $old_gift_products = $this->getGiftProductAtCart($id_cart, $id_mdgift_rule, null, true);
                    $this->deleteFromCartProduct($id_cart, $id_mdgift_rule);
                    $this->calculateCartDiscount($id_cart, $id_mdgift_rule, $id_cart_rule);
                    if (!empty($old_gift_products)) {
                        foreach ($old_gift_products as $old_product) {
                            $cart->updateQty((int) $old_product['quantity'], $old_product['id_product'], $old_product['id_product_attribute'], false, 'down', 0, null, false, true);
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }
    
    public function voucherRemoved()
    {
        $cart = $this->context->cart;
        $sql = 'select ccr.`id_cart_rule`, mrc.`id_cart_rule` as id_cart_rule_gift, mrc.`id_mdgift_rule`, mrc.`id_cart` 
			from `' . _DB_PREFIX_ . 'mdgift_rule_cart` mrc LEFT JOIN `' . _DB_PREFIX_ . 'cart_cart_rule` ccr 
				on ccr.`id_cart_rule` = mrc.`id_cart_rule` where mrc.`id_cart` = '.(int) $cart->id;
        $cart_rules = Db::getInstance()->executeS($sql);
        foreach ($cart_rules as $row) {
            if (!$row['id_cart_rule']) {
                $old_gift_products = $this->getGiftProductAtCart($row['id_cart'], $row['id_mdgift_rule'], null, true);
                if (!empty($old_gift_products)) {
                    foreach ($old_gift_products as $old_product) {
                        $cart->updateQty((int) $old_product['quantity'], $old_product['id_product'], $old_product['id_product_attribute'], false, 'down', 0, null, false, true);
                    }
                }
                $this->deleteFromCartProduct($row['id_cart'], $row['id_mdgift_rule']);
                Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."mdgift_rule_cart`
                    WHERE `id_cart` = ".(int)$cart->id." AND `id_mdgift_rule` =".(int)$row['id_mdgift_rule']);
            }
        }
    }

    public function deleteFromCartProduct($id_cart, $id_mdgift_rule, $id_product = null, $id_product_attribute = null)
    {
        Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."mdgift_rule_cart_product`
						  WHERE `id_cart` = ".(int)$id_cart." AND `id_mdgift_rule` =".(int)$id_mdgift_rule.
                          ($id_product ? " AND `id_product` = ".(int)$id_product : "").
                          ($id_product_attribute ? " AND `id_product_attribute` = ".(int)$id_product_attribute : ""));
    }

    
    public function calculateCartDiscount($id_cart, $id_mdgift_rule, $id_cart_rule)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            $id_cart = $this->context->cart->id;
        }
        
        $cart = $this->context->cart;
        $voucher_amount = 0;
        $products = $this->getGiftProductAtCart($id_cart, $id_mdgift_rule);
        
        if (!empty($products)) {
            foreach ($products as $productItem) {
                $cart_products = $cart->getProducts($productItem['id_product']);
                foreach ($cart_products as $cart_product) {
                    if ($cart_product['id_product_attribute'] == $productItem['id_product_attribute']
                        && $cart_product['id_product'] == $productItem['id_product']) {
                        $voucher_amount += self::switchPrice($cart_product['price_with_reduction'] * 1, Context::getContext()->currency, Currency::getDefaultCurrency(), false);
                        break;
                    }
                }
            }
        }
        $cart_rule = new CartRule($id_cart_rule);
        if ($voucher_amount > 0 && !empty($products)) {
            $cart_rule->date_from = date('Y-m-d H:i:s');
            $cart_rule->date_to = date('Y-m-d H:i:s');
            $cart_rule->reduction_amount = (float)$voucher_amount;
            $cart_rule->date_add = date('Y-m-d H:i:s');
            $cart_rule->date_upd = date('Y-m-d H:i:s');
            $cart_rule->update();
        } else {
            $cart_rule->delete();
            DB::getInstance()->delete('cart_cart_rule', 'id_cart_rule = ' . (int)$id_cart_rule . ' AND id_cart = ' . (int)$id_cart);
            DB::getInstance()->delete('mdgift_rule_cart', 'id_mdgift_rule = ' . (int)$id_mdgift_rule . ' AND id_cart = ' . (int)$id_cart);
        }
    }
    public function getTotalProduct()
    {
        $sql = 'SELECT count(*) as nb_product 
                FROM `'._DB_PREFIX_.'mdgift_rule_product`
                WHERE `id_mdgift_rule` = '.(int) $this->id_mdgift_rule;

        $nb_product = Db::getInstance()->getValue($sql);
        return $nb_product;
    }
    public static function deleteRulesNotUsed($id_mdgift_rule = null)
    {
        $sql = 'SELECT `id_cart`, `id_mdgift_rule`, `id_cart_rule`
                FROM `'._DB_PREFIX_.'mdgift_rule_cart` mdrc
                WHERE mdrc.`id_cart_rule` NOT IN (SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'order_cart_rule`)'.
                ($id_mdgift_rule ? ' AND `id_mdgift_rule` = '.(int)$id_mdgift_rule : '');

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $rule) {
            $cartRule = new CartRule((int)$rule['id_cart_rule']);
            $cart = new Cart((int)$rule['id_cart']);

            try {
                Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."mdgift_rule_cart_product`
						  WHERE `id_mdgift_rule` =".(int)$id_mdgift_rule);
    
                Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."mdgift_rule_cart`
                    WHERE `id_cart` = ".(int)$cart->id." AND `id_cart_rule` =".(int)$rule['id_cart_rule']);
                $cart->removeCartRule((int)$rule['id_cart_rule']);
                $cartRule->delete();
            } catch (Exception $e) {
            }
        }

        return true;
    }
    
    public function createCode($prefix, $length)
    {
        $alphanum = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $prefix . Tools::substr(str_shuffle(str_repeat($alphanum, ceil($length / Tools::strlen($alphanum)))), 1, $length);
    }
}
