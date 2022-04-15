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

<div class="form-group condition_append condition_type_element_total_cart_amount">
                <div class="col-lg-11 col-lg-offset-1">
                    <div class="alert alert-info">
                        {l s='Apply rule only to the carts with the amount defined' mod='mdgiftproduct'}
                    </div>
                </div>
            </div>

            <div class="form-group condition_append condition_type_element_total_cart_amount">
                <div class="col-lg-11 col-lg-offset-1">
                    <div class="row">
                        <div class="col-lg-1">
                            <select name="cdt_cart_amount_operator[{$condition->id_mdgift_rule_condition|intval}]">
                                <option value="0" {if $condition->cart_amount_operator|intval == 0}selected="selected"{/if}>>=</option>
                                <option value="1" {if $condition->cart_amount_operator|intval == 1}selected="selected"{/if}>=</option>
                                <option value="2" {if $condition->cart_amount_operator|intval == 2}selected="selected"{/if}><=</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" name="cdt_cart_amount[{$condition->id_mdgift_rule_condition|intval}]" value="{$condition->cart_amount|floatval}" />
                        </div>
                        <div class="col-lg-2">
                            <select name="cdt_cart_amount_currency[{$condition->id_mdgift_rule_condition|intval}]">
                            {foreach from=$currencies item='currency'}
                                <option value="{$currency.id_currency|intval}"
                                {if $condition->cart_amount_currency|intval == $currency.id_currency
                                    || (!$condition->cart_amount_currency|intval && $currency.id_currency == $defaultCurrency)}
                                    selected="selected"
                                {/if}
                                >
                                    {$currency.iso_code|escape:'html':'UTF-8'}
                                </option>
                            {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="row row-margin-top">
                        <div class="col-lg-3">
                            <select name="cdt_cart_amount_tax[{$condition->id_mdgift_rule_condition|intval}]">
                                <option value="0" {if $condition->cart_amount_tax|intval == 0}selected="selected"{/if}>{l s='Tax excluded' mod='mdgiftproduct'}</option>
                                <option value="1" {if $condition->cart_amount_tax|intval == 1}selected="selected"{/if}>{l s='Tax included' mod='mdgiftproduct'}</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select name="cdt_cart_amount_shipping[{$condition->id_mdgift_rule_condition|intval}]">
                                <option value="0" {if $condition->cart_amount_shipping|intval == 0}selected="selected"{/if}>{l s='Shipping excluded' mod='mdgiftproduct'}</option>
                                <option value="1" {if $condition->cart_amount_shipping|intval == 1}selected="selected"{/if}>{l s='Shipping included' mod='mdgiftproduct'}</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select name="cdt_cart_amount_discount[{$condition->id_mdgift_rule_condition|intval}]">
                                <option value="0" {if $condition->cart_amount_discount|intval == 0}selected="selected"{/if}>{l s='Discounts excluded' mod='mdgiftproduct'}</option>
                                <option value="1" {if $condition->cart_amount_discount|intval == 1}selected="selected"{/if}>{l s='Discounts included' mod='mdgiftproduct'}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>