<?php global $arrDefined ?>
<link href="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.css" rel="stylesheet" type="text/css">
<link href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/contact.css" />
<style>
	table>tbody>tr>th {
		background-color: #3B5998;
		color: #fff;
		border-top: 1px solid #CCCCCC;
		padding-right: 15px;
	}
</style>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui.customize-autocomplete.js" type="text/javascript"></script>
<div class="full-width">
	<div class="content t-left">
		<table class="list-zebra data-grid" width="100%" cellpadding="0" cellspacing="0">
	        <thead>
	        	<tr class="table-title">
	            	<th colspan="4"><p class="drag">CHANGE DETAIL</p></th>
	          	</tr>
	        </thead>
	        <tbody>
	        	<?php if(!empty($oChange)) { ?>
	          	<tr>
	            	<th class="w15 t-right">ChangeID</th>
	            	<td><?php echo $oChange->itsm_change_id ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Title</th>
	            	<td><?php $oChange->title=trim($oChange->title); echo isset($oChange->title)?$oChange->title:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Change Reason</th>
	            	<td><?php $oChange->description=trim($oChange->description); echo isset($oChange->description)?$oChange->description:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Plan/Instruction</th>
	            	<td><?php $oChange->plan=trim($oChange->plan); echo isset($oChange->plan)?$oChange->plan:'' ?></td>
	          	</tr>
	          	<tr>
	           		<th class="t-right">Service</th>
	            	<td><?php $oChange->service=trim($oChange->service); echo isset($oChange->service)?$oChange->service:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Assignment Group</th>
	            	<td><?php $oChange->assignment_group=trim($oChange->assignment_group); echo isset($oChange->assignment_group)?$oChange->assignment_group:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Change Coordinator</th>
	            	<td><?php $oChange->change_coordinator=trim($oChange->change_coordinator); echo isset($oChange->change_coordinator)?$oChange->change_coordinator:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Informed Groups</th>
	            	<td><?php $oChange->informed_groups=trim($oChange->informed_groups); echo isset($oChange->informed_groups)?$oChange->informed_groups:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Created Date</th>
	            	<td><?php $oChange->created_date=trim($oChange->created_date); echo isset($oChange->created_date)?$oChange->created_date:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Planned Start</th>
	            	<td><?php $oChange->planned_start=trim($oChange->planned_start); echo isset($oChange->planned_start)?$oChange->planned_start:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Planned End</th>
	            	<td><?php $oChange->planned_end=trim($oChange->planned_end); echo isset($oChange->planned_end)?$oChange->planned_end:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Downtime Start</th>
	            	<td><?php $oChange->down_start=trim($oChange->down_start); echo isset($oChange->down_start)?$oChange->down_start:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Downtime End</th>
	            	<td><?php $oChange->down_end=trim($oChange->down_end); echo isset($oChange->down_end)?$oChange->down_end:'' ?></td>
	          	</tr>
	          	<tr>
	            	<th class="t-right">Status</th>
	            	<td><?php $oChange->status=trim($oChange->status); echo isset($oChange->status)?$oChange->status:'' ?></td>
	          	</tr>
	          	<?php if(isset($strChangeView) && $strChangeView !== $arrDefined['change_view']['F']) { ?>
	          	<tr>
	            	<td colspan="2" class="t-right">
	            		<a id="lnkFollow" class="peter-river-flat-button hand-pointer">Follow</a>
	            		<?php if($strChangeView === $arrDefined['change_view']['N']) { ?>
						<a id="lnkDone" class="emerald-flat-button hand-pointer">Done</a>
						<?php } ?>
	            	</td>
	          	</tr>
	          	<?php } ?>
	          	<?php } else { ?>
	          	<tr>
	          		<td colspan="14">No more information.</td>
	          	</tr> 
	          	<?php } ?>
	        </tbody>
        </table>
  	</div>
</div>
<script type="text/javascript">
	var change_id = '<?php if(!empty($oChange)) { echo @$oChange->itsm_change_id; } ?>';
	var change_view = '<?php if(isset($strChangeView)) { echo trim($strChangeView); } ?>';
	function ProcessMoveChangeJSONResult(strURL, strMessage) {
		var strResult = AjaxLoadByPost(strURL, {'change_id':change_id, 'change_view': change_view});
		if(strResult === '<?php echo UPDATED_STATUS ?>') {
			parent.window.location.reload();
		} else if(strResult === '<?php echo NOT_UPDATED_STATUS ?>') {
			alert(strMessage);
			parent.window.location.reload();
		}
	}
	
	$(document).ready(function() {
		$('#lnkFollow').on('click', function(){
			var strURL = '<?php echo $base_url ?>change/change/update_follow_change';
			ProcessMoveChangeJSONResult(strURL, 'Error! Change is not followed!');
		});
		
		<?php if(isset($strChangeView) && $strChangeView === $arrDefined['change_view']['N']) { ?>
		$('#lnkDone').on('click', function(){
			var strURL = '<?php echo $base_url ?>change/change/move_all_changes';
			ProcessMoveChangeJSONResult(strURL, 'Error!!');
		});
		<?php } ?>
	});
</script>