var i=0;

function timedCount()
{
	var currentTime = new Date()
	var month = currentTime.getMonth() + 1
	var day = currentTime.getDate()
	var year = currentTime.getFullYear()
	var hours = currentTime.getHours()
	var minutes = currentTime.getMinutes()
	var seconds = currentTime.getSeconds() 
	
	month  = month < 10 ? "0" + month : month;
	day  = day < 10 ? "0" + day : day;
	hours  = hours < 10 ? "0" + hours : hours;
	minutes  = minutes < 10 ? "0" + minutes : minutes;
	seconds  = seconds < 10 ? "0" + seconds : seconds;
		
	var strTime = day + "/" + month + "/" + year + " " + hours + ":" + minutes + ":" + seconds; 
	
	//i=i+1;
	postMessage(strTime);
	setTimeout("timedCount()",100);
}

timedCount(); 