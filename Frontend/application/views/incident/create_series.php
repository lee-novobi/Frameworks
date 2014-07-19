<?php global $arrDefined; ?>
<link href="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.css" rel="stylesheet" type="text/css">
<link href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" rel="stylesheet" type="text/css">
<link href="<?php echo $base_url?>asset/css/bootstrap.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $base_url?>asset/css/bootstrap-responsive.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $base_url?>asset/css/metro-bootstrap.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $base_url?>asset/css/metro.css" type="text/css" rel="stylesheet" />
<style type="text/css">
	input:focus, textarea:focus, select:focus {
		border-color: #FF6600 !important;
	}
	input:active {
		border-color: #FB4E0B !important;
	}
	input:hover, textarea:hover, select:hover {
		border-color: #1BA1E2 !important;
	}
	.table_02 {
		font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
	}
	.table_02>tbody>tr>th {
		background-color: #2460DD;
	}
	#tblSelectedItems>thead>tr>th {
		background-color: #6AAAF0;
	}
	.ui-datepicker .ui-datepicker-buttonpane button {
		padding: .1em .7em;
	}
</style>
<script src="<?php echo $base_url ?>asset/js/jquery.ajaxqueue.v2.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui-1-9-2.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui.customize-autocomplete.js" type="text/javascript"></script>
<script src="<?php echo $base_url ?>asset/js/ui/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/metro-base.js"></script>
<script src="<?php echo $base_url ?>asset/js/jquery.blockUI.js" type="text/javascript" ></script>

<div class="heading">
  <h3 style="line-height: normal; float: left;">Open Incident Ticket</h3>&nbsp;&nbsp;&nbsp;<img id="waitingIcon" style="display: none" src="<?php echo $base_url ?>asset/images/icons/lightbox-ico-loading.gif">
  <div class="resBtnSearch"> <a href="#"><span class="icon16 brocco-icon-search"></span></a> </div>
  <!-- <div class="search">
    <form id="searchform" action="search.html" />
    
    <input type="text" id="tipue_search_input" class="top-search" placeholder="Search here ..." />
    <input type="submit" id="tipue_search_button" class="search-btn" value="" />
    </form>
  </div> -->
  <!-- End search -->
  
  <!-- <ul class="breadcrumb">
    <li>You are here:</li>
    <li> <a href="#" class="tip" title="back to dashboard"> <span class="icon16 icomoon-icon-screen"></span> </a> <span class="divider"> <span class="icon16 icomoon-icon-arrow-right"></span> </span> </li>
    <li class="active"> Incidents <span class="divider"> <span class="icon16 icomoon-icon-arrow-right"></span> </span> </li>
    <li class="active"> Create Series </li>
  </ul> -->
</div>
<!-- End .heading-->

