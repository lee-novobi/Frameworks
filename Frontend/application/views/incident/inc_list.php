<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<div class="max-width">
    <div class="content">
    	<?php echo $message;?>
    	<!-- <div class="new-wrapper w100 t-right">
    		<a href="<?php echo $base_url?>admin/category/new_category"><button type="button" class="grey"><img src="<?php echo $base_url?>asset/images/icons/add.png" style="vertical-align:top; margin-right: 2px" />New</button></a>
    	</div> -->
    	<div id="tblIncidentList">
	    	<table width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
	            <thead>
	            	<tr class="table-title">
	            		<th colspan="8">
	            			<p class="grid">Incident Đang Xử Lý</p>
	            		</th>
	            	</tr>
	                <tr>
	                    <th class="w5 t-center">Note</th>
	                    <th class="w5 t-center">Contact<br />History</th>
	                    <th class="w3 t-center">Inc ID</th>
	                    <th class="t-center">Title</th>
	                    <th class="wp180 t-center">Outage Start</th>
	                    <th class="wp100 t-center">Created By</th>
	                    <th class="t-center wp80">Action</th>
	                </tr>
	            </thead>
	            <tbody>
	            	<?php foreach($arrInc as $oInc) {?>
	            	<?php if(strtolower($oInc['internal_status'])==INCIDENT_STATUS_CLOSED) {?>
	            	<?php if(!$arrSelectedShiftInfo['is_current_shift']) {?>
	                <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
	                	<td>&nbsp;</td>
	                	<td class="t-center" style="vertical-align: middle;"><?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_HISTORY, 'title' => 'Action history of '. @$oInc['itsm_incident_id'], 'onclick'=>'PopupContactHistory(\''.@$oInc['itsm_incident_id'].'\')')); ?></td>
	                	<td class="t-center <?php if (in_array($oInc['itsm_incident_id'], $arrUpdateFailInc)) { ?>bg-red<?php } ?>">
                        	<a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>" >
								<?php echo $oInc['itsm_incident_id'] ?>
                            </a>
                        </td>
	                	<td class="t-left">
                        <a class="hand-pointer" onclick="PopupViewDetail('<?php echo $oInc['itsm_incident_id']?>')">
							<?php echo htmlspecialchars($oInc['title']) ?>
                        </a>
                        </td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
	                	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
	                	<td class="t-center" style="vertical-align: middle;">
                             <?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'onclick' => 'PopupContact(\'' . $oInc['product'] . '\', \''. @$oInc['itsm_incident_id'] . '\')')); ?>
                             <?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_EDIT, 'onclick'=>'PopUpUpdateIncident(\''.@$oInc['itsm_incident_id'].'\')')); ?>
	                	</td>
	                </tr>
	                <?php }} else { ?>
	                <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
	                	<td>&nbsp;</td>
	                	<td class="t-center" style="vertical-align: middle;"><?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_HISTORY, 'title' => 'Action history of '. @$oInc['itsm_incident_id'], 'onclick'=>'PopupContactHistory(\''.@$oInc['itsm_incident_id'].'\')')); ?></td>
	                	<td class="t-center <?php if (in_array($oInc['itsm_incident_id'], $arrUpdateFailInc)) { ?>bg-pink<?php } ?>">
                        	<a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>" >
								<?php echo $oInc['itsm_incident_id'] ?>
                            </a>
                        </td>
	                	<td class="t-left">
                        	<a class="hand-pointer" onclick="PopupViewDetail('<?php echo $oInc['itsm_incident_id']?>')" >
								<?php echo htmlspecialchars($oInc['title']) ?>
                        	</a>
                        </td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
	                	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
	                	<td class="t-center" style="vertical-align: middle;">
                             <?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_CALL, 'onclick' => 'PopupContact(\'' . $oInc['product'] . '\', \''. @$oInc['itsm_incident_id'] . '\')')); ?>
                             <?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_EDIT, 'onclick'=>'PopUpUpdateIncident(\''.@$oInc['itsm_incident_id'].'\')')); ?>
	                	</td>
	                </tr>
	                <?php } ?>
	                <?php } ?>
	            </tbody>
			</table>
		</div>
		<div id="pp_tblIncidentList" class="easyui-pagination"></div>
		<input type="hidden" id="hidPage1" value="<?php echo $nPage1 ?>">
		<input type="hidden" id="hidPageSize1" value="<?php echo $nPageSize1 ?>">
		<?php if($arrSelectedShiftInfo['is_current_shift']) {?>
		<br />
		<div id="tblIncidentWithoutSubareaList">
			<table width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
	            <thead>
	            	<tr class="table-title">
	            		<th colspan="8">
	            			<p class="grid">Incident Đã Close Nhưng Chưa Có Area, Subarea</p>
	            		</th>
	            	</tr>
	                <tr>
	                    <th class="w5 t-center">Note</th>
	                    <th class="w5 t-center">Contact<br />History</th>
	                    <th class="w3 t-center">Inc ID</th>
	                    <th class="t-center">Title</th>
	                    <th class="wp180 t-center">Outage Start</th>
	                    <th class="wp100 t-center">Created By</th>
	                    <th class="t-center wp80">Action</th>
	                </tr>
	            </thead>
	            <tbody>
	            	<?php foreach($arrIncWithoutSubarea as $oInc) {?>
	                <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
	                	<td>&nbsp;</td>
	                	<td class="t-center" style="vertical-align: middle;"><?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_HISTORY, 'title' => 'Action history of '. @$oInc['itsm_incident_id'], 'onclick'=>'PopupContactHistory(\''.@$oInc['itsm_incident_id'].'\')')); ?></td>
	                	<td class="t-center <?php if (in_array($oInc['itsm_incident_id'], $arrUpdateFailInc)) { ?>bg-pink<?php } ?>">
                        	<a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>">
								<?php echo $oInc['itsm_incident_id'] ?>
                            </a>
                        </td>
	                	<td class="t-left">
						<a class="hand-pointer" onclick="PopupViewDetail('<?php echo $oInc['itsm_incident_id']?>')">
						<?php echo htmlspecialchars($oInc['title']) ?>
                         </a>
                         </td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
	                	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
	                	<td class="t-center" style="vertical-align: middle;">
                        	 <?php $this->tpl->load_anchor_icon( array('img'=> ICON_IMG_CALL, 'onclick' => 'PopupContact(\'' . $oInc['product'] . '\', \''. @$oInc['itsm_incident_id'] . '\')') ); ?>
                             <?php $this->tpl->load_anchor_icon( array('img'=> ICON_IMG_EDIT, 'onclick'=>'PopUpUpdateIncident(\''.@$oInc['itsm_incident_id'].'\')')); ?>
	                	</td>
	                </tr>
	                <?php } ?>
	            </tbody>
			</table>
		</div>
		<div id="pp_tblIncidentWithoutSubarea" class="easyui-pagination"></div>
		<input type="hidden" id="hidPage2" value="<?php echo $nPage2 ?>">
		<input type="hidden" id="hidPageSize2" value="<?php echo $nPageSize2 ?>">
		<br />
		<div id="tblIncidentClosedBySEList">
			<table width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
	            <thead>
	            	<tr class="table-title">
	            		<th colspan="8">
	            			<p class="grid">Incident Vừa Close Bởi SE</p>
	            		</th>
	            	</tr>
	                <tr>
	                    <th class="w5 t-center">Note</th>
	                    <th class="w5 t-center">Contact<br />History</th>
	                    <th class="w3 t-center">Inc ID</th>
	                    <th class="t-center">Title</th>
	                    <th class="wp180 t-center">Outage Start</th>
	                    <th class="wp100 t-center">Created By</th>
	                    <th class="t-center wp80">Action</th>
	                </tr>
	            </thead>
	            <tbody>
	            	<?php foreach($arrIncJustClosedBySE as $oInc) {?>
	                <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
	                	<td>&nbsp;</td>
	                	<td class="t-center" style="vertical-align: middle;"><?php $this->tpl->load_anchor_icon(array('img'=>ICON_IMG_HISTORY, 'title' => 'Action history of '. @$oInc['itsm_incident_id'], 'onclick'=>'PopupContactHistory(\''.@$oInc['itsm_incident_id'].'\')')); ?></td>
	                	<td class="t-center <?php if (in_array($oInc['itsm_incident_id'], $arrUpdateFailInc)) { ?>bg-pink<?php } ?>">
                        	<a href="<?php echo $base_url.'incident/incident/incident_detail?incidentid='.$oInc['itsm_incident_id'];?>">
								<?php echo $oInc['itsm_incident_id'] ?>
                            </a>
                        </td>
	                	<td class="t-left">
                        <a class="hand-pointer" onclick="PopupViewDetail('<?php echo $oInc['itsm_incident_id']?>')">
							<?php echo htmlspecialchars($oInc['title']) ?>
                        </a>
                        </td>
	                	<td class="t-right"><?php echo htmlspecialchars($oInc['outage_start']) ?></td>
	                	<td class="t-center"><?php echo htmlspecialchars($oInc['created_by']) ?></td>
	                	<td class="t-center" style="vertical-align: middle;">
	                		 <?php $this->tpl->load_anchor_icon( array('img'=> 'appbar.checkmark.thick.png', 'title'=> 'Không alert incident này nữa', 'class' => 'btn btn-tick', 'onclick'=>'ClosedAlertIncClosedbySE(\''.@$oInc['itsm_incident_id'].'\')')); ?>
	                		 <?php $this->tpl->load_anchor_icon( array('img'=> ICON_IMG_CALL, 'onclick' => 'PopupContact(\'' . $oInc['product'] . '\', \''. @$oInc['itsm_incident_id'] . '\')') ); ?>
                             <?php $this->tpl->load_anchor_icon( array('img'=> ICON_IMG_EDIT, 'onclick'=>'PopUpUpdateIncident(\''.@$oInc['itsm_incident_id'].'\')')); ?>
	                	</td>
	                </tr>
	                <?php } ?>
	            </tbody>
			</table>
		</div>
		<div id="pp_tblIncidentClosedBySE" class="easyui-pagination"></div>
		<input type="hidden" id="hidPage3" value="<?php echo $nPage3 ?>">
		<input type="hidden" id="hidPageSize3" value="<?php echo $nPageSize3 ?>">
		<?php } ?>
    </div>
