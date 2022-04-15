{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright    Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="compare">
	<a class="leo-compare-button btn-primary btn-product btn{if $added} added{/if}" href="javascript:void(0)" data-id-product="{$leo_compare_id_product}" title="{if $added}{l s='Remove from Compare' mod='leofeature'}{else}{l s='Add to Compare' mod='leofeature'}{/if}">
		<span class="leo-compare-bt-loading cssload-speeding-wheel"></span>
		<span class="leo-compare-bt-content">
			<i class="leofal leofa-sync"></i>
			<span class="btn-title">{l s='Add to compare' d='Shop.Theme.Global'}</span>
		</span>
	</a>
</div>