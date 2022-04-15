{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\front\products\file_tpl -->
{if isset($product.attribute)}
{foreach from=$product.attribute item=attribute key=key}
	{if $attribute}
	{if !isset($leoajax)}
	<div class="product-full-attr">
	{/if}
	    <ul class="product-attr">
	    {foreach from=$attribute item=attr}
	    	{if $attr.group_type == 'color'}
		        <li class="color product_{$attr.group_name}" style="background-color: {$attr.color};">
		            <a class="{$attr.group_name}" title="{$attr.name}" href="{$attr.url}"></a>
		        </li>
		    {else}
		    	<li class="product_{$attr.group_name} {if $attr.group_name == 'Size' && $attr.quantity == 0}Sold-Out{/if}">
		            <a class="{$attr.group_name}" title="{$attr.name}" href="{$attr.url}">{$attr.name}</a>
		        </li>
	        {/if}
	    {/foreach}
	    </ul>
	{if !isset($leoajax)}
	</div>
	{/if}
	{/if}
{/foreach}
{else}
<div class="product-item-attribute product-full-attr product-attribute-{$product.id_product}" data-idproduct="{$product.id_product}"></div>
{/if}