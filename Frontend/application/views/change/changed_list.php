<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/change.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<div class="max-width">
    <div class="content">
    	<?php echo $message;?>
    	<!-- <div class="new-wrapper w100 t-right">
    		<a href="<?php echo $base_url?>admin/category/new_category"><button type="button" class="grey"><img src="<?php echo $base_url?>asset/images/icons/add.png" style="vertical-align:top; margin-right: 2px" />New</button></a>
    	</div> -->
    	<form method="get" id="frmFilter">
    		<table id="tblFilter" cellpadding="0" cellspacing="0" width="100%" border="0">
    			<tr>
		    		<td class="wp50">Product</td>
		    		<td class="t-left wp140">
		    			<input class="wp120" type="text" name="search-product" id="txtProduct" value="<?php echo @$_REQUEST['search-product'] ?>">
		    		</td>
		    		<td class="t-left">
		    			<input type="submit" value="Search" class="styled-button-2">
		    		</td>
	    		</tr>
    		</table>
    		<input type="hidden" id="hidPageChangeFollow" name="page_change_follow" value="<?php echo $iPageChangeFollow ?>" />
    		<input type="hidden" id="hidPageSizeChangeFollow" name="limit_change_follow" value="<?php echo $iPageSizeChangeFollow ?>" />
    	</form><br />
    	<table id="tblChangeList" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
            <thead>
                <tr class="table-title">
                    <th class="t-center wp60">Change<br />ID</th>
                    <th class="t-center wp95">Product</th>
                    <th class="t-center wp250">Title</th>
                    <th class="t-center wp130">Planned Start</th>
                    <th class="t-center wp130">Planned End</th>
                    <th class="t-center wp130">Down Start</th>
                    <th class="t-center wp130">Down End</th>
                    <th class="t-center wp60">Created<br />By</th>
                    <th class="t-center wp60">Status</th>
                    <th class="t-center wp50">Action</th>
                </tr>
            </thead>
            <tbody>
            	<?php if(!empty($arrChangeFollow)) { foreach($arrChangeFollow as $oCF) {?>
                <tr class="<?php echo (($bRowAlternate=!$bRowAlternate)? 'odd' : 'even') ?>">
                	<td class="t-center <?php if(!empty($oCF->down_start) || !empty($oCF->down_end)) echo 'bg-red cl-white' ?>">
                		<a title="View Change Detail" class="hand-pointer <?php if(!empty($oCF->down_start) || !empty($oCF->down_end)) echo 'cl-white' ?>" onclick="ViewChangeDetail('<?php echo $oCF->itsm_change_id ?>');">
                			<?php echo $oCF->itsm_change_id ?>
                		</a>
                	</td>
                	<td class="t-left"><?php if(!empty($oCF->service)) echo trim($oCF->service) ?></td>
                	<td class="t-left"><?php echo $oCF->title ?></td>
                	<td class="t-center"><?php echo $oCF->planned_start ?></td>
                	<td class="t-center"><?php echo $oCF->planned_end ?></td>
                	<td class="t-center"><?php if(!empty($oCF->down_start)) echo $oCF->down_start ?></td>
                	<td class="t-center"><?php if(!empty($oCF->down_end)) echo $oCF->down_end ?></td>
                	<td class="t-center"><?php echo $oCF->created_by ?></td>
                	<td class="t-center"><?php echo $oCF->status ?></td>
                	<td class="t-center" style="vertical-align: middle;">
                	<!-- <?php $this->tpl->load_anchor_icon(array('img' => 'appbar.page.multiple.png', 'class' => 'btn btn-note', 'title' => 'Note', 'onclick'=>'javascript:;') ); ?> -->
                	<?php $this->tpl->load_anchor_icon(array('img' => ICON_IMG_CALL, 'onclick'=>'ViewContactOfChange(\''. $oCF->itsm_change_id . '\', \'' . @$oCF->service . '\');') ); ?>
                	</td>
                </tr>
                <?php } } ?>
            </tbody>
		</table>
		<div id="pp_tblChangeList" class="easyui-pagination"></div>
    </div>
</div>

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/changelist.pagination.js"></script>
<script type="text/javascript">
	var iTotal = <?php echo $iTotal ?>;
	var iCurrentPage = <?php echo $iPageChangeFollow ?>;
	var iPage = <?php echo $iPageSizeChangeFollow ?>;
	var strChangeViewType = '<?php echo $strChangeViewType ?>';
	var strChangeCtl = '<?php echo $strChangeCtl; ?>';
	
	function ViewChangeDetail(strChangeId) {
		var nHeight = 500;
		var strURL = base_url + "change/change/change_detail?changeid=" + strChangeId + '&change_view=' + strChangeViewType;
		nShowingPopup++;
		CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
	}
	
	function ViewContactOfChange(strChangeId, strProduct) {
		var nHeight = 600;
		var strURL = base_url + "change/change/contact_of_change?change_id=" + strChangeId + '&product=' + strProduct;
		nShowingPopup++;
		CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
	}
	
	$(document).ready(function() {
		$('#total-new-change').text('[<?php echo $iNewChangesTotal ?>]');
		$('#total-follow-change').text('[<?php echo $iFollowChangesTotal ?>]');
		$('#total-all-changes').text('[<?php echo $iAllChangesTotal ?>]');
	});
</script>