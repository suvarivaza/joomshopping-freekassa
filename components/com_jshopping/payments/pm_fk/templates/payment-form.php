<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		<form method="GET" action="<?= $url ?>" id="paymentform" name="paymentform" method="get">
			<input type="hidden" name="m" value="<?= $merchant_id ?>">
			<input type="hidden" name="oa" value="<?= $order_amount ?>">
			<input type="hidden" name="o" value="<?= $order_id ?>">
			<input type="hidden" name="s" value="<?= $sign ?>">
			<input type="hidden" name="currency" value="<?= $currency ?>">
			<input type="hidden" name="lang" value="ru">
			<input type="submit" name="pay" value="Оплатить">
		</form>
		<br>
		<script type="text/javascript">document.getElementById('paymentform').submit();</script>
	</body>
</html>
