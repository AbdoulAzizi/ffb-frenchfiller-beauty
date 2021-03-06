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
<!-- Block search module TOP -->
<div id="search_widget" class="search-widget js-dropdown popup-over" data-search-controller-url="{$search_controller_url}">
	<a id="click_show_search" href="javascript:void(0)" data-toggle="dropdown" class="float-xs-right popup-title">
	    <i class="leofal leofa-search" aria-hidden="true"></i>
	</a>
	<span class="close-overlay"><i class="fa fa-close"></i></span>
	<div class="over-layer"></div>
	<div class="block-form clearfix">
		<form method="get" action="{$search_controller_url}" class="popup-content dropdown-menu form-search" id="search_form">
			<input type="hidden" name="controller" value="search">
			<input class="search_query" type="text" name="s" value="{$search_string}" placeholder="{l s='Search' d='Shop.Theme.Catalog'}" aria-label="{l s='Search' d='Shop.Theme.Catalog'}">
			<button class="search-button" type="submit">
				<i class="leofal leofa-search"></i>
			</button>
		</form>
	</div>
</div>
<!-- /Block search module TOP -->
