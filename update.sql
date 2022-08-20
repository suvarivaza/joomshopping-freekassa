INSERT INTO `#__jshopping_payment_method`
(`name_en-GB`, `description_en-GB`, `name_de-DE`,
`description_de-DE`, `name_ru-RU`,  `payment_code`, `payment_class`, `scriptname`,
`payment_publish`, `payment_ordering`, `payment_params`,
`payment_type`, `price`, `price_type`, `tax_id`,
`show_descr_in_email`) VALUES
('FreeKassa', '', 'FreeKassa', '', 'FreeKassa', 'freekassa', 'pm_fk', 'pm_fk', 1, 3,
'transaction_end_status=6\r\ntransaction_pending_status=1\r\ntransaction_failed_status=3\r\n',
 2, 0.00, 1, 1, 0);
