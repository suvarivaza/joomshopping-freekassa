<?php
defined('_JEXEC') or die('Restricted access');
$db = JFactory::getDbo();
$query = $db->getQuery(true); // Create a new query object.
$query->delete($db->quoteName("#__jshopping_payment_method"))->where(array($db->quoteName('payment_code') . ' = ' . $db->quote('pm_fk')));
$db->setQuery($query)->execute();

jimport('joomla.filesystem.folder');
JFolder::delete(JPATH_ROOT.'/components/com_jshopping/payments/pm_fk');
?>