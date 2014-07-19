<?php global $arrDefined; ?>
<table id="tblContactList" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
    <thead>
    	<tr class="table-title">
    		<th colspan="6">
    			<p class="drag">Contact Point</p>
    		</th>
    	</tr>
        <tr>
            <th class="t-center">Fullname</th>
            <th class="t-left">Email</th>
            <th class="t-center">Mobile</th>
            <th class="t-center">Ext</th>
            <th class="t-center">Role</th>
            <th class="t-center">Action</th>
        </tr>
    </thead>
    <tbody id="tbodytblContactList">
        <?php if(!empty($arrUsers)) {?>
		<?php foreach($arrUsers as $index=>$objOneUser) { ?>
		<tr <?php if($index % 2 == 0) { ?>class="even"<?php } ?>class="odd">
			<td class="t-left" style="padding-left: 8px"><?php echo htmlentities($objOneUser['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
			<td class="t-left"><?php echo htmlentities($objOneUser['email']); ?></td>
			<td class="t-left"><?php echo htmlentities($objOneUser['mobile']); ?></td>
			<td class="t-center"><?php echo htmlentities($objOneUser['ext']); ?></td>
			<td class="t-center"><?php echo htmlentities($objOneUser['role']); ?></td>
			<td class="t-center" title="<?php if(is_null($objOneUser['vng_dept'])) { echo NOT_HR; } elseif ($objOneUser['vng_dept'] != $objOneUser['sdk_dept'] && !in_array($objOneUser['vng_dept'], $arrDefined['department_special'])) {
		          echo USER_SDK . htmlentities($objOneUser['sdk_dept']) . USER_HR . htmlentities($objOneUser['vng_dept']) . ')'; 
		     } ?>" style="<?php if(is_null($objOneUser['vng_dept'])) { ?>vertical-align: middle; background-color: #FF7575<?php } elseif ($objOneUser['vng_dept'] != $objOneUser['sdk_dept'] && !in_array($objOneUser['vng_dept'], $arrDefined['department_special'])) { ?>
		          vertical-align: middle;background-color: #FFFF94
		     <?php } ?>">
		        <?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'id' => 'list_mobile_user_' . htmlentities($objOneUser['userid']), 'onclick' => 'list_mobile_user(' . htmlentities($objOneUser['userid']) . ');')); ?>
			    <?php $this->tpl->load_anchor_icon(array('img'=> 'appbar.message.smiley.png', 'class' => 'btn btn-bluesky', 'title' => 'Click to send sms', 'id' => 'send_sms_' . htmlentities($objOneUser['userid']), 'onclick' => 'send_message(' . htmlentities($objOneUser['userid']) . ');')); ?>
			</td>
		</tr>
		<?php } /* end foreach */ ?>
		<?php } else { ?>
		<tr>
		     <td colspan="6">No more data available in table.</td>
		</tr>
		<?php } /* end else */ ?>
    </tbody>
</table>