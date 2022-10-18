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
 * argv[4] -> -report=html (optional)
 * argv[5] -> -output=./output/out.html (optional)
 * */

$report_html = false;

$report_html_path = null;

if (in_array('-report=html', $argv))
{
   $report_html = true;

   $e = array_search('-report=html', $argv);

   unset($argv[$e]);
   $argc--;
}

if (in_array('-output=', $argv))
{
   $report_html_path = end($argv);

   $f = array_search('-output=', $argv);

   unset($argv[$f]);
   $argc--;

   array_pop($argv);
   $argc--;
}

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

// HTML test report generator
if ($report_html && $report_html_path == null)
{
   $run->render_reports_html();
}
else
if ($report_html && $report_html_path != null)
{
   $run->render_reports_html($report_html_path);
}
else
if (!$report_html && $report_html_path != null)
{
   echo 'missing parameters, this should contain "-report=html" before "'. $report_html_path .'"'. PHP_EOL;
   exit;
}
else
{
   $run->render_reports();
}

?>