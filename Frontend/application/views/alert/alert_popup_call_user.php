<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/common.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/jconfirm.action.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/contact.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.css" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/common.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_no_selector.js"></script>

<body style="background-color: white; ">
	<div id="tblActionCallWrapper">
		<div id="divPhoneRinging" class="t-center" style="padding-top: 30px;" align="center">
			<img src="<?php echo $base_url?>asset/images/icons/phone_ringing.gif" alt="Phone ringing"><br>
			<p style="font-family:verdana;">Calling <b><?php echo htmlentities($arrUserInfo['full_name'], ENT_QUOTES, 'UTF-8'); ?></b> - <b id="bMobile"><?php echo @$arrUserInfo['mobile'][0]; ?></b>.........<p>
		</div>
		<div class="t-center" style="padding-top: 30px;" align="center">
			<a id="btnSuccess" class="peter-river-flat-button hand-pointer">Call Success</a>
			<a id="btnRecall" class="emerald-flat-button hand-pointer" phone_num="<?php echo @$arrUserInfo['mobile'][0]; ?>">Recall</a>
			<a id="btnFail" class="alizarin-flat-button hand-pointer">Call Fail & SMS</a>
		</div>
	</div>
	<?php if(!empty($arrUserInfo)) { ?>
	<?php if(count($arrUserInfo['mobile']) > 1) { ?>
	<br />
	<div style="margin: auto; width: 800px; padding-top: 10px; background-color: white;" class="t-center">
	<table id="tblCallUser" width="100%" cellpadding="0" cellspacing="0" class="list">
		<thead>
			<tr class="table-title">
				<th colspan="5"><p class="drag" style="margin-top: 0;">User <?php echo htmlentities($arrUserInfo['full_name'], ENT_QUOTES, 'UTF-8'); ?>'s phone numbers</p></th>
			</tr>
			<tr>
				<th class="t-center" style="width: 20%;">Phone</th>
				<th class="t-center" style="width: 20%;">Action</th>
			</tr>
		</thead>
		<tbody id="tbodytblCallUser">
		<?php foreach($arrUserInfo['mobile'] AS $idx=>$rowMobile) {?>
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
var iUserId = <?php echo $arrUserInfo['userid']; ?>;
var alert_id = '<?php echo $strAlertId; ?>';
var alert_msg = '<?php echo htmlspecialchars($strAlertMsg, ENT_QUOTES, 'utf-8'); ?>';
var time_alert = '<?php echo $strTimeAlert; ?>';

function call_mobile(strMobile) {
	$.ajax({
		type: 'POST',
		url: '<?php echo $base_url; ?>contact/contact/call_user',
		data: {
			strMobile:strMobile,
			user_name:'<?php echo $arrUserInfo['full_name']; ?>',
			ref_id:iUserId,
			alert_id:alert_id,
			alert_msg:alert_msg,
			time_alert:time_alert
		}

	});
}

$(document).ready(function() {
	call_mobile('<?php echo htmlentities($arrUserInfo['mobile'][0]) ?>');
	
	$('#btnSuccess').click(function()
	{
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>contact/contact/call_user_success',
			data: {
				iUserId:iUserId,
				alert_id:alert_id,
				alert_msg:alert_msg,
				time_alert:time_alert,
				identifier:1
			},
			success: function(result) {
				<?php if(count($arrUserInfo['mobile']) == 1) { ?>
					parent.$.fancybox.close();
				<?php } elseif (count($arrUserInfo['mobile']) > 1) { ?>
					$('#tblActionCallWrapper').hide();
				<?php } ?>
	
			}
		});
	});
	
	$('#btnFail').click(function()
	{
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>contact/contact/call_user_fail',
			data: {
				iUserId:iUserId,
				alert_id:alert_id,
				alert_msg:alert_msg,
				time_alert:time_alert,
				identifier:1
			},
			success: function(result) {
				var url = base_url + 'contact/contact/load_popup_sms?userid=' + iUserId + '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_msg) + '&time_alert=' + time_alert + '&sms_identifier=1';
				<?php if(count($arrUserInfo['mobile']) == 1) { ?>
					// parent.$.fancybox.close();
					create_fancybox_next(url, '90%', 280);
				<?php } elseif (count($arrUserInfo['mobile']) > 1) { ?>
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
				user_name:'<?php echo $arrUserInfo['full_name'];?>',
				ref_id:iUserId,
				alert_id:alert_id,
				alert_msg:alert_msg,
				time_alert:time_alert,
				action_type:'recall'
			},
			success:function(result) {
				var obj = jQuery.parseJSON(result);
				if(obj!=null) {
					if(obj.msg == 'success') {
						$('#bMobile').html(strMobile);
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
			user_name:'<?php echo $arrUserInfo['full_name'];?>',
			ref_id:iUserId,
			alert_id:alert_id,
			alert_msg:alert_msg,
			time_alert:time_alert
		},
		success:function(result) {
			var obj = jQuery.parseJSON(result);
			if(obj!=null) {
				if(obj.msg == 'success') {
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