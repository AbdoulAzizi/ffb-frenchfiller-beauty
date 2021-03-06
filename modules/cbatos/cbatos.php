<?php

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class CbAtos extends PaymentModule
{
	const IN_NONE = 0;
	const IN_TEXT = 1;
	const IN_SELECT = 2;
	const IN_CHECKBOX = 3;
	const IN_INTERNAL = 4;
	const IN_TEXTAREA = 5;
	const T_NONE = 0;
	const T_BOOL = 1;
	const T_INT = 2;
	const T_UNSIGNED_INT = 3;
	const T_ABS_POSITIVE_INT = 4;
	const T_FLOAT = 5;
	const T_UNSIGNED_FLOAT = 6;
	const T_STRING = 7;
	const T_PATH = 8;
	const T_URI = 9;
	const RETURN_PROTOCOL_AUTO = '';
	const RETURN_PROTOCOL_HTTP = 'http://';
	const RETURN_PROTOCOL_HTTPS = 'https://';
	const RETURN_DOMAIN_AUTO = '';
	const RETURN_CONTEXT_USER = 'user';
	const RETURN_CONTEXT_SILENT = 'silent';
	const AUTODISPATCHING_MASK = 'autodispatch/%s.pub.php';
	const CTRL_USER_RETURN = 'userreturn';
	const CTRL_SILENT_RESPONSE = 'silentresponse';
	const CTRL_PAYMENT_GATEWAY = 'paymentgateway';
	const CTRL_PAYMENT_FAILURE = 'paymentfailure';
	const BLOCK_ALIGN_CENTER = 'center';
	const BLOCK_ALIGN_LEFT = 'left';
	const BLOCK_ALIGN_RIGHT = 'right';
	const BIN_REQUEST = 'request';
	const BIN_RESPONSE = 'response';
	const PATHFILE = 'pathfile';
	const PARMCOM = 'parmcom';
	const CERTIF = 'certif';
	const PATHFILE_VARLENGTH = 78;
	const RECEIPT_COMPLEMENT_MAXLENGTH = 3072;
	const MODE_SINGLE = 1;
	const MODE_2TPAYMENT = 2;
	const MODE_3TPAYMENT = 3;
	const TABLE_TRANSACTION_TODAY = '_transactions_today';
	const TABLE_RESPONSE_LOCK = '_response_lock';
	const ATOS_FIELD_TRANSACTION_ID = 'transaction_id';
	const ATOS_FIELD_AUTHORISATION_ID = 'authorisation_id';
	const ATOS_FIELD_PAYMENT_CERTIFICATE = 'payment_certificate';
	const CNF_VERSION = 'VERSION';
	const CONVERT_TO_DEFAULT = 1;
	const CONVERT_FROM_DEFAULT = 2;
	const CNF_BANK = 'BANK';
	const CNF_PRODUCTION = 'PRODUCTION';
	const CNF_MERCHANT_ID = 'MERCHANT_ID';
	const CNF_ISO_LANG = 'ISO_LANG';
	const CNF_CAPTURE_DAY = 'CAPTURE_DAY';
	const CNF_CAPTURE_MODE = 'CAPTURE_MODE';
	const CNF_RESPONSE_LOG_TXT = 'RESPONSE_LOG_TXT';
	const CNF_RESPONSE_LOG_CSV = 'RESPONSE_LOG_CSV';
	const CNF_LOG_PATH = 'LOG_PATH';
	const CNF_ORDER_MESSAGE = 'ORDER_MESSAGE';
	const CNF_CHECK_VERSION = 'CHECK_VERSION';
	const CNF_OS_PAYMENT_CANCELLED = 'OS_PAYMENT_CANCELLED';
	const CNF_OS_PAYMENT_FAILED = 'OS_PAYMENT_FAILED';
	const CNF_ONECLICK_ENABLE = 'ONECLICK_ENABLE';
	const CNF_OS_NONZERO_COMPCODE = 'OS_NONZERO_COMPCODE';
	const CNF_SINGLE = 'SINGLE';
	const CNF_PAYMENT_MEANS = 'PAYMENT_MEANS';
	const CNF_MINAMOUNT = 'MINAMOUNT';
	const CNF_OS_PAYMENT_SUCCESS = 'OS_PAYMENT_SUCCESS';
	const CNF_CARD_IMG_PATH = 'CARD_IMG_PATH';
	const CNF_BLOCK_ALIGN = 'BLOCK_ALIGN';
	const CNF_BLOCK_ORDER = 'BLOCK_ORDER';
	const CNF_HEADER_FLAG = 'HEADER_FLAG';
	const CNF_TARGET = 'TARGET';
	const CNF_TEMPLATE_FILE = 'TEMPLATE_FILE';
	const CNF_LOGO_LEFT = 'LOGO_LEFT';
	const CNF_LOGO_CENTER = 'LOGO_CENTER';
	const CNF_LOGO_RIGHT = 'LOGO_RIGHT';
	const CNF_LOGO_SUBMIT = 'LOGO_SUBMIT';
	const CNF_LOGO_NORMAL_RETURN = 'LOGO_NORMAL_RETURN';
	const CNF_LOGO_CANCEL_RETURN = 'LOGO_CANCEL_RETURN';
	const CNF_BG_IMAGE = 'BG_IMAGE';
	const CNF_BG_COLOR = 'BG_COLOR';
	const CNF_TXT_COLOR = 'TXT_COLOR';
	const CNF_TXT_FONT = 'TXT_FONT';
	const CNF_CONCURRENCY_MAX_WAIT = 'CONCUR_MAX_WAIT';
	const CNF_NO_TID_GENERATION = 'NO_TID_GENERATION';
	const CNF_MIN_TID = 'MIN_TID';
	const CNF_MAX_TID = 'MAX_TID';
	const CNF_FORCE_RETURN = 'FORCE_RETURN';
	const CNF_OP_FIELD_TID = 'OP_FIELD_TID';
	const CNF_BINARIES_IN_PATH = 'BINARIES_IN_PATH';
	const CNF_BIN_PATH = 'BIN_PATH';
	const CNF_BIN_SUFFIX = 'BIN_SUFFIX';
	const CNF_PARAM_PATH = 'PARAM_PATH';
	const CNF_RETURN_PROTOCOL_USER = 'RETURN_PROTOCOL_USER';
	const CNF_RETURN_DOMAIN_USER = 'RETURN_DOMAIN_USER';
	const CNF_RETURN_DOMAIN_SILENT = 'RETURN_DOMAIN_SILENT';
	const CNF_DEBUG_MODE = 'DEBUG_MODE';
	const CNF_DEBUG_GID = 'DEBUG_GID';
	const CNF_TID_TZ = 'TID_TZ';
	const CNF_DATA_CONTROLS = 'DATA_CONTROLS';
	const CNF_CUSTOM_DATA = 'CUSTOM_DATA';
	const CNF_2TPAYMENT = '2TPAYMENT';
	const CNF_2TPAYMENT_MEANS = '2TPAYMENT_MEANS';
	const CNF_2TPAYMENT_MINAMOUNT = '2TPAYMENT_MINAMOUNT';
	const CNF_2TPAYMENT_SPACING = '2TPAYMENT_SPACING';
	const CNF_2TPAYMENT_DELAY = '2TPAYMENT_DELAY';
	const CNF_2TPAYMENT_OS = '2TPAYMENT_OS';
	const CNF_2TPAYMENT_FP_FXD = '2TPAYMENT_FP_FXD';
	const CNF_2TPAYMENT_FP_PCT = '2TPAYMENT_FP_PCT';
	const CNF_3TPAYMENT = '3TPAYMENT';
	const CNF_3TPAYMENT_MEANS = '3TPAYMENT_MEANS';
	const CNF_3TPAYMENT_MINAMOUNT = '3TPAYMENT_MINAMOUNT';
	const CNF_3TPAYMENT_SPACING = '3TPAYMENT_SPACING';
	const CNF_3TPAYMENT_DELAY = '3TPAYMENT_DELAY';
	const CNF_3TPAYMENT_OS = '3TPAYMENT_OS';
	const CNF_3TPAYMENT_FP_FXD = '3TPAYMENT_FP_FXD';
	const CNF_3DS_BYPASS_UNDER_AMOUNT = '3DS_BYPASS_UNDER';
	const CNF_3DS_BYPASS_IF_VALIDATED_ORDER_OVER_AGE = '3DS_BYPASS_VORDERAGE';
	const CNF_3TPAYMENT_FP_PCT = '3TPAYMENT_FP_PCT';
	const FILE_ERROR_LOG = 'error.log';

	private $_confVars;
	private $_confVarsByName;
	private $_newConfVars = array(
		'3.3.0' => array(self::CNF_OS_NONZERO_COMPCODE, self::CNF_DATA_CONTROLS, self::CNF_CUSTOM_DATA),
		'4.1.0' => array(self::CNF_CONCURRENCY_MAX_WAIT),
		'4.1.2' => array(self::CNF_ONECLICK_ENABLE, self::CNF_3DS_BYPASS_UNDER_AMOUNT, self::CNF_3DS_BYPASS_IF_VALIDATED_ORDER_OVER_AGE)
	);
	private $_banks = array(
		'' => '',
		'cyberplus' => 'CyberPlus - Banque Populaire',
		'etransactions' => 'E-Transactions - Cr??dit Agricole',
		'elysnet' => 'ElysNet - CCF/HSBC',
		'mercanet' => 'Mercanet - BNP Paribas',
		'scelliusnet' => 'ScelliusNet - La Banque Postale',
		'sherlocks' => 'Sherlocks - LCL',
		'sogenactif' => 'Sogenactif - Soci??t?? G??n??rale',
		'webaffaires' => 'WebAffaires - Cr??dit du Nord',
		'citelis' => 'Cit??lis - Cr??dit Mutuel',
		'smc' => 'Soci??t?? Marseillaise de Cr??dit'
	);
	private $_demoCertificates = array(
		'cyberplus' => '038862749811111',
		'etransactions' => '013044876511111',
		'elysnet' => '014102450311111',
		'mercanet' => '082584341411111',
		'scelliusnet' => '014141675911111',
		'sherlocks' => '014295303911111',
		'sogenactif' => '014213245611111',
		'webaffaires' => '014022286611111',
		'citelis' => '029800266211111',
		'smc' => '011223344551111'
	);

	public function __construct()
	{
		$this->name = 'cbatos';
		$this->author = 'CS Internet Creations';
		$this->tab = 'payments_gateways';
		$this->need_instance = 1;
		$this->version = '7.0.2';
		$this->currencies_mode = 'checkbox';
		$this->ps_versions_compliancy['min'] = '1.7.0.0';
		$this->ps_versions_compliancy['max'] = '1.7';
		parent::__construct();
		if (empty($this->_path)) {
			$this->_path = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
		}
		if (empty($this->local_path)) {
			$this->local_path = _PS_MODULE_DIR_.$this->name.'/';
		}
		$this->displayName = $this->l('Credit Card Payment with SIPS/ATOS');
		$this->description = $this->l('SIPS/ATOS payment module by CS Internet Creations');
		$this->confirmUninstall = $this->l('Uninstall this module will erase your configuration including current transaction ID, continue ?');
		if (defined('_PS_ADMIN_DIR_')) {
			$this->autoCheck();
		}
	}

	public function get($varname)
	{
		$this->initConfVars();
		$value = Configuration::get(Tools::strtoupper($this->name).'_'.$varname);
		if ($this->_confVarsByName[$varname]['type'] == self::T_BOOL)
			$value = (bool)$value;
		return $value;
	}

	public function set($varname, $value)
	{
		$this->initConfVars();
		switch ($this->_confVarsByName[$varname]['type'])
		{
			case self::T_NONE:
				return false;
			case self::T_BOOL:
				$value = $value ? 1 : 0;
				break;
			case self::T_INT:
				$value = (int)$value;
				break;
			case self::T_UNSIGNED_INT:
				$value = max((int)$value, 0);
				break;
			case self::T_ABS_POSITIVE_INT:
				$value = max((int)$value, 1);
				break;
			case self::T_FLOAT:
				if (is_string($value))
				{
					$localeinfo = localeconv();
					$value = preg_replace('/[.,]/', $localeinfo['decimal_point'], $value);
				}
				$value = (float)$value;
				break;
			case self::T_UNSIGNED_FLOAT:
				if (is_string($value))
				{
					$localeinfo = localeconv();
					$value = preg_replace('/[.,]/', $localeinfo['decimal_point'], $value);
				}
				$value = max((float)$value, 0.0);
				break;
			case self::T_STRING:
			case self::T_URI:
				$value = (string)$value;
				break;
			case self::T_PATH:
				$value = rtrim((string)$value, '/\\').DIRECTORY_SEPARATOR;
				break;
			default:
				throw new PrestaShopModuleException('Unknown type for confVar '.$varname);
		}
		return Configuration::updateValue(Tools::strtoupper($this->name).'_'.$varname, $value);
	}

	public function deleteVar($varname)
	{
		Configuration::deleteByName(Tools::strtoupper($this->name).'_'.$varname);
	}

	public function getTable($table, $addPrefix = true)
	{
		return ($addPrefix ? _DB_PREFIX_ : '').$this->name.$table;
	}

	public function install()
	{
		$result = true;
		try {
			if (!parent::install()) {
				throw new Exception($this->l('Fatal error: parent::install(): Prestashop internal module installation procedure failed, installation can\'t go any further.'));
			}
			foreach (array('paymentOptions', 'paymentReturn') as $hook) {
				if ($result) {
					if (!$this->registerHook($hook)) {
						$result = false;
						$this->_errors[] = sprintf($this->l('Unable to subscribe to hook %s'), $hook);
					}
				}
			}

			if ($result) {
				$DB = Db::getInstance(TRUE);
				if (!$DB->execute('
						CREATE TABLE IF NOT EXISTS `'.$this->getTable(self::TABLE_TRANSACTION_TODAY).'` (
							`date`				DATE				NOT NULL,
							`transaction_id`	MEDIUMINT UNSIGNED	NOT NULL	AUTO_INCREMENT,
							PRIMARY KEY (`date`,`transaction_id`)
						)
						ENGINE=MyISAM
						;
				', false)) {
						throw new Exception(sprintf($this->l('Fatal error: Installation of the database table failed, error code: %u, error message: %s'), $DB->getNumberError(), $DB->getMsgError()));
				}
				$this->installResponseLockTable();
			}
		} catch (Exception $e) {
			$result = false;
			$this->_errors[] = sprintf('%s(%u): %s'.PHP_EOL.'%s'.PHP_EOL.'%s', $e->getFile(), $e->getLine(), $e->getMessage(), $e->getTraceAsString(), $e->getPrevious());
		}
		if ($result)
		{
			$this->setDefaults();
			$this->updateAtosParamFiles();
		}
		else
		{
			parent::uninstall();
		}
		if ($this->_errors) {
			foreach ($this->_errors as $error) {
				$this->error(__LINE__, 'CbAtos Installation: '.$error);
			}
		}
		return $result;
	}

	public function uninstall()
	{
		Db::getInstance(TRUE)->execute('DROP TABLE IF EXISTS `'.$this->getTable(self::TABLE_TRANSACTION_TODAY).'`', false);
		$this->initConfVars();
		foreach ($this->_confVarsByName as $varname)
			$this->deleteVar($varname);
		return parent::uninstall();
	}

	public function canProcess($mode = NULL, Cart $cart = null, $skipHealthChecks = FALSE)
	{
		if (!$this->id)
			return false;
		if (!$this->active)
			return false;

		switch ($mode)
		{
			case self::MODE_SINGLE:
				if (!$this->get(self::CNF_SINGLE)) return false;
				break;
			case self::MODE_2TPAYMENT:
				if (!$this->get(self::CNF_2TPAYMENT)) return false;
				break;
			case self::MODE_3TPAYMENT:
				if (!$this->get(self::CNF_3TPAYMENT)) return false;
				break;
			case NULL:
				break;
			default:
				throw new PrestaShopModuleException('Invalid Argument $mode');
		}

		if (!$skipHealthChecks) {
			if (!$this->get(self::CNF_BANK) || !array_key_exists($this->get(self::CNF_BANK), $this->_banks))
				return false;
			if ($this->get(self::CNF_PRODUCTION) && !$this->get(self::CNF_MERCHANT_ID))
				return false;
			if (!$this->get(self::CNF_NO_TID_GENERATION))
			{
				if ($this->get(self::CNF_MIN_TID) > $this->get(self::CNF_MAX_TID))
					return false;
				$last_tid = (int)Db::getInstance(TRUE)->getValue('SELECT max(`transaction_id`) as `value` FROM `'.$this->getTable(self::TABLE_TRANSACTION_TODAY).'` WHERE `date` = \''.date('Y-m-d').'\'', FALSE);
				if ($last_tid >= $this->get(self::CNF_MAX_TID))
					return false;
			}
		}

		if (!is_null($mode) && !empty($cart)) {
			if ($cart->getOrderTotal() < $this->defaultCurrencyConvert($this->getMinAmount($mode), $cart->id_currency, self::CONVERT_FROM_DEFAULT))
				return false;
		}
		return true;
	}

	public function hookPaymentOptions()
    {
        if (!$this->canProcess()) {
            return [];
        }
        $texts = [
            $this->l('Pay with credit card'),
            $this->l('Pay with credit card: spread your payments over two months'),
            $this->l('Pay with credit card: spread your payments over three months')
        ];
        $paymentOptions = [];

        $orderTotal = $this->context->cart->getOrderTotal();
        $transactionId = $this->generateTransactionId();
        for ($paymentMode = 1; $paymentMode <= 3; ++$paymentMode) {
            if (!$this->canProcess($paymentMode, $this->context->cart, true)) continue;
            $_paymentOption = new PaymentOption();
            $_paymentOption
                ->setModuleName($this->name)
                ->setCallToActionText($this->l($texts[$paymentMode-1]))
                ->setBinary(true)
                ->setForm($this->getPaymentRedirectionForm($orderTotal, $this->context->currency, $paymentMode, [
                    'customer_id' => $this->context->customer->id,
                    'order_id' => $this->context->cart->id
                ], $transactionId))
                
                ->setAdditionalInformation($this->fetch('module:'.$this->name.'/views/templates/hook/payment_option_additional.tpl'))
            ;
            $this->smarty->assign('cbatos_mode', $paymentMode);
            if ($this->getDebugMode()) {

                $_paymentOption->setForm(preg_replace('/&(?!(#[0-9]+|[a-zA-Z]+);)/', '&amp;', $_paymentOption->getForm()));
            }
            $paymentOptions[] = $_paymentOption;
        }

        return $paymentOptions;
    }

	public function getModuleLink($controller, array $params = array(), $ssl = null)
	{
	    return $this->context->link->getModuleLink($this->name, $controller, $params, $ssl);
	}

	public function getPathUri()
	{
		if (version_compare(_PS_VERSION_, '1.5', '>=')) {
			return parent::getPathUri();
		} else {
			return $this->_path;
		}
	}

	public function hookPaymentReturn($params)
	{
	    $message = Tools::getValue('sips_message');
	    if ($message) {
	        $response = $this->uncypherResponse($message, CbAtosModuleResponseObject::TYPE_USER);
            $this->smarty->assign('cbatos_response', $response);
        }
		return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
	}

	public function getPaymentRedirectionForm($amount, Currency $currency, $mode, $mergeParams = array(), $transaction_id = NULL)
	{
		$atosAmount = $amount;
		if ($currency->decimals)
			$atosAmount *= 100;

		$data = array();
		$params = array(
			'language' => $this->get(self::CNF_ISO_LANG) ? $this->get(self::CNF_ISO_LANG) : $this->context->language->iso_code,
			'merchant_id' => $this->get(self::CNF_PRODUCTION) ? $this->get(self::CNF_MERCHANT_ID) : $this->_demoCertificates[$this->get(self::CNF_BANK)],
			'currency_code' => $currency->iso_code_num,
			'caddie' => $this->encodeCaddieField($currency->iso_code),
			'amount' => (int)round($atosAmount),
			'pathfile' => $this->get(self::CNF_PARAM_PATH).self::PATHFILE,
			'normal_return_url' => $this->getBankReturnUri(self::RETURN_CONTEXT_USER),
			'cancel_return_url' => $this->getBankReturnUri(self::RETURN_CONTEXT_USER),
			'automatic_response_url' => $this->getBankReturnUri(self::RETURN_CONTEXT_SILENT)
		);
		if (!empty($_SERVER['REMOTE_ADDR']))
		{
			$params['customer_ip_address'] = Tools::substr($_SERVER['REMOTE_ADDR'], max(0, Tools::strlen($_SERVER['REMOTE_ADDR']) - 20), min(19, Tools::strlen($_SERVER['REMOTE_ADDR'])));
		}
		if (Tools::strlen($this->context->customer->email) <= 128)
			$params['customer_email'] = $this->context->customer->email;
		if (!is_null($transaction_id))
		{
			$params['transaction_id'] = $transaction_id;
		} elseif (!$this->get(self::CNF_NO_TID_GENERATION)) {
			$params['transaction_id'] = $this->generateTransactionId();
			if (empty($params['transaction_id']))
			{
				$this->error(__LINE__, 'No transaction_id has been generated', 4);
				return false;
			}
		}
		switch ($mode)
		{
			case self::MODE_SINGLE:
				$params['payment_means'] = $this->get(self::CNF_PAYMENT_MEANS);
				$params['capture_mode'] = $this->get(self::CNF_CAPTURE_MODE);
				$params['capture_day'] = $this->get(self::CNF_CAPTURE_DAY);
				break;
			case self::MODE_2TPAYMENT:
				$params['payment_means'] = $this->get(self::CNF_2TPAYMENT_MEANS);
				$params['capture_mode'] = 'PAYMENT_N';
				$params['capture_day'] = $this->get(self::CNF_2TPAYMENT_DELAY);
				$initialAmount = $this->defaultCurrencyConvert($this->get(self::CNF_2TPAYMENT_FP_FXD), $currency, self::CONVERT_FROM_DEFAULT) + $this->get(self::CNF_2TPAYMENT_FP_PCT) / 100 * $amount;
				if ($currency->decimals)
					$initialAmount *= 100;
				$initialAmount = str_pad((string)(int)Tools::ps_round($initialAmount), 3, '0', STR_PAD_LEFT);
				array_push($data, 'NB_PAYMENT=2', 'PERIOD='.$this->get(self::CNF_2TPAYMENT_SPACING), 'INITIAL_AMOUNT='.$initialAmount);
				break;
			case self::MODE_3TPAYMENT:
				$params['payment_means'] = $this->get(self::CNF_3TPAYMENT_MEANS);
				$params['capture_mode'] = 'PAYMENT_N';
				$params['capture_day'] = $this->get(self::CNF_3TPAYMENT_DELAY);
				$initialAmount = $this->defaultCurrencyConvert($this->get(self::CNF_3TPAYMENT_FP_FXD), $currency, self::CONVERT_FROM_DEFAULT) + $this->get(self::CNF_3TPAYMENT_FP_PCT) / 100 * $amount;
				if ($currency->decimals)
					$initialAmount *= 100;
				$initialAmount = str_pad((string)(int)Tools::ps_round($initialAmount), 3, '0', STR_PAD_LEFT);
				array_push($data, 'NB_PAYMENT=3', 'PERIOD='.$this->get(self::CNF_3TPAYMENT_SPACING), 'INITIAL_AMOUNT='.$initialAmount);
				break;
		}
		if ($this->get(self::CNF_FORCE_RETURN)) {
			array_push($data, 'NO_RESPONSE_PAGE');
		}
		if ($this->get(self::CNF_ONECLICK_ENABLE)) {
			array_push($data, 'WALLET_ID=CUSTOMER_ID');
		}
		$bBypass3DS = false;
		$iNo3DSMaxCartAmount = $this->get(self::CNF_3DS_BYPASS_UNDER_AMOUNT);
		if ($iNo3DSMaxCartAmount > 0) {
			if ($this->defaultCurrencyConvert($amount, $currency, self::CONVERT_TO_DEFAULT) < $iNo3DSMaxCartAmount) {
				$bBypass3DS = true;
			}
		}
		unset($iNo3DSMaxCartAmount);
		$iNo3DSWhenValidatedOrderExistsThatOld = $this->get(self::CNF_3DS_BYPASS_IF_VALIDATED_ORDER_OVER_AGE);
		if (!$bBypass3DS && ($iNo3DSWhenValidatedOrderExistsThatOld > 0) && ($this->context->customer instanceof Customer)) {
			$dbConn = Db::getInstance(_PS_USE_SQL_SLAVE_);
			$iValidOrdersCount = $dbConn->getValue(
				'
					SELECT count(`o`.`id_order`)
					FROM `'._DB_PREFIX_.'orders` `o`
					WHERE
						`o`.`id_customer` = '.(int)$this->context->customer->id.'
						AND
						`o`.`valid`
						AND
						`o`.`date_add` < \''.pSQL(date('Y-m-d H:i:s', strtotime('- '.$iNo3DSWhenValidatedOrderExistsThatOld.' days'))).'\'
				',
				false
			);
			if ($iValidOrdersCount > 0) {
				$bBypass3DS = true;
			}
			unset($iValidOrdersCount);
		}
		unset($iNo3DSWhenValidatedOrderExistsThatOld);
		if ($bBypass3DS) {
			array_push($data, '3D_BYPASS');
		}
		unset($bBypass3DS);
		$controls = str_replace("\n", '', str_replace("\r", '', $this->get(self::CNF_DATA_CONTROLS)));
		if (!empty($controls))
		{
			array_push($data, '<CONTROLS>'.$controls.'</CONTROLS>');
		}
		unset($controls);
		$this->initConfVars();
		foreach ($this->_confVarsByName as $name => $varconf)
			if (!empty($varconf['autofeed']) && !empty($varconf['atos']))
				$params[$varconf['atos']] = $this->get($name);
		if (isset($mergeParams['data']))
		{
			if (is_null($mergeParams['data']))
			{
				$data = array();
			} else {
				$mergeData = is_array($mergeParams['data']) ? $mergeParams['data'] : explode(';', $mergeParams['data']);
				$data = array_merge($data, $mergeData);
				unset($mergeData);
			}
			unset($mergeParams['data']);
		}
		$customData = trim($this->get(self::CNF_CUSTOM_DATA), ' ;');
		if (!empty($customData))
		{
			$customData = explode(';', $customData);
			$data = array_merge($data, $customData);
		}
		unset($customData);
		$params['data'] = implode(';', $data);
		$params = array_merge($params,$mergeParams);
		if (!isset($params['receipt_complement']))
		{
			$this->smarty->assign(array(
				'CbAtos' => $this,
				'cbatos_cart' => $this->context->cart,
				'cbatos_params' => $params,
				'cbatos_mode' => $mode,
				'cbatos_fromCharset' => 'UTF-8',
				'cbatos_toCharset' => 'ISO-8859-1//TRANSLIT'
			), null, true);
			$params['receipt_complement'] = trim($this->display(__FILE__, 'views/templates/hook/param_receipt_complement.tpl'));
			$fromCharset = $this->smarty->getTemplateVars('cbatos_fromCharset');
			$toCharset = $this->smarty->getTemplateVars('cbatos_toCharset');
			$this->smarty->clearAssign('cbatos_fromCharset');
			$this->smarty->clearAssign('cbatos_toCharset');
			if (!empty($params['receipt_complement'])) {
				$params['receipt_complement'] = iconv($fromCharset, $toCharset, $params['receipt_complement']);
				if ($params['receipt_complement'] === FALSE)
				{
					$this->error(__LINE__, sprintf('Iconv failed to convert encoding of receipt_complement from %s to %s', $fromCharset, $toCharset), 3);
					unset($params['receipt_complement']);
				} else {
					$rawReceipt = $params['receipt_complement'];
					$params['receipt_complement'] = '';

					for ($c = 0; $c < Tools::strlen($rawReceipt); $c++)
						if (ord($rawReceipt[$c]) <= 128)
							$params['receipt_complement'] .= $rawReceipt[$c];
						else
							$params['receipt_complement'] .= '&#'.ord($rawReceipt[$c]).';';
					if (Tools::strlen($params['receipt_complement']) > self::RECEIPT_COMPLEMENT_MAXLENGTH) {
						$this->error(__LINE__, sprintf('Receipt complement is too long: %u characters long, %u characters max.', Tools::strlen($params['receipt_complement']), self::RECEIPT_COMPLEMENT_MAXLENGTH), 3, $params['receipt_complement']);
						unset($params['receipt_complement']);
					}
				}
			} else {
				unset($params['receipt_complement']);
			}
		}
		$params['amount'] = str_pad((string)$params['amount'], 3, '0', STR_PAD_LEFT);
		$call = $this->rawCall(self::BIN_REQUEST, $this->paramsToArgs($params));
		if ($call->exit_code != 0)
		{
			$this->error(__LINE__, sprintf('Error when calling request ATOS binary, exit code was: '.$call->exit_code), 4, $call);
			return false;
		}
		$result = new CbAtosModuleRequestOutputParser($call);
		if (!$result->success)
		{
			$this->error(__LINE__, 'Atos invocation returned an error: '.$call->command, 4, $result);
			return false;
		}
		if ($this->getDebugMode())
		{
			return (empty($result->error) ? $result->form : $result->error);
		}
		return $result->form;
	}

	public function encodeCaddieField($currencyCode)
    {
        return json_encode([
            $currencyCode
        ]);
    }

    public function decodeCaddieField($caddieField, &$currencyCode)
    {
        $caddieDecoded = json_decode($caddieField, true);
        if ($caddieDecoded === null) {
            throw new Exception(sprintf('%s: cannot json decode caddie field: %s', __METHOD__, $caddieField));
        } elseif (!is_array($caddieDecoded)) {
            throw new Exception(sprintf('%s: unexpected decoded caddie field type: %s', __METHOD__, $caddieField));
        }
        list($currencyCode) = $caddieDecoded;
    }

	public function uncypherResponse($message, $responseType)
	{
		$params = array(
			'pathfile' => $this->get(self::CNF_PARAM_PATH).self::PATHFILE,
			'message' => $message
		);
		$call = $this->rawCall(self::BIN_RESPONSE, $this->paramsToArgs($params));
		if ($call->exit_code != 0)
		{
			$this->error(__LINE__, sprintf('Error when calling response ATOS binary, exit code was: %u', $call->exit_code), 4, $call);
			return false;
		}
		$result = new CbAtosModuleResponseOutputParser($call, $message, $responseType, $this);
		if (!$result->success)
		{
			$this->error(__LINE__, 'Failure to uncypher bank response '.$message, 4, $result);
			return false;
		}
		return $result->response;
	}


	public function processResponse(CbAtosModuleResponseObject $response)
	{
		if (is_null($response))
			throw new InvalidArgumentException('$response must be not null');
		$this->logResponse($response);
		$this->context->cart = new Cart((int)$response->order_id);
		if (!Validate::isLoadedObject($this->context->cart))
			throw new PrestaShopModuleException('Payment cart cannot be loaded');
		if (is_null($this->context->link))
			$this->context->link = new Link();

        if (class_exists('Cache', false) && method_exists('Cache', 'clean')) {
            Cache::clean('Cart::orderExists_'.(string)$response->order_id);
        }
		if ($this->context->cart->orderExists())
		{
			return Order::getByCartId($this->context->cart->id);
		} else {
		    $currencyCode = null;
		    $this->decodeCaddieField($response->caddie, $currencyCode);
			$this->context->currency = Currency::getCurrencyInstance(Currency::getIdByIsoCode($currencyCode));
			if (!Validate::isLoadedObject($this->context->currency))
				throw new PrestaShopModuleException('Payment currency cannot be loaded');
			if ($response->capture_mode == 'PAYMENT_N')
			{
                $responseNbPayment = $response->getDataVar('NB_PAYMENT');
                switch ($responseNbPayment)
				{
					case 2:
						$mode = self::MODE_2TPAYMENT;
						break;
					case 3:
						$mode = self::MODE_3TPAYMENT;
						break;
                    default:
                        throw new Exception(sprintf('Unexpected NB_PAYMENT value: %s', $responseNbPayment));
				}

				$timezone = new DateTimeZone($this->get(self::CNF_TID_TZ));
				$paymentDate = DateTime::createFromFormat('Ymd', $response->payment_date, $timezone);
				$period = new DateInterval(sprintf('P%uD', (int)$response->getDataVar('PERIOD')));
				for ($pn = 1; $pn < $response->getDataVar('NB_PAYMENT'); $pn++)
				{
					$paymentDate->add($period);
					$this->reserveTransactionId(DB::getInstance(), $paymentDate, $response->transaction_id, false, true);
				}
			} else {
				$mode = self::MODE_SINGLE;
			}
            $orderState = $this->get(self::CNF_OS_PAYMENT_FAILED);
            switch ($response->response_code)
			{
				case '00':
					switch ($mode)
					{
						case self::MODE_SINGLE:
							$orderState = $this->get(self::CNF_OS_PAYMENT_SUCCESS);
							break;
						case self::MODE_2TPAYMENT:
							$orderState = $this->get(self::CNF_2TPAYMENT_OS);
							break;
						case self::MODE_3TPAYMENT:
							$orderState = $this->get(self::CNF_3TPAYMENT_OS);
							break;
					}
					switch ($response->complementary_code)
					{
						case null:
						case '':
						case '00':
							break;
						default:
							$orderState = $this->get(self::CNF_OS_NONZERO_COMPCODE);
							break;
					}
					break;
				case '17':
					$orderState = $this->get(self::CNF_OS_PAYMENT_CANCELLED);
					break;
			}
			if (!$orderState)
				return NULL;
			$amount = $response->amount;
			if ($this->context->currency->decimals)
				$amount /= 100;
			$extraVars = array();
			$orderLog = $this->get(self::CNF_ORDER_MESSAGE) ? array() : null;
			foreach (CbAtosModuleResponseObject::$fields as $field)
			{
				$extraVars['cbatos_'.$field] = $response->{$field};
				if (is_array($orderLog)) $orderLog[] = $field.': '.$response->{$field};
			}
			$extraVars['transaction_id'] = $response->{$this->get(self::CNF_OP_FIELD_TID)};
			$this->validateOrder(
				$this->context->cart->id,
				$orderState,
				$amount,
				$this->displayName,
				implode(PHP_EOL, $orderLog),
				$extraVars,
				$this->context->currency->id,
				false,
				$this->context->cart->secure_key
			);
			$order = new Order($this->currentOrder);
			if (version_compare(_PS_VERSION_, '1.5', '>=')) {
				$orderPayments = OrderPayment::getByOrderReference($order->reference);
				if (is_array($orderPayments) && count($orderPayments) == 1) {
					$orderPayment = array_shift($orderPayments);
					$orderPayment->payment_method = $this->displayName;
					$orderPayment->id_currency = $this->context->currency->id;
					$orderPayment->conversion_rate = $this->context->currency->conversion_rate;
					$orderPayment->card_brand = $response->payment_means;
					$orderPayment->card_number = str_replace('.', ' #### #### ##', $response->card_number);
					if ($response->capture_mode == 'PAYMENT_N') {
						$orderPayment->payment_method .= ' x' . $response->getDataVar('NB_PAYMENT');
					}
					$orderPayment->save();
				}
			}
			return $order;
		}
	}


	protected $_defaultCurrency = null;
	public function getDefaultCurrency()
	{
		if (is_null($this->_defaultCurrency))
			$this->_defaultCurrency = Currency::getDefaultCurrency();
		return $this->_defaultCurrency;
	}

	public function defaultCurrencyConvert($amount, $currency, $direction)
	{
		if (is_numeric($currency))
			$currency = Currency::getCurrencyInstance((int)$currency);
		if (!Validate::isLoadedObject($currency))
			throw new PrestaShopModuleException('Argument $currency must be a Currency object or a valid currency ID');
		$amount = (float)$amount;
		if ($this->getDefaultCurrency()->conversion_rate != $currency->conversion_rate)
			switch ($direction) 
			{
				case self::CONVERT_TO_DEFAULT:
					$amount *= (float)$this->getDefaultCurrency()->conversion_rate / (float)$currency->conversion_rate;
					break;
				case self::CONVERT_FROM_DEFAULT:
					$amount /= (float)$this->getDefaultCurrency()->conversion_rate / (float)$currency->conversion_rate;
					break;
				default:
					throw new PrestaShopModuleException('Invalid Argument $direction (must be self::CONVERT_*)');
			}
		return $amount;
	}

	public function getMinAmount($mode)
	{
		switch ($mode)
		{
			case self::MODE_SINGLE:
				return $this->get(self::CNF_MINAMOUNT);
			case self::MODE_2TPAYMENT:
				return $this->get(self::CNF_2TPAYMENT_MINAMOUNT);
			case self::MODE_3TPAYMENT:
				return $this->get(self::CNF_3TPAYMENT_MINAMOUNT);
			default:
				throw new PrestaShopModuleException('Invalid Argument $mode (must be self::MODE_*)');
		}
	}

	public function getBankReturnUri($context)
	{

		switch ($context)
		{
			case self::RETURN_CONTEXT_USER:
				$protocol = $this->get(self::CNF_RETURN_PROTOCOL_USER);
				$domain = $this->get(self::CNF_RETURN_DOMAIN_USER);
				$controller = $this->getPathUri().sprintf(self::AUTODISPATCHING_MASK, self::CTRL_USER_RETURN);
				break;
			case self::RETURN_CONTEXT_SILENT:
				return $this->getModuleLink(self::CTRL_SILENT_RESPONSE, [], false);
			default:
				throw new PrestaShopModuleException('Invalid Argument $context (must be self::RETURN_CONTEXT_*)');
		}
		if ($protocol == self::RETURN_PROTOCOL_AUTO)
		{
			$protocol = Configuration::get('PS_SSL_ENABLED') ? self::RETURN_PROTOCOL_HTTPS : self::RETURN_PROTOCOL_HTTP;
		}
		if ($domain == self::RETURN_DOMAIN_AUTO)
		{
			$domain = ($protocol == self::RETURN_PROTOCOL_HTTPS) ? Tools::getShopDomainSsl(false) : Tools::getShopDomain(false);
		}
		return $protocol.$domain.$controller;
	}

	public function getDebugMode()
	{
		return $this->get(self::CNF_DEBUG_MODE)
				|| (
					($gid = $this->get(self::CNF_DEBUG_GID))
					&& in_array($gid, $this->context->customer->getGroups(), false)
				)
		;
	}

	public function generateTransactionId()
	{
		$DB = Db::getInstance(true);
		$timezoneStr = $this->get(self::CNF_TID_TZ);
		if (empty($timezoneStr))
		{
			$timezoneStr = 'UTC';
		}
		$timezone = new DateTimeZone($timezoneStr);
		$yesterday = new DateTime('-1 day', $timezone);
		$DB->delete($this->getTable(self::TABLE_TRANSACTION_TODAY, false), '`date` < \''.$yesterday->format('Y-m-d').'\'');
		$now = new DateTime('now', $timezone);
		$tid = $this->reserveTransactionId($DB, $now);
		if ($tid < $this->get(self::CNF_MIN_TID))
		{
			$this->reserveTransactionId($DB, $now, $this->get(self::CNF_MIN_TID) - 1, false, true);
			$tid = $this->reserveTransactionId($DB, $now);
		}
		if ($tid > $this->get(self::CNF_MAX_TID))
			return false;
		return $tid;
	}

	public function reserveTransactionId(Db $DB, DateTime $date, $id = null, $throwException = true, $ignoreDuplicate = false)
	{
		$data = array('date' => $date->format('Y-m-d'));
		if (!is_null($id)) {
			$data['transaction_id'] = $id;
		}
		$result = null;
		if (version_compare(_PS_VERSION_, '1.5', '>=')) {
			$result = $DB->insert($this->getTable(self::TABLE_TRANSACTION_TODAY, false), $data, false, false, $ignoreDuplicate ? Db::INSERT_IGNORE : Db::INSERT);
		} else {
			$data_sql = array();
			foreach ($data as $_key => $_value) {
				$data_sql[] = '`'.$_key.'` = \''.pSQL($_value, true).'\'';
			}
			$result = $DB->Execute(
				($ignoreDuplicate
					? 'INSERT IGNORE'
					: 'INSERT'
				).' INTO `'.$this->getTable(self::TABLE_TRANSACTION_TODAY, true).'`
				SET '.implode(', ', $data_sql)
			);
		}
		if (!$result) {
			if ($throwException) {
				throw new PrestaShopDatabaseException();
			}
			return null;
		}
		return $DB->Insert_ID();
	}

	public function getResponseLock($id_cart)
	{
		$DB = Db::getInstance(true);
		$result = $DB->getValue('SELECT `lock` from `'.$this->getTable(self::TABLE_RESPONSE_LOCK).'` WHERE `id_cart` = '.(int)$id_cart, false);
        return $result;
	}

	public function tryCreateResponseLock($id_cart, &$lock = null)
	{
	    if (is_null($lock)) {
            $lock = uniqid('', true);
        }
		$DB = Db::getInstance(true);
		$DB->execute('
			INSERT IGNORE INTO `'.$this->getTable(self::TABLE_RESPONSE_LOCK).'`
			SET
				`id_cart` = '.(int)$id_cart.',
				`lock` = \''.pSQL($lock).'\'
			;
		', false);
		return $lock == $this->getResponseLock($id_cart);
	}

	public function removeResponseLock($id_cart, $lock)
	{
		$DB = Db::getInstance(true);
		$DB->execute('
			DELETE FROM `'.$this->getTable(self::TABLE_RESPONSE_LOCK).'`
			WHERE
				`id_cart` = '.(int)$id_cart.' AND
				`lock` = \''.pSQL($lock).'\'
			;
		', false);
	}

	public function waitForLockRemoval($id_cart)
	{
		$max_wait_mts = microtime(true) + (int)$this->get(self::CNF_CONCURRENCY_MAX_WAIT);
		$success = false;
		while (($max_wait_mts > microtime(true)) && !$success) {
			usleep(100000); 
			$success = !$this->getResponseLock($id_cart);
		}
		return $success;
	}

	protected function initConfVars()
	{
		if (!empty($this->_confVars)) return;
		$comment = 0;
        $this->_confVars = array(
			'INTERNAL' => array(
				self::CNF_VERSION => array(
					'type' => self::T_STRING,
					'input' => self::IN_INTERNAL,
					'default' => '0'
				),
			),
			'BASIC' => array(
				self::CNF_BANK => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('Your bank'),
					'values' => $this->_banks,
					'default' => ''
				),
				self::CNF_PRODUCTION => array(
					'type' => self::T_BOOL,
					'input' => self::IN_SELECT,
					'description' => $this->l('ATOS Run mode'),
					'values' => array(
						FALSE => $this->l('Demonstration: Use your bank\'s demo certificate'),
						TRUE => $this->l('(Pre-)Production: Use your production certificate')
					),
					'default' => FALSE
				),
				self::CNF_MERCHANT_ID => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'template' => 'merchant_id',
					'description' => $this->l('Select the production certificate (unused in demonstration mode)'),
					'hint' => Tools::htmlentitiesDecodeUTF8(sprintf($this->l('Your production certificate must be uploaded to "%s" defined in advanced configuration, named certif.fr.xxxxxxxxxxxxxxx where xxxxxxxxxxxxxxx is your merchant ID, this file has to be read/write protected, only PHP and you should be able to read it, and only you should be able to modify it.'), $this->l('Location of ATOS configuration'))),
					'values' => new CbAtosModuleFunctionCall('getMerchantIdList', array(TRUE)),
					'default' => ''
				),
				self::CNF_ISO_LANG => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Force a language in payment server'),
					'hint' => $this->l('If none given, ISO code of the language used by client to browse your shop will be sent to ATOS API. See ATOS doc. for available language codes.'),
					'default' => ''
				),
				self::CNF_RESPONSE_LOG_TXT => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Log bank responses in human readable format'),
					'default' => TRUE
				),
				self::CNF_RESPONSE_LOG_CSV => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Log bank responses in CSV format (currently needed to display payment information on user return).'),
					'default' => TRUE
				),
				self::CNF_LOG_PATH => array(
					'type' => self::T_PATH,
					'input' => self::IN_TEXT,
					'description' => $this->l('Responses logs storage path'),
					'hint' => $this->l('MUST only be accessible to you and PHP user. MUST be writable by PHP user.'),
					'width' => '100%',
					'default' => $this->local_path . 'log'.DIRECTORY_SEPARATOR
				),
				self::CNF_ORDER_MESSAGE => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Add a summary of the transaction as order message'),
					'hint' => $this->l('Only visible by back office users.'),
					'default' => TRUE
				),
				self::CNF_OS_PAYMENT_CANCELLED => array(
					'type' => self::T_UNSIGNED_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Order state to apply on cancel return'),
					'values' => CbAtosModuleFunctionCall::factory('getOrderStatesSelectArray', array($this->l('No order creation'))),
					'default' => 0
				),
				self::CNF_OS_PAYMENT_FAILED => array(
					'type' => self::T_UNSIGNED_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Order state to apply on payment fail return'),
					'values' => CbAtosModuleFunctionCall::factory('getOrderStatesSelectArray', array($this->l('No order creation'))),
					'default' => 0
				),
				self::CNF_OS_NONZERO_COMPCODE => array(
					'type' => self::T_UNSIGNED_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Order state to apply on payment success with a non zero complementary_code'),
					'values' => CbAtosModuleFunctionCall::factory('getOrderStatesSelectArray'),
					'default' => Configuration::get('PS_OS_PAYMENT')
				),
				self::CNF_ONECLICK_ENABLE => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Enable OneClick payment feature.'),
					'hint' => $this->l('Requires your SIPS contract to include this feature.'),
					'default' => FALSE
				)
			),
			'SINGLE' => array(
				self::CNF_SINGLE => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Enable single payment mode'),
					'default' => FALSE
				),
				self::CNF_PAYMENT_MEANS => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Accepted payment means'),
					'width' => '100%',
					'default' => 'CB,3,VISA,3,MASTERCARD,3'
				),
				self::CNF_MINAMOUNT => array(
					'type' => self::T_UNSIGNED_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Disable this payment method when cart amount is below this value'),
					'default' => 0.0
				),
				self::CNF_CAPTURE_MODE => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('Select capture mode to apply'),
					'hint' => $this->l('See ATOS doc. about capture mode.'),
					'values' => array(
						'AUTHOR_CAPTURE' => 'AUTHOR_CAPTURE',
						'VALIDATION' => 'VALIDATION'
					),
					'default' => 'AUTHOR_CAPTURE'
				),
				self::CNF_CAPTURE_DAY => array(
					'type' => self::T_UNSIGNED_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Select capture delay'),
					'hint' => $this->l('See ATOS doc. about capture mode.'),
					'values' => range(0,99),
					'default' => 0
				),
				self::CNF_OS_PAYMENT_SUCCESS => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Select order state to apply on a successful payment'),
					'values' => CbAtosModuleFunctionCall::factory('getOrderStatesSelectArray'),
					'default' => version_compare(_PS_VERSION_, '1.5', '>=')
						? Configuration::get('PS_OS_PAYMENT')
						: _PS_OS_PAYMENT_
				),
			),
			'2TIMES' => array(
				self::CNF_2TPAYMENT => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Enable 2 times payments mode'),
					'default' => FALSE
				),
				self::CNF_2TPAYMENT_MEANS => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Accepted payment means'),
					'width' => '100%',
					'default' => 'CB,3,VISA,3,MASTERCARD,3'
				),
				self::CNF_2TPAYMENT_MINAMOUNT => array(
					'type' => self::T_UNSIGNED_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Minimum cart amount to use 2 times payments'),
					'default' => 0.0
				),
				self::CNF_2TPAYMENT_DELAY => array(
					'type' => self::T_UNSIGNED_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Days before first payment'),
					'values' => range(0,99),
					'default' => 0
				),
				self::CNF_2TPAYMENT_SPACING => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Days between the payments'),
					'values' => $this->_mirrorArray(range(1,30)),
					'default' => 30
				),
				self::CNF_2TPAYMENT_OS => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Order state to apply'),
					'values' => CbAtosModuleFunctionCall::factory('getOrderStatesSelectArray'),
					'default' => Configuration::get('PS_OS_PAYMENT')
				),
				self::CNF_2TPAYMENT_FP_FXD => array(
					'type' => self::T_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('First payment fixed amount'),
					'default' => 0.0
				),
				self::CNF_2TPAYMENT_FP_PCT => array(
					'type' => self::T_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('First payment amount relative to cart amount'),
					'hint' => $this->l('Added to fixed amount.'),
					'field_suffix' => '%',
					'default' => 50.0
				),
			),
			'3TIMES' => array(
				self::CNF_3TPAYMENT => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Enable 3 times payments mode'),
					'default' => FALSE
				),
				self::CNF_3TPAYMENT_MEANS => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Accepted payment means'),
					'width' => '100%',
					'default' => 'CB,3,VISA,3,MASTERCARD,3'
				),
				self::CNF_3TPAYMENT_MINAMOUNT => array(
					'type' => self::T_UNSIGNED_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Minimum cart amount to use 3 times payments'),
					'default' => 0.0
				),
				self::CNF_3TPAYMENT_DELAY => array(
					'type' => self::T_UNSIGNED_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Days before first payment'),
					'values' => range(0,99),
					'default' => 0
				),
				self::CNF_3TPAYMENT_SPACING => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Days between the payments'),
					'values' => $this->_mirrorArray(range(1,30)),
					'default' => 30
				),
				self::CNF_3TPAYMENT_OS => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('Order state to apply'),
					'values' => CbAtosModuleFunctionCall::factory('getOrderStatesSelectArray'),
					'default' => Configuration::get('PS_OS_PAYMENT')
				),
				self::CNF_3TPAYMENT_FP_FXD => array(
					'type' => self::T_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('First payment fixed amount'),
					'default' => 0.0
				),
				self::CNF_3TPAYMENT_FP_PCT => array(
					'type' => self::T_FLOAT,
					'input' => self::IN_TEXT,
					'description' => $this->l('First payment amount relative to cart amount'),
					'hint' => $this->l('Added to fixed amount.'),
					'field_suffix' => '%',
					'default' => 33.4
				),
			),
			'GRAPHIC' => array(
				'comment'.($comment++) => array(
					'type' => self::T_NONE,
					'input' => self::T_NONE,
					'text' => $this->l('Following options are applied to the form redirecting to bank server (the clickable card logos). See ATOS pages customisation doc.')
				),
				self::CNF_CARD_IMG_PATH => array(
					'type' => self::T_URI,
					'input' => self::IN_TEXT,
					'description' => $this->l('Web URI of the folder containing card logos'),
					'hint' => $this->l('Change this to use a custom card logos pack. You should put your logos in a theme sub-folder. An undocumented limitation of PATHFILE reader seems to limit this field to 78 characters.'),
					'width' => '100%',
					'default' => $this->_path . 'views/img/card_logo/'
				),
/*				self::CNF_BLOCK_ORDER => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Block order'),
					'default' => '1,2,3,4,5,6,7,8'
				),  
				self::CNF_BLOCK_ALIGN => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('Block alignement'),
					'values' => array(
						self::BLOCK_ALIGN_LEFT => $this->l('Left'),
						self::BLOCK_ALIGN_CENTER => $this->l('Center'),
						self::BLOCK_ALIGN_RIGHT => $this->l('Right')
					),
					'default' => self::BLOCK_ALIGN_CENTER
				),
				self::CNF_HEADER_FLAG => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('Payment security comment'),
					'values' => array(
						'yes' => $this->l('Yes'),
						'no' => $this->l('No')
					),
					'default' => 'yes'
				),
				self::CNF_TARGET => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Link to bank\'s page HTML target'),
					'default' => ''
				),
				'comment'.($comment++) => array(
					'type' => self::T_NONE,
					'input' => self::T_NONE,
					'text' => $this->l('Following options are applied to the pages located on bank server. See ATOS pages customisation doc.')
				),
				self::CNF_TEMPLATE_FILE => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Stylesheet'),
					'default' => ''
				),
				self::CNF_LOGO_LEFT => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Upper left logo'),
					'default' => ''
				),  
				self::CNF_LOGO_CENTER => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Center banner'),
					'default' => ''
				),  */
				self::CNF_LOGO_RIGHT => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Upper right logo'),
					'default' => 'merchant.gif'
				)
