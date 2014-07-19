<div id="divContactHistory">
	<table id="tblContactHistory" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
    <thead>
    	<tr class="table-title">
            <th class="t-center">Content</th>
            <th class="t-left">Action Type</th>
            <th class="t-center">User Action</th>
            <th class="t-center">IncidentID</th>
            <th class="t-center">Connection Start Time</th>
            <th class="wp110 t-center">Call End Time/<br />SMS Sent Time</th>
        </tr>
    </thead>
    <tbody id="tbodytblContactHistory">
    	<?php if(!empty($oHistory)) { ?>
    		<?php foreach($oHistory as $oRecord) { ?> 
    			<tr>
    				<td><?php echo htmlspecialchars($oRecord->content, ENT_QUOTES, 'utf-8');?></td>
    				<td><?php switch($oRecord->action_type) {
							case CONTACT_ACTION_TYPE_CALL_SUCCESS:
								echo CALL_SUCCESS;
								break;
							case CONTACT_ACTION_TYPE_CALL_FAIL:
								echo CALL_FAIL;
								break;
							case CONTACT_ACTION_TYPE_SMS:
								echo "SMS";
								break;
							default:
								echo "Call";
								break;
						} ?>
    				</td>
    				<td><?php echo htmlspecialchars($oRecord->full_name, ENT_QUOTES, 'utf-8'); ?></td>
    				<td><?php echo $oRecord->incident_id; ?></td>
    				<td class="t-right"><?php echo @$oRecord->connect_start; ?></td>
    				<td class="t-right"><?php echo $oRecord->created_date; ?></td>
    			</tr>
    			<?php } /* end foreach */ ?>
    	<?php } else { ?>
    		<tr>
    			<td colspan="5">No more data available in table.</td>
    		</tr>
    	<?php } ?>
    </tbody>
</div>