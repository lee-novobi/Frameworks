<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/ccu_product.css" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<div class="max-width">
    <div class="content">
    	<?php echo $message;?>
    	<!-- <div class="new-wrapper w100 t-right">
    		<a href="<?php echo $base_url?>admin/category/new_category"><button type="button" class="grey"><img src="<?php echo $base_url?>asset/images/icons/add.png" style="vertical-align:top; margin-right: 2px" />New</button></a>
    	</div> -->
    	<table id="tblCCUProductAlert" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
            <thead>
            	<tr class="table-title">
            		<th colspan="8">
            			<p class="grid" style="color: #fff;">CCU Product Alert</p>
            		</th>
            	</tr>
                <tr>
                    <th class="w10 t-center">Product</th>
                    <th class="t-center">CCU Down</th>
                    <th class="t-center">% CCU Down</th>
                    <th class="t-center">Current CCU</th>
                    <th class="t-center">CCU 5mins ago</th>
                    <th class="t-center">Time Alert</th>
                </tr>
            </thead>
            <tbody>
                <tr class="odd">
                	<td style="color: red;">GN</td>
                	<td style="color: red;">3875</td>
                	<td style="background-color: red;">35%</td>
                	<td style="color: red;">6530</td>
                	<td>10200</td>
                	<td>2013-06-29 17:55:00</td>
                </tr>
                <tr class="even">
                	<td style="color: #2EB82E;">MSG</td>
                	<td style="color: #2EB82E;">1200</td>
                	<td style="background-color: #33CC33;">15%</td>
                	<td style="color: #2EB82E;">6742</td>
                	<td>5542</td>
                	<td>2013-06-29 18:00:00</td>
                </tr>
            </tbody>
		</table><br />
		<table id="tblCCUDashboardLog" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
            <thead>
            	<tr class="table-title">
            		<th colspan="8">
            			<p class="grid" style="color: #fff;">CCU Dashboard Log</p>
            		</th>
            	</tr>
                <tr>
                    <th class="w10 t-center">Product</th>
                    <th class="t-center">CCU Down</th>
                    <th class="t-center">% CCU Down</th>
                    <th class="t-center">Current CCU</th>
                    <th class="t-center">CCU 5mins ago</th>
                    <th class="t-center">Time Alert</th>
                </tr>
            </thead>
            <tbody>
                <tr class="odd">
                	<td style="color: red;">JX1</td>
                	<td style="color: red;">1000</td>
                	<td style="background-color: red;">30%</td>
                	<td>2000</td>
                	<td style="color: red;">3000</td>
                	<td>2013-06-29 17:40:00</td>
                </tr>
                <tr class="even">
                	<td style="color: #2EB82E;">JX2</td>
                	<td style="color: #2EB82E;">2500</td>
                	<td style="background-color: #33CC33;">50%</td>
                	<td>5000</td>
                	<td style="color: #2EB82E;">7500</td>
                	<td>2013-06-29 17:40:00</td>
                </tr>
            </tbody>
		</table>
    </div>
</div>
