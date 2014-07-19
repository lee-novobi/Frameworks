<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
    <option area="" value="">&nbsp </option>
	<?php foreach ($arrAffectedCI as $oAffectedCI) { ?>
    <option relationship_name="<?php echo $oAffectedCI->relationship_name ?>" value="<?php echo $oAffectedCI->related_ci ?>"><?php echo $oAffectedCI->related_ci ?></option>
    <?php } ?>
</select>