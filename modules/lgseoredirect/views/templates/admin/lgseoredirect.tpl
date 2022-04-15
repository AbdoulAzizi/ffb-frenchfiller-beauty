{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}
<h2>{$lgseoredirect_displayName|escape:'htmlall':'UTF-8'}</h2><br>
{include './variables.tpl'}
{include './menu_bar.tpl'}
{include './create_redirect.tpl'}
{include './import_redirects_bulk.tpl'}
{include './list.tpl'}
{if isset($lgseoredirect_pagesnotfoundenabled) && $lgseoredirect_pagesnotfoundenabled}
    {include './pages_not_found.tpl'}
{/if}
