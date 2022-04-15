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
    var path="{$module_dir|escape:'javascript':'UTF-8'}/chronopost/";
    var failure_msg="{l s='Error while creating carrier.' mod='chronopost'}";
    var success_msg="{l s='Carrier successfully created' mod='chronopost'}";
    var chronopost_secret="{$chronopost_secret|escape:'javascript':'UTF-8'}";
</script>
<script src="{$module_dir|escape:'javascript':'UTF-8'}/chronopost/views/js/config.js"></script>

<div class="row" id="chronoconfig">
	<div class="col-sm-4" id="chronoconfig_left">
		<div data-spy="affix">
			<h1>{l s='Module Chronopost' mod='chronopost'}</h1>

			<ol class="nav nav-pills nav-stacked" id="chrononav">
				<li role="presentation"><a href="#account">1. {l s='Configure account' mod='chronopost'}</a></li>
				<li role="presentation"><a href="#carriers">2. {l s='Configure carriers' mod='chronopost'}</a></li>
				<li role="presentation"><a href="#shipping">3. {l s='Configure shipping options' mod='chronopost'}</a></li>
				<li role="presentation"><a href="#rdv">4. {l s='Configure Chrono Precise' mod='chronopost'}</a></li>
				<li role="presentation"><a href="#pricing">5. {l s='Configure pricing' mod='chronopost'}</a></li>
			</ol>
		</div>
	</div>
	<div class="col-sm-8" id="chronoconfig_main">

		<div class="alert alert-dismissible alert-success" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<p>
				<strong>{l s='Offer to your customers the first Express delivery service with the offical Chronopost module for Prestashop 1.5 and 1.6. With Chronopost, your customer will have the choice of the main delivery modes within 24h : at home,  at a Pickup point or at the office !' mod='chronopost'}</strong>
			</p><p>
                {l s='Your customers will also have the rdv service :  They are notified by email or SMS the day before the delivery and can reschedule the delivery or ask to be delivered at a pickup point among more than 17 000 points (post offices, Pickup relay or Chronopost agencies).' mod='chronopost'}
			</p><p>
                {l s='Expand your business internationally with Chronopost international delivery service which is included in this module.' mod='chronopost'}
			</p><p><strong>
                    {l s='Find all these services in the Chronopost e-commerce pack : MyChrono. To activate the module on your site, contact us at ' mod='chronopost'}<a href="mailto:demandez.a.chronopost@chronopost.fr">demandez.a.chronopost@chronopost.fr</a>
				</strong>
			</p>
		</div>

		<h2 id="account">{l s='Configure account' mod='chronopost'}</h2>
		<style>
			body {
				position: relative;
			}
		</style>
		<script>
            var module_dir="{$module_dir|escape:'htmlall':'UTF-8'}";
            var chronopost_secret="{$chronopost_secret|escape:'htmlall':'UTF-8'}";
            {literal}
            $(function() {
                $("#testWSLogin").live("click", function(e)
                {
                    e.preventDefault();
                    var account_number = $(this).parent().parent().find("#chrono_account").val();
                    var account_password = $(this).parent().parent().find("#chrono_password").val();
                    $.get(module_dir+"chronopost/async/testLogin.php?account="+account_number+"&shared_secret="+chronopost_secret+"&password="+account_password, function(data) {
                        if (data == "OK") alert("Les identifiants fournis sont valides !");
                        else alert(data);
                    });
                    return false;
                });

                $("#addAccount").live('click', function(e)
                {
                    e.preventDefault();
                    var elem = $(".account").last().clone();
                    elem.find('input[type=text]').val('');
                    var t = $(elem).insertBefore($('.panel-accounts .panel-footer'));
                    if($(elem).find("#deleteAccount").length == 0){
                    	$(elem).find(".form-group").last().append('<button id = "deleteAccount" class="btn btn-default">Supprimer le contrat</button>');
                    }
                });

                $("#deleteAccount").live('click', function(e)
                {
                    e.preventDefault();
                    if(confirm("Valider la suppression du compte ?")){
                        $(this).parent().parent().remove();
                    }
                });

                $('#cleanAddresses').on('click', function(e){
                    e.preventDefault();

                    if(confirm('Supprimer toutes les adresses définies ?')) {
                        var groups = ["shipper", "customer", "return"];
                        groups.forEach(function (group) {
                            $('input[name="chronoparams[' + group + '][mobile]"]').val("");
                            $('input[name="chronoparams[' + group + '][phone]"]').val("");
                            $('input[name="chronoparams[' + group + '][email]"]').val("");
                            $('input[name="chronoparams[' + group + '][contactname]"]').val("");
                            $('select[name="chronoparams[' + group + '][country]"]').val("FR");
                            $('input[name="chronoparams[' + group + '][city]"]').val("");
                            $('input[name="chronoparams[' + group + '][zipcode]"]').val("");
                            $('input[name="chronoparams[' + group + '][address2]"]').val("");
                            $('input[name="chronoparams[' + group + '][address]"]').val("");
                            $('input[name="chronoparams[' + group + '][name2]"]').val("");
                            $('input[name="chronoparams[' + group + '][name]"]').val("");
                            $('select[name="chronoparams[' + group + '][civility]"]').val("M");
                        });
                    }
                })
            });
            {/literal}
		</script>

		<form action="{$post_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal" method="post">
			<input type="hidden" name="createnewcarrier" id="createnewcarrier" value="" />
			<div class="panel panel-accounts">
				<div class="panel-heading">
					<i class="icon-briefcase"></i> {l s='Account credentials' mod='chronopost'}
				</div>
                {if !$general_accounts}
					<div class="account">
						<div class="form-group">
							<label class="control-label col-lg-3">
                                {l s='Contract number' mod='chronopost'}
							</label>
							<div class="col-lg-9 ">
								<input id = "chrono_account" type="text" name="chronoparams[general][account][]" value=""/>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3">{l s='Subaccount' mod='chronopost'}</label>
							<div class="col-lg-9">
								<input id = "chrono_subaccount" type="text" name="chronoparams[general][subaccount][]" value=""/>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='The password contains 6 digits. If you do not have it, please contact Chronopost' mod='chronopost'}" data-html="true">{l s='Chronopost password' mod='chronopost'}</span></label>
							<div class="col-lg-9">
								<input id = "chrono_password" type="text" name="chronoparams[general][password][]" value=""/>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3">{l s='Account name' mod='chronopost'}</label>
							<div class="col-lg-9">
								<input id = "chrono_accountname" type="text" name="chronoparams[general][accountname][]" value=""/>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<button id = "testWSLogin" class="btn btn-default">{l s='Test the validity of identifiers' mod='chronopost'}</button>
						</div>
					</div>

                {else}
                    {foreach from=$general_accounts item=account name=accounts}
						<div class="account">
							<div class="form-group">
								<label class="control-label col-lg-3">
                                    {l s='Contract number' mod='chronopost'}
								</label>
								<div class="col-lg-9 ">
									<input id = "chrono_account" type="text" name="chronoparams[general][account][]" value="{$account['account']|escape:'htmlall':'UTF-8'}"/>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Subaccount' mod='chronopost'}</label>
								<div class="col-lg-9">
									<input id = "chrono_subaccount" type="text" name="chronoparams[general][subaccount][]" value="{$account['subaccount']|escape:'htmlall':'UTF-8'}"/>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='The password contains 6 digits. If you do not have it, please contact Chronopost'  mod='chronopost'}" data-html="true">{l s='Chronopost password' mod='chronopost'}</span></label>
								<div class="col-lg-9">
									<input id = "chrono_password" type="text" name="chronoparams[general][password][]" value="{$account['password']|escape:'htmlall':'UTF-8'}"/>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Account name' mod='chronopost'}</label>
								<div class="col-lg-9">
									<input id = "chrono_accountname" type="text" name="chronoparams[general][accountname][]" value="{$account['accountname']|escape:'htmlall':'UTF-8'}"/>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3"></label>
								<button id = "testWSLogin" class="btn btn-default">{l s='Test the validity of identifiers' mod='chronopost'}</button>
                                {if $smarty.foreach.accounts.first}
                                {else}
									<button id = "deleteAccount" class="btn btn-default">{l s='Delete account' mod='chronopost'}</button>
                                {/if}
							</div>
						</div>
                    {/foreach}
                {/if}
				<div class="panel-footer">

					<button id = "addAccount" class="btn btn-default"><i class="icon-plus"></i> {l s='Add account' mod='chronopost'}</button>
					<button type="submit" class="btn btn-default pull-right" name="submitChronoConfig"><i class="process-icon-save"></i> {l s='Save' mod='chronopost'}</button>
				</div>
			</div>

			<div class="panel" class="shipperInfo">
				<div class="panel-heading">
					<i class="icon-truck"></i> {l s='Shipper address' mod='chronopost'}
				</div>

                {$shipper_form|escape:'quotes':'UTF-8'}
			</div>

			<div class="panel" class="invoicingInfo">
				<div class="panel-heading">
					<i class="icon-euro"></i> {l s='Invoicing address' mod='chronopost'}
				</div>
				<!--<div class="form-group">
					<label class="control-label col-lg-3" for="copyShipperInfo">{l s='Use the same information as above' mod='chronopost'}</label>
					<div class="col-lg-9">
						<input type="checkbox" id="copyShipperInfo"/>
					</div>
				</div>-->
                {$customer_form|escape:'quotes':'UTF-8'}
			</div>

			<div class="panel" class="returnInfo">
				<div class="panel-heading">
					<i class="icon-arrow-left"></i> {l s='Return address' mod='chronopost'}
				</div>

                {$return_form|escape:'quotes':'UTF-8'}
				<div class="panel-footer">
					<button id="cleanAddresses" class="btn btn-default"><i class="icon-trash"></i> {l s='Delete all addresses' mod='chronopost'}</button>
					<button type="submit" class="btn btn-default pull-right" name="submitChronoConfig"><i class="process-icon-save"></i> {l s='Save' mod='chronopost'}</button>
				</div>
			</div>

			<div class="panel" class="returnDefault">
				<div class="panel-heading">
					<i class="icon-arrow-left"></i> {l s='Default return address' mod='chronopost'}
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">
                        {l s='Default return address' mod='chronopost'}
					</label>
					<div class="col-lg-9 ">
						<select name="chronoparams[return][default]">
							<option value="0"{if $return_default=='0'} selected{/if}>{l s='Return address' mod='chronopost'}</option>
							<option value="1"{if $return_default=='1'} selected{/if}>{l s='Invoice address' mod='chronopost'}</option>
							<option value="2"{if $return_default=='2'} selected{/if}>{l s='Shipping address' mod='chronopost'}</option>
						</select>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-default pull-right" name="submitChronoConfig"><i class="process-icon-save"></i> {l s='Save' mod='chronopost'}</button>
				</div>
			</div>

			<h2 id="carriers">{l s='Configure carriers' mod='chronopost'}</h2>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-truck"></i> {l s='Carriers' mod='chronopost'}
				</div>
                {foreach from=$carriers_tpl item=tpl}
                    {$tpl|escape:'quotes':'UTF-8'}
                {/foreach}
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-globe"></i> {l s='Configure pickup map' mod='chronopost'}
				</div>
				<div class="alert alert-info">
                    {l s='In order to use Google Maps, you will need an API Key which can be generated from the Google Developers Console.' mod='chronopost'}
					<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">{l s='Click here for more informations.' mod='chronopost'}</a>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Display map for pickup' mod='chronopost'}</label>
					<div class="col-lg-9">
						<select name="chronoparams[map][enabled]">
							<option value="1">{l s='Yes' mod='chronopost'}</option>
							<option value="0"{if $map_enabled==0} selected{/if}>{l s='No' mod='chronopost'}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Google Maps API key' mod='chronopost'}</label>
					<div class="col-lg-9">
						<input type="text" name="chronoparams[map][apikey]" value="{$map_apikey|escape:'htmlall':'UTF-8'}"/>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-default pull-right" name="submitChronoConfig">
						<i class="process-icon-save"></i> {l s='Save' mod='chronopost'}</button>
				</div>
			</div>


			<h2 id="shipping">{l s='Configure shipping options' mod='chronopost'}</h2>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-asterisk"></i> {l s='Print options' mod='chronopost'}
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Waybill print type' mod='chronopost'}</label>
					<div class="col-lg-9">
						<select name="chronoparams[general][printmode]">
                            {foreach from=$print_modes key="k" item="v"}
								<option value="{$k|escape:'htmlall':'UTF-8'}"{if $k==$selected_print_mode} selected{/if}>{$v|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Product unit weight' mod='chronopost'}</label>
					<div class="col-lg-9">
						<select name="chronoparams[general][weightcoef]"/>';
                        {foreach from=$weights key="v" item="k"}
							<option value="{$k|escape:'htmlall':'UTF-8'}"{if $k==$selected_weight} selected{/if}>{$v|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
						</select>
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<i class="icon-euro"></i> {l s='Insurance' mod='chronopost'}
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Activate insurance' mod='chronopost'}</label>
					<div class="col-lg-9">
						<select name="chronoparams[advalorem][enabled]">
							<option value="1">{l s='Yes' mod='chronopost'}</option>
							<option value="0"{if $advalorem_enabled==0} selected{/if}>{l s='No' mod='chronopost'}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='By enabling this option, for each package exceeding the amount you enter, your shipment will be insured up to the amount of its the articles (maximum 20,000€ ). You can enter the amount to insure on your order detail.' mod='chronopost'}" data-html="true">{l s='Minimum amount to insure' mod='chronopost'}</span></label>

					<div class="col-lg-9">
						<input type="text" name="chronoparams[advalorem][minvalue]" value="{$advalorem_minvalue|escape:'htmlall':'UTF-8'}"/>
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<i class="icon-inbox"></i> {l s='BAL option' mod='chronopost'}
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Careful : This option has to be contracted first' mod='chronopost'}" data-html="true">{l s='Activate mailbox option' mod='chronopost'}</span></label>

					<div class="col-lg-9">
						<select name="chronoparams[bal][enabled]">
							<option value="0">{l s='No' mod='chronopost'}</option>
							<option value="1"{if $bal_enabled==1} selected{/if}>{l s='Yes' mod='chronopost'}</option>
						</select>
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<i class="icon-truck"></i> {l s='Delivery on saturday option' mod='chronopost'}
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Activate Saturday delivery option' mod='chronopost'}</label>

					<div class="col-lg-9">
						<select name="chronoparams[saturday][active]">
							<option value="yes"{if $saturday_active=='yes'} selected{/if}>{l s='Yes' mod='chronopost'}</option>
							<option value="no"{if $saturday_active!='yes'} selected{/if}>{l s='No' mod='chronopost'}</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Option checked by default' mod='chronopost'}</label>
					<div class="col-lg-9">
						<select name="chronoparams[saturday][checked]">
							<option value="yes"{if $saturday_checked=='yes'} selected{/if}>{l s='Yes' mod='chronopost'}</option>
							<option value="no"{if $saturday_checked!='yes'} selected{/if}>{l s='No' mod='chronopost'}</option>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-3"></div>
					<div class="col-lg-9">
                        {l s='Display the Saturday delivery option from:' mod='chronopost'}
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Day' mod='chronopost'}</label>
					<div class="col-lg-9">
                        {$day_start|escape:'quotes':'UTF-8'}
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Hour' mod='chronopost'}</label>
					<div class="col-lg-3">{$hour_start|escape:'quotes':'UTF-8'}</div><div class="col-lg-3">{$minute_start|escape:'quotes':'UTF-8'}</div>
				</div>

				<div class="row">
					<div class="col-lg-3"></div>
					<div class="col-lg-9">
                        {l s='Until:' mod='chronopost'}
					</div>
				</div>


				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Day' mod='chronopost'}</label>
					<div class="col-lg-9">
                        {$day_end|escape:'quotes':'UTF-8'}
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Hour' mod='chronopost'}</label>
					<div class="col-lg-3">
                        {$hour_end|escape:'quotes':'UTF-8'} </div>
					<div class="col-lg-3">{$minute_end|escape:'quotes':'UTF-8'}</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-default pull-right" name="submitChronoConfig"><i class="process-icon-save"></i> {l s='Save' mod='chronopost'}</button>
				</div>
			</div>



			<h2 id="rdv">{l s='Configure Precise' mod='chronopost'}</h2>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-calendar"></i> {l s='Day of package collection' mod='chronopost'}
				</div>

				<div class="alert alert-info">
                    {l s='The Chrono Precise calendar displayed to the customer will start from this day. If you define a starting day it will take precedence on the number of days.' mod='chronopost'}
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Number of days for you to prepare the order for shipment.' mod='chronopost'}" data-html="true">{l s='Number of days after order' mod='chronopost'}</span></label>
					<div class="col-lg-9">
						<div class="input-group">
							<input name="chronoparams[rdv][delay]" type="text"
								   value="{$rdv_delay|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">{l s='days' mod='chronopost'}</div>
						</div>
					</div>
				</div>


				<div class="form-group">
					<label class="col-lg-3 control-label">
						<strong>{l s='Or' mod='chronopost'}</strong> {l s='day' mod='chronopost'}
					</label>
					<div class="col-lg-9">
                        {$day_rdv_on|escape:'quotes':'UTF-8'}
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-3 control-label">
                        {l s='Hour' mod='chronopost'}
					</label>
					<div class="col-lg-3">
                        {$hour_rdv_on|escape:'quotes':'UTF-8'}
					</div>
					<div class="col-lg-3">
                        {$minute_rdv_on|escape:'quotes':'UTF-8'}
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<i class="icon-calendar"></i> {l s='Days with no collection' mod='chronopost'}
				</div>


				<div class="form-group">
					<label class="col-lg-3 control-label">
                        {l s='From:' mod='chronopost'}
					</label>
					<div class="col-lg-9">
                        {$day_rdv_close_start|escape:'quotes':'UTF-8'}
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-3 control-label">
                        {l s='Hour' mod='chronopost'}
					</label>
					<div class="col-lg-3">
                        {$hour_rdv_close_start|escape:'quotes':'UTF-8'}
					</div>
					<div class="col-lg-3">
                        {$minute_rdv_close_start|escape:'quotes':'UTF-8'}
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-3 control-label">
                        {l s='Until:' mod='chronopost'}
					</label>
					<div class="col-lg-9">
                        {$day_rdv_close_end|escape:'quotes':'UTF-8'}
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-3 control-label">
                        {l s='Hour' mod='chronopost'}
					</label>
					<div class="col-lg-3">
                        {$hour_rdv_close_end|escape:'quotes':'UTF-8'}
					</div>
					<div class="col-lg-3">
                        {$minute_rdv_close_end|escape:'quotes':'UTF-8'}
					</div>
				</div>
			</div>


			<div class="panel">
				<div class="panel-heading">
					<i class="icon-money"></i> {l s='rdv pricing' mod='chronopost'}
				</div>
				<div class="form-group">
					<div class="col-sm-3"></div>
					<div class="col-sm-2">{l s='RDV slot' mod='chronopost'} 1</div>
					<div class="col-sm-2">{l s='RDV slot' mod='chronopost'} 2</div>
					<div class="col-sm-2">{l s='RDV slot' mod='chronopost'} 3</div>
					<div class="col-sm-2">{l s='RDV slot' mod='chronopost'} 4</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">{l s='State' mod='chronopost'}</label>
					<div class="col-sm-2">
						<select name="chronoparams[rdv][state1]">
							<option value="1"{if $rdv_state1 == 1} selected{/if}>{l s='Open' mod='chronopost'}</option>
							<option value="0"{if $rdv_state1 == 0} selected{/if}>{l s='Closed' mod='chronopost'}</option>
						</select>
					</div>
					<div class="col-sm-2">
						<select name="chronoparams[rdv][state2]">
							<option value="1"{if $rdv_state2 == 1} selected{/if}>{l s='Open' mod='chronopost'}</option>
							<option value="0"{if $rdv_state2 == 0} selected{/if}>{l s='Closed' mod='chronopost'}</option>
						</select>
					</div>
					<div class="col-sm-2">
						<select name="chronoparams[rdv][state3]">
							<option value="1"{if $rdv_state3 == 1} selected{/if}>{l s='Open' mod='chronopost'}</option>
							<option value="0"{if $rdv_state3 == 0} selected{/if}>{l s='Closed' mod='chronopost'}</option>
						</select>
					</div>
					<div class="col-sm-2">
						<select name="chronoparams[rdv][state4]">
							<option value="1"{if $rdv_state4 == 1} selected{/if}>{l s='Open' mod='chronopost'}</option>
							<option value="0"{if $rdv_state4 == 0} selected{/if}>{l s='Closed' mod='chronopost'}</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3">{l s='Pricing' mod='chronopost'}</label>
					<div class="col-sm-2">
						<div class="input-group">
							<input type="text" name="chronoparams[rdv][price1]" value="{$rdv_price1|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">€</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="input-group">
							<input type="text" name="chronoparams[rdv][price2]" value="{$rdv_price2|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">€</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="input-group">
							<input type="text" name="chronoparams[rdv][price3]" value="{$rdv_price3|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">€</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="input-group">
							<input type="text" name="chronoparams[rdv][price4]" value="{$rdv_price4|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">€</div>
						</div>
					</div>
				</div>
			</div>


			<h2 id="pricing">{l s='Configure pricing' mod='chronopost'}</h2>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-money"></i> {l s='Corsica' mod='chronopost'}
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Configurable amount according to your pricing policy. However, the amount charged by Chronopost corresponds to pricing policy specified in your contract.' mod='chronopost'}" data-html="true">{l s='Supplement for deliveries to Corsica' mod='chronopost'}</span></label>

					<div class="col-lg-9">
						<div class="input-group">
							<div class="input-group-addon">+</div>
							<input name="chronoparams[corsica][supplement]" type="text"
								   value="{$corsica_supplement|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">€</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-cloud-download"></i> {l s='Pricing with QuickCost' mod='chronopost'}
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3"><span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Quickcost will calculate the cost of an item, depending on the rates negociated with Chronopost. This option replaces the use of the fee schedule.' mod='chronopost'}" data-html="true">{l s='Activate Quickcost' mod='chronopost'}</span></label>

					<div class="col-lg-9">
						<select name="chronoparams[quickcost][enabled]">
							<option value="0"{if $quickcost_enabled != 1} selected{/if}>{l s='No' mod='chronopost'}</option>
							<option value="1"{if $quickcost_enabled == 1} selected{/if}>{l s='Yes' mod='chronopost'}</option>
						</select>
					</div>


				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Margin on QuickCost prices' mod='chronopost'}</span></label>

					<div class="col-lg-9">
						<div class="input-group">
							<div class="input-group-addon">+</div>
							<input name="chronoparams[quickcost][supplement]" type="text"
								   value="{$quickcost_supplement|escape:'htmlall':'UTF-8'}"/>
							<div class="input-group-addon">%</div>
						</div>
					</div>
				</div>

				<div class="panel-footer">
					<button type="submit" class="btn btn-default pull-right" name="submitChronoConfig"><i class="process-icon-save"></i> {l s='Save' mod='chronopost'}</button>
				</div>
			</div>
		</form>
	</div>
</div>