/*				self::CNF_LOGO_SUBMIT => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Submit logo'),
					'default' => ''
				),
				self::CNF_LOGO_NORMAL_RETURN => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Normal return logo'),
					'default' => ''
				),
				self::CNF_LOGO_CANCEL_RETURN => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Cancel return logo'),
					'default' => ''
				),
				self::CNF_BG_IMAGE => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('HTML Background image'),
					'default' => ''
				),
				self::CNF_BG_COLOR => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('HTML Background RGB color'),
					'default' => ''
				),
				self::CNF_TXT_FONT => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('HTML Text font'),
					'default' => ''
				),
				self::CNF_TXT_COLOR => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('HTML Text RGB color'),
					'default' => ''
				) */
			),   
			'ADVANCED' => array(
				self::CNF_FORCE_RETURN => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Force user return from bank'),
					'hint' => $this->l('Disables transaction summary on bank server, see NO_RESPONSE_PAGE data param in ATOS doc.'),
					'default' => FALSE
				),
				self::CNF_CONCURRENCY_MAX_WAIT => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_TEXT,
					'description' => $this->l('How many seconds can we wait for the result of the silent response'),
					'default' => 3
				),
				self::CNF_NO_TID_GENERATION => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Don\'t generate transaction ID'),
					'hint' => $this->l('ATOS API will be called without transaction ID, meaning that it will be set to HHMMSS according to server\'s time when calling ATOS API, which can cause a lot of problems (less transactions possible per days, possible collisions between clients, much less safe than segmenting available IDs between websites when using single certificate on multiple websites).'),
					'default' => FALSE
				),
				self::CNF_TID_TZ => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('transaction_id time zone'),
					'hint' => Tools::htmlentitiesDecodeUTF8(sprintf($this->l('Allows to sync transaction_id sequence resetting with SIPS servers midnight. Ask you SIPS support what you should select here. Unused if option "%s" is checked.'), $this->l('Don\'t generate transaction ID'))),
					'values' => CbAtosModuleFunctionCall::factory('getTimeZonesArraySelect'),
					'default' => FALSE,
				),
				self::CNF_MIN_TID => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Minimum transaction ID to use'),
					'hint' => Tools::htmlentitiesDecodeUTF8(sprintf($this->l('Between 1 and 999999. Unused if option "%s" is checked.'), $this->l('Don\'t generate transaction ID'))),
					'default' => 1
				),
				self::CNF_MAX_TID => array(
					'type' => self::T_ABS_POSITIVE_INT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Maximum transaction ID to use'),
					'hint' => Tools::htmlentitiesDecodeUTF8(sprintf($this->l('Between 1 and 999999. Unused if option "%s" is checked.'), $this->l('Don\'t generate transaction ID'))),
					'default' => 999999
				),
				self::CNF_OP_FIELD_TID => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('ATOS Response field to use as PrestaShop\'s transaction ID in order payment'),
					'values' => $this->_mirrorArray(array(
						self::ATOS_FIELD_TRANSACTION_ID,
						self::ATOS_FIELD_PAYMENT_CERTIFICATE,
						self::ATOS_FIELD_AUTHORISATION_ID
					)),
					'default' => self::ATOS_FIELD_TRANSACTION_ID
				),
				self::CNF_BINARIES_IN_PATH => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Call binaries without path'),
					'hint' => $this->l('Check it if (and ONLY if) the location of the ATOS binaries to use is a folder of the PATH system var.'),
					'default' => FALSE
				),
				self::CNF_BIN_PATH => array(
					'type' => self::T_PATH,
					'input' => self::IN_TEXT,
					'description' => $this->l('Location of ATOS binaries'),
					'hint' => $this->l('Unused if the option above is enabled. PHP user MUST be able to CD to this dir.'),
					'width' => '100%',
					'default' => $this->local_path . 'bin'.DIRECTORY_SEPARATOR
				),
				self::CNF_BIN_SUFFIX => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Optionnal suffix to append at the end of request and response binaries before calling them.'),
					'default' => ''
				),
				self::CNF_PARAM_PATH => array(
					'type' => self::T_PATH,
					'input' => self::IN_TEXT,
					'description' => $this->l('Location of ATOS configuration'),
					'hint' => $this->l('MUST be readable (and writable to allow module to update those files) by PHP user.'),
					'width' => '100%',
					'default' => $this->local_path . 'param'.DIRECTORY_SEPARATOR
				),
				self::CNF_RETURN_PROTOCOL_USER => array(
					'type' => self::T_STRING,
					'input' => self::IN_SELECT,
					'description' => $this->l('User return protocol'),
					'hint' => $this->l('Used to generate the user return URL transmitted to ATOS API. Automatic means HTTPS will be used when, and only when, PS_SSL_ENABLED configuration is ON.'),
					'values' => array(
						self::RETURN_PROTOCOL_AUTO => $this->l('automatic'),
						self::RETURN_PROTOCOL_HTTP => self::RETURN_PROTOCOL_HTTP,
						self::RETURN_PROTOCOL_HTTPS => self::RETURN_PROTOCOL_HTTPS
					),
					'default' => self::RETURN_PROTOCOL_AUTO
				),
				self::CNF_RETURN_DOMAIN_USER => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('User return front office domain'),
					'hint' => $this->l('Used to generate the user return URL transmitted to ATOS API. Leave empty to use Prestashop\'s Shop domain.'),
					'width' => '100%',
					'default' => CbATos::RETURN_DOMAIN_AUTO
				),
				self::CNF_RETURN_DOMAIN_SILENT => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Silent return front office domain'),
					'hint' => $this->l('Used to generate the silent return URL transmitted to ATOS API. Leave empty to use Prestashop\'s Shop domain.'),
					'width' => '100%',
					'default' => CbATos::RETURN_DOMAIN_AUTO
				),
				self::CNF_DEBUG_MODE => array(
					'type' => self::T_BOOL,
					'input' => self::IN_CHECKBOX,
					'description' => $this->l('Debug mode'),
					'hint' => $this->l('Prints debug outputs alongside with payment redirection form. To allow internal module exceptions to be displayed too, set _PS_MODE_DEV_ Prestashop constant to TRUE in prestashop/config/defines.inc.php. Also displays a textarea to allow testing overrides.'),
					'default' => FALSE
				),
				self::CNF_DEBUG_GID => array(
					'type' => self::T_INT,
					'input' => self::IN_SELECT,
					'description' => $this->l('PrestaShop debug group'),
					'hint' => $this->l('Forces Debug mode for PrestaShop users which belong to this group. Does not allow to obtain debug output from SIPS API.'),
					'default' => 0,
					'values' => CbAtosModuleFunctionCall::factory('getUserGroupArraySelect')
				),
