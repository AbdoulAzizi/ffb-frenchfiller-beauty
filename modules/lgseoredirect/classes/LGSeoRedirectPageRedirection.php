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

class LGSeoRedirectPageRedirection
{
    /**a
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'         => 'lgseoredirect_pageredirection',
        'primary'       => 'id_lgseoredirect_pageredirection',
        'multilang'     => false,
        'multilangshop' => true,
        'fields'    => array(
            'id_pagenotfound' => array('type' => self::TYPE_INT, 'required' => true),
            'id_pagenotfound' => array('type' => self::TYPE_INT, 'required' => true),
        )
    );
}
