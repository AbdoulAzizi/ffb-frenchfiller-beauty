{if $cbatos_response}
<table class="table">
	<caption></caption>
	<thead>
		<tr>
			<th class="first_item">{l s='Payment summary:' mod='cbatos'}</th>
			<th class="last_item">&nbsp;</th>
		</tr>
	</thead>
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
	</tbody>
</table>
{/if}
