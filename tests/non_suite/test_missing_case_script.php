<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use CaboLabs\Debbie\DebbieRun;

$runner = new DebbieRun();
$runner->init('./tests');
// Simulates executing a test case that does not exist
$runner->run_case('suite_missing_file', 'TestCaseMissing');