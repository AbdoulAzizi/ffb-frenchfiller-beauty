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

require_once(_PS_MODULE_DIR_.'mdgiftproduct/classes/models/MdGiftRule.php');

class MdgiftproductFreeGiftproductsModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }
    
    public function displayAjaxAddToCart()
    {
        /* Action to Add gift ToCart */
        if (Tools::getValue('secureKey') == $this->module->secure_key) {
            $gift_id = Tools::getValue('gift');
            $mdgiftRule = new MdGiftRule($gift_id);
            $mdgiftRule->addGiftToCart();
        }
    }
    
    public function displayAjaxRemoveVoucher()
    {
        if (Tools::getValue('secureKey') == $this->module->secure_key) {
            //var_dump('test');exit();
            $mdgiftRule = new MdGiftRule();
            $mdgiftRule->voucherRemoved();
        }
    }
    
    public function displayAjaxCheckSelectedGift()
    {
        if (Tools::getValue('secureKey') == $this->module->secure_key) {
            $id_cart = $this->context->cart->id;
            $mdgiftRule = new MdGiftRule();
            $gift_rules = $mdgiftRule->findValidGiftRules();
            $gifts_in_cart = MdGiftRule::getGiftProductAddedToCart($id_cart);
            $this->context->smarty->assign(
                array(
                    'gift_rules' => $gift_rules,
                    'gifts_in_cart' => $gifts_in_cart,
                    'image_size' => ImageType::getFormatedName('home')
                )
            );
            $gift_html = $this->context->smarty->fetch('module:mdgiftproduct/views/templates/hook/front/shopping-cart-gift.tpl');
            die(Tools::jsonEncode(
                array(
                'status' => true,
                'gift_rules' => $gift_rules,
                'gift_html' => $gift_html,
                )
            ));
        }
    }
}
