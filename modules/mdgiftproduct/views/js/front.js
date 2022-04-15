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

$(document).ready(function() {
	
	if($('.cart-item .price .product-price').length){
		$('.cart-item .price .product-price').each(function(index, element){
			var number = parseInt($.trim($(this).text().replace(/[^0-9]/gi, '')));
			if(number  == 0){
				$(this).closest('.cart-item').find('.qty').html('<div class="form-control static-qty">1</div>');
			}
		});
	}
	if($('.gift--slider').length){
			$('.gift--slider').each(function(index){
				var id = $(this).attr('id');
				if($(this).find('.splide__slide').length > 3){
					new Splide( '#'+id, {
							perPage: 3,
							rewind : true,
							pagination:false,
							gap:15
						} ).mount();
				}
			});
		}
					

		$(document).on('change', '[name="mgift_ipa"]', function(e) {
			var url_image = $(this).find('option:selected').data('url_image');
			if(url_image){
				$(this).closest('.giftProduct').find('.mgift_thumb').attr('src',url_image);
			}
		});
		setSelectedImageUrl();
		
		$(document).on("change",'input.rbutton', function() {
		//$inputs.change(function() {
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
					  "products":products,
					  "action":"addToCart",
					  "secureKey":secureKey,
					  "gift":wrapper.attr('data-id')
					},
					success: function (response) {
						if(v178 == 'yes'){
							prestashop.emit('updateCart', {
							  reason: 'refresh',
							  resp: 'cart'
							});
						}else{
							prestashop.emit('updateCart', {
							  reason: 'refresh'
							});
						}
						
						$button.removeClass('processing');
						
						if(response){
							if($.parseJSON(response).status == false)
								alert($.parseJSON(response).result);
						}
						
					},
					error: function () {}
				});
				
			}
			
		});
		prestashop.on(
		'updateCart',
		function (event) {
				if(event.reason.linkAction == 'remove-voucher'){
					$.ajax({
						url: gift_controller_url,
						type: 'POST',
						cache: false,
						data:'action=removeVoucher&secureKey=' + secureKey,
						success: function (data) {
							if(v178 == 'yes'){
								prestashop.emit('updateCart', {
								  reason: 'refresh',
								  resp: 'cart'
								});
							}else{
								prestashop.emit('updateCart', {
								  reason: 'refresh'
								});
							}
							//checkSelectedGift();
						},
						complete: function (data) {
							checkSelectedGift();
						},
						error: function () {}
					});
				}
				if($('#cart').length){
					checkSelectedGift();
				}				
		});
});

function checkSelectedGift(){
	$.ajax({
		url: gift_controller_url,
		type: 'POST',
		cache: false,
		data:'action=checkSelectedGift&secureKey=' + secureKey,
		dataType:"json",
		success: function (data) {
			if($('.gifts-panel').length){
				$('.gifts-panel').html(data.gift_html);		
			}else{
				var $elemgift = $("<div>").addClass('gifts-panel').html(data.gift_html)
				$('#cart .cart-grid-body').append($elemgift);
			}
			setSelectedImageUrl();
			$('.gift--slider').each(function(index){
				if($(this).find('.splide__slide').length > 3){
					var id = $(this).attr('id');
					new Splide( '#'+id, {
							perPage: 3,
							rewind : true,
							pagination:false,
							gap:15
						} ).mount();
				}
				
			});
			if($('.cart-item .price .product-price').length){
				$('.cart-item .price .product-price').each(function(index, element){
					var number = parseInt($.trim($(this).text().replace(/[^0-9]/gi, '')));
					if(number  == 0){
						$(this).closest('.cart-item').find('.qty').html('<div class="form-control static-qty">1</div>');
					}
				});
			}
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
