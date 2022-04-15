<?PHP
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2019 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * Search Tool
 * version 1.2.0
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

class searchToolHtmlBoxPro extends htmlboxpro
{
    public $addon;
    public $name;
    public $tab;
    public $context;

    public function __construct($addon = null, $tab = null)
    {
        $this->tab = $tab;
        $this->addon = $addon;
        $this->name = $addon;
        $this->context = Context::getContext();
        if (Tools::getValue('searchType', 'false') != 'false' && Tools::getValue('ajax') == 1) {
            if (Tools::getValue('searchType') == 'manufacturer') {
                echo Tools::jsonEncode($this->searchForID('manufacturer', 'name', trim(Tools::getValue('q')), false));
                die();
            } elseif (Tools::getValue('searchType') == 'product') {
                echo Tools::jsonEncode($this->searchForID('product_lang', 'name', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'category') {
                echo Tools::jsonEncode($this->searchForID('category_lang', 'name', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'supplier') {
                echo Tools::jsonEncode($this->searchForID('supplier', 'name', trim(Tools::getValue('q')), false));
                die();
            } elseif (Tools::getValue('searchType') == 'cms_category') {
                echo Tools::jsonEncode($this->searchForID('cms_category_lang', 'name', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'cms') {
                echo Tools::jsonEncode($this->searchForID('cms_lang', 'meta_title', trim(Tools::getValue('q')), true));
                die();
            } elseif (Tools::getValue('searchType') == 'customer') {
                echo Tools::jsonEncode($this->searchForID('customer', array('email', 'firstname', 'lastname'), trim(Tools::getValue('q')), true));
                die();
            }
        } elseif (Tools::getValue('getCombinations') == 1 && Tools::getValue('searchByID', 'false') != 'false' && Tools::getValue('ajax') == 1) {
            echo $this->returnCombinations(Tools::getValue('searchByID'), Tools::getValue('combinationsClass'));
        }
    }

    public function initTool()
    {
        $this->context->smarty->assign('SearchToolLink', $this->context->link->getAdminLink('AdminModules', false) . '&token=' . Tools::getValue('token') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&ajax=1&module_name=' . $this->name);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->addon . '/lib/searchTool/views/scripts.tpl');
    }

    public function searchTool($type, $resultInput, $replacementType = 'replace', $returnBox = false, $object = false, $combinations = false, $combination_input = '', $selected_combination = false)
    {
        $array = array();
        if ($returnBox == true) {
            if ($object != false) {
                $objectClass = ucfirst($type);
                if ($objectClass == 'Cms_category') {
                    $objectClass = 'CMSCategory';
                } elseif ($objectClass == 'Cms') {
                    $objectClass = 'CMS';
                }
                if (class_exists($objectClass)) {
                    $object_exploded = explode(',', $object);
                    foreach ($object_exploded AS $object_item) {
                        if ($type == 'product') {
                            $object_to_display = new $objectClass($object_item, false, $this->context->language->id);
                        } else {
                            $object_to_display = new $objectClass($object_item, $this->context->language->id);
                        }
                        $array[] = '<div class="' . $type . $resultInput . $object_item . '"><span class="btn btn-default" onclick="SearchToolRemoveItem(\'' . $type . $resultInput . $object_item . '\',\'' . $type . '\',\'' . $resultInput . '\',\'' . $object_item . '\');"><i class="icon-remove"></i></span> #' . $object_to_display->id . ' ' . (isset($object_to_display->firstname) ? $object_to_display->firstname . ' ' . $object_to_display->lastname . ' ' . $object_to_display->email : (isset($object_to_display->name) ? $object_to_display->name : (isset($object_to_display->meta_title) ? $object_to_display->meta_title : ''))) . '</div>';
                    }
                }
            }
            return '<div class="' . $resultInput . '_' . $type . 'sBox">' . implode('', $array) . '</div>' . ($type == 'product' ? (isset($object_to_display->id) ? (($combinations == true) ? (($selected_combination != false) ? $this->returnCombinations($object_to_display->id, $combination_input, $selected_combination) : '') : '') : '') : '');
        }
        return '<input style="width: 40px; font-size: 10px; margin: 0px; height: 17px;" type="text" placeholder="' . $this->l('Search') . '" id="searchTool_' . $type . '" data-replacementtype="' . $replacementType . '" data-resultinput="' . $resultInput . '" data-type="' . $type . '" data-toggle="tooltip" data-original-title="'.$this->l('Search for name of: ').$type.'" class="label-tooltip searchToolInput searchTool_' . $type . '"/>';
    }

    public function searchForID($table, $field, $term, $shop = false)
    {
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . $table . '` WHERE ' . (is_array($field) ? $this->returnArrayFields($field, $term) : ($field . " LIKE '%" . psql($term) . "%' ")) . ($shop != false ? 'AND id_shop="' . $shop . '"' : '') . ' GROUP BY id_' . str_replace('_lang', '', $table));
        return $result;
    }

    public function returnArrayFields($field, $term)
    {
        $return = array();
        foreach ($field AS $f) {
            $return[] = $f . " LIKE '%" . psql($term) . "%' ";
        }
        return implode("OR ", $return);
    }

    public function returnCombinations($id = 0, $class = 'class', $preselected = false)
    {
        $product = new Product($id, false, $this->context->language->id);
        $combinations = $product->getAttributeCombinations($this->context->language->id);
        $combinations_array = array();

        if (count($combinations) > 0) {
            foreach ($combinations AS $key => $combination) {
                $cb = new Combination($combination['id_product_attribute'], $this->context->language->id);
                $cb_name = $cb->getAttributesName($this->context->language->id);
                $combination_name = '';
                if (count($cb_name) > 0) {
                    foreach ($cb_name AS $cb_name) {
                        $combination_name .= $cb_name['name'] . ' ';
                    }
                }
                $combinations_array[$combination['id_product_attribute']] = "<div class='form-control-static margin-form '><div class='btn btn-default " . $class . "Buttons " . (($preselected != false) ? (($preselected == $combination['id_product_attribute']) ? 'active' : '') : '') . "' onclick=\"$('." . $class . "Buttons').removeClass('active'); $(this).addClass('active'); $('#" . $class . "').val(" . $combination['id_product_attribute'] . ")\" >" . $this->l('select') . "</div> " . $combination_name . " " . ($combination['reference'] != '' ? '(' . $combination['reference'] . ')' : '') . "</div>";
            }
            return '<div class="panel ' . $class . 'selectedCombinations" style="margin-top:20px"><h3>' . $this->l('Select combination') . '</h3>' . implode('', $combinations_array) . '</div>';
        }
    }
}