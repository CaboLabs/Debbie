<?php

$_BASE = __DIR__ . '/'; // .'/../';

spl_autoload_register(function ($class) {
  global $_BASE;
  //echo $_BASE.str_replace('\\', '/', $class).'.php' . PHP_EOL;
  if (file_exists($_BASE.str_replace('\\', '/', $class).'.php'))
  {
    require_once($_BASE.str_replace('\\', '/', $class).'.php');
  }
});



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

$run = new \phtest\PhTestRun();
$run->init($argv[1]);

// case or cases specific
if ($argc == 4)
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