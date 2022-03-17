<?php

require './src/Spider.php';

$codes = require('./src/static/Region.php');

$spider = new Spider();
$standard = $code = $spider->year2020();

$make = new Write();
$make->region($codes, $standard);
$make->regionCode($codes);
$make->postalCode($codes);
$make->telCode($codes);
$make->jsonRegionCode();
$make->regionChildren($codes);