/*				self::CNF_DATA_CONTROLS => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXTAREA,
					'description' => $this->l('Allows to configure controls on payments, please ask your SIPS provider for the appropriate documentation'),
					'hint' => 'Content will be wrapped into <CONTROLS> tags, do not input these tags! Line ending will be stripped off, so you can use them for a better readability.',
					'width' => '100%',
					'default' => ''
				),
				self::CNF_CUSTOM_DATA => array(
					'type' => self::T_STRING,
					'input' => self::IN_TEXT,
					'description' => $this->l('Allows to append custom parameters to the SIPS data field'),
					'width' => '100%',
					'default' => ''
				) */
			),
			'3D SECURE' => array(
				self::CNF_3DS_BYPASS_UNDER_AMOUNT => array(
					'type' => self::T_INT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Bypass 3DS check if cart amount is lower than this amount (expressed in default currency).'),
					'default' => 0
				),
				self::CNF_3DS_BYPASS_IF_VALIDATED_ORDER_OVER_AGE => array(
					'type' => self::T_INT,
					'input' => self::IN_TEXT,
					'description' => $this->l('Bypass 3DS check if customer has a validated order having at least this age (in days).'),
					'hint' => $this->l('Enter zero if you do not wish to bypass 3DS check based on existence of such order.'),
					'default' => 0
				)
			),
		);
		$this->_confVarsByName = array();
		foreach ($this->_confVars as $section)
			$this->_confVarsByName += $section;
	}

	public function getContent()
	{
	    $this->autoCheck();
		$this->initConfVars();
		foreach ($this->_confVars as $sectionName => $sectionVars)
			if (( $sectionName != 'INTERNAL' ) && Tools::isSubmit('btnSubmit'.$sectionName))
		{
			foreach ($sectionVars as $varname => $declaration)
			{
				if ($declaration['type'] == self::T_NONE) continue;
				if ($declaration['input'] == self::IN_INTERNAL) continue;
				if ($declaration['input'] == self::IN_NONE) continue;
				$this->set($varname, Tools::getValue('cbatos_'.$varname));
			}
			$this->updateAtosParamFiles();
		}
		$errorsIndex = $this->autoCheck();
		$html = '
		<h2>'.$this->displayName.'</h2>
		<dl>
			<dt>'.$this->l('Version').'</dt>
			<dd>'.$this->version.'</dd>
			<dt>'.$this->l('Author').'</dt>
			<dd>'.$this->author.'</dd>
			<legend>Votre Installation facile</legend>
			'.$this->l('1/ Munissez vous de votre certificat de production (envoy?? par la banque ou a t??lecharger) Si vous t??l??chargez, utilisez le format classique et non pas php ou jsp.').' <br />
			'.$this->l('1/ Chargez le certificat dans le dossier param du module (format certif.fr.xxxxxxxxx ou xxx est votre identifiant marchand ou num??ro de siret').'<br />
			'.$this->l('3/ Renommez ce fichier si n??cesaire dans la forme identique aux fichiers de demonstration.').'<br />
			'.$this->l('4/ Passez tout le module en chmod 755 r??cursif par FTP et verifiez').'<br />
			'.$this->l('5/ Eliminez tous les messages ci dessous sauf le test si vous testez le module.').'<br />
			'.$this->l('SI TOUTES CES INSTRUCTIONS NE SUFFISENT PAS POUR FAIRE FONCTIONNER LE MODULE').'<br />
			'.$this->l('1a/Verifiez que la fonction exec() est bien autoris??e sur votre serveur ou demandez ?? votre h??bergeur.').' <br />
			'.$this->l('2a/Regardez si il n y a pas un dossier cgi-bin a la racine de votre h??bergement.').' <br />
			'.$this->l('3a/Si ce dossier existe,il faut y placer les fichiers binaires du dossier bin et modifier le chemin de ces fichiers dans le module dans Emplacement des fichiers binaires.').' <br />
			'.$this->l('----------------------').'<br />
			'.$this->l('REQUIS POUR FONCTIONNER : La fonction exec() , php version 5.3 ou sup??rieur, Prestahop 1.7.0 ou sup??rieur, php safe_mode off').'<br />
			'.$this->l('Si un probleme persiste contactez nous en pr??cisant le message erreur indiqu??.').'<br />
			'.$this->l('Lisez bien la documentation ATOS envoy??e par votre banque.Vous devez effectuer un test avec votre certificat avant de demander la mise en production en remplissant le pv de recette.').'<br />
			'.$this->l('Support installation').' <a href="mailto:contact@cs-internet-creations.com">CS-Internet-Creations</a> '.$this->l('ou utilisez').' <a href="https://www.cs-internet-creations.com/fr/contactez-nous">'.$this->l('contact form').'</a>.
			<legend>Erreurs du module</legend>
		</dl>
		<style type="text/css" media="all">
			#cbatoscontent input[type="text"],
			#cbatoscontent select { display: block; box-sizing: border-box; }
			#cbatoscontent .atosref { display: inline-block; padding: 0.15em 0.4em 0.15em 0.15em; margin: 0.3em 0.15em; border: 1px dashed #034d93; }
			#cbatoscontent th,
			#cbatoscontent td { padding: 0.8em; border-bottom: 1px solid #ccc; }
			#cbatoscontent th.comment { border-bottom: 1px solid #666; }
			.ui-tooltip { text-align: left; }
			#cbatosconfigtabs.cbatos-fake-ui li { display: inline-block; padding: 2px 4px;}
			#cbatosconfigtabs.cbatos-fake-ui li a { text-decoration: underline; }
			#cbatosconfigtabs.cbatos-fake-ui li.active { font-weight: bold; }
		</style>
		<script type="text/javascript">
		
		</script>
		<div id="cbatoscontent">
		';
		if (!empty($this->_errors))
		{
			$html .= '
			<ol class="errors">
			';
			foreach ($this->_errors as $error)
			{
				$html .= '
				<li class="error">'.nl2br(Tools::htmlentitiesUTF8($error)).'</li>
				';
			}
			$html .= '
			</ol>
			';
		}
		$errorLog = $this->get(self::CNF_LOG_PATH) . self::FILE_ERROR_LOG;
		if (file_exists($errorLog))
		{
			$html .= '
                <h4>' . $this->l('Error log') . ' <small>' . Tools::htmlentitiesUTF8($errorLog) . '</small></h4>
			    <p style="white-space: pre-wrap; border: 1px solid red; padding: 1em;">' . preg_replace('/^(\|\+&gt; [0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}:)/m', '<br /><strong style="font-size: larger;">$1</strong>', Tools::htmlentitiesUTF8(Tools::file_get_contents($errorLog))) . '</p>';
		}
		$html .= '<p>'.$this->l('Many options have additionnal information displayed by hovering corresponding input field with your mouse cursor.').'</p>';
		foreach (array_keys($this->_confVars) as $_index => $_name)
		{
			if ($_name == 'INTERNAL') continue;
            $html .= $this->generateConfigSectionForm($_name, $_index);
		}
		$html .= '</div>';
		return $html;
	}

    protected function generateConfigSectionForm($sectionName, $index)
    {
        $isCurrent = (int)Tools::getValue('cbatos_opennedPannel') === $index;
        $sectionId = 'section'.(int)$index;
        $sectionVars = $this->_confVars[$sectionName];
        $html = '
                <h3 class="mb-0">' . Tools::htmlentitiesUTF8($this->l($sectionName)) . '</h3>
                <form action="' . Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']) . '" method="post">
                    <fieldset>
                        <input type="hidden" name="cbatos_opennedPannel" value="' . (int)$index . '" />
                        <table border="5" cellpadding="0" cellspacing="0" id="form" style="width: 80%;color:blue;" class="cbatos">
';
        foreach ($sectionVars as $name => $declaration) {
            if ($declaration['input'] == self::IN_INTERNAL) continue;
            ob_start();
            $html .= $this->generateConfigFormField($name, $declaration);
            $ob_local = ob_get_clean();
            if (!empty($ob_local))
                $html .= '<tr><td colspan="2">' . $ob_local . '</td></tr>';
        }
        $html .= '
                            <tr><td colspan="2" align="center"><input class="button" name="btnSubmit' . $sectionName . '" value="' . $this->l('Update settings') . '" type="submit" /></td></tr>
                        </table>
                    </fieldset>
                </form>
';
		
		return $html;
	}

    protected function generateConfigFormField($name, $declaration)
    {
        if ($declaration['input'] == self::IN_NONE) {
            return '
            <tr>
                <th colspan="2" class="comment">
                    <p>' . Tools::htmlentitiesUTF8($declaration['text']) . '</p>
                </th>
            </tr>';
        } else {
            $html = '
            <tr>
                <th width="250" style="vertical-align: top; text-align: right; padding-right: 1em;">
                    <label for="cbatos_conffield_' . Tools::htmlentitiesUTF8($name) . '">' . preg_replace('/\([^)]+\)/', '<span style="font-size: smaller;">$0</span>', Tools::htmlentitiesUTF8($declaration['description'])) . '</label>
                </th>
                <td style="padding-bottom:15px;">';
            $styles = array();
            if (!empty($declaration['field_suffix']))
                array_push($styles, 'display: inline-block;');
            if (!empty($declaration['width']))
                array_push($styles, 'width: ' . $declaration['width'] . ';');
            $styles = implode(' ', $styles);
            switch ($declaration['input']) {
                case self::IN_NONE:
                    break;
                case self::IN_TEXT:
                    $html .= '<input type="text" name="cbatos_' . Tools::htmlentitiesUTF8($name) . '" id="cbatos_conffield_' . Tools::htmlentitiesUTF8($name) . '" value="' . Tools::htmlentitiesUTF8($this->get($name)) . '" style="' . $styles . '" />';
                    break;
                case self::IN_TEXTAREA:
                    $html .= '<textarea cols="80" rows="10" name="cbatos_' . Tools::htmlentitiesUTF8($name) . '" id="cbatos_conffield_' . Tools::htmlentitiesUTF8($name) . '" style="' . $styles . '">' . Tools::htmlentitiesUTF8($this->get($name)) . '</textarea>';
                    break;
                case self::IN_SELECT:
                    $_values = $declaration['values'];
                    if ($_values instanceof CbAtosModuleDynamicValue)
                        $_values = $_values->getValue($this);
                    $html .= '<select name="cbatos_' . Tools::htmlentitiesUTF8($name) . '" id="cbatos_conffield_' . Tools::htmlentitiesUTF8($name) . '" style="' . $styles . '">';
                    foreach ($_values as $value => $display) {
                        $html .= '<option value="' . Tools::htmlentitiesUTF8($value) . '" ' . ($this->get($name) == $value ? 'selected="selected"' : '') . '>' . Tools::htmlentitiesUTF8($display) . '</option>';
                    }
                    $html .= '</select>';
                    unset($_values);
                    break;
                case self::IN_CHECKBOX:
                    $html .= '
                        <input type="hidden" name="cbatos_' . Tools::htmlentitiesUTF8($name) . '" value="0" />
                        <input type="checkbox" name="cbatos_' . Tools::htmlentitiesUTF8($name) . '" id="cbatos_conffield_' . Tools::htmlentitiesUTF8($name) . '" value="1"' . ($this->get($name) ? ' checked="checked"' : '') . ' style="' . $styles . '" />';
                    break;
                default:
                    $html .= 'ERROR UNKNOWN INPUT TYPE';
            }
            if (!empty($declaration['field_suffix']))
                $html .= Tools::htmlentitiesUTF8($declaration['field_suffix']);
            $html .= '<br />';
            if (!empty($declaration['hint'])) {
                $html .= '<p class="hint">' . str_replace('|', '<br />', Tools::htmlentitiesUTF8($declaration['hint'])) . '</p>';
            }
            if (!empty($declaration['atos'])) {
                $html .= '<strong title="' . Tools::htmlentitiesUTF8($this->l('ATOS parameter, cf parameters glossary in ATOS doc.')) . '" class="atosref"><img src="' . Tools::htmlentitiesUTF8($this->_path) . 'views/img/atos_icon.gif" width="16" height="16" />&nbsp;<em>' . $declaration['atos'] . '</em></strong>';
            }
            if (!empty($declaration['pathfile'])) {
                $html .= '<strong title="' . Tools::htmlentitiesUTF8($this->l('ATOS pathfile parameter, cf ATOS programmer\'s guide.')) . '" class="atosref"><img src="' . Tools::htmlentitiesUTF8($this->_path) . 'views/img/atos_icon.gif" width="16" height="16" />&nbsp;pathfile: <em>' . $declaration['pathfile'] . '</em></strong>';
            }
            if (!empty($declaration['parmcom'])) {
                $html .= '<strong title="' . Tools::htmlentitiesUTF8($this->l('This value is written in parmcom file to following configuration entry')) . '" class="atosref"><img src="' . Tools::htmlentitiesUTF8($this->_path) . 'views/img/atos_icon.gif" width="16" height="16" />&nbsp;parmcom: <em>' . $declaration['parmcom'] . '</em></strong>';
            }
            $html .= '
                </td>
            </tr>
					';
        }
        return $html;
    }

    public function generatePathfileContent()
	{
		return array(
			'DEBUG' => $this->get(self::CNF_DEBUG_MODE) ? 'YES' : 'NO',
			'D_LOGO' => $this->get(self::CNF_CARD_IMG_PATH),
			'F_CERTIFICATE' => $this->get(self::CNF_PARAM_PATH) . self::CERTIF,
			'F_PARAM' => $this->get(self::CNF_PARAM_PATH) . self::PARMCOM,
			'F_DEFAULT' => $this->get(self::CNF_PARAM_PATH) . self::PARMCOM . '.' . $this->get(self::CNF_BANK)
		);
	}

	public function updateAtosParamFiles()
	{
		if (!$this->get(self::CNF_BANK))
			return;
		if ($this->get(self::CNF_PRODUCTION) && !$this->get(self::CNF_MERCHANT_ID))
			return;
		$this->initConfVars();
		$pathfile_content = $this->generatePathfileContent();
		foreach ($pathfile_content as $name => &$line)
			$line = $name . '!' . $line . '!';
		$pathfile = $this->get(self::CNF_PARAM_PATH) . self::PATHFILE;
		$parmcom_content = array();
		foreach ($this->_confVarsByName as $name => $declaration)
			if (!empty($declaration['parmcom']) && $this->get($name))
				$parmcom_content[] = $declaration['parmcom'] . '!' . $this->get($name) . '!';
		$parmcom = $this->get(self::CNF_PARAM_PATH) . self::PARMCOM . '.' . ( $this->get(self::CNF_PRODUCTION) ? $this->get(self::CNF_MERCHANT_ID) : $this->_demoCertificates[$this->get(self::CNF_BANK)] );
		try {
			foreach (array($pathfile => $pathfile_content, $parmcom => $parmcom_content) as $file => $content)
			{
				array_unshift($content, '# Generated by module ' . $this->name . ' ' . $this->version . ' on Prestashop ' . _PS_VERSION_);
				if (!file_put_contents($file, implode(PHP_EOL, $content).PHP_EOL))
				{
					throw new Exception(sprintf($this->l('Unable to write to file %s'), $file));
				}
			}
		}
		catch (Exception $e)
		{
			$this->_errors[] = $e->getMessage();
		}
	}

	public function logResponse(CbAtosModuleResponseObject $response)
	{
		if ($this->get(self::CNF_RESPONSE_LOG_TXT))
		{
			try 
			{
				$file = $this->get(self::CNF_LOG_PATH).date('Y-m-d').'.log';
				$handle = fopen($file, 'a');
				if (!is_resource($handle))
					throw new PrestaShopModuleException('Unable to open file in append mode: '.$file);
				fwrite($handle, '----- '.date('Y-m-d H:i:s').PHP_EOL);
				foreach (array_merge(CbAtosModuleResponseObject::$fields, CbAtosModuleResponseObject::$additionnalLoggableFields) as $name)
					fwrite($handle, $name . ': ' . $response->{$name} . PHP_EOL);
				fwrite($handle, PHP_EOL);
				fclose($handle);
			} catch (Exception $e) {
				$this->error(__LINE__, $e->getMessage(), 3, NULL, false);
			}
		}
		if ($this->get(self::CNF_RESPONSE_LOG_CSV))
		{
			try 
			{
				$file = $this->get(self::CNF_LOG_PATH).date('Y-m-d').'.csv';
				$new = !file_exists($file);
				$handle = fopen($file, 'a');
				if (!is_resource($handle))
					throw new PrestaShopModuleException('Unable to open file in append mode: '.$file);
				if ($new)
				{
					$fields = array_merge(CbAtosModuleResponseObject::$fields, CbAtosModuleResponseObject::$additionnalLoggableFields);
					array_unshift($fields, 'log_date');
					fputcsv($handle, $fields, ';', '"');
				}
				$fields = array(date('Y-m-d H:i:s'));
				foreach (array_merge(CbAtosModuleResponseObject::$fields, CbAtosModuleResponseObject::$additionnalLoggableFields) as $name)
					array_push($fields, (string)$response->{$name});
				fputcsv($handle, $fields, ';', '"');
				fclose($handle);
			} catch (Exception $e) {
				$this->error(__LINE__, $e->getMessage(), 3, NULL, false);
			}
		}
	}

	public function getDynamicValue(CbAtosModuleDynamicValue $dynamicDescriptor) 
	{
		if ($dynamicDescriptor instanceof CbAtosModuleProperty) {
			return $this->{$dynamicDescriptor->getPropertyName()};
		}
		if ($dynamicDescriptor instanceof CbAtosModuleFunctionCall) {
			return call_user_func_array(array($this, $dynamicDescriptor->getFunctionName()), $dynamicDescriptor->getParameters());
		}
		throw new PrestaShopModuleException('Unimplemented dynamic value descriptor '.(get_class($dynamicDescriptor)));
	}

	public function getMerchantIdList($prependEmptyLine = FALSE) 
	{
		$prefix = 'certif.fr.';
		$prefix_length = Tools::strlen($prefix);
		$path = $this->get(self::CNF_PARAM_PATH);
		if (!is_dir($path)) return FALSE;
		$oldPath = getcwd();
		chdir($path);
		$files = glob($prefix.str_repeat('?', 15));
		$codes = array();
		foreach ($files as $file)
		{
			$code = Tools::substr($file, $prefix_length);
			if (preg_match('/^[0-9]{15}$/', $code))
			{
				if (in_array($code, $this->_demoCertificates)) continue;
				$info = '';
				try
				{
					$cert = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					if (empty($cert)) throw new Exception('Unable to read file or file empty');
					$matches = null;
					while ($line = array_pop($cert))
					{
						if (preg_match('/^\+*(?P<info>[^+]+)\++$/', $line, $matches))
						{
							$info = $matches['info'];
							break;
						}
					}
					if (empty($info))
						throw new Exception('None found');
				}
				catch (Exception $e)
				{
					$info = $this->l('Error reading certificate information: '.$e->__toString());
				}
				$codes[$code] = $code.' ('.$info.')';
			}
		}
		chdir($oldPath);
		if ($prependEmptyLine)
			return array(is_bool($prependEmptyLine) ? '' : $prependEmptyLine) + $codes;
		return $codes;
	}

	public function autoCheck()
	{
		if (!ModuleManagerBuilder::getInstance()->build()->isInstalled($this->name)) return [];
		$this->initConfVars();
		$this->warning = array();
		$this->_errors = array();
		$errorsIndex = array('BASIC' => 0, 'SINGLE' => 0, '2TIMES' => 0, '3TIMES' => 0, 'GRAPHIC' => 0, 'ADVANCED' => 0, '3DS' => 0);
		if (version_compare($this->version, $this->get(self::CNF_VERSION), '>'))
			$this->postUpdate();
		$errorLogFile = $this->get(self::CNF_LOG_PATH) . self::FILE_ERROR_LOG;
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->_errors[] = sprintf('Your PHP version is %s, this module has been written for PHP 5.3 or higher.', PHP_VERSION);
		}
		if (file_exists($errorLogFile))
		{
			$this->_errors[] = sprintf(
				$this->l('An error log file exists, please read file `%s` and archive it to stop seeing this warning.'),
				$errorLogFile
			);
		}
		if (!$this->get(self::CNF_PRODUCTION))
		{
			$errorsIndex['BASIC']++;
			$this->_errors[] = $this->l('You are still in demonstration mode.');
		}
		if (!$this->get(self::CNF_BANK) || !array_key_exists($this->get(self::CNF_BANK), $this->_banks))
		{
			$errorsIndex['BASIC']++;
			$this->_errors[] = $this->l('No bank selected.');
		}
		if ($this->get(self::CNF_PRODUCTION) && !$this->get(self::CNF_MERCHANT_ID))
		{
			$errorsIndex['BASIC']++;
			$this->_errors[] = $this->l('No production certificate selected.');
		}
		if (!$this->get(self::CNF_NO_TID_GENERATION) && !$this->get(self::CNF_TID_TZ))
		{
			$errorsIndex['ADVANCED']++;
			$this->_errors[] = $this->l('Your transaction_id time zone is not set.');
		}
		if ($this->get(self::CNF_DEBUG_MODE))
		{
			$errorsIndex['ADVANCED']++;
			$this->_errors[] = $this->l('Debug mode is active.');
		}

		if (ini_get('safe_mode'))
		{
			$this->_errors[] = $this->l('Safe mode is activated, this module is not compatible with PHP safe_mode.');
		}
		if (!$this->get(self::CNF_BINARIES_IN_PATH))
		{
			if (file_exists($this->get(self::CNF_BIN_PATH)) && is_dir($this->get(self::CNF_BIN_PATH)))
			{
				$cwd = getcwd();
				if (!chdir($this->get(self::CNF_BIN_PATH)))
				{
					$errorsIndex['ADVANCED']++;
					$this->_errors[] = sprintf($this->l('Unable to set binaries path ( %s ) as current working directory. ATOS binary executable call will probably fail.'), $this->get(self::CNF_BIN_PATH));
				}
				chdir($cwd);
			}
			else
			{
				$errorsIndex['ADVANCED']++;
				$this->_errors[] = sprintf($this->l('Binaries path \'%s\' does not exist, or is not a directory, or rights on it are to low to see it from PHP user. ATOS binary executable call will probably fail.'), $this->get(self::CNF_BIN_PATH));
			}
		}
		foreach (array(self::BIN_REQUEST => array(), self::BIN_RESPONSE => array('message=012345')) as $bin => $args)
		{
			$systemCall = $this->rawCall($bin, $args, true);
			if ($systemCall->exit_code != 0)
			{
				$this->_errors[] = sprintf($this->l('Error when calling %s binary, system exit code: %u, text output: %s'), $bin, $systemCall->exit_code, implode(PHP_EOL, $systemCall->output));
			}
		}
		if (!( $this->get(self::CNF_SINGLE) || $this->get(self::CNF_2TPAYMENT) || $this->get(self::CNF_3TPAYMENT) ))
		{
			$this->_errors[] = $this->l('No payment mode enabled');
		}
		foreach ($this->generatePathfileContent() as $name => $value)
		{
			if (Tools::strlen($value) > self::PATHFILE_VARLENGTH)
			{
				$this->_errors[] = sprintf($this->l('Pathfile %1$s value is %4$u characters long. An undocumented limitation of ATOS SIPS pathfile reader seems to disallow pathfile values to be longer than %3$u characters. F_* values can be shortened by moving param directory upper on the file system and updating corresponding entry in advanced conf.'), $name, $value, self::PATHFILE_VARLENGTH, Tools::strlen($value));
			}
		}
		if ($this->get(self::CNF_LOG_PATH) == $this->_confVarsByName[self::CNF_LOG_PATH]['default'])
		{
			$this->_errors[] = $this->l('Logs location is the default location which should be moved for security reason. Put it outside of HTTP document root and any public access folder if you can. Make sure no one who shouldn\'t has access to it. Do not forget to update module\'s config with new location in basic panel.');
		}
		if ($this->get(self::CNF_PARAM_PATH) == $this->_confVarsByName[self::CNF_PARAM_PATH]['default'])
		{
			$this->_errors[] = $this->l('ATOS SIPS parameter files location the default location which should be moved for security reason. Put it outside of HTTP document root and any public access folder if you can. Make sure no one who shouldn\'t has access to it. Do not forget to update module\'s config with new location in advanced panel.');
		}
		foreach (array('request', 'get', 'post') as $method)
		{
			$suhosin_key = 'suhosin.'.$method.'.max_value_length';
			$suhosin_value = ini_get($suhosin_key);
			if ($suhosin_value && $suhosin_value < 2048)
				$this->_errors[] = sprintf($this->l('%1$s PHP suhosin configuration value is %2$s. A value below 2048 could lead to the unability to process ATOS SIPS response. To know the exact value you need please take contact with your ATOS SIPS tech support. http://www.hardened-php.net/suhosin/configuration.html#%1$s'), $suhosin_key, $suhosin_value);
		}
		switch (count($this->_errors))
		{
			case 0:
				break;
			case 1:
				$this->warning = $this->_errors[0];
				break;
			default:
				$this->warning = sprintf($this->l('%u warnings/errors, see module\'s configuration page for more information'), count($this->_errors));
		}
		return $errorsIndex;
	}

	public function paramsToArgs(array $params)
	{
		$args = array();
		foreach ($params as $name => $value)
			if (!empty($value) || $value === 0 || $value === '0')
				$args[] = $name.'='.$value;
		return $args;
	}

	public function rawCall($bin_name, array $args = array(), $redirect_stderr_to_stdout = false)
	{
		if (!empty($args))
		{
			$args = ' ' . implode(' ', array_map('escapeshellarg', $args));
		}
		else
		{
			$args = '';
		}
		if ($redirect_stderr_to_stdout) {
			$args .= ' '.'2>&1';
		}
		return new CbAtosModuleSystemCall(escapeshellcmd(( $this->get(self::CNF_BINARIES_IN_PATH) ? '' : $this->get(self::CNF_BIN_PATH) ) . $bin_name . $this->get(self::CNF_BIN_SUFFIX)) . $args);
	}

	public function postUpdate()
	{
		$current_version = $this->get(self::CNF_VERSION);
		if (!$current_version)
			$current_version = '0';
		$toUpdate = array();
		foreach ($this->_newConfVars as $version => $vars)
			if (version_compare($version, $current_version, '>'))
				$toUpdate = array_merge($toUpdate, $vars);
		if (version_compare('4.1.0', $current_version, '>')) {
			$this->installResponseLockTable();
		}
		if ($this->setDefaults($toUpdate))
			$this->set(self::CNF_VERSION, $this->version);
		$this->updateAtosParamFiles();
	}

	public function setDefaults($toUpdate = null)
	{
		$defaults = array();
		$this->initConfVars();
		foreach ($this->_confVars as $section)
			foreach ($section as $name => $declaration)
		{
			if (is_array($toUpdate) && !in_array($name, $toUpdate)) continue;
			if ($declaration['type'] == self::T_NONE) continue;
			$defaults[$name] = $declaration['default'];
			if ($defaults[$name] instanceof CbAtosModuleDynamicValue)
				$defaults[$name] = $defaults[$name]->getValue($this);
		}
		$retval = TRUE;
		foreach ($defaults as $k => $v) $retval = $this->set($k, $v) && $retval;
		return $retval;
	}

	private $_orderStatesArrayCache;

	public function getOrderStatesSelectArray($prependEmptyLine = FALSE)
	{
		if (empty($this->_orderStatesArrayCache))
		{
			$this->_orderStatesArrayCache = array();
			foreach (OrderState::getOrderStates($this->context->language->id) as $order_state)
			{
				$this->_orderStatesArrayCache[$order_state['id_order_state']] = $order_state['name'];
			}
		}
		if ($prependEmptyLine)
			return array(is_bool($prependEmptyLine) ? '' : $prependEmptyLine) + $this->_orderStatesArrayCache;
		return $this->_orderStatesArrayCache;
	}

	public function getTimeZonesArraySelect()
	{
		$array = DateTimeZone::listIdentifiers();
		array_unshift($array, '');
		return $this->_mirrorArray($array);
	}

    public function getUserGroupArraySelect()
	{
		$groups = Group::getGroups($this->context->language->id);
		$groups_select = array(0 => '');
		foreach ($groups as $group)
		{
			$groups_select[$group['id_group']] = $group['name'];
		}
		return $groups_select;
	}

	private function _mirrorArray(array $array)
	{
		$return = array();
		foreach ($array as $v)
			$return[$v] = $v;
		return $return;
	}

	protected function installResponseLockTable()
	{
		$DB = Db::getInstance(true);
		if (!$DB->execute('
						CREATE TABLE IF NOT EXISTS `' . $this->getTable(self::TABLE_RESPONSE_LOCK) . '` (
							`id_cart`	INT UNSIGNED		NOT NULL,
							`lock`		CHAR(23)			NOT NULL,
							PRIMARY KEY (`id_cart`)
						)
						;
				', false)
		) {
			throw new Exception(sprintf($this->l('Fatal error: Installation of the database table failed, error code: %u, error message: %s'), $DB->getNumberError(), $DB->getMsgError()));
		}
	}

	private function ___forceTranslationKeys()
	{
		$this->l('BASIC');
		$this->l('SINGLE');
		$this->l('2TIMES');
		$this->l('3TIMES');
		$this->l('GRAPHIC');
		$this->l('ADVANCED');
	}

	public function error($line, $message, $severity = 4, $object = NULL, $dieIfDevMode = true, $dieAnyway = false, $file = __FILE__)
	{
		$severity = (int)$severity;
		$error = $file.'('.$line.'): '.$message;
		$fullError = $error.(is_null($object) ? '' : PHP_EOL.'debug object: '.print_r($object, true));
		if (class_exists('Logger')) {
			try {
				$errorlog = $fullError;
				if (!is_null($object)) {
					$objectOutput = print_r($object, true);
					//var_dump($objectOutput);
					/*$loggerMessageDefinition = ObjectModel::getDefinition('Logger', 'message');
					if ($loggerMessageDefinition['validate'] === 'isMessage') {
						if (!Validate::isMessage($objectOutput)) {
							$errorlog = $error . PHP_EOL . 'debug object: ' . sprintf(
									'Can\'t dump debug object `%s` to Prestashop logger, please see `%s` in module\'s log directory.',
									is_object($object)
										? get_class($object)
										: gettype($object),
									self::FILE_ERROR_LOG
								);
						}
					}*/
				}
				//Logger::addLog($errorlog, $severity);
			} catch (Exception $exception) {}
		}
		$logpath = $this->get(self::CNF_LOG_PATH);

		if (empty($logpath)) {
			$logpath = $this->local_path.'log'.DIRECTORY_SEPARATOR;
		}
		$logfile = $logpath . self::FILE_ERROR_LOG;
		$logFileHandle = fopen($logfile, 'a');
		if (is_resource($logFileHandle))
		{
			fwrite($logFileHandle, '|+> '.date('Y-m-d H:i:s').' severity '.$severity);
			fwrite($logFileHandle, $fullError);
			fwrite($logFileHandle, PHP_EOL);
			fclose($logFileHandle);
		} else {
			trigger_error('CbAtos, severity '.$severity.', '.$fullError, E_USER_WARNING);
		}
		if (_PS_MODE_DEV_)
		{
			echo '<h2>'.nl2br(Tools::htmlentitiesUTF8($message), true).'</h2>';
			echo '<h3>'.$file.':'.$line.'</h3>';
			if (is_null($object) && $dieIfDevMode)
				exit;
			Tools::dieObject($object, $dieIfDevMode);
		}
		if ($dieAnyway)
			throw new PrestaShopModuleException($fullError);
	}
}

