<table width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
    <thead>
    	<tr class="table-title">
    		<th colspan="8">
    			<p class="grid">Incident Đang Xử Lý</p>
    		</th>
    	</tr>
        <tr>
            <th class="w5 t-center">Note</th>
            <th class="w5 t-center">Contact<br />History</th>
            <th class="w3 t-center">Inc ID</th>
            <th class="t-center">Title</th>
            <th class="wp180 t-center">Outage Start</th>
            <th class="wp100 t-center">Created By</th>
            <th class="t-center wp80">Action</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($arrInc as $oInc) {?>
    	<?php if(strtolower($oInc['internal_status'])==INCIDENT_STATUS_CLOSED) {?>
    	<?php if(!$arrSelectedShiftInfo['is_current_shift']) {?>
        <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
        	<td>&nbsp;</td>
        	<td class="t-center" style="vertical-align: middle;"><?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_HISTORY, 'title' => 'Action history of '. @$oInc['itsm_incident_id'], 'onclick'=>'PopupContactHistory(\''.@$oInc['itsm_incident_id'].'\')')); ?></td>
        	<td class="t-center <?php if (in_array($oInc['itsm_incident_id'], $arrUpdateFailInc)) { ?>bg-pink<?php } ?>"><a href="<?php echo $base_url.'incident/incident/incident_detail?'.$oInc['itsm_incident_id'];?>"><?php echo $oInc['itsm_incident_id'] ?></a></td>
        	<td class="t-left">
			<a class="hand-pointer" onclick="PopupViewDetail('<?php echo $oInc['itsm_incident_id']?>')">
				<?php echo htmlspecialchars($oInc['title']) ?>
            </a>
            </td>
        	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
        	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
        	<td class="t-center" style="vertical-align: middle;" title="Action">
        		<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'onclick' => 'PopupContact(\'' . $oInc['product'] . '\', \''. @$oInc['itsm_incident_id'] . '\')')); ?>
                <?php  $this->tpl->load_anchor_icon(array('img' => ICON_IMG_EDIT, 'onclick' => "PopUpUpdateIncident('".$oInc['itsm_incident_id']."')")); ?>
        	</td>
        </tr>
        <?php }} else { ?>
        <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
        	<td>&nbsp;</td>
        	<td class="t-center" style="vertical-align: middle;"><?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_HISTORY, 'title' => 'Action history of '. @$oInc['itsm_incident_id'], 'onclick'=>'PopupContactHistory(\''.@$oInc['itsm_incident_id'].'\')')); ?></td>
        	<td class="t-center <?php if (in_array($oInc['itsm_incident_id'], $arrUpdateFailInc)) { ?>bg-pink<?php } ?>">
            <a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>">
				<?php echo $oInc['itsm_incident_id'] ?>
            </a>
            </td>
        	<td class="t-left">
            <a class="hand-pointer" onclick="PopupViewDetail('<?php echo $oInc['itsm_incident_id']?>')">
				<?php echo htmlspecialchars($oInc['title']) ?>
            </a>
            </td>
        	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
        	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
        	<td class="t-center" style="vertical-align: middle;">
            	<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'onclick' => 'PopupContact(\'' . $oInc['product'] . '\', \''. @$oInc['itsm_incident_id'] . '\')')); ?>
                <?php  $this->tpl->load_anchor_icon(array('img' => ICON_IMG_EDIT, 'onclick' => "PopUpUpdateIncident('".$oInc['itsm_incident_id']."')")); ?>
        		<!--<a href="#"><img src="<?php echo $base_url?>asset/images/icons/phone.png" width="18px" /></a>
        		<a onclick="PopUpUpdateIncident('<?php echo @$oInc['itsm_incident_id'] ?>')" href="#"><img src="<?php echo $base_url?>asset/images/icons/edit.png" width="18px" /></a>
        		-->
            </td>
        </tr>
        <?php } ?>
        <?php } ?>
    </tbody>
</table>
