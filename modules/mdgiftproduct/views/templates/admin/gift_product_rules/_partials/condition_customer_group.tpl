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

{if isset($condition->group) && (($condition->group.unselected|@count) + ($condition->group.selected|@count)) > 0}
            <div class="form-group condition_append condition_type_element_customer_group">
                <div class="col-lg-11 col-lg-offset-1">
                    <div class="alert alert-info">
                        {l s='Apply rule only to selected groups' mod='mdgiftproduct'}
                    </div>
                </div>
            </div>
            <div class="form-group condition_append condition_type_element_customer_group">
                <div class="col-lg-11 col-lg-offset-1">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="group_restriction_div" class="row">
                                <table class="table">
									<tr class="row-select-unselect">
										<td class="col-xs-6">
											<p>{l s='Unselected groups' mod='mdgiftproduct'}</p>
											<div class="input-group">
												<span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
												<input type="text" class="search_select" id="search_unselected_group_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
											</div>
											<select id="unselected_group_{$condition->id_mdgift_rule_condition|intval}" class="input-large unselected" multiple>
												{foreach from=$condition->group.unselected item='group'}
													<option value="{$group.id_group|intval}">{$group.name|escape:'html':'UTF-8'} (ID: {$group.id_group|escape:'html':'UTF-8'})</option>
												{/foreach}
											</select>
											<script type="text/javascript">
												{if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
													$('#unselected_group_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_unselected_group_{$condition->id_mdgift_rule_condition|intval}', true);
													{if !$ajax}{literal}});{/literal}{/if}
											</script>
											<a id="group_select_add_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_add" >{l s='Add' mod='mdgiftproduct'} <i class="icon-arrow-right"></i></a>
										</td>
										<td class="col-xs-6">
											<p>{l s='Selected groups' mod='mdgiftproduct'}</p>
											<input type="hidden" name="cdt_old_group[{$condition->id_mdgift_rule_condition|intval}]" value="{','|implode:$condition->group.old_selected}"/>
											<div class="input-group">
												<span class="input-group-addon">{l s='Search' mod='mdgiftproduct'}</span>
												<input type="text" class="search_select" id="search_selected_group_{$condition->id_mdgift_rule_condition|intval}" autocomplete="off">
											</div>
											<select name="cdt_selected_group[{$condition->id_mdgift_rule_condition|intval}][]" id="selected_group_{$condition->id_mdgift_rule_condition|intval}" class="input-large selected" multiple>
												{foreach from=$condition->group.selected item='group'}
													<option value="{$group.id_group|intval}">{$group.name|escape:'html':'UTF-8'} (ID: {$group.id_group|escape:'html':'UTF-8'})</option>
												{/foreach}
											</select>
											<script type="text/javascript">
												{if !$ajax}{literal}$(window).load(function() {{/literal}{/if}
													$('#selected_group_{$condition->id_mdgift_rule_condition|intval}').searchInSelect('#search_selected_group_{$condition->id_mdgift_rule_condition|intval}', true);
													{if !$ajax}{literal}});{/literal}{/if}
											</script>
											<a id="group_select_remove_{$condition->id_mdgift_rule_condition|intval}" class="btn btn-default btn-block clearfix select_remove" ><i class="icon-arrow-left"></i> {l s='Remove' mod='mdgiftproduct'} </a>
										</td>
									</tr>
								
                                </table>
                            </div>
                            <div class="row row-margin-top">
                                <label class="control-label col-lg-4">{l s='Consider only default customer group' mod='mdgiftproduct'}</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <span class="switch prestashop-switch fixed-width-lg" id="cdt_customer_default_group_{$condition->id_mdgift_rule_condition|intval}">
                                            <input type="radio" name="cdt_customer_default_group[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_customer_default_group_on_{$condition->id_mdgift_rule_condition|intval}" value="1" {if $condition->customer_default_group|intval}checked="checked"{/if} />
                                            <label class="t" for="cdt_customer_default_group_on_{$condition->id_mdgift_rule_condition|intval}">{l s='Yes' mod='mdgiftproduct'}</label>
                                            <input type="radio" name="cdt_customer_default_group[{$condition->id_mdgift_rule_condition|intval}]" id="cdt_customer_default_group_off_{$condition->id_mdgift_rule_condition|intval}" value="0" {if !$condition->customer_default_group|intval}checked="checked"{/if}/>
                                            <label class="t" for="cdt_customer_default_group_off_{$condition->id_mdgift_rule_condition|intval}">{l s='No' mod='mdgiftproduct'}</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}