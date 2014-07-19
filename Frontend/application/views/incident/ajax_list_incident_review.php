<table width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
    <thead>
    	<tr class="table-title">
    		<th colspan="10">
    			<p class="grid">Incident Closed</p>
    		</th>
    	</tr>
        <tr>
            <th class="t-center">Note</th>
            <th class="t-center">Contact<br />History</th>
            <th class="w3 t-center">Inc ID</th>
            <th class="t-center">Title</th>
            <th class="t-center">Outage Start</th>
            <th class="t-center">Outage End</th>
            <th class="t-center">ITSM Status</th>
            <th class="t-center">Created By</th>
            <th class="t-center">Action</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($arrInc as $oInc) {?>
    	<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
        	<td>&nbsp;</td>
        	<td>&nbsp;</td>
        	<td class="t-center">
            <a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>">
				<?php echo $oInc['itsm_incident_id'] ?>
            </a>
            </td>
        	<td class="t-left"> <a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>"> <?php echo htmlspecialchars($oInc['title']) ?> </a></td>
        	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
        	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_end']) ?></td>
        	<td class="t-right"><?php echo htmlspecialchars($oInc['status']) ?></td>
        	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
        	<td class="t-center" style="vertical-align: middle;" title="Phone">
        		<?php  $this->tpl->load_anchor_icon(array('img' => ICON_IMG_CALL)); ?>
        	 	<?php  $this->tpl->load_anchor_icon(array('img' => ICON_IMG_EDIT, 'onclick' => "PopUpUpdateIncident('".$oInc['itsm_incident_id']."')")); ?>
        	</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
