{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright    Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="wishlist">
	{if isset($wishlists) && count($wishlists) > 1}
		<div class="dropdown leo-wishlist-button-dropdown">
		  <button class="leo-wishlist-button dropdown-toggle show-list btn-primary btn-product btn{if $added_wishlist} added{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id-wishlist="{$id_wishlist}" data-id-product="{$leo_wishlist_id_product}" data-id-product-attribute="{$leo_wishlist_id_product_attribute}">
			<span class="leo-wishlist-bt-loading cssload-speeding-wheel"></span>
			<span class="leo-wishlist-bt-content">
				<i class="leofal fa-heart"></i>
				<span class="btn-title">{l s='Add to wishlist' d='Shop.Theme.Global'}</span>
			</span>
			
		  </button>
		  <div class="dropdown-menu leo-list-wishlist leo-list-wishlist-{$leo_wishlist_id_product}">
			{foreach from=$wishlists item=wishlists_item}
				<a href="javascript:void(0)" class="dropdown-item list-group-item list-group-item-action wishlist-item{if in_array($wishlists_item.id_wishlist, $wishlists_added)} added {/if}" data-id-wishlist="{$wishlists_item.id_wishlist}" data-id-product="{$leo_wishlist_id_product}" data-id-product-attribute="{$leo_wishlist_id_product_attribute}" title="{if in_array($wishlists_item.id_wishlist, $wishlists_added)}{l s='Remove from Wishlist' mod='leofeature'}{else}{l s='Add to Wishlist' mod='leofeature'}{/if}">{$wishlists_item.name}</a>			
			{/foreach}
		  </div>
		</div>
	{else}
		<a class="leo-wishlist-button btn-product btn-primary btn{if $added_wishlist} added{/if}" href="javascript:void(0)" data-id-wishlist="{$id_wishlist}" data-id-product="{$leo_wishlist_id_product}" data-id-product-attribute="{$leo_wishlist_id_product_attribute}" title="{if $added_wishlist}{l s='Remove from Wishlist' mod='leofeature'}{else}{l s='Add to Wishlist' mod='leofeature'}{/if}">
			<span class="leo-wishlist-bt-loading cssload-speeding-wheel"></span>
			<span class="leo-wishlist-bt-content">
				<i class="leofal fa-heart"></i>
				<span class="btn-title">{l s='Add to wishlist' d='Shop.Theme.Global'}</span>
			</span>
		</a>
	{/if}
</div>