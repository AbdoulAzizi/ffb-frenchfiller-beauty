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

class LGSeoRedirectPageNotFound extends ObjectModel
{
    /**a
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'         => 'lgseoredirect_pagenotfound',
        'primary'       => 'id_lgseoredirect_pagenotfound',
        'multilang'     => false,
        'multilangshop' => true,
        'fields'    => array(
            'id_pagenotfound' => array('type' => self::TYPE_INT, 'required' => true),
            'request_uri'     => array('type' => self::TYPE_INT, 'required' => true),
        )
    );
}
