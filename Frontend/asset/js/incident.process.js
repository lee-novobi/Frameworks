/**
 * @author Amidamaru
 */
function SelectCheckedItems(){
	$("input:checkbox[id^='chkProduct']:checked").each(function(index, item) {
		MoveProductToSelectedList(item.id);
	});
}

function SelectAllItems(){
	$("input:checkbox[id^='chkProduct']").each(function(index, item) {
		MoveProductToSelectedList(item.id); 
	});
}

function DeSelectAllItems() {
	$(".button-deselect").trigger('click');
}

function MoveProductToSelectedList(chkID) {
	var no                = 0;
	var item              = $('#'+chkID);
	var product_id        = item.val();
	var product_name      = item.attr('product_name');
	var ob_date			  = item.attr('ob_date');
	// var location		  = item.attr('location');
	// var product_itsm_name = item.attr('product_itsm_name');
	var department_id     = item.attr('department_id');
	var department        = item.attr('department_name');
	// var department_itsm   = item.attr('department_itsm_name');
	var position          = item.attr('position');
	

	var rowItem         = 'rowSelectedProduct' + product_id + '_' + no;
	var cellAssignment  = 'cellAssignment' + product_id + '_' + no;
	var cellAssignee    = 'cellAssignee' + product_id + '_' + no;
	var cellPrefix      = 'cellPrefix' + product_id + '_' + no;
	var cellDeSelect    = 'celDeSelect' + product_id + '_' + no;
	var cellCloneSelect = 'celCloneSelect' + product_id + '_' + no;
	var cellImpactLevel = 'celImpactLevel' + product_id + '_' + no;
	var cellCriticalAsset = 'cellCriticalAsset' + product_id + '_' + no;
	var cboAssignment   = 'sltAssignmentGroup' + product_id + '_' + no;
	var cboAssignee     = 'sltAssignee' + product_id + '_' + no;
	var cboImpactLevel  = 'sltImpactLevel' + product_id + '_' + no;
	var cboCriticalAsset = 'sltCriticalAsset' + product_id + '_' + no;
	var txtPrefix       = 'txtPrefix' + product_id + '_' + no;
	var cellAction	 	= 'cellAction' + product_id + '_' + no;
	
	var prefix          = '[' + department + ']' + '[' + product_name + ']';
	
	// var spanRequireCriticalAsset = '<span id="require_critical_asset" class="require_mark" style="display:none">*</span>';

	var htmlImpLvl = '<select class="wp50" id="' + cboImpactLevel + '" name="' + cboImpactLevel + '">';
	htmlImpLvl    += '<option value=""></option>';
	htmlImpLvl    += '<option value="6">6</option>';
	htmlImpLvl    += '<option value="5">5</option>';
	htmlImpLvl    += '<option value="4">4</option>';
	htmlImpLvl    += '<option value="3">3</option>';
	htmlImpLvl    += '<option value="2">2</option>';
	htmlImpLvl    += '<option value="1">1</option>';
	htmlImpLvl    += '</select>';

	var html  = '<tr class="row-selected-item" id="' + rowItem + '" product_id="' + product_id + '" no="0" product_name="' + product_name + '" ob_date="' + ob_date + '" department="' + department + '">';
	html += '<td>' + product_name + '</td>';
	html += '<td>' + department + '</td>';
	html += '<td id="' + cellAssignment + '">&nbsp;</td>';
	html += '<td id="' + cellAssignee + '">&nbsp;</td>';
	html += '<td id="' + cellImpactLevel + '">' + htmlImpLvl + '</td>';
	html += '<td id="' + cellCriticalAsset + '"><select id="' + cboCriticalAsset + '" name="critical_asset" class="wp70"><option value="">&nbsp </option></select></td>';
	html += '<td id="' + cellPrefix + '"><input type="text" class="wp160" value="' + prefix + '" id="' + txtPrefix + '"></td>';
	html += '<td id="' + cellAction + '" class="t-center"><input title="deselect" class="button-deselect" type="button" value="" onclick="DeSelectedItem($(this).parent().parent(),' + product_id + ", '" + product_name + "', '" + ob_date + "', " + department_id + ", '" + department + "', " + position + ')">&nbsp;&nbsp;<input title="clone" class="button-cloneselect" type="button" value="" onclick="CloneSelectedItem($(this).parent(),' + product_id + ", '" + product_name + "', '" + ob_date + "', " + department_id + ", '" + department + "', " + position + ')"></td>';
 	// html += '<td id="' + cellDeSelect + '"><input class="button-deselect" style="border: 0;background: url(' + webroot + 'images/icons/close_button.gif) no-repeat;width: 20px;height: 20px" type="button" value="" onclick="DeSelectedItem($(this).parent().parent(),' + product_id + ", '" + product_name + "', '" + ob_date + "', " + department_id + ", '" + department + "','" + department + "', " + position + ')"></td>';
	// html += '<td id="' + cellCloneSelect + '"><input class="button-cloneselect" style="border: 0;background: url(' + webroot + 'images/icons/add.png) no-repeat;width: 20px;height: 20px" type="button" value="" onclick="CloneSelectedItem($(this).parent(),' + product_id + ", '" + product_name + "', '" + ob_date + "', " + department_id + ", '" + department + "','" + department + "', " + position + ')"></td>';
	html += '</tr>';
	$('#tblSelectedItems').append(html);

	var url = base_url + 'incident/incident/ajax_get_assignment_group_of_product_and_department/' + cboAssignment + '/' + cboAssignment + '/wp180?department=' + encodeURIComponent(department) + '&product=' + encodeURIComponent(product_name);
	// alert(url);
	$('#' + cellImpactLevel).change(function() {
		$("#" + rowItem).attr("impact_level", $('#' + cboImpactLevel).val());
	});
	$('#' + cellAssignment).load(url, function() {
		$('#' + cboAssignment).change(function() {
			var impact_level = 5;
			var assignment_group_name = $('#' + cboAssignment + ' option:selected').text();
			var url = base_url + 'incident/incident/ajax_get_assignee_of_assignment_group/' +cboAssignee + '/' + cboAssignee + '/w100?product=' + product_id + '&impact=' + impact_level + '&assignment_grp=' + encodeURIComponent(assignment_group_name);
			$('#' + cellAssignee).load(url, function() {
				$("#" + cboAssignee).change(function() {
					var assignee = $('#' + cboAssignee + ' option:selected').val();
					$("#" + rowItem).attr("assignee", assignee);
				});

				var assignee = $('#' + cboAssignee + ' option:selected').val();
				$("#" + rowItem).attr("assignee", assignee);
			});

			$("#" + rowItem).attr("assignment_group", assignment_group_name);
		});
		var assignment_group = $('#' + cboAssignment + ' option:selected').val();
		$("#" + rowItem).attr("assignment_group", assignment_group);
	});
	url = base_url + 'incident/incident/ajax_get_assignees_of_product/' + cboAssignee + '/' + cboAssignee + '/w100?department=' + encodeURIComponent(department) + '&product=' + encodeURIComponent(product_name);
	$('#' + cellAssignee).load(url, function() {
		$("#" + cboAssignee).change(function() {
			var assignee = $('#' + cboAssignee + ' option:selected').val();
			$("#" + rowItem).attr("assignee", assignee);
		});
		$("#" + rowItem).attr("assignee", $('#' + cboAssignee + ' option:selected').val());
	});
	
	if($.trim(department) != '') {
		critical_asset_url = base_url + 'incident/incident/ajax_get_critical_asset_of_department/' + cboCriticalAsset + '/' + cboCriticalAsset + '/wp70/multi?department=' + encodeURIComponent(department);
	}
	var strCriticalAssetHtml = AjaxLoad(critical_asset_url);
	$('#' + cellCriticalAsset).html(strCriticalAssetHtml);
	$("#" + cboCriticalAsset).change(function() {
		var critical_asset = $('#' + cboCriticalAsset + ' option:selected').val();
		$("#" + rowItem).attr("critical_asset", critical_asset);	
	});
	$("#" + rowItem).attr("critical_asset", $('#' + cboCriticalAsset + ' option:selected').val());
	
	
	var divItem = '#item_' + product_id;
	$(divItem).remove();
}

