<?php
ini_set('display_errors', "1");
error_reporting(E_ALL);

// define('WSDL_URL', 'http://internal.shop-esb.123.vn/esb/api/vng.gpi.esb.ecommerce.actionhandler.IActionHandler/callG8BECstool');
define('WSDL_URL', 'http://internal.shop-esb-stag.123.vn/esb/api/vng.gpi.esb.ecommerce.actionhandler.IActionHandler/callG8BECstool');

$data = array(
	'json_send_info' => json_encode(
							array(
								array(
									'code' => 'G8-20131205-0006',
									'date' => '2013-12-05 13:40:56',
									'status' => 'reject'
								)
							)
						),
	'function'       => 'update_status_incident',
	'model'          => 'vng.cstool.sdk.interface'
);
$strData = json_encode($data);
print_r($strData . "\n");
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, WSDL_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $strData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($strData))                                                                       
);

$strResult = curl_exec($ch);
curl_close($ch);


print_r($strResult);
print_r("\n");
?>