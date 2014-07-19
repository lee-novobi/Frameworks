<?php global $arrDefined; ?>
<?php if($bIsAjax) {?>

<table width="100%" cellspacing="0" cellpadding="0" class="list-zebra" id="tblAlert">
  <thead>
    <tr class="table-title">
      <th colspan="6"><p class="drag">ACKNOWLEDGED ALERT</p></th>
    </tr>
    <tr>
      <th class="t-center wp50">Location</th>
      <th class="t-center wp50">Source</th>
      <!-- <th class="t-center">NumOf<br />Case</th> -->
      <th class="t-center wp180">Hostname</th>
      <th class="t-center wp600">Alert</th>
      <th class="t-center wp50">Priority</th>
      <th class="t-center wp150">Time Alert</th>
      <!-- <th class="t-center">Status</th> --> 
      <!-- <th class="t-center">Contact</th> -->
      <th class="t-center wp130">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php } ?>
    <?php if (count($arrAlerts) > 0) { ?>
    <?php foreach ($arrAlerts as $k => $oAlert) { ?>
    <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
      <td style="padding-left: 15px" class="t-left"> QTSC </td>
      <td style="padding-left: 15px" class="t-left"><?php echo @$oAlert['source_from']; ?></td>
      <!-- <td style="padding-left: 15px" class="t-left">
      	<?php echo @$oAlert['num_of_case']; ?>
      </td> -->
      <td style="padding-left: 15px" class="t-left"><?php echo @$oAlert['zbx_host']; ?></td>
      <?php if (in_array(intval(@$oAlert['zabbix_triggerid']), $arrIgnoredTriggers)|| in_array((string)@$oAlert['_id'], $arrIgnoredTriggers)) { ?>
      <td class="t-left" style="padding-left: 15px; background:#efefef;"><?php } else { ?>
      <td style="padding-left: 15px" 
	  class="<?php if(isset($oAlert['priority'])) 
						{
							if(strtolower($oAlert['priority']) == "critical") { echo "t-left bg-red cl-white"; }
							elseif(strtolower($oAlert['priority']) == "high") { echo "t-left bg-pink cl-white"; } 
							elseif(strtolower($oAlert['priority']) == "medium" || strtolower($oAlert['priority']) == "low") { echo "t-left"; }
							else { echo "t-left bg-red cl-white"; }	
						}
					else { echo "t-left bg-red cl-white"; } ?>"><?php } ?>
		<?php $strZbxHostId = !empty($oAlert['zabbix_hostid']) ? $oAlert['zabbix_hostid'] : 0;
			  $strZbxZabbixServerId = !empty($oAlert['zbx_zabbix_server_id']) ? $oAlert['zbx_zabbix_server_id'] : 0; ?>
		<a title="View host detail" class="<?php if(isset($oAlert['priority'])) 
						{
							if(strtolower($oAlert['priority']) == "critical") { echo "cl-white"; }
							elseif(strtolower($oAlert['priority']) == "high") { echo "cl-white"; } 
							elseif(strtolower($oAlert['priority']) == "medium" || strtolower($oAlert['priority']) == "low") { echo "t-left"; }
							else { echo "t-left cl-white"; }	
						}
					else { echo "t-left cl-white"; } ?>" target="_blank" href="<?php echo ODA_HOST_DETAIL_URL . $strZbxHostId . "/". $strZbxZabbixServerId ?>">
        <?php if(in_array(strtolower(@$oAlert['source_from']), $arrDefined['source_allow_show_numof_case'])) { ?>
        <span class="numof-case"><?php echo @$oAlert['num_of_case']; ?></span>
        <?php } ?>
        <?php echo htmlentities($oAlert['alert_message'], ENT_QUOTES, "UTF-8"); ?>
        </a>
       </td>
      <td class="<?php if(isset($oAlert['priority'])) 
						{
							if(strtolower($oAlert['priority']) == "critical") { echo "t-left bg-red cl-white"; }
							elseif(strtolower($oAlert['priority']) == "high") { echo "t-left bg-pink cl-white"; } 
							elseif(strtolower($oAlert['priority']) == "medium" || strtolower($oAlert['priority']) == "low") { echo "t-left"; }	
						} ?>"><?php if(isset($oAlert['priority'])) echo $oAlert['priority']; ?></td>
      <td class="t-center"><?php if(!isset($oAlert['clock'])) echo date('Y-m-d H:i:s', $oAlert['create_date']); else echo date('Y-m-d H:i:s', $oAlert['clock']); ?></td>
      <td class="t-center" style="width:100px"><?php $this->tpl->load_anchor_icon( array('img'=> ICON_IMG_ACK, 'onclick'=>'PopUpACK(\''.@$oAlert['_id'].'\')') ); ?>
        <?php if(!isset($oAlert['itsm_incident_id']) || $oAlert['itsm_incident_id'] == ""){ ?>
        <?php $this->tpl->load_anchor_icon(array('img' => ICON_IMG_INC, 'onclick'=>'PopUpCreateIncident(\''.@$oAlert['_id'].'\')') ); ?>
        <?php } 
		else { ?>
       <!-- <a title="Create Incident" class="contact-link sms" href="#"><img src="<?php echo $base_url?>asset/images/icons/icon_ack_off.png" width="15px"/></a> -->
        <?php $this->tpl->load_anchor_icon(array('img' => ICON_IMG_INC, 'class'=> 'btn btn-disable') ); ?>
        <?php } ?>
        <?php if(@$oAlert['zbx_host'] != ''){ ?>
        <?php 
			$strParameter = "'". @$oAlert['source_from']. "','" . @$oAlert['department'] . "','" . urlencode(@$oAlert['product']) . "','" . @$oAlert['zbx_zabbix_server_id']. "','". @$oAlert['zabbix_hostid']. "','". @$oAlert['zbx_host'] ."','".(string)@$oAlert['_id'] . "', '" . urlencode($oAlert['alert_message']) . "', '";
			$strParameter .= (isset($oAlert['clock']) ? $oAlert['clock'] : $oAlert['create_date']) . "'";
			$arrDataCall = array('img'=> ICON_IMG_CALL, 'onclick'=>"PopContactPoint($strParameter)");
			if(!empty($oAlert['noof_call_success']) || !empty($oAlert['noof_call_fail'])) {
				$arrDataCall['from_alert'] = 1; 
				$arrDataCall['display_call_num'] = 1;
				$arrDataCall['noof_success'] = !empty($oAlert['noof_call_success']) ? $oAlert['noof_call_success'] : 0;
				$arrDataCall['noof_fail'] = !empty($oAlert['noof_call_fail']) ? $oAlert['noof_call_fail'] : 0;
				$arrDataCall['noof_call'] = $arrDataCall['noof_success'] + $arrDataCall['noof_fail'];
				$arrDataCall['alert_history'] = 'ViewActionHistory4Alert(\''. @$oAlert['source_from']. '\',\'' . @$oAlert['zbx_host'] . '\',\'' . @$oAlert['_id'].'\',\''. urlencode($oAlert['alert_message']) . '\','. (isset($oAlert['clock']) ? $oAlert['clock'] : $oAlert['create_date']). ')';
			}
			
			$this->tpl->load_anchor_icon($arrDataCall); ?>
        <?php } else { ?>
        <span class="btn btn-disable"> <img width="24px" src="<?php echo $base_url ?>asset/images/metro/dark/appbar.phone.png"> </span>
        <?php } ?></td>
    </tr>
    <?php } ?>
    <?php } ?>
    <?php if($bIsAjax) {?>
  </tbody>
</table>
<?php } ?>
