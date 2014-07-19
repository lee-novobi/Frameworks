<?php global $arrDefined; ?>
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/alert.css" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<?php echo $message; ?>
<h2>Alert Info</h2>
<form id="frmAlertACK" type="post" action="">
<table cellpadding="3" cellspacing="0" width="100%" style="font-size: 13px;">
	<tbody>
		<tr>
			<td class="t-left" style="width: 100px; padding-left: 20px;">Host name:</td>
			<td><?php if(isset($oAlert['zbx_host'])) { echo @$oAlert['zbx_host']; } ?></td>
		</tr>
		<tr>
			<td class="t-left" style="padding-left: 20px;">Description:</td>
			<td style="color: #DB2A36; font-weight: bold;"><?php echo htmlentities($oAlert['alert_message'], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<!-- <tr>
			<td class="t-left" style="padding-left: 20px;">Last changed:</td>
			<td></td>
		</tr>
		<tr>
			<td class="t-left" style="padding-left: 20px;">Ignore Alert:</td>
			<td><input type="checkbox" name="chkIgnoreAlert" value="<?php if(strtolower(@$oAlert['source_from']) != 'zabbix') { echo @$oAlert['_id'];} else { echo @$oAlert['zabbix_triggerid'];} ?>"></td>
		</tr> -->
	</tbody>
</table>
<h3>More Alerts</h3>
<!--<div class="scrollWrapper">-->
<table cellpadding="0" cellspacing="0" width="100%" class="list-zebra">
	<thead>
		<tr class="table-title">
			<th class="t-center wp50"><input type="checkbox" id="ack_all"></th>
			<th class="t-center wp50">Location</th>
            <th class="t-center wp50">Source</th>
            <th class="t-center wp100">Hostname</th>
            <th class="t-center">Alert</th>
            <th class="t-center wp110">Time Alert</th>
            <!--<th class="t-center wp90">Check Ignore<br>Alert</th>-->
		</tr>
	</thead>
	<tbody id="tbodyMoreAlert">
		<?php if(!empty($arrAlertsNoACK)) { ?>
		<?php foreach ($arrAlertsNoACK as $key => $oAlertNoACK) { ?>
		<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
			<td class="t-center">
	      		<input type="checkbox" class="case" name="chkACK" value="<?php echo $oAlertNoACK['_id']; ?>">
	      	</td>
	    	<td style="padding-left: 15px" class="t-left">
	      		QTSC
	      	</td>
	      	<td style="padding-left: 15px" class="t-left">
	      		<?php echo @$oAlertNoACK['source_from']; ?>
	      	</td>
	      	<td style="padding-left: 15px" class="t-left">
	      		<?php echo @$oAlertNoACK['zbx_host']; ?>
	      	</td>
	      	<?php if (in_array(intval(@$oAlertNoACK['zabbix_triggerid']), $arrIgnoredTriggers) || in_array((string)@$oAlertNoACK['_id'], $arrIgnoredTriggers)) { ?>
	      	<td class="t-left" style="padding-left: 15px; background:#efefef;">
	      	<?php } else { ?>
	      	<td style="padding-left: 15px" 
	  		class="<?php if(isset($oAlertNoACK['priority'])) 
						{
							$oAlertNoACK['priority'] = strtolower($oAlertNoACK['priority']);      
							if($oAlertNoACK['priority'] == "critical") { echo "t-left bg-red cl-white"; }
							elseif($oAlertNoACK['priority'] == "high") { echo "t-left bg-pink cl-white"; } 
							elseif($oAlertNoACK['priority'] == "medium" || $oAlertNoACK['priority'] == "low") { echo "t-left"; }
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
	      	<?php if(in_array(strtolower(@$oAlertNoACK['source_from']), $arrDefined['source_allow_show_numof_case'])) { ?>
	      	<span class="numof-case"><?php echo @$oAlertNoACK['num_of_case']; ?></span>
	      	<?php } ?>
		  	<?php echo htmlentities($oAlertNoACK['alert_message'], ENT_QUOTES, "UTF-8"); ?>
		  	<?php if(!empty($oAlertNoACK['noof_call_success']) || !empty($oAlertNoACK['noof_call_fail'])) { ?>
	        <span class="numof-call" title="Num of Call"><?php echo !empty($oAlertNoACK['noof_call_success']) ? $oAlertNoACK['noof_call_success'] : 0 ?> |
	        	<?php echo !empty($oAlertNoACK['noof_call_fail']) ? $oAlertNoACK['noof_call_fail'] : 0 ?>
	        </span>
	        <?php } ?>
	        </a>
	      	</td>
	      	<td class="t-right">
            <?php if(!isset($oAlertNoACK['clock'])) echo date('Y-m-d H:i:s', $oAlertNoACK['create_date']); else echo date('Y-m-d H:i:s', $oAlertNoACK['clock']); ?>
	      	</td>
	      	<!-- <td class="t-center">
	      		<input type="checkbox" onclick="IgnoreAlert();" name="chkIgnoreAlert_<?php ?>" value="<?php if(strtolower(@$oAlertNoACK['source_from']) != 'zabbix') { echo @$oAlertNoACK['_id'];} else { echo @$oAlertNoACK['zabbix_triggerid'];} ?>">
	      	</td> -->
	    </tr>
	    <?php } /* End foreach */ ?>
	    <?php } /* End if */ ?>
	</tbody>
</table>
<!--</div>-->
<div id="divNewACK">
	<h2>Input new ACK</h2>
	<p>Type Issue:
		<span style="margin-right: 20px"><input id="rdoTypeNoInc" type="radio" name="rdoTypeIssue" value="<?php echo ISSUE_TYPE_NO_INC; ?>" checked="checked">No Incident</span>
		<span style="margin-right: 20px"><input id="rdoTypeInc" type="radio" name="rdoTypeIssue" value="<?php echo ISSUE_TYPE_INC; ?>">Incident</span>
		<span style="margin-right: 20px"><input type="radio" name="rdoTypeIssue" value="<?php echo ISSUE_TYPE_NEW_SERVER; ?>">New server</span>
		<span style="margin-right: 20px"><input type="radio" name="rdoTypeIssue" value="<?php echo ISSUE_TYPE_IN_MAINTENANCE; ?>">In Maintenance</span>
		<span><input type="radio" name="rdoTypeIssue" value="<?php echo ISSUE_TYPE_BEING_CONFIGURED; ?>">Being configured</span>
	</p>
	<?php if(htmlentities($oAlert['source_from'], ENT_QUOTES, "UTF-8") == 'CS') { ?>
	<p>
		<input id="txtITSMId" list="dlITSMId" type="list" style="width:400px;" onfocus="if(this.value=='Incident ID') this.value='';" onblur="if(this.value=='') this.value='Incident ID';" value="Incident ID">
		<datalist id="dlITSMId">
		<?php foreach($arrIncFollow as $rowInc) { ?>
			<option value="<?php echo $rowInc['itsm_incident_id'].'-'.$rowInc['title'] ?>">
		<?php } ?>
		</datalist>
		<input id="btnLinkCSAlert" type="button" value="Link Alert" onclick="LinkCSAlert()">
		<input id="btnRejectCSAlert" type="button" value="Reject Alert" onclick="RejectCSAlert()">
	</p>
	<?php } ?>
	<textarea id="txtACKMsg" rows="5" class="w90"></textarea><br />
	<input type="button" value="Add ACK" onclick="AddACK()">
    <!-- <?php if (isset($oAlert['zabbix_triggerid']) && $oAlert['zabbix_triggerid'] !== '') { ?> 
    <?php if (!in_array($oAlert['zabbix_triggerid'], $arrIgnoredTriggers)) { ?>
   	<input type="button" value="Ignore Alert" onclick="IgnoreAlert(1)">
    <?php } else { ?>
     <input type="button" value="Stop Ignore" onclick="IgnoreAlert(0)">
    <?php } } ?> -->
    <input type="checkbox" style="margin-left: 50px;" name="chkIgnoreAlert" value="<?php if(strtolower(@$oAlert['source_from']) != 'zabbix') { echo @$oAlert['_id'];} else { echo @$oAlert['zabbix_triggerid'];} ?>" 
    <?php if ((isset($oAlert['zabbix_triggerid']) && $oAlert['zabbix_triggerid'] !== '' && in_array(intval($oAlert['zabbix_triggerid']), $arrIgnoredTriggers)) || (strtolower(@$oAlert['source_from']) != 'zabbix' && in_array((string)@$oAlert['_id'], $arrIgnoredTriggers))) { 
    	?>checked="checked"<?php } ?>>Check Ignored Alert
</div>
</form> <!-- end #frmAlertACK -->
<h2>Last ACK</h2>
<div id="divACKListWrapper">
	<div id="divACKList"></div>
	<div id="divACKListPagination" style="background:#efefef; border: 1px solid #CCCCCC;"></div>
</div>
<input type="hidden" id="hidAlertID" value="<?php echo $oAlert['_id'] ?>">
<input type="hidden" id="hidTriggerID" value="<?php if(strtolower(@$oAlert['source_from']) != 'zabbix') { echo @$oAlert['_id'];} else { echo @$oAlert['zabbix_triggerid'];} ?>">
<input type="hidden" id="hidCurrentACKPage" value="<?php echo $nPage  ?>">
<input type="hidden" id="hidACKPageSize" value="<?php echo $nPageSize ?>">
<script language="javascript" type="text/javascript">
var iTotalACK       = <?php echo $nTotalACK ?>;
var iCurrentACKPage = <?php echo $nPage  ?>;
var iACKPageSize    = <?php echo $nPageSize  ?>;

$(document).ready(function(){
	ListACK();
	$('#divACKListPagination').pagination({
	    total: iTotalACK,
	    pageNumber: iCurrentACKPage,
	    pageSize: 10,
	    showPageList: false,
	    showRefresh: false,
	    onSelectPage: function(nPage, nPageSize){
	    	$("#hidCurrentACKPage").val(nPage);
	    	$("#hidACKPageSize").val(nPageSize);
	    	ListACK();
	    }
	});

	// @thaodt: update checkbox check all event
	$('#ack_all').click(function() {
		var checked_status = this.checked;
		$("input[name=chkACK]").each(function()
		{
			this.checked = checked_status;
		});
	});
	
	$('input[name=chkIgnoreAlert]').click(function() {
		var strMsg = $("#txtACKMsg").val();
		var strSourceFrom = '<?php echo @$oAlert['source_from']; ?>';
		var strSourceId = '<?php echo @$oAlert['source_id']; ?>';
		var nIsIgnored = 0;
		
		if($(this).is(':checked')) {
			nIsIgnored = 1;
		}	
		var strURL = base_url + "<?php echo $this->router->directory ?>alert/ignore_alert";
		var strID = $(this).val();
		$.post(
			strURL,
			{'triggerid': strID, 'is_ignored': nIsIgnored, 'msg': strMsg, 'source_from': strSourceFrom, 'source_id': strSourceId},
			function(){
				location.reload();
				window.parent.SetReloadAlertListAfterACK();
			}
		);
		
	});
	//==================CS Alert================
	$('#btnRejectCSAlert').css('display','inline');
	$('#txtITSMId').css('display','none');
	$('#btnLinkCSAlert').css('display','none');
	
	// $('#rdoTypeNoInc').on("change", function(event){
              // $('#btnRejectCSAlert').css('display','inline');
              // $('#txtITSMId').css('display','none');
              // $('#btnLinkCSAlert').css('display','none');
    // });
	
	// $('#rdoTypeInc').on("change", function(event){
            // $('#btnRejectCSAlert').css('display','none');
            // $('#txtITSMId').css('display','inline');
            // $('#btnLinkCSAlert').css('display','inline');
    // });
	
	$('input[name=rdoTypeIssue]').on("change", function(event){
		var strTypeIssue = $('input[name=rdoTypeIssue]:checked').val();
        if(strTypeIssue == '<?php echo ISSUE_TYPE_NO_INC; ?>')
		{
			$('#btnRejectCSAlert').css('display','inline');
			$('#txtITSMId').css('display','none');
			$('#btnLinkCSAlert').css('display','none');
		}
		else if(strTypeIssue == '<?php echo ISSUE_TYPE_INC; ?>')
		{
			$('#btnRejectCSAlert').css('display','none');
            $('#txtITSMId').css('display','inline');
            $('#btnLinkCSAlert').css('display','inline');
		}
		else
		{
			$('#btnRejectCSAlert').css('display','none');
            $('#txtITSMId').css('display','none');
            $('#btnLinkCSAlert').css('display','none');
		}
    });
	
});

function ListACK(){
	var nPage = $("#hidCurrentACKPage").val();
	var nPageSize = $("#hidACKPageSize").val();

	var strURL = base_url + "<?php echo $this->router->directory ?>alert/ajax_ack_list?page=" + nPage + "&limit=" + nPageSize + "&alertid=" + "<?php echo $oAlert['_id'] ?>";
	$("#divACKList").load(strURL)
}

// function IgnoreAlert(nIsIgnored) {
	// var strURL = base_url + "<?php echo $this->router->directory ?>alert/ignore_alert";
	// var strTriggerID = $("#hidTriggerID").val();
	// $.post(
		// strURL,
		// {'triggerid': strTriggerID, 'is_ignored': nIsIgnored},
		// function(){
			// location.reload();
			// window.parent.SetReloadAlertListAfterACK();
		// }
	// );
// }

function AddACK(){
	var strURL = base_url + "<?php echo $this->router->directory ?>alert/add_alert_ack";
	var strAID = $("#hidAlertID").val();
	var strMsg = $("#txtACKMsg").val();
	var strTypeIssue = $('input[name=rdoTypeIssue]:checked').val();
	var iCountChecked = 0;
	var arrMoreAlerts = new Array();
	var iTblMoreAlertLength = $('#tbodyMoreAlert tr').length;
	
	if($.trim(strMsg) != '') {
		$('#tbodyMoreAlert tr').each(function() {
			if($(this).find('input[name=chkACK]').is(':checked')) {
				//iCountChecked += 1;
				arrMoreAlerts.push($(this).find('input[name=chkACK]:checked').val());
			}
		});
		
		// console.debug(arrMoreAlerts);
		
		$.post(
			strURL,
			{'alertid': strAID, 'arrMoreAlerts': arrMoreAlerts, 'type_issue': strTypeIssue, 'msg': strMsg},
			function(){
				$("#hidCurrentACKPage").val(1);
				$("#txtACKMsg").val("");
				ListACK();

				iTotalACK++;
				$('#divACKListPagination').pagination({
				    total: iTotalACK,
				    pageNumber: 1
				});
				window.parent.SetReloadAlertListAfterACK();
			}
		);
	} else {
		alert('Vui lòng nhập nội dung ACK');
	}
}

function RejectCSAlert(){
	var strURL = base_url + "<?php echo $this->router->directory ?>alert/reject_cs_alert";
	var strAID = $("#hidAlertID").val();
	var strMsg = $("#txtACKMsg").val();
	var strTypeIssue = $('input[name=rdoTypeIssue]:checked').val();
	var iCountChecked = 0;
	var arrMoreAlerts = new Array();
	var iTblMoreAlertLength = $('#tbodyMoreAlert tr').length;
	
	if($.trim(strMsg) != '') {
		$('#tbodyMoreAlert tr').each(function() {
			if($(this).find('input[name=chkACK]').is(':checked')) {
				//iCountChecked += 1;
				arrMoreAlerts.push($(this).find('input[name=chkACK]:checked').val());
			}
		});
		var strResult = AjaxLoadByPost(strURL, {'alertid': strAID, 'arrMoreAlerts': arrMoreAlerts, 'type_issue': strTypeIssue, 'msg': strMsg});
		var jsonResult = JSON.parse(strResult);
		if(jsonResult.result == true)
		{
			alert(jsonResult.msg);
			$("#hidCurrentACKPage").val(1);
			$("#txtACKMsg").val("");
			ListACK();

			iTotalACK++;
			$('#divACKListPagination').pagination({
				total: iTotalACK,
				pageNumber: 1
			});
			window.parent.SetReloadAlertListAfterACK();
		}
		else
		{
			alert(jsonResult.msg + jsonResult.arrError);
		}
	} else {
		alert('Vui lòng nhập nội dung');
	}
}

function LinkCSAlert(){
	var strURL = base_url + "<?php echo $this->router->directory ?>alert/link_cs_alert";
	var strAID = $("#hidAlertID").val();
	var strITSMId = $("#txtITSMId").val();
	var strMsg = $("#txtACKMsg").val();
	var strTypeIssue = $('input[name=rdoTypeIssue]:checked').val();
	var arrMoreAlerts = new Array();
	
	if($.trim(strITSMId) != '') {
		$('#tbodyMoreAlert tr').each(function() {
			if($(this).find('input[name=chkACK]').is(':checked')) {
				//iCountChecked += 1;
				arrMoreAlerts.push($(this).find('input[name=chkACK]:checked').val());
			}
		});
		
		var strResult = AjaxLoadByPost(strURL, {'alertid': strAID, 'arrMoreAlerts': arrMoreAlerts, 'strITSMId': strITSMId, 'type_issue': strTypeIssue, 'msg': strMsg});
		var jsonResult = JSON.parse(strResult);
		alert(jsonResult.msg);
		if(jsonResult.result == true)
		{
			$("#hidCurrentACKPage").val(1);
			$("#txtACKMsg").val("");
			$("#txtITSMId").val("Incident ID");
			ListACK();

			iTotalACK++;
			$('#divACKListPagination').pagination({
				total: iTotalACK,
				pageNumber: 1
			});
			window.parent.SetReloadAlertListAfterACK();
		}
	} else {
		alert('Vui lòng nhập ITSM ID');
	}
}
</script>