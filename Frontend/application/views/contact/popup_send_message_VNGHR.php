<link rel="StyleSheet" type="text/css" href="<?php echo $base_url; ?>asset/css/sms.style.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url; ?>asset/css/contact.css" />

<script type="text/javascript">
//var arr_mob = new Array();
function validVal(event, keyRE) {
	if (	( typeof(event.keyCode) != 'undefined' && event.keyCode > 0 && String.fromCharCode(event.keyCode).search(keyRE) != (-1) ) || 
		( typeof(event.charCode) != 'undefined' && event.charCode > 0 && String.fromCharCode(event.charCode).search(keyRE) != (-1) ) ||
		( typeof(event.charCode) != 'undefined' && event.charCode != event.keyCode && typeof(event.keyCode) != 'undefined' && event.keyCode.toString().search(/^(8|9|13|45|46|35|36|37|39)$/) != (-1) ) ||
		( typeof(event.charCode) != 'undefined' && event.charCode == event.keyCode && typeof(event.keyCode) != 'undefined' && event.keyCode.toString().search(/^(8|9|13)$/) != (-1) ) ) {
		return true;
	} else {
		return false;
	}
}
$(document).ready(function () {
    
    $('#message').bind('keypress', function (event) {
	    var regex = new RegExp("^[a-zA-Z0-9,_,~,!,@,#,$,%,^,&,(,),*,+,=,:,;,<,>,?,.,//,',\",\\[,\\], \b\t\-]+$");
	    return validVal(event, regex);
	});
    
	$("#send").click(function(){
        var message = $("#message").val();
        var mobile_selected = $("input:radio[name=rr]:checked + label").text();
        //console.log(mobile_selected);
        if($.trim(message).length == '') {
            alert("Message is not null!");
            return;
        }
        if (message.length > 160) {
			alert("Number of characters must be <= 160");
			return;
		}
		$.ajax({
		    type: 'POST',
		    url: base_url + "contact/contact/send_sms",
		    data:{
		    	userid:<?php echo $arrOneUser["id"] ?>, 
		    	message:message, 
		    	user_name:'<?php echo $arrOneUser['fullname'];?>', 
		    	mobile:mobile_selected, 
		    	incident_id:'<?php echo @$strIncidentId ?>', 
		    	alert_id: '<?php echo @$strAlertId ?>',
		    	alert_msg: '<?php echo urlencode(@$strAlertMsg) ?>',
		    	time_alert: '<?php echo @$strTimeAlert ?>',
		    	sms_identifier: '<?php echo @$sms_identifier ?>',
		    	change_id: '<?php echo @$strChangeId ?>'
		    },
		    cache: false,
		    connection: 'closed',
		    success: function(data) {
                var parent = window.parent;
                var object = jQuery.parseJSON(data);
                if(object.note == 'ok') {
                    parent.$.fancybox.close();
                    parent.$('div#msgalert').removeAttr('class').addClass('notification ' + object.type_msg);
                    parent.$('div#msg_content').text(object.content);
                    parent.$('div#msgalert').removeAttr('style');
                    parent.$("body, html").animate({ scrollTop: parent.$('div#msgalert').offset().top }, 1000);
                } else {
                    $('#tblSendSMS').find('tr:eq(2)').children('td:eq(2)').html(object.content).css({'color': 'red', 'font-weight': 'bold'});
                    $('#tblSendSMS').find('tr:eq(2)').children('td:eq(2)').css('display', '');
                }
		    },
		    error: function(xhr, errText, ex) {
		    }
		});
		
	});
    
    $('#btnCancel').click(function()
	{
		parent.$.fancybox.close();
	});
});
</script>
<div style="background-color: white;">
    <table id="tblSendSMS" cellpadding="10" cellspacing="0" width="100%" border="1" style="border-color: lightgrey; border-style: solid; border-width: 0; padding: 5px;">
        <tr>
            <th colspan="3" style="font-size: 15px;color: #33CC33">Send message</th>
        </tr>
    	<tr>
    		<td>Send to</td>
    		<td colspan="2">
				<?php $output = htmlentities($arrOneUser['fullname'], ENT_QUOTES, 'UTF-8');
                    foreach($arrOneUser['cellphone'] as $idx=>$mob) {
                        if($idx == 0) {
                            $output .= '<input type="radio" id="r'. $idx .'" name="rr" checked="checked" />
                                    <label for="r'. $idx .'"><span></span>' . $mob . '</label>';
                        } else {
                            $output .= '<input type="radio" id="r'. $idx .'" name="rr" />
                                    <label for="r'. $idx .'"><span></span>' . $mob . '</label>';
                        }
                    }
                echo $output;
                ?>
			</td>
    	</tr>
    	<tr>
    		<td>Message content</td>
    		<td><textarea cols="60" rows="3" name="message" id="message" style="background-color: #f6f6f6; border: 1px solid #ccc;"></textarea><br />
            <label style="color: blue; font-weight: bold;">Message don't include characters as {} | \</label>
    		</td>
            <td style="display: none;"></td>
    	</tr>
    	
    	<tr>
    		<td class="t-center" colspan="3">
    			<a id="send" class="midnight-blue-flat-button hand-pointer">Send</a>
    			<a id="btnCancel" class="silver-flat-button hand-pointer">Cancel</a>
    		</td>
    	</tr>
    </table>
</div>