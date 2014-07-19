function AjaxLoad(url) {
	var strHtml = '';
		$.ajax(
			{
				url: url,
				type: 'get',
				async: false,
				cache: false,
				success: function(data)
				{
					// data contains error
					if (data != 'error') { strHtml = data; }
					// data contains your html
					else
					{
						alert('Sorry but your session is invalid. Please relogin!');
						window.location.href = base_url + "login";
					}
				}
			}
		);
	return strHtml;
}

//-----------------------------------------------------------------------------------------------------------//
function AjaxLoadByPost(url, data) {
	var strHtml = '';
		$.ajax(
			{
				url: url,
				type: 'post',
				async: false,
				data: data,
				cache: false,
				success: function(data)
				{
					// data contains error
					if (data != 'error') { strHtml = data; }
					// data contains your html
					else
					{
						alert('Sorry but your session is invalid. Please relogin!');
						window.location.href = base_url + "login";
					}
				}
			}
		);
	return strHtml;
}

// -------------------------------------------------------------------------------------------------------- //
/* CLOSE NOTIFICATION ANIMATION */
jQuery(document).ready(function($) {
	$(".close").click(
		function () {
			$(this).parent().fadeTo(400, 0, function () {
				$(this).slideUp(400);
			});
			return false;
		}
	);
});

// -------------------------------------------------------------------------------------------------------- //
String.prototype.lpad = function(padString, length) {
	var str = this;
    while (str.length < length)
        str = padString + str;
    return str;
};

//pads right
String.prototype.rpad = function(padString, length) {
	var str = this;
    while (str.length < length)
        str = str + padString;
    return str;
};
function getWidth() {
  var myWidth = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
  }
  return myWidth;
}

function getHeight() {
  var myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myHeight = document.body.clientHeight;
  }
  return myHeight;
}
/**

 * Convert number of bytes into human readable format
 *
 * @param integer bytes     Number of bytes to convert
 * @param integer precision Number of digits after the decimal separator
 * @return string
 */
function bytesToSize(bytes, precision)
{
	var kilobyte = 1024;
	var megabyte = kilobyte * 1024;
	var gigabyte = megabyte * 1024;
	var terabyte = gigabyte * 1024;

	if ((bytes >= 0) && (bytes < kilobyte)) {
		return bytes + ' B';

	} else if ((bytes >= kilobyte) && (bytes < megabyte)) {
		return (bytes / kilobyte).toFixed(precision) + ' KB';

	} else if ((bytes >= megabyte) && (bytes < gigabyte)) {
		return (bytes / megabyte).toFixed(precision) + ' MB';

	} else if ((bytes >= gigabyte) && (bytes < terabyte)) {
		return (bytes / gigabyte).toFixed(precision) + ' GB';

	} else if (bytes >= terabyte) {
		return (bytes / terabyte).toFixed(precision) + ' TB';

	} else {
		return bytes + ' B';
	}
}

function bpsToSize(bytes, precision)
{
	var kilobyte = 1024;
	var megabyte = kilobyte * 1024;
	var gigabyte = megabyte * 1024;
	var terabyte = gigabyte * 1024;

	if ((bytes >= 0) && (bytes < kilobyte)) {
		return bytes + ' B';

	} else if ((bytes >= kilobyte) && (bytes < megabyte)) {
		return (bytes / kilobyte).toFixed(precision) + ' KB\/s';

	} else if ((bytes >= megabyte) && (bytes < gigabyte)) {
		return (bytes / megabyte).toFixed(precision) + ' MB\/s';

	} else if ((bytes >= gigabyte) && (bytes < terabyte)) {
		return (bytes / gigabyte).toFixed(precision) + ' GB\/s';

	} else if (bytes >= terabyte) {
		return (bytes / terabyte).toFixed(precision) + ' TB\/s';

	} else {
		return bytes + ' B\/s';
	}
}

/**
 * Convert number of bytes into human readable format
 *
 * @param integer bytes     Number of bytes to convert
 * @param integer precision Number of digits after the decimal separator
 * @return string
 */
