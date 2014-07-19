<?php
define('MONGO_USER', 'u_ma');
define('MONGO_PASS', 'Ma20!3');
define('MONGO_DB', 'monitoring_assistant');
define('MONGO_HOST', '10.30.15.8');
define('MONGO_PORT', 27017);

define('MYSQL_USER', 'quangtm3');
define('MYSQL_PASS', 'quang123');
define('MYSQL_DB', 'test');
define('MYSQL_HOST', '10.40.9.240');
define('MYSQL_PORT', 3306);

define('ZABBIX_SERVER_ID', 2);

define('WSDL_URL', 'http://sdk_ws_dev:8031/SDKService.svc?wsdl');

set_time_limit(0);
ini_set('memory_limit', -1);

define('CLIENT_ID',   'g8');
define('CLIENT_KEY',  'g89*209!$st');
define('OPERATION',   'OpenIncidentByG8');
define('DATA_FORMAT', 'JSON');

define('OPEN', 1);
define('UPDATE', 2);

define('DEBUG', FALSE);

$unixtime=time();

$pass=$unixtime.CLIENT_KEY;
$tt = $unixtime."";
$inc_id = "TEST-G8-" . date('YmdHis');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	#pd($_FILES);
	#$m = new Mongo(sprintf('mongodb://%s:%s@%s:%s/%s', MONGO_USER, MONGO_PASS, MONGO_HOST, MONGO_PORT, MONGO_DB));
	#$db = $m->selectDB(MONGO_DB);
	#$collection = new MongoCollection($db, 'g8_alerts');

	#$collection->insert($itemApp);
	$arrFilename = array();
	$arrFileData = array();
	foreach($_FILES as $oUF){
		if($oUF['error'] == 0){
			$arrFilename[] = $inc_id . '_' . str_replace(array(" ", "-"), "_", $oUF['name']);
			$arrFileData[] = file_get_contents($oUF['tmp_name']);
		}
	}
	
	if(!DEBUG){
		$clientClass = 'SoapClient';
		$client = new $clientClass(WSDL_URL,array("trace"=> 1,"exceptions" => 1,"cache_wsdl" => 0,"soap_version" => SOAP_1_1));
	} else {
		$clientClass = 'DebugSoapClient';
		$client = new $clientClass(WSDL_URL,array("trace"=> 1,"exceptions" => 1,"cache_wsdl" => 0,"soap_version" => SOAP_1_1));
		#$client->sendRequest = false;
		#$client->printRequest = true;
		#$client->formatXML = false;
	}
		
	try {
		$arrData = array(
			"AffectedDeals"				=> $_POST['data']['affected_deals'],
			"CCList"					=> $_POST['data']['to_email_list'],
			"ToList"					=> $_POST['data']['cc_email_list'],
			"ImpactedCustomerCount"		=> $_POST['data']['case'],
			"IncidentCode"				=> $inc_id,
			"OutageStart"				=> $unixtime,
			"ProductAlias"				=> '123VN',
			"Action"					=> OPEN,
			"Title"						=> $_POST['data']['title'],
			"Description"				=> $_POST['data']['description'],
			"AttachmentList"			=> implode(';', $arrFilename)
		);
		$data = json_encode($arrData);
		$param = array(
			"ClientId"			=> CLIENT_ID,
			"Checksum"			=> md5($pass),
			"Operation"			=> OPERATION,
			"RequestData"		=> $data,
			"DataFormat"		=> DATA_FORMAT,
			"RequestTime"		=> $unixtime,
			"ClientIdField"		=> CLIENT_ID,
			"ChecksumField"		=> md5($pass),
			"OperationField"	=> OPERATION,
			"RequestDataField"	=> $data,
			"DataFormatField"	=> DATA_FORMAT,
			"RequestTimeField"	=> $unixtime,
			"PostData"			=> $arrFileData
		);

		$var = new SoapVar($param, SOAP_ENC_OBJECT, "SDKPostRequest", "http://schemas.datacontract.org/2004/07/SDKService");
		$client->Call(array("request" => $var));
		print_r($client->__getLastRequest());
	} catch (Exception $e) {
		echo 'Caught exception: ', $e->getMessage(), "\n";
	}
}
?>
<html>
	<form method="post" enctype="multipart/form-data">
		<table cellpadding="0" cellspacing="5">
			<tr>
				<th>Affected Deals</th>
				<td><input type="text" class="w1" name="data[affected_deals]" value="VNN2013091400683; VNN2013091302501; VNN2013091001507"></td>
			</tr>
			<tr>
				<th>Num Of Case</th>
				<td><input type="text" class="w1" name="data[case]" value="100"></td>
			</tr>
			<tr>
				<th>To Mail</th>
				<td><input type="text" class="w1" name="data[to_email_list]" value="thuyntt4@vng.com.vn"></td>
			</tr>
			<tr>
				<th>CC Mail</th>
				<td><input type="text" class="w1" name="data[cc_email_list]" value="thuyntt4@vng.com.vn"></td>
			</tr>
			<tr>
				<th>Description</th>
				<td><textarea type="text" class="w1" name="data[description]">Description Test</textarea></td>
			</tr>
			<tr>
				<th>Title</th>
				<td><textarea type="text" class="w1" name="data[title]">Title Test</textarea></td>
			</tr>
			<tr>
				<th>File 1</th>
				<td><input type="file" name="file1"></td>
			</tr>
			<tr>
				<th>File 2</th>
				<td><input type="file" name="file2"></td>
			</tr>
			<tr>
				<th>File 3</th>
				<td><input type="file" name="file3"></td>
			</tr>
			<tr>
				<th>File 4</th>
				<td><input type="file" name="file4"></td>
			</tr>
			<tr>
				<th>File 5</th>
				<td><input type="file" name="file5"></textarea></td>
			</tr>
		</table>
		<input type="submit" value="Submit">
	</form>
</html>
<?php
function d()
{
  die("123456");
}
// ---------------------------------------------------------------------------------------------- //
function p($v)
{
  echo('<pre>');
  print_r($v);
  echo('</pre>');
}
// ---------------------------------------------------------------------------------------------- //
function pd($v)
{
  echo('<pre>');
  print_r($v);
  echo('</pre>');
  d();
}
// ---------------------------------------------------------------------------------------------- //
class DebugSoapClient extends SoapClient {
  public $sendRequest = true;
  public $printRequest = false;
  public $formatXML = false;

  public function __doRequest($request, $location, $action, $version, $one_way=0) {
    if ( $this->printRequest ) {
      if ( !$this->formatXML ) {
        $out = $request;
      }
      else {
        #$doc = new DOMDocument;
        #$doc->preserveWhiteSpace = false;
        #$doc->loadxml($request);
        #$doc->formatOutput = true;
        #$out = $doc->savexml();
      }
      #echo $out;
    }

    if ( $this->sendRequest ) {
      return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
    else {
      return '';
    }
  }
}
// ---------------------------------------------------------------------------------------------- //
?>