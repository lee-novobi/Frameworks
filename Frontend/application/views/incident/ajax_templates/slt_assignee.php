<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <?php  foreach ($arrAssignee as $oAssignee) { ?>
    <option value="<?php echo $oAssignee->name;  ?>"><?php echo $oAssignee->name; ?></option>
    <?php } ?>
</select>