<?php

namespace phtest;

class PhTestRun {

   // root folder where all the test suites have their own folder
   private $test_suite_root = './tests';

   public function init($test_suite_root = './tests')
   {
      if (!is_dir($test_suite_root))
      {
         echo "Folder $test_suite_root doesn't exist";
         exit;
      }
   }

   public function run_all()
   {
      // TODO
   }

   public function run_suite($suite)
   {
      // TODO
   }

   public function run_suites(...$suites)
   {
      // TODO
   }

   public function run_case($suite, $case)
   {
      // TODO
   }

   public function run_cases($suite, ...$cases)
   {
      $path = $this->test_suite_root . DIRECTORY_SEPARATOR . $suite;
      $suite_dir = dir($path);

      $test_cases = array();

      // $test_case is a class name
      while (false !== ($test_case = $suite_dir->read()))
      {
         $test_case_path = $path.'/'.$test_case;
         
         if (is_file($test_case_path) && in_array($test_case, $cases))
         {
            $test_cases[$test_case] = $test_case_path;
         }
      }

      $phsuite = new PhTestSuite($suite, $test_cases);
      $phsuite->run();
      $phsuite->render_reports();
   }
}

?>