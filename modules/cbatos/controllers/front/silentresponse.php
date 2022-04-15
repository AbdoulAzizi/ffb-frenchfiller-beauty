<?php

require_once 'basebankreturn.php';

class CbAtosSilentResponseModuleFrontController extends CbAtosBaseBankReturnFrontController
{
	public $ssl = false;

    protected function getResponseType()
    {
        return CbAtosModuleResponseObject::TYPE_SILENT;
    }

    public function init()
    {
        parent::init();
        if (empty($this->bankMessage))
        {
            header(null, null, 400);
            exit;
        }
        if (!($this->bankResponse instanceof CbAtosModuleResponseObject))
        {
            header(null, null, 403);
            exit;
        }
        $id_cart = (int)$this->bankResponse->order_id;
        $lock = null;
        if ($this->module->tryCreateResponseLock($id_cart, $lock)) {
            $this->module->processResponse($this->bankResponse);
            $this->module->removeResponseLock($id_cart, $lock);
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        header(null, null, 200);
        exit;
    }

	protected function sslRedirection() {}
}
