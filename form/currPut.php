<?php
@date_default_timezone_set("GMT");
include '../config.php';
include '../functions.php';

$currency_code = $_POST['currency_code'];
$currency_name = $_POST['currency_name'];
$rate = $_POST['rate'];
$cntry = $_POST['cntry'];
$timestamp = time();
$method = $_POST['method'];

$currencies_xml = simplexml_load_file("../data/currencies.xml");
$rates_xml = simplexml_load_file("../data/rates.xml");


//function to get all available currecy codes to be used to check the validity of users entered ccode
$all_codes = get_all_codes($currencies_xml);

//function to all cntry names available to check against inputted country names.
$all_cntry = get_all_cntry($currencies_xml);
$explode_cntry = explode(",", $cntry);

/*if($cntry = '' || !in_array($explode_cntry, $all_cntry)) {
  $xml = error_message_forms(2300, $error_hash, $method);
  header("Content-Type: text/xml");
  echo $xml;
  exit;
}*/

if($currency_code != strtoupper($currency_code) || $currency_code == '') {
  $xml = error_message_forms(2200, $error_hash, $method);
  header("Content-Type: text/xml");
  echo $xml;
  exit;
}

if($rate == '') {
  $xml = error_message_forms(2100, $error_hash, $method);
  header("Content-Type: text/xml");
  echo $xml;
  exit;
}

if(!preg_match('/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/', $rate)) {
  $xml = error_message_forms(2100,  $error_hash, $method);
  echo $xml;
  exit;
}

if(!in_array($currency_code, $all_codes)) {
  $xml = error_message_forms(2400, $error_hash, $method);
  header("Content-Type: text/xml");
  echo $xml;
  exit;
}

if(!file_exists("../data/currencies.xml") || !file_exists("../data/rates.xml")) {
  $xml = error_message_forms(2500,  $error_hash, $method);
  echo $xml;
  exit;
}


$rate = round((float)$rate, 2, PHP_ROUND_HALF_UP);

//add to currencies file

/*$dom = new DOMDocument("1.0", "utf-8");
$dom->load("../data/currencies.xml");


$root = $dom->getElementsByTagName("currencies")->item(0);
$currencyTag = $dom->createElement("currency");

$a = $dom->createElement("ccode", $currency_code);
$b = $dom->createElement("cname", $currency_name);
$c = $dom->createElement("cntry", $cntry);

$currencyTag->appendChild($a);
$currencyTag->appendChild($b);
$currencyTag->appendChild($c);

$root->appendChild($currencyTag);
$dom->save("../data/currencies.xml");
*/

unset($test);
//add to rates file
$dom = new DOMDocument("1.0");
$dom->load("../data/rates.xml");

$root = $dom->getElementsByTagName("rates")->item(0);
$ratesTag = $dom->createElement("rate");
$root->appendChild($ratesTag);


$ratesTag->setAttribute("code", $currency_code);
$ratesTag->setAttribute("value", $rate);
$ratesTag->setAttribute("timestamp", $timestamp);
$dom->save("../data/rates.xml");

header("Content-Type: text/xml");

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<method type="put">';
  $xml .= "<at>".date("d F Y H:i", $timestamp)."</at>";
  $xml .= "<rate>".$rate."</rate>";
    $xml .= "<curr>";
      $xml .= "<code>".$currency_code."</code>";
      $xml .= "<name>".$currency_name."</name>";
      $xml .= "<loc>".$cntry."</loc>";
    $xml .= "</curr>";
$xml .= "</method>";

echo $xml;
?>
