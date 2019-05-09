<?php
@date_default_timezone_set("GMT");

$c_codes = ["GBP", "CAD", "CHF", "CNY", "DKK", "EUR", "HKD", "HUF", "INR", "JPY", "MXN", "MYR", "NOK", "NZD", "PHP", "RUB", "SEK", "SGD", "THB", "TRY", "USD", "ZAR"];

$xml = simplexml_load_file('https://finance.yahoo.com/webservice/v1/symbols/allcurrencies/quote?format=xml') or die("Error: Cannot load xml object");
$usd_rate = $xml->xpath('//field[text()="USD/GBP"]/following-sibling::field[@name="price"]');
$gbp_rate = 1 / (float) $usd_rate[0];

$writer =  new XMLWriter();
$writer->openURI('data/rates.xml');
$writer->startDocument("1.0");
$writer->startElement("rates");

$codes = $xml->xpath('//field[@name="name"]');
$values = $xml->xpath('//field[@name="price"]');
$time_stamps = $xml->xpath('//field[@name="ts"]');

foreach($codes as $key=>$code) {

  if(in_array(substr($code, -3), $c_codes)) {
    $writer->startElement("rate");
      $writer->writeAttribute('code', substr($code, -3));

      if(substr($code, -3) == 'GBP') {
        $writer->writeAttribute('value', '1.00');
      } else {
        $gbp_val = (float) $values[$key] * $gbp_rate;
        $writer->writeAttribute('value', $gbp_val);
      }
      $writer->writeAttribute('timestamp', $time_stamps[$key]);
    $writer->endElement();
  }
}

$writer->endDocument();
$writer->flush();
//file_put_contents('../data/rates.xml', $writer->save());
//echo "file created";
?>
