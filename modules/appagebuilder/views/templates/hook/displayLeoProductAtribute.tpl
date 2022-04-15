{*
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
{if isset($st_att_list_groups)}
<div class="st_attr_list_container {if $st_att_list_center} st_attr_list_text_center{/if}">
{foreach from=$st_att_list_groups key=id_attribute_group item=group}
    {if $group.attributes|@count && ($st_att_list_show==1 || array_sum($group.attributes_quantity))}
        <div class="st_attr_list_item">
            <strong>{$group.name} :</strong>
            {if ($group.group_type == 'color' && $st_att_list_color)}
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                    {if $st_att_list_show==1 || $group.attributes_quantity[$id_attribute]}
                        <span class="st_attr_list_swatch" title="{$group_attribute}" style="{if $group.colors[$id_attribute]['type']}background-image: url('{$group.colors[$id_attribute]['value']}');{else}background-color:{$group.colors[$id_attribute]['value']};{/if}"></span>
                    {/if}
                {/foreach}
            {else}
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                    {if $st_att_list_show==1 || $group.attributes_quantity[$id_attribute]}
                        <span class="st_attr_list_text">{$group_attribute}</span>
                    {/if}
                {/foreach}
            {/if}
        </div>
    {/if}
{/foreach}
</div>
{/if}