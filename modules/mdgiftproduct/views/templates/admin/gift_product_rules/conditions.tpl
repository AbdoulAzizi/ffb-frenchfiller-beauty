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

<div id="conditions" class="form-group">
    {if isset($conditions) && $conditions|@count}
                <div class="form-group form-control-static">
                    <div class="conditions_container">
						{foreach from=$conditions item='condition'}
                            {include file="$tpl_dir/gift_product_rules/condition.tpl" condition=$condition}
                        {/foreach}
                    </div>
                    <div class="col-lg-2 col-md-offset-5">
						<a class="btn btn-default pull-right add-condition">
                            <i class="material-icons">add_circle</i> {l s='Add new condition' mod='mdgiftproduct'}
                        </a>
                    </div>
                </div>
    {/if}
</div>
