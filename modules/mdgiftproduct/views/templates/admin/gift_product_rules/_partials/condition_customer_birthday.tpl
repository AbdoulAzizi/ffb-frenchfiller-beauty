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

<div class="form-group condition_append condition_type_element_customer_birthday">
       <div class="col-lg-11 col-lg-offset-1">
           <div class="alert alert-info">
               {l s='Apply rule only on customers\' birthday' mod='mdgiftproduct'}
           </div>
       </div>
   </div>
   <div class="form-group condition_append condition_type_element_customer_birthday">
       <div class="col-lg-11 col-lg-offset-1">
           <div class="row">
               <div class="col-lg-12">
                   <div class="input-group">
                       <span class="switch prestashop-switch fixed-width-lg" id="cdt_customer_birthday_{$condition->id_mdgift_rule_condition|intval}">
                           <input type="radio" name="cdt_customer_birthday[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_customer_birthday_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->customer_birthday|intval}checked="checked"{/if} />
                           <label class="t" for="cdt_customer_birthday_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                           <input type="radio" name="cdt_customer_birthday[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_customer_birthday_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->customer_birthday|intval}checked="checked"{/if}/>
                           <label class="t" for="cdt_customer_birthday_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                           <a class="slide-button btn"></a>
                       </span>
                   </div>
               </div>
           </div>
       </div>
</div>