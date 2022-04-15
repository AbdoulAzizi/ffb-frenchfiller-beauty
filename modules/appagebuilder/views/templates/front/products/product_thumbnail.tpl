{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{block name='product_thumbnail'}
{if isset($cfg_product_list_image) && $cfg_product_list_image}
	<div class="leo-more-info" data-idproduct="{$product.id_product}"></div>
{/if}

{if $product.cover}
    {if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
        {* ENABLE LAZY LOAD OWL_CAROUSEL *}

	    {if $lmobile_swipe && $isMobile}
		    <div class="product-list-images-mobile">
		    	<div>
		{/if}
			    	<a href="{$product.url}" class="thumbnail product-thumbnail">
					  <img
						class="img-fluid lazyOwl"
						src = ""
						data-src = "{$product.cover.bySize.home_default.url}"
						alt = "{$product.cover.legend}"
						data-full-size-image-url = "{$product.cover.large.url}"
					  >
					  {if isset($cfg_product_one_img) && $cfg_product_one_img}
						<span class="product-additional" data-idproduct="{if $lmobile_swipe && $isMobile}0{else}{$product.id_product}{/if}"></span>
					  {/if}
					</a>
		{if $lmobile_swipe == 1 && $isMobile}			
				</div>
		    	{foreach from=$product.images item=image}
			    	{if $product.cover.bySize.home_default.url != $image.bySize.home_default.url}
			            <div>
					    	<a href="{$product.url}" class="thumbnail product-thumbnail">
			                    <img
			                      class="img-fluid thumb js-thumb {if $image.id_image == $product.cover.id_image} selected {/if}{if $aplazyload} lazy{/if}"
			                      data-src="{$image.bySize.home_default.url}"
			                      alt="{$image.legend}"
			                      title="{$image.legend}"
			                      itemprop="image"
			                    >
			                </a>
						</div>
					{/if}
				{/foreach}
			<div>
		{/if}
    {else}
    	{if $lmobile_swipe == 1 && $isMobile}
		    <div class="product-list-images-mobile">
		    	<div>
		{/if}
		    	<a href="{$product.url}" class="thumbnail product-thumbnail">
				  <img
					class="img-fluid"
					src = "{$product.cover.bySize.home_default.url}"
					alt = "{$product.cover.legend}"
					data-full-size-image-url = "{$product.cover.large.url}"
				  >
				  {if isset($cfg_product_one_img) && $cfg_product_one_img}
					<span class="product-additional" data-idproduct="{if $lmobile_swipe && $isMobile}0{else}{$product.id_product}{/if}"></span>
				  {/if}
				</a>

		{if $lmobile_swipe == 1 && $isMobile}
				</div>
		    	{foreach from=$product.images item=image}
			    	{if $product.cover.bySize.home_default.url != $image.bySize.home_default.url}
			            <div>
					    	<a href="{$product.url}" class="thumbnail product-thumbnail">
			                    <img
			                      class="thumb js-thumb img-fluid {if $image.id_image == $product.cover.id_image} selected {/if}"
			                      src="{$image.bySize.home_default.url}"
			                      alt="{$image.legend}"
			                      title="{$image.legend}"
			                      itemprop="image"
			                    >
			                </a>
						</div>	
					{/if}
				{/foreach}
			</div>
		{/if}
    {/if}
{else}
  <a href="{$product.url}" class="thumbnail product-thumbnail leo-noimage">
 <img
   {if $aplazyload}class="lazy" data-src{else}src{/if} = "{$urls.no_picture_image.bySize.home_default.url}"
 >
  </a>
{/if}
  
{/block}