interface CbAtosModuleDynamicValue
{

	public function getValue(CbAtos $module);
}

abstract class CbAtosModuleInternalDynamicValueAbstract implements CbAtosModuleDynamicValue
{

	public function getValue(CbAtos $module)
	{
		return $module->getDynamicValue($this);
	}
}

class CbAtosModuleProperty extends CbAtosModuleInternalDynamicValueAbstract {

	private static $_instances = array();

	private $_name;

	public static function factory($name) {
		if (!isset(self::$_instances[$name]))
			self::$_instances[$name] = new self($name);
		return self::$_instances[$name];
	}

	public function __construct($name) {
		$this->_name = $name;
	}

	public function getPropertyName() {
		return $this->_name;
	}
}

class CbAtosModuleFunctionCall extends CbAtosModuleInternalDynamicValueAbstract
{

	private static $_instances = array();

	private $_funcName;

	private $_parameters;

	public static function factory($name, array $parameters = array()) {
		$cacheName = $name.'('.implode(',', $parameters).')';
		if (!isset(self::$_instances[$cacheName]))
			self::$_instances[$cacheName] = new self($name, $parameters);
		return self::$_instances[$cacheName];
	}

	public function __construct($funcName, array $parameters = array())
	{
		$this->_funcName = $funcName;
		$this->_parameters = $parameters;
	}

