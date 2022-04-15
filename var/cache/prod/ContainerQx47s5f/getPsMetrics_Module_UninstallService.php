<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'ps_metrics.module.uninstall' shared service.

return $this->services['ps_metrics.module.uninstall'] = new \PrestaShop\Module\Ps_metrics\Module\Uninstall(${($_ = isset($this->services['ps_metrics.module']) ? $this->services['ps_metrics.module'] : $this->load('getPsMetrics_ModuleService.php')) && false ?: '_'}, ${($_ = isset($this->services['ps_metrics.repository.configuration']) ? $this->services['ps_metrics.repository.configuration'] : $this->load('getPsMetrics_Repository_ConfigurationService.php')) && false ?: '_'}, ${($_ = isset($this->services['ps_metrics.helper.json']) ? $this->services['ps_metrics.helper.json'] : $this->load('getPsMetrics_Helper_JsonService.php')) && false ?: '_'}, ${($_ = isset($this->services['ps_metrics.helper.module']) ? $this->services['ps_metrics.helper.module'] : ($this->services['ps_metrics.helper.module'] = new \PrestaShop\Module\Ps_metrics\Helper\ModuleHelper())) && false ?: '_'}, ${($_ = isset($this->services['ps_metrics.helper.logger']) ? $this->services['ps_metrics.helper.logger'] : ($this->services['ps_metrics.helper.logger'] = new \PrestaShop\Module\Ps_metrics\Helper\LoggerHelper())) && false ?: '_'}, ${($_ = isset($this->services['ps_metrics.api.analytics']) ? $this->services['ps_metrics.api.analytics'] : $this->load('getPsMetrics_Api_AnalyticsService.php')) && false ?: '_'});