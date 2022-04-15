<?php
require_once 'basebankreturn.php';

class CbAtosUserReturnModuleFrontController extends CbAtosBaseBankReturnFrontController
{
	public $ssl = true;

    protected function getResponseType()
    {
        return CbAtosModuleResponseObject::TYPE_USER;
    }

    public function init()
	{
		parent::init();

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}

	public function initContent()
	{
		parent::initContent();
		if (empty($this->bankMessage))
			Tools::redirectLink($this->context->link->getPageLink('history.php', true));
        $this->context->smarty->assign(array(
            'cbatos_pathURI' => $this->module->getPathUri(),
            'cbatos_ajaxUrl' => $this->module->getModuleLink('ajax', ['DATA' => $this->bankMessage], true),
        ));
        $this->registerStylesheet('modules-cbatos-processing_payment_response', 'modules/'.$this->module->name.'/views/css/processing_payment_response.css');
        $this->registerJavascript('modules-cbatos-processing_payment_response', 'modules/'.$this->module->name.'/views/js/processing_payment_response.js');
        $this->setTemplate('module:'.$this->module->name.'/views/templates/front/processing_payment_response.tpl');
	}
}
