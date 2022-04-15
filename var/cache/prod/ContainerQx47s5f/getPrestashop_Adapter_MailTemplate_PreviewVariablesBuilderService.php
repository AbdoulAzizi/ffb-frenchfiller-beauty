<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'prestashop.adapter.mail_template.preview_variables_builder' shared service.

return $this->services['prestashop.adapter.mail_template.preview_variables_builder'] = new \PrestaShop\PrestaShop\Adapter\MailTemplate\MailPreviewVariablesBuilder(${($_ = isset($this->services['prestashop.adapter.legacy.configuration']) ? $this->services['prestashop.adapter.legacy.configuration'] : ($this->services['prestashop.adapter.legacy.configuration'] = new \PrestaShop\PrestaShop\Adapter\Configuration())) && false ?: '_'}, ${($_ = isset($this->services['prestashop.adapter.legacy.context']) ? $this->services['prestashop.adapter.legacy.context'] : $this->getPrestashop_Adapter_Legacy_ContextService()) && false ?: '_'}, ${($_ = isset($this->services['prestashop.adapter.data_provider.employee']) ? $this->services['prestashop.adapter.data_provider.employee'] : $this->load('getPrestashop_Adapter_DataProvider_EmployeeService.php')) && false ?: '_'}, ${($_ = isset($this->services['prestashop.adapter.mail_template.partial_template_renderer']) ? $this->services['prestashop.adapter.mail_template.partial_template_renderer'] : $this->load('getPrestashop_Adapter_MailTemplate_PartialTemplateRendererService.php')) && false ?: '_'}, ${($_ = isset($this->services['prestashop.core.localization.locale.context_locale']) ? $this->services['prestashop.core.localization.locale.context_locale'] : $this->load('getPrestashop_Core_Localization_Locale_ContextLocaleService.php')) && false ?: '_'}, ${($_ = isset($this->services['translator.default']) ? $this->services['translator.default'] : $this->getTranslator_DefaultService()) && false ?: '_'});