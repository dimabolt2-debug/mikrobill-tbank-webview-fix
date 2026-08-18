<?php
if (file_exists("config.php")){
	include_once ("config.php");
	include_once ("smallfunc.php");	
} else {
	include_once ("../config.php");
	include_once ("../smallfunc.php");
}


if ($GLOBALS['Language']=='rus' || !strlen($GLOBALS['Language'])>0){
	if (file_exists("template/staticpages.php")){
		include_once ("template/staticpages.php");	
	} else {
		include_once ("../template/staticpages.php");
	}
} else {
	if (file_exists("template/staticpages_" . $GLOBALS['Language'] . ".php")){
		include_once ("template/staticpages_" . $GLOBALS['Language'] . ".php");
	} else {
		if (file_exists("../template/staticpages_eng.php")){
			include_once ("../template/staticpages_eng.php");
		} else{
			include_once ("template/staticpages_eng.php");
		}
	}
}


#=========== Функции общего назначения ==============
function MakeActivity($mysql)
{
	$sql = "INSERT INTO `refresh_db` VALUES (1);";
	mysql_query($sql,$mysql);
	
}

# Форматирование даты (в MikroBILL месяц выводится в числовом формате, а заказчику нужен в текстовом)
function dateformating($date) 
{
	$strings=$GLOBALS['strings'];
	$date = explode(' ',$date);
	$date2 = explode('-',$date[0]);
	$date = explode(':',$date[1]);
	$date = $date[0] . ':' . $date[1];
	
	if (date('Y')==$date2[0]){$date2[0]='';} else {$date2[0]=$date2[0].' ';}
	
	// 1 - Января 
	// 2 - Февраля 
	// 3 - Марта 
	// 4 - Апреля 
	// 5 - Мая 
	// 6 - Июня 
	// 7 - Июля 
	// 8 - Августа 
	// 9 - Сентября 
	// 10 - Октября 
	// 11 - Ноября 
	// 12 - Декабря 
	
	switch ($date2[1]) {
		case 1: $ret = $date2[2].' '.$strings[1].' '.$date2[0]. $date; return $ret;
		case 2: $ret = $date2[2].' '.$strings[2].' '.$date2[0]. $date; return $ret;
		case 3: $ret = $date2[2].' '.$strings[3].' '.$date2[0]. $date; return $ret;
		case 4: $ret = $date2[2].' '.$strings[4].' '.$date2[0]. $date; return $ret;
		case 5: $ret = $date2[2].' '.$strings[5].' '.$date2[0]. $date; return $ret;
		case 6: $ret = $date2[2].' '.$strings[6].' '.$date2[0]. $date; return $ret;
		case 7: $ret = $date2[2].' '.$strings[7].' '.$date2[0]. $date; return $ret;
		case 8: $ret = $date2[2].' '.$strings[8].' '.$date2[0]. $date; return $ret;
		case 9: $ret = $date2[2].' '.$strings[9].' '.$date2[0]. $date; return $ret;
		case 10: $ret = $date2[2].' '.$strings[10].' '.$date2[0]. $date; return $ret;
		case 11: $ret = $date2[2].' '.$strings[11].' '.$date2[0]. $date; return $ret;
		case 12: $ret = $date2[2].' '.$strings[12].' '.$date2[0]. $date; return $ret;
		}
	}

function parse_template($vars, $template, $strings = NULL, $NoReadTemplate = false, $ShowChatraButton = false) 
{
	if (!$strings){
		$strings=$GLOBALS['strings'];
	}
	
	$_SESSION['Language']=$GLOBALS['Language'];

	if (!isset($vars['FIO'])){$vars['FIO']="";}
	if (!isset($vars['user_name'])){$vars['user_name']="";}
	if (!isset($vars['LOGIN'])){$vars['LOGIN']="";}
	if (!isset($vars['page_title'])){$vars['page_title']=$GLOBALS['PortalName'];}
	if (!isset($vars['out'])){$vars['out']="";}
	if (!isset($vars['contract'])){$vars['contract']="";}
	if (!isset($vars['ballance'])){$vars['ballance']="";}
	if (!isset($vars['Y'])){$vars['Y']="";}
	if (!isset($vars['PortalAddress'])){$vars['PortalAddress']="";}
	if (!isset($vars['PortalName'])){$vars['PortalName']="";}	
	
	# Function body
	
	if (!$NoReadTemplate){
		
		# Чтение файла
		$template=str_replace(array('..',':','\\'),'',$template);
		$f_pointer	= fopen($template, 'r');
		$file		= fread($f_pointer, filesize($template));
		fclose($f_pointer);
	} else {$file=$template;}
		
		$file=str_replace('<?','',$file);
		
		$i=0;
		$i=strpos($file,'%STRINGS_',$i);
		while (($i < strlen($file)) && ($i==true)):
			
			$j=strpos($file,'%',$i+9);
			$NextDiv = 10;
			if ($j){
				$v1=trim(substr($file,$i+9,$j-$i-9));
				$v2=substr($file,$i,$j-$i+1);
				$args=strpos($v1,'~');
				if (strlen($args)>0){
					$args=explode('~',$v1);
					$v1=$args[0];
					$MyStr = $strings[(int)$v1];
					unset($args[0]);
					$MyStr = vsprintf($MyStr,$args);
					

				} else {
					$MyStr = $strings[(int)$v1];
				}
				$file = str_replace($v2, $MyStr, $file);
				$NextDiv = strlen($MyStr);
			}			
			$i +=$NextDiv;
			if ($i<strlen($file)){
				$i=strpos($file,'%STRINGS_',$i);
			}
		endwhile;	
		
		if ($i<strlen($file)){
			$i=strpos($file,'%REM ',$i);
		}
		while (($i < strlen($file)) && ($i==true)):
			
			$j=strpos($file,"\n",$i+1);
			if ($j){
				$file = str_replace(substr($file,$i,$j-$i), '', $file);
			}			
			$i +=1;
			$i=strpos($file,'%REM ',$i);
		endwhile;		
		
		
		//if (!isset($_SESSION['other_content'])){$_SESSION['other_content']='';}
		if (!isset($_SESSION['smotreshka_login'])){$_SESSION['smotreshka_login']='';}		
		if (!isset($_SESSION['smotreshka_pass'])){$_SESSION['smotreshka_pass']='';}
		if (!isset($_SESSION['iptvportal_login'])){$_SESSION['iptvportal_login']='';}		
		if (!isset($_SESSION['iptvportal_pass'])){$_SESSION['iptvportal_pass']='';}
		if (!isset($_SESSION['moovi_login'])){$_SESSION['moovi_login']='';}		
		if (!isset($_SESSION['moovi_pass'])){$_SESSION['moovi_pass']='';}
		if (!isset($_SESSION['prosto_devices'])){$_SESSION['prosto_devices']='';}	
		//if (!isset($_SESSION['prosto_login'])){$_SESSION['prosto_login']='';}		
		//if (!isset($_SESSION['prosto_pass'])){$_SESSION['prosto_pass']='';}
		if (!isset($_SESSION['megogo_login'])){$_SESSION['megogo_login']='';}		
		if (!isset($_SESSION['megogo_pass'])){$_SESSION['megogo_pass']='';}
		if (!isset($_SESSION['omegatv_code'])){$_SESSION['omegatv_code']='';}
		if (!isset($_SESSION['omegatv_watch'])){$_SESSION['omegatv_watch']='';}
		if (!isset($_SESSION['omegatv_id'])){$_SESSION['omegatv_id']='';}
		if (!isset($_SESSION['wink_login'])){$_SESSION['wink_login']='';}
		if (!isset($GLOBALS['st_v'])){$GLOBALS['st_v']='';}
		if (!isset($GLOBALS['st_n'])){$GLOBALS['st_n']='';}
		
		if (!isset($_SESSION['login'])){$_SESSION['login']='';}
		if (!isset($GLOBALS['address'])){$GLOBALS['address']='';}
		
		
		$vars['FIO']=str_replace('<?','',$vars['FIO']);
		$vars['page_title']=str_replace('<?','',$vars['page_title']);
		$vars['contract']=str_replace('<?','',$vars['contract']);
		$vars['ballance']=str_replace('<?','',$vars['ballance']);
		$vars['out']=str_replace('<?','',$vars['out']);
		
		$Login='';
		if (isset($_COOKIE['MikroBILL_Last_Login'])){
			$Login=$_COOKIE['MikroBILL_Last_Login'];
		}
		if (strlen($Login)<1){
			if (isset($_SESSION['login'])){
				$Login = $_SESSION['login'];
			}
		}
		
		
		
		$file		= str_replace('%FIO%', $vars['FIO'], $file); // Вывод ФИО клиента
		$file		= str_replace('%SMOTRESHKA_LOGIN%', str_replace('<?','',$_SESSION['smotreshka_login']), $file);
		$file		= str_replace('%SMOTRESHKA_PASS%', str_replace('<?','',$_SESSION['smotreshka_pass']), $file);
		$file		= str_replace('%IPTVPORTAL_LOGIN%', str_replace('<?','',$_SESSION['iptvportal_login']), $file);
		$file		= str_replace('%IPTVPORTAL_PASS%', str_replace('<?','',$_SESSION['iptvportal_pass']), $file);
		$file		= str_replace('%MOOVI_LOGIN%', str_replace('<?','',$_SESSION['moovi_login']), $file);
		$file		= str_replace('%MOOVI_PASS%', str_replace('<?','',$_SESSION['moovi_pass']), $file);
		$file		= str_replace('%PROSTO_DEVICES%', str_replace('<?','',$_SESSION['prosto_devices']), $file);
		//$file		= str_replace('%PROSTO_LOGIN%', str_replace('<?','',$_SESSION['prosto_login']), $file);
		//$file		= str_replace('%PROSTO_PASS%', str_replace('<?','',$_SESSION['prosto_pass']), $file);
		$file		= str_replace('%MEGOGO_LOGIN%', str_replace('<?','',$_SESSION['megogo_login']), $file);
		$file		= str_replace('%MEGOGO_PASS%', str_replace('<?','',$_SESSION['megogo_pass']), $file);
		$file		= str_replace('%OMEGATV_ID%',str_replace('<?','', $_SESSION['omegatv_id']), $file);
		$file		= str_replace('%OMEGATV_CODE%',str_replace('<?','', $_SESSION['omegatv_code']), $file);
		$file		= str_replace('%OMEGATV_WATCH%', str_replace('<?','',$_SESSION['omegatv_watch']), $file);
		$file		= str_replace('%WINK_LOGIN%', str_replace('<?','',$_SESSION['wink_login']), $file);
		if (isset($_SESSION['omegatv_playlist'])){
			$file		= str_replace('%OMEGATV_PLAYLIST%', str_replace('<?','',$_SESSION['omegatv_playlist']), $file);
		}
		//$file		= str_replace('%LOGIN%', str_replace('<?','',$_SESSION['login']), $file);
		$file		= str_replace('%PAGE_TITLE%', $vars['page_title'], $file); // Вывод заголовка страницы в тег <TITLE> и на саму страницу в <H1>
		$file		= str_replace('%CONTENTS%',$vars['out'], $file); // Вывод основного содержимого страницы
		$file		= str_replace('%BILL_NO%', $vars['contract'], $file); // Вывод номера счета в верхнем блоке
		$file		= str_replace('%BALANCE%', $vars['ballance'], $file); // Вывод Баланса клиента в верхнем блоке
		//$file		= str_replace('%YEAR%', date('Y'), $file); // Вывод даты в подвале страницы
		//$file		= str_replace('%MENU%', menuManager(), $file);
		$file		= str_replace('%ADDRESS%', str_replace('<?','',$GLOBALS['address']), $file);
		$file		= str_replace('%PORTAL_ADDRESS%',str_replace('<?','', $GLOBALS['PortalAddress']), $file);
		$file		= str_replace('%PORTAL_NAME%',str_replace('<?','',$GLOBALS['PortalName']), $file);
		$file		= str_replace('%ST_V%',str_replace('<?','',date("Y").' &#169; '.$GLOBALS['PortalName'].' | '.$strings[399].': '.$GLOBALS['st_v']), $file);
		$file		= str_replace('%ST_N%','?'.str_replace('<?','',$GLOBALS['st_n']), $file);
		$file		= str_replace('%LOGIN%', $Login, $file);
		$file		= str_replace('%SMS_PREFIX%', $GLOBALS['SMS_Tel_Prefix'], $file);
		//$file		= str_replace('%OTHER_CONTENTS%', $GLOBALS['other_content'], $file);
		
		
		
		foreach ($vars as $k => $v) {
			if ((!is_array ($k)) && (!is_array ($v))){
				$v=str_replace('<?','',$v);
				$file = str_replace('%'.$k.'%', $v, $file);
			}
		}	
		
		if ($ShowChatraButton){
			$file .= ShowChatra();
		}
		
		return $file;		
	}



function declension($count, $form1, $form2, $form3){
			$count = abs($count) % 100;
			$lcount = $count % 10;
			if ($count >= 11 && $count <= 19) return($form3);
			if ($lcount >= 2 && $lcount <= 4) return($form2);
			if ($lcount == 1) return($form1);
			return $form3;
		}

function Symbol($in) {
		$BalA = explode(' ', $in);
		if ($BalA[1] == 'Руб.') {return '<span class="rub">'.$BalA[0].'</span>';} else
		{
			return $BalA[0];
			//return $in;
		}
	}

function setPage() {
		$arr = array('one', 'two', 'three', 'four', 'six');
		$def = 'one';
		foreach ($arr as $val) 
			$res[$val] = 'hide';
		if(isset($_GET['page']) && in_array(addslashes($_GET['page']), $arr))
			$def = addslashes($_GET['page']);
		$res[$def] = 'show';
		return $res;
	}

function menuManager($crumbs=false,$crumbs_lnk=false)
{	
	$strings=$GLOBALS['strings'];
	
	// 489 - Домой
	
	if($crumbs){
		$result = array(
					'two' => '<li><a href="./index.php?page=two">'.$strings[407].'</a></li>',		//407 - Услуги и тарифы
					'three' => '<li><a href="./index.php?page=three">'.$strings[405].'</a></li>',	//405 - Настройки
					'four' => '<li><a href="./index.php?page=four">'.$strings[406].'</a></li>',	//406 - Платежи
					'five' => '<li><a href="./news.php">'.$strings[111].'</a></li>',			//111 - Новости
					'six' => '<li><a href="./index.php?page=six">'.$strings[413].'</a></li>'		//413 - Помощь
					);
		return '<div class="crumbs">
					<ul>
						<li><a href="index.php?page=index"><i class="fas fa-home"></i><p style="margin-left:15px;">'.$strings[489].'</p></a></li>
						'.$result[$crumbs]
						.(($crumbs_lnk)? '
						<li><a href="'.$crumbs_lnk[0].'">'.$crumbs_lnk[1].'</a></li>':'').'
					</ul>
				</div>';
	}
	
	if (!isset($GLOBALS['EnablePayECash'])){$GLOBALS['EnablePayECash']='';}
	if (!isset($GLOBALS['UseQiwiBox'])){$GLOBALS['UseQiwiBox']='';}
	if (!isset($GLOBALS['UseTV'])){$GLOBALS['UseTV']='';}
	if (!isset($GLOBALS['UseCustomServices2'])){$GLOBALS['UseCustomServices2']='';}
	if (!isset($GLOBALS['EnableOnpay'])){$GLOBALS['EnableOnpay']='';}
	if (!isset($GLOBALS['EnableModulbank'])){$GLOBALS['EnableModulbank']='';}
	if (!isset($GLOBALS['EnableYandexPay'])){$GLOBALS['EnableYandexPay']='';}
	if (!isset($GLOBALS['EnableTinkoff'])){$GLOBALS['EnableTinkoff']='';}
	if (!isset($GLOBALS['EnablePayplug'])){$GLOBALS['EnablePayplug']='';}
	if (!isset($GLOBALS['UserCanChangePassword'])){$GLOBALS['UserCanChangePassword']='';}
	if (!isset($GLOBALS['EnableROBOKASSA'])){$GLOBALS['EnableROBOKASSA']='';}
	if (!isset($GLOBALS['UseCryptoCloud'])){$GLOBALS['UseCryptoCloud']='';}
	if (!isset($GLOBALS['ShowNews'])){$GLOBALS['ShowNews']='';}
	if (!isset($GLOBALS['PortalAddress'])){$GLOBALS['PortalAddress']='';}

	$PaymentsCount = 0;
	$PaymentsLastURL = '';
	
	$ShowLiqPay=false;
	if ($GLOBALS['EnablePayCreditCards']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['LiqPay_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['LiqPay_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['LiqPay_allowed_objects']))>=1)) {
			$ShowLiqPay=true;
		}	
	}
	
	$ShowPayeer=false;
	if ($GLOBALS['EnablePayECash']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Payeer_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Payeer_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Payeer_allowed_objects']))>=1)) {
			$ShowPayeer=true;
		}	
	}
	
	
	$ShowGorod74=false;
	if ($GLOBALS['EnableGorod74']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Gorod74_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Gorod74_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Gorod74_allowed_objects']))>=1)) {
			$ShowGorod74=true;
		}	
	}
	
	$ShowVivaWallet=false;
	if ($GLOBALS['UseVivaWallet']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['VivaWallet_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['VivaWallet_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['VivaWallet_allowed_objects']))>=1)) {
			$ShowVivaWallet=true;
		}	
	}
	
	$ShowOnPay=false;
	if ($GLOBALS['EnableOnpay']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['OnPay_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['OnPay_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['OnPay_allowed_objects']))>=1)) {
			$ShowOnPay=true;
		}	
	}
	
	$ShowModulbank=false;
	if ($GLOBALS['EnableModulbank']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Modulbank_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Modulbank_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Modulbank_allowed_objects']))>=1)) {
			$ShowModulbank=true;
		}	
	}
	
	$ShowPSB=false;
	if ($GLOBALS['UsePSB']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PSB_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PSB_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['PSB_allowed_objects']))>=1)) {
			$ShowPSB=true;
		}	
	}
	
	$ShowAlphabank=false;
	if ($GLOBALS['UseAlphabank']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Alphabank_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Alphabank_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Alphabank_allowed_objects']))>=1)) {
			$ShowAlphabank=true;
		}	
	}
	
	
	
	$ShowYaMoney=false;
	if ($GLOBALS['EnableYandexPay']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Yandex_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Yandex_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Yandex_allowed_objects']))>=1)) {
			$ShowYaMoney=true;
		}	
	}
	$ShowSelfWork=false;
	if ($GLOBALS['UseSelfWork']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['SelfWork_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['SelfWork_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['SelfWork_allowed_objects']))>=1)) {
			$ShowSelfWork=true;
		}	
	}
	
	
	$ShowPayAnyway=false;
	if ($GLOBALS['UsePayAnyway']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayAnyway_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayAnyway_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['PayAnyway_allowed_objects']))>=1)) {
			$ShowPayAnyway=true;
		}	
	}
	
	
	$ShowPayKeeper=false;
	if ($GLOBALS['UsePayKeeper']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayKeeper_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayKeeper_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['PayKeeper_allowed_objects']))>=1)) {
			$ShowPayKeeper=true;
		}	
	}
	
	
	$ShowYaKassa=false;
	if ($GLOBALS['EnableYandexKassaPay']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['YandexKassa_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['YandexKassa_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['YandexKassa_allowed_objects']))>=1)) {
			$ShowYaKassa=true;
		}	
	}
	
	$ShowSberbank=false;
	if ($GLOBALS['EnableSberbank']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Sberbank_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Sberbank_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Sberbank_allowed_objects']))>=1)) {
			$ShowSberbank=true;
		}	
	}
	
	$ShowOSMP_SBRF=false;
	if ($GLOBALS['UseOSMP_SBRF']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['OSMP_SBRF_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['OSMP_SBRF_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['OSMP_SBRF_allowed_objects']))>=1)) {
					
			if ($GLOBALS['OSMP_SBRF_UseQR']=='1'){	
				$ShowOSMP_SBRF=true;
			}
		}	
	}
	
	$ShowClickUZ=false;
	if ($GLOBALS['EnableClick']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Click_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Click_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Click_allowed_objects']))>=1)) {
			$ShowClickUZ=true;
		}	
	}
	
	$ShowiPayUA=false;
	if ($GLOBALS['EnableiPay']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['iPay_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['iPay_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['iPay_allowed_objects']))>=1)) {
			$ShowiPayUA=true;
		}	
	}
	
	$ShowTinkoff=false;
	if ($GLOBALS['EnableTinkoff']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Tinkoff_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Tinkoff_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Tinkoff_allowed_objects']))>=1)) {
			$ShowTinkoff=true;
		}	
	}
	
	$ShowPayMe=false;
	if ($GLOBALS['EnablePayMe']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayMe_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayMe_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['PayMe_allowed_objects']))>=1)) {
			$ShowPayMe=true;
		}	
	}
	
	
	$ShowPayPlug=false;
	if ($GLOBALS['EnablePayplug']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayPlug_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayPlug_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['PayPlug_allowed_objects']))>=1)) {
			$ShowPayPlug=true;
		}	
	}
	
	
	$ShowROBOKASSA=false;
	if ($GLOBALS['EnableROBOKASSA']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['ROBOKASSA_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['ROBOKASSA_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['ROBOKASSA_allowed_objects']))>=1)) {
			$ShowROBOKASSA=true;
		}	
	}
	
	$ShowCryptoCloud=false;
	if ($GLOBALS['UseCryptoCloud']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['CryptoCloud_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['CryptoCloud_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['CryptoCloud_allowed_objects']))>=1)) {
			$ShowCryptoCloud=true;
		}	
	}
	
	$ShowOzon=false;
	if ($GLOBALS['UseOzon']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Ozon_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Ozon_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Ozon_allowed_objects']))>=1)) {
			$ShowOzon=true;
		}	
	}
	
	
	$ShowProdamus=false;
	if ($GLOBALS['UseProdamus']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Prodamus_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Prodamus_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['Prodamus_allowed_objects']))>=1)) {
			$ShowProdamus=true;
		}	
	}
	
	
	$ShowQiwiBox=false;
	if ($GLOBALS['UseQiwiBox']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['QiwiBox_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['QiwiBox_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['QiwiBox_allowed_objects']))>=1)) {
			$ShowQiwiBox=true;
		}	
	}
	
	$ShowPayHUB=false;
	if ($GLOBALS['EnablePayHUB']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayHUB_allowed_objects']))>=1) or
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayHUB_allowed_objects']))>=1) or
				(strlen(array_search(12211221122112, $GLOBALS['PayHUB_allowed_objects']))>=1)) {
			$ShowPayHUB=true;
		}	
	}
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
	
	
	$query = "SELECT otherinfo FROM stat WHERE shortguid='".mysql_real_escape_string($_SESSION['shortguid'])."';";
	
	$res = mysql_query($query,$GLOBALS['mysql']);
	$row=mysql_fetch_row($res);
	
	
	$otherinfo = explode("||", $row[0]);
	
	//539 - Доступ на короткое время
	$TemporaryAccessName = $strings[539];
	$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($otherinfo[172])."' and `object_type` = 3;";
	$res = mysql_query($sql,$GLOBALS ["mysql"]);
	
	$SvcName = '';
	if (mysql_num_rows($res)>0){
	
		$row = mysql_fetch_array($res);
		$JSON=json_decode($row[0],true);
		
		if (isset($JSON['NameLNG'])){
			$SvcName = htmlspecialchars($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']]);
		}
		
		if (strlen($SvcName)>0){
			$TemporaryAccessName = htmlspecialchars($SvcName);
		}
	}
	
	//13 - Сменить тарифный план
	$changetarif = ($GLOBALS['UserCanChangeTarif']=='True')?'<a href="./changetarif.php">'.$strings[13].'</a>':'';
	
	//14 - Платёж через «Payeer»
	$payecash = ($ShowPayeer)?'<a href="./payeer.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePayeerStdTitle']==1)?($strings[14]):($GLOBALS['PayeerTitle']))).'</a>':'';
	$GLOBALS['UsePayeer'] = (strlen($payecash)>0)?(1):(0);
	
	//14 - Платёж через «Payeer»
	$payecash2 = ($ShowGorod74)?'<a href="./gorod74.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseGorod74StdTitle']==1)?(str_replace('Payeer','Gorod 74',$strings[14])):($GLOBALS['Gorod74Title']))).'</a>':'';
	$GLOBALS['UseGorod74'] = (strlen($payecash2)>0)?(1):(0);
	
	
	//591 - Платёж через «Privat24»
	$Privat24cash = ($GLOBALS['Privat24InWEB'])?'<a href="./privat24.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePrivat24StdTitle']==1)?($strings[591]):($GLOBALS['Privat24Title']))).'</a>':'';
	$GLOBALS['UsePrivat24'] = (strlen($Privat24cash)>0)?(1):(0);
	
	//591 - Платёж через «Privat24»
	$EasyPay = (($GLOBALS['EasyPayInWEB'])&&($GLOBALS['EnableEasyPay']=='True'))?'<a href="./easypay.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseEasyPayStdTitle']==1)?(str_replace('Privat24','EasyPay',$strings[591])):($GLOBALS['EasyPayTitle']))).'</a>':'';
	$GLOBALS['UseEasyPay'] = (strlen($EasyPay)>0)?(1):(0);
	
	
	//15 - Платёж через «LiqPay»
	$liqpay = ($ShowLiqPay)?'<a href="./liqpay.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseLiqPayStdTitle']==1)?($strings[15]):($GLOBALS['LiqpayTitle']))).'</a>':'';
	$GLOBALS['UseLiqPay'] = (strlen($liqpay)>0)?(1):(0);
	
	//16 - Терминалы «Qiwi»
	$qiwibox = '';
	$qiwibox = ($ShowQiwiBox)?'<a href="./qiwibox.php">'.(htmlspecialchars(($GLOBALS['UseQiwiBoxStdTitle']==1)?($strings[16]):($GLOBALS['QiwiBoxTitle']))).'</a>':'';
	
	$OSMP_Qiwi = '';
	$OSMP_Qiwi = ($GLOBALS['UseOSMP_Qiwi']=='True')?'<a href="./osmp_qiwi.php">'.$strings[462].'</a>':'';
	//17 - Карты оплаты
	$cards = ($GLOBALS['EnablePayCards']=='True')?'<a href="./cards.php">'.$strings[17].'</a>':'';
	//18 - Перевести деньги
	$sendmoneys = ($GLOBALS['sendmoney']=='True')?'<a href="./sendmoney.php">'.$strings[18].'</a>':'';
	// 483 - Пополнить баланс телефона
	$sendmoneys .= ($GLOBALS['sendmoney2']=='True')?'<a href="./sendmoney2.php">'.$strings[483].'</a>':'';
	//19 - Телевидение - Смотрёшка
	//475 - Телевидение - Omega.TV
	
	if (strlen(strpos($GLOBALS['TV24h_URL'],'caspio'))>0){$strings[480]=str_replace('24h.TV','TV COM', $strings[480]);}
	
	
	$TVservices = ($GLOBALS['UseTV']=='True')?'<a href="./managetv.php">'.$strings[19].'</a>':'';
	$TVservices .= ($GLOBALS['UseOmegaTV']=='True')?'<a href="./managetv2.php">'.$strings[475].'</a>':'';
	$TVservices .= ($GLOBALS['Use24hTV']=='True')?'<a href="./managetv3.php">'.$strings[480].'</a>':'';
	$TVservices .= ($GLOBALS['UseTrinitY']=='True')?'<a href="./managetv4.php">'.$strings[481].'</a>':'';
	$TVservices .= ($GLOBALS['UseSweetTV']=='True')?'<a href="./managetv8.php">'.$strings[546].'</a>':'';
	$TVservices .= ($GLOBALS['UseProstoTV']=='True')?'<a href="./managetv5.php">'.$strings[521].'</a>':'';
	$TVservices .= ($GLOBALS['UseMegogoTV']=='True')?'<a href="./managetv6.php">'.$strings[522].'</a>':'';
	$TVservices .= ($GLOBALS['UseIPTVPORTAL']=='True')?'<a href="./managetv7.php">'.$strings[547].'</a>':'';
	$TVservices .= ($GLOBALS['UseWinkTV']=='True')?'<a href="./managetv10.php">'.str_replace('Omega.TV', 'Wink', $strings[475]).'</a>':'';
	$TVservices .= ($GLOBALS['UseTVIPmedia']=='True')?'<a href="./managetv11.php">'.str_replace('Omega.TV', 'TVIP media', $strings[475]).'</a>':'';
	$TVservices .= ($GLOBALS['UseTvime']=='True')?'<a href="./managetv12.php">'.str_replace('Omega.TV', 'Tvime', $strings[475]).'</a>':'';
	$TVservices .= ($GLOBALS['UseMoovi']=='True')?'<a href="./managetv13.php">'.str_replace('Omega.TV', 'Moovi', $strings[475]).'</a>':'';

	//20 - Услуги
	$services = ($GLOBALS['UseCustomServices2']=='True')?'<a href="./manageservices.php">'.$strings[20].'</a>':'';
	
	
	if (!isset($_SESSION['reserv_name'])){
		$_SESSION['reserv_name']=$strings[28];
	}
	
	if (!isset($_SESSION['turbo_name'])){
		$_SESSION['turbo_name']=$strings[21];
	}
	
	if (!isset($_SESSION['delayedfee_name'])){
		$_SESSION['delayedfee_name']=$strings[22];
	}
	
	
	//21 - Ускорение
	$useturbo = '';
	if (isset($_SESSION['canuseturbo'])){
		$useturbo = ($_SESSION['canuseturbo']=='True')?'<a href="./startturbo.php">'.$_SESSION['turbo_name'].'</a>':'';
	}
	
	$UseTemporaryAccess='';
	$UseTemporaryAccess = (($_SESSION['tmp_access_started']=='0')&&($_SESSION['show_tmp_access']=='1'))?'<a style="background: #81A36A;" href="./tmp_access.php">'.$TemporaryAccessName.'</a>':'';
	
	
	//22 - Обещанный платеж
	$usepromisepay = '';
	$usepromisepay2 = '';
	if (isset($_SESSION['canusepromisepay'])){
		$usepromisepay = ($_SESSION['canusepromisepay']=='True')?'<a href="./promisepay.php">'.$_SESSION['delayedfee_name'].'</a>':'';
		$usepromisepay2 = ($_SESSION['canusepromisepay']=='True')?'<a style="background: linear-gradient(to top left, #AD994F, #AF973D)" href="./promisepay.php">'.$_SESSION['delayedfee_name'].'</a>':'';
	}
	//23 - Платёж через «Onpay»
	$useonpay = ($ShowOnPay)?'<a href="./onpay.php">'.(htmlspecialchars(($GLOBALS['UseOnpayStdTitle']==1)?($strings[23]):($GLOBALS['OnpayTitle']))).'</a>':'';
	$GLOBALS['UseOnPay'] = (strlen($useonpay)>0)?(1):(0);
	
	
	
	//23 - Платёж через «Onpay»
	$useModulbank = ($ShowModulbank)?'<a href="./modulbank.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseModulbankStdTitle']==1)?($strings[23]):($GLOBALS['ModulbankTitle']))).'</a>':'';
	
	
	$usePSB = ($ShowPSB)?'<a href="./psbank.php">'.(htmlspecialchars(($GLOBALS['UsePSBStdTitle']==1)?(str_replace('Payplug','Промсвязьбанк',$strings[25])):($GLOBALS['PSBTitle']))).'</a>':'';
	$GLOBALS['UsePSBank'] = (strlen($usePSB)>0)?(1):(0);
	
	$useAlphabank = ($ShowAlphabank)?'<a href="./alphabank.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseAlphabankStdTitle']==1)?(str_replace('Payplug','Альфабанк',$strings[25])):($GLOBALS['AlphabankTitle']))).'</a>':'';
	$GLOBALS['UseAlfabank'] = (strlen($useAlphabank)>0)?(1):(0);
	
	//24 - Платёж через «Yandex»
	$useya = ($ShowYaMoney)?'<a href="./yamoney.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseYandexStdTitle']==1)?($strings[24]):($GLOBALS['YandexTitle']))).'</a>':'';
	$GLOBALS['UseYaMoney'] = (strlen($useya)>0)?(1):(0);
	
	$useSelfWork = ($ShowSelfWork)?'<a href="./selfwork.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseSelfWork_StdTitle']==1)?(str_replace('Payplug','SelfWork.ru',$strings[25])):($GLOBALS['SelfWorkTitle']))).'</a>':'';
	$GLOBALS['UseSelfWork'] = (strlen($useSelfWork)>0)?(1):(0);
	
	//25 - Платёж через «Payplug»
	$usePayKeeper = ($ShowPayKeeper)?'<a href="./paykeeper.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePayKeeperStdTitle']==1)?(str_replace('Payplug','PayKeeper',$strings[25])):($GLOBALS['PayKeeperTitle']))).'</a>':'';
	$GLOBALS['UsePayKeeper'] = (strlen($usePayKeeper)>0)?(1):(0);
	
	//25 - Платёж через «Payplug»
	$usePayAnyway = ($ShowPayAnyway)?'<a href="./payanyway.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePayAnywayStdTitle']==1)?(str_replace('Payplug','PayAnyway',$strings[25])):($GLOBALS['PayAnywayTitle']))).'</a>':'';
	$GLOBALS['UsePayAnyway'] = (strlen($usePayAnyway)>0)?(1):(0);
	
	//25 - Платёж через «Payplug»
	$useCryptoCloud = ($ShowCryptoCloud)?'<a href="./cryptocloud.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseCryptoCloudStdTitle']==1)?(str_replace('Payplug','Crypto Cloud',$strings[25])):($GLOBALS['CryptoCloudTitle']))).'</a>':'';
	$GLOBALS['UseCryptoCloud'] = (strlen($useCryptoCloud)>0)?(1):(0);
	
	//25 - Платёж через «Payplug»
	$useOzon = ($ShowOzon)?'<a href="./ozon.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseOzonStdTitle']==1)?(str_replace('Payplug','Ozon',$strings[25])):($GLOBALS['OzonTitle']))).'</a>':'';
	$GLOBALS['UseOzon'] = (strlen($useOzon)>0)?(1):(0);
	
	
	//511 - Платёж через VivaWallet
	$useviva = ($ShowVivaWallet)?'<a href="./vivawallet.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseVivaWalletStdTitle']==1)?($strings[511]):($GLOBALS['VivaWalletTitle']))).'</a>':'';
	$GLOBALS['UseVivaWallet'] = (strlen($useviva)>0)?(1):(0);
	
	//496 - Платёж через «Яндекс.Кассу»
	$useya_kassa = ($ShowYaKassa)?'<a href="./yakassa.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseYandexKassaStdTitle']==1)?($strings[496]):($GLOBALS['YandexKassaTitle']))).'</a>':'';
	$GLOBALS['UseYaKassa'] = (strlen($useya_kassa)>0)?(1):(0);
	
	//498 - Платёж через «Сбербанк»
	$use_sberbank = ($ShowSberbank)?'<a href="./sbrf.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseSberbankStdTitle']==1)?($strings[498]):($GLOBALS['SberbankTitle']))).'</a>':'';
	$GLOBALS['UseSBRF'] = (strlen($use_sberbank)>0)?(1):(0);
	
	//498 - Платёж через «Сбербанк»
	$use_OSMP_SBRF = ($ShowOSMP_SBRF)?'<a href="./osmp_sbrf.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseOSMP_SBRF_StdTitle']==1)?($strings[498].' QR'):($GLOBALS['OSMP_SBRF_Title']))).'</a>':'';
	$GLOBALS['UseOSMP_SBRF'] = (strlen($use_OSMP_SBRF)>0)?(1):(0);
	
	//579 - Платёж через «Click.uz»
	$use_click = ($ShowClickUZ)?'<a href="./clickuz.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseClickStdTitle']==1)?($strings[579]):($GLOBALS['ClickTitle']))).'</a>':'';
	$GLOBALS['UseClickUZ'] = (strlen($use_click)>0)?(1):(0);
	
	//581 - Платёж через «iPay.ua»
	$use_iPay = ($ShowiPayUA)?'<a href="./ipay.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseiPayStdTitle']==1)?($strings[581]):($GLOBALS['iPayTitle']))).'</a>':'';
	$GLOBALS['UseiPay'] = (strlen($use_iPay)>0)?(1):(0);
	
	//397 - Платёж через «Тинькофф»
	$Tinkoff = ($ShowTinkoff)?'<a href="./tinkoff.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseTinkoffStdTitle']==1)?($strings[397]):($GLOBALS['TinkoffTitle']))).'</a>':'';
	$GLOBALS['UseTinkoff'] = (strlen($Tinkoff)>0)?(1):(0);
	
	//587 - Платёж через Paycom.uz
	$PayMe = ($ShowPayMe)?'<a href="./payme.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePayMeStdTitle']==1)?($strings[587]):($GLOBALS['PayMeTitle']))).'</a>':'';
	$GLOBALS['UsePayMe'] = (strlen($PayMe)>0)?(1):(0);
	
	//528 - Платёж через Prodamus
	$Prodamus = ($ShowProdamus)?'<a href="./prodamus.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseProdamusStdTitle']==1)?($strings[528]):($GLOBALS['ProdamusTitle']))).'</a>':'';
	$GLOBALS['UseProdamus'] = (strlen($Prodamus)>0)?(1):(0);
	
	//25 - Платёж через «Payplug»
	$usepayplug = ($ShowPayPlug)?'<a href="./payplug.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePayPlugStdTitle']==1)?($strings[25]):($GLOBALS['PayPlugTitle']))).'</a>':'';
	$GLOBALS['UsePayPlug'] = (strlen($usepayplug)>0)?(1):(0);
	
	//872 - Платёж через «Payhub.com.ua»
	$ShowPayHUB = ($ShowPayHUB)?'<a href="./payhub.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UsePayHUBStdTitle']==1)?($strings[588]):($GLOBALS['PayHUBTitle']))).'</a>':'';
	$GLOBALS['UsePayHUB'] = (strlen($ShowPayHUB)>0)?(1):(0);
	
	//27 - Платёж через «Robokassa»
	$robokassa = ($ShowROBOKASSA)?'<a href="./robokassa.php" pay_btn>'.(htmlspecialchars(($GLOBALS['UseROBOKASSA_StdTitle']==1)?($strings[27]):($GLOBALS['ROBOKASSA_Title']))).'</a>':'';
	$GLOBALS['UseROBOKASSA'] = (strlen($robokassa)>0)?(1):(0);
	
	
	//26 - Смена пароля
	$PassChange=($GLOBALS['UserCanChangePassword']=='True')?'<a href="./changepass.php">'.$strings[26].'</a>':'';
	
	$CanReserving = '';
		
	//28 - Резервирование
	if (isset($_SESSION['can_reserving']) && isset($_SESSION['reserving']) && isset($_SESSION['reservingnext'])) {
		if (($_SESSION['can_reserving']=='1') or ($_SESSION['reserving']=='1') or ($_SESSION['reservingnext']=='1') or ($_SESSION['reservingnext']=='0')) {
			$CanReserving = '<a href="./reserving.php">'.$_SESSION['reserv_name'].'</a>';
		}
	}
	
	$UserCanChangeWEBlanguage='';
	// 384 - Выбрать язык
	$UserCanChangeWEBlanguage=($GLOBALS['UserCanChangeLanguage']=='True')?'<a href="./language.php">'.$strings[384].'</a>':'';
	
	
	# Читаем файл произвольного меню
	
	//$CustomMenuFile = "template/custom_menu.txt";
	$CustomMenu1 = "";
	$CustomMenu2 = "";
	$CustomMenu3 = "";
	$CustomMenu4 = "";
	

	
	$sql = "SELECT * FROM `custom_menu`;";
	$res = mysql_query($sql,$mysql);
	while($row = mysql_fetch_array($res)) {
		
		$URL = $row[2];
		$InNewWindow=$row[3];
		$InInterface=$row[4];
		
		if (substr($URL,0,4)=='DOC='){
			$URL='files/templates/print_template.php?template='.substr($URL,4).'&iamclient=1';
			$InInterface=0;
		}
		
		
		$WindowMode='';
		if ($InNewWindow=='1'){
			$WindowMode = "target='_blank'";
		}
		
		$InterfaceMode='';
		if ($InInterface=='1'){
			$InterfaceMode = "./showpersonalframe.php?page=";
		}
		
		switch ($row[0]) {
				case "0":
					$CustomMenu1.= "<a $WindowMode href='$InterfaceMode$URL'>".$row[1].'</a>';
					break;
				case "1":
					$CustomMenu2.= "<a $WindowMode href='$InterfaceMode$URL'>".$row[1].'</a>';
					break;
				case "2":
					$CustomMenu3.= "<a $WindowMode href='$InterfaceMode$URL'>".$row[1].'</a>';
					break;
				case "3":
					$CustomMenu4.= "<a $WindowMode href='$InterfaceMode$URL'>".$row[1].'</a>';
					break;
			}
	}
	
	
	//29 - Статистика платежей
	//30 - Изменить личные данные
	//31 - Часто Задаваемые Вопросы
	//32 - Контакты
	$result = array();
	$result['two'] = '
					'.$UseTemporaryAccess.'
					'.$changetarif.'
					'.$useturbo.'
					'.$usepromisepay.'
					'.$sendmoneys.'
					'.$TVservices.'
					'.$services.'
					'.$CanReserving.'
					'.$CustomMenu1;
					
					
					$BottomInformer = '';
					
					if (($_SESSION['CashFromUsersToMoneyAcc']=='1') or ($GLOBALS['CashFromUsersToMoneyAcc']=='1')){
						
						$PaymentsString = $qiwibox.'
										'.$OSMP_Qiwi.'
										'.$useya.'
										'.$useSelfWork.'
										'.$usePayAnyway.'
										'.$usePayKeeper.'
										'.$useviva.'
										'.$useya_kassa.'
										'.$Tinkoff.'
										'.$PayMe.'
										'.$Prodamus.'
										'.$use_sberbank.'
										'.$use_OSMP_SBRF.'
										'.$use_click.'
										'.$use_iPay.'
										'.$robokassa.'
										'.$useCryptoCloud.'
										'.$useOzon.'
										'.$liqpay.'
										'.$payecash.'
										'.$payecash2.'
										'.$Privat24cash.'
										'.$EasyPay.'
										'.$useonpay.'
										'.$useModulbank.'
										'.$usePSB.'
										'.$useAlphabank.'
										'.$usepayplug.'
										'.$ShowPayHUB;
										
										
					} else {
						$PaymentsString = '';
						//595 - Пополнение группового счёта через абонента запрещено в настройках!
						$BottomInformer = '<br><br><b><font color=darkorange>'.$strings[595].'</font><b>';
					}
					
					
	$result['four'] = $UseTemporaryAccess. 
					  $usepromisepay2.'
					<a href="./payments.php">'.$strings[29].'</a>
					'.$cards.'
					'.$PaymentsString.'
					'.$CustomMenu2.'
					'.$BottomInformer;
				
	
	//628 - Подключить Push уведомления
	
	$result['three'] = '
					'.$PassChange.'
					<a href="./changepi.php">'.$strings[30].'</a>
					<a href="./pwa_info.php" id="pwa_info" style="display:none;">'.$strings[628].'</a>
					'.$UserCanChangeWEBlanguage.'
					'.$CustomMenu3;
	$result['six'] = '
					<a href="./faq.php">'.$strings[31].'</a>
					<a href="./contacts.php">'.$strings[32].'</a>';
					
	if ($GLOBALS['UseTicketsForUsers'] == 1){
		$result['six'] .= '
					<a href="./help.php">'.$strings[181].'</a>';
	}
	
	$result['six'] .= $CustomMenu4;
	
	
	return $result;
}


# Получение основной информации о пользователе
function getuserinfo() 
{
	
	$strings=$GLOBALS['strings'];
	
	$query = "SELECT user_name, user_pswd, FIO, tarif, ballance, traffic, state, turbo, turboenabled, data1, data2, stopdate, turboisallowed, promisepay, promisepayenabled, curspd, state, otherinfo, tarif_guid, shortguid, tarifends, pinfo, nexttarif, daystopay, paysize, spdlim, isarchived, group_guid, can_reserving, reserving, client_name2, todaytraffic, usrip, reservingnext, contract, `paytime`, `shortguid2` FROM stat WHERE shortguid='".mysql_real_escape_string($_SESSION['shortguid'])."';";
	
		$result = mysql_query($query,$GLOBALS['mysql']);
		if (isset($_SESSION['auth'])){
			if ((mysql_num_rows($result)==0)&& ($_SESSION['auth'])) {
				
				//echo 'Free session #1'; exit();
				
				$_SESSION['login'] = '';
				$_SESSION['smotreshka_login'] = '';
				$_SESSION['smotreshka_pass'] = '';
				$_SESSION['wink_login'] = '';
				$_SESSION['prosto_devices'] = '';
				//$_SESSION['prosto_login'] = '';
				//$_SESSION['prosto_pass'] = '';
				$_SESSION['megogo_login'] = '';
				$_SESSION['megogo_pass'] = '';
				$_SESSION['password'] = '';
				$_SESSION['auth'] = false;
				$_SESSION['shortguid'] = 0;
				$_SESSION['shortguid2'] = 0;
				$_SESSION['guig'] = 0;
				$_SESSION['contract'] = '';
				$_COOKIE['MikroBILL_WEB_Language']=$GLOBALS['Language'];
				header('location: index.php');
			}
		}
		
		if (!isset($_COOKIE['MikroBILL_WEB_Language'])){
				$_COOKIE['MikroBILL_WEB_Language']=$GLOBALS['Language'];
		}
		
		if (mysql_num_rows($result)==0){
			return $result;
		}
		
		
		
		$result = mysql_fetch_assoc($result);
		# Получение информации о тарифах
		$res = mysql_query("SELECT * FROM tarifs WHERE tarif_guid='".mysql_real_escape_string($result['tarif_guid'])."'",$GLOBALS['mysql']);
		
		if (mysql_num_rows($res)>0){
			$result['tarifs'] = mysql_fetch_assoc($res); 
		}
		
		$res = mysql_query("SELECT * FROM groups WHERE group_guid='".mysql_real_escape_string($result['group_guid'])."'",$GLOBALS['mysql']);
		if (mysql_num_rows($res)>0){
			$result['groups'] = mysql_fetch_assoc($res); 
		}
			
		if (isset($result['tarifs'])){
			$result['pay_cost'] = $result['tarifs']['user_pay']; // Записываем размер абонентской платы в отдельный элемент массива, для удобства обращения.
			$result['promisepayaddmoney'] = $result['tarifs']['promisepay_addmoney']; // Записываем сумму обещанного платежа в отдельный элемент массива.
		}else {
			$result['pay_cost'] = 0;
			$result['promisepayaddmoney'] = 0;
		}
		
		$otherinfo = explode("||", $result['otherinfo']);
		
		$Mail = $otherinfo[4];
		$SmotreshkaLogin = $otherinfo[81];
		$SmotreshkaPass = $otherinfo[90];
		$wink_login = $otherinfo[162];
		$ProstoDevices = $otherinfo[116];
		$ProstoLogin = $otherinfo[112];
		$ProstoPass = $otherinfo[113];
		$MegogoLogin = $otherinfo[114];
		$MegogoPass = $otherinfo[115];
		$iptvportal_Login = $otherinfo[137];
		$iptvportal_Pass = $otherinfo[138];
		$moovi_Login = $otherinfo[176];
		$moovi_Pass = $otherinfo[177];
		
		$Page='';
		if (isset($_REQUEST["page"])){
			$Page=$_REQUEST["page"];
		}
		if ($GLOBALS['WhenPayStartFriendlyAccess']==1){
			if ($Page == 'four'){
				
				if (((int)$otherinfo[95]<>-1)&&($otherinfo[133]<>'1')){
					
					$GUID = uniqid();
					$sql = "INSERT INTO `actions` VALUES('FRIENDLY_ACCESS','".mysql_real_escape_string($_SESSION['login'])."','false','','$GUID');";
					mysql_query($sql, $GLOBALS ["mysql"]);
					
					$sql = "INSERT INTO `refresh_db` VALUES (1);";
					mysql_query($sql,$GLOBALS ["mysql"]);
				}
			}
		}
		
		
		if (strlen($otherinfo[108])>3){
			
			$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($otherinfo[108])."' and `object_type` = 3;";
			$res = mysql_query($sql,$GLOBALS ["mysql"]);
				
			if (mysql_num_rows($res)>0){
				
				$row = mysql_fetch_array($res);
				$JSON=json_decode($row[0],true);
				
				if (isset($JSON['NameLNG'])){
					$SvcName = htmlspecialchars($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']]);
				} else {
					$SvcName = '';
				}
				
				if (strlen($SvcName)>0){
					$strings[28] = htmlspecialchars($SvcName);
				}
			}
		}
		
		if (strlen($otherinfo[109])>3){
		
			$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($otherinfo[109])."' and `object_type` = 3;";
			$res = mysql_query($sql,$GLOBALS ["mysql"]);
				
			if (mysql_num_rows($res)>0){
				
				$row = mysql_fetch_array($res);
				$JSON=json_decode($row[0],true);
				
				if (isset($JSON['NameLNG'])){
					$SvcName = htmlspecialchars($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']]);
				} else {
					$SvcName = '';
				}
				
				if (strlen($SvcName)>0){
					$strings[21] = htmlspecialchars($SvcName);
				}
			}
		}
		
		if (strlen($otherinfo[110])>3){
			
			$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($otherinfo[110])."' and `object_type` = 3;";
			$res = mysql_query($sql,$GLOBALS ["mysql"]);
			
			if (mysql_num_rows($res)>0){
				
				$row = mysql_fetch_array($res);
				$JSON=json_decode($row[0],true);
				
				if (isset($JSON['NameLNG'])){
					$SvcName = htmlspecialchars($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']]);
				} else {
					$SvcName = '';
				}
				
				if (strlen($SvcName)>0){
					$strings[22] = htmlspecialchars($SvcName);
				}
			}
		}
		
			//echo $result['contract'];exit();
		$address = explode("||", $result['pinfo']);
		$address = $address[4];
	
		$result['address'] = $address;
		$result['mail'] = $Mail;
		$GLOBALS['address']= $address;
		
		if (isset($result['tarifs'])){
			$_SESSION['canuseturbo'] = $result['tarifs']['turbo_enabled'];
			$_SESSION['canusepromisepay'] = $result['tarifs']['promisepay_enabled'];
		}	
			
		$_SESSION['shortguid'] = $result['shortguid'];
		$_SESSION['shortguid2'] = $result['shortguid2'];
				
		$_SESSION['tarif_guid']=$result['tarif_guid'];
		$_SESSION['group_guid']=$result['group_guid'];
		$_SESSION['can_reserving']=$result['can_reserving'];
		$_SESSION['reservingnext']=$result['reservingnext'];
		$_SESSION['reserving']=$result['reserving'];
		$_SESSION['client']=$result['client_name2'];
		$_SESSION['smotreshka_login']=$SmotreshkaLogin;
		$_SESSION['smotreshka_pass']=$SmotreshkaPass;
		$_SESSION['wink_login']=$wink_login;
		$_SESSION['iptvportal_login']=$iptvportal_Login;
		$_SESSION['iptvportal_pass']=$iptvportal_Pass;
		$_SESSION['moovi_login']=$moovi_Login;
		$_SESSION['moovi_pass']=$moovi_Pass;
		$_SESSION['prosto_devices']=$ProstoDevices;
		//$_SESSION['prosto_login']=$ProstoLogin;
		//$_SESSION['prosto_pass']=$ProstoPass;
		$_SESSION['megogo_login']=$MegogoLogin;
		$_SESSION['megogo_pass']=$MegogoPass;
		$_SESSION['omegatv_id']=$otherinfo[97];
		$_SESSION['Language']=$GLOBALS['Language'];
		$_SESSION['contract']=$result['contract'];
		$_SESSION['omegatv_playlist']=$otherinfo[107];
		$_SESSION['CashFromUsersToMoneyAcc']=$otherinfo[163];
		$_SESSION['reserv_name']=$strings[28];
		$_SESSION['delayedfee_name']=$strings[22];
		$_SESSION['turbo_name']=$strings[21];
		
		$_SESSION['tmp_access_started']=$otherinfo[133];
		$_SESSION['show_tmp_access']=$otherinfo[134];
		
		
		return $result;
	}	


function CleanTicketFromInfo($info){
	$out='';
	$PosStart2=0;
	
	while (strlen(mb_strpos($info,'#Ticket_',$PosStart2))>0){
		
		$PosStart=mb_strpos($info,'#Ticket_',$PosStart2);
		
		if (strlen($out)>0){
			$out.='<br>';
		} elseif ($PosStart>0){
			$out.=mb_substr($info,0,$PosStart).'<br>';
		}
		
		
		$PosEnd=mb_strpos($info,'#',$PosStart+8);
		$PosStart2=mb_strpos($info,'#End_Ticket#',$PosEnd);
		$out .=mb_substr($info,$PosEnd+1,$PosStart2-$PosEnd-1);
	}
	
	if (strlen($out)==0){$out=$info;} else {$out .= '<br>'.mb_substr($info,$PosStart2+12);}
	
	return '<div>'.$out.'</div>';
}
	

# Вывод информации о пользователе
function show_base_info()
{	  	
	$strings=$GLOBALS['strings'];
	
	# Получаем информацию из базы данных
	$result = getuserinfo();
	
	
	if ($result['isarchived']==1 && $GLOBALS ['ArchiveWEBLogin'] <> 'True'){
		$_SESSION['auth']=false;
		Authentication();
		exit();
	}
	
	$OtherInfo = $result['otherinfo'];
	$OtherInfo = explode("||",$OtherInfo);
	
	if ($OtherInfo[131]=='1'){
		$_SESSION['auth']=false;
		Authentication();
		exit();	
	}
	
	if ($GLOBALS['HotSpotWebAuth'] == "True"){
			
				$sql = "SELECT `otherinfo`, `user_name`, `user_pswd` FROM `stat` WHERE `shortguid` = '".$_SESSION['guid']."';"; 
				
				$res = mysql_query($sql,$GLOBALS['mysql']);
				$row = mysql_fetch_array($res);
				$row2=explode('||',$row[0]);
				
				if (isset($_SERVER['HTTPS'])){$pref='https://';}else{$pref='http://';}
				
				if ($row2[5]=='HotSpot'){
					$ishotspot=true;
					echo '<script type="text/javascript">	
				
				var URL;
				URL = "' . $pref . $row2[66].'/login?username=" + encodeURIComponent("'.trim($row[1]).'") + "&password=" + encodeURIComponent("'.trim($row[2]).'");
				GetHTTP(URL);
				
				
	function GetHTTP(url) {
		var request=null;
		request=new XMLHttpRequest();
		request.open(\'GET\', url, false);
		request.send(null);
		return request.responseText;
	}
	</script>
	';	 
			mysql_query('INSERT INTO `actions` VALUES ("END_HOT_AUTH","'.$_SESSION['guid'].'","","","'.uniqid().'");',$GLOBALS['mysql']);
	
		}
	}
	
	if (!isset($_SESSION['guid'])){
		logout();
	}
	
	$sql = "SELECT `user_name`, `tarif_guid`, `group_guid` FROM `stat` WHERE `shortguid` = '".mysql_real_escape_string($_SESSION['guid'])."';"; 
				
		$res = mysql_query($sql,$GLOBALS['mysql']);
		
		if (mysql_num_rows($res)==0){
			logout();
		}
		
		$row = mysql_fetch_array($res);
				
		$_SESSION['login']=$row[0];
		$_SESSION['tarif_guid']=$row[1];
		$_SESSION['group_guid']=$row[2];
	
	
	$InAccindent = $OtherInfo[132];
	$OffertaAccepted = $OtherInfo[148];
	
	# Определяем заголовок страницы
	//33 - Общая информация
	//$result['page_title']		= $strings[33];
		if (!empty($result['stopdate'])) 
		{
			if ($OtherInfo[35]==1){
				//34 - Предполагаемая дата отключения:
				//415 -Текущего баланса хватит до 
				$result['dates'] = '
					<div class="m1">'.$strings[415].' '.$result['stopdate'].'</div>';
			}
		}
		else 
		{			
			$result['dates'] = '';
		}
	
	$PromisePayCost=0;
	$PersonalInfo = $result['pinfo'];
	$PersonalInfo = explode("||",$PersonalInfo);
	$PortPay = str_replace(',','.',$OtherInfo[24]);
	$DaysToStop = str_replace(',','.',$OtherInfo[31]);
	$BalA= explode(' ', $result['ballance']);
	
	# Информер о балансе (выводится начиная с 3 дней до списания средств, в случае, если у пользователя недостаточно средств для оплаты абонентской платы
	$difference = round(floatval(str_replace(',','.',$BalA[0])) - floatval(str_replace(',','.',$result['paysize'])),2);
	$difference = $difference - str_replace(',','.',$OtherInfo[55]);
	$blocktime = date("Y-m-d");
	$dtp = (int)$result['daystopay'];
	$blocktime=strtotime($blocktime . "+".$dtp." days"); 	
	
	//if ($OtherInfo[79] == '0') {
	//	$allCost = (float)str_replace(',','.',$result['paysize']) - (float)str_replace(',','.',$result['ballance']); // Абон. плата за все услуги (в данном участе только оплата по тарифу, добавляется в любом случае)
	//	$allCost = $allCost - (float)str_replace(',','.',$OtherInfo[55]);
	//} else {
	//	$allCost =-(float)str_replace(',','.',$OtherInfo[80]) + (float)str_replace(',','.',$OtherInfo[55]);
	//}
	//$allCost = $allCost - (float)$PersonalInfo[26];	  	
	
	$additional = '';
	
	# Если активирован обещанный платеж, добавляется его стоимость и добавляется название услуги в список подключенных услуг
	if ($result['promisepay']=='True') { 
		$addf = true;
		//35 - Обещанный платеж	 
		$promisePayText = '<div>'.$strings[35].': <span class="bold">'.intval($OtherInfo[29]). " ". $GLOBALS['curr'] . '</span></div>';
		//$allCost = $allCost - (float)$PromisePayCost;
		$additional .= '<div>'.$strings[35].'</div>';
		}
	# Если активирован режим турбо, добавляется его стоимость и добавляется название услуги в список подключенных услуг
	if ($result['turbo'] =='True') {
		$addf = true;
		//$allCost = $allCost - (float)$result['tarifs']['turbo_cost'];
		//36 - Режим ускорения
		$additional .= '<div>'.$strings[36].'</div>';
		}
	if ($result['reserving']=='1'){
		//37 - Включено резервирование абонентской линии!
		$additional.= '<div>'.$strings[37].'</div>';
	}

	$allCost = $OtherInfo[88];

	if ((int)$DaysToStop<=3)
	{	
		if (($result['promisepayenabled']=='True') and ($result['promisepay']=='False') and ((intval($result['ballance']) < 0) or ($OtherInfo[94]==1))) 
		{ 
			//35 - Обещанный платеж
			//38 - Вы можете воспользоваться услугой
			$allowpp = $strings[38].' <a href="promisepay.php">'.$strings[35].'</a>';
		} 
		else 
		{
			$allowpp = '';
		}
		
		if ($difference==0) {$difference=0.01;}
		$PayMSG=''; $result['informer-pay'] = '';
		if ($OtherInfo[79] == '0') {
			//39 - После списания абонентской платы %s Ваш баланс составит
			$PayMSG=sprintf($strings[39],'<span class="underline">'.$result['stopdate'].'</span>').' '.$OtherInfo[58].' '.$GLOBALS['curr'];
		} else {
			if ($OtherInfo[35]==1){
				//40 - После списания %s Ваш баланс составит
				$PayMSG=sprintf($strings[40],$result['stopdate']).' '.$OtherInfo[80].' '.$GLOBALS['curr'];
			}
		}
		if (($result['promisepayenabled']=='True') and ($result['promisepay']=='False') and ((intval($result['ballance']) < 0) or ($OtherInfo[94]==1))) 
		{ 
			//35 - Обещанный платеж
			//38 - Вы можете воспользоваться услугой
			$allowpp = $strings[38].' <a href="promisepay.php">'.$strings[35].'</a>';
		} 
		//41 - ВНИМАНИЕ
		if ((strlen($PayMSG)>0) || ($allCost > 0)){
			if (strlen($PayMSG)>0)
				$result['informer-pay'] .= '
				<div class="i m3">
					<i class="shout fas fa-exclamation-circle"></i>
					'.$PayMSG.'
				</div>';
			$s='';
			if ($OtherInfo[35]==1){
				//42 - Во избежание блокировки доступа к сети пополните Ваш баланс на
				$s = $strings[42];
			} else {
				// 396 - Для возобновления доступа к сети пополните Ваш баланс на
				$s = $strings[396];
			}
				if ($allCost > 0){
					$result['informer-pay'] .= '
					<div class="i m3">
						<i class="shout fas fa-exclamation-circle"></i>
						'.$s.' '.($allCost).' '.$GLOBALS['curr'].'
					</div>';
				}
				if (strlen($allowpp)>0){
					$result['informer-pay'] .= '
					<div class="i m3">
						<i class="shout fas fa-exclamation-circle"></i>
						'.$allowpp.'
					</div>';
				}
		}
	} 
	else 
	{
		$result['informer-pay'] = '';
	}

	$result['informer'] = '';
	
	if (strlen($OtherInfo[152])>0){
		// 584 - Внимание! С %s начнёт списываться полная оплата по тарифу!
		$result['informer'] =  '<div class="info">'.sprintf($strings[584],$OtherInfo[152]).'</div>';
	}
	
	$Passport_Informer='';
	if ($GLOBALS['BlockAccessIfOldPassport']=='1'){
					
		if ($OtherInfo[187]=='1'){
			// 656 - Доступ приостановлен! Необходимо обновить паспортные данные! <br> Обратитесь в <b><a href=\'help.php\' style=\'color: black;\'>службу поддержки</a></b>!
			$Passport_Informer = '<div class="info_red"><div><p style="width: 75%">'.$strings[656].'</p></div></div>';
		}
					
	}
	
	
	$Accident_Informer = (($GLOBALS['UseAccidentMode']=='1')&&($InAccindent==1))?'<div class="info_red"><div>'.$GLOBALS['AccidentInformation'].'</div></div>':'';
	

	// 574 - Доступ приостановлен! Необходимо принять публичную оферту!
	$Oferta_Informer = (($GLOBALS['NoPayWithoutOferta']=='1')&&($OffertaAccepted==0))?'<div class="info_red"><div><p style="width: 70%">'.sprintf($strings[651],'&nbsp<a href="oferta_show.php" target="_blank" style="color:white;">'.$strings[575].'</a>!</p>').'<button style="position: absolute; right: 10px;float: right;" onclick="PopUP()">'.$strings[577].'</button></div></div>':'';

	$Oferta_Informer = (($GLOBALS['NoInternetWithoutOferta']=='1')&&($OffertaAccepted==0))?'<div class="info_red"><div><p style="width: 70%">'.sprintf($strings[574],'&nbsp<a href="oferta_show.php" target="_blank" style="color:white;">'.$strings[575].'</a>!</p>').'<button style="position: absolute; right: 10px;float: right;" onclick="PopUP()">'.$strings[577].'</button></div></div>':$Oferta_Informer;

	
	$temporary_access_informer = 0;
	$res = mysql_query('SELECT `temporary_access_informer` FROM `tarifs` WHERE `tarif_guid` = '.mysql_real_escape_string($_SESSION['tarif_guid']).';',$GLOBALS['mysql']);
	
	if (mysql_num_rows($res)>0){
		$row = mysql_fetch_row($res);
		$temporary_access_informer = $row[0];
	}

	// 544 - У вас активирован временный доступ. До завершения осалось %s минут.
	// 592 - Вы можете %s кратковременный доступ.
	// 593 - активировать
	if ($OtherInfo[169]=='1'){
		$tmp_access_informer = ($OtherInfo[133]=='1')?('<div class="info_green"><div>'.sprintf($strings[544], round((int)$OtherInfo[135]/60)).'</div></div>'):((($temporary_access_informer=='1')&&($OtherInfo[35]=='0'))?('<div class="info_green"><div>'.sprintf($strings[592],'&nbsp<a href="tmp_access.php" style="color:white;font-weight: bold;">'.$strings[593].'</a>&nbsp').'</div></div>'):(''));
	} else {$tmp_access_informer = '';}
	
	//43 - АРХИВ
	if ($result['tarif'] == $strings[43] && $GLOBALS['ToArchiveAfter'] > 0)
	{
		//41 - ВНИМАНИЕ
		//44 - Ваша учетная запись заблокирована (более %s дней без активности)!
		//45 - Для возобновления доступа обратитесь в службу поддержки.
		$result['informer'] = '
						<div class="warning">
							'.sprintf($strings[44],$GLOBALS['ToArchiveAfter']).' 
							'.(($GLOBALS['AutoGetOutFromArchive']!=1)?('<br>'.$strings[45]):('')).'
						</div>
						';
	}
	
	if (!isset($result['tarifs']['information'])){$result['tarifs']['information']='';}
	if (!isset($result['groups']['information'])){$result['groups']['information']='';}
	
	if ((strlen($PersonalInfo[8])>0) || (strlen($PersonalInfo[9])>0) || (strlen($result['tarifs']['information'])>0) || (strlen($result['groups']['information'])>0)) {
		
		$Inf1= parse_template($result,base64_decode($PersonalInfo[8]),NULL,TRUE,false);
		$Inf2= parse_template($result,base64_decode($PersonalInfo[9]),NULL,TRUE,false);
		
		
		//46 - Информация
		if (strlen($PersonalInfo[8])>0) {$result['informer'] .= '<div>'.str_replace("\n", '<br>', CleanTicketFromInfo($Inf1)).'</div>';}
		if (strlen($PersonalInfo[9])>0) {$result['informer'] .= '<div>'.str_replace("\n", '<br>', CleanTicketFromInfo($Inf2)).'</div>';}
		if (strlen($result['tarifs']['information'])>0) {$result['informer'] .= '<div>'.str_replace("\n", '<br>', base64_decode($result['tarifs']['information'])).'</div>';}
		if (strlen($result['groups']['information'])>0) {$result['informer'] .= '<div>'.str_replace("\n", '<br>', base64_decode($result['groups']['information'])).'</div>';}
	}	
	if (strlen($result['informer'])>0)
		$result['informer'] = '<div class="info">'.$result['informer'].'</div>';

	if (strlen($tmp_access_informer)>0){
		$result['informer'] = $tmp_access_informer . '' . $result['informer'];
	}

	if (strlen($Accident_Informer)>0){
		$result['informer'] = $Accident_Informer . '' . $result['informer'];
	}
	
	if (strlen($Oferta_Informer)>0){
		$result['informer'] = $Oferta_Informer . '' . $result['informer'];
	}
	
	if (strlen($Passport_Informer)>0){
		$result['informer'] = $Passport_Informer . '' . $result['informer'];
	}
	

	$Addr=$OtherInfo[2];
	$AutoPromisePay = $OtherInfo[10];
	$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	$promisePayText="";
	$CurMonthStart = $OtherInfo[17];
	$CurMonthTraffic = $OtherInfo[18];
	$TrafLimExt = str_replace(',','.',$OtherInfo[30]);
		
	
	# Подготавливаем вывод содержимого страницы
	$result['traffic'] = explode(' ',$result['traffic']);
	//47 - Мб
	$result['traffic'] = $result['traffic'][0].' '.$strings[47];
	
	$menu = menuManager();
	
	$Pay2='';
	
	$allCost = str_replace(',','.',$OtherInfo[74]);
	
	if ($GLOBALS['UseDefaultPayment']==1){
		
		$PayPage='';
		$PaymentGateway = $strings[342];
		
		if (($GLOBALS['DefaultPaymentID']==0)&&($GLOBALS['UseLiqPay'])){
			$PayPage='liqpay.php';
			$PaymentGateway .= ' LiqPay';
		} elseif (($GLOBALS['DefaultPaymentID']==1)&&($GLOBALS['UsePayeer'])){
			$PayPage='payeer.php';
			$PaymentGateway .= ' Payeer';
		} elseif (($GLOBALS['DefaultPaymentID']==3)&&($GLOBALS['UseOnPay'])){
			$PayPage='onpay.php';
			$PaymentGateway .= ' OnPay';
		} elseif (($GLOBALS['DefaultPaymentID']==5)&&($GLOBALS['UsePayPlug'])){
			$PayPage='payplug.php';
			$PaymentGateway .= ' PayPlug';
		} elseif (($GLOBALS['DefaultPaymentID']==6)&&($GLOBALS['UseYaMoney'])){
			$PayPage='yamoney.php';
			$PaymentGateway .= ' Ю.Money';
		} elseif (($GLOBALS['DefaultPaymentID']==11)&&($GLOBALS['UseEasyPay'])){
			$PayPage='easypay.php';
			$PaymentGateway .= ' EasyPay';
		} elseif (($GLOBALS['DefaultPaymentID']==14)&&($GLOBALS['UsePrivat24'])){
			$PayPage='privat24.php';
			$PaymentGateway .= ' Privat24';
		} elseif (($GLOBALS['DefaultPaymentID']==16)&&($GLOBALS['UseROBOKASSA'])){
			$PayPage='robokassa.php';
			$PaymentGateway .= ' ROBOKASSA';
		} elseif (($GLOBALS['DefaultPaymentID']==20)&&($GLOBALS['UseTinkoff'])){
			$PayPage='tinkoff.php';
			$PaymentGateway .= ' Тинькофф';
		} elseif (($GLOBALS['DefaultPaymentID']==28)&&($GLOBALS['UseYaKassa'])){
			$PayPage='yakassa.php';
			$PaymentGateway .= ' Ю.Kassa';
		} elseif (($GLOBALS['DefaultPaymentID']==29)&&($GLOBALS['UseSBRF'])){
			$PayPage='sbrf.php';
			$PaymentGateway .= ' Сбербанк';
		} elseif (($GLOBALS['DefaultPaymentID']==31)&&($GLOBALS['UseVivaWallet'])){
			$PayPage='vivawallet.php';
			$PaymentGateway .= ' VivaWallet';
		} elseif (($GLOBALS['DefaultPaymentID']==38)&&($GLOBALS['UseProdamus'])){
			$PayPage='prodamus.php';
			$PaymentGateway .= ' Prodamus';
		} elseif (($GLOBALS['DefaultPaymentID']==46)&&($GLOBALS['UseClickUZ'])){
			$PayPage='clickuz.php';
			$PaymentGateway .= ' Click.UZ';
		} elseif (($GLOBALS['DefaultPaymentID']==47)&&($GLOBALS['UseiPay'])){
			$PayPage='ipay.php';
			$PaymentGateway .= ' iPay.ua';
		} elseif (($GLOBALS['DefaultPaymentID']==51)&&($GLOBALS['UsePayMe'])){
			$PayPage='payme.php';
			$PaymentGateway .= ' PayMe';
		} elseif (($GLOBALS['DefaultPaymentID']==54)&&($GLOBALS['UsePayHUB'])){
			$PayPage='payhub.php';
			$PaymentGateway .= ' PayHUB';
		} elseif (($GLOBALS['DefaultPaymentID']==56)&&($GLOBALS['UseGorod74'])){
			$PayPage='gorod74.php';	
			$PaymentGateway .= ' Город 74';
		} elseif (($GLOBALS['DefaultPaymentID']==60)&&($GLOBALS['UsePSBank'])){
			$PayPage='psbank.php';	
			$PaymentGateway .= ' Промсвязьбанк';
		} elseif (($GLOBALS['DefaultPaymentID']==61)&&($GLOBALS['UseAlfabank'])){
			$PayPage='alphabank.php';
			$PaymentGateway .= ' Альфабанк';
		} elseif (($GLOBALS['DefaultPaymentID']==63)&&($GLOBALS['UsePayAnyway'])){
			$PayPage='payanyway.php';
			$PaymentGateway .= ' PayAnyWay';
		} elseif (($GLOBALS['DefaultPaymentID']==64)&&($GLOBALS['UsePayKeeper'])){
			$PayPage='paykeeper.php';
			$PaymentGateway .= ' PayKeeper';
		} elseif (($GLOBALS['DefaultPaymentID']==72)&&($GLOBALS['UseSelfWork'])){
			$PayPage='selfwork.php';
			$PaymentGateway .= ' SelfWork';
		} elseif (($GLOBALS['DefaultPaymentID']==77)&&($GLOBALS['UseCryptoCloud'])){
			$PayPage='CryptoCloud.php';
			$PaymentGateway .= ' Crypto Cloud';
		} elseif (($GLOBALS['DefaultPaymentID']==79)&&($GLOBALS['UseOzon'])){
			$PayPage='ozon.php';
			$PaymentGateway .= ' Ozon';
		}
		
		
		if (strlen($PayPage)>0){
		
			$Pay2='<div class="four PayDiv" style="height:32px; padding: 2px; margin-bottom: 10px; border: 2px solid #252525; border-radius: 2px; position: absolute; bottom: 0;">
						<form method="POST" action="'.$PayPage.'" target="_blank">
							<table>
								<tr>
									<td>
										<div class="DispAtCompact">
											<i class="fas fa-credit-card" style="font-size: 26px; padding-top:0px;margin-left:4px;"></i>
										</div>
										<div class="DispAtWide">'.$strings[220].':</div>
									</td>
									<td>
										&nbsp<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay" style="height:27px;width:64px;"/>
										<input type="hidden" name="gopay" id="gopay" value="1">
										<input type="hidden" name="paysize" id="paysize" value="">
									</td>
									<td width=26px>
										'.$GLOBALS['curr'].'
									</td>
									<td>
										
										<button class="PayButton" type="submit" name="billme" style="display: inline-block; margin-left:6px;" onClick="if (isTelegramWebApp()){alert(\''.$strings[603].'\');return false;}document.getElementById(\'paysize\').value=document.getElementById(\'sum\').value;" onsubmit="return false;">'.$strings[218].'</button>
										
										<div class="DispAtWide">&nbsp&nbsp ' . $PaymentGateway. '</div>
									</td>
								</tr>
							</table>
						</form>
					</div>';
		}
	}
	
	//48 - Да	
	if ($OtherInfo[35] == 1) {
		
		
		
		//403 - Интернет подключен, все должно работать.
		//415 -Текущего баланса хватит до 
		$result['state'] = $strings[403];
		} else {
			//404 - Доступ в интернет приостановлен.
			$result['state'] = '<i class="shout fas fa-exclamation-circle"></i>'.$strings[404];
			
			if (($GLOBALS['AutoGetOutFromArchive']=='1')&&($result['isarchived']=='1')){
				$result['state'] .= '<br>'.$strings[600];
			}
		}
	
	# Если дополнительные услуги подключены, то создаем вывод этих услуг
	$additional_functions = Array('','','','','');

	$OtherInfo[65]=str_replace(',','.',$OtherInfo[65]);
	if ((float)$OtherInfo[65]>0){
		//412 - на сумму
		$additional_functions[4] = ' '.$strings[412].' '.$OtherInfo[65].' '.$GLOBALS['curr'];
	}
	
	if ((strlen($PersonalInfo[10])>0) && ($GLOBALS['HideServicesList'])=='0') {
		
		//echo $PersonalInfo[10];exit();
		$SVC_info='';
		$SVC_a=explode('#NL#',$PersonalInfo[10]);
		
		$Sc=0;
		foreach ($SVC_a as $value) {
			$SVC_a2=explode('*|*',$value);
			
			if (count($SVC_a2)==3){
				//394 - Абонентская плата
				$Abon="";
				if (strlen($SVC_a2[1])>0){
					$Abon="<font style = 'font-size : 11px;' color = 'darkgray'> ({$strings[394]}: {$SVC_a2[1]} {$GLOBALS['curr']} )</font>";
				}					
				$SVC_info = $SVC_info . "<span class='tooltip'><button type='button' class='tooltip-btn'>{$SVC_a2[0]} $Abon <span class='tooltip-text'>{$SVC_a2[2]}</span>  </button></span><br>";
				$Sc +=1;
			}
		}
		
		//51 - Подключенные услуги
		//409 - услуга
		//410 - услуги
		//411 - услуг
		$additional_functions[0] = '
			<div class="m3 i">'.$strings[51].':</div>
			<ul class="m1">
				'.$SVC_info.'
			</ul>';
			//$PersonalInfo[10]
			//
		$additional_functions[1] = $Sc;
		$additional_functions[2] = '<div> '.$additional_functions[1].' '.declension($additional_functions[1],$strings[409],$strings[410],$strings[411]).' '.$additional_functions[4].'</div>';
	}
						
	$agent = "";
	//52 - Скачать агента авторизации
	if ($GLOBALS ["use_agent"] == "True"){$agent = "<a href = 'agent.php'>".$strings[52]."</a>";}

	
	$Credit = "";
	//414 - При балансе %s доступ в интернет приостанавливается.
	if (((float)str_replace(',','.',$OtherInfo['16'])<=1)&&($OtherInfo[35]=='1')){
		$StopSum=(float)str_replace(',','.',$OtherInfo['16']);
		$Credit ='<div class="m2">'.sprintf($strings[414],$StopSum.' '.$GLOBALS['curr']).'</div>';
	}
	
	$abonplat='';
	$OtherInfo[44]=str_replace(',','.',$OtherInfo[44]);
	//if ((float)$OtherInfo[44]>0){
		
		$PortPayOut='';
		//54 - в том числе аренда порта
		if ((float)$PortPay>0) {
			$DstPortPayName = (($GLOBALS ['ChangePortPayName'] == 1)&&(strlen($GLOBALS ['PortPayName'])>0))?($GLOBALS ['PortPayName']):($strings[54]);
			$PortPayOut= "<br><font style = 'font-size : 11px;'><i> (".$DstPortPayName.": $PortPay " . $GLOBALS['curr'] . ")</i></font>";
		}
		
		
		//55 - Абонентская плата по тарифу
		if (((int)$OtherInfo[35]==1)&&(strlen($result['stopdate'])>0)){
			$abonplat .= '<div class="datespanel">'.$strings[415].' '.$result['stopdate'].'</div>';
		}
		
		if (mb_strlen($OtherInfo[45])>0){
			$OtherInfo[45] = "<font style = 'font-size : 11px;'><i> " . $OtherInfo[45] . "</i></font>";
		}
		
		$abonplat .='<div>'.$strings[55].': '.$OtherInfo[44].' ' . $GLOBALS['curr'].$PortPayOut.' '.$OtherInfo[45].'</div>';
	//}
	
	if (strlen($Pay2)>0){$result['state']='';}
	
	$rplat='';
	$OtherInfo[74]=str_replace(',','.',$OtherInfo[74]);
	if ((int)$OtherInfo[74]>0){
		//56 - Рекомендуемая сумма к оплате
		$rplat='<div class="i m3">'.$strings[56].':</div>
				<div class="vg m2">
					<div>
						<div></div>
						<div>'.Symbol($OtherInfo[74].' '.$GLOBALS['curr']).'</div>
					</div>
				</div>';
	}
	$adr='';
	//57 - Адрес подключения
	if (strlen($Addr)>0){$adr='<div>'.$strings[57].': <span class="bold">'.$Addr.'</span></div>';}
	$spdlims='';
	//58 - Огр. скорости
	if ($result['spdlim']<>'- / -'){
		$spdlims='					<div style="min-width: 125px;">
										<div>'.$strings[402].', '.preg_replace('~[^\s]*\s?/\s?[^\s]*\s(.*)~', '$1', $result['spdlim']).':</div>
										<div>'.preg_replace('~([^\s]*\s?/\s?[^\s]*)\s.*~', '$1', $result['spdlim']).'</div>
									</div>';}
	$ShowNextTarif='';
	$TrafLim='';
	$NextTarif='';
	
		$NameLNG = htmlspecialchars($result['tarif']);
		$sql = "SELECT `object_data` FROM `system_objects` WHERE `object_name` = '".mysql_real_escape_string($NameLNG)."' and `object_type` = 0;";
		$res = mysql_query($sql,$GLOBALS ["mysql"]);
			
		if (mysql_num_rows($res)>0){
			
			$row = mysql_fetch_array($res);
			$JSON=json_decode($row[0],true);
			
			if (!isset($JSON['NameLNG'])){
				$NameLNG = '';
			} else{		
				$NameLNG = htmlspecialchars($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']]);
			}
		}
		
		if (strlen($NameLNG)==0){$NameLNG = $result['tarif'];}
	
	if ((strlen($result['nexttarif'])>0) && ($result['nexttarif'] <> $result['tarif']) && (strlen($result['tarifends'])>0) ) {
		
		$NameLNG2 = '';
		
		$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($OtherInfo[174])."' and `object_type` = 0;";
		$res = mysql_query($sql,$GLOBALS ["mysql"]);
			
		if (mysql_num_rows($res)>0){
			
			$row = mysql_fetch_array($res);
			$JSON=json_decode($row[0],true);
			
			if (!isset($JSON['NameLNG'])){
				$NameLNG2 = '';
			} else{		
				$NameLNG2 = htmlspecialchars($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']]);
			}
		}
		
		//59 - переход на
		$NextTarif=' <span class="i small"> ('.$strings[59].' \'' . $NameLNG2 . '\': ' . $result['tarifends'] .')</span>';
	}
	
	//60 - лимит
	if (strlen($OtherInfo[62])>0){$TrafLim = ', '.$strings[60].' ' . $OtherInfo[62];}
	if (strlen($OtherInfo[61])>0){$TrafLim2 = ', '.$strings[60].' ' . $OtherInfo[61];}else{$TrafLim2='';}
	
	if ($TrafLimExt==1){
		//61 - Достигнут лимит по трафику!
		$s2 ='<div class="i m3">
						<i class="shout fas fa-exclamation-circle"></i>'.$strings[61].'
					</div>';
	} else {$s2 ='';}
	
	$Kdiv=$GLOBALS['Kbit_Size']*$GLOBALS['Kbit_Size'];
	$todaytraffic=explode('/',$result['todaytraffic']);
	$todaytraffic=round((float)$todaytraffic[0]/$Kdiv) . ' / ' . round((float)$todaytraffic[1]/$Kdiv);
	$ip=trim($result['usrip'],';');
	if (strlen($ip)>70){$ip=substr($ip,0,90) . '...';}
	$ip=str_replace(';',',',$ip);
	if (!isset($result['dates'])){$result['dates']='';}
	# Получение ИО
	$FIO = explode(' ',$result['FIO']);
	if (count($FIO)>2){unset($FIO[0]);}
	$FIO = implode(' ',$FIO);
	
	# Готовим основное содержимое страницы
	//62 - Статистика договора
	//63 - № договора
	//64 - Расход трафика в текущем месяце
	//65 - с
	//66 - Расход трафика за сутки
	//67 - Статус подключения
	//68 - Ваш IP адрес
	//69 - Текущий тарифный план
	//400 - Здравствуйте,
	//401 - Ваш баланс
	//402 - Скорость
	//405 - Настройки
	//406 - Платежи
	//407 - Услуги и тарифы
	//408 - Ваш тариф
	//111 - Новости
	//413 - Помощь

	
	$news = ShowNews(true);
	$page = setPage();
	$Cur='';
	$BalA = explode(' ', $result['ballance']);
	if ($BalA[1] <> 'Руб.') {$Cur=', '.$BalA[1];}
	
	$Adr='';
	
	if (($GLOBALS['ShowUserAddressInWEB']==1)&&(mb_strlen($PersonalInfo[4])>0)){
		$Adr='
		<tr>
			<td>
				<span style="position: relative; font-size:8px; color: lightgray; top:-32px; left: 100px;">'.$PersonalInfo[4].'</span>
			</td>
		</tr>';
	}
	
	$result['out'] = $result['informer'].'
						<div id="menu" class="menu '.$page['one'].'">
							<div class="one'.($OtherInfo[35]==1?'':' animate'). ($GLOBALS['UseSpeedTest']==0?'':' speed-true') .'">
								<i class="fas fa-user-circle"></i>
								<table>
									<tr>
										<td>
											<div class="h3"><span>'.$strings[400].'</span> '.$FIO.'<span>!</span></div>
										</td>
									</tr>
									'.$Adr.'
								</table>
								
								<div class="vg">
									<div style="min-width: 125px;">
										<div>'.$strings[401].$Cur.':</div>
										<div>'.Symbol($result['ballance']).'</div>
									</div>
									'.$spdlims.'
								</div>
								<div class="txt">
									'.$abonplat.'<div class="txt2" style="height:50px;"></div>
									<div>'.$strings[68].': '.$ip.'</div>'.$Pay2.'
									<div>'.$result['state'].'</div>
								</div>
								<div class="graph">
									<canvas id="canvas" width="147" height="248"></canvas>
								</div>
								<div id="style"><script type="text/javascript">AutoStyleIco()</script></div>
							</div>
							<div class="three" onclick="Menu(this)" data-html="'.$strings[405].'">
								<i class="fas fa-cog"></i>
							</div>
							<div class="four" onclick="Menu(this)" data-html="'.$strings[406].'">
								<i class="fas fa-piggy-bank'.(strlen($result['informer-pay'])>0?' active animate':'').'"></i>
							</div> 
							<div class="two" onclick="Menu(this)" data-html="'.$strings[407].'">
								<i class="fas fa-shopping-bag'.(strlen($s2)>0?' active animate':($additional_functions[1]>0?' active':'')).'" data-html="'.$additional_functions[1].'"></i>
								<div class="txt">
									<div>'.$strings[408].':</div>
									<div>«'.$NameLNG.'» '.$NextTarif.'</div>
									'.$additional_functions[2].'
								</div>
							</div> 
							<div '.(isset($GLOBALS['ShowNews']) && $GLOBALS['ShowNews']=='True'?'class="five" onclick="location.href = \'./news.php\'"':'class="five deac"').' data-html="'.$strings[111].'">
								<i class="fas fa-envelope-open'.($news>0?' active':'').'" data-html="'.$news.'"></i>
							</div>
							<div class="six" onclick="Menu(this)" data-html="'.$strings[413].'">
								<i class="fas fa-life-ring"></i>
							</div>
						</div>
						<div id="two" class="'.$page['two'].'">
							'.menuManager('two').'
							<div class="content">
								<div class="two">
									<i class="fas fa-shopping-bag"></i>
									<div class="h3 m4">'.$strings[407].'</div>
									'.$s2.'
									<div class="i m3">'.$strings[69].':</div>
									<div class="h4 m1 tarif">«'.$NameLNG.'» '.$NextTarif.'</div>
									<div class="m2 small">'.$strings[64].' '. $CurMonthTraffic . " (".$strings[65]." ".$CurMonthStart.") ".$TrafLim.'</div>
									<div class="m1 small">'.$strings[66].' '. $todaytraffic . ' Мб.'.$TrafLim2.'</div>
									'.$additional_functions[0].'
								</div>
								'.$menu['two'].'
							</div>
						</div>
						<div id="three" class="'.$page['three'].'">
							'.menuManager('three').'
							<div class="content">
								<div class="three">
									<i class="fas fa-cog"></i>
									<div class="h3 m4">'.$strings[405].'</div>
									<div class="i m3 style">'.$strings[416].'</div>
								</div>
								'.$menu['three'].'
								'.$agent.'
							</div>
						</div>
						<div id="four" class="'.$page['four'].'">
							'.menuManager('four').'
							<div class="content">
								<div class="four">
									<div id="TelegramAlert" style="display:none;background-color: #D84136;text-align: center;padding:16px;">'.$strings[603].'</div>
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.$strings[406].'</div>'
									.$result['informer-pay']
									.$rplat
									.$Credit
									.$result['dates'].'
								</div>
								'.$menu['four'].'
							</div>
						</div>
						<div id="six" class="'.$page['six'].'">
							'.menuManager('six').'
							<div class="content">
								<div class="six">
									<i class="fas fa-life-ring"></i>
									<div class="h3 m4">'.$strings[405].'</div>
									<div class="i m3">'.$strings[416].'</div>
								</div>
								'.$menu['six'].'
							</div>
						</div>
						<div class="head" style="justify-content: center;">'.$GLOBALS['WEB_Basement'].'</div>
						
						<body onload="if (isTelegramWebApp()){document.getElementById(\'TelegramAlert\').style.display=\'block\';BlockPayment();}">
						
						<script type="text/javascript">
							
							function isTelegramWebApp() {
							  return typeof TelegramWebviewProxy !== "undefined";
							}
							
							function BlockPayment() {
								const payLinks = Array.from(document.querySelectorAll("a[pay_btn]"));
								payLinks.forEach(btn => {
									btn.removeAttribute("href");
									btn.style.opacity=0.3;
									btn.style.cursor="not-allowed";
								});
							}
							
    document.addEventListener("click", function (e) {
      const isButton = e.target.classList.contains("tooltip-btn");
      const allTooltips = document.querySelectorAll(".tooltip");

      allTooltips.forEach(t => t.classList.remove("visible"));

      if (isButton) {
        const parentTooltip = e.target.closest(".tooltip");
        parentTooltip.classList.add("visible");
      }
    });

    // Дополнительно: закрытие при потере фокуса клавиатурой
    document.querySelectorAll(".tooltip-btn").forEach(btn => {
      btn.addEventListener("blur", () => {
        setTimeout(() => {
          const tooltip = btn.closest(".tooltip");
          tooltip?.classList.remove("visible");
        }, 100);
      });
    });

							
						</script>'; 
						
	if (isset ($_REQUEST["pushed"])){
		if (is_numeric($_REQUEST["pushed"])){
			$GUID = mysql_real_escape_string($_REQUEST["pushed"]);
			$shortguid = mysql_real_escape_string($_SESSION['shortguid']);
			
			$sql = "SELECT `text` FROM `pushed` WHERE `id` = '$GUID' AND `shortguid` = '$shortguid';";
			$res = mysql_query($sql,$GLOBALS ["mysql"]);
				
			if (mysql_num_rows($res)>0){
				$row = mysql_fetch_row($res);
				
				if (mb_strlen($row[0])>0){
					$result['out'] .= PopUP2($row[0], 'OK');
				}
			}
		}
	}
						
	function Rnd($n,$k) {
		return floor($n*$k)/$k;
	} 
	function extr($v1,$v2=false,$l1=false,$l2=false){
		$out['metric'] = 'MB';
		$k = 1; $r = 100; 
		$v1 = explode('/', str_replace(' ','',$v1)); 
		if ($v2) $v2 = explode('/', str_replace(' ','',$v2));
		if ($l1) $l1 = preg_replace('/[^0-9]/', '', $l1);
		if ($l2) $l2 = preg_replace('/[^0-9]/', '', $l2);
		$Ksize = $GLOBALS['Kbit_Size'];
		//if (($v1[0] > $Ksize && (!$v2 || $v2[0] > 1024)){
		//if ( ($l1 > $Ksize && ($l2 > $Ksize || !$l2)) || (!$l2 && !$l2 && $v1[0] > $Ksize && (!$v2 || $v2[0] > $Ksize)) ){
		
		if (!$v2){
			$v2=[0,0];
		}
		
		if ( ($l1 > $Ksize || $l2 > $Ksize || $v1[0] > $Ksize || $v2[0] > $Ksize) ){
			$k = $Ksize; $r = 100; $out['metric'] = 'GB';  				
		}  
		
		if (strlen($l1)==0){$l1=false;}
		if (strlen($l2)==0){$l2=false;}
		
		if (!is_array($v1)){$v1=array(0,0);}
		if (!is_array($v2)){$v2=array(0,0);}
		
		$out['lim1'] = ($l1)?Rnd( $l1/$k, $r):0;
		$out['calc1'] = ($l1)?Rnd($out['lim1']-$v1[0]/$k, $r):Rnd($v1[0]/$k, $r);
		$out['lim2'] = ($l2)?Rnd( $l2/$k, $r):0;
		$out['calc2'] = ($l2)?Rnd($out['lim2'] - $v2[0]/$k, $r):Rnd($v2[0]/$k, $r);		
		return $out;
	}

	$NeetToShowTrafChart = true;
	// 454 - месяц 455 - Лимит 456 - Трафик 457 - Осталось 458 - Пакет трaфика 459 -  Расход трaфика
	if(strlen($OtherInfo[61])>0 && strlen($OtherInfo[62])>0)
		{
			$gr = extr($CurMonthTraffic, $todaytraffic, $OtherInfo[62],$OtherInfo[61]);
			$gr['title'] = $strings[458];
			$gr['conf'] = 'labels: ["'.$strings[454].'", "'.$strings[446].'"],
						datasets: [{
							label: "'.$strings[457].' '.$gr['metric'].'",
							backgroundColor: cchrt[2],
							data: ['.$gr['calc1'].', '.$gr['calc2'].']
						}, {
							label: "'.$strings[455].' '.$gr['metric'].'",
							backgroundColor: cchrt[3],
							data: ['.$gr['lim1'].', '.$gr['lim2'].']
						}]';
	} elseif(strlen($OtherInfo[61])>0 || strlen($OtherInfo[62])>0)
		{
			if(strlen($OtherInfo[61])>0) {
				$gr = extr($todaytraffic, false, $OtherInfo[61]);
				$gr['time']	= $strings[446];
			}else{
				$gr = extr($CurMonthTraffic, false, $OtherInfo[62]);
				$gr['time']	= $strings[454];		
			}
			$gr['title'] = $strings[458];
			$gr['conf'] = 'labels: ["'.$gr['time'].'"],
						datasets: [{
							label: "'.$strings[457].' '.$gr['metric'].'",
							backgroundColor: cchrt[2],
							data: ['.$gr['calc1'].']
						}, {
							label: "'.$strings[455].' '.$gr['metric'].'",
							backgroundColor: cchrt[3],
							data: ['.$gr['lim1'].']
						}]';
	} else {
		$NeetToShowTrafChart = false;
		$gr = extr($CurMonthTraffic, $todaytraffic);
		$gr['title'] = $strings[459];
		$gr['conf'] = 'labels: ["'.$strings[454].'", "'.$strings[446].'"],
						datasets: [{
						label: "'.$strings[456].' '.$gr['metric'].'",
						backgroundColor: cchrt[2],
						data: ['.$gr['calc1'].', '.$gr['calc2'].']
					}]';
	}
	
	// 576 - Принять публичную оферту?
	// 577 - Принять
	// 578 - Отклонить
	$PopUPvar="<div><div>".$strings[576]."</div><div class='b'><button type='button' name='go' onclick='GetHTTP(\\\"api.php?action=SET_OFERTA&value=\\\");Confirm();location.reload();'>".$strings[577]."</button><button type='button' onclick='Confirm()'>".$strings[578]."</button></div></div>";
	$PWA_Shown=false;
	
	if ($GLOBALS['BookmarkMessage']==1){
		
		if (isset($_SESSION['guid'])){			
			
			$query = "SELECT `timeval` FROM `last_pwa_check` WHERE shortguid=".mysql_real_escape_string($_SESSION['shortguid']).";";
			$MySQL_result = mysql_query($query,$GLOBALS['mysql']);
			if (mysql_num_rows($MySQL_result)==0){
				$query = "INSERT INTO `last_pwa_check` (`shortguid`, `timeval`) VALUES (".mysql_real_escape_string($_SESSION['shortguid']).",UNIX_TIMESTAMP(NOW()) - ". (86400 *($GLOBALS['AutoRecheckUsersMobTelInterval']+1)).") ON DUPLICATE KEY UPDATE `shortguid` = ".mysql_real_escape_string($_SESSION['shortguid']).", `timeval` = UNIX_TIMESTAMP(NOW()) -  ". (86400 *($GLOBALS['AutoRecheckUsersMobTelInterval']+1)).";";
				$MySQL_result = mysql_query($query,$GLOBALS['mysql']);
				
				$result['out'] .= PopUP3($strings[627], $strings[48], $strings[623]);
				$PWA_Shown=true;
			} else {
				
				$row = mysql_fetch_array($MySQL_result);
				
				// 627 - Необходимо добавить сайт на рабочий стол, чтобы получать Push уведомления. Продолжим?<br><a href='#'>Инструкция.</a>
				if ((time()-$row[0]) > (86400 * $GLOBALS['AutoRecheckUsersMobTelInterval'])){
					$query = "INSERT INTO `last_pwa_check` (`shortguid`, `timeval`) VALUES (".mysql_real_escape_string($_SESSION['shortguid']).",UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE `shortguid` = ".mysql_real_escape_string($_SESSION['shortguid']).", `timeval` = UNIX_TIMESTAMP(NOW());";
					$MySQL_result = mysql_query($query,$GLOBALS['mysql']);
					$result['out'] .= PopUP3($strings[627], $strings[48], $strings[623]);
					$PWA_Shown=true;
				}
			}
		}
	}
	
	
	$result['out'] .= '
						<script type="text/javascript">
							var colorchart = {
								dark: ["#fff","#fffbf8","#fffbf8","#fcd8b5",true], 
								green: ["#5a5a5a","#0a9677","#0a9677","#9dd5c8",false], 
								red: ["#5a5a5a","#d22a2a","#d22a2a","#eda9a9",false]
							};
							var cchrt = WhatColorGraph();
							var barChartData = {
								'.$gr['conf'].'
							};
							window.addEventListener("load", function() {
								if ('.((($NeetToShowTrafChart) || ($GLOBALS['ShowTrafChart']==1))?('true'):('false')).'){
									var ctx = document.getElementById("canvas").getContext("2d");
									Chart.defaults.global.defaultFontColor = cchrt[0];
									window.myBar = new Chart(ctx, {
										type: "bar",
										data: barChartData,
										options:{
											legend:{
												   display:false
												},
											title: {
												display: true,
												text: "'.$gr['title'].', '.$gr['metric'].'",
												fontStyle: "normal",
											},
											tooltips: {
												mode: "index",
												intersect: false
											},
											responsive: true,
											scales: {
												xAxes: [{
													stacked: true,
													gridLines : {
															display : false,
															color : cchrt[1],
															zeroLineColor: cchrt[1],
														}
												}],
												yAxes: [{
													stacked: false,
													gridLines : {
															display: cchrt[4],
															color : cchrt[1]
														},
													ticks: {
														suggestedMin: 0
													}
												}]
											}
										}
									});
								}
							})
							
							function PopUP(){
								document.body.innerHTML += "<div id=\'confirm_div\' name=\'confirm_div\' class=\'space confirm\' style=\'display:none;\'></div>";
								document.getElementById("confirm_div").style="block";
								document.getElementById("confirm_div").innerHTML="'.$PopUPvar.'";
							}							
							
								(async () => {
								  const status = new PwaStatus();
								  await status.detect();
								  //console.log(status.toJSON());
								  
								  if ((!status.isPWA)||(!status.isSubscribed)){
										
										let Pwa = document.getElementById("pwa_info");
										
										if (Pwa){
											Pwa.style.display="flex";
										}
										
										
										//const hint = getPwaInstallHint();
										//const div = document.createElement("div");
										//div.style.cssText = "padding:10px;background:#f0f0f0;margin:10px;";
										//div.textContent = hint;
										//document.body.appendChild(div);
								  }
								})();
							
						</script>';
		
						
	if (($GLOBALS['AutoRecheckUsersMobTel']==1)&&(!$PWA_Shown)){
		
		$pInfo = $result['pinfo'];
		$pInfo = explode("||",$pInfo);
		$LastTelChars='';

		if (strlen($pInfo[0]) > 7){
			$LastTelChars=substr($pInfo[0], -4);
		}
		
		if (strlen($LastTelChars)>3){
			
			$query = "SELECT `timeval` FROM `last_tel_check` WHERE shortguid=".mysql_real_escape_string($_SESSION['shortguid']).";";
			$MySQL_result = mysql_query($query,$GLOBALS['mysql']);
			if (mysql_num_rows($MySQL_result)==0){
				$query = "INSERT INTO `last_tel_check` (`shortguid`, `timeval`) VALUES (".mysql_real_escape_string($_SESSION['shortguid']).",UNIX_TIMESTAMP(NOW()) - ". (86400 *($GLOBALS['AutoRecheckUsersMobTelInterval']+1)).") ON DUPLICATE KEY UPDATE `shortguid` = ".mysql_real_escape_string($_SESSION['shortguid']).", `timeval` = UNIX_TIMESTAMP(NOW()) -  ". (86400 *($GLOBALS['AutoRecheckUsersMobTelInterval']+1)).";";
				$MySQL_result = mysql_query($query,$GLOBALS['mysql']);
				
				$result['out'] .= PopUP(sprintf($strings[505],'*******'.$LastTelChars), $strings[48], $strings[96]);
			} else {
				
				$row = mysql_fetch_array($MySQL_result);
				
				// 505 - Давайте проверим, <br>Ваш телефонный номер прежний: %s?
				if ((time()-$row[0]) > (86400 * $GLOBALS['AutoRecheckUsersMobTelInterval'])){
					$query = "INSERT INTO `last_tel_check` (`shortguid`, `timeval`) VALUES (".mysql_real_escape_string($_SESSION['shortguid']).",UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE `shortguid` = ".mysql_real_escape_string($_SESSION['shortguid']).", `timeval` = UNIX_TIMESTAMP(NOW());";
					$MySQL_result = mysql_query($query,$GLOBALS['mysql']);
					$result['out'] .= PopUP(sprintf($strings[505],'*******'.$LastTelChars), $strings[48], $strings[96]);
				}
			}
		}
		
	}
		
	# Вызываем функцию вывода информации на страницу
	return parse_template($result,'./template/default.php',NULL,FALSE,true);	
	}
	
function PopUP($Msg, $Yes, $No){
	return '
<div class="space confirm">
	<div>
		<div>'.$Msg.'</div>
		<div class="b">
			<button type="button" name="go" onclick="Confirm()">'.$Yes.'</button>
			<button type="button" onclick="window.location.href=\'changepi.php\'">'.$No.'</button>
		</div>
	</div>
</div>';
}
function PopUP2($Msg, $Yes){
	return '
<div class="space confirm">
	<div>
		<div>'.$Msg.'</div>
		<div class="b">
			<button type="button" name="go" onclick="Confirm()">'.$Yes.'</button>
		</div>
	</div>
</div>';
}
function PopUP3($Msg, $Yes, $No){
	return '
<div class="space confirm">
	<div>
		<div>'.$Msg.'</div>
		<div class="b">
			<button type="button" name="go" onclick="window.location.href=\'pwa_info.php\'">'.$Yes.'</button>
			<button type="button" onclick="Confirm()">'.$No.'</button>
		</div>
	</div>
</div>';
}


	
	# Резервирование
function Reserving() 	
{
	if (($_SESSION['can_reserving']<>'1') and ($_SESSION['reserving']<>'1') and ($_SESSION['reservingnext']<>'1') and ($_SESSION['reservingnext']<>'0')) {
		exit();
	}
	
	$strings=$GLOBALS['strings'];
		
	$result = getuserinfo();
		
	//70 - Резервирование абонентской линии
	$result['page_title'] = $strings[70];
	$uid = uniqid("");
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
	
	$MinTime=$result['tarifs']['reserving_min_days'];
	$Time=$result['tarifs']['reserving_max_days'];
	$Price=$result['tarifs']['reserving_cost'];
	$Price2=$result['tarifs']['reserving_cost2'];
	$OnFeeExecute=$result['tarifs']['reserving_start_at_fee_date'];
	$reserving_can_set_date=$result['tarifs']['reserving_can_set_date'];
	
	
	$otherinfoA=explode('||',$result['otherinfo']);
	
	
	# Если пользователь передал данные на сервер, то они обрабатываются и добавляются в базу, а так же, выводится сообщение
	if (isset($_POST['reservingme'])) {
			
		$reserving = mysql_real_escape_string($_POST['reservingme']);
		$rdate='';
		if (isset($_POST['rdate'])){
			$rdate = mysql_real_escape_string($_POST['rdate']);
		}
		
		
		$sql = "SELECT `paytime` FROM `stat` WHERE user_name='".mysql_real_escape_string($_SESSION['login'])."';";
		$sql_arr = mysql_fetch_row(mysql_query($sql,$mysql));
						
		if ($reserving=='1'){
			if ($OnFeeExecute=='1') {
				//71 - Резервирование будет включено в конце расчётного периода
				$rescode = $strings[71].': '.$sql_arr[0];
			} else {
				//72 - Резервирование будет включено в течении нескольких минут
				$rescode = $strings[72];}
		} else {
			if ($OnFeeExecute=='1') {
				//73 - Резервирование будет отключено после окончания расчётного периода
				$rescode = $strings[72].': '.$sql_arr[0];
				if ((int)$MinTime>1){
					//74 - но не ранее
					//75 - дней с момента активации
					$rescode.= ', '.$strings[74].' '.$MinTime.' '.$strings[75].'.';
				}
			}else {
				if ((int)$MinTime>1) {
					$otherinfo=$result['otherinfo'];
					$otherinfoA=explode('||',$otherinfo);
					//76 - Резервирование будет отключено по расписанию
					$rescode = $strings[76].' ' . $otherinfoA[75];
				} else {
					//77 - Резервирование будет отключено в течении нескольких минут
					$rescode = $strings[76];
				}
			}
		}
		
		
		$sql = "INSERT INTO actions VALUES ('START_RESERVING','".mysql_real_escape_string($_SESSION['login'])."','".$reserving.'|'.$rdate."','','$uid');";
		mysql_query($sql,$mysql);
		
		$sql = "UPDATE `stat` SET `reservingnext`='".$reserving."' WHERE user_name='".mysql_real_escape_string($_SESSION['login'])."';";
		mysql_query($sql,$mysql);		
		
		MakeActivity($mysql);
		
		$result['out'] = '
						'.menuManager('two',array("./reserving.php",$strings[28])).'
						<div class="content">
							<div class="two">
								<i class="fas fa-clock"></i>
								<div class="i m3">'.$rescode.'</div>
							</div>
						</div>
						<script>Refresh("reserving.php")</script>';
		} else {
			# Если данные не передавались, то выводится форма
			$sql = "SELECT `reserving`, `reservingnext`, `paytime` FROM `stat` WHERE user_name='".mysql_real_escape_string($_SESSION['login'])."';";
			$sql_arr = mysql_fetch_row(mysql_query($sql,$mysql));
	
			$btn = ''; $btn2 = '';
			if (($result['reservingnext']=='0')&&($result['reserving']=='1')){
				$otherinfo=$result['otherinfo'];
				$otherinfoA=explode('||',$otherinfo);
				//$ReservStopDate=date('d.m.Y H:i', (int)$otherinfoA[75] + (int)$MinTime * 86400);
				//78 - Запланировано отключение
				$btn2 = '<div class="m1">'.$strings[78].' ' . $otherinfoA[75] . '.</div>';
				
			}
	
			if ($sql_arr[1]=='-1') {
			
				#Добавить время отл/вкл. рещервирования
				if ($sql_arr[0]=='1') {
					//79 - Отключить резервирование
					//80 - Отключить резевирование абонентской линии?
						if ($OnFeeExecute=='1') {
							//81 - Отключение услуги просиходит в конце расчётного периода: %s, но не ранее %s %s с момента активации.
							$btn .='<div class="m1">'.sprintf($strings[81],$sql_arr[2],$MinTime,declension($MinTime,$strings[447],$strings[448], $strings[448])).'</div>';
						}else {
							//82 - Отключение услуги просиходит не ранее %s %s с момента активации.
							$btn .='<div class="m1">'.sprintf($strings[82],$MinTime,declension($MinTime,$strings[447],$strings[448], $strings[448])).'</div>';
						}
					$btn.=$btn2.'<button type="button" name="go" class="m3" onclick="Confirm(this,\''.$strings[80].'\')">'.$strings[79].'</button>
						<input type="hidden" name="reservingme" id="reservingme" value="0" />';
				} else {
					//83 - Включить резервирование
					//84 - Активировать резевирование абонентской линии?
					//85 - Стоимость подключения услуги
					//86 - Максимальный срок резервирования
					//87 - Минимальный срок резервирования
					//88 - Дней
					//394 - Абонентская плата
					$btn.='
						<div class="m1">'.$strings[85].' '.$Price.' '.$GLOBALS['curr'].'</div>
						<div class="m1">'.$strings[394].' '.$Price2.' '.$GLOBALS['curr'].'</div>
						<div class="m1">'.$strings[86].' '.$Time.' '.declension($Time,$strings[446],$strings[447], $strings[448]).'.</div>
						<div class="m1">'.$strings[87].' '.$MinTime.' '.declension($MinTime,$strings[446],$strings[447], $strings[448]);
						if ($OnFeeExecute=='1') {
							$btn .=', '.$strings[89].'.</div>
							<div class="m1">'.$strings[90].' '.$sql_arr[2].'</div>';
						} else 
							$btn.='  .</div>';
						if ($reserving_can_set_date==1){
														
							$D=date('Y', $otherinfoA[105]);						
							$DstDate=date('Y-m-d',(int)$otherinfoA[105]);
							if (((int)$D <2000) || ((int)$D >2099)){$DstDate=date("Y-m-d",  time());}
							//echo($DstDate);exit;
							$btn.='
						<div class="m1">'.$strings[504].' <input type="date" name="rdate" value="'.$DstDate.'" style=""></div>';
						}
						$btn.=$btn2.'
						<button type="button" name="go" class="m3" onclick="Confirm(this,\''.$strings[84].'\')">'.$strings[83].'</button>
						<input type="hidden" name="reservingme" id="reservingme" value="1" />';
				}
			} elseif($sql_arr[1]=='0'){
				//91 - Активировать резевирование абонентской линии?
				//83 - Включить резервирование
						//85 - Стоимость предоставления услуги
						if ($sql_arr[1]<>$sql_arr[0]){
							$btn.= '<div class="m1">'.$strings[85].': '.$Price.' '.$GLOBALS['curr'].'</div>';
						}
						$btn.= '<div class="m1">'.$strings[86].' '.$Time.' '.declension($Time,$strings[446],$strings[447], $strings[448]).'.</div>
								<div class="m1">'.$strings[87].' '.$MinTime.' '.declension($MinTime,$strings[446],$strings[447], $strings[448]);
						if ($OnFeeExecute=='1') {
							//89 - но не меньше расчётного периода
							$btn.= ', '.$strings[89].'.</div>';
							if ($sql_arr[1]<>$sql_arr[0]){
								//90 - Активация услуги просиходит в конце расчётного периода
								$btn.= '<div class="m1">'.$strings[90].' ...</div>';
							}
						} else
							$btn.='.</div>';
					$btn.=$btn2.'<button type="button" name="go" class="m3" onclick="Confirm(this,\''.$strings[91].'\')">'.$strings[83].'</button>
							<input type="hidden" name="reservingme" id="reservingme" value="1" />';
			} elseif($sql_arr[1]=='1') {
				//79 - Отключить резервирование
				//80 - Отключить резевирование абонентской линии?
						if ($OnFeeExecute=='1') {
							//81 - Отключение услуги просиходит в конце расчётного периода: %s, но не ранее %s %s с момента активации.
							$btn .='<div class="m1">'.sprintf($strings[81],$sql_arr[2],$MinTime,declension($MinTime,$strings[447],$strings[448], $strings[448])).'</div>';
						}else {
							//82 - Отключение услуги просиходит не ранее %s %s с момента активации.
							$btn .='<div class="m1">'.sprintf($strings[82],$MinTime,declension($MinTime,$strings[447],$strings[448], $strings[448])).'</div>';
						}
					$btn.=$btn2.'<button type="button" name="go" class="m3" onclick="Confirm(this,\''.$strings[80].'\')">'.$strings[79].'</button>
							<input type="hidden" name="reservingme" id="reservingme" value="0" />';
			}
			
			
			$ReservingComment=$strings[430];
			if (strlen($otherinfoA[108])>0){
				$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($otherinfoA[108])."' and `object_type` = 3;";
				$res = mysql_query($sql,$GLOBALS ["mysql"]);
					
				if (mysql_num_rows($res)>0){
							
					$row = mysql_fetch_array($res);
					$JSON=json_decode($row[0],true);
					
					if (isset($JSON['NameLNG'])){
						$SvcName = $JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']];
					} else {
						$SvcName = '';
					}
					
					if (isset($JSON['CommentLNG'])){
						$SvcComment = $JSON['CommentLNG'][$_COOKIE['MikroBILL_WEB_Language']];
					} else {
						$SvcComment = '';
					}
					
					
					if (strlen($SvcName)>0){
						$name = $SvcName;
					}
					if (strlen($SvcComment)>0){
						$ReservingComment = $SvcComment;
					}
					
				}
			}
			
			
				//86 - Стоимость предоставления услуги
				//430 - ... описание резервирования...
				//431 - Параметры услуги
			$result['out'] = '
						'.menuManager('two',array("./reserving.php",$strings[28])).'
						<div class="content">
							<div class="two">
								<i class="fas fa-clock"></i>
								<div class="h3 m3">'.$strings[70].'</div>
								<div class="m2">'.$ReservingComment.'</div>
								<div class="i m3">'.$strings[431].':</div>
								<form action="./reserving.php" method="POST">
									'.$btn.'
								</form>
								</div>
							</div>
						</div>';
			}
	mysql_close($mysql);
	return parse_template($result, './template/default.php',NULL,FALSE,true);	
}




	
	
# Редактирование пользовательских данных
function changeDetails() 	
{
	$result = getuserinfo();
		
	$strings=$GLOBALS['strings'];

	//92 - Изменение личных данных
	$result['page_title'] = $strings[92];
	
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');


	# Если пользователь передал данные на сервер, то они обрабатываются и добавляются в базу, а так же, выводится сообщение
	if ( (isset($_POST['go'])) || (isset($_POST['go2']))  || (isset($_POST['go3']))) {
		
		if (isset($_POST['go'])) {
		
			$mail = (empty($_POST['mail']))?' ':mysql_real_escape_string($_POST['mail']);
			$Telegram = (empty($_POST['Telegram']))?' ':mysql_real_escape_string($_POST['Telegram']);
			$skype = (empty($_POST['skype']))?' ':mysql_real_escape_string($_POST['skype']);

			$details = "||$mail||$Telegram||$skype";
			$uid = uniqid("");
			//93 - Данные будут изменены в течение пяти минут
			$rescode = $strings[93]; 
			$sql = "INSERT INTO `actions` VALUES ('SET_DETAILS','".mysql_real_escape_string($_SESSION['login'])."','".$details."','','$uid');";
			mysql_query($sql,$mysql);
		} 
		
		if (isset($_POST['go2'])) {
			$phone = (empty($_POST['phone']))?' ':mysql_real_escape_string($_POST['phone']);
			$code = (empty($_POST['code']))?' ':mysql_real_escape_string($_POST['code']);
			
			$sql = "SELECT * FROM `actionslog` WHERE value2='".$_SESSION['shortguid']."' and `action` = 'GET_SMS_CODE' ORDER by dateday DESC LIMIT 1;";
			$ret = mysql_fetch_array(mysql_query($sql,$mysql));
			$codeExt=$ret[5];
			if (strlen($codeExt)>3){
				
				if ($codeExt == $code) {
					$uid = uniqid("",true);
					//93 - Данные будут изменены в течение пяти минут
					$rescode = $strings[93]; 
					$sql = "INSERT INTO `actions` VALUES ('SET_DETAILS2','".mysql_real_escape_string($_SESSION['login'])."','".$phone."','','$uid');";
					mysql_query($sql,$mysql);
				} else {
					//94 - Неверный код подтверждения!
					$rescode = $strings[94]; 
				}
			}
		}
		
		if (isset($_POST['go3'])) {
		
			$mac = (empty($_POST['mac']))?' ':mysql_real_escape_string($_POST['mac']);
			
			$details = "$mac";
			$uid = uniqid("");
			//93 - Данные будут изменены в течение пяти минут
			$rescode = $strings[93]; 
			$sql = "INSERT INTO `actions` VALUES ('SET_MAC','".mysql_real_escape_string($_SESSION['login'])."','".$details."','','$uid');";
			mysql_query($sql,$mysql);
		} 
		
		MakeActivity($mysql);
			
		//95 - Изменение данных
		$result['out'] = '
					'.menuManager('three',array("./changepi.php",$strings[30])).'
					<div class="content">
						<div class="three">
							<div class="i m3">'.$rescode.'</div>							
						</div>
					</div>
					<script>Refresh("changepi.php")</script>';
		} else {
			# Если данные не передавались, то выводится форма
			$sql = "SELECT `pinfo`, `otherinfo`, `shortguid2`, `user_pswd` FROM stat WHERE user_name='".mysql_real_escape_string($_SESSION['login'])."';";
				$ret = mysql_fetch_array(mysql_query($sql,$mysql));
						
			$retA1 = explode("||",$ret[0]);
			$retA2 = explode("||",$ret[1]);
			$skype=filter($retA2[36]);
			$Telegram=filter($retA1[2]);
			$mail=filter($retA1[1]);
			$tel=filter($retA1[0]);
			$mac=filter($retA2[6]);
			$tlg=filter($retA2[130]);
			$max=filter($retA2[185]);
			$OnlySMS=filter($retA2[161]);
			
			
			
			// 596 - Нельзя изменить телефонный номер! Необходимо установить пароль на WEB-кабинет!
			$NoPass = (strlen($ret[3])<1)?('alert("' . $strings[596] . '"); return;'):('');


			//$mac=explode(';',$mac);
			//$mac=$mac[0];
			
			//95 - Изменение данных
			//96 - Изменить номер телефона
			//97 - Проверочный код
			//98  - Изменить телефон
			//99  - Введите новый номер телефона!
			//124 - Изменить данные
			//507 - Тел.
			//508 - Изменить MAC-адрес
			//509 - MAC
			//510 - Изменить MAC
			$result['out'] = '
					'.menuManager('three',array("./changepi.php",$strings[30])).'
					<div class="content">
						<div class="three">
							<i class="fas fa-cog"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="i m3">'.$strings[95].'</div>
							<form action="./changepi.php" method="POST">
								<div class="m2">
									<span style="display:inline-block;width:78px">WhatsApp</span><input type="text" name="skype" value="'.$skype.'" />
								</div>
								<div class="m2">
									<span style="display:inline-block;width:78px">Telegram</span><input type="text" name="Telegram" value="'.$Telegram.'" />
								</div>
								<div class="m2">
									<span style="display:inline-block;width:78px">Email</span><input type="text" name="mail" value="'.$mail.'" />
								</div>
								<button type="submit" name="go" class="m3" style="width:190px;">'.$strings[124].'</button>
								<div class="i m3">'.$strings[96].'</div>
								<div class="m2">
									<input type="hidden" name="oldtel" value="'.$tel.'">
									<span style="display:inline-block;width:78px">'.$strings[507].'</span><input type="text" name="phone" value="'.$tel.'" tag=""/>
								
								<br>';
								
								if (($GLOBALS['UseTelegram']=='1')&&($OnlySMS=='0')){
									$CheckedState='';$CheckedState2='';
									$TgDisplay='display: none;';
									$MaxDisplay='display: none;';
									if ($GLOBALS['TelegramOnlyFromPhone'] == '1') {
										$ret[2]='/start';
									}
									
									if ($tlg=='1'){$CheckedState='checked';$TgDisplay='';}
									$result['out'] .= '
									<span style="display:inline-block;width:78px;margin: 10px 0px;"></span>
									<input type="checkbox" ' . $CheckedState . ' name="usetelegram" id="usetelegram" onChange="ProcessTelegram();"><label for="usetelegram">'.$strings[526].'</label>
									<br>
									
									<div id="tginfo" name="tginfo" style="'.$TgDisplay.'margin: 10px 48px;">'.
									sprintf($strings[527],trim($retA2[126],'@'),$retA2[126],$ret[2]).
									'</div>
									';
									
									
									
									if ((mb_strtolower(trim($GLOBALS['curr'])) !='грн') && (mb_strtolower(trim($GLOBALS['curr'])) !='грн.')  && (mb_strtolower(trim($GLOBALS['curr'])) !='₴')){
										$MaxName = str_replace('Telegram', 'MAX', $strings[526]);
										$BotAdr=str_replace('https://t.me/', 'https://max.ru/', $strings[527]);
										if ($max=='1'){$CheckedState2='checked';$MaxDisplay='';}
										$result['out'] .= '
										<span style="display:inline-block;width:78px;margin: 10px 0px;"></span>
										<input type="checkbox" ' . $CheckedState2 . ' name="usemax" id="usemax" onChange="ProcessMax();"><label for="usemax">'.$MaxName.'</label>
										<br>
										
										<div id="maxinfo" name="maxinfo" style="'.$MaxDisplay.'margin: 10px 48px;">'.
										sprintf($BotAdr,trim($retA2[184],'@'),$retA2[184],$ret[2]).
										'</div>
										';
									}
								}
								
								
							$result['out'] .= '
								</div>
								<div id="showsms2" name="showsms2">
									<button type="button" alt="'.$strings[98].'" title="'.$strings[98].'" onClick="SendCode(this);" class="m3"  style="width:190px;">'.$strings[98].'</button>
								</div>
								<div id="smscode" name="smscode" style="display: none;">
									<div class="m2">
										<span style="display:inline-block;width:142px">'.$strings[97].'</span><input type="text" name="code" id = "code" style="width:106px"/> 
									</div>
										<button type="submit" name="go2" class="m3" style="width:190px;">'.$strings[98].'</button>
								</div>';
								
								if ($GLOBALS['ClientCanChangeMAC']==1){
								$result['out'] .= '
									<div class="i m3">'.$strings[508].'</div>
									<div class="m2">
										<span style="display:inline-block;width:78px">'.$strings[509].'</span><input type="text" name="mac" value="'.$mac.'" tag="" />
									</div>
									<button type="submit" name="go3" class="m3" style="width:190px;">'.$strings[510].'</button>';
								}
								$result['out'] .= '
							</form>
						</div>
					</div>';
			$result['out'] .= '
<script type="text/javascript">	

	function ProcessTelegram(){
		
		GetHTTP(\'api.php?action=USE_TELEGRAM&value=\' + encodeURIComponent(window.document.all.usetelegram.checked));
		
		if (window.document.all.usetelegram.checked == true){
			document.getElementById("tginfo").style.display = "block";
		} else {
			document.getElementById("tginfo").style.display = "none";
		}
	}
	
	function ProcessMax(){
		
		GetHTTP(\'api.php?action=USE_MAX&value=\' + encodeURIComponent(window.document.all.usemax.checked));
		
		if (window.document.all.usemax.checked == true){
			document.getElementById("maxinfo").style.display = "block";
		} else {
			document.getElementById("maxinfo").style.display = "none";
		}
	}

	function SendCode(id){
		if (window.document.all.oldtel.value == window.document.all.phone.value) {
			//alert("'.$strings[99].'");
			Confirm(id,\''.$strings[99].'\',true);
		} else{
			'.$NoPass.'
			
			GetHTTP(\'api.php?action=GET_SMS_CODE&value=\' + encodeURIComponent(window.document.all.phone.value));
			document.getElementById("smscode").style.display = "block";
			document.getElementById("showsms2").style.display = "none";
			//alert(\'Код отправлен!\');
		}
	}
					
	function GetHTTP(url) {
		var request=null;
		request=new XMLHttpRequest();
		request.open(\'GET\', url, false);
		request.send(null);
		return request.responseText;
	}
</script>';
			}
	mysql_close($mysql);
	return parse_template($result, './template/default.php',NULL,FALSE,true);	
	}

	
	function CheckProfile($result,$crumbs='',$bg='blue',$ignore=false){
	
		$strings=$GLOBALS['strings'];
		$otherinfoA=explode('||',$result['otherinfo']);
		$pinfoA=explode('||',$result['pinfo']);
		
		//($otherinfoA[84]==1)||
		if ((((strlen($pinfoA[0])<6) && (strlen($pinfoA[1])<3)) || (strlen($pinfoA[3])<4))&&($GLOBALS['NoAccessWithoutPassport']=='True')){
			// 46 - Информация
			// 398 - Для продолжения необходимо добавить персональные данные в профиль <br> Обратитесь в <a href="help.php">службу поддержки</a>.
			$result['out'] = '
				'.$crumbs.'
				<div class="content">
					<div class="'.$bg.'">
						<div class="h3 m4">'.$strings[46].'</div>
						<div class="i m3"><i class="shout fas fa-exclamation-circle"></i>'.$strings[398].'</div>
					</div>
				</div>
				<script>Refresh("help.php",6)</script>';

			echo parse_template($result, './template/default.php');
			exit();
	} elseif ($GLOBALS['UseAutoRecheckPassports']=='1') {
		
		if ($otherinfoA[187]=='1'){
			// 656 - Доступ приостановлен! Необходимо обновить паспортные данные! <br> Обратитесь в <b><a href=\'help.php\' style=\'color: black;\'>службу поддержки</a></b>!
			
			$result['out'] = '
				'.$crumbs.'
				<div class="content">
					<div class="'.$bg.'">
						<div class="h3 m4">'.$strings[46].'</div>
						<div class="i m3"><i class="shout fas fa-exclamation-circle"></i>'.$strings[656].'</div>
					</div>
				</div>
				<script>Refresh("help.php",6)</script>';

			echo parse_template($result, './template/default.php');
			exit();
		}
		
	} else if (($GLOBALS['NoPayWithoutOferta']=='1')||($GLOBALS['NoInternetWithoutOferta']=='1')){
		
			if ($otherinfoA[148]!='1'){
				
				// 46 - Информация
				
				$Oferta_Informer = '';
				
				
				if ($GLOBALS['NoInternetWithoutOferta']=='1'){
					// 574 - Доступ приостановлен! Необходимо принять публичную оферту!
					$Oferta_Informer .= '<div class="info_red"><div><p style="width: 75%">'.sprintf($strings[574],'&nbsp<a href="oferta_show.php" target="_blank" style="color:white;">'.$strings[575].'</a>!</p>').'<button style="position: absolute; right: 10px;float: right;" onclick="PopUP()">'.$strings[577].'</button></div></div>';
				} else {
					$Oferta_Informer .= '<div class="info_red"><div><p style="width: 75%">'.sprintf($strings[651],'&nbsp<a href="oferta_show.php" target="_blank" style="color:white;">'.$strings[575].'</a>!</p>').'<button style="position: absolute; right: 10px;float: right;" onclick="PopUP()">'.$strings[577].'</button></div></div>';
				}
				
				// 576 - Принять публичную оферту?
				// 577 - Принять
				// 578 - Отклонить
				$PopUPvar="<div><div>".$strings[576]."</div><div class='b'><button type='button' name='go' onclick='GetHTTP(\\\"api.php?action=SET_OFERTA&value=\\\");Confirm();location.reload();'>".$strings[577]."</button><button type='button' onclick='Confirm()'>".$strings[578]."</button></div></div>";

				
				$result['out'] = '
					'.$crumbs.'
					<div class="content">
						<div class="'.$bg.'">
							<div class="h3 m4">'.$strings[46].'</div>
							<div class="i m3">'.$Oferta_Informer.'</div>
						</div>
					</div>
					<script>
						function PopUP(){
							document.body.innerHTML += "<div id=\'confirm_div\' name=\'confirm_div\' class=\'space confirm\' style=\'display:none;\'></div>";
							document.getElementById("confirm_div").style="block";
							document.getElementById("confirm_div").innerHTML="'.$PopUPvar.'";
						}
					</script>';

				echo parse_template($result, './template/default.php');
				exit();
			}
		}
	}
	
	
function SendMoney2(){

	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	// 483 - Пополнить баланс телефона
	$crumbs = menuManager('two',array("./sendmoney2.php",$strings[483]));	
	CheckProfile($result,$crumbs,'two');
	
	if (($GLOBALS['sendmoney2'] == "True") && (isset ($_REQUEST["toclient"])) && (isset ($_REQUEST["sendpass"]))) {
		
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
		
		$c=mysql_real_escape_string($_SESSION['login']);
		$c2=mysql_real_escape_string($_REQUEST['toclient']);
		$pass=mysql_real_escape_string($_REQUEST['sendpass']);
		$money=0;
		
		$result['out'] = '';
		
		if (is_numeric($_REQUEST['money'])) {
			$money=$_REQUEST['money'];
		} else {
			//488 - Введите корректную сумму!
			$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">';
			$result['out'] .= '<div class="i m3">'.$strings[488].'</div>
			<script>Refresh("sendmoney2.php")</script>';
			$result['out'] .= '</div>
						</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true);
		}
		
		$sql = "SELECT `ballance` ,`user_pswd` FROM `stat` WHERE `user_name` = '$c';";
			$res = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($res);
			$ballance = $row[0];
			$pass2 = $row[1];
			mysql_free_result($res);
		
		$bal = explode(" ",$ballance);
		$ballance = $bal[0];
						
			//100 - Пересылка средств
			$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">';			
			if ($pass==$pass2){
				$sql = "SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_PRICE2' 
				UNION 
				SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_COMMISSION2';";
				$res = mysql_query($sql,$mysql) or die (mysql_error());
				$row = mysql_fetch_array($res, MYSQL_NUM);
				$SendMoneyPrice = $row[0];
				$row = mysql_fetch_array($res, MYSQL_NUM);
				$SendMoneyCommission = $row[0];
				mysql_free_result($res);
		
				$money2 = $money + $SendMoneyPrice + $money * ($SendMoneyCommission / 100);
			
				if (intval($ballance) < intval($money2)) {
					//102 - Недостаточно средств для перевода!
					$result['out'] .= '<div class="i m3">'.$strings[102].'</div>
					<script>Refresh("sendmoney2.php")</script>';
				} else {
					$uid = uniqid("");
					$datetoday = date("Y-m-d H:i:s");
					
					$sql0 = "INSERT INTO actions VALUES('SEND_MONEY_TO_PHONE','$c||$c2','$money','$datetoday','$uid');";
					
					mysql_query($sql0,$mysql);
					
					MakeActivity($mysql);
				
					//103 - Деньги будут отосланы в течении нескольких минут!
					$result['out'] .= '<div class="i m3"><i class="shout fas fa-check-circle"></i>'.$strings[103].'</div>
					<script>Refresh("index.php?page=index")</script>';
					//exit();
				}
			} else {
				//388 - Неверный пароль!
				$result['out'] .= '<div class="i m3">'.$strings[388].'</div>
				<script>Refresh("sendmoney2.php")</script>';
			}
		
		$result['out'] .= '</div>
						</div>';
	} else {
	
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
	
			$sql = "SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_PRICE2' 
			UNION 
			SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_COMMISSION2';";
			$res = mysql_query($sql,$mysql) or die (mysql_error());
			$row = mysql_fetch_array($res, MYSQL_NUM);
			$sp1 = $row[0];
			$row = mysql_fetch_array($res, MYSQL_NUM);
			$sp2 = $row[0];
			mysql_free_result($res);
	
		// 483 - Пополнение мобильного телефона
		// 484 - Форма пополнения баланса мобильного телефона
		// 485 - Номер телефона получателя
		// 486 - Деньги будут списаны со счёта услуги Интернет.
		// 278 - Введён неверный номер телефона!
		// 487 - Перевести деньги на счёт указанного телефона?
		
		$result['out']=$crumbs.'
			<div class="content">
				<div class="two">
					<i class="fas fa-hand-holding-usd"></i>
					<div class="h3 m4">'.$strings[483].'</div>
					<div class="i m3">'.$strings[484].':</div>
					<form action="sendmoney2.php" method="POST" name="sendform" id="sendform">
						<div class="m2">
							<input type="text" name="toclient" id="toclient" value="" placeholder="'.$strings[485].'">
						</div>
						<div class="m2">
							<input type="text" name="money" id="money" value="" placeholder="'.$strings[420].'" autocomplete="off">
						</div>
						<div class="m2">
							<input type="password" name="sendpass" id="sendpass" value="" placeholder="'.$strings[421].'" autocomplete="off">
						</div>
						<div class="i m3">'.$strings[422].' '.$sp1.' '.$GLOBALS ['curr'].', '.$strings[423].' '.$sp2.'%</div>
						<div class="i m3"><i class="shout fas fa-exclamation-circle"></i>'.$strings[486].'</div>
							<button type="button" onClick="SendMoney2(this)" class="m3">
								<!-- <img src="./img/wallet.png" style="vertical-align: middle"> -->
								'.$strings[107].'
							</button>
					</form>
				</div>
			</div>
			
			<script type="text/javascript">
			function SendMoney2(id){
				if (window.document.all.toclient.value.length != 11) {
					Confirm(id,\''.$strings[278].'\',true);
					return;
				}
				if (window.document.all.money.value.length == 0) {
					Confirm(id,\''.$strings[109].'\',true);
					return;
				}
				Confirm(id,\''.$strings[487].'\');
			}
		</script>';
	}
	
	return parse_template($result, './template/default.php',NULL,FALSE,true);
	
}
	

function SendMoney(){

	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	//100 - Пересылка средств
	$result['page_title'] = $strings[100];

	//CheckProfile($result);

	$crumbs = menuManager('two',array("./sendmoney.php",$strings[18]));	
	CheckProfile($result,$crumbs,'two');	

	if (($GLOBALS['sendmoney'] == "True") && (isset ($_REQUEST["toclient"])) && (isset ($_REQUEST["sendpass"]))) {
		
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
		
		$c=mysql_real_escape_string($_SESSION['login']);
		$c2=mysql_real_escape_string($_REQUEST['toclient']);
		$pass=mysql_real_escape_string($_REQUEST['sendpass']);
		$money=0;
		if (is_numeric($_REQUEST['money'])) {
			$money=$_REQUEST['money'];
		} else {
			//488 - Введите корректную сумму!
			$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">';
			$result['out'] .= '<div class="i m3">'.$strings[488].'</div>
			<script>Refresh("sendmoney.php")</script>';
			$result['out'] .= '</div>
						</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true);
		}
		
		$sql = "SELECT `ballance` ,`user_pswd` FROM `stat` WHERE `user_name` = '$c';";
			$res = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($res);
			$ballance = $row[0];
			$pass2 = $row[1];
			mysql_free_result($res);
		
		$bal = explode(" ",$ballance);
		$ballance = $bal[0];
		
		if (strlen($c2)<$GLOBALS['ClientContractLen']){
			for ($i = strlen($c2); $i <$GLOBALS['ClientContractLen']; $i++) {
				$c2 = '0' . $c2;
			}
		}
		
		$sql = "SELECT `contract` FROM `stat` WHERE `contract` = '$c2';";
		
			$res = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($res);
			$contract = $row[0];
			
			mysql_free_result($res);
					
			//100 - Пересылка средств
			$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">';
								
		if (strlen($contract)<1){
			//101 - Получатель не найден!
			$result['out'] .= '<div class="i m3">'.$strings[101].'</div>
			<script>Refresh("sendmoney.php")</script>';
		} else {
			
			if ($pass==$pass2){
				$sql = "SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_PRICE'"; 
				 
				
				$res = mysql_query($sql,$mysql) or die (mysql_error());
				$row = mysql_fetch_array($res, MYSQL_NUM);
				$SendMoneyPrice = $row[0];
				
				$sql = "SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_COMMISSION';";
				$res = mysql_query($sql,$mysql) or die (mysql_error());
				$row = mysql_fetch_array($res, MYSQL_NUM);
				
				$SendMoneyCommission = $row[0];
				mysql_free_result($res);
		
				$money2 = $money + $SendMoneyPrice + $money * ($SendMoneyCommission / 100);
			
				if (intval($ballance) < intval($money2)) {
					//102 - Недостаточно средств для перевода!
					$result['out'] .= '<div class="i m3">'.$strings[102].'</div>
					<script>Refresh("sendmoney.php")</script>';
					//exit();
				} else {
					$uid = uniqid("");
					$datetoday = date("Y-m-d H:i:s");
					
					$sql0 = "INSERT INTO actions VALUES('SEND_MONEY','$c||$c2','$money','$datetoday','$uid');";
					//$sql = "INSERT INTO actionslog VALUES('$datetoday','SEND_MONEY','$c||$c2','$money','$uid','$c');";
				
					mysql_query($sql0,$mysql);
					//mysql_query($sql,$mysql);
					MakeActivity($mysql);
				
					//103 - Деньги будут отосланы в течении нескольких минут!
					$result['out'] .= '<div class="i m3"><i class="shout fas fa-check-circle"></i>'.$strings[103].'</div>
					<script>Refresh("index.php?page=index")</script>';
					//exit();
				}
			} else {
				//388 - Неверный пароль!
				$result['out'] .= '<div class="i m3">'.$strings[388].'</div>
				<script>Refresh("sendmoney.php")</script>';
			}
		}
		$result['out'] .= '</div>
						</div>';
	} else {
		//100 - Пересылка средств
		//104 - Получатель
		//105 - (укажите номер договора)
		//106 - Сумма
		//107 - Отправить
		//108 - Введите договор клиента, кому необходимо отослать деньги!
		//109 - Введите сумму перевода!
		//110 - Передать деньги выбранному клиенту?
		//352 - Пароль
		//387 - от WEB-кабинета
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
	
			$sql = "SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_PRICE';";
			
			$res = mysql_query($sql,$mysql) or die (mysql_error());
			$row = mysql_fetch_array($res, MYSQL_NUM);
			$sp1 = $row[0];
			
			$sql = "SELECT `workparams`.`param_value` FROM `workparams` WHERE `workparams`.`param_name` = 'SEND_MONEY_COMMISSION';";
			$res = mysql_query($sql,$mysql) or die (mysql_error());
			
			
			$row = mysql_fetch_array($res, MYSQL_NUM);
			$sp2 = $row[0];
			mysql_free_result($res);

		//419 - Номер договора получателя
		//420 - Сумма перевода
		//421 - Ваш пароль
		//422 - Стоимость перевода
		//423 - комиссия от суммы
		//424 - Форма пополнения баланса другому абоненту
		$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">
								<i class="fas fa-hand-holding-usd"></i>
								<div class="h3 m4">'.$strings[100].'</div>
								<div class="i m3">'.$strings[424].':</div>
								<form action="sendmoney.php" method="POST" name="sendform" id="sendform">
									<div class="m2">
										<input type="text" name="toclient" id="toclient" value="" placeholder="'.$strings[419].'">
									</div>
									<div class="m2">
										<input type="text" name="money" id="money" value="" placeholder="'.$strings[420].'" autocomplete="off">
									</div>
									<div class="m2">
										<input type="password" name="sendpass" id="sendpass" value="" placeholder="'.$strings[421].'" autocomplete="off">
									</div>
									<div class="i m3">'.$strings[422].' '.$sp1.' '.$GLOBALS ['curr'].', '.$strings[423].' '.$sp2.'%</div>
										<button type="button" onClick="SendMoney(this)" class="m3">
											<!-- <img src="./img/wallet.png" style="vertical-align: middle"> -->
											'.$strings[107].'
										</button>
								</form>
							</div>
						</div>
		
		<script type="text/javascript">
			function SendMoney(id){
				if (window.document.all.toclient.value.length < 3) {
					Confirm(id,\''.$strings[108].'\',true);
					return;
				}
				if (window.document.all.money.value.length == 0) {
					Confirm(id,\''.$strings[109].'\',true);
					return;
				}
				Confirm(id,\''.$strings[110].'\');
			}
		</script>
		';
	}
	
	return parse_template($result, './template/default.php',NULL,FALSE,true);
}
	
	

function ShowNews($number=false){
	
	if (!isset($_SESSION['auth'])){
		$_SESSION['auth']=false;
	}	
	
	if (!$_SESSION['auth']){exit();}
	
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	//111 - Новости
	$result['page_title'] = $strings[111];

	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
	
	$sql = "SELECT tarif_guid, group_guid, otherinfo FROM stat WHERE user_name = '".mysql_real_escape_string($result['user_name'])."';";
	$res = mysql_query($sql,$mysql);
	$row = mysql_fetch_array($res);
	$tarif_guid = $row[0];
	$group_guid = $row[1];
	$otherinfo=explode('||', $row[2]);
	mysql_free_result($res);
	
	$FindedSVC = array();
	$cSVC=explode('/*',$otherinfo[12]);
	foreach ($cSVC as &$CurSVC) {
		$SVC=explode('|',$CurSVC);
		$FindedSVC[]=$SVC[0];
	}
	
	
	if (isset($_REQUEST["id"])) {
	
		$id=mysql_real_escape_string($_REQUEST["id"]);
				
		$mquery = mysql_query("SELECT * FROM `news` WHERE `id` = '".$id."' and `isvisible` = 1;", $mysql);
		$row = mysql_fetch_array($mquery);
				
		$pass = false;
		$a=explode("||",$row[7]);
		foreach ($a as &$value) {
			if (strlen($value)>0){
				if (($value == $tarif_guid) || ($value == $group_guid) || ($value=='12211221122112') || ((strlen(array_search($value,$FindedSVC)))>0)) {
					$pass = true;
					break;
				}
			}
		}

		if ($pass == true){	
				$result['out'] = '
					'.menuManager('five').'
					<div class="content">
						<div class="five">
							<i class="fas fa-envelope-open"></i>
							<div class="h3 m4">'.htmlspecialchars($row[2]).'</div>
							<div class="m3">';
				if (file_exists('./news/'.$row[5].'.html')) {
					$result['out'] .= file_get_contents(str_replace(array(':','..'),'','./news/'.$row[5].'.html'));
				
					mysql_query("UPDATE `news` SET `views`=`views`+1 WHERE `id` = '".$id."';", $mysql);
				}
				else 	
					$result['out'] .= 'Error load news...';
				$result['out'] .= '
							</div>
						</div>
					</div>';
		}
		$i=1;
		
	} else {
	
		if ($GLOBALS['ShowNews']!='True') {
			if($number) return '';
			echo "<script>Refresh('index.php?page=index',0)</script>";
			exit();
		}
		
		$mquery = mysql_query("SELECT * FROM news WHERE `isvisible` = 1 ORDER BY `actiondate` DESC;", $mysql);
				
		$result['out'] = '
					'.menuManager('five').'
					<div class="content news">';
		
		$i=0;
		while($row = mysql_fetch_array($mquery)) { 
				
			$pass = false;
			$a=explode("||",$row[7]);
			foreach ($a as &$value) {

				if (strlen($value)>0){
					
					if (($value == $tarif_guid) || ($value == $group_guid) || ($value=='12211221122112') || ((strlen(array_search($value,$FindedSVC)))>0)) {
						
						$pass = true;
						break;
					}
				}
			}
	
			if ($pass == true) {
				$news_date = date_format(date_create($row[1]),'d.m.Y H:i');
				$news_header = filter($row[2]);
				$id=$row[0];
				$result['out'].= '									
						<a href=\'news.php?id='.$id . '\' class="five"><span>'.$news_date.'</span> '.$news_header.'</a>';
				$i = $i + 1;
			}	
		} if($number) return $i;
	}

	$result['out'] .= '
					</div><div class="head" style="justify-content: center;">'.$GLOBALS['WEB_Basement'].'</div>';
		
	//113 - Нет новостей
	if ($i==0){
		$result['out'] = '
					'.menuManager('five').'
					<div class="content">
						<div class="five">
							<i class="fas fa-envelope-open"></i>
							<div class="h3 m4">'.$strings[111].'</div>
							<div class="i m3">'.$strings[113].'</div>
						</div>
					</div><div class="head" style="justify-content: center;">'.$GLOBALS['WEB_Basement'].'</div>'; 
	}	
	
	return parse_template($result, './template/default.php',NULL,FALSE,true);
}
	
	
	
# Турбо режим
function setturbo() 
{
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	//114 - Ускорение
	$result['page_title'] = $strings[114];
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
		$mquery = mysql_fetch_array(mysql_query("SELECT * FROM tarifs WHERE tarif_name='".mysql_real_escape_string($result['tarif'])."'"));
	
	
	# Если пользователь нажал кнопку, отправляем запрос на сервер
	if (isset($_POST['go'])) {
	
		$datetoday = date("Y-m-d H:i:s");
		$sql1 = "INSERT INTO actionslog VALUES ('".$datetoday."','turbo_button','".mysql_real_escape_string($_SESSION['login'])."','','','".mysql_real_escape_string($_SESSION['login'])."');";
		
		$uid = uniqid();
		$sql2 = "INSERT INTO actions VALUES ('TURBO_BUTTON','".mysql_real_escape_string($_SESSION['login'])."','".$datetoday."','','".$uid."');";
		
		$sql3 = "UPDATE stat SET turbo='True' WHERE shortguid='".mysql_real_escape_string($_SESSION['shortguid'])."';"; 
		
			mysql_query($sql1,$mysql) or die('q1');
			mysql_query($sql2,$mysql) or die('q2');
			mysql_query($sql3,$mysql) or die('q3');
		
		
		MakeActivity($mysql);
		
		//115 - Активация режима ускорения
		//116 - Режим ускорения успешно установлен. Ускорение станет активно втечение 5 минут.
		$result['out'] = '
						'.menuManager('two',array("./startturbo.php",$strings[114])).'
						<div class="content">
							<div class="two">
								<div class="i m3">'.$strings[116].'</div>
							</div>
						</div>
						<script>Refresh("startturbo.php")</script>';
		
		# Если не нажал, создаем форму
		} else {
		if ($mquery['turbo_enabled'] === 'True') {
		
			$sql = "SELECT user_name, turbo, turboenabled, tarif, state, promisepayenabled, promisepay, turboisallowed, otherinfo FROM stat WHERE user_name = '".mysql_real_escape_string($_SESSION['login'])."'";
				$mquer = mysql_query($sql,$mysql);
				$row = mysql_fetch_array($mquer);
				
				$TurboNowRuning = $row[1];
				$TurboEnabled = $row[2];
				$tarif = $row[3];
				$UserEnabled = $row[4];
				$PromisePayEnabled = $row[5];
				$PromisePayNowRuning = $row[6];
				$CurUserTurboAllowed = $row[7];
				$OtherInfo = $row[8];
				$OtherInfo = explode("||",$OtherInfo);
			
			$AutoPromisePay = $OtherInfo[10];
			$PromisePayCost = $OtherInfo[11];
			
			
			$row = $mquery;
			$tarif_name = $mquery['tarif_name'];
			$turbo_value = $mquery['turbo_value'];
			$turbo_len = $mquery['turbo_len'];
			$turbo_pause = $mquery['turbo_pause'];
			$turbo_cost = $mquery['turbo_cost'];
			$turbo_enabled_in_group = $mquery['turbo_enabled'];
			//$promisepay_len_money = $mquery['promisepay_len_money'];
			//$promisepay_len_days = $mquery['promisepay_len_days'];
			//$promisepay_enabled_in_group = $mquery['promisepay_enabled_in_group'];
			//$promisepay_len = $mquery['promisepay_len'];
			$line = "";
			
		
			$TurboA=explode(' ', $turbo_len);
						
			$turbo_len = '';
			
			$times_values = array('сек.','мин.','час.','д.','лет');
			$times = seconds2times((int)$TurboA[0]*60);
			for ($i = count($times)-1; $i >= 0; $i--)
			{
				if ((int)$times[$i] != 0){$turbo_len .= $times[$i] . ' ' . $times_values[$i] . ' ';}
			}
			
					
			//117 - Режим ускорения даёт прибавку скорости
			//118 - продолжительностью
			//119 - Воспользоваться услугой ускорения можно не чаще чем один раз в 
			//85 - Стоимость подключения услуги
			//120 - Внимание!
			$line = $line.
					'<div class="m2">'.$strings[117].' '.$turbo_value.', '.$strings[118].' '.$turbo_len.'</div>';
				if ($turbo_cost != 0) {
					$line = $line.'
					<div class="m1">'.$strings[120].' '.$strings[85].' '.$turbo_cost.'</div>';
				}
				if (strlen($turbo_pause)>0){$line = $line.
					'<div class="m1">'.$strings[119].' ' .$turbo_pause. '</div>';
				}
			
			
			# Вывод формы только в случае, если можно активировать режим турбо
			if ($turbo_enabled_in_group == "True") {

				if ($TurboNowRuning == "True") {
					//121 - Ускорение включено!
					//122 - Активировать ускорение
					//123 - Активировать режим ускорения?
					$line = $line.'<div class="i m3"><i class="shout fas fa-check-circle"></i>'.$strings[121].'</div>';
					} else {
						if (($TurboEnabled == "True") and ($UserEnabled <> "Нет") ){
							$line = $line."
												<form action='startturbo.php' method='POST'>
													<button type='button' name='go' class='m3' onclick='Confirm(this,\"".$strings[123]."\")'>".$strings[122]."</button>
												</form>";
							} else {  
								if ($UserEnabled == "Нет") {$CurUserTurboAllowed = "False";}				
								if 	($CurUserTurboAllowed == "True") {
									//125 - Необходимо подождать!
									$line = $line.'<div class="i m3"><i class="shout fas fa-exclamation-circle"></i>'.$strings[125].'</div>';
									} else {
										//126 - Недостаточно средств!
										$line = $line.'<div class="i m3"><i class="shout fas fa-exclamation-circle"></i>'.$strings[126]."</div>";
										}	
								}
						}
					}
			
			//127 - Ускорение невозможно для вашего тарифа!
			//418 - Увеличение скорости
			$result['out'] = '
				'.menuManager('two',array("./startturbo.php",$strings[114])).'
				<div class="content">
					<div class="two">
						<i class="fas fa-rocket"></i>
						<div class="h3 m4">'.$strings[418].'</div>
						'.$line.'
					</div>
				</div>';
			} else {
				# Если режим турбо запрещен для пользователя, то выводим сообщение вместо формы
				$result['out'] = '
					'.menuManager('two',array("./startturbo.php",$strings[114])).'
					<div class="content">
						<div class="two">
							<div class="h3 m4">'.$strings[418].'</div>
							<div class="i m3"><i class="shout fas fa-times-circle"></i>'.$strings[127].'</div>
						</div>
					</div>
					<script>Refresh("index.php?page=index")</script>';
				}
			}
	mysql_close($mysql);
	return parse_template($result, './template/default.php',NULL,FALSE,true);	
	}
	
	function seconds2times($seconds)
{
    $times = array();
    
    $count_zero = false;
    
    $periods = array(60, 3600, 86400, 31536000);
    
    for ($i = 3; $i >= 0; $i--)
    {
        $period = floor($seconds/$periods[$i]);
        if (($period > 0) || ($period == 0 && $count_zero))
        {
            $times[$i+1] = $period;
            $seconds -= $period * $periods[$i];
            
            $count_zero = true;
        }
    }
    
    $times[0] = $seconds;
    return $times;
}

function tmp_access(){
	
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	
	//540 - Временный доступ
	$result['page_title'] = $strings[540];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
	
	$sql = "SELECT `otherinfo` FROM `stat` WHERE `shortguid` = '".mysql_real_escape_string($_SESSION['shortguid'])."'";
		
	$query=mysql_query($sql,$mysql);
	$row = mysql_fetch_array($query);
	$OtherInfo = $row[0];	

	$OtherInfo = explode("||",$OtherInfo);
	
	if (isset($_POST['go'])) {
		
		$GUID = uniqid();
		$sql = "INSERT INTO `actions` VALUES('FRIENDLY_ACCESS','".mysql_real_escape_string($_SESSION['login'])."','false','','$GUID');";
		mysql_query($sql, $mysql) or die(mysql_error());
			
		
		$OtherInfo[133] = '1';
		$newValue = mysql_real_escape_string(implode("||",$OtherInfo));
		$sql = "UPDATE `stat` SET `otherinfo`='".$newValue."' where  shortguid = '".mysql_real_escape_string($_SESSION['shortguid'])."';";
		mysql_query($sql, $mysql) or die(mysql_error());
		MakeActivity($mysql);
		
		//543 - Временный доступ активирован. Изменения вступят в силу в течении минуты.
		$result['out'] = '
							'.menuManager('two',array("./promisepay.php",$strings[128])).'
							<div class="content">
								<div class="two">
								<i class="fas fa-shopping-bag"></i>
									<div class="i m3">'.$strings[543].'</div>
								</div>
							</div>
							<script>Refresh("index.php?page=index")</script>';
		
	} else {
		
		$sql = "SELECT `temporary_access_length`,`temporary_access_name`, `temporary_access_description` FROM tarifs WHERE tarif_name='".mysql_real_escape_string($result['tarif'])."'";
		$mquery = mysql_fetch_array(mysql_query($sql,$mysql));
		
		//539 - Доступ на короткое время
	//$TemporaryAccessName = $strings[539];
	//$res = mysql_query('SELECT `temporary_access_name` FROM `tarifs` WHERE `tarif_guid` = '.mysql_real_escape_string($_SESSION['tarif_guid']).';',$mysql);
	//if (mysql_num_rows($res)>0){
	//	$row = mysql_fetch_row($res);
	//	$TemporaryAccessName = $row[0];
	//}
		
		if ($OtherInfo[169]=='1'){
			
			// 542 - Активировать временный доступ
			$Tmp_access_btn = '
								<form action="tmp_access.php" method="POST">
									<button type="submit" name="go" class="m3">'.$strings[542].'</button>
								</form>';
			
			
			// 541 - Вы можете активировать кратковременный доступ для пополнения счёта. Срок дейсвия услуги: %s минут.
			$result['out'] = '
									'.menuManager('two',array("./tmp_access.php",$mquery['temporary_access_name'])).'
									<div class="content">
										<div class="two">
											<i class="fas fa-shopping-bag"></i>
											<div class="h3 m4">'.$mquery['temporary_access_name'].'</div>
											<div class="m2">'.$mquery['temporary_access_description'].'<br>'.sprintf($strings[541],round($mquery['temporary_access_length'])).'</div>
											'.$Tmp_access_btn.'
										</div>
									</div>';
		} else {
			
			//601 - Временный доступ в данный момент недоступен!
			$result['out'] = '
							'.menuManager('two',array("./promisepay.php",$strings[128])).'
							<div class="content">
								<div class="two">
								<i class="fas fa-shopping-bag"></i>
									<div class="i m3">'.$strings[601].'</div>
								</div>
							</div>
							<script>Refresh("index.php?page=index")</script>';	
		}
	}
	return parse_template($result, './template/default.php',NULL,FALSE,true);
}

	
# Обещанный платеж
function setpromisepay() 
{

	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	$PromisePayCost = "";
	//128 - Обещанный платеж
	$result['page_title'] = $strings[128];
	
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
		
		$sql = "SELECT * FROM tarifs WHERE tarif_name='".mysql_real_escape_string($result['tarif'])."'";
		$mquery = mysql_fetch_array(mysql_query($sql,$mysql));
		
	# Если нажата кнопка обещанного платежа, то добавляются запросы в базу и выводятся сообщения
	if (isset($_POST['go'])) {
	
		$datetoday = date("Y-m-d H:i:s");
		
		$sql1 = "INSERT INTO actionslog VALUES ('".$datetoday."','promise_pay','".mysql_real_escape_string($_SESSION['login'])."','','','".mysql_real_escape_string($_SESSION['login'])."');";
		$uid = uniqid();
		$sql2 = "INSERT INTO actions VALUES ('PROMISE_PAY','".mysql_real_escape_string($_SESSION['login'])."','".$datetoday."','','".$uid."');";
		$sql3 = "UPDATE stat SET promisepay='True' WHERE shortguid='".mysql_real_escape_string($_SESSION['shortguid'])."';"; 
		
			mysql_query($sql1,$mysql) or die('q1');
			mysql_query($sql2,$mysql) or die('q2');
			mysql_query($sql3,$mysql) or die('q3');
			MakeActivity($mysql);
		
		
		//129 - Активация обещанного платежа
		//130 - Обещанный платеж добавлен. Начисление произойдет в течение 5 минут.
		$result['out'] = '
							'.menuManager('two',array("./promisepay.php",$strings[128])).'
							<div class="content">
								<div class="two">
								<i class="fas fa-shopping-bag"></i>
									<div class="i m3">'.$strings[130].'</div>
								</div>
							</div>
							<script>Refresh("index.php?page=index")</script>';
		
		} else {
		
		
		# Если кнопка не нажата, готовится вывод формы
		if ($mquery['promisepay_enabled'] === 'True') {
		
			$sql = "SELECT user_name, turbo, turboenabled, tarif, state, promisepayenabled, promisepay, turboisallowed, otherinfo FROM stat WHERE user_name = '".mysql_real_escape_string($_SESSION['login'])."'";

				$mquer = mysql_query($sql,$mysql);
				$row = mysql_fetch_array($mquer);
				
				$TurboNowRuning = $row[1];
				$TurboEnabled = $row[2];
				$tarif = $row[3];
				$UserEnabled = $row[4];
				$PromisePayEnabled = $row[5];
				$promisepaynowruning = $row[6];
				$CurUserTurboAllowed = $row[7];
				$OtherInfo = $row[8];				

			
			$OtherInfo = explode("||",$OtherInfo);
			$AutoPromisePay = $OtherInfo[10];
			$PromisePayCost = $OtherInfo[11];
			
			$row = $mquery;
			$tarif_name = $row['tarif_name'];
			$turbo_value = $row['turbo_value'];
			$turbo_len = $row['turbo_len'];
			$turbo_pause = $row['turbo_pause'];
			$turbo_cost = $row['turbo_cost'];
			
			$promisepay_len_money = $row['promisepay_addmoney'];
			$promisepay_len_days = $row['promisepay_adddays'];
			$promisepay_enabled_in_group = $row['promisepay_enabled'];
			$promisepay_len = $row['promisepay_len'];
			
			// Срок действия ОП
			$PromiseP_Len = $OtherInfo[67];
			// Стоимость ОП
			$PromiseP_Cost = $OtherInfo[68];
			// Выдаваемая сумма
			$PromiseP_Summ = $OtherInfo[69];
			// Только раз в месяц
			$PromiseP_OnePerMonth = $OtherInfo[70];
			// Если заблокирован
			$PromiseP_IfBlocked = $OtherInfo[71];
			// Если оплатили больше
			$PromiseP_IfPay = $OtherInfo[72];
			// Возвращён быстро - нет оплаты
			$PromiseP_FastReturn = $OtherInfo[73];
			
			$line = "";
			$AutoCreditBtn = "";	
			$PromiseP_Out='';
			
			if (($PromiseP_OnePerMonth==1) || ($PromiseP_IfBlocked==1) || ($PromiseP_IfPay==1)){
				$N=1;
				//131 - Услугой «Обещанный платёж» можно воспользоваться
				$PromiseP_Out='<div class="i m3">'.$strings[131].':</div>';
				//132 - Если вы заблокированы по балансу.
				if ($PromiseP_IfBlocked==1){$PromiseP_Out.='<div class="m1">'.$N.'. '.$strings[132].'</div>'; $N +=1;}
				//133 - Только один раз в месяц.
				if ($PromiseP_OnePerMonth==1){$PromiseP_Out.='<div class="m1">'.$N.'. '.$strings[133].'</div>'; $N +=1;}
				//134 - Если после последней активации обещанного платежа пополнили баланс на сумму больше размера обещанного платежа.
				if ($PromiseP_IfPay==1){$PromiseP_Out.='<div class="m1">'.$N.'. '.$strings[134].'</div>'; $N +=1;}
					
			}
			
			//135 - Важно
			//136 - Стоимость подключения услуги
			$PromiseP_Out.='<div class="i m3">'.$strings[135].':</div>
								<div class="m1">- '.$strings[136].' '.$PromiseP_Cost.' '.$GLOBALS ['curr'].'</div>';
			//137 - Если обещанный платёж возращён за %s %s, стоимость подключения не взымается.
			if ($PromiseP_FastReturn>0){$PromiseP_Out.='<div class="m1">- '.sprintf($strings[137],''.$PromiseP_FastReturn.'',declension($PromiseP_FastReturn,$strings[446],$strings[447], $strings[448])).'</div>';}
			//138 - Период действия услуги: %s %s.
			//139 - Обещанный платёж даёт в долг
			//140 - Если по истечении срока дейтствия обещанного платежа вы не внесли плату за Интернет, предоставление услуг будет приостановлено.
			$PromiseP_Out.='<div class="m1">- '.sprintf($strings[138],''.$PromiseP_Len,declension($PromiseP_Len,$strings[446],$strings[447], $strings[448])).'</div>';
			
			if ($PromisePayEnabled == "True"){$PromiseP_Out.='<div class="m1">- '.$strings[139].' '.$PromiseP_Summ.' '.$GLOBALS ['curr'].'</div>';}
										
			$PromiseP_Out.='<div class="m1">- '.$strings[140].'</div>';
			
			
			# Если активирована кнопка автоматического платежа
	if (isset($_POST['autocredit_go'])) {
			
		if ($AutoPromisePay == 'false') {
			
			$GUID = uniqid();
			$datetoday =  date("Y-m-d H:i:s");
			$sql0 = "INSERT INTO actionslog VALUES('$datetoday','AUTOPROMISEPAY','".mysql_real_escape_string($_SESSION['login'])."','true','$GUID','".mysql_real_escape_string($_SESSION['login'])."');";
			$sql1 = "INSERT INTO actions VALUES('AUTOPROMISEPAY','".mysql_real_escape_string($_SESSION['login'])."','true','','$GUID');";
			$AutoPromisePay = 'true';
			
			$OtherInfo[10] = 'true';
			$newValue = mysql_real_escape_string(implode("||",$OtherInfo));
			$sql2= "UPDATE stat SET otherinfo='".$newValue."' where  shortguid = '".mysql_real_escape_string($_SESSION['shortguid'])."';";
		
				mysql_query($sql0, $mysql) or die(mysql_error());
				mysql_query($sql1, $mysql) or die(mysql_error());
				mysql_query($sql2, $mysql) or die(mysql_error());

		} else {
		
			$datetoday =  date("Y-m-d H:i:s");
			$GUID = uniqid();
			
			$sql0 = "INSERT INTO actionslog VALUES('$datetoday','AUTOPROMISEPAY','".mysql_real_escape_string($_SESSION['login'])."','false','$GUID','".mysql_real_escape_string($_SESSION['login'])."');";
			$sql1 = "INSERT INTO actions VALUES('AUTOPROMISEPAY','".mysql_real_escape_string($_SESSION['login'])."','false','','$GUID');";
			
			$OtherInfo[10] = 'false';
			$AutoPromisePay = 'false';
			$newValue = mysql_real_escape_string(implode("||",$OtherInfo));
			$sql2 = "UPDATE stat SET otherinfo='".$newValue."' where  shortguid = '".mysql_real_escape_string($_SESSION['shortguid'])."';";
			
				mysql_query($sql0, $mysql) or die(mysql_error());
				mysql_query($sql1, $mysql) or die(mysql_error());
				mysql_query($sql2, $mysql) or die(mysql_error());
			
		}
			
		MakeActivity($mysql);
		
		}
			//$PromisePayEnabled = "True";
			
			
			# Если обещанный платеж разрешен в группе...
			if ($promisepay_enabled_in_group == "True") {
				
				# и если активен в данный момент, выводим сообщение о его активности
				if ($promisepaynowruning == "True") {
					//141 - Обещанный платеж включен!
					$line = $line.'<div class="i m3"><i class="shout fas fa-check-circle"></i>'.$strings[141].'</div>';
					} else {
						
						# если не активен, но разрешен
						if ($PromisePayEnabled == "True"){
					
								//142 - Активировать обещанный платеж
								$line = $line."
												<form action='promisepay.php' method='POST'>
													<button type='submit' name='go' title='".$strings[142]."'class='m3'>".$strings[142]."</button>
												</form>"; 
							//		}
							} else {
								if (intval($result['ballance']) >= 0) {
									//143 - В данный момент невозможно активировать обещанный платёж.
									$line = $line.'<div class="i m3"><i class="shout fas fa-exclamation-circle"></i>'.$strings[143].'</div>';
									
									} else {
										# Если пользователю не доступен платеж
										//144 - Обещанный платеж Вам не доступен.
										$line = $line.'<div class="i m3"><i class="shout fas fa-times-circle"></i>'.$strings[144].'</div>';
										}
								}
						}
						# Если разрешен автоматический платеж, но он не активирован, выводим кнопку включения автоматического платежа
							if ($GLOBALS['AllowAutoCredit'] == 'True' and $AutoPromisePay == 'false')
								//145 - Автоматический обещанный платеж
								//146 - Использовать автоматический обещанный платеж
								$AutoCreditBtn = '
													<div class="h3 m4">'.$strings[145].'</div>
														<form action="promisepay.php" method="POST">
															<button type="submit" name="autocredit_go" class="m3">'.$strings[146].'</button>
														</form>';
						# Если разрешен автоматический платеж и активирован, выводим кнопку отключения автоматического платежа
							if ($GLOBALS['AllowAutoCredit'] == 'True' and $AutoPromisePay == 'true')
								//145 - Автоматический обещанный платеж
								//147 - Отключить автоматический обещанный платеж
								$AutoCreditBtn = '
												<div class="h3 m4">'.$strings[145].'</div>
													<form action="promisepay.php" method="POST">
														<button type="submit" name="autocredit_go" class="m3">'.$strings[147].'</button>
													</form>';
					}
			
			
			$P_Out='';
			
			//$GLOBALS['prpayintrotext'] = str_replace('%PROMISE_PAY_INFO%', $PromiseP_Out, $GLOBALS['prpayintrotext']);
			
			$PP_Info=$GLOBALS['prpayintrotext'];
			
			if ($GLOBALS['AllowAutoCredit'] == 'True'){
				$PP_Info = str_replace('#AUTO_PROMISED_PAY_INFO#',$GLOBALS['prpayintrotext2'],$PP_Info);
			} else {
				$PP_Info = str_replace('#AUTO_PROMISED_PAY_INFO#','',$PP_Info);
			}
			
			//130 - Активация обещанного платежа
			$result['out'] = '
							'.menuManager('two',array("./promisepay.php",$strings[128])).'
							<div class="content">
								<div class="two">
									<i class="fas fa-shopping-bag"></i>
									<div class="h3 m4">'.$strings[128].'</div>
									<div class="m2">'.$PP_Info.'</div>
									'.$PromiseP_Out.'
									'.$line.'
									'.$AutoCreditBtn.'
								</div>
							</div>';
			} else {
			
				$sql = "SELECT otherinfo FROM stat WHERE user_name = '".mysql_real_escape_string($_SESSION['login'])."'";

				
				$mquer = mysql_query($sql,$GLOBALS['mysql']) or die('123');
				$row = mysql_fetch_array($mquer);
				$OtherInfo=$row[0];
				$OtherInfo = explode("||",$OtherInfo);
				
				
				//130 - Активация обещанного платежа
				//144 - Обещанный платеж Вам не доступен.
				$result['out'] = '
							'.menuManager('two',array("./promisepay.php",$strings[128])).'
							<div class="content">
								<div class="two">
									<i class="fas fa-shopping-bag"></i>
									<div class="h3 m4">'.$strings[128].'</div>
									<div class="i m3"><i class="shout fas fa-times-circle"></i>'.$strings[144].'</div>
								</div>
							</div>';
				}
			}
		
	return parse_template($result, './template/default.php',NULL,FALSE,true);	
	}

# Дополнительные услуги
function additionalServices($service_type, $TopText, $BottomText)
{
	//error_reporting(E_ALL);
	$result = getuserinfo();
	$service_type=mysql_real_escape_string($service_type);
	$strings=$GLOBALS['strings'];
	$LinkID='';
	if ($service_type>1){
		$LinkID=$service_type;
	}
	$crumbs = menuManager('two',array((($service_type==0)?"./manageservices.php":"./managetv$LinkID.php"),(($service_type==0)?$strings[20]:$strings[149])));
	
	//148 - Дополнительные услуги
	//149 - Телевидение
	$result['page_title'] = ($service_type==0)?$strings[148]:$strings[149];
	
	$OtherInfo = $result['otherinfo'];
	$OtherInfoA = explode("||",$OtherInfo);	
	$LinkedSVCS = explode("/*",$OtherInfoA[12]);
	$BadSVCS = explode("/*",$OtherInfoA[83]);
	
	$AllServices=array();
	$tmpSVCS = explode("/*",$OtherInfoA[12]);
	foreach ($tmpSVCS as &$value) {
		$tmpSVCS2 = explode('|',$value);
		if (strlen($tmpSVCS2[0])>0){
			$AllServices[]=$tmpSVCS2[0];
		}
	}
	
	$serv='';
	$OrNeeded=false;
	foreach ($AllServices as &$value) {
		if ($OrNeeded){$serv .= ' or ';}
		$serv .= '`guid`='.$value;
		$OrNeeded=true;
	}
	if (strlen($serv)>0){
		$serv = 'or (`can_deactivate` = 1 and (' . $serv . '))';
	}
	
	$curr = $GLOBALS['curr'];
	
	$Services=array();
	$sql = "SELECT `id`,`object_data` FROM `system_objects` WHERE `object_type` = 3;";
	$res = mysql_query($sql,$GLOBALS["mysql"]) or die ('#5' . mysql_error());
	while($row = mysql_fetch_array($res)) { 
		$Services[]=array('ID' => $row[0],
						  'JSON' => json_decode($row[1],true));
	}
	

	if ((($GLOBALS['UseCustomServices2'] == 'True')&&($service_type==0)) || 
		((($GLOBALS['UseTV'] == 'True')||($GLOBALS['UseOmegaTV'] == 'True')||
		($GLOBALS['Use24hTV'] == 'True')||($GLOBALS['UseTrinitY'] == 'True')||
		($GLOBALS['UseSweetTV'] == 'True')||($GLOBALS['UseProstoTV'] == 'True')||
		($GLOBALS['UseMegogoTV'] == 'True')||($GLOBALS['UseIPTVPORTAL'] == 'True')||
		($GLOBALS['UseMoovi'] == 'True')||
		($GLOBALS['UseWinkTV'] == 'True')||($GLOBALS['UseTVIPmedia'] == 'True'))&&($service_type>0)) )
	{
		if (isset($_POST['go']))
		{
			
			$client = mysql_real_escape_string($_SESSION['login']);
			$value=$_POST['value'];
			$name=$_POST['name'];
			$DisablePlan=$_POST['disableplan'];
			$datetoday = date("Y-m-d H:i:s");
			$MyData = explode("||",mysql_real_escape_string($value));
			
			$good=false;
			$sql2 = "SELECT service_name, guid, paysize, payafterday, period, startcost, one_time_service, linked_guids, isvisible, stopcost FROM services WHERE `isvisible` = 1 $serv ORDER BY display_index ASC;";
			
			$res = mysql_query($sql2,$GLOBALS['mysql']);
		
				while($row = mysql_fetch_array($res)) { 
					//$name = $row[0];
					$id = $row[1];
					$paysize = $row[2];
					$payafterday = $row[3];
					$period = $row[4];
					$paysize2 = $row[5];
					$one_time_service =  $row[6];
					$linked_guids = $row[7];
					$stopcost = $row[9];
					
					if (($MyData[1]==$id) || (strlen($linked_guids)==0)){
						if (CheckService($linked_guids)){$good=true;break;}
					}
				}	
				
			if (!$good){return false;}

			
			$GUID = uniqid();		
			$sql0 = "INSERT INTO actionslog VALUES('$datetoday','ENABLE_SERVICE','$client','".mysql_real_escape_string($value)."||False','$GUID','".mysql_real_escape_string($_SESSION['login'])."');";
			$sql = "INSERT INTO actions VALUES('ENABLE_SERVICE','$client','".mysql_real_escape_string($value)."||False','','$GUID');";
			
				mysql_query($sql0);
				mysql_query($sql);
									
			
			if ($MyData[0]=="false") 
			{
				if (strlen($DisablePlan)==0) {
					$OtherInfoA[12] = str_replace($MyData[1], "", $OtherInfoA[12]);
					//150 - отключена
					$checkValue = $strings[150];
				} else {
					$OtherInfoA[12] = str_replace($MyData[1], $MyData[1]."|$DisablePlan|False", $OtherInfoA[12]);
					//151 - подключена
					$checkValue = $strings[151];
				}
			} 
			else 
			{
				$sum2=count($LinkedSVCS)-1;
				$finded=false;
				for($i=0; $i <= $sum2; $i++) 
				{	
					$LinkedSVCS_v = explode("|",$LinkedSVCS[$i]);
					if ($LinkedSVCS_v[0] == $MyData[1]) 
					{
						$finded=true;
						break;
					}
				}
				if ($finded==false)
				{
					$OtherInfoA[12] = $OtherInfoA[12] . "/*" . $MyData[1]; 
				}
				//151 - подключена
				$checkValue = $strings[151];
			}
			
			$NewValue = mysql_real_escape_string(implode("||",$OtherInfoA));
			
			$sql= "UPDATE stat SET otherinfo='$NewValue' where  user_name = '$client';";
				mysql_query($sql);
						
			MakeActivity($GLOBALS['mysql']);
			
			//148 - Дополнительные услуги
			//149 - Телевидение
			//152 - Услуга
			//153 - успешно
			$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">
								<!-- <i class="fas fa-shopping-bag"></i> -->
								<!-- <div class="h3 m4">'.(($service_type==0)?$strings[148]:$strings[149]).'</div> -->
								<div class="i m3">'.$strings[152].' &laquo;'.$name.'&raquo; '.$strings[153].' '.$checkValue.'.</div>';
			if (($MyData[0]=="false") && (strlen($DisablePlan)>2)) {
				//154 - Отключение услуги произойдёт по расписанию в 
				//$result['out'] .= '<div class="i m5">'.$strings[154].' '.$DisablePlan.'</div>';
				$WaitTime = '7';
			} else {
				//155 - Изменения вступят в силу через несколько минут.
				$result['out'] .= '<div class="i m5">'.$strings[155].'</div>';
				$WaitTime = '3';
			}
			$ServiceNum='';
			if ($service_type>1){
				$ServiceNum=$service_type;
			}
			$result['out'] .= '</div>
							</div>
						</div>
		   <script>Refresh("'.(($service_type==0)?'manageservices':'managetv'.$ServiceNum).'.php",'.$WaitTime.')</script>';
		}
		else
		{
			$sql = "SELECT `pinfo` FROM `stat` WHERE `shortguid` = " . mysql_real_escape_string($_SESSION['shortguid']);
			$res = mysql_query($sql,$GLOBALS['mysql']);
			$row = mysql_fetch_array($res);
			$personalinfo = explode("||",$row[0]);
	
			$GoodPersonal=0;
			
			if (
				(($GLOBALS['ClientCanActivateTV']!='True') && ($service_type==1)) ||
				(($GLOBALS['ClientCanActivateTV_Omega']!='True') && ($service_type==2)) ||
				(($GLOBALS['ClientCanActivateTV_24h']!='True') && ($service_type==3)) ||
				(($GLOBALS['ClientCanActivateTV_TrinitY']!='True') && ($service_type==4)) ||
				(($GLOBALS['ClientCanActivateTV_Sweet']!='True') && ($service_type==8)) ||
				(($GLOBALS['ClientCanActivateTV_Prosto']!='True') && ($service_type==5)) ||
				(($GLOBALS['ClientCanActivateTV_Megogo']!='True') && ($service_type==6)) ||
				(($GLOBALS['ClientCanActivateTV_IPTVPORTAL']!='True') && ($service_type==7)) ||
				(($GLOBALS['ClientCanActivateTV_Wink']!='True') && ($service_type==10)) ||
				(($GLOBALS['ClientCanActivateTV_TVIPmedia']!='True') && ($service_type==11))||
				(($GLOBALS['ClientCanActivateTV_Tvime']!='True') && ($service_type==12))||
				(($GLOBALS['ClientCanActivateTV_Moovi']!='True') && ($service_type==13))
			) {$GoodPersonal=3;} else {
				
				$NoMail=(((strpos($personalinfo[1],'@'))||(strpos($personalinfo[1],'.')))||($service_type==3)); 
				$NoFIO=(strlen($personalinfo[3])>4);
				$NoTel=(strlen($personalinfo[0])>4);
				$GoodPersonal2=$GoodPersonal;
				
				if ($NoFIO==false){
					$GoodPersonal2=2;
				} elseif ($NoMail==false) {
					$GoodPersonal2=1;
				} elseif ($NoTel==false) {
					$GoodPersonal2=4;
				}
				
				if (
					(($GLOBALS['NoTVWithoutFullName']=='True') && ($service_type==1)) ||
					(($GLOBALS['NoTVWithoutFullName_Omega']=='True') && ($service_type==2)) ||
					(($GLOBALS['NoTVWithoutFullName_24h']=='True') && ($service_type==3)) ||
					(($GLOBALS['NoTVWithoutFullName_TrinitY']=='True') && ($service_type==4)) ||
					(($GLOBALS['NoTVWithoutFullName_Sweet']=='True') && ($service_type==8)) ||
					(($GLOBALS['NoTVWithoutFullName_Prosto']=='True') && ($service_type==5)) ||
					(($GLOBALS['NoTVWithoutFullName_Megogo']=='True') && ($service_type==6)) ||
					(($GLOBALS['NoTVWithoutFullName_IPTVPORTAL']=='True') && ($service_type==7)) ||
					(($GLOBALS['NoTVWithoutFullName_Wink']=='True') && ($service_type==9)) ||
					(($GLOBALS['NoTVWithoutFullName_TVIPmedia']=='True') && ($service_type==10))||
					(($GLOBALS['NoTVWithoutFullName_Tvime']=='True') && ($service_type==11))||
					(($GLOBALS['NoTVWithoutFullName_Moovi']=='True') && ($service_type==12))
					){
					$GoodPersonal=$GoodPersonal2;
				}
		
			}
					$serviceList = '';
										
					//163 - Основные
					//164 - услуги
					//165 - пакеты
					$h1='						
						<div class="two">
							<div class="h3 m4">'.$strings[163].' '.(($service_type==0)?$strings[164]:$strings[165]).'
							</div>
						</div>';
			 
			 
					$sum=0;
					
					
					
					$sql = "SELECT `service_name`, `guid`, `paysize`, `payafterday`, `period`, `startcost`, `one_time_service`, `linked_guids`, `isvisible`, `comment`, `root_service`, `root_service2`, `tag`, `grace_period`, `stopcost`, `can_deactivate` FROM `services` WHERE `service_type` = $service_type and (`isvisible` = 1 $serv) and (`root_service2`='' and `root_service` = 0) ORDER BY `display_index`;";
					
					$res = mysql_query($sql,$GLOBALS['mysql']) or die(mysql_error());
					
					$founded=false;
					$serviceList2='';
					while($row = mysql_fetch_array($res)) { 
						$r = GetServiceStr($row, $LinkedSVCS, $service_type, $BadSVCS, $GoodPersonal, $AllServices, $Services);
						if (strlen($r)>0){
							$serviceList2 .= $r;
							$founded=true;
							$sum+=1;
						}
					}	
					
					//166 - Дополнительные
					//164 - услуги
					//165 - пакеты
					$h2='
						<div class="two">
							<div class="h3 m4">'.$strings[166].' '.(($service_type==0)?$strings[164]:$strings[165]).'
							</div>
						</div>';
					
					$sql = "SELECT `service_name`, `guid`, `paysize`, `payafterday`, `period`, `startcost`, `one_time_service`, `linked_guids`, `isvisible`, `comment`, `root_service`, `root_service2`, `tag`, `grace_period`, `stopcost`, `can_deactivate` FROM `services` WHERE `service_type` = $service_type and (`isvisible` = 1 $serv) and (`root_service2`<>'' or `root_service` > 0) ORDER BY `display_index`;";
					$res = mysql_query($sql,$GLOBALS['mysql']) or die(mysql_error());
					
					$founded2=false;
					$serviceList3='';
					while($row = mysql_fetch_array($res)) { 
					
						$r=GetServiceStr($row, $LinkedSVCS, $service_type, $BadSVCS, $GoodPersonal, $AllServices, $Services);
						if (strlen($r)>0){
							$serviceList3 .= $r;
							$founded2=true;
							$sum+=1;
						}
					}	
					if ($founded){
						if (!$founded2){$h1='';}
						$serviceList .=$h1.''.$serviceList2.'';
					}
					if ($founded2){
						$serviceList .=$h2.$serviceList3.'';
					}
		   
					$serviceList .= '<br>';
					
					// 167 - услуг
					// 168 - TV пакетов
					// 623 - Нет
					if ((int)$sum==-1){$serviceList = $strings[623].' '.(($service_type==0)?$strings[167]:$strings[168]);}
				//} 
		   
					// 20 - Услуги
					// 148 - Дополнительные услуги
					// 149 - Телевидение
					if(strlen($TopText)>0)
						 $TopText = '<div class="two banner">'.$TopText.'</div>';
					if(strlen($BottomText)>0)
						 $BottomText = '<div class="two banner">'.$BottomText.'</div>';
					$result['out'] = '
						'.$crumbs.'
						<div class="content">
							<div class="two">
								<div class="h3 m4">'.(($service_type==0)?$strings[148]:$strings[149]).'</div>
							</div>					
							'.$TopText.$serviceList.$BottomText.'
						</div>';
			
		}
	}
	
	return parse_template($result, './template/default.php',NULL,FALSE,true);
}

function GetServiceStr($row,$LinkedSVCS, $service_type, $BadSVCS, $GoodPersonal,$AllServices,$Services) {
	
	$strings=$GLOBALS['strings'];
	
	$name = $row[0];
	$guid = $row[1];
	$paysize = $row[2];
	$payafterday = $row[3];
	$period = $row[4];
	$paysize2 = $row[5];
	$one_time_service =  $row[6];
	$linked_guids = $row[7];
	$visible = $row[8];
	$comment = $row[9];
	$grace_period=$row[13];
	$stopcost=$row[14];
	$can_deactivate=$row[15];
		
	if ($row[10]=='0'){$row[10]='';} else {$row[10] .=',';}
	if (strlen($row[11])==0){$row[10]=substr($row[10],0,strlen($row[10])-1);}
	$row[11]=$row[10].$row[11];
	$tag=$row[12];
	//$tag=ChannelsList();
	
	$ch = explode('||',$tag);
	if (count($ch)>2){
		$tag = " (<a href='channelslist.php?id={$ch[0]}&tvid={$ch[1]}'>{$strings[622]} {$ch[2]}</a>)";
	} else {$tag='';}
	
	//622 - каналов
	
	if (strlen($row[11])>0){
		$RootSVC = explode(',',$row[11]);
	} else {
		$RootSVC = array();
	}
	
	$sql = "SELECT `object_data` FROM `system_objects` WHERE `id` = '".mysql_real_escape_string($guid)."' and `object_type` = 3;";
	$res = mysql_query($sql,$GLOBALS ["mysql"]);
		
	if (mysql_num_rows($res)>0){
				
		$row = mysql_fetch_array($res);
		$JSON=json_decode($row[0],true);
		
		if (isset($JSON['NameLNG'])){
			$SvcName = $JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']];
		} else {
			$SvcName = '';
		}
		
		if (isset($JSON['CommentLNG'])){
			$SvcComment = $JSON['CommentLNG'][$_COOKIE['MikroBILL_WEB_Language']];
		} else {
			$SvcComment = '';
		}
		
		
		if (strlen($SvcName)>0){
			$name = $SvcName;
		}
		if (strlen($SvcComment)>0){
			$comment = $SvcComment;
		}
		
		
		
		if (isset($JSON['TVpacketID'])){
			
			if (strlen($JSON['TVpacketID'])>0){
				
				$ServiceType=(count($ch)>1)?($ch[1]):('');
				$sql = "select count(*) from `smotreshka_channels` where `subscription_id` = '".mysql_real_escape_string($JSON['TVpacketID'])."';";
				$res = mysql_query($sql,$GLOBALS ["mysql"]);
				if (mysql_num_rows($res)>0){
					
					if (isset($JSON['ServiceType'])){
						$ServiceType = (int)$JSON['ServiceType'] - 10;
					}
					
					if ((int)$ServiceType<0){
						$ServiceType=$ch[1];
					}
					
					$row = mysql_fetch_array($res);
					$tag = " (<a href='channelslist.php?id={$JSON['TVpacketID']}&tvid={$ServiceType}'>{$strings[622]} {$row[0]}</a>)";
				}
			}
		}
	}
	

	return PrintService($name,$guid,$paysize,$payafterday,$period,$LinkedSVCS,$paysize2, $one_time_service, 
						$linked_guids, $visible, $comment, $RootSVC, $service_type, $tag, $BadSVCS, 
						$grace_period, $GoodPersonal,$stopcost,$can_deactivate,$AllServices,$Services);
}

function CheckService($linked_guids){
	
	$LinkedSVCS_v = explode("||",$linked_guids);
	foreach ($LinkedSVCS_v as &$value) {
		
		if ($value=='12211221122112'){return true;}
		if (($value==$_SESSION['tarif_guid']) || ($value==$_SESSION['group_guid'])) {
			return true;
		}
	}
	
	return false;
}

function escape_js_single_quote($str) {
	 $str = str_replace(
        ["\\",   "'",   "\"",  "\n",  "\r"],
        ["\\\\", "\\'", "\\\"", "\\n", "\\r"],
        $str
    );

    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function PrintService($name,$guid,$paysize,$payafterday,$period,$LinkedSVCS,$paysize2,$one_time_service, 
						$linked_guids, $visible, $comment, $RootSVC, $service_type, $tag, $BadSVCS, 
						$grace_period, $GoodPersonal, $stopcost, $can_deactivate, $AllServices,
						$Services) {
			
			
	if (!CheckService($linked_guids)){return '';}
				
	$sum2=count($LinkedSVCS,1) - 1;
	$sum3=count($BadSVCS,1) - 1;
	$strings=$GLOBALS['strings'];
	
	// 425 - Отключить
	// 426 - Подключить
	// 427 - Услуга подключена
	$btnValue = $strings[426];
	$btnValue2 = $strings[426];
	$checked = '';
	$checkValue = "true";
	$DisablePlan = '';
	$DisableButton = '';
	$Finded=false;
	$Finded2=false;
	$ConfirmCode='';
	if (($visible==0)&&(($can_deactivate==0)||(array_search($guid, $AllServices) === false))){$DisableButton=' disabled ';}
				
	$ac='';
	$ThisIsDisable=false;
	for($i=0; $i <= $sum2; $i++) 
	{		
		$LinkedSVCS_v = explode("|",$LinkedSVCS[$i]."|||");		
		
		if ($LinkedSVCS_v[0] == $guid) 
		{
			$btnValue = $strings[425];
			$btnValue2 = $strings[425];
			$checked = '<i class="shout fas fa-plug" title="'.$strings[428].'"></i>';
			$checkValue = "false";
			if ((int)$period>0){$DisablePlan = $LinkedSVCS_v[1];}
			if ($one_time_service == '1'){$DisableButton = ' disabled ';}
			$Finded=true;
			$ThisIsDisable=true;
			
			
			if (count($LinkedSVCS_v)>=9){
				$ac= ' ' . sprintf($strings[647],$LinkedSVCS_v[7],$LinkedSVCS_v[8]);
			}
			
			break;
		}else {
			
				foreach ($RootSVC as &$value) {
					if ($value==$LinkedSVCS_v[0]){
						$Finded2=true;
					}
				}
		}
				
	}
		
	if (($visible==0)&&(!$Finded)){return "";}	
	
	if ((count($RootSVC)>0) && ($Finded2==false)) {
		//print_r ($RootSVC); exit();
		$DisableButton=' disabled ';
		//$checked = '<i class="shout fas fa-plug" title="'.$strings[428].'"></i>';
	}
	
	$NameOrig=$name;
	
	switch ($GoodPersonal) {
    case 0:	
		
		$name = escape_js_single_quote($name);
		
		$add='';
		if ($ThisIsDisable){
												
			$RootTree=CheckRootTree($Services,$guid,$AllServices);
												
			if (count($RootTree)>0){
				$add="<br><br><b>{$strings[120]}</b> <br>{$strings[619]} ". GetSVCnames($RootTree) .'!';
			}
		}
		
		// 172 - услугу
        $ConfirmCode=$btnValue2.' '.$strings[172].' '. "\'$name\'" .'?'.$add.'\')">';
        break;
    case 1:
		// 169 - Для продолжение необходимо <a href="changepi.php">добавить</a> email в профиль!
		$ConfirmCode=$strings[169]."', true)\">";
        break;
    case 2:
		// 170 - Для продолжение необходимо добавить ФИО в профиль! <br> Обратитесь в <a href="help.php">службу поддержки</a>.
        $ConfirmCode=$strings[170]."', true)\">";
        break;
	case 3:
		//156 - Для подключения пакетов TV обратитесь в %s службу поддержки
		$ConfirmCode=sprintf($strings[156],'<b><a style=\\\'color: black;\\\' href=\\\'help.php\\\'>').'</a></b>!\', true)">';
        break;		
	case 4:
		// 503 - Для подключения услуги TV у абонента должey быть указан телефонный номер! <br> Обратитесь в <a href="help.php">службу поддержки</a>.
        $ConfirmCode=$strings[503]."', true)\">";
        break;
	}
	
	
	
	$SubmitBTN='<button type="button" name="go" '.$DisableButton.' onclick="Confirm(this,\''.$ConfirmCode.$btnValue.'</button>';
	
	
	if (strlen($DisablePlan)>2){
		// 173 - отключение запланировано
		// 427 - отключение
		$SubmitBTN= $strings[427]." $DisablePlan";
	} 
	
	
	for($i=0; $i <= $sum3; $i++) 
	{	
		$BadSVCS_v = explode("|",$BadSVCS[$i]."|||");	
		if ($BadSVCS_v[0] == $guid) 
		{	
			if ($BadSVCS_v[1]<>'2'){
				// 390 - Недоступно для подключения!
				// 525 - Недостаточно средств для активации услуги!
				$SubmitBTN = ($BadSVCS_v[1]=='0')?($strings[390]):($strings[525]);
				break;
			} else {return '';}
		}
	}
		

	$serviceList = '
	<div class="two service">
		<div class="a">'.$checked.'
		</div><div class="b">';

	if (count($RootSVC)>0){
		
		$i = 0;
		// 174 - Доступно после подключения услуги
		$comment .= " (".$strings[174].": ";
		foreach ($RootSVC as &$RS) {
			$RS=mysql_real_escape_string($RS);
			//$sql2 = "SELECT service_name, guid, paysize, payafterday, period, startcost, one_time_service, linked_guids, isvisible FROM services WHERE guid='$RS';";
			
			$sql2 = "SELECT `object_data`, `object_name` FROM `system_objects` WHERE `id` = '$RS';";
			$res = mysql_query($sql2,$GLOBALS['mysql']);
			
			if (mysql_num_rows($res)>0){
				$row = mysql_fetch_array($res); 
				
				$SvcName=$row[1];
				
				$JSON=json_decode($row[0],true);
		
				if (isset($JSON['NameLNG'])){
					if (strlen($JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']])>0){
						$SvcName = $JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']];
					}
				}
				
				// 177 - или
				if (strlen($row[0])>0) {
					$comment .= "'" . $SvcName . "', ".$strings[177]." ";
					$i = $i + 1;
				}
			}
		}
		if ($i>0){
			$comment = substr($comment, 0, strlen($comment)-strlen("', ".$strings[177]));
		}
		$comment .=")";
		
	}
	
	$serviceList .= '
			<div class="h4">'.$NameOrig.' '.$tag.'</div>
			<div class="small">'.$comment.'</div>';

	if ((float)$paysize > 0) {
				
		// 394 - Абонентская плата
		$serviceList .= '
			<div class="small">
				'.$strings[394].': '.$paysize.' '.$GLOBALS['curr'] . $ac;
		if ($one_time_service<>'1') {
			// 175 - в день
			// 176 - в месяц
			if (($payafterday=="0") || ($payafterday=="1")){
				$serviceList .= " ".$strings[175];
				if ($grace_period>0){
					// 392 - с \s дня
					$serviceList .= ' (' . sprintf($strings[392],((int)$grace_period+1)).')';
				}
			}elseif (($payafterday=="2") || ($payafterday=="3")){
				$serviceList .= " ".$strings[176];
				if ($grace_period>0){
					// 391 - с \s месяца
					$serviceList .= ' (' . sprintf($strings[391],((int)$grace_period+1)).')';
				}
			}			
		}
		$serviceList .= '
			</div>';
	} else {
		// 429 - Без абонентской платы
		$serviceList .= '
			<div class="small">
				'.$strings[429].'
			</div>';
	}
	if ((float)$paysize2 > 0) {
		// 537 - Подключение
		$serviceList .= '
			<div class="small">' . $strings[537] .' '.$paysize2.' '.$GLOBALS['curr'].'</div>';
	}
	if ((float)$stopcost > 0) {
		// 538 - Отключение:
		$serviceList .= '
			<div class="small">' . $strings[538] .' '.$stopcost.' '.$GLOBALS['curr'].'</div>';
	}
	$serviceList .= '
		</div><div class="c">
			<form action="" method="POST">
				<input type="hidden" name="value" value="'.$checkValue.'||'.$guid.'"/>
				<input type="hidden" name="name" value="'.$name.'"/>
				<input type="hidden" name="disableplan" value="'.$DisablePlan.'"/>'.
				$SubmitBTN.'
			</form>
		</div>
	</div>';
	
	return $serviceList;
}


	function CheckRootTree($Services,$DstSVC,$AllServices){
		$RootTree=array();
		foreach ($Services as &$SVC_OBJ) {
			$SVC=$SVC_OBJ['JSON'];
			
			if ($SVC_OBJ['ID']!=$DstSVC){
				
				if ((isset($SVC['RootService']))&&(isset($SVC['RootService2']))){
					
					$RootSVC = $SVC['RootService2'];
					if ($SVC['RootService']>0){$RootSVC[]=$SVC['RootService'];}
					
					if (array_search($DstSVC, $RootSVC) !== false){
						if (array_search($SVC_OBJ['ID'], $AllServices) !== false){
							$ServiceName = $SVC['Name'];
							
							if (!isset($JSON['NameLNG'])){
								$NameLNG = '';
							} else{		
								$NameLNG = $JSON['NameLNG'][$GLOBALS['Language']];
							}
							if (strlen($NameLNG)>0){$ServiceName = $NameLNG;}
							
							$RootTree[]=$ServiceName;
							$RootTree=array_merge($RootTree, CheckRootTree($Services,$SVC_OBJ['ID'],$AllServices));
						}
					}
				}
				
			}
		}
		return $RootTree;
	}
	
	function GetSVCnames($RootTree){
		$Ret='';
		foreach ($RootTree as &$SVC) {
			$SVC=str_replace("'","\\'",$SVC);
			$Ret = $Ret . "\\'{$SVC}\\', ";
		}
		return trim(trim($Ret),',');
	}


function ChannelsList($TVid, $OnlyCount=false){

	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	$id='';
	if (isset($_REQUEST['id'])){
		$id=mysql_real_escape_string($_REQUEST['id']);
	}
	$Name='';
	$channels='';
	
	$SubscriptionTable='';
	$ChannelTable='';
	$LinkID='';
	$WSize=100;
	$HSize=100;
	if ($TVid==0){
		$SubscriptionTable='smotreshka_subscriptions';
		$ChannelTable='smotreshka_channels';
		$WSize=100;
		$HSize=50;
	} elseif ($TVid==1) {
		$SubscriptionTable='omegatv_subscriptions';
		$ChannelTable='omegatv_channels';
		$LinkID=2;
		$WSize=100;
		$HSize=100;
	} elseif ($TVid==2) {
		$SubscriptionTable='tv24h_subscriptions';
		$ChannelTable='tv24h_channels';
		$LinkID=3;
		$WSize=100;
		$HSize=50;
	} elseif ($TVid==10) {
		$SubscriptionTable='tvipmedia_subscriptions';
		$ChannelTable='tvipmedia_channels';
		$LinkID=11;
		$WSize=100;
		$HSize=50;
	} elseif ($TVid==12) {
		$SubscriptionTable='moovi_subscriptions';
		$ChannelTable='moovi_channels';
		$LinkID=13;
		$WSize=100;
		$HSize=50;
	}
	
	$res = mysql_query("SELECT `subscription_name` FROM `$SubscriptionTable` WHERE `subscription_id` = $id;",$GLOBALS['mysql']) or die (mysql_error());
	
	if (mysql_num_rows($res)){
		
		$row = mysql_fetch_array($res);
		$Name = $row[0];
		
		if (strlen($row[0])>0){
			$res = mysql_query("SELECT `channel_name`, `channel_image` FROM `$ChannelTable` WHERE `subscription_id`=$id;",$GLOBALS['mysql'])or die (mysql_error());
			
			$channels='<table border=0 cellspacing=10>';
			$i=0;
			$j=1;
			while ($row = mysql_fetch_array($res)){
				
				if ($i==0){$channels .='<tr>';}
				$channels .='<td align="center"><img src="'.$row[1].'" width='.$WSize.' height='.$HSize.'></td><td><b>'.$j.'. </b>'.$row[0].'</td>';
				if ($i>=1){$i=0;}else {$i+=1;}
				if ($i==0){$channels .='</tr>';}
				$j++;
			}
			$channels .='</table>';
		}
	}	
	
	if ($OnlyCount){
		return $j-1;
	}
		
	// 178 - Список каналов
	$result['page_title'] = $strings[178];
	// 179 - Список каналов пакета
	// 180 - Назад
	$result['out'] = '
				'.menuManager('two',array("./managetv$LinkID.php",$strings[149])).'
				<div class="content">
					<div class="two">
						<div class="i m3">'.$strings[179].' "'.$Name.'":</div>
						<div class="m2">'.$channels.'</div>
					</div>
					<a href="javascript:history.back()">'.$strings[180].'</a>
				</div>';
	return parse_template($result, './template/default.php',NULL,FALSE,true);
}

	
# Добавление пользовательских запросов в техподдержку
function addticket() 
{
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	if ($GLOBALS['UseTicketsForUsers'] != 1){exit();}
	
	//181 - Обратная связь
	$result['page_title'] = $strings[181];
	
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
	
	$datetoday = date("Y-m-d H:i:s");
	
	# Если нажата кнопка то отправляем запрос в базу
	if (isset($_POST["go"])) {
		$appl = mysql_real_escape_string($_POST["appl"]);
		$appl=remove_emoji($appl);
		
		if (mb_strlen($_POST["tel"])>0){
			$appl=$appl . "\n" . str_replace('+8','+7',$_POST["tel"]);
		}
		
		$tel ="";
		$mail ="";
		$Telegram ="";
		
		//if (strlen($appl)>3){
			$sql = "SELECT pinfo, otherinfo FROM stat WHERE user_name='".mysql_real_escape_string($_SESSION['login'])."';";
			$mquery = mysql_fetch_assoc(mysql_query($sql));
		
			$pinfo = $mquery['pinfo'];
			$otherinfo = $mquery['otherinfo'];
		
			$pinfoa = explode("||",$pinfo . "||||||||||");
			$otherinfo = explode("||",$otherinfo . "||||||||||");
			$tel = $pinfoa[0];
			$mail = $pinfoa[1];
			$Telegram =$pinfoa[2];			
			$Adr=mysql_real_escape_string($GLOBALS['address']);
			$Files=''; 			
			
			if (isset($_FILES['fileobj']['tmp_name'])){
			
				if (strlen($_FILES['fileobj']['tmp_name'])>0){
				
					$sql = "SELECT `param_value` FROM `workparams` WHERE `param_name`='CRYPTO_KEY_2';";
					$result_sql = mysql_query($sql,$mysql);
					$row = mysql_fetch_array($result_sql);
					$CRYPTO_KEY_2=$row[0];
					$FileData=file_get_contents($_FILES['fileobj']['tmp_name']);
				
					unlink($_FILES['fileobj']['tmp_name']);
					
					$EncImg=Encrypt($FileData,
								base64_decode($GLOBALS['CRYPTO_KEY_1']),
								base64_decode($CRYPTO_KEY_2));
					
					
					$sql = "INSERT INTO `objects` VALUES(NULL, '".mysql_real_escape_string($_FILES['fileobj']['name'])."', '".mysql_real_escape_string($EncImg)."',".strlen($FileData).",0)";
					
					mysql_query($sql,$mysql) or die(mysql_error());
					
					//echo $sql;exit();
					//$D = gmdate( 'H', abs(date( 'Z' )))*3600 + gmdate( 'i', abs(date( 'Z' )))*60;
					$name=iconv('utf-8', 'cp1251', $_FILES['fileobj']['name']);
					//$name=$_FILES['fileobj']['name'];
							
					$Files = base64_encode(
								mysql_insert_id() . '/*\\' . (time() + abs(date( 'Z' ))) . '/*\\' . 
								mysql_real_escape_string($name) . '/*\\/*\\' . $_SESSION['shortguid'] . '/|\\'
							);					
				}	
			}
					
		if ((strlen($_FILES['fileobj']['tmp_name'])>0) || (strlen($appl))>0){
			
			
			//182 - Заявка принята! <br>С вами свяжутся в ближайшее время.
			$Msg=$strings[182];
			
			if ($otherinfo[168]<>'1'){
				$Adr=mysql_real_escape_string($Adr);
				$sql2 = "INSERT INTO tickets (`user_name`,`tickettime`,`tickettext`,`ticketstatus`,`tickettype`,`ticketcomment`,`ticketreaded`,`user_login`,`ticketcreator`,`ticketcomment2`,`working_address`,`filename`) VALUES ('".mysql_real_escape_string($_SESSION['shortguid'])."', '$datetoday', '" . str_replace("'"," ",$appl) . "',0,0,'',0,'".mysql_real_escape_string($_SESSION['login'])."','1','','$Adr','$Files');";
				mysql_query($sql2,$mysql);
			
				$id=uniqid();
				$contacts = mysql_real_escape_string("$tel||$mail||$Telegram||Ремонт||||".mysql_insert_id()."||on");
				$sql = "INSERT INTO actions VALUES ('ADD_TICKET','".mysql_real_escape_string($_SESSION['shortguid'])."','".$contacts."','$appl','$id');";
				mysql_query($sql,$mysql);
				$sql = "INSERT INTO `refresh_db` VALUES (1);";
				mysql_query($sql,$mysql);
			} else {
				//182 - Действие запрещено!
				$Msg=$strings[599];
			}
			//181 - Обратная связь
			$result['out'] = '
					'.menuManager('six',array("./help.php",$strings[181])).'
					<div class="content">
						<div class="six">
							<div class="i m3">'.$Msg.'</div>							
						</div>
					</div>
					<script>Refresh("help.php")</script>';	
		} else {$result['out'] = '<script>history.back();</script>';}
		
								
	} else {
		
		$TelAct='';
		if ($GLOBALS['ForceTicketPhones']=='1'){
			//649 - Пожалуйста укажите телефон для связи!
			$TelAct="if ((document.getElementById('tel').value.length<12)||(document.getElementById('tel').value.length>12)){alert('{$strings[649]}'); return;}";
		}
		
		# Если нет, выводим форму
		//183 - Оставьте заявку и вам обязательно ответят
		//184 - Текст заявки
		//185 - Вы можете прикрепить к тикету файл
		//186 - Отправить заявку
		$result['out'] = '
					'.menuManager('six',array("./help.php",$strings[181])).'
					<div class="content">
						<div class="six">
							<i class="fas fa-comment"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="i m3">'.$strings[183].'</div>
							<form id="ticket" action="help.php" method="POST" enctype="multipart/form-data">
								<textarea type="text" name="appl" id="appl" value="" class="m2" rows="6" placeholder="'.$strings[184].'"></textarea>
								<div class="m2">
									'.$strings[648].': <br><input type="text" name="tel" id="tel" inputmode="numeric" maxlength=12>
								</div>
								<br>
								<div class="m2">
									<input type="file" style="cursor:pointer" name="fileobj" value=""/>
								</div>
								<input type="hidden" name="go" value="1"/>
								<button type="button" title="'.$strings[186].'" class="m2" onclick="'.$TelAct.'document.getElementById(\'ticket\').submit();">'.$strings[186].'</button>
							</form>
							'.ticketHistory($mysql).'
						</div>
					</div>
					
					<script>
					

						setInputFilter(\'tel\', function(value) {
							//return /^\d*\.?\d*$/.test(value);
							return /^\+?\d*$/.test(value);
						}, 0);

					
					
					
					function setInputFilter(textbox, inputFilter,opt=-1) {

					  textbox = document.getElementById(textbox);

					  [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
						textbox.addEventListener(event, function(e) {
						  if (inputFilter(this.value)) {
							// Accepted value.
							if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0){
							  this.classList.remove("input-error");
							  this.setCustomValidity("");
							  
							  if (opt==0){
								  if (this.value.length>0){
									  if (this.value.substring(0,1) != "+"){
										  this.value = "+" + this.value;
									  }
								  }
							  }11
							}

							this.oldValue = this.value;
							this.oldSelectionStart = this.selectionStart;
							this.oldSelectionEnd = this.selectionEnd;
						  }
						  else if (this.hasOwnProperty("oldValue")) {
							// Rejected value: restore the previous one.
							this.classList.add("input-error");
							this.setCustomValidity("'.$strings[650].'");
							this.reportValidity();
							this.value = this.oldValue;
							this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
						  }
						  else {
							// Rejected value: nothing to restore.
							this.value = "";
						  }
						});
					  });
					}
					</script>
					';
		}		
		mysql_close($mysql);
	
	if (isset($GLOBALS['UseChatra'])){
		if (($GLOBALS['UseChatra']==1)&&($GLOBALS['OpenChatraWindowOnHelpPage']==1)){
			$result['out'] .= '<script>
	var MyChatraTimer=setTimeout(ChatraShowFunc, 2000);
	function ChatraShowFunc() {
		clearInterval(MyChatraTimer);
		Chatra("openChat", true);
	}
</script>';
		}
	}
	
	
	return parse_template($result, './template/default.php',NULL,FALSE,true);	
	} 
function ticketHistory($mysql)
{
	if ($GLOBALS['ClientCanReadTickets']<>'True'){
		return '';
	}

	$strings=$GLOBALS['strings'];
	
	// 1 - Января
	// 2 - Февраля
	// 3 - Марта
	// 4 - Апреля
	// 5 - Мая
	// 6 - Июня
	// 7 - Июля
	// 8 - Августа
	// 9 - Сентября
	// 10 - Октября
	// 11 - Ноября
	// 12 - Декабря
	$monthArray = array($strings[1],$strings[2],$strings[3],$strings[4],$strings[5],$strings[6],$strings[7],$strings[8],$strings[9],$strings[10],$strings[11],$strings[12]);
	$result ="";
	
	$sql = "SELECT * FROM tickets WHERE user_name='".mysql_real_escape_string($_SESSION['shortguid'])."' and `visible` = 1 ORDER BY tickettime DESC";

		$mysqlQueryResult = mysql_query($sql, $mysql);

		while ($arrRow = mysql_fetch_assoc($mysqlQueryResult))
		{
			$mysqlResult[]=$arrRow;
		};
		
	
	$ticketTable ="";
	
	if (!empty($mysqlResult))
	{
		foreach ($mysqlResult as $ticket)
		{
			// 187 - В работе
			$Status = $strings[187];
			// 188 - Завершена
			if ($ticket['ticketstatus'] == "1") $Status = $strings[188];
		
		
			$unixTimeStamp = strtotime($ticket["tickettime"]);
			$month = $monthArray[date('n',$unixTimeStamp)-1];
			$dateTime = '<span>'.date('j ',$unixTimeStamp).$month.'</span>'.date(' Y <br> H:i',$unixTimeStamp);
			$Files='';
			if (strlen($ticket["filename"])>0) {
				$Files=explode('/*\\',base64_decode($ticket["filename"]));	
				// 189 - Документ
				$Files=''.$strings[189].':  <a href="kassa/api.php?action=GET_OBJECT&value3=1&value2='.$_SESSION['shortguid'].'&value='. $Files[0].'">'.iconv('cp1251', 'utf-8',$Files[2]).'</a><br>';
			}
			
			
			$ticketTable .= '
				<div>
					<div>'.$strings[191].' '.$ticket["id"].', '.$strings[442].' '.$dateTime.'</div>
					<div>'.$strings[193].': '.$Status.'</div>
					<div>'.$Files.str_replace("\n", '<br>', filter($ticket["tickettext"])).'</div>
					'.(strlen($ticket["ticketcomment"])>0?'
					<div>'.$strings[195].'</div>
					<div>'.filter($ticket["ticketcomment"]).'</div>':'').'
				</div>
			'; 
		}
	
		// 190 - Все обращения
		// 191 - №
		// 192 - Дата
		// 193 - Статус
		// 194 - Заявка
		// 195 - Комментарий
		// 438 - История обращений
		// 442 - от
		$result = '
			<div class="i m4">'.$strings[438].'</div>
			<div class="ticket">
				'.$ticketTable.'
			</div>
		';
	}
	else
	{
		$result='';
	}
		
	return $result;
}



# Статистика платежей
function payments() 
{
	$result = getuserinfo();
	$otherinfo=explode('||',$result['otherinfo']);
	$strings=$GLOBALS['strings'];
	
	// 196 - Статистика платежей
	$result['page_title'] = $strings[196];

	$q=($otherinfo[151]<0)?($otherinfo[151]):(mysql_real_escape_string($_SESSION['shortguid']));
	$q2=($otherinfo[151]<0)?(" and `client_login` = '".mysql_real_escape_string($_SESSION['shortguid'])."' "):('');
	$query = "SELECT moneytime as Дата, cash as Сумма, cash_name as Валюта, actball as Баланс, value1 as Примечание FROM moneys WHERE user_name = '$q' $q2 and moneytime>".strtotime("-1 year", time())." ORDER BY moneytime DESC";
	//echo $query;exit();
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMES utf8mb4;') or die('qf');
		$mysqlq = mysql_query($query, $mysql);
		mysql_close($mysql);
		while ($arrRow = mysql_fetch_assoc($mysqlq))
		{
			$MyResult[]=$arrRow;
		};
	//echo 1;exit();
	# Создаем заголовок страницы
	// 197 - Зачисления и списания
	// 192 - Дата
	// 198 - Тип операции
	// 199 - Сумма
	// 200 - Баланс
	// 201 - Примечание
	$out = '
	'.menuManager('four',array("./payments.php",$strings[196])).'
	<div class="content">
		<div class="four">
			<div class="h3 m4">'.$strings[196].'</div>
			<div class="">
				<table class="payments">
				<tr>
					<th>'.$strings[192].'</th>
					<th><!-- '.$strings[198].' --></th>
					<th>'.$strings[199].'</th>
					<th>'.$strings[200].'</th>
					<th>'.$strings[201].'</th>
				</tr> 				
				';
				
		# Обрабатываем вывод таблицы
		if (!isset($MyResult)){$MyResult= array();}
		foreach ($MyResult as $line) {
			# Форматируем дату
			$line['Дата'] = dateformating(gmdate("Y-m-j H:i:s", $line['Дата']/1000));
			# Добавление иконок
				$counter = 1;
				
				# Если сумма больше нуля, выводим +
				if (floatval($line['Сумма']) > 0) {
					$icon = '<td><i class="fas fa-plus-circle"></i></td>';
					}
				# Если сумма больше нуля, выводим -
				if (floatval($line['Сумма']) < 0) {
					$icon = '<td><i class="fas fa-minus-circle"></i></td>';
					}
				
				# Если сумма нуль, выводим ?
				if (floatval($line['Сумма']) == 0) {
					$icon = '<td><i class="fas fa-envelope-open"></i></td>';
					}

					# Форматирование суммы
				$sum = $line['Сумма'].' '.$line['Валюта'];
				$line['Сумма'] = $sum;
				$sum2 = $line['Баланс'].' '.$line['Валюта'];
				$line['Баланс'] = $sum2;
				$line['Примечание'] = filter($line['Примечание']);
				unset($line['Валюта']);
			# Вывод массива MySQL
			# Если сумма равна нулю, выводим пустую ячейку
				#if (intval($line['Сумма']) <> 0) {
					//$icon = '<td align="center" valign="middle" style="border: 1px dotted #ccc; padding: 2px; margin: 0px;"></td>';
					$out .= '
					<tr>';
					foreach ($line as $col_value) {
						# Форматирование вывода валюты по правому краю
						//$align = (($line['Сумма'] == $col_value) || ($line['Баланс'] == $col_value))?'align="right" ':'';
						$out .= '
							<td'.($counter == 3?' data-html="'.$strings[200].': ">':'>').$col_value.'</td>';
						if ($counter == 1) $out .= '
							'.$icon;
						$counter++;
					}
					$out .= '
					</tr>';
				#}
		}
	$out .= '</table>
			</div>
		</div>
	</div>'; 
	$result['out'] = $out;
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
	
}


function PayHUB() 
{
	if ($GLOBALS['EnablePayHUB']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayHUB_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayHUB_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['PayHUB_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePayHUBStdTitle']==1)?($strings[588]):($GLOBALS['PayHUBTitle']));
	
	$crumbs = menuManager('four',array("./payhub.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 588 - Оплата через PayHUB
	$result['page_title'] = $strings[588];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (isset($_REQUEST['billme']))
	{
		if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После обработки деньги поступят в течении нескольких минут.
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[204].'</div>
						</div>
					</div>
					<script>Refresh("pay2.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='<script>;
								window.parent.location = "index.php"
							</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		if ($GLOBALS['EnablePayHUB'] <> 1)
		{
			// 589 - Пополнение счёта через Payplug запрещено!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.str_replace('Payplug','PayHUB',$strings[589]).'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		if ((int)$_POST['paysize'] < $GLOBALS['PayHUBMinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[207].' '.$GLOBALS['PayHUBMinPlat'].' '.$GLOBALS['curr'].'</div>
						</div>
					</div>
					<script>Refresh("payhub.php",4)</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
	
		$login = mysql_real_escape_string($_SESSION['login']);
		$paysize = mysql_real_escape_string($_POST['paysize']);
		$paysize = str_replace(",",".",$paysize);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['PayHUB_Commission'],(float)$GLOBALS['PayHUB_Commission2']);
		
		//echo $paysize;exit();
		
		$contract = "";

		$sql = "SELECT otherinfo, shortguid, tarif FROM stat WHERE user_name = '$login'";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$Tariff=addslashes($row[2]);
			mysql_free_result($mysql_result);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		$order_id = $short_guid . "-" . uniqid();


		$PayHUBShopID='';
		$PayHUBShopKey = '';
		$PayHUBconfigID = '';
		$PayHUBmerchantConfigID = '';
		$PayHUB_IsTest = 1;
		
		$PayHUBID = $a[158];
		if ((int)$PayHUBID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$PayHUBID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$PayHUBShopID = $Params['PayHUBShopID'];
				$PayHUBShopKey = $Params['PayHUBShopKey'];
				$PayHUBconfigID = $Params['PayHUBconfigID'];
				$PayHUBmerchantConfigID = $Params['PayHUBmerchantConfigID'];
				$PayHUB_IsTest = $Params['PayHUB_IsTest'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 54";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				
				$Params = GetParams($row[0]);
				$PayHUBID = $row[0];

				$PayHUBShopID = $Params['PayHUBShopID'];
				$PayHUBShopKey = $Params['PayHUBShopKey'];
				$PayHUBconfigID = $Params['PayHUBconfigID'];
				$PayHUBmerchantConfigID = $Params['PayHUBmerchantConfigID'];
				$PayHUB_IsTest = $Params['PayHUB_IsTest'];
			}
		}
		
		$PayHUBShopID=addslashes($PayHUBShopID);
		$PayHUBShopKey=addslashes($PayHUBShopKey);
		$PayHUBconfigID=addslashes($PayHUBconfigID);
		$PayHUBmerchantConfigID=addslashes($PayHUBmerchantConfigID);
		
		$parameters='{
  "params": {
    "login": "'.$PayHUBShopID.'",
    "password": "'.$PayHUBShopKey.'",
    "client": "transacter"
  }
}';
		$URL_Prefix = (($PayHUB_IsTest==1)||($PayHUB_IsTest=='True')||($PayHUB_IsTest=='true'))?('innsmouth'):('rlyeh');
		
		$ch=curl_init();

		curl_setopt($ch,CURLOPT_URL,'https://'.$URL_Prefix.'.payhub.com.ua/auth/token');
		 
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
		curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_SSLv3' );
		//curl_setopt($ch, CURLOPT_CAPATH, PATH_TO_CERT_DIR);
	
	
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json'
		));
		
		curl_setopt($ch,CURLINFO_HEADER_OUT,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$parameters);


		$r=curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		$r=json_decode($r,true);
				
		$Token=$r['data']['access_token'];
		
		//echo $Token;exit();
		//echo 'Result = ' . $r; 
		//echo 'info = ' . $info;
		//
		
		if (strlen($error)>0){
			echo 'Error = ' . $error;
			exit();
		}	
		
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("payhub.php", $url);
		
		if (strlen($GLOBALS['PaySite'])>8) {
			$success_url = trim($GLOBALS['PaySite'],'/') . "/pay2.php?action=1";
			$server_url = trim($GLOBALS['PaySite'],'/') . "pay2.php?action=3";
			$cancel_url = trim($GLOBALS['PaySite'],'/') . "index.php?page=four";
		} else {
			
			$success_url = $uri[0] . "pay2.php?action=1";
			$server_url = $uri[0] . "pay2.php?action=3";
			$cancel_url = $uri[0] . "index.php?page=four";
		}
		
		// 590 - Оплата услуг '%s'.
		// 55 - Оплата по тарифу
		$parameters='{
  "external_id": "'.$order_id.'",
  "options": {
    "ttl": 0,
    "create_short_url": true,
    "backurl": {
      "success": "'.$success_url.'",
      "error": "'.$server_url.'",
      "cancel": "'.$cancel_url.'"
    }
  },
  "lang": "UK",
  "title": "'.addslashes(sprintf($strings[590],$GLOBALS['PortalName'])).'",
  "amount": '.((float)$paysize * 100).',
  "commission": '.$GLOBALS['PayHUB_Commission'].',
  "description": "'.$strings[55]." '$Tariff'".'",
  "short_description": "'.addslashes(sprintf($strings[590],$GLOBALS['PortalName'])).'",
  "merchant_config_id": "'.$PayHUBmerchantConfigID.'",
  "config_id": "'.$PayHUBconfigID.'",
  "disposable": true
}';
		
		//echo 'Params = ' . $parameters .'<br>';
		//echo 'Headers = ' . 'Authorization: Bearer ' . $Token  . '<br>';
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://'.$URL_Prefix.'.payhub.com.ua/frames/links/pga');
		
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
		curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_SSLv3' );
		//curl_setopt($ch, CURLOPT_CAPATH, PATH_TO_CERT_DIR);
		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $Token 
		));
		
		curl_setopt($ch,CURLINFO_HEADER_OUT,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$parameters);

		$r=curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
				
				
		$r=json_decode($r,true);
		$URL='';		
		$URL=$r['short_url'];
		

		//echo 'Result = ' . $r; 
		//echo 'info = ' . $info;
		//
		
		if (strlen($error)>0){
			echo 'Error = ' . $error;
			exit();
		}	
		
		if (strlen($URL)==0){
			echo 'Bad credentials!';exit();
		}
		
		PaymntToLog($PayHUBID,$order_id);
		$sql = "INSERT INTO`operations_payhub`(`id`,`contract`,`sum`,`operation_id`,`date`,`status`)VALUES(NULL,'".$result['shortguid']."','".$paysize."','".$order_id."','".date("Y-m-d H:i:s")."','0');";
		mysql_query($sql,$mysql);
		
		header('Location: '.$URL);
		exit();
		//echo $URL;exit();
		

		// 209 - Загружается платёжный интерфейс...		
		// 210 - Данный браузер не поддреживается! <br> Используйте более современный, например: Chrome, Opera или Safari.
		$result['out'] .= '
		'.$crumbs.'<br>
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<form action="'.$URL.'" method="POST" accept-charset="utf-8" target="myiframe" id="iframe_data_form">
				</form>
				<div id="loadbill" name="loadbill">
					<div class="i m3">'.$strings[209].'</div>
					<img src = "./img/loading.gif" style="border:0" class="m3">
				</div>
				<iframe name="myiframe" id="myiframe" src="" width="780" height="1000" align="center" frameborder="0" scrolling="no" style="visibility:hidden" onload="ShowBill();">
					Ваш браузер не поддерживает плавающие фреймы!
				</iframe>
			</div>
		</div>';
		
		$result['out'].="
		<script type=\"text/javascript\">
			var useragent=navigator.userAgent;
	
			if (useragent.indexOf('MSIE')!= -1)
			{
				document.getElementById(\"loadbill\").innerHTML = \"<center><font color='red'> <br><b>".$strings[210]."</b></font><br><br></center>\";
			} 
			else 
			{
				document.getElementById('iframe_data_form').submit();
			}	
			function ShowBill()
			{
				if (useragent.indexOf('MSIE')== -1)
				{
					document.getElementById(\"loadbill\").innerHTML = \"\";
					document.getElementById(\"myiframe\").style.visibility = 'visible';
				}
			}
		</script>";
		

		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login'";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			mysql_free_result($mysql_result);
		
		$allCost = $a[74];
		
		$pcomm=1;
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		$AllComm = (float)$GLOBALS['PayHUB_Commission'] + (float)$GLOBALS['PayHUB_Commission2'];
				
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		
		// 212 - Для перевода денег на лицевой счет через Payplug необходимо ввести нужную сумму и нажать кнопку «оплатить», после чего на защищенном сервере системы Payplug произвести платёж.<br><br> Деньги поступят на счет в течение нескольких минут после совершения транзакции. <br><br>
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.str_replace('Payplug','PayHUB',$strings[212]).'</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST">
					<div class="m2">
						<input type="text" name="paysize" id="cashsize" value="'.$allCost.'" class="pay"/> '.$comm2.'
					</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<inpurst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>t type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfi';
					}
				}
				// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
				// 218 - Оплатить
				$result['out'] .= '
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'<br>
					<br>
						<font size="2" color="darkgray">
						<div class="m2"> <img src="img/visa.jpg"> </div>
						<div class="m2"> З метою забезпечення найбільш сучасних заходів безпеки онлайн-платежів ми підтримуємо послуги забезпечення безпеки Verified by Visa і MasterCard® SecureCode ™.</div>
						<div class="m2"> Послуга 3D-Secure забезпечує максимальний захист онлайн платежів, яка  розроблена платіжними системами VISA та MasterCard.</div>
						<div class="m2"> 3D-Secure - це  перевірка безпеки онлайн-платежів, яка активується автоматично. Для списання суми з рахунку, необхідно  ввести код, який надсилається в SMS повідомленні за номером телефону який закріплений за банківською карткою.</div>
						<div class="m2"> Платежі на сайті   сертифіковані рівнем  PCI DSS (Payment Card Industry Data Security Standard) – стандарт безпеки даних індустрії платіжних карток. Стандарт розроблений міжнародними платіжними системами Visa та MasterCard і ін.</div>
					</font>
				</form>
			</div>
		</div>'; 
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	
}


function PayPlugPay() 
{
	if ($GLOBALS['EnablePayplug']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayPlug_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayPlug_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['PayPlug_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePayPlugStdTitle']==1)?($strings[25]):($GLOBALS['PayPlugTitle']));
	
	$crumbs = menuManager('four',array("./payplug.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 203 - Оплата через Payplug
	// 25 - Платёж через «Payplug»
	$result['page_title'] = $strings[203];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (isset($_REQUEST['billme']))
	{
		if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После обработки деньги поступят в течении нескольких минут.
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[204].'</div>
						</div>
					</div>
					<script>Refresh("pay2.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='<script>;
								window.parent.location = "index.php"
							</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		if ($GLOBALS['EnablePayplug'] <> "True")
		{
			// 206 - Пополнение счёта через Payplug запрещено!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[206].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		if ((int)$_POST['paysize'] < $GLOBALS['PayplugMinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[207].' '.$GLOBALS['PayplugMinPlat'].' '.$GLOBALS['curr'].'</div>
						</div>
					</div>
					<script>Refresh("payplug.php",4)</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
	
		$login = mysql_real_escape_string($_SESSION['login']);
		$paysize = mysql_real_escape_string($_POST['paysize']);
		$paysize = str_replace(",",".",$paysize);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['PayPlug_Commission'],(float)$GLOBALS['PayPlug_Commission2']);
		
		//echo $paysize;exit();
		
		$contract = "";

		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login'";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			mysql_free_result($mysql_result);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		$Description = "Internet payment";
		$order_id = $short_guid . "||" . uniqid();

		if (strlen($contract)>0){$Description = $Description . " ($contract)";}

		$xml="
		<request>
			<operation_id>$order_id</operation_id>
			<amount>$paysize</amount>
			<description>$Description</description>
		</request>
		";
	
		$PayPlug_Shop=$GLOBALS['PayPlug_Shop'];
		$private_key = $GLOBALS['PayPlug_KEY'];
		
		
		$PayPlugID = $a[124];
		if ((int)$PayPlugID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$PayPlugID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$PayPlug_Shop = $Params['PayPlug_Shop'];
				$private_key = $Params['PayPlug_KEY'];
			}
		} else  {
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 5";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$PayPlug_Shop = $Params['PayPlug_Shop'];
				$private_key = $Params['PayPlug_KEY'];
			}
		}
		
		
		$signature= base64_encode(sha1($xml.$private_key,1));
		$xml_encoded = base64_encode($xml);

		// 209 - Загружается платёжный интерфейс...		
		// 210 - Данный браузер не поддреживается! <br> Используйте более современный, например: Chrome, Opera или Safari.
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<form action="https://'.$PayPlug_Shop.'.payplug.in/gateways/api/v2" method="POST" accept-charset="utf-8" '.$GLOBALS['PayPlug_PayInIframe'].' id="iframe_data_form">
					<input type="hidden" name="parameters_xml" value='.$xml_encoded.' />
					<input type="hidden" name="signature" value='.$signature.' />
				</form>
				<div id="loadbill" name="loadbill">
					<div class="i m3">'.$strings[209].'</div>
					<img src = "./img/loading.gif" style="border:0" class="m3">
				</div>
				<iframe name="myiframe" id="myiframe" src="" width="780" height="1000" align="center" frameborder="0" scrolling="no" style="visibility:hidden" onload="ShowBill();">
					Ваш браузер не поддерживает плавающие фреймы!
				</iframe>	
			</div>
		</div>';
		
		$result['out'].="
		<script type=\"text/javascript\">
			var useragent=navigator.userAgent;
	
			if (useragent.indexOf('MSIE')!= -1)
			{
				document.getElementById(\"loadbill\").innerHTML = \"<center><font color='red'> <br><b>".$strings[210]."</b></font><br><br></center>\";
			} 
			else 
			{
				document.getElementById('iframe_data_form').submit();
			}	
			function ShowBill()
			{
				if (useragent.indexOf('MSIE')== -1)
				{
					document.getElementById(\"loadbill\").innerHTML = \"\";
					document.getElementById(\"myiframe\").style.visibility = 'visible';
				}
			}
		</script>";
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login'";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			mysql_free_result($mysql_result);
		
		$allCost = $a[74];
		
		$pcomm=1;
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		$AllComm = (float)$GLOBALS['PayPlug_Commission'] + (float)$GLOBALS['PayPlug_Commission2'];
				
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		
		// 212 - Для перевода денег на лицевой счет через Payplug необходимо ввести нужную сумму и нажать кнопку «оплатить», после чего на защищенном сервере системы Payplug произвести платёж.<br><br> Деньги поступят на счет в течение нескольких минут после совершения транзакции. <br><br>
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.$strings[212].'</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST">
					<div class="m2">
						<input type="text" name="paysize" id="cashsize" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
					</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}
				// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
				// 218 - Оплатить
				$result['out'] .= '
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
				</form>
			</div>
		</div>'; 
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	
}


function GetSummWithComission($PaySize, $Comission, $Comission2, $ProviderComission=false){
	
	if (!$ProviderComission){
		$pcomm2=((float)(100/(100-(float)$Comission2)/100))*100;
		$PaySize=$PaySize * (float)$pcomm2;
	}
	
	$pcomm2=((float)(100/(100-(float)$Comission)/100))*100;
	$PaySize=$PaySize * (float)$pcomm2;
	$PaySize=ceil($PaySize*100)/100;
	
	if ($GLOBALS['RocomendedSummOnInteger']==1){
		$PaySize=ceil($PaySize);
	}
	
	
	return $PaySize;
}


function creditCardPay() 
{

	if ($GLOBALS['EnablePayCreditCards']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['LiqPay_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['LiqPay_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['LiqPay_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}

	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseLiqPayStdTitle']==1)?($strings[15]):($GLOBALS['LiqpayTitle']));
	
	$crumbs = menuManager('four',array("./liqpay.php",$Title));
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 216 - Оплата банковской картой
	// 435 - Оплата банковской картой через сервис LiqPay
	$result['page_title'] = $strings[435];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (isset($_REQUEST['billme']))
	{
		if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После одобрения банком деньги поступят в течении нескольких минут.
			$result['out'].= $crumbs.'
			<div class="content">
				<div class="four">
					<div class="i m3">'.$strings[204].'</div>
				</div>
			</div>
			<script>Refresh("pay2.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='
			<script type="text/javascript">
				window.parent.location = "index.php"
			</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'].=$crumbs.'
			<div class="content">
				<div class="four">
					<div class="i m3">'.$strings[205].'</div>
				</div>
			</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		if ($GLOBALS['EnablePayCreditCards'] <> "True")
		{
			// 217 - Пополнение счёта банковской картой запрещено!
			$result['out'].=$crumbs.'
			<div class="content">
				<div class="four">
					<div class="i m3">'.$strings[217].'</div>
				</div>
			</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$paysize = mysql_real_escape_string($_POST['paysize']);
		$paysize = str_replace(",",".",$paysize);
		
		if (isset($_POST['mycashname'])){
			$currencyname = mysql_real_escape_string($_POST['mycashname']);
		} else {
			$currencyname = $GLOBALS['curr'];
			
			if (($GLOBALS['curr']=='Грн.') || ($GLOBALS['curr']=='UAH') || ($GLOBALS['curr']=='₴')){
				$currencyname = 'UAH';
			} elseif (($GLOBALS['curr']=='$') || ($GLOBALS['curr']=='USD')){
				$currencyname = 'USD';
			} elseif (($GLOBALS['curr']=='€') || ($GLOBALS['curr']=='EUR')){
				$currencyname = 'EUR';
			}
		}
			
		$contract = "";
		
		
			$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$LiqPay=$a[32];
			mysql_free_result($mysql_result);
		//echo $a[74];exit();
		
		if (strlen($LiqPay)<8){
			$sql = "SELECT * FROM `liqpay_params`;";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$LiqPay=$row[0];
			mysql_free_result($mysql_result);
		}
		
		
			
		$sql = "SELECT * FROM `liqpay_params` WHERE guid = ".mysql_real_escape_string($LiqPay).";";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$merchant_id=$row[3];
			$private_key=$row[4];
			$service_name=$row[5];
			$phone=$row[2];
			$params=$row[6];
			
		
		$LiqpayMinPlat = $GLOBALS['LiqpayMinPlat'];
		$LiqPay_Commission = (float)$GLOBALS['LiqPay_Commission'];
		$LiqPay_Commission2 = (float)$GLOBALS['LiqPay_Commission2'];
		
		$paramsA=explode('||',$params);
		if (count($paramsA)>=3){
			if (is_numeric($paramsA[0])){
				$LiqpayMinPlat = (float)$paramsA[0];
			}
			if (is_numeric($paramsA[1])){
				$LiqPay_Commission = (float)$paramsA[1];
			}
			if (is_numeric($paramsA[2])){
				$LiqPay_Commission2 = (float)$paramsA[2];
			}
		}
		
		
		
		if ((float)$_POST['paysize'] < $LiqpayMinPlat) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].=$crumbs.'
			<div class="content">
				<div class="four">
					<div class="i m3">'.$strings[207].' '.$LiqpayMinPlat.' '.$GLOBALS['curr'].'</div>
				</div>
			</div>
			<script>Refresh("liqpay.php",4)</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("liqpay.php", $url);
		
		if (strlen($GLOBALS['PaySite'])>8) {
			$result_url = trim($GLOBALS['PaySite'],'/') . "/pay2.php?action=1";
			$server_url = trim($GLOBALS['PaySite'],'/') . "/payin/liqpay/payin.php";
		} else {
			
			$result_url = $uri[0] . "pay2.php?action=1";
			$server_url = $uri[0] . "payin/liqpay/payin.php";
		}
		

		
		
		$paysize=GetSummWithComission($paysize,$LiqPay_Commission,$LiqPay_Commission2);
		
		//echo $paysize;exit();

	
			
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}	
		
		$Description = $service_name;
		$order_id = "MikroBILL_$short_guid|$LiqPay|".uniqid();

		if (strlen($contract)>0){$Description = $Description . " ($contract)";}	 

		$url="https://liqpay.ua/?do=clickNbuy";
		$method='';
		$xml="
		<request>      
			<version>1.2</version>
			<result_url>$result_url</result_url>
			<server_url>$server_url</server_url>
			<merchant_id>$merchant_id</merchant_id>
			<order_id>$order_id</order_id>
			<amount>$paysize</amount>
			<currency>$currencyname</currency>
			<description>$Description</description>
			<default_phone>$phone</default_phone>
			<pay_way>$method</pay_way> 
		</request>
		";	
	
		$signature=base64_encode(sha1($private_key . $paysize . $currencyname . $merchant_id . $order_id . 'buy' . $Description . $result_url . $server_url,1));
	
		// 209 - Загружается платёжный интерфейс...
		//$target=$GLOBALS['LiqPay_PayInIframe'];
		$target='';
		$result['out'].=$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<form method="POST" action="https://www.liqpay.ua/api/pay" '.$target.' id="iframe_data_form">
					<input type="hidden" name="public_key" value="'.$merchant_id.'" />
					<input type="hidden" name="amount" value="'.$paysize.'" />
					<input type="hidden" name="currency" value="'.$currencyname.'" />
					<input type="hidden" name="description" value="'.$Description.'" />
					<input type="hidden" name="order_id" value="'.$order_id.'" />
					<input type="hidden" name="result_url" value="'.$result_url.'" />
					<input type="hidden" name="server_url" value="'.$server_url.'" />  
					<input type="hidden" name="type" value="buy" />
					<input type="hidden" name="signature" value="'.$signature.'" />
					<input type="hidden" name="language" value="ru" />
				</form>
				<div id="loadbill" name="loadbill">
					<div class="i m3">'.$strings[209].'</div>
					<img src = "./img/loading.gif" style="border:0" class="m3">
				</div>
				<iframe name="myiframe" id="myiframe" src="" width="780" height="1200" align="center" frameborder="0" scrolling="no" style="visibility:hidden" onload="ShowBill();">
				Ваш браузер не поддерживает плавающие фреймы!
				</iframe>';			
		
		
		PaymntToLog($LiqPay,$order_id);
		
		// 210 - Данный браузер не поддреживается! <br> Используйте более современный, например: Chrome, Opera или Safari.
		$result['out'].="
		<script type=\"text/javascript\">
			var useragent=navigator.userAgent;	
			if (useragent.indexOf('MSIE')!= -1)
			{
				document.getElementById(\"loadbill\").innerHTML = \"<center><font color='red'> <br><b>".$strings[210]."</b></font><br><br></center>\";
			} 
			else 
			{
				document.getElementById('iframe_data_form').submit();
			}  	
			function ShowBill()
			{
				if (useragent.indexOf('MSIE')== -1)
				{
					document.getElementById(\"loadbill\").innerHTML = \"\";
					document.getElementById(\"myiframe\").style.visibility = 'visible';					
				}
			}
		</script>
		";
		
		// 208 - Назад к заполнению формы
		$result['out'].='
			</div>
		</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{

	$login = mysql_real_escape_string($_SESSION['login']);
		$comm='';

		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$LiqPay=$a[32];
			mysql_free_result($mysql_result);
	
	
		$allCost =$a[74];
		
		
		if (strlen($LiqPay)<8){
			$sql = "SELECT * FROM `liqpay_params`;";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$LiqPay=$row[0];
			mysql_free_result($mysql_result);
		}
		
		
			
		$sql = "SELECT * FROM `liqpay_params` WHERE guid = ".mysql_real_escape_string($LiqPay).";";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$merchant_id=$row[3];
			$private_key=$row[4];
			$service_name=$row[5];
			$phone=$row[2];
			$params=$row[6];
					
		
		$LiqpayMinPlat = $GLOBALS['LiqpayMinPlat'];
		$LiqPay_Commission = (float)$GLOBALS['LiqPay_Commission'];
		$LiqPay_Commission2 = (float)$GLOBALS['LiqPay_Commission2'];
		
		$paramsA=explode('||',$params);
		if (count($paramsA)>=3){
			if (is_numeric($paramsA[0])){
				$LiqpayMinPlat = (float)$paramsA[0];
			}
			if (is_numeric($paramsA[1])){
				$LiqPay_Commission = (float)$paramsA[1];
			}
			if (is_numeric($paramsA[2])){
				$LiqPay_Commission2 = (float)$paramsA[2];
			}
		}
		
		
		
		
		$pcomm=1;
		$AllComm = $LiqPay_Commission + $LiqPay_Commission2;
		if ((float)$AllComm>0){$pcomm=1 + (float)$AllComm/100;}
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));		
		
				
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm>0)?$strings[211]." ".$AllComm.'%':'';
			
		
		// 219 - Для перевода  денег на лицевой счет с кредитной карты необходимо ввести нужную сумму и нажать кнопку «оплатить»,...
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.sprintf($strings[443],'LiqPay').'</div>
				<div class="spoiler m2" data-open="'.$strings[444].'" data-close="'.$strings[445].'">
					<div>'.$strings[219].'</div>
				</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST" target="_blank" />
					<div class="m2">
							<input type="text" name="paysize" id="cashsize" value="'.$allCost.'" class="pay"/>
							<span class="select"><select name="mycashname" id="mycashname">
								<option'.((($GLOBALS['curr']=='Грн.') || ($GLOBALS['curr']=='UAH') || ($GLOBALS['curr']=='₴'))?' selected':'').'>UAH</option>
								<option'.((($GLOBALS['curr']=='$') || ($GLOBALS['curr']=='USD'))?' selected':'').'>USD</option>
								<option'.((($GLOBALS['curr']=='€') || ($GLOBALS['curr']=='EUR'))?' selected':'').'>EUR</option>
							</select></span> '.$comm2.'
					</div>';
					if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}					
				$result['out'] .= '
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
				</form>
			</div>
		</div>'; 
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	
}


function PrintBonuses(){
	
	
	$out='';
	if ($GLOBALS['UsePayBonus']=='True'){

		$i=0;
		$Bonuses=$GLOBALS['Bonuses'];
		$sum = (count($Bonuses,COUNT_RECURSIVE)/3) - 1; 
		$strings=$GLOBALS['strings'];
		
		if ($sum>0){
			
			$query = "SELECT tarif_guid, group_guid FROM `stat` WHERE `shortguid`='".mysql_real_escape_string($_SESSION['shortguid'])."';";
			$result = mysql_query($query,$GLOBALS['mysql']);
			if (mysql_num_rows($result)>0){
				$row = mysql_fetch_array($result);
				
					// 221 - Бонусы при пополнении
					$curr=$GLOBALS['curr'];

					$q=0;
					for($q=0; $q < $sum; $q++) 
					{ 
						$PaySize = $Bonuses['PaySize'][$q];
						$BonusSize = $Bonuses['BonusSize'][$q];
						$BonusesLinkedGUIDs = $Bonuses['BonusesLinkedGUIDs'][$q];
						
						if ((strpos($BonusesLinkedGUIDs,$row[0].'||',0)!==false) || (strpos($BonusesLinkedGUIDs,$row[1].'||',0)!==false) || (strpos($BonusesLinkedGUIDs,'12211221122112||',0)!==false)){
							
							// 222 - При пополнении на сумму свыше %s бонус в размере %s!
							$out.='<div class="'.(($q>0)?'m5':'m3').'">* '.sprintf($strings[222], $PaySize, $BonusSize).'</div>';
							$i +=1;
						}
					
					}
				
			}
			
			if ($i<=1){$out ='<br>'.$out;}
			
	   }
	}
   
    $out2='';
   
    if ($GLOBALS['UseDiscounts']=='True'){
		$i=0;
		$Discounts=$GLOBALS['Discounts'];
		$sum = (count($Discounts,COUNT_RECURSIVE)/3) - 1; 
		$strings=$GLOBALS['strings'];
		
		if ($sum>0){
			
			$query = "SELECT tarif_guid, group_guid FROM `stat` WHERE `shortguid`='".mysql_real_escape_string($_SESSION['shortguid'])."';";
			$result = mysql_query($query,$GLOBALS['mysql']);
			if (mysql_num_rows($result)>0){
				$row = mysql_fetch_array($result);
				
					// 519 - Скидки
					$curr=$GLOBALS['curr'];

					$q=0;
					for($q=0; $q < $sum; $q++) 
					{ 
						$DiscountSize = $Discounts['DiscountSize'][$q];
						$MaxDiscountSize = $Discounts['MaxDiscountSize'][$q];
						$DiscountsLinkedGUIDs = $Discounts['DiscountsLinkedGUIDs'][$q];
						
						if ((strpos($DiscountsLinkedGUIDs,$row[0].'||',0)!==false) || (strpos($DiscountsLinkedGUIDs,$row[1].'||',0)!==false) || (strpos($DiscountsLinkedGUIDs,'12211221122112||',0)!==false)){
							
							// 520 - При своевременном пополнении скидка до %s!
							$out2.='<div class="'.(($q>0)?'m5':'m3').'">* '.sprintf($strings[520], $MaxDiscountSize).'</div>';
							$i +=1;
						}
					
					}
				
			}
			
			//if ($i<=1){$out2 ='<br>'.$out2;}
		}
   }
   

	return $out.$out2;
}

function Onpay()
{
	if ($GLOBALS['EnableOnpay']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['OnPay_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['OnPay_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['OnPay_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}

	
	
	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseOnpayStdTitle']==1)?($strings[23]):($GLOBALS['OnpayTitle']));
	
	$crumbs = menuManager('four',array("./onpay.php",$Title));
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 223 - Оплата через платежную систему Onpay
	$result['page_title'] = $strings[223];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if (isset($_REQUEST['billme']))
	{

		if ($GLOBALS['EnableOnpay'] <> "True")
		{
			// 224 - Пополнение счёта через платёжную систему Onpay запрещено!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[224].'</div>
						</div>
					</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((float)$_POST['sum'] < $GLOBALS['Onpay_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[207].' '.$GLOBALS['Onpay_MinPlat'].' '.$GLOBALS['curr'].'</div>
						</div>
					</div>
					<script>Refresh("onpay.php",4)</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		$login = mysql_real_escape_string($_SESSION['login']);
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		include 'payin/onpay/onpay_functions.php';
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
		
		$Onpay_Login = $GLOBALS['OnpayLogin'];
		$Onpay_SecretCode = $GLOBALS['OnpaySecrertWord'];	
		
		$OnpayID = $otherinfo[118];
		if ((int)$OnpayID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$OnpayID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Onpay_Login = $Params['Onpay_Login'];
				$Onpay_SecretCode = $Params['Onpay_SecretCode'];
				
			}
		} else  {
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 3;";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Onpay_Login = $Params['Onpay_Login'];
				$Onpay_SecretCode = $Params['Onpay_SecretCode'];
				
										   
			}
		}
		
		$_POST['sum']=GetSummWithComission((float)$_POST['sum'],(float)$GLOBALS['Onpay_Commission'],(float)$GLOBALS['Onpay_Commission2'],($GLOBALS['Onpay_ProviderComission']!='True'));
		$_REQUEST['sum']=$_POST['sum'];
		
		//echo $_POST['sum'];exit();
		
		
		// 208 - Назад к заполнению формы
		$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.process_first_step($Onpay_Login, $Onpay_SecretCode).'</div>
						</div>
					</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
		
		
		$AllComm = (float)$GLOBALS['Onpay_Commission'];
		if ($GLOBALS['Onpay_ProviderComission']<>'true'){
			$AllComm = $AllComm + (float)$GLOBALS['Onpay_Commission2'];
		}
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm>0)?$strings[211]." ".$AllComm.'%':'';		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<i class="fas fa-piggy-bank"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="m2">'.sprintf($strings[443],'Onpay').'</div>
							<div class="i m3">'.$strings[220].':</div>
							<form method="POST" target="_blank">
								<div class="m2">
								<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].' '.$comm2.'
								</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
								// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
								// 218 - Оплатить
								$result['out'] .= '
									<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									'.$comm.PrintBonuses().'<br>
									<br>
										<font size="2" color="darkgray">
										<div class="m2"> <img src="img/visa_mir.jpg"><a href="https://onpay.ru/" target="_blank"><img src="img/onpay.jpg"></a></div>
										<div class="m2"> <b>Уважаемый клиент!</b><br> 
Вы можете оплатить свой заказ онлайн с помощью банковской карты через платежный сервис
компании Onpay. После подтверждения заказа Вы будете перенаправлены на защищенную платежную
страницу Onpay, где необходимо будет ввести данные для оплаты заказа. После успешной оплаты на
указанную в форме оплаты электронную почту будет направлен электронный чек с информацией о
заказе и данными по произведенной оплате.</div>
										<div class="m2"> <b>Гарантии безопасности</b><br> 
Безопасность процессинга Onpay подтверждена сертификатом стандарта безопасности данных
индустрии платежных карт PCI DSS. Надежность сервиса обеспечивается интеллектуальной
системой мониторинга мошеннических операций, а также применением 3D Secure - современной
технологией безопасности интернет-платежей. </div>
										<div class="m2"> Данные Вашей карты вводятся на специальной защищенной платежной странице. Передача
информации в компанию Onpay происходит с применением технологии шифрования TLS. Дальнейшая
передача информации осуществляется по закрытым банковским каналам, имеющим наивысший
уровень надежности.</div>
										<div class="m2"> <b>Onpay не передает данные Вашей карты магазину и иным третьим лицам!</b></div>
										<div class="m2"> Если Ваша карта поддерживает технологию 3D Secure, для осуществления платежа, Вам необходимо
будет пройти дополнительную проверку пользователя в банке-эмитенте (банк, который выпустил
Вашу карту). Для этого Вы будете направлены на страницу банка, выдавшего карту. Вид проверки
зависит от банка. Как правило, это дополнительный пароль, который отправляется в SMS, карта
переменных кодов, либо другие способы.</div>
									</font>
							</form>
						</div>
					</div>'; 
		return parse_template($result, './template/default.php',NULL,FALSE,true) ; 
	}	
}


function TinkoffMoney()
{
	if ($GLOBALS['EnableTinkoff']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Tinkoff_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Tinkoff_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Tinkoff_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseTinkoffStdTitle']==1)?($strings[397]):($GLOBALS['TinkoffTitle']));
	
	$crumbs = menuManager('four',array("./tinkoff.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 433 - Оплата через платежную систему Тинькофф Банк
	$result['page_title'] = $strings[433];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
		
	
	if(isset($_POST['sum'])&&(int)$_POST['sum']>0){
		
		$_POST['sum'] = GetSummWithComission($_POST['sum'],(float)$GLOBALS['Tinkoff_Commission'],(float)$GLOBALS['Tinkoff_Commission2']);
		
		header('Location: tinkoff2.php?paysize='.$_POST['sum']);exit();
		
	} else {
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		//if ($result['turbo'] =='True') {
		//	$allCost += intval($result['tarifs']['turbo_cost']);
		//}
		
		if ($allCost==0){
			$allCost = $OtherInfo[89];
		}
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Tinkoff_Commission']+(float)$GLOBALS['Tinkoff_Commission2'];
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		if ($allCost < 0)  {$allCost = 100;}
		if (($allCost == 0) && ($Bal<0)) {
			$allCost = -$Bal;
		}		
		
		if ($allCost<$GLOBALS['Tinkoff_MinPlat']){$allCost=$GLOBALS['Tinkoff_MinPlat'];}
		
		
		// 211 - с учётом комиссии
		$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		// 220 - Сумма платежа
	 // 443 - Для пополнения баланса введите нужную сумму и нажмите кнопку «Оплатить», после чего произведите платёж на защищенном сервере системы %s.
	 // 497 - Yandex.Касса
		$result['out'] = '
			'.$crumbs.'
			<div class="content">
				<div class="four">
					<i class="fas fa-piggy-bank"></i>
					<div class="h3 m4">'.$result['page_title'].'</div>
					<div class="m2">'.sprintf($strings[443],'Tinkoff').'</div>
					<div class="i m3">'.$strings[220].':</div>
					<form method="POST" name="sumform" target="_blank">
						<div class="m2">
							<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
						</div>
						<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					</form>
				'.PrintBonuses().'
				</div>
			</div>';
			
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
	}

}



function YaKassa()
{// вывод страницы оплаты

	if ($GLOBALS['EnableYandexKassaPay']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['YandexKassa_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['YandexKassa_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['YandexKassa_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	

	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');

	$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
	$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	
	$uri=explode("yakassa.php", $url);
	$return_url=$GLOBALS['PortalAddress'] . 'pay2.php?action=1';//После оплаты перенаправить на этот url


	$result=getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseYandexKassaStdTitle']==1)?($strings[496]):($GLOBALS['YandexKassaTitle']));
	
	$crumbs = menuManager('four',array("./yakassa.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 496 - Платёж через Yandex.Касса
	$result['page_title'] = $strings[496];
	 
				
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	
	if(isset($_POST['sum'])&&(int)$_POST['sum']>0){
		
		
	$shop_id=$GLOBALS['YaKassaShopID'];
	$shop_key=$GLOBALS['YaKassaShopKey'];
	$YaKassaVAT='2';
	$YaKassaPayType='full_prepayment';
	
	
	$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
	
	
		$YaKassaID = $otherinfo[120];
		if ((int)$YaKassaID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$YaKassaID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$shop_id = $Params['YaKassaShopID'];
				$shop_key = $Params['YaKassaShopKey'];
				
				if (isset($Params['YaKassaVAT'])){
					$YaKassaVAT = $Params['YaKassaVAT'];
				}
				if (isset($Params['YaKassaPayType'])){
					
					if ((int)$Params['YaKassaPayType']=='1'){
						$YaKassaPayType = 'full_payment';
					}
				}
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 28";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);
				
				$YaKassaID=$row[1];

				$shop_id = $Params['YaKassaShopID'];
				$shop_key = $Params['YaKassaShopKey'];
				
				if (isset($Params['YaKassaVAT'])){
					$YaKassaVAT = $Params['YaKassaVAT'];
				}
				if (isset($Params['YaKassaPayType'])){
					
					if ((int)$Params['YaKassaPayType']=='1'){
						$YaKassaPayType = 'full_payment';
					}
				}
			}
		}
		
	
		if ($GLOBALS['EnableYandexKassaPay'] <> "True")
		{
			// 502 - Пополнение счёта через платёжную систему Yandex.Деньги запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[502].'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
	
	
		$_POST['sum']=(float)$_POST['sum'];
		
		$_POST['sum']=GetSummWithComission($_POST['sum'],(float)$GLOBALS['YandexKassa_Commission'],(float)$GLOBALS['YandexKassa_Commission2']);
		
		//echo $_POST['sum'];exit();
		
		$pinfoA=explode('||',$result['pinfo']);
		if (strlen($result['FIO'])<3){$result['FIO']='Unknown person';}
		
		$MailOrTel='"phone": "'.addslashes($pinfoA[0]).'"';
		if ((strlen($pinfoA[1])>3)&&(strpos($pinfoA[1], '@')>1)){
			$MailOrTel='"email": "'.addslashes($pinfoA[1]).'"';
		}
		
		
		$parameters='{
	        "amount": {
	          "value": "'.$_POST['sum'].'",
	          "currency": "RUB"
	        },
	        "confirmation": {
	          "type": "redirect",
	          "return_url": "'.$return_url.'"
	        },
	        "capture": true,
	        "description": "Payment '.date("d.m.y H:i:s").' for  '. $_SESSION['shortguid'] .'",
			"receipt": {
			  "customer": {
				"full_name": "'.addslashes($result['FIO']).'",
				'.$MailOrTel.'
			  },
			  "items": [
				{
				  "description": "'.addslashes($result['tarif']).'",
				  "quantity": "1.00",
				  "amount": {
					"value": "'.$_POST['sum'].'",
					"currency": "RUB"
				  },
				  "vat_code": "'.$YaKassaVAT.'",
				  "payment_mode": "'.$YaKassaPayType.'",
				  "payment_subject": "commodity"
				}
			  ]
			}
	    }';
		
		
		//print_r($parameters);exit();
		
		//str_replace(',','.',$OtherInfo[29]);
		//echo $shop_id . ':' . $shop_key; exit();
		//echo $parameters; exit();
		$ch=curl_init();

		curl_setopt($ch,CURLOPT_URL,'https://api.yookassa.ru/v3/payments');
		 
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
		curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_SSLv3' );
		//curl_setopt($ch, CURLOPT_CAPATH, PATH_TO_CERT_DIR);
	
	
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Idempotence-Key: MB-'.RandomString(5).'-'.$_SESSION['shortguid'],
			'Content-Type: application/json'
		));
		
		curl_setopt($ch,CURLINFO_HEADER_OUT,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$parameters);
		curl_setopt($ch,CURLOPT_USERPWD,"$shop_id:$shop_key");


		$r=curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		$r=json_decode($r,true);
		
		//echo 'Result = ' . $r; 
		//echo 'info = ' . $info;
		//
		
		if (strlen($error)>0){
			echo 'Error = ' . $error;
			exit();
		}	
		
		
			
		if(isset($r['confirmation']['confirmation_url'])){
			$sql="INSERT INTO`operations_yandexkassa`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$result['shortguid']."','".$_POST['sum']."','".$r['id']."','".$r['status']."','".date("Y-m-d H:i:s")."')";
			mysql_query($sql,$mysql);
			
			if (mysql_errno()>0){
				$fp = fopen("payin/yandex.kassa/payin.txt", "a"); 
				fputs($fp, "\n\nDate = " . date("d.m.y H:i:s")."\n");
				fputs($fp, "MySQL Err: " . mysql_error() ."\n");
				fputs($fp, "MySQL query: " . $sql ."\n");
				fclose($fp);
			}
			
			PaymntToLog($YaKassaID,$r['id']);
			
			header('Location: '.$r['confirmation']['confirmation_url']);
		} else {
			print_r($r);
		}
		
	}else{
	
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

				
		$pcomm=1;
		$AllComm = (float)$GLOBALS['YandexKassa_Commission']+(float)$GLOBALS['YandexKassa_Commission2'];
		// 211 - с учётом комиссии
		$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
	
	
	 // 220 - Сумма платежа
	 // 443 - Для пополнения баланса введите нужную сумму и нажмите кнопку «Оплатить», после чего произведите платёж на защищенном сервере системы %s.
	 // 497 - Yandex.Касса
		$result['out'] = '
			'.$crumbs.'
			<div class="content">
				<div class="four">
					<i class="fas fa-piggy-bank"></i>
					<div class="h3 m4">'.$strings[496].'</div>
					<div class="m2">'.sprintf($strings[443],$strings[497]).'</div>
					<div class="i m3">'.$strings[220].':</div>
					<form method="POST" name="sumform">
						<div class="m2">
							<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
						</div>
						<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					</form>
				'.PrintBonuses().'
				</div>
			</div>';
	}
	
	
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
}

function gen_uuid($p1=true){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',mt_rand(0,0xffff),mt_rand(0,0x0fff)|0x4000,mt_rand(0,0x3fff)|0x8000,mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff));
	}


function PaycomMoney(){
	if ($GLOBALS['EnablePayMe']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayMe_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayMe_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['PayMe_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePayMeStdTitle']==1)?($strings[587]):($GLOBALS['PayMeTitle']));
	
	$crumbs = menuManager('four',array("./payme.php",$Title));
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 587 - Оплата через платежную систему Paycom.uz
	$result['page_title'] = $strings[587];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{
		if ((int)$_POST['sum'] < $GLOBALS['PayMe_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['PayMe_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("payme.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("payme.php", $url);
		
		$server_url = $uri[0] . "pay2.php?action=1";

		//$server_url = str_replace(':','%3A',$server_url);
		//$server_url = str_replace('/','%2F',$server_url);
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['PayMe_Commission'],(float)$GLOBALS['PayMe_Commission2']);
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		$PayMe_MerchantID='';
		
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$LinkedPayMe=mysql_real_escape_string($a[156]);
			mysql_free_result($mysql_result);
		
		
		if ((int)$LinkedPayMe >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$LinkedPayMe';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);
				$PayMe_MerchantID = $Params['PayMe_MerchantID'];
				$PayMe_KEY = $Params['PayMe_KEY'];
			}
		} else  {
			
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 51";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				
				$Params = GetParams($row[0]);

				$PayMe_MerchantID = $Params['PayMe_MerchantID'];
				$PayMe_KEY = $Params['PayMe_KEY'];
			}
		}
		
		
		$paysize=$paysize*100;
		$url = 'https://checkout.paycom.uz/'.base64_encode("m=$PayMe_MerchantID;ac.order_id=$login;a=$paysize;c=$server_url");
		
		echo "<script>location.href='$url'</script>";
		
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['PayMe_Commission'];
		//if (!$GLOBALS ["PayMe_NoCommision"]=='True'){
			$AllComm = $AllComm + (float)$GLOBALS['PayMe_Commission2'];
		//}
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['PayMe_MinPlat']){$allCost=$GLOBALS['PayMe_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payplug','Paycom.uz',$strings[203]).'</div>
									<div class="m2">'.sprintf($strings[443],'Paycom.uz').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}


function YaMoney()
{
	
	if ($GLOBALS['EnableYandexPay']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Yandex_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Yandex_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Yandex_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseYandexStdTitle']==1)?($strings[24]):($GLOBALS['YandexTitle']));
	
	$crumbs = menuManager('four',array("./yamoney.php",$Title));	
	//CheckProfile($result,$crumbs,'four');
	
	// 225 - Оплата через платежную систему Yandex.Деньги
	$result['page_title'] = $strings[225];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{

		if ($GLOBALS['EnableYandexPay'] <> "True")
		{
			// 226 - Пополнение счёта через платёжную систему Yandex.Деньги запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[226].'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((int)$_POST['sum'] < $GLOBALS['Yandex_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['Yandex_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("yamoney.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("yamoney.php", $url);
		//if (strlen($GLOBALS['PaySite'])>5) {
		//	$server_url = $GLOBALS['PaySite'] . "/pay2.php?action=1";
		//} else {
			
			//if (isset($_SERVER['HTTPS'])){$pref='https://';}else{$pref='http://';}
			
			$server_url = $uri[0] . "pay2.php?action=1";
		//}
		//$server_url = str_replace(':','%3A',$server_url);
		//$server_url = str_replace('/','%2F',$server_url);
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['Yandex_Commission'],(float)$GLOBALS['Yandex_Commission2'],($GLOBALS['Yandex_NoCommision']=='True'));
		
		//echo $paysize;exit();
		
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		
		
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$YandexPay=mysql_real_escape_string($a[76]);
			mysql_free_result($mysql_result);
		
		
		if (strlen($YandexPay)<8){
			$sql = "SELECT * FROM `yandex_params`;";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$YandexPay=mysql_real_escape_string($row[0]);
			mysql_free_result($mysql_result);
		}
		
		
		$purse = '';
		if (strlen($YandexPay)>0){
			$sql = "SELECT * FROM `yandex_params` WHERE `guid` = $YandexPay;";
			$mysql_result = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysql_result)>0){
				$row = mysql_fetch_array($mysql_result);
				$purse=trim($row[2]);
				$private_key=$row[3];
			}
		}
			
			if (strlen($purse)==0){
				$sql = "SELECT * FROM `yandex_params`;";
				$mysql_result = mysql_query($sql,$mysql);
				if (mysql_num_rows($mysql_result)>0){
					$row = mysql_fetch_array($mysql_result);
					$purse=trim($row[2]);
					$private_key=$row[3];
					$YandexPay=$row[0];
				}
			}
		
		$Yandex_PhonePay='';
		$order_id=RandomString(6) . '_' . $result['shortguid'];
		//if ($GLOBALS['Yandex_PhonePay']=='True'){$Yandex_PhonePay='&mobile-payment-type-choice=on';}
		$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.$strings[225].'</div>
									<div class="i m3">'.$strings[209].'</div>
									<div class="m2">
										<form method="POST" action="https://yoomoney.ru/quickpay/confirm.xml" id="iframe_data_form">
											<input type="hidden" name="receiver" value="'.$purse.'" />
											<input type="hidden" name="label" value="'.$order_id.'" />
											<input type="hidden" name="targets" value="Plata za Internet ('.$contract.')" />
											<input type="hidden" name="sum" value="'.$paysize.'" />
											<input type="hidden" name="successURL" value="'.$server_url.'" />
											<input type="hidden" name="quickpay-form" value="shop" />
											<input type="hidden" name="paymentType" value="AC" />
											<input type="hidden" name="need-phone" value="false" />
											<input type="hidden" name="need-email" value="false" />
											<input type="hidden" name="need-fio" value="false" />
											<input type="hidden" name="need-address" value="false" />
										</form>
									</div>
									' . $comm . PrintBonuses() . '
								</div>
							</div>';

		//if ($GLOBALS['Yandex_NoCommision']=='False'){
			// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
		//	$comm="<br>".$strings[215];
		//}
		// 208 - Назад к заполнению формы
		$result['out'].= '
		<script type="text/javascript">
			
			document.getElementById(\'iframe_data_form\').submit();
		
			function GetHTTP(url) {
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url, false);
				request.send(null);
				return request.responseText;
			}
		</script>
		';
		
		PaymntToLog($YandexPay,$order_id);
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Yandex_Commission'];
		if (!$GLOBALS ["Yandex_NoCommision"]=='True'){
			$AllComm = $AllComm + (float)$GLOBALS['Yandex_Commission2'];
		}
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['Yandex_MinPlat']){$allCost=$GLOBALS['Yandex_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.$strings[225].'</div>
									<div class="m2">'.sprintf($strings[443],'Yandex').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}



function SelfWork()
{
	
	if ($GLOBALS['UseSelfWork']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['SelfWork_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['SelfWork_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['SelfWork_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	//14 - Платёж через «Payeer»
	$Title = htmlspecialchars(($GLOBALS['UseSelfWork_StdTitle']==1)?(str_replace('Payeer', 'SelfWork',$strings[14])):($GLOBALS['SelfWorkTitle']));
	
	$crumbs = menuManager('four',array("./selfwork.php",$Title));	
	//CheckProfile($result,$crumbs,'four');
	
	// 203 - Оплата через платежную систему Payplug
	$result['page_title'] = str_replace('Payplug', 'SelfWork',$strings[203]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{

		if ($GLOBALS['UseSelfWork'] <> "True")
		{
			// 529 - Пополнение счёта через платёжную систему Prodamus запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'. str_replace('Prodamus', 'SelfWork', $strings[529]).'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((int)$_POST['sum'] < $GLOBALS['SelfWork_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['SelfWork_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("selfwork.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['SelfWork_Commission'],0,false);		
		$amount=intval($paysize*100);
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		$sql = "SELECT otherinfo, shortguid, tarif FROM stat WHERE user_name = '$login';";
			
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$tarif_name=$row[2];
			$SelfWork_ID=mysql_real_escape_string($a[175]);
			mysql_free_result($mysql_result);
		
		$SelfWorkSecretCode='?';
		$SelfWorkServiceName=$tarif_name;
		if ((int)$SelfWork_ID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$SelfWork_ID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$SelfWorkSecretCode = $Params['SelfWorkSecretCode'];
				$SelfWorkServiceName = $Params['SelfWorkServiceName'];				
			}
		} else  {
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 72";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$SelfWorkSecretCode = $Params['SelfWorkSecretCode'];
				$SelfWorkServiceName = $Params['SelfWorkServiceName'];
				
			}
		}
		
		$Yandex_PhonePay='';
		$order_id=RandomString(6) . '_' . $result['shortguid'];
		
		$SelfWorkSign = hash("sha256",$order_id.$amount.$SelfWorkServiceName.'1'.$amount.$SelfWorkSecretCode);
		
		$result['out'].= $crumbs.'
							<div class="contentsdfsd">
								<div class="four243">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Prodamus', 'SelfWork', $strings[529]).'</div>
									<div class="i m3">'.$strings[209].'</div>
									<div class="m23">
										<form method="POST" action="https://pro.selfwork.ru/merchant/v1/init" id="iframe_data_form">
											<input  name="order_id" value="'.$order_id.'" />
											<input  name="amount" value="'.$amount.'" />
											<input  name="info[0][name]" value="'.$SelfWorkServiceName.'" />
											<input  name="info[0][quantity]" value="1" />
											<input  name="info[0][amount]" value="'.$amount.'" />
											<input name="signature" value="'.$SelfWorkSign.'" />
											<button id="smz-init-payment-button" type="submit">Оплатить33</button>
										</form>
									</div>
									' . $comm . PrintBonuses() . '
								</div>
							</div>
							<script src="https://pro.selfwork.ru/merchant-app/smz-init-payment.js"></script>';

		//if ($GLOBALS['Yandex_NoCommision']=='False'){
			// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
		//	$comm="<br>".$strings[215];
		//}
		// 208 - Назад к заполнению формы
		$result['out'].= '
		<script type="text/javascript">
			
			//document.getElementById(\'iframe_data_form\').submit();
		
			function GetHTTP(url) {
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url, false);
				request.send(null);
				return request.responseText;
			}
		</script>
		';
		
		PaymntToLog($SelfWork_ID,$order_id);
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['SelfWork_Commission'];
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['SelfWork_MinPlat']){$allCost=$GLOBALS['SelfWork_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','SelfWork',$strings[227]).'</div>
									<div class="m2">'.sprintf($strings[443],'SelfWork').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}




function PayKeeper()
{
	
	if ($GLOBALS['UsePayKeeper']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayKeeper_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayKeeper_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['PayKeeper_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePayKeeperStdTitle']==1)?(str_replace('Payplug','PayKeeper',$strings[25])):($GLOBALS['PayKeeperTitle']));
	
	$crumbs = menuManager('four',array("./paykeeper.php",$Title));

	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	$result['page_title'] = $Title;
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{

		if ($GLOBALS['UsePayKeeper'] <> "True")
		{
			// 228 - Пополнение счёта через платёжную систему Payeer запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.str_replace('Payeer','PayKeeper',$strings[227]).'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((int)$_POST['sum'] < $GLOBALS['PayKeeper_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['PayKeeper_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("paykeeper.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['PayKeeper_Commission'],0,false);
		
		$paysize=number_format($paysize,2,'.','');
		
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		
		
		$sql = "SELECT otherinfo, shortguid, pinfo FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$pinfo=explode('||',$row[2]);
			$client_email=$pinfo[1];
			$client_phone=$pinfo[0];
			
			$PayKeeper=mysql_real_escape_string($a[171]);
			mysql_free_result($mysql_result);
		
		
		
		if ((int)$PayKeeper >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$PayKeeper';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$PayKeeper_Merchant_ID = $Params['PayKeeper_Merchant_ID'];
				$PayKeeper_VerificationCode = $Params['PayKeeper_VerificationCode'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 64";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$PayKeeper = $row[1];
				
				$Params = GetParams($row[0]);

				$PayKeeper_Merchant_ID = $Params['PayKeeper_Merchant_ID'];
				$PayKeeper_VerificationCode = $Params['PayKeeper_VerificationCode'];
								
			}
		}
		
		

		$order_id=RandomString(6) . '_' . $result['shortguid'];
		$service_name=$strings[408] . " '" . $result['tarif'] . "'";
		
		
		$sign = hash('sha256', $paysize.$result['shortguid'].$order_id.$service_name.$client_email.$client_phone.$PayKeeper_VerificationCode);
		


		
		$sql="INSERT INTO `operations_paykeeper`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$result['shortguid']."','".$paysize."','".$order_id."','0','".date("Y-m-d H:i:s")."');";
		mysql_query($sql,$GLOBALS['mysql']);
				
		PaymntToLog($PayKeeper,$order_id);
		
		$PayKeeper_Merchant_ID = trim($PayKeeper_Merchant_ID,'/');
		
		//echo $PayKeeper_Merchant_ID;exit();
		
		$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','PayKeeper',$strings[227]).'</div>
									<div class="i m3">'.$strings[209].'</div>
									<div class="m2" style="display:none;">
										<form method="post" action="'.$PayKeeper_Merchant_ID.'/create/" >
											 <input type="hidden" name="sum" value="'.$paysize.'">
											 <input type="hidden" name="orderid" value="'.$order_id.'">
											 <input type="hidden" name="clientid" value="'.$result['shortguid'].'">
											 <input type="hidden" name="service_name" value="'.$service_name.'">
											 <input type="hidden" name="client_email" value="'.$client_email.'">
											 <input type="hidden" name="client_phone" value="'.$client_phone.'">
											 <input type="hidden" name="sign" value="'.$sign.'">
											 <input type="submit" id="gopay" value="">
										</form>
									</div>
									' . $comm . PrintBonuses() . '
								</div>
							</div>';

		
		// 208 - Назад к заполнению формы
		$result['out'].= '
		<script type="text/javascript">
			
			document.getElementById(\'gopay\').click();
			
			//window.location="index.php";
			
			function GetHTTP(url) {
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url, false);
				request.send(null);
				return request.responseText;
			}
		</script>
		';
		
		PaymntToLog($PayKeeper,$order_id);
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['PayKeeper_Commission'];
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['PayKeeper_MinPlat']){$allCost=$GLOBALS['PayKeeper_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','PayKeeper',$strings[227]).'</div>
									<div class="m2">'.sprintf($strings[443],'PayKeeper').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}


function PayAnywayMoney()
{
	
	if ($GLOBALS['UsePayAnyway']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PayAnyway_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PayAnyway_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['PayAnyway_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePayAnywayStdTitle']==1)?(str_replace('Payplug','PayAnyway',$strings[25])):($GLOBALS['PayAnywayTitle']));
	
	$crumbs = menuManager('four',array("./payanyway.php",$Title));	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	$result['page_title'] = $Title;
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{

		if ($GLOBALS['UsePayAnyway'] <> "True")
		{
			// 228 - Пополнение счёта через платёжную систему Payeer запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.str_replace('Payeer','PayAnyway',$strings[227]).'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((int)$_POST['sum'] < $GLOBALS['PayAnyway_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['PayAnyway_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("yamoney.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['PayAnyway_Commission'],0,false);
		
		$paysize=number_format($paysize,2,'.','');
		
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		
		
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];
			$PayAnyway=mysql_real_escape_string($a[170]);
			mysql_free_result($mysql_result);
		
		
		
		if ((int)$PayAnyway >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$PayAnyway';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$PayAnyWay_MNT_ID = $Params['PayAnyWay_MNT_ID'];
				$PayAnyWay_VerificationCode = $Params['PayAnyWay_VerificationCode'];
				$PayAnyWay_isTest = $Params['PayAnyWay_isTest'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 63";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$PayAnyway = $row[1];
				
				$Params = GetParams($row[0]);

				$PayAnyWay_MNT_ID = $Params['PayAnyWay_MNT_ID'];
				$PayAnyWay_VerificationCode = $Params['PayAnyWay_VerificationCode'];
				$PayAnyWay_isTest = $Params['PayAnyWay_isTest'];
								
			}
		}
		
		
		$MNT_CURRENCY_CODE='RUB';
		if ($GLOBALS['curr']=='$' || $GLOBALS['curr']=='USD'){
			$MNT_CURRENCY_CODE='USD';
		} elseif ($GLOBALS['curr']=='€' || $GLOBALS['curr']=='EUR'){
			$MNT_CURRENCY_CODE='EUR';
		}
		
		$moneta_locale='ru';
		if ($GLOBALS['Language']=='eng'){
			$moneta_locale='en';
		}
		
		$order_id=RandomString(6) . '_' . $result['shortguid'];
		$MNT_SIGNATURE = md5($PayAnyWay_MNT_ID.$order_id.$paysize.$MNT_CURRENCY_CODE.$result['shortguid'].strtolower($PayAnyWay_isTest).$PayAnyWay_VerificationCode);
		
		$DstURL=(($PayAnyWay_isTest=='0')||($PayAnyWay_isTest=='False')||($PayAnyWay_isTest=='false'))?('https://www.payanyway.ru/assistant.htm'):('https://demo.moneta.ru/assistant.htm');
		
		$sql="INSERT INTO `operations_payanyway`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$result['shortguid']."','".$paysize."','".$order_id."','0','".date("Y-m-d H:i:s")."');";
		mysql_query($sql,$GLOBALS['mysql']);
				
		PaymntToLog($PayAnyway,$order_id);
		
		
		$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.$strings[225].'</div>
									<div class="i m3">'.$strings[209].'</div>
									<div class="m2">
										<form method="post" action="'.$DstURL.'">
											 <input type="hidden" name="MNT_ID" value="'.$PayAnyWay_MNT_ID.'">
											 <input type="hidden" name="MNT_TRANSACTION_ID" value="'.$order_id.'">
											 <input type="hidden" name="MNT_CURRENCY_CODE" value="'.$MNT_CURRENCY_CODE.'">
											 <input type="hidden" name="MNT_SUBSCRIBER_ID" value="'.$result['shortguid'].'">
											 <input type="hidden" name="MNT_AMOUNT" value="'.$paysize.'">
											 <input type="hidden" name="moneta.locale" value="'.$moneta_locale.'">
											 <input type="hidden" name="MNT_SIGNATURE" value="'.$MNT_SIGNATURE.'">
											 <input type="submit" id="gopay" value="">
										</form>
									</div>
									' . $comm . PrintBonuses() . '
								</div>
							</div>';

		
		// 208 - Назад к заполнению формы
		$result['out'].= '
		<script type="text/javascript">
			
			document.getElementById(\'gopay\').click();
			
			//window.location="index.php";
			
			function GetHTTP(url) {
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url, false);
				request.send(null);
				return request.responseText;
			}
		</script>
		';
		
		PaymntToLog($PayAnyway,$order_id);
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['PayAnyway_Commission'];
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['PayAnyway_MinPlat']){$allCost=$GLOBALS['PayAnyway_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','PayAnyway',$strings[227]).'</div>
									<div class="m2">'.sprintf($strings[443],'PayAnyway').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}



function PSbank()
{
	
	if ($GLOBALS['UsePSB']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['PSB_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['PSB_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['PSB_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePSBStdTitle']==1)?(str_replace('Payplug','Промсвязьбанк',$strings[25])):($GLOBALS['PSBTitle']));
	
	$crumbs = menuManager('four',array("./psbank.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 203 - Оплата через платежную систему Payplug
	$result['page_title'] = str_replace('Payplug', 'Промсвязьбанк',$strings[203]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{
		
		if ((float)$_POST['sum'] < $GLOBALS['PSB_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['PSB_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("yamoney.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("psbank.php", $url);

		$backref = $uri[0] . "pay2.php?action=1";
		//$backref = str_replace(':','%3A',$backref);
		//$backref = str_replace('/','%2F',$backref);
		
		$notify_url = $uri[0] . "payin/psbank/payin.php";
		//$notify_url = str_replace(':','%3A',$notify_url);
		//$notify_url = str_replace('/','%2F',$notify_url);
		
		
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['PSB_Commission'],0,false);
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		
		
		$sql = "SELECT otherinfo, shortguid, pinfo FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$otherinfo = explode("||",$row[0]);
			$pinfo = explode("||",$row[2]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysql_result);
		
		
		
		
		$PSBID = $otherinfo[166];
		if ((int)$PSBID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$PSBID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$PSBMerchantID = $Params['PSBMerchantID'];
				$PSBTerminalID = $Params['PSBTerminalID'];
				$PSBMerchantName = $Params['PSBMerchantName'];
				$PSBKey1 = $Params['PSBKey1'];
				$PSBKey2 = $Params['PSBKey2'];
				$PSB_IsTest = $Params['PSB_IsTest'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 60";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$PSBID = $row[1];
				
				$Params = GetParams($row[0]);

				$PSBMerchantID = $Params['PSBMerchantID'];
				$PSBTerminalID = $Params['PSBTerminalID'];
				$PSBMerchantName = $Params['PSBMerchantName'];
				$PSBKey1 = $Params['PSBKey1'];
				$PSBKey2 = $Params['PSBKey2'];
				$PSB_IsTest = $Params['PSB_IsTest'];
								
			}
		}
		
		$TestPlace='';
		if (($PSB_IsTest=='1')||($PSB_IsTest=='True')||($PSB_IsTest=='true')){$TestPlace='test.';}
		
		$order_id=RandomString(18);// . '_' . $result['shortguid'];
		
		$sql="INSERT INTO `operations_psbank`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$result['shortguid']."','".str_replace(',','.',$_POST['sum'])."','".$order_id."','0','".date("Y-m-d H:i:s")."');";
		mysql_query($sql,$mysql);
		
		

	//Данные для отправки на ПШ
	$data = [
	 'amount' => number_format($_POST['sum'],2,'.',''),
	 'currency' => 'RUB',
	 'order' => $order_id,
	 'desc' => $GLOBALS['PortalName'] . ' - Оплата [' . $contract . ']',
	 'terminal' => $PSBTerminalID,
	 'trtype' => '1',
	 'merch_name' => $PSBMerchantName,
	 'merchant' => $PSBMerchantID,
	 'email' => $pinfo[1],
	 'timestamp' => gmdate("YmdHis"),
	 'nonce' => bin2hex(random_bytes(16)),
	 'backref' => $backref,
	 'notify_url' => $notify_url,
	 'cardholder_notify' => 'EMAIL',
	 //'merchant_notify' => 'EMAIL',
	 //'merchant_notify_email' => 'merchant@mail.test'
	];
	//Расчет P_SIGN
	$vars =
	["amount","currency","order","merch_name","merchant","terminal","email","trtype","timestamp","nonce","backref"
	];
	$string = '';
	foreach ($vars as $param){
	 if(isset($data[$param]) && strlen($data[$param]) != 0){
	 $string .= strlen($data[$param]) . $data[$param];
	 } else {
	 $string .= "-";
	 }
	}
	$key = strtoupper(implode(unpack("H32",pack("H32",$PSBKey1) ^ pack("H32",$PSBKey2))));
	$data['p_sign'] = strtoupper(hash_hmac('sha256', $string, pack('H*', $key)));
			
				
		$FormData='';
		foreach ($data as $param => $value) {
			$FormData .="<input type='hidden' name='" . strtoupper($param) . "' value='" . $value . "'/>";
		}
		
		$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','Модульбанк',$strings[227]).'</div>
									<div class="i m3">'.$strings[209].'</div>
									<div class="m2">
										<form id="payment_form" action="https://'.$TestPlace.'3ds.payment.ru/cgi-bin/cgi_link" method = "POST">
											'.$FormData.'
										</form>
									</div>
									' . $comm . PrintBonuses() . '
								</div>
							</div>';

		
		// 208 - Назад к заполнению формы
		$result['out'].= '
		<script type="text/javascript">
			
			document.getElementById(\'payment_form\').submit();
		
			function GetHTTP(url) {
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url, false);
				request.send(null);
				return request.responseText;
			}
		</script>
		';
		
		PaymntToLog($PSBID,$order_id);
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['PSB_Commission'];
				
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['PSB_MinPlat']){$allCost=$GLOBALS['PSB_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payplug','Промсвязьбанк',$strings[203]).'</div>
									<div class="m2">'.sprintf($strings[443],'Промсвязьбанк').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}



function CryptoCloud(){
	
	if ($GLOBALS['UseCryptoCloud']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['CryptoCloud_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['CryptoCloud_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['CryptoCloud_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseCryptoCloudStdTitle']==1)?(str_replace('Payplug','Crypto Cloud',$strings[25])):($GLOBALS['CryptoCloudTitle']));
	
	$crumbs = menuManager('four',array("./cryptocloud.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 203 - Оплата через платежную систему Payplug
	$result['page_title'] = str_replace('Payplug', 'Crypto Cloud',$strings[203]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{
		
		if ((float)$_POST['sum'] < $GLOBALS['CryptoCloud_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['CryptoCloud_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("cryptocloud.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}		
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['CryptoCloud_Commission'],0,false);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		$login=mysql_real_escape_string($result['user_name']);
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		
		
		$CryptoCloudShopID = '';
		$CryptoCloudAPIkey = '';
		$CryptoCloudSecretKey = '';
		
		$CryptoCloudID = $OtherInfo[178];
		if ((int)$CryptoCloudID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$CryptoCloudID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$CryptoCloudShopID = $Params['CryptoCloudShopID'];
				$CryptoCloudAPIkey = $Params['CryptoCloudAPIkey'];
				$CryptoCloudSecretKey = $Params['CryptoCloudSecretKey'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 77";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$CryptoCloudID = $row[1];
				
				$Params = GetParams($row[0]);
				
				$CryptoCloudShopID = $Params['CryptoCloudShopID'];
				$CryptoCloudAPIkey = $Params['CryptoCloudAPIkey'];
				$CryptoCloudSecretKey = $Params['CryptoCloudSecretKey'];				
			}
		}
		
		$order_id=RandomString(8) . '_' . $result['shortguid'];
		$summ = mysql_real_escape_string($paysize);
		$sql="INSERT INTO `operations_cryptocloud`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$result['shortguid']."','".$summ."','".$order_id."','0','".date("Y-m-d H:i:s")."');";
		mysql_query($sql,$GLOBALS['mysql']);
		
		PaymntToLog($CryptoCloudID,$order_id);
		
		$Locale = ($_COOKIE['MikroBILL_WEB_Language']!='rus')?('en'):('ru');
		
		$Currency='';
		$GLOBALS['curr']=trim($GLOBALS['curr'],' .');
		if ((mb_strtolower($GLOBALS['curr'])=='usd')||($GLOBALS['curr']=='$')){
			$Currency='USD';
		} elseif ((mb_strtolower($GLOBALS['curr'])=='eur')||($GLOBALS['curr']=='€')){
			$Currency='EUR';
		} elseif ((mb_strtolower($GLOBALS['curr'])=='грн.')||(mb_strtolower($GLOBALS['curr'])=='грн')||($GLOBALS['curr']=='₴')){
			$Currency='UAH';
		} elseif ((mb_strtolower($GLOBALS['curr'])=='руб')||($GLOBALS['curr']=='₽')){
			$Currency='RUB';
		} else {
			$Currency=mb_strtoupper($GLOBALS['curr']);
		}
		
		
		$PayContent = '<div style="background-color: rgba(255, 255, 255, 0.1); border-radius:12px; max-width:600px;"><link href="https://api.cryptocloud.plus/static/widget/v2/css/app.css" rel="stylesheet" >
<vue-widget shop_id="'.$CryptoCloudShopID.'" api_key="'.$CryptoCloudAPIkey.'" currency="'.$Currency.'" amount="'.$paysize.'" locale="'.$Locale.'" ></vue-widget>
</div><script src="https://api.cryptocloud.plus/static/widget/v2/js/app.js" ></script >';
		
		$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$PayContent.'</div>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
		
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['CryptoCloud_Commission'];
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['CryptoCloud_MinPlat']){$allCost=$GLOBALS['CryptoCloud_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payplug','Crypto Cloud',$strings[203]).'</div>
									<div class="m2">'.sprintf($strings[443],'Crypto Cloud').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	
}


function Ozon(){
	
	if ($GLOBALS['UseOzon']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Ozon_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Ozon_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Ozon_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseOzonStdTitle']==1)?(str_replace('Payplug','Ozon',$strings[25])):($GLOBALS['OzonTitle']));
	
	$crumbs = menuManager('four',array("./ozon.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 203 - Оплата через платежную систему Payplug
	$result['page_title'] = str_replace('Payplug', 'Crypto Cloud',$strings[203]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{
		
		if ((float)$_POST['sum'] < $GLOBALS['Ozon_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['Ozon_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("ozon.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}		
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['Ozon_Commission'],0,false);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		$login=mysql_real_escape_string($result['user_name']);
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		
		
		$OzonAccessKey = '';
		$OzonSecretKey = '';
		$OzonNotificationSecretKey = '';
		
		$OzonID = $OtherInfo[186];
		if ((int)$OzonID >-1){
			
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$OzonID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);
				
				$OzonAccessKey = $Params['OzonAccessKey'];
				$OzonSecretKey = $Params['OzonSecretKey'];
				$OzonNotificationSecretKey = $Params['OzonNotificationSecretKey'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 79;";
			$mysqlResult = mysql_query($sql,$mysql);
			
			if (mysql_num_rows($mysqlResult)>0){
				
				$row = mysql_fetch_array($mysqlResult);
				$OzonID = $row[1];
				
				$Params = GetParams($row[0]);
				
				$OzonAccessKey = $Params['OzonAccessKey'];
				$OzonSecretKey = $Params['OzonSecretKey'];
				$OzonNotificationSecretKey = $Params['OzonNotificationSecretKey'];				
			}
		}
		
		$order_id=RandomString(8) . '_' . $result['shortguid'];
		$summ = mysql_real_escape_string($paysize);
		$sql="INSERT INTO `operations_ozon`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$result['shortguid']."','".$summ."','".$order_id."','0','".date("Y-m-d H:i:s")."');";
		mysql_query($sql,$GLOBALS['mysql']);
		
		PaymntToLog($OzonID,$order_id);
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=GetSiteFolder();

		$backref = $uri . "pay2.php?action=1";
		$err = $uri . "pay2.php?action=3";
		$notify_url = $uri . "payin/ozon/payin.php";
		
		$Summ=(int)sprintf("%.0f", $paysize * 100);
		
		//     
		$Sign = sha1($OzonAccessKey . '' . $order_id . '' . 'PAY_ALGO_SMS' . '643' . $Summ);
		
		$Data = ['accessKey' => $OzonAccessKey,
				 'amount' => ['currencyCode' => '643', 'value' => $Summ],
				 'extId' => $order_id,
				 'failUrl' => $err,
				 'mode' => 'MODE_SHORTENED',
				 'notificationUrl' => $notify_url,
				 'paymentAlgorithm' => 'PAY_ALGO_SMS',
				 'requestSign' => $Sign,
				 'successUrl' => $backref
				];
		
		
		//print_r($Data);exit();
		
		 $json = json_encode($Data, JSON_UNESCAPED_UNICODE);
		
		$ch=curl_init();

		curl_setopt($ch,CURLOPT_URL,'https://payapi.ozon.ru/v1/createOrder');
		 
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
		curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_SSLv3' );
	
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json'
		));
		
		curl_setopt($ch,CURLINFO_HEADER_OUT,true);
		
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
		


		$r=curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);
		
		if (strlen($error)>0){
			echo ($error . '<br>');
		}
		
		curl_close($ch);
		
		$JSON=json_decode($r,true);
		
		$payLink='';
		
		if (isset($JSON['order']['payLink'])){
			$payLink=$JSON['order']['payLink'];
		} else {
			print_r ($JSON);
		}
		
		
		if (strlen($payLink)>0){
			header('Location: '.$payLink);
		} else {
			
			$result['out'].= $crumbs.'
								<div class="content">
									<div class="four">
										<div class="i m3"><br>Unknown error!</div>
									</div>
								</div>';
			
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Ozon_Commission'];
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['Ozon_MinPlat']){$allCost=$GLOBALS['Ozon_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payplug','Crypto Cloud',$strings[203]).'</div>
									<div class="m2">'.sprintf($strings[443],'Crypto Cloud').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	
}

function GetSiteFolder(){
	
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';

	$host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	$host = strtolower(trim($host));
	if (strpos($host, ':') !== false) {
		$host = explode(':', $host)[0];
	}
	$domain = preg_replace('/[^a-z0-9.-]/', '', $host);
	
	$script_name = $_SERVER['SCRIPT_NAME'];
	$current_dir = dirname($script_name);
	
	$current_dir = ($current_dir === DIRECTORY_SEPARATOR || $current_dir === '/') ? '/' : rtrim($current_dir, '/') . '/';
	
	$current_url = $protocol . $domain . $current_dir;
	
	return $current_url;
}


function Alphabank()
{
	
	if ($GLOBALS['UseAlphabank']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Alphabank_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Alphabank_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Alphabank_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseAlphabankStdTitle']==1)?(str_replace('Payplug','Альфабанк',$strings[25])):($GLOBALS['AlphabankTitle']));
	
	$crumbs = menuManager('four',array("./alphabank.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 203 - Оплата через платежную систему Payplug
	$result['page_title'] = str_replace('Payplug', 'Альфабанк',$strings[203]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{
		
		if ((float)$_POST['sum'] < $GLOBALS['Alphabank_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['Alphabank_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("alphabank.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("alphabank.php", $url);

		$backref = $uri[0] . "pay2.php?action=1";
		//$backref = str_replace(':','%3A',$backref);
		//$backref = str_replace('/','%2F',$backref);
		
		$notify_url = $uri[0] . "payin/alphabank/payin.php";
		//$notify_url = str_replace(':','%3A',$notify_url);
		//$notify_url = str_replace('/','%2F',$notify_url);
		
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['Alphabank_Commission'],0,false);
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		
		
		$sql = "SELECT `otherinfo`, `shortguid`, `pinfo`, `contract` FROM `stat` WHERE `user_name` = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$otherinfo = explode("||",$row[0]);
			$pinfo = explode("||",$row[2]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			$contract=$row[3];
			mysql_free_result($mysql_result);
		
		
		$AlphabankMerchant = '';
		$AlphabankPassword = '';
		$Alphabank_IsTest = '';
		
		$AlphabankID = $otherinfo[167];
		if ((int)$AlphabankID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$AlphabankID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$AlphabankMerchant = $Params['AlphabankMerchant'];
				$AlphabankPassword = $Params['AlphabankPassword'];
				$Alphabank_IsTest = $Params['Alphabank_IsTest'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 61";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$AlphabankID = $row[1];
				
				$Params = GetParams($row[0]);
				
				$AlphabankMerchant = $Params['AlphabankMerchant'];
				$AlphabankPassword = $Params['AlphabankPassword'];
				$Alphabank_IsTest = $Params['Alphabank_isTest'];				
			}
		}
		

		//$TestPlace='';
		//if (($PSB_IsTest=='1')||($PSB_IsTest=='True')||($PSB_IsTest=='true')){$TestPlace='test.';}
		
		$order_id=RandomString(8) . '_' . $result['shortguid'];
		
		$FirmName=('Оплата "'.$GLOBALS['PortalName'].'"');
		
		//408 - Тариф
		$TarifName=($strings[408]);
		$TarifName2=($result['tarif']);
		
		//$def1=array("name"=>"amount", "value"=>$_POST['sum'],"title"=>$FirmName);
		//$def1=json_encode($def1);
		//$def2=array("name"=>"orderNumber","value"=>$order_id,"title"=>"UserID","state"=>1);
		//$def2=json_encode($def2);
		//$def3=array("name"=>"description","value"=>$TarifName2,"title"=>$TarifName,"state"=>0);
		//$def3=json_encode($def3);
		
		//$def1=str_replace('{','%7B',$def1);
		//$def1=str_replace('}','%7D',$def1);
		
		//$def1=urlencode($def1);
		//$def2=urlencode($def2);
		//$def3=urlencode($def3);
		
		
		//$DstURL='https://alfa.rbsuat.com/payment/constructor/prepay.html?login='.$AlphabankMerchant.'&logo=1&def='.$def1.'&def='.$def2.'&def='.$def3.'&depositFlag=1';
		
		$DstURL=(($Alphabank_IsTest=='1')||($Alphabank_IsTest=='True')||($Alphabank_IsTest=='true'))?'https://alfa.rbsuat.com/payment/rest/register.do':'https://payment.alfabank.ru/payment/rest/register.do';
		
		
		//print('|'.$AlphabankMerchant.'|'.$AlphabankPassword.'|'.$Alphabank_IsTest.'|'.$DstURL);exit();
		
		$aData = array(
				'orderNumber' => $order_id,
				'amount' => intval((float)$paysize*100),
				'returnUrl' => $backref,
				'userName' => $AlphabankMerchant,
				'password' => $AlphabankPassword,
				'description' => $strings[408] . " '" . $result['tarif'] . "' (" . $contract . ")"
			);
			
			//echo $DstURL;
			//echo '<br>';
		//print_r($aData);exit();
		
		$curl=curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $DstURL);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
		curl_setopt($curl, CURLOPT_TIMEOUT, 8);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($aData));
		
		$response = curl_exec($curl);			
		$info = curl_getinfo($curl);
		$error = curl_error($curl);
		curl_close($curl);
		
		
		if (strlen($error)>0){
			echo 'error='.$error;
			exit();
		} else {
			
			$data=json_decode($response,true);
			
			if (!isset($data['errorMessage'])){
				$formUrl = $data['formUrl'];
				//$orderId = $data['orderId'];
				
				$shortguid = mysql_real_escape_string($_SESSION['shortguid']);
				$summ = mysql_real_escape_string($paysize);
				
				
				$sql="INSERT INTO `operations_alphabank`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$shortguid."','".$summ."','".$order_id."','0','".date("Y-m-d H:i:s")."');";
				mysql_query($sql,$GLOBALS['mysql']);
				
				PaymntToLog($AlphabankID,$order_id);
				
				header('location: ' . $formUrl);
			} else {
				echo 'error='.$data['errorMessage'];
				exit();
			}	
				
		}
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Alphabank_Commission'];
				
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['Alphabank_MinPlat']){$allCost=$GLOBALS['Alphabank_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payplug','Альфабанк',$strings[203]).'</div>
									<div class="m2">'.sprintf($strings[443],'Альфабанк').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}





function Modulbank()
{
	
	if ($GLOBALS['EnableModulbank']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Modulbank_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Modulbank_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Modulbank_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseModulbankStdTitle']==1)?(str_replace('Onpay', 'Модульбанк', $strings[23])):($GLOBALS['ModulbankTitle']));
	
	$crumbs = menuManager('four',array("./modulbank.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 223 - Оплата через платежную систему Onpay
	$result['page_title'] = str_replace('Onpay', "Модульбанк", $strings[223]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	$comm='';
	$comm2='';
	
	if (isset($_REQUEST['billme']))
	{
		
		if ((int)$_POST['sum'] < $GLOBALS['Modulbank_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['Modulbank_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("modulbank.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("modulbank.php", $url);
		//if (strlen($GLOBALS['PaySite'])>5) {
		//	$server_url = $GLOBALS['PaySite'] . "/pay2.php?action=1";
		//} else {
			
			//if (isset($_SERVER['HTTPS'])){$pref='https://';}else{$pref='http://';}
			
			$server_url = $uri[0] . "pay2.php?action=1";
		//}
		//$server_url = str_replace(':','%3A',$server_url);
		//$server_url = str_replace('/','%2F',$server_url);
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['Modulbank_Commission'],(float)$GLOBALS['Modulbank_Commission2'],($GLOBALS['Modulbank_NoCommision']=='True'));
		
		//echo $paysize;exit();
		
		
		$contract=str_replace('/','%2F',$result['contract']);
		
		$login = mysql_real_escape_string($_SESSION['login']);
		
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		
		
		
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";
			$mysql_result = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysql_result);
			$a = explode("||",$row[0]);
			$contract = $a[0];
			$short_guid=$row[1];

			mysql_free_result($mysql_result);
		
		
		$Modulbank_MerchantID = '';
		$Modulbank_SecretCode = '';
		
		$ModulbankID = $a[164];
		if ((int)$ModulbankID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$ModulbankID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Modulbank_MerchantID = $Params['Modulbank_MerchantID'];
				//$Modulbank_SecretCode = $Params['Modulbank_SecretCode'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 57;";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);
				
				$ModulbankID=$row[1];

				$Modulbank_MerchantID = $Params['Modulbank_MerchantID'];
				//$Modulbank_SecretCode = $Params['Modulbank_SecretCode'];
			}
		}
		
		$order_id=RandomString(6) . '_' . $result['shortguid'];
		$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','Модульбанк',$strings[227]).'</div>
									<div class="i m3">'.$strings[209].'</div>
									<div class="m2">
										<form method="POST" action="https://yoomoney.ru/quickpay/confirm.xml" id="iframe_data_form">
											<input type="hidden" name="receiver" value="'.$purse.'" />
											<input type="hidden" name="label" value="'.$order_id.'" />
											<input type="hidden" name="targets" value="Plata za Internet ('.$contract.')" />
											<input type="hidden" name="sum" value="'.$paysize.'" />
											<input type="hidden" name="successURL" value="'.$server_url.'" />
											<input type="hidden" name="quickpay-form" value="shop" />
											<input type="hidden" name="paymentType" value="AC" />
											<input type="hidden" name="need-phone" value="false" />
											<input type="hidden" name="need-email" value="false" />
											<input type="hidden" name="need-fio" value="false" />
											<input type="hidden" name="need-address" value="false" />
										</form>
									</div>
									' . $comm . PrintBonuses() . '
								</div>
							</div>';

		//if ($GLOBALS['Yandex_NoCommision']=='False'){
			// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
		//	$comm="<br>".$strings[215];
		//}
		// 208 - Назад к заполнению формы
		$result['out'].= '
		<script type="text/javascript">
			
			document.getElementById(\'iframe_data_form\').submit();
		
			function GetHTTP(url) {
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url, false);
				request.send(null);
				return request.responseText;
			}
		</script>
		';
		
		PaymntToLog($ModulbankID,$order_id);
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
	else
	{
		
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Yandex_Commission'];
		if (!$GLOBALS ["Yandex_NoCommision"]=='True'){
			$AllComm = $AllComm + (float)$GLOBALS['Yandex_Commission2'];
		}
		
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		
		if ($allCost<$GLOBALS['Yandex_MinPlat']){$allCost=$GLOBALS['Yandex_MinPlat'];}
			// 211 - с учётом комиссии
			$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.str_replace('Payeer','Модульбанк',$strings[227]).'</div>
									<div class="m2">'.sprintf($strings[443],'Yandex').'</div>
									<div class="i m3">'.$strings[220].':</div>
									<form method="POST" name="sumform">
										<div class="m2">
											<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].$comm2.'
										</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
									// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
									// 218 - Оплатить
									$result['out'].='
										<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									</form>
								'.$comm.PrintBonuses().'
							<script language="JavaScript">
								//document.sumform.billme.click();
							</script>
								</div>
							</div>';
		
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}




function Prodamus()
{
	
	if ($GLOBALS['UseProdamus']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Prodamus_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Prodamus_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Prodamus_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseProdamusStdTitle']==1)?($strings[528]):($GLOBALS['ProdamusTitle']));
	
	$crumbs = menuManager('four',array("./prodamus.php",$Title));	
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 528 - Оплата через платежную систему Prodamus
	$result['page_title'] = $strings[528];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if (isset($_REQUEST['billme']))
	{

		if ($GLOBALS['UseProdamus'] <> "True")
		{
			// 529 - Пополнение счёта через платёжную систему Prodamus запрещено!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[529].'</div>
						</div>
					</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		//if ((int)$_POST['sum'] < $GLOBALS['Onpay_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
		//	$result['out'] .= '
		//			'.$crumbs.'
		//			<div class="content">
		//				<div class="four">
		//					<div class="i m3">'.$strings[207].' '.$GLOBALS['Onpay_MinPlat'].' '.$GLOBALS['curr'].'</div>
		//				</div>
		//			</div>
		//			<script>Refresh("onpay.php",4)</script>';
		//	return parse_template($result, './template/clear.php'); 
		//}
		$login = mysql_real_escape_string($_SESSION['login']);
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$Phone = $otherinfo[3];
			$Email = $otherinfo[4];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
		
		$ProdamusShopURL = '';
		$ProdamusShopKey = '';
		
		$ProdamusID = $otherinfo[129];
		if ((int)$ProdamusID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$ProdamusID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$ProdamusShopURL = $Params['ProdamusShopURL'];
				$ProdamusShopKey = $Params['ProdamusShopKey'];
			}
		} else  {
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 38;";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$ProdamusShopURL = $Params['ProdamusShopURL'];
				$ProdamusShopKey = $Params['ProdamusShopKey'];
			}
		}
		
		include 'payin/prodamus/prodamus_functions.php'; 
		
		$paysize = mysql_real_escape_string($_POST['sum']);
		
		$paysize=GetSummWithComission($paysize,(float)$GLOBALS['Prodamus_Commission'],(float)$GLOBALS['Prodamus_Commission2']);
		
		//echo $paysize;exit();
		
		// 208 - Назад к заполнению формы
		$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.Process_Prodamus($login,$paysize,$short_guid,$contract,$Phone,$ProdamusShopURL,$ProdamusShopKey,$Email).'</div>
						</div>
					</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
				
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Prodamus_Commission']+(float)$GLOBALS['Prodamus_Commission2'];
		
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?', '.$strings[211]." ".$AllComm.'%':'';		
		
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<i class="fas fa-piggy-bank"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="m2">'.sprintf($strings[443],'Prodamus.ru').'</div>
							<div class="i m3">'.$strings[220].':</div>
							<form method="POST" target="_blank">
								<div class="m2">
								<input type="text" name="sum" id="sum" value="'.$allCost.'" class="pay"/> '.$GLOBALS['curr'].' '.$comm2.'
								</div>';
								if ($GLOBALS['ClientCanStartAt1st']=='True'){
									$otherinfoA=explode('||', $result['otherinfo']);
									if ($otherinfoA[35]=='0') {
										$v='';
										if ($otherinfoA[77]=='True'){$v='checked';}
										// 214 - Запустить с началом следующего расчётного периода
										$result['out'].='
										<div class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
									}
								}
								// 215 - Уважаемый абонент! Возможна небольшая комиссия, размер которой зависит от соглашения вашего банка с платёжной системой.
								// 218 - Оплатить
								$result['out'] .= '
									<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
									'.$comm.PrintBonuses().'
							</form>
						</div>
					</div>'; 
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}



function RandomString($length){
	$random = '';
	for ($i = 0; $i < $length; $i++) {
		$random .= rand(0, 9);
	}
	return $random;
}

function OsmpSbrfMoney() {
	
	if ($GLOBALS['UseOSMP_SBRF']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['OSMP_SBRF_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['OSMP_SBRF_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['OSMP_SBRF_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseOSMP_SBRF_StdTitle']==1)?($strings[498].' QR'):($GLOBALS['OSMP_SBRF_Title']));
	
	// 498 - Платёж через «Сбербанк»
	$crumbs = menuManager('four',array("./osmp_sbrf.php",$Title));
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 499 - Оплата через платежную систему Сбербанк
	$result['page_title'] = $strings[499];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
		
	if (isset($_REQUEST['billme'])){
		
					
		if ($GLOBALS['UseOSMP_SBRF'] <> "True")
		{
			// 501 - Пополнение счёта через платёжную систему Сбербанк запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[501].'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((int)$_POST['sum'] < $GLOBALS['OSMP_SBRF_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['OSMP_SBRF_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("osmp_sbrf.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		
		if (isset($_POST['sum']))
		{	
			
			$login = mysql_real_escape_string($_SESSION['login']);
			$sql = "SELECT otherinfo, shortguid, FIO, pinfo, contract FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			$pinfo=explode('||',$row[3]);
			$contract=$row[4];
			
			$adr=$pinfo[4];
		
			$aAdr=explode(',',$adr);
			$adr=trim($aAdr[count($aAdr)-1]);
			if (count($aAdr)>1){
				$adr=trim($aAdr[count($aAdr)-2]) . ', ' . $adr;
				if (count($aAdr)>2){
					$adr=trim($aAdr[count($aAdr)-3]) . ', ' . $adr;
				}
			}
			
			
			$FIO = str_replace('  ', ' ', $row[2]);
			$FIOa = explode(' ', $FIO);
			
			if (count($FIOa)<3){
				if (count($FIOa)>1){
					$FIO = $FIOa[0] . ' ' . mb_substr($FIOa[1],0,1) . '.';
				}
			} else {
				$FIO = $FIOa[1] .  ' ' . $FIOa[2] . ' ' . mb_substr($FIOa[0],0,1);
			}
			
			
			mysql_free_result($mysqlResult);
		
		
			$_POST['sum']=GetSummWithComission($_POST['sum'],(float)$GLOBALS['OnlinePayComm_OSMP_SBRF'],0)*100;
			
			
			$Dir='kassa/module/qr/temp/';
			if (is_dir($Dir)){
				$files=scandir($Dir,1);
				for ($i=0; $i<count($files); $i++) {
					$FileName=$Dir.$files[$i];
					
					if (!is_dir($FileName)){
						if ((time()-filemtime($FileName))>60){
							unlink($Dir.$files[$i]);
						}
					}
				}
			}
			
			$QR_Data="ST00012|NAME={$GLOBALS['OSMP_SBRF_Company']}|
PERSONALACC={$GLOBALS['OSMP_SBRF_LS']}|
BANKNAME={$GLOBALS['OSMP_SBRF_Bank']}|
BIC={$GLOBALS['OSMP_SBRF_BIK']}|
CORRESPACC={$GLOBALS['OSMP_SBRF_KS']}|
PAYEEINN={$GLOBALS['OSMP_SBRF_INN']}|
KPP={$GLOBALS['OSMP_SBRF_KPP']}|
PersAcc={$contract}|
FIO=$FIO|
ADDRESS=$adr|
SUM={$_POST['sum']}";

			$qr_file0=uniqid();
			$qr_file=__DIR__ . "/../kassa/module/qr/temp/$qr_file0.png";
			$qr_file2="../kassa/module/qr/temp/$qr_file0.png";
			require_once (__DIR__ . '/../kassa/module/qr/qrlib.php');
			
			QRcode::png($QR_Data, $qr_file, 'M', 2);
			
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<i class="fas fa-piggy-bank"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="m2">
							</div>
								<form method="POST" name="sumform">
									<div class="content" style="">
										<div style="margin:0;padding:0">
											<br>
											<center><img src='.$qr_file2.'></center>
										</div>
									</div>
								</form>
						</div>
					</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 	
			
		}
				
	} else {
	
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		if ($allCost==0){
			$allCost = $OtherInfo[89];
		}
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['OnlinePayComm_OSMP_SBRF'];

		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		if ($allCost < 0)  {$allCost = 100;}
		if (($allCost == 0) && ($Bal<0)) {
			$allCost = -$Bal;
		}		
		$paysize=$allCost;
		
		$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 500 - Сбербанк
		// 220 - Сумма платежа
		// 218 - Оплатить
		$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<i class="fas fa-piggy-bank"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="m2">'.sprintf($strings[443],$strings[500]).'<br>'.$strings[602].'<br>'.
							'
							</div>
								<form method="POST" name="sumform">
									<div class="content" style="">
										<div style="margin:0;padding:0">
											<div class="i m3">
												'.$strings[220].':
											</div>
											<div class="m2">
												<input class="pay" type="text" name="sum" id="sum" required value="'.$paysize.'"> '.$GLOBALS['curr'].$comm2.'
											</div>
											<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
										</div>
									</div>
								</form>
								' . PrintBonuses() . '
						</div>
					</div>';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 	
	}
	
}

function SbrfMoney()
{
	
	if ($GLOBALS['EnableSberbank']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Sberbank_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Sberbank_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Sberbank_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseSberbankStdTitle']==1)?($strings[498]):($GLOBALS['SberbankTitle']));
	
	// 498 - Платёж через «Сбербанк»
	$crumbs = menuManager('four',array("./sbrf.php",$Title));
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 499 - Оплата через платежную систему Сбербанк
	$result['page_title'] = $strings[499];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
		
	if (isset($_REQUEST['billme'])){
		
					
		if ($GLOBALS['EnableSberbank'] <> "True")
		{
			// 501 - Пополнение счёта через платёжную систему Сбербанк запрещено!
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[501].'</div>
								</div>
							</div>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ((int)$_POST['sum'] < $GLOBALS['Sberbank_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['Sberbank_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("sbrf.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		
		
		if (isset($_POST['sum']))
		{	

			$iOrder = RandomString(6) . '_' . $_SESSION['shortguid'];
			
			$SberbankUsername=$GLOBALS['SberbankUsername'];
			$SberbankPassword=$GLOBALS['SberbankPassword'];
			$Sberbank_IsTest=$GLOBALS['Sberbank_IsTest'];
			$Sberbank3DS=1;
			$SberGUID=0;
			
			$login = mysql_real_escape_string($_SESSION['login']);
			$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
	
	
		$SberbankID = $otherinfo[121];
		if ((int)$SberbankID >-1){
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `id` = '$SberbankID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$SberGUID=$row[1];
				$SberbankUsername = $Params['SberbankUsername'];
				$SberbankPassword = $Params['SberbankPassword'];
				$Sberbank_IsTest = $Params['Sberbank_IsTest'];
				
				if (isset($Params['Sberbank3DS'])){
					$Sberbank3DS=$Params['Sberbank3DS'];
				}
				
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 29";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);
				$SberGUID=$row[1];
				$SberbankID=$row[1];

				$SberbankUsername = $Params['SberbankUsername'];
				$SberbankPassword = $Params['SberbankPassword'];
				$Sberbank_IsTest = $Params['Sberbank_IsTest'];
				
				if (isset($Params['Sberbank3DS'])){
					$Sberbank3DS=$Params['Sberbank3DS'];
				}
			}
		}
		
		$_POST['sum']=GetSummWithComission($_POST['sum'],(float)$GLOBALS['Sberbank_Commission'],(float)$GLOBALS['Sberbank_Commission2']);
		
		//echo $_POST['summ'];exit();
			
			
			
			$aData = array(
				'userName' => $SberbankUsername,
				'password' => $SberbankPassword,
				'orderNumber' => $iOrder,
				'amount' => $_POST['sum'] * 100,
				'returnUrl' => $GLOBALS['PortalAddress'] . 'payin/sberbank/payin.php?acc=' . $SberGUID
			);
			
			if ($Sberbank3DS!=1){
				$aData['features'] = 'FORCE_SSL';
			}
			
			
			$pinfoA=explode('||',$result['pinfo']);
			$Tel=$pinfoA[0];
			$Email=$pinfoA[1];
			
			if (strlen($Tel)>3){
				$aData['phone'] = $Tel;
			}
			if (strlen($Email)>3){
				$aData['email'] = $Email;
			}
			
			//https://3dsec.sberbank.ru/payment/finish.html
			//print_r($aData);exit();
			
			$curl = curl_init();
			
			$URL='';
			if (($Sberbank_IsTest=='True')||($Sberbank_IsTest=='true')){
				$URL='https://3dsec.sberbank.ru/payment/rest/register.do';
			} else {$URL='https://securepayments.sberbank.ru/payment/rest/register.do';}
			
			curl_setopt($curl, CURLOPT_URL, $URL);
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
			curl_setopt($curl, CURLOPT_TIMEOUT, 8);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($aData));
			
			$response = curl_exec($curl);			
			$info = curl_getinfo($curl);
			$error = curl_error($curl);
			curl_close($curl);
				
			if (strlen($error)>0){
				echo 'error='.$error;
			} else {
				
				$data=json_decode($response,true);
				
				if (!isset($data['errorMessage'])){
					$formUrl = $data['formUrl'];
					$orderId = $data['orderId'];
					
					$shortguid = mysql_real_escape_string($_SESSION['shortguid']);
					$sum = mysql_real_escape_string($_POST['sum']);
					$orderId_bd = mysql_real_escape_string($orderId);
					
					$sql="INSERT INTO `operations_sberbank`(`id`,`shortguid`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'".$shortguid."','".$sum."','".$orderId_bd."','0','".date("Y-m-d H:i:s")."');";
					mysql_query($sql,$GLOBALS['mysql']);
					
					PaymntToLog($SberbankID,$orderId_bd);
					
					header('location: ' . $formUrl);
				} else {
					echo 'error='.$data['errorMessage'];
				}
				
				
			}
			
			//echo '=response='.$response;
			//echo '=info=';
			//print_r($info);
			//echo '=error='.$error;
		
		}
		
				
	} else {
	
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);

		$allCost = str_replace(',','.',$OtherInfo[74]);

		if ($allCost==0){
			$allCost = $OtherInfo[89];
		}
		
		$pcomm=1;
		$AllComm = (float)$GLOBALS['Sberbank_Commission']+(float)$GLOBALS['Sberbank_Commission2'];

		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
		if ($allCost < 0)  {$allCost = 100;}
		if (($allCost == 0) && ($Bal<0)) {
			$allCost = -$Bal;
		}		
		$paysize=$allCost;
		
		$comm2=((float)$AllComm>0)?', '.$strings[211]." ".$AllComm.'%':'';
		
		
		// 213 - Управление платежом
		// 500 - Сбербанк
		// 220 - Сумма платежа
		// 218 - Оплатить
		$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<i class="fas fa-piggy-bank"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="m2">'.sprintf($strings[443],$strings[500]).'<br>'.$strings[602].'<br>'.
							'<br>
							<font color="darkgray">
							<b>Если страница оплаты не открывается, установите сертификаты Минцифры с сайта <a href="https://gosuslugi.ru/crt" style="color:lightgray" target="_blank">Госуслуги</a>, или установите <a href="https://browser.yandex.ru/" style="color:lightgray" target="_blank">Яндекс.Браузер</a>.</b>
							</font>
							</div>
								<form method="POST" name="sumform">
									<div class="content" style="">
										<div style="margin:0;padding:0">
											<div class="i m3">
												'.$strings[220].':
											</div>
											<div class="m2">
												<input class="pay" type="text" name="sum" id="sum" required value="'.$paysize.'"> '.$GLOBALS['curr'].$comm2.'
											</div>
											<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
										</div>
									</div>
								</form>
								' . PrintBonuses() . '
						</div>
					</div>';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 	
	}
	
}




function ClickUZ()
{
	
	if ($GLOBALS['EnableClick']==1){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Click_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Click_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Click_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseClickStdTitle']==1)?($strings[579]):($GLOBALS['ClickTitle']));
	
	$crumbs = menuManager('four',array("./clickuz.php",$Title));

	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 579 - Платёж через «Click.uz»
	$result['page_title'] = $strings[579];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if ((isset($_REQUEST['billme']))&& !isset($_REQUEST['gopay']))
	{
		
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После одобрения банком деньги поступят в течении нескольких минут.
			 $result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[204].'</div>
						</div>
					</div>
					<script>Refresh("pay3.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='<script type="text/javascript">
								window.parent.location = "index.php"
							</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}



		$result['out'].=$crumbs;
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
				
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
				
		$AllComm = (float)$GLOBALS['Click_Commission'] + (float)$GLOBALS['Click_Commission2'];
		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		
		
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid2 FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
			
			
		
			
		$Click_ServiveID = '';
		$Click_MerchantID = '';
		$Click_SecretKey = '';
		
		$ClickID = $otherinfo[149];
		if ((int)$ClickID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$ClickID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Click_ServiveID = $Params['Click_ServiveID'];
				$Click_MerchantID = $Params['Click_MerchantID'];
				$Click_SecretKey = $Params['Click_SecretKey'];
			}
		} else  {
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 46;";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Click_ServiveID = $Params['Click_ServiveID'];
				$Click_MerchantID = $Params['Click_MerchantID'];
				$Click_SecretKey = $Params['Click_SecretKey'];
			}
		}
		
		//$m_orderid = get_rnd_nums(4) . '_' . $short_guid;
		$m_orderid = $login;
		
		
		// 443 - Для пополнения баланса введите нужную сумму ...
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.sprintf($strings[443],'Click').'</div>
				<div class="spoiler m2" data-open="'.$strings[444].'" data-close="'.$strings[445].'">
				</div>
				<div class="i m3">'.$strings[220].':</div>
				<div class="m2">
					<input type="text" name="paysize" id="cashsize" value="'.$allCost.'"  class="pay" /> 
					'. $GLOBALS['curr'].$comm2.'
				</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}
				// 218 - Оплатить
				// 207 - Минимальная сумма платежа
				$result['out'].='<script src="https://my.click.uz/pay/checkout.js"></script>
					<button name="billme" id="billme" class="m3" onclick="document.getElementById(\'cashsize\').value=document.getElementById(\'cashsize\').value.replace(\',\',\'.\'); if (document.getElementById(\'cashsize\').value<'.$GLOBALS['Click_MinPlat'].'){alert(\''.$strings[207].' '.$GLOBALS['Click_MinPlat'].' '.$GLOBALS['curr'].'!\');return;} if (document.getElementById(\'startonfirst\')){GetHTTP2(\'api.php?action=START_ME_AT_1ST&value=\'+document.getElementById(\'startonfirst\').checked,function(){});}createPaymentRequest({service_id: '.$Click_ServiveID.', merchant_id: '.$Click_MerchantID.', amount: document.getElementById(\'cashsize\').value, transaction_param: \''.$m_orderid.'\', merchant_user_id: \''.$short_guid.'\'}, function(data) {});">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
			</div>
		</div>
		<script>
			function GetHTTP2(url,callback){ 
				var request=null;
				request=new XMLHttpRequest();
				request.open(\'GET\', url);
				request.msCaching = \'disabled\';	// for IE
				request.responseType = \'text\';
				request.send(null);	
				request.onreadystatechange = function() {
					if (this.readyState == 4) {
						if(this.status == 200) {
							callback(this.response);
						}
					}
				}	
			}';
			
		if (isset($_REQUEST['gopay'])){
			$result['out'].='document.getElementById("billme").click();';	
		}
		
		$result['out'].='</script>	
		';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}



function payeerPay()
{
	
	if ($GLOBALS['EnablePayECash']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Payeer_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Payeer_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Payeer_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	
	$Title = htmlspecialchars(($GLOBALS['UsePayeerStdTitle']==1)?($strings[14]):($GLOBALS['PayeerTitle']));
	
	$crumbs = menuManager('four',array("./payeer.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 227 - Оплата через платежную систему Payeer
	$result['page_title'] = $strings[227];
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if (isset($_REQUEST['billme']))
	{
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После одобрения банком деньги поступят в течении нескольких минут.
			 $result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[204].'</div>
						</div>
					</div>
					<script>Refresh("pay3.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='<script type="text/javascript">
								window.parent.location = "index.php"
							</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		if ($GLOBALS['EnablePayECash'] <> "True")
		{
			// 228 - Пополнение счёта через платёжную систему Payeer запрещено!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[228].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		
		
		if ((int)$_POST['paysize'] < $GLOBALS['PayeerMinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[207].' '.$GLOBALS['PayeerMinPlat'].' '.$GLOBALS['curr'].'</div>
						</div>
					</div>
					<script>Refresh("payeer.php",4)</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
			
			
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
			
		$m_shop = $GLOBALS['Payeer_ShopID'];
		$m_key = $GLOBALS['Payeer_ShopKey'];
		
		$PayeerID = $otherinfo[117];
		if ((int)$PayeerID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$PayeerID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$m_shop = $Params['PayeerShopID'];
				$m_key = $Params['PayeerShopKey'];
			}
		} else  {
			$sql = "SELECT `params`, `id` FROM `payment_params` WHERE `type` = 1;";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$m_shop = $Params['PayeerShopID'];
				$m_key = $Params['PayeerShopKey'];
				
				$PayeerID = $row[1];
			}
		}
		
		$m_orderid = "MB_" . $short_guid . "_" . get_rnd_nums(8);
				
		if (isset($_POST['currencyname'])){
			$m_curr = $_POST['currencyname'];
		} else {
			
			$m_curr = $GLOBALS['curr'];
			if (($GLOBALS['curr']=='Руб.')||($GLOBALS['curr']=='RUB')||($GLOBALS['curr']=='RUR')||($GLOBALS['curr']=='₽')){
				$m_curr = 'RUB';
			} elseif (($GLOBALS['curr']=='$') || ($GLOBALS['curr']=='USD')) {
				$m_curr = 'USD';
			} elseif (($GLOBALS['curr']=='€') || ($GLOBALS['curr']=='EUR')) {
				$m_curr = 'EUR';
			}
			
		}
		
		
		$m_desc=base64_encode("Internet payment" . " ($contract)");
		
		$m_amount=GetSummWithComission($_POST['paysize'],(float)$GLOBALS['Payeer_Commission'],(float)$GLOBALS['Payeer_Commission2']);
		$m_amount = number_format($m_amount, 2, '.', '');
		
		$m_sign = GetPayeerSign($m_shop,$m_orderid,$m_amount,$m_curr,$m_desc,$m_key);  	
	
		
		$sql = "INSERT INTO`operations_payeer`(`id`,`sum`,`operation_id`,`status`,`actiondate`)VALUES(NULL,'$m_amount','$m_orderid','','".date("Y-m-d H:i:s")."');";
		mysql_query($sql,$mysql);
		
		// 209 - Загружается платёжный интерфейс...
		$result['out'].=$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<form id="getpay" id="getpay" method="GET" action="https://payeer.com/merchant/" id="iframe_data_form">
					<input type="hidden" name="m_shop" value="'.$m_shop.'">
					<input type="hidden" name="m_orderid" value="'.$m_orderid.'">
					<input type="hidden" name="m_amount" value="'.$m_amount.'">
					<input type="hidden" name="m_curr" value="'.$m_curr.'">
					<input type="hidden" name="m_desc" value="'.$m_desc.'">
					<input type="hidden" name="m_sign" value="'.$m_sign.'">
					<input type="submit" style="visibility:hidden" name="m_process" value="send" />
				</form>
				<div id="loadbill" name="loadbill">
					<div class="i m3">'.$strings[209].'</div>
					<img src = "./img/loading.gif" style="border:0" class="m3">
				</div>
				<iframe name="myiframe" id="myiframe" src="" width="780" height="550" align="center" frameborder="0" scrolling="yes"  onload="ShowBill();">
				Ваш браузер не поддерживает плавающие фреймы!
				</iframe>';	
		
		PaymntToLog($PayeerID,$m_orderid);
		
		// 210 - Данный браузер не поддреживается! <br> Используйте более современный, например: Chrome, Opera или Safari.
		$result['out'].="
		<script type=\"text/javascript\"> 
				var form = document.getElementById(\"getpay\");
				form.submit();		
				var useragent=navigator.userAgent; 	
				if (useragent.indexOf('MSIE')!= -1){
					document.getElementById(\"loadbill\").innerHTML = \"<center><font color='red'><br><b>".$strings[210]."</b></font><br><br></center>\";
				} else {
					document.getElementById('iframe_data_form').submit();
				} 	
				function ShowBill(){
					if (useragent.indexOf('MSIE')== -1){
					document.getElementById(\"loadbill\").innerHTML = \"\";
					document.getElementById(\"myiframe\").style.visibility = 'visible';
					}
				}
		</script>
		";
		
		// 208 - Назад к заполнению формы
		$result['out'].='
			</div>
		</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
				
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
				
		$AllComm = (float)$GLOBALS['Payeer_Commission'] + (float)$GLOBALS['Payeer_Commission2'];
		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		
		//echo '='.mb_strtolower($GLOBALS['curr']);exit();
		//echo '='.mb_strpos(mb_strtolower($GLOBALS['curr']),'грн');exit();
		
		// 443 - Для пополнения баланса введите нужную сумму ...
		// 229 - Для перевода денег на лицевой счет с помощью электронной валюты необходимо ввести нужную сумму и нажать кнопку «оплатить», после чего на защищенном сервере системы Payeer указать нужный способ пополнения.<br><br>
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.sprintf($strings[443],'Payeer').'</div>
				<div class="spoiler m2" data-open="'.$strings[444].'" data-close="'.$strings[445].'">
					<div>'.$strings[229].'</div>
				</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST">
				<div class="m2">
					<input type="text" name="paysize" id="cashsize" value="'.$allCost.'"  class="pay"//> 
					<span class="select"><select name="currencyname" id="mycashname">'.
								(((mb_strpos(mb_strtolower($GLOBALS['curr']),'uah')===FALSE)&&(mb_strpos(mb_strtolower($GLOBALS['curr']),'грн')===FALSE)&&(mb_strpos(mb_strtolower($GLOBALS['curr']),'₴')===FALSE))?('<option'.(($GLOBALS['curr']=='Руб.' || $GLOBALS['curr']=='RUB' || $GLOBALS['curr']=='RUR' || $GLOBALS['curr']=='₽')?' selected':'').'>RUB</option>'):('')).
								'<option'.(($GLOBALS['curr']=='$' || $GLOBALS['curr']=='USD')?' selected':'').'>USD</option>
								<option'.(($GLOBALS['curr']=='€' || $GLOBALS['curr']=='EUR')?' selected':'').'>EUR</option>
					</select></span> '.$comm2.'
				</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}
				// 218 - Оплатить
				$result['out'].='
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
				</form>
			</div>
		</div>';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}



function Gorod74Pay()
{
	
	if ($GLOBALS['EnableGorod74']=='True'){

		if ((strlen(array_search($_SESSION['tarif_guid'], $GLOBALS['Payeer_allowed_objects']))==0) and
				(strlen(array_search($_SESSION['group_guid'], $GLOBALS['Payeer_allowed_objects']))==0) and
				(strlen(array_search(12211221122112, $GLOBALS['Gorod74_allowed_objects']))==0)) {
			exit();
		}	
	} else {exit();}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseGorod74StdTitle']==1)?(str_replace('Payeer','Gorod 74',$strings[14])):($GLOBALS['Gorod74Title']));
	
	$crumbs = menuManager('four',array("./gorod74.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 227 - Оплата через платежную систему Payeer
	$result['page_title'] = str_replace('Payeer', 'Gorod 74', $strings[227]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if (isset($_REQUEST['billme']))
	{
		
		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		
		
		if ((int)$_POST['paysize'] < $GLOBALS['Gorod74MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[207].' '.$GLOBALS['PayeerMinPlat'].' '.$GLOBALS['curr'].'</div>
						</div>
					</div>
					<script>Refresh("payeer.php",4)</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}

		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid2, FIO, tarif FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			$FIO=$row[2];
			$tarif=$row[3];
			mysql_free_result($mysqlResult);
			
			
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
			
		
		$m_orderid = "MB_" . $short_guid . "_" . get_rnd_nums(8);
		$m_amount = number_format($_POST['paysize'], 2, ".", "");
		//$m_curr = $_POST['currencyname'];
		$m_desc=base64_encode("Internet payment" . " ($contract)");
		
		$m_amount=GetSummWithComission($m_amount,(float)$GLOBALS['Payeer_Commission'],(float)$GLOBALS['Payeer_Commission2']);
		
		//echo $m_amount;exit();
		
		//$m_sign = GetPayeerSign($m_shop,$m_orderid,$m_amount,$m_curr,$m_desc,$m_key);  	
	

		$url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://';
		$url = $url . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		$uri=explode("gorod74.php", $url);
		
		if (strlen($GLOBALS['PaySite'])>8) {
			$result_url = trim($GLOBALS['PaySite'],'/') . "/pay2.php?action=1";
			$fail_url = trim($GLOBALS['PaySite'],'/') . "/pay2.php?action=3";
		} else {
			
			$result_url = $uri[0] . "pay2.php?action=1";
			$fail_url = $uri[0] . "pay2.php?action=3";
		}
		
		//$sign = sha1($GLOBALS['Gorod74Login'] . $GLOBALS['Gorod74Password'] . $GLOBALS['Gorod74ServiceID'] . $m_orderid . $m_amount);
		
		
		//$Descr = iconv('utf-8', 'cp1251', "Оплата по тарифу '$tarif'");
		$Descr = "Оплата по тарифу '$tarif'";
		
		// 218 - Оплатить
		// 209 - Загружается платёжный интерфейс...
		$result['out'].=$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				
				<form id="getpay" action="gorod74_pay.php" name=frmPay method="post"> 
					<input type="hidden" name="order_id" value="'.$m_orderid.'">
					<input type="hidden" name="sum" value="'.$m_amount.'">
					<input type="hidden" name="payer" value="'.$FIO.'">
					<input type="hidden" name="description" value="'.$Descr.'">					
				</form>
				
				';	
	
		
		// 210 - Данный браузер не поддреживается! <br> Используйте более современный, например: Chrome, Opera или Safari.
		$result['out'].="
		<script type=\"text/javascript\"> 
				var form = document.getElementById(\"getpay\");
				form.submit();
		</script>
		";
		
		// 208 - Назад к заполнению формы
		$result['out'].='
			</div>
		</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
				
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
				
		$AllComm = (float)$GLOBALS['Gorod74_Commission'];
		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		// 443 - Для пополнения баланса введите нужную сумму ...
		// 229 - Для перевода денег на лицевой счет с помощью электронной валюты необходимо ввести нужную сумму и нажать кнопку «оплатить», после чего на защищенном сервере системы Payeer указать нужный способ пополнения.<br><br>
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.sprintf($strings[443],'Gorod 74').'</div>
				<div class="spoiler m2" data-open="'.$strings[444].'" data-close="'.$strings[445].'">
					<div>'.str_replace('Payeer', 'Gorod 74',$strings[229]).'</div>
				</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST">
				<div class="m2">
					<input type="text" name="paysize" id="cashsize" value="'.$allCost.'"  class="pay"//> ' . $GLOBALS['curr'] . '
					</span> '.$comm2.'
				</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}
				// 218 - Оплатить
				$result['out'].='
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
				</form>
			</div>
		</div>';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}





function Privat24Pay()
{
	
	if (($GLOBALS['EnablePrivat24']<>'True')||($GLOBALS['Privat24InWEB']<>1)){
		exit();
	}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UsePrivat24StdTitle']==1)?($strings[591]):($GLOBALS['Privat24Title']));
	
	$crumbs = menuManager('four',array("./privat24.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 227 - Оплата через платежную систему Payeer
	$result['page_title'] = str_replace('Payeer', 'Privat24',$strings[227]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if (isset($_REQUEST['billme']))
	{
		if ((int)$_POST['paysize'] < $GLOBALS['Privat24_MinPlat']) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['Privat24_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("privat24.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После одобрения банком деньги поступят в течении нескольких минут.
			 $result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[204].'</div>
						</div>
					</div>
					<script>Refresh("pay3.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='<script type="text/javascript">
								window.parent.location = "index.php"
							</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
			
			
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
			
		$amount = number_format($_POST['paysize'], 2, ".", "");
		
		$m_amount=GetSummWithComission($amount,(float)$GLOBALS['Privat24Comission'],0);
		
		$Acc=mysql_real_escape_string(($GLOBALS['Privat24SearchOption']==0)?($contract):($_SESSION['login']));
		
		$Privat24_Token = $GLOBALS['Privat24_Token'];
		
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
		
		
		$Privat24ID = $otherinfo[181];
		if ((int)$Privat24ID >-1){
			$sql = "SELECT `params` FROM `payment_params` WHERE `id` = '$Privat24ID';";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Privat24_Token = $Params['Privat24_Token'];
			}
		} else  {
			$sql = "SELECT `params` FROM `payment_params` WHERE `type` = 14;";
			$mysqlResult = mysql_query($sql,$mysql);
			if (mysql_num_rows($mysqlResult)>0){
				$row = mysql_fetch_array($mysqlResult);
				$Params = GetParams($row[0]);

				$Privat24_Token = $Params['Privat24_Token'];		
										   
			}
		}
		
		
		header('Location: https://next.privat24.ua/payments/form/{"token":"'.$Privat24_Token.'","sum":'.$amount.',"personalAccount":"'.$Acc.'"}');
		
		
		// 208 - Назад к заполнению формы
		$result['out'].='
			</div>
		</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
				
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
				
		$AllComm = (float)$GLOBALS['Privat24Comission'];
		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		// 443 - Для пополнения баланса введите нужную сумму ...
		// 229 - Для перевода денег на лицевой счет с помощью электронной валюты необходимо ввести нужную сумму и нажать кнопку «оплатить», после чего на защищенном сервере системы Payeer указать нужный способ пополнения.<br><br>
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.sprintf($strings[443],'Privat24').'</div>
				<div class="spoiler m2" data-open="'.$strings[444].'" data-close="'.$strings[445].'">
					<div>'.str_replace('Payeer', 'Privat24',$strings[229]).'</div>
				</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST" target="_blank" >
				<div class="m2">
					<input type="text" name="paysize" id="cashsize" value="'.$allCost.'"  class="pay">
					'.$GLOBALS['curr']. $comm2.'
				</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}
				// 218 - Оплатить
				$result['out'].='
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
				</form>
			</div>
		</div>';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}


function EasyPay()
{
	
	if (($GLOBALS['EnableEasyPay']<>'True')||($GLOBALS['EasyPayInWEB']<>1)){
		exit();
	}
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$Title = htmlspecialchars(($GLOBALS['UseEasyPayStdTitle']==1)?(str_replace('Privat24','EasyPay',$strings[591])):($GLOBALS['EasyPayTitle']));
	
	$crumbs = menuManager('four',array("./easypay.php",$Title));	
	CheckProfile($result,$crumbs,'four');
	
	// 227 - Оплата через платежную систему Payeer
	$result['page_title'] = str_replace('Payeer', 'EasyPay',$strings[227]);
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
			
	$result['out'] ="";
	if (!isset($_REQUEST["action"])){$_REQUEST["action"]="";}
	
	if (isset($_REQUEST['billme']))
	{
		if (((int)$_POST['paysize'] < $GLOBALS['EasyPay_MinPlat'])||((int)$_POST['paysize'] > $GLOBALS['EasyPay_MaxPlat'])) {
			// 207 - Минимальная сумма платежа
			// 208 - Назад к заполнению формы
			$result['out'].= $crumbs.'
							<div class="content">
								<div class="four">
									<div class="i m3">'.$strings[207].' '.$GLOBALS['EasyPay_MinPlat'].' '.$GLOBALS['curr'].'</div>
								</div>
							</div>
							<script>Refresh("easypay.php",4)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
		
		if ($_REQUEST["action"] == 1)
		{
			// 204 - Ваша заявка принята! <br>После одобрения банком деньги поступят в течении нескольких минут.
			 $result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[204].'</div>
						</div>
					</div>
					<script>Refresh("pay3.php?action=2",5)</script>';
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
			exit();
		}
		if ($_REQUEST["action"] == 2) 
		{
			$result['out'].='<script type="text/javascript">
								window.parent.location = "index.php"
							</script>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
			exit();
		}

		if (!$_SESSION['auth'])
		{
			// 205 - Для продолжения необходимо авторизоваться!
			$result['out'] .= '
					'.$crumbs.'
					<div class="content">
						<div class="four">
							<div class="i m3">'.$strings[205].'</div>
						</div>
					</div>';
			return parse_template($result, './template/clear.php',NULL,FALSE,true); 
		}
		
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$sql = "SELECT otherinfo, shortguid FROM stat WHERE user_name = '$login';";

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$otherinfo = explode("||",$row[0]);
			$contract = $otherinfo[0];
			$short_guid=$row[1];
			mysql_free_result($mysqlResult);
			
			
		if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
		
		if ($_REQUEST["startonfirst"] == 'on'){
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		} else {
			$datetoday = date("Y-m-d H:i:s");
			$uid = uniqid("");
			$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
			mysql_query($sql,$mysql);
		}
			
		$amount = number_format($_POST['paysize'], 2, ".", "");
		
		$m_amount=GetSummWithComission($amount,(float)$GLOBALS['EasyPay_Commission'],0);
		
		
		header('Location: https://easypay.ua/ua/catalog/internet/'.$GLOBALS['EasyPayShopID'].'?account='.$login.'&amount='.$m_amount);
		
		
		// 208 - Назад к заполнению формы
		$result['out'].='
			</div>
		</div>';
		return parse_template($result, './template/clear.php',NULL,FALSE,true); 
	}
	else
	{
		$comm='';
		$OtherInfo = $result['otherinfo'];
		$OtherInfo = explode("||",$OtherInfo);
		$AutoPromisePay = $OtherInfo[10];
		$PromisePayCost = str_replace(',','.',$OtherInfo[29]);
	
		$allCost = str_replace(',','.',$OtherInfo[74]);
				
		$BalA= explode(' ', $result['ballance']);
		$Bal=floatval(str_replace(',','.',$BalA[0]));
		
				
		$AllComm = (float)$GLOBALS['Privat24Comission'];
		
		
		// 211 - Взымается дополнительная комиссия в размере
		$comm2=((float)$AllComm-1>0)?$strings[211]." ".$AllComm.'%':'';
		
		// 443 - Для пополнения баланса введите нужную сумму ...
		// 229 - Для перевода денег на лицевой счет с помощью электронной валюты необходимо ввести нужную сумму и нажать кнопку «оплатить», после чего на защищенном сервере системы Payeer указать нужный способ пополнения.<br><br>
		// 213 - Управление платежом
		// 220 - Сумма платежа
		$result['out'] .= '
		'.$crumbs.'
		<div class="content">
			<div class="four">
				<i class="fas fa-piggy-bank"></i>
				<div class="h3 m4">'.$result['page_title'].'</div>
				<div class="m2">'.sprintf($strings[443],'Privat24').'</div>
				<div class="spoiler m2" data-open="'.$strings[444].'" data-close="'.$strings[445].'">
					<div>'.str_replace('Payeer', 'EasyPay',$strings[229]).'</div>
				</div>
				<div class="i m3">'.$strings[220].':</div>
				<form method="POST" target="_blank" >
				<div class="m2">
					<input type="text" name="paysize" id="cashsize" value="'.$allCost.'"  class="pay">
					'.$GLOBALS['curr']. $comm2.'
				</div>';
				if ($GLOBALS['ClientCanStartAt1st']=='True'){
					$otherinfoA=explode('||', $result['otherinfo']);
					if ($otherinfoA[35]=='0') {
						$v='';
						if ($otherinfoA[77]=='True'){$v='checked';}
						// 214 - Запустить с началом следующего расчётного периода
						$result['out'].='
							<div class="m3">
								<input type="checkbox" name="startonfirst" id="startonfirst" '.$v.'>
								<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
							</div>';
					}
				}
				// 218 - Оплатить
				$result['out'].='
					<button type="submit" name="billme" class="m3">'.$strings[218].'</button>
					'.$comm.PrintBonuses().'
				</form>
			</div>
		</div>';
		return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}
}


# Страница смены тарифа
function tarification() 
{

	# Получаем данные пользователя из базы
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	// 230 - Изменение тарифа
	$result['page_title'] 	= $strings[230];
	# Определяем текущий тариф пользователя и следующий рекомендуемый тариф
	$result['tarif']	= getcurtarif();
	
	$tarifends = "";
	$tarifends = $result['tarifends'];

	if ($tarifends <> "-") {
		if (strtotime(date("Y-m-d H:m:s")) < strtotime($tarifends)) {$tarifends= " &nbsp; (окончание: $tarifends)";} else {$tarifends = "";}
	} else {$tarifends = "";}
	
	if (!empty($result['tarif']['cur']))
	{
	# Если тариф не выбран, выводим список тарифов, если выбран, то выводим результат
	if (!isset($_POST['go'])) {

		// 13 - Тарифные планы
		// 231 - Тарифный план
		// 232 - Текущий тарифный план
		// 233 - Изменить на другой тарифный план
		// 417 - Смена тарифного плана
		
		
		$NameLNG = $result['tarif']['cur'];
		$sql = "SELECT `object_data` FROM `system_objects` WHERE `object_name` = '".mysql_real_escape_string($NameLNG)."' and `object_type` = 0;";
		$res = mysql_query($sql,$GLOBALS ["mysql"]);
			
		if (mysql_num_rows($res)>0){
			
			$row = mysql_fetch_array($res);
			$JSON=json_decode($row[0],true);
			
			if (!isset($JSON['NameLNG'])){
				$NameLNG = '';
			} else{		
				$NameLNG = $JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']];
			}
		}
		
		if (strlen($NameLNG)==0){$NameLNG = $result['tarif']['cur'];}
		
		$T1=html_entity_decode($result['tarif']['cur'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$T2=html_entity_decode($result['tarif']['next'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		
		$result['out'] = '
							'.menuManager('two',array("./changetarif.php",$strings[13])).'
							<div class="content">
								<div class="two">
									<i class="fas fa-shopping-bag"></i>
									<div class="h3 m4">'.$strings[417].'</div>
									<div class="i m3">'.$strings[232].' «'.$NameLNG. '»</div>
									'.show_next_tarif($T1, $T2).'
								</div>
							</div>
						</div>';
		} else {
			// 231 - Тарифный план
			$result['out'] = '
					'.menuManager('two',array("./changetarif.php",$strings[13])).'
					<div class="content">
						<div class="two">
							<div class="i m3">'.change_tarif_submit().'</div>
						</div>
					</div>';
			}
	}
	else
	{
		// 231 - Тарифный план
		// 235 - Изменение тарифного плана невозможно
		$result['out'] = '
		'.menuManager('two',array("./changetarif.php",$strings[13])).'
		<div class="content">
			<div class="two">
				<div class="i m3">'.$strings[235].'</div>
			</div>
		</div>';
	}
	# Выводим информацию на страницу
	return parse_template($result,'./template/default.php',NULL,FALSE,true);
	}

# Смена тарифного плана
function change_tarif_submit()
{

	$out='';
	
	if (isset($_POST['tarif'])){
	
		$strings=$GLOBALS['strings'];
		$tarif = mysql_real_escape_string($_POST['tarif']);

		# Проверяем возможность смены тарифа, по балансу пользователя
		# Получаем стоимость смены тарифа из базы данных
		$sql = "SELECT `changetarifcost` FROM `tarifs` WHERE `tarif_guid` = '".$tarif."';"; 
		$res = mysql_query($sql,$GLOBALS['mysql']);
		$row = mysql_fetch_array($res);
		$changetarifcost = str_replace(',','.',$row[0]);
		mysql_free_result($res);
	
		# Получаем баланс пользователя из базы данных
		$sql = "SELECT balance FROM search_tags WHERE shortguid = '".mysql_real_escape_string($_SESSION['shortguid'])."';";
			$res = mysql_query($sql,$GLOBALS['mysql']);
			$row = mysql_fetch_array($res);
			$ballance = $row[0];
			mysql_free_result($res);
					
		$bal = explode(" ",$ballance);
		$ballance = $bal[0];

		# Сравниваем баланс и стоимость смены тарифа
		$datetoday = date("Y-m-d");
		$timetoday = date("H:i:s");
		$datetoday.="  ". $timetoday;
		
		if ($ballance >= (float)$changetarifcost) {
			$uid = uniqid();
			
			$sql0 = "INSERT INTO actionslog VALUES('$datetoday','CHANGE_TARIF','".mysql_real_escape_string($_SESSION['login'])."','$tarif','$uid','".mysql_real_escape_string($_SESSION['login'])."');";
			$sql = "INSERT INTO actions VALUES('CHANGE_TARIF','".mysql_real_escape_string($_SESSION['login'])."','$tarif','$datetoday','$uid');";

				$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
				mysql_select_db($GLOBALS['mysql_db'], $GLOBALS['mysql']);
				mysql_query('SET NAMEs utf8mb4;');
				mysql_query($sql0,$GLOBALS['mysql']);
				mysql_query($sql,$GLOBALS['mysql']);
				MakeActivity($GLOBALS['mysql']);
				//mysql_close($GLOBALS['mysql']);
				
			// 236 - Заявка на изменение тарифа принята
			$out = $strings[236].'<script>Refresh("changetarif.php")</script>';
			
		} else {
			# Если стоимость больше, то выводим сообщение об ошибке
			// 237 - Недостаточно средств для изменения тарифа
			$out = $strings[237].'<script>Refresh("changetarif.php")</script>';
		}
	}
	return $out;
	}
	
# Вывод списка тарифов
function show_next_tarif($curtarif, $nexttarif)
{
		
		$strings=$GLOBALS['strings'];
		
		
		$curtarif=mysql_real_escape_string($curtarif);
		$nexttarif=mysql_real_escape_string($nexttarif);
		
		
		
		$query = "SELECT followingtarifs FROM tarifs WHERE tarif_name = '".$curtarif."'";
		
			$res = mysql_query($query, $GLOBALS['mysql']) or die(mysql_error());
			$res = mysql_fetch_array($res);
			$ftarifs = $res[0];

			
		$query = "SELECT tarifends FROM stat WHERE shortguid='".mysql_real_escape_string($_SESSION['shortguid'])."';";
			$res = mysql_query($query, $GLOBALS['mysql']);
			$row = mysql_fetch_array($res);
			$TarifEnds=$row[0];
						
			
		$tarifsa = explode ("||", $ftarifs);
		
		$NextTarif='';
		if (strlen($TarifEnds)>3) {
			// 238 - начнётся с 
			$NextTarif='  ('.$strings[238].' ' . $TarifEnds .')';
		}
		
		# Подготавливаем вывод списка тарифов (выпадающий список)

		$list = '';
		$LoadDefDescr='';
		$i = 0;
		if (strlen($nexttarif) == 0) {$nexttarif = $curtarif;}
			foreach ($tarifsa as $value) {
				$GrpA = explode('*-*',$value);
				$GRPName = html_entity_decode($GrpA[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
				$ss="";
				$ss2='';
				$Cost='&nbsp; ';
				if (count($GrpA)>1){$Cost=$GrpA[1];}
				if ($nexttarif == $GRPName) {$ss=" checked='checked' ";$ss2=$NextTarif;$Cost='';}
				if (strlen($GRPName) >0) {
					
					$sql = "SELECT `tarif_guid` FROM `tarifs` WHERE `tarif_name` = '".mysql_real_escape_string($GRPName)."';";
					$res = mysql_query($sql, $GLOBALS["mysql"]) or die (mysql_error());
					$row = mysql_fetch_array($res);			
					$TarifName=$row[0];
					
					
					
					
					$NameLNG =$GRPName;
					$sql = "SELECT `object_data` FROM `system_objects` WHERE `object_name` = '".mysql_real_escape_string($GRPName)."' and `object_type` = 0;";
					$result = mysql_query($sql,$GLOBALS ["mysql"]);
						
					if (mysql_num_rows($result)>0){
						
						$row2 = mysql_fetch_array($result);
						$JSON=json_decode($row2[0],true);
						
						if (!isset($JSON['NameLNG'])){
							$NameLNG = '';
						} else{		
							$NameLNG = $JSON['NameLNG'][$_COOKIE['MikroBILL_WEB_Language']];
						}
					}
					
					if (strlen($NameLNG)==0){$NameLNG =$GRPName;}
					
					if ($nexttarif == $GRPName) {$LoadDefDescr = $TarifName;}
					
					
					$list =  $list.'
							<div class="m2">
								<input type="radio" '.$ss.' value="'.$TarifName.'" name="tarif'.'" id="tarif'.$i.'" onclick="LoadDescription(this)" />
								<label for="tarif'.$i.'"> '.$NameLNG.'</label> 
								<i> '.$ss2. $Cost . '</i>
							</div>';
					$i = $i + 1;		
				}
			}
		
		$out='';
		
		# Подготавливаем форму с RadioGroup и кнопкой
		// 239 - Заказать изменение тарифа
		// 240 - Изменить тариф?
		// 241 - Описание
		// 234 - Выберите тариф, чтобы увидеть описание.
		if ($i>0){
			
			$Vis='';
			
			if ($i==1){
				$Vis='style="display: none;"';
			}
			
			$out = '
				<div class="chn-tarif">
					<form action=\'./changetarif.php\' method=\'POST\'>
						'.$list.'
						<div class="i m3" '.$Vis.'>'.$strings[234].'</div>
						<button type=\'button\' name=\'go\' onclick="ChangeTarif(this)" class="m3" '.$Vis.'>'.$strings[239].'</button>
					</form>
					<div style="visibility: hidden; border-width: 0 px;" id="opisanie" name="opisanie">
								
					</div>
				</div>';	
		} else {
			// 389 - Нет доступных тарифных планов!
			$out='<div class="i m3">'.$strings[389].'</div>';
		}
			// 441 - Выберите новый тарифный план!
			$out.= ' <script type="text/javascript">
					
					function LoadDescription(obj){
						var res;
						res = GetHTTP("api.php?action=GETGROUPDESCRIPTION&value=" + obj.value);
						SetText ("opisanie",res);
						if (res.length < 3){
							document.getElementById("opisanie").style.visibility = "hidden";
						} else{document.getElementById("opisanie").style.visibility = "visible";}
					}
	
					function GetHTTP(url) {
						var request=null;
						request=new XMLHttpRequest();
						request.open("GET", url, false);
						request.send(null);
						return request.responseText;
					}
		
					function SetText(obj, txt){
						document.getElementById(obj).innerHTML = txt;
					}

					function ChangeTarif(id){
						let checked = \'\';
						Array.from(document.getElementsByName(\'tarif\')).forEach(function(v){if(v.checked) checked = v.value});
						(checked == \''.$nexttarif.'\')?Confirm(id,\''.$strings[441].'\',true):Confirm(id,\''.$strings[240].'\');
					}
					</script>
					';
					
			if (strlen($LoadDefDescr)>0){
				$out.= ' <script type="text/javascript">
						var res;
						res = GetHTTP("api.php?action=GETGROUPDESCRIPTION&value='.$LoadDefDescr.'");
						SetText ("opisanie",res);
						
						if (res.length > 2){document.getElementById("opisanie").style.visibility = "visible";}
						
				</script>
				';
			
			}
			

	return $out;

	}

# Получение текущего тарифа пользователя и следующего рекомендуемого из базы базы данных
function getcurtarif()
{
	$query = "SELECT tarif, nexttarif FROM stat WHERE shortguid='".mysql_real_escape_string($_SESSION['shortguid'])."'";
	
		$result = mysql_query($query, $GLOBALS['mysql']);
		$result = mysql_fetch_assoc($result);
		$tarif['cur'] = $result['tarif'];
		$tarif['next'] = $result['nexttarif'];
		
	return $tarif;
}


function GroupsList($result){
	
	$tarif = "";
	$tarif = $result['tarif'];
	$Grps = array();
	$nexttarif = "";
	$nexttarif = $result['nexttarif'];

	$c=$_SESSION['login'] ;
	
	$sql = "SELECT `followingtarifs` FROM `tarifs` WHERE `tarif_name` = '".mysql_real_escape_string($tarif)."';";
		$res = mysql_query($sql,$GLOBALS ["mysql"]);
		$row = mysql_fetch_array($res);
		$ftarifs = $row[0];
		mysql_free_result($res);
		
	$tarifsa = explode ("||", $ftarifs);
	if (strlen($nexttarif)==0){$nexttarif=$tarif;}
	
	$Ret = "";
	
	$Ret = "<select name='tarif' onChange='TarifChangeInfo();' size='1' style='width: 330px'>
			<option value=''>";
		foreach ($tarifsa as $value) {
			$GRPName = $value;
			$ss="";
			$tarif2 = explode ("(", $GRPName);
			if ($nexttarif == trim($tarif2[0])) {$ss=" selected='selected' ";}
			if (strlen($GRPName) >0) {$Ret.=  "<option $ss value='" . $GRPName . "'>" . $GRPName . "</option>";}
			array_push($Grps,$GRPName);
		}
   	$Ret.= "</select>";
	
	$GLOBALS['tarifs_div'] = "";
	GetTarifsDescription($Grps, $tarif);
	
	return $Ret;
}

function GetTarifsDescription($Grps, $tarif){
	$sql = "SELECT tarif_name, tarifdescr FROM tarifs";
	$i=0;

		$res = mysql_query($sql,$GLOBALS ["mysql"]);
		while($row = mysql_fetch_array($res)) {
			$tarif_name = $row[0];
			$tarifdescr = $row[1];
			if ((is_numeric(array_search($tarif_name,$Grps))) or ($tarif == $tarif_name)){
				CreateGroupDescr($tarif_name,$tarifdescr, $i);
				$i++;
			}
			
		}
		mysql_free_result($res);
	
}

function CreateGroupDescr($Group, $Descr, $i){

	$GLOBALS['tarifs_div'] .= "<div id='grp$i' name='grp$i' style='display: none;'>";
	$Descr = str_replace("\r\n","&#13;&#10;",$Descr);
	$GLOBALS['tarifs_div'] .=  "$Group\r\n$Descr</div>";

}

function changeTarif()
{
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	// 242 - Изменение тарифа
	$result['page_title'] = $strings[242];
	$result['out'] ="";
	$CurTarif = "";
	$CurTarif = $result['tarif'];
	$tarifends = "";
	$tarifends = $result['tarifends'];
	$GrpsList = "";
	$GrpList = GroupsList($result);

	
	$c="";$t="";

	if ($_SESSION['auth'] && $GLOBALS['UserCanChangeTarif'] == "True" && isset($_REQUEST['tarif'])) {
		
		// Тут нужно установить тариф
		$c=mysql_real_escape_string($result['user_name']);
		if (isset($_REQUEST['tarif'])) {$t=mysql_real_escape_string($_REQUEST['tarif']);}
	
		$sql = "SELECT changetarifcost FROM tarifs WHERE tarif_name = '$t';";
			$res = mysql_query($sql,$GLOBALS ["mysql"]);
			$row = mysql_fetch_array($res);
			$changetarifcost = $row[0];
			mysql_free_result($res);
			
		$ballance = "";
		$ballance = $result['ballance'];
		$bal = explode(" ",$ballance);
		$ballance = $bal[0];
		
		if (intval($ballance) > intval($changetarifcost)) {
			$uid = uniqid("");
			$datetoday = date("Y-m-d H:i:s");
			$sql = "INSERT INTO addcash VALUES('$c','CHANGE TARIF','$t','',0,'$uid','CHANGE TARIF','$datetoday');";
				mysql_query($sql,$GLOBALS ["mysql"]);
				MakeActivity($GLOBALS ["mysql"]);
					
			// 243 - Тариф изменён!
			// 244 - Выбор тарифа
			$result['out'].='<h1>'.$result['page_title']."</h1>
			<fieldset>
				<legend>".$strings[244]."</legend>
				<br><font color='green'><b>".$strings[243]."</b></font><br><br><br>
				<META HTTP-EQUIV=Refresh Content='3;URL=index.php'>
			</fieldset>";
		} else {
			// 244 - Выбор тарифа
			// 237 - Недостаточно средств для изменения тарифа
			$result['out'].='<h1>'.$result['page_title']."</h1>
			<fieldset>
				<legend>".$strings[244]."</legend>
				<br><font color='red'><b>".$strings[237]."</b></font><br><br><br>
				<META HTTP-EQUIV=Refresh Content='3;URL=changetarif.php'>
			</fieldset>";
		}
	}
	else {
		if ($tarifends <> "-") {
			if (strtotime(date("Y-m-d H:m:s")) < strtotime($tarifends)) {$tarifends= " &nbsp; (окончание: $tarifends)";}
		} else {$tarifends = "";}
	
		// 245 - Выберите следующий тарифный план из списка ниже.
		// 244 - Выбор тарифа
		// 246 - Текущий тариф
		// 247 - Следующий тариф
		// 248 - Выбрать тариф
		// 241 - Описание
		$result['out'].='<h1>'.$result['page_title']."</h1>
		<body onload='TarifChangeInfo()'>
		<p>".$strings[245]."</p>
		<fieldset>
		
		<STYLE TYPE='text/css'>

		TD{font-family: Arial; font-size: 10pt;}
		
		</STYLE>
		<legend>".$strings[244]."</legend>
			
			<table>

				<tr>
					<td>".$strings[246].":</td>
					<td>$CurTarif $tarifends</td>
					<td></td>
				</tr>
				<tr>
					<td>".$strings[247].":</td>
					<td>$GrpList</td>
					<td><button style='width:10px;height:22px' alt='".$strings[248]."' title='".$strings[248]."' onClick='ChangeTarif(" . '"' . $CurTarif . '"' . ")'><img src='./img/accept.png'></button></td>
				</tr>
				<tr>
					<td style='vertical-align: top;'>".$strings[241].":<br><br><br><br><br></td>
					<td colspan=2><textarea name='tarifdescr' id='tarifdescr' rows='12' cols='35' readonly/></textarea></td>
				</tr>
			</table>
			
		</fieldset>";
		
		
		// 249 - Выберите тариф!
		// 250 - Выбрать тариф %s в качестве следующего тарифа?
		$result['out'].='		
		<script type="text/javascript">
			function GetText(obj){
				//alert(document.getElementById(obj).innerHTML);
				return document.getElementById(obj).innerHTML;
			}
	
			function ChangeTarif(CurTarif) 
			{
				if (window.document.all.tarif.value == "") {
					alert("'.$strings[249].'");
				} else {
					NewTarif = window.document.all.tarif.value.split("  (")[0];
					if (CurTarif != NewTarif) {
						if (confirm("'.sprintf($strings[250],'\'" + NewTarif + "\'').'")) {window.location="changetarif.php?tarif=" + NewTarif;}
					} else {alert("Данный тариф уже установлен!");}
				}
			}
			
			function TarifChangeInfo() { 
				
				var Tarif = window.document.all.tarif.value.split("  (")[0];

				for (var i = 0; i < window.document.all.tarif.options.length-1; i++) {
					var txtA = GetText("grp" + i).split("\n");
					var txt = txtA[0];
					if (Tarif == txt) {
						txtA[0]="";
						txt = txtA.join("");
						window.document.all.tarifdescr.value = txt;
						break;
					}
				}
			}
		</script>';
	
		$result['out'].=$GLOBALS['tarifs_div'];
	}
		
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
}

function ShowPersonalFrame($FileName)
{
	
	if (!isset($FileName)){$FileName='';}
	if ((substr(strtolower($FileName),0,7)=='http://')||(substr(strtolower($FileName),0,8)=='https://')){header('location: '.$FileName);}
	
	$result = getuserinfo();
	$Title='';
	
	$menu_id=-1;
		
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
		
		$sql = "SELECT `menu_name`, `menu_id` FROM `custom_menu` WHERE `menu_value` = '". mysql_real_escape_string($FileName)."';";
		$mysqlResult = mysql_query($sql,$mysql);
		
		if (mysql_num_rows($mysqlResult)>0){
			$row = mysql_fetch_array($mysqlResult);
			$Title=$row[0];
			$menu_id=$row[1];
			$FileName=str_replace(array('..',':','\\'),'',$FileName);
		}
		
	$file='';
	
	if ($GLOBALS['UsePHPeval']==1){
		
		if (mysql_num_rows($mysqlResult)==0){echo('This file is not allowed! Add the menu file via the WEB personalization settings.'); exit();}
		
		
		//ob_start();
		//$file=str_replace('<?php','',$file);
		//$file=str_replace('<?','',$file);
		//$file=str_replace('? > ','',$file); - убрать пробел между ? и > !!!

		//$file=eval($file);
		//$file = ob_get_contents();
		//ob_end_clean();
		
		$file='<iframe name="myiframe" id="myiframe" src="'.$FileName.'" align="center" frameborder="0" scrolling="no" onload="this.width  = this.contentWindow.document.body.scrollWidth; this.height = this.contentWindow.document.body.scrollHeight;">
					Ваш браузер не поддерживает плавающие фреймы!
				</iframe>';
		//echo $file; exit;
	} else {
		if (file_exists($FileName)){
			$f_pointer	= fopen($FileName, 'r');
			$file		= fread($f_pointer, filesize($FileName));
			fclose($f_pointer);
		} else {
			echo 'File not found!';
		}
	}
	
	$result['page_title'] = $Title;
	
	$crumbs = '';
	$bg='personal';
	$url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	switch ($menu_id) {
    case 0:
		$crumbs = menuManager('two',array($url,$Title));
		$bg = 'two';
        break;
    case 1:
		$crumbs = menuManager('four',array($url,$Title));
		$bg = 'four';
        break;
    case 2:
		$crumbs = menuManager('three',array($url,$Title));
		$bg = 'three';
        break;
	case 3:
		$crumbs = menuManager('six',array($url,$Title));
		$bg = 'six';
		break;
	}
	
	
	if (!isset($_REQUEST['flat'])){
		$file='<div class="content"><div class="'.$bg.'">'.$file.'</div></div>';
	}	
	
		
	$result['out'] =  $crumbs.$file;
	//echo $file; exit;
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
}

function payCards()
{
	
	$result = getuserinfo();
	//CheckProfile($result);
	$strings=$GLOBALS['strings'];
	
	$crumbs = menuManager('four',array("./cards.php",$strings[17]));	
	
	if (($GLOBALS['NoAccessWithoutPassport']=='True')||($GLOBALS['NoPayWithoutOferta']=='1')){
		CheckProfile($result,$crumbs,'four');
	}
	
	// 251 - Оплата картой
	$result['page_title'] = $strings[251];
	
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');
	
			
	$result['out'] ="";
	
	if (isset($_POST['go']))
	{
	    if ($GLOBALS['EnablePayCards']<>'True'){exit();}
		
		$login = mysql_real_escape_string($_SESSION['login']);
		$shortguid = mysql_real_escape_string($_SESSION['shortguid']);
		$cardlogin = mysql_real_escape_string($_REQUEST['cardlogin']);
		$cardpassword = mysql_real_escape_string($_REQUEST['cardpassword']);
		$client_name ='';
		
		$sql = "SELECT tarif_guid, client_name2, group_guid FROM stat WHERE shortguid = '$shortguid'";
			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$grp = $row[0];
			$grp2 = $row[2];
			$client_name = mysql_real_escape_string($row[1]);
			mysql_free_result($mysqlResult);
		
		$tarif = strtoupper($grp);
			
		if (strlen($client_name)==0){
			// 252 - Активация карты оплаты
			// 253 - Ошибка! Войдите снова в свою учётную запись.
			$result['out'].= OutMessage($strings[252], $strings[253], $crumbs);
			return parse_template($result, './template/default.php',NULL,FALSE,true); 
		}
			
	
		$sql= "SELECT * FROM cards WHERE (cardlogin='$cardlogin' and cardpassword='$cardpassword' and isenabled = 1);";
		
		$money = 0;

			$mysqlResult = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($mysqlResult);
			$crd = $row[0];
			$money = $row[2];
			$moneytype=$row[5];
			$activator = $row[6];
			$cardgrp =  $row[7];
			$cardgrp2 =  $row[13];
			$cardn  =  $row[8];
			mysql_free_result($mysqlResult);
				
		
		if (strlen($crd) > 0) 
		{
		
			if ($moneytype <> 'NEW_CLIENT') 
			{
				//echo "cardgrp=$cardgrp<br>";
				//echo "cardgrp2=$cardgrp2<br>";
				//echo "grp=$grp<br>";
				//echo "grp2=$grp2<br>";
				
			
				//$res1=((((strlen($cardgrp) < 5) || ($cardgrp == $grp)) ));
				//$res2=(((strlen($cardgrp2) < 5) || ($cardgrp2 == $grp2)) );
				//echo "$res1<br>";
				//echo "$res2<br>";
				//exit();
				if (((strlen($cardgrp) < 5) || ($cardgrp == $grp)) && ((strlen($cardgrp2) < 5) || ($cardgrp2 == $grp2)) )
				{
					$money = str_replace(",",".",$money)+0;
					
					if (($money > 0) and ($activator == '')) 
					{
		
						$moneycash = 0;
						$moneydays = 0;
						//moneytype
						if ($moneytype==0) 
						{
							$moneycash = $money;
						}
						if ($moneytype==1) 
						{
							$moneydays = $money;
						}
						
						if (!isset($_REQUEST["startonfirst"])){$_REQUEST["startonfirst"]='';}
						
						if ($_REQUEST["startonfirst"] == 'on'){
							$datetoday = date("Y-m-d H:i:s");
							$uid = uniqid("");
							$sql = "INSERT INTO `actions` VALUES ('START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
							mysql_query($sql,$mysql);
						} else {
							$datetoday = date("Y-m-d H:i:s");
							$uid = uniqid("");
							$sql = "INSERT INTO `actions` VALUES ('NO_START_ME_AT_1ST','$login','$datetoday','".mysql_real_escape_string($_REQUEST["startonfirst"])."','$uid');";
							mysql_query($sql,$mysql);
						}
						
						
						$datetoday =date("Y-m-d H:i:s");
						$uid = uniqid();
						
						$moneycash=mysql_real_escape_string($moneycash);
						$moneydays=mysql_real_escape_string($moneydays);
						$cardn=mysql_real_escape_string($cardn);
						
						// add cash
						$sql = "INSERT INTO addcash2 VALUES ($shortguid,$moneycash,$moneydays,'card №$cardn', 0, '$uid','', '$datetoday');";
						// disable card	
						$sql2 = "UPDATE `cards` SET `activatedate`='$datetoday', `activator`='$client_name' WHERE `cardlogin`='$cardlogin';";
						
						mysql_query("UPDATE `cards` SET `cardpar`=0 WHERE `cardlogin`='$cardlogin';",$mysql) or die ("Activate card error!");
						
						if (mysql_query($sql2,$mysql)){
							mysql_query($sql,$mysql);
						}
									
						MakeActivity($mysql);
				
						// 252 - Активация карты оплаты
						// 254 - Баланс будет пополнен в течении пяти минут!
						$result['out'] = OutMessage($strings[252],'<i class="shout fas fa-check-circle"></i>'.$strings[254],$crumbs);						
					} 
					else 
					{
						// 252 - Активация карты оплаты
						// 255 - Карта уже была активирована!
						$result['out'] .= OutMessage($strings[252],'<i class="shout fas fa-times-circle"></i>'.$strings[255],$crumbs);						
					}
				} 
				else 
				{
					// 252 - Активация карты оплаты
					// 256 - Карта не предназначена для этого тарифа!
					$result['out'].= OutMessage($strings[252],'<i class="shout fas fa-exclamation-circle"></i>'.$strings[256],$crumbs);
				}
			} 
			else 
			{
				// 252 - Активация карты оплаты
				// 257 - Уже зарегистрированный пользователь не может активировать эту карту!
				$result['out'].= OutMessage($strings[252],'<i class="shout fas fa-exclamation-circle"></i>'.$strings[257],$crumbs);
			}
		} 
		else 
		{
			// 252 - Активация карты оплаты
			// 258 - Неверный логин или пароль карты!
			$result['out'].= OutMessage($strings[252],'<i class="shout fas fa-exclamation-circle"></i>'.$strings[258],$crumbs);
		} 
		$result['out'].= "<script>Refresh('cards.php')</script>";
	}
	else
	{
		// 1- Пароль карты
		// 259 - <p>Пополнение интернет-счета по карте оплаты – это наиболее быстрый способ оплатить связь. Необходимо лишь купить одну из карт наиболее удобного номинала, а затем активировать ее.</p>
		// 252 - Активация карты оплаты
		// 260 - Логин карты
		// 261 - Пароль карты
		$result['out'] = '
							'.$crumbs.'
							<div class="content">
								<div class="four">
									<i class="fas fa-piggy-bank"></i>
									<div class="h3 m4">'.$strings[252].'</div>
									<div class="m2">'.$strings[259].'</div>	
									<form action="" method="POST" >							
										<div id="" class="m2">
											<input type="text" name="cardlogin" value="" placeholder="'.$strings[260].'" autocomplete="off"/>
										</div>
										<div class="m2">
											<input type="text" name = "cardpassword" value="" placeholder="'.$strings[261].'" autocomplete="off"/>
										</div>';
							if ($GLOBALS['ClientCanStartAt1st']=='True'){
								$otherinfoA=explode('||', $result['otherinfo']);
								if ($otherinfoA[35]=='0') {
									$v='';
									if ($otherinfoA[77]=='True'){$v='checked';}
									// 214 - Запустить с началом следующего расчётного периода				
									$result['out'].='
										<div id="" class="m3">
											<input type="checkbox" name="startonfirst" id="startonfirst" $v>
											<label for="startonfirst">'.$strings[214].' - '.$result['paytime'].'</label>
										</div>';
								}
							}

				// 262 - Активировать карту
				$result['out'].='
										<button type="submit" name="go" class="m3">'.$strings[262].'</button>					
									</form>
								</div>
							</div>
		';
	}
	
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
}

function OutMessage($Cap, $Msg, $crumbs=''){
	$strings=$GLOBALS['strings'];
	$Out = $crumbs.'
							<div class="content">
								<div class="four">
									<!-- <div class="h3 m4">' .$Cap.'</div> -->
									<div class="i m3">'.$Msg.'</div>
								</div>
							</div>
							<script>Refresh("index.php?page=index")</script>';
	return $Out;
}

#========Статические страницы=========#
function faqpage() 
{
	$strings=$GLOBALS['strings'];
	$result = getuserinfo();
	$result['page_title'] = $GLOBALS['faqpage']['page_title'];
	$result['out'] = '
					'.menuManager('six',array("./faq.php",$strings[31])).'
					<div class="content">
						<div class="six">
							<i class="fas fa-life-ring"></i>
							<div class="h3 m4">'.$GLOBALS['faqpage']['page_title'].'</div>
							<div class="m2">'.$GLOBALS['faqpage']['contents'].'</div>							
						</div>
					</div>';
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
}


function PWA_Info() 
{
	//629 - Push уведомления
	$strings=$GLOBALS['strings'];
	$result = getuserinfo();
	//$result['page_title'] = $GLOBALS['faqpage']['page_title'];
	
	$lng_arr=[];
	$lng_arr["%STRINGS_479%"] = $strings[479]; //OK
	for ($i=630;$i<=644;$i++){
		$lng_arr["%STRINGS_$i%"] = $strings[$i];
	}
	$JSON=str_replace('\\','\\\\',json_encode($lng_arr));
	$JSON2=str_replace("'","\\'",$JSON);
	$JSON=str_replace('"','\"',$JSON);
	
	$shortguid = mysql_real_escape_string($_SESSION['shortguid']);
	
	//echo ($JSON);exit();
	
	$result['out'] = '
					'.menuManager('three',array("./pwa_info.php",$strings[629])).'
					<div class="content">
						<div class="three">
							<i class="fas fa-life-ring"></i>
							<div class="h3 m4">'.$strings[629].'</div>
							<div class="m2" id="m2_content"><br><br><button onclick=\'subscribe(' . htmlspecialchars(json_encode($lng_arr), ENT_QUOTES, 'UTF-8') . ',' . $shortguid . ');\'>'.$strings[639].'</button></div>							
						</div>
					</div>
					
	<script>
		document.getElementById("m2_content").innerHTML = getPwaInstallHint("'.$JSON.'") + document.getElementById("m2_content").innerHTML;
	</script>';
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
}

	
	
function mysql_result_all($result, $borderinfo) {
	$nrow = 0;
	$Ret=$borderinfo;
	//for($i = 0; $i < mysql_num_fields($result); $i++) echo("<th>".mysql_field_name($result, $i)."</th>\n");
	//echo("</tr>\n");
	while($row = mysql_fetch_array($result)) {
		$Ret .="<tr>";
		for($i = 0; $i < mysql_num_fields($result); $i++) {
			$row[$i]=str_replace("\n", '<br>', $row[$i]);
			if ($i==0){
				$Ret .="<td><b>$row[$i]</b></td>\n";
			} else {$Ret .="<td>$row[$i]</td>\n";}
		}
		$Ret .= "</tr>\n";
		$nrow++;
	}
	$Ret .= "</table>\n";
	return $Ret;
}
	
function contactspage() 
{
	$strings=$GLOBALS['strings'];
	$result = getuserinfo();
	
	$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
	mysql_select_db($GLOBALS['mysql_db'], $mysql);
	mysql_query('SET NAMEs utf8mb4;');
	
	$res = mysql_query("SELECT * FROM `contacts`;",$GLOBALS ["mysql"]);
	$contacts=mysql_result_all($res,"<table><tr>\n");
			
	
	$result['out'] = '
				'.menuManager('six',array("./contacts.php",$strings[32])).'
				<div class="content">
					<div class="six">
						<i class="fas fa-map-marker-alt"></i>
						<div class="h3 m4">'.$GLOBALS['contacts']['page_title'].'</div>
						<div class="m2">'.$contacts.'</div>							
					</div>
				</div>';
	return parse_template($result, './template/default.php',NULL,FALSE,true); 
	}



# Проверка имени пользователя и пароля из базы данных
function UserExistsInDB_ODBC ($user, $pswd)
{
		//$user=mysql_real_escape_string($user);
		//$pswd=mysql_real_escape_string($pswd);
		
		$REPORT['state'] = FALSE;
		$REPORT['finded_usr_guid']='0';
		
		$strings=$GLOBALS['strings'];
				
		// В PHP криво работает преобразование в верхний регистр для кириллицы, поэтому используем для этого MySQL
		$usl='`user_name`';
		$usl2="'$user'";
		if ($GLOBALS['NoCaseSensitiveLogin']==1){
			$usl2="UPPER('$user')";
			$usl='UPPER(`user_name`)';
		}
		
		# Получение данных пользователя из базы
		$sql = "SELECT `user_name`, `user_pswd`, `usrip`, `shortguid` FROM `stat` WHERE $usl = $usl2;";
		

		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']) or die (mysql_error());
		mysql_select_db($GLOBALS['mysql_db'], $mysql) or die (mysql_error());
		$result = mysql_query($sql,$mysql) or die (mysql_error());

			if (mysql_num_rows($result)>0){
			
				$row = mysql_fetch_assoc($result);
				$REPORT['finded_usr_guid']=$row['shortguid'];
				
				if (CheckLogonTime_User(0,3,$row['shortguid'],0, $mysql,0)==false){
					$REPORT['state'] = FALSE;
					// 263 - Слишком много неверных попыток авторизоваться. Пожалуйста, подождите.
					$REPORT['err_code'] = $strings[263];
					return $REPORT;
				}
			
				$sql = "SELECT `user_name`, `user_pswd`, `usrip`, `shortguid` FROM `stat` WHERE $usl = $usl2 and `user_pswd`='$pswd';";
				//echo $sql;exit();
				$result = mysql_query($sql,$mysql);
						
				if (mysql_num_rows($result)>0){
					$row = mysql_fetch_assoc($result);
					$ips = explode(";", $row['usrip']);
					$UN = $row['user_name'];
					$guid = $row['shortguid'];
									
				} else {
					$REPORT['state'] = FALSE;
					// 264 - Неверный логин или пароль!
					$REPORT['err_code'] = $strings[264];
					return $REPORT;
				}
			} else {
				
				$REPORT['state'] = FALSE;
				// 264 - Неверный логин или пароль!
				$REPORT['err_code'] = $strings[264];
				return $REPORT;
			}
			CheckLogonTime_User(0,3,$guid,1,$mysql,1);	
		
		
	# Проверка IP-адреса пользователя, если разрешен вход только с одного IP

	if (strlen($UN)>0) {	
		if ($GLOBALS ['SingleIPLogon'] == 'True') {
			include "getip.php";
			$uip=$LocalIP;
			
			foreach ($ips as &$ipval) {
				if ( $uip == $ipval) {
					$REPORT['state'] = TRUE;
					$REPORT['guid'] = $guid;
					CheckLogonTime_User(0,3,$row['shortguid'],1, $mysql,0);
					return $REPORT;}
				}

			$REPORT['state'] = FALSE;
			// 265 - Не разрешён вход с этого IP!
			$REPORT['err_code'] = $strings[265];
			return $REPORT;
			}	
		$REPORT['state'] = TRUE;
		$REPORT['guid'] = $guid;
		return $REPORT;
		} else {
			$REPORT['state'] = FALSE;
			// 264 - Неверный логин или пароль!
			$REPORT['err_code'] = $strings[264];
			return $REPORT;
			}
	}

function ShowChatra(){
	if (isset($GLOBALS['UseChatra'])){
		if ($GLOBALS['UseChatra']==1){
			return '<script>
			(function(d, w, c) {
				w.ChatraID = "'.$GLOBALS['ChatraKEY'].'";
				var s = d.createElement("script");
				w[c] = w[c] || function() {
					(w[c].q = w[c].q || []).push(arguments);
				};
				s.async = true;
				s.src = (d.location.protocol === "https:" ? "https:": "http:")
				+ "//call.chatra.io/chatra.js";
				if (d.head) d.head.appendChild(s);
			})(document, window, "Chatra");
			</script>';
			}
	}
	return '';
}

# Функция авторизации пользователя
function authentication() 
{

	$strings=$GLOBALS['strings'];
	
	if (!isset($GLOBALS['HotSpotWebAuth'])){$GLOBALS['HotSpotWebAuth']='';}
	if (!isset($GLOBALS['ShowContractOnFirstLogon'])){$GLOBALS['ShowContractOnFirstLogon']='';}		
	
	if (isset($_REQUEST['go'])) {

		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query('SET NAMEs utf8mb4;');

		$login = mysql_real_escape_string($_REQUEST['login']);
		$pass = mysql_real_escape_string($_REQUEST['pass']);
			
		
		# Проверяем данные о пользователе

		$auth = UserExistsInDB_ODBC($login, $pass);		 		

		# Если данные верны, записываем информацию о пользователе в сессию и переходим на главную страницу
		include 'getip.php';
		
		$ishotspot=false;

		if ($auth['state']) {
			
			
			$usl='`user_name`';
			$usl2="'$login'";
			if ($GLOBALS['NoCaseSensitiveLogin']==1){
				$usl2="UPPER('$login')";
				$usl='UPPER(`user_name`)';
			}
			
			
			$sql = "SELECT `otherinfo`, `isarchived`, `user_name` FROM `stat` WHERE $usl = $usl2 and `user_pswd` = '$pass'";
			
			$res = mysql_query($sql,$mysql);
			$row = mysql_fetch_array($res);
			
			# Записываем сессию пользователя
			$_SESSION['login'] = $row[2];

			
			$OtherInfo=explode('||',$row[0]);
			
			if ($row[1]==1 && $GLOBALS ['ArchiveWEBLogin'] <> 'True'){
				$_SESSION['auth']=false;
				$auth['state']=false;
				// 354 - Доступ ограничен!
				$auth['err_code'] = $strings[354];
			}
			
			
			if ($OtherInfo[131]=='1'){
				$_SESSION['auth']=false;
				$auth['state']=false;
				// 354 - Доступ ограничен!
				$auth['err_code'] = $strings[354];
			}
			
		}



		if ($auth['state']) {
			//echo 'State='. $auth['state'];
			
			if (isset($_COOKIE['MikroBILL_WEB_Language'])){
				$uid = uniqid("");
				$shortguid=mysql_real_escape_string($auth['guid']);	
				$mylng=mysql_real_escape_string($_COOKIE['MikroBILL_WEB_Language']);
				$sql = "INSERT INTO actions VALUES ('SET_WEB_LANGUAGE','$shortguid','$mylng','','$uid');";
				mysql_query($sql,$mysql);
			}
			
			
			$sql = "INSERT INTO `logons` (`user_id`, `user_type`, `actiondate`, `ip`, `state`, `result`) VALUES (".mysql_real_escape_string($auth['guid']).",0,".(time() + $GLOBALS['TimeOffset']).",'".mysql_real_escape_string($LocalIP)."',1,1);";
			mysql_query($sql,$mysql);
			
			$_SESSION['password'] = $pass;
			$_SESSION['auth'] = true;
			$_SESSION['guid'] = $auth['guid'];
			$_SESSION['shortguid'] = $auth['guid'];
		
			# WEB-авторизация
			if (($GLOBALS['UseWebAuth'] == "True") && ($GLOBALS['AutoWebAuth'] == "True")){
				include_once "getip.php";
				$uid = uniqid("");
				
				if ($GLOBALS ["WEB_AuthAddOnlyGrayIP"]=='1'){
					if (filter_var($LocalIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)){
						$LocalIP='';
					}
				}
				
				if (strlen($LocalIP)>0){
					$sql = "INSERT INTO actions VALUES ('WEB_AUTORIZE','$login','$pass','".mysql_real_escape_string($LocalIP)."','$uid');";
					mysql_query($sql,$mysql);
					MakeActivity($mysql);
				}

				$sql = "SELECT user_name, otherinfo, shortguid FROM stat WHERE user_name = '$login' and user_pswd = '$pass'";
				$res = mysql_query($sql,$mysql);
				$row = mysql_fetch_array($res);
				$Client = $row[0];
				$OtherInfo = $row[1];
				mysql_free_result($res);
				
				$OP = explode("||", $OtherInfo);
				$OP[7]="True";
				$OtherInfo = mysql_real_escape_string(implode("||",$OP));
				$sql = "UPDATE stat SET `otherinfo`='$OtherInfo' WHERE `user_name` = '$login' and `user_pswd` = '$pass';";
				mysql_query($sql,$mysql);
				$_SESSION['web_auth'] = True;
				$_SESSION['web_auth_login'] = $login;
				$_SESSION['shortguid'] = $row[2];
				MakeActivity($mysql);
			} else {
				
				$sql = "SELECT webauth FROM stat WHERE shortguid = '".mysql_real_escape_string($_SESSION['guid'])."';"; 
				
				$res = mysql_query($sql,$mysql);
				$row = mysql_fetch_array($res);
				
				if ($row[0]=='1') {
					include_once "getip.php";
					$uid = uniqid("");
					
					
					if ($GLOBALS ["WEB_AuthAddOnlyGrayIP"]=='1'){
						if (filter_var($LocalIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)){
							$LocalIP='';
						}
					}
					
					if (strlen($LocalIP)>0){
						$sql = "INSERT INTO actions VALUES ('WEB_AUTORIZE','$login','$pass','".mysql_real_escape_string($LocalIP)."','$uid');";
						mysql_query($sql,$mysql);
						MakeActivity($mysql);
					}
				}
			}
			
			
			if ($GLOBALS['HotSpotWebAuth'] == "True"){
			
				$sql = "SELECT otherinfo FROM stat WHERE shortguid = '".$_SESSION['guid']."';"; 
				
				$res = mysql_query($sql,$mysql);
				$row = mysql_fetch_array($res);
				$row=explode('||',$row[0]);
				
				if (isset($_SERVER['HTTPS'])){$pref='https://';}else{$pref='http://';}
				
				if ($row[5]=='HotSpot'){
					$ishotspot=true;
					echo '<script type="text/javascript">	
				
				var URL;
				URL = "' . $pref . $row[66].'/login?username=" + encodeURIComponent("'.trim($login).'") + "&password=" + encodeURIComponent("'.trim($pass).'");
				GetHTTP(URL);
				
				
	function GetHTTP(url) {
		var request=null;
		request=new XMLHttpRequest();
		request.open(\'GET\', url, false);
		request.send(null);
		return request.responseText;
	}
	</script>
	';	  
}
			}
			
			$NeedToNavigate='';	
			if (($GLOBALS['ShowContractOnFirstLogon'] == "True") && strlen($GLOBALS['FirstLogonPage'])>3){
				
				$sql = "SELECT user_id FROM login_first WHERE user_id = '".mysql_real_escape_string($_SESSION['guid'])."';";
				$res = mysql_query($sql,$mysql);
				$row = mysql_fetch_array($res);
				$UN=0;
				$UN=$row[0];
				if ($UN==0) {
					$sql = "INSERT INTO login_first VALUES ('".mysql_real_escape_string($auth['guid'])."');";
					mysql_query($sql,$mysql);
					//header('location: showpersonalframe.php?page='.$GLOBALS['FirstLogonPage']);
					$NeedToNavigate='showpersonalframe.php?page='.$GLOBALS['FirstLogonPage'];
				} else {
					//header('location: index.php');
					if (strlen($GLOBALS['CustomDefWEBpage'])>3){
						$NeedToNavigate=$GLOBALS['CustomDefWEBpage'];
					}else {$NeedToNavigate='index.php';}
				}
			} else {
				//header('location: index.php');
				
				if (strlen($GLOBALS['CustomDefWEBpage'])>3){
					$NeedToNavigate=$GLOBALS['CustomDefWEBpage'];
				}else {$NeedToNavigate='index.php';}
			}
			
			setcookie("MikroBILL_Last_Login",$login,time()+333333333);
			
			
			if ((strlen($NeedToNavigate)>0)&&(isset($_REQUEST['page']))) {
				$NeedToNavigate=$_REQUEST['page'].'.php';
			}
			
			if (strlen($NeedToNavigate)>0) {
				if ($ishotspot==true){
					echo '<script type="text/javascript">
						window.location="'.$NeedToNavigate.'";
					</script>
					';
				} else {header('location: '.$NeedToNavigate);}
			}
		
			
			# Если нет, выводим страницу авторизации с сообщением об ошибке
		} else { 
						
				$sql = "INSERT INTO `logons` (`user_id`, `user_type`, `actiondate`, `ip`, `state`, `result`) VALUES (".mysql_real_escape_string($auth['finded_usr_guid']).",0,".(time() + $GLOBALS['TimeOffset']).",'".mysql_real_escape_string($LocalIP)."',0,0);";
				mysql_query($sql,$mysql);
				
				$GLOBALS['strings'][0]='';
				if ($GLOBALS['UseMaskedLogin']==1){
					$GLOBALS['strings'][0]='placeholder="'.$GLOBALS['LoginPlaceholder'].'" data-slots="'.$GLOBALS['LoginInputSlot'].'" data-accept="'.$GLOBALS['LoginPattern'].'"';
				}
				
				
				$template_name = './template/login_temp.php';
				$result['out']='';
				$file		= parse_template($result, $template_name,NULL,FALSE,true);
				
				// 361 - Ошибки!
				// 353 - Забыли пароль?
				$error = '<div class="i mess"><i class="shout fas fa-exclamation-circle"></i>'.$auth['err_code'].'</div>';
				//if (isset($_SESSION)) $file =  str_replace('%LOGIN%', $_SESSION['login'], $file);
				
				$Login='';
				if (isset($_COOKIE['MikroBILL_Last_Login'])){
					$Login=$_COOKIE['MikroBILL_Last_Login'];
				}
				if (strlen($Login)<1){
					if (isset($_SESSION['login'])){
						$Login = $_SESSION['login'];
					}
				}
				
				$file		= str_replace('%LOGIN%', $Login, $file);
				
				$agent=GetLogonOptions();
				
				
				$file		= str_replace('%ERRORS%', $error, $file);
				$file		= str_replace('%LOGIN_CSS_CLASS%', 'login-s2', $file);
				$file		= str_replace('%LOGIN_CSS_CLASS2%', 'login-p float-left', $file);
				$file		= str_replace('%AGENT%', $agent, $file);
				$file		= str_replace('%INFO%', '', $file);
				echo $file;				
							
			}
			mysql_close($mysql);
		} else {
			
			$GLOBALS['strings'][0]='';
			if ($GLOBALS['UseMaskedLogin']==1){
				$GLOBALS['strings'][345] = $GLOBALS['LoginPlaceholder'];
				$GLOBALS['strings'][0]=' data-slots="'.$GLOBALS['LoginInputSlot'].'" data-accept="'.$GLOBALS['LoginPattern'].'"';
			}
			
			$template_name = './template/login_temp.php';
			$result['out']='';
			$Chatra=($GLOBALS['ChatraOnMainPage']==1)?(true):(false);
			
			$file		= parse_template($result, $template_name,NULL,FALSE,$Chatra);
			
			if (!isset($error)){$error="";}
			if (!isset($_SESSION['login'])){$_SESSION['login']="";}
			//if (isset($_SESSION)) $file =  str_replace('%LOGIN%', $_SESSION['login'], $file);
			$agent='';
			$Point=-10;
			
			if (!isset($GLOBALS['UseCardForCreateClient'])){$GLOBALS['UseCardForCreateClient']='';}
			if (!isset($GLOBALS['SMS_Registr'])){$GLOBALS['SMS_Registr']='';}
			if (!isset($GLOBALS['Email_Registr'])){$GLOBALS['Email_Registr']='';}
			if (!isset($GLOBALS['WEB_Basement'])){$GLOBALS['WEB_Basement']='';}
			
			
			$agent=GetLogonOptions();


			$Login='';
			$info='';
			if (isset($_COOKIE['MikroBILL_Last_Login'])){
				$Login=mysql_real_escape_string($_COOKIE['MikroBILL_Last_Login']);
			}
			if ((strlen($Login)>0)&&($GLOBALS['UseAccidentMode']=='1')){
				
				$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
				mysql_select_db($GLOBALS['mysql_db'], $mysql);
				mysql_query('SET NAMEs utf8mb4;');
		
				$sql = "SELECT user_name, otherinfo, shortguid FROM stat WHERE user_name = '$Login'";
				$res = mysql_query($sql,$mysql);
				
				if (mysql_num_rows($res) > 0) {
					$row = mysql_fetch_array($res);
					$Client = $row[0];
					$OtherInfo = $row[1];
					mysql_free_result($res);
					
					$OtherInfoA = explode("||", $OtherInfo);
					if ($OtherInfoA[132]=='1'){
						$info='<div class="info_red" z-index=0><div>'.$GLOBALS['AccidentInformation'].'</div></div>';
					}
				}
			}

			
				
			$file		= str_replace('%ERRORS%', $error, $file);
			$file		= str_replace('%INFO%', $info, $file);
			$file		= str_replace('%LOGIN_CSS_CLASS%', 'login-s', $file);
			$file		= str_replace('%LOGIN_CSS_CLASS2%', 'login-p', $file);
			$file		= str_replace('%AGENT%', $agent, $file);
			
			//print($_COOKIE['MikroBILL_Last_Login']);
			//exit();
			echo $file;
			}			
	}

function GetLogonOptions(){
	
	$strings=$GLOBALS['strings'];
	$agent='';
	// 52 - Скачать агента авторизации
	// 358 - Зарегистрировать нового клиента картой
	// 359 - Зарегистрировать нового клиента через SMS
	// 360 - Зарегистрировать нового клиента через Email
	if ($GLOBALS['use_agent']=='True') {
		$agent='<div class="m2"><a href="agent.php" target="_blank">'.$strings[52].'</a></div>';}
	if (($GLOBALS['UseCardForCreateClient']=='True')&&($GLOBALS['Card_RegistrOnlyDirectLink']=='0')){
		$agent.='<div class="m2"><a href="newclient.php">'.$strings[358].'</a></div>';}
	if (($GLOBALS['SMS_Registr']=='True')&&($GLOBALS['SMS_RegistrOnlyDirectLink']=='0')) {
		$agent.='<div class="m2"><a href="smsregistr.php">'.$strings[359].'</a></div>';}
	if (($GLOBALS['Email_Registr']=='True')&&($GLOBALS['Email_RegistrOnlyDirectLink']=='0')) {
		$agent.='<div class="m2"><a href="emailregistr.php">'.$strings[360].'</a></div>';}
	if (strlen($GLOBALS['WEB_Basement'])>0) {
		$agent.='<div class="head" style="justify-content: center;">'.$GLOBALS['WEB_Basement'].'</div>';}
		
	return $agent;
}

# Смена пароля
function changePass () 
{
	$result = getuserinfo();
	
	$strings=$GLOBALS['strings'];
	
	// 266 - Изменение пароля
	$result['page_title'] = $strings[266];

	if (isset($_POST['go'])) {
		if (($_POST['newpass'] === $_POST['confirm']) && ($GLOBALS['UserCanChangePassword'] == 'True')) {
		
				$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
				mysql_select_db($GLOBALS['mysql_db'], $mysql);
				mysql_query('SET NAMEs utf8mb4;');
			
			$oldpass = mysql_real_escape_string($_POST['oldpass']);
			$newpass = mysql_real_escape_string($_POST['newpass']);
			if ($result['user_pswd'] === $oldpass) {
				
				if (strlen($newpass)>2){
					$uid = uniqid("");
					$usr = mysql_real_escape_string($_SESSION['login']);
					$sql = "INSERT INTO actions VALUES ('SET_PASSWORD','$usr','$newpass','','$uid');";

					mysql_query($sql,$mysql);
				 
					// 267 - Пароль успешно изменен! <br>Данные будут изменены в течение пяти минут.
					$rescode = $strings[267]; 
					MakeActivity($mysql);
					
					} else {
						// 268 - Новый пароль слишком короткий!
						$rescode = $strings[268];
					}
					
				} else {
					// 269 - Введен неверный прежний пароль!
					$rescode = $strings[269];
					}
			} else {
				// 270 - Введенные пароли не совпадают!
				$rescode = $strings[270];
			}
			
		// 266 - Изменение пароля
		// 437 - Форма смены пароля
		$result['out'] = '
					'.menuManager('three',array("./changepass.php",$strings[26])).'
					<div class="content">
						<div class="three">
							<div class="i m3">'.$rescode.'</div>							
						</div>
					</div>
					<script>Refresh("changepass.php")</script>';
		} else {
			// 266 - Изменение пароля
			// 271 - Прежний пароль
			// 272 - Новый пароль
			// 273 - Подтверждение
			// 274 - Внимание! Вместе с паролем изменятся реквизиты для входа в Интернет.
			// 275 - Изменить пароль
			$result['out'] = '
					'.menuManager('three',array("./changepass.php",$strings[26])).'
					<div class="content">
						<div class="three">
							<i class="fas fa-cog"></i>
							<div class="h3 m4">'.$result['page_title'].'</div>
							<div class="i m3">'.$strings[437].':</div>
							<form action="./changepass.php" method="POST">
								<div class="m2">
									<input type="password" name="oldpass" placeholder="'.$strings[271].'"/>
								</div>
								<div class="m2">
									<input type="password" name="newpass" placeholder="'.$strings[272].'"/>
								</div>
								<div class="m2">
									<input type="password" name="confirm" placeholder="'.$strings[273].'"/>
								</div>						
								
								<button type="submit" name="go" class="m3">'.$strings[275].'</button>
								<br><br>
								<div class="m2"><i class="shout fas fa-exclamation-circle"></i>'.$strings[274].'</div>
							</form>	
							
						</div>
					</div>';
			}
	return parse_template($result, './template/default.php',NULL,FALSE,true);
	}
# Деавторизация пользователя
function logout() 
{
	include_once "./getip.php";
	include_once "./config.php";
	
	//$login = mysql_real_escape_string($_SESSION['web_auth_login']);
	$uid = uniqid("");
	
	if (($GLOBALS['UseWebAuth'] == "True") && ($GLOBALS['AutoWebAuth'] == "True")){
		include_once "getip.php";

		$login = mysql_real_escape_string($_SESSION['web_auth_login']);
		$sql = "INSERT INTO actions VALUES ('WEB_LOGOUT','$login','','".mysql_real_escape_string($LocalIP)."','$uid');";
	
		$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
		mysql_select_db($GLOBALS['mysql_db'], $mysql);
		mysql_query($sql,$mysql);
		MakeActivity($mysql);
		
	}
	$Sess=session_id();
	
	//echo 'Free session #2'; exit();
	$_SESSION['web_auth'] = False;
	$_SESSION['web_auth_login'] = "";
	$_SESSION['login'] ='';
	$_SESSION['password'] = '';
	$_SESSION['auth'] = false;
	$_SESSION['shortguid'] = 0;
	$_SESSION['guig'] = '';
	
	session_unset();
	session_destroy();
	setcookie('PHPSESSID','');
	header('location: index.php?freechat='.urlencode($Sess));
	exit();
	}

function restoreAccess($id) 
{

	$strings=$GLOBALS['strings'];
	
	if ($id==1){
		if ($GLOBALS['WEB_Pass_Restore_Email'] !="True") 
		{
			// 276 - Функция восстановления пароля через Email отключена!
			$errors = '<div class="m3 err">'.$strings[276].'</div>';
		}
	} else {
		if ($GLOBALS['WEB_Pass_Restore'] !="True") 
		{
			// 277 - Функция восстановления пароля через SMS отключена!
			$errors = '<div class="m3 err">'.$strings[277].'</div>';
		} else{
			if (isset($_REQUEST['phone'])){
				if (strlen($_REQUEST['phone'])<9) {
					// 278 - Введён неверный номер телефона!
					$errors = '<div class="m3 err">'.$strings[278].'</div>';
				}
			}
		}
	}
	
	
	if (empty($errors))
	{
		//if (( (isset($_REQUEST['phone']) || isset($_REQUEST['email'])) && (strlen($_REQUEST['email'])>0) || (strlen($_REQUEST['phone'])>0) ) && ((isset($_REQUEST['client'])) ||  $GLOBALS['SMS_RestoreWithoutLogin'] == 'True'))
		if	(	( (isset($_REQUEST['phone']) && strlen($_REQUEST['phone'])>0) || (isset($_REQUEST['email']) && strlen($_REQUEST['email'])>0)) &&	(isset($_REQUEST['client']) || $GLOBALS['SMS_RestoreWithoutLogin'] == 'True'))
		{
			
			if (!isset($_REQUEST['client'])){$_REQUEST['client']='';}
			if (!isset($_REQUEST['phone'])){$_REQUEST['phone']='';}
			if (!isset($_REQUEST['email'])){$_REQUEST['email']='';}
			
			$l=mysql_real_escape_string($_REQUEST['client']);
			$t=mysql_real_escape_string($_REQUEST['phone']);
			$e=mysql_real_escape_string($_REQUEST['email']);
			
			$t=trim($t);
			$t=trim($t,'+');
			if (substr($t,0,1)=='8'){$t=substr($t,1);}
			if (substr($t,0,1)=='7'){$t=substr($t,1);}
			
			$t1= '7'.$t;
			$t2= '+7'.$t;
			$t3= '8'.$t;
			
						
			if ($id==1){
				$sql = "select `user_name`, `shortguid` FROM `stat` WHERE `pinfo` like '%||$e||%'";
			} else {$sql = "select `user_name`, `shortguid` FROM `stat` WHERE `otherinfo` like '%$t%'";}
			
			
			if ($GLOBALS['SMS_RestoreWithoutLogin'] == 'False'){$sql.=" and `user_name`='$l'";}
		
					
				$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
				mysql_select_db($GLOBALS['mysql_db'], $mysql);
				mysql_query('SET NAMEs utf8mb4;');
				$mysqlResult = mysql_query($sql,$mysql) or die('qf');
				
				$UN = '';
				$GUID='';
				
				if (mysql_num_rows($mysqlResult)>0){
					$row = mysql_fetch_array($mysqlResult);
					$UN = mysql_real_escape_string($row[0]);
					$GUID=mysql_real_escape_string($row[1]);
				}	
				
								
			$sql = "select `value2` FROM `actionslog` WHERE `value2`='$GUID' and `action` = 'RESTORE_ACCESS' and `dateday` > (NOW() - INTERVAL 10 MINUTE)";
			$mysqlResult1 = mysql_query($sql,$mysql) or die('qf');
			
			$sql = "select `value2` FROM `actionslog` WHERE `value2`='$GUID' and `action` = 'RESTORE_ACCESS' and `dateday` > (NOW() - INTERVAL 1 DAY)";
			$mysqlResult2 = mysql_query($sql,$mysql) or die('qf');
			
			if ((mysql_num_rows($mysqlResult1) > 0) or (mysql_num_rows($mysqlResult2) > 3)) {
			
				// 279 - Необходимо подождать перед повторной попыткой восстановления пароля!
				$errors = '<div class="m3 err">'.$strings[279].'</div>';
			
			} else {
			
				if (strlen($UN)>0) 
				{
					if ($id==1){
						// 280 - Ваша заявка на восстановление пароля принята. <br> Пароль будет выслан в Email в течении минуты.
						// 281 - Вернутся на страницу ввода логина и пароля
						$result['out'] = "<div class=\"m3 acc\">".$strings[280]."<br>
						<a href=\"index.php\">".$strings[281]."</a></div>";
						$uid = uniqid();
						$sql = "INSERT INTO `actions` VALUES ('RESTORE_ACCESS_EMAIL','$UN','','','$uid');";
						mysql_query($sql,$GLOBALS ["mysql"]);
				
						$sql = "INSERT INTO `actionslog` VALUES (NOW(),'RESTORE_ACCESS_EMAIL','$UN','$GUID','$uid','');";
						mysql_query($sql,$GLOBALS ["mysql"]);
					} else {
						// 282 - Ваша заявка на восстановление пароля принята. <br> Пароль будет выслан в SMS в течении минуты.
						// 281 - Вернутся на страницу ввода логина и пароля
						$result['out'] = "<div class=\"m3 acc\">".$strings[282]."<br>
						<a href=\"index.php\">".$strings[281]."</a></div>";
						$uid = uniqid();
						$sql = "INSERT INTO `actions` VALUES ('RESTORE_ACCESS','$UN','','','$uid');";
						mysql_query($sql,$GLOBALS ["mysql"]);
				
						$sql = "INSERT INTO `actionslog` VALUES (NOW(),'RESTORE_ACCESS','$UN','$GUID','$uid','');";
						mysql_query($sql,$GLOBALS ["mysql"]);
					}
				
					MakeActivity($mysql);
				} 
				else 
				{
					if ($id==1){
						// 283 - Не найден Email
						$errors= "<div class=\"m3 err\">" . $strings[283];
						// 284 - или логин
						if ($GLOBALS['SMS_RestoreWithoutLogin'] == 'False'){$errors.=" " . $strings[284];}
						$errors.="! </div>";
					} else {
						// 285 - Не найден телефон
						$errors= "<div class=\"m3 err\">" . $strings[285];
						// 284 - или логин
						if ($GLOBALS['SMS_RestoreWithoutLogin'] == 'False'){$errors.=" " . $strings[284];}
						$errors.="! </div>";
					}
				
				}
			}
		}
	}
	
	if (!isset($result['out'])){$result['out'] ="";}
	$result['out'] = (!empty($errors))?$errors:$result['out'];

	$Ret='';
	if ($id==1){
		$Ret=parse_template($result, ($GLOBALS['SMS_RestoreWithoutLogin'] == 'True')?'./template/forgot2_email.php':'./template/forgot_email.php',NULL,FALSE,true);
	} else {$Ret=parse_template($result, ($GLOBALS['SMS_RestoreWithoutLogin'] == 'True')?'./template/forgot2.php':'./template/forgot.php',NULL,FALSE,true);}
	return $Ret;

}

function Email_Registr(){

	$strings=$GLOBALS['strings'];
	
	if ($GLOBALS['Email_Registr'] !="True") 
	{
		// 286 - Функция регистрации абонента по Email отключена!
		$errors = "<div class=\"m3 err\">".$strings[286]."</div>";
	}
	
	for ($i=0;$i<=16;$i++){
		$GLOBALS['strings'][$i] = '';	
	}
	
	
	if ($GLOBALS['Email_RegistrShowOferta']=='0'){
		$GLOBALS['strings'][5] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['Email_Registr_NeedHomeAddress']=='0'){
		$GLOBALS['strings'][13] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['Email_Registr_NeedInstallationAddress']=='0'){
		$GLOBALS['strings'][14] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['Email_Registr_NeedPostAddress']=='0'){
		$GLOBALS['strings'][15] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['Email_Registr_NeedPassportPhoto']=='0'){
		$GLOBALS['strings'][16] = ' style="display:none;" ';
	}
	
	$GLOBALS['strings'][7] = (isset($_REQUEST['tariff']))?($_REQUEST['tariff']):('');
	$GLOBALS['strings'][8] = (isset($_REQUEST['group']))?($_REQUEST['group']):('');

	if (empty($errors))
	{	
		if ((isset($_REQUEST['phone'])) && (isset($_REQUEST['fio'])) && (isset($_REQUEST['passcard'])) && (isset($_REQUEST['email'])))
		{
			if (!isset($_REQUEST['oferta'])){$_REQUEST['oferta']='';}
			
			
			$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
			mysql_select_db($GLOBALS['mysql_db'], $mysql);
			mysql_query('SET NAMEs utf8mb4;');
			
			$data='';
			$t=mysql_real_escape_string($_REQUEST['phone']);
			$fio=mysql_real_escape_string($_REQUEST['fio']);
			$passcard=mysql_real_escape_string($_REQUEST['passcard']);
			$email=mysql_real_escape_string($_REQUEST['email']);
			$oferta=mysql_real_escape_string($_REQUEST['oferta']);
			
			$HomeAddress=mysql_real_escape_string($_REQUEST['HomeAddress']);
			$InstallationAddress=mysql_real_escape_string($_REQUEST['InstallationAddress']);
			$PostAddress=mysql_real_escape_string($_REQUEST['PostAddress']);
			
			$PassportPhoto='';
			$PassportPhotoName='';
			$FileError=false;
			
			if (isset($_FILES['scan'])){
				if ($_FILES['scan']['error']==1){
					$FileError=true;
				} else {
					
					if (isset($_FILES['scan']['tmp_name'])){
						if (file_exists($_FILES['scan']['tmp_name'])){
							$PassportPhotoName=$_FILES['scan']['name'];
							
							$size=@getimagesize($_FILES['scan']['tmp_name']);
							if($size[0]>5 AND $size[1]>5){
							
								$sql = "SELECT `param_value` FROM `workparams` WHERE `param_name`='CRYPTO_KEY_2';";
								$res = mysql_query($sql,$mysql);
								$row = mysql_fetch_array($res);
								$CRYPTO_KEY_2=$row[0];
								
								$FileData=file_get_contents($_FILES['scan']['tmp_name']);
								
								$EncImg=Encrypt($FileData,
												base64_decode($GLOBALS['CRYPTO_KEY_1']),
												base64_decode($CRYPTO_KEY_2));
								
								
								$sql = "INSERT INTO `objects` VALUES(NULL, '".mysql_real_escape_string($PassportPhotoName)."', '".mysql_real_escape_string($EncImg)."',".strlen($FileData).",0)";
								mysql_query($sql,$mysql);
								$PassportPhoto=mysql_insert_id();
							} else {
								$FileError=true;
							}
						}
					}
				}
			}
			
			$URL_Suffix='';
			
			if ((isset($_REQUEST['tariff']))||(isset($_REQUEST['group']))){
				if (isset($_REQUEST['tariff'])){
					$data = $_REQUEST['tariff'];
					$URL_Suffix='?tariff='.$_REQUEST['tariff'];
				}
				$data .= '~~';
				if (isset($_REQUEST['group'])){
					$data .= $_REQUEST['group'];
					if (strlen($URL_Suffix)>0){$URL_Suffix .='&';}else{$URL_Suffix .='?';}
					$URL_Suffix .='group='.$_REQUEST['group'];
				}
			}
			
			//print_r($_REQUEST);
			//echo $URL_Suffix;
			//exit();
			
			
			if (($GLOBALS['Email_RegistrShowOferta']=='1')&&($_REQUEST["oferta"] <> 'on')){
				// 517 - Необходимо принять договор публичной оферты!
				// 289 - Вернутся на страницу регистрации
				$result['out'] = "
						<div class=\"m3 err\">".$strings[517]."<br> 
						<a href='emailregistr.php".$URL_Suffix."'>".$strings[289]."</a></div>";
				
				$GLOBALS['strings'][0] = $_REQUEST['email'];
				$GLOBALS['strings'][1] = $_REQUEST['phone'];
				$GLOBALS['strings'][2] = $_REQUEST['passcard'];
				$GLOBALS['strings'][3] = $_REQUEST['fio'];
				
				$GLOBALS['strings'][9] = (isset($_REQUEST['HomeAddress']))?($_REQUEST['HomeAddress']):('');
				$GLOBALS['strings'][10] = (isset($_REQUEST['InstallationAddress']))?($_REQUEST['InstallationAddress']):('');
				$GLOBALS['strings'][11] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
				//$GLOBALS['strings'][12] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
				
				
				$GLOBALS['strings'][6]='';
				if ($GLOBALS['UseMaskedTel']==1){
					$GLOBALS['strings'][6]='placeholder="'.$GLOBALS['TelPlaceholder'].'" data-slots="'.$GLOBALS['TelInputSlot'].'" data-accept="'.$GLOBALS['TelPattern'].'"';
				}else {
					$GLOBALS['strings'][6]='placeholder="'.$GLOBALS['strings'][334].'"';
				}
				
				//echo $GLOBALS['strings'][1];
				//exit();
			
				return parse_template($result, './template/emailregistr.php',NULL,FALSE,true);
				exit();
			}
				
				
				
				//print_r($GLOBALS['strings']);exit();
				
				
				
				if ((strlen($email)>3) && (strpos($email, '@'))) {
					
					$GoodAction= true;
					
					if ($GLOBALS['Email_RegistrFullData']=='1'){ 
						if ((mb_strlen($fio, 'UTF-8')<10) || ((strlen($passcard))<10) || (strlen($t)<10)) {
							$GoodAction= false;
						}
						if (($GLOBALS['Email_Registr_NeedHomeAddress']=='1')&&(strlen($HomeAddress)<10)){
							$GoodAction= false;
						}
						if (($GLOBALS['Email_Registr_NeedInstallationAddress']=='1')&&(strlen($InstallationAddress)<10)){
							$GoodAction= false;
						}
						if (($GLOBALS['Email_Registr_NeedPostAddress']=='1')&&(strlen($PostAddress)<10)){
							$GoodAction= false;
						}
						if (($GLOBALS['Email_Registr_NeedPassportPhoto']=='1')&&(strlen($PassportPhoto)==0)){
							$GoodAction= false;
						}
					}
					
					if ($FileError){
						$GoodAction= false;
						$strings[515]="Image too large!";
					}
					
					
					if ($GoodAction){
						$t=trim($t);			
						$sql = "select `user_name`, `shortguid` FROM `stat` WHERE `pinfo` like '%$email%'";
						$mysqlResult = mysql_query($sql,$mysql) or die('qf');
						if (mysql_num_rows($mysqlResult)>0) {
							// 287 - Абонент с таким Email уже существует.
							// 289 - Вернутся на страницу регистрации
							// 288 - Восстановить пароль от учётной записи
							$result['out'] = "
							<div class=\"m3 err\">".$strings[287]."<br> 
							<a href=\"emailregistr.php".$URL_Suffix."\">".$strings[289]."</a><br>
							<a href=\"forgot.php\">".$strings[288]."</a></div>";
						} else {
							// 290 - Ваша заявка на регистрацию принята. <br> Реквизиты для входа будут высланы в Email в течении минуты.
							// 281 - Вернутся на страницу ввода логина и пароля
							$result['out'] = "
							<div class=\"m3 acc\">".$strings[290]."<br>
							<a href=\"index.php\">".$strings[281]."</a></div>";
							$uid = uniqid();
							include_once "getip.php";
							$data=mysql_real_escape_string($data);
							$sql = "INSERT INTO actions VALUES ('EMAIL_REGISTR','$t','$fio','$passcard||$email||".mysql_real_escape_string($LocalIP)."||$data||$HomeAddress||$InstallationAddress||$PostAddress||$PassportPhoto','$uid');";
							mysql_query($sql,$mysql);
							MakeActivity($mysql);
							$GLOBALS['strings'][4] = ' style="display:none;" ';
						}
					} else {
							// 515 - Пожалуйста, заполните все доступные поля!
							// 289 - Вернутся на страницу регистрации
							$result['out'] = "
						<div class=\"m3 err\">".$strings[515]."<br> 
						<a href='emailregistr.php".$URL_Suffix."'>".$strings[289]."</a></div>";
						
						$GLOBALS['strings'][0] = $_REQUEST['email'];
						$GLOBALS['strings'][1] = $_REQUEST['phone'];
						$GLOBALS['strings'][2] = $_REQUEST['passcard'];
						$GLOBALS['strings'][3] = $_REQUEST['fio'];
						
						$GLOBALS['strings'][9] = (isset($_REQUEST['HomeAddress']))?($_REQUEST['HomeAddress']):('');
						$GLOBALS['strings'][10] = (isset($_REQUEST['InstallationAddress']))?($_REQUEST['InstallationAddress']):('');
						$GLOBALS['strings'][11] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
					}
			} else {
				
				$GLOBALS['strings'][0] = $_REQUEST['email'];
				$GLOBALS['strings'][1] = $_REQUEST['phone'];
				$GLOBALS['strings'][2] = $_REQUEST['passcard'];
				$GLOBALS['strings'][3] = $_REQUEST['fio'];
				
				$GLOBALS['strings'][9] = (isset($_REQUEST['HomeAddress']))?($_REQUEST['HomeAddress']):('');
				$GLOBALS['strings'][10] = (isset($_REQUEST['InstallationAddress']))?($_REQUEST['InstallationAddress']):('');
				$GLOBALS['strings'][11] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
				
				// 291 - Неправильный email.
				// 289 - Вернутся на страницу регистрации
				$result['out'] = "
						<div class=\"m3 err\">".$strings[291]."<br> 
						<a href='emailregistr.php".$URL_Suffix."'>".$strings[289]."</a></div>";
			}			
		}
	}
	
	if (!isset($result['out'])){$result['out'] ="";}
	$result['out'] = (!empty($errors))?$errors:$result['out'];
	
	$GLOBALS['strings'][6]='';
	if ($GLOBALS['UseMaskedTel']==1){
		$GLOBALS['strings'][6]='placeholder="'.$GLOBALS['TelPlaceholder'].'" data-slots="'.$GLOBALS['TelInputSlot'].'" data-accept="'.$GLOBALS['TelPattern'].'"';
	}else {
		$GLOBALS['strings'][6]='placeholder="'.$GLOBALS['strings'][334].'"';
	}
	
	return parse_template($result, './template/emailregistr.php',NULL,FALSE,true);
}

function SMS_Registr(){

	$strings=$GLOBALS['strings'];
	
	for ($i=0;$i<=16;$i++){
		$GLOBALS['strings'][$i] = '';	
	}
	
	if ($GLOBALS['SMS_RegistrShowOferta']=='0'){
		$GLOBALS['strings'][5] = ' style="display:none;" ';
	}

	if ($GLOBALS['SMS_Registr_NeedHomeAddress']=='0'){
		$GLOBALS['strings'][13] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['SMS_Registr_NeedInstallationAddress']=='0'){
		$GLOBALS['strings'][14] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['SMS_Registr_NeedPostAddress']=='0'){
		$GLOBALS['strings'][15] = ' style="display:none;" ';
	}
	
	if ($GLOBALS['SMS_Registr_NeedPassportPhoto']=='0'){
		$GLOBALS['strings'][16] = ' style="display:none;" ';
	}
	
	
	
	$GLOBALS['strings'][6] = (isset($_REQUEST['tariff']))?($_REQUEST['tariff']):('');
	$GLOBALS['strings'][7] = (isset($_REQUEST['group']))?($_REQUEST['group']):('');
	
	$URL_Suffix='';
	
	if ((isset($_REQUEST['tariff']))||(isset($_REQUEST['group']))){
		if (isset($_REQUEST['tariff'])){
			$data = $_REQUEST['tariff'];
			$URL_Suffix='?tariff='.$_REQUEST['tariff'];
		}
		$data .= '~~';
		if (isset($_REQUEST['group'])){
			$data .= $_REQUEST['group'];
			if (strlen($URL_Suffix)>0){$URL_Suffix .='&';}else{$URL_Suffix .='?';}
			$URL_Suffix .='group='.$_REQUEST['group'];
		}
	}
	
	//print_r($_REQUEST);
	//echo $data;
	//exit();
			
			
	if ($GLOBALS['SMS_Registr'] !="True") 
	{
		// 292 - Функция регистрации абонента по SMS отключена!
		$errors = "<div class=\"m3 err\">".$strings[292]."</div>";
	}

	if (empty($errors))
	{	
		if ((isset($_REQUEST['phone'])) && (isset($_REQUEST['fio'])) && (isset($_REQUEST['passcard'])) && (isset($_REQUEST['email'])))
		{
			$t=mysql_real_escape_string($_REQUEST['phone']);
			$fio=mysql_real_escape_string($_REQUEST['fio']);
			$passcard=mysql_real_escape_string($_REQUEST['passcard']);
			$email=mysql_real_escape_string($_REQUEST['email']);
			$oferta='';
			if (isset($_REQUEST['oferta'])){mysql_real_escape_string($_REQUEST['oferta']);}else{$_REQUEST["oferta"]='';}
			
			$HomeAddress=mysql_real_escape_string($_REQUEST['HomeAddress']);
			$InstallationAddress=mysql_real_escape_string($_REQUEST['InstallationAddress']);
			$PostAddress=mysql_real_escape_string($_REQUEST['PostAddress']);
			
			$PassportPhoto='';
			$PassportPhotoName='';
			$FileError=false;
			
			$mysql = mysql_connect($GLOBALS['mysql_adr'],$GLOBALS['mysql_user'],$GLOBALS['mysql_pass']);
			mysql_select_db($GLOBALS['mysql_db'], $mysql);
			mysql_query('SET NAMEs utf8mb4;');
			
			
			if (isset($_FILES['scan'])){
				if ($_FILES['scan']['error']==1){
					$FileError=true;
				} else {
					
					if (isset($_FILES['scan']['tmp_name'])){
						if (file_exists($_FILES['scan']['tmp_name'])){
							
							$PassportPhotoName=$_FILES['scan']['name'];
							
							$size=@getimagesize($_FILES['scan']['tmp_name']);
							if($size[0]>5 AND $size[1]>5){
							
								$sql = "SELECT `param_value` FROM `workparams` WHERE `param_name`='CRYPTO_KEY_2';";
								$res = mysql_query($sql,$mysql);
								$row = mysql_fetch_array($res);
								$CRYPTO_KEY_2=$row[0];
								
								$FileData=file_get_contents($_FILES['scan']['tmp_name']);
								
								$EncImg=Encrypt($FileData,
												base64_decode($GLOBALS['CRYPTO_KEY_1']),
												base64_decode($CRYPTO_KEY_2));
								
								
								$sql = "INSERT INTO `objects` VALUES(NULL, '".mysql_real_escape_string($PassportPhotoName)."', '".mysql_real_escape_string($EncImg)."',".strlen($FileData).",0)";
								mysql_query($sql,$mysql);
								$PassportPhoto=mysql_insert_id();
							} else {
								$FileError=true;
							}
						}
					}
					
				}
			}
			
			
			if (($GLOBALS['SMS_RegistrShowOferta']=='1')&&($_REQUEST["oferta"] <> 'on')){
				// 517 - Необходимо принять договор публичной оферты!
				// 289 - Вернутся на страницу регистрации
				$result['out'] = "
						<div class=\"m3 err\">".$strings[517]."<br> 
						<a href='smsregistr.php".$URL_Suffix."'>".$strings[289]."</a></div>";
				
				$GLOBALS['strings'][1] = $_REQUEST['email'];
				$GLOBALS['strings'][2] = $_REQUEST['passcard'];
				$GLOBALS['strings'][3] = $_REQUEST['fio'];
				$GLOBALS['SMS_Tel_Prefix'] = $_REQUEST['phone'];
				
				$GLOBALS['SMS_Tel_Prefix'] = $_REQUEST['phone'];
				
				$GLOBALS['strings'][9] = (isset($_REQUEST['HomeAddress']))?($_REQUEST['HomeAddress']):('');
				$GLOBALS['strings'][10] = (isset($_REQUEST['InstallationAddress']))?($_REQUEST['InstallationAddress']):('');
				$GLOBALS['strings'][11] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
				
				$GLOBALS['strings'][0]='';
				if ($GLOBALS['UseMaskedTel']==1){
					$GLOBALS['strings'][0]='placeholder="'.$GLOBALS['TelPlaceholder'].'" data-slots="'.$GLOBALS['TelInputSlot'].'" data-accept="'.$GLOBALS['TelPattern'].'"';
				}else {
					$GLOBALS['strings'][0]='placeholder="'.$GLOBALS['strings'][334].'"';
				}
				
				
				return parse_template($result, './template/smsregistr.php',NULL,FALSE,true);
				exit();
			}
			
			
				$GoodAction= true;
				
				if ($GLOBALS['SMS_RegistrFullData']=='1'){ 
					if ((mb_strlen($fio, 'UTF-8')<10) || ((strlen($passcard))<10) || (strlen($t)<10)) {
						$GoodAction= false;
					}
					if (($GLOBALS['SMS_Registr_NeedHomeAddress']=='1')&&(strlen($HomeAddress)<10)){
						$GoodAction= false;
					}
					if (($GLOBALS['SMS_Registr_NeedInstallationAddress']=='1')&&(strlen($InstallationAddress)<10)){
						$GoodAction= false;
					}
					if (($GLOBALS['SMS_Registr_NeedPostAddress']=='1')&&(strlen($PostAddress)<10)){
						$GoodAction= false;
					}
					if (($GLOBALS['SMS_Registr_NeedPassportPhoto']=='1')&&(strlen($PassportPhoto)==0)){
						$GoodAction= false;
					}
				}
					
				
				if (strlen($t)>=10){
					
					if ($GoodAction){
						$t=trim($t);
						$orig_t=$t;
						
						$t=trim($t,'+');
						if (substr($t,0,1)=='8'){$t=substr($t,1);}
						if (substr($t,0,1)=='7'){$t=substr($t,1);}
					
						$sql = "select `user_name`, `shortguid` FROM `stat` WHERE `otherinfo` like '%$t%'";
						$mysqlResult = mysql_query($sql,$mysql) or die('qf');
						if (mysql_num_rows($mysqlResult)>0) {
							// 293 - Абонент с таким телефоном уже существует.
							// 289 - Вернутся на страницу регистрации
							// 288 - Восстановить пароль от учётной записи
							$result['out'] = "
							<div class=\"m3 err\">".$strings[293]."<br> 
							<a href=\"smsregistr.php".$URL_Suffix."\">".$strings[289]."</a><br>
							<a href=\"forgot.php\">".$strings[288]."</a></div>";
						} else {
							// 294 - Ваша заявка на регистрацию принята. <br> Реквизиты для входа будут высланы в SMS в течении минуты.
							// 281 - Вернутся на страницу ввода логина и пароля
							$result['out'] = "
							<div class=\"m3 acc\">".$strings[294]."<br>
							<a href=\"index.php\">".$strings[281]."</a></div>";
							$uid = uniqid();
							include_once "getip.php";
							$data=mysql_real_escape_string($data);
							$sql = "INSERT INTO actions VALUES ('SMS_REGISTR','$orig_t','$fio','$passcard||$email||".mysql_real_escape_string($LocalIP)."||$data||$HomeAddress||$InstallationAddress||$PostAddress||$PassportPhoto','$uid');";
							
							mysql_query($sql,$mysql);
							MakeActivity($mysql);
							$GLOBALS['strings'][4] = ' style="display:none;" ';
						}
					} else {
							// 515 - Пожалуйста, заполните все доступные поля!
							// 289 - Вернутся на страницу регистрации
							$result['out'] = "
						<div class=\"m3 err\">".$strings[515]."<br> 
						<a href='smsregistr.php".$URL_Suffix."'>".$strings[289]."</a></div>";
						
						$GLOBALS['strings'][1] = $_REQUEST['email'];
						$GLOBALS['strings'][2] = $_REQUEST['passcard'];
						$GLOBALS['strings'][3] = $_REQUEST['fio'];
						$GLOBALS['SMS_Tel_Prefix'] = $_REQUEST['phone'];
						
						$GLOBALS['strings'][9] = (isset($_REQUEST['HomeAddress']))?($_REQUEST['HomeAddress']):('');
						$GLOBALS['strings'][10] = (isset($_REQUEST['InstallationAddress']))?($_REQUEST['InstallationAddress']):('');
						$GLOBALS['strings'][11] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
					}
			} else {
				
				$GLOBALS['strings'][1] = $_REQUEST['email'];
				$GLOBALS['strings'][2] = $_REQUEST['passcard'];
				$GLOBALS['strings'][3] = $_REQUEST['fio'];
				$GLOBALS['SMS_Tel_Prefix'] = $_REQUEST['phone'];
				
				$GLOBALS['strings'][9] = (isset($_REQUEST['HomeAddress']))?($_REQUEST['HomeAddress']):('');
				$GLOBALS['strings'][10] = (isset($_REQUEST['InstallationAddress']))?($_REQUEST['InstallationAddress']):('');
				$GLOBALS['strings'][11] = (isset($_REQUEST['PostAddress']))?($_REQUEST['PostAddress']):('');
				
				// 295 - Неправильный номер телефона.
				// 289 - Вернутся на страницу регистрации
				$result['out'] = "
						<div class=\"m3 err\">".$strings[295]."<br> 
						<a href='smsregistr.php".$URL_Suffix."'>".$strings[289]."</a></div>";
			}			
		}
	}
	
	if (!isset($result['out'])){$result['out'] ="";}
	$result['out'] = (!empty($errors))?$errors:$result['out'];
	
	$GLOBALS['strings'][0]='';
	if ($GLOBALS['UseMaskedTel']==1){
		$GLOBALS['strings'][0]='placeholder="'.$GLOBALS['TelPlaceholder'].'" data-slots="'.$GLOBALS['TelInputSlot'].'" data-accept="'.$GLOBALS['TelPattern'].'"';
	}else {
		$GLOBALS['strings'][0]='placeholder="'.$GLOBALS['strings'][334].'"';
	}
	
	return parse_template($result, './template/smsregistr.php',NULL,FALSE,true);

}


function IPAutoLogon(){
	
	if ($GLOBALS['AutoLogonIfIPMatch'] == 'True') {
		
		$LocalIP2='';
		
		if ($GLOBALS['GetIPfromJava'] == 'True') {
			if (!isset($_REQUEST['srcip'])){
				
			?>
				<!doctype html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf8">
							<meta name="viewport" content="width=device-width, initial-scale=1.0">
							
							<meta name="mobile-web-app-capable" content="yes" />
							<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
							<meta name="apple-mobile-web-app-title" content="Личный кабинет">
							<link rel="apple-touch-icon" sizes="128x128" href="./img/logo.png">
							
							<!-- Цвет адресной строки в мобильных браузерах -->
							<meta name="theme-color" content="#8A8A8A">
									
							<!-- Manifest -->
							<link rel="manifest" href="./manifest.json">

							<!-- Для старых браузеров -->
							<link rel="icon" href="./favicon.ico" sizes="any" type="image/x-icon">
							<link rel="apple-touch-icon" sizes="192x192" href="./img/logo_192.png">
							<link rel="apple-touch-startup-image" sizes="512x512" href="./img/logo_512.png">
						</head>
						<body>
							<form action='index.php' method='POST' name='sendform' id='sendform'>
								<input type='hidden' name='srcip' id='srcip' value = ''>
							</form>				
							<script>
								try{
									window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
									var pc = new RTCPeerConnection({iceServers:[]}), noop = function(){};      
									pc.createDataChannel('');
									pc.createOffer(pc.setLocalDescription.bind(pc), noop);
									pc.onicecandidate = function(ice){
										var myIP = /([0-9]{1,3}(\\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate)[1];
										document.getElementById('srcip').value = myIP; 						
										pc.onicecandidate = noop;
										//document.getElementById('sendform').submit()
									};						
								} catch (err) {
									//document.getElementById('sendform').submit()
								} finally {
									if (document.getElementById('srcip').value.length>0){
										document.getElementById('sendform').submit()
									}
								}
							</script>
						</body>
					</html>";
					
					<?php
				//exit();
				
			} else {
				if ((strlen($_REQUEST['srcip'])>6)&&(strlen($_REQUEST['srcip'])<16)){
					$LocalIP2=$_REQUEST['srcip'];
				}
			}
		}
	//($ip3 == $LocalIP2)
		include "getip.php";
		$ip=mysql_real_escape_string($LocalIP);
		$ShortGUID_Archive=0;
		$sql = "SELECT `user_name`, `user_pswd`, `usrip`, `shortguid`, `usrpppip`, `isarchived`, `otherinfo` FROM stat WHERE usrip LIKE '%$ip%' or usrpppip LIKE '%$ip%';";
		
			$result = mysql_query($sql,$GLOBALS ["mysql"]);
			while($row = mysql_fetch_array($result)) { 
			
				$ip2 = $row[2];
				$ip4 = array_merge (explode(";",$ip2),explode(";",$row[4]));
				
				$Login = explode(';',$row[0]);
				$Login = $Login[0];
				
				$Pass = explode(';',$row[1]);
				$Pass = $Pass[0];
				
				
				$Login_Archive='';
				$Password_Archive='';
				//$ShortGUID_Archive=0;  // вместо этой назначена выше
								
				// PHP IP Check
				if ($row[5]==0){
					foreach ($ip4 as $ip3) {
							
						$ip3=trim($ip3);
						if (($ip3 == $ip)&&(strlen($ip3)>6)) {
							
							$otherinfoA = explode('||',$row[6]);
							if ($otherinfoA[160]==1){return false;}
							
							$_SESSION['login'] = $Login;
							$_SESSION['password'] = $Pass;
							
							if (isset($_SESSION['auth'])){
								if (!$_SESSION['auth']){
									$sql = "INSERT INTO `logons` (`user_id`, `user_type`, `actiondate`, `ip`, `state`, `result`) VALUES (".mysql_real_escape_string($row[3]).",0,".(time() + $GLOBALS['TimeOffset']).",'".mysql_real_escape_string($ip)."',1,1);";
									mysql_query($sql,$GLOBALS ["mysql"]);
								}
							} else {
								$sql = "INSERT INTO `logons` (`user_id`, `user_type`, `actiondate`, `ip`, `state`, `result`) VALUES (".mysql_real_escape_string($row[3]).",0,".(time() + $GLOBALS['TimeOffset']).",'".mysql_real_escape_string($ip)."',1,1);";
								mysql_query($sql,$GLOBALS ["mysql"]);
							}
							
							$_SESSION['auth'] = true;
							$_SESSION['shortguid'] = $row[3];
							$_SESSION['guid'] = $row[3];
														
							//header('location: index.php');
							//print_r($_SESSION);
							//echo 1; exit();
							
							if (strlen($GLOBALS['CustomDefWEBpage'])>3){
								$NeedToNavigate=$GLOBALS['CustomDefWEBpage'];
							}else {$NeedToNavigate='index.php';}
							header('location: '.$NeedToNavigate);
							
							return true;
							exit();
						}
						
					}
				} else {
					foreach ($ip4 as $ip3) {
						$ip3=trim($ip3);				
						if (($ip3 == $ip)&&(strlen($ip3)>6)) {
							$Login_Archive=$row[0];
							$Password_Archive=$row[1];
							$ShortGUID_Archive=$row[3];
						}
					}
					
				}	
				
				
				// JavaScript IP Check
				if (strlen($LocalIP2)>6){
					if ($row[5]==0){
						foreach ($ip4 as $ip3) {
								
							$ip3=trim($ip3);
							if (($ip3 == $LocalIP2)&&(strlen($ip3)>6)) {
								
								$otherinfoA = explode('||',$row[6]);
								if ($otherinfoA[160]==1){return false;}
								
								if (isset($_SESSION['auth'])){
									if (!$_SESSION['auth']){
										$sql = "INSERT INTO `logons` (`user_id`, `user_type`, `actiondate`, `ip`, `state`, `result`) VALUES (".mysql_real_escape_string($row[3]).",0,".(time() + $GLOBALS['TimeOffset']).",'".mysql_real_escape_string($LocalIP2)."',1,1);";
										mysql_query($sql,$GLOBALS ["mysql"]);
									}
								} else {
									$sql = "INSERT INTO `logons` (`user_id`, `user_type`, `actiondate`, `ip`, `state`, `result`) VALUES (".mysql_real_escape_string($row[3]).",0,".(time() + $GLOBALS['TimeOffset']).",'".mysql_real_escape_string($LocalIP2)."',1,1);";
									mysql_query($sql,$GLOBALS ["mysql"]);
								}
								
								$_SESSION['login'] = $row[0];
								$_SESSION['password'] = $row[1];
								$_SESSION['auth'] = true;
								$_SESSION['shortguid'] = $row[3];
								$_SESSION['guig'] = $row[3];
								
								//echo 2; exit();
								
								//header('location: index.php');
								
								if (strlen($GLOBALS['CustomDefWEBpage'])>3){
									$NeedToNavigate=$GLOBALS['CustomDefWEBpage'];
								}else {$NeedToNavigate='index.php';}
								header('location: '.$NeedToNavigate);
								
								return true;
								exit();
							}
							
						}
					} else {
						foreach ($ip4 as $ip3) {
							$ip3=trim($ip3);				
							if (($ip3 == $LocalIP2)&&(strlen($ip3)>6)) {
								$Login_Archive=$row[0];
								$Password_Archive=$row[1];
								$ShortGUID_Archive=$row[3];
							}
						}
						
					}					
				}
			}

			if ($ShortGUID_Archive>1000){
				$_SESSION['login'] = $row[0];
				$_SESSION['password'] = $row[1];
				$_SESSION['auth'] = true;
				$_SESSION['shortguid'] = $row[3];
				$_SESSION['guig'] = $row[3];
				header('location: index.php');
				return true;
			}

			
	} else { return false;}
}

function AgentDownload(){
	
	$strings=$GLOBALS['strings'];

	$result['page_title'] = $strings[371];
	
	$result['out'] = parse_template($result, './template/agent.php',NULL,FALSE,true);
	// 371 - Агент авторизации
	
	print parse_template($result, './template/default.php');

}

function PrintWebAuth(){
	$tmp_array=array();
	$result['out'] = parse_template($tmp_array, './template/webauth2.php',NULL,FALSE,true);
	print parse_template($result, './template/webauth.php',NULL,FALSE,true);
}


function remove_emoji($string) {

	// Match Emoticons
	$regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
	$clear_string = preg_replace($regex_emoticons, '', $string);

	// Match Miscellaneous Symbols and Pictographs
	$regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
	$clear_string = preg_replace($regex_symbols, '', $clear_string);

	// Match Transport And Map Symbols
	$regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
	$clear_string = preg_replace($regex_transport, '', $clear_string);

	// Match Miscellaneous Symbols
	$regex_misc = '/[\x{2600}-\x{26FF}]/u';
	$clear_string = preg_replace($regex_misc, '', $clear_string);

	// Match Dingbats
	$regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
	$clear_string = preg_replace($regex_dingbats, '', $clear_string);

	return $clear_string;
}

?>