	public function getFunctionName()
	{
		return $this->_funcName;
	}

	public function getParameters()
	{
		return $this->_parameters;
	}
}

class CbAtosModuleSystemCall
{
	public $command;
	public $output;
	public $exit_code;
	public $last_line;

	public function __construct($command)
	{
		$this->command = $command;
		$this->last_line = exec($command, $this->output, $this->exit_code);
	}
}

class CbAtosModuleRequestOutputParser
{

	public $success;
	public $error;
	public $form;

	public function __construct(CbAtosModuleSystemCall $call)
	{
		$output = explode('!', trim($call->last_line, '!'));
		list($atosResultCode, $atosError) = $output;
		if (isset($output[2])) $this->form = $output[2];
		$this->success = $atosResultCode == 0;
		$this->error = $atosError;
	}
}

class CbAtosModuleResponseObject
{
	const CONSTRUCT_NEW = 1;
	const CONSTRUCT_HYDRATE = 2;

	const TYPE_USER = 'user';
	const TYPE_SILENT = 'silent';

	public $merchant_id;
	public $merchant_country;
	public $amount;
	public $transaction_id;
	public $payment_means;
	public $transmission_date;
	public $payment_time;
	public $payment_date;
	public $response_code;
	public $payment_certificate;
	public $authorisation_id;
	public $currency_code;
	public $card_number;
	public $cvv_flag;
	public $cvv_response_code;
	public $bank_response_code;
	public $complementary_code;
	public $complementary_info;
	public $return_context;
	public $caddie;
	public $receipt_complement;
	public $merchant_language;
	public $language;
	public $customer_id;
	public $order_id;
	public $customer_email;
	public $customer_ip_address;
	public $capture_day;
	public $capture_mode;
	public $data;

