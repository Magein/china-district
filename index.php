<?php

require './src/Spider.php';

$codes = require('./src/static/District.php');

//$spider = new Spider();
//$standard = $code = $spider->year2020();

$make = new Write();
//$make->region($codes, $standard);
//$make->regionCode($codes);
//$make->postalCode($codes);
//$make->telCode($codes);
$make->jsonDistrictCode();
$make->districtChildren($codes);



