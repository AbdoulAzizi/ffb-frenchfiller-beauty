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

class EDQuery
{
    // --- Query builder vars:
    protected $source_cart;
    protected $tp;
    protected $offset;
    protected $row_count = 10;
    protected $version;
    protected $languages;
    protected $recent_data = false;

    // --- Constructor / destructor:

    public function __construct()
    {
    }

    // --- Configuration methods:

    public function setRowCount($number)
    {
        $this->row_count = (int)$number;
    }

    public function setLanguages($string)
    {
        $this->languages = pSQL($string);
    }

    public function setVersion($string)
    {
        $this->version = $string;
    }

    public function setCart($string)
    {
        $this->source_cart = $string;
    }

    public function setPrefix($string)
    {
        $this->tp = pSQL($string);
    }

    public function setOffset($number)
    {
        $this->offset = (int)$number;
    }

    public function setRecentData($recent_data)
    {
        $this->recent_data= (bool)$recent_data;
    }

    // --- get query string methods:

    public function getDefaultShopValues()
    {
        $q = array();

        if (version_compare($this->version, '1.5', '<')) {
            $q['root_home'] = "SELECT `id_category` AS home,`id_category` AS root FROM `" . pSQL($this->tp) . "category` WHERE `id_parent` = 0";
            $q['get_max_cat'] = "SELECT max(id_category) AS max_cat_id FROM " . pSQL($this->tp) . "category";
        } else {
            $q['root_home'] = "SELECT `id_category` AS home, (SELECT `id_category` as root FROM `" . pSQL($this->tp) . "category` WHERE `id_parent` = 0) AS root FROM `" . pSQL($this->tp) . "category` WHERE `id_parent` = (SELECT `id_category` FROM `" . pSQL($this->tp) . "category` WHERE `id_parent` = 0)";
            $q['get_max_cat'] = "SELECT max(id_category) AS max_cat_id FROM " . pSQL($this->tp) . "category";
        }
        $q['default_lang'] = "SELECT cfg.*, lg.* FROM " . pSQL($this->tp) . "lang AS lg LEFT JOIN " . pSQL($this->tp) . "configuration AS cfg ON lg.id_lang = cfg.value WHERE cfg.name = 'PS_LANG_DEFAULT'";
        $q['default_currency'] = "SELECT cfg.*, cur.* FROM " . pSQL($this->tp) . "currency AS cur LEFT JOIN " . pSQL($this->tp) . "configuration AS cfg ON cur.id_currency = cfg.value WHERE cfg.name = 'PS_CURRENCY_DEFAULT'";
        $q['root_category'] = "SELECT * FROM " . pSQL($this->tp) . "configuration WHERE name = 'PS_ROOT_CATEGORY'";

        return $q;
    }

    public function getMappingInfo($default_lang)
    {
        $q = array();
        if (version_compare($this->version, '1.5', '<')) {
            $q['multi_shops'] = 'SELECT 0 as `source_id`, `value` as `source_name` FROM  `' . pSQL($this->tp) . 'configuration` WHERE `name` =  \'PS_SHOP_NAME\'';
        } else {
            $q['multi_shops'] = 'SELECT `id_shop` as `source_id`, `name` as `source_name` FROM  `' . pSQL($this->tp) . 'shop` WHERE `active` = 1';
        }

        $q['languages'] = 'SELECT `id_lang` as `source_id`, `name` as `source_name` FROM `' . pSQL($this->tp) . 'lang` WHERE `active` = 1';
        if (version_compare($this->version, '1.4', '<')) {
            $q['currencies'] = 'SELECT `id_currency` as `source_id`, `name` as `source_name` FROM `' . pSQL($this->tp) . 'currency`';
        } else {
            $q['currencies'] = 'SELECT `id_currency` as `source_id`, `name` as `source_name` FROM `' . pSQL($this->tp) . 'currency` WHERE `active` = 1';
        }
        $q['order_states'] = 'SELECT os.id_order_state as `source_id`, os.name as `source_name` FROM `' . pSQL($this->tp) . 'order_state_lang` AS `os` WHERE id_lang = ' . (int)pSQL($default_lang) . ' GROUP BY os.id_order_state';
        $q['customer_groups'] = 'SELECT `g`.`id_group` as `source_id`,  `g`.`name` as `source_name` FROM `' . pSQL($this->tp) . 'group_lang` AS `g` WHERE id_lang = ' . (int)pSQL($default_lang) . ' GROUP BY `g`.`id_group`';

        return $q;
    }

