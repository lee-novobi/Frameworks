<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<div class="max-width">
    <div class="content">
    	<?php echo $message;?>
    	<!-- <div class="new-wrapper w100 t-right">
    		<a href="<?php echo $base_url?>admin/category/new_category"><button type="button" class="grey"><img src="<?php echo $base_url?>asset/images/icons/add.png" style="vertical-align:top; margin-right: 2px" />New</button></a>
    	</div> -->
    	<div id="tblIncidentReviewList">
	    	<table width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
	            <thead>
	            	<tr class="table-title">
	            		<th colspan="10">
	            			<p class="grid">Incident Closed</p>
	            		</th>
	            	</tr>
	                <tr>
	                    <th class="t-center">Note</th>
	                    <th class="t-center">Contact<br />History</th>
	                    <th class="w3 t-center">Inc ID</th>
	                    <th class="t-center">Title</th>
	                    <th class="t-center">Outage Start</th>
	                    <th class="t-center">Outage End</th>
	                    <th class="t-center">ITSM Status</th>
	                    <th class="t-center">Created By</th>
	                    <th class="t-center">Contact</th>
	                    <th class="t-center">Edit</th>
	                </tr>
	            </thead>
	            <tbody>
	            	<?php foreach($arrInc as $oInc) {?>
	            	<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
	                	<td>&nbsp;</td>
	                	<td>&nbsp;</td>
	                	<td class="t-center"><?php echo $oInc['itsm_incident_id'] ?></td>
	                	<td class="t-left"><?php echo htmlspecialchars($oInc['title']) ?></td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_end']) ?></td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['status']) ?></td>
	                	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
	                	<td class="t-center" style="vertical-align: middle;" title="Phone">
	                		<a href="#"><img src="<?php echo $base_url?>asset/images/icons/phone.png" width="18px" /></a>
	                	</td>
	                	<td class="t-center" style="vertical-align: middle;" title="Edit">
	                		<a href="#"><img src="<?php echo $base_url?>asset/images/icons/edit.png" width="18px" /></a>
	                	</td>
	                </tr>
	                <?php } ?>
	            </tbody>
			</table>
		</div>
		<div id="pp_tblIncidentList" class="easyui-pagination"></div>
		<input type="hidden" id="hidPage1" value="<?php echo $nPage1 ?>">
		<input type="hidden" id="hidPageSize1" value="<?php echo $nPageSize1 ?>">
    </div>
</div>
<input type="hidden" id="hidQueryString" value="<?php echo $strQueryString ?>">
<script type="text/javascript">
var iTotal1    = <?php echo empty($arrInc)?0:$arrInc[0]['total'] ?>;
var iTotal2    = <?php echo empty($arrChg)?0:$arrChg[0]['total'] ?>;
var iTotal3    = <?php echo empty($arrTsk)?0:$arrTsk[0]['total'] ?>;
var iPageSize1 = <?php echo $nPageSize1 ?>;
var iPageSize2 = <?php echo $nPageSize2 ?>;
var iPageSize3 = <?php echo $nPageSize3 ?>;
var iPage1 = <?php echo $nPage1 ?>;
var iPage2 = <?php echo $nPage2 ?>;
var iPage3 = <?php echo $nPage3 ?>;
var strURLIncidentCtrl = '<?php echo $incident_directory ?>incident/';
var paging_type = '<?php echo $strIncListPagingType ?>';
</script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/review.pagination.js"></script>