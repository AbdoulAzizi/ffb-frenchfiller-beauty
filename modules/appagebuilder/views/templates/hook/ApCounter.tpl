{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
<!-- @file modules\appagebuilder\views\templates\hook\Apcounter -->
<div class="block Apcounter {(isset($formAtts.widget_class)) ? $formAtts.widget_class : ''|escape:'html':'UTF-8'}">
	{if isset($formAtts.widget_title) && !empty($formAtts.widget_title)}
        <h4 class="title_block">
            {$formAtts.widget_title|escape:'html':'UTF-8'}
        </h4>
    {/if}
    {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
    {/if}
    
    {if isset($formAtts.links) && $formAtts.links|@count > 0}
        <ul>
        {foreach from=$formAtts.links item=item}
            <li>
            	{if isset($item.link_title) && !empty($item.link_title)}
			        <h4 class="counter_title_top">
			            {$item.link_title|escape:'html':'UTF-8'}
			        </h4>
			    {/if}
            	<div class="numscroller{if isset($item.counterclass) && !empty($item.counterclass)} {$item.counterclass}{/if}" data-min="{$item.min}" data-max="{$item.max}" data-delay="{$item.delay}" data-increment="{$item.increment}">0</div>
                {if isset($item.number_suffix) && !empty($item.number_suffix)}
                <span class="counter-suffix">{$item.number_suffix}</span>
                {/if}
            	{if isset($item.bootom_html) && !empty($item.bootom_html)}
			        <div class="counter_bottom">
			            {$item.bootom_html nofilter}{* HTML form , no escape necessary *}
			        </div>
			    {/if}
            </li>
        {/foreach}
        </ul>
    {/if}
</div>
{/if}