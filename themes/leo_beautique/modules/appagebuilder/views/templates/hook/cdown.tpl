{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}

{if $product}
<ul class="deal-clock lof-clock-{$product.id_product|intval}-detail list-inline">
	{if isset($product.js) && $product.js == 'unlimited'}<div class="lof-labelexpired">{l s='Unlimited' d='Shop.Theme.Global'}</div>{/if}
</ul>
{if isset($product.js) && $product.js != 'unlimited'}
	<script type="text/javascript">
		{literal}
		jQuery(document).ready(function($){{/literal}
			var text_d = '{l s='days' d='Shop.Theme.Global'}';
			var text_h = '{l s='hours' d='Shop.Theme.Global'}';
			var text_m = '{l s='min' d='Shop.Theme.Global'}';
			var text_s = '{l s='sec' d='Shop.Theme.Global'}';
			$(".lof-clock-{$product.id_product|intval}-detail").lofCountDown({literal}{{/literal}
				TargetDate:"{$product.js.month|escape:'html':'UTF-8'}/{$product.js.day|escape:'html':'UTF-8'}/{$product.js.year|escape:'html':'UTF-8'} {$product.js.hour|escape:'html':'UTF-8'}:{$product.js.minute|escape:'html':'UTF-8'}:{$product.js.seconds|escape:'html':'UTF-8'}",
				DisplayFormat:'<li class="z-depth-1">%%D%%<span>'+text_d+'</span></li><li class="z-depth-1">%%H%%<span>'+text_h+'</span></li><li class="z-depth-1">%%M%%<span>'+text_m+'</span></li><li class="z-depth-1">%%S%%<span>'+text_s+'</span></li>',
				FinishMessage: "{$product.finish|escape:'html':'UTF-8'}"
			{literal}
			});
		});
		{/literal}
	</script>
{/if}
{/if}
