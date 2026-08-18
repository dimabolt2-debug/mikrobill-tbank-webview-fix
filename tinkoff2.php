<?php

	require('./template/functions.php');

	session_start();
	if (empty($_SESSION['auth'])) {
		header('Location: login.php', true, 303);
		exit();
	}

	function TinkoffInitError($logMessage, $httpStatus = 502)
	{
		error_log('[T-Bank Init] ' . $logMessage);
		http_response_code($httpStatus);
		echo '<!doctype html><html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
		echo '<link rel="stylesheet" type="text/css" href="./template/templates/sn/css/style.css"></head>';
		echo '<body><div style="max-width:520px;margin:40px auto;padding:20px">';
		echo 'Не удалось открыть форму оплаты. <a href="tinkoff.php">Вернуться и повторить</a>.';
		echo '</div></body></html>';
		exit();
	}

	$paysize = str_replace(',', '.', (string)($_REQUEST['paysize'] ?? ''));
	if (!is_numeric($paysize) || (float)$paysize <= 0) {
		TinkoffInitError('Invalid payment amount', 400);
	}
	$paysize = number_format((float)$paysize, 2, '.', '');
	$shortguid = (string)($_SESSION['shortguid'] ?? '');

	$Lng = 'ru';
	if ($GLOBALS['Language'] <> 'rus') {
		$Lng = 'en';
	}

	$terminalkey = (string)$GLOBALS['Tinkoff_TerminalID'];
	$password = (string)$GLOBALS['Tinkoff_Password'];
	$Tinkoff_VAT = 'none';
	$Tinkoff_SNO = 'osn';
	$Tinkoff_ServiceName = '';
	$TinkoffID = -1;
	$otherinfo = array();
	$pinfo = array('', '');
	$payerName = '';

	$login = mysql_real_escape_string($_SESSION['login']);
	$sql = "SELECT otherinfo, shortguid, pinfo, FIO FROM stat WHERE user_name = '$login';";
	$mysqlResult = mysql_query($sql, $mysql);

	if (mysql_num_rows($mysqlResult) > 0) {
		$row2 = mysql_fetch_array($mysqlResult);
		$otherinfo = explode('||', $row2[0]);
		$shortguid = $row2[1];
		$pinfo = array_pad(explode('||', $row2[2]), 2, '');
		$payerName = (string)$row2[3];
		mysql_free_result($mysqlResult);
	}

	$TinkoffID = (int)($otherinfo[122] ?? -1);
	if ($TinkoffID > -1) {
		$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$TinkoffID';";
		$mysqlResult = mysql_query($sql, $mysql);
		if (mysql_num_rows($mysqlResult) > 0) {
			$row = mysql_fetch_array($mysqlResult);
			$Params = GetParams($row[0]);
			$terminalkey = (string)($Params['TinkoffTerminalID'] ?? '');
			$password = (string)($Params['TinkoffPassword'] ?? '');
			if (!empty($Params['Tinkoff_VAT'])) {
				$Tinkoff_VAT = $Params['Tinkoff_VAT'];
			}
			if (!empty($Params['Tinkoff_SNO'])) {
				$Tinkoff_SNO = $Params['Tinkoff_SNO'];
			}
			if (!empty($Params['Tinkoff_ServiceName'])) {
				$Tinkoff_ServiceName = $Params['Tinkoff_ServiceName'];
			}
			mysql_free_result($mysqlResult);
		}
	} else {
		$sql = 'SELECT `params`, `id` FROM `payment_params` WHERE `type` = 20';
		$mysqlResult = mysql_query($sql, $mysql);
		if (mysql_num_rows($mysqlResult) > 0) {
			$row = mysql_fetch_array($mysqlResult);
			$Params = GetParams($row[0]);
			$TinkoffID = (int)$row[1];
			$terminalkey = (string)($Params['TinkoffTerminalID'] ?? '');
			$password = (string)($Params['TinkoffPassword'] ?? '');
			if (!empty($Params['Tinkoff_VAT'])) {
				$Tinkoff_VAT = $Params['Tinkoff_VAT'];
			}
			if (!empty($Params['Tinkoff_SNO'])) {
				$Tinkoff_SNO = $Params['Tinkoff_SNO'];
			}
			if (!empty($Params['Tinkoff_ServiceName'])) {
				$Tinkoff_ServiceName = $Params['Tinkoff_ServiceName'];
			}
			mysql_free_result($mysqlResult);
		}
	}

	if ($terminalkey === '' || $password === '') {
		TinkoffInitError('Terminal credentials are not configured', 500);
	}

	if ((float)$paysize < (float)$GLOBALS['Tinkoff_MinPlat']) {
		$paysize = number_format((float)$GLOBALS['Tinkoff_MinPlat'], 2, '.', '');
	}

	if ($Tinkoff_ServiceName === '') {
		$Tinkoff_ServiceName = 'Internet service - ' . $GLOBALS['PortalName'];
	}

	$order_id = 'MB_' . RandomString(6) . '_' . $shortguid;
	$amountKopecks = (int)round((float)$paysize * 100);
	$description = 'MB_' . $shortguid;

	$initRequest = array(
		'TerminalKey' => $terminalkey,
		'Amount' => $amountKopecks,
		'OrderId' => $order_id,
		'Description' => $description,
		'Language' => $Lng
	);

	$customerData = array();
	if ($pinfo[1] !== '') {
		$customerData['Email'] = $pinfo[1];
	}
	if ($pinfo[0] !== '') {
		$customerData['Phone'] = $pinfo[0];
	}
	if ($payerName !== '') {
		$customerData['Name'] = $payerName;
	}
	if (count($customerData) > 0) {
		$initRequest['DATA'] = $customerData;
	}

	if (((strlen($pinfo[1]) > 5) && strpos($pinfo[1], '@') > 0) || strlen($pinfo[0]) > 5) {
		$initRequest['Receipt'] = array(
			'Email' => $pinfo[1],
			'Phone' => $pinfo[0],
			'Taxation' => $Tinkoff_SNO,
			'Items' => array(array(
				'Name' => $Tinkoff_ServiceName,
				'Price' => $amountKopecks,
				'Quantity' => 1.00,
				'Amount' => $amountKopecks,
				'Tax' => $Tinkoff_VAT
			))
		);
	}

	$portalAddress = rtrim((string)$GLOBALS['PortalAddress'], '/') . '/';
	if (filter_var($portalAddress, FILTER_VALIDATE_URL) && stripos($portalAddress, 'https://') === 0) {
		$initRequest['NotificationURL'] = $portalAddress . 'payin/tinkoff/payin.php';
		$initRequest['SuccessURL'] = $portalAddress;
		$initRequest['FailURL'] = $portalAddress . 'tinkoff.php';
	}

	$tokenFields = array();
	foreach ($initRequest as $key => $value) {
		if (!is_array($value) && !is_object($value)) {
			$tokenFields[$key] = (string)$value;
		}
	}
	$tokenFields['Password'] = $password;
	ksort($tokenFields, SORT_STRING);
	$initRequest['Token'] = hash('sha256', implode('', array_values($tokenFields)));

	$requestJson = json_encode($initRequest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	if ($requestJson === false) {
		TinkoffInitError('Unable to encode Init request', 500);
	}
	$caFile = 'C:/Program Files/Apache/cert/russian-trusted-root-ca.pem';
	if (!is_readable($caFile)) {
		TinkoffInitError('T-Bank CA certificate is not installed', 500);
	}

	$curl = curl_init('https://securepay.tinkoff.ru/v2/Init');
	if ($curl === false) {
		TinkoffInitError('Unable to initialize cURL', 500);
	}
	curl_setopt_array($curl, array(
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_TIMEOUT => 25,
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_SSL_VERIFYHOST => 2,
		CURLOPT_CAINFO => $caFile,
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Accept: application/json',
			'Expect:',
			'User-Agent: MikroBILL-TBank-ServerInit/1.0'
		),
		CURLOPT_POSTFIELDS => $requestJson
	));

	$responseBody = curl_exec($curl);
	$curlError = curl_error($curl);
	$httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if ($responseBody === false) {
		TinkoffInitError('Transport error: ' . $curlError);
	}

	$initResponse = json_decode($responseBody, true);
	if (!is_array($initResponse)) {
		TinkoffInitError('Invalid JSON response, HTTP ' . $httpCode);
	}

	$isSuccess = ($initResponse['Success'] ?? false) === true || ($initResponse['Success'] ?? '') === 'true';
	$paymentURL = (string)($initResponse['PaymentURL'] ?? '');
	$paymentURLParts = parse_url($paymentURL);
	$paymentHost = is_array($paymentURLParts) ? (string)($paymentURLParts['host'] ?? '') : '';
	$isTrustedPaymentHost = preg_match('/(^|\\.)(tinkoff\\.ru|tbank\\.ru)$/i', $paymentHost) === 1;

	if ($httpCode < 200 || $httpCode >= 300 || !$isSuccess || !is_array($paymentURLParts) || ($paymentURLParts['scheme'] ?? '') !== 'https' || !$isTrustedPaymentHost) {
		$errorCode = (string)($initResponse['ErrorCode'] ?? 'unknown');
		$message = (string)($initResponse['Message'] ?? 'Init rejected');
		TinkoffInitError('HTTP ' . $httpCode . ', code ' . $errorCode . ', message ' . $message);
	}

	PaymntToLog($TinkoffID, $order_id);
	header('Location: ' . $paymentURL, true, 303);
	exit();
