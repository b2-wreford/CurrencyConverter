<?php
$input_params =  ["from", "to", "amnt", "format"];
$data_format = ["xml", "json"];

$error_hash = [
  1000 => 'Required parameter is missing',
  1100 => 'Parameter not recognized',
  1200 => 'Currency type not recognized',
  1300 => 'Currency amount must be a decimal number',
  1400 => 'Format must be xml or json',
  1500 => 'Error in service',
  2000 => 'Method not recognized or is missing',
  2100 => 'Rate in wrong format or is missing',
  2200 => 'Currency code in wrong format or is missing',
  2300 => 'Country name in wrong format or is missing',
  2400 => 'Currency code not found for update',
  2500 => 'Error in service'
];

$c_codes = ["GBP", "CAD", "CHF", "CNY", "DKK", "EUR", "HKD", "HUF", "INR", "JPY", "MXN", "MYR", "NOK", "NZD", "PHP", "RUB", "SEK", "SGD", "THB", "TRY", "USD", "ZAR"];

?>
