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
<div class="table_name">{$block_name|escape:'htmlall':'UTF-8'}</div>
<div class="cb"></div>
<div class="cn_table{$scrollClass|escape:'htmlall':'UTF-8'}">
    <div class="cn_top">
        <div class="cn_name">
            {l s='Field' mod='xmlfeeds'}
        </div>
        {if empty($only_checkbox)}
            <div class="cn_name_xml">
                {l s='Column name' mod='xmlfeeds'}
            </div>
        {/if}
        <div class="cn_status">
            {l s='Status' mod='xmlfeeds'}
        </div>
    </div>
    {foreach $fields as $f}
        <div class="cn_line">
            <div class="cn_name">
                {$f.title|escape:'htmlall':'UTF-8'}
            </div>
            {if empty($f.is_only_checkbox)}
                {if $f.field_name == 'id_product+product'}
                    <div class="cn_name_xml">
                        <input style="width: 75px;" type="text" name="product_id_prefix" value="{if !empty($f.product_id_prefix)}{$f.product_id_prefix}{/if}" placeholder="prefix"/>
                        <input style="width: 140px;" type="text" name="{$f.field_name|escape:'htmlall':'UTF-8'}" value="{$f.value|escape:'htmlall':'UTF-8'}" size="30"{if !empty($f.placeholder)} placeholder="{$f.placeholder}"{/if}/>
                    </div>
                {elseif !empty($f.isEditPriceField)}
                    <div class="cn_name_xml">
                        <input style="width: 140px;" type="text" name="{$f.field_name|escape:'htmlall':'UTF-8'}" value="{$f.value|escape:'htmlall':'UTF-8'}" size="30"{if !empty($f.placeholder)} placeholder="{$f.placeholder}"{/if}/>
                        <span style="cursor: pointer; text-decoration: underline; color: #0077a4; font-size: 12px;" class="open-edit-price-action" data-pid="{$f.field_name_safe|escape:'htmlall':'UTF-8'}">{l s='Edit price' mod='xmlfeeds'}</span>
                        <div id="edit-price-box_{$f.field_name_safe|escape:'htmlall':'UTF-8'}" style="display: none;">
                            <select name="edit_price_type[{$f.field_name}]" style="display: inline-block; width: 110px;">
                                {foreach $f.editPriceTypeList as $epId => $epVal}
                                    <option value="{$epId}" {if $f.editPriceType == $epId}selected{/if} >{$epVal|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                            <input style="width: 90px;" type="text" name="edit_price_value[{$f.field_name|escape:'htmlall':'UTF-8'}]" placeholder="value" value="{$f.editPriceValue|escape:'htmlall':'UTF-8'}">
                        </div>
                    </div>
                {else}
                    <div class="cn_name_xml">
                        <input type="text" name="{$f.field_name}" value="{$f.value}" size="30"{if !empty($f.placeholder)} placeholder="{$f.placeholder}"{/if}/>
                    </div>
                {/if}
            {/if}
            <div class="cn_status">
                <label>
                    {include file="{$tpl_dir}/views/templates/admin/helper/status.tpl" id=$f.status_name name=$f.status_name status=$f.status_value}
                </label>
            </div>
        </div>
    {/foreach}
</div>
<div class="cb"></div>
