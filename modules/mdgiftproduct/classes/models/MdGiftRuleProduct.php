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

class MdGiftRuleProduct extends ObjectModel
{
    public $id_mdgift_rule_product;
    public $id_mdgift_rule;
    public $id_product;
    public $product_filter=null;
    public $gift_product_select;

    public $quantity;
    public static $definition = array(
        'table' => 'mdgift_rule_product',
        'primary' => 'id_mdgift_rule_product',
        'fields' => array(
            'id_mdgift_rule' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
        )
    );
    
    public function __construct($id = null)
    {
        parent::__construct($id);
        if (Context::getContext()->employee &&
            Context::getContext()->controller instanceof AdminGiftProductRulesController) {
            $this->getGiftProduct($this->id_mdgift_rule);
        }
    }
    
    public static function getProducts($id_mdgift_rule)
    {
        $sql = 'SELECT id_mdgift_rule_product
                FROM `'._DB_PREFIX_.'mdgift_rule_product`
                WHERE `id_mdgift_rule` = '.(int)$id_mdgift_rule;
                
        $results = Db::getInstance()->executeS($sql);
        $return = array_map(function ($item) {
            return $item['id_mdgift_rule_product'];
        }, $results);
        return $return;
    }
    
    public static function getProductsAttribut($id_mdgift_rule, $id_product)
    {
        $sql = 'SELECT id_product_attribute
                FROM `'._DB_PREFIX_.'mdgift_rule_product_attribute`
                WHERE `id_mdgift_rule` = '.(int)$id_mdgift_rule.' AND id_product='.(int)$id_product;
        $results = Db::getInstance()->executeS($sql);
        $return = array_map(function ($item) {
            return $item['id_product_attribute'];
        }, $results);
        return $return;
    }
    
    
    public function findProducts($search)
    {
        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }

        if ($products = Product::searchByName((int)$this->context->language->id, $search)) {
            foreach ($products as &$product) {
                $combinations = array();
                $productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
                $attributes = $productObj->getAttributesGroups((int)$this->context->language->id);
                $convertPrice = Tools::convertPrice($product['price_tax_incl'], $this->context->currency);
                $product['formatted_price'] = Tools::displayPrice($convertPrice, $this->context->currency);

                foreach ($attributes as $attribute) {
                    if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                        $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                    }
                    $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
                    $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                    $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                    if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                        $price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
                        $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_incl, $this->context->currency), $this->context->currency);
                    }
                }

                foreach ($combinations as &$combination) {
                    $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                }

                $product['combinations'] = $combinations;
            }

            return array(
                'products' => $products,
                'found' => true
            );
        } else {
            return array('found' => false, 'notfound' => Tools::displayError('No product has been found.'));
        }
    }
    
    public function getGiftProduct($id_mdgift_rule)
    {
        $product = new Product((int)$this->id_product, false, Configuration::get('PS_LANG_DEFAULT'), Context::getContext()->shop->id);
        if (!Validate::isLoadedObject($product)) {
            $this->id_product = null;
            return false;
        }
        if (Validate::isUnsignedId($this->id_product) &&
            ($product = new Product($this->id_product, false, Context::getContext()->language->id)) &&
            Validate::isLoadedObject($product)) {
            $this->product_filter = (!empty($product->reference) ? $product->reference : $product->name);
            $this->hasAttribute = $product->hasAttributes();
            $this->id_product_attribute = $this->getProductsAttribut($id_mdgift_rule, $this->id_product);
        }
        
        if ((int)$this->id_product) {
            $search_products = $this->findProducts($this->product_filter);
            $this->search_products = isset($search_products['products']) && is_array($search_products['products']) ?
                                $search_products['products'] : [];
        }
    }
}
