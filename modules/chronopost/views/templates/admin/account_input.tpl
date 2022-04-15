<select name="account[{$id_order|escape:'htmlall':'UTF-8'}]" {if $disable} disabled {/if}>
    {foreach from=$available_accounts item=account name=accounts}
        <option value="{$account['account']|escape:'htmlall':'UTF-8'}" {if ($default_account['account']==$account['account'] && $disable==false) || $account['account']==$account_used } selected {/if}>{$account['accountname']|escape:'htmlall':'UTF-8'}</option>
    {/foreach}
</select>
{if $disable}
    <input name="account[{$id_order|escape:'htmlall':'UTF-8'}]" type="hidden" value="{$account_used}">
{/if}