<?php

namespace Suvarivaza\JoomShopping\FreeKassa;

class CheckTransaction
{
    /**
     * Список доверенных IP адресов
     */
    public $trust_list_ip;

    /**
     * Заказ, и информация о нём
     */
    public $order;

    /**
     * Наименование лог файла
     */
    public $log_file;

    /**
     * Массив REQUEST
     */
    public $request;

    /**
     * Конвертация строки в массив
     */
    private function convertStringToArray($data)
    {
        return explode(',', str_replace(' ', '', $data));
    }

    /**
     * Проверка IP адреса с которого приходит запрос
     * вернет true в случае если IP с которого приходит запрос не доверенный
     * вернет false в случае если IP доверенный
     */
    private function isNotTrustIp()
    {
        $current_ip = '';

        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $current_ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $current_ip = $_SERVER['REMOTE_ADDR'];
        }

        return !in_array($current_ip, $this->convertStringToArray($this->trust_list_ip));
    }

    /**
     * Проверка доверенных IP, сравнение сигнатур, сравнение сумм
     */
    public function checkResponse($signature)
    {

        /**
         * Проверка доверенных адресов
         */
        if ($this->isNotTrustIp()) {
            throw new \Exception('Доступ имеют только доверенные IP адреса.');
        }


        /**
         * Сравнение сигнатур
         */
        if ($this->request['SIGN'] !== $signature) {
            throw new \Exception('Сигнатуры не совпадают.');
        }

        /**
         * Сравнение суммы оплаты и суммы заказа
         */
        if ((string) $this->request['AMOUNT'] !== (string) format_amount($this->order->order_total)) {
            throw new \Exception('Суммы заказа не совпадают.');
        }
    }

    /**
     * Запись данных в лог
     */
    public function logError($error)
    {
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . $this->log_file,
            PHP_EOL . date("[m/d/Y h:i:s a] ", time()) . $error . ' IP ' . $_SERVER['REMOTE_ADDR'],
            FILE_APPEND
        );
    }
}
