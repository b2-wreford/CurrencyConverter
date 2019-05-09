<?php
@date_default_timezone_set("GMT");
require 'config.php';
require 'functions.php';

extract($_GET);
if(isset($from)) $from = strtoupper($from);
if(isset($to)) $to = strtoupper($to);

if(!isset($format) || $format == '') {
  $format = 'xml';
}

//gets all the parameters from the URL and to be used later for validation
$compare_params = array_intersect($input_params, array_keys($_GET));

//if less than four params echo the first error message
if(count($_GET) < 4) {
  echo error_message(1000, $error_hash, $format);
  exit;
}

//checks that even if there are four parameters that they are the correct parameters we are looking for
if(count($compare_params) < 4) {
  echo error_message(1100, $error_hash, $format);
  exit;
}

$rates_xml = simplexml_load_file('data/rates.xml');
$currencies_xml = simplexml_load_file('data/currencies.xml');
$currency_codes = get_ccodes($rates_xml);

//checks entered currency codes are valid for to and from
if(!in_array($from, $currency_codes) || !in_array($to, $currency_codes)) {
  echo error_message(1200, $error_hash, $format);
  exit;
}

//checks the amnt enter is in the correct format to two decimal places using the preg_match
if (!preg_match('/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/', $amnt)) {
  echo error_message(1300,  $error_hash, $format);
  exit;
}

//checks format
if(!in_array(strtolower($format), $data_format)) {
  echo error_message(1400, $error_hash, $format);
  exit;
}

$cntry_array = get_all_cntry($currencies_xml);
$timestamps = $rates_xml->xpath('//@timestamp');
$rates_xml = check_timestamps($timestamps);

if(!$rates_xml || !$currencies_xml) {
  echo error_message(1500, $error_hash, $format);
  //run a function to create the files here?
  exit;
}
//rates
$to_rate = get_rate($to, $rates_xml);
$to_rate_rounded = round((float)$to_rate[0], 2, PHP_ROUND_HALF_UP); // ????????????
$from_rate = get_rate($from, $rates_xml);
//timestamps
$to_time_stamp = get_timestamp($to, $rates_xml);
$from_time_stamp = get_timestamp($from, $rates_xml);
$time_stamp = date("d M Y H:i", (int)$from_time_stamp[0]);
//country names
$from_cname = get_cname($from, $currencies_xml);
$from_loc = get_cntry($from, $currencies_xml);
$to_cname = get_cname($to, $currencies_xml);
$to_loc = get_cntry($to, $currencies_xml);
//exchanged rate
$exchanged_rate = $amnt * (float)$to_rate_rounded;
$exchanged_rate_rounded = round((float)$exchanged_rate, 2, PHP_ROUND_HALF_UP);


//builds an associate array of values and keys to be passed to the function build the xml
$responses =  [
  'last_updated' => $time_stamp,
  'rate' => $to_rate_rounded,
  'from_code' => $from,
  'from_cname' => (string) $from_cname[0],
  'from_loc' => (string) $from_loc[0],
  'from_amnt' => $amnt,
  'to_code' => $to,
  'to_cname' => (string) $to_cname[0],
  'to_loc' => (string) $to_loc[0],
  'to_amnt' => $exchanged_rate_rounded,
];

$response_xml = response($responses);

if($format == 'xml') {
  header("Content-Type:text/xml");
  echo $response_xml;
} else {
  $json = simplexml_load_string('<conv>'.$response_xml.'</conv>');
  header('Content-Type: application/json');
  echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>
