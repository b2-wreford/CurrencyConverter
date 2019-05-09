<?php
@date_default_timezone_set("GMT");
require '../config.php';
require '../functions.php';

$currency_code = $_POST['currency_code'];
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

$rates_ccodes = get_ccodes($rates_xml);

if(!in_array($currency_code, $rates_ccodes)) {
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


$dom = new DOMDocument("1.0", "utf-8");
$dom->load("../data/rates.xml");

// xpath to select notes for delete
$nodes = $dom->getElementsByTagName("rate");
foreach ($nodes as $n) {
    if($n->getAttribute("code") == $currency_code) {
         $n->parentNode->removeChild($n);
        }
    }

$dom->save("../data/rates.xml");

/*
$dom = new DOMDocument("1.0", "utf-8");
$dom->load("../data/currencies.xml");

// xpath to select nodes for delete
$nodes = $dom->getElementsByTagName("currency");
$xpath = new DOMXPath($dom);
$delete_query = $xpath->query('//currency[ccode[text()="'.$currency_code.'"]]');
foreach ($delete_query as $n) {
        $n->parentNode->removeChild($n);
}

$dom->save("../data/currencies.xml");
*/

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<method type='.$method.'>';
    $xml .= "<at>".date("d F Y H:i", $timestamp)."</at>";
    $xml .= "<code>".$currency_code."</code>";
$xml .= "</method>";


echo $xml;


?>
