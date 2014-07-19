<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
	<option value="">&nbsp </option>
    <?php foreach ($arrProduct as $oProduct) { ?>
    <option value="<?php echo $oProduct->name ?>"><?php echo $oProduct->name ?></option>
    <?php } ?>
</select>