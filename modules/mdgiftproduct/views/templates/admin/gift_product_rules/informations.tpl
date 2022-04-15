{**
* 2020-2021
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
*  @author Digincube
*  @copyright 2020-2021
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="form-group">
    <label class="control-label col-lg-3">{l s='Enabled' mod='mdgiftproduct'}</label>
    <div class="col-lg-9">
        <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
            <label class="t" for="active_on">{l s='Yes' mod='mdgiftproduct'}</label>
            <input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
            <label class="t" for="active_off">{l s='No' mod='mdgiftproduct'}</label>
            <a class="slide-button btn"></a>
        </span>
    </div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3 required">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='This will be displayed in the cart summary, as well as on the invoice.' mod='mdgiftproduct'}">
			{l s='Name' mod='mdgiftproduct'}
		</span>
	</label>
	
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-8">
                {foreach from=$languages item=language}
                    {if $languages|count > 1}
                    <div class="row">
                        <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $display_language}style="display:none"{/if}>
                            <div class="col-lg-9">
                                {/if}
                                <input id="name_{$language.id_lang|intval}" type="text"  name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:'html':'UTF-8'}">
                                {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    {$language.iso_code|escape:'html':'UTF-8'}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='This title will be displayed in gift section.' mod='mdgiftproduct'}">
			{l s='Gift section title' mod='mdgiftproduct'}
		</span>
	</label>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-8">
                {foreach from=$languages item=language}
                    {if $languages|count > 1}
                    <div class="row">
                        <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $display_language}style="display:none"{/if}>
                            <div class="col-lg-9">
                                {/if}
                                <input id="name_{$language.id_lang|intval}" type="text"  name="title_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'title', $language.id_lang|intval)|escape:'html':'UTF-8'}">
                                {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    {$language.iso_code|escape:'html':'UTF-8'}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='This description will be displayed in gift section.' mod='mdgiftproduct'}">
			{l s='Description' mod='mdgiftproduct'}
		</span>
	</label>
	
    <div class="col-lg-9">
			{foreach from=$languages item=language}
                    {if $languages|count > 1}
                    <div class="row">
                        <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $display_language}style="display:none"{/if}>
                            <div class="col-lg-9">
                                {/if}
								<textarea name="description_{$language.id_lang|intval}" rows="2" class="textarea-autosize">{$currentTab->getFieldValue($currentObject, 'description', $language.id_lang|intval)|escape:'html':'UTF-8'}</textarea>
								{if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    {$language.iso_code|escape:'html':'UTF-8'}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                    {/if}
          {/foreach}
    </div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='The default period is one month.' mod='mdgiftproduct'}">
			{l s='Valid' mod='mdgiftproduct'}
		</span>
	</label>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">{l s='From' mod='mdgiftproduct'}</span>
                    <input type="text" class="datepicker input-medium" name="date_from"
                    value="{if $currentTab->getFieldValue($currentObject, 'date_from')}{$currentTab->getFieldValue($currentObject, 'date_from')|escape:'html':'UTF-8'}{else}{$defaultDateFrom|escape:'html':'UTF-8'}{/if}" />
                    <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">{l s='To' mod='mdgiftproduct'}</span>
                    <input type="text" class="datepicker input-medium" name="date_to"
                    value="{if $currentTab->getFieldValue($currentObject, 'date_to')}{$currentTab->getFieldValue($currentObject, 'date_to')|escape:'html':'UTF-8'}{else}{$defaultDateTo|escape:'html':'UTF-8'}{/if}" />
                    <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='The cart rule will be applied to the first X customers only.' mod='mdgiftproduct'}">
			{l s='Total available' mod='mdgiftproduct'}
		</span>
	</label>
	
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-1">
                <input type="text" class="input-mini" name="quantity" value="{$currentTab->getFieldValue($currentObject, 'quantity')|intval}" />
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-lg-3">
        <span>
            {l s='Times used' mod='mdgiftproduct'}
        </span>
    </label>
    <div class="col-lg-9">
        <label class="control-label" style="border:1px solid #bbcdd2;padding:6px 9px;width:50px;text-align:left;margin-right:3px;background:#e9e9e9;">{$times_used|intval}</label> <span>fois</span>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-3">
		<span>
			{l s='Rule cumulative with other regular cart rule in the cart ?' mod='mdgiftproduct'}
		</span>
    </label>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="compatible_cart_rules" id="compatible_cart_rules_on" value="1" {if $currentTab->getFieldValue($currentObject, 'compatible_cart_rules')|intval || !$currentObject->id}checked="checked"{/if} />
                    <label class="t" for="compatible_cart_rules_on">{l s='Yes' mod='mdgiftproduct'}</label>
                    <input type="radio" name="compatible_cart_rules" id="compatible_cart_rules_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'compatible_cart_rules')|intval && $currentObject->id}checked="checked"{/if} />
                    <label class="t" for="compatible_cart_rules_off">{l s='No' mod='mdgiftproduct'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="control-label col-lg-3">
        {l s='Apply this gift rule to products that already discounted by the previous rules' mod='mdgiftproduct'}
    </label>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="apply_products_already_discounted" id="apply_products_already_discounted_on" value="1" {if $currentTab->getFieldValue($currentObject, 'apply_products_already_discounted')|intval || !$currentObject->id}checked="checked"{/if} />
                    <label class="t" for="apply_products_already_discounted_on">{l s='Yes' mod='mdgiftproduct'}</label>
                    <input type="radio" name="apply_products_already_discounted" id="apply_products_already_discounted_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'apply_products_already_discounted')|intval && $currentObject->id}checked="checked"{/if} />
                    <label class="t" for="apply_products_already_discounted_off">{l s='No' mod='mdgiftproduct'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    </div>
</div>