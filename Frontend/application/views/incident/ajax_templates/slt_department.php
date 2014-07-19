<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <?php  foreach ($arrDepartment as $oDepartment) { ?>
    <option <?php if ($strSelectedDepartment == $oDepartment->name) { ?>selected="selected"<?php } ?> value="<?php echo $oDepartment->name;  ?>"><?php echo $oDepartment->name ?></option>
    <?php } ?>
</select>