<!DOCTYPE html>
<html>
<head>
<meta content="IE=9,chrome=1" http-equiv="X-UA-Compatible">
<meta charset="utf-8">
<title>SDK Monitoring Assistant</title>
 <link type="image/png" href="<?php echo $base_url?>asset/images/favicon.png" rel="shortcut icon">

<!-- STYLES -->
<link href="<?php echo $base_url?>asset/css/common.css" type="text/css" rel="stylesheet" />
<!-- <link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" /> -->
<!-- END STYLES -->

<!-- JAVASCRIPTS -->
<script type="text/javascript">
	var base_url = '<?php echo $base_url?>';
    var nShowingPopup = 0;
</script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/common.js"></script>
<!-- <script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script> -->
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<!-- <script type="text/javascript" src="<?php echo $base_url?>asset/js/ui/jquery-ui-1-9-2.js"></script> -->
<!-- Merry Christmas -->
<!--<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery.snow.min.1.0.js"></script> -->

<!-- END JAVASCRIPTS -->
</head>
<body>
<div style="padding-left: 20px; padding-top: 10px; padding-bottom: 0px;">
    <a href="<?php echo $base_url?>alert/alert/alert_list" style="float: left">
        <img id="imgLogo" width="40px" src="<?php echo $base_url?>asset/images/icons/vng_logo.png">
    </a>
    <h1 id="datetime" style="float: left; margin-left: 70%;"></h1>
</div>
<div style="clear: both"></div>
<div id="screen_dashboard">
	<div id="body-wrapper">
	    <div id="content-wrapper" >
	    	<?php echo $_content?>
	    </div>
	</div>
</div>
<div id="bttop">BACK TO TOP</div>
<script type='text/javascript'>$(function(){$(window).scroll(function(){if($(this).scrollTop()!=0){$('#bttop').fadeIn();}else{$('#bttop').fadeOut();}});$('#bttop').click(function(){$('body,html').animate({scrollTop:0},800);});});</script>
</body>
<script type="text/javascript">
        var base_url = '<?php echo $base_url?>';
		// $.fn.snow();
</script>
</html>
