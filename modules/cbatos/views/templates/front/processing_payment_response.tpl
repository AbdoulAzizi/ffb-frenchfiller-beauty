{extends file='page.tpl'}

{block name="page_content"}
	<h2>{l s='Processing payment' mod='cbatos'}</h2>
	<div class="cbatos-status-wrapper cbatos-status-awaiting" data-ajax-url="{$cbatos_ajaxUrl}">
		<div class="cbatos-awaiting-silentesponse">
			<p>
				<img src="{$cbatos_pathURI}views/img/atos.gif" alt="{l s='Card payment' mod='cbatos'}" style="float:left; margin: 0px 10px 5px 0px;" />
				{l s='We are currently processing your payment. It may take a few moments, you can wait here or close this page and wait for our order confirmation email.' mod='cbatos'}
			</p>
			<div class="cbatos-status-icon-wrapper">
				<i class="material-icons cbatos-status-icon">sync</i>
			</div>
		</div>
		<div class="cbatos-silentresponse-completed">
			<p>
				<img src="{$cbatos_pathURI}views/img/atos.gif" alt="{l s='Card payment' mod='cbatos'}" style="float:left; margin: 0px 10px 5px 0px;" />
				{l s='Your payment has been processed, you will be redirected in a few seconds.' mod='cbatos'}
			</p>
			<div class="cbatos-status-icon-wrapper">
				<i class="material-icons cbatos-status-icon">done</i>
			</div>
		</div>
	</div>
{/block}
