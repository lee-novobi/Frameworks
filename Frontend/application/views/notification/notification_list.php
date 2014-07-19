<?php global $arrDefined /* Define trong helper/defined_helper.php */; ?>
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/noti.css" />

    <div id="noti-wrapper">
		<div id="dvHeadIncidentNoti" style="width:235px; overflow-y: auto;">
			<table cellspacing="0" class="list-zebra" cellpadding="0" id="tblIncidentNoti" border="0">
				<thead>
					  <tr class="table-title">
						<th colspan="3"><p class="drag">Remind Incident</p></th>
					  </tr>
					  <tr>
						<th class="t-center wp80">Incident</th>
						<th class="t-center wp80">Action</th>
						<th class="t-center wp80">Created date</th>
					  </tr>
				</thead>
			</table>
		</div>
		<div id="dvIncidentNoti" style="height:300px; overflow-y: auto;">
		  <table cellspacing="0" class="list-zebra" cellpadding="0" id="tblIncidentNoti" border="0">
			
			<tbody>
			  <?php echo $strIncidentNoti ?>
			</tbody>
		  </table>
		</div>
		<!-- <div id="dvHeadSEReportNoti" style="width:235px; overflow-y: auto;">
			<table cellspacing="0" class="list-zebra" cellpadding="0" id="tblSEReportNoti" border="0">
				<thead>
					  <tr class="table-title">
						<th colspan="3"><p class="drag">Remind SE Report</p></th>
					  </tr>
					  <tr>
						<th class="t-center wp80">Incident</th>
						<th class="t-center wp80">Action</th>
						<th class="t-center wp80">Created date</th>
					  </tr>
				</thead>
			</table>
		</div>
		<div id="dvSEReportNoti" style="height:300px; overflow-y: auto;">
		  <table cellspacing="0" cellpadding="0" class="list-zebra" id="tblSEReportNoti" border="0">
			<tbody>
			  <?php echo $strSEReportNoti ?>
			</tbody>
		  </table>
		</div> -->
    </div>
    <div style="clear:both"></div>
<script>
	function CloseNoti(iNotiType, iNotiId)
	{
		var strURL = base_url + "notification/notification/CloseNotiById";
		var bResult = AjaxLoadByPost(strURL,{'noti_type':iNotiType,'noti_id':iNotiId});
		if(bResult)
			location.reload();
	}
</script>