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


<div class="panel" id="gift-rule-panel"> 
	<h3 class="md-title-rule-panel"><i class="material-icons">card_giftcard</i> {l s='Gift product rule' mod='mdgiftproduct'}</h3>
	<div class="productTabs">
		<ul class="tab nav nav-tabs">
			<li class="tab-row active">
				<a class="tab-page" data-target="informations" id="gift_product_rule_link_informations" href="javascript:void(0);"><i class="material-icons">info</i> {l s='Gift rule informations' mod='mdgiftproduct'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" data-target="conditions" id="gift_product_rule_link_conditions" href="javascript:void(0);"><i class="material-icons">assignment</i> {l s='Conditions' mod='mdgiftproduct'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" data-target="products_items" id="gift_product_rule_link_products_items" href="javascript:void(0);"><i class="material-icons">card_giftcard</i> {l s='Free Gift products' mod='mdgiftproduct'}</a>
			</li>
		</ul>
	</div>
	<form action="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$currentToken|escape:'html':'UTF-8'}" id="gift_rule_form" class="form-horizontal" method="post" data-token="{$currentToken}">
		{if $currentObject->id}<input type="hidden" name="id_mdgift_rule" value="{$currentObject->id|intval}" />{/if}
		<input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
		<div id="gift_product_rule_informations" class="panel gift_product_rule_tab" style="display:block;">
			{include file="$tpl_dir/gift_product_rules/informations.tpl"}
		</div>
		<div id="gift_product_rule_conditions" class="panel gift_product_rule_tab">
			{include file="$tpl_dir/gift_product_rules/conditions.tpl"}
		</div>
		<div id="gift_product_rule_products_items" class="panel gift_product_rule_tab">
			{include file="$tpl_dir/gift_product_rules/products_items.tpl"}
		</div>
		<div class="panel-footer" id="toolbar-footer">
			<button type="submit" class="btn btn-default pull-right" name="submitAddmdgift_rule" id="{$table|escape:'html':'UTF-8'}_form_submit_btn"><i class="process-icon-save"></i> <span>{l s='Save' mod='mdgiftproduct'}</span></button>
			<a id="desc-cart_rule-cancel" class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$currentToken|escape:'html':'UTF-8'}">
				<i class="process-icon-cancel"></i> <span>Annuler</span>
			</a>
			<button type="submit" class="btn btn-default pull-right" name="submitAddmdgift_ruleAndStay">
			<i class="process-icon-save-and-stay"></i><span>{l s='Save & stay' mod='mdgiftproduct'}</span>
			</button>
		</div>
	</form>
</div>
<script>
	$('.datepicker[name="date_from"], .datepicker[name="date_to"]').datetimepicker({
        format: 'Y-m-d H:i:s',        
        step: 15
    });
</script>
