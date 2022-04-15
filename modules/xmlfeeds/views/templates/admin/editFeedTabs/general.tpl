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
    <tr style="display: none;">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}nav-home.gif" /></td>
        <td width="200"><b>Feed id:</b></td>
        <td>
            <input style="width: 35px;" type="text" readonly="readonly" name="feed_id" value="{$page|escape:'htmlall':'UTF-8'}">

        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}translation.gif" /></td>
        <td width="200"><b>{l s='Feed name:' mod='xmlfeeds'}</b></td>
        <td>
            <input style="width: 290px;" type="text" name="name" value="{$s.name|escape:'htmlall':'UTF-8'}" required>
            {if !empty($s.feed_mode)}<img style="margin-top: -1px;" class="feed_type_id" alt="Feed type" title="Feed type" src="../modules/{$name|escape:'htmlall':'UTF-8'}/views/img/type_{$s.feed_mode|escape:'htmlall':'UTF-8'}.png" />{/if}
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}access.png" /></td>
        <td width="200"><b>{l s='Feed status:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="xmf_feed_status">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='xmf_feed_status' name='status' status=$s.status}
            </label>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}supplier.gif" /></td>
        <td width="200"><b>{l s='Shipping country:' mod='xmlfeeds'} </b></td>
        <td>
            <select name="shipping_country">
                <option value="0">{l s='Default' mod='xmlfeeds'}</option>
                {foreach $countries as $c}
                    <option value="{$c.id_country|escape:'htmlall':'UTF-8'}" {if $s.shipping_country == $c.id_country}selected{/if}>{$c.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr class="only-product order-settings">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}AdminBackup.gif" /></td>
        <td width="200"><b>{l s='Use cron:' mod='xmlfeeds'} </b></td>
        <td>
            <label for="use_cron">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='use_cron' name='use_cron' status=$s.use_cron}
            </label>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}multishop_config.png" /></td>
        <td width="200"><b>{l s='Split by combination:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="split_by_combination">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='split_by_combination' name='split_by_combination' status=$s.split_by_combination}
            </label>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}copy_files.gif" /></td>
        <td width="200"><b>{l s='Split feed:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="split_feed" style="margin-top: 4px;">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='split_feed' name='split_feed' status=$s.split_feed}
            </label>
            <input style="width: 130px; margin-left: 14px;" placeholder="{l s='Products per feed' mod='xmlfeeds'}" type="text" name="split_feed_limit" value="{if !empty($s.split_feed_limit)}{$s.split_feed_limit|escape:'htmlall':'UTF-8'}{/if}" size="6">
        </td>
    </tr>
    {if empty($s.use_cron)}
        <tr>
            <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}database_gear.gif" /></td>
            <td width="200"><b>{l s='Use cache:' mod='xmlfeeds'}</b></td>
            <td>
                <label for="use_cache" style="margin-top: 4px;">
                    {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='use_cache' name='use_cache' status=$s.use_cache}
                </label>
                <input style="width: 130px; margin-left: 14px;" placeholder="{l s='Period in minutes' mod='xmlfeeds'}" type="text" name="cache_time" value="{if !empty($s.cache_time)}{$s.cache_time|escape:'htmlall':'UTF-8'}{/if}" size="6">
                {if $s.use_cache eq 1 && empty($s.cache_time)}
                    <div class="alert-small-blmod ">{l s='Please enter cache period in minutes (e.g. 180)' mod='xmlfeeds'}</div>
                {/if}
            </td>
        </tr>
    {/if}
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}computer_key.png" /></td>
        <td width="200"><b>{l s='Protect by IP addresses:' mod='xmlfeeds'}</b></td>
        <td>
            <input type="text" name="protect_by_ip" value="{$s.protect_by_ip|escape:'htmlall':'UTF-8'}">
            <div class="bl_comments">{l s='[Use a comma to separate them (e.g. 11.10.1.1, 22.2.2.3)]' mod='xmlfeeds'}</div>
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}htaccess.gif" /></td>
        <td width="200"><b>{l s='Protect with password:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="use_password" style="margin-top: 4px;">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='use_password' name='use_password' status=$s.use_password}
            </label>
            <input style="display: inline-block; width: 130px; margin-left: 14px;" placeholder="{l s='Password' mod='xmlfeeds'}" type="password" name="password" value="{if !empty($s.password)}{$s.password|escape:'htmlall':'UTF-8'}{/if}" size="6">
            {if $s.use_password eq 1 && empty($s.password)}
                <div class="alert-small-blmod">{l s='Please enter a password' mod='xmlfeeds'}</div>
            {/if}
        </td>
    </tr>
    {if $s.feed_mode == 'vi'}
        <tr>
            <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}coupon.gif" /></td>
            <td width="200"><b>{l s='Vivino, bottle size:' mod='xmlfeeds'}</b></td>
            <td>
                <select name="vivino_bottle_size" style="width: 273px; display: inline-block;">
                    <option value="0">{l s='none' mod='xmlfeeds'}</option>
                    {foreach $productFeatures as $f}
                        <option value="{$f.id_feature|escape:'htmlall':'UTF-8'}"{if $s.vivino_bottle_size eq $f.id_feature} selected{/if}>{$f.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <input style="width: 75px; margin-top: -3px;" type="text" name="vivino_bottle_size_default" value="{$s.vivino_bottle_size_default|escape:'htmlall':'UTF-8'}" placeholder="{l s='Default size' mod='xmlfeeds'}" />
            </td>
        </tr>
        <tr>
            <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}cart.gif" /></td>
            <td width="200"><b>{l s='Vivino, lot size:' mod='xmlfeeds'}</b></td>
            <td>
                <select name="vivino_lot_size" style="width: 273px; display: inline-block;">
                    <option value="0">{l s='none' mod='xmlfeeds'}</option>
                    {foreach $productFeatures as $f}
                        <option value="{$f.id_feature|escape:'htmlall':'UTF-8'}"{if $s.vivino_lot_size eq $f.id_feature} selected{/if}>{$f.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <input style="width: 75px; margin-top: -3px;" type="text" name="vivino_lot_size_default" value="{$s.vivino_lot_size_default|escape:'htmlall':'UTF-8'}" placeholder="{l s='Default size' mod='xmlfeeds'}" />
            </td>
        </tr>
    {/if}
    {if $s.feed_mode == 'spa'}
        <tr>
            <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}coupon.gif" /></td>
            <td width="200"><b>{l s='Spartoo, size:' mod='xmlfeeds'}</b></td>
            <td>
                <select name="spartoo_size">
                    <option value="0">{l s='none' mod='xmlfeeds'}</option>
                    {foreach $productAttributes as $f}
                        <option value="{$f.id_attribute_group|escape:'htmlall':'UTF-8'}"{if $s.spartoo_size eq $f.id_attribute_group} selected{/if}>{$f.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
    {/if}
    {if $s.feed_mode == 's'}
        <tr>
            <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}coupon.gif" /></td>
            <td width="200"><b>{l s='Skroutz Analytics ID:' mod='xmlfeeds'}</b></td>
            <td>
                <input type="text" name="skroutz_analytics_id" value="{$s.skroutz_analytics_id|escape:'htmlall':'UTF-8'}">
                <div class="bl_comments">{l s='[If you want to use Skroutz Analytics, please insert shop account ID]' mod='xmlfeeds'}</div>
            </td>
        </tr>
    {/if}
</table>