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

<div class="panel product_container">
	<div class="form-group product-item" data-id="{$rproduct->id_mdgift_rule_product}">
		<div class="row">
		
		<div class="col-lg-10">
		<div class="row row-margin-top">
			<label class="control-label col-lg-3">
				{l s='Search a product' mod='mdgiftproduct'}
			</label>
			<div class="col-lg-9">
				<div class="input-group col-lg-5">
					<input class="searchProductFilter" type="text" name="gift_products[{$rproduct->id_mdgift_rule_product}]" value="{$rproduct->product_filter}" />
					<span class="input-group-addon"><i class="icon-search"></i></span>
				</div>
			</div>
		</div>

		<div class="row row-margin-top">
			<div class="col-lg-12">
				<div class="products_found" {if !isset($rproduct->search_products) || empty($rproduct->search_products)}style="display:none;"{/if}>
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Matching products' mod='mdgiftproduct'}</label>
						<div class="col-lg-5">
							
							<select class="gift_product control-form" name="id_product[{$rproduct->id_mdgift_rule_product|intval}]">
								{if isset($rproduct->search_products) && !empty($rproduct->search_products)}
									{foreach from=$rproduct->search_products item='product_item'}
										<option {if isset($rproduct->id_product) && $rproduct->id_product == $product_item.id_product }selected{/if} value="{$product_item.id_product}">{$product_item.name}</option>
									{/foreach}
								{else}
									<option value="0" selected></option>
								{/if}
							</select>
						</div>
					</div>
					<div class="gift_product_attributes" class="form-group" {if !isset($rproduct->hasAttribute) || empty($rproduct->hasAttribute)}style="display:none;"{/if}>
						<label class="control-label col-lg-3">{l s='Available combinations' mod='mdgiftproduct'}</label>
						<div class="col-lg-5 gift_product_attributes_selection">
							{if isset($rproduct->search_products) && !empty($rproduct->search_products)}
								{foreach from=$rproduct->search_products item='product_item'}
									<div class="attributes_container" data-product="{$product_item.id_product}"
										{if !isset($product_item.combinations) || empty($product_item.combinations) || $rproduct->id_product != $product_item.id_product }style="display:none"{/if}>
									<select multiple class="multiSelect control-form ipa_product" name="id_product_attribute[{$rproduct->id_mdgift_rule_product|intval}][{$product_item.id_product}][]" >
									{foreach from=$product_item.combinations item='combination'}
										<option {if $combination.id_product_attribute|in_array:$rproduct->id_product_attribute}selected{/if} value="{$combination.id_product_attribute}">{$combination.attributes} {$combination.formatted_price}</option>
									{/foreach}
									</select>
									</div>
								{/foreach}
							{/if}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-2">
        <a class="btn btn-default remove-product" href="javascript:void(0);">
            <i class="material-icons">delete</i> {l s='Remove product' mod='mdgiftproduct'}
        </a>
    </div>
	</div>
</div>
</div>