	public $original_message;
	public $caller_ip_address;
	public $response_type;

	public $dataFlags = array();
	public $dataVars = array();

	public static $fields = array(
		'merchant_id',
		'merchant_country',
		'amount',
		'transaction_id',
		'payment_means',
		'transmission_date',
		'payment_time',
		'payment_date',
		'response_code',
		'payment_certificate',
		'authorisation_id',
		'currency_code',
		'card_number',
		'cvv_flag',
		'cvv_response_code',
		'bank_response_code',
		'complementary_code',
		'complementary_info',
		'return_context',
		'caddie',
		'receipt_complement',
		'merchant_language',
		'language',
		'customer_id',
		'order_id',
		'customer_email',
		'customer_ip_address',
		'capture_day',
		'capture_mode',
		'data'
	);

	public static $shortResponseUnavailableFields = array(
		'caddie',
		'customer_email',
		'customer_id',
		'customer_ip_address',
		'merchant_language',
		'order_validity',
		'receipt_complement',
		'return_context',
		'transaction_condition'
	);

	public static $additionnalLoggableFields = array(
		'original_message',
		'caller_ip_address',
		'response_type'
	);

	public function __construct(array $responseFields, $originalMessage, $type, $mode = self::CONSTRUCT_NEW, CbAtos $module)
	{
		switch ($mode)
		{
			case self::CONSTRUCT_NEW:
				$this->original_message = $originalMessage;
				$this->response_type = $type;
				if (is_array($_SERVER) && isset($_SERVER['REMOTE_ADDR']))
					$this->caller_ip_address = $_SERVER['REMOTE_ADDR'];
				$fields = self::$fields;
				if (count($responseFields) < count($fields))
				{
					foreach (self::$shortResponseUnavailableFields as $field)
						array_splice($fields, array_search($field, $fields), 1);
					if (count($responseFields) != count($fields))
						$module->error(__LINE__, 'Fields count mismatch in uncyphered response', 4, array('received fields' => $responseFields, 'known fields' => self::$fields), true, true);
				}
				foreach ($fields as $pos => $name)
					if (isset($responseFields[$pos]))
						$this->{$name} = $responseFields[$pos];
				if (!empty($this->data))
				{
					foreach (explode(';', $this->data) as $dataPiece)
					{
						if (preg_match('/([^=]+)=(.*)/', $dataPiece, $matches))
						{
							$this->dataVars[$matches[1]] = $matches[2];
						} else {
							$this->dataFlags[$dataPiece] = TRUE;
						}
					}
				}
				break;
			case self::CONSTRUCT_HYDRATE:
				foreach ($responseFields as $name => $value)
					if (property_exists($this, $name))
						$this->{$name} = $value;
				break;
			default:
				throw new PrestaShopModuleException('Illegal argument $mode, must be one of self::CONSTRUCT_*');
		}
	}

	public function hasDataFlag($flagname)
	{
		return !empty($this->dataFlags[$flagname]);
	}

	public function hasDataVar($varname)
	{
		return isset($this->dataVars[$varname]);
	}

	public function getDataVar($varname)
	{
		if (!$this->hasDataVar($varname))
			return null;
		return $this->dataVars[$varname];
	}

	public static function hydrate($data, CbAtos $module)
	{
		return new self($data, null, null, self::CONSTRUCT_HYDRATE, $module);
	}
}

class CbAtosModuleResponseOutputParser
{

	public $success;
	public $error;

	public $response;

	public function __construct(CbAtosModuleSystemCall $call, $originalMessage, $type, CbAtos $module)
	{
		$output = explode('!', Tools::substr($call->last_line, 1));
		$this->success = array_shift($output) == 0;
		$this->error = array_shift($output);
		if ($this->success)
			$this->response = new CbAtosModuleResponseObject($output, $originalMessage, $type, CbAtosModuleResponseObject::CONSTRUCT_NEW, $module);
	}
}
