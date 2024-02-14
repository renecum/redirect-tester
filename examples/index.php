<?php
require_once __DIR__ .'/../vendor/autoload.php';

use RedirectTester\Tester;

$domain = 'foobar.com';
$filename = 'filename.csv';
$tester = new \RedirectTester\Tester($domain);
$tester->appendToURLs("?tztest=true");
$tester->testFile($filename);
$tester->reportResults();
