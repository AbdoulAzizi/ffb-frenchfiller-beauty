<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function upgrade_module_1_1_5()
{
    $add    = false;
    $update = false;

    if (!Db::getInstance()->ExecuteS('SHOW COLUMNS FROM '._DB_PREFIX_.'lgseoredirect LIKE "id_shop"')) {
        $add = Db::getInstance()->Execute('ALTER TABLE '._DB_PREFIX_.'lgseoredirect ADD id_shop int(11) NOT NULL');
        $update = Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'lgseoredirect SET id_shop=1');
    } else {
        $add = true;
        $update = true;
    }

    return $add and $update;
}
