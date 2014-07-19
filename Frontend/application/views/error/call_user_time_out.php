<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/jconfirm.action.css" />

<body style="background-color: white; ">
<div class="t-center" style="padding-top: 30px;">
<h1>AVAYA Service Failed !!!</h1><br />
<h2>Error Time Out!<h2>
</div>
<div class="t-center" style="padding-top: 30px;">
		<a id="btnFail" class="del hand-pointer">Call Fail</a>
</div>
</body>

<script type="text/javascript">
$(document).ready(function() {
	var user_name = '<?php echo htmlspecialchars($strUserName, ENT_QUOTES, 'UTF-8'); ?>';
	$('#btnFail').click(function()
	{
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>contact_point/ctl_user/call_user_fail',
			data: {
				user_name:user_name
			},
			success: function(result) {
				parent.$.fancybox.close();
			}
		});
	});	
});

 
</script>