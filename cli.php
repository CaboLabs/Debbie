<?php

$_BASE = __DIR__ . '/';

// composer includes
require __DIR__ . '/vendor/autoload.php';


//print_r($argv);
//print_r($argc);

/*
 * argv[0] -> cli.php
 * argv[1] -> root
 * argv[2] -> suite (optional)
 * argv[3] -> case (optional)
 * */

if ($argc < 2)
{
   echo 'Missing test_root and test_suite'. PHP_EOL;
   exit;
}

$run = new \CaboLabs\PhTest\PhTestRun();
$run->init($argv[1]);

// Method specific
if ($argc > 4)
{
   $methods = array();

   for ($i = 4; $i < $argc; $i++) {
      $methods[] = $argv[$i];
   }

   $run->run_case($argv[2], $argv[3], $methods);
}
// case or cases specific
else if ($argc == 4)
{
   $run->run_cases($argv[2], $argv[3]);
}
// suite specified
else if ($argc == 3)
{
   $run->run_suite($argv[2]);
}
// run all
else
{
   $run->run_all();
}

$run->render_reports();

?>