function DeSelectedItem(obj, product_id, product_name, ob_date, department_id, department_name, position) {
	var product_id_count = $('tr[product_id="' + product_id + '"]').size();
// alert(product_id_count);
	if(product_id_count == 1) {
		var dept = $('#sltDepartmentList option:selected').val();
		if(department_name == dept) {
			var html = '<div id="item_' + product_id + '" class="item-list" for="chkProduct_' + product_id + '">';
			html += '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td>';
			html += '<input type="checkbox" id="chkProduct_' + product_id + '" value="' + product_id + '" product_name="' + product_name + '" ob_date="' + ob_date + '" department_name="' + department_name + '" department_id="' + department_id + '" position="' + position + '">&nbsp;';
			html += product_name + '</td>';
			html += '<td align="right"><input type="button" value="" class="button-img-add" onclick="MoveProductToSelectedList(' + "'chkProduct_" + product_id + "'" + ')"></td>';
			html += '</tr></table>';
			html += '</div>';

			if($("input:checkbox[id^='chkProduct']").length > 0) {
				if($("input:checkbox[id^='chkProduct']:last").attr('position') < position) {
					$("#divProductList").append(html);
				} else {
					$('input:checkbox').each(function(index, item) {
						if($(this).attr('position') > position){
							$(html).insertBefore("#item_" + item.value);
							return false;
						}
					});
				}
			} else {
				$("#divProductList").append(html);
			}
		}
	}
	obj.remove();
}

