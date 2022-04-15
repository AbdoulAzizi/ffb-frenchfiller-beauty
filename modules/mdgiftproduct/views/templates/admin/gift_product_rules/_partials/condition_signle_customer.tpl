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

<div class="form-group condition_append condition_type_element_customer_single">
   <div class="col-lg-11 col-lg-offset-1">
       <div class="alert alert-info">
           {l s='Apply rule only to one selected customer' mod='mdgiftproduct'}
       </div>
   </div>
</div>
<div class="form-group condition_append condition_type_element_customer_single">
     <div class="col-lg-11 col-lg-offset-1">
         <div class="row">
             <div class="col-lg-12">
                 <div class="input-group">
                     <span class="input-group-addon"><i class="icon-user"></i></span>
                     <input type="hidden" id="condition_id_customer_{$condition->id_mdgift_rule_condition|intval}" name="cdt_id_customer[{$condition->id_mdgift_rule_condition|intval}]" value="{$condition->id_customer|intval}" />
                     <input type="text" class="searchCustomer input-xlarge" name="cdt_customer_filter[{$condition->id_mdgift_rule_condition|intval}]" value="{$condition->customer_filter|escape:'html':'UTF-8'}" />
                     <span class="input-group-addon"><i class="icon-search"></i></span>
                 </div>
             </div>
         </div>
     </div>
 </div>