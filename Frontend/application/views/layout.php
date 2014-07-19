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
	var strDirIncident    = '<?php echo $incident_directory ?>';
	var nShowingPopup = 0;
</script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/common.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/noti_list.js"></script>
<!-- <script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script> -->
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<!-- <script type="text/javascript" src="<?php echo $base_url?>asset/js/ui/jquery-ui-1-9-2.js"></script> -->
<!-- Merry Christmas -->
<!--<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery.snow.min.1.0.js"></script> -->
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/incident.create_inc.js"></script>

<!-- END JAVASCRIPTS -->
</head>
<body>
<div id="screen">
	<div id="header">
		<div id="logo_and_login_info">
      		 <div id="search-wrapper">
            <form method="GET">
                <input id="iptSearch" type="text" name="k" value="<?php echo @$_GET['k']?>">
                <input id="iptSearchIcon" type="image" alt="Submit button" src="<?php echo $base_url?>asset/images/icons/find.png">
            </form>
            </div>
            <div id="header-menu-wrapper">
            	<ul id="header-menu">
                	<li id="name" class="navItem middleItem">
                    	<a class="navLink" href="#">Schedule</a>
                    </li>
                    <li id="name" class="navItem middleItem">
                    	<a id="contact" class="navLink" href="<?php echo $base_url?>contact/contact">Contact</a>
                    </li>
                    <li id="name" class="navItem middleItem">
                    	<a id="note" class="navLink" href="#">Note</a>
                    </li>
					<li id="name" class="navItem middleItem">
                    	<a class="navLink" href="#">KB</a>
                    </li>
					<li id="name" class="navItem middleItem">
                    	<a class="navLink" href="#">Maintenance</a>
                    </li>
                    <!--<li id="name" class="navItem middleItem">
                    	<a class="navLink" href="#">Quick Action</a>
                    </li>-->
                    <li id="name" class="navItem middleItem">
                        <a class="styled-button-2 hand-pointer" onclick="PopupCreateNew();">Create Inc
                         </a>
                    </li>
                    <li id="name" class="navItem middleItem">
                        <a class="styled-button-2 hand-pointer" onclick="PopupCreateSeries();" style="margin: 5px;">Create Multi Inc
                            <!-- <input type="submit" value="Create Multi Inc" class="styled-button-2"> -->
                         </a>
                    </li>
                </ul>
            </div>
            <div id="user-wrapper">
            	<img src="<?php echo $base_url?>asset/images/uploads/users/<?php echo $this->session->userdata('username'); ?>.jpg" width="32px" />
				<span id="welcome-text">Welcome, <?php $strTmpUName = $this->session->userdata('username'); echo empty($strTmpUName)?'Guest':$this->session->userdata('userfullname'); ?></span>&nbsp &nbsp
                <a href="<?php echo $base_url?>logout">Logout</a>

                <br/>
            </div>
		</div>
		<div id="main_menu">
	        <ul>
            	<li><div id="menu-alert"><a href="<?php echo $base_url?>alert/alert/alert_list">Alert</a></div></li>
	        	<li><div id="menu-incident"><a href="<?php echo $base_url?>incident/incident/inc_list">Incident</a></div></li>
	            <li><div id="menu-change" module="change"><a href="<?php echo $base_url?>change/change/new_change">Change</a></div></li>
	            <li><div id="menu-task" module="task"><a href="<?php echo $base_url?>task/task">Tasklist</a></div></li>
	            <li><div id="menu-review" module="review"><a href="<?php echo $base_url?>review/review">Review List</a></div></li>
	            <li><div id="menu-ccu_product" module="ccu"><a href="<?php echo $base_url?>ccu_product">CCU Product</a></div></li>
	        </ul>
		</div>
		<div id="sub_menu">
	        <ul>
            	<!-- SUBMENU ALERT -->
	            <li class="sub-alert">
                	<div id="submenu-alert_list">
                		<a href="<?php echo $base_url?>alert/alert/alert_list">
                        	<span class="submenu-link"><img src="<?php echo $base_url?>asset/images/icons/support_roadmap.gif" width="12px">All Alerts</span>
                        </a>
                    </div>
                </li>
                <li class="sub-alert">
                	<div id="submenu-alert">
                		<a href="<?php echo $base_url?>alert/alert/alert_list?layout=dashboard">
                        	<span class="submenu-link"><img src="<?php echo $base_url?>asset/images/icons/support_roadmap.gif" width="12px">Dashboard</span>
                        </a>
                    </div>
                </li>
                <li class="sub-alert">
                	<div id="submenu-alert_list_history">
                		<a href="<?php echo $base_url?>alert/alert/alert_list_history">
                        	<span class="submenu-link"><img src="<?php echo $base_url?>asset/images/icons/support_roadmap.gif" width="12px">History Alerts</span>
                        </a>
                    </div>
                </li>
                <!-- <li class="sub-alert">
                	<div id="submenu-alert">
                		<a href="<?php echo $base_url?>alert/alert/alert_full_hdd">
                        	<span class="submenu-link"><img src="<?php echo $base_url?>asset/images/icons/support_roadmap.gif" width="12px">Alerts Full HDD</span>
                        </a>
                    </div>
                </li> -->
                <!-- END: SUBMENU ALERT -->
            	<!-- SUBMENU INCIDENT -->
	            <li class="sub-incident">
                	<div id="submenu-incident">
                		<a href="<?php echo $base_url?>incident/incident/inc_list">
                        	<span class="submenu-link"><img src="<?php echo $base_url?>asset/images/icons/support_roadmap.gif" width="12px">All Incidents</span>
                        </a>
                    </div>
                </li>
	            <!-- <li class="sub-incident">
                	<div id="submenu-status_inc">
                    	<a href="<?php echo $base_url?>#">
                        <span class="submenu-link"><img src="<?php echo $base_url?>asset/images/icons/exclamation.png" width="14px"> Status Incident</span>
                        </a>
                    </div>
                </li> -->
                <!-- END: SUBMENU INCIDENT -->
                <!-- SUBMENU CHANGE -->
                <li class="sub-change"><div id="submenu-new_change"><a href="<?php echo $base_url;?>change/change/new_change">New Change<b id="total-new-change" class="cl-common-x-count"></b></a></div></li>
	        	<li class="sub-change"><div id="submenu-follow_change" ><a href="<?php echo $base_url?>change/change/follow_change">Follow Change<b id="total-follow-change" class="cl-common-x-count"></b></a></div></li>
	            <li class="sub-change"><div id="submenu-all_changes"><a href="<?php echo $base_url?>change/change/all_changes">All Changes<b id="total-all-changes" class="cl-common-x-count"></b></a></div></li>
                <!-- END: SUBMENU CHANGE -->
                <!-- SUBMENU TASK -->
                <li class="sub-task"><div id="submenu-new_task"><a href="<?php echo $base_url?>">New Task</a></div></li>
	        	<li class="sub-task"><div id="submenu-follow_task"><a href="<?php echo $base_url?>">Follow Task</a></div></li>
	            <li class="sub-task"><div id="submenu-task"><a href="<?php echo $base_url?>task/task">All Tasks</a></div></li>
                <!-- END: SUBMENU TASK -->
	        </ul>
		</div>
	</div>
	<div id="body-wrapper">
	    <div id="content-wrapper" >
	    	<?php echo $_content?>
	    </div>
	</div>
	<div id="right-side"></div>
</div>
<div id="bttop">BACK TO TOP</div>
<script type='text/javascript'>$(function(){$(window).scroll(function(){if($(this).scrollTop()!=0){$('#bttop').fadeIn();}else{$('#bttop').fadeOut();}});$('#bttop').click(function(){$('body,html').animate({scrollTop:0},800);});});</script>
</body>
<script type="text/javascript">
   $(document).ready(function() {
		<?php if(empty($funcName)) { $strActiveMenuName = $subModuleName; } else { $strActiveMenuName = $funcName; } ?>
	    $('#menu-<?php echo strtolower($moduleName)?>').addClass('active-menu');
		$('#submenu-<?php echo strtolower($strActiveMenuName)?>').addClass('active-menu');
		$('.sub-<?php echo strtolower($moduleName)?>').css('display', 'block');
        $('ul#header-menu li').each(function(index) {
            if($(this).find('a').attr('id') == '<?php echo strtolower($moduleName);?>') {
                $('a#<?php echo strtolower($moduleName);?>').addClass('active_header_menu');
            }
        });
        var base_url = '<?php echo $base_url?>';
	});
</script>
</html>
