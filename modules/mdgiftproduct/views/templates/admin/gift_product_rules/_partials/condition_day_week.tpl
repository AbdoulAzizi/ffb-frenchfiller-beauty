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

<div class="form-group condition_append condition_type_element_day_week">
   <div class="col-lg-11 col-lg-offset-1">
       <input type="hidden" id="condition_schedule_{$condition->id_mdgift_rule_condition|intval}" name="cdt_schedule[{$condition->id_mdgift_rule_condition|intval}]" value=""/>
       <div id="scheduleContainer_{$condition->id_mdgift_rule_condition|intval}"></div>
       <script>
           $('document').ready(function() {
               var businessHoursManager = $("#scheduleContainer_{$condition->id_mdgift_rule_condition|intval}").businessHours({
                   {if isset($condition->schedule) && $condition->schedule != ''}operationTime:{$condition->schedule|escape:'quotes':'UTF-8'},{/if}
                   weekdays:[{l s='\'Monday\',\'Tuesday\',\'Wednesday\',\'Thursday\',\'Friday\',\'Saturday\',\'Sunday\'' mod='mdgiftproduct'}],
                   defaultOperationTimeFrom:"00:00",
                   defaultOperationTimeTill:"23:59",
                   postInit:function(){
                       $('.operationTimeFrom, .operationTimeTill').datetimepicker({
						  datepicker:false,
						  format:'H:i',
						  step: 10
						});
                   },
                   dayTmpl:'<div class="dayContainer col-xs-3 col-md-2 col-lg-1"><div class="weekday"></div>' +
                       '<div data-original-title="" class="colorBox"><input type="checkbox" class="invisible operationState"></div>' +
                       '<div class="operationDayTimeContainer">' +
                           '<div class="operationTime input-group"><span class="input-group-addon"><i class="icon icon-sun"></i></span><input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value=""></div>' +
                           '<div class="operationTime input-group"><span class="input-group-addon"><i class="icon icon-moon"></i></span><input type="text" name="endTime" class="mini-time form-control operationTimeTill" value=""></div>' +
                       '</div></div>'
               });

               $("#condition_schedule_{$condition->id_mdgift_rule_condition|intval}").val(JSON.stringify(businessHoursManager.serialize()));

               $('.dayContainer .operationState').change(function() {
                   $("#condition_schedule_{$condition->id_mdgift_rule_condition|intval}").val(JSON.stringify(businessHoursManager.serialize()));
               });

               $('.dayContainer .mini-time').change(function() {
                   $("#condition_schedule_{$condition->id_mdgift_rule_condition|intval}").val(JSON.stringify(businessHoursManager.serialize()));
               });
           });
       </script>
  </div>
</div>