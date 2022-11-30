<?php

namespace CaboLabs\PhTest;

use \CaboLabs\PhBasic\BasicString as str;

class PhTestRun {

   // root folder where all the test suites have their own folder
   private $test_suite_root = './tests';

   private $reports = array();

   private $after_each_test_function;

   public function init($test_suite_root = './tests')
   {
      if (!is_dir($test_suite_root))
      {
         echo "Folder $test_suite_root doesn't exist\n";
         exit;
      }

      if (!str::endsWith($test_suite_root, DIRECTORY_SEPARATOR))
      {
         $test_suite_root .= DIRECTORY_SEPARATOR;
      }

      $this->test_suite_root = $test_suite_root;
   }

   public function run_all()
   {
      $root_dir = dir($this->test_suite_root);

      if ($root_dir === FALSE)
      {
         echo "Can't read ". $root_dir ."\n";
         exit();
      }

      while (false !== ($test_suite = $root_dir->read()))
      {
         if (!is_dir($this->test_suite_root . $test_suite) ||
             in_array($test_suite, array('.', '..', 'data'))) continue;

         $path = $this->test_suite_root . $test_suite;

         if (!str::endsWith($path, DIRECTORY_SEPARATOR))
         {
            $path .= DIRECTORY_SEPARATOR;
         }

         $suite_dir = dir($path);

         if ($suite_dir === FALSE)
         {
            echo "Can't read ". $suite_dir ."\n";
            exit();
         }

         $test_cases = array();

         // $test_case is a class name
         while (false !== ($test_case = $suite_dir->read()))
         {
            // only php files are valid test cases
            if (preg_match('/\.php$/', $test_case))
            {
               $test_case_path = $path . $test_case;

               $namespaced_class = substr(str_replace(['./', '/'], ['', '\\'], $test_case_path), 0, -4);
               
               if (is_file($test_case_path))
               {
                  $test_cases[$namespaced_class] = $test_case_path;
               }
            }
         }

         $suite_dir->close();

         $phsuite = new PhTestSuite($test_suite, $test_cases);
         $phsuite->run($this->after_each_test_function);
         $this->reports[] = $phsuite->get_reports();
      }

      // TODO: should close all directory handles!
      $root_dir->close();
   }

   public function run_suite($suite)
   {
      // runs all cases in the suite
      $this->run_cases($suite);
   }

   public function run_suites(...$suites)
   {
      // TODO
   }

   public function run_case($suite, $case, $method = NULL)
   {
      $path = $this->test_suite_root . $suite;

      if (!str::endsWith($path, DIRECTORY_SEPARATOR))
      {
         $path .= DIRECTORY_SEPARATOR;
      }

      $test_case_path = $path . $case . '.php';

      $namespaced_class = substr(str_replace(['./', '/'], ['', '\\'], $test_case_path), 0, -4);

      $test_cases = array();
      if (is_file($test_case_path))
      {
         $test_cases[$namespaced_class] = $test_case_path;
      }

      $phsuite = new PhTestSuite($suite, $test_cases);
      $phsuite->run($this->after_each_test_function, $method);
      $this->reports[] = $phsuite->get_reports();
   }

   public function run_cases($suite, ...$cases)
   {
      $path = $this->test_suite_root . $suite;

      if (!str::endsWith($path, DIRECTORY_SEPARATOR))
      {
         $path .= DIRECTORY_SEPARATOR;
      }

      $suite_dir = dir($path);

      if ($suite_dir === FALSE)
      {
         echo "Can't read ". $suite_dir ."\n";
         exit();
      }
      
      $test_cases = array();

      // $test_case is a class name
      while (false !== ($test_case = $suite_dir->read()))
      {
         if (empty($cases))
         {
            $test_case_path = $path . $test_case;
         }
         else
         {
            $test_case_path = $path . $cases[0] . '.php';
         }
         
         $namespaced_class = substr(str_replace(['./', '/'], ['', '\\'], $test_case_path), 0, -4);
           
         if (is_file($test_case_path))
         {
            $test_cases[$namespaced_class] = $test_case_path;
         }
   
      }

      $suite_dir->close();

      $phsuite = new PhTestSuite($suite, $test_cases);
      $phsuite->run($this->after_each_test_function);
      $this->reports[] = $phsuite->get_reports();
   }

