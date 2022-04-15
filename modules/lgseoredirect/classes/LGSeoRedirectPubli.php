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

class LGSeoRedirectPubli
{
    const MODULE_NAME = 'lgseoredirect';
    private $module;
    private $iso_langs = array('es', 'en', 'fr', 'it', 'de');

    private static $instance = null;

    protected function __construct()
    {
        $this->module = Module::getInstanceByName(self::MODULE_NAME);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new LGSeoRedirectPubli();
        }
        return self::$instance;
    }

    public function getHeader()
    {
        return $this->getP('top');
    }

    public function getFooter()
    {
        return $this->getP('bottom');
    }

    protected function getP($template)
    {
        $context          = Context::getContext();
        $current_iso_lang = $context->language->iso_code;
        $iso              = (in_array($current_iso_lang, $this->iso_langs)) ? $current_iso_lang : 'en';

        $context->smarty->assign(
            array(
                'lgpublicidad_iso'      => $iso,
                'lgpublicidad_base_url' => _MODULE_DIR_. self::MODULE_NAME,
            )
        );

        return $this->module->display(
            $this->module->getLocalPath(),
            'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . '_p_' . $template . '.tpl'
        );
    }
}
