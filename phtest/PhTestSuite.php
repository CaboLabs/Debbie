<?php

namespace phtest;

class PhTestSuite {

   // name of the folder where the test cases for the suite are defined
   // should be the prefix of the $test_cases paths
   private $test_suite_name;

   // paths to test case files
   private $test_cases;

   // Reportes de resultados del test
   private $reports = array();

   function __construct($test_suite_name, $test_cases = array())
   {
      $this->test_suite_name = $test_suite_name;

      // load test cases from their files
      foreach ($test_cases as $test_case => $test_case_path)
      {
         require_once($test_case_path);

         // test case object has a reference to it's test suite
         $test_case_object = new $test_case($this, $test_case_path);
         $this->test_cases[$test_case] = $test_case_object;
      }

      //print_r($this->test_cases);
   }

   public function run()
   {
      foreach ($this->test_cases as $test_case_class => $test_case_object)
      {
         $test_names = get_class_methods($test_case_object);
         //print_r($test_names);
         foreach ($test_names as $test_name)
         {
            // execute only methods that starts with 'test'
            if (strncmp($test_name, 'test', strlen('test')) === 0)
            {
               // TODO: grab al lthe output even from correct running (try { })
               echo 'test: '. $test_name . PHP_EOL;

               $this->report_start($test_case_object, $test_name);

               // get all output
               ob_start();

               try
               {
                  // invokes the test function
                  call_user_func(array($test_case_object, $test_name));
               }
               catch (\Exception $e)
               {
                  // TODO
                  $trace = debug_backtrace(0);
                  array_shift($trace); // removes the call to assert()
                  array_pop($trace); // removes the call to cli.run_cases()

  
                  $this->report_exception($test_case_class, $test_name, $e, $trace);
               }

               // all echoes and prints that could happen during execution
               $output = ob_get_contents();

               ob_end_clean();

               $this->report_end($test_case_object, $test_name, $output);
            }
         }
      }
   }

   public function report_start($test_case_object, $test_name)
   {
      $test_case_object->before_test($test_name);
   }

   public function report_end($test_case_object, $test_name, $output)
   {
      $test_case_object->after_test($test_name, $output);
   }

   public function report_assert($test_case_class, $test_name, $type, $msg, $trace = array(), $params = array())
   {
      // Esto se podria poner en la vista
      // Muestra variables con valor y tipo
      $_params = '';
      foreach ($params as $key=>$value)
      {
         $_params .= $key.'='.$value.'('.gettype($value).')'."\n";
      }

      if (!isset($this->reports[$test_case_class]))
      {
         $this->reports[$test_case_class] = array();
      }
      if (!isset($this->reports[$test_case_class][$test_name]))
      {
         $this->reports[$test_case_class][$test_name] = array();
         $this->reports[$test_case_class][$test_name]['asserts'] = array();
      }
      $this->reports[$test_case_class][$test_name]['asserts'][] = array('type'=>$type, 'msg'=>$msg, 'trace'=>$trace, 'params'=>$_params);
   }

   public function report_output($test_case_class, $test_case_name, $output)
   {
      if (!isset($this->reports[$test_case_class]))
      {
         $this->reports[$test_case_class] = array();
      }
      if (!isset($this->reports[$test_case_class][$test_case_name]))
      {
         $this->reports[$test_case_class][$test_case_name] = array();
      }
      $this->reports[$test_case_class][$test_case_name]['output'] = $output;
   }

   public function report_exception($test_case_class, $test_name, $exception, $trace = array(), $params = array())
   {
      $_params = '';
      foreach ($params as $key=>$value)
      {
         $_params .= $key.'='.$value.'('.gettype($value).')'."\n";
      }

      if (!isset($this->reports[$test_case_class]))
      {
         $this->reports[$test_case_class] = array();
      }
      if (!isset($this->reports[$test_case_class][$test_name]))
      {
         $this->reports[$test_case_class][$test_name] = array();
         $this->reports[$test_case_class][$test_name]['asserts'] = array();
      }
      $this->reports[$test_case_class][$test_name]['asserts'][] = array('type'=>'EXCEPTION', 'msg'=>$exception->getMessage(), 'trace'=>$trace, 'params'=>$_params);
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
}
?>
