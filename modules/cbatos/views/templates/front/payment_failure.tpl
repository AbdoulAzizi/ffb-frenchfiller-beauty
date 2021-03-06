{extends file='page.tpl'}

{block name="page_content"}
	<h2>{l s='Payment' mod='cbatos'}</h2>

	<h3>
        {l s='Card payment failure' mod='cbatos'}
	</h3>
	<p>
		<img src="{$cbatos_pathURI}views/img/atos.gif" alt="{l s='Card payment' mod='cbatos'}" style="float:left; margin: 0px 10px 5px 0px;" />
        {if $cbatos_response && $cbatos_response->response_code == '17'}
            {l s='The payment has been cancelled.' mod='cbatos'}<br />
        {elseif $cbatos_response}
            {l s='The payment is a failure. It means that either your transaction has been refused by your bank or an error has prevented the transaction to complete.' mod='cbatos'}<br />
        {else}
            {l s='The payment is a failure. It means that either your transaction has been refused by your bank or you decided to cancel the payment process.' mod='cbatos'}<br />
        {/if}
        {l s='Click the other payment methods button to restart payment process with one of the available methods.' mod='cbatos'}
	</p>
	<p class="cart_navigation">
		<a href="{$link->getPageLink('order')}" class="button_large">{l s='Other payment methods' mod='cbatos'}</a>
	</p>
    {if $cbatos_response}
		<br /><br />
		<table class="table">
			<thead>
			<tr>
				<th class="first_item">{l s='Payment summary:' mod='cbatos'}</th>
				<th class="last_item">&nbsp;</th>
			</tr>
			</thead>
			<caption></caption>
			<tbody>
			<tr class="item">
				<td>{l s='Merchant ID' mod='cbatos'}</td>
				<td>{$cbatos_response->merchant_id}</td>
			</tr>
			<tr class="alternate_item">
				<td>{l s='Transaction reference' mod='cbatos'}</td>
				<td>{$cbatos_response->transaction_id}</td>
			</tr>
			<tr class="item">
				<td>{l s='Payment means' mod='cbatos'}</td>
				<td>{$cbatos_response->payment_means}</td>
			</tr>
			<tr class="alternate_item">
				<td>{l s='Authorisation ID' mod='cbatos'}</td>
				<td>{$cbatos_response->authorisation_id}</td>
			</tr>
			<tr class="item">
				<td>{l s='Payment certificate' mod='cbatos'}</td>
				<td>{$cbatos_response->payment_certificate}</td>
			</tr>
			<tr class="alternate_item">
				<td>{l s='Payment date' mod='cbatos'}</td>
				<td>{$cbatos_response->payment_date}</td>
			</tr>
			<tr class="item">
				<td>{l s='Amount' mod='cbatos'}</td>
				<td>{Tools::displayPrice($cbatos_amount, $cbatos_currency)}</td>
			</tr>
			</tbody>
		</table>
		<p class="cart_navigation">
			<a href="{$link->getPageLink('order')}" class="button_large">{l s='Other payment methods' mod='cbatos'}</a>
		</p>
    {/if}
{/block}
