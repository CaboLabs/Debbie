<?php

namespace phtest;

abstract class PhTestCase {


   // path to the test case
   private $path;

   // PhTestSuite
   private $suite;

   // test being executed
   private $current_test;

   function __construct($suite, $path)
   {
      $this->suite = $suite;
      $this->path = $path;
   }

   // invoked before one test is executed in this test case
   public function before_test($test_name)
   {
      $this->current_test = $test_name;
   }

   // invoked after one test is executed in this test case, passing the total output
   public function after_test($test_name, $output)
   {
      $this->current_test = NULL;
      $this->suite->report_output(get_class($this), $test_name, $output);
   }

   // test: assert should be an outside function to the calling trace to have the line number were the assert was called
   /*
   public function get_calling_class() {

      //get the trace
      $trace = debug_backtrace();

      print_r($trace);
  
      // Get the class that is asking for who awoke it
      $class = $trace[1]['class'];
  
      // +1 to i cos we have to account for calling this function
      for ( $i=1; $i<count( $trace ); $i++ ) {
          if ( isset( $trace[$i] ) ) // is it set?
               if ( $class != $trace[$i]['class'] ) // is it a different class
                   return $trace[$i];//['class'];
      }
  }
  */

   public function assert($cond, $msg = '', $params = array())
   {
//       echo 'calling class'. PHP_EOL;
// print_r($this->get_calling_class());
// echo PHP_EOL;

      // TODO: obtener un mensaje que diga mas, linea, clase y
      //       metodo donde se intenta verificar la condicion
      //if (!$cond) $this->suite->report('error');
      if (!$cond)
      {
         // http://php.net/manual/en/function.debug-backtrace.php
         $trace = debug_backtrace(0);
         array_shift($trace); // removes the call to assert()
         array_pop($trace); // removes the call to cli.run_cases()

         $this->suite->report_assert(get_class($this), $this->current_test, 'ERROR', $msg, $trace, $params);
      }
      else
      {
         // tengo que mostrar los tests correctos
         $this->suite->report_assert(get_class($this), $this->current_test, 'OK', $msg);
      }
   }
}

?>
