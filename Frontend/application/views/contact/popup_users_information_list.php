<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.css" />

<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_v2.js"></script>
<?php global $arrDefined;?>
<div style="background-color: light-grey">
	<table id="tblSearchByUserResult" width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
		<thead>
			<tr class="table-title">
                <th class="t-center">Name</th>
                <th class="t-center">Department</th>
                <th class="t-center">Product</th>
                <th class="t-center">Role</th>
                <th class="t-left">Email</th>
                <th class="t-center">Mobile</th>
                <th class="t-center">Ext</th>
                <th class="t-center">Action</th>
            </tr>
		</thead>
		<tbody>
			<?php if(!empty($arrContacts)) {?>
			<?php foreach ($arrContacts as $index => $objOneUser) { ?>
			<tr <?php if($index % 2 == 0) { ?>class="even"<?php } ?>class="odd">
				<td class="t-left"><?php echo htmlentities($objOneUser['full_name'], ENT_QUOTES, 'UTF-8');?></td>
				<td class="t-left"><?php echo htmlentities($objOneUser['sdk_dept']);?></td>
				<td class="t-left"><?php echo htmlentities($objOneUser['product'], ENT_QUOTES, 'UTF-8');?></td>
				<td class="t-center"><?php if(!empty($objOneUser['role'])) { echo htmlentities($objOneUser['role']); } else { echo NO_ROLE; }?></td>
				<td class="t-left"><?php echo htmlentities($objOneUser['email']);?></td>
				<td class="t-center"><?php echo htmlentities($objOneUser['mobile']);?></td>
				<td class="t-center"><?php echo htmlentities($objOneUser['ext']);?></td>
				<td class="t-center" title="<?php if(is_null($objOneUser['vng_dept'])) { echo NOT_HR; } elseif ($objOneUser['vng_dept'] != $objOneUser['sdk_dept'] && !in_array($objOneUser['vng_dept'], $arrDefined['department_special'])) {
          echo USER_SDK . htmlentities($objOneUser['sdk_dept']) . USER_HR . htmlentities($objOneUser['vng_dept']) . ')'; 
     } ?>" style="<?php if(is_null($objOneUser['vng_dept'])) { ?>vertical-align: middle; background-color: #FF7575<?php } elseif ($objOneUser['vng_dept'] != $objOneUser['sdk_dept'] && !in_array($objOneUser['vng_dept'], $arrDefined['department_special'])) { ?>
          vertical-align: middle;background-color: #FFFF94
     <?php } ?>">
					<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'id' => 'list_mobile_user_' . htmlentities($objOneUser['userid']), 'onclick' => ((isset($iCallFrom)) ? 'list_mobile_user(' : 'ChooseCauseToLinkAction(1, ') . htmlentities($objOneUser['userid']) . ')')); ?>
	    			<?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.message.smiley.png', 'class' => 'btn btn-bluesky', 'title' => 'Click to send sms', 'id' => 'send_sms_' . htmlentities($objOneUser['userid']), 'onclick' => ((isset($iCallFrom)) ? 'send_message(' : 'ChooseCauseToLinkAction(2, ') . htmlentities($objOneUser['userid']) . ')')); ?>
				</td>
			</tr>
			<?php } /* end foreach */?>
			<?php } else {?>
			<tr>
				<td colspan="8">No results were found corresponding to your search keywords.</td>
			</tr>
			<?php } /* end else */?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var alert_id = '<?php echo @$strAlertId ?>';
	var inc_id = '<?php echo @$strIncidentId ?>';
	var alert_mesg = '<?php echo @$strAlertMsg ?>';
	var time_alert = '<?php echo @$strTimeAlert ?>';
	var change_id = '<?php echo @$strChangeId ?>';
	
	function send_message(userid) {
        var url = base_url + 'contact/contact/load_popup_sms?userid=' + userid + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_mesg) + '&time_alert=' + time_alert + '&sms_identifier=1' + '&change_id=' + change_id;
        CreateFancyBox('a#send_sms_' + userid, url, '70%');
    }

    function list_mobile_user(userid) {
        var url = base_url + 'contact/contact/load_popup_list_mobile_user?userid=' + userid + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_mesg) + '&time_alert=' + time_alert + '&change_id=' + change_id;
        CreateFancyBox('a#list_mobile_user_' + userid, url, '70%', 400);
    } 
    
    function ChooseCauseToLinkAction(action, userid) {
    	var url = base_url + 'contact/contact/load_cause_link?user_id=' + userid + '&action=' + action;
    	if(action == 1) { //call
    		CreateFancyBox('a#list_mobile_user_' + userid, url, '80%', 300);	
    	} else if (action == 2) { //send sms
    		CreateFancyBox('a#send_sms_' + userid, url, '80%', 300);
    	}
    	
    }
</script>