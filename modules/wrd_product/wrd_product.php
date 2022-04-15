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
 * @author    Hennes Hervé <contact@h-hennes.fr>
 * @copyright 2013-2016 Hennes Hervé
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  http://www.h-hennes.fr/blog/
 */

use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerFilters;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Wrd_Product extends Module
{

    public function __construct()
    {

        $this->name = 'wrd_product';
        $this->tab = 'others';
        $this->author = 'hhennes et Web R&D Informatique';
        $this->version = '0.1.1';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('wrd_product');
        $this->description = $this->l('add new fields to product');
        $this->ps_versions_compliancy = array('min' => '1.7.1', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->_installSql()
            //Pour les hooks suivants regarder le fichier src\PrestaShopBundle\Resources\views\Admin\Product\form.html.twig
            || !$this->registerHook('displayAdminProductsExtra')
            || !$this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            || !$this->registerHook('actionOrderGridDefinitionModifier')
        || !$this->registerHook('actionOrderGridQueryBuilderModifier')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    protected function _installSql()
    {
        //$sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
        //        . "ADD custom_field VARCHAR(255) NULL";
        $sqlInstallLang = "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
//                . "ADD custom_field_lang VARCHAR(255) NULL,"
            . "ADD custom_composition_field_lang_wysiwyg TEXT NULL,"
            . "ADD custom_utilisation_field_lang_wysiwyg TEXT NULL";

        //$returnSql = Db::getInstance()->execute($sqlInstall);
        $returnSqlLang = Db::getInstance()->execute($sqlInstallLang);

        return //$returnSql &&
            $returnSqlLang;
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function _unInstallSql()
    {
        //$sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
        //         . "DROP custom_field";
        $sqlInstallLang = "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
            . "DROP custom_composition_field_lang_wysiwyg,DROP custom_utilisation_field_lang_wysiwyg";

        //$returnSql = Db::getInstance()->execute($sqlInstall);
        $returnSqlLang = Db::getInstance()->execute($sqlInstallLang);

        return //$returnSql &&
            $returnSqlLang;
    }

    /**
     * This function is required in order to make module compatible with new translation system.
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        return $this->_displayHookContentBlock(__FUNCTION__);
    }

    /**
     * Affichage des informations supplémentaires sur la fiche produit
     * @param type $params
     * @return type
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $product = new Product($params['id_product']);
        $languages = Language::getLanguages($active);
        $this->context->smarty->assign(array(
                //'custom_field' => $product->custom_field,
                'languages' => $languages,
                //'custom_field_lang' => $product->custom_field_lang,
                'custom_composition_field_lang_wysiwyg' => $product->custom_composition_field_lang_wysiwyg,
                'custom_utilisation_field_lang_wysiwyg' => $product->custom_utilisation_field_lang_wysiwyg,
                'default_language' => $this->context->employee->id_lang,
            )
        );

        return $this->display(__FILE__, 'views/templates/hook/extrafields.tpl');
    }


    /**
     * Fonction pour afficher les différents blocks disponibles
     * @param type $hookName
     * @return type
     */
    protected function _displayHookContentBlock($hookName)
    {
        $this->context->smarty->assign('hookName', $hookName);
        return $this->display(__FILE__, 'views/templates/hook/hookBlock.tpl');
    }

    /**
     * Hook allows to modify Customers grid definition.
     * This hook is a right place to add/remove columns or actions (bulk, grid).
     *
     * @param array $params
     */
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        /** @var GridDefinitionInterface $definition */
        $definition = $params['definition'];

        $translator = $this->getTranslator();

        $definition
            ->getColumns()
            ->addAfter(
                'new',
                (new DataColumn('carrier_name'))
                    ->setName($translator->trans('carrier_name', [], 'Modules.Wrdproduct.Admin'))
                    ->setOptions([
                        'field' => 'carrier_name',
                        //'callback' => 'printCarrierIcon',
                    ])
            )
        ;

        $definition->getFilters()->add(
            (new Filter('carrier_name', TextType::class))
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Search carrier', [], 'Modules.Wrdproduct.Admin'),
                    ],
                    'required' => false,
                ])
                ->setAssociatedColumn('carrier_name')
        );
    }

    /**
     * Hook allows to modify Customers query builder and add custom sql statements.
     *
     * @param array $params
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];

        /** @var CustomerFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder->addSelect(
            'oc.`id_carrier`, ca.name as carrier_name'
        );

        $searchQueryBuilder->leftJoin(
            'o',
            '`' . pSQL(_DB_PREFIX_) . 'order_carrier`',
            'oc',
            'o.`id_order` = oc.id_order'
        );

        $searchQueryBuilder->leftJoin(
            'oc',
            '`' . pSQL(_DB_PREFIX_) . 'carrier`',
            'ca',
            'ca.`id_carrier` = oc.id_carrier'
        );

        if ('carrier_name' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('ca.`name`', $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('carrier_name' === $filterName) {
                $searchQueryBuilder->andWhere('ca.`name` LIKE :carrier_name');
                $searchQueryBuilder->setParameter('carrier_name', '%'.$filterValue.'%');

                if (!$filterValue) {
                    $searchQueryBuilder->orWhere('ca.`name` IS NULL');
                }
            }
        }
    }

    // Recuperation de l'image du transporteur
    public function printCarrierIcon($id_order, $tr)
    {
        if (file_exists(_PS_TMP_IMG_DIR_ . 'carrier_mini_' . $tr['id_carrier'] . '_1.jpg')) {
            return '<img src="../img/tmp/carrier_mini_' . $tr['id_carrier'] . '_1.jpg'.'" class="imgm img-thumbnail" />';
        }
        return null;
    }
}
