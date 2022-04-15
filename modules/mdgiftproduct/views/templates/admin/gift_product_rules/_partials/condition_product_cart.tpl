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

<div class="form-group condition_append condition_type_element_products_cart">
    <div class="col-lg-11 col-lg-offset-1">
        <div class="row">
            <div class="col-lg-1">
                <select name="cdt_products_operator[{$condition->id_mdgift_rule_condition|intval}]">
                    <option value="0" {if $condition->products_operator|intval == 0}selected="selected"{/if}>>=</option>
                    <option value="1" {if $condition->products_operator|intval == 1}selected="selected"{/if}>=</option>
                    <option value="2" {if $condition->products_operator|intval == 2}selected="selected"{/if}><=</option>
                </select>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">{l s='Amount of selected products in cart' mod='mdgiftproduct'}</span>
                    <input type="text" name="cdt_products_amount[{$condition->id_mdgift_rule_condition|intval}]" value="{$condition->products_amount|floatval}" />
                </div>
            </div>
            <div class="col-lg-2">
                <select name="cdt_products_amount_currency[{$condition->id_mdgift_rule_condition|intval}]">
                {foreach from=$currencies item='currency'}
                    <option value="{$currency.id_currency|intval}"
                    {if $condition->products_amount_currency|intval == $currency.id_currency
                        || (!$condition->products_amount_currency|intval && $currency.id_currency == $defaultCurrency)}
                        selected="selected"
                    {/if}
                    >
                        {$currency.iso_code|escape:'html':'UTF-8'}
                    </option>
                {/foreach}
                </select>
            </div>
            <div class="col-lg-3">
                <select name="cdt_products_amount_tax[{$condition->id_mdgift_rule_condition|intval}]">
                    <option value="0" {if $condition->products_amount_tax|intval == 0}selected="selected"{/if}>{l s='Tax excluded' mod='mdgiftproduct'}</option>
                    <option value="1" {if $condition->products_amount_tax|intval == 1}selected="selected"{/if}>{l s='Tax included' mod='mdgiftproduct'}</option>
                </select>
            </div>
            <div class="col-lg-12 help-block">{l s='If cart amount is higher, lower or equal to the amount defined, rule will be applied' mod='mdgiftproduct'}</div>
        </div>
        
    </div>
</div>

<div class="form-group condition_append condition_type_options_8 condition_type_element_products_cart">
    <div class="col-lg-11 col-lg-offset-1">
        <div class="row">
            <div class="col-lg-5">
                <label class="control-label">{l s='Consider products with special price' mod='mdgiftproduct'}</label>
            </div>
             <div class="col-lg-7">
                <div class="input-group">
                    <span class="switch prestashop-switch fixed-width-lg" id="condition_apply_discount_to_special_{$condition->id_mdgift_rule_condition|intval}">
                        <input type="radio" name="cdt_apply_discount_to_special[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_apply_discount_to_special_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->apply_discount_to_special|intval}checked="checked"{/if} />
                        <label class="t" for="cdt_apply_discount_to_special_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                        <input type="radio" name="cdt_apply_discount_to_special[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_apply_discount_to_special_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->apply_discount_to_special|intval}checked="checked"{/if}/>
                        <label class="t" for="cdt_apply_discount_to_special_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
                <div class="help-block">{l s='If disabled, products with special price will be discarded' mod='mdgiftproduct'}</div>
            </div>
        </div>
    </div>
</div>

