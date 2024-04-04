<?php

namespace CaboLabs\Debbie;

class DebbieSuite {

   // name of the folder where the test cases for the suite are defined
   // should be the prefix of the $test_cases paths
   private $test_suite_name;

   // paths to test case files
   private $test_cases = [];

   // Reportes de resultados del test
   private $reports = [];

   // total execution time in microseconds
   private $execution_time;

   function __construct($test_suite_name, $test_cases = [])
   {
      $this->test_suite_name = $test_suite_name;

      // load test cases from their files
      foreach ($test_cases as $test_case => $test_case_path)
      {
         require_once($test_case_path);

         // if the file found doesn't define a DebbieTestCase, do not try to create the test case object
         if (class_exists($test_case) && is_subclass_of($test_case, '\CaboLabs\Debbie\DebbieTestCase'))
         {
            // test case object has a reference to it's test suite
            $test_case_object = new $test_case($this, $test_case_path);
            $this->test_cases[$test_case] = $test_case_object;
         }
      }
   }

   public function run($after_each_test_callback = NULL, array $specific_methods = [])
   {
      // for execution time calculation
      $test_start_time = microtime(true);

      foreach ($this->test_cases as $test_case_class => $test_case_object)
      {
         $test_names = $specific_methods ? $specific_methods : get_class_methods($test_case_object);

         // execute only methods that starts with 'test'
         $test_names = array_filter($test_names, function ($n)
         {
            return (strncmp($n, 'test', strlen('test')) === 0);
         });

         //print_r($test_names);
         foreach ($test_names as $i => $test_name)
         {
            //echo 'test: '. $test_name . PHP_EOL;
            if (!method_exists($test_case_object, $test_name))
            {
               echo "Method $test_name not found \n";
               continue;
            }

            $this->report_start($test_case_object, $test_name);

            // get all output
            ob_start();

            try
            {
               // invokes the test function
               call_user_func(array($test_case_object, $test_name));
            }
            catch (\Throwable $e) // This catches Exception and Error
            {
               $this->report_exception($test_case_class, $test_name, $e);
            }

            // all echoes and prints that could happen during execution
            $output = ob_get_contents();
            ob_end_clean();

            $this->report_end($test_case_object, $test_name, $output);

            if ($after_each_test_callback)
            {
               // TEST: dont execute for the last one
               //if (($i+1) < count($test_names))
               //{
               //echo "TRUNCATE $i of ". count($test_names) . PHP_EOL;
               $after_each_test_callback();
               //}
            }
         }
      }

      $test_end_time = microtime(true);
      $this->execution_time = round($test_end_time - $test_start_time, 5);
   }

   public function report_start($test_case_object, $test_name)
   {
      $test_case_object->before_test($test_name);
   }

   public function report_end($test_case_object, $test_name, $output)
   {
      $test_case_object->after_test($test_name, $output);
   }

   public function report_assert($test_case_class, $test_name, $type, $msg, $trace = [], $params = [])
   {
      // Esto se podria poner en la vista
      // Muestra variables con valor y tipo
      $_params = '';
      foreach ($params as $key => $value)
      {
         $_params .= $key . '=' . $value . '(' . gettype($value) . ')' . "\n";
      }

      if (!isset($this->reports[$test_case_class]))
      {
         $this->reports[$test_case_class] = [];
      }
      if (!isset($this->reports[$test_case_class][$test_name]))
      {
         $this->reports[$test_case_class][$test_name] = [];
         $this->reports[$test_case_class][$test_name]['asserts'] = [];
      }
      $this->reports[$test_case_class][$test_name]['asserts'][] = array('type' => $type, 'msg' => $msg, 'trace' => $trace, 'params' => $_params);
   }

   public function report_output($test_case_class, $test_case_name, $output)
   {
      if (!isset($this->reports[$test_case_class]))
      {
         $this->reports[$test_case_class] = [];
      }
      if (!isset($this->reports[$test_case_class][$test_case_name]))
      {
         $this->reports[$test_case_class][$test_case_name] = [];
      }
      $this->reports[$test_case_class][$test_case_name]['output'] = $output;
   }

   public function report_exception($test_case_class, $test_name, $exception, $params = [])
   {
      $is_fatal = in_array(get_class($exception), ['Error', 'ErrorException']);
      $_params = '';
      foreach ($params as $key => $value)
      {
         $_params .= $key . '=' . $value . '(' . gettype($value) . ')' . "\n";
      }

      if (!isset($this->reports[$test_case_class]))
      {
         $this->reports[$test_case_class] = [];
      }
      if (!isset($this->reports[$test_case_class][$test_name]))
      {
         $this->reports[$test_case_class][$test_name] = [];
         $this->reports[$test_case_class][$test_name]['asserts'] = [];
      }

      $trace = $exception->getTrace();
      array_pop($trace); // removes call to cli
      array_pop($trace); // removes the call to phtests/cli
      array_pop($trace); // removes call to DebbieRun
      array_pop($trace); // removes call to DebbieSuite

      $this->reports[$test_case_class][$test_name]['asserts'][] = array(
         'type'   => $is_fatal ? 'ERROR' : 'EXCEPTION',
         'msg'    => $exception->getMessage(),
         'trace'  => $trace,
         'params' => $_params
      );
   }

   public function get_reports()
   {
      return $this->reports;
   }

   public function render_reports()
   {
      // TODO
      print_r($this->reports);
   }

   public function get_execution_time()
   {
      return $this->execution_time;
   }
}
