<?php

namespace Suvarivaza\JoomShopping\FreeKassa;

class FreeKassaPayment extends \PaymentRoot {

    private $config;

    private $order_status;

    function __construct()
    {
        $this->config = new Config();
        $this->config->host = \JUri::getInstance();
        $this->config->pm_method = 'pm_fk';

        $this->order_status = 2;
    }

    public function showPaymentForm($params, $pmconfigs)
    {
        render_template('next-form', null);
    }

    public function showAdminFormParams($params)
    {
        $form_fields = $this->config->getAdminFormFields();

		foreach ($form_fields as $key => $value) {
			if (!isset($params[$key])) {
				$params[$key] = '';
			}
		}

		render_template('admin-form', [
            'form_fields' => $form_fields,
            'params' => $params
        ]);
	}

    public function checkTransaction($pmconfigs, $order, $act)
    {
        $check_transaction = new CheckTransaction();
        $check_transaction->trust_list_ip = $pmconfigs['ip_filter'];
        $check_transaction->order = $order;
        $check_transaction->log_file = $pmconfigs['log_file'];
        $check_transaction->request = \JFactory::getApplication()->input->getArray();


        $signature = generate_signature([
            $pmconfigs['merchant_id'],
            format_amount($order->order_total),
            $pmconfigs['secret_word_2'],
            $order->order_id
        ], ':');


        try {
            $check_transaction->checkResponse($signature);
            $this->order_status = 1;
        } catch (\Exception $error) {
            $check_transaction->logError($error->getMessage());
            $this->order_status = 3;
        }

        return array($this->order_status, '');
    }

    public function showEndForm($pmconfigs, $order)
    {
        $signature = generate_signature([
            $pmconfigs['merchant_id'],
			format_amount($order->order_total),
            $pmconfigs['secret_word_1'],
            $order->currency_code_iso,
			$order->order_id
        ], ':');

        render_template('payment-form', [
            'url' => $pmconfigs['merchant_url'],
            'merchant_id' => $pmconfigs['merchant_id'],
            'order_amount' => format_amount($order->order_total),
            'order_id' => $order->order_id,
            'sign' => $signature,
            'currency' => $order->currency_code_iso
        ]);
    }

    public function getUrlParams($pmconfigs)
    {
        $params = array();
        $params['order_id'] = \JFactory::getApplication()->input->getInt('MERCHANT_ORDER_ID');
        $params['hash'] = '';
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = 1;

        return $params;
    }

}
