/**
 * 2021-2022
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize the module for your
 * needs please refer to
 * http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
 * for more information.
 *
 * @author    Digincube <digincubeagency@gmail.com>
 * @copyright 2021-2022 Digincube
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var condition_selectors = new Array('country', 'carrier', 'group', 'cart_rule', 'product', 'category', 'attribute', 'feature', 'zone', 'state', 'manufacturer', 'supplier', 'order_state', 'gender', 'currency');

$(document).ready(function() {

    
		
	$('.gift_product_rule_tab').hide();
	$('.tab-row.active').removeClass('active');
	$('#gift_product_rule_informations').show();
	$('#gift_product_rule_link_informations').parent().addClass('active');
	ToogleConditionType();
	
	$(document).on('change', '.condition_condition_type', function (e){
        var $elem = $(this);
		var type = $elem.val();
		if(type != undefined && type !=''){
			$elem.closest('.condition').find(".condition_append").hide();
			$elem.closest('.condition').find('.condition_type_element_'+type).show();
		}else{
			$elem.closest('.condition').find(".condition_append").hide();
		}
    });
	
	$(document).on('click', '#gift-rule-panel .productTabs .tab-page', function (e){
		var tab = $(this).attr('data-target');
        $('.gift_product_rule_tab').hide();
		$('.tab-row.active').removeClass('active');
		$('#gift_product_rule_' + tab).show();
		$('#gift_product_rule_link_' + tab).parent().addClass('active');
		$('#currentFormTab').val(tab);
    });
	


	$(document).on('click', '.select_add', function (e){
        var $unselected = $(this).closest('.row-select-unselect').find('select.unselected');
        var $selected = $(this).closest('.row-select-unselect').find('select.selected');
  		addGiftProductOption($unselected, $selected);
    });
	$(document).on('click', '.select_remove', function (e){
        var $unselected = $(this).closest('.row-select-unselect').find('select.unselected');
        var $selected = $(this).closest('.row-select-unselect').find('select.selected');
 		removeGiftProductOption($selected, $unselected);
    });
	
	$(document).on('change', '[name^=cdt_restriction_]', function (e){
       ToogleSwitchFilter();
    });
	ToogleSwitchFilter();
	$(document).on('click', '.add-condition', function (e){
		//var $this = $(this);
		$('#conditions').find("#condition_new_condition_loader .spinner").show();
		$.ajax({
			type: 'GET',
			url: 'ajax-tab.php',
			async:false, 
			data: {
				controller:'AdminGiftProductRules',
				token:$('#gift_rule_form').attr('data-token'),
				newCondition:1,
			},
			success : function(res)
			{
			   $('#conditions').find("#condition_new_condition_loader .spinner").show();
				if (res != "") {
					$('#conditions').find(".conditions_container ").append(res);
					
				}
			}
		}).done(function(){
			ToogleConditionType();
			ToogleSwitchFilter();
		});
    });
	$(document).on('click', '.add-rproduct', function (e){
 		$.ajax({
			type: 'GET',
			url: 'ajax-tab.php',
			async:false, 
			data: {
				controller:'AdminGiftProductRules',
				token:$('#gift_rule_form').attr('data-token'),
				newProduct:1,
			},
			success : function(res)
			{
				if (res != "") {
					$('.rproducts_container').append(res);
					
				}
			}
		}).done(function(){
			//ToogleConditionType();
		});
    });
	$(document).on('click', '.remove-condition', function (e){
		$(this).closest('.condition').remove();
    });
	
	$(document).on('click', '.remove-product', function (e){
		$(this).closest('.product_container').remove();
    });
	
	
	
	$('#gift_rule_form').submit(function() {
		/*if ($('#customerFilter').val() == '') {
			$('#condition_id_customer').val('0');
		}*/

		//Remove all values from search fields, because if don't the hidden values are not set
		$('.search_select').val('').trigger('keyup');

		for (i in condition_selectors) {
			
			$('[id^=selected_' + condition_selectors[i] + ']').each(function() {
				$(this).find('option').each(function() {
					$(this).prop('selected', true);
				});
			});
		}
	});
	
	$(document).on('keyup', '.searchProductFilter', function (e){
		var $elem = $(this);
		console.log('test');
		$elem.typeWatch({
			captureLength: 2,
			highlight: true,
			wait: 200,
			callback: function(){
				searchProducts($elem);
			}
		});
		
	});
	
	
				
	$(document).on('change', '.gift_product', function (e){
		var selected_product = $(this).val();
		var $target_attribute_select = $(this).closest('.product-item').find('.attributes_container[data-product="'+selected_product+'"]');
        if($target_attribute_select.length){
			$(this).closest('.product-item').find('.gift_product_attributes_selection').show();
			$(this).closest('.product-item').find('.gift_product_attributes').show();
			if($target_attribute_select.find('option').length)
				$target_attribute_select.show().siblings('.attributes_container').hide();
			else{
				$(this).closest('.product-item').find('.gift_product_attributes_selection').hide();
			}
				 
		}else{
			$(this).closest('.product-item').find('.gift_product_attributes_selection').hide();
		}
    });
	
	if($('.searchCustomer').length){
        $('.searchCustomer').autocomplete('ajax-tab.php', {
			  formatItem: function(data, i, max, value, term) {
				  return value;
			  },
			  dataType: 'json',
			  selectFirst: false,
			  minChars: 2,
			  max: 60,
			  width: 420,
			  scroll: true,
			  parse: function(data) {
				  var temp_array = new Array();
				  for (var i = 0; i < data.length; i++)
					  temp_array[temp_array.length] = { data: data[i], value: data[i].fullname + ' (' + data[i].email + ')' };
				  return temp_array;
			  },
			  extraParams: {
				  findCustomer: 1,
				  controller: 'AdminGiftProductRules',
				  token: $('#gift_rule_form').attr('data-token'),
			  }
		}).result(function(event, data, formatted) {
			$(this).closest('.condition_type_element_customer_single').find('input[type="hidden"]').val(data.id_customer);
			$(this).val(data.fullname + ' (' + data.email + ')');
		});
    }
    
		
});
function ToogleSwitchFilter()
{
     $('[name^=cdt_restriction_]:checked').each(function() {
		if ($(this).val() == 0) {
			$(this).closest('.restriction-row').find('.filter-container').hide();
		} else {
			$(this).closest('.restriction-row').find('.filter-container').show();

		}
	});
}

