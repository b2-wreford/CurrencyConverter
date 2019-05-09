<?php
@date_default_timezone_set("GMT");
require '../config.php';
require '../functions.php';

$currency_code = $_POST['currency_code'];
$rate = $_POST['rate'];
$method = $_POST['method'];
$timestamp = time();

$currencies_xml = simplexml_load_file("../data/currencies.xml");
$rates_xml = simplexml_load_file("../data/rates.xml");

if($currency_code != strtoupper($currency_code) || $currency_code == '') {
  $xml = error_message_forms(2200, $error_hash, $method);
  header("Content-Type: text/xml");
  echo $xml;
  exit;
}

if(!in_array($currency_code, $c_codes)) {
  $xml = error_message_forms(2400, $error_hash, $method);
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

if(!file_exists("../data/currencies.xml") || !file_exists("../data/rates.xml")) {
  $xml = error_message_forms(2500,  $error_hash, $method);
  echo $xml;
  exit;
}

//updates rate
$dom = new DOMDocument("1.0");
$dom->load('../data/rates.xml');
$xp = new DomXPath($dom);

$initial_rate = $xp->query('//rate[@code="'.$currency_code.'"]');
$initial_rate = $initial_rate->item(0)->getAttribute('value');

$res = $xp->query('//rate[@code="'.$currency_code.'"]');
$res->item(0)->setAttribute("value", $rate);
$res->item(0)->setAttribute("timestamp", $timestamp);
$dom->save('../data/rates.xml');

$cntry = $currencies_xml->xpath('//currency[ccode[text()="'.$currency_code.'"]]/cntry');
$cname = $currencies_xml->xpath('//currency[ccode[text()="'.$currency_code.'"]]/cname');

header("Content-Type: text/xml");

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<method type="post">';
  $xml .= "<at>".date("d F Y H:i", $timestamp)."</at>";
  $xml .= "<prev>";
    $xml .= "<rate>".$initial_rate."</rate>";
    $xml .= "<curr>";
      $xml .= "<code>".$currency_code."</code>";
      $xml .= "<name>".$cname[0]."</name>";
      $xml .= "<loc>".$cntry[0]."</loc>";
    $xml .= "</curr>";
  $xml .= "</prev>";
  $xml .= "<new>";
  $xml .= "<rate>".$rate."</rate>";
    $xml .= "<curr>";
      $xml .= "<code>".$currency_code."</code>";
      $xml .= "<name>".$cname[0]."</name>";
      $xml .= "<loc>".$cntry[0]."</loc>";
    $xml .= "</curr>";
  $xml .= "</new>";
$xml .= "</method>";


echo $xml;
?>
