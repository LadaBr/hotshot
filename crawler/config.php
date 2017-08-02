<?php
// OC
/*
$oc_servername = 'hotshot.sk';
$oc_dbname = "zadmin_ocdev";
$oc_username = "development";
$oc_password = "adejyra8e";
$oc_image_path = "/image/";

$oc_url = 'http://oc.dev.hotshot.sk';
*/
// PS
/*
$ps_servername = 'hotshot.sk';
$ps_dbname = "zadmin_psdev16";
$ps_username = "development";
$ps_password = "adejyra8e";
$ps_key = "47IRY99YPTJ18YAYVDTCBAK8K3ER9ZIR";
$ps_home_category_id = 2;

$ps_url = 'http://ps.dev.hotshot.sk';



$oc_servername = '85.255.3.225';
$oc_dbname = "zadmin_toppredajold";
$oc_username = "toppredajold";
$oc_password = "te2e9ynuq";
$oc_image_path = "/image/";
$oc_db_prefix = "toppred_";

$oc_url = 'http://85.255.3.225';


// Create connection
$oc_db = new mysqli($oc_servername,$oc_username,$oc_password,$oc_dbname);
// Check connection
if ($oc_db->connect_error) {
    die("Connection failed: " . $oc_db->connect_error);
} 
mysqli_set_charset($oc_db,"utf8");
*/

$ps_servername = 'localhost';
$ps_dbname = "terra_toppredaj";
$ps_username = "toppredaj";
$ps_password = "4x45j2aax";
$ps_key = "8T7N4MR17BK6G68JTUVQR9PPJH53EZSN";
$ps_home_category_id = 499;
$ps_db_prefix = "ps_";

$ps_url = 'http://toppredaj.sk/';

// Create connection
$ps_db = new mysqli($ps_servername,$ps_username,$ps_password,$ps_dbname);
// Check connection
if ($ps_db->connect_error) {
    die("Connection failed: " . $ps_db->connect_error);
} 
mysqli_set_charset($ps_db,"utf8");

function pprint($var) {
    print_r("<pre>");
    print_r($var);
    print_r("</pre>");
}

class SimpleXMLExtended extends SimpleXMLElement{ 
  public function addCData($cdata_text){ 
   $node= dom_import_simplexml($this); 
   $no = $node->ownerDocument; 
   $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
}

require 'PSWebServiceLibrary.php';




$webService = new PrestaShopWebservice($ps_url, $ps_key, true);

$scheme = array(
    "products" => $webService -> get(array('url' => $ps_url . '/api/products?schema=blank')),
    "categories" => $webService -> get(array('url' => $ps_url . '/api/categories?schema=blank')),
    "manufacturers" => $webService -> get(array('url' => $ps_url . '/api/manufacturers?schema=blank')),
    "stock_availables" => $webService -> get(array('url' => $ps_url . '/api/stock_availables?schema=blank')),
    "combinations" => $webService -> get(array('url' => $ps_url . '/api/combinations?schema=blank')),
    "product_option_values" => $webService -> get(array('url' => $ps_url . '/api/product_option_values?schema=blank')),
    "product_options" => $webService -> get(array('url' => $ps_url . '/api/product_options?schema=blank')),
    "languages" => $webService -> get(array('url' => $ps_url . '/api/languages?schema=blank')),

);


?>