<link rel="StyleSheet" type="text/css" href="<?php echo $base_url; ?>asset/css/contact.css" />
<link href="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.css" rel="stylesheet" type="text/css">
<link href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_v2.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_no_selector.js"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui.customize-autocomplete.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/contact.action_link_cause.js"></script>

<div class="max-width">
    <div class="content">
    	<div id="divChosen">
    		
    		<table id="tblActionLink" cellpadding="0" cellspacing="0" border="0" width="100%">
    			<thead>
    			<tr>
    				<th colspan="2" class="t-left"><h3 style="padding-left: 20px; padding-top: 5px;">What cause do your call want to link to?</h3></th>
    			</tr>
    			</thead>
    			<tbody id="tbodytblActionLink">
    				<tr>
    					<td class="t-center">
    						<input type="radio" name="rdoLinkCause" id="rdoLinkCause" value="alert" />Alert
    					</td>
    					<td class="t-left">
    						<input type="radio" name="rdoLinkCause" id="rdoLinkCause" value="incident" />Incident
    					</td>
    				</tr>
    				<tr>
    					<td colspan="2" id="celLinkCause" class="t-center" style="padding: 20px;">
    						<!-- <select id="sltLinkCause" name="link_cause" class="wp400">
    							<option value="">&nbsp;</option>
    						</select> -->
    						<input class="wp600" id="iptLinkCause" name="link_cause" style="display: none;" />
    						<input type="hidden" id="hidAlertId" value="" />
    						<input type="hidden" id="hidTimeAlert" value="" />
    						<input type="hidden" id="hidIncidentId" value="" />
    					</td>
    				</tr>
    				<tr>
    					<td colspan="2" style="padding-bottom: 10px;" class="t-right">
    						<a id="cancel-link" class="hand-pointer">Skip & Next</a>
    						<a id="save-link" class="hand-pointer" style="margin-right: 15px;">Next</a>
	    					
    					</td>
    				</tr>
    			</tbody>
    		</table>
    	</div>
    </div>
</div>

<script type="text/javascript">
	var userid = <?php echo $iUserId ?>;
	var action = <?php echo $iAction ?>;
	var option = '<?php echo @$strOption ?>';
	$(document).ready(function() {		
		LinkCauseBindChange();
		
		$('#save-link').click(function() {
			if(action == 1) //call
				list_mobile_user(1, option);
			else if(action == 2) //send msg
				send_message(1, option);
		});
		$('#cancel-link').click(function() {
			if(action == 1) 
				list_mobile_user(0, option);
			else if(action == 2) //send msg
				send_message(0, option);
		});
	});
</script>