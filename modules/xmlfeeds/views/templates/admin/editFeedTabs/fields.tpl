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
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}tab-tools.gif" /></td>
        <td width="200"><b>{l s='Add CDATA:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="cdata_status">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='cdata_status' name='cdata_status' status=$s.cdata_status}
            </label>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}page_white_text.png" /></td>
        <td width="200"><b>{l s='Drop html tags:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="html_tags_status">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='html_tags_status' name='html_tags_status' status=$s.html_tags_status}
            </label>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}tab-categories.gif" /></td>
        <td width="200"><b>{l s='Category map:' mod='xmlfeeds'}</b></td>
        <td>
            <select name="category_map_id">
                <option value="0">{l s='None' mod='xmlfeeds'}</option>
                {foreach $categoryMapList as $c}
                    <option value="{$c.id|escape:'htmlall':'UTF-8'}" {if $s.category_map_id == $c.id}selected{/if}>{$c.title|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}coupon.gif" /></td>
        <td width="200"><b>{l s='Attribute map:' mod='xmlfeeds'}</b></td>
        <td>
            <select name="attribute_map_id">
                <option value="0">{l s='None' mod='xmlfeeds'}</option>
                {foreach $attributeMapList as $c}
                    <option value="{$c.id|escape:'htmlall':'UTF-8'}" {if $s.attribute_map_id == $c.id}selected{/if}>{$c.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}box.gif" /></td>
        <td width="200"><b>{l s='Feature map:' mod='xmlfeeds'}</b></td>
        <td>
            <select name="feature_map_id">
                <option value="0">{l s='None' mod='xmlfeeds'}</option>
                {foreach $featureMapList as $c}
                    <option value="{$c.id|escape:'htmlall':'UTF-8'}" {if $s.feature_map_id == $c.id}selected{/if}>{$c.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}delivery_in.gif" /></td>
        <td width="200"><b>{l s='Delivery times, in stock:' mod='xmlfeeds'}</b></td>
        <td>
            <input type="text" name="in_stock_text" value="{$s.in_stock_text|escape:'htmlall':'UTF-8'}" size="6">
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}delivery_out.gif" /></td>
        <td width="200"><b>{l s='Delivery, out of stock:' mod='xmlfeeds'}</b></td>
        <td>
            <input type="text" name="out_of_stock_text" value="{$s.out_of_stock_text|escape:'htmlall':'UTF-8'}" size="6">
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}delivery_in.gif" /></td>
        <td width="200"><b>{l s='Delivery, on demand:' mod='xmlfeeds'}</b></td>
        <td>
            <input type="text" name="on_demand_stock_text" value="{$s.on_demand_stock_text|escape:'htmlall':'UTF-8'}" size="6">
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}comment.gif" /></td>
        <td width="200"><b>{l s='Header rows:' mod='xmlfeeds'}</b></td>
        <td>
            <textarea name="header_information" style="max-width: 470px; width: 100%; height: 60px;">{$s.header_information|escape:'htmlall':'UTF-8'}</textarea>
            <div class="bl_comments">{l s='[Make sure that you have entered validate XML code]' mod='xmlfeeds'}</div>
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}comment.gif" /></td>
        <td width="200"><b>{l s='Footer rows:' mod='xmlfeeds'}</b></td>
        <td>
            <textarea name="footer_information" style="max-width: 470px; width: 100%; height: 60px;">{$s.footer_information|escape:'htmlall':'UTF-8'}</textarea>
            <div class="bl_comments">{l s='[Make sure that you have entered validate XML code]' mod='xmlfeeds'}</div>
        </td>
    </tr>
    <tr>
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}edit.gif" /></td>
        <td width="200"><b>{l s='Extra feed rows:' mod='xmlfeeds'}</b></td>
        <td>
            <textarea name="extra_feed_row" style="max-width: 470px; width: 100%; height: 60px;">{$s.extra_feed_row|escape:'htmlall':'UTF-8'}</textarea>
            <div class="bl_comments">{l s='[Make sure that you have entered validate XML code]' mod='xmlfeeds'}</div>
        </td>
    </tr>
    <tr class="only-product">
        <td width="20"><img src="{$moduleImgPath|escape:'htmlall':'UTF-8'}navigation.png" /></td>
        <td width="200"><b>{l s='Feed generation time:' mod='xmlfeeds'}</b></td>
        <td>
            <label for="feed_generation_time" style="margin-top: 4px;">
                {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id='feed_generation_time' name='feed_generation_time' status=$s.feed_generation_time}
            </label>
            <input style="width: 175px; margin-left: 14px;" type="text" name="feed_generation_time_name" value="{$s.feed_generation_time_name|escape:'htmlall':'UTF-8'}" placeholder="Field name" size="6">
        </td>
    </tr>
</table>