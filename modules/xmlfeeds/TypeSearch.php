<?php
/**
 * 2010-2021 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2021 Bl Modules
 * @license
 */

require(dirname(__FILE__).'/../../config/config.inc.php');
require(dirname(__FILE__).'/../../modules/xmlfeeds/xmlfeeds.php');
require(dirname(__FILE__).'/../../modules/xmlfeeds/FeedType.php');

class TypeSearch extends Xmlfeeds
{
    public function init()
    {
        $this->smarty->assign([
            'feedTypeList' => $this->filterTypes(),
            'contactUsUrl' => $this->contactUsUrl,
        ]);

        echo $this->displaySmarty('views/templates/admin/page/searchFeedTypeApi.tpl');
    }

    protected function filterTypes()
    {
        $FeedType = new FeedType();
        $types = $FeedType->getAllTypes();

        $search = Tools::strtolower(htmlspecialchars(Tools::getValue('s'), ENT_QUOTES));

        if (empty($search)) {
            return $types;
        }

        $this->saveToLog($search);

        foreach ($types as $k => $v) {
            $name = Tools::strtolower($v['name']);

            if (strpos($name, $search) === false) {
                unset($types[$k]);
            }
        }

        return $types;
    }

    protected function saveToLog($search)
    {
        return false;
        
        Db::getInstance()->Execute('
            INSERT INTO '._DB_PREFIX_.'blmod_xml_feed_search_query
            (`value`, `ip_address`, `date_add`)
            VALUE
            ("'.pSQL($search).'", "'.pSQL(Tools::getRemoteAddr()).'", "'.pSQL(date('Y-m-d H:i:s')).'")
        ');
    }
}

$typeSearch = new TypeSearch();
$typeSearch->init();
