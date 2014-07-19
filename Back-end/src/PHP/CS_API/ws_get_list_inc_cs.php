<?php
#require_once('../lib/nusoap.php');
require_once('ws_common.php');
$oCollectionProductMap = null;

while(true){
	echo "Start GetListINC\n";
	if(CLIENT_TYPE == WS){
		$client = new SoapClient(WSDL_URL,array("trace"=> 1,"exceptions" => 1,"cache_wsdl" => 0,"soap_version" => SOAP_1_1));
	} else {
		$client = new CSWSTest();
	}
	$oResult = null;
	try {
		$oRawResult = $client->GetListINC(array("sigkey" => SECKEY));
		WriteLog($oRawResult, GETLIST_LOG_PATH);
		#vd($oRawResult);
		if(is_object($oRawResult)){
			$oRawResult = (array)$oRawResult;
		}

		if(isset($oRawResult['GetListINCResult'])){
			$oResult = json_decode($oRawResult['GetListINCResult']);
			#vd($oResult->ResponseData->Content1->ListProcessingINC);
			if($oResult->ResponseData->ResponseCode == RESPONSE_OK){
				$arrProcessingInc = array();
				@$arrProcessingInc1 = $oResult->ResponseData->Content1->ListProcessingINC;
				@$arrProcessingInc2 = $oResult->ResponseData->Content2->ListClosingINC;
				
				if(is_array($arrProcessingInc1)) $arrProcessingInc = $arrProcessingInc1;
				if(is_array($arrProcessingInc2)) $arrProcessingInc = array_merge($arrProcessingInc, $arrProcessingInc2);

				if(is_array($arrProcessingInc)){
					$arrProcessingTicketID = array();
					$arrProcessingAlertID  = array();

					$m                     = new Mongo(sprintf('mongodb://%s:%s@%s:%s/%s', MONGO_USER, MONGO_PASS, MONGO_HOST, MONGO_PORT, MONGO_DB));
					$db                    = $m->selectDB(MONGO_DB);
					$oCollection           = new MongoCollection($db, MONGO_CS_ALERT_COLLECTION);
					$oCollectionHistory    = new MongoCollection($db, MONGO_CS_ALERT_HISTORY_COLLECTION);
					$oCollectionCentral    = new MongoCollection($db, MONGO_CENTRAL_ALERT_COLLECTION);
					$oCollectionProductMap = new MongoCollection($db, MONGO_PRODUCT_MAP_COLLECTION);
					$oCollectionAlertACK   = new MongoCollection($db, MONGO_ACK_ALERT_COLLECTION);
					
					if(!empty($oCollection)){
						foreach($arrProcessingInc as $oInc){
							if(PARAM_NAME_TYPE == TYPE_DOC){
								$strIncidentCode = $oInc->INCCode;
							} else {
								$strIncidentCode = $oInc->IncidentCode;
							}

							if(!empty($strIncidentCode)){
								$arrProcessingTicketID[] = $strIncidentCode;
								if(PARAM_NAME_TYPE == TYPE_DOC){
									$oCSAlertInfo = array(
										'ticket_id'     => $oInc->INCCode,
										'title'         => $oInc->INCName,
										'product'       => $oInc->ProductCode,
										'server_name'   => $oInc->ServerName,
										'description'   => $oInc->ErrorDescription,
										'error_message'	=> $oInc->ErrorAnnoucement,
										'num_of_case'   => (int)$oInc->NumberOfCase,
									);
								} else {
									$oCSAlertInfo = array(
										'ticket_id'     => $oInc->IncidentCode,
										'title'         => $oInc->IncidentName,
										'product'       => $oInc->ProductCode,
										'server_name'   => $oInc->Server,
										'description'   => $oInc->Description,
										'error_message' => $oInc->InfoErrorToSDK,
										'num_of_case'   => (int)$oInc->NumberOfCase,
										'attachment'    => $oInc->ImageLinks,
										'cs_status'     => $oInc->INCStatus,
									);
								}

								$oCSAlertInfoHistory                      = $oCSAlertInfo;
								$oCSAlertInfoHistory['created_date']      = date('Y-m-d H:i:s');
								$oCSAlertInfoHistory['created_timestamp'] = time();
								CalculateCPH($oCSAlertInfoHistory, $oCollectionHistory);
								$oCollectionHistory->insert($oCSAlertInfoHistory);
								$oCSAlertInfoNew                          = $oCSAlertInfo;
								$oCSAlertInfoNew['case_per_hour']         = $oCSAlertInfoHistory['case_per_hour'];
								$oCSAlertInfoNew['history_refer']         = $oCSAlertInfoHistory['history_refer'];
								$oCSAlertInfoNew['deleted']               = 0;
								$oCSAlertInfoNew['itsm_case']             = 0;
								// $oCSAlertInfo['case_per_hour']          = $oCSAlertInfoHistory['case_per_hour'];
								unset($oCSAlertInfoHistory);
								
								// Tim trong raw alert xem ticket nay da tung duoc ghi nhan chua
								// Neu da tung tung ghi nhan thi so sanh voi data hien tai xem co thay doi gi khong
								// - Neu co thay doi:
								//   + Update lai raw data moi
								//   + Qua bang centralize alert
								//     * Neu dang alert thi tat di. Sau do insert vao lai alert moi voi thong tin moi
								//     * Neu da tung alert (alert da switch off):
								//       @ Neu chua mo incident thi insert vao lai alert moi voi thong tin moi
								//       @ Neu da mo incident thi insert vao lai alert moi voi thong tin moi + thong tin incident da mo
								if(PARAM_NAME_TYPE == TYPE_DOC){
									#pd(PARAM_NAME_TYPE);
									$oTicket = GetTicketByTicketID($oInc->INCCode, $oCollection);
								} else {
									$oTicket = GetTicketByTicketID($oInc->IncidentCode, $oCollection);
								}
								if(!empty($oTicket)){
									$arrProcessingAlertID[]                  = (string)$oTicket['_id'];
									$oCSAlertInfo['itsm_id']                 = @$oTicket['itsm_id'];
									$oCSAlertInfoNew['itsm_id']              = @$oTicket['itsm_id'];
									$oCSAlertInfoNew['itsm_status']          = @$oTicket['itsm_status'];
									$oCSAlertInfoNew['itsm_status_notified'] = @$oTicket['itsm_status_notified'];
									// Xu ly bang raw alert
									$oSameTicket = GetTicketByFullCondition(
													array(
														'ticket_id'     => $oCSAlertInfo['ticket_id'],
														'title'         => $oCSAlertInfo['title'],
														'product'       => $oCSAlertInfo['product'],
														'server_name'   => $oCSAlertInfo['server_name'],
														'error_message' => $oCSAlertInfo['error_message'],
														'description'   => $oCSAlertInfo['description'],
														'num_of_case'   => $oCSAlertInfo['num_of_case'],
														'attachment'    => $oCSAlertInfo['attachment'],
													), $oCollection
									);
									if(empty($oSameTicket) || $oSameTicket['deleted'] == 1){
										if($oTicket['num_of_case'] != $oCSAlertInfo['num_of_case']){
											$oCSAlertInfoNew['itsm_case'] = 0;
										}
										$oCollection->update(array('_id' => $oTicket['_id']), array('$set' => $oCSAlertInfoNew));
										// Update bang central alert
										$oCSAlertInfo['_id'] = (string)$oTicket['_id'];
										UpdateCentralAlert($oCSAlertInfo, $oCollectionCentral, $oCollectionAlertACK);
									}
								} else {
									$oCollection->insert($oCSAlertInfoNew);
									$oCSAlertInfo['_id'] = (string)$oCSAlertInfoNew['_id'];
									$arrProcessingAlertID[] = (string)$oCSAlertInfoNew['_id'];
									UpdateCentralAlert($oCSAlertInfo, $oCollectionCentral, $oCollectionAlertACK);
								}
							}
						}
						$oCollection->update(
								array('ticket_id' => array('$nin' => $arrProcessingTicketID)),
								array('$set' => array('deleted' => 1, 'deleted_date' => date('Y-m-d H:i:s'))),
								array('multiple' => true)
						);
						$oCollectionCentral->update(
								array('source_from' => SRC_FROM_CS, 'source_id' => array('$nin' => $arrProcessingAlertID), 'is_show' => 1),
								array('$set' => array('is_show' => HIDE_BY_CS_STOP_ALERT)),
								array('multiple' => true)
						);
						$m->close();
					} else {
						$strMess = "Mongo connection problem.";
						WriteLog($strMess, GETLIST_LOG_PATH);
					}
				} else {
					$strMess = "List Processing Incident is not Array";
					WriteLog($strMess, GETLIST_LOG_PATH);
				}
			} else {
				$strMess = "Response FAIL";
				WriteLog($strMess, GETLIST_LOG_PATH);
			}
		} else {
			$strMess = "Request FAIL";
			WriteLog($strMess, GETLIST_LOG_PATH);
		}
	} catch (Exception $e) {
		$strMess = 'Caught exception: ' . $e->getMessage();
		WriteLog($strMess, GETLIST_LOG_PATH);
	}
	WriteLog(LOG_SEPERATOR, GETLIST_LOG_PATH);
	sleep(SLEEP_TIME);
}

exit();
?>