<div class="form-group condition_append condition_type_element_products_cart">
    <div class="col-lg-11 col-lg-offset-1">
        <p class="inline-label condition_type_options_4">{l s='- With the selected products bought -' mod='mdgiftproduct'}</p>
        <div class="panel">
            <div class="panel-heading">{l s='Product filters' mod='mdgiftproduct'}</div>
            {if isset($condition->product) && (($condition->product.unselected|@count) + ($condition->product.selected|@count)) > 0}
                <div class="row row-margin-top restriction-row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <label class="control-label">{l s='Filter by product' mod='mdgiftproduct'}</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_product_{$condition->id_mdgift_rule_condition|intval}">
                                        <input type="radio" name="cdt_restriction_product[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_product_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_product|intval}checked="checked"{/if} />
                                        <label class="t" for="cdt_restriction_product_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                        <input type="radio" name="cdt_restriction_product[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_product_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_product|intval}checked="checked"{/if}/>
                                        <label class="t" for="cdt_restriction_product_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 row-margin-top filter-container">
                        <div class="row">
                            <div id="restriction_product_div" class="row">
                                <table class="table">
                                    <tr class="row-select-unselect">
                                        <td class="col-xs-6 col-unselected">
                                            <p>{l s='Unselected products' mod='mdgiftproduct'}</p>
                                            <div class="input-group">
                                                <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                                <input type="text" class="search_select" id="search_unselected_product_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                            </div>
                                            <select id="unselected_product_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
                                            </select>
                                            <script type="text/javascript">
                                                {if !$ajax}
												{literal}
												$(window).load(function() {
												{/literal}
												{/if}
                                                    var unselected_product_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->product.unselected|json_encode};
													var unselected_product_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                    for (var i = 0; i < unselected_product_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                        unselected_product_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + unselected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].id_product + "' > " + unselected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].name + "  (ID: "+unselected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].id_product+(unselected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].reference ? " - Reference: "+unselected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].reference : "")+")</option>";
                                                    }
                                                    $("#unselected_product_{$condition->id_mdgift_rule_condition|intval}").html(unselected_product_{$condition->id_mdgift_rule_condition|intval}_options);
                                                    $('#unselected_product_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_product_{$condition->id_mdgift_rule_condition|intval}', true);
                                                {if !$ajax}{literal}});{/literal}{/if}
                                            </script>
                                            <a id="product_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
                                        </td>
                                        <td class="col-xs-6 col-selected">
                                            <p>{l s='Selected products' mod='mdgiftproduct'}</p>
                                            <input type="hidden" name="cdt_old_product[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->product.old_selected}"/>
											<div class="input-group">
                                                <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                                <input type="text" class="search_select" id="search_selected_product_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                            </div>
                                            <select name="cdt_selected_product[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_product_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
                                            </select>
                                            <script type="text/javascript">
                                                {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                    var selected_product_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->product.selected|json_encode};
                                                    console.log(selected_product_{$condition->id_mdgift_rule_condition|intval}_values);
													var selected_product_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                    for (var i = 0; i < selected_product_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                        selected_product_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + selected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].id_product + "' > " + selected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].name + "  (ID: "+selected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].id_product+(selected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].reference ? " - Reference: "+selected_product_{$condition->id_mdgift_rule_condition|intval}_values[i].reference : "")+")</option>";
                                                    }
                                                    $("#selected_product_{$condition->id_mdgift_rule_condition|intval}").html(selected_product_{$condition->id_mdgift_rule_condition|intval}_options);
                                                    $('#selected_product_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_product_{$condition->id_mdgift_rule_condition|intval}', true);
                                                {if !$ajax}{literal}});{/literal}{/if}
                                            </script>
                                            <a id="product_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}

            {if isset($condition->attribute) && (($condition->attribute.unselected|@count) + ($condition->attribute.selected|@count)) > 0}
                <div class="row row-margin-top restriction-row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <label class="control-label">{l s='Filter by attribute' mod='mdgiftproduct'}</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_attribute_{$condition->id_mdgift_rule_condition|intval}">
                                        <input type="radio" name="cdt_restriction_attribute[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_attribute_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_attribute|intval}checked="checked"{/if} />
                                        <label class="t" for="cdt_restriction_attribute_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                        <input type="radio" name="cdt_restriction_attribute[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_attribute_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_attribute|intval}checked="checked"{/if}/>
                                        <label class="t" for="cdt_restriction_attribute_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 row-margin-top filter-container">
                        <div class="row" id="restriction_attribute_div">
                            <table class="table">
                                <tr class="row-select-unselect">
                                    <td class="col-xs-6">
                                        <p>{l s='Unselected attributes' mod='mdgiftproduct'}</p>
                                        <div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_unselected_attribute_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select id="unselected_attribute_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                var unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->attribute.unselected|json_encode};
                                                var unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                for (var i = 0; i < unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                    unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_values[i].id_attribute + "' > " + unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_values[i].name + "  (ID: "+unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_values[i].id_attribute+")</option>";
                                                }
                                                $("#unselected_attribute_{$condition->id_mdgift_rule_condition|intval}").html(unselected_attribute_{$condition->id_mdgift_rule_condition|intval}_options);
                                                $('#unselected_attribute_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_attribute_{$condition->id_mdgift_rule_condition|intval}', true);
                                            {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="attribute_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
                                    </td>
                                    <td class="col-xs-6">
                                        <p>{l s='Selected attributes' mod='mdgiftproduct'}</p>
 										<input type="hidden" name="cdt_old_attribute[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->attribute.old_selected}"/>
										<div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_attribute_select_2_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select name="cdt_selected_attribute[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_attribute_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                var selected_attribute_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->attribute.selected|json_encode};
                                                var selected_attribute_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                for (var i = 0; i < selected_attribute_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                    selected_attribute_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + selected_attribute_{$condition->id_mdgift_rule_condition|intval}_values[i].id_attribute + "' > " + selected_attribute_{$condition->id_mdgift_rule_condition|intval}_values[i].name + "  (ID: "+selected_attribute_{$condition->id_mdgift_rule_condition|intval}_values[i].id_attribute+")</option>";
                                                }
                                                $("#selected_attribute_{$condition->id_mdgift_rule_condition|intval}").html(selected_attribute_{$condition->id_mdgift_rule_condition|intval}_options);
                                                $('#selected_attribute_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_attribute_{$condition->id_mdgift_rule_condition|intval}', true);
                                            {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="attribute_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}

            {if isset($condition->feature) && (($condition->feature.unselected|@count) + ($condition->feature.selected|@count)) > 0}
                <div class="row row-margin-top restriction-row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <label class="control-label">{l s='Filter by feature' mod='mdgiftproduct'}</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_feature_{$condition->id_mdgift_rule_condition|intval}">
                                        <input type="radio" name="cdt_restriction_feature[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_feature_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_feature|intval}checked="checked"{/if} />
                                        <label class="t" for="cdt_restriction_feature_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                        <input type="radio" name="cdt_restriction_feature[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_feature_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_feature|intval}checked="checked"{/if}/>
                                        <label class="t" for="cdt_restriction_feature_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 row-margin-top filter-container">
                        <div class="row" id="restriction_feature_div">
                            <table class="table">
                                <tr class="row-select-unselect">
                                    <td class="col-xs-6">
                                        <p>{l s='Unselected features' mod='mdgiftproduct'}</p>
                                        <div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_unselected_feature_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select id="unselected_feature_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                var unselected_feature_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->feature.unselected|json_encode};
                                                var unselected_feature_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                for (var i = 0; i < unselected_feature_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                    unselected_feature_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + unselected_feature_{$condition->id_mdgift_rule_condition|intval}_values[i].id_feature + "' > " + unselected_feature_{$condition->id_mdgift_rule_condition|intval}_values[i].name + " (ID: " + unselected_feature_{$condition->id_mdgift_rule_condition|intval}_values[i].id_feature + ")</option>";
                                                }
                                                $("#unselected_feature_{$condition->id_mdgift_rule_condition|intval}").html(unselected_feature_{$condition->id_mdgift_rule_condition|intval}_options);
                                                $('#unselected_feature_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_feature_{$condition->id_mdgift_rule_condition|intval}', true);
                                            {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="feature_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
                                    </td>
                                    <td class="col-xs-6">
                                        <p>{l s='Selected features' mod='mdgiftproduct'}</p>
 										<input type="hidden" name="cdt_old_feature[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->feature.old_selected}"/>
										<div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_selected_feature_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select name="cdt_selected_feature[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_feature_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                var selected_feature_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->feature.selected|json_encode};
                                                var selected_feature_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                for (var i = 0; i < selected_feature_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                    selected_feature_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + selected_feature_{$condition->id_mdgift_rule_condition|intval}_values[i].id_feature + "' > " + selected_feature_{$condition->id_mdgift_rule_condition|intval}_values[i].name + " (ID: " + selected_feature_{$condition->id_mdgift_rule_condition|intval}_values[i].id_feature + ")</option>";
                                                }
                                                $("#selected_feature_{$condition->id_mdgift_rule_condition|intval}").html(selected_feature_{$condition->id_mdgift_rule_condition|intval}_options);
                                                $('#selected_feature_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_feature_{$condition->id_mdgift_rule_condition|intval}', true);
                                            {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="feature_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}

            {if isset($condition->category) && (($condition->category.unselected|@count) + ($condition->category.selected|@count)) > 0}
                <div class="row row-margin-top restriction-row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <label class="control-label">{l s='Filter by category' mod='mdgiftproduct'}</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_category_{$condition->id_mdgift_rule_condition|intval}">
                                        <input type="radio" name="cdt_restriction_category[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_category_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_category|intval}checked="checked"{/if} />
                                        <label class="t" for="cdt_restriction_category_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                        <input type="radio" name="cdt_restriction_category[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_category_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_category|intval}checked="checked"{/if}/>
                                        <label class="t" for="cdt_restriction_category_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 row-margin-top filter-container">
                        <div class="row" id="restriction_category_div">
                            <table class="table">
                                <tr class="row-select-unselect">
                                    <td class="col-xs-6">
                                        <p>{l s='Unselected categories' mod='mdgiftproduct'}</p>
                                        <div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_unselected_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select id="unselected_category_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                var unselected_category_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->category.unselected|json_encode};
                                                var unselected_category_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                for (var i = 0; i < unselected_category_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                    unselected_category_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + unselected_category_{$condition->id_mdgift_rule_condition|intval}_values[i].id_category + "' > " + unselected_category_{$condition->id_mdgift_rule_condition|intval}_values[i].name + " (ID: " + unselected_category_{$condition->id_mdgift_rule_condition|intval}_values[i].id_category + ")</option>";
                                                }
                                                $("#unselected_category_{$condition->id_mdgift_rule_condition|intval}").html(unselected_category_{$condition->id_mdgift_rule_condition|intval}_options);
                                                $('#unselected_category_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_category_{$condition->id_mdgift_rule_condition|intval}', true);
                                            {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="category_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
                                    </td>
                                    <td class="col-xs-6">
                                        <p>{l s='Selected categories' mod='mdgiftproduct'}</p>
 										<input type="hidden" name="cdt_old_category[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->category.old_selected}"/>
										<div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_selected_category_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select name="cdt_selected_category[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_category_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                var selected_category_{$condition->id_mdgift_rule_condition|intval}_values = {$condition->category.selected|json_encode};
                                                var selected_category_{$condition->id_mdgift_rule_condition|intval}_options = '';
                                                for (var i = 0; i < selected_category_{$condition->id_mdgift_rule_condition|intval}_values.length; i++) {
                                                    selected_category_{$condition->id_mdgift_rule_condition|intval}_options += "<option value='" + selected_category_{$condition->id_mdgift_rule_condition|intval}_values[i].id_category + "' > " + selected_category_{$condition->id_mdgift_rule_condition|intval}_values[i].name + " (ID: " + selected_category_{$condition->id_mdgift_rule_condition|intval}_values[i].id_category + ")</option>";
                                                }
                                                $("#selected_category_{$condition->id_mdgift_rule_condition|intval}").html(selected_category_{$condition->id_mdgift_rule_condition|intval}_options);
                                                $('#selected_category_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_category_{$condition->id_mdgift_rule_condition|intval}', true);
                                            {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="category_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            {/if}

            {if isset($condition->supplier) && (($condition->supplier.unselected|@count) + ($condition->supplier.selected|@count)) > 0}
                <div class="row row-margin-top restriction-row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <label class="control-label">{l s='Filter by supplier' mod='mdgiftproduct'}</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_supplier{$condition->id_mdgift_rule_condition|intval}">
                                        <input type="radio" name="cdt_restriction_supplier[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_supplier_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_supplier|intval}checked="checked"{/if} />
                                        <label class="t" for="cdt_restriction_supplier_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                        <input type="radio" name="cdt_restriction_supplier[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_supplier_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_supplier|intval}checked="checked"{/if}/>
                                        <label class="t" for="cdt_restriction_supplier_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 row-margin-top filter-container">
                        <div class="row" id="restriction_supplier_div">
                            <table class="table">
                                <tr class="row-select-unselect">
                                    <td class="col-xs-6">
                                        <p>{l s='Unselected suppliers' mod='mdgiftproduct'}</p>
                                        <div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_unselected_supplier_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select id="unselected_supplier_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
                                            {foreach from=$condition->supplier.unselected item='supplier'}
                                                <option value="{$supplier.id_supplier|intval}">{$supplier.name|escape:'html':'UTF-8'} (ID: {$supplier.id_supplier|escape:'html':'UTF-8'})</option>
                                            {/foreach}
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                $('#unselected_supplier_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_supplier_{$condition->id_mdgift_rule_condition|intval}', true);
                                                {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="supplier_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
                                    </td>
                                    <td class="col-xs-6">
                                        <p>{l s='Selected suppliers' mod='mdgiftproduct'}</p>
 										<input type="hidden" name="cdt_old_supplier[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->supplier.old_selected}"/>
										<div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_selected_supplier_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select name="cdt_selected_supplier[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_supplier_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
                                            {foreach from=$condition->supplier.selected item='supplier'}
                                                <option value="{$supplier.id_supplier|intval}">{$supplier.name|escape:'html':'UTF-8'} (ID: {$supplier.id_supplier|escape:'html':'UTF-8'})</option>
                                            {/foreach}
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                $('#selected_supplier_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_supplier_{$condition->id_mdgift_rule_condition|intval}', true);
                                                {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="supplier_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}

            {if isset($condition->manufacturer) && (($condition->manufacturer.unselected|@count) + ($condition->manufacturer.selected|@count)) > 0}
                <div class="row row-margin-top restriction-row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <label class="control-label">{l s='Filter by manufacturer' mod='mdgiftproduct'}</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_manufacturer_{$condition->id_mdgift_rule_condition|intval}">
                                        <input type="radio" name="cdt_restriction_manufacturer[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_manufacturer_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_manufacturer|intval}checked="checked"{/if} />
                                        <label class="t" for="cdt_restriction_manufacturer_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                        <input type="radio" name="cdt_restriction_manufacturer[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_manufacturer_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_manufacturer|intval}checked="checked"{/if}/>
                                        <label class="t" for="cdt_restriction_manufacturer_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                        <a class="slide-button btn"></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 row-margin-top filter-container">
                        <div class="row" id="restriction_manufacturer_div">
                            <table class="table">
                                <tr class="row-select-unselect">
                                    <td class="col-xs-6">
                                        <p>{l s='Unselected manufacturers' mod='mdgiftproduct'}</p>
                                        <div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_unselected_manufacturer_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select id="unselected_manufacturer_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
                                            {foreach from=$condition->manufacturer.unselected item='manufacturer'}
                                                <option value="{$manufacturer.id_manufacturer|intval}">{$manufacturer.name|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                $('#unselected_manufacturer_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_manufacturer_{$condition->id_mdgift_rule_condition|intval}', true);
                                                {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="manufacturer_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
                                    </td>
                                    <td class="col-xs-6">
                                        <p>{l s='Selected manufacturers' mod='mdgiftproduct'}</p>
 										<input type="hidden" name="cdt_old_manufacturer[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->manufacturer.old_selected}"/>
										<div class="input-group">
                                            <span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
                                            <input type="text" class="search_select" id="search_selected_manufacturer_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
                                        </div>
                                        <select name="cdt_selected_manufacturer[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_manufacturer_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
                                            {foreach from=$condition->manufacturer.selected item='manufacturer'}
                                                <option value="{$manufacturer.id_manufacturer|intval}">{$manufacturer.name|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                        <script type="text/javascript">
                                            {if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
                                                $('#selected_manufacturer_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_manufacturer_{$condition->id_mdgift_rule_condition|intval}', true);
                                                {if !$ajax}{literal}});{/literal}{/if}
                                        </script>
                                        <a id="manufacturer_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}

            <div class="row row-margin-top restriction-row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-5">
                            <label class="control-label">{l s='Filter by price' mod='mdgiftproduct'}</label>
                        </div>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <span class="switch prestashop-switch fixed-width-lg" id="cdt_restriction_price_{$condition->id_mdgift_rule_condition|intval}">
                                    <input type="radio" name="cdt_restriction_price[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_price_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->restriction_price|intval}checked="checked"{/if} />
                                    <label class="t" for="cdt_restriction_price_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                    <input type="radio" name="cdt_restriction_price[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_restriction_price_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->restriction_price|intval}checked="checked"{/if}/>
                                    <label class="t" for="cdt_restriction_price_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                    <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 row-margin-top filter-container">
                    <div class="row">
                        <div class="col-lg-offset-1 col-lg-4">
                            <label class="control-label">{l s='From' mod='mdgiftproduct'}</label>
                        </div>
                        <div class="col-lg-2">
                            <input type="text" name="cdt_product_price_from[{$condition->id_mdgift_rule_condition|intval}]" value="{$condition->product_price_from|floatval}" />
                        </div>
                        <div class="col-lg-2">
                            <select name="cdt_product_price_from_currency[{$condition->id_mdgift_rule_condition|intval}]" data-group="currency2[{$condition->id_mdgift_rule_condition|intval}]">
                                {foreach from=$currencies item='currency'}
                                    <option value="{$currency.id_currency|intval}"
                                        {if $condition->product_price_from_currency|intval == $currency.id_currency
                                        || (!$condition->product_price_from_currency|intval && $currency.id_currency == $defaultCurrency)}
                                            selected="selected"
                                        {/if}
                                    >
                                        {$currency.iso_code|escape:'html':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select name="cdt_product_price_from_tax[{$condition->id_mdgift_rule_condition|intval}]" data-group="tax2[{$condition->id_mdgift_rule_condition|intval}]">
                                <option value="0" {if $condition->product_price_from_tax|intval == 0}selected="selected"{/if}>{l s='Tax excluded' mod='mdgiftproduct'}</option>
                                <option value="1" {if $condition->product_price_from_tax|intval == 1}selected="selected"{/if}>{l s='Tax included' mod='mdgiftproduct'}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row row-margin-top">
                        <div class="col-lg-offset-1 col-lg-4">
                            <label class="control-label">{l s='To' mod='mdgiftproduct'}</label>
                        </div>
                        <div class="col-lg-2">
                            <input type="text" name="cdt_product_price_to[{$condition->id_mdgift_rule_condition|intval}]" value="{$condition->product_price_to|floatval}" />
                        </div>
                        <div class="col-lg-2">
                            <select name="cdt_product_price_to_currency[{$condition->id_mdgift_rule_condition|intval}]" data-group="currency2[{$condition->id_mdgift_rule_condition|intval}]">
                                {foreach from=$currencies item='currency'}
                                    <option value="{$currency.id_currency|intval}"
                                        {if $condition->product_price_to_currency|intval == $currency.id_currency
                                        || (!$condition->product_price_to_currency|intval && $currency.id_currency == $defaultCurrency)}
                                            selected="selected"
                                        {/if}
                                    >
                                        {$currency.iso_code|escape:'html':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select name="cdt_product_price_to_tax[{$condition->id_mdgift_rule_condition|intval}]" data-group="tax2[{$condition->id_mdgift_rule_condition|intval}]">
                                <option value="0" {if $condition->product_price_to_tax|intval == 0}selected="selected"{/if}>{l s='Tax excluded' mod='mdgiftproduct'}</option>
                                <option value="1" {if $condition->product_price_to_tax|intval == 1}selected="selected"{/if}>{l s='Tax included' mod='mdgiftproduct'}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>