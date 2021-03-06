<div class="m-b-1 m-t-1">
    <h2>{l s='Custom Attribute from module' mod='wrd_product'}</h2>
    
        <fieldset class="form-group">
            {*<div class="col-lg-12 col-xl-4">
                <label class="form-control-label">{l s='my custom field' mod='hhproduct'}</label>
                <input type="text" name="custom_field" class="form-control" {if $custom_field && $custom_field != ''}value="{$custom_field}"{/if}/>
                <label class="form-control-label">{l s='my custom lang field' mod='hhproduct'}</label>
                <div class="translations tabbable">
                    <div class="translationsFields tab-content">
                        {foreach from=$languages item=language }
                            <div class="tab-pane translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                                <input type="text" name="custom_field_lang_{$language.id_lang}" class="form-control" {if isset({$custom_field_lang[$language.id_lang]}) && {$custom_field_lang[$language.id_lang]} != ''}value="{$custom_field_lang[$language.id_lang]}"{/if}/>
                            </div>    
                        {/foreach}    
                    </div>
                </div>
            </div>*}
            {* Peter Bug 1.7.7 il faut ajouter translation-field *}
            <div class="col-lg-12 col-xl-12">        
                <label class="form-control-label">{l s='Composition' mod='wrd_product'}</label>
                <div class="translations tabbable">
                    <div class="translationsFields tab-content bordered">
                        {foreach from=$languages item=language }
                            <div class="tab-pane translation-field translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                               <textarea name="custom_composition_field_lang_wysiwyg_{$language.id_lang}" class="autoload_rte">{if isset({$custom_composition_field_lang_wysiwyg[$language.id_lang]}) && {$custom_composition_field_lang_wysiwyg[$language.id_lang]} != ''}{$custom_composition_field_lang_wysiwyg[$language.id_lang]}{/if}</textarea>
                            </div>    
                        {/foreach}    
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12">
                <label class="form-control-label">{l s='Mode d\'utilisation' mod='wrd_product'}</label>
                <div class="translations tabbable">
                    <div class="translationsFields tab-content bordered">
                        {foreach from=$languages item=language }
                            <div class="tab-pane translation-field translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                                <textarea name="custom_utilisation_field_lang_wysiwyg_{$language.id_lang}" class="autoload_rte">{if isset({$custom_utilisation_field_lang_wysiwyg[$language.id_lang]}) && {$custom_utilisation_field_lang_wysiwyg[$language.id_lang]} != ''}{$custom_utilisation_field_lang_wysiwyg[$language.id_lang]}{/if}</textarea>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </fieldset>
    
        <div class="clearfix"></div>
</div>