    public function getCountInfo()
    {
        $q = array();

        if (Tools::getValue('entities_taxes') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['taxes'] = 'SELECT count(1) as `c`  FROM  `' . pSQL($this->tp) . 'tax_rules_group` ';
            }
        }

        if (Tools::getValue('entities_manufacturers') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['manufacturers'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'manufacturer`';
            }
        }

        if (Tools::getValue('entities_categories') == 1) {
            $root_cat = Configuration::get('migrationpro_source_root_cat');
            $home_cat = Configuration::get('migrationpro_source_home_cat');
            if (!Tools::getValue('migrate_recent_data')) {
                if (version_compare($this->version, '1.5', '<')) {
                    $q['categories'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'category` WHERE id_category != ' . (int)$root_cat . ' ';
                } else {
                    $q['categories'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'category` WHERE id_category != ' . (int)$root_cat . ' AND id_category != ' . (int)$home_cat . '  ';
                }
            }
        }

        if (Tools::getValue('entities_carriers') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['carriers'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'carrier` WHERE `deleted` = 0';
            }
        }

//        if (Tools::getValue('entities_warehouse') == 1) {
//            if (Tools::getValue('migrate_recent_data')) {
//                $q['warehouse'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'warehouse` WHERE `deleted` != 0 ';
//            }
//        }

        if (Tools::getValue('entities_catalog_price_rules') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['catalog_price_rules'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'specific_price_rule`';
            }
        }

        if (Tools::getValue('entities_employees') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['employees'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'employee`';
            }
        }

        if (Tools::getValue('entities_products') == 1) {
            $last_migrated_product_id = MigrationProMigratedData::getLastId('product');
            if (Tools::getValue('migrate_recent_data')) {
                $q['products'] = 'SELECT count(1) as `c`  FROM  `' . pSQL($this->tp) . 'product` WHERE `id_product` > '.(int)$last_migrated_product_id;
            } else {
                $q['products'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'product`';
            }
        }

        if (Tools::getValue('entities_products') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['accessories'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'accessory`';
            }
        }

        if (Tools::getValue('entities_customers') == 1) {
            $last_migrated_customer_id = MigrationProMigratedData::getLastId('customer');
            if (Tools::getValue('migrate_recent_data')) {
                $q['customers'] = 'SELECT count(1) as `c`  FROM  `' . pSQL($this->tp) . 'customer` WHERE `id_customer` > '.(int)$last_migrated_customer_id;
            } else {
                $q['customers'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'customer` WHERE id_customer != 0';
            }
        }

        if (Tools::getValue('entities_orders') == 1 && Tools::getIsset('entities_customers') == 1) {
            $last_migrated_order_id = MigrationProMigratedData::getLastId('order');
            if (Tools::getValue('migrate_recent_data')) {
                $q['orders'] = 'SELECT count(1) as `c`  FROM  `' . pSQL($this->tp) . 'orders` WHERE `id_order` > '.(int)$last_migrated_order_id;
            } else {
                $q['orders'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'orders` WHERE id_order != 0';
            }
        }

        if (Tools::getValue('entities_orders') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['message_threads'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'customer_thread`';
            }
        }

        if (Tools::getValue('entities_cart_rules') == 1) {
            if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
                if (!Tools::getValue('migrate_recent_data')) {
                    $q['cart_rules'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'discount`';
                }
            } else {
                if (!Tools::getValue('migrate_recent_data')) {
                    $q['cart_rules'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'cart_rule`';
                }
            }
        }

        if (Tools::getValue('entities_cms') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['cms'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'cms`';
            }
        }

        if (Tools::getValue('entities_metas') == 1) {
            if (!Tools::getValue('migrate_recent_data')) {
                $q['seo'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'meta`';
            }
        }


        // @TODO migrate comments in module version 2.1
        /*if (Tools::getIsset('entities_reviews') && Tools::getIsset('entities_products')) {
            $q['reviews'] = 'SELECT count(1) as `c` FROM  `' . pSQL($this->tp) . 'product_comment`';
        }*/

        return $q;
    }


    // --- Tax methods:

    public function taxRulesGroup()
    {
        $q = array();
        $q['tax_rules_group'] = 'SELECT * FROM ' . pSQL($this->tp) . 'tax_rules_group ORDER BY id_tax_rules_group ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        $q['tax_rules_group_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'tax_rules_group_shop';

        return $q;
    }

    public function taxRules($taxRulesGroupIds)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'tax_rule WHERE id_tax_rules_group IN (' . pSQL($taxRulesGroupIds) . ')';
    }

    public function taxLangCountryLangState($taxIds, $countryIds, $stateIds)
    {
        $q = array();
        $q['tax'] = 'SELECT * FROM ' . pSQL($this->tp) . 'tax WHERE id_tax IN (' . pSQL($taxIds) . ')';
        $q['tax_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'tax_lang WHERE id_tax IN (' . pSQL($taxIds) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['country'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country WHERE id_country IN (' . pSQL($countryIds) . ')';
        $q['country_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_shop WHERE id_country IN (' . pSQL($countryIds) . ')';
        $q['country_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_lang WHERE id_country IN (' . pSQL($countryIds) . ') AND id_lang
         IN ( ' . pSQL($this->languages) . ' )';
        $q['state'] = 'SELECT * FROM ' . pSQL($this->tp) . 'state WHERE id_state IN (' . pSQL($stateIds) . ')';

        return $q;
    }

    // --- Manufactures methods:

    public function manufactures()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'manufacturer ORDER BY id_manufacturer ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    public function manufacturesSqlSecond($id_manufactures)
    {
        $q = array();
        $q['manufactures_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'manufacturer_lang ORDER BY id_manufacturer IN (' . pSQL($id_manufactures) . ') AND id_lang
        IN ( ' . pSQL($this->languages) . ' )';

        $q['manufactures_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'manufacturer_shop WHERE id_manufacturer IN (' . pSQL($id_manufactures) . ')';

        return $q;
    }

    // --- Category methods:

    public function category()
    {
        $root_cat = Configuration::get('migrationpro_source_root_cat');
        $home_cat = Configuration::get('migrationpro_source_home_cat');
        if (version_compare($this->version, '1.5', '<')) {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'category WHERE id_category !=' . (int)$root_cat . ' ORDER BY level_depth ASC, id_category ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'category WHERE id_category !=' . (int)$root_cat . ' AND id_category != ' . (int)$home_cat . ' ORDER BY level_depth ASC, id_category ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
    }

    public function singleCategory($id_category)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'category WHERE id_category = ' . (int)$id_category;
    }

    public function categorySqlSecond($id_categories)
    {
        $q = array();
        $q['category_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'category_lang WHERE id_category IN (' . pSQL($id_categories) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';

        $q['category_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'category_shop WHERE id_category IN (' . pSQL($id_categories) . ')';
        $q['category_group'] = 'SELECT * FROM ' . pSQL($this->tp) . 'category_group WHERE id_category IN (' .  pSQL($id_categories) . ')';

        return $q;
    }

    // --- Carrier method:

    public function carrier()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'carrier WHERE `deleted` != 1 ORDER BY id_carrier ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    public function carrierSqlSecond($id_carriers)
    {
        $q = array();
        $q['all_zones'] = 'SELECT * FROM ' . pSQL($this->tp) . 'zone';
        $q['carrier_delivery'] = 'SELECT * FROM ' . pSQL($this->tp) . 'delivery WHERE id_carrier IN (' . pSQL($id_carriers) . ')';
        $q['range_price'] = 'SELECT * FROM ' . pSQL($this->tp) . 'range_price WHERE id_carrier IN (' . pSQL($id_carriers) . ')';
        $q['range_weight'] = 'SELECT * FROM ' . pSQL($this->tp) . 'range_weight WHERE id_carrier IN (' . pSQL($id_carriers) . ')';
        $q['carrier_group'] = 'SELECT * FROM ' . pSQL($this->tp) . 'carrier_group WHERE id_carrier IN (' . pSQL($id_carriers) . ')';
        $q['carrier_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'carrier_lang WHERE id_carrier IN (' . pSQL($id_carriers) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['carrier_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'carrier_shop WHERE id_carrier IN (' . pSQL($id_carriers) . ')';
        $q['carrier_tax_rules_group_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'carrier_tax_rules_group_shop WHERE 
        id_carrier 
        IN (' . pSQL($id_carriers) . ')';
        $q['carrier_zone'] = 'SELECT * FROM ' . pSQL($this->tp) . 'carrier_zone WHERE id_carrier IN (' . pSQL($id_carriers) . ')';

        return $q;
    }

    // --- Warehouse methodes

//    public function warehouses()
//    {
//        return 'SELECT * FROM ' . pSQL($this->tp) . 'warehouse where deleted != 0 LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
//    }
//
//    public function warehousesSqlSecond($id_warehouses, $id_address)
//    {
//        $q = array();
//        $q['warehouse_carrier'] = 'SELECT * FROM ' . pSQL($this->tp) . 'warehouse_carrier WHERE id_warehouse IN (' . pSQL($id_warehouses) . ')';
//        $q['warehouse_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'warehouse_shop WHERE id_warehouse IN (' . pSQL($id_warehouses) . ')';
//        $q['address'] = 'SELECT a.*, z.iso_code as zone_code, c.iso_code as country_code
//                                            FROM ' . pSQL($this->tp) . 'address AS a
//                                                LEFT JOIN ' . pSQL($this->tp) . 'country AS c ON a.id_country = c.id_country
//                                                LEFT JOIN ' . pSQL($this->tp) . 'state AS z ON a.id_state = z.id_state
//                                            WHERE a.id_address IN (' . pSQL($id_address) . ')';
//
//        return $q;
//    }

    // --- Product method:

    public function product()
    {
        $last_migrated_product_id = MigrationProMigratedData::getLastId('product');
        if ($this->recent_data) {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'product WHERE `id_product` > '.(int)$last_migrated_product_id.' ORDER BY id_product ASC LIMIT ' . (int)$this->row_count;
        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'product ORDER BY id_product ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
    }

    public function singleProduct($id_product)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'product WHERE id_product = ' . (int)$id_product;
    }

    public function productSqlSecond($id_product)
    {
        $q = array();
        $q['product_carrier'] = 'SELECT a.*, b.id_carrier FROM ' . pSQL($this->tp) . 'product_carrier AS a INNER JOIN ' . pSQL($this->tp) . 'carrier AS b ON a.id_carrier_reference = b.id_reference WHERE b.deleted = 0 AND id_product IN (' . pSQL($id_product) . ')';
        $q['product_pack'] = 'SELECT * FROM ' . pSQL($this->tp) . 'pack WHERE id_product_pack IN (' . pSQL($id_product) . ')';
        $q['product_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_lang WHERE id_product IN (' . pSQL($id_product) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['specific_price'] = 'SELECT * FROM ' . pSQL($this->tp) . 'specific_price WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['category_product'] = 'SELECT * FROM ' . pSQL($this->tp) . 'category_product WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['stock_available'] = 'SELECT * FROM ' . pSQL($this->tp) . 'stock_available WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['image'] = 'SELECT * FROM ' . pSQL($this->tp) . 'image WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['product_download'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_download WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['product_attachment'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_attachment WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['product_attribute'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_attribute WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['product_supplier'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_supplier WHERE id_product IN (' . pSQL($id_product) . ') AND id_product_attribute = 0';
        $q['feature_product'] = 'SELECT * FROM ' . pSQL($this->tp) . 'feature_product WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['customization_field'] = 'SELECT * FROM ' . pSQL($this->tp) . 'customization_field WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['product_tag'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_tag WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['product_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_shop WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['stock'] = 'SELECT * FROM ' . pSQL($this->tp) . 'stock WHERE id_product IN (' . pSQL($id_product) . ')';
        $q['warehouse_product_location'] = 'SELECT * FROM ' . pSQL($this->tp) . 'warehouse_product_location WHERE id_product IN (' . pSQL($id_product) . ')';

        return $q;
    }

    public function productSqlThird(
        $id_product_attribute,
        $id_feature,
        $id_feature_value,
        $id_supplier,
        $id_customization_field,
        $id_tag,
        $id_image,
        $id_attachment
    ) {
        $q = array();
        $q['product_attribute_combination'] = 'SELECT pac.*, a.* FROM ' . pSQL($this->tp) . 'product_attribute_combination as pac LEFT JOIN ' . pSQL($this->tp) . 'attribute as a ON a.id_attribute = pac.id_attribute WHERE pac.id_product_attribute IN (' . pSQL($id_product_attribute) . ')';
        $q['product_attribute_image'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_attribute_image WHERE id_product_attribute IN (' . pSQL($id_product_attribute) . ')';
        $q['product_attribute_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'product_attribute_shop WHERE id_product_attribute IN (' . pSQL($id_product_attribute) . ')';
        $q['feature_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'feature_lang WHERE id_feature IN (' . pSQL($id_feature) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['feature_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'feature_shop WHERE id_feature IN (' . pSQL($id_feature) . ')';
        $q['feature_value'] = 'SELECT * FROM ' . pSQL($this->tp) . 'feature_value WHERE id_feature_value IN (' . pSQL($id_feature_value) . ')';
        $q['feature_value_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'feature_value_lang WHERE id_feature_value IN (' .
            pSQL($id_feature_value) . ') AND id_lang IN (' . pSQL($this->languages) . ')';
        $q['supplier'] = 'SELECT * FROM ' . pSQL($this->tp) . 'supplier WHERE id_supplier IN (' . pSQL($id_supplier) . ')';
        $q['supplier_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'supplier_shop WHERE id_supplier IN (' . pSQL($id_supplier) . ')';
        $q['supplier_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'supplier_lang WHERE id_supplier IN (' . pSQL($id_supplier) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['customization_field_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'customization_field_lang WHERE id_customization_field IN (' . pSQL($id_customization_field) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['tag'] = 'SELECT * FROM ' . pSQL($this->tp) . 'tag WHERE id_tag IN (' . pSQL($id_tag) . ')'; //@TODO  AND id_lang IN (' . pSQL($this->languages)ForQuery . ')
        $q['image_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'image_lang WHERE id_image IN (' . pSQL($id_image) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['image_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'image_shop WHERE id_image IN (' . pSQL($id_image) . ')';
        $q['attachment'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attachment WHERE id_attachment IN (' . pSQL($id_attachment) . ')';
        $q['attachment_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attachment_lang WHERE id_attachment IN (' . pSQL($id_attachment) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';

        return $q;
    }

    public function productSqlFourth($id_attribute_group, $id_attribute)
    {
        $q = array();
        $q['attribute_group'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attribute_group WHERE id_attribute_group IN (' . pSQL($id_attribute_group) . ')';
        $q['attribute_group_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attribute_group_shop WHERE id_attribute_group IN (' . pSQL($id_attribute_group) . ')';
        $q['attribute_group_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attribute_group_lang WHERE id_attribute_group IN (' . pSQL($id_attribute_group) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['attribute'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attribute WHERE id_attribute IN (' . pSQL($id_attribute) . ')';
        $q['attribute_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attribute_shop WHERE id_attribute IN (' . pSQL($id_attribute) . ')';
        $q['attribute_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'attribute_lang WHERE id_attribute IN (' . pSQL($id_attribute) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';

        return $q;
    }

    public function specificPriceRule()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'specific_price_rule ORDER BY id_specific_price_rule ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    public function specificPriceRuleCountry($id_countries)
    {
        $q = array();

        $q['country'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country WHERE id_country IN (' . pSQL($id_countries) . ')';
        $q['country_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_shop WHERE id_country IN (' . pSQL($id_countries) . ')';
        $q['country_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_lang WHERE id_country IN (' . pSQL($id_countries) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';

        return $q;
    }

    public function specificPriceRuleConditionGroup($id_specific_price_rules)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'specific_price_rule_condition_group WHERE id_specific_price_rule IN (' . pSQL($id_specific_price_rules) . ')';
    }

    public function specificPriceRuleCondition($id_specific_price_rule_condition_groups)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'specific_price_rule_condition WHERE id_specific_price_rule_condition_group IN (' . pSQL($id_specific_price_rule_condition_groups) . ')';
    }

    // --- product accessories method:
    public function accessories()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'accessory  LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    // --- Employee method:
    public function employee()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'employee ORDER BY id_employee ASC LIMIT ' . (int)$this->offset . ',' .
            (int)$this->row_count;
    }

    public function employeeShop($id_employees)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'employee_shop WHERE id_employee IN (' . pSQL($id_employees) . ')';
    }

    // --- Order method:

    public function order()
    {
        $last_migrated_order_id = MigrationProMigratedData::getLastId('order');
        if ($this->recent_data) {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'orders WHERE id_order != 0 AND `id_order` > '.(int)$last_migrated_order_id.' ORDER BY id_order ASC LIMIT ' . (int)$this->row_count;
        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'orders WHERE id_order != 0 ORDER BY id_order ASC LIMIT  ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
    }

    public function orderSqlSecond($id_order, $id_address_delivery, $id_currency, $order_reference)
    {
        $q = array();
        $q['message'] = 'SELECT * FROM ' . pSQL($this->tp) . 'message WHERE id_order IN (' . pSQL($id_order) . ')';
        $q['order_detail'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_detail WHERE id_order IN (' . pSQL($id_order) . ')';
        $q['order_return'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_return WHERE id_order IN (' . pSQL($id_order) . ')';
        $q['order_history'] = 'SELECT *  FROM ' . pSQL($this->tp) . 'order_history WHERE id_order IN (' . pSQL($id_order) . ') ORDER BY id_order_history ASC';
        $q['order_invoice'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_invoice WHERE id_order IN (' . pSQL($id_order) . ')';
        $q['order_carrier'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_carrier WHERE id_order IN (' . pSQL($id_order) . ')';
        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
            $q['order_cart_rule'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_discount WHERE id_order IN (' . pSQL($id_order) . ')';
            $q['order_payment'] = 'SELECT * FROM ' . pSQL($this->tp) . 'payment_cc WHERE id_order IN (' . pSQL($id_order) . ')';
        } else {
            $q['order_cart_rule'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_cart_rule WHERE id_order IN (' . pSQL($id_order) . ')';
            $q['order_payment'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_payment WHERE order_reference IN (' . pSQL($order_reference) . ')';
        }
        $q['invoice_payment'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_invoice_payment WHERE id_order IN (' . pSQL($id_order) . ')';
        $q['address'] = 'SELECT * FROM ' . pSQL($this->tp) . 'address WHERE id_address IN (' . pSQL($id_address_delivery) . ')';
        $q['currency'] = 'SELECT currency_id, code FROM ' . pSQL($this->tp) . 'currency WHERE id_currency IN (' . pSQL($id_currency) . ')';
        $q['customer_thread'] = 'SELECT * FROM ' . pSQL($this->tp) . 'customer_thread WHERE id_order IN (' . pSQL($id_order) . ')';
        $q['order_message'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_message';
        $q['order_slip'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_slip WHERE id_order IN (' . pSQL($id_order) . ')';

        return $q;
    }

    public function orderSqlThird($id_order_detail, $id_order_return, $id_order_invoice, $id_country, $id_state, $id_customer_thread, $orderMessageIds, $orderSlips, $id_message)
    {
        $q = array();
        $q['message_readed'] = 'SELECT * FROM ' . pSQL($this->tp) . 'message_readed WHERE id_message IN (' . pSQL($id_message) . ')';
        $q['order_detail_tax'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_detail_tax WHERE id_order_detail IN (' . pSQL($id_order_detail) . ')';
        $q['order_return_detail'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_return_detail WHERE id_order_return IN (' . pSQL($id_order_return) . ')';
        $q['invoice_tax'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_invoice_tax WHERE id_order_invoice IN (' . pSQL($id_order_invoice) . ')';
        $q['country'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country WHERE id_country IN (' . pSQL($id_country) . ')';
        $q['country_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_shop WHERE id_country IN (' . pSQL($id_country) . ')';
        $q['country_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_lang WHERE id_country IN (' . pSQL($id_country) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['state'] = 'SELECT * FROM ' . pSQL($this->tp) . 'state WHERE id_state IN (' . pSQL($id_state) . ')';
        $q['customer_message'] = 'SELECT * FROM ' . pSQL($this->tp) . 'customer_message WHERE id_customer_thread IN (' . pSQL($id_customer_thread) . ')';
        $q['order_message_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_message_lang WHERE id_order_message IN (' . pSQL($orderMessageIds) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['order_slip_detail'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_slip_detail WHERE id_order_slip IN (' . pSQL($orderSlips) . ')';
        $q['order_slip_detail_tax'] = 'SELECT * FROM ' . pSQL($this->tp) . 'order_slip_detail';

        return $q;
    }


    public function customerThreads()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'customer_thread ORDER BY id_customer_thread ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    public function customerMessages($id_customer_thread)
    {
//        if (version_compare(Configuration::get('migrationpro_version'), '1.6', '<')) {
//            return 'SELECT * FROM ' . pSQL($this->tp) . 'customer_message WHERE id_customer_thread IN (' . pSQL($id_customer_thread) . ')';
//        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'customer_message WHERE id_customer_thread IN (' . pSQL($id_customer_thread) . ')';
//        }
    }

    // --- Customer methods:

    public function customers()
    {
        $last_migrated_customer_id = MigrationProMigratedData::getLastId('customer');
        if ($this->recent_data) {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'customer WHERE id_customer != 0 AND `id_customer` > '.(int)$last_migrated_customer_id.' ORDER BY id_customer ASC LIMIT ' . (int)$this->row_count;
        } else {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'customer WHERE id_customer != 0 ORDER BY id_customer ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
        }
    }

    public function address($id_customers)
    {
        $q = array();

        $q['address'] = 'SELECT a.*, z.iso_code as zone_code, c.iso_code as country_code
                                            FROM ' . pSQL($this->tp) . 'address AS a
                                                LEFT JOIN ' . pSQL($this->tp) . 'country AS c ON a.id_country = c.id_country
                                                LEFT JOIN ' . pSQL($this->tp) . 'state AS z ON a.id_state = z.id_state
                                            WHERE a.id_customer IN (' . pSQL($id_customers) . ')';

        $q['customer_group'] = 'SELECT * FROM ' . pSQL($this->tp) . 'customer_group WHERE id_customer IN (' . pSQL($id_customers) . ')';

        return $q;
    }

    public function countryState($id_countries, $id_states)
    {
        $q = array();
        $q['countries'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country WHERE id_country IN (' .
            pSQL($id_countries) . ')';
        $q['country_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_shop WHERE id_country IN (' . pSQL($id_countries) . ')';
        $q['country_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_lang WHERE id_country IN (' . pSQL($id_countries) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['states'] = 'SELECT * FROM ' . pSQL($this->tp) . 'state WHERE id_state IN (' .
            pSQL($id_states) . ')';

        return $q;
    }

    public function cart($id_customers)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'cart where id_customer IN (' . pSQL($id_customers) . ')';
    }

    public function cartProductCartRule($id_carts)
    {
        $q = array();
        $q['cart_product'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_product where id_cart in (' . pSQL($id_carts) . ')';
        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
            $q['cart_cart_rule'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_discount where id_cart in (' . pSQL($id_carts) . ')';
            return $q;
        }
        $q['cart_cart_rule'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_cart_rule where id_cart in (' . pSQL($id_carts) . ')';

        return $q;
    }

    // --- CMS method:
    public function cms()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'cms ORDER BY id_cms ASC LIMIT ' . (int)$this->offset . ',' .
            (int)$this->row_count;
    }

    public function cmsSqlSecond($id_cms, $id_cms_categories)
    {
        $q = array();
        $q['cms_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_lang WHERE id_cms IN (' .
            pSQL($id_cms) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['cms_role'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_role WHERE id_cms IN (' .
            pSQL($id_cms) . ')';
        $q['cms_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_shop WHERE id_cms IN (' .
            pSQL($id_cms) . ')';
        $q['cms_category'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_category WHERE id_cms_category IN (' .
            pSQL($id_cms_categories) . ')';
        $q['cms_block'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_block WHERE id_cms_category IN (' .
            pSQL($id_cms_categories) . ')';

        return $q;
    }

    public function cmsSqlThird($id_cms_role, $id_cms_categories, $id_cms_blocks)
    {
        $q = array();
        $q['cms_role_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_role_lang WHERE id_cms_role IN (' .
            pSQL($id_cms_role) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['cms_category_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_category_lang WHERE id_cms_category IN 
        (' . pSQL($id_cms_categories) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['cms_category_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_category_shop WHERE id_cms_category IN 
        (' . pSQL($id_cms_categories) . ')';
        $q['cms_block_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_block_lang WHERE id_cms_block IN (' .
            pSQL($id_cms_blocks) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        $q['cms_block_page'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_block_page WHERE id_cms_block IN (' .
            pSQL($id_cms_blocks) . ')';
        $q['cms_block_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cms_block_shop WHERE id_cms_block IN (' .
            pSQL($id_cms_blocks) . ')';

        return $q;
    }

    // --- Cart Rule methods:

    public function cartRule($id_customers)
    {
        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
            return 'SELECT * FROM ' . pSQL($this->tp) . 'discount WHERE id_customer IN (' . pSQL($id_customers) . ') LIMIT 10';
        }

        return 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule WHERE id_customer IN (' . pSQL($id_customers) . ') LIMIT 10';
    }

    public function cartRuleSqlSecond($id_cart_rules)
    {
        $q = array();
        if (version_compare(Configuration::get('migrationpro_version'), '1.5', '<')) {
            $q['cart_rule_langs'] = 'SELECT * FROM ' . pSQL($this->tp) . 'discount_lang WHERE id_discount IN (' . pSQL($id_cart_rules) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        } else {
            $q['cart_rule_langs'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_lang WHERE id_cart_rule IN (' . pSQL($id_cart_rules) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
        }
        $q['cart_rule_carriers'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_carrier WHERE id_cart_rule IN (' .
            pSQL($id_cart_rules)
            . ')';
        $q['cart_rule_combinations'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_combination WHERE id_cart_rule_1 IN 
        (' .
            pSQL($id_cart_rules)
            . ')';
        $q['cart_rule_countries'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_country WHERE id_cart_rule IN (' .
            pSQL($id_cart_rules)
            . ')';
        $q['cart_rule_groups'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_group WHERE id_cart_rule IN (' .
            pSQL($id_cart_rules)
            . ')';
        $q['cart_rule_product_rule_groups'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_product_rule_group WHERE id_cart_rule IN (' .
            pSQL($id_cart_rules)
            . ')';
        $q['cart_rule_shops'] = 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_shop WHERE id_cart_rule IN (' .
            pSQL($id_cart_rules)
            . ')';

        return $q;
    }

    public function cartRuleCountry($id_countries)
    {
        $q = array();
        $q['country'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country WHERE id_country IN (' . pSQL($id_countries) . ')';
        $q['country_shop'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_shop WHERE id_country IN (' . pSQL($id_countries) . ')';
        $q['country_lang'] = 'SELECT * FROM ' . pSQL($this->tp) . 'country_lang WHERE id_country IN (' . pSQL($id_countries) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';

        return $q;
    }

    public function cartRuleProductRule($id_product_rule_groups)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_product_rule WHERE id_product_rule_group IN (' . pSQL($id_product_rule_groups) . ')';
    }

    public function cartRuleProductRuleValue($id_product_rules)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'cart_rule_product_rule_value WHERE id_product_rule IN (' . pSQL($id_product_rules) . ')';
    }

    // --- Meta methods:

    public function meta()
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'meta ORDER BY id_meta ASC LIMIT ' . (int)$this->offset . ',' . (int)$this->row_count;
    }

    public function metaLang($id_metas)
    {
        return 'SELECT * FROM ' . pSQL($this->tp) . 'meta_lang WHERE id_meta IN (' . pSQL($id_metas) . ') AND id_lang IN ( ' . pSQL($this->languages) . ' )';
    }
}