function CloneSelectedItem(obj, product_id, product_name, ob_date, department_id, department, position) {
	if(obj){
		var item = obj.parent();
		var product_id = obj.parent().attr('product_id');
		var no = parseInt($('tr[product_id="' + product_id + '"]:last').attr('no'),10) + 1;

		var rowItem         = 'row' + product_id + '_' + no;
		var cellAssignment  = 'cellAssignment' + product_id + '_' + no;
		var cellAssignee    = 'cellAssignee' + product_id + '_' + no;
		var cellPrefix      = 'cellPrefix' + product_id + '_' + no;
		var cellDeSelect    = 'celDeSelect' + product_id + '_' + no;
		var cellCloneSelect = 'celCloneSelect' + product_id + '_' + no;
		var cellImpactLevel = 'celImpactLevel' + product_id + '_' + no;
		var cellCriticalAsset = 'cellCriticalAsset' + product_id + '_' + no;
		var cboAssignment   = 'sltAssignmentgroup' + product_id + '_' + no;
		var cboAssignee     = 'sltAssignee' + product_id + '_' + no;
		var cboImpactLevel  = 'sltImpactLevel' + product_id + '_' + no;
		var cboCriticalAsset = 'sltCriticalAsset' + product_id + '_' + no;
		var txtPrefix       = 'txtPrefix' + product_id + '_' + no;
		var cellAction	 	= 'cellAction' + product_id + '_' + no;
		
		var prefix             = '[' + department + ']' + '[' + product_name + ']';

		var htmlImpLvl = '<select class="wp50" id="' + cboImpactLevel + '" name="' + cboImpactLevel + '">';
		htmlImpLvl    += '<option value=""></option>';
		htmlImpLvl    += '<option value="6">6</option>';
		htmlImpLvl    += '<option value="5">5</option>';
		htmlImpLvl    += '<option value="4">4</option>';
		htmlImpLvl    += '<option value="3">3</option>';
		htmlImpLvl    += '<option value="2">2</option>';
		htmlImpLvl    += '<option value="1">1</option>';
		htmlImpLvl    += '</select>';

		var html  = '<tr class="row-selected-item" id="' + rowItem + '" product_id="' + product_id + '" no="' + no + '" product_name="' + product_name + '" ob_date="' + ob_date + '" department="' + department + '">';
		html += '<td>' + product_name + '</td>';
		html += '<td>' + department + '</td>';
		html += '<td id="' + cellAssignment + '">&nbsp;</td>';
		html += '<td id="' + cellAssignee + '">&nbsp;</td>';
		html += '<td id="' + cellImpactLevel + '">' + htmlImpLvl + '</td>';
		html += '<td id="' + cellCriticalAsset + '"><select id="' + cboCriticalAsset + '" name="critical_asset" class="wp70"><option value="">&nbsp </option></select></td>';
		html += '<td id="' + cellPrefix + '"><input type="text" class="wp160" value="' + prefix + '" id="' + txtPrefix + '"></td>';
		html += '<td id="' + cellAction + '" class="t-center"><input class="button-deselect" type="button" value="" onclick="DeSelectedItem($(this).parent().parent(),' + product_id + ", '" + product_name + "', '" + ob_date + "', " + department_id + ", '" + department + "', " + position + ')">&nbsp;&nbsp;<input class="button-cloneselect" type="button" value="" onclick="CloneSelectedItem($(this).parent(),' + product_id + ", '" + product_name + "', '" + ob_date + "', " + department_id + ", '" + department + "', " + position + ')"></td>';
		html += '</tr>';
		$('#tblSelectedItems').append(html);

		var url = base_url + 'incident/incident/ajax_get_assignment_group_of_product_and_department/' + cboAssignment + '/' + cboAssignment + '/wp180?department=' + encodeURIComponent(department) + '&product=' + encodeURIComponent(product_name);
		$('#' + cellImpactLevel).change(function(){
			$("#" + rowItem).attr("impact_level", $('#' + cboImpactLevel).val());
		});
		$('#' + cellAssignment).load(url, function(){
			$('#' + cboAssignment).change(function(){
				var impact_level = 5;
				var assignment_group_name = $('#' + cboAssignment + ' option:selected').text();
				var url = base_url + 'incident/incident/ajax_get_assignee_of_assignment_group/' + cboAssignee + '/' + cboAssignee + '/w100?product=' + product_id + '&impact=' + impact_level + '&assignment_grp=' + encodeURIComponent(assignment_group_name);
				
				$('#' + cellAssignee).load(url, function() {
					$("#" + cboAssignee).change(function() {
						var assignee = $('#' + cboAssignee + ' option:selected').val();
						$("#" + rowItem).attr("assignee", assignee);
					});

					var assignee = $('#' + cboAssignee + ' option:selected').val();
					$("#" + rowItem).attr("assignee", assignee);
				});

				$("#" + rowItem).attr("assignment_group", assignment_group_name);
			});
			var assignment_group = $('#' + cboAssignment + ' option:selected').val();
			$("#" + rowItem).attr("assignment_group", assignment_group);
		});
		url = base_url + 'incident/incident/ajax_get_assignees_of_product/' + cboAssignee + '/' + cboAssignee + '/w100?department=' + encodeURIComponent(department) + '&product=' + encodeURIComponent(product_name);
		$('#' + cellAssignee).load(url, function() {
			$("#" + cboAssignee).change(function() {
				var assignee = $('#' + cboAssignee + ' option:selected').val();
				$("#" + rowItem).attr("assignee", assignee);
			});
			$("#" + rowItem).attr("assignee", $('#' + cboAssignee + ' option:selected').val());
		});
		
		if($.trim(department) != '') {
			critical_asset_url = base_url + 'incident/incident/ajax_get_critical_asset_of_department/' + cboCriticalAsset + '/' + cboCriticalAsset + '/wp70/multi?department=' + encodeURIComponent(department);
		}
		var strCriticalAssetHtml = AjaxLoad(critical_asset_url);
		$('#' + cellCriticalAsset).html(strCriticalAssetHtml);
		$("#" + cboCriticalAsset).change(function() {
			var critical_asset = $('#' + cboCriticalAsset + ' option:selected').val();
			$("#" + rowItem).attr("critical_asset", critical_asset);	
		});
		$("#" + rowItem).attr("critical_asset", $('#' + cboCriticalAsset + ' option:selected').val());
	}
}