function ToogleConditionType()
{
    $('.conditions_container .condition').each(function() {
        var type = $(this).find('.condition_condition_type').val();
		if(type != undefined && type !=''){
			$(this).find('.condition_append').hide();
			$(this).find('.condition_type_element_'+type).show();
			//$('.condition_type_options_'+type).show().siblings("div[class^='condition_type_options_']").hide();
		}else{
			$(this).find(".condition_append").hide();
		}
    });
}


jQuery.fn.searchInSelect = function(input, matchingSingle) {
    return this.each(function() {
        var select = $(this);
        var options_select = [];
        select.find('option').each(function() {
            options_select.push({value: $(this).val(), text: $(this).text()});
        });
        select.data('options', options_select);
        input = input.replace( /(:|\.|\[|\]|,|=|@)/g, "\\$1" );
        $(input).bind('keyup', function() {
            var options_select = select.empty().scrollTop(0).data('options');
            var search = $.trim($(this).val());
            var regex = new RegExp(search,'gi');

            var new_options_select_html = '';
            $.each(options_select, function(i, option) {
                if(option.text.match(regex) !== null) {
                    new_options_select_html += '<option value="' + option.value + '">' + option.text + '</option>';
                }
            });

            select.append(new_options_select_html);

            if (matchingSingle === true &&
                select.children().length === 1) {
                select.children().get(0).selected = true;
            } else if (select.children().length > 0) {
                select.children().get(0).selected = false;
            }
        })
    })
};

function addGiftProductOption(from,to) {
    var selected = from.find('option:selected');
    var selectedVal = [];
    selected.each(function(){
        selectedVal.push($(this).val());
    });

    var options = from.data('options');
    var tempOption = [];

    var targetOptions = to.data('options');
	console.log(targetOptions);
     $.each(options, function(i) {
        var option = options[i];
        if($.inArray(option.value, selectedVal) === -1) {
            tempOption.push(option);
        } else {
            targetOptions.push(option);
        }

    });

    from.find('option:selected').remove().appendTo(to);

    to.data('options', targetOptions);
    from.data('options', tempOption);
}
function removeGiftProductOption(from,to) {
    var selected = from.find('option:selected');
    var selectedVal = [];
    selected.each(function(){
        selectedVal.push($(this).val());
    });

    var options = from.data('options');
    var tempOption = [];

    var targetOptions = to.data('options');

    $.each(options, function(i) {
        var option = options[i];
        if($.inArray(option.value, selectedVal) === -1) {
            tempOption.push(option);
        } else {
            targetOptions.push(option);
        }

    });

    from.find('option:selected').remove().appendTo(to);

    to.data('options', targetOptions);
    from.data('options', tempOption);
}


function searchProducts(element)
{	var $parent_product = element.closest('.product-item');
	var id = $parent_product.attr('data-id');
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: 'ajax-tab.php',
        async: true,
        dataType: 'json',
        data: {
            controller: 'AdminGiftProductRules',
            token: $('#gift_rule_form').attr('data-token'),
            action: 'findProducts',
            product_search: element.val()
        },
        success : function(res)
        {
            var products_found = '';
            var attributes_html = '';

            if (res.found) {
               $parent_product.find('.products_found').show();
                $.each(res.products, function() {
                    products_found += '<option value="' + this.id_product + '">' + this.name + (this.combinations.length == 0 ? ' - ' + this.formatted_price : '') + '</option>';

                    attributes_html += '<div class="attributes_container" data-product="' + this.id_product + '" ' + (!this.combinations.length ? 'style="display:none"' : '') + '>'+
						'<select multiple class="multiSelect control-form ipa_product" name="id_product_attribute['+id+']['+this.id_product+'][]" >';

					$.each(this.combinations, function() {
                        attributes_html += '<option ' + (this.default_on == 1 ? 'selected="selected"' : '') + ' value="' + this.id_product_attribute + '">' + this.attributes + ' - ' + this.formatted_price + '</option>';
                    });
                    attributes_html += '</select></div>';
                });
 				$parent_product.find('.gift_product').html(products_found);
				$parent_product.find('.gift_product_attributes_selection').html(attributes_html);
				var selected_product = $parent_product.find('.gift_product').val();
				var $target_attribute_select = $parent_product.find('.attributes_container[data-product="'+selected_product+'"]');
                if($target_attribute_select.length){
					$parent_product.find('.gift_product_attributes').show();
					$target_attribute_select.show().siblings('.attributes_container').hide();
				}
            } else {
				element.closest('.product-item').find('.products_found').hide();
            }
        }
    });
}