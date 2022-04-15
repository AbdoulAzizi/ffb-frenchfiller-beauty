{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\appagebuilder\views\templates\front\products\file_tpl -->
{block name='product_thumbnail'}
{if isset($cfg_product_list_image) && $cfg_product_list_image}
	<div class="leo-more-info" data-idproduct="{$product.id_product}"></div>
{/if}
{if $product.cover}
	<a href="{$product.url}" class="thumbnail product-thumbnail">
	  <img
	    class="img-fluid"
		src = "{$product.cover.bySize.home_default.url}"
		alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
		data-full-size-image-url = "{$product.cover.large.url}"
	  >
	  {if isset($cfg_product_one_img) && $cfg_product_one_img}
		<span class="product-additional" data-idproduct="{$product.id_product}"></span>
	  {/if}
	</a> 
{else}
	<a href="{$product.url}" class="thumbnail product-thumbnail">
            <img
              src = "{$urls.no_picture_image.bySize.home_default.url}"
            >
	    {if isset($cfg_product_one_img) && $cfg_product_one_img}
	    	<span class="product-additional" data-idproduct="{$product.id_product}"></span>
	    {/if}
    </a>
{/if}	
{/block}

