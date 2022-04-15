<?php
/*
 *  @Website: apollotheme.com - prestashop template provider
 *  @author Apollotheme <apollotheme@gmail.com>
 *  @copyright  2007-2016 Apollotheme
 *  @description: ApPageBuilder is module help you can build content for your shop
 */
if (!class_exists("ApPageSetup")) {

    class ApPageSetup
    {

        public static function getTabs()
        {
            return array(
                array(
                    'class_name' => 'AdminApPageBuilderProfiles',
                    'name' => 'Ap Profiles Manage',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderPositions',
                    'name' => 'Ap Positions Manage',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderShortcode',
                    'name' => 'Ap ShortCode Manage',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderHome',
                    'name' => 'Ap Hook Builder',
                    'id_parent' => -1,
                ),
                array(
                    'class_name' => 'AdminApPageBuilderProducts',
                    'name' => 'Ap Products List Builder',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderDetails',
                    'name' => 'Ap Products Details Builder',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderHook',
                    'name' => 'Ap Hook Control Panel',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderThemeEditor',
                    'name' => 'Ap Live Theme Editor',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderModule',
                    'name' => 'Ap Module Configuration',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderThemeConfiguration',
                    'name' => 'Ap Theme Configuration',
                ),
                array(
                    'class_name' => 'AdminApPageBuilderImages',
                    'name' => 'Ap Image Manage',
                    'id_parent' => -1,
                ),
                array(
                    'class_name' => 'AdminApPageBuilderShortcodes',
                    'name' => 'Ap Shortcodes Builder',
                    'id_parent' => -1,
                ),
            );
        }

        public static function createTables($reset = 0)
        {
            if ($reset == 0 && file_exists(_PS_MODULE_DIR_.'appagebuilder')) {
                require_once(_PS_MODULE_DIR_.'appagebuilder/libs/LeoDataSample.php');

                $sample = new Datasample();
                if ($sample->processImport('appagebuilder')) {
                    return true;
                }
            }
            $drop = '';
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_profiles`;';
            }
            //each shop will have one or more profile
            $res = (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_profiles` (
                    `id_appagebuilder_profiles` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255),
                        `group_box` varchar(255),
                        `profile_key` varchar(255),
                        `page` varchar(255),
                        `params` text,
                        `header` int(11) unsigned NOT NULL,
                        `content` int(11) unsigned NOT NULL,
                        `footer` int(11) unsigned NOT NULL,
                        `product` int(11) unsigned NOT NULL,
                        `active` TINYINT(1),
                        `active_mobile` TINYINT(1),
                        `active_tablet` TINYINT(1),
                    PRIMARY KEY (`id_appagebuilder_profiles`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_lang`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_lang` (
                   `id_appagebuilder_profiles` int(11) NOT NULL AUTO_INCREMENT,
                   `id_lang` int(10) unsigned NOT NULL,
                   `friendly_url` varchar(255),
                    `meta_title` varchar(255),
                    `meta_description` varchar(255),
                    `meta_keywords` varchar(255),
                   PRIMARY KEY (`id_appagebuilder_profiles`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_profiles_shop` (
                  `id_appagebuilder_profiles` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  `active_mobile` TINYINT(1),
                  `active_tablet` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_profiles`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_products`;';
            }
            //we can create product item for each shop
            $res &= (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_products` (
                    `id_appagebuilder_products` int(11) NOT NULL AUTO_INCREMENT,
                        `plist_key` varchar(255),
                        `name` varchar(255),
                        `class` varchar(255),
                        `params` text,
                        `type` TINYINT(1),
                        `active` TINYINT(1),
                        `active_mobile` TINYINT(1),
                        `active_tablet` TINYINT(1),
                    PRIMARY KEY (`id_appagebuilder_products`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_products_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_products_shop` (
                  `id_appagebuilder_products` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  `active_mobile` TINYINT(1),
                  `active_tablet` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_products`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            $res &= (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_details` (
                    `id_appagebuilder_details` int(11) NOT NULL AUTO_INCREMENT,
                        `plist_key` varchar(255),
                        `name` varchar(255),
                        `class_detail` varchar(255),
                        `url_img_preview` varchar(255),
                        `params` text,
                        `type` TINYINT(1),
                        `active` TINYINT(1),
                        `active_mobile` TINYINT(1),
                        `active_tablet` TINYINT(1),
                    PRIMARY KEY (`id_appagebuilder_details`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_details_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_details_shop` (
                  `id_appagebuilder_details` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  `active_mobile` TINYINT(1),
                  `active_tablet` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_details`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder`;';
            }
            $res &= (bool)Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder` (
                    `id_appagebuilder` int(11) NOT NULL AUTO_INCREMENT,
                        `id_appagebuilder_positions` int(11) NOT NULL,
                        `hook_name` varchar(255),
                        `id_appagebuilder_shortcode` int(11) NOT NULL,
                    PRIMARY KEY (`id_appagebuilder`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');

            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shop` (
                  `id_appagebuilder` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_appagebuilder`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_lang`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_lang` (
                   `id_appagebuilder` int(11) NOT NULL AUTO_INCREMENT,
                   `id_lang` int(10) unsigned NOT NULL,
                   `params` MEDIUMTEXT,
                   PRIMARY KEY (`id_appagebuilder`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
                
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_positions`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_positions` (
                    `id_appagebuilder_positions` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `position` varchar(255) NOT NULL,
                    `position_key` varchar(255) NOT NULL,
                    `params` text,
                    PRIMARY KEY (`id_appagebuilder_positions`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_positions_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_positions_shop` (
                  `id_appagebuilder_positions` int(11) NOT NULL AUTO_INCREMENT,
                  `id_shop` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_appagebuilder_positions`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_page_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_page` (
                  `id_product` int(11) unsigned NOT NULL,
                  `id_category` int(11) unsigned NOT NULL,
                  `page` varchar(255) NOT NULL,
                  `id_shop` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_product`, `id_category`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            //DONGND:: create table for ap shortcode
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode` (
                  `id_appagebuilder_shortcode` int(11) NOT NULL AUTO_INCREMENT,                  
                  `shortcode_key` varchar(255) NOT NULL,
                  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_shortcode`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            //DONGND:: create table for ap shortcode (lang)
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_lang`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_lang` (
                   `id_appagebuilder_shortcode` int(11) unsigned NOT NULL,
                   `id_lang` int(10) unsigned NOT NULL,
                   `shortcode_name` text NOT NULL,
                   PRIMARY KEY (`id_appagebuilder_shortcode`, `id_lang`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            //DONGND:: create table for ap shortcode (shop)
            if ($reset == 1) {
                $drop = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_shop`;';
            }
            $res &= Db::getInstance()->execute($drop.'
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'appagebuilder_shortcode_shop` (
                  `id_appagebuilder_shortcode` int(11) unsigned NOT NULL,
                  `id_shop` int(10) unsigned NOT NULL,
                  `active` TINYINT(1),
                  PRIMARY KEY (`id_appagebuilder_shortcode`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
            ');
            return $res;
        }

        public static function installSample()
        {
            $id_shop = Context::getContext()->shop->id;

            //table appagebuilder_profiles
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_profiles`');
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_profiles` (`id_appagebuilder_profiles`, `name`, `group_box`, `profile_key`, `page`, `params`, `header`, `content`, `footer`, `product`, `active`, `active_mobile`, `active_tablet`) VALUES
(1, 'Demo 1', '', 'profile1573644882', 'index', '{\"fullwidth_index_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":\"1\",\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"fullwidth_other_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":\"1\",\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"disable_cache_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":\"1\",\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0}}', 1, 2, 3, 4, 0, 0, 0),
(2, 'Demo 2', '', 'profile1574071534', 'index', '{\"fullwidth_index_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":0,\"displayHome\":\"1\",\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"fullwidth_other_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":0,\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"disable_cache_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":0,\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0}}', 5, 6, 7, 4, 0, 0, 0),
(3, 'Demo 3', '', 'profile1575994666', 'index', '{\"fullwidth_index_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":0,\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"fullwidth_other_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":0,\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"disable_cache_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":0,\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0}}', 9, 10, 11, 4, 0, 0, 0),
(4, 'Product List', '', 'profile2167878897', 'index', '{\"fullwidth_index_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":\"1\",\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"fullwidth_other_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":\"1\",\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0},\"disable_cache_hook\":{\"displayBanner\":0,\"displayNav1\":0,\"displayNav2\":0,\"displayTop\":\"1\",\"displayHome\":0,\"displayFooterBefore\":0,\"displayFooter\":0,\"displayFooterAfter\":0}}', 12, 13, 14, 15, 0, 0, 0);";
            Db::getInstance()->execute($sql);

            //table appagebuilder_profiles_shop
            Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'appagebuilder_profiles_shop`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_profiles_shop` (`id_appagebuilder_profiles`, `id_shop`, `active`, `active_mobile`, `active_tablet`) VALUES
(1, ID_SHOP, 1, 0, 0),
(2, ID_SHOP, 0, 0, 0),
(3, ID_SHOP, 0, 0, 0),
(4, ID_SHOP, NULL, NULL, NULL);';
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_profiles_lang`');
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_profiles_lang` (`id_appagebuilder_profiles`, `id_lang`, `friendly_url`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
(1, ID_LANG, 'demo_1', '', '', ''),
(2, ID_LANG, 'demo_2', '', '', ''),
(3, ID_LANG, 'demo_3', '', '', ''),
(4, ID_LANG, 'Product List', '', '', '');";
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $sqlRun = str_replace('ID_LANG', (int)$lang["id_lang"], $sql);
                Db::getInstance()->execute($sqlRun);
            }

            //table appagebuilder_positions
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_positions`');
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_positions` (`id_appagebuilder_positions`, `name`, `position`, `position_key`, `params`) VALUES
(1, 'header1', 'header', 'position1573788113', NULL),
(2, 'content1', 'content', 'position1573790518', NULL),
(3, 'footer1', 'footer', 'position1573808439', NULL),
(4, 'product', 'product', 'position1573815716', NULL),
(5, 'header2', 'header', 'position1574080501', NULL),
(6, 'content2', 'content', 'position1574078342', NULL),
(7, 'footer2', 'footer', 'position1574051395', NULL),
(8, 'product1574068924', 'product', 'position1574068924', NULL),
(9, 'header3', 'header', 'position1575975608', NULL),
(10, 'content3', 'content', 'position1575968183', NULL),
(11, 'footer3', 'footer', 'position1575977543', NULL),
(12, 'Duplicate 3651227315', 'header', 'duplicate_3651227315', NULL),
(13, 'Duplicate 3180179918', 'content', 'duplicate_3180179918', NULL),
(14, 'Duplicate 2440101375', 'footer', 'duplicate_2440101375', NULL),
(15, 'Duplicate 1859842468', 'product', 'duplicate_1859842468', NULL);";
            Db::getInstance()->execute($sql);

            //table appagebuilder_positions_shop
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_positions_shop`');
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_positions_shop` (`id_appagebuilder_positions`, `id_shop`) VALUES
(1, ID_SHOP),
(2, ID_SHOP),
(3, ID_SHOP),
(4, ID_SHOP),
(5, ID_SHOP),
(6, ID_SHOP),
(7, ID_SHOP),
(8, ID_SHOP),
(9, ID_SHOP),
(10, ID_SHOP),
(11, ID_SHOP),
(12, ID_SHOP),
(13, ID_SHOP),
(14, ID_SHOP),
(15, ID_SHOP);";
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            //table appagebuilder
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder`');
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "appagebuilder` (`id_appagebuilder`, `id_appagebuilder_positions`, `hook_name`, `id_appagebuilder_shortcode`) VALUES
(1, 1, 'displayBanner', 0),
(2, 1, 'displayNav1', 0),
(3, 1, 'displayNav2', 0),
(4, 1, 'displayTop', 0),
(5, 1, 'displayNavFullWidth', 0),
(6, 2, 'displayLeftColumn', 0),
(7, 2, 'displayHome', 0),
(8, 2, 'displayRightColumn', 0),
(9, 3, 'displayFooterBefore', 0),
(10, 3, 'displayFooter', 0),
(11, 3, 'displayFooterAfter', 0),
(12, 4, 'displayLeftColumnProduct', 0),
(13, 4, 'displayRightColumnProduct', 0),
(14, 4, 'displayReassurance', 0),
(15, 4, 'displayProductButtons', 0),
(16, 4, 'displayFooterProduct', 0),
(17, 5, 'displayBanner', 0),
(18, 5, 'displayNav1', 0),
(19, 5, 'displayNav2', 0),
(20, 5, 'displayTop', 0),
(21, 5, 'displayNavFullWidth', 0),
(22, 6, 'displayLeftColumn', 0),
(23, 6, 'displayHome', 0),
(24, 6, 'displayRightColumn', 0),
(25, 7, 'displayFooterBefore', 0),
(26, 7, 'displayFooter', 0),
(27, 7, 'displayFooterAfter', 0),
(28, 8, 'displayLeftColumnProduct', 0),
(29, 8, 'displayRightColumnProduct', 0),
(30, 8, 'displayReassurance', 0),
(31, 8, 'displayProductButtons', 0),
(32, 8, 'displayFooterProduct', 0),
(33, 9, 'displayBanner', 0),
(34, 9, 'displayNav1', 0),
(35, 9, 'displayNav2', 0),
(36, 9, 'displayTop', 0),
(37, 9, 'displayNavFullWidth', 0),
(38, 10, 'displayLeftColumn', 0),
(39, 10, 'displayHome', 0),
(40, 10, 'displayRightColumn', 0),
(41, 11, 'displayFooterBefore', 0),
(42, 11, 'displayFooter', 0),
(43, 11, 'displayFooterAfter', 0),
(44, 12, 'displayBanner', 0),
(45, 12, 'displayNav1', 0),
(46, 12, 'displayNav2', 0),
(47, 12, 'displayTop', 0),
(48, 12, 'displayNavFullWidth', 0),
(49, 13, 'displayLeftColumn', 0),
(50, 13, 'displayHome', 0),
(51, 13, 'displayRightColumn', 0),
(52, 14, 'displayFooterBefore', 0),
(53, 14, 'displayFooter', 0),
(54, 14, 'displayFooterAfter', 0),
(55, 15, 'displayLeftColumnProduct', 0),
(56, 15, 'displayRightColumnProduct', 0),
(57, 15, 'displayReassurance', 0),
(58, 15, 'displayProductButtons', 0),
(59, 15, 'displayFooterProduct', 0);";
            Db::getInstance()->execute($sql);

            
            //table appagebuilder_lang
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_lang`');

            $sql = "INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_lang` (`id_appagebuilder`, `id_lang`, `params`) VALUES
(1, 1, ''),
(2, 1, ''),
(3, 1, '[ApRow form_id=\"form_41207122582597845\" container=\"container\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1575991173\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"5\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_1575984779\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"7\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow]'),
(4, 1, '[ApRow form_id=\"form_1575963750\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1575964007\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow][ApRow form_id=\"form_7818444817951359\" container=\"container\" class=\"row\" bg_config=\"fullwidth\" bg_type=\"normal\" bg_color=\"#2fb5d2\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_4089073323820816\" id=\"widget_megamenu_demo\" xl=\"8\" lg=\"8\" md=\"4\" sm=\"4\" xs=\"4\" sp=\"4\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_7459367365282602\" class=\"box_phone\" xl=\"4\" lg=\"4\" md=\"8\" sm=\"8\" xs=\"8\" sp=\"8\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow]'),
(5, 1, ''),
(6, 1, ''),
(7, 1, '[ApRow form_id=\"form_7631228159743372\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_26432540424340315\" xl=\"8\" lg=\"8\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_6083637853601561\" id=\"widget_image_demo\" class=\"box-img\" xl=\"4\" lg=\"4\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow][ApRow form_id=\"form_44574962729101545\" id=\"widget_html_demo\" class=\"row block-services\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_7178010341851546\" xl=\"3\" lg=\"3\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApHtml form_id=\"form_8464916951570048\" accordion_type=\"full\" active=\"1\" content_html=\"<div>_APENTER_<p class=_APQUOT_h4_APQUOT_ style=_APQUOT_text-align: center;_APQUOT_>Free Shipping</p>_APENTER_<p style=_APQUOT_text-align: center;_APQUOT_>For Most Orders Over $249</p>_APENTER_</div>\"][/ApHtml][/ApColumn][ApColumn form_id=\"form_8719829997168135\" xl=\"3\" lg=\"3\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApHtml form_id=\"form_9719032812091192\" accordion_type=\"full\" active=\"1\" content_html=\"<div>_APENTER_<p class=_APQUOT_h4_APQUOT_ style=_APQUOT_text-align: center;_APQUOT_>Returns Easy</p>_APENTER_<p style=_APQUOT_text-align: center;_APQUOT_>30-Day Return Policy</p>_APENTER_</div>\"][/ApHtml][/ApColumn][ApColumn form_id=\"form_8000991657545159\" xl=\"3\" lg=\"3\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApHtml form_id=\"form_42787492444979915\" accordion_type=\"full\" active=\"1\" content_html=\"<div>_APENTER_<p class=_APQUOT_h4_APQUOT_ style=_APQUOT_text-align: center;_APQUOT_>Gift Cards</p>_APENTER_<p style=_APQUOT_text-align: center;_APQUOT_>The Gift That Never Expires</p>_APENTER_</div>\"][/ApHtml][/ApColumn][ApColumn form_id=\"form_5901350665483052\" xl=\"3\" lg=\"3\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApHtml form_id=\"form_6419793310697052\" accordion_type=\"full\" active=\"1\" content_html=\"<div>_APENTER_<p class=_APQUOT_h4_APQUOT_ style=_APQUOT_text-align: center;_APQUOT_>Supports</p>_APENTER_<p style=_APQUOT_text-align: center;_APQUOT_>Service Supports 24/7</p>_APENTER_</div>\"][/ApHtml][/ApColumn][/ApRow][ApRow form_id=\"form_7207444312600826\" id=\"widget_product_carousel_demo\" class=\"row\" margin_top=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_6039641913314738\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_23395424498191855\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"1\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"1\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" nbitemsperline_desktop=\"4\" nbitemsperline_smalldesktop=\"3\" nbitemsperline_tablet=\"3\" nbitemsperline_smalldevices=\"2\" nbitemsperline_extrasmalldevices=\"2\" nbitemsperline_smartphone=\"1\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"default\" title=\"New Arrival\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_6601177085491516\" class=\"row\" margin_bottom=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_2582059690598228\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApImage form_id=\"form_7283074466665263\" animation=\"none\" animation_delay=\"0.5\" alt=\"banner\" is_open=\"0\" width=\"100%\" height=\"auto\" image=\"banner-3.jpg\"][/ApImage][/ApColumn][/ApRow][ApRow form_id=\"form_7823738684390903\" id=\"widget_product_list_demo\" class=\"row\" margin_bottom=\"50px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_8255894086686762\" xl=\"4\" lg=\"4\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApProductList form_id=\"form_42094509166273925\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" columns=\"1\" nb_products=\"4\" profile=\"plist1573805184\" use_animation=\"1\" use_showmore=\"1\" active=\"1\" title=\"New product\"][/ApProductList][/ApColumn][ApColumn form_id=\"form_8542280216804634\" xl=\"4\" lg=\"4\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApProductList form_id=\"form_7713724557006556\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"all\" order_way=\"asc\" order_by=\"id_product\" columns=\"1\" nb_products=\"4\" profile=\"plist1573805184\" use_animation=\"1\" use_showmore=\"1\" active=\"1\" title=\"All Product\"][/ApProductList][/ApColumn][ApColumn form_id=\"form_9975348930240445\" xl=\"4\" lg=\"4\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApProductList form_id=\"form_8810060819053422\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"home_featured\" order_way=\"asc\" order_by=\"id_product\" columns=\"1\" nb_products=\"4\" profile=\"plist1573805184\" use_animation=\"1\" use_showmore=\"1\" active=\"1\" title=\"Home featured\"][/ApProductList][/ApColumn][/ApRow][ApRow form_id=\"form_8437167889843195\" class=\"row\" margin_bottom=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1993420269659217\" xl=\"6\" lg=\"6\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_8999951559048493\" xl=\"6\" lg=\"6\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow][ApRow form_id=\"form_8437955337961330\" id=\"widget_manufature_demo\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_954496413585237\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApManuFacturersCarousel form_id=\"form_2338351499273367\" value_by_manufacture=\"0\" manuselect=\"3,4,5,6,7,8,9,10,11\" imagetype=\"manu_default\" order_way=\"asc\" order_by=\"id_manufacturer\" manu_limit=\"10\" carousel_type=\"owlcarousel\" items=\"6\" itemsdesktop=\"5\" itemsdesktopsmall=\"4\" itemstablet=\"3\" itemsmobile=\"3\" itempercolumn=\"1\" autoplay=\"1\" stoponhover=\"1\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"1\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_  dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" active=\"1\"][/ApManuFacturersCarousel][/ApColumn][/ApRow]'),
(8, 1, ''),
(9, 1, '[ApRow form_id=\"form_5429155647202754\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_9449411274493844\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow]'),
(10, 1, '[ApRow form_id=\"form_7964943615243816\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_6433662393198297\" xl=\"6\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_26381044273164315\" xl=\"2\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_8700035159101096\" xl=\"2\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][ApColumn form_id=\"form_9416450636723080\" xl=\"2\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][/ApColumn][/ApRow]'),
(11, 1, ''),
(12, 1, ''),
(13, 1, ''),
(14, 1, ''),
(15, 1, ''),
(16, 1, ''),
(17, 1, ''),
(18, 1, ''),
(19, 1, '[ApRow form_id=\"form_1575989147\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1575990392\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"5\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_1575984273\" name_module=\"ps_contactinfo\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][/ApColumn][ApColumn form_id=\"form_1575980342\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"7\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_1575962999\" name_module=\"ps_shoppingcart\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][ApModule form_id=\"form_1575960356\" name_module=\"ps_customersignin\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(20, 1, '[ApRow form_id=\"form_1575962768\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_14180263359069655\" xl=\"2\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApGenCode form_id=\"form_19979043621160115\" id_gencode=\"id_gencode_5def15ffb20e9_1575949823\" content_html=\"<div class=_APQUOT_logo2_APQUOT_ id=_APQUOT__desktop_logo_APQUOT_>_APENTER_            _APOCBRACKET_if $page.page_name == _APAPOST_index_APAPOST__APCCBRACKET__APENTER_              <h1>_APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_              </h1>_APENTER_            _APOCBRACKET_else_APCCBRACKET__APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_            _APOCBRACKET_/if_APCCBRACKET__APENTER_        </div>\" active=\"1\"][/ApGenCode][/ApColumn][ApColumn form_id=\"form_15056692689723665\" xl=\"10\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApMegamenu form_id=\"form_6563712728685567\" megamenu_group=\"a7f7fb8ed060a0d6b9e0a6dc2a744fca\"][/ApMegamenu][ApModule form_id=\"form_1575983386\" name_module=\"ps_searchbar\" hook=\"displayTop\" is_display=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(21, 1, ''),
(22, 1, ''),
(23, 1, '[ApRow form_id=\"form_4773654450891359\" class=\"row\" padding_top=\"30px\" bg_config=\"fullwidth\" bg_type=\"normal\" bg_color=\"#8dd5bb\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_7543257771413326\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApSlideShow form_id=\"form_6527285977741811\" slideshow_group=\"1a9436d12827c870d10cc0d9b19b2c00\"][/ApSlideShow][/ApColumn][/ApRow][ApRow form_id=\"form_5997226741847663\" id=\"widget_image_category_demo\" container=\"container\" class=\"row\" margin_top=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_9508995733601432\" xl=\"4\" lg=\"4\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApCategoryImage form_id=\"form_6037664710174581\" categorybox=\"3\" category_img=\"_APOCBRACKET__APQUOT_3_APQUOT_:_APQUOT_cate-1.png_APQUOT__APCCBRACKET_\" cate_depth=\"2\" orderby=\"position\" showicons=\"1\" limit=\"5\" id_root=\"2\" id_lang=\"1\" active=\"1\"][/ApCategoryImage][/ApColumn][ApColumn form_id=\"form_7464763343926051\" xl=\"4\" lg=\"4\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApCategoryImage form_id=\"form_5597911976126988\" categorybox=\"6\" category_img=\"_APOCBRACKET__APQUOT_6_APQUOT_:_APQUOT_cate-2.png_APQUOT__APCCBRACKET_\" cate_depth=\"2\" orderby=\"position\" showicons=\"1\" limit=\"5\" id_root=\"2\" id_lang=\"1\" active=\"1\"][/ApCategoryImage][/ApColumn][ApColumn form_id=\"form_19680671459890395\" xl=\"4\" lg=\"4\" md=\"6\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApCategoryImage form_id=\"form_12156373721318365\" categorybox=\"6\" category_img=\"_APOCBRACKET__APQUOT_6_APQUOT_:_APQUOT_cate-3.png_APQUOT__APCCBRACKET_\" cate_depth=\"2\" orderby=\"position\" showicons=\"1\" limit=\"5\" id_root=\"2\" id_lang=\"1\" active=\"1\"][/ApCategoryImage][/ApColumn][/ApRow][ApRow form_id=\"form_14368127203109305\" id=\"widget_360_demo\" container=\"container\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_21824132228761822\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApImage360 form_id=\"form_19927976503617642\" spin=\"drag\" speed=\"50\" mousewheel_step=\"1\" smoothing=\"1\" initialize_on=\"load\" autospin=\"once\" autospin_start=\"load\" autospin_stop=\"hover\" autospin_speed=\"2000\" autospin_direction=\"clockwise\" start_column=\"1\" loop_column=\"1\" reverse_column=\"0\" hint=\"1\" total_slider=\"1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25\" image_360=\"is_main_24.jpg\" image360_1=\"is_main.jpg\" image360_2=\"is_main_1.jpg\" image360_3=\"is_main_2.jpg\" image360_4=\"is_main_3.jpg\" image360_5=\"is_main_4.jpg\" image360_6=\"is_main_5.jpg\" image360_7=\"is_main_6.jpg\" image360_8=\"is_main_7.jpg\" image360_9=\"is_main_8.jpg\" image360_10=\"is_main_9.jpg\" image360_11=\"is_main_10.jpg\" image360_12=\"is_main_11.jpg\" image360_13=\"is_main_12.jpg\" image360_14=\"is_main_13.jpg\" image360_15=\"is_main_14.jpg\" image360_16=\"is_main_15.jpg\" image360_17=\"is_main_16.jpg\" image360_18=\"is_main_17.jpg\" image360_19=\"is_main_18.jpg\" image360_20=\"is_main_19.jpg\" image360_21=\"is_main_20.jpg\" image360_22=\"is_main_21.jpg\" image360_23=\"is_main_22.jpg\" image360_24=\"is_main_23.jpg\" image360_25=\"is_main_24.jpg\" title=\"Image 360\" message=\"Drag image to spin\" message_desktop_hint=\"Drag to spin\" message_mobile_hint=\"Swipe to spin\"][/ApImage360][/ApColumn][/ApRow][ApRow form_id=\"form_8779149021904622\" container=\"container\" class=\"row\" padding_top=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_2400471214664980\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApTabs form_id=\"form_4340514007273008\" class=\"\" tab_type=\"tabs-top\" active_tab=\"1\" fade_effect=\"0\" override_folder=\"\" title=\"\" sub_title=\"\"][ApTab form_id=\"form_7333668169817907\" id=\"tab_31716850884779505\" css_class=\"\" image=\"\" override_folder=\"\" title=\"New Product\" sub_title=\"\"][ApProductCarousel form_id=\"form_6229459505766115\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"1\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"1\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"default\"][/ApProductCarousel][/ApTab][ApTab form_id=\"form_8681159914793934\" id=\"tab_6644982808847248\" css_class=\"\" image=\"\" override_folder=\"\" title=\"Best Seller\" sub_title=\"\"][ApProductCarousel form_id=\"form_2417365443117802\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"all\" order_way=\"desc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"1\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"1\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"default\" active=\"1\"][/ApProductCarousel][/ApTab][ApTab form_id=\"form_11357659526612512\" id=\"tab_25667583197845615\" css_class=\"\" image=\"\" override_folder=\"\" title=\"Home Feature\" sub_title=\"\"][ApProductCarousel form_id=\"form_5016330860603840\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"home_featured\" order_way=\"desc\" order_by=\"name\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"1\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"1\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"default\" active=\"1\"][/ApProductCarousel][/ApTab][/ApTabs][/ApColumn][/ApRow][ApRow form_id=\"form_5202862811765011\" container=\"container\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_3100748228391440\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApManuFacturersCarousel form_id=\"form_5164167081742457\" value_by_manufacture=\"0\" manuselect=\"3,4,5,6,7,8,9,10,11\" imagetype=\"manu_default\" order_way=\"asc\" order_by=\"id_manufacturer\" manu_limit=\"10\" carousel_type=\"owlcarousel\" items=\"6\" itemsdesktop=\"5\" itemsdesktopsmall=\"4\" itemstablet=\"3\" itemsmobile=\"3\" itempercolumn=\"1\" autoplay=\"1\" stoponhover=\"1\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_  dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" active=\"1\" title=\"Manufactures Carousel\"][/ApManuFacturersCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_33277741804384965\" id=\"widget_coundown_demo\" container=\"container\" class=\"row\" padding_top=\"50px\" bg_config=\"fullwidth\" bg_type=\"normal\" bg_color=\"#eaeaea\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_42348154883545415\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApCountdown form_id=\"form_7395226462238854\" class=\"count-down\" time_from=\"2018-03-07 00:00:00\" time_to=\"2022-03-31 00:00:00\" new_tab=\"1\" override_folder=\"count-down\" active=\"1\" title=\"Ap Count Down\" sub_title=\"get <b>50%</b> off your order of $199 or more\" link_label=\"Shop now\" link=\"#\"][/ApCountdown][/ApColumn][/ApRow][ApRow form_id=\"form_9086436004868424\" id=\"widget_block_carousel\" container=\"container\" class=\"row\" padding_top=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_7477904243559653\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockCarousel form_id=\"form_12261146722491855\" class=\"testimonial column-view\" is_open=\"0\" carousel_type=\"owlcarousel\" items=\"2\" itemsdesktop=\"2\" itemsdesktopsmall=\"1\" itemstablet=\"1\" itemsmobile=\"1\" itempercolumn=\"1\" autoplay=\"1\" stoponhover=\"0\" responsive=\"1\" navigation=\"1\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"1\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_  dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" nbitemsperpage=\"12\" interval=\"5000\" total_slider=\"3|2|1\" override_folder=\"testimonial\" tit_3_1=\"<b>Mrs. Mara</b> - Flight attendant\" img_3_1=\"icon-testimonial-2.png\" link_3_1=\"#\" descript_3_1=\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\" tit_2_1=\"<b>Mr. Thanh</b> - Theme Development\" img_2_1=\"icon-testimonial-3.png\" link_2_1=\"#\" descript_2_1=\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\" tit_1_1=\"<b>Mrs. Mara</b> - Flight attendant\" img_1_1=\"icon-testimonial-1.png\" link_1_1=\"#\" descript_1_1=\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\" active=\"1\" title=\"Ap Block Carousel\"][/ApBlockCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_2286779306044819\" id=\"widget_instagram_demo\" container=\"container\" class=\"row\" min_height=\"\" margin_top=\"\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_43272881296891455\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApInstagram form_id=\"form_1574515521\" accordion_type=\"full\" client_id=\"1974f93de9464ceca4c2e48559bc13fa\" access_token=\"6311168720.1974f93.d65a33021c104d6c979debfc89c2ffe3\" user_id=\"6311168720\" sort_by=\"none\" limit=\"12\" resolution=\"thumbnail\" profile_link=\"gallery_prestashop_fr\" carousel_type=\"owlcarousel\" items=\"6\" itemsdesktop=\"6\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" active=\"1\" title=\"Instagram\"][/ApInstagram][/ApColumn][/ApRow][ApRow form_id=\"form_20061413327727668\" id=\"widget_google_map_demo\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1574513083\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApGmap form_id=\"form_8488792657276424\" gkey=\"AIzaSyCWJmaoDNR_l3GVkP6uRnMzsGG5iuuU_AM\" zoom=\"11\" width=\"100%\" height=\"600px\" display_store=\"1\" store=\"1,2,3\" is_display_list=\"0\" stores=\"0\" sitemap=\"0\" active=\"1\"][/ApGmap][/ApColumn][/ApRow]'),
(24, 1, ''),
(25, 1, '[ApRow form_id=\"form_5947363874335610\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_7250986288069211\" xl=\"4\" lg=\"4\" md=\"5\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApHtml form_id=\"form_30267903786466035\" accordion_type=\"accordion_small_screen\" active=\"1\" title=\"html\" content_html=\"<div>Appagbuilder is an exciting UK based independent womenswear brand, founded by NAHN in 2018. Appagbuilder has seen extraordinary growth since it’s inception – now selling worldwide.</div>\"][/ApHtml][/ApColumn][ApColumn form_id=\"form_6887537115695043\" xl=\"2\" lg=\"2\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_2114172248822944\" total_link=\"4\" list_id_link=\"1,2,3,4,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"New products\" target_type_1=\"_self\" link_type_1=\"link-category\" cmspage_id_1=\"1\" category_id_1=\"2\" manufacture_id_1=\"3\" page_id_1=\"address\" link_title_2_1=\"Best sales\" target_type_2=\"_self\" link_type_2=\"link-category\" cmspage_id_2=\"1\" category_id_2=\"2\" manufacture_id_2=\"3\" page_id_2=\"address\" link_title_3_1=\"Prices drop\" target_type_3=\"_self\" link_type_3=\"link-page\" cmspage_id_3=\"1\" category_id_3=\"2\" manufacture_id_3=\"3\" page_id_3=\"pricesdrop\" link_title_4_1=\"Contact us\" target_type_4=\"_self\" link_type_4=\"link-page\" cmspage_id_4=\"1\" category_id_4=\"2\" manufacture_id_4=\"3\" page_id_4=\"contact\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"3\" page_id=\"address\" active=\"1\" title=\"BLOCK LINKS\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_1508294448134297\" xl=\"2\" lg=\"2\" md=\"4\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_27413901108726315\" total_link=\"4\" list_id_link=\"1,2,3,4,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"Personal info\" target_type_1=\"_self\" link_type_1=\"link-page\" cmspage_id_1=\"1\" category_id_1=\"2\" manufacture_id_1=\"3\" page_id_1=\"my-account\" link_title_2_1=\"Orders\" target_type_2=\"_self\" link_type_2=\"link-page\" cmspage_id_2=\"1\" category_id_2=\"2\" manufacture_id_2=\"3\" page_id_2=\"order\" link_title_3_1=\"Credit slips\" target_type_3=\"_self\" link_type_3=\"link-page\" cmspage_id_3=\"1\" category_id_3=\"2\" manufacture_id_3=\"3\" page_id_3=\"order-slip\" link_title_4_1=\"Addresses\" target_type_4=\"_self\" link_type_4=\"link-page\" cmspage_id_4=\"1\" category_id_4=\"2\" manufacture_id_4=\"3\" page_id_4=\"address\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"3\" page_id=\"address\" active=\"1\" title=\"YOUR ACCOUNT\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_8363977578804733\" id=\"widget_facebook_demo\" xl=\"4\" lg=\"4\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApFacebook form_id=\"form_9322634979572840\" page_url=\"https://www.facebook.com/leotheme\" target=\"light\" height=\"60px\" tabs=\"timeline\" hide_cover=\"0\" show_facepile=\"1\" hide_cta=\"0\" small_header=\"0\" adapt_container_width=\"1\" active=\"1\" title=\"FACEBOOK\"][/ApFacebook][ApModule form_id=\"form_4323638678247519\" name_module=\"ps_socialfollow\" hook=\"displayFooter\" is_display=\"1\" active=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(26, 1, ''),
(27, 1, ''),
(28, 1, ''),
(29, 1, ''),
(30, 1, ''),
(31, 1, ''),
(32, 1, ''),
(33, 1, ''),
(34, 1, ''),
(35, 1, '[ApRow form_id=\"form_1575988939\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1575969672\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"5\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_1575972353\" name_module=\"ps_contactinfo\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][/ApColumn][ApColumn form_id=\"form_1575993734\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"7\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_9456135902001488\" name_module=\"ps_languageselector\" is_display=\"1\"][/ApModule][ApModule form_id=\"form_1575983796\" name_module=\"ps_customersignin\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(36, 1, '[ApRow form_id=\"form_1575965629\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_5045180959288269\" class=\"header-logo\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApGenCode form_id=\"form_6032947010629811\" id_gencode=\"id_gencode_5def15ffb20e9_1575949823\" content_html=\"<div class=_APQUOT_logo3_APQUOT_ id=_APQUOT__desktop_logo_APQUOT_>_APENTER_            _APOCBRACKET_if $page.page_name == _APAPOST_index_APAPOST__APCCBRACKET__APENTER_              <h1>_APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_              </h1>_APENTER_            _APOCBRACKET_else_APCCBRACKET__APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_            _APOCBRACKET_/if_APCCBRACKET__APENTER_        </div>\" active=\"1\"][/ApGenCode][ApModule form_id=\"form_1575986922\" name_module=\"ps_searchbar\" hook=\"displayTop\" is_display=\"1\"][/ApModule][/ApColumn][/ApRow][ApRow form_id=\"form_17883894411352738\" class=\"row menu-h3\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_7525998560049925\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"0\" controller_pages=\"\" controller_id=\"\"][ApGenCode form_id=\"form_13583411226796015\" id_gencode=\"id_gencode_5def188e19001_1575950478\" content_html=\"<div class=_APQUOT_hidden-md-up text-sm-center mobile_APQUOT_>_APENTER_          <div class=_APQUOT_float-xs-left_APQUOT_ id=_APQUOT_menu-icon_APQUOT_>_APENTER_            <i class=_APQUOT_material-icons d-inline_APQUOT_>_APAMP_#xE5D2;</i>_APENTER_          </div>_APENTER_          <div class=_APQUOT_float-xs-right_APQUOT_ id=_APQUOT__mobile_cart_APQUOT_></div>_APENTER_          <div class=_APQUOT_float-xs-right_APQUOT_ id=_APQUOT__mobile_user_info_APQUOT_></div>_APENTER_          <div class=_APQUOT_top-logo_APQUOT_ id=_APQUOT__mobile_logo_APQUOT_></div>_APENTER_          <div class=_APQUOT_clearfix_APQUOT_></div>_APENTER_        </div>\" active=\"1\"][/ApGenCode][ApGenCode form_id=\"form_1213316999784758\" id_gencode=\"id_gencode_5def17fc3fde7_1575950332\" content_html=\"<div id=_APQUOT_mobile_top_menu_wrapper_APQUOT_ class=_APQUOT_row hidden-md-up_APQUOT_ style=_APQUOT_display:none;_APQUOT_>_APENTER_        <div class=_APQUOT_js-top-menu mobile_APQUOT_ id=_APQUOT__mobile_top_menu_APQUOT_></div>_APENTER_        <div class=_APQUOT_js-top-menu-bottom_APQUOT_>_APENTER_          <div id=_APQUOT__mobile_currency_selector_APQUOT_></div>_APENTER_          <div id=_APQUOT__mobile_language_selector_APQUOT_></div>_APENTER_          <div id=_APQUOT__mobile_contact_link_APQUOT_></div>_APENTER_        </div>_APENTER_      </div>\" active=\"1\"][/ApGenCode][/ApColumn][ApColumn form_id=\"form_7571441693000340\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApMegamenu form_id=\"form_6052836394002201\" megamenu_group=\"a7f7fb8ed060a0d6b9e0a6dc2a744fca\" active=\"1\"][/ApMegamenu][ApModule form_id=\"form_4591460040801075\" name_module=\"ps_shoppingcart\" hook=\"displayTop\" is_display=\"1\" active=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(37, 1, ''),
(38, 1, '');
INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_lang` (`id_appagebuilder`, `id_lang`, `params`) VALUES
(39, 1, '[ApRow form_id=\"form_7329041331187141\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_7409167617308913\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApSlideShow form_id=\"form_7669488393204372\" slideshow_group=\"1a9436d12827c870d10cc0d9b19b2c00\"][/ApSlideShow][/ApColumn][/ApRow][ApRow form_id=\"form_9942998478444192\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_4819620634851423\" xl=\"9\" lg=\"9\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_23065033376659495\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"all\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"3\" itemsdesktop=\"3\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"1\" itempercolumn=\"2\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"default\" active=\"1\" title=\"ALL PRODUCT\"][/ApProductCarousel][/ApColumn][ApColumn form_id=\"form_7863268556108546\" xl=\"3\" lg=\"3\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApImage form_id=\"form_16701167514932335\" animation=\"none\" animation_delay=\"0.5\" alt=\"banner\" is_open=\"0\" width=\"100%\" height=\"auto\" active=\"1\" title=\"Banner Image\" image=\"banner_2.jpg\"][/ApImage][ApProductCarousel form_id=\"form_7935608383902590\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"1\" itemsdesktop=\"1\" itemsdesktopsmall=\"1\" itemstablet=\"1\" itemsmobile=\"1\" itempercolumn=\"4\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1573805184\" title=\"NEW PRODUCT\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_1923865936401458\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_709221701171476\" id=\"widget_hostpot_demo\" class=\"\" xl=\"9\" lg=\"9\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApImageHotspot form_id=\"form_7516543904586766\" alt=\"jean\" is_open=\"0\" width=\"100%\" height=\"auto\" total_slider=\"3|2|1\" temp_image_3_1=\"1.jpg\" temp_description_3_1=\"<a class=_APQUOT_title_APQUOT_ href=_APQUOT_#_APQUOT_>Sayer sneaker</a> <br>_APENTER_$300.00\" temp_top_3=\"82\" temp_left_3=\"14\" temp_location_3=\"top-right\" temp_textalign_3=\"center\" temp_trigger_3=\"hoverable\" temp_opacity_3=\"1\" temp_width_3=\"160px\" temp_margin_3=\"0\" temp_padding_3=\"5px\" temp_imagealign_3=\"left\" temp_image_2_1=\"2.jpg\" temp_description_2_1=\"<a class=_APQUOT_title_APQUOT_ href=_APQUOT_#_APQUOT_>Slim fit chino</a> <br>_APENTER_$22.00\" temp_top_2=\"71\" temp_left_2=\"45\" temp_location_2=\"top-right\" temp_textalign_2=\"center\" temp_trigger_2=\"hoverable\" temp_opacity_2=\"1\" temp_width_2=\"160px\" temp_margin_2=\"0\" temp_padding_2=\"5px\" temp_imagealign_2=\"left\" temp_image_1_1=\"3.jpg\" temp_description_1_1=\"<a class=_APQUOT_title_APQUOT_ href=_APQUOT_#_APQUOT_>Slim fit polo van</a> <br>_APENTER_$229.00\" temp_top_1=\"16\" temp_left_1=\"34\" temp_location_1=\"bottom\" temp_textalign_1=\"center\" temp_trigger_1=\"hoverable\" temp_opacity_1=\"1\" temp_width_1=\"160px\" temp_margin_1=\"0\" temp_padding_1=\"5px\" temp_imagealign_1=\"left\" active=\"1\" title=\"Image Hotspot\" image=\"banner_hotspot.jpg\" url=\"#\"][/ApImageHotspot][/ApColumn][ApColumn form_id=\"form_6321553372583825\" xl=\"3\" lg=\"3\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApTwitter form_id=\"form_6271080214318957\" accordion_type=\"full\" twidget_id=\"578806287158251521\" count=\"1\" username=\"prestashop\" border_color=\"#000\" link_color=\"#000\" text_color=\"#000\" name_color=\"#000\" mail_color=\"#000\" width=\"262\" height=\"494\" transparent=\"1\" show_replies=\"1\" show_header=\"1\" show_footer=\"1\" show_border=\"1\" show_scrollbar=\"1\" active=\"1\" title=\"Twitter\"][/ApTwitter][/ApColumn][/ApRow][ApRow form_id=\"form_8292848178791468\" id=\"widget_accordions\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_12596941852958068\" id=\"accordions\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApAccordions form_id=\"form_18542129836843275\" active_type=\"hideall\" active_accordion=\"1\" active=\"1\" title=\"ACCORDIONS\"][ApAccordion form_id=\"form_17498723053509888\" id=\"collapse_36659499943977285\" parent_id=\"accordion_9671892441020728\" active=\"1\" title=\"Accordion 1\"][ApHtml form_id=\"form_5591011388455949\" accordion_type=\"full\" content_html=\"<p>Mauris in erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet nisi sed ut imperdiet nisi..</p>\"][/ApHtml][/ApAccordion][ApAccordion form_id=\"form_15155408489846168\" parent_id=\"accordion_9671892441020728\" id=\"collapse_9763303073669280\" title=\"Accordion 2\"][ApHtml form_id=\"form_6439690831444843\" accordion_type=\"full\" content_html=\"<p>Mauris in erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet nisi sed ut imperdiet nisi..</p>\"][/ApHtml][/ApAccordion][/ApAccordions][/ApColumn][/ApRow][ApRow form_id=\"form_7334806451461785\" id=\"widget_alert\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_9748498353925220\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApAlert form_id=\"form_39439686026128055\" alert_type=\"alert-success\" active=\"1\" title=\"ALERT SUCCESSS\" content_html=\"<p>Mauris in erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet nisi sed ut imperdiet nisi..</p>\"][/ApAlert][ApAlert form_id=\"form_4768234595108320\" alert_type=\"alert-info\" active=\"1\" title=\"ALERT INFO\" content_html=\"<p>Mauris in erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet nisi sed ut imperdiet nisi..</p>\"][/ApAlert][ApAlert form_id=\"form_39130250486140115\" alert_type=\"alert-warning\" active=\"1\" title=\"ALERT WARNING\" content_html=\"<p>Mauris in erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet nisi sed ut imperdiet nisi..</p>\"][/ApAlert][ApAlert form_id=\"form_3539762164083305\" alert_type=\"alert-danger\" active=\"1\" title=\"ALERT DANGER\" content_html=\"<p>Mauris in erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet erat justo nullam ac urna eu felis ut imperdiet nisi eu felis dapibus condimentum sit amet a sed ut imperdiet nisi sed ut imperdiet nisi..</p>\"][/ApAlert][/ApColumn][/ApRow][ApRow form_id=\"form_6300216649977450\" id=\"widget_button_demo\" class=\"row title-center\" margin_top=\"30px\" margin_bottom=\"30px\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\" title=\"Widget Button\"][ApColumn form_id=\"form_7316229138562265\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_8629020777313513\" use_outline_button=\"no\" button_type=\"btn-primary\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" active=\"1\" name=\"Button Primary\" url=\"#\"][/ApButton][/ApColumn][ApColumn form_id=\"form_38779494558919725\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_949215806469186\" use_outline_button=\"no\" button_type=\"btn-secondary\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" name=\"Button Secondary\" url=\"#\"][/ApButton][/ApColumn][ApColumn form_id=\"form_9764293874874776\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_8544608668883269\" use_outline_button=\"no\" button_type=\"btn-success\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" name=\"Button Success\" url=\"#\"][/ApButton][/ApColumn][ApColumn form_id=\"form_412527037740403\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_4651312742252121\" use_outline_button=\"no\" button_type=\"btn-info\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" name=\"Button Info \" url=\"#\"][/ApButton][/ApColumn][ApColumn form_id=\"form_8333580481461117\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_5838532475554427\" use_outline_button=\"no\" button_type=\"btn-warning\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" name=\"Button Warning\" url=\"#\"][/ApButton][/ApColumn][ApColumn form_id=\"form_8232629400444439\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_10064983499322402\" use_outline_button=\"no\" button_type=\"btn-danger\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" name=\"Button Danger\" url=\"#\"][/ApButton][/ApColumn][ApColumn form_id=\"form_7404815519635035\" xl=\"3\" lg=\"4\" md=\"4\" sm=\"6\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApButton form_id=\"form_3490213315125594\" use_outline_button=\"no\" button_type=\"btn-link\" outline_button_type=\"btn-outline-primary\" button_size=\"btn-lg\" is_block=\"0\" is_blank=\"0\" name=\"Button Link\" url=\"#\"][/ApButton][/ApColumn][/ApRow][ApRow form_id=\"form_6774338548772507\" id=\"widget_blog_demo\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_5058735458914049\" class=\"title-center\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlog form_id=\"form_5321476075520832\" chk_cat=\"5577844800e55bda2c0540af22ba96ce,0abc8c406b64fa2f13f5a7cbecbfb67f,1dcae6f22c5962b687451c98c27946f0\" bleoblogs_width=\"690\" bleoblogs_height=\"300\" bleoblogs_show=\"0\" show_title=\"1\" show_desc=\"0\" bleoblogs_sima=\"1\" bleoblogs_saut=\"1\" bleoblogs_scat=\"0\" bleoblogs_scre=\"1\" bleoblogs_scoun=\"0\" bleoblogs_shits=\"0\" order_way=\"random\" order_by=\"id_leoblog_blog\" nb_blogs=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"1\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_  dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" active=\"1\" title=\"BLOG\"][/ApBlog][/ApColumn][/ApRow]'),
(40, 1, ''),
(41, 1, ''),
(42, 1, '[ApRow form_id=\"form_1576002072\" container=\"container\" class=\"leo-footer-center \" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1575982269\" xl=\"2-4\" lg=\"2-4\" md=\"4\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_1576004350\" total_link=\"5\" list_id_link=\"1,2,3,4,5,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,target_type_5,link_type_5,cmspage_id_5,category_id_5,product_id_5,manufacture_id_5,page_id_5,page_param_5,supplier_id_5,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,link_title_5,link_url_5,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"About Us\" link_title_1_2=\"À propos\" link_title_1_3=\"Über uns\" link_title_1_4=\"Chi siamo\" link_title_1_5=\"Sobre nosotros\" link_title_1_6=\"من نحن\" target_type_1=\"_self\" link_type_1=\"link-cms\" link_url_1_1=\"#\" cmspage_id_1=\"4\" category_id_1=\"3\" manufacture_id_1=\"1\" page_id_1=\"address\" link_title_2_1=\"Contact US\" link_title_2_2=\"Contactez nous\" link_title_2_3=\"Kontakt US\" link_title_2_4=\"Contattaci\" link_title_2_5=\"Contáctenos\" link_title_2_6=\"اتصل بنا\" target_type_2=\"_self\" link_type_2=\"link-page\" cmspage_id_2=\"1\" category_id_2=\"2\" manufacture_id_2=\"1\" page_id_2=\"contact\" link_title_3_1=\"Terms _APAMP_ Conditions\" link_title_3_2=\"Termes et conditions\" link_title_3_3=\"Terms _APAMP_ Bedingungen\" link_title_3_4=\"Termini _APAMP_ Condizioni\" link_title_3_5=\"Términos y condiciones\" link_title_3_6=\"البنود و الظروف\" target_type_3=\"_self\" link_type_3=\"link-cms\" cmspage_id_3=\"3\" category_id_3=\"2\" manufacture_id_3=\"1\" page_id_3=\"bestsales\" link_title_4_1=\"Faq\" link_title_4_2=\"Faq\" link_title_4_3=\"Faq\" link_title_4_4=\"Faq\" link_title_4_5=\"Faq\" link_title_4_6=\"Faq\" target_type_4=\"_self\" link_type_4=\"link-url\" link_url_4_1=\"#\" link_url_4_2=\"#\" link_url_4_3=\"#\" link_url_4_4=\"#\" link_url_4_5=\"#\" link_url_4_6=\"#\" cmspage_id_4=\"1\" category_id_4=\"2\" manufacture_id_4=\"1\" page_id_4=\"address\" link_title_5_1=\"Delivery\" link_title_5_2=\"Livraison\" link_title_5_3=\"Lieferung\" link_title_5_4=\"Consegna\" link_title_5_5=\"Entrega\" link_title_5_6=\"توصيل\" target_type_5=\"_self\" link_type_5=\"link-cms\" link_url_5_1=\"#\" link_url_5_2=\"#\" link_url_5_3=\"#\" link_url_5_4=\"#\" link_url_5_5=\"#\" link_url_5_6=\"#\" cmspage_id_5=\"1\" category_id_5=\"2\" manufacture_id_5=\"1\" page_id_5=\"address\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"1\" page_id=\"address\" active=\"1\" title=\"Information\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_1575973547\" xl=\"2-4\" lg=\"2-4\" md=\"4\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_1575972180\" total_link=\"6\" list_id_link=\"1,3,4,5,6,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,target_type_5,link_type_5,cmspage_id_5,category_id_5,product_id_5,manufacture_id_5,page_id_5,page_param_5,supplier_id_5,target_type_6,link_type_6,cmspage_id_6,category_id_6,product_id_6,manufacture_id_6,page_id_6,page_param_6,supplier_id_6,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,link_title_5,link_url_5,link_title_6,link_url_6,link_title_7,link_url_7,link_title_8,link_url_8,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"Home\" link_title_1_2=\"Accueil\" link_title_1_3=\"Zuhause\" link_title_1_4=\"Casa\" link_title_1_5=\"Hogar\" link_title_1_6=\"لبيتي\" target_type_1=\"_self\" link_type_1=\"link-page\" cmspage_id_1=\"1\" category_id_1=\"2\" manufacture_id_1=\"1\" page_id_1=\"index\" link_title_3_1=\"Privacy\" link_title_3_2=\"Intimité\" link_title_3_3=\"Privatleben\" link_title_3_4=\"Intimità\" link_title_3_5=\"Intimidad\" link_title_3_6=\"الإجمالية\" target_type_3=\"_self\" link_type_3=\"link-cms\" cmspage_id_3=\"7\" category_id_3=\"2\" manufacture_id_3=\"1\" page_id_3=\"discount\" link_title_4_1=\"Address\" link_title_4_2=\"Adresser\" link_title_4_3=\"Anschrift\" link_title_4_4=\"Indirizzo\" link_title_4_5=\"Dirección\" link_title_4_6=\"عنوان\" target_type_4=\"_self\" link_type_4=\"link-page\" link_url_4_1=\"#\" link_url_4_2=\"#\" link_url_4_3=\"#\" link_url_4_4=\"#\" link_url_4_5=\"#\" link_url_4_6=\"#\" cmspage_id_4=\"4\" category_id_4=\"2\" manufacture_id_4=\"1\" page_id_4=\"address\" link_title_5_1=\"History\" link_title_5_2=\"Histoire\" link_title_5_3=\"Geschichte\" link_title_5_4=\"Storia\" link_title_5_5=\"Historia\" link_title_5_6=\"التاريخ\" target_type_5=\"_self\" link_type_5=\"link-page\" cmspage_id_5=\"5\" category_id_5=\"2\" manufacture_id_5=\"1\" page_id_5=\"history\" link_title_6_1=\"Identity\" link_title_6_2=\"Identité\" link_title_6_3=\"Identität\" link_title_6_4=\"Identità\" link_title_6_5=\"Identidad\" link_title_6_6=\"هوية\" target_type_6=\"_self\" link_type_6=\"link-page\" cmspage_id_6=\"1\" category_id_6=\"2\" manufacture_id_6=\"1\" page_id_6=\"identity\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"1\" page_id=\"address\" active=\"1\" title=\"Quick Link\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_1575986505\" xl=\"2-4\" lg=\"2-4\" md=\"4\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_1575984794\" total_link=\"5\" list_id_link=\"1,2,3,4,5,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,target_type_5,link_type_5,cmspage_id_5,category_id_5,product_id_5,manufacture_id_5,page_id_5,page_param_5,supplier_id_5,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,link_title_5,link_url_5,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"Search\" link_title_1_2=\"Chercher\" link_title_1_3=\"Suche\" link_title_1_4=\"Ricerca\" link_title_1_5=\"Buscar\" link_title_1_6=\"بحث\" target_type_1=\"_self\" link_type_1=\"link-page\" cmspage_id_1=\"1\" category_id_1=\"2\" manufacture_id_1=\"2\" page_id_1=\"search\" link_title_2_1=\"About\" link_title_2_2=\"Sur\" link_title_2_3=\"Über\" link_title_2_4=\"Di\" link_title_2_5=\"Sobre\" link_title_2_6=\"حول\" target_type_2=\"_self\" link_type_2=\"link-cms\" cmspage_id_2=\"4\" category_id_2=\"2\" manufacture_id_2=\"2\" page_id_2=\"address\" link_title_3_1=\"Contact\" link_title_3_2=\"Contactez\" link_title_3_3=\"Kontakt\" link_title_3_4=\"Contatto\" link_title_3_5=\"Contacto\" link_title_3_6=\"اتصل\" target_type_3=\"_self\" link_type_3=\"link-page\" cmspage_id_3=\"1\" category_id_3=\"2\" manufacture_id_3=\"2\" page_id_3=\"contact\" link_title_4_1=\"Blog\" link_title_4_2=\"Blog\" link_title_4_3=\"Blog\" link_title_4_4=\"Blog\" link_title_4_5=\"Blog\" link_title_4_6=\"مدونة\" target_type_4=\"_self\" link_type_4=\"link-url\" link_url_4_1=\"#\" link_url_4_2=\"#\" link_url_4_3=\"#\" link_url_4_4=\"#\" link_url_4_5=\"#\" link_url_4_6=\"#\" cmspage_id_4=\"1\" category_id_4=\"2\" manufacture_id_4=\"2\" page_id_4=\"bestsales\" link_title_5_1=\"Stores\" link_title_5_2=\"Magasins\" link_title_5_3=\"Shops\" link_title_5_4=\"I negozi\" link_title_5_5=\"Víveres\" link_title_5_6=\"مخازن\" target_type_5=\"_self\" link_type_5=\"link-page\" cmspage_id_5=\"1\" category_id_5=\"2\" manufacture_id_5=\"2\" page_id_5=\"stores\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"2\" page_id=\"address\" active=\"1\" title=\"Service\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_5934567299870418\" class=\"mobile_app\" xl=\"4-8\" lg=\"4-8\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApRawHtml form_id=\"form_1575972944\" active=\"1\" title=\"Get Mobile App\" content_html=\"<div class=_APQUOT_footer-app_APQUOT_>_APENTER_<a href=_APQUOT_#_APQUOT_ ><img class=_APQUOT_img-fluid_APQUOT_ src=_APQUOT__AP_IMG_DIR/ap-play.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_<a  href=_APQUOT_#_APQUOT_><img class=_APQUOT_img-fluid_APQUOT_ src=_APQUOT__AP_IMG_DIR/ap-store.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_</div>\"][/ApRawHtml][ApRawHtml form_id=\"form_1575989909\" active=\"1\" title=\"Payment\" content_html=\"<div class=_APQUOT_payment_APQUOT_>_APENTER_<a href=_APQUOT_#_APQUOT_ class=_APQUOT_image_APQUOT_><img src=_APQUOT__AP_IMG_DIR/payment-1.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_<a  href=_APQUOT_#_APQUOT_ class=_APQUOT_image_APQUOT_><img src=_APQUOT__AP_IMG_DIR/payment-2.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_<a href=_APQUOT_#_APQUOT_ class=_APQUOT_image_APQUOT_><img src=_APQUOT__AP_IMG_DIR/payment-3.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_<a  href=_APQUOT_#_APQUOT_ class=_APQUOT_image_APQUOT_><img src=_APQUOT__AP_IMG_DIR/payment-4.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_<a  href=_APQUOT_#_APQUOT_ class=_APQUOT_image_APQUOT_><img src=_APQUOT__AP_IMG_DIR/payment-5.jpg_APQUOT_ alt=_APQUOT__APQUOT_ /></a>_APENTER_</div>\"][/ApRawHtml][/ApColumn][/ApRow]'),
(43, 1, ''),
(44, 1, ''),
(45, 1, ''),
(46, 1, '[ApRow form_id=\"form_2651446950\" container=\"container\" class=\"row\" bg_config=\"boxed\" bg_type=\"normal\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_3373811259\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"5\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_1765023432\" name_module=\"ps_contactinfo\" hook=\"displayNav2\" is_display=\"1\" active=\"1\"][/ApModule][/ApColumn][ApColumn form_id=\"form_2306086964\" sm=\"6\" xs=\"6\" sp=\"6\" md=\"6\" lg=\"6\" xl=\"7\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_3596292037\" name_module=\"ps_shoppingcart\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][ApModule form_id=\"form_2938833997\" name_module=\"ps_customersignin\" hook=\"displayNav2\" is_display=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(47, 1, '[ApRow form_id=\"form_3277497040\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_1756625215\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApGenCode form_id=\"form_2053174386\" id_gencode=\"id_gencode_5def15ffb20e9_1575949823\" content_html=\"<div class=_APQUOT_logo1_APQUOT_ id=_APQUOT__desktop_logo_APQUOT_>_APENTER_            _APOCBRACKET_if $page.page_name == _APAPOST_index_APAPOST__APCCBRACKET__APENTER_              <h1>_APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_              </h1>_APENTER_            _APOCBRACKET_else_APCCBRACKET__APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_            _APOCBRACKET_/if_APCCBRACKET__APENTER_        </div>\" active=\"1\"][/ApGenCode][/ApColumn][/ApRow][ApRow form_id=\"form_1737684657\" container=\"container\" class=\"row\" bg_config=\"fullwidth\" bg_type=\"normal\" bg_color=\"#2fb5d2\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_2548400419\" id=\"widget_megamenu_demo\" xl=\"8\" lg=\"8\" md=\"4\" sm=\"4\" xs=\"4\" sp=\"4\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApMegamenu form_id=\"form_2554282457\" megamenu_group=\"a7f7fb8ed060a0d6b9e0a6dc2a744fca\"][/ApMegamenu][/ApColumn][ApColumn form_id=\"form_3164015092\" class=\"box_phone\" xl=\"4\" lg=\"4\" md=\"8\" sm=\"8\" xs=\"8\" sp=\"8\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApHtml form_id=\"form_2896308504\" accordion_type=\"full\" active=\"1\" content_html=\"<div class=_APQUOT_phone_APQUOT_><span class=_APQUOT_text_title_APQUOT_>Hotline:</span> <span class=_APQUOT_number_APQUOT_>123-456-789</span></div>\"][/ApHtml][/ApColumn][/ApRow]'),
(48, 1, ''),
(49, 1, '');
INSERT INTO `" . _DB_PREFIX_ . "appagebuilder_lang` (`id_appagebuilder`, `id_lang`, `params`) VALUES
(50, 1, '[ApRow form_id=\"form_5434246471152652\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_2950283089140644\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_44088604858164115\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578320989\" title=\"Product list type 1\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_9815254952137266\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_14982503651221598\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_6788615855417797\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578323366\" active=\"1\" title=\"Product list type 2\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_5940309516490198\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_8970716971618842\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_6447289602734867\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578303419\" active=\"1\" title=\"Product list type 3\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_8510298725452336\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_8591731758033259\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_4587650191457826\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578317996\" active=\"1\" title=\"Product list type 4\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_42181197827589115\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_5187000168487005\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_20713482932077152\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578329417\" active=\"1\" title=\"Product list type 5\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_8646868633355228\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_51189592447420294\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_4834321381785066\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578318370\" active=\"1\" title=\"Product list type 6\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_2130824081636663\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_5848089537583785\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_9331349061068360\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578301575\" active=\"1\" title=\"Product list type 7\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_4873304335103161\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_9668632715747066\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_6467330523131616\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578323848\" active=\"1\" title=\"Product list type 8\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_14808080933304835\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_35018995750947705\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_4970372376883826\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578313973\" active=\"1\" title=\"Product list type 9\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_5024119229109825\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_5853480599812013\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_21017853711770535\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578332766\" active=\"1\" title=\"Product list type 10\"][/ApProductCarousel][/ApColumn][/ApRow][ApRow form_id=\"form_6450422775269838\" id=\"\" container=\"\" class=\"row\" min_height=\"\" margin_top=\"100px\" margin_bottom=\"\" padding_top=\"\" padding_bottom=\"\" bg_config=\"boxed\" bg_type=\"normal\" bg_size=\"\" bg_color=\"\" bg_img=\"\" bg_position=\"\" bg_repeat=\"no-repeat\" parallax_speed=\"0.1\" parallax_axis=\"both\" parallax_strength=\"0.5\" parallax_rid=\"0.5\" parallax_hoffsets=\"0.1\" parallax_voffsets=\"0.1\" video_link=\"\" video_id=\"\" specific_type=\"all\" controller_id=\"\" controller_pages=\"\" active=\"1\" title=\"\" sub_title=\"\"][ApColumn form_id=\"form_6412206451569814\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApProductCarousel form_id=\"form_9117879519800640\" value_by_product_type=\"1\" category_type=\"all\" product_type=\"new_product\" order_way=\"asc\" order_by=\"id_product\" nb_products=\"10\" carousel_type=\"owlcarousel\" items=\"4\" itemsdesktop=\"4\" itemsdesktopsmall=\"3\" itemstablet=\"2\" itemsmobile=\"2\" itempercolumn=\"1\" autoplay=\"0\" stoponhover=\"0\" responsive=\"1\" navigation=\"0\" autoheight=\"0\" mousedrag=\"1\" touchdrag=\"1\" lazyload=\"0\" lazyfollow=\"0\" lazyeffect=\"fade\" pagination=\"0\" paginationnumbers=\"0\" scrollperpage=\"0\" paginationspeed=\"800\" slidespeed=\"200\" nbitemsperpage=\"12\" interval=\"5000\" slick_vertical=\"0\" slick_autoplay=\"1\" slick_pauseonhover=\"1\" slick_loopinfinite=\"0\" slick_arrows=\"1\" slick_dot=\"0\" slick_centermode=\"0\" slick_centerpadding=\"60\" slick_row=\"1\" slick_slidestoshow=\"5\" slick_slidestoscroll=\"1\" slick_items_custom=\"_APOBRACKET__APOBRACKET_1200, 6_APCBRACKET_,_APOBRACKET_992, 5_APCBRACKET_,_APOBRACKET_768, 4_APCBRACKET_, _APOBRACKET_576, 3_APCBRACKET_,_APOBRACKET_480, 2_APCBRACKET__APCBRACKET_\" slick_custom_status=\"0\" slick_custom=\"_APOCBRACKET__APENTER_    dots: true,_APENTER_  infinite: false,_APENTER_  speed: 300,_APENTER_  slidesToShow: 4,_APENTER_  slidesToScroll: 4,_APENTER_  responsive: _APOBRACKET__APENTER_    _APOCBRACKET__APENTER_      breakpoint: 1024,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 3,_APENTER_        slidesToScroll: 3,_APENTER_        infinite: true,_APENTER_        dots: true_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 600,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 2,_APENTER_        slidesToScroll: 2_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET_,_APENTER_    _APOCBRACKET__APENTER_      breakpoint: 480,_APENTER_      settings: _APOCBRACKET__APENTER_        slidesToShow: 1,_APENTER_        slidesToScroll: 1_APENTER_      _APCCBRACKET__APENTER_    _APCCBRACKET__APENTER_  _APCBRACKET__APENTER__APCCBRACKET_\" profile=\"plist1578316307\" active=\"1\" title=\"Product list type 11\"][/ApProductCarousel][/ApColumn][/ApRow]'),
(51, 1, ''),
(52, 1, '[ApRow form_id=\"form_2569187007\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_3550485382\" xl=\"12\" lg=\"12\" md=\"12\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApModule form_id=\"form_2283198203\" name_module=\"ps_emailsubscription\" hook=\"displayFooterBefore\" is_display=\"1\"][/ApModule][/ApColumn][/ApRow]'),
(53, 1, '[ApRow form_id=\"form_1792218608\" class=\"row\" specific_type=\"\" controller_pages=\"\" controller_id=\"\"][ApColumn form_id=\"form_3011428857\" xl=\"6\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApGenCode form_id=\"form_1806356868\" id_gencode=\"id_gencode_5e1d8677b3f07_1578993271\" content_html=\"<div class=_APQUOT_logo1_APQUOT_ id=_APQUOT_footer_desktop_logo_APQUOT_>_APENTER_            _APOCBRACKET_if $page.page_name == _APAPOST_index_APAPOST__APCCBRACKET__APENTER_              <h1>_APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_              </h1>_APENTER_            _APOCBRACKET_else_APCCBRACKET__APENTER_                <a href=_APQUOT__APOCBRACKET_$urls.base_url_APCCBRACKET__APQUOT_>_APENTER_                  <img class=_APQUOT_lazy logo img-responsive_APQUOT_ data-src=_APQUOT__APOCBRACKET_$shop.logo_APCCBRACKET__APQUOT_ alt=_APQUOT__APOCBRACKET_$shop.name_APCCBRACKET__APQUOT_>_APENTER_                </a>_APENTER_            _APOCBRACKET_/if_APCCBRACKET__APENTER_        </div>\" active=\"1\"][/ApGenCode][ApHtml form_id=\"form_2672259775\" accordion_type=\"full\" content_html=\"<div>Appagbuilder is an exciting UK based independent womenswear brand, founded by NAHN in 2018.</div>\"][/ApHtml][ApProductTag form_id=\"form_3057567518\" accordion_type=\"full\" random_display=\"0\" active=\"1\" sub_title=\"Product Tags:\" displayed_tags=\"10\" tag_levels=\"10\"][/ApProductTag][/ApColumn][ApColumn form_id=\"form_3398059139\" xl=\"2\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_2473580534\" total_link=\"5\" list_id_link=\"1,2,3,4,5,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,target_type_5,link_type_5,cmspage_id_5,category_id_5,product_id_5,manufacture_id_5,page_id_5,page_param_5,supplier_id_5,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,link_title_5,link_url_5,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"About us\" target_type_1=\"_self\" link_type_1=\"link-cms\" cmspage_id_1=\"4\" category_id_1=\"2\" manufacture_id_1=\"3\" page_id_1=\"address\" link_title_2_1=\"Contact\" target_type_2=\"_self\" link_type_2=\"link-page\" cmspage_id_2=\"1\" category_id_2=\"2\" manufacture_id_2=\"3\" page_id_2=\"contact\" link_title_3_1=\"Legal Notice\" target_type_3=\"_self\" link_type_3=\"link-cms\" cmspage_id_3=\"2\" category_id_3=\"2\" manufacture_id_3=\"3\" page_id_3=\"address\" link_title_4_1=\"Privacy\" target_type_4=\"_self\" link_type_4=\"link-cms\" cmspage_id_4=\"3\" category_id_4=\"2\" manufacture_id_4=\"3\" page_id_4=\"address\" link_title_5_1=\"Stores\" target_type_5=\"_self\" link_type_5=\"link-page\" cmspage_id_5=\"1\" category_id_5=\"2\" manufacture_id_5=\"3\" page_id_5=\"stores\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"3\" page_id=\"address\" active=\"1\" title=\"OUR STORE\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_2747350081\" xl=\"2\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_3024617484\" total_link=\"5\" list_id_link=\"1,2,3,4,5,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,target_type_5,link_type_5,cmspage_id_5,category_id_5,product_id_5,manufacture_id_5,page_id_5,page_param_5,supplier_id_5,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,link_title_5,link_url_5,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"Home\" target_type_1=\"_self\" link_type_1=\"link-page\" cmspage_id_1=\"1\" category_id_1=\"2\" manufacture_id_1=\"3\" page_id_1=\"index\" link_title_2_1=\"Address\" target_type_2=\"_self\" link_type_2=\"link-page\" cmspage_id_2=\"1\" category_id_2=\"2\" manufacture_id_2=\"3\" page_id_2=\"address\" link_title_3_1=\"Sitemap\" target_type_3=\"_self\" link_type_3=\"link-page\" cmspage_id_3=\"1\" category_id_3=\"2\" manufacture_id_3=\"3\" page_id_3=\"sitemap\" link_title_4_1=\"Category\" target_type_4=\"_self\" link_type_4=\"link-category\" cmspage_id_4=\"1\" category_id_4=\"3\" manufacture_id_4=\"3\" page_id_4=\"address\" link_title_5_1=\"New products\" target_type_5=\"_self\" link_type_5=\"link-page\" cmspage_id_5=\"1\" category_id_5=\"2\" manufacture_id_5=\"3\" page_id_5=\"newproducts\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"3\" page_id=\"address\" title=\"QUICK LINK\"][/ApBlockLink][/ApColumn][ApColumn form_id=\"form_2998304547\" xl=\"2\" lg=\"3\" md=\"3\" sm=\"12\" xs=\"12\" sp=\"12\" specific_type=\"all\" active=\"1\" controller_pages=\"\" controller_id=\"\"][ApBlockLink form_id=\"form_2303257323\" total_link=\"5\" list_id_link=\"1,2,3,4,5,\" list_field=\"target_type_1,link_type_1,cmspage_id_1,category_id_1,product_id_1,manufacture_id_1,page_id_1,page_param_1,supplier_id_1,target_type_2,link_type_2,cmspage_id_2,category_id_2,product_id_2,manufacture_id_2,page_id_2,page_param_2,supplier_id_2,target_type_3,link_type_3,cmspage_id_3,category_id_3,product_id_3,manufacture_id_3,page_id_3,page_param_3,supplier_id_3,target_type_4,link_type_4,cmspage_id_4,category_id_4,product_id_4,manufacture_id_4,page_id_4,page_param_4,supplier_id_4,target_type_5,link_type_5,cmspage_id_5,category_id_5,product_id_5,manufacture_id_5,page_id_5,page_param_5,supplier_id_5,\" list_field_lang=\"link_title_1,link_url_1,link_title_2,link_url_2,link_title_3,link_url_3,link_title_4,link_url_4,link_title_5,link_url_5,\" accordion_type=\"accordion_small_screen\" link_title_1_1=\"Search\" target_type_1=\"_self\" link_type_1=\"link-page\" cmspage_id_1=\"1\" category_id_1=\"2\" manufacture_id_1=\"3\" page_id_1=\"search\" link_title_2_1=\"History\" target_type_2=\"_self\" link_type_2=\"link-page\" cmspage_id_2=\"1\" category_id_2=\"2\" manufacture_id_2=\"3\" page_id_2=\"history\" link_title_3_1=\"Supplier\" target_type_3=\"_self\" link_type_3=\"link-category\" cmspage_id_3=\"1\" category_id_3=\"2\" manufacture_id_3=\"3\" page_id_3=\"address\" link_title_4_1=\"Pagenotfound\" target_type_4=\"_self\" link_type_4=\"link-page\" cmspage_id_4=\"1\" category_id_4=\"2\" manufacture_id_4=\"3\" page_id_4=\"index\" link_title_5_1=\"Best sales\" target_type_5=\"_self\" link_type_5=\"link-category\" cmspage_id_5=\"1\" category_id_5=\"2\" manufacture_id_5=\"3\" page_id_5=\"pricesdrop\" target_type=\"_self\" link_type=\"link-url\" cmspage_id=\"1\" category_id=\"2\" manufacture_id=\"3\" page_id=\"address\" active=\"1\" title=\"SUPPORTS\"][/ApBlockLink][/ApColumn][/ApRow]'),
(54, 1, ''),
(55, 1, ''),
(56, 1, ''),
(57, 1, ''),
(58, 1, ''),
(59, 1, '');";
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $sqlRun = str_replace('ID_LANG', (int)$lang["id_lang"], $sql);
                Db::getInstance()->execute($sqlRun);
            }

            //table appagebuilder_shop
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_shop`');
            $sql = 'INSERT INTO `'._DB_PREFIX_.'appagebuilder_shop` (`id_appagebuilder`, `id_shop`) VALUES
(1, ID_SHOP),
(2, ID_SHOP),
(3, ID_SHOP),
(4, ID_SHOP),
(5, ID_SHOP),
(6, ID_SHOP),
(7, ID_SHOP),
(8, ID_SHOP),
(9, ID_SHOP),
(10, ID_SHOP),
(11, ID_SHOP),
(12, ID_SHOP),
(13, ID_SHOP),
(14, ID_SHOP),
(15, ID_SHOP),
(16, ID_SHOP),
(17, ID_SHOP),
(18, ID_SHOP),
(19, ID_SHOP),
(20, ID_SHOP),
(21, ID_SHOP),
(22, ID_SHOP),
(23, ID_SHOP),
(24, ID_SHOP),
(25, ID_SHOP),
(26, ID_SHOP),
(27, ID_SHOP),
(28, ID_SHOP),
(29, ID_SHOP),
(30, ID_SHOP),
(31, ID_SHOP),
(32, ID_SHOP),
(33, ID_SHOP),
(34, ID_SHOP),
(35, ID_SHOP),
(36, ID_SHOP),
(37, ID_SHOP),
(38, ID_SHOP),
(39, ID_SHOP),
(40, ID_SHOP),
(41, ID_SHOP),
(42, ID_SHOP),
(43, ID_SHOP),
(44, ID_SHOP),
(45, ID_SHOP),
(46, ID_SHOP),
(47, ID_SHOP),
(48, ID_SHOP),
(49, ID_SHOP),
(50, ID_SHOP),
(51, ID_SHOP),
(52, ID_SHOP),
(53, ID_SHOP),
(54, ID_SHOP),
(55, ID_SHOP),
(56, ID_SHOP),
(57, ID_SHOP),
(58, ID_SHOP),
(59, ID_SHOP);';
            $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
            Db::getInstance()->execute($sql);

            //table appagebuilder_products
            // Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_products`');
            // $sql = "";
            // Db::getInstance()->execute($sql);

            //table appagebuilder_products_shop
//             Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'appagebuilder_products_shop`');
//             $sql = "INSERT INTO `"._DB_PREFIX_."appagebuilder_products_shop` (`id_appagebuilder_products`, `id_shop`, `active`, `active_mobile`, `active_tablet`) VALUES
// (1, ID_SHOP, 0, NULL, NULL),
// (2, ID_SHOP, 1, NULL, NULL),
// (3, ID_SHOP, 0, NULL, NULL),
// (4, ID_SHOP, 0, NULL, NULL),
// (5, ID_SHOP, 0, NULL, NULL),
// (6, ID_SHOP, 0, NULL, NULL),
// (7, ID_SHOP, 0, NULL, NULL),
// (8, ID_SHOP, 0, NULL, NULL),
// (9, ID_SHOP, 0, NULL, NULL),
// (10, ID_SHOP, 0, NULL, NULL),
// (11, ID_SHOP, 0, NULL, NULL),
// (12, ID_SHOP, 0, NULL, NULL);";
//             $sql = str_replace('ID_SHOP', (int)$id_shop, $sql);
//             Db::getInstance()->execute($sql);

            //copy product profile
            $folder = apPageHelper::getConfigDir('theme_profiles');
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            $tpl_grid = Tools::file_get_contents(_PS_MODULE_DIR_.'appagebuilder/views/templates/front/product-item/plist1427203522.tpl');
            ApPageSetting::writeFile($folder, 'plist1427203522.tpl', $tpl_grid);
        }

        public static function installModuleTab()
        {
            $id_parent = Tab::getIdFromClassName('IMPROVE');

            //create parent tab
            $newtab = new Tab();
            $newtab->class_name = 'AdminApPageBuilder';
            $newtab->id_parent = $id_parent;
            $newtab->module = 'appagebuilder';
            foreach (Language::getLanguages(false) as $l) {
                $newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans('Ap PageBuilder', array(), 'Modules.Appagebuilder.Admin');
            }

            if ($newtab->save()) {

                $id_parent = $newtab->id;
                # insert icon for tab
                Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "tab" WHERE `id_tab` = "'.(int)$newtab->id.'"');

                foreach (self::getTabs() as $tab) {
                    $newtab = new Tab();
                    $newtab->class_name = $tab['class_name'];
                    $newtab->id_parent = isset($tab['id_parent']) ? $tab['id_parent'] : $id_parent;
                    $newtab->module = 'appagebuilder';
                    foreach (Language::getLanguages(false) as $l) {
                        $newtab->name[$l['id_lang']] = Context::getContext()->getTranslator()->trans($tab['name'], array(), 'Modules.Appagebuilder.Admin');
                    }
                    $newtab->save();
                }
                return true;
            }
            
            return false;
        }

        public static function installConfiguration()
        {
            $res = true;
            $res &= Configuration::updateValue('APPAGEBUILDER_PRODUCT_MAX_RANDOM', 2);
            $res &= Configuration::updateValue('APPAGEBUILDER_GUIDE', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_OWL', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_STELLAR', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_WAYPOINTS', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_INSTAFEED', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_HTML5VIDEO', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_SAVE_MULTITHREARING', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_FULLPAGEJS', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_IMAGE360', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_IMAGEHOTPOT', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_SAVE_SUBMIT', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_PRODUCTZOOM', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_TABCOLLAPSE', 0);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_AJAX', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_PN', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_TRAN', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_IMG', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_COUNT', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_COLOR', 1);
//            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_ACOLOR', 1);
            $res &= Configuration::updateValue('APPAGEBUILDER_COLOR', '');
            $res &= Configuration::updateValue('APPAGEBUILDER_COOKIE_PROFILE', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_SLIDE_IMAGE', 1);

            $res &= Configuration::updateValue('APPAGEBUILDER_HEADER_HOOK', implode(',', ApPageSetting::getHook('header')));
            $res &= Configuration::updateValue('APPAGEBUILDER_CONTENT_HOOK', implode(',', ApPageSetting::getHook('content')));
            $res &= Configuration::updateValue('APPAGEBUILDER_FOOTER_HOOK', implode(',', ApPageSetting::getHook('footer')));
            $res &= Configuration::updateValue('APPAGEBUILDER_PRODUCT_HOOK', implode(',', ApPageSetting::getHook('product')));
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_HEADER_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_CONTENT_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_FOOTER_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_PRODUCT_ID', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_GLOBAL_PROFILE_PARAM', '');
            $res &= Configuration::updateValue('APPAGEBUILDER_LOAD_COOKIE', 0);
            $res &= Configuration::updateValue('APPAGEBUILDER_REGISTER', 0);
            return $res;
        }

        public static function deleteTables()
        {
            return Db::getInstance()->execute('DROP TABLE IF EXISTS `'.
                _DB_PREFIX_.'appagebuilder_profiles`, `'.
                _DB_PREFIX_.'appagebuilder_profiles_lang`, `'.
                _DB_PREFIX_.'appagebuilder_profiles_shop`, `'.
                _DB_PREFIX_.'appagebuilder_products`, `'.
                _DB_PREFIX_.'appagebuilder_products_shop` , `'.
                _DB_PREFIX_.'appagebuilder`, `'.
                _DB_PREFIX_.'appagebuilder_shop`, `'.
                _DB_PREFIX_.'appagebuilder_lang`, `'.
                _DB_PREFIX_.'appagebuilder_extracat`, `'.
                _DB_PREFIX_.'appagebuilder_extrapro`, `'.
                _DB_PREFIX_.'appagebuilder_page`, `'.
                _DB_PREFIX_.'appagebuilder_details`, `'.
                _DB_PREFIX_.'appagebuilder_details_shop`, `'.
                _DB_PREFIX_.'appagebuilder_positions`, `'.
        _DB_PREFIX_.'appagebuilder_shortcode`, `'.
                _DB_PREFIX_.'appagebuilder_shortcode_lang`, `'.
                _DB_PREFIX_.'appagebuilder_shortcode_shop`, `'.
                _DB_PREFIX_.'appagebuilder_positions_shop`;
            ');
        }
        
        public static function uninstallModuleTab()
        {
            $id = Tab::getIdFromClassName('AdminApPageBuilder');
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }

            foreach (self::getTabs() as $tab) {
                $id = Tab::getIdFromClassName($tab['class_name']);
                if ($id) {
                    $tab = new Tab($id);
                    $tab->delete();
                }
            }
            return true;
        }
        
        public static function uninstallConfiguration()
        {
            $res = true;
            $res &= Configuration::deleteByName('APPAGEBUILDER_PRODUCT_MAX_RANDOM');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GUIDE');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_OWL');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_STELLAR');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_WAYPOINTS');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_INSTAFEED');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_HTML5VIDEO');
            $res &= Configuration::deleteByName('APPAGEBUILDER_SAVE_MULTITHREARING');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_FULLPAGEJS');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_IMAGE360');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_IMAGEHOTPOT');
            $res &= Configuration::deleteByName('APPAGEBUILDER_SAVE_SUBMIT');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_PRODUCTZOOM');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_TABCOLLAPSE');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_AJAX');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_PN');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_TRAN');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_IMG');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_COUNT');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_COLOR');
//            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_ACOLOR');
            $res &= Configuration::deleteByName('APPAGEBUILDER_COLOR');
            $res &= Configuration::deleteByName('APPAGEBUILDER_COOKIE_PROFILE');
            $res &= Configuration::deleteByName('APPAGEBUILDER_SLIDE_IMAGE');
            
            
            $res &= Configuration::deleteByName('APPAGEBUILDER_HEADER_HOOK');
            $res &= Configuration::deleteByName('APPAGEBUILDER_CONTENT_HOOK');
            $res &= Configuration::deleteByName('APPAGEBUILDER_FOOTER_HOOK');
            $res &= Configuration::deleteByName('APPAGEBUILDER_PRODUCT_HOOK');

            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_HEADER_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_CONTENT_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_FOOTER_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_PRODUCT_ID');
            $res &= Configuration::deleteByName('APPAGEBUILDER_GLOBAL_PROFILE_PARAM');
            $res &= Configuration::deleteByName('APPAGEBUILDER_LOAD_COOKIE');
            
            //DONGND:: remove config check override for shortcode
            $res &= Configuration::deleteByName('APPAGEBUILDER_OVERRIDED');
            return $res;
        }
        
        /**
         * Remove file index.php in sub folder theme/translations folder when install theme
         */
        public static function processTranslateTheme()
        {
            $theme_name = apPageHelper::getInstallationThemeName();
            if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/config.xml')) {
                $directories = glob(_PS_ALL_THEMES_DIR_.$theme_name.'/translations/*', GLOB_ONLYDIR);
                if (count($directories) > 0) {
                    foreach ($directories as $directories_val) {
                        if (file_exists($directories_val.'/index.php')) {
                            unlink($directories_val.'/index.php');
                        }
                    }
                }
            }
        }
        
        /**
         * Remove file index.php for translate in Quickstart version
         */
        public static function processTranslateQSTheme()
        {
            # GET ARRAY THEME_NAME
            $arr_theme_name = array();
            $themes = glob(_PS_ROOT_DIR_.'/themes/*/config/theme.yml');
            if (count($themes) > 1) {
                foreach ($themes as $key => $value) {
                    $temp_name = basename(Tools::substr($value, 0, -strlen('/config/theme.yml')));
                    if ($temp_name == 'classic') {
                        continue;
                    } else {
                        $arr_theme_name[] = $temp_name;
                    }
                }
            }
            
            foreach ($arr_theme_name as $key => $theme_name) {
                //DONGND:: remove index.php in sub folder theme/translations folder when install theme
                
                if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/config.xml')) {
                    $directories = glob(_PS_ALL_THEMES_DIR_.$theme_name.'/translations/*', GLOB_ONLYDIR);
                    if (count($directories) > 0) {
                        foreach ($directories as $directories_val) {
                            if (file_exists($directories_val.'/index.php')) {
                                unlink($directories_val.'/index.php');
                            }
                        }
                    }
                }
            }
        }
    }

}