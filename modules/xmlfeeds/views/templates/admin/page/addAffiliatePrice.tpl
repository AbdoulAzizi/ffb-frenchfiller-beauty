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
{$message}
<form action="{$postUrl|escape:'htmlall':'UTF-8'}" method="post">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cog"></i> {l s='Create new modified price' mod='xmlfeeds'}
        </div>
        <div class="row">
            <table border="0" width="100%" cellpadding="3" cellspacing="0">
                 <tr>
                    <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}translation.gif" /></td>
                    <td width="140"><b>{l s='Name:' mod='xmlfeeds'}</b></td>
                    <td>
                        <input style="max-width: 462px;" type="text" name="xml_name" value="" required>
                    </td>
                </tr>
                <tr>
                    <td width="20" class="al-t" style="padding-top: 6px;"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}invoice.gif" /></td>
                    <td width="140" class="al-t" style="padding-top: 6px;"><b>{l s='Price formula:' mod='xmlfeeds'}</b></td>
                    <td>
                        <input style="max-width: 462px;" type="text" name="price" value="" required>
                        <div class="bl_comments price-formula-text">[{l s='Words' mod='xmlfeeds'} "<span>base_price</span>", "<span>sale_price</span>", "<span>tax_price</span>", "<span>shipping_price</span>", "<span>price_without_discount</span>", "<span>wholesale_price</span>" {l s='will be replaced by appropriate product value. Example of a formula: sale_price - 15' mod='xmlfeeds'}]</div>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="add_affiliate_action" value="1">
            <center><input type="submit" name="add_affiliate_price" value="{l s='Create' mod='xmlfeeds'}" class="button" /></center>
            {if !empty($prices)}
                <br/><hr/>
                <ul class="bl_affiliate_prices_list">
                    {foreach $prices as $p}
                        <li>
                            {$p.affiliate_name|escape:'htmlall':'UTF-8'}: <span style="color: #268CCD">{$p.affiliate_formula|escape:'htmlall':'UTF-8'}</span>
                            <a href="{$full_address_no_t|escape:'htmlall':'UTF-8'}&add_affiliate_price=1&delete_affiliate_price={$p.affiliate_id|escape:'htmlall':'UTF-8'}{$token|escape:'htmlall':'UTF-8'}" onclick="return confirm('{l s='Are you sure you want to delete?' mod='xmlfeeds'}')">
                                <img style="margin-bottom:2px;" alt="{l s='Delete' mod='xmlfeeds'}" title="{l s='Delete' mod='xmlfeeds'}" src="{$moduleImgPath|escape:'htmlall':'UTF-8'}delete.gif"></a><br/>
                            <div class="bl_comments" style="margin-top: 4px; margin-bottom: 4px;">URL: {$rootFile|escape:'htmlall':'UTF-8'}id=<b>FEED_ID</b>&affiliate={$p.affiliate_name|escape:'htmlall':'UTF-8'}</div>
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </div>
    </div>
</form>