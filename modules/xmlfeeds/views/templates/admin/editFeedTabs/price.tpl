{*
 * 2010-2021 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2021 Bl Modules
 * @license
*}
<table border="0" width="100%" cellpadding="3" cellspacing="0">
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}dollar.gif" /></td>
        <td width="140"><b>{l s='Currency:' mod='xmlfeeds'}</b></td>
        <td>
            <select name="currency_id">
                <option value="">{l s='Default' mod='xmlfeeds'}</option>
                {foreach $currencyList as $c}
                    <option value="{$c.id|escape:'htmlall':'UTF-8'}"{if $s.currency_id == $c.id} selected{/if}>{$c.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}payment.gif" /></td>
        <td width="200"><b>{l s='Price with currency:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="price_with_currency">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='price_with_currency' name='price_with_currency' status=$s.price_with_currency}
            </label>
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}money.gif" /></td>
        <td width="200"><b>{l s='Price format:' mod='xmlfeeds'}</b></td>
        <td>
            <select name="price_format_id">
                <option value="0"{if empty($s.price_format_id)} selected{/if}>{l s='Default' mod='xmlfeeds'}</option>
                {foreach $priceFromList as $pfId => $pf}
                    <option value="{$pfId|escape:'htmlall':'UTF-8'}"{if $s.price_format_id == $pfId} selected{/if}>{$pf|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}tab-shipping.gif" /></td>
        <td width="200"><b>{l s='Shipping price:' mod='xmlfeeds'}</b></td>
        <td>
            <label class="blmod_mr20">
                <input type="radio" name="shipping_price_mode" value="0"{if $s.shipping_price_mode eq 0} checked="checked"{/if}> {l s='Default carrier' mod='xmlfeeds'}
            </label>
            <label class="blmod_mr20">
                <input type="radio" name="shipping_price_mode" value="1"{if $s.shipping_price_mode eq 1} checked="checked"{/if}> {l s='According to the country' mod='xmlfeeds'}
            </label>
        </td>
    </tr>
</table>