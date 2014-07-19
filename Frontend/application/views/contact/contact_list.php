
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/css/contact.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url; ?>asset/js/ajax_jquery_autocomplete/styles.4jqautocomplete.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.css" />
<style type="text/css">
    #btnFilter:hover {
        color: #122435;
        font-size: 12px;
        font-weight: bold;
    }

    #btnFilter {
        font-weight: normal;
        font-size: 12px;
    }
</style>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/ajax_jquery_autocomplete/jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/autocomplete_user_defined/contact.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_v2.js"></script>
<script type="text/javascript" src="<?php echo $base_url; ?>asset/js/fancybox_creation_no_selector.js"></script>

<div class="max-width">
    <div class="content">
        <div id="msgalert" class="notification" style="display: none">
            <a class="close" href="#"><img alt="close" title="Close this notification" src="<?php echo $base_url?>asset/images/icons/cross_grey_small.png"></a>
            <div id="msg_content">
                <!-- insert message content -->
            </div>
        </div>
        <div id="accor" class="module easyui-accordion" data-options="animate: false" style="width: 900px;">
            <div title="Filter" data-options="iconCls:'icon-search',selected:false" style="overflow:auto;padding:10px 10px;">
                <table cellpadding="2" cellspacing="3" border="0" width="100%" style="margin-bottom: 5px;">
                    <tr><td colspan="6">
                        <form id="frmFilter" action="" method="post">
                            <table cellpadding="2" cellspacing="3" border="0" width="100%" style="margin-bottom: 5px;">
                                <tr>
                                    <td>Department</td>
                                    <td>
                                        <select id="cboDepartment" name="department">
                                            <option value="-1"><?php echo STRING_LIST; ?></option>
                                            <?php if(!empty($arrDepartments)) {; ?>
                                            <?php foreach($arrDepartments as $index=>$objOneDepartment) {?>
                                                <option value="<?php echo $objOneDepartment['departmentid'];?>" <?php if($objOneDepartment['departmentid'] == @$_POST['department']) {?>selected="selected"<?php } ?>><?php echo $objOneDepartment['name'];?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="t-right">Product</td>
                                    <td class="t-center">
                                        <select id="cboProduct" name="product" style="width: 300px;">
                                            <option value="-1"><?php echo STRING_LIST; ?></option>
                                            <?php if(!empty($arrProducts)) {?>
                                            <?php foreach($arrProducts as $index=>$objOneProduct) {?>
                                                <option value="<?php echo $objOneProduct['productid'];?>" <?php if($objOneProduct['productid'] == @$_POST['product']) {?>selected="selected"<?php } ?>><?php echo $objOneProduct['name'];?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td colspan="2" class="t-center"><input type="hidden" id="btnFilter" class="search" name="btnFilter" value="Search" style="padding: 3px 15px !important;" /></td>
                                </tr>
                            </table>
                        </form></td>
                    </tr>
                    <tr>
                        <td>Search by product</td>
                        <td align="left">
                            <input class="input-short" style="width: 150px;" type="text" id="search_by_product" name="txtSearchByProduct"  />
                        </td>
                        <td><a class="search hand-pointer" id="btnSearchByProduct">Search</a></td>
                        <td>Search by user</td>
                        <td><input class="input-short" style="width: 150px;" type="text" id="search_by_user"  />
                        </td>
                        <td><a id="btnSearchByUser" class="search hand-pointer">Search</a></td>
                    </tr>
                </table>
            </div>
        </div> <!-- end .module --><br/>
        <div id="Contact">
        	<table id="tblContactList" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
                <thead>
                	<tr class="table-title">
                		<th colspan="7">
                			<p class="drag">Contact Point</p>
                		</th>
                	</tr>
                    <tr>
                        <th class="wp50 t-center">Fullname</th>
                        <th class="wp50 t-left">Email</th>
                        <th class="wp50 t-center">Mobile</th>
                        <th class="w5 t-center">Ext</th>
                        <th class="w10 t-center">Role</th>
                        <th class="w5 t-center">Contact<br />History</th>
                        <th class="wp30 t-center">Action</th>
                    </tr>
                </thead>
                <tbody id="tbodytblContactList">
                    <?php $this->load->view('contact/contact_content_ajax', array('arrUsers' => $arrUsers)); ?>
                </tbody>
    		</table>
        </div>
		<!-- <div id="pp_tblContactList" class="easyui-pagination"></div> --><br /><br />
        <div id="Escalation">
            <table id="tblEscalation" width="100%" cellpadding="0" cellspacing="0" class="list-zebra">
                <thead>
                <tr class="table-title">
                    <th colspan="8">
                        <p class="drag">Contact Escalation</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div id="divEscalation" class="module easyui-accordion" data-options="animate: false">
                            <div id="incLevel1" title="Incident Level 1" data-options="iconCls:'icon-level',selected:true" style="overflow:auto;padding:10px 10px 0 10px;">
                                <?php $this->load->view('contact/escalation_content_each_incident_level', array('arrEscalationUsers' => $arrEscalationUsers, 'noIncidentLevel' => 1)); ?> 
                            </div>
                            <div id="incLevel2" title="Incident Level 2" data-options="iconCls:'icon-level'" style="padding:10px;">
                                <?php $this->load->view('contact/escalation_content_each_incident_level', array('arrEscalationUsers' => $arrEscalationUsers, 'noIncidentLevel' => 2)); ?>
                            </div>
                            <div id="incLevel3" title="Incident Level 3" data-options="iconCls:'icon-level'" style="padding:10px;">
                                <?php $this->load->view('contact/escalation_content_each_incident_level', array('arrEscalationUsers' => $arrEscalationUsers, 'noIncidentLevel' => 3)); ?>
                            </div>
                            <div id="incLevel4" title="Incident Level 4" data-options="iconCls:'icon-level'" style="padding:10px;">
                                <?php $this->load->view('contact/escalation_content_each_incident_level', array('arrEscalationUsers' => $arrEscalationUsers, 'noIncidentLevel' => 4)); ?>
                            </div>
                            <div id="incLevel5" title="Incident Level 5" data-options="iconCls:'icon-level'" style="padding:10px;">
                                <?php $this->load->view('contact/escalation_content_each_incident_level', array('arrEscalationUsers' => $arrEscalationUsers, 'noIncidentLevel' => 5)); ?>
                            </div>
                            <div id="incLevel6" title="Incident Level 6" data-options="iconCls:'icon-level'" style="padding:10px;">
                                <?php $this->load->view('contact/escalation_content_each_incident_level', array('arrEscalationUsers' => $arrEscalationUsers, 'noIncidentLevel' => 6)); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div> <!-- end #Escalation -->
    </div>
</div>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/contact_list.pagination.js"></script>
<script type="text/javascript">
	var identifier = 'contact';
    function loadlist(selobj,url,valueattr,nameattr)
    {
        $(selobj).empty();
        $.getJSON(url,{},function(data) 
        {
            if(data == '') {
                $(selobj).append($('<option></option>').val(-1).html('<?php echo STRING_LIST;?>'));
            } else {
                $.each(data, function(i,obj) 
                {
                    $(selobj).append($('<option></option>').val(obj[valueattr]).html(obj[nameattr]));
                }); 
            }         
        }); 
    }

    /* function send_message(userid) {
        var url = base_url + 'contact/contact/load_popup_sms?userid=' + userid;
        CreateFancyBox('a#send_sms_' + userid, url, '70%');
    } */

    /* function list_mobile_user(userid) {
        var url = base_url + 'contact/contact/load_popup_list_mobile_user?userid=' + userid;
        CreateFancyBox('a#list_mobile_user_' + userid, url, '70%', 380);
    } */

    function get_contact_searched_by_user_domain(userDomain) {
        var url = base_url + 'contact/contact/load_popup_user_information?user=' + userDomain + '&identifier=' + identifier;
        CreateFancyBox('#btnSearchByUser', url, '90%', 500);
    }
    
    function ViewActionHistory(strUserId) {
    	var url = base_url + 'contact/contact/view_action_history_of_user?ref_id=' + strUserId;
    	CreateFancyBox('a#view_history_' + strUserId, url, '90%', 300);
    }
    
    function ChooseCauseToLinkAction(userid, action) {
    	var url = base_url + 'contact/contact/load_cause_link?user_id=' + userid + '&action=' + action;
    	if(action == 1) {
    		CreateFancyBox('a#link_action_' + userid, url, '60%', 300);	
    	} else if (action == 2) {
    		CreateFancyBox('a#send_sms_' + userid, url, '60%', 300);
    	}
    	
    }

    $(document).ready(function() {
        $('#cboDepartment').change(function(event) {
            /* Act on the event */
            var department_selected = $(this).val();
            var url = base_url + 'contact/contact/get_product_list_by_departmentid_json/' + department_selected;
            loadlist($('#cboProduct').get(0), url, 'productid', 'product_name');
            //$('#frmFilter').submit();
        }); 

        $('#cboProduct').change(function(event) {
            /* Act on the event */
            $('#frmFilter').submit();
        });
        
        $('#btnSearchByUser').click(function() {
            var keysearch_user = $('#search_by_user').val();
            if($.trim(keysearch_user).length == 0) {
                alert('You have not entered key search! Please fill it.');        
                return false;
            } else {
                get_contact_searched_by_user_domain(keysearch_user, identifier);
            }

        });

        $('#btnSearchByProduct').click(function(event) {
            /* Act on the event */
            var product_name = $('#search_by_product').val();
            if($.trim(product_name).length == 0) {
                alert('You have not entered key search! Please fill it.');   
                return false;
            } else {
                var url = base_url + 'contact/contact/load_popup_contact_by_product?product_name=' + product_name;
                CreateFancyBox('#btnSearchByProduct', url, '90%', 450);
            }

        });

        $('#search_by_user').bind('keypress', function(e) {
            /* Act on the event */
            if(e.keyCode == 13) {
                var keysearch = $(this).val();
                if($.trim(keysearch).length == 0) {
                    alert('You have not entered key search! Please fill it.');
                } else {
                    var url = base_url + 'contact/contact/load_popup_user_information?user=' + keysearch + '&identifier=' + identifier;
                    create_fancybox(url, '90%', 500);
                }
                  
            } 
        });

        $('#search_by_product').bind('keypress', function(e) {
            if(e.keyCode == 13) {
                var keysearch = $(this).val();
                if($.trim(keysearch).length == 0) {
                    alert('You have not entered key search! Please fill it.');
                } else {
                    var url = base_url + 'contact/contact/load_popup_contact_by_product?product_name=' + keysearch;
                    create_fancybox(url, '90%', 450);
                }
                  
            } 

        });
    });
</script>