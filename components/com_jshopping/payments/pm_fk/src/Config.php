<?php

namespace Suvarivaza\JoomShopping\FreeKassa;

class Config
{
    public $host;

    public $pm_method = 'pm_fk';

    /**
     * Поля формы администратора
     */
    public $admin_form_fields;


    function __construct()
    {
        $this->admin_form_fields = [
            'merchant_url' => [
                'name' => 'URL мерчанта',
                'description' => '',
                'value' => 'https://pay.freekassa.ru/',
                'type' => 'text',
                'size' => '70'
            ],
            'merchant_id' => [
                'name' => 'Идентификатор кассы',
                'description' => '',
                'value' => '',
                'type' => 'text',
                'size' => '70'
            ],
            'secret_word_1' => [
                'name' => 'Первое секретное слово',
                'description' => 'Перовое секретное слово в настройках кассы.',
                'value' => '',
                'type' => 'password',
                'size' => '70'
            ],
            'secret_word_2' => [
                'name' => 'Второе секретное слово',
                'description' => 'Второе секретное слово в настройках кассы.',
                'value' => '',
                'type' => 'password',
                'size' => '70'
            ],
            'log_file' => [
                'name' => 'Путь до лог файла',
                'description' => '',
                'value' => '/freekassa.log',
                'type' => 'text',
                'size' => '70'
            ],
            'ip_filter' => [
                'name' => 'Доверенные IP адреса',
                'description' => 'Список доверенных IP адресов.',
                'value' => '168.119.157.136, 168.119.60.227, 138.201.88.124, 178.154.197.79',
                'type' => 'text'
            ],
            'transaction_end_status' => [
                'name' => 'Статус заказа для успешных транзакций',
                'description' => 'merchant_url',
                'type' => 'select',
                'value' => $this->getStatusesOrder(),
                'size' => '70'
            ],
            'transaction_pending_status' => [
                'name' => 'Статус заказа для платежей в ожидании',
                'description' => '',
                'value' => $this->getStatusesOrder(),
                'type' => 'select',
                'size' => '70'
            ],
            'transaction_failed_status' => [
                'name' => 'Статус заказа для неудавшихся транзакций',
                'description' => '',
                'value' => $this->getStatusesOrder(),
                'type' => 'select',
                'size' => '70'
            ],
            'status_url' => [
                'name' => 'URL оповещения',
                'description' => '',
                'value' => $this->getConfigureURL('notify'),
                'type' => 'text',
                'readonly' => true,
                'size' => '70'
            ],
            'success_url' => [
                'name' => 'URL возврата в случае успеха',
                'description' => '',
                'value' => $this->getConfigureURL('success'),
                'type' => 'text',
                'readonly' => true,
                'size' => '70'
            ],
            'fail_url' => [
                'name' => 'URL возврата в случае неудачи',
                'description' => '',
                'value' => $this->getConfigureURL('fail'),
                'type' => 'text',
                'readonly' => true,
                'size' => '70'
            ]
        ];
    }

    private function getURI()
    {
        $uri = \JFactory::getURI();

        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        $port = $uri->getPort();

        if ($port and (string)$port !== '80') {
            return $scheme . '://' . $host . ':' . $port . '/';
        } else {
            return $scheme . '://' . $host . '/';
        }
    }

    public function getAdminFormFields()
    {
        return $this->admin_form_fields;
    }

    public function getStatusesOrder()
    {
        $order_model = \JModelLegacy::getInstance('orders', 'JshoppingModel');

        return !$order_model ? null : $order_model->getAllOrderStatus();
    }

    public function getConfigureURL($action)
    {
        return $this->getURI() . 'index.php?option=com_jshopping&controller=checkout&task=step7&act=' . $action . '&js_paymentclass=' . $this->pm_method;
    }
}
