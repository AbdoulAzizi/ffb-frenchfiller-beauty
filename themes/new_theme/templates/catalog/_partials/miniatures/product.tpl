{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='product_miniature_item'}
<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
  {if isset($position)}<meta itemprop="position" content="{$position}" />{/if}
<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemprop="item" itemscope itemtype="http://schema.org/Product">
	<div class="thumbnail-container">
		<div class="product-image">
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
						/> 
						{if isset($cfg_product_one_img) && $cfg_product_one_img}
							<span class="product-additional" data-idproduct="{$product.id_product}"></span>
						{/if}
					</a>
				{else}
					<a href="{$product.url}" class="thumbnail product-thumbnail">
				            <img src = "{$urls.no_picture_image.bySize.home_default.url}" />
					    {if isset($cfg_product_one_img) && $cfg_product_one_img}
					    	<span class="product-additional" data-idproduct="{$product.id_product}"></span>
					    {/if}
				        </a>
				{/if}	
			{/block}
			
			{include file='catalog/_partials/product-flags.tpl'}
			<div class="functional-buttons clearfix">
				{block name='quick_view'}
					<div class="quickview{if !$product.main_variants} no-variants{/if} hidden-sm-down">
						<a
						  href="#"
						  class="quick-view btn-product btn btn-primary"
						  data-link-action="quickview"
						  data-source=".thumb-gallery-{$product.id}-{$product.id_product_attribute}"
						  title="{l s='Quick view' d='Shop.Theme.Actions'}"
						>
							<span class="leo-quickview-bt-loading cssload-speeding-wheel"></span>
							<span class="leo-quickview-bt-content">
								<i class="leofal fa-eye"></i>
								<span class="name-btn-product">{l s='Quick view' d='Shop.Theme.Actions'}</span>
							</span>
						</a>
					</div>
				{/block}
				{hook h='displayLeoCompareButton' product=$product}
				{hook h='displayLeoWishlistButton' product=$product}
				{hook h='displayLeoCartButton' product=$product}
			</div>
		</div>
		<div class="product-meta">
			<div class="product-description">	

				{block name='product_name'}
			          {if !isset($page.page_name) || $page.page_name == 'index'}
			            <h3 class="h3 product-title" itemprop="name"><a href="{$product.url}" itemprop="url" content="{$product.url}">{$product.name|truncate:30:'...'}</a></h3>
			          {else}
			            <h2 class="h3 product-title" itemprop="name"><a href="{$product.url}" itemprop="url" content="{$product.url}">{$product.name|truncate:30:'...'}</a></h2>
			          {/if}
				{/block}

				{hook h='displayLeoProductListReview' product=$product}
				{block name='product_reviews'}
					{hook h='displayProductListReviews' product=$product}
				{/block}

				{block name='product_price_and_shipping'}
					{if $product.show_price}
            <div class="product-price-and-shipping">
							{if $product.has_discount}
								{hook h='displayProductPriceBlock' product=$product type="old_price"}
								<span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{$product.regular_price}</span>
								{if $product.discount_type === 'percentage'}
									<span class="discount-percentage discount-product">{$product.discount_percentage}</span>
								{elseif $product.discount_type === 'amount'}
									<span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
								{/if}
							{/if}

							{hook h='displayProductPriceBlock' product=$product type="before_price"}

							<span class="price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">{$product.price}</span>
							<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="invisible">
							  <meta itemprop="priceCurrency" content="{$currency.iso_code}" />
							  <meta itemprop="price" content="{$product.price_amount}" />
							</div>

							{hook h='displayProductPriceBlock' product=$product type='unit_price'}

							{hook h='displayProductPriceBlock' product=$product type='weight'}
						</div>
					{/if}
				{/block}
				
				{block name='product_description_short'}
					<div class="product-description-short" itemprop="description">{$product.description_short|truncate:150:'...' nofilter}</div>
				{/block}

			</div>
			<div class="highlighted-informations{if !$product.main_variants} no-variants{/if} hidden-sm-down">
				{block name='product_variants'}
					{if $product.main_variants}
						{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
					{/if}
				{/block}
			</div>
			{hook h='displayLeoCartAttribute' product=$product}
			{hook h='displayLeoCartQuantity' product=$product}
		</div>
	</div>
</article>
{/block}
