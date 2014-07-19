<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

define('MONGO_USER', 'u_ma');
define('MONGO_PASS', 'Ma20!3');
define('MONGO_DB',   'monitor_assistant');
define('MONGO_HOST', '10.30.15.234');
define('MONGO_PORT', 27017);
define('MONGO_CS_ALERT_COLLECTION',         'cs_alerts');
define('MONGO_CS_ALERT_HISTORY_COLLECTION', 'cs_alerts_history');
define('MONGO_CENTRAL_ALERT_COLLECTION',    'monitoring_assistant_alerts');
define('MONGO_PRODUCT_MAP_COLLECTION',      'map_product');
define('MONGO_ACK_ALERT_COLLECTION',        'alerts_ack');

#define('WSDL_URL', 'http://localhost:9080/SDKSupportService04/SDKSuportSerivces.svc?wsdl');
define('WSDL_URL', 'http://api.sdk.td.vn/SDKSupportService04/SDKSuportSerivces.svc?wsdl');

// define('SECKEY', 'sdk123');
define('SECKEY', 'V34qG36hWwRxbff2ZeGH');

define('RESPONSE_OK', 1);
define('PERIOD_LENGTH', 60*30);
define('PERIOD_DELTA', 60*3);

define('WS', 'webservice');
define('STD', 'std_class');
define('CLIENT_TYPE', WS);
#define('CLIENT_TYPE', STD);

define('TYPE_DOC', 'param_like_document');
define('UNKNOWN',  'unknown');
define('SRC_FROM_CS', 'CS');

define('PARAM_NAME_TYPE', UNKNOWN);
#define('PARAM_NAME_TYPE', TYPE_DOC);
define('ITSM_INCIDENT_OPENED', 24);
define('SLEEP_TIME', 60);

define('BASE_LOG_PATH', '/var/www/html/sdkhome/monitor/backend/logs/php/');
define('LOG_PATH', BASE_LOG_PATH . 'log_');
define('GETLIST_LOG_PATH', BASE_LOG_PATH . 'log_get_list_');
define('UPDATE_LOG_PATH', BASE_LOG_PATH . 'log_update_');

define('CS_OPEN',     24);
define('CS_RESOLVED', 25);
define('CS_CLOSED',   26);
define('CS_REJECTED', 27);

