/**
 * @author Amidamaru
 */
function PopupCreateNew() {
	var nHeight = 700;
	var strURL = base_url + strDirIncident + "incident/create_incident";
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

// -------------------------------------------------------------------------------------------------------- //

function PopupCreateSeries() {
	var nHeight = 700;
	var strURL = base_url + strDirIncident + "incident/create_series";
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
} 