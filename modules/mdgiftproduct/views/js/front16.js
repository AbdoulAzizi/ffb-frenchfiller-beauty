/**
* 2007-2021 PrestaShop
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
*  @author    Digincube <digincubeagency@gmail.com>
*  @copyright 2021-2022 Digincube
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function() {
	if($('.gift-slider').length){
		$('.gift-slider').each(function(){
			tns({
				"container": '#'+$(this).attr('id'),
				"items": 4,
				"mouseDrag": true,
				"swipeAngle": false,
				"speed": 400,
				"gutter": 20,
				"loop": false,
				"controlsText": ['', ''],
				"nav": false,
			});
		})
		
	}
					

		$(document).on('change', '[name="mgift_ipa"]', function(e) {
			var url_image = $(this).find('option:selected').data('url_image');
			if(url_image){
				$(this).closest('.giftProduct').find('.mgift_thumb').attr('src',url_image);
			}
		});
		setSelectedImageUrl();
		/*$(document).on("click",'.giftProduct', function() {
			var $warraper = $(this).closest('.gift-widget-wrapper');
			$(this).closest(".owl-item").siblings().find('.selected').removeClass('selected');
			$(this).addClass('selected');
			var id_product = $(this).attr('data-product_id');
			var id_product_attribute = $(this).find("select[name='mgift_ipa']").val();
			console.log(id_product,id_product_attribute);
			$warraper.find('.id_product').val(id_product);
			$warraper.find('.id_product_attribute').val(id_product_attribute);
		});*/
		
		//var $inputs = $('input.rbutton');
		$(document).on("change",'input.rbutton', function() {
			var $this = $(this);
			var $wrapper = $(this).closest('.gift-widget-wrapper');
			var max_gift = $wrapper.attr('data-max');
			$(this).closest(".giftProduct").addClass('selected');
			if($wrapper.find('input.rbutton:checked').length > max_gift){
				$wrapper.find('input.rbutton:checked').each(function(index){
					if($this.val() != $(this).val()){
						$(this).prop('checked', false);
						$(this).closest(".giftProduct").removeClass('selected');
						return false; // breaks
					}
				})
			}
			
			if(!$(this).is(':checked')){
				$(this).prop('checked', false);
				$(this).closest(".giftProduct").removeClass('selected');
			}
			//$inputs.not(':checked').prop('disabled', $('input.rbutton:checked').length == 2);       
		});
		


		
		$(document).on('click', '.addGiftToCart', function () {
			var wrapper = $(this).closest('.gift-widget-wrapper');
			var $button = $(this);
			var products = [];
			wrapper.find( ".rbutton:checked" ).each(function( index ) {
				var $product = $(this).closest('.giftProduct ');
				var product = {id_product: $product.attr('data-product_id'), id_product_attribute: $product.find('.mgift_ipa').val()};
				products.push(product);
			});
			if(products.length){
				$button.addClass('processing');
				$.ajax({
					url: gift_controller_url,
					type: 'POST',
					cache: false,
					data: {
					  "products": products,
					  "action" :"addToCart",
					  "secureKey":secureKey,
					  "gift":wrapper.attr('data-id')
					},
					success: function (response) {
						$button.removeClass('processing');
						location.reload();
					},
					error: function () {}
				});
				
			}
			
		});
		
		$( document ).ajaxComplete(function( event, xhr, settings ) {
		 if($(xhr.responseJSON.HOOK_SHOPPING_CART).hasClass('gifts-panel')){
			 //setSelectedImageUrl();
			 $('.gift-slider').each(function(){
				tns({
						"container": '#'+$(this).attr('id'),
						"items": 3,
						"mouseDrag": true,
						"swipeAngle": false,
						"speed": 400,
						"gutter": 20,
						"loop": false,
						"controlsText": ['', ''],
						"nav": false,
					});
			})
		 }
		});
});

function checkSelectedGift(){
	$.ajax({
		url: gift_controller_url,
		type: 'POST',
		cache: false,
		data:'action=checkSelectedGift',
		dataType:"json",
		success: function (data) {
			if(!$('.gifts-panel').length){
				$('.cart-grid-body').append($('<div class="gifts-panel">'));
			}
			$('.gifts-panel').html(data.gift_html);			
			setSelectedImageUrl();
			$('.gift-slider').each(function(){
				tns({
						"container": '#'+$(this).attr('id'),
						"items": 3,
						"mouseDrag": true,
						"swipeAngle": false,
						"speed": 400,
						"gutter": 20,
						"loop": false,
						"controlsText": ['', ''],
						"nav": false,
					});
			})
		}
	});
}

function setSelectedImageUrl(){
	$('.giftProduct').each(function(index){
		//var giftProduct = $(this).closest('.giftProduct');
		var url_image = $(this).find('.mgift_ipa option:selected').data('url_image');
		$(this).find('img').attr('src',url_image)
	});
}
