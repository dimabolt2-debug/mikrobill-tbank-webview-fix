<?php
	
	require('./template/functions.php');
	
	session_start();
	
	echo '<div id="redirect">Redirecting...</div>';
	
	$strings=$GLOBALS['strings'];

	$paysize=str_replace(',','.',$_REQUEST['paysize']);
	$shortguid=$_SESSION['shortguid'];
	
	$Lng='ru';
	if ($GLOBALS['Language']<>'rus'){$Lng='en';}
		
	$terminalkey=$GLOBALS['Tinkoff_TerminalID'];
	$Tinkoff_VAT = 'none';
	$Tinkoff_SNO = 'osn';
	$Tinkoff_ServiceName = '';
	
	$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid, pinfo, FIO FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			
			if (mysql_num_rows($mysqlResult)>0){
				$row2 = mysql_fetch_array($mysqlResult);
				$otherinfo = explode("||",$row2[0]);
				$contract = $otherinfo[0];
				$shortguid=$row2[1];
				$pinfo = explode("||",$row2[2]);
				mysql_free_result($mysqlResult);
			}
	
		$TinkoffID = $otherinfo[122];

		if ((int)$TinkoffID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$TinkoffID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$terminalkey = $Params['TinkoffTerminalID'];
				if (isset($Params['Tinkoff_VAT'])){
					if (strlen($Params['Tinkoff_VAT'])>0){$Tinkoff_VAT = $Params['Tinkoff_VAT'];}
				}
				if (isset($Params['Tinkoff_SNO'])){
					if (strlen($Params['Tinkoff_SNO'])>0){$Tinkoff_SNO = $Params['Tinkoff_SNO'];}
				}
				if (isset($Params['Tinkoff_ServiceName'])){
					if (strlen($Params['Tinkoff_ServiceName'])>0){$Tinkoff_ServiceName = $Params['Tinkoff_ServiceName'];}
				}
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 20";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);
				$TinkoffID = $row[1];
				$terminalkey = $Params['TinkoffTerminalID'];
				if (isset($Params['Tinkoff_VAT'])){
					if (strlen($Params['Tinkoff_VAT'])>0){$Tinkoff_VAT = $Params['Tinkoff_VAT'];}
				}
				if (isset($Params['Tinkoff_SNO'])){
					if (strlen($Params['Tinkoff_SNO'])>0){$Tinkoff_SNO = $Params['Tinkoff_SNO'];}
				}
				if (isset($Params['Tinkoff_ServiceName'])){
					if (strlen($Params['Tinkoff_ServiceName'])>0){$Tinkoff_ServiceName = $Params['Tinkoff_ServiceName'];}
				}
			}
		}
	$order_id='MB_'.RandomString(6) . '_' . $shortguid;
	
	if ($paysize < $GLOBALS['Tinkoff_MinPlat']){
		$paysize = $GLOBALS['Tinkoff_MinPlat'];
	}
	
	if (strlen($Tinkoff_ServiceName)==0){
		$Tinkoff_ServiceName = "Internet service - ".$GLOBALS['PortalName'];
	}
	
	$Receipt = '';
	
	if (((strlen($pinfo[1])>5)&& strpos($pinfo[1],'@')>0)||(strlen($pinfo[0])>5)){
				
		$ReceiptArr=array("Email" => $pinfo[1],
						  "Phone" => $pinfo[0],
						  "Taxation" => $Tinkoff_SNO,
						  "Items" => array(array("Name" => $Tinkoff_ServiceName,
											     "Price" => ($paysize*100),
											     "Quantity" => 1.00,
											     "Amount" => ($paysize*100),
											     "Tax" => $Tinkoff_VAT))
						  );		
						  
		$Receipt = json_encode($ReceiptArr);
	}
?>
<html>
 <head>
  <meta charset="UTF-8">
  <style>.tinkoffPayRow{display:block;margin:1%;width:160px;}</style>
  <script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"></script>
  <link rel="stylesheet" type="text/css" href="./template/templates/sn/css/style.css">
 </head>
 <body style="background:transparent;margin: 0;">
 <form name="TinkoffPayForm" id="TinkoffPay" onsubmit="pay(this); return false;">
	<input class="tinkoffPayRow" type="hidden" name="amount" id="amount" required value="<?php echo $paysize; ?>">
	<input class="tinkoffPayRow" type="hidden" name="terminalkey" value="<?php echo $terminalkey; ?>">
	<input class="tinkoffPayRow" type="hidden" name="frame" value="true">
	<input class="tinkoffPayRow" type="hidden" name="language" value="<?php echo $Lng; ?>">
    <input class="tinkoffPayRow" type="hidden" placeholder="Номер заказа" name="order" value="<?php echo ($order_id); ?>">
    <input class="tinkoffPayRow" type="hidden" placeholder="Описание заказа" name="description" value="MB_<?php echo $shortguid; ?>">
    <input class="tinkoffPayRow" type="hidden" placeholder="ФИО плательщика" name="name" value="<?php echo ($row2[3]); ?>">
    <input class="tinkoffPayRow" type="hidden" placeholder="E-mail" name="email"  value="<?php echo $pinfo[1]; ?>">
    <input class="tinkoffPayRow" type="hidden" placeholder="Контактный телефон" name="phone" value="<?php echo ($pinfo[0]); ?>">
	<input class="tinkoffPayRow" type="hidden" name="receipt" id="receipt" value='<?php echo ($Receipt); ?>'>
  </form>
 </body>
</html>
<?php
	PaymntToLog($TinkoffID,$order_id);
?>
<script type="text/javascript">
	pay(document.getElementById('TinkoffPay'));
	document.getElementById('redirect').innerHTML='';
</script>