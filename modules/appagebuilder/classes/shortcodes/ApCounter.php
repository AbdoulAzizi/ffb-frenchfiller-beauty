<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2019 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class ApCounter extends ApShortCodeBase
{
    public $name = 'ApCounter';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Counter'),
            'position' => 5,
            'desc' => $this->l('Show Counter Box'),
            'icon_class' => 'icon icon-list',
            'tag' => 'content');
    }

    public function getConfigList()
    {
        $languages = Language::getLanguages();
        $list_id_lang = array();
        foreach ($languages as $languages_val) {
            array_push($list_id_lang, $languages_val['id_lang']);
        }
        
        // Get value with keys special
        $languages = Language::getLanguages(false);
        $config_val = array();
        
        $list_field = Tools::getValue('list_field');
        $list_field = array_filter(explode(',', $list_field));
        foreach ($list_field as $field) {
            $key = $field;
            $config_val[$key] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($key));
        }
        
        $list_field_lang = Tools::getValue('list_field_lang');
        $list_field_lang = array_filter(explode(',', $list_field_lang));
        foreach ($languages as $lang) {
            foreach ($list_field_lang as $field) {
                $key = $field.'_'.$lang['id_lang'];
                $config_val[$key] = str_replace($this->str_search, $this->str_relace_html_admin, Tools::getValue($key));
            }
        }
        $inputs = array(
            array(
                'type' => 'leoalert',
                'name' => 'alertloadjs',
                'html_text' => $this->l('You have to turn on script numscroller-1.0 for Counter in back-office > ApPageBuilder > Ap Module Configuration'),
                'href' => 'index.php?controller=AdminModules&configure=appagebuilder&token='.Tools::getAdminTokenLite('AdminModules').'#APPAGEBUILDER_LOAD_IMAGEHOTPOT_on',
                'show' => !Configuration::get('APPAGEBUILDER_LOAD_NUMSCROLLER'),
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<script>var listData = '.Tools::jsonEncode($config_val).';</script>',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' =>   '<script>
                                        var totalLanguage = "'.count($languages).'";
                                        var list_id_lang = \''.Tools::jsonEncode($list_id_lang).'\';
                                        var remove_button_text = "'.$this->l('Remove Counter').'";
                                        var duplicate_button_text = "'.$this->l('Duplicate Counter').'";
                                    </script>',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'total_link',
                'default' => '0',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'list_id_link',
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'list_field',
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'hidden',
                'name' => 'list_field_lang',
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'counterbox',
                'name' => 'title',
                'lang' => 'true',
                'label' => $this->l('Title'),
                'default' => '',
                'form_group_class' => 'hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'widget_title',
                'label' => $this->l('Widget Title'),
                'hint'  => $this->l('Auto hide if leave it blank'),
                'lang'  => 'true',
                'default' => '',
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Widget Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'type' => 'text',
                'name' => 'widget_class',
                'label' => $this->l('CSS Class'),
                'default' => '',
            ),
            array(
                'type'  => 'text',
                'label' => $this->l('Title'),
                'name'  => 'link_title',
                'lang'  => true,
                'default'   => '',
                'class' => 'tmp',
                'form_group_class'  => 'parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'min',
                'label' => $this->l('Starting Number'),
                'form_group_class' => 'aprow_general',
                'default' => '1',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'max',
                'label' => $this->l('Ending Number'),
                'form_group_class' => 'aprow_general',
                'default' => '10000',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'increment',
                'label' => $this->l('Increment Number'),
                'form_group_class' => 'aprow_general',
                'default' => '50',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'delay',
                'label' => $this->l('Delay Time'),
                'form_group_class' => 'aprow_general',
                'default' => '3',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'number_suffix',
                'label' => $this->l('Number Suffix'),
                'form_group_class' => 'aprow_general',
                'default' => '+',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'textarea',
                'name' => 'bootom_html',
                'label' => $this->l('Counter Bootom Html'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'text',
                'name' => 'counterclass',
                'label' => $this->l('CSS Class'),
                'default' => '',
                'class' => 'tmp',
                'form_group_class' => 'parent-tmp hidden',
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<button type="button" class="add-new-link btn btn-default">
                                        <i class="process-icon-new"></i> '.$this->l('Add new Counte').'</button>',
                'form_group_class' => 'frm-add-new-link',
            ),
        );
        
        return $inputs;
    }
    
    public function getConfigValue()
    {
        $config_val = parent::getConfigValue();
        $config_val['target_type'] = '_self';
        return $config_val;
    }

    public function endRenderForm()
    {
        $this->helper->module = new $this->module_name();
    }

    public function prepareFontContent($assign, $module = null)
    {
        unset($module);
        if (!Configuration::get('APPAGEBUILDER_LOAD_NUMSCROLLER')) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Please enable Numscroller-1.0 for Counter.';
            return $assign;
        }
        $formAtts = $assign['formAtts'];
        $formAtts['links'] = array();

        if (!isset($formAtts['list_id_link'])) {
            $assign['formAtts']['lib_has_error'] = true;
            $assign['formAtts']['lib_error'] = 'Dont have any counter. Please create new';
            return $assign;
        }

        $id_forms = $formAtts['list_id_link'];
        $id_forms = array_filter(explode(',', $id_forms));
        foreach ($id_forms as $id_form) {
            $index = '_'.$id_form;
            $indexlang = $index.'_'.Context::getContext()->language->id;
            $link = array();
            $link['link_title'] = (isset($formAtts['link_title'.$indexlang]) && $formAtts['link_title'.$indexlang]) ? $formAtts['link_title'.$indexlang] : '';
            $link['bootom_html'] = (isset($formAtts['bootom_html'.$indexlang]) && $formAtts['bootom_html'.$indexlang]) ? $formAtts['bootom_html'.$indexlang] : '';
            $link['min'] = $formAtts['min'.$index];
            $link['max'] = $formAtts['max'.$index];
            $link['delay'] = $formAtts['delay'.$index];
            $link['increment'] = $formAtts['increment'.$index];
            $link['number_suffix'] = str_replace($this->str_search, $this->str_relace_html, $formAtts['number_suffix'.$index]);
            $link['counterclass'] = isset($formAtts['counterclass'.$index])?$formAtts['counterclass'.$index]:'';
            $formAtts['links'][] = $link;
        }
        $assign['formAtts']['links'] = $formAtts['links'];
        return $assign;
    }
}
