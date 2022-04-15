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

class MdGiftRuleProductAttribute extends ObjectModel
{
    public $id_mdgift_rule_product_attribute;
    public $id_mdgift_rule;
    public $id_product;
    public $id_product_attribute;
    public $active;
    public static $definition = array(
        'table' => 'mdgift_rule_product_attribute',
        'primary' => 'id_mdgift_rule_product_attribute',
        'fields' => array(
            'id_mdgift_rule' => array('type' => self::TYPE_INT),
            'id_product' => array('type' => self::TYPE_INT),
            'id_product_attribute' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL)
        )
    );
}
