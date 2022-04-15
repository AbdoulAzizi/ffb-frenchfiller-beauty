{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<div class="col-md-12">
	<h2>{l s='Home' mod='appagebuilder'}</h2>
	<ul>
		{if isset($leo_profiles) && !empty($leo_profiles)}
			{foreach from=$leo_profiles item=leo_profile}
				<li>
					<a href="{$leo_profile.url}">{$leo_profile.name}</a>
				</li>
			{/foreach}
		{/if}
	</ul>
</div>