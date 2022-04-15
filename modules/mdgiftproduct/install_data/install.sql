CREATE TABLE IF NOT EXISTS `__PREFIX_rule` (
  `id_mdgift_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `id_shop` int(11) unsigned NOT NULL DEFAULT '1',
  `compatible_cart_rules` tinyint(1) NOT NULL,
  `apply_products_already_discounted` tinyint(1) NOT NULL,
  `quantity` int(10) unsigned DEFAULT '0',
  `quantity_per_user` int(10) unsigned DEFAULT '0',
  `date_add` datetime DEFAULT NULL,
  `date_upd` datetime DEFAULT NULL,
  `code_prefix` varchar(254) DEFAULT NULL,
  `nb_product_gift` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id_mdgift_rule`)
) ENGINE=_MYSQL_ENGINE_ AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_cart` (
  `id_cart` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_cart_rule` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_cart`,`id_mdgift_rule`,`id_cart_rule`),
  KEY `id_cart` (`id_cart`),
  KEY `id_cart_id_cart_rule` (`id_cart`,`id_cart_rule`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_cart_product` (
  `id_cart` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `id_product_attribute` int(10) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  KEY `id_cart_id_mdgift_rule` (`id_cart`,`id_mdgift_rule`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mdgift_rule` int(10) NOT NULL,
  `condition_type` varchar(254) NOT NULL,
  `id_customer` int(10) unsigned DEFAULT NULL,
  `customer_default_group` int(10) unsigned DEFAULT NULL,
  `customer_birthday` tinyint(1) DEFAULT NULL,
  `products_operator` tinyint(1) unsigned DEFAULT NULL,
  `products_amount` decimal(17,2) DEFAULT NULL,
  `products_nb` int(10) DEFAULT NULL,
  `products_nb_operator` tinyint(1) DEFAULT NULL,
  `products_nb_same` tinyint(1) unsigned DEFAULT NULL,
  `products_nb_same_attributes` tinyint(1) unsigned DEFAULT NULL,
  `products_default_category` tinyint(1) unsigned DEFAULT NULL,
  `product_price_from` decimal(17,2) NOT NULL DEFAULT '0.00',
  `product_price_from_currency` int(10) unsigned NOT NULL,
  `product_price_from_tax` tinyint(1) NOT NULL,
  `product_price_to` decimal(17,2) NOT NULL DEFAULT '0.00',
  `product_price_to_currency` int(10) unsigned NOT NULL,
  `product_price_to_tax` tinyint(1) unsigned NOT NULL,
  `products_amount_tax` tinyint(1) DEFAULT NULL,
  `products_amount_currency` int(10) DEFAULT NULL,
  `apply_discount_to_special` tinyint(1) NOT NULL,
  `restriction_product` tinyint(1) unsigned NOT NULL,
  `restriction_attribute` tinyint(1) unsigned NOT NULL,
  `restriction_feature` tinyint(1) unsigned NOT NULL,
  `restriction_category` tinyint(1) unsigned NOT NULL,
  `restriction_supplier` tinyint(1) unsigned NOT NULL,
  `restriction_manufacturer` tinyint(1) unsigned NOT NULL,
  `restriction_price` tinyint(1) unsigned NOT NULL,
  `cart_amount_operator` tinyint(1) unsigned DEFAULT NULL,
  `cart_amount` decimal(17,2) DEFAULT NULL,
  `cart_amount_currency` int(10) unsigned DEFAULT NULL,
  `cart_amount_tax` tinyint(1) unsigned DEFAULT NULL,
  `cart_amount_shipping` tinyint(1) unsigned DEFAULT NULL,
  `cart_amount_discount` tinyint(1) unsigned DEFAULT NULL,
  `cart_weight_operator` tinyint(1) unsigned DEFAULT NULL,
  `cart_weight` decimal(17,2) DEFAULT NULL,
  `schedule` text,
  `age_from` int(10) unsigned DEFAULT NULL,
  `age_to` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`),
  KEY `id_mdgift_rule` (`id_mdgift_rule`)
) ENGINE=_MYSQL_ENGINE_ AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_attribute` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_attribute` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_attribute`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_category` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_category`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_feature` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_feature` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_feature`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_gender` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) NOT NULL,
  `id_gender` int(10) NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_gender`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_group` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_group`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_manufacturer` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) NOT NULL,
  `id_manufacturer` int(10) NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_manufacturer`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_product` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) NOT NULL,
  `id_product` int(10) NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_product`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_condition_supplier` (
  `id_mdgift_rule_condition` int(10) unsigned NOT NULL,
  `id_mdgift_rule` int(10) NOT NULL,
  `id_supplier` int(10) NOT NULL,
  PRIMARY KEY (`id_mdgift_rule_condition`,`id_mdgift_rule`,`id_supplier`),
  KEY `id_mdgift_rule_condition` (`id_mdgift_rule_condition`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX_rule_lang` (
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_lang` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_mdgift_rule`,`id_lang`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_product` (
  `id_mdgift_rule_product` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_mdgift_rule_product`),
  UNIQUE KEY `id_mdgift_rule` (`id_mdgift_rule`,`id_product`)
) ENGINE=_MYSQL_ENGINE_ AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_rule_product_attribute` (
  `id_mdgift_rule` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `id_product_attribute` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_mdgift_rule`,`id_product`,`id_product_attribute`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_md_giftproduct` (
  `id_md_giftproduct` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_md_giftproduct`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;