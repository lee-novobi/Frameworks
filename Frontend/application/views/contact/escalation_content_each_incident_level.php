<?php global $arrDefined; ?>
<table id="tblEachIncidentEscalation" cellpadding="0" cellspacing="0" class="list">
	<thead>
		<tr>
			<th class="t-center" width="5%">Level Escalation</th>
			<th class="t-center" width="16%">Fullname</th>
			<th class="t-left" width="10%">Email</th>
			<th class="t-center" width="10%">Mobile</th>
			<th class="t-center" width="5%">Ext</th>
			<th class="t-center" width="8%">Duration</th>
			<th class="t-center w5">Contact<br />History</th>
			<th class="t-center" width="8%">Action</th>
		</tr>
	</thead>
	<tbody id="tbodytblEachIncidentEscalation">
		<?php if(!empty($arrEscalationUsers)) {?>
		<?php foreach ($arrEscalationUsers as $index => $objOneEscalationUser) { ?>
		<?php if($objOneEscalationUser['level_incident_id'] == $noIncidentLevel) { ?>
		<tr>
			<td class="t-center"><?php echo LEVEL_ESCALATION_PREFIX . htmlentities($objOneEscalationUser['level_escalation_id']); ?></td>
			<td><?php echo htmlentities($objOneEscalationUser['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
			<td><?php echo htmlentities($objOneEscalationUser['email']); ?></td>
			<td><?php echo htmlentities($objOneEscalationUser['mobile']); ?></td>
			<td class="t-center"><?php echo htmlentities($objOneEscalationUser['ext']); ?></td>
			<td class="t-center"><?php echo htmlentities($objOneEscalationUser['duration']); ?></td>
			<td class="t-center"><?php $this->tpl->load_anchor_icon(array('id'=> 'view_history_' . htmlentities($objOneEscalationUser['userid']), 'img'=>ICON_IMG_HISTORY, 'title' => 'View action history', 'onclick'=>'ViewActionHistory('. htmlentities($objOneEscalationUser['userid']) . ')')); ?></td>
			<td class="t-center" title="<?php if(is_null($objOneEscalationUser['vng_dept'])) { echo NOT_HR; } elseif ($objOneEscalationUser['vng_dept'] != $objOneEscalationUser['sdk_dept'] && !in_array($objOneEscalationUser['vng_dept'], $arrDefined['department_special'])) {
          echo USER_SDK . htmlentities($objOneEscalationUser['sdk_dept']) . USER_HR . htmlentities($objOneEscalationUser['vng_dept']) . ')'; 
     } ?>" style="<?php if(is_null($objOneEscalationUser['vng_dept'])) { ?>vertical-align: middle; background-color: #FF7575<?php } elseif ($objOneEscalationUser['vng_dept'] != $objOneEscalationUser['sdk_dept'] && !in_array($objOneEscalationUser['vng_dept'], $arrDefined['department_special'])) { ?>
          vertical-align: middle;background-color: #FFFF94
     <?php } ?>">
	   	<?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'id' => 'link_action_' . htmlentities($objOneEscalationUser['userid']), 'onclick' => 'ChooseCauseToLinkAction(' . htmlentities($objOneEscalationUser['userid']) . ', 1)')); ?>
	    <?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.message.smiley.png', 'class' => 'btn btn-bluesky', 'title' => 'Click to send sms', 'id' => 'send_sms_' . htmlentities($objOneEscalationUser['userid']), 'onclick' => 'ChooseCauseToLinkAction(' . htmlentities($objOneEscalationUser['userid']) . ', 2)')); ?>
	    </td>
		</tr>
		<?php } /* end if in loop */ ?>
		<?php } /* end foreach */ ?>
		<?php } else {?>
		<tr>
			<td colspan="8" class="t-center">No more data available in table.</td>
		</tr>
		<?php } /* end else */ ?>
	</tbody>
</table>