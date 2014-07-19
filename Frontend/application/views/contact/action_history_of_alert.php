<div id="divContactHistory">
	<table cellpadding="3" cellspacing="0" width="100%" style="font-size: 13px;">
		<tbody>
			<tr>
				<td class="t-left" style="width: 100px; padding-left: 20px;">Host name:</td>
				<td style="color: #DB2A36; font-weight: bold;"><?php if(isset($strZbxHost)) { echo @$strZbxHost; } ?></td>
			</tr>
			<tr>
				<td class="t-left" style="padding-left: 20px;">Source:</td>
				<td><?php if(isset($strSource)) { echo @$strSource; } ?></td>
			</tr>
			<tr>
				<td class="t-left" style="padding-left: 20px;">Alert:</td>
				<td style="color: #DB2A36; font-weight: bold;"><?php if(isset($strAlertMsg)) { echo urldecode($strAlertMsg); } ?></td>
			</tr>
			<tr>
				<td class="t-left" style="padding-left: 20px;">Time Alert:</td>
				<td><?php if (isset($strTimeAlert)) { echo date(FORMAT_MYSQL_DATETIME, $strTimeAlert); } ?></td>
			</tr>
		</tbody>
	</table><br />
	<table id="tblActionHistory" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
    <thead>
    	<tr class="table-title">
            <th class="t-center">Content</th>
            <th class="t-left">Action Status</th>
            <th class="t-center">User Action</th>
            <th class="t-center">Time Action</th>
        </tr>
    </thead>
    <tbody id="tbodytblActionHistory">
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
								echo "Sent SMS";
								break;
							default:
								echo "Call";
								break;
						} ?>
    				</td>
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