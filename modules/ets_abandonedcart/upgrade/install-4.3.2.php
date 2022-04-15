<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_4_3_2()
{
    Db::getInstance()->execute('
        DELETE ddt 
            FROM `' . _DB_PREFIX_ . 'ets_abancart_discount_display_tracking` ddt 
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_display_tracking` dt ON dt.id_ets_abancart_display_tracking = ddt.id_ets_abancart_display_tracking
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_abancart_reminder` ar ON ar.id_ets_abancart_reminder = dt.id_ets_abancart_reminder
        WHERE ar.discount_option="fixed"
    ');

    return true;
}