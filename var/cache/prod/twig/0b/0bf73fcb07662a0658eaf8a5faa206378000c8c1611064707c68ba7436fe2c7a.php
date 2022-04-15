<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* __string_template__831aba286e0a735148dd48ff078609d3ac7f55dd4c167cdcb9cd24f22b928e84 */
class __TwigTemplate_b7b02bb2cabe74cde3abd0fd1765fd999fcbae8f076ca8b7cd08c2221d69a323 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'stylesheets' => [$this, 'block_stylesheets'],
            'extra_stylesheets' => [$this, 'block_extra_stylesheets'],
            'content_header' => [$this, 'block_content_header'],
            'content' => [$this, 'block_content'],
            'content_footer' => [$this, 'block_content_footer'],
            'sidebar_right' => [$this, 'block_sidebar_right'],
            'javascripts' => [$this, 'block_javascripts'],
            'extra_javascripts' => [$this, 'block_extra_javascripts'],
            'translate_javascripts' => [$this, 'block_translate_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"fr\">
<head>
  <meta charset=\"utf-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
<meta name=\"robots\" content=\"NOFOLLOW, NOINDEX\">

<link rel=\"icon\" type=\"image/x-icon\" href=\"/img/favicon.ico\" />
<link rel=\"apple-touch-icon\" href=\"/img/app_icon.png\" />

<title>SEO & URL • French Filler Beauty</title>

  <script type=\"text/javascript\">
    var help_class_name = 'AdminMeta';
    var iso_user = 'fr';
    var lang_is_rtl = '0';
    var full_language_code = 'fr';
    var full_cldr_language_code = 'fr-FR';
    var country_iso_code = 'FR';
    var _PS_VERSION_ = '1.7.7.3';
    var roundMode = 2;
    var youEditFieldFor = '';
        var new_order_msg = 'Une nouvelle commande a été passée sur votre boutique.';
    var order_number_msg = 'Numéro de commande : ';
    var total_msg = 'Total : ';
    var from_msg = 'Du ';
    var see_order_msg = 'Afficher cette commande';
    var new_customer_msg = 'Un nouveau client s\\'est inscrit sur votre boutique';
    var customer_name_msg = 'Nom du client : ';
    var new_msg = 'Un nouveau message a été posté sur votre boutique.';
    var see_msg = 'Lire le message';
    var token = '63ddb2edcf70016c36b329ff814a2090';
    var token_admin_orders = '2b44d6c0052b668d51cb2abc2e440775';
    var token_admin_customers = 'd3e952f4bde1fab15f7ca607b038ec09';
    var token_admin_customer_threads = 'd778549e3624d1346499314214d392ea';
    var currentIndex = 'index.php?controller=AdminMeta';
    var employee_token = 'e27084d152b3ea4dbcbb272fd7cbf91f';
    var choose_language_translate = 'Choisissez la langue :';
    var default_language = '1';
    var admin_modules_link = '/admin984s0lgwu/index.php/improve/modules/catalog/recommended?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU';
    var admin_notification_get_link = '/admin984s0lgwu/index.php/common/notifications?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU';
    var admin_notification_push_link = '/admin984s0lgwu/index.php/common/notifications/ack?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU';
    var tab_modules_list = '';
    var update_success_msg = 'Mise à jour réussie';
    var errorLogin = 'PrestaShop n\\'a pas pu se connecter à Addons. Veuillez vérifier vos identifiants et votre connexion Internet.';
    var search_product_msg = 'Rechercher un produit';
  </script>

      <link href=\"/admin984s0lgwu/themes/new-theme/public/theme.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/js/jquery/plugins/chosen/jquery.chosen.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/js/jquery/plugins/fancybox/jquery.fancybox.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/admin984s0lgwu/themes/default/css/vendor/nv.d3.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/ps_mbo/views/css/recommended-modules.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/leofeature/views/css/back.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/emarketing/views/css/menuTabIcon.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/appagebuilder/views/css/admin/style.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/leoblog/views/css/admin/blogmenu.css\" rel=\"stylesheet\" type=\"text/css\"/>
      <link href=\"/modules/ets_abandonedcart/views/css/icon-admin.css\" rel=\"stylesheet\" type=\"text/css\"/>
  
  <script type=\"text/javascript\">
var appagebuilder_listshortcode_url = \"https:\\/\\/ffb.shinagency.mypreprod.fr\\/admin984s0lgwu\\/index.php?controller=AdminApPageBuilderShortcode&token=a7d077ecac2b3ae7fcd991e420dcbe1a&get_listshortcode=1\";
var appagebuilder_module_dir = \"\\/modules\\/appagebuilder\\/\";
var baseAdminDir = \"\\/admin984s0lgwu\\/\";
var baseDir = \"\\/\";
var changeFormLanguageUrl = \"\\/admin984s0lgwu\\/index.php\\/configure\\/advanced\\/employees\\/change-form-language?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\";
var currency = {\"iso_code\":\"EUR\",\"sign\":\"\\u20ac\",\"name\":\"Euro\",\"format\":null};
var currency_specifications = {\"symbol\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"currencyCode\":\"EUR\",\"currencySymbol\":\"\\u20ac\",\"numberSymbols\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"positivePattern\":\"#,##0.00\\u00a0\\u00a4\",\"negativePattern\":\"-#,##0.00\\u00a0\\u00a4\",\"maxFractionDigits\":2,\"minFractionDigits\":2,\"groupingUsed\":true,\"primaryGroupSize\":3,\"secondaryGroupSize\":3};
var host_mode = false;
var leofeature_module_dir = \"\\/modules\\/leofeature\\/\";
var number_specifications = {\"symbol\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"numberSymbols\":[\",\",\"\\u202f\",\";\",\"%\",\"-\",\"+\",\"E\",\"\\u00d7\",\"\\u2030\",\"\\u221e\",\"NaN\"],\"positivePattern\":\"#,##0.###\",\"negativePattern\":\"-#,##0.###\",\"maxFractionDigits\":3,\"minFractionDigits\":0,\"groupingUsed\":true,\"primaryGroupSize\":3,\"secondaryGroupSize\":3};
var prestashop = {\"debug\":false};
var show_new_customers = \"1\";
var show_new_messages = false;
var show_new_orders = \"1\";
var url_ajax_blog = \"https:\\/\\/ffb.shinagency.mypreprod.fr\\/modules\\/leoblog\\/adminajax.php\";
</script>
<script type=\"text/javascript\" src=\"/admin984s0lgwu/themes/new-theme/public/main.bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/jquery/plugins/jquery.chosen.js\"></script>
<script type=\"text/javascript\" src=\"/js/jquery/plugins/fancybox/jquery.fancybox.js\"></script>
<script type=\"text/javascript\" src=\"/js/admin.js?v=1.7.7.3\"></script>
<script type=\"text/javascript\" src=\"/admin984s0lgwu/themes/new-theme/public/cldr.bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/tools.js?v=1.7.7.3\"></script>
<script type=\"text/javascript\" src=\"/admin984s0lgwu/public/bundle.js\"></script>
<script type=\"text/javascript\" src=\"/js/vendor/d3.v3.min.js\"></script>
<script type=\"text/javascript\" src=\"/admin984s0lgwu/themes/default/js/vendor/nv.d3.min.js\"></script>
<script type=\"text/javascript\" src=\"/modules/appagebuilder/views/js/admin/function.js\"></script>
<script type=\"text/javascript\" src=\"/modules/ps_mbo/views/js/recommended-modules.js?v=2.0.1\"></script>
<script type=\"text/javascript\" src=\"/admin984s0lgwu/themes/default/js/bundle/module/module_card.js?v=1.7.7.3\"></script>
<script type=\"text/javascript\" src=\"/modules/leofeature/views/js/back.js\"></script>
<script type=\"text/javascript\" src=\"/modules/ets_abandonedcart/views/js/admin_all.js\"></script>
<script type=\"text/javascript\" src=\"/modules/ps_faviconnotificationbo/views/js/favico.js\"></script>
<script type=\"text/javascript\" src=\"/modules/ps_faviconnotificationbo/views/js/ps_faviconnotificationbo.js\"></script>

  <script>
  if (undefined !== ps_faviconnotificationbo) {
    ps_faviconnotificationbo.initialize({
      backgroundColor: '#DF0067',
      textColor: '#FFFFFF',
      notificationGetUrl: '/admin984s0lgwu/index.php/common/notifications?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU',
      CHECKBOX_ORDER: 1,
      CHECKBOX_CUSTOMER: 1,
      CHECKBOX_MESSAGE: 1,
      timer: 120000, // Refresh every 2 minutes
    });
  }
</script>
<script type=\"text/javascript\">
    var ETS_AC_LINK_REMINDER_ADMIN = \"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderEmail&token=7a9222d2815bf4d6d8b3edb8c789397f\";
    var ETS_AC_LINK_CAMPAIGN_TRACKING = \"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACTracking&token=0c8a64963b55d9218c234ff7fb85a9ba\";
    var ETS_AC_LOGO_LINK = \"https://ffb.shinagency.mypreprod.fr/img/french-filler-beauty-logo-1623309279.jpg\";
    var ETS_AC_IMG_MODULE_LINK = \"https://ffb.shinagency.mypreprod.fr/modules/ets_abandonedcart/views/img/origin/\";
    var ETS_AC_FULL_BASE_URL = \"https://ffb.shinagency.mypreprod.fr/\";
    var ETS_AC_ADMIN_CONTROLLER= \"AdminMeta\";
    var ETS_AC_TRANS = {};
    ETS_AC_TRANS['clear_tracking'] = \"Effacer le suivi\";
    ETS_AC_TRANS['email_temp_setting'] = \"Paramètres du modèle d'e-mail \";
    ETS_AC_TRANS['confirm_clear_tracking'] = \"Effacer le suivi supprimera également toutes les données du tableau de suivi de la campagne ainsi que les statistiques du tableau de bord. Voulez-vous vraiment effacer le suivi ?\";
    ETS_AC_TRANS['confirm_delete_lead_field'] = \"Voulez-vous supprimer ce champ ? \";
    ETS_AC_TRANS['lead_form_not_found'] = \"Formulaire de prospects n'existe pas \";
    ETS_AC_TRANS['lead_form_disabled'] = \"Formulaire de prospects est désactivé \";
</script>


";
        // line 125
        $this->displayBlock('stylesheets', $context, $blocks);
        $this->displayBlock('extra_stylesheets', $context, $blocks);
        echo "</head>

<body
  class=\"lang-fr adminmeta\"
  data-base-url=\"/admin984s0lgwu/index.php\"  data-token=\"OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\">

  <header id=\"header\" class=\"d-print-none\">

    <nav id=\"header_infos\" class=\"main-header\">
      <button class=\"btn btn-primary-reverse onclick btn-lg unbind ajax-spinner\"></button>

            <i class=\"material-icons js-mobile-menu\">menu</i>
      <a id=\"header_logo\" class=\"logo float-left\" href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDashboard&amp;token=b93641c1f55be04c3e4659fc1d40f0a7\"></a>
      <span id=\"shop_version\">1.7.7.3</span>

      <div class=\"component\" id=\"quick-access-container\">
        <div class=\"dropdown quick-accesses\">
  <button class=\"btn btn-link btn-sm dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" id=\"quick_select\">
    Accès rapide
  </button>
  <div class=\"dropdown-menu\">
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminOrders&amp;token=2b44d6c0052b668d51cb2abc2e440775\"
                 data-item=\"Commandes\"
      >Commandes</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminStats&amp;module=statscheckup&amp;token=a3e43d2672c4d4e34ca5cb54a39966f4\"
                 data-item=\"Évaluation du catalogue\"
      >Évaluation du catalogue</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderShortcode&amp;updateappagebuilder_shortcode=&amp;id_appagebuilder_shortcode=2&amp;token=a7d077ecac2b3ae7fcd991e420dcbe1a\"
                 data-item=\"Image produit bas\"
      >Image produit bas</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php/improve/modules/manage?token=5f564693604fdb9f47b55d572c9cdc3f\"
                 data-item=\"Modules installés\"
      >Modules installés</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCartRules&amp;addcart_rule&amp;token=4a5ee379e1cf68e5c4b07cf543ece297\"
                 data-item=\"Nouveau bon de réduction\"
      >Nouveau bon de réduction</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php/sell/catalog/products/new?token=5f564693604fdb9f47b55d572c9cdc3f\"
                 data-item=\"Nouveau produit\"
      >Nouveau produit</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php/sell/catalog/categories/new?token=5f564693604fdb9f47b55d572c9cdc3f\"
                 data-item=\"Nouvelle catégorie\"
      >Nouvelle catégorie</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderThemeConfiguration&amp;token=63ad87916cd4962919b1c8edfb7af3f6\"
                 data-item=\"policeecriture\"
      >policeecriture</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderEmail&amp;addets_abancart_campaign&amp;token=7a9222d2815bf4d6d8b3edb8c789397f\"
                 data-item=\"relance panier\"
      >relance panier</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminModules&amp;&amp;configure=leoslideshow&amp;editSlider=1&amp;id_slide=18&amp;id_group=9&amp;token=b0a5059f84287fbecc7e962de2b161f9\"
                 data-item=\"SLIDE\"
      >SLIDE</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminModules&amp;&amp;configure=leoslideshow&amp;editSlider=1&amp;id_slide=21&amp;id_group=11&amp;token=b0a5059f84287fbecc7e962de2b161f9\"
                 data-item=\"SLIDE MOBILE\"
      >SLIDE MOBILE</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderShortcode&amp;updateappagebuilder_shortcode=&amp;id_appagebuilder_shortcode=3&amp;token=a7d077ecac2b3ae7fcd991e420dcbe1a\"
                 data-item=\"styliser page description\"
      >styliser page description</a>
          <a class=\"dropdown-item\"
         href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php/improve/international/translations/?lang=fr&amp;type=themes&amp;locale=fr-FR&amp;selected=leo_beautique&amp;-jp1-tbh_lTk&amp;token=5f564693604fdb9f47b55d572c9cdc3f\"
                 data-item=\"Traductions - Liste\"
      >Traductions - Liste</a>
        <div class=\"dropdown-divider\"></div>
          <a
        class=\"dropdown-item js-quick-link\"
        href=\"#\"
        data-rand=\"181\"
        data-icon=\"icon-AdminParentMeta\"
        data-method=\"add\"
        data-url=\"index.php/configure/shop/seo-urls\"
        data-post-link=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminQuickAccesses&token=dbd2106d2f54af7a3a3672727edeba50\"
        data-prompt-text=\"Veuillez nommer ce raccourci :\"
        data-link=\"SEO &amp; URL - Liste\"
      >
        <i class=\"material-icons\">add_circle</i>
        Ajouter la page actuelle à l'accès rapide
      </a>
        <a class=\"dropdown-item\" href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminQuickAccesses&token=dbd2106d2f54af7a3a3672727edeba50\">
      <i class=\"material-icons\">settings</i>
      Gérez vos accès rapides
    </a>
  </div>
</div>
      </div>
      <div class=\"component\" id=\"header-search-container\">
        <form id=\"header_search\"
      class=\"bo_search_form dropdown-form js-dropdown-form collapsed\"
      method=\"post\"
      action=\"/admin984s0lgwu/index.php?controller=AdminSearch&amp;token=74ac45c4ced3e2efb50256d5f02fc3de\"
      role=\"search\">
  <input type=\"hidden\" name=\"bo_search_type\" id=\"bo_search_type\" class=\"js-search-type\" />
    <div class=\"input-group\">
    <input type=\"text\" class=\"form-control js-form-search\" id=\"bo_query\" name=\"bo_query\" value=\"\" placeholder=\"Rechercher (ex. : référence produit, nom du client, etc.) d='Admin.Navigation.Header'\">
    <div class=\"input-group-append\">
      <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle js-dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
        Partout
      </button>
      <div class=\"dropdown-menu js-items-list\">
        <a class=\"dropdown-item\" data-item=\"Partout\" href=\"#\" data-value=\"0\" data-placeholder=\"Que souhaitez-vous trouver ?\" data-icon=\"icon-search\"><i class=\"material-icons\">search</i> Partout</a>
        <div class=\"dropdown-divider\"></div>
        <a class=\"dropdown-item\" data-item=\"Catalogue\" href=\"#\" data-value=\"1\" data-placeholder=\"Nom du produit, référence, etc.\" data-icon=\"icon-book\"><i class=\"material-icons\">store_mall_directory</i> Catalogue</a>
        <a class=\"dropdown-item\" data-item=\"Clients par nom\" href=\"#\" data-value=\"2\" data-placeholder=\"Nom\" data-icon=\"icon-group\"><i class=\"material-icons\">group</i> Clients par nom</a>
        <a class=\"dropdown-item\" data-item=\"Clients par adresse IP\" href=\"#\" data-value=\"6\" data-placeholder=\"123.45.67.89\" data-icon=\"icon-desktop\"><i class=\"material-icons\">desktop_mac</i> Clients par adresse IP</a>
        <a class=\"dropdown-item\" data-item=\"Commandes\" href=\"#\" data-value=\"3\" data-placeholder=\"ID commande\" data-icon=\"icon-credit-card\"><i class=\"material-icons\">shopping_basket</i> Commandes</a>
        <a class=\"dropdown-item\" data-item=\"Factures\" href=\"#\" data-value=\"4\" data-placeholder=\"Numéro de facture\" data-icon=\"icon-book\"><i class=\"material-icons\">book</i> Factures</a>
        <a class=\"dropdown-item\" data-item=\"Paniers\" href=\"#\" data-value=\"5\" data-placeholder=\"ID panier\" data-icon=\"icon-shopping-cart\"><i class=\"material-icons\">shopping_cart</i> Paniers</a>
        <a class=\"dropdown-item\" data-item=\"Modules\" href=\"#\" data-value=\"7\" data-placeholder=\"Nom du module\" data-icon=\"icon-puzzle-piece\"><i class=\"material-icons\">extension</i> Modules</a>
      </div>
      <button class=\"btn btn-primary\" type=\"submit\"><span class=\"d-none\">RECHERCHE</span><i class=\"material-icons\">search</i></button>
    </div>
  </div>
</form>

<script type=\"text/javascript\">
 \$(document).ready(function(){
    \$('#bo_query').one('click', function() {
    \$(this).closest('form').removeClass('collapsed');
  });
});
</script>
      </div>

      
      
      <div class=\"component\" id=\"header-shop-list-container\">
          <div class=\"shop-list\">
    <a class=\"link\" id=\"header_shopname\" href=\"http://ffb.shinagency.mypreprod.fr/\" target= \"_blank\">
      <i class=\"material-icons\">visibility</i>
      Voir ma boutique
    </a>
  </div>
      </div>

              <div class=\"component header-right-component\" id=\"header-notifications-container\">
          <div id=\"notif\" class=\"notification-center dropdown dropdown-clickable\">
  <button class=\"btn notification js-notification dropdown-toggle\" data-toggle=\"dropdown\">
    <i class=\"material-icons\">notifications_none</i>
    <span id=\"notifications-total\" class=\"count hide\">0</span>
  </button>
  <div class=\"dropdown-menu dropdown-menu-right js-notifs_dropdown\">
    <div class=\"notifications\">
      <ul class=\"nav nav-tabs\" role=\"tablist\">
                          <li class=\"nav-item\">
            <a
              class=\"nav-link active\"
              id=\"orders-tab\"
              data-toggle=\"tab\"
              data-type=\"order\"
              href=\"#orders-notifications\"
              role=\"tab\"
            >
              Commandes<span id=\"_nb_new_orders_\"></span>
            </a>
          </li>
                                    <li class=\"nav-item\">
            <a
              class=\"nav-link \"
              id=\"customers-tab\"
              data-toggle=\"tab\"
              data-type=\"customer\"
              href=\"#customers-notifications\"
              role=\"tab\"
            >
              Clients<span id=\"_nb_new_customers_\"></span>
            </a>
          </li>
                                    <li class=\"nav-item\">
            <a
              class=\"nav-link \"
              id=\"messages-tab\"
              data-toggle=\"tab\"
              data-type=\"customer_message\"
              href=\"#messages-notifications\"
              role=\"tab\"
            >
              Messages<span id=\"_nb_new_messages_\"></span>
            </a>
          </li>
                        </ul>

      <!-- Tab panes -->
      <div class=\"tab-content\">
                          <div class=\"tab-pane active empty\" id=\"orders-notifications\" role=\"tabpanel\">
            <p class=\"no-notification\">
              Pas de nouvelle commande pour le moment :(<br>
              Et pourquoi pas lancer des promotions de saison ?
            </p>
            <div class=\"notification-elements\"></div>
          </div>
                                    <div class=\"tab-pane  empty\" id=\"customers-notifications\" role=\"tabpanel\">
            <p class=\"no-notification\">
              Aucun nouveau client pour l'instant :(<br>
              Êtes-vous actifs sur les réseaux sociaux en ce moment ?
            </p>
            <div class=\"notification-elements\"></div>
          </div>
                                    <div class=\"tab-pane  empty\" id=\"messages-notifications\" role=\"tabpanel\">
            <p class=\"no-notification\">
              Pas de nouveau message pour l'instant.<br>
              Pas de nouvelle, bonne nouvelle, n'est-ce pas ?
            </p>
            <div class=\"notification-elements\"></div>
          </div>
                        </div>
    </div>
  </div>
</div>

  <script type=\"text/html\" id=\"order-notification-template\">
    <a class=\"notif\" href='order_url'>
      #_id_order_ -
      de <strong>_customer_name_</strong> (_iso_code_)_carrier_
      <strong class=\"float-sm-right\">_total_paid_</strong>
    </a>
  </script>

  <script type=\"text/html\" id=\"customer-notification-template\">
    <a class=\"notif\" href='customer_url'>
      #_id_customer_ - <strong>_customer_name_</strong>_company_ - enregistré le <strong>_date_add_</strong>
    </a>
  </script>

  <script type=\"text/html\" id=\"message-notification-template\">
    <a class=\"notif\" href='message_url'>
    <span class=\"message-notification-status _status_\">
      <i class=\"material-icons\">fiber_manual_record</i> _status_
    </span>
      - <strong>_customer_name_</strong> (_company_) - <i class=\"material-icons\">access_time</i> _date_add_
    </a>
  </script>
        </div>
      
      <div class=\"component\" id=\"header-employee-container\">
        <div class=\"dropdown employee-dropdown\">
  <div class=\"rounded-circle person\" data-toggle=\"dropdown\">
    <i class=\"material-icons\">account_circle</i>
  </div>
  <div class=\"dropdown-menu dropdown-menu-right\">
    <div class=\"employee-wrapper-avatar\">
      
      <span class=\"employee_avatar\"><img class=\"avatar rounded-circle\" src=\"https://profile.prestashop.com/adwords%40shin-agency.com.jpg\" /></span>
      <span class=\"employee_profile\">Ravi de vous revoir shin agency</span>
      <a class=\"dropdown-item employee-link profile-link\" href=\"/admin984s0lgwu/index.php/configure/advanced/employees/9/edit?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\">
      <i class=\"material-icons\">settings</i>
      Votre profil
    </a>
    </div>
    
    <p class=\"divider\"></p>
    <a class=\"dropdown-item\" href=\"https://www.prestashop.com/fr/ressources/documentation?utm_source=back-office&amp;utm_medium=profile&amp;utm_campaign=resources-fr&amp;utm_content=download17\" target=\"_blank\"><i class=\"material-icons\">book</i> Documentation</a>
    <a class=\"dropdown-item\" href=\"https://www.prestashop.com/fr/formation?utm_source=back-office&amp;utm_medium=profile&amp;utm_campaign=resources-fr&amp;utm_content=download17\" target=\"_blank\"><i class=\"material-icons\">school</i> Formation</a>
    <a class=\"dropdown-item\" href=\"https://www.prestashop.com/fr/experts?utm_source=back-office&amp;utm_medium=profile&amp;utm_campaign=resources-fr&amp;utm_content=download17\" target=\"_blank\"><i class=\"material-icons\">person_pin_circle</i> Trouver un expert</a>
    <a class=\"dropdown-item\" href=\"https://addons.prestashop.com/fr/?utm_source=back-office&amp;utm_medium=profile&amp;utm_campaign=addons-fr&amp;utm_content=download17\" target=\"_blank\"><i class=\"material-icons\">extension</i> Place de marché de PrestaShop</a>
    <a class=\"dropdown-item\" href=\"https://www.prestashop.com/fr/contact?utm_source=back-office&amp;utm_medium=profile&amp;utm_campaign=resources-fr&amp;utm_content=download17\" target=\"_blank\"><i class=\"material-icons\">help</i> Centre d'assistance</a>
    <p class=\"divider\"></p>
    <a class=\"dropdown-item employee-link text-center\" id=\"header_logout\" href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLogin&amp;logout=1&amp;token=c4b912861ef4c08036c21a7d797518d4\">
      <i class=\"material-icons d-lg-none\">power_settings_new</i>
      <span>Déconnexion</span>
    </a>
  </div>
</div>
      </div>
          </nav>
  </header>

  <nav class=\"nav-bar d-none d-print-none d-md-block\">
  <span class=\"menu-collapse\" data-toggle-url=\"/admin984s0lgwu/index.php/configure/advanced/employees/toggle-navigation?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\">
    <i class=\"material-icons\">chevron_left</i>
    <i class=\"material-icons\">chevron_left</i>
  </span>

  <div class=\"nav-bar-overflow\">
    <ul class=\"main-menu\">
              
                    
                    
          
            <li class=\"link-levelone \" data-submenu=\"1\" id=\"tab-AdminDashboard\">
              <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDashboard&amp;token=b93641c1f55be04c3e4659fc1d40f0a7\" class=\"link\" >
                <i class=\"material-icons\">trending_up</i> <span>Tableau de bord</span>
              </a>
            </li>

          
                      
                                          
                    
          
            <li class=\"category-title \" data-submenu=\"2\" id=\"tab-SELL\">
                <span class=\"title\">Vendre</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"3\" id=\"subtab-AdminParentOrders\">
                    <a href=\"/admin984s0lgwu/index.php/sell/orders/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-shopping_basket\">shopping_basket</i>
                      <span>
                      Commandes
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-3\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"4\" id=\"subtab-AdminOrders\">
                                <a href=\"/admin984s0lgwu/index.php/sell/orders/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"5\" id=\"subtab-AdminInvoices\">
                                <a href=\"/admin984s0lgwu/index.php/sell/orders/invoices/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Factures
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"6\" id=\"subtab-AdminSlip\">
                                <a href=\"/admin984s0lgwu/index.php/sell/orders/credit-slips/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Avoirs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"7\" id=\"subtab-AdminDeliverySlip\">
                                <a href=\"/admin984s0lgwu/index.php/sell/orders/delivery-slips/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Bons de livraison
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"8\" id=\"subtab-AdminCarts\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCarts&amp;token=4d6c702641e71e66ebfe9e91a533f7b5\" class=\"link\"> Paniers
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"158\" id=\"subtab-AdminDhlOrders\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDhlOrders&amp;token=1cfb18382fb012d9494c0318a9d6a47b\" class=\"link\"> DHL Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"159\" id=\"subtab-AdminDhlLabel\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDhlLabel&amp;token=80ea37f53a82f6602ac82f592dbee14c\" class=\"link\"> DHL Etiquettes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"160\" id=\"subtab-AdminDhlBulkLabel\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDhlBulkLabel&amp;token=d188b37b556f064b8bd2915daa65bac5\" class=\"link\"> DHL Etiquettes en masse
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"161\" id=\"subtab-AdminDhlPickup\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDhlPickup&amp;token=0c15647c08a305aae3a7f3f08f0b15d6\" class=\"link\"> DHL Enlèvement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"162\" id=\"subtab-AdminDhlManifest\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDhlManifest&amp;token=66cb432bdc655c987a9ce0e2539c1e4a\" class=\"link\"> DHL Manifeste
                                </a>
                              </li>

                                                                                                                                    </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"9\" id=\"subtab-AdminCatalog\">
                    <a href=\"/admin984s0lgwu/index.php/sell/catalog/products?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-store\">store</i>
                      <span>
                      Catalogue
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-9\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"10\" id=\"subtab-AdminProducts\">
                                <a href=\"/admin984s0lgwu/index.php/sell/catalog/products?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Produits
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"11\" id=\"subtab-AdminCategories\">
                                <a href=\"/admin984s0lgwu/index.php/sell/catalog/categories?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Catégories
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"12\" id=\"subtab-AdminTracking\">
                                <a href=\"/admin984s0lgwu/index.php/sell/catalog/monitoring/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Suivi
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"13\" id=\"subtab-AdminParentAttributesGroups\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminAttributesGroups&amp;token=e25aa4e85d46433787ecb7bcd5afd852\" class=\"link\"> Attributs &amp; caractéristiques
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"16\" id=\"subtab-AdminParentManufacturers\">
                                <a href=\"/admin984s0lgwu/index.php/sell/catalog/brands/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Marques et fournisseurs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"19\" id=\"subtab-AdminAttachments\">
                                <a href=\"/admin984s0lgwu/index.php/sell/attachments/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Fichiers
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"20\" id=\"subtab-AdminParentCartRules\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCartRules&amp;token=4a5ee379e1cf68e5c4b07cf543ece297\" class=\"link\"> Réductions
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"23\" id=\"subtab-AdminStockManagement\">
                                <a href=\"/admin984s0lgwu/index.php/sell/stocks/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Stocks
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"24\" id=\"subtab-AdminParentCustomer\">
                    <a href=\"/admin984s0lgwu/index.php/sell/customers/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-account_circle\">account_circle</i>
                      <span>
                      Clients
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-24\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"25\" id=\"subtab-AdminCustomers\">
                                <a href=\"/admin984s0lgwu/index.php/sell/customers/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"26\" id=\"subtab-AdminAddresses\">
                                <a href=\"/admin984s0lgwu/index.php/sell/addresses/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Adresses
                                </a>
                              </li>

                                                                                                                                    </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"28\" id=\"subtab-AdminParentCustomerThreads\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCustomerThreads&amp;token=d778549e3624d1346499314214d392ea\" class=\"link\">
                      <i class=\"material-icons mi-chat\">chat</i>
                      <span>
                      SAV
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-28\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"29\" id=\"subtab-AdminCustomerThreads\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCustomerThreads&amp;token=d778549e3624d1346499314214d392ea\" class=\"link\"> SAV
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"30\" id=\"subtab-AdminOrderMessage\">
                                <a href=\"/admin984s0lgwu/index.php/sell/customer-service/order-messages/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Messages prédéfinis
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"31\" id=\"subtab-AdminReturn\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminReturn&amp;token=f7c33aaad56255245defd96d82109f58\" class=\"link\"> Retours produits
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"32\" id=\"subtab-AdminStats\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminStats&amp;token=a3e43d2672c4d4e34ca5cb54a39966f4\" class=\"link\">
                      <i class=\"material-icons mi-assessment\">assessment</i>
                      <span>
                      Statistiques
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                                            
          
                      
                                          
                    
          
            <li class=\"category-title \" data-submenu=\"42\" id=\"tab-IMPROVE\">
                <span class=\"title\">Personnaliser</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"43\" id=\"subtab-AdminParentModulesSf\">
                    <a href=\"/admin984s0lgwu/index.php/improve/modules/manage?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Modules
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-43\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"44\" id=\"subtab-AdminModulesSf\">
                                <a href=\"/admin984s0lgwu/index.php/improve/modules/manage?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Gestionnaire de modules 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"48\" id=\"subtab-AdminParentModulesCatalog\">
                                <a href=\"/admin984s0lgwu/index.php/modules/addons/modules/catalog?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Catalogue de modules
                                </a>
                              </li>

                                                                                                                                        
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"186\" id=\"subtab-AdminLeoBootstrapMenuModule\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoBootstrapMenuModule&amp;token=184485dfcf1527c7df8f3363b401db4f\" class=\"link\"> Leo Megamenu Configuration
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"189\" id=\"subtab-AdminLeoSlideshowMenuModule\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoSlideshowMenuModule&amp;token=c6d91b7f903bff035d7193ca57cb677f\" class=\"link\"> Leo Slideshow Configuration
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"191\" id=\"subtab-AdminLeoProductSearchModule\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoProductSearchModule&amp;token=3f29d8f08fae1264ef25e76b7fd8306b\" class=\"link\"> Leo Product Search Configuration
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"192\" id=\"subtab-AdminLeoQuickLoginModule\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoQuickLoginModule&amp;token=3bca1807d42a430c7a02c6ee5b926401\" class=\"link\"> Leo Quick Login Configuration
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"52\" id=\"subtab-AdminParentThemes\">
                    <a href=\"/admin984s0lgwu/index.php/improve/design/themes/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-desktop_mac\">desktop_mac</i>
                      <span>
                      Apparence
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-52\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"126\" id=\"subtab-AdminThemesParent\">
                                <a href=\"/admin984s0lgwu/index.php/improve/design/themes/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Thème et logo
                                </a>
                              </li>

                                                                                                                                        
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"137\" id=\"subtab-AdminPsMboTheme\">
                                <a href=\"/admin984s0lgwu/index.php/modules/addons/themes/catalog?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Catalogue de thèmes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"55\" id=\"subtab-AdminParentMailTheme\">
                                <a href=\"/admin984s0lgwu/index.php/improve/design/mail_theme/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Thème d&#039;email
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"57\" id=\"subtab-AdminCmsContent\">
                                <a href=\"/admin984s0lgwu/index.php/improve/design/cms-pages/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Pages
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"58\" id=\"subtab-AdminModulesPositions\">
                                <a href=\"/admin984s0lgwu/index.php/improve/design/modules/positions/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Positions
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"59\" id=\"subtab-AdminImages\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminImages&amp;token=7be82e4169d9a2a5ff4199575ee0f592\" class=\"link\"> Images
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"125\" id=\"subtab-AdminLinkWidget\">
                                <a href=\"/admin984s0lgwu/index.php/modules/link-widget/list?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Link Widget
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"60\" id=\"subtab-AdminParentShipping\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCarriers&amp;token=74363813eae418e35989a78e4a4a0766\" class=\"link\">
                      <i class=\"material-icons mi-local_shipping\">local_shipping</i>
                      <span>
                      Livraison
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-60\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"61\" id=\"subtab-AdminCarriers\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCarriers&amp;token=74363813eae418e35989a78e4a4a0766\" class=\"link\"> Transporteurs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"62\" id=\"subtab-AdminShipping\">
                                <a href=\"/admin984s0lgwu/index.php/improve/shipping/preferences?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Préférences
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"164\" id=\"subtab-AdminImportChronopost\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminImportChronopost&amp;token=eeb8a14e55a0c2ecbd9fae3a10d84002\" class=\"link\"> Import Chronopost
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"165\" id=\"subtab-AdminExportChronopost\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminExportChronopost&amp;token=2d9ac4b7ff9add49cd443752e27cf220\" class=\"link\"> Export Chronopost
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"166\" id=\"subtab-AdminBordereauChronopost\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminBordereauChronopost&amp;token=8ae5894103b2ce02851c041c71da95d1\" class=\"link\"> Bordereau de fin de journée
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"198\" id=\"subtab-AdminColissimoDashboard\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminColissimoDashboard&amp;token=300fa35468ea88fb70d0851188c25b2f\" class=\"link\"> Colissimo - Tableau de bord
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"199\" id=\"subtab-AdminColissimoAffranchissement\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminColissimoAffranchissement&amp;token=c613defc3a71443f19cb208f444b56af\" class=\"link\"> Colissimo - Affranchissement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"200\" id=\"subtab-AdminColissimoDepositSlip\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminColissimoDepositSlip&amp;token=b3e963f83574fde804604d831c68569e\" class=\"link\"> Colissimo - Bordereaux
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"201\" id=\"subtab-AdminColissimoColiship\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminColissimoColiship&amp;token=061889415a7cd8cee9aa3026ebf8c8d2\" class=\"link\"> Colissimo - Coliship
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"63\" id=\"subtab-AdminParentPayment\">
                    <a href=\"/admin984s0lgwu/index.php/improve/payment/payment_methods?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-payment\">payment</i>
                      <span>
                      Paiement
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-63\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"64\" id=\"subtab-AdminPayment\">
                                <a href=\"/admin984s0lgwu/index.php/improve/payment/payment_methods?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Modes de paiement
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"65\" id=\"subtab-AdminPaymentPreferences\">
                                <a href=\"/admin984s0lgwu/index.php/improve/payment/preferences?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Préférences
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"66\" id=\"subtab-AdminInternational\">
                    <a href=\"/admin984s0lgwu/index.php/improve/international/localization/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-language\">language</i>
                      <span>
                      International
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-66\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"67\" id=\"subtab-AdminParentLocalization\">
                                <a href=\"/admin984s0lgwu/index.php/improve/international/localization/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Localisation
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"72\" id=\"subtab-AdminParentCountries\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminZones&amp;token=569dd4057b430d1a93fe971034321a82\" class=\"link\"> Zones géographiques
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"76\" id=\"subtab-AdminParentTaxes\">
                                <a href=\"/admin984s0lgwu/index.php/improve/international/taxes/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Taxes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"79\" id=\"subtab-AdminTranslations\">
                                <a href=\"/admin984s0lgwu/index.php/improve/international/translations/settings?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Traductions
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"131\" id=\"subtab-AdminEmarketing\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEmarketing&amp;token=ffb02fc67b5a3afbc44a27e01dd919e5\" class=\"link\">
                      <i class=\"material-icons mi-track_changes\">track_changes</i>
                      <span>
                      Advertising
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"167\" id=\"subtab-AdminApPageBuilder\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderProfiles&amp;token=4fd648350f62315040499d603db5a5a1\" class=\"link\">
                      <i class=\"material-icons mi-tab\">tab</i>
                      <span>
                      Ap PageBuilder
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-167\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"168\" id=\"subtab-AdminApPageBuilderProfiles\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderProfiles&amp;token=4fd648350f62315040499d603db5a5a1\" class=\"link\"> Ap Profiles Manage
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"169\" id=\"subtab-AdminApPageBuilderPositions\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderPositions&amp;token=f46b31ac18359a1b824cb2378f45355c\" class=\"link\"> Ap Positions Manage
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"170\" id=\"subtab-AdminApPageBuilderShortcode\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderShortcode&amp;token=a7d077ecac2b3ae7fcd991e420dcbe1a\" class=\"link\"> Ap ShortCode Manage
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"172\" id=\"subtab-AdminApPageBuilderProducts\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderProducts&amp;token=67ee33ed7355807dd07120fc268b0eda\" class=\"link\"> Ap Products List Builder
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"173\" id=\"subtab-AdminApPageBuilderDetails\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderDetails&amp;token=950d194c6f755c714b5bf58f06d767c9\" class=\"link\"> Ap Products Details Builder
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"175\" id=\"subtab-AdminApPageBuilderThemeEditor\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderThemeEditor&amp;token=d3f53c0562322bfd27bad83c8e1a5811\" class=\"link\"> Ap Live Theme Editor
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"176\" id=\"subtab-AdminApPageBuilderModule\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderModule&amp;token=660ed48e40f4e794ca3bbaa7efe2ff3a\" class=\"link\"> Ap Module Configuration
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"177\" id=\"subtab-AdminApPageBuilderThemeConfiguration\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderThemeConfiguration&amp;token=63ad87916cd4962919b1c8edfb7af3f6\" class=\"link\"> Ap Theme Configuration
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"190\" id=\"subtab-AdminApPageBuilderHook\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminApPageBuilderHook&amp;token=c25cbd4aa753f776e20091dc3dcd4395\" class=\"link\"> Ap Hook Control Panel
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"180\" id=\"subtab-AdminLeoblogManagement\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoblogDashboard&amp;token=8dc812db83527af9c44437444f2e12c4\" class=\"link\">
                      <i class=\"material-icons mi-create\">create</i>
                      <span>
                      Leo Blog Gestion
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-180\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"181\" id=\"subtab-AdminLeoblogDashboard\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoblogDashboard&amp;token=8dc812db83527af9c44437444f2e12c4\" class=\"link\"> Blog Dashboard
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"182\" id=\"subtab-AdminLeoblogCategories\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoblogCategories&amp;token=b9cf4f693eae67d2abefe2c1944dfd81\" class=\"link\"> Categories Management
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"183\" id=\"subtab-AdminLeoblogBlogs\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoblogBlogs&amp;token=2a1835a9d31150ee4f03fbbc84831fc6\" class=\"link\"> Blogs Management
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"184\" id=\"subtab-AdminLeoblogComments\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoblogComments&amp;token=7de17e243f2ad1e6af13a293caec5826\" class=\"link\"> Comment Management
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"185\" id=\"subtab-AdminLeoblogModule\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminLeoblogModule&amp;token=e062e352ef4de585a0444db02e8af964\" class=\"link\"> Leo Blog Configuration
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title -active\" data-submenu=\"80\" id=\"tab-CONFIGURE\">
                <span class=\"title\">Configurer</span>
            </li>

                              
                  
                                                      
                                                          
                  <li class=\"link-levelone has_submenu -active open ul-open\" data-submenu=\"81\" id=\"subtab-ShopParameters\">
                    <a href=\"/admin984s0lgwu/index.php/configure/shop/preferences/preferences?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-settings\">settings</i>
                      <span>
                      Paramètres de la boutique
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_up
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-81\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"82\" id=\"subtab-AdminParentPreferences\">
                                <a href=\"/admin984s0lgwu/index.php/configure/shop/preferences/preferences?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Paramètres généraux
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"85\" id=\"subtab-AdminParentOrderPreferences\">
                                <a href=\"/admin984s0lgwu/index.php/configure/shop/order-preferences/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Commandes
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"88\" id=\"subtab-AdminPPreferences\">
                                <a href=\"/admin984s0lgwu/index.php/configure/shop/product-preferences/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Produits
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"89\" id=\"subtab-AdminParentCustomerPreferences\">
                                <a href=\"/admin984s0lgwu/index.php/configure/shop/customer-preferences/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Clients
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"93\" id=\"subtab-AdminParentStores\">
                                <a href=\"/admin984s0lgwu/index.php/configure/shop/contacts/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Contact
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo -active\" data-submenu=\"96\" id=\"subtab-AdminParentMeta\">
                                <a href=\"/admin984s0lgwu/index.php/configure/shop/seo-urls/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Trafic et SEO
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"100\" id=\"subtab-AdminParentSearchConf\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminSearchConf&amp;token=1eb649342e495e616d04bbea9533b294\" class=\"link\"> Rechercher
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"103\" id=\"subtab-AdminAdvancedParameters\">
                    <a href=\"/admin984s0lgwu/index.php/configure/advanced/system-information/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\">
                      <i class=\"material-icons mi-settings_applications\">settings_applications</i>
                      <span>
                      Paramètres avancés
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-103\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"104\" id=\"subtab-AdminInformation\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/system-information/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Informations
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"105\" id=\"subtab-AdminPerformance\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/performance/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Performances
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"106\" id=\"subtab-AdminAdminPreferences\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/administration/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Administration
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"107\" id=\"subtab-AdminEmails\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/emails/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Email
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"108\" id=\"subtab-AdminImport\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/import/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Importer
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"109\" id=\"subtab-AdminParentEmployees\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/employees/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Équipe
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"113\" id=\"subtab-AdminParentRequestSql\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/sql-requests/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Base de données
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"116\" id=\"subtab-AdminLogs\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/logs/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Logs
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"117\" id=\"subtab-AdminWebservice\">
                                <a href=\"/admin984s0lgwu/index.php/configure/advanced/webservice-keys/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" class=\"link\"> Webservice
                                </a>
                              </li>

                                                                                                                                                                                              
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"202\" id=\"subtab-AdminCdcGoogletagmanagerOrders\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminCdcGoogletagmanagerOrders&amp;token=ac1af387ed08c0b70b81b6de8ae19e58\" class=\"link\"> GTM Orders
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title \" data-submenu=\"121\" id=\"tab-DEFAULT\">
                <span class=\"title\">Détails</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"212\" id=\"subtab-AdminGiftProductRules\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminGiftProductRules&amp;token=7d28335d91069285e510052a04d301b1\" class=\"link\">
                      <i class=\"material-icons mi-settings_applications\">settings_applications</i>
                      <span>
                      Free Gifts Products Promo
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"213\" id=\"subtab-AdminGeneratedGifts\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminGeneratedGifts&amp;token=9c0c7fe62aaeb3d8043f4e09bf02122a\" class=\"link\">
                      <i class=\"material-icons mi-settings_applications\">settings_applications</i>
                      <span>
                      Stats generated Gifts
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                      
                                          
                    
          
            <li class=\"category-title \" data-submenu=\"214\" id=\"tab-AdminEtsAC\">
                <span class=\"title\">Customer reminders</span>
            </li>

                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"215\" id=\"subtab-AdminEtsACDashboard\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACDashboard&amp;token=e41cb14320aa35ca3f598b4f6266a4c9\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Tableau de bord
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"216\" id=\"subtab-AdminEtsACCampaign\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderEmail&amp;token=7a9222d2815bf4d6d8b3edb8c789397f\" class=\"link\">
                      <i class=\"material-icons mi-\"></i>
                      <span>
                      Campagnes de relance
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-216\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"217\" id=\"subtab-AdminEtsACReminderEmail\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderEmail&amp;token=7a9222d2815bf4d6d8b3edb8c789397f\" class=\"link\"> E-mails automatisés de panier abandonné
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"218\" id=\"subtab-AdminEtsACReminderCustomer\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderCustomer&amp;token=cae1bac38a175e0f0c183d19ebc77376\" class=\"link\"> E-mails personnalisés et newsletter
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"219\" id=\"subtab-AdminEtsACReminderPopup\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderPopup&amp;token=8fcadd5a4ba85c304f88087a6670d8d0\" class=\"link\"> Relance pop-up 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"220\" id=\"subtab-AdminEtsACReminderBar\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderBar&amp;token=4f4d7dc2e5052d489ea00cb4bae0e66d\" class=\"link\"> Relance dans la barre d’information 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"221\" id=\"subtab-AdminEtsACReminderBrowser\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderBrowser&amp;token=45c38c073c4efe3d126b68d1cb256fcc\" class=\"link\"> Notification web push 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"222\" id=\"subtab-AdminEtsACReminderLeave\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderLeave&amp;token=52e759d1b38da099389dae863e54c90a\" class=\"link\"> Relance lors de la sortie du site web
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"223\" id=\"subtab-AdminEtsACReminderBrowserTab\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACReminderBrowserTab&amp;token=77705dea74d5804eacad9bd9613e0dab\" class=\"link\"> Notification dans l’onglet du navigateur 
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"224\" id=\"subtab-AdminEtsACCart\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACCart&amp;token=6447142ecc55c9a9a18f1203993dcfd1\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Paniers abandonnés
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"225\" id=\"subtab-AdminEtsACConvertedCarts\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACConvertedCarts&amp;token=89b7f996dd51052bab2165e980560c96\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Paniers récupérés
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"226\" id=\"subtab-AdminEtsACEmailTemplate\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACEmailTemplate&amp;token=e9a6a786234a38720c7173894f36e6ad\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Modèles d’e-mail
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"227\" id=\"subtab-AdminEtsACTracking\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACEmailTracking&amp;token=fb744bc502e71829325ef4aa35b5c9ff\" class=\"link\">
                      <i class=\"material-icons mi-\"></i>
                      <span>
                      Suivi des campagnes
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-227\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"228\" id=\"subtab-AdminEtsACEmailTracking\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACEmailTracking&amp;token=fb744bc502e71829325ef4aa35b5c9ff\" class=\"link\"> Suivi des e-mails
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"229\" id=\"subtab-AdminEtsACDisplayTracking\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACDisplayTracking&amp;token=624e65f65a15840943de41c78e52af6b\" class=\"link\"> Suivi d’affichage
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"230\" id=\"subtab-AdminEtsACDiscounts\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACDiscounts&amp;token=3ab2e3e45d7975ffc14bf8268cc8ac0c\" class=\"link\"> Remises
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"231\" id=\"subtab-AdminEtsACDisplayLog\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACDisplayLog&amp;token=15d387a287da3528e88068cf6eb44c80\" class=\"link\"> Journal d\\&#039;affichage de la campagne
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone has_submenu\" data-submenu=\"232\" id=\"subtab-AdminEtsACMailConfigs\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACMailServices&amp;token=6a23ca9cb6b8c9b70fdd96633402cfdd\" class=\"link\">
                      <i class=\"material-icons mi-\"></i>
                      <span>
                      Configuration du courrier
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                              <ul id=\"collapse-232\" class=\"submenu panel-collapse\">
                                                      
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"233\" id=\"subtab-AdminEtsACMailServices\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACMailServices&amp;token=6a23ca9cb6b8c9b70fdd96633402cfdd\" class=\"link\"> Service de courrier
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"234\" id=\"subtab-AdminEtsACMailQueue\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACMailQueue&amp;token=42a34bc5542205246b46327bcd3fbdab\" class=\"link\"> File d’attente des e-mails 
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"235\" id=\"subtab-AdminEtsACIndexedCarts\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACIndexedCarts&amp;token=3d391026e515db3f8a24836b610758bf\" class=\"link\"> Paniers indexés
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"236\" id=\"subtab-AdminEtsACIndexedCustomers\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACIndexedCustomers&amp;token=e1702b2f7270af26b91f2c7e605d0ae6\" class=\"link\"> Clients indexés
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"237\" id=\"subtab-AdminEtsACUnsubscribed\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACUnsubscribed&amp;token=fc8a64bdfb180ed6d7bc0223f113e08c\" class=\"link\"> Liste des désabonnés
                                </a>
                              </li>

                                                                                  
                              
                                                            
                              <li class=\"link-leveltwo \" data-submenu=\"238\" id=\"subtab-AdminEtsACMailLog\">
                                <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACMailLog&amp;token=c86b35d7ad09168bcd9be3db4864608b\" class=\"link\"> Journal de courrier
                                </a>
                              </li>

                                                                              </ul>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"239\" id=\"subtab-AdminEtsACLeads\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACLeads&amp;token=776bd7a0f0f18c3724047d5df9886f5d\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Prospects
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"240\" id=\"subtab-AdminEtsACConfigs\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACConfigs&amp;token=2b6fcc96fc296c6aed5b5c4041210c89\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Automatisation
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                                              
                  
                                                      
                  
                  <li class=\"link-levelone\" data-submenu=\"241\" id=\"subtab-AdminEtsACOtherConfigs\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminEtsACOtherConfigs&amp;token=c72fecbeb914c569669c51583bbdb14f\" class=\"link\">
                      <i class=\"material-icons mi-extension\">extension</i>
                      <span>
                      Autres réglages
                      </span>
                                                    <i class=\"material-icons sub-tabs-arrow\">
                                                                    keyboard_arrow_down
                                                            </i>
                                            </a>
                                        </li>
                              
          
                  </ul>
  </div>
  
</nav>

<div id=\"main-div\">
          
<div class=\"header-toolbar d-print-none\">
  <div class=\"container-fluid\">

    
      <nav aria-label=\"Breadcrumb\">
        <ol class=\"breadcrumb\">
                      <li class=\"breadcrumb-item\">Trafic et SEO</li>
          
                      <li class=\"breadcrumb-item active\">
              <a href=\"/admin984s0lgwu/index.php/configure/shop/seo-urls/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" aria-current=\"page\">SEO &amp; URL</a>
            </li>
                  </ol>
      </nav>
    

    <div class=\"title-row\">
      
          <h1 class=\"title\">
            SEO &amp; URL          </h1>
      

      
        <div class=\"toolbar-icons\">
          <div class=\"wrapper\">
            
                                                          <a
                  class=\"btn btn-primary  pointer\"                  id=\"page-header-desc-configuration-add\"
                  href=\"/admin984s0lgwu/index.php/configure/shop/seo-urls/new?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\"                  title=\"Ajouter une page\"                >
                  <i class=\"material-icons\">add_circle_outline</i>                  Ajouter une page
                </a>
                                      
            
                              <a class=\"btn btn-outline-secondary btn-help btn-sidebar\" href=\"#\"
                   title=\"Aide\"
                   data-toggle=\"sidebar\"
                   data-target=\"#right-sidebar\"
                   data-url=\"/admin984s0lgwu/index.php/common/sidebar/https%253A%252F%252Fhelp.prestashop.com%252Ffr%252Fdoc%252FAdminMeta%253Fversion%253D1.7.7.3%2526country%253Dfr/Aide?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\"
                   id=\"product_form_open_help\"
                >
                  Aide
                </a>
                                    </div>
        </div>
      
    </div>
  </div>

  
      <div class=\"page-head-tabs\" id=\"head_tabs\">
      <ul class=\"nav nav-pills\">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li class=\"nav-item\">
                    <a href=\"/admin984s0lgwu/index.php/configure/shop/seo-urls/?_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU\" id=\"subtab-AdminMeta\" class=\"nav-link tab active current\" data-submenu=\"97\">
                      SEO & URL
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                <li class=\"nav-item\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminSearchEngines&token=59ede75a8c16ea4631b9456040a52900\" id=\"subtab-AdminSearchEngines\" class=\"nav-link tab \" data-submenu=\"98\">
                      Moteurs de recherche
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                <li class=\"nav-item\">
                    <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminReferrers&token=602c5418465c1876d75768bbd1d701ae\" id=\"subtab-AdminReferrers\" class=\"nav-link tab \" data-submenu=\"99\">
                      Affiliés
                      <span class=\"notification-container\">
                        <span class=\"notification-counter\"></span>
                      </span>
                    </a>
                  </li>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </ul>
    </div>
    <script>
  if (undefined !== mbo) {
    mbo.initialize({
      translations: {
        'Recommended Modules and Services': 'Modules et services recommandés',
        'Close': 'Fermer',
      },
      recommendedModulesUrl: '/admin984s0lgwu/index.php/modules/addons/modules/recommended?tabClassName=AdminMeta&_token=OxURYcs8V_QyhA6HcHWNch3FfhXhHvLtyQbGBrZUfnU',
      shouldAttachRecommendedModulesAfterContent: 0,
      shouldAttachRecommendedModulesButton: 1,
      shouldUseLegacyTheme: 0,
    });
  }
</script>

</div>
      
      <div class=\"content-div  with-tabs\">

        

                                                        
        <div class=\"row \">
          <div class=\"col-sm-12\">
            <div id=\"ajax_confirmation\" class=\"alert alert-success\" style=\"display: none;\"></div>


  ";
        // line 1809
        $this->displayBlock('content_header', $context, $blocks);
        // line 1810
        echo "                 ";
        $this->displayBlock('content', $context, $blocks);
        // line 1811
        echo "                 ";
        $this->displayBlock('content_footer', $context, $blocks);
        // line 1812
        echo "                 ";
        $this->displayBlock('sidebar_right', $context, $blocks);
        // line 1813
        echo "
            
          </div>
        </div>

      </div>
    </div>

  <div id=\"non-responsive\" class=\"js-non-responsive\">
  <h1>Oh non !</h1>
  <p class=\"mt-3\">
    La version mobile de cette page n'est pas encore disponible.
  </p>
  <p class=\"mt-2\">
    En attendant que cette page soit adaptée au mobile, vous êtes invité à la consulter sur ordinateur.
  </p>
  <p class=\"mt-2\">
    Merci.
  </p>
  <a href=\"https://ffb.shinagency.mypreprod.fr/admin984s0lgwu/index.php?controller=AdminDashboard&amp;token=b93641c1f55be04c3e4659fc1d40f0a7\" class=\"btn btn-primary py-1 mt-3\">
    <i class=\"material-icons\">arrow_back</i>
    Précédent
  </a>
</div>
  <div class=\"mobile-layer\"></div>

      <div id=\"footer\" class=\"bootstrap\">
    
</div>
  

      <div class=\"bootstrap\">
      <div class=\"modal fade\" id=\"modal_addons_connect\" tabindex=\"-1\">
\t<div class=\"modal-dialog modal-md\">
\t\t<div class=\"modal-content\">
\t\t\t\t\t\t<div class=\"modal-header\">
\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
\t\t\t\t<h4 class=\"modal-title\"><i class=\"icon-puzzle-piece\"></i> <a target=\"_blank\" href=\"https://addons.prestashop.com/?utm_source=back-office&utm_medium=modules&utm_campaign=back-office-FR&utm_content=download\">PrestaShop Addons</a></h4>
\t\t\t</div>
\t\t\t
\t\t\t<div class=\"modal-body\">
\t\t\t\t\t\t<!--start addons login-->
\t\t\t<form id=\"addons_login_form\" method=\"post\" >
\t\t\t\t<div>
\t\t\t\t\t<a href=\"https://addons.prestashop.com/fr/login?email=adwords%40shin-agency.com&amp;firstname=shin+agency&amp;lastname=yann&amp;website=http%3A%2F%2Fffb.shinagency.mypreprod.fr%2F&amp;utm_source=back-office&amp;utm_medium=connect-to-addons&amp;utm_campaign=back-office-FR&amp;utm_content=download#createnow\"><img class=\"img-responsive center-block\" src=\"/admin984s0lgwu/themes/default/img/prestashop-addons-logo.png\" alt=\"Logo PrestaShop Addons\"/></a>
\t\t\t\t\t<h3 class=\"text-center\">Connectez-vous à la place de marché de PrestaShop afin d'importer automatiquement tous vos achats.</h3>
\t\t\t\t\t<hr />
\t\t\t\t</div>
\t\t\t\t<div class=\"row\">
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<h4>Vous n'avez pas de compte ?</h4>
\t\t\t\t\t\t<p class='text-justify'>Les clés pour réussir votre boutique sont sur PrestaShop Addons ! Découvrez sur la place de marché officielle de PrestaShop plus de 3 500 modules et thèmes pour augmenter votre trafic, optimiser vos conversions, fidéliser vos clients et vous faciliter l’e-commerce.</p>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<h4>Connectez-vous à PrestaShop Addons</h4>
\t\t\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t\t\t<div class=\"input-group\">
\t\t\t\t\t\t\t\t<div class=\"input-group-prepend\">
\t\t\t\t\t\t\t\t\t<span class=\"input-group-text\"><i class=\"icon-user\"></i></span>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<input id=\"username_addons\" name=\"username_addons\" type=\"text\" value=\"\" autocomplete=\"off\" class=\"form-control ac_input\">
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t\t\t<div class=\"input-group\">
\t\t\t\t\t\t\t\t<div class=\"input-group-prepend\">
\t\t\t\t\t\t\t\t\t<span class=\"input-group-text\"><i class=\"icon-key\"></i></span>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<input id=\"password_addons\" name=\"password_addons\" type=\"password\" value=\"\" autocomplete=\"off\" class=\"form-control ac_input\">
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<a class=\"btn btn-link float-right _blank\" href=\"//addons.prestashop.com/fr/forgot-your-password\">Mot de passe oublié</a>
\t\t\t\t\t\t\t<br>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"row row-padding-top\">
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t\t\t<a class=\"btn btn-default btn-block btn-lg _blank\" href=\"https://addons.prestashop.com/fr/login?email=adwords%40shin-agency.com&amp;firstname=shin+agency&amp;lastname=yann&amp;website=http%3A%2F%2Fffb.shinagency.mypreprod.fr%2F&amp;utm_source=back-office&amp;utm_medium=connect-to-addons&amp;utm_campaign=back-office-FR&amp;utm_content=download#createnow\">
\t\t\t\t\t\t\t\tCréer un compte
\t\t\t\t\t\t\t\t<i class=\"icon-external-link\"></i>
\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"col-md-6\">
\t\t\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t\t\t<button id=\"addons_login_button\" class=\"btn btn-primary btn-block btn-lg\" type=\"submit\">
\t\t\t\t\t\t\t\t<i class=\"icon-unlock\"></i> Connexion
\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div id=\"addons_loading\" class=\"help-block\"></div>

\t\t\t</form>
\t\t\t<!--end addons login-->
\t\t\t</div>


\t\t\t\t\t</div>
\t</div>
</div>

    </div>
  
";
        // line 1920
        $this->displayBlock('javascripts', $context, $blocks);
        $this->displayBlock('extra_javascripts', $context, $blocks);
        $this->displayBlock('translate_javascripts', $context, $blocks);
        echo "</body>
</html>";
    }

    // line 125
    public function block_stylesheets($context, array $blocks = [])
    {
    }

    public function block_extra_stylesheets($context, array $blocks = [])
    {
    }

    // line 1809
    public function block_content_header($context, array $blocks = [])
    {
    }

    // line 1810
    public function block_content($context, array $blocks = [])
    {
    }

    // line 1811
    public function block_content_footer($context, array $blocks = [])
    {
    }

    // line 1812
    public function block_sidebar_right($context, array $blocks = [])
    {
    }

    // line 1920
    public function block_javascripts($context, array $blocks = [])
    {
    }

    public function block_extra_javascripts($context, array $blocks = [])
    {
    }

    public function block_translate_javascripts($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "__string_template__831aba286e0a735148dd48ff078609d3ac7f55dd4c167cdcb9cd24f22b928e84";
    }

    public function getDebugInfo()
    {
        return array (  2010 => 1920,  2005 => 1812,  2000 => 1811,  1995 => 1810,  1990 => 1809,  1981 => 125,  1973 => 1920,  1864 => 1813,  1861 => 1812,  1858 => 1811,  1855 => 1810,  1853 => 1809,  165 => 125,  39 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "__string_template__831aba286e0a735148dd48ff078609d3ac7f55dd4c167cdcb9cd24f22b928e84", "");
    }
}
