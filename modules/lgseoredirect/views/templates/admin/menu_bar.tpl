{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}
<div id="menubar">
    <fieldset>
        <a id="buttonindividualredirect" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
            <i class="icon-plus-square"></i>&nbsp;{l s='Create a redirect' mod='lgseoredirect'}
        </a>
        <a id="buttonbulkredirects" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
            <i class="icon-cloud-upload"></i>&nbsp;{l s='Import redirects in bulk' mod='lgseoredirect'}
        </a>
        <a id="buttonlistredirects" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
            <i class="icon-list"></i>&nbsp;{l s='List of created redirects' mod='lgseoredirect'} ({$countredirects|intval})
        </a>
        {if isset($lgseoredirect_pagesnotfoundenabled) && $lgseoredirect_pagesnotfoundenabled}
            <a id="buttonpagesnotfound" class="lgseoredirect_menubarbutton button btn btn-default" style="width:280px;">
                <i class="icon-list"></i>&nbsp;{l s='pages not found' mod='lgseoredirect'} ({$lgseoredirects_count_pages_not_found|intval})
            </a>
        {/if}
    </fieldset>
</div>
