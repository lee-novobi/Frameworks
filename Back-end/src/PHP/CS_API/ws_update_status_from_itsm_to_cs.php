<?php
require_once('ws_common.php');
$arrStatusMap = array(
	'open'		=> 24,
	'resolved'	=> 25,
	'closed'	=> 26,
	'rejected'	=> 27
);

while(true){
	if(CLIENT_TYPE == WS){
		$client = new SoapClient(WSDL_URL,array("trace"=> 1,"exceptions" => 1,"cache_wsdl" => 0,"soap_version" => SOAP_1_1));
	} else {
		$client = new CSWSTest();
	}
	# vd($client);
	// Select alert with status has just been updated
	$m = new Mongo(sprintf('mongodb://%s:%s@%s:%s/%s', MONGO_USER, MONGO_PASS, MONGO_HOST, MONGO_PORT, MONGO_DB));
	$db = $m->selectDB(MONGO_DB);
	$oCollection = new MongoCollection($db, MONGO_CS_ALERT_COLLECTION);
	$arrTicket = ListWaitingNotifyStatusTicket($oCollection);
	foreach($arrTicket as $oTicket){
		$nCSStatus = 99;
		if(isset($arrStatusMap[strtolower($oTicket['itsm_status'])])){
			$nCSStatus = $arrStatusMap[strtolower($oTicket['itsm_status'])];
		}
		if($nCSStatus == CS_CLOSED){
			$strClosedDate = date('Y-m-d H:i:s');
		} else {
			$strClosedDate = "";
		}
		$params = array(
					"sigkey" 		=> SECKEY,
					"INCCode" 		=> $oTicket['ticket_id'],
					'INCStatusID'	=> $nCSStatus,
					'CreatedBy'		=> "sdk",
					'Comment'		=> (isset($oTicket['msg'])&&!empty($oTicket['msg']))?$oTicket['msg']:"",
					'ITSMCloseDate'	=> $strClosedDate,
					'ITSMCode'		=> $oTicket['itsm_id']);
		WriteLog(json_encode($params), UPDATE_LOG_PATH);
		$oRs = $client->UpdateStatusINC($params);
		WriteLog($oRs, UPDATE_LOG_PATH);
		if(is_object($oRs)){
			$oRs = (array)$oRs;
			if(isset($oRs['UpdateStatusINCResult'])){
				$oRsDetail = json_decode($oRs['UpdateStatusINCResult']);
				$oRsDetail = (array)$oRsDetail->ResponseData;
				#vd($oRsDetail);
				if($oRsDetail[" ResponseCode"] == 1){
					UpdateWaitingNotifyStatus($oTicket, $oCollection);
					WriteLog("UpdateWaitingNotifyStatus OK", UPDATE_LOG_PATH);
				}
			}
		}
		WriteLog(LOG_SEPERATOR, UPDATE_LOG_PATH);
	}
	sleep(SLEEP_TIME);
}
?>