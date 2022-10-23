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
 * argv[5] -> -output="./output/out.html" (optional), if no path is specified it will be saved in the root
 * */

$output = './';

$report = 'html';

foreach ($argv as $arg)
{
   $search = substr($arg, 0, 7);

   if ($search == '-report')
   {
      $type_out = explode("=", $arg);
      $report = end($type_out);
      $argc--;
   }

   if ($search == '-output')
   {
      $search_path = explode("=", $arg);
      $output = end($search_path);
      $argc--;
   }
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

if ($report == 'html')
{
   $run->render_reports_html($output);
}
else
if ($report != 'html' && $report != 'text')
{
   echo '"-report=" should be equal to "html" or "text"'. PHP_EOL;
   exit;
}
else
if ($report == 'text')
{
   $run->render_reports();
}
else
{
   $run->render_reports_html($output);
}

?>