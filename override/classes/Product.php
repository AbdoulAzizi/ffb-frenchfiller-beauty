<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Hennes Hervé <contact@h-hennes.fr>
 *  @copyright 2013-2016 Hennes Hervé
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  http://www.h-hennes.fr/blog/
 */
class Product extends ProductCore {
    
    /*
    * module: wrd_product
    * date: 2022-01-17 12:57:40
    * version: 0.1.1
    */
    public $custom_composition_field_lang_wysiwyg;
    /*
    * module: wrd_product
    * date: 2022-01-17 12:57:40
    * version: 0.1.1
    */
    public $custom_utilisation_field_lang_wysiwyg;
    /*
    * module: wrd_product
    * date: 2022-01-17 12:57:40
    * version: 0.1.1
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null) {
        /*self::$definition['fields']['custom_field'] = [
            'type' => self::TYPE_STRING,
            'required' => false, 'size' => 255
        ];
        self::$definition['fields']['custom_field_lang']     = [
            'type' => self::TYPE_STRING,
            'lang' => true,
            'required' => false, 'size' => 255
        ];*/
        self::$definition['fields']['custom_composition_field_lang_wysiwyg']     = [
            'type' => self::TYPE_HTML,
            'lang' => true,
            'required' => false,
            'validate' => 'isCleanHtml'
        ];
        self::$definition['fields']['custom_utilisation_field_lang_wysiwyg']     = [
            'type' => self::TYPE_HTML,
            'lang' => true,
            'required' => false,
            'validate' => 'isCleanHtml'
        ];parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}
