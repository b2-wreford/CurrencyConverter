<?php

//error message function to return xml depending on values passed
function error_message($error_num, $error_hash, $format='xml') {

  $error_msg = $error_hash[$error_num];
  if($format == 'json') {

    $json = array("conv" => array("error" => array("code" => "$error_num", "message" => $error_msg)));
    header('Content-Type: application/json');
    return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

  } else {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= "<conv>";
      $xml .= "<error>";
        $xml .= "<code>" .$error_num. "</code>";
        $xml .= "<msg>" .$error_msg. "</msg>";
      $xml .= "</error>";
    $xml .= "</conv>";

    header("Content-Type:text/xml");
    return $xml;
  }
}

//error messages for the form
function error_message_forms($error_num, $error_hash, $method_type) {

  $error_msg = $error_hash[$error_num];

  $xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $xml .= '<method type="'.$method_type.'">';
    $xml .= "<error>";
      $xml .= "<code>" .$error_num. "</code>";
      $xml .= "<msg>" .$error_msg. "</msg>";
    $xml .= "</error>";
  $xml .= "</method>";

  header("Content-Type:text/xml");
  return $xml;
}
//returns value of rate for a specfic currency code
function get_rate($code, $xml) {
  return $xml->xpath('//rate[@code="'.$code.'"]/@value');
}

//returns currency name from the currency rates file for specifc currency code
function get_cname($code, $xml) {
  return $xml->xpath('//currency[ccode[text()="'.$code.'"]]/cname');
}

//returns the timestamp
function get_timestamp($code, $xml) {
  return $xml->xpath('//rate[@code="'.$code.'"]/@timestamp');
}
//returns the country name for a specific currency code
function get_cntry($code, $xml) {
  return $xml->xpath('//currency[ccode[text()="'.$code.'"]]/cntry');
}

//returns an array of all currency codes currently in rates xml for use in currDelete to check the users enter value matches one currently in the file
function get_ccodes($xml) {
  $all = $xml->xpath('//rate[@code]');
  $ccodes = array();
  foreach($all as $individual) {
    $ccode = (string)$individual['code'];
    $ccodes[] = $ccode;
  }
  return $ccodes;
}

//returns an array of all the available country names by splitting the values for cntry for each currency by ',' using the explode function
function get_all_cntry($xml) {
  $all = $xml->xpath('//currency/cntry');
  $countries = array();
  foreach($all as $groups) {
    //$implode = implode(" ", $individual);
    $xplode = explode(",", $groups);
    foreach($xplode as $individual) {
      $countries[] = trim($individual);
    }
  }
  return $countries;
}

//returns an array of all available currency codes worldwide from the currencies xml file for use when adding a new currency to the rates xml file that it;s a valid code for a currency
function get_all_codes($xml) {
  $all = $xml->xpath('//ccode');
  $ccodes = array();
  foreach($all as $individual) {
    $ccode = (string)$individual;
    $ccodes[] = $ccode;
  }
  return $ccodes;
}

//function for generating the responses
function response(&$response) {
  $xml = "<conv>\n";
    $xml .= "<at>".$response['last_updated']."</at>\n";
    $xml .= "<rate>".$response['rate']."</rate>\n";
      $xml .= "<from>";
        $xml .= "<code>".$response['from_code']."</code>\n";
        $xml .= "<curr>".$response['from_cname']."</curr>\n";
        $xml .= "<loc>".$response['from_loc']."</loc>\n";
        $xml .= "<amnt>" .$response['from_amnt']. "</amnt>\n"; //NEEDS TO BE DECIMAL PLACE
      $xml .= "</from>\n";
      $xml .= "<to>\n";
        $xml .= "<code>".$response['to_code']."</code>\n";
        $xml .= "<curr>".$response['to_cname']."</curr>\n";
        $xml .= "<loc>".$response['to_loc']."</loc>\n";
        $xml .= "<amnt>".$response['to_amnt']."</amnt>\n";
      $xml .= "</to>";
  $xml .= "</conv>";

return $xml;
}


//function for checking the time stamps foreach value in the rates xml file and one happens to be over 12 hours, will run the script for updating the file and then returning the result back otherwise just
// provides the link to the rates file
function check_timestamps($time_stamps) {

  $current_time = time();
  $TWELVE_HOURS = 60 * 60 * 12;
  $is_over_twelve = false;

  // foreach($time_stamps as $time_stamp) {

  //   $delta_time = $current_time - $time_stamp;

  //   if($delta_time > $TWELVE_HOURS) {
  //     include 'create_rates_xml.php';
  //     $rates_xml = simplexml_load_file('data/rates.xml');
  //     return $rates_xml;
  //     exit;
  //   }
  // }

  $rates_xml = simplexml_load_file('data/rates.xml');
  return $rates_xml;
}
?>
