<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/contact.css" />
<div style="background-color: white; margin: 5px;">
	<form id="frmSaveUserInfoFromVNGHR" method="post" action="">
	    <table id="tblUserInfoFromVNGHR" cellpadding="0" cellspacing="10" width="100%" border="0">
	    	<tr><th colspan="2"><h3 style="margin-top: 0px;"><img src="<?php echo $base_url?>asset/images/icons/review.png" width="25px" style="margin-right: 5px;" />Review User Information Before Saving</h3></th></tr>
	    	<tr>
	    		<td>Select Deparment</td>
	    		<td align="left">
                    <select name="sltDepartment" id="sltDepartment">
                    	<?php if(!empty($arrDepartments)) { ?>
                    	<?php foreach ($arrDepartments as $index => $oDepartment) { ?>
                    	<option value="<?php echo $oDepartment['departmentid'] ?>"><?php echo $oDepartment['name'] ?></option>
                    	<?php } /* end foreach */ ?>
                    	<?php } /* end if */ ?>
                    </select>
				</td>
	    	</tr>
	    	<tr>
	    		<td>Full Name</td>
	    		<td><?php echo $strFullName; ?></td>
	    	</tr>
	    	<tr>
	    		<td>Mobile</td>
	    		<td><?php echo $strMobile; ?></td>
	    	</tr>
	    	<tr>
	    		<td>Email</td>
	    		<td><?php echo $strEmail; ?></td>
	    	</tr>
	    	<tr>
	    		<td colspan="2" class="t-right">
	    			<a id="save-link" class="save hand-pointer">Save</a>
	    			<a id="cancel-link" class="cancel hand-pointer">Cancel</a>
	    		</td>
	    	</tr>
	    </table>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#save-link').click(function() {
			var departmentid = $('#sltDepartment :selected').val();
			$.ajax({
				url: '<?php echo $base_url ?>contact/contact/save_user_info_from_VNGDB',
				type: 'POST',
				data: {
					departmentid: departmentid,
					staffid: <?php echo $iStaffId; ?>
				},
				success: function(result) {
					var obj = $.parseJSON(result);
					if(obj!=null) {
						if(obj.msg == '<?php echo ALREADY; ?>') {
							alert('This user information has been existed in ContactPoint SDK!');
						} else if(obj.msg == '<?php echo MESSAGE_TYPE_SUCCESS; ?>') {
							alert('Save user information successfully!');
							parent.$.fancybox.close();
						} else if(obj.msg == '<?php echo INSERTDB_ERROR ?>') {
							alert('Save Action Fail!');
						} else if(obj.msg == '<?php echo NOT_ENOUGH ?>') {
							alert('Lack of information, please check carefully again!');
						}
					} else {
						alert('Internal Error!');
					}
				}
			});
		});

		$('#cancel-link').click(function(event) {
			/* Act on the event */
			parent.$.fancybox.close();
		});
	});
</script>