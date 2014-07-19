<div class="divGroupWrapper">	
	<?php foreach ($arrProducts as $k=>$v) { ?>
        <a class="metro-link mright-1 odd" title="<?php echo $k?>" onclick="SubmitForm('<?php echo $k?>')">
        <span class="spanCountValue" id="notificationsCountValue"><?php echo $v?></span> <?php echo $k?> </a> 
	<?php } ?>
</div>
<div class="clear"></div>