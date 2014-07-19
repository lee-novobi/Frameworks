<div id="divContactHistory" style="border: 10px solid #fff;">
	<table id="tblContactHistory" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
    <thead>
    	<tr class="table-title">
            <th class="wp250 t-center">Cause</th>
            <th class="wp110 t-center">Time Alert</th>
            <th class="wp50 t-left">Action Type</th>
            <th class="wp180 t-center">Content</th>
            <th class="wp100 t-center">User Action</th>
            <th class="wp110 t-center">Time Action</th>
        </tr>
    </thead>
    <tbody id="tbodytblContactHistory">
    	<?php if(!empty($oHistory)) { ?>
    		<?php foreach($oHistory as $oRecord) { ?> 
    			<tr>
    				<td><?php if(!empty($oRecord->alert_id)) {
    					 	echo $oRecord->alert_message; 
						} elseif(!empty($oRecord->incident_id)) {
							echo $oRecord->incident_id;
						} elseif(!empty($oRecord->change_id)) {
							echo 'From Change '. $oRecord->change_id;
						} else {
							echo '';
						}?></td>
    				<td><?php echo isset($oRecord->time_alert) ? $oRecord->time_alert: ''; ?></td>
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
    				<td><?php echo htmlspecialchars($oRecord->content, ENT_QUOTES, 'utf-8');?></td>
    				<td><?php echo htmlspecialchars($oRecord->full_name, ENT_QUOTES, 'utf-8'); ?></td>
    				<td class="t-right"><?php echo @$oRecord->connect_start; ?></td>
    			</tr>
    			<?php } /* end foreach */ ?>
    	<?php } else { ?>
    		<tr>
    			<td colspan="5">No more data available in table.</td>
    		</tr>
    	<?php } ?>
    </tbody>
</div>