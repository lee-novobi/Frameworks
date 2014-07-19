<?php global $arrDefined; ?>
<link href="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.css" rel="stylesheet" type="text/css">
<link href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" rel="stylesheet" type="text/css">
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui.customize-autocomplete.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>
<div class="full-width">
  <div class="content t-left"> <?php echo $message;?>
  <div id="content-header" class="content-header">
    	<h1>Update Incident</h1> 
    </div>
    <form id="fCreateIncident" method="post" action="<?php echo $base_url?>incident/incident/update_incident_status_submit" onsubmit="return onValidation()">
      <input type="hidden" name="itsm_incident_id" value="<?php echo $oIncident['itsm_incident_id'] ?>"/>
      <table class="table_01" width="100%" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <th width="200">ITSM ID</th>
            <td><strong><?php echo $oIncident['itsm_incident_id'] ?></strong></td>
          </tr>
          <tr>
            <th>Status</th>
            <td><select name="incident_status" id="drpstatus" class="w120">
                <!-- <option value="Closed" selected="selected">Closed</option> -->
                <option value="Resolved">Resolved</option>
                <option value="Reopen">Reopen</option>
              </select></td>
          </tr>
          <tr>
            <th><span class="require_mark">*</span>&nbsp;Outage End Date<br />
              (yyyy-mm-dd H:m:s)</th>
            <td><input type="text" name="outage_end" id="outage_end" value="<?php echo (isset($oIncident['outage_end']) && @$oIncident['outage_end'] != "") ? $oIncident['outage_end'] : date('Y-m-d H:i:s') ?>"></td>
          </tr>
          <tr>
             <th><span class="require_mark">*</span>&nbsp;Area</th>
             <td><select id="drparea" name="area" class="wp120">
                <option value="">&nbsp </option>
                <?php foreach ($arrArea as $oArea) { ?>
                <option value="<?php echo $oArea->name?>" <?php if($oIncident['area'] == $oArea->name) { echo 'selected="selected"'; } ?>><?php echo $oArea->name ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <th><span class="require_mark">*</span>&nbsp;Sub Area</th>
            <td id="celSubArea"><select id="drpsubarea" name="subarea" class="wp200">
                <option area="" value="">&nbsp </option>
                <?php foreach ($arrSubarea as $oSubarea) { ?>
                <option area="<?php echo strtolower($oSubarea->area) ?>" value="<?php echo $oSubarea->name ?>" <?php if($oIncident['subarea'] == $oSubarea->name) { echo 'selected="selected"'; } ?>><?php echo $oSubarea->name ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <th valign="top"><span class="require_mark">*</span>&nbsp;Solution</th>
            <td><textarea name="solution" id="solution" class="w600" rows="5"><?php echo @$oIncident['solution'] ?></textarea></td>
          </tr>
          <tr>
            <th><span class="require_mark">*</span>&nbsp;Closure Code</th>
            <td><select name="closurecode" id="drpclosurecode" class="w180">
                <option value="">&nbsp;</option>
                <option value="Automatically Closed" <?php if(@$oIncident['closurecode'] == 'Automatically Closed') { ?>selected="selected"<?php } ?>>Automatically Closed</option>
                <option value="Not Reproducible" <?php if(@$oIncident['closurecode'] == 'Not Reproducible') { ?>selected="selected"<?php } ?>>Not Reproducible</option>
                <option value="Out of Scope"  <?php if(@$oIncident['closurecode'] == 'Out of Scope') { ?>selected="selected"<?php } ?>>Out of Scope</option>
                <option value="Request Rejected" <?php if(@$oIncident['closurecode'] == 'Request Rejected') { ?>selected="selected"<?php } ?>>Request Rejected</option>
                <option value="Solved by Change/Service Request" <?php if(@$oIncident['closurecode'] == 'Solved by Change/Service Request') { ?>selected="selected"<?php } ?> >Solved by Change/Service Request</option>
                <option value="Solved by User Instruction"  <?php if(@$oIncident['closurecode'] == 'Solved by User Instruction') { ?>selected="selected"<?php } ?>>Solved by User Instruction</option>
                <option value="Solved by Workaround"  <?php if(@$oIncident['closurecode'] == 'Solved by Workaround') { ?>selected="selected"<?php } ?>>Solved by Workaround</option>
                <option value="Unable to solve"  <?php if(@$oIncident['closurecode'] == 'Unable to solve') { ?>selected="selected"<?php } ?>>Unable to solve</option>
                <option value="Withdrawn by User" <?php if(@$oIncident['closurecode'] == 'Withdrawn by User') { ?>selected="selected"<?php } ?>>Withdrawn by User</option>
              </select></td>
          </tr>
          <tr>
            <th>Resolved by</th>
            <td><select name="resolvedby" id="drpresolvedby" class="w180">
                <option value="">&nbsp;</option>
                <option value="SDK" <?php if($oIncident['resolved_by'] == 'SDK') { ?>selected="selected"<?php } ?>>SDK</option>
                <option value="SE" <?php if($oIncident['resolved_by'] == 'SE') { ?>selected="selected"<?php } ?>>SE</option>
                <option value="SDK and SE" <?php if($oIncident['resolved_by'] == 'SDK and SE') { ?>selected="selected"<?php } ?>>SDK and SE</option>
              </select></td>
          </tr>
          <tr>
            <th valign="top">SDK Note</th>
            <td><textarea name="sdk_note" id="sdknote" class="w600" rows="5"><?php echo @$oIncident['sdknote'] ?></textarea></td>
          </tr>
          <tr>
            <th colspan="2" align="center"> 
            	<input id="btnSubmit" type="submit" value="Save" class="" onclick="return onValidation();">
                <input type="button" value="Cancel" onclick="javascript:window.location='<?php echo $base_url.'incident/incident/incident_detail?layout='.@$_REQUEST['layout'].'&incidentid='.$oIncident['itsm_incident_id']?>';">
            </th>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
	<script type="text/javascript">
	
	$(document).ready(function () {
		$("#drpstatus").bind('change', function(){
			EnableRelatedFields();
		});

		$('#outage_end').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm:ss'
		});
        
        AreaBindChange();
	});
    
    function AreaBindChange() {
	   $("#drparea").bind('change', function(){
    		var area_name = $('#drparea').val();
    		var url = base_url + 'incident/incident/ajax_get_subarea_of_area/subarea/drpsubarea/wp200?area=all';
    		if ( area_name != "" )
    		{
    			area_name = encodeURIComponent(area_name);
    			url = base_url + 'incident/incident/ajax_get_subarea_of_area/subarea/drpsubarea/wp200?area=' + area_name;
    		}
    		var strHtml = AjaxLoad(url);
    		$("#celSubArea").html(strHtml);
    	});
    }

	function EnableRelatedFields(){
		var status = $('#drpstatus').val();
		status = status.toLowerCase();
		if(status=='closed' || status=='resolved'){
			$('#drpclosurecode').removeAttr('disabled');
			$('#solution').removeAttr('disabled');
			$('#outage_end').removeAttr('disabled', '');
		} else if(status=='open'){
			$('#outage_end').attr('disabled', '');
			$('#drpclosurecode').attr('disabled', '');
			$('#solution').attr('disabled', '');
		} else {
			$('#drpclosurecode').attr('disabled', '');
			$('#solution').attr('disabled', '');
			$('#outage_end').removeAttr('disabled', '');
		}
	}

	function onValidation(){
		var message = '';
		var status  = $('#drpstatus').val();

		status      = status.toLowerCase();

		if(status=='closed' || status=='resolved'){
			if($("#outage_end").val()==''){
				message += 'Vui lòng nhập Outage End Date\n';
			}
			if($("#solution").val()==''){
				message += 'Vui lòng nhập Solution\n';
			}
			if($("#drpclosurecode").prop("selectedIndex")==0){
				message += 'Vui lòng chọn Closure Code\n';
			}
            if($("#drparea").prop("selectedIndex")==0){
				message += 'Vui lòng chọn Area\n';
			}
             if($("#drpsubarea").prop("selectedIndex")==0){
				message += 'Vui lòng chọn Subarea\n';
			}
		}

		if(message == ''){
			return true;
		} else {
			alert(message);
			return false;
		}
	}
	
</script> 