<div class="row-fluid">
  <div class="span12">
    <div style="margin-bottom: 20px;" class="widget">
      <ul class="nav nav-tabs full-widget bg-cyan" id="myTab1">
        <li class="active"><a data-toggle="tab" href="#affected_service"><span style="font-weight: bold;">Affected Service</span></a></li>
        <li class=""><a data-toggle="tab" href="#inc_content"><span style="font-weight: bold;">Incident Content</span></a></li>
      </ul>
      <div class="tab-content">
      
        <div id="affected_service" class="tab-pane fade active in">
        <!-- <form id="create_series_form" method="post" name="create_series_form" action="<?php echo $base_url ?>index.php/incident/create_series_sm"> -->
          <div>
            <button class="btn btn-info btn-small" type="submit" onclick="onSubmit();">Submit</button>
            <button class="btn btn-danger btn-small" type="button" onclick="parent.$.fancybox.close();">Close</button>
            <div class="marginB10"></div>
          </div><br />	
          <table class="table_02" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<th class="w15">&nbsp;</th>
					<th class="w3">&nbsp;</th>
					<th>Selected</th>
				</tr>
				<tr>
					<td style="padding-bottom: 2px; vertical-align: top;">
						<select id="sltDepartmentList" style="margin-bottom: 2px;">
							<option value="">&nbsp;</option>
							<?php foreach ($arrDepartment as $oDepartment) { ?>
			                <option obj_id="<?php echo $oDepartment->departmentid?>" value="<?php echo $oDepartment->name?>"><?php echo $oDepartment->name ?></option>
			                <?php } ?>
						</select>
						<br>
						<div id="divProductList" style="border: 1px solid #ccc;height: 460px; overflow-y: auto;"></div>
					</td>
					<td>
						<input class="button-img-checked" onclick="SelectCheckedItems();" title="Select Checked Items"><br>
						<input class="button-img-select-all" onclick="SelectAllItems();" title="Select All Items"><br>
						<input class="button-img-deselect-all" onclick="DeSelectAllItems();" title="De-select All Items">
					</td>
					<td style="vertical-align: top;">
						<table id="tblSelectedItems" cellpadding="0" cellspacing="0" border="0" class="responsive display table table-bordered table-standard table_incident_affected_service" width="100%">
				        	<thead>
				            	<tr>
					                <th class="th_product">Product</th>
					                <th class="th_dept">Dept</th>
					                <th class="th_assignment w35">Assignment</th>
					                <th class="th_assignee w15">Assignee</th>
					                <th class="th_impact_level">Impact<br>Level</th>
					                <th class="th_critical_asset w15">Critical Asset</th>
					                <th class="th_title_prefix">Title Prefix</th>
					                <th class="th_action t-center w10">Action</th>
				              	</tr>
				            </thead>
				            <tbody>
				              
				            </tbody>
				        </table>
					</td>
         </table>
         <!-- </form> -->
        </div>
       
       <div id="inc_content" class="tab-pane fade">
          <div class="open_inc_sm_btn">
            <button class="btn btn-info btn-small" type="submit" onclick="onSubmit();">Submit</button>
            <button class="btn btn-danger btn-small" type="button" onclick="parent.$.fancybox.close();">Close</button>
            <div class="marginB10"></div>
          </div> <br />
          <table cellpadding="0" cellspacing="0" border="0" class="table_01" width="100%">
            <tr>
              <th class="">&nbsp;</th>
              <td class="" id="celDepartment">&nbsp;</td>
              <th class="w15">Area</th>
              <td><select id="drparea" name="drparea" class="">
                  	<option value="">&nbsp;</option>
                  	<?php foreach ($arrArea as $oArea) { ?>
                	<option value="<?php echo $oArea->name?>"><?php echo $oArea->name ?></option>
                	<?php } ?>
                </select></td>
            </tr>
            <tr>
              <th><span class="require_mark">*</span>&nbsp;Outage Start</th>
              <td><input type="text" name="outagedate" id="outagedate" value="<?php echo date('Y-m-d H:i:s'); ?>"></td>
              <th>Sub Area</th>
              <td id="celSubArea"><select id="drpsubarea" name="drpsubarea" class="">
              	<option area="" value="" selected="selected">&nbsp;</option>
              	<?php foreach ($arrSubarea as $oSubarea) { ?>
                <option area="<?php echo strtolower($oSubarea->area) ?>" value="<?php echo $oSubarea->id ?>"><?php echo $oSubarea->name ?></option>
                <?php } ?>
                </select></td>
            </tr>
            <tr>
              <th>Downtime Start</th>
              <td colspan="1"><input type="text" name="downtimestart" id="downtimestart" value="" class="downtime text"></td>
              <td colspan="2" style="border-left:none">&nbsp;</td>
            </tr>
            <tr>
              <th>Bug category</th>
              <td><select id="drpbugcategory" name="drpbugcategory" class="">
                  		<option value="">&nbsp;</option>
                  		<?php foreach ($arrBugCategory as $oBugCategory) { ?>
                		<option value="<?php echo $oBugCategory->bug_category_key ?>"><?php echo $oBugCategory->bug_category_name ?></option>
                		<?php } ?>
                </select></td>
              <th><span class="require_mark">*</span>&nbsp;Impact</th>
              <td><select name="drpimpactlevel" id="drpimpactlevel" class="impact">
                  <option value="6">6</option>
                  <option value="5" selected="selected">5</option>
                  <option value="4">4</option>
                  <option value="3">3</option>
                  <option value="2">2</option>
                  <option value="1">1</option>
                </select></td>
            </tr>
            <tr>
              <th>Bug Unit</th>
              <td id="celBugUnit"><select id="drpbugunit" name="drpbugunit" class="">
					<option value=""></option>
                  	<?php foreach ($arrBugUnit as $oBugUnit) { ?>
                	<option value="<?php echo $oBugUnit->unit_key ?>"><?php echo $oBugUnit->unit_name ?></option>
                	<?php } ?>
                </select></td>
              <th><span class="require_mark">*</span>&nbsp;Urgency</th>
              <td><select name="drpurgencylevel" id="drpurgencylevel" class="impact">
                  <option value="5">5</option>
                  <option value="4" selected="selected">4</option>
                  <option value="3">3</option>
                  <option value="2">2</option>
                  <option value="1">1</option>
                </select></td>
            </tr>
            <tr>
              <th>&nbsp;</th>
              <td>&nbsp;</td>
              <th>CCU Times</th>
              <td><input type="text" name="txtCCUTime" id="txtCCUTime" class="" value=""></td>
            </tr>
            <tr>
              <th>&nbsp;</th>
              <td>&nbsp;</td>
              <th>CCU/Connection/ Transaction</th>
              <td><input type="text" name="txtUserImpacted" id="txtUserImpacted" class="" value=""></td>
            </tr>
            <tr>
              <th>&nbsp;</th>
              <td>&nbsp;</td>
              <th>E.U.I (by CS channel)</th>
              <td><input type="text" name="txtCustomerImpacted" id="txtCustomerImpacted" class="" value="0"></td>
            </tr>
            <tr>
              <th>&nbsp;</th>
              <td>&nbsp;</td>
              <th>Caused by external service</th>
              <td><input type="checkbox" id="chkCauseByExt" name="chkCauseByExt" value="t" class="chk_causebyext">
                &nbsp;&nbsp;
                <select id="drpcausebyexternal" name="drpcausebyexternal" class="">
                  <option value="">&nbsp;</option>
                  <?php foreach($arrCausedByDept as $oRcause) { ?>
                	<option value="<?php echo $oRcause->value ?>"><?php echo $oRcause->description ?></option>
                	<?php } ?>
                </select></td>
            </tr>
            <!-- <tr>
            	<th>Critical Asset&nbsp;<span id="require_critical_asset" class="require_mark" style="display:none">*</span></th>
            	<td colspan="3" id="celCriticalAsset"><select id="cboCriticalAsset" name="critical_asset" class="w50">
                		<option value="">&nbsp </option>
              		</select></td>
            </tr> -->
            <tr>
              <th><span class="require_mark">*</span>&nbsp;Title</th>
              <td colspan="3"><input type="text" name="txttitle" id="txttitle" class="title w90" value=""></td>
            </tr>
            <tr>
              <th><span class="require_mark">*</span>&nbsp;Description</th>
              <td colspan="3" class="description_td"><textarea rows="5" class="w98" name="txtdescription" id="txtdescription" style="margin: 3px 0;"></textarea></td>
            </tr>
            <tr>
              <th>SDK Info</th>
              <td style="vertical-align:top"> Detector<br>
                <select id="drpsdkdetector" name="drpsdkdetector">
					<option value="">&nbsp;</option>
					<?php foreach($arrDetector as $oDetector){ ?>
                    <option value="<?php echo $oDetector->value ?>"><?php echo $oDetector->description ?></option>
                    <?php } ?>                  
                </select></td>
              <td colspan="2" style="border-left: 0; vertical-align:top"> SDK Note<br>
                <textarea id="txtsdknote" name="txtsdknote" class="w90" rows="3"></textarea></td>
            </tr>
          </table>
        </div>
      </div> <!-- End .tab-content -->
    </div> <!-- End .widget -->
  </div>
  <!-- End .span12 --> 
  <div id="divResult"></div>
