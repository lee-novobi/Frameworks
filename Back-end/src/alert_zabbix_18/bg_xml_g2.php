<?php
    require_once ("init.php");
    require_once "include/f_index.php";       
    require_once (PATH_CORE."class.xmlparse.php");
    require_once (PATH_CORE."class.process.php");
    require_once (PATH_CORE."HttpClient.class.php");
    
    /* Init class */
    $oCXML      = new CXMLParse();
    $oCProcess  = new CProcess();
    
    /* Get content file */
    try{                
        $sData = HttpClient::quickPost(LINK_FILE, "");   
        $sData = str_replace("Content-type: text/plain","",$sData);
        $sData = trim($sData);
		echo $sData;
        if($sData==""){
            return;
        }
        $aData = $oCXML->_xml2array($sData,1,"");        
        $a_host_status = $oCProcess->_process_host_status($aData);
        $a_host_status_service = $oCProcess->_process_host_status_service($aData);
        $oCProcess->_updateAll2Hide();
        /* Progress for host status */
        foreach($a_host_status as $v_status){
            $curr_state = $v_status['current_state'];
            if(strtolower(trim($v_status['service_description'])) == "checkping_latency"){
                continue;
            }
            if( $curr_state == STATUS_WARNING || $curr_state == STATUS_CRITICAL){
                $isExisted = $oCProcess->_isExistedInDB($v_status);
                if($isExisted == EXISTED){
                    $oCProcess->_update2DB($v_status);
                }else if($isExisted == NOT_EXISTED){
                    $oCProcess->_insert2DB($v_status);
                }
            }
        }
        /* end */
        /* Progress for service status */
        foreach($a_host_status_service as $v_status_service){
            $curr_state = $v_status_service['current_state'];
            if(strtolower(trim($v_status_service['service_description'])) == "checkping_latency"){
                continue;
            }
            if($curr_state == STATUS_WARNING || $curr_state == STATUS_CRITICAL){ 
                $isExisted = $oCProcess->_isExistedInDB($v_status_service);
                if($isExisted == EXISTED){
                    $oCProcess->_update2DB($v_status_service);
                }else if($isExisted == NOT_EXISTED){
                    $oCProcess->_insert2DB($v_status_service);
                }
            }          
        }
        /* end */
    }catch(exception $ex){                 
        die("We have error!!!");
    }  
?>