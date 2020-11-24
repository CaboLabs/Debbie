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
         $test_cases[$test_case] = $test_case_object;
      }
   }

   public function run()
   {
      foreach ($this->test_cases as $test_case_class => $test_case_object)
      {
         $test_names = get_class_methods($test_case_object);
         foreach ($test_names as $test_name)
         {
            // execute only methods that starts with 'test'
            if (strncmp($test_name, 'test', strlen('test')) === 0)
            {
               try
               {
                  call_user_func(array($test_case_object, $test_name));
               }
               catch (\Exception $e)
               {
                  ob_start();
                  debug_print_backtrace(); // Stack de llamadas que resultaron en un test que falla
                  $trace = ob_get_contents();
                  $more_info = ob_get_contents(); // Todos los echos y prints que se pudieron hacer
                  ob_end_clean();

                  // Se quita la llamada a este metodo de el stack (assert)
                  $pos = strpos($trace, "\n");
                  if ($pos !== false)
                  {
                     $trace = substr($trace, $pos);
                  }

                  // TODO: hay que remover las ultimas lineas que son llamadas del framework
                  /*
                  #4  CoreController->testAppAction(Array ()) called at [C:\wamp\www\YuppPHPFramework\core\mvc\core.mvc.YuppController.class.php:59]
                  #5  YuppController->__call(testApp, Array ())
                  #6  CoreController->testApp() called at [C:\wamp\www\YuppPHPFramework\core\routing\core.routing.Executer.class.php:163]
                  #7  Executer->execute() called at [C:\wamp\www\YuppPHPFramework\core\web\core.web.RequestManager.class.php:158]
                  #8  RequestManager::doRequest() called at [C:\wamp\www\YuppPHPFramework\index.php:94]
                  */

                  $this->report($test_case_class, 'EXCEPTION', $e->getMessage(), $trace, $more_info);
               }
            }
         }
      }
   }

   public function report($test_case_class, $type, $msg, $trace = '', $moreInfo = '', $params = array())
   {
      // Esto se podria poner en la vista
      // Muestra variables con valor y tipo
      $_params = '';
      foreach ($params as $key=>$value)
      {
         $_params .= $key.'='.$value.'('.gettype($value).')'."\n";
      }

      //$this->reports[] = array('type'=>$type, 'msg'=>$msg, 'trace'=>$trace, 'moreInfo'=>$moreInfo, 'params'=>$_params);
      if (!isset($this->reports[$test_case_class])) $this->reports[$test_case_class] = array();
      $this->reports[$test_case_class][] = array('type'=>$type, 'msg'=>$msg, 'trace'=>$trace, 'moreInfo'=>$moreInfo, 'params'=>$_params);
   }

   public function get_reports()
   {
      return $this->reports;
   }

   public function render_reports()
   {
      // TODO
   }
}
?>
