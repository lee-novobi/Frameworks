<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.css" />

<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_v2.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_no_selector.js"></script>
<?php global $arrDefined;?>
<div style="background-color: lightgrey; margin: 5px;">
	<?php if(!empty($arrSearchUsersResult)) { ?>
	<?php foreach ($arrSearchUsersResult as $key => $arrResult) { ?>
	<table id="tblSearchByUserResult_<?php echo $key;?>" width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
		<thead>
			<tr class="table-title">
                <th class="t-left" style="border-right: 1px solid #ddd;" width="30%"><?php echo htmlentities($arrResult[0]['full_name'], ENT_QUOTES, 'UTF-8');?></th>
                <th class="t-center" style="border-right: 1px solid #ddd;" width="40%"><?php echo htmlentities($arrResult[0]['mobile']); ?></th>
                <th class="t-center" style="border-right: 1px solid #ddd;" width="20%"><?php echo htmlentities($arrResult[0]['email']);?></th>
                <th class="t-center" style="border-right: 1px solid #ddd;">
                	<?php $this->tpl->load_anchor_icon(array('id'=> 'view_history_' . $key, 'img'=>ICON_IMG_HISTORY, 'title' => 'View action history', 'onclick'=>'ViewActionHistory('. $key . ', \'' . SDK . '\')')); ?>
                	<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'id' => 'list_mobile_user_' . $key, 'onclick' => (!isset($strIdentifier) ? 'list_mobile_user(' : 'ChooseCauseToLinkAction(1, ') . $key . ', \'' . SDK . '\')')); ?>
	    			<?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.message.smiley.png', 'class' => 'btn btn-bluesky', 'title' => 'Click to send sms', 'id' => 'send_sms_' . $key, 'onclick' => (!isset($strIdentifier) ? 'send_message(': 'ChooseCauseToLinkAction(2, ') . $key . ', \'' . SDK . '\')')); ?>
	    		</th>
	    			
            </tr>
            <tr>
            	<th>Department</th>
            	<th>Product</th>
            	<th colspan="2">Role</th>
            </tr>
		</thead>
		<tbody>
			<?php foreach($arrResult as $idx=>$oneUser) {?>
			<tr>
				<td><?php echo htmlentities($oneUser['sdk_dept']);?></td>
				<td><?php echo htmlentities($oneUser['product'], ENT_QUOTES, 'UTF-8');?></td>
				<td colspan="2"	><?php echo htmlentities($oneUser['role']);?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table><br />
	<?php } /* end foreach */?>
	<?php } /* end if */ ?><br />
	<?php if(!empty($arrVNGStaffs)) { ?>
	<table id="tblListUsersVNG" width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
		<thead>
			<tr class="table-title">
				<th colspan="6">
					<p class="drag">USERS INFORMATION FROM VNG-HR</p>
				</th>
			</tr>
			<tr>
				<th>Fullname</th>
				<th>Mobile</th>
				<th>Email</th>
				<th class="t-center">Department</th>
				<th class="t-center">Title</th>
				<th class="t-center w15">Action</th>
			</tr>
		</thead>
		<tbody id="tbodytblListUsersVNG">
			<?php foreach ($arrVNGStaffs as $index => $oOneStaff) { ?>
			<tr no="<?php echo $oOneStaff->id;?>">
                <td class="t-left"><?php echo htmlspecialchars($oOneStaff->fullname, ENT_QUOTES, 'UTF-8')?></td>
                <td class="t-left"><?php $oOneStaff->cellphone = trim($oOneStaff->cellphone); if(!empty($oOneStaff->cellphone) && $oOneStaff->cellphone != 'NULL') { echo htmlspecialchars(preg_replace('/\s+/', '',$oOneStaff->cellphone)); } else { echo NO_PHONE; } ?></td>
                <td class="t-left"><?php $oOneStaff->email = trim($oOneStaff->email); if(!empty($oOneStaff->email)) { echo htmlspecialchars(strtolower($oOneStaff->email)); } else { echo NO_EMAIL; }?></td>
                <td class="t-left"><?php echo htmlspecialchars($oOneStaff->department);?></td>
                <td class="t-left"><?php echo htmlspecialchars($oOneStaff->title);?></td>
                <td class="t-center"><?php $oOneStaff->cellphone = trim($oOneStaff->cellphone); if(!empty($oOneStaff->cellphone) && $oOneStaff->cellphone != NULL_STRING) { ?>
                	<?php $this->tpl->load_anchor_icon(array('id'=> 'view_history_' . $oOneStaff->id, 'img'=>ICON_IMG_HISTORY, 'title' => 'View action history', 'onclick'=>'ViewActionHistory('. $oOneStaff->id . ', \'' . HR_VNG . '\')')); ?>
                	
                	<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'id' => 'list_mobile_user_' . $oOneStaff->id, 'onclick' => (!isset($strIdentifier) ? 'list_mobile_user(' : 'ChooseCauseToLinkAction(1, ') . $oOneStaff->id . ', \'' . HR_VNG . '\')')); ?>
	    			
	    			<?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.message.smiley.png', 'class' => 'btn btn-bluesky', 'title' => 'Click to send sms', 'id' => 'send_sms_' . $oOneStaff->id, 'onclick' => (!isset($strIdentifier) ? 'send_message(' : 'ChooseCauseToLinkAction(2, ') . $oOneStaff->id . ', \'' . HR_VNG . '\')')); ?>
	    			
	    			<?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.save.png', 'class' => 'btn btn-success', 'title' => 'Click to save user info', 'id' => 'save_staff_' . $oOneStaff->id, 'onclick' => 'saveStaffInfo(' . $oOneStaff->id . ')')); ?>
	    			<?php } else {?>
	    			<?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.save.png', 'class' => 'btn btn-success', 'title' => 'Click to save user info', 'id' => 'save_staff_' . $oOneStaff->id, 'onclick' => 'saveStaffInfo(' . $oOneStaff->id . ')')); ?><?php } ?>
	    		</td>
                <td style="display:none;"><?php echo trim($oOneStaff->extension); ?></td>
            </tr>
            <?php } /* end foreach */?>
		</tbody>
	</table><br />
	<?php } /* end if */?> 
	<?php if(empty($arrSearchUsersResult) && empty($arrVNGStaffs)) { ?>
	<p style="text-align: center; font-size: 20px; font-weight: bold; vertical-align: middle;">No results were found corresponding to your search keywords.</p>
	<p style="text-align: center; vertical-align: middle;"><img src="<?php echo $base_url;?>asset/images/funny_not_found.png" width="350px" /></p>
	<?php } /* end if */?>
