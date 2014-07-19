<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<div class="max-width">
    <div class="content">
    	<?php echo $message;?>
    	<!-- <div class="new-wrapper w100 t-right">
    		<a href="<?php echo $base_url?>admin/category/new_category"><button type="button" class="grey"><img src="<?php echo $base_url?>asset/images/icons/add.png" style="vertical-align:top; margin-right: 2px" />New</button></a>
    	</div> -->
    	<table id="tblTaskList" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
            <thead>
            	<!-- <tr class="table-title">
            		<th colspan="8">
            			<p class="grid">Task List</p>
            		</th>
            	</tr> -->
                <tr class="table-title">
                    <th class="w3 t-center">ID</th>
                    <th class="t-center">Category</th>
                    <th class="t-center">Title</th>
                    <th class="t-center">Assignee</th>
                    <th class="t-center">Status</th>
                    <th class="t-center">Requester</th>
                    <th class="t-center">Created date</th>
                    <th class="t-center">View</th>
                </tr>
            </thead>
            <tbody>
                <tr class="odd">
                	<td class="t-center">1650</td>
                	<td class="t-center">Monitor</td>
                	<td class="t-left">FW: [GN] Open & Mergeserver ngày 28/06</td>
                	<td class="t-center">doint</td>
                	<td class="t-center">assigned</td>
                	<td class="t-center">tg.servicedesk</td>
                	<td class="t-center">2013-06-27 19:01:30</td>
                	<td class="t-center" style="vertical-align: middle;" title="Detail">
                	<a href="#"><img src="<?php echo $base_url?>asset/images/icons/task_detail.png" width="18px" /></a>
                	</td>
                </tr>
                <tr class="even">
                	<td class="t-center">2100</td>
                	<td class="t-center">Maintenance</td>
                	<td class="t-left">FW: [JX1] Uppatch</td>
                	<td class="t-center">dungdv2</td>
                	<td class="t-center">assigned</td>
                	<td class="t-center">hoanghh</td>
                	<td class="t-center">2013-06-20 10:01:10</td>
                	<td class="t-center w5" style="vertical-align: middle;" title="Detail">
                	<a href="#"><img src="<?php echo $base_url?>asset/images/icons/task_detail.png" width="18px" /></a>
                	</td>
                </tr>
                <tr class="odd">
                	<td class="t-center">2998</td>
                	<td class="t-center">Open Server</td>
                	<td class="t-left">FW: [WLY] New server open on 13-August-2013</td>
                	<td class="t-center">phuongnv3</td>
                	<td class="t-center">Initialized</td>
                	<td class="t-center">tg.servicedesk</td>
                	<td class="t-center">2013-08-12 13:06:21</td>
                	<td class="t-center" style="vertical-align: middle;" title="Detail">
                	<a href="#"><img src="<?php echo $base_url?>asset/images/icons/task_detail.png" width="18px" /></a>
                	</td>
                </tr>
                <tr class="even">
                	<td class="t-center">3000</td>
                	<td class="t-center">Monitor</td>
                	<td class="t-left">FW: CAB meeting | Change di chuyển tủ rack hệ thống NetApp V6280 sang vị trí mới</td>
                	<td class="t-center">phuongnv3</td>
                	<td class="t-center">Initialized</td>
                	<td class="t-center">tg.servicedesk</td>
                	<td class="t-center">2013-08-12 13:56:11</td>
                	<td class="t-center" style="vertical-align: middle;" title="Detail">
                	<a href="#"><img src="<?php echo $base_url?>asset/images/icons/task_detail.png" width="18px" /></a>
                	</td>
                </tr>
            </tbody>
		</table>
		<div id="pp_tblTaskList" class="easyui-pagination"></div>
    </div>
</div>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/tasklist.pagination.js"></script>