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

<div id="condition_{$condition->id_mdgift_rule_condition|intval}_container" class="col-lg-12 condition panel">
    <div class="row">
        
        <div class="col-lg-10">
            <div class="form-group">
                <label class="control-label col-lg-1"><span>{l s='Condition' mod='mdgiftproduct'}</span></label>
                <div class="col-lg-11">
                    <select name="cdt_condition_type[{$condition->id_mdgift_rule_condition|intval}]" class="condition_condition_type" >
                        <option value="">{l s='-- Choose --' mod='mdgiftproduct'}</option>
                        <optgroup label="{l s='Cart' mod='mdgiftproduct'}">
                            <option value="total_cart_amount" {if isset($condition->condition_type) && $condition->condition_type == "total_cart_amount"}selected="selected"{/if}>{l s='Total cart amount' mod='mdgiftproduct'}</option>
                            <option value="cart_weight" {if isset($condition->condition_type) && $condition->condition_type == "cart_weight"}selected="selected"{/if}>{l s='Cart weight' mod='mdgiftproduct'}</option>
                            <option value="products_cart" {if isset($condition->condition_type) && $condition->condition_type == "products_cart"}selected="selected"{/if}>{l s='Products in the cart' mod='mdgiftproduct'}</option>
                        </optgroup>
						 <optgroup label="{l s='Customer' mod='mdgiftproduct'}">
							<option value="customer_single" {if isset($condition->condition_type) && $condition->condition_type == "customer_single"}selected="selected"{/if}>{l s='Only one customer' mod='mdgiftproduct'}</option>
							<option value="customer_group" {if isset($condition->condition_type) && $condition->condition_type == "customer_group"}selected="selected"{/if}>{l s='Customer group' mod='mdgiftproduct'}</option>
                            <option value="customer_gender" {if isset($condition->condition_type) && $condition->condition_type == "customer_gender"}selected="selected"{/if}>{l s='Customer gender' mod='mdgiftproduct'}</option>
							<option value="customer_birthday" {if isset($condition->condition_type) && $condition->condition_type == "customer_birthday"}selected="selected"{/if}>{l s='Customer birthday' mod='mdgiftproduct'}</option>
                            <option value="customer_age" {if isset($condition->condition_type) && $condition->condition_type == "customer_age"}selected="selected"{/if}>{l s='Customer age' mod='mdgiftproduct'}</option>
                        </optgroup>
						<optgroup label="{l s='Day' mod='mdgiftproduct'}">
                            <option value="day_week" {if isset($condition->condition_type) && $condition->condition_type == "day_week"}selected="selected"{/if}>{l s='Day in week' mod='mdgiftproduct'}</option>
                        </optgroup>
                    </select>
                </div>
            </div>
			{include file="$tpl_dir/gift_product_rules/_partials/condition_product_cart.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_total_cart_amount.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_cart_weight.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_customer_group.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_customer_gender.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_customer_age.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_day_week.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_customer_birthday.tpl"}
			{include file="$tpl_dir/gift_product_rules/_partials/condition_signle_customer.tpl"}
	   </div>
		<div class="col-lg-2">
			<a class="btn btn-default remove-condition" href="javascript:void(0);">
                <i class="material-icons">delete</i> {l s='Delete condition' mod='mdgiftproduct'}
            </a>
        </div>
    </div>
</div>