define('LOG_SEPERATOR', '-----------------------------------------------------------------------------------------------');
define('SHOW', 1);
define('HIDE_BY_CS_NEW_ALERT', 2);
define('HIDE_BY_CS_STOP_ALERT', 0);

ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php
// ---------------------------------------------------------------------------------------------- //
function UpdateWaitingNotifyStatus($oTicket, $oCollection){
	if(!empty($oCollection)){
		$oCursor = $oCollection->update(array('_id' => $oTicket['_id']), array('$set' => array('itsm_status_notified' => 1)));
	}
}
// ---------------------------------------------------------------------------------------------- //
function ListWaitingNotifyStatusTicket($oCollection){
	$arrResult = array();
	if(!empty($oCollection)){
		$oCursor = $oCollection->find(array('itsm_status_notified' => 0));
		foreach ($oCursor as $oDoc) {
			$arrResult[] = $oDoc;
		}
	}
	return $arrResult;
}
// ---------------------------------------------------------------------------------------------- //
function GetTicketByTicketID($strTicketID, $oCollection){
	if(!empty($strTicketID)){
		$oCursor = $oCollection->find(array('ticket_id' => $strTicketID));
		foreach ($oCursor as $oDoc) {
			return $oDoc;
		}
	}
	return null;
}
// ---------------------------------------------------------------------------------------------- //
function GetTicketByFullCondition($oAlertInfo, $oCollection){
	if(!empty($oAlertInfo)){
		$oCursor = $oCollection->find($oAlertInfo);
		foreach ($oCursor as $oDoc) {
			return $oDoc;
		}
	}
	return null;
}
// ---------------------------------------------------------------------------------------------- //
function CalculateCPH(&$oAlertInfo, $oCollectionHistory){
	$nCurrentTimeStamp = $oAlertInfo['created_timestamp'];
	$minTimestamp = $nCurrentTimeStamp-PERIOD_LENGTH-PERIOD_DELTA;
	$maxTimestamp = $nCurrentTimeStamp-PERIOD_LENGTH;

	$oHistoryAlertCursor = $oCollectionHistory->find(
		array(
			'ticket_id'			=> $oAlertInfo['ticket_id'],
			'created_timestamp' => array('$gte' => $minTimestamp, '$lte' => $maxTimestamp)
		)
	);
	$oHistoryAlertCursor->sort(array('created_timestamp' => -1))->limit(1);
	$oHistoryAlertInfo = $oHistoryAlertCursor->getNext();
	if(!empty($oHistoryAlertInfo)){
		$oAlertInfo['case_per_hour'] = $oAlertInfo['num_of_case'] - $oHistoryAlertInfo['num_of_case'];
		$oAlertInfo['history_refer'] = $oHistoryAlertInfo['_id'];
	} else {
		$oAlertInfo['case_per_hour'] = $oAlertInfo['num_of_case'];
		$oAlertInfo['history_refer'] = null;
	}
}
// ---------------------------------------------------------------------------------------------- //
function GetCentralAlertByRawID($strRawAlertID, $oCollectionCentral){
	if(!empty($strTicketID)){
		$oCursor = $oCollectionCentral->find(array('source_id' => $strRawAlertID));
		foreach ($oCursor as $oDoc) {
			return $oDoc;
		}
	}
	return null;
}
// ---------------------------------------------------------------------------------------------- //
function GetProductMap($strClientProduct){
	global $oCollectionProductMap;
	if(!empty($oCollectionProductMap) && !empty($strClientProduct)){
		$arrCondition['src_product'] = new MongoRegex('/^' . $strClientProduct . '$/i');
		$arrCondition['source'] = new MongoRegex('/^' . SRC_FROM_CS . '$/i');
		
		$oCursor = $oCollectionProductMap->find($arrCondition);
		foreach ($oCursor as $oDoc) {
			if(!empty($oDoc['itsm_product']))
				return $oDoc['itsm_product'];
			return $oDoc['src_product'];
		}
	}
	return $strClientProduct;
}
// ---------------------------------------------------------------------------------------------- //
function UpdateCentralAlert($oCSAlertInfo, $oCollectionCentral, $oCollectionAlertACK){
	$arrACK = ListACKOfAlert($oCSAlertInfo['_id'], $oCollectionCentral, $oCollectionAlertACK);
	$arrFindCond = array('source_id' => $oCSAlertInfo['_id'], 'source_from' => SRC_FROM_CS, 'is_show' => 1);
	#j($arrFindCond);
	$oCollectionCentral->update($arrFindCond, array('$set' => array('is_show' => HIDE_BY_CS_NEW_ALERT)), array('multiple' => true));
	
	$strAttachments = '';
	if(!empty($oCSAlertInfo['attachment'])){
		$arrLink = explode(';', $oCSAlertInfo['attachment']);
		foreach($arrLink as $key=>$strLink){
			$strAttachments .= 'Link ' . ($key+1) . ': ' . $strLink . "\n";
		}
	}
	$oAlertNew = array();
	
	$oAlertNew['alert_message'] = $oCSAlertInfo['title'];
	$oAlertNew['attachments']   = @$oCSAlertInfo['attachments'];
	$oAlertNew['description']   = sprintf("Server: %s\nErrorMessage:%s\nDescription:%s\n%s", $oCSAlertInfo['server_name'], $oCSAlertInfo['error_message'], $oCSAlertInfo['description'], $strAttachments);
	$oAlertNew['is_show']       = SHOW;
	$oAlertNew['is_acked']      = 0;
	$oAlertNew['num_of_case']   = $oCSAlertInfo['num_of_case'];
	#$oAlertNew['case_per_hour'] = $oCSAlertInfo['case_per_hour'];
	$oAlertNew['product']       = GetProductMap($oCSAlertInfo['product']);
	$oAlertNew['source_from']   = SRC_FROM_CS;
	$oAlertNew['source_id']     = $oCSAlertInfo['_id'];
	$oAlertNew['title']         = $oCSAlertInfo['title'];
	$oAlertNew['ticket_id']     = $oCSAlertInfo['ticket_id'];
	$oAlertNew['itsm_incident_id']     = $oCSAlertInfo['itsm_id'];
	$oAlertNew['create_date']   = time();
	$oAlertNew['clock']         = time();
	
	if(!empty($oAlertNew['product'])){
		$oAlertNew['alert_message'] = sprintf('[%s] %s', $oAlertNew['product'], $oAlertNew['alert_message']);
	}
	
	if(!empty($oCSAlertInfo['itsm_id'])){
		$oAlertNew['alert_message'] = sprintf('Incident %s - %s', $oCSAlertInfo['itsm_id'], $oAlertNew['alert_message']);
		$oAlertNew['is_acked']      = 1;
		// $oAlertNew['itsm_incident_id'] = $oCSAlertInfo['itsm_id'];
	}
	$oCollectionCentral->insert($oAlertNew);
	if(!empty($oAlertNew['_id'])){
		foreach($arrACK as $oACK){
			$oCollectionAlertACK->insert(array(
				'alert_id'     => $oAlertNew['_id'],
				'msg'          => $oACK['msg'],
				'unixtime'     => $oACK['unixtime'],
				'created_date' => $oACK['created_date'],
				'ack_by'       => $oACK['ack_by'],
				'source_from'  => SRC_FROM_CS,
				'source_id'    => new MongoId($oCSAlertInfo['_id'])
			));
		}
	}
}
// ---------------------------------------------------------------------------------------------- //
function ListACKOfAlert($strSrcAlertID, $oCollectionCentral, $oCollectionAlertACK){
	// Tim alert moi nhat cua ticket
	// Sau do lay list ack cua alert nay copy qua alert vua insert
	// ({source_from:'CS',source_id:'525f67b120bab901698b4568'}).sort({created_date:-1}).limit(1)
	$arrResult = array();
	$oAlertCursor = $oCollectionCentral->find(array('source_from' => SRC_FROM_CS, 'source_id' => $strSrcAlertID))->sort(array('create_date' => -1))->limit(1);
	#j(array('source_from' => SRC_FROM_CS, 'source_id' => $strSrcAlertID));
	foreach($oAlertCursor as $oAlert){
		$oAlertACKCursor = $oCollectionAlertACK->find(array('alert_id' => $oAlert['_id']));
		foreach($oAlertACKCursor as $oACK){
			$arrResult[] = $oACK;
		}
		break;
	}
	// $oAlertACKCollection->update(array('alert_id' => $oOldAlertID), array('alert_id' => $oNewAlertID), array('multiple' => true));
	return $arrResult;
}
// ---------------------------------------------------------------------------------------------- //
function j($value=""){
	if(is_object($value) || is_array($value)){
		$value = json_encode($value) . "\n";
		print_r($value);
	} else {
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}
}
// ---------------------------------------------------------------------------------------------- //
function pd($value=""){
	echo '<pre>';
	print_r($value);
	echo '</pre>';
	exit(0);
}
// ---------------------------------------------------------------------------------------------- //
function vd($value=""){
	echo '<pre>';
	var_dump($value);
	echo '</pre>';
	exit(0);
}
// ---------------------------------------------------------------------------------------------- //
function WriteLog($v, $path=LOG_PATH){
	if(is_object($v) || is_array($v)){
		$v = json_encode($v);
	}
	$path .= date('Ymd');
	#touch($path);
	error_log('--> ' . date('Y-m-d H:i:s') . ' ' . print_r($v, true) . "\n", 3, $path);
}
// ---------------------------------------------------------------------------------------------- //
class CSWSTest{
	public function GetListINC(){
		$data = array(
			"GetListINCResult" => '{
				"ResponseData":{
					"ResponseCode":1,
					"Content1":{
						"ListProcessingINC":[
							{
								"INCCode"			:"INC-00057",
								"INCName"			:"Khiếu kiện giải đấu THDNB 7",
								"ProductCode"		:"123VN",
								"ServerName"		:"Bạch Long",
								"ErrorDescription"	:"Khiếu kiện giải đấu THDNB 7",
								"ErrorAnnoucement"	:"Khiếu kiện giải đấu THDNB 7",
								"NumberOfCase"		:200
							}
						]
					}
				}
			}'
		);
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, "http://10.30.15.9/test/cs/fake_api.php?act=list");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		// grab URL and pass it to the browser
		$strResponse = curl_exec($ch);
		// close cURL resource, and free up system resources
		curl_close($ch);
		$data = json_decode($strResponse);
		#pd($data);
		return ($data);
	}

	public function UpdateStatusINC($arrParam){
		$ch = curl_init();

		// set URL and other appropriate options
		$strURL = sprintf("http://10.30.15.9/test/cs/fake_api.php?act=notify&ticket=%s&status=%s&comment=%s", $arrParam['INCCode'], $arrParam['INCStatusID'], $arrParam['Comment']);
		print_r($strURL);
		curl_setopt($ch, CURLOPT_URL, $strURL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		// grab URL and pass it to the browser
		$strResponse = curl_exec($ch);
		// close cURL resource, and free up system resources
		curl_close($ch);
		$data = json_decode($strResponse);
		#pd($data);
		return ($data);
	}
}
// ---------------------------------------------------------------------------------------------- //
?>