   public function render_reports($test_time)
   {
      global $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful;

      $total_cases_failed = $total_cases_successful = array();

      echo 'Test report: '. PHP_EOL . PHP_EOL;
      
      foreach ($this->reports as $i => $test_suite_reports)
      {
         $total_suites ++;

         foreach ($test_suite_reports as $test_case => $reports)
         {
            $successful = 0;
            $failed = 0;

            echo '├── Test case: '. $test_case .'  ── Total case: '. count($reports) . PHP_EOL;
            echo '|   |'. PHP_EOL;

            $total_cases ++;

            foreach ($reports as $test_function => $report)
            {

               echo '|   ├── Test: '. $test_function . PHP_EOL;

               $total_tests ++;

               //print_r($report['asserts']);

               if (isset($report['asserts']))
               {
                  foreach ($report['asserts'] as $assert_report)
                  {
                     if ($assert_report['type'] == 'ERROR')
                     {
                        echo '|   |   |'. PHP_EOL;
                        echo "|   |   └── \033[91mERROR: ". $assert_report['msg'] ."\033[0m". PHP_EOL;
                     
                        $total_failed ++;
                        $failed ++;
                     }
                     else if ($assert_report['type'] == 'OK')
                     {
                        echo '|   |   |'. PHP_EOL;
                        echo "|   |   └── \033[92mOK: ". $assert_report['msg'] ."\033[0m". PHP_EOL;
                     
                        $total_successful ++;
                        $successful ++;
                     }
                     else if ($assert_report['type'] == 'EXCEPTION')
                     {
                        echo '|   |   |'. PHP_EOL;
                        echo "|   |   └── \033[94mEXCEPTION: ". $assert_report['msg'] ."\033[0m". PHP_EOL;
                     }
                  }

                  $total_asserts ++;
               }

               if (!empty($report['output']))
               {
                  echo '|   |   |'. PHP_EOL;
                  echo '|   |   └── OUTPUT: '. $report['output'] . PHP_EOL;
               }

               echo '|   |'. PHP_EOL;

            }

            if ($failed > 0)
            {
               $total_cases_failed[] = [
                  'case' => $test_case,
                  'case_failed' => $failed, 
                  'case_successful' => $successful
               ];
            }

            if ($successful > 0 && $failed == 0)
            {
               $total_cases_successful[] = [
                  'case' => $test_case, 
                  'case_failed' => $failed, 
                  'case_successful' => $successful
               ];
            }
            
         }
      }

      echo PHP_EOL;
      
      $this->get_summary_report($test_time, $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful, $total_cases_failed, $total_cases_successful);
   }

   public function render_reports_html($path, $test_time)
   {
      /** @Var string $html_report, contains the result of all the report suites, test assets
       *  @Var string $name_test_cases, extract only the suite name
       *  @Var string $failed_Summ, renders the content of the summary table failed cases
       *  @Var string $succ_Summ, renders the content of the summary table $successful cases
       *  @Var int $successful, count successful asserts per test
       *  @Var int $failed, count failed asserts per test
       *  @Var string $names, extracts only the names of the suites from the array
       *  @Var int $total_cases, stores the total cases number
       *  @Var int $total_failed, stores the total failed cases number
       *  @Var int $total_successful, stores the total successful cases number
       */
      global $html_report, $content, $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful;

      $total_cases_failed = $total_cases_successful = array();

      $html_report = '';
      $name_test_cases = '';

      $failed_Summ = "";
      $succ_Summ = "";

      foreach ($this->reports as $i => $test_suite_reports) {
         $total_suites++;

         foreach ($test_suite_reports as $test_case => $reports) {
            $successful = 0;
            $failed = 0;

            $names = explode("\\", $test_case);

            $total_cases++;

            $html_report .= '<div
               class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
               <h6 class="m-0 font-weight-bold text-primary">' . $names[2] . '</h6>
               </div>
               <!-- Card Body -->
               <div class="card-body">
               <div>
                  <table class="table">
                     <thead>
                     <tr>
                        <th scope="col">Class</th>
                        <th scope="col">Asserts</th>
                        <th scope="col"></th>
                     </tr>
                     </thead>
                  <tbody><tr>';

            foreach ($reports as $test_function => $report) {
               $html_report .= '<td>' . $test_function . '</td>';

               $total_tests++;

               if (isset($report['asserts'])) {
                  foreach ($report['asserts'] as $assert_report) {
                     if ($assert_report['type'] == 'ERROR') {
                        $html_report .= '<td class ="text-danger">ERROR: ' . $assert_report['msg'] . '</td>';

                        $total_failed++;
                        $failed++;
                     } else if ($assert_report['type'] == 'OK') {
                        $html_report .= '<td class="text-success">OK: ' . $assert_report['msg'] . '</td>';

                        $total_successful++;
                        $successful++;
                     } else if ($assert_report['type'] == 'EXCEPTION') {
                        $html_report .= '<td class="text-primary">EXCEPTION: ' . $assert_report['msg'] . '</td>';
                     }

                     if (!empty($report['output'])) {
                        $html_report .= '<td class="text-secondary">OUTPUT: ' . $report['output'] . '</td>';
                     }
                     $html_report .= '</tr>';
                  }

                  $total_asserts++;
               }
            }
            $html_report .= '</tr></tbody></table><br></div></div>';

            if ($failed > 0) {
               $total_cases_failed[] = [
                  'case' => $test_case,
                  'case_failed' => $failed,
                  'case_successful' => $successful
               ];
            }

            if ($successful > 0 && $failed == 0) {
               $total_cases_successful[] = [
                  'case' => $test_case,
                  'case_failed' => $failed,
                  'case_successful' => $successful
               ];
            }

            $name_test_cases .= '<li class="nav-item">
               <a class="nav-link collapsed" href="#"
                  aria-expanded="true" aria-controls="collapseTwo">';

            if ($failed > 0)
            {
               $name_test_cases .= '<i class="fas fa-times-circle"></i>';
            }
            else
            {
               $name_test_cases .= '<i class="fa fa-check"></i>';
            }
         
            $name_test_cases .= '<span>' . $names[2] . '</span>
               </a>
               </li>';
         }

      }

      if (count($total_cases_failed) >= 1) {
         $failed_cases = count($total_cases_failed);

         foreach ($total_cases_failed as $total_case_failed) {
            $names_failed = explode("\\", $total_case_failed['case']);

            $failed_Summ .= "<tr>
               <td>" . $names_failed[1] . "</td>
               <td>" . $names_failed[2] . "</td>
               <td> " . $total_case_failed['case_successful'] . "</td>
               <td>" . $total_case_failed['case_failed'] . "</td>
            </tr>";
         }
      } else {
         $failed_cases = 0;
      }

      if (count($total_cases_successful) >= 1) {
         $successful_case = count($total_cases_successful);

         foreach ($total_cases_successful as $total_case_successful) {
            $names_successful = explode("\\", $total_case_successful['case']);

            $succ_Summ .= "<tr>
               <td>" . $names_successful[1] . "</td>
               <td>" . $names_successful[2] . "</td>
               <td>" . $total_case_successful["case_successful"] . "</td>
            </tr>";
         }
      } else {
         $successful_case = 0;
      }

      $content = new \CaboLabs\PhTest\PhTestHtmlTemplate;

      $content->Html_template($total_suites, $total_cases, $failed_cases, $successful_case, $html_report, $test_time, $total_tests, $total_successful, $total_failed, $total_asserts, $failed_Summ, $succ_Summ, $name_test_cases);

      if ($path == './') {
         $path = 'test_report.html';
      }

      file_put_contents($path, $content);
   }