function kilobytesToSize(kilobytes, precision)
{
	return bytesToSize(kilobytes*1024, precision);
}
// -------------------------------------------------------------------------------------------------------- //
/* CREATE FANCYBOX */
// If clicking the overlay should not close FancyBox
function CreateFancyBoxModal(url, height, onClose){
	var oOptions = {
			'href': url,
			'titlePosition'		: 'none',
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'fade',
			'type'              : 'iframe',
			'width'             : '90%',
			'height'            : height,
			'hideOnOverlayClick': false
	};
	if(onClose != undefined) oOptions.onClosed = onClose;
	$.fancybox(oOptions);
}
// -------------------------------------------------------------------------------------------------------- //

/* CREATE FANCYBOX */
// If clicking the overlay should not close FancyBox
function CreateFancyBoxContactPoint(url, height, onClose){
	var oOptions = {
			'href': url,
			'titlePosition'		: 'none',
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'fade',
			'type'              : 'iframe',
			'hideOnOverlayClick': false
	};
	if(onClose != undefined) oOptions.onClosed = onClose;
	$.fancybox(oOptions);
}
// -------------------------------------------------------------------------------------------------------- //

/* CREATE FANCYBOX */
function CreateFancyBox(url, height, onClose){
	var oOptions = {
			'href': url,
			'titlePosition'		: 'none',
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'fade',
			'type'              : 'iframe',
			'width'             : '90%',
			'height'            : height};
	if(onClose != undefined) oOptions.onClosed = onClose;
	$.fancybox(oOptions);
}
// -------------------------------------------------------------------------------------------------------- //

/* FORMAT HIGHCHART TOOLTIP*/
function FormatHighchartTooltip(unixtime, value) {
	return '<strong>' + ConvertUnixTimeToFormattedDateTime(unixtime) + '</strong><br>' + Number((value).toFixed(3)).toString();
}
// -------------------------------------------------------------------------------------------------------- //
/* CONVERT UNIX TIME TO FORMATTED DATETIME */
function ConvertUnixTimeToFormattedDateTime(unixtime) {
	var oJSDate = new Date(unixtime);
	var date = $.datepicker.formatDate('DD. MM d. ', oJSDate);
	var time = oJSDate.getHours() + ':' + (oJSDate.getMinutes()<10?'0':'') + oJSDate.getMinutes();
	return date + time;
}
//-------------------------------------------------------------------------------------------------------- //
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
//-------------------------------------------------------------------------------------------------------- //
function UrlExists(url) {
	var http = new XMLHttpRequest();
	http.open('HEAD', url, false);
	http.send();
	return http.status!=404;
}
//-------------------------------------------------------------------------------------------------------- //
function AttachjQueryUIAutoComplete(selector, arrData) {
	$(selector).autocomplete({
		source: arrData,
		open: function(event, ui) {
            $(this).autocomplete("widget").css({
                "width": 600
            });
        },
        focus: function( event, ui ) { //Triggered when focus is moved to an item (not selecting).
	        $( "#iptLinkCause" ).val( ui.item.label );
	        if(ui.item.alert_id) $('#hidAlertId').val(ui.item.alert_id);
			if(ui.item.time_alert) $('#hidTimeAlert').val(ui.item.time_alert);
			if(ui.item.incident_id) $('#hidIncidentId').val(ui.item.incident_id);
	        return false;
	    },
		select: function(event, ui) { //Triggered when an item is selected from the menu. The default action is to replace the text field's value with the value of the selected item. Canceling this event prevents the value from being updated, but does not prevent the menu from closing.
			if(ui.item.alert_id) $('#hidAlertId').val(ui.item.alert_id);
			if(ui.item.time_alert) $('#hidTimeAlert').val(ui.item.time_alert);
			if(ui.item.incident_id) $('#hidIncidentId').val(ui.item.incident_id);
			
			return false;
		}
	    
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a " + (item.alert_id ? "alert_id=" + item.alert_id + ' ' : ' ') + (item.time_alert ? "time_alert=" + item.time_alert : ' ') +
        (item.incident_id ? "incident_id=" + item.incident_id : ' ')  + ">" + item.label + "</a>" )
        .appendTo( ul );
    };
}
