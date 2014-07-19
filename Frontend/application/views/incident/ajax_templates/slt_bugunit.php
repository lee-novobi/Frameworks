<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
	<option value="">&nbsp;</option>
    <?php foreach ($arrBugUnit as $oBugUnit) { ?>
    <option value="<?php echo $oBugUnit->unit_key ?>"><?php echo $oBugUnit->unit_name ?></option>
    <?php } ?>
</select>