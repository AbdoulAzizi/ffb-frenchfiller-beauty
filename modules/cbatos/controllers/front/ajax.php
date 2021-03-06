<?php

require_once 'basebankreturn.php';

class CbAtosAjaxModuleFrontController extends CbAtosBaseBankReturnFrontController
{
    protected function getResponseType()
    {
        return CbAtosModuleResponseObject::TYPE_USER;
    }

    public function initContent()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        $id_cart = (int)$this->bankResponse->order_id;
        $action = Tools::getValue('action');
        switch ($action) {
            case 'process-response':
                if (empty($this->bankMessage)) {
                    header(null, null, 400);
                    exit;
                }
                if (!($this->bankResponse instanceof CbAtosModuleResponseObject)) {
                    header(null, null, 503);
                }
                $lock = null;
                $hasLock = $this->module->tryCreateResponseLock($id_cart, $lock);
                if (!$hasLock) {
                    die(json_encode([
                        'result' => false
                    ]));
                }
                $order = $this->module->processResponse($this->bankResponse);
                $this->module->removeResponseLock($id_cart, $lock);
                $url = null;
                if ($order instanceof OrderCore) {
                    $url = $this->context->link->getPageLink('order-confirmation', null, null, [
                        'id_cart' => $id_cart,
                        'id_module' => $this->module->id,
                        'key' => $order->secure_key,
                        'sips_message' => $this->bankMessage
                    ]);
                } else {
                    $url = $this->module->getModuleLink(CbAtos::CTRL_PAYMENT_FAILURE, [
                        'message' => $this->bankMessage
                    ]);
                }
                die(json_encode([
                    'result' => true,
                    'url' => $url
                ]));
            default:
                die('');
        }

    }

}
