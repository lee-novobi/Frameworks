<?php $bFoundL1 = FALSE; ?>
<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <option value="">&nbsp</option>
    <?php  foreach ($arrAllAssignmentGroup as $oAssignmentGroup) { ?>
    <?php if(!empty($oSelectedAssignmentGroupL1)) {?>
    <option <?php if(strcasecmp($oSelectedAssignmentGroupL1->name, $oAssignmentGroup->name)===0) { $bFoundL1 = TRUE; ?>selected="selected"<?php } ?> value="<?php echo$oAssignmentGroup->name;  ?>"><?php echo $oAssignmentGroup->name; ?></option>
    <?php } else { ?>
    <option value="<?php echo$oAssignmentGroup->name;  ?>"><?php echo $oAssignmentGroup->name; ?></option>
    <?php } ?>
    <?php } ?>
</select>
<?php if(!$bFoundL1) {?>
<img title="Tool không tìm thấy Assignment Group L1." src="<?php echo $base_url ?>asset/images/icons/icon_ack.png">
<?php } ?>