</div>
<script type="text/javascript">
	var inc_id = '<?php echo $strIncidentId; ?>';
	var alert_id = '<?php echo $strAlertId; ?>';
	var alert_msg = '<?php echo urlencode($strAlertMsg) ?>';
	var time_alert = '<?php echo $strTimeAlert; ?>';
	var change_id = '<?php echo $strChangeId; ?>';
	
	function send_message(userid, option_send) {
        if(option_send == '<?php echo SDK; ?>') {
            var url = base_url + 'contact/contact/load_popup_sms?userid=' + userid + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_msg) + '&time_alert=' + time_alert + '&change_id=' + change_id;    
        } else if(option_send == '<?php echo HR_VNG; ?>') {
            var url = base_url + 'contact/contact/load_popup_sms_vng_staff_list?userid=' + userid + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_msg) + '&time_alert=' + time_alert + '&change_id=' + change_id;     
        }
        
        CreateFancyBox('a#send_sms_' + userid, url, '90%');
    }

    function list_mobile_user(userid, option_call) {
        if(option_call == '<?php echo SDK; ?>') {
            var url = base_url + 'contact/contact/load_popup_list_mobile_user?userid=' + userid + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_msg) + '&time_alert=' + time_alert + '&change_id=' + change_id;
        } else if(option_call == '<?php echo HR_VNG; ?>') {
            var url = base_url + 'contact/contact/load_popup_list_mobile_user_vng_staff_list?userid=' + userid + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_msg) + '&time_alert=' + time_alert + '&change_id=' + change_id;
        }
        CreateFancyBox('a#list_mobile_user_' + userid, url, '90%', 380);
    } 
	
	function ChooseCauseToLinkAction(action, userid, option) {
    	var url = base_url + 'contact/contact/load_cause_link?user_id=' + userid + '&action=' + action + '&option=' + option;
    	if(action == 1) { //call
    		CreateFancyBox('a#list_mobile_user_' + userid, url, '80%', 300);	
    	} else if (action == 2) { //send sms
    		CreateFancyBox('a#send_sms_' + userid, url, '80%', 300);
    	}
    	
    }
    
    function saveStaffInfo(userid) {
    	var full_name = $.trim($('#tbodytblListUsersVNG').find('tr[no='+ userid + ']').children('td:first').text());
    	var mobile = $.trim($('#tbodytblListUsersVNG').find('tr[no='+ userid + ']').children('td:eq(1)').text());
    	var email = $.trim($('#tbodytblListUsersVNG').find('tr[no='+ userid + ']').children('td:eq(2)').text());
    	var department_name = $.trim($('#tbodytblListUsersVNG').find('tr[no='+ userid + ']').children('td:eq(3)').text());
        var ext = $.trim($('#tbodytblListUsersVNG').find('tr[no='+ userid + ']').children('td:eq(6)').text());
    	if(!full_name && (!mobile || mobile == '<?php echo NO_PHONE?>') && (!email || email == '<?php echo NO_EMAIL ?>') && !department_name) {
    		alert('Not enough information required to save.');
    	} else if(!full_name) {
    		alert('User name not empty. Don\'t save!');
    	} else if(!email || email == '<?php echo NO_EMAIL; ?>') {
    		alert('Email is not empty. Don\'t save!');
        } else if(!department_name || department_name == '<?php echo NO_DEPT;?>') {
            var url = base_url + 'contact/contact/load_popup_choose_department_contactSDK?userid=' + userid;
            create_fancybox(url, '60%', 205);

    	} else {
    		$.ajax({
    			url: base_url + 'contact/contact/check_department_existed_in_contactSDK',
    			type: 'POST',
    			data: {
                    userid: userid
    			},
    		})
    		.done(function(result) {
    			var obj = jQuery.parseJSON(result);
    			if(obj!=null) {
    				if(obj.msg == '<?php echo NO_DEPT;?>' || obj.msg == '<?php echo NOT_EXIST_STRING; ?>') {
                        var url = base_url + 'contact/contact/load_popup_choose_department_contactSDK?userid=' + userid;
                        create_fancybox(url, '60%', 205);
    				} else if(obj.msg == '<?php echo NO_NAME; ?>') {
    					alert('User name not empty. Don\'t save!');
    				} else if(obj.msg == '<?php echo NO_EMAIL; ?>') {
    					alert('Email is not empty. Don\'t save!');
    				} else if(obj.msg == '<?php echo INSERTDB_ERROR; ?>') {
    					alert('Save Action Fail!');
    				} else if(obj.msg == '<?php echo MESSAGE_TYPE_SUCCESS; ?>') {
                        alert('Save user information successfully');
                    } else if(obj.msg == '<?php echo ALREADY; ?>') {
                        alert('This user information has been existed in ContactPoint SDK!');
                    } else if(obj.msg == '<?php echo INVALID_EMAIL; ?>') {
                        alert('Invalid Email.');
                    }
    			} else {
    				alert('Internal Error!');
    			}
    		});
    		
    	}
    }
    
    function ViewActionHistory(userid, option_view) {
        var url = base_url + 'contact/contact/view_action_history_of_user?ref_id=' + userid;
    	CreateFancyBox('a#view_history_' + userid, url, '90%', 300);
    }
</script>