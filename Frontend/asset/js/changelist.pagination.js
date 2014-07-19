$(document).ready(function(){
	$('#pp_tblChangeList').pagination({  
	    total:iTotal,  
	    pageSize:iPage,
	    pageNumber:iCurrentPage,
	    showRefresh: false,
	    onSelectPage: function(strPageChange, strPageSizeChange){
	    	OnChangeListPageChange(strPageChange, strPageSizeChange);
	    },
	    onChangePageSize: function(strPageSizeChange){
			OnChangeListPageChange(1, strPageSizeChange);
	    }
	}); 
});

function OnChangeListPageChange(strPageChange, strPageSizeChange) {
	var strURL = base_url + 'change/change/' + strChangeCtl + '?page_change_follow=' + strPageChange + '&limit_change_follow=' + strPageSizeChange;
	window.location = strURL;
}