</div>
<!-- End .row-fluid -->

<script>
var arrCriticalAssetRequire = <?php echo json_encode($arrDefined['critical_asset_require']) ?>;
	$(document).ready(function() {
		$('#outagedate, #downtimestart').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm:ss'
		}); 
		
		// LoadCriticalAsset();
		// SetCriticalAssetRequire();
		//on change department
		$('#sltDepartmentList').bind('change', function() {
			onDepartmentChange();
		});
		
		AreaBindChange();
		BugCategoryBindChange();
		
		$("#divResult").dialog({
			autoOpen: false, //If set to true, the dialog will automatically open upon initialization. If false, the dialog will stay hidden until the open() method is called.
			buttons: [{ //Specifies which buttons should be displayed on the dialog. The context of the callback is the dialog element; if you need access to the button, it is available as the target of the event object.
				id: "button-ok",
				text: "Close",
				class: "wp50",
				// style: 'color: black',
				click: function() { $(this).dialog("close"); $.unblockUI(); 
					parent.$.fancybox.close();
					// $(':input').not(':button, :submit, :reset, :hidden').removeAttr('checked').removeAttr('selected').not(':checkbox, :radio, select').val('');
				} //method to hide its close button
			}],
			title: 'Sending request to ITSM ...',
			modal: true, //If set to true, the dialog will have modal behavior; other items on the page will be disabled, i.e., cannot be interacted with. Modal dialogs create an overlay below the dialog but above other page elements.
			width: 600,
			minHeight: 300,
			position: ["center", 20],
			closeText: 'hide', //Specifies the text for the close button. Note that the close text is visibly hidden when using a standard theme. Default: "close"
			closeOnEscape: false, //Specifies whether the dialog should close when it has focus and the user presses the escape (ESC) key. Default: true
			open: function(event, ui) { $(".ui-dialog-titlebar-close").remove(); } //Triggered when the dialog is opened.
		});
	});
	
	function onDepartmentChange() {
		var department_name = $('#sltDepartmentList option:selected').val();
		var url = base_url + 'incident/incident/ajax_get_product_list_of_department?department=' + department_name;
		$("#divProductList").load(url, function() {
			$("input:checkbox[id^='chkProduct']").click(function(event) { $(this).prop('checked', !$(this).prop('checked')); })
			$('.item-list').bind({
				'click': function(){
					var item = $(this).attr('for');
					$('#' + item).prop('checked', !$('#' + item).prop('checked'));
				}
			});
			HideSelectedItemFromChooseList();
		});
	}
	
	function HideSelectedItemFromChooseList() {
		$(".row-selected-item").each(function(index, item) {
			$("#item_" + $(this).attr('product_id')).remove();
		});
	}
	
	function AreaBindChange() {
		$("#drparea").bind('change', function(){
			var area_name = $('#drparea').val();
			var url = base_url + 'incident/incident/ajax_get_subarea_of_area/subarea/drpsubarea/wp200?area=all';
			if ( area_name != "" )
			{
				area_name = encodeURIComponent(area_name);
				url = base_url + 'incident/incident/ajax_get_subarea_of_area/subarea/drpsubarea/wp200?area=' + area_name;	
			}
			var strHtml = AjaxLoad(url);
			$("#celSubArea").html(strHtml);
		});
	}
	
	function BugCategoryBindChange() {
		$("#drpbugcategory").bind('change', function(){
			var bug_category = $('#drpbugcategory').val();
			if ( bug_category != "" )
			{
				bug_category = encodeURIComponent(bug_category);
				var url = base_url + 'incident/incident/ajax_get_bug_unit_by_category/bugunit/drpbugunit/wp200?bug_category=' + bug_category;
	
				var strHtml = AjaxLoad(url);
				$("#celBugUnit").html(strHtml);
			}
		});
	}
	
	function onValidation() {
		var message = '';
		
		if($(".row-selected-item").length==0){
			message += 'Bạn vẫn chưa chọn product nào\n';
		}
		$(".row-selected-item").each(function(index, item) {
			var department_name = $(this).attr('department');
			var cboProduct   = $(this).attr('product_name');
			var drpassignee  = $(this).attr('assignee');
			var drpcriticalasset = $(this).attr('critical_asset'); 
			
			if(drpassignee == null || drpassignee == '') {
				message += 'Vui lòng chọn Assignee cho product "' + cboProduct + '"\n';
			}
			if(arrCriticalAssetRequire.indexOf(department_name.toLowerCase())!==-1) {
				if(drpcriticalasset == null || drpcriticalasset == '') {
					message += 'Vui lòng chọn Critical Asset cho product "' + cboProduct + '"\n';
				}
			}
		});
		
		if($("#txttitle").val()=='') {
			message += 'Vui lòng nhập Title\n';
		}
		if($("#txtdescription").val()=='') {
			message += 'Vui lòng nhập Description\n';
		}
		if($("#outagedate").val()=='') {
			message += 'Vui lòng chọn Outage Start\n';
		}

		if(message == '') {
			return true;
		} else {
			alert(message);
			return false;
		}
	}
	
	function onSubmit() {
		if(onValidation()) {
			var txtdescription      = $("#txtdescription").val();
			var drpproducttype      = "";
			var drpbugcategory      = $("#drpbugcategory").val();
			var drpbugunit          = $("#drpbugunit").val();
			var drpurgencylevel     = $("#drpurgencylevel").val();
			var drpimpactlevel      = $("#drpimpactlevel").val();
			var drparea             = $("#drparea").val();
			var drpsubarea          = $("#drpsubarea").val();
			var drpsdkdetector      = $("#drpsdkdetector").val();
			var txtsdknote          = $("#txtsdknote").val();
			var outagedate          = $("#outagedate").val();
			var txtCCUTime          = $("#txtCCUTime").val();
			var txtCustomerImpacted = $("#txtCustomerImpacted").val();
			var chkCauseByExt       = $("#chkCauseByExt").is(':checked') ? $("#chkCauseByExt").val() : '';
			var drpcausebyexternal  = $("#drpcausebyexternal").val();
			var txtUserImpacted     = $("#txtUserImpacted").val();
			var downtimestart       = $("#downtimestart").val();
	
			var url = base_url + "incident/incident/create_series_submit";
			var count = 0;
	
			$("#button-ok").button("disable");
			$.blockUI({ message: null });
			$("#waitingIcon").show();
			$("#divResult").html('<div id="waiting"></div>');
			$("#divResult").dialog("open");
	
			$(".row-selected-item").each(function(index, item) {
				var no                = $(this).attr('no');
				var cboProduct        = $(this).attr('product_id');
				var drpdepartment     = $(this).attr('department');
				var drpassignmentgroup = $(this).attr('assignment_group');
				var drpassignee       = $(this).attr('assignee');
				var txttitle          = $('#txtPrefix' + cboProduct + '_' + no).val() + ' ' + $("#txttitle").val();
				var private_impact_lvl= $(this).attr('impact_level');
				var drpcriticalasset  = $(this).attr('critical_asset');
	
				if(private_impact_lvl != null && private_impact_lvl != '') {
					drpimpactlevel = private_impact_lvl;
				}
	
				$.ajaxQueue({
		        	type: "POST",
		        	data: {
		        		cboProduct: cboProduct,
		        		drpdepartment: drpdepartment,
		        		drpassignmentgroup: drpassignmentgroup,
		        		drpassignee: drpassignee,
		        		drpcriticalasset: drpcriticalasset,
		        		txttitle: txttitle,
		        		txtdescription: txtdescription,
		        		drpproducttype: drpproducttype,
		        		drpbugcategory: drpbugcategory,
		        		drpbugunit: drpbugunit,
		        		drpurgencylevel: drpurgencylevel,
		        		drpimpactlevel: drpimpactlevel,
		        		drparea: drparea,
		        		drpsubarea: drpsubarea,
		        		drpsdkdetector: drpsdkdetector,
		        		txtsdknote: txtsdknote,
		        		outagedate: outagedate,
		        		txtCCUTime: txtCCUTime,
		        		txtCustomerImpacted: txtCustomerImpacted,
		        		chkCauseByExt: chkCauseByExt,
		        		drpcausebyexternal: drpcausebyexternal,
		        		txtUserImpacted: txtUserImpacted,
		        		downtimestart: downtimestart,
		        		send_sms: 1
		        	},
					url: url,
					beforeSend: function(xhr) {
						$("#waiting").html('<p>' + $('#txtPrefix' + cboProduct + '_' + no).val() + ' ... <img src="' + base_url + 'asset/images/icons/indicator.gif"></p>');
					},
					success: function( response ) {
						if(response.toLowerCase() != 'success') {
							$("#divResult").append('<p>Process ' + $('#txtPrefix' + cboProduct + '_' + no).val() + ' ... <img src="' + base_url + 'asset/images/icons/error.png" width="16px">&nbsp;<b style="color: #FF4747;">' + response + '</b></p>');
						} else {
							$("#divResult").append('<p>Process ' + $('#txtPrefix' + cboProduct + '_' + no).val() + ' ... <img src="' + base_url + 'asset/images/icons/icon_tickmark.gif">&nbsp;<b style="color: blue;">' + response + '</b></p>');
						}
						count++;
						if(count==$(".row-selected-item").length) {
							$("#waiting").html('');
							$("#button-ok").button("option", "disabled", false);
							$("#waitingIcon").hide();
							alert('Xong');
							// parent.$.fancybox.close();
						}
					}
		        });
			});
		}
	}
</script>
<script type="text/javascript" src="<?php echo $base_url;?>asset/js/incident.process.js"></script>