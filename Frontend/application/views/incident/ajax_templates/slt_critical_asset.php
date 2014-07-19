<select id="<?php echo $strTagId ?>" name="<?php echo $strTagName ?>" class="<?php echo $strTagClass ?>">
	<option value="">&nbsp </option>
	<?php if(!empty($arrCriticalAsset)) { ?>
    <?php foreach ($arrCriticalAsset as $oCriticalAsset) { ?>
    <option value="<?php echo $oCriticalAsset->critical_asset ?>"><?php echo $oCriticalAsset->critical_asset ?></option>
    <?php } ?>
    <?php } /* end if */ ?>
</select>
<?php if(!empty($iRequireCA)) { ?>
	<span id="require_critical_asset" class="require_mark">*</span>
<?php } ?>