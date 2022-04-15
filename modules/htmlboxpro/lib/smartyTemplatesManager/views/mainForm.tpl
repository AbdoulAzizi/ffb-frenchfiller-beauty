{*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2019 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
*}
<link rel="stylesheet" href="../modules/{$etm_addon}/lib/smartyTemplatesManager/css/smartyTemplatesManager.css">

<div id="content" class="bootstrap" style="padding:0px!important;">
    <div class="panel clearfix">
        <div class="panel-heading">{l s='Smarty templates manager for module' mod='htmlboxpro'} </div>
        <div class="col-lg-2" id="smartyTemplatesManagerMenu">
            <div class="productTabs">
                <ul class="tab">
                    <li class="tab-row">
                        <a class="etm_tab_page selected" id="etm_link_manageTemplates"
                           href="javascript:displayEtmTab('manageTemplates');">1. {l s='Edit templates' mod='htmlboxpro'}</a>
                        <a class="etm_tab_page" id="etm_link_createTemplates"
                           href="javascript:displayEtmTab('createTemplates');">2. {l s='Create template' mod='htmlboxpro'}</a>
                    </li>
                </ul>
            </div>
            {if $etm_additional_variables != false}
                <div class="panel">
                    <div class="alert alert-info">
                        <strong>{l s='additional smarty variables available exclusively for this module' mod='htmlboxpro'}</strong><br/><br/>
                        {foreach $etm_additional_variables as $variable => $key}
                            {$variable}
                            <br/>
                        {/foreach}
                    </div>
                </div>
            {/if}
        </div>
        <div class="col-lg-10">
            <div id="etm_manageTemplates" class="etm_tab tab-pane" style="display:none;">
                {$etm_templates nofilter}
            </div>
            <div id="etm_createTemplates" class="etm_tab tab-pane" style="display:none;">
                {$etm_create_template nofilter}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<script>
    var etm_module_url = '{$etm_module_url|escape:javascript}';
    $(document).ready(function () {
        displayEtmTab('manageTemplates');
        BindEtmScripts();
    });
</script>
