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

class MdGiftInstaller
{
    public $module;
    public $context;

    private $hooks_front = array(
        'header',
        'displayShoppingCartFooter',
        'displayShoppingCart',
        'shoppingCartExtra',
        'renderShoppingCartWidget',
        'actionCartSave'
    );
    private $hooks_admin = array(
        'displayBackOfficeHeader',
        'actionAdminControllerSetMedia'
    );

    private static $controllers = array(
        array(
            'name'  => 'Free Gifts Products Promo',
            'class' => 'AdminGiftProductRules'
        ),
        array(
            'name'  => 'Stats generated Gifts',
            'class' => 'AdminGeneratedGifts'
        )
    );

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getHooks()
    {
        return array_merge($this->hooks_front, $this->hooks_admin);
    }

    public function getFrontHooks()
    {
        return $this->hooks_front;
    }

    public function installHooks()
    {
        foreach ($this->getHooks() as $hook) {
            $this->module->registerHook($hook);
        }
        return true;
    }

    public function uninstallHooks()
    {
        foreach ($this->getHooks() as $hook) {
            $this->module->unregisterHook($hook);
        }
        return true;
    }

    public function installControllers()
    {
        $success = true;
        foreach (self::$controllers as $controller) {
            $success &= $this->installController($controller);
        }
        return $success;
    }

    public function installController($controller)
    {
        $languages = Language::getLanguages();
        $tab = new Tab();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $controller['name'];
        }
        $tab->class_name = $controller['class'];
        $tab->id_parent = (int)Tab::getIdFromClassName('DEFAULT');
        $tab->icon = 'settings_applications';
        $tab->module = $this->module->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallController($name)
    {
        $tab = new Tab((int)Tab::getIdFromClassName($name));
        return $tab->delete();
    }

    public function uninstallControllers()
    {
        $success = true;
        foreach (self::$controllers as $controller) {
            $success &= $this->uninstallController($controller ['class']);
        }
        return $success;
    }

    public function execSQLFile($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        if (!$sql = Tools::file_get_contents($path)) {
            return false;
        }
        $sql = str_replace(
            array('__PREFIX', '_MYSQL_ENGINE_'),
            array(_DB_PREFIX_ . 'mdgift', _MYSQL_ENGINE_),
            $sql
        );
        $sql = preg_split('/;\s*[\r\n]+/', $sql);
        foreach ($sql as $query) {
            if (trim($query) && !Db::getInstance()->execute(trim($query))) {
                return false;
            }
        }
        return true;
    }
    
    public function execUninstallScript()
    {
        $success = true;
        $uninstall_script = $this->module->getDir() . 'sql/tables.json';
        $contents = Tools::file_get_contents($uninstall_script);
        $tables = json_decode($contents, true);
        if (is_array($tables)) {
            foreach ($tables as $table) {
                $sql = 'DROP TABLE IF EXISTS `' . pSQL(_DB_PREFIX_ . 'mdgift'. $table) . '`;';
                $success &= Db::getInstance()->execute($sql);
            }
        }
        return $success;
    }

    public function upgradeSQL($version)
    {
        $path = $this->module->getFolderPath('upgrade/sql') . "upgrade-{$version}.sql";
        return $this->execSQLFile($path);
    }
}
