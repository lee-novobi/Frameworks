<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/jconfirm.action.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/contact.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.css" />

<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_no_selector.js"></script>
<body style="background-color: white; ">
	<div id="tblActionCallWrapper">
		<div id="divPhoneRinging" class="t-center" style="padding-top: 30px;">
			<img src="<?php echo $base_url?>asset/images/icons/phone_ringing.gif" alt="Phone ringing"><br>
			<p style="font-family:verdana;"><label id="lblCall">Calling </label><b><?php echo htmlentities($arrUser['full_name'], ENT_QUOTES, 'UTF-8'); ?></b> - <b id="bMobile"><?php echo @$arrUser['mobile'][0]; ?></b>.........<p>
		</div>
		<div class="t-center" style="padding-top: 30px;">
			<a id="btnSuccess" class="peter-river-flat-button hand-pointer">Call Success</a>
			<a id="btnRecall" class="emerald-flat-button hand-pointer" phone_num="<?php echo @$arrUser['mobile'][0]; ?>">Recall</a>
			<a id="btnFail" class="alizarin-flat-button hand-pointer">Call Fail & SMS</a>
		</div>
	</div>
	<?php if(!empty($arrUser)) { ?>
	<?php if(count($arrUser['mobile']) > 1) { ?>
	<br />
	<div style="margin: auto; width: 90%; padding-top: 10px; background-color: white;" class="t-center">
	<table id="tblCallUser" width="100%" cellpadding="0" cellspacing="0" class="list">
		<thead>
			<tr class="table-title">
				<th colspan="5"><p class="drag" style="margin-top: 0;">User <?php echo htmlentities($arrUser['full_name'], ENT_QUOTES, 'UTF-8'); ?>'s phone numbers</p></th>
			</tr>
			<tr>
				<th class="t-center" style="width: 20%;">Phone</th>
				<th class="t-center" style="width: 20%;">Action</th>
			</tr>
		</thead>
		<tbody id="tbodytblCallUser">
		<?php foreach($arrUser['mobile'] AS $idx=>$rowMobile) {?>
			<?php //if($idx > 0) { ?>
			<tr>
					<td style="width:20%;" class="edit_dept t-center"><?php echo htmlentities($rowMobile);?></td>
					<td style="width:20%;" class="t-center" style="padding-left: 10px; vertical-align: middle;">
						<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'id' => 'call_user_' . $idx, 'onclick' => 'call_user_mobile(\'' . htmlentities($rowMobile) . '\')')); ?>
					</td>
			</tr>
			<?php // } /* End If */ ?>
		<?php } /* End foreach */ ?>
		</tbody>	
	</table>
	</div>
	<?php } } ?>
</body>

<script type="text/javascript">
var inc_id = '<?php echo $strIncidentId; ?>';
var alert_id = '<?php echo $strAlertId; ?>';
var alert_msg = '<?php echo urlencode($strAlertMsg) ?>';
var time_alert = '<?php echo $strTimeAlert ?>';
var change_id = '<?php echo $strChangeId ?>';

$(document).ready(function() {
	$('#btnSuccess').click(function()
	{
		var iUserId = <?php echo $arrUser['userid']; ?>;
		
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>contact/contact/call_user_success',
			data: {
				iUserId:iUserId,
				incident_id:inc_id,
				alert_id:alert_id,
				alert_msg:alert_msg,
				time_alert:time_alert,
				change_id:change_id
			},
			success: function(result) {
				//parent.$.fancybox.close();
				// $('#tblActionCallWrapper').css('display', 'none');
				<?php if(count($arrUser['mobile']) == 1) { ?>
					parent.$.fancybox.close();
				<?php } elseif (count($arrUser['mobile']) > 1) { ?>
					$('#tblActionCallWrapper').hide();
				<?php } ?>
				
			}
		});
	});
	
	$('#btnFail').click(function()
	{
		var iUserId = <?php echo $arrUser['userid']; ?>;
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>contact/contact/call_user_fail',
			// url: '<?php echo $base_url ?>contact/contact/call_fail_and_sms', 
			data: {
				iUserId:iUserId,
				incident_id:inc_id,
				alert_id:alert_id,
				alert_msg:alert_msg,
				time_alert:time_alert,
				user_in:'<?php echo SDK;?>', //nhan dạng để choose func show sms popup
				change_id:change_id
			},
			success: function(result) {
				var url = base_url + 'contact/contact/load_popup_sms?userid=' + iUserId + '&incident_id=' + inc_id + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_msg) + '&time_alert=' + time_alert + '&sms_identifier=1' + '&change_id=' + change_id;
				<?php if(count($arrUser['mobile']) == 1) { ?>
					// parent.$.fancybox.close();
					create_fancybox_next(url, '90%', 280);
				<?php } elseif (count($arrUser['mobile']) > 1) { ?>
					$('#tblActionCallWrapper').hide();
					create_fancybox(url, '90%', 280);
				<?php } ?>	
			}
		});
	});
	
	$('#btnRecall').click(function() {
		var strMobile = $(this).attr('phone_num');
		$('#divPhoneRinging').css('display', 'none');
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>contact/contact/call_user',
			data: {
				strMobile:strMobile,
				user_name:'<?php echo $arrUser['full_name'];?>',
				ref_id:'<?php echo $arrUser['userid'];?>',
				incident_id:inc_id,
				alert_id:alert_id,
				alert_msg:alert_msg,
				time_alert:time_alert,
				action_type:'recall',
				change_id:change_id
			},
			success:function(result) {
				var obj = jQuery.parseJSON(result);
				if(obj!=null) {
					if(obj.msg == 'success') {
						$('#bMobile').html(strMobile);
						// $(this).attr('phone_num', strMobile);
						$('#lblCall').html('Redial ');
						$('#divPhoneRinging').show();
					} else if(obj.msg == 'call_time_out') {
						alert('Service call TIME OUT!');
					} else if(obj.msg == 'no_found_avaya') {
						alert('No found IP Avaya!');
					}
				} else {
					alert('Internal Error');
				}
			}
		});
	});
});
 
function call_user_mobile(strMobile) {
	$.ajax({
		type: 'POST',
		url: '<?php echo $base_url; ?>contact/contact/call_user',
		data: {
			strMobile:strMobile,
			user_name:'<?php echo $arrUser['full_name'];?>',
			ref_id:'<?php echo $arrUser['userid'];?>',
			incident_id:inc_id,
			alert_id:alert_id,
			alert_msg:alert_msg,
			time_alert:time_alert,
			change_id:change_id
		},
		success:function(result) {
			var obj = jQuery.parseJSON(result);
			if(obj!=null) {
				if(obj.msg == 'success') {
					// $('#tblActionCallWrapper').css('display', '');
					$('#bMobile').html(strMobile);
					$('#btnRecall').attr('phone_num', strMobile);
					$('#tblActionCallWrapper').show();
				} else if(obj.msg == 'call_time_out') {
					alert('Service call TIME OUT!');
				} else if(obj.msg == 'no_found_avaya') {
					alert('No found IP Avaya!');
				}
			} else {
				alert('Internal Error');
			}
		}

	});
}
</script>