{**
* 2020-2021
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author Digincube
*  @copyright 2020-2021
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="form-group">
    <div class="col-lg-11">
        <div class="row row-margin-top row-margin-bottom">
            <label class="control-label col-lg-5">
			{l s='How many free products will receive your customer ? ' mod='mdgiftproduct'}
			</label>
            <div class="col-lg-7">
                <div class="input-group">
                    <input type="text" name="nb_product_gift" value="{if $currentTab->getFieldValue($currentObject, 'nb_product_gift')}{$currentTab->getFieldValue($currentObject, 'nb_product_gift')}{else}1{/if}"/>
                </div>
            </div>
        </div>
    </div>
 </div>
<div id="rproducts" class="form-group">
    {if isset($rproducts) && $rproducts|@count}
        <div class="form-group">
            <div class="rproducts_container">
				{foreach from=$rproducts item='rproduct'}
                    {include file="$tpl_dir/gift_product_rules/signleproduct.tpl" rproduct=$rproduct}
                {/foreach}
            </div>
            <div class="col-lg-2 col-md-offset-5">
                <a class="btn btn-default pull-right add-rproduct">
                    <i class="material-icons">add_circle</i> {l s='Add new product' mod='mdgiftproduct'}
                </a>
            </div>
        </div>
    {/if}
</div>