   public function get_summary_report($test_time, $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful, $total_cases_failed, $total_cases_successful)
   {
      echo 'Summary reports: '. PHP_EOL . PHP_EOL;

      echo 'Tests reports - Total suites: '.  $total_suites .'  --> total time: '. $test_time .  ' μs' .PHP_EOL;

      echo PHP_EOL;

      echo 'Total tests cases: '. $total_cases . PHP_EOL;

      echo PHP_EOL;

      echo 'Total tests: '. $total_tests . PHP_EOL;

      echo PHP_EOL;

      echo '  asserts failed: '. $total_failed . PHP_EOL;

      echo PHP_EOL;

      echo '  asserts successful: '. $total_successful . PHP_EOL;

      echo PHP_EOL;

      echo '  Total asserts: '. $total_asserts . PHP_EOL;

      echo PHP_EOL;

      echo PHP_EOL;

      if (count($total_cases_failed) >= 1)
      {

         echo 'Cases failed: ('. count($total_cases_failed) . ')'. PHP_EOL;

         echo PHP_EOL;

         foreach ($total_cases_failed as $total_case_failed)
         {
            echo '  '. $total_case_failed["case"] . PHP_EOL;

            echo PHP_EOL;

            echo '    asserts failed: '. $total_case_failed["case_failed"] . PHP_EOL;

            echo PHP_EOL;

            echo '    asserts successful: '. $total_case_failed["case_successful"] . PHP_EOL;

            echo PHP_EOL;
         }
      }
      else
      {
         echo 'Cases failed: 0' . PHP_EOL;

         echo PHP_EOL;
      }

      echo PHP_EOL;

      if (count($total_cases_successful) >= 1)
      {
         echo 'Cases successful: ('. count($total_cases_successful) . ')'. PHP_EOL;

         echo PHP_EOL;

         foreach ($total_cases_successful as $total_case_successful)
         {
            echo '  '. $total_case_successful["case"] . PHP_EOL;

            echo PHP_EOL;

            echo '    asserts failed: '. $total_case_successful["case_failed"] . PHP_EOL;

            echo PHP_EOL;

            echo '    asserts successful: '. $total_case_successful["case_successful"] . PHP_EOL;

            echo PHP_EOL;
         }
      }
      else
      {
         echo 'Cases successful: 0' . PHP_EOL;

         echo PHP_EOL;
      }

      echo PHP_EOL;   
   }

   public function get_reports()
   {
      return $this->reports;
   }

   public function after_each_test($callback)
   {
      $this->after_each_test_function = $callback;
   }
}

?>