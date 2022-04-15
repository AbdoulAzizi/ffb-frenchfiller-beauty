{*
  * MODULE PRESTASHOP OFFICIEL CHRONOPOST
  * 
  * LICENSE : All rights reserved - COPY AND REDISTRIBUTION FORBIDDEN WITHOUT PRIOR CONSENT FROM OXILEO
  * LICENCE : Tous droits réservés, le droit d'auteur s'applique - COPIE ET REDISTRIBUTION INTERDITES
* SANS ACCORD EXPRES D'OXILEO
  *
  * @author    Oxileo SAS <contact@oxileo.eu>
  * @copyright 2001-2018 Oxileo SAS
  * @license   Proprietary - no redistribution without authorization
  *}
<script>
    var lt="{$lt|escape:'htmlall':'UTF-8'}";
    var lt_history={$lt_history};
    var lt_history_link={$lt_history_link};
	var path="{$module_uri|escape:'javascript':'UTF-8'}";
	var id_order="{$id_order|escape:'javascript':'UTF-8'}";
	var chronopost_secret="{$chronopost_secret|escape:'javascript':'UTF-8'}";
    var chronopost_delayed_errors={$chronopost_errors};
</script>
<script src="{$module_uri|escape:'htmlall':'UTF-8'}/views/js/adminOrder.js"></script>

<br /><fieldset style = "width:400px;"><legend><img src="../img/admin/delivery.gif"/>
{l s='Print the Chronopost waybills' mod='chronopost'}
</legend><form method="POST" action="{$module_uri|escape:'htmlall':'UTF-8'}/postSkybill.php" id="chrono_form">
{if $bal==1}
	<p style = "text-align:center;width:400px"><b>Option BAL activée.</b></p>
{/if}

{if $saturday==1}
	<label>{l s='Saturday delivery option' mod='chronopost'}</label>
		<div class="margin-form"><input type="checkbox" name="shipSaturday" value="yes" {if $saturday_ok==1} checked{/if}/>
	</div>
{/if}

<label>{l s='Number of parcels' mod='chronopost'}</label>
<div class="margin-form"><input type="text" name="multiOne" id = "multiOne" value="{$nbwb|escape:'htmlall':'UTF-8'}"/></div>

<label for = "account"  class="control-label col-sm-4">{l s='Account to use' mod='chronopost'}</label>
		<select id="account" class="chosen form-control" name="account" {if $account_used }disabled{/if}>
                {foreach from=$available_accounts item=account name=accounts}
					<option value="{$account['account']|escape:'htmlall':'UTF-8'}"
                            {if $default_account==$account['account'] && !$account_used} selected
                            {elseif $account_used && $account_used==$account['account']} selected
                            {/if}>
                        {$account['accountname']|escape:'htmlall':'UTF-8'}
					</option>
                {/foreach}
			</select>
            {if $account_used}<input type="hidden" name="account" value="{$account_used}">{/if}
		<div id="dimensions">
			<div class="dimensions-group">
				<label class="control-label col-sm-4"></label>
				<table>
					<thead>
					<tr>
						<th style="text-align: center">Poids</th>
						<th style="text-align: center">Longueur</th>
						<th style="text-align: center">Hauteur</th>
						<th style="text-align: center">Largeur</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><input type="text" name="weight[]" min="0" id="weight" value="{$default_weight}" class="form-control"/></td>
						<td><input type="text" name="length[]" min="0" id="length" value="" class="form-control"/></td>
						<td><input type="text" name="height[]" min="0" id="height" value="" class="form-control"/></td>
						<td><input type="text" name="width[]" min="0" id="width" value="" class="form-control"/></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>


{if $to_insure>-1}
	<br/><label for = "advalorem">{l s='Shipment insurance' mod='chronopost'}</label>
	<div class="margin-form"><input type="checkbox" id = "advalorem" name="advalorem" value="yes" {if $to_insure>0} checked{/if}/>
	</div>
	<label>{l s='Value to insure' mod='chronopost'}</label><div class="margin-form"> <input type="text" name="advalorem_value" value="{$to_insure|escape:'htmlall':'UTF-8'}"/>
	</div>
{/if}
<input type="hidden" name="orderid" value="{$id_order|escape:'htmlall':'UTF-8'}"/><p style = "text-align:center">
<input type="hidden" name="shared_secret" value="{$chronopost_secret|escape:'htmlall':'UTF-8'}"/><input type="submit" value="{l s='Print waybill' mod='chronopost'}" class="button" style = "margin:10px;" id="chronoSubmitButton"/>
{if $return==1 AND $disable_return_button==0}<input style = "margin:10px;" type="submit" name="return" value="{l s='Print the return waybill' mod='chronopost'}" class="button"/>{/if}
</form></fieldset>
