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

<div class="form-group condition_append condition_type_element_customer_age">
      <div class="col-lg-11 col-lg-offset-1">
          <div class="alert alert-info">
              {l s='Apply rule only on customer age' mod='mdgiftproduct'}
          </div>
      </div>
  </div>
  <div class="form-group condition_append condition_type_element_customer_age">
      <div class="col-lg-11 col-lg-offset-1">
          <div class="row">
              <div class="col-lg-3">
                  <div class="input-group">
                      <span class="input-group-addon">{l s='From' mod='mdgiftproduct'}</span>
                      <input type="text" class="input-medium" name="cdt_age_from[{$condition->id_mdgift_rule_condition|intval}]"
                      value="{if isset($condition->age_from)}{$condition->age_from|escape:'html':'UTF-8'}{/if}" />
                      <span class="input-group-addon">{l s='years' mod='mdgiftproduct'}</span>
                  </div>
              </div>
              <div class="col-lg-3">
                  <div class="input-group">
                      <span class="input-group-addon">{l s='To' mod='mdgiftproduct'}</span>
                      <input type="text" class="input-medium" name="cdt_age_to[{$condition->id_mdgift_rule_condition|intval}]"
                      value="{if isset($condition->age_to)}{$condition->age_to|escape:'html':'UTF-8'}{/if}" />
                      <span class="input-group-addon">{l s='years' mod='mdgiftproduct'}</span>
                  </div>
              </div>
          </div>
      </div>
</div>