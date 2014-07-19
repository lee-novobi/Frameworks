<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
	<option value="">&nbsp </option>
    <?php foreach ($arrSubarea as $oSubarea) { ?>
    <option value="<?php echo $oSubarea->name ?>"><?php echo $oSubarea->name ?></option>
    <?php } ?>
</select>