<?php

namespace Suvarivaza\Joomshopping\FreeKassa;

class FreeKassaPayment extends \PaymentRoot {

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

        $this->log("\n-----------------------------------------------------\nПолучен запрос act = $act ". print_r($check_transaction->request, true));

        //обработка URL возврата в случае успеха
        //нужно просто вернуть на страницу благодарности о заказе
        //менять статус заказа здесь нельзя. статус заказа меняем при оповещении notify
        if ($act == 'return')
        {
            $this->log('Обработка URL возврата в случае успеха..');
            if($pmconfigs['transaction_end_status'] == $order->order_status) {
                $this->log('Заказ оплачен. Статус заказа уже был изменен на '. $pmconfigs['transaction_end_status']);
                return array(1, 'Заказ №' . $order->order_number . ' уже оплачен'); //order already paid
            }
            else {
                $this->log('Заказ еще не оплачен. Ожидаем уведомление о оплате счета..');
                return array(2, ''); //order not paid yet
            }
        }

        $signature = generate_signature([
            $pmconfigs['merchant_id'],
            format_amount($order->order_total),
            $pmconfigs['secret_word_2'],
            $order->order_id
        ], ':');

        //act = notify
        try {
            $this->log('Проверяем статус транзакции..');
            $check_transaction->checkResponse($signature);
            $this->log('Заказ успешно оплачен!');
            return array(1, 'Заказ №' . $order->order_number . ' успешно оплачен'); //возвращаем код 1 - заказ оплачен
        } catch (\Exception $error) {
            $this->log('ОШИБКА! '. $error->getMessage());
            return array(0, $error->getMessage()); //возвращаем код 0 - ошибка
        }


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


    function nofityFinish($pmconfigs, $order, $rescode){
        saveToLog("checkout.log", __METHOD__ . " | " . 'nofityFinish ');
        echo "YES";
    }

    function finish($pmconfigs, $order, $rescode, $act){
        saveToLog("checkout.log", __METHOD__ . " | " . 'finish ');
    }

    /**
     * Запись данных в лог
     */
    public function log($msg)
    {
        saveToLog('freekassa.log', $msg);
    }

}
