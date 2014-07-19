<select id="<?php echo $strTagId; ?>" class="<?php echo $strTagClass; ?>" name="<?php echo $strTagName; ?>">
	<option value="">&nbsp; </option>
	<?php if(!empty($arrAssigneesL1)) { ?>
	<?php foreach($arrAssigneesL1 as $idx=>$oAssignee) { ?>
	<?php if(!empty($oAssigneeL1)) { ?>
		<option <?php if($oAssigneeL1->name === $oAssignee->name) {?>selected="selected"<?php } ?> value="<?php echo $oAssignee->name; ?>"><?php echo $oAssignee->name; ?></option>
	<?php } else { ?>
		<option value="<?php echo $oAssignee->name; ?>"><?php echo $oAssignee->name; ?></option>
		<?php } /* End else */ ?>
	<?php } } ?>
    
</select>