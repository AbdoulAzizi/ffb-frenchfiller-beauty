{*
* 2007-2020 PrestaShop
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
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<form class="form-horizontal"
      action="#"
      name="colissimo_widget_config_form"
      id="colissimo-widget-config-form"
      method="post"
      enctype="multipart/form-data">
  <div class="alert alert-info">
    {l s='While installing the module, 4 carriers have been created.' mod='colissimo'}<br/>
    <a href="{$link->getAdminLink('AdminCarriers')|escape:'htmlall':'UTF-8'}"
       target="_blank">{l s='Click here to configure their zones, ranges and prices.' mod='colissimo'}</a><br/><br/>
    <b><i class="icon icon-warning"></i> {l s='In order for the module to work properly, please do not delete these carriers' mod='colissimo'}
    </b>
  </div>
  <div class="panel">
    <div class="panel-heading">
      {l s='Brexit' mod='colissimo'}
    </div>
    <div class="row">
      <div class="brexit-intro">
        <img src="{$data['img_path']|escape:'htmlall':'UTF-8'}icons/icon_uk.png"/>
        <p>{l s='As you probably know, the coming withdrawal of the United Kingdom from the European Union will impact deliveries to the UK. The timeline of the Brexit is still uncertain but you can enable Brexit mode below when the withdrawal will be effective in order to apply changes caused by the Brexit.' mod='colissimo'}</p>
      </div>

      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>
            {l s='Enable Brexit' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg colissimo-enable-brexit">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_ENABLE_BREXIT"
                   id="COLISSIMO_ENABLE_BREXIT_on"
                   {if $form_data['COLISSIMO_ENABLE_BREXIT']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_ENABLE_BREXIT_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_ENABLE_BREXIT"
                   id="COLISSIMO_ENABLE_BREXIT_off"
                   {if !$form_data['COLISSIMO_ENABLE_BREXIT']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_ENABLE_BREXIT_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
    </div>
    <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoEnableBrexit">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
      </button>
    </div>
  </div>
  <div class="panel">
    <div class="panel-heading">
      {l s='Pick-up service' mod='colissimo'}
    </div>
    <div class="row">
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='Choosing "Yes" (default option) will give you access to the latest features of the Widget.' mod='colissimo'}<br />{l s='Choose "No" if you experience any problems with the display of the Widget (conflict with other modules or theme). The module will use local files that you will be able to override if needed.' mod='colissimo'}">
            {l s='Use default Colissimo widget' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg ">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_WIDGET_REMOTE"
                   id="COLISSIMO_WIDGET_REMOTE_on"
                   {if $form_data['COLISSIMO_LOGS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_WIDGET_REMOTE_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_WIDGET_REMOTE"
                   id="COLISSIMO_WIDGET_REMOTE_off"
                   {if !$form_data['COLISSIMO_WIDGET_REMOTE']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_WIDGET_REMOTE_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3">
          <div x-ms-format-detection="none" class="help-block">
            {l s='Do not forget to empty PrestaShop cache if you change this option' mod='colissimo'}
            <span></span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group">
        <label class="control-label col-lg-3 required">
          <span>{l s='Webservice URL' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <div class="fixed-width-xxl">
            <input value="{$form_data['COLISSIMO_WIDGET_ENDPOINT']|escape:'htmlall':'UTF-8'}"
                   type="text"
                   name="COLISSIMO_WIDGET_ENDPOINT"
                   class="input fixed-width-xxl"
                   required="required">
          </div>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <a class="btn btn-primary col-lg-offset-3" id="colissimo-check-widget">
        <i class="icon icon-check"></i> {l s='Check availability' mod='colissimo'}
      </a>
      <hr/>
      <div class="row">
        <div class="col-lg-9 col-lg-offset-3">
          <p class="colissimo-subtitle">{l s='Display preferences' mod='colissimo'}</p>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-3 required">
          <span>{l s='Address fields color' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <div class="input-group fixed-width-lg">
            <input type="color"
                   data-hex="true"
                   class="color mColorPickerInput"
                   name="COLISSIMO_WIDGET_COLOR_2"
                   value="{$form_data['COLISSIMO_WIDGET_COLOR_2']|escape:'htmlall':'UTF-8'}">
          </div>
        </div>
        <div class="col-lg-9 col-lg-offset-3">
          <div x-ms-format-detection="none" class="help-block">
            {l s='Sections hightlighted in orange in the example below' mod='colissimo'}
            <span></span>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-3 required">
          <span>{l s='Pickup list color' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <div class="input-group fixed-width-lg">
            <input type="color"
                   data-hex="true"
                   class="color mColorPickerInput"
                   name="COLISSIMO_WIDGET_COLOR_1"
                   value="{$form_data['COLISSIMO_WIDGET_COLOR_1']|escape:'htmlall':'UTF-8'}">
          </div>
        </div>
        <div class="col-lg-9 col-lg-offset-3">
          <div x-ms-format-detection="none" class="help-block">
            {l s='Sections hightlighted in grey in the example below' mod='colissimo'}
            <span></span>
          </div>
        </div>
      </div>
      <div class="form-group"><label class="control-label col-lg-3 ">
          <span>{l s='Text font' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <select name="COLISSIMO_WIDGET_FONT" class=" fixed-width-xxl">
            {foreach $widget_fonts as $id => $font}
              <option {if $id == $form_data['COLISSIMO_WIDGET_FONT']|escape:'htmlall':'UTF-8'}selected="selected"{/if}
                      value="{$id|intval}">
                {$font|escape:'htmlall':'UTF-8'}
              </option>
            {/foreach}
          </select>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>{l s='Display example' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <img src="{$data['img_path']|escape:'htmlall':'UTF-8'}colissimo_widget.png"/>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoWidgetConfigForm">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
      </button>
    </div>
  </div>
</form>
{literal}
<script type="text/javascript">
    var genericErrorMessage = "{/literal}{l s='Webservice connexion could not be checked' mod='colissimo'}{literal}";
    var colissimoCredentialsToken = '{/literal}{getAdminToken tab='AdminColissimoTestCredentials'}{literal}';

    $(document).ready(function () {
        $(document).on('click', '#colissimo-check-widget', function () {
            var checkWidget = $('#colissimo-check-widget');

            testWidgetCredentials(checkWidget);
        });
    });
</script>
{/literal}
