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
{if isset($gift_rules) && $gift_rules|@count gt 0}
	<div class="gifts-panel">
	{foreach from=$gift_rules item=gift key=id_mdgift_rule name=gift}
		{if !empty($gift.products)}
		<div class="gift-widget-wrapper" data-id="{$id_mdgift_rule}" data-max="{$gift.nb_product_gift}" >
			<form class="form-add-gift">
				<input class="id_product" name="id_product" type="hidden" value=""/>
				<input class="id_product_attribute" name="id_product_attribute" type="hidden" value=""/>
			</form>
			<div class="gift-widget-title">{$gift.title}</div>
			<p>{$gift.description}</p>
			<div class="gift--slider splide" id="gift-slider_{$id_mdgift_rule}">
				<div class="splide__track">
				<ul class="splide__list">
					{foreach from=$gift.products item=giftProduct name=giftProduct}
					{if !empty($giftProduct->id)}
					<li class="splide__slide">
						<label for="gp-{$id_mdgift_rule}-{$giftProduct->id}" class="item giftProduct {if isset($gifts_in_cart[$id_mdgift_rule][$giftProduct->id]['id_product'])}selected{/if}" data-product_id="{$giftProduct->id|escape:'htmlall':'UTF-8'}">
							<div class="content">
								<img src="{$giftProduct->image|escape:'html':'UTF-8'}" class="mgift_thumb" />
								<div class="gp-name align-center">{$giftProduct->name|escape:'html':'UTF-8'}</div>

								{if isset($giftProduct->selectedCombinations)}
									{if count($giftProduct->selectedCombinations) > 0}
										<select name="mgift_ipa" class="mgift_ipa">
											{foreach from=$giftProduct->selectedCombinations item=attribute_group name=attribute_group}
												{assign var="combination_img" value=$giftProduct->combinationImages[$attribute_group.id_product_attribute][0]['id_image']}
												<option {if isset($gifts_in_cart[$id_mdgift_rule][$giftProduct->id]['id_product_attribute'])
												&& $gifts_in_cart[$id_mdgift_rule][$giftProduct->id]['id_product_attribute'] == $attribute_group.id_product_attribute}selected{/if}
												data-url_image="{if $combination_img}{$link->getImageLink($giftProduct->link_rewrite, $combination_img, $image_size)}{else}{$giftProduct->image|escape:'html':'UTF-8'}{/if}" value="{$attribute_group.id_product_attribute|escape:'htmlall':'UTF-8'}">{$attribute_group.attributes|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</select>
									{/if}
								{/if}
								<input type="checkbox" class="rbutton" name="gift-radio" {if isset($gifts_in_cart[$id_mdgift_rule][$giftProduct->id]['id_product'])}checked{/if} id="gp-{$id_mdgift_rule}-{$giftProduct->id}" value="{$giftProduct->id|escape:'htmlall':'UTF-8'}"/>
							</div>
						</label>
					</li>
					{/if}
					{/foreach}
				</ul>
				</div>
			</div>
			<div class="add-gift-action">
				<a class="btn btn-primary addGiftToCart" href="javascript:void(0);">{l s='Add selected gifts' mod='mdgiftproduct'}</a>
			</div>
		</div>
		{/if}
	{/foreach}
	</div>
{/if}
