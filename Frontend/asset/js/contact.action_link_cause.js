/**
 * @author Amidamaru
 */
function PrepareDataByOption(option, url, height) {
	if(option == 1) { //save
		if($.trim($('#iptLinkCause').val())) {
			if($.trim($('#hidAlertId').val()) && $.trim($('#hidTimeAlert').val()) && !$.trim($('#hidIncidentId').val())) {
				var alert_message = $.trim($('#iptLinkCause').val());
				var alert_id = $.trim($('#hidAlertId').val());
				var time_alert = $.trim($('#hidTimeAlert').val());
				url += '&alert_id=' + alert_id + '&alert_msg=' + encodeURIComponent(alert_message) + '&time_alert=' + time_alert;
			} else if($.trim($('#hidIncidentId').val()) && !$.trim($('#hidAlertId').val()) && !$.trim($('#hidTimeAlert').val())) {
				var incident_id = $.trim($('#hidIncidentId').val());
				url += '&incident_id=' + incident_id;
			}
			
		} else {
			alert('You have not chosen Cause yet!'); return;
		}
	}
	create_fancybox_next(url, '70%', height);
}

function list_mobile_user(option, choose_view) {
	if(!choose_view || choose_view == 'sdk') {
		var url = base_url + 'contact/contact/load_popup_list_mobile_user?userid=' + userid;
	} else if(choose_view == 'hr_vng') {
		var url = base_url + 'contact/contact/load_popup_list_mobile_user_vng_staff_list?userid=' + userid;
	}
	PrepareDataByOption(option, url, 380);   
}

function send_message(option, choose_view) {
	if(!choose_view || choose_view == 'sdk') {
    	var url = base_url + 'contact/contact/load_popup_sms?userid=' + userid;
    } else if(choose_view == 'hr_vng') {
   		var url = base_url + 'contact/contact/load_popup_sms_vng_staff_list?userid=' + userid;
   	}
    PrepareDataByOption(option, url, 280);
}

function GetCauseList(strCause) {
	var lstCause = Array();
	var link_cause_url = base_url + 'contact/contact/ajax_get_cause';
	$.ajax({
		url: link_cause_url,
		async: false,
		data: {cause: strCause}
	}).done(function(result) {
		var obj = jQuery.parseJSON(result);
		if(obj!=null) {
			// console.log(obj);
			lstCause = obj;
		}
	});
	return lstCause;
}

function LinkCauseBindChange() {
	$("input[name='rdoLinkCause']").click(function() {
		if($(this).is(':checked')) {
			$('#hidAlertId').val('');
			$('#hidTimeAlert').val('');
			$('#hidIncidentId').val('');
			var LinkCauseVal = $.trim($(this).val());
			// alert(LinkCauseVal);
			if(LinkCauseVal != '') {					
				// GetCauseList(LinkCauseVal);
				
				AttachjQueryUIAutoComplete('#iptLinkCause', GetCauseList(LinkCauseVal));
				$('#iptLinkCause').css('display', '');
			}
		}
	});
}
