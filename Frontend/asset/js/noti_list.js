$(document).ready(function(){
	var strURL = base_url + "notification/notification/NotificationList";
	var strResult = AjaxLoad(strURL);
	$("#right-side").html(strResult);
	
});