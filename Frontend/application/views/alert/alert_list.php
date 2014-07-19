<?php global $arrDefined /* Define trong helper/defined_helper.php */; ?>
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/alert.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/alert.alert_list.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/web_worker/web_worker.alert.js"></script>
<div class="full-width">
  <div class="content t-left">
    <form method="GET" id="formFilter">
	    <table id="tblFilter" cellpadding="0" cellspacing="0" width="100%" border="0">
	    	<tr>
	    		<td class="wp50">Source</td>
	    		<td class="t-left wp130">
	    			<select class="wp120" id="cboSourceFrom" name="source_from">
	    				<option value="">All</option>
	    				<?php foreach($arrDefined['source_from'] as $strKey=>$strName) { ?>
	    				<option value="<?php echo $strKey ?>" <?php if(strtolower(@$_REQUEST['source_from'])==$strKey) {?>selected="selected"<?php } ?>><?php echo $strName ?></option>
	    				<?php } ?>
	    			</select>
	    		</td> 
                
	    		<td class="wp60">Host Name</td>
	    		<td class="t-left wp130">
	    			<input class="wp120" type="text" name="zbx_host" id="txtZbxHost" value="<?php echo @$_REQUEST['zbx_host'] ?>">
	    		</td>
	    		<td class="t-left">
	    			<input type="submit" value="Search" class="styled-button-2">
	    		</td>
               <!-- <td align="right"> <h1 id="datetime"></h1></td> -->
	    	</tr>
	    </table>
        <br />
        <div id="divGroups"> <img src="<?php echo $base_url?>asset/images/icons/loading-horizon.gif" /> </div>
	    <input type="hidden" id="hidPageNoACK" value="<?php echo $iPageNoACK ?>" name="page_no_acked">
	    <input type="hidden" id="hidPageSizeNoACK" value="<?php echo $iPageSizeNoACK?>" name="limit_no_acked">
	    <input type="hidden" id="hidPageACK" value="<?php echo $iPageACK ?>" name="page_acked">
	    <input type="hidden" id="hidPageSizeACK" value="<?php echo $iPageSizeACK?>" name="limit_acked">
        <input type="hidden" id="hidSourceFrom" value="<?php echo @$_REQUEST['source_from']?>">
        <input type="hidden" id="hidKeyword" value="<?php echo @$_REQUEST['zbx_host']?>">
        <input type="hidden" id="hidLayout" value="<?php echo @$_REQUEST['layout']?>" name="layout">
        <input type="hidden" id="hidProduct" value="" name="product">
	  </form>
    </div>
    <br />
    <div id="alert-wrapper" class="peak_wrapper">
      <table width="100%" cellspacing="0" cellpadding="0" class="list-zebra" id="tblAlert" border="0">
        <thead>
          <tr class="table-title">
            <th colspan="7"><p class="drag">NO-ACK ALERT</p></th>
          </tr>
          <tr>
            <th class="t-center wp50">Location</th>
            <th class="t-center wp50">Source</th>
            <th class="t-center wp180">Hostname</th>
            <th class="t-center wp600">Alert</th>
            <th class="t-center wp50">Priority</th>
            <th class="t-center wp150">Time Alert</th>
            <th class="t-center wp130">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $strAlertViewNoACK ?>
        </tbody>
      </table>
      <div id="div-pagination-no-acked" style="background:#efefef; border: 1px solid #CCCCCC;"></div>
      <br />
      <table width="100%" cellspacing="0" cellpadding="0" class="list-zebra" id="tblAlertACKED" border="0">
        <thead>
          <tr class="table-title">
            <th colspan="7"><p class="drag">ACKED ALERT</p></th>
          </tr>
          <tr>
            <th class="t-center wp50">Location</th>
            <th class="t-center wp50">Source</th>
            <!-- <th class="t-center wp40">NumOf<br />Case</th> -->
            <th class="t-center wp180">Hostname</th>
            <th class="t-center wp600">Alert</th>
            <th class="t-center wp50">Priority</th>
            <th class="t-center wp150">Time Alert</th>
            <!-- <th class="t-center">Status</th> -->
            <!-- <th class="t-center wp80">Contact</th> -->
            <th class="t-center wp130">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $strAlertViewACK ?>
        </tbody>
      </table>
      <div id="div-pagination-acked" style="background:#efefef; border: 1px solid #CCCCCC;"></div>
      <input type="hidden" id="hidNeedReloadAfterACK" value="0">
      <input type="hidden" id="hidQueryString" value="<?php echo $strQueryString ?>">
    </div>
    <div style="clear:both"></div>
    <br />
  </div>
</div>
<script type="text/javascript">
var iPageSizeNoACK      = <?php echo $iPageSizeNoACK ?>;
var iTotalRecordsNoACK  = <?php echo $iTotalRowNoACK?>;
var iCurrentPageNoACK   = <?php echo $iPageNoACK?>;
var iPageSizeACK      = <?php echo $iPageSizeACK ?>;
var iTotalRecordsACK  = <?php echo $iTotalRowACK?>;
var iCurrentPageACK   = <?php echo $iPageACK?>;
var iPageSizeHistory  		= <?php echo $iPageSizeHistory?>;
var iTotalRecordsHistory  	= <?php echo $iTotalRowHistory?>;
var iCurrentPageHistory  	= <?php echo $iPageHistory?>;
// var nShowingPopup     = 0;
var strDirIncident    = '<?php echo $incident_directory ?>';
var strDirAlert       = '<?php echo $alert_directory ?>';
var strACK_NO_INC     = '<?php echo ACK_NO_INC ?>';
var nInterval         = <?php echo $arrDefined['auto_refresh_page'][$subModuleName][$funcName] ?>;


$(document).ready(function(){
	var strSrc = $("#hidSourceFrom").val();
	var strKeyword = $("#hidKeyword").val();
	var strUrl = base_url + 'alert/alert/ajax_get_alert_groups?source_from=' + strSrc + '&zbx_host=' + strKeyword;
	var strHtml = AjaxLoad(strUrl);
	//alert (strUrl);
	$("#divGroups").html(strHtml);
});

function SubmitForm(product){
	$("#hidProduct").val(product);
	$("#formFilter").submit();
}


</script>
<script type="text/javascript">
/* TimeCount */ 
/* Author: DuyLH */
/* var w;

function startWorker()
{
	if(typeof(Worker)!=="undefined")
	{
	  if(typeof(w)=="undefined")
		{
			w=new Worker(base_url + "asset/js/web_worker/web_worker.alert.js");
		}
	  w.onmessage = function (event) {
		$("#datetime").html(event.data) ;
	  };
	}
}

startWorker();

/* ReloadChecker */ 
/* Author: DuyLH 
   Deprecated function */
/* if(typeof(EventSource)!=="undefined")
{
  	var source = new EventSource(base_url + "alert/alert/sse_get_is_changed_flag");
  	source.onmessage=function(event)
	{
		if (event.data == 1) 
		{
			location.reload();
		}
	};
}*/

</script>