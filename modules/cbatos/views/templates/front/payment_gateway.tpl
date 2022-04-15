<!doctype html>
<html lang="{$language.iso_code}">

<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
</head>

<body id="{$page.page_name}" class="{$page.body_classes|classnames}">

{block name='hook_after_body_opening_tag'}
    {hook h='displayAfterBodyOpeningTag'}
{/block}

<header id="header">
    {block name='header'}
        {include file='checkout/_partials/header.tpl'}
    {/block}
</header>

{block name='notifications'}
    {include file='_partials/notifications.tpl'}
{/block}

<section id="wrapper">
    {hook h="displayWrapperTop"}
	<div class="container">

        {block name='content'}
			<section id="content">
				<div class="row">
					<div class="col-md-8">
                        {block name='cart_summary'}
                            {render file='checkout/checkout-process.tpl' ui=$checkout_process}
                        {/block}

						<h2>{l s='Payment' mod='cbatos'}</h2>

                        {print_r($context, true)}

						<h3>
                            {l s='Card payment' mod='cbatos'}
                            {if $cbatos_mode > cbatos::MODE_SINGLE}
                                {capture assign='cbatos_splitMsg'}{l s='in %u times' mod='cbatos'}{/capture}
                                {capture assign='cbatos_splitMsg'}{$cbatos_splitMsg|sprintf:$cbatos_mode}{/capture}
                                {$cbatos_splitMsg}
                            {/if}
						</h3>
						<p>
							<img src="{$cbatos_pathURI}views/img/atos.gif" alt="{l s='Card payment' mod='cbatos'}" style="float:left; margin: 0px 10px 5px 0px;" />
                            {l s='You have chosen to pay by card' mod='cbatos'}{if $cbatos_mode > cbatos::MODE_SINGLE} {$cbatos_splitMsg}{/if}.<br />
                            {l s='You will be redirected to a secure bank server where your card informations will be asked.' mod='cbatos'}<br />
                            {l s='At any moment you can hit the cancel button in order to come back to our payment methods choice from bank server' mod='cbatos'}<br />
                            {l s='Total amount to be paid:' mod='cbatos'}
							<span id="amount" class="price">{Tools::displayPrice($cbatos_totalAmount, $cbatos_paymentCurrency->id)}</span>
						</p>

                        {if $cbatos_form}
							<p style="margin-top:20px;">
								<strong>{l s='Click on one of the payment meanings logos below to proceed on a secure bank server.' mod='cbatos'}</strong>
							</p>
                            {$cbatos_form nofilter}
                        {else}
							<div class="error">
								<strong>{l s='Sorry, no more CB payments can be processed today, bank server should be available again at midnight.' mod='cbatos'}</strong>
							</div>
                        {/if}

						<p class="cart_navigation">
							<a href="{$link->getPageLink('order.php', true, null)|cat:'?step=3&cgv=1'}" class="button_large">{l s='Other payment methods' mod='cbatos'}</a>
						</p>

					</div>
					<div class="col-md-4">

                        {block name='cart_summary'}
                            {include file='checkout/_partials/cart-summary.tpl' cart = $cart}
                        {/block}

                        {hook h='displayReassurance'}
					</div>
				</div>
			</section>
        {/block}
	</div>
    {hook h="displayWrapperBottom"}
</section>

<footer id="footer">
    {block name='footer'}
        {include file='checkout/_partials/footer.tpl'}
    {/block}
</footer>

{block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{/block}

{block name='hook_before_body_closing_tag'}
    {hook h='displayBeforeBodyClosingTag'}
{/block}

</body>

</html>
