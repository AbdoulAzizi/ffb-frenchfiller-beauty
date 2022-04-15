{*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from MigrationPro MMC
* Use, copy, modification or distribution of this source file without written
* license agreement from the MigrationPro MMC is strictly forbidden.
* In order to obtain a license, please contact us: migrationprommc@gmail.com
*
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise a une licence commerciale
* concedee par la societe MigrationPro MMC
* Toute utilisation, reproduction, modification ou distribution du present
* fichier source sans contrat de licence ecrit de la part de la MigrationPro MMC est
* expressement interdite.
* Pour obtenir une licence, veuillez contacter la MigrationPro MMC a l'adresse: migrationprommc@gmail.com
*
* @package   MigrationPro: Prestashop To PrestaShop
* @author    Edgar I.
* @copyright Copyright (c) 2012-2016 MigrationPro MMC
* @license   Commercial license
*}

<div style="font-size:1.2rem; font-weight:500; margin-top:-5px; margin-bottom:5px">
  {l s='Connection with source PrestaShop cart was not established.' mod='migrationpro'}</div>
    <p>&raquo; {l s='Check the connector module.' mod='migrationpro'}
      <a style="display: inline;color: #00aff0" href="{$sourceURL|escape:'htmlall':'UTF-8'}" target="_blank">
            {l s='Test now' mod='migrationpro'}</a> </p>
    <p>&raquo; {l s='If you enabled an SSL certificate on your (old) source PrestaShop cart, please try with a HTTPS protocol.' mod='migrationpro'}</p>
    <p>&raquo; {l s='Try URL with or without "www" prefix. Check your Apache or Nginx server redirection.' mod='migrationpro'}</p>
    <p>&raquo; {l s='Disable maintenance mode or add (new) target PrestaShop server IP address.' mod='migrationpro'}</p>
