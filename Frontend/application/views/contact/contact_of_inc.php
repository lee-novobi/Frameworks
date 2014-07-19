<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/gray/easyui.css" />
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
                                                <option value="<?php echo $objOneDepartment['departmentid'];?>" <?php if($objOneDepartment['departmentid'] == @$_POST['department'] || (@$iDepartmentSelected == $objOneDepartment['departmentid'])) {?>selected="selected"<?php } ?>><?php echo $objOneDepartment['name'];?></option>
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
                                                <option value="<?php echo $objOneProduct['productid'];?>" <?php if($objOneProduct['productid'] == @$_POST['product'] || (@$iProductIdSelected == $objOneProduct['productid'])) {?>selected="selected"<?php } ?>><?php echo $objOneProduct['name'];?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td colspan="2" class="t-center"><input type="hidden" id="btnFilter" class="search" name="btnFilter" value="Search" style="padding: 3px 15px !important;" /></td>
                                </tr>
                            </table>
                            <input type="hidden" id="hidIncidentId" value="<?php echo $strIncidentId; ?>" name="incident_id">
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
        </div> <!-- end #accor --><br/>
        <div id="Contact">
    		<?php echo $strContactView; ?>
        </div> <!-- end div#Contact -->
    </div>
</div>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/contact_list.pagination.js"></script>
<script type="text/javascript">
	var inc_id = '<?php echo $strIncidentId; ?>';
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

    function send_message(userid) {
        var url = base_url + 'contact/contact/load_popup_sms?userid=' + userid + '&incident_id=' + inc_id;
        CreateFancyBox('a#send_sms_' + userid, url, '70%');
    }

    function list_mobile_user(userid) {
        var url = base_url + 'contact/contact/load_popup_list_mobile_user?userid=' + userid + '&incident_id=' + inc_id;
        CreateFancyBox('a#list_mobile_user_' + userid, url, '70%', 380);
    }

    function get_contact_searched_by_user_domain(userDomain, incident_id) {
        var url = base_url + 'contact/contact/load_popup_user_information?user=' + userDomain + '&incident_id=' + incident_id;
        CreateFancyBox('#btnSearchByUser', url, '90%', 500);
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
                get_contact_searched_by_user_domain(keysearch_user, inc_id);
            }

        });

        $('#btnSearchByProduct').click(function(event) {
            /* Act on the event */
            var product_name = $('#search_by_product').val();
            if($.trim(product_name).length == 0) {
                alert('You have not entered key search! Please fill it.');   
                return false;
            } else {
                var url = base_url + 'contact/contact/load_popup_contact_by_product?product_name=' + product_name + '&call_from=inc&incident_id=' + inc_id;
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
                    var url = base_url + 'contact/contact/load_popup_user_information?user=' + keysearch + '&incident_id=' + inc_id;
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
                    var url = base_url + 'contact/contact/load_popup_contact_by_product?product_name=' + keysearch + '&call_from=inc&incident_id=' + inc_id; 
                    create_fancybox(url, '90%', 450);
                }
                  
            } 

        });
    });
</script>