</div>
<input type="hidden" id="hidQueryString" value="<?php echo $strQueryString ?>">
<script type="text/javascript">
var iTotal1    = <?php echo empty($arrInc)?0:$arrInc[0]['total'] ?>;
var iTotal2    = <?php echo empty($arrIncWithoutSubarea)?0:$arrIncWithoutSubarea[0]['total'] ?>;
var iTotal3    = <?php echo empty($arrIncJustClosedBySE)?0:$arrIncJustClosedBySE[0]['total'] ?>;
var iPageSize1 = <?php echo $nPageSize1 ?>;
var iPageSize2 = <?php echo $nPageSize2 ?>;
var iPageSize3 = <?php echo $nPageSize3 ?>;
var iPage1 = <?php echo $nPage1 ?>;
var iPage2 = <?php echo $nPage2 ?>;
var iPage3 = <?php echo $nPage3 ?>;
var strURLIncidentCtrl = '<?php echo $incident_directory ?>incident/';
var paging_type = '<?php echo $strIncListPagingType ?>';
var nShowingPopup     = 0;


function PopUpUpdateIncident(strIncidentId) {
	var nHeight = 700;
	var strURL = base_url + strURLIncidentCtrl + "incident_detail?layout=popup&incidentid=" + strIncidentId;
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

function ReloadAlertList(){
	location.reload();
}

function AutoRefresh(){
	setTimeout(function(){AutoRefresh()}, nInterval*1000);
	if(nShowingPopup == 0){
		ReloadAlertList();
	}
}

function PopupContact(strProduct, strIncidentId) {
	var nHeight = 700;
	var strURL = base_url + strURLIncidentCtrl + "contact_of_incident?product=" + strProduct + "&incident_id=" + strIncidentId;
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

function PopupContactHistory(strIncidentId) {
	var nHeight = 200;
	var strURL = base_url + strURLIncidentCtrl + "contact_action_history?incident_id=" + strIncidentId;
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

function PopupViewDetail(strIncidentId) {
    var nHeight = 700;
	var strURL = base_url + strURLIncidentCtrl + "view_incident_detail/" + strIncidentId;
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

function ClosedAlertIncClosedbySE(strIncidentId) {
	var strURL = base_url + strURLIncidentCtrl + "close_alert_incident_closed_by_se?incident_id=" + strIncidentId;
    var doConfirm = confirm('Are you sure you want to stop alert Incident "' + strIncidentId + '"?');
    if (doConfirm == true) {
    	$.ajax({
    		url: strURL
    		
    	}).done(function( response ) {
    		// alert(response);
    		// if(response == 'true') {
    			location.reload();
    		// } else {}
    	});
    }
}
</script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/incidentlist.pagination.js"></script>

