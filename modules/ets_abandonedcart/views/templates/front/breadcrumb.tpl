{*
* 2007-2022 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if isset($breadcrumb.links) && $breadcrumb.links}
    {assign var='ik' value=0}
    {foreach from=$breadcrumb.links item='link'}
        {assign var='ik' value=$ik+1}
        {if $ik < count($breadcrumb.links)}
            <a class="solo-breadcrumb-a" href="{$link.url|escape:'html':'UTF-8'}">{$link.title|escape:'html':'UTF-8'}</a>
            <span class="navigation-pipe">{$navigationPipe|escape:'quotes':'UTF-8'}</span>
        {else}
            <span class="navigation_page">{$link.title|escape:'html':'UTF-8'}</span>
        {/if}
    {/foreach}
{/if}