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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Mdgiftproduct extends Module
{
    protected $config_form = false;
    public $installer;

    public function __construct()
    {
        $this->name = 'mdgiftproduct';
        $this->tab = 'front_office_features';
        $this->version = '1.0.8';
        $this->author = 'Digincube.com';
        $this->need_instance = 0;
        $this->module_key = '3b06f9c8e37d6bd95ed9cf8d6bfc03b0';
        $this->registerClassLoader();
        $this->secure_key = $this->hashFunc($this->name);

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Free Gifts Products Promo');
        $this->description = $this->l('Free Gifts Products Promo');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->installer = new MdGiftInstaller($this, $this->context);
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if (!$this->installer->installHooks()) {
            return false;
        }

        if (!$this->installer->installControllers()) {
            return false;
        }

        if (!$this->execSQL('install')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!$this->installer->execUninstallScript()) {
            return false;
        }

        $this->installer->uninstallControllers();

        return parent::uninstall();
    }
    
    public function execSQL($type)
    {
        $path = dirname(__FILE__) . '/install_data/' . $type . '.sql';

        return $this->installer->execSQLFile($path);
    }
    
    public function getDir()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
    public function registerClassLoader()
    {
        spl_autoload_register('Mdgiftproduct::loadClass');
    }

    public static function loadClass($class_name)
    {
        // search in classes folder
        $class_path = dirname(__FILE__).'/classes/'.$class_name.'.php';
        if (!is_file($class_path)) {
            $folders = array('models', 'tools', 'module', Tools::strtolower($class_name));
            foreach ($folders as $folder) {
                $class_path = dirname(__FILE__).'/classes/'.$folder.'/'.$class_name.'.php';
                if (is_file($class_path)) {
                    break;
                }
            }
        }
        if (is_file($class_path)) {
            require_once($class_path);
        }
    }

    public function getContent()
    {
        if (Tools::getValue('magic')) {
            return $this->renderForm();
        }
        return Tools::redirectAdmin('index.php?controller=AdminGiftProductRules&token='.Tools::getAdminTokenLite('AdminGiftProductRules'));
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMd_giftproductModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
            ),
        );
    }

    
    
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/splide.min.js');
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->context->controller->addJS($this->_path.'/views/js/front16.js');
        } else {
            $this->context->controller->addJS($this->_path.'/views/js/front.js');
        }
        
        $this->context->controller->addCSS($this->_path.'/views/css/splide.min.css');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        
        Media::addJsDef(
            array(
                'gift_controller_url' => $this->context->link->getModuleLink('mdgiftproduct', 'FreeGiftproducts'),
                'secureKey' => $this->secure_key,
                'v178' => version_compare(_PS_VERSION_, '1.7.8', '<') ? 'no' : 'yes',
                'cart_page_redirect_link' => $this->context->link->getPageLink(
                    'cart',
                    null,
                    $this->context->language->id,
                    array('action' => 'show'),
                    false,
                    null,
                    true
                ),
            )
        );
        $cart = Context::getContext()->cart;
        $id_cart = $cart->id;
        $gift = new MdGiftRule();
        $validGifts = $gift->findValidGiftRules();
        $giftIncart = $gift->getExistingGiftRulesInCart(Context::getContext()->cart->id);
        
        if (empty($giftIncart)) {
            if (!empty($validGifts)) {
                foreach ($validGifts as $giftItem) {
                    if (isset($giftItem['products']) && count($giftItem['products']) == 1 && (count($giftItem['products'][0]->selectedCombinations) == 1
                    || empty($giftItem['products'][0]->selectedCombinations))) {
                        $product = reset($giftItem['products']);
                        $products = [['id_product'=>$product->id,'id_product_attribute'=>reset($product->selectedCombinations)['id_product_attribute']]];
                        
                        $mdgiftRule = new MdGiftRule($giftItem['id']);
                        $mdgiftRule->addGiftToCart($products, true);
                    }
                }
            }
        } elseif (!empty($giftIncart)) {
            $giftIdsIncart = array_column($giftIncart, 'id_mdgift_rule');
            $gift_to_delete = array_filter(array_diff($giftIdsIncart, array_keys($validGifts)));
            if (!empty($gift_to_delete)) {
                foreach ($gift_to_delete as $key => $id_mdgift_rule) {
                    $id_cart_rule = $giftIncart[$key]['id_cart_rule'];
                    $old_gift_products = $gift->getGiftProductAtCart($id_cart, $id_mdgift_rule, null, true);
                    if (!empty($old_gift_products)) {
                        foreach ($old_gift_products as $old_product) {
                            $cart->updateQty((int) $old_product['quantity'], $old_product['id_product'], $old_product['id_product_attribute'], false, 'down', 0, null, false);
                        }
                    }
                    $gift->deleteFromCartProduct($id_cart, $id_mdgift_rule);
                    $gift->calculateCartDiscount($id_cart, $id_mdgift_rule, $id_cart_rule);
                }
            }
        }
    }


    public function hookDisplayShoppingCartFooter($params)
    {
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
        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/front/shopping-cart-gift.tpl');
    }
    
    public function hookRenderShoppingCartWidget($params)
    {
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
        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/front/shopping-cart-gift.tpl');
    }

    public function hookActionCartSave($params)
    {
        if (Tools::getValue('delete') == '1' || Tools::getValue('delete') == 'true') {
            $mdgiftRule = new MdGiftRule();
            $mdgiftRule->deleteFromCart();
            return true;
        }
    }

    public function hashFunc($passwd)
    {
        return md5(_COOKIE_KEY_ . $passwd);
    }
}
