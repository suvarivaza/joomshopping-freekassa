<?php

namespace Suvarivaza\Joomshopping\FreeKassa;

class FreeKassaPayment extends \PaymentRoot
{

    private $config;

    function __construct()
    {
        $this->config = new Config();
        $this->config->host = \JUri::getInstance();
        $this->config->pm_method = 'pm_fk';
    }

    public function showPaymentForm($params, $pmconfigs)
    {
        render_template('next-form', null);
    }


    public function showAdminFormParams($params)
    {

        $form_fields = $this->config->getAdminFormFields();

        //???
//		foreach ($form_fields as $key => $value) {
//			if (!isset($params[$key])) $params[$key] = '';
//		}

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
        $check_transaction->request = \JFactory::getApplication()->input->getArray();
        $pid = $check_transaction->request['intid'];

        $this->log("\n-----------------------------------------------------\nПолучен запрос act = $act " . print_r($check_transaction->request, true));

        switch ($act) {
            case 'notify':
                $this->log('notify process..');

                $signature = generate_signature([
                    $pmconfigs['merchant_id'],
                    format_amount($order->order_total),
                    $pmconfigs['secret_word_2'],
                    $order->order_id
                ], ':');

                try {
                    $this->log('check transaction..');
                    $check_transaction->checkResponse($signature);
                    $this->log('Order is paid');
                    return array(1, '[FK] order is paid [order: ' . $order->order_id . ', pid: ' . $pid . ']'); //возвращаем код 1 - статус заказа изменится на оплачен
                } catch (\Exception $error) {
                    $this->log('ERROR! ' . $error->getMessage());
                    return array(0, '[FK] error [order: ' . $order->order_id . ', error: ' . $error->getMessage() . ']'); //возвращаем код 0 - ошибка
                }
            case 'return':
                $this->log('return process..');
                return array(9, ''); //здесь нельзя менять статус заказа! просто завершаем заказ и перенаправляем на страницу благоданости!
            case 'cancel':
                $this->log('cancel process..');
                return array(3, '[FK] cancel order [order: ' . $order->order_id . ', pid: ' . $pid . ']');
        }

        return array(0, "Payment error [order: " . $order->order_id . "]");

    }

    /**
     * start payment
     * @param $pmconfigs
     * @param $order
     */
    public function showEndForm($pmconfigs, $order)
    {
        $signature = generate_signature([
            $pmconfigs['merchant_id'],
            format_amount($order->order_total),
            $pmconfigs['secret_word_1'],
            $order->currency_code_iso,
            $order->order_id
        ], ':');

        $data = [];
        $data['m'] = $pmconfigs['merchant_id'];
        $data['oa'] = format_amount($order->order_total);
        $data['o'] = $order->order_id;
        $data['currency'] = $order->currency_code_iso;
        $data['lang'] = 'ru';
        $data['s'] = $signature;

        $url = $pmconfigs['merchant_url'];
        $url .= "?" . http_build_query($data);
        header("Location: $url");

//        render_template('payment-form', [
//            'url' => $pmconfigs['merchant_url'],
//            'merchant_id' => $pmconfigs['merchant_id'],
//            'order_amount' => format_amount($order->order_total),
//            'order_id' => $order->order_id,
//            'sign' => $signature,
//            'currency' => $order->currency_code_iso
//        ]);
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


    function nofityFinish($pmconfigs, $order, $rescode)
    {
        $this->log( __METHOD__ . " | " . 'nofityFinish');
        echo "YES";
    }

    function finish($pmconfigs, $order, $rescode, $act)
    {
        $this->log( __METHOD__ . " | " . 'finish');
    }

    /**
     * Запись данных в лог
     */
    public function log($msg)
    {
        if(function_exists('saveToLog')){
            saveToLog('freekassa.log', $msg);
        }
    }

}
