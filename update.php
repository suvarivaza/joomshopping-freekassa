<?php

defined('_JEXEC') or die;

$db = JFactory::getDbo();
$db->setQuery("SELECT payment_id FROM `#__jshopping_payment_method` WHERE payment_class='pm_fk'");
$payment_id = $db->loadResult();

//insert payment method if not exist
if(!$payment_id) {

    $query = $db->getQuery(true); // Create a new query object.

    $payment_params = 'transaction_end_status=6\r\ntransaction_pending_status=1\r\ntransaction_failed_status=3\r\n';

    $queryParams = array(
        "payment_code" => $db->quote('freekassa'),
        "payment_class" => $db->quote('pm_fk'),
        "scriptname" => $db->quote('pm_fk'),
        "payment_publish" => 1,
        "payment_ordering" => 3,
        "payment_params" => $db->quote($payment_params),
        "payment_type" => 2,
        "price" => 0.00,
        "price_type" => 1,
        "tax_id" => 1,
        "show_descr_in_email" => 0,
        "name_en-GB" => $db->quote('FreeKassa'),
        "name_de-DE" => $db->quote('FreeKassa'),
        "name_ru-RU" => $db->quote('FreeKassa'),
    );

    //add table payment method
    $query->insert($db->quoteName("#__jshopping_payment_method"))
        ->columns($db->quoteName(array_keys($queryParams)))
        ->values(implode(",", array_values($queryParams)));
    $db->setQuery($query)->execute();
}

//install addon
$addon = JTable::getInstance('addon', 'jshop');
$addon->loadAlias('freekassa');
$addon->name = 'FreeKassa';
$addon->version = '1.1.0';
$addon->uninstall = '/components/com_jshopping/payments/pm_fk/uninstall.php';
$addon->store();

$back = 'index.php?option=com_jshopping&controller=payments';
?>