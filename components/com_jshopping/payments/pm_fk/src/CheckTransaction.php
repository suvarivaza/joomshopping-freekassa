<?php

namespace Suvarivaza\Joomshopping\FreeKassa;

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
     * Массив REQUEST
     */
    public $request;

    /**
     * IP с которого приходит запрос
     */
    private $current_ip = '';

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

        if(!$this->trust_list_ip) return false; //проверка не будет производится если список доверенных IP пуст

        //если сайт проксируется через Cloudflare то реальный IP сервера будет в $_SERVER["HTTP_CF_CONNECTING_IP"]
        //но так как этот заголовок легко подделать нужно проеверить что $_SERVER['REMOTE_ADDR'] действительно пренадлежит Cloudflare
        if (Cloudflare::isActive()) {
            $this->current_ip = Cloudflare::getRealIp();
        } else if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $this->current_ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $this->current_ip = $_SERVER['REMOTE_ADDR'];
        }

        return !in_array($this->current_ip, $this->convertStringToArray($this->trust_list_ip));
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
            throw new \Exception('Запрос пришел с недоверенного IP ' . $this->current_ip);
        }


        /**
         * Сравнение сигнатур
         */
        if ($this->request['SIGN'] !== $signature) {
            throw new \Exception('Сигнатуры не совпадают! Проверьте корректность настройки секретных слов!');
        }

        /**
         * Сравнение суммы оплаты и суммы заказа
         */
        if ((string)$this->request['AMOUNT'] !== (string)format_amount($this->order->order_total)) {
            throw new \Exception('Суммы заказа не совпадают!');
        }
    }


}
