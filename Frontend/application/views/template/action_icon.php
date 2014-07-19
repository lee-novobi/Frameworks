<a id="<?php echo $id ?>" href="<?php echo $href ?>" title="<?php echo $title ?>" onclick="<?php echo $onclick ?>" name="<?php echo $name ?>" <?php if(isset($from_alert)) {?>style="margin-right: -10px;"<?php } ?>>
	<span class="<?php echo $class ?>">
		<img width="<?php echo $img_width ?>" src="<?php echo $icon_link ?>">
    </span>
    <?php if(!empty($display_call_num)) { ?>
    	<span><a class="noof-call hand-pointer" onclick="<?php echo $alert_history; ?>"><?php echo $noof_call ?></a></span>
    <?php } ?>
</a>
