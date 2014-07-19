<?php if(!empty($arrProduct)) {?>
	<?php foreach($arrProduct as $idx=>$oProduct) { ?>
<div id="item_<?php echo $oProduct->productid;?>" class="item-list" for="chkProduct_<?php echo $oProduct->productid;?>">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td><input type="checkbox" style="margin: 0;" id="chkProduct_<?php echo $oProduct->productid;?>" value="<?php echo $oProduct->productid;?>" product_name="<?php echo $oProduct->name; ?>" ob_date="<?php echo $oProduct->ob_date; ?>" department_name="<?php echo $oProduct->department_name; ?>" department_id="<?php echo $oProduct->department_id; ?>" position="<?php echo $idx; ?>">&nbsp;<?php echo $oProduct->name; ?></td>
			<td class="t-right"><input type="button" value="" class="button-img-add" onclick="MoveProductToSelectedList('chkProduct_<?php echo $oProduct->productid;?>');"></td>
		</tr>
	</table>
</div>
	<?php } /* End foreach */ ?>
<?php } /* End if */ ?>