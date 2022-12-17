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

      $total_cases_failed = $total_cases_successful = [];

      echo 'Test report: '. PHP_EOL . PHP_EOL;
      
      foreach ($this->reports as $i => $test_suite_reports)
      {
         $total_suites ++;

         foreach ($test_suite_reports as $test_case => $reports)
         {
            $successful = 0;
            $failed = 0;

            echo '├── Test case: '. $test_case .'  (test count: '. count($reports) .')'. PHP_EOL;
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
      /** @var String $html_report, contains the result of all the report suites, test assets
       *  @var String $menu_items, extract only the suite name
       *  @var String $menu_subitems, extract only the tests name
       *  @var String $failed_Summ, renders the content of the summary table failed cases
       *  @var String $succ_Summ, renders the content of the summary table $successful cases
       *  @var int $successful, count successful asserts per test
       *  @var int $failed, count failed asserts per test
       *  @var String $names, extracts only the names of the suites from the array
       *  @var int $total_cases, stores the total cases number
       *  @var int $total_failed, stores the total failed cases number
       *  @var int $total_successful, stores the total successful cases number
       */
      global $html_report, $content, $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful;

      $total_cases_failed = $total_cases_successful = [];
      $namesSuitessubmenu = [];

      $html_report = '';
      $menu_items = '';

      $failed_Summ = "";
      $succ_Summ = "";

      $h = 0;
      $c = 0;

      foreach ($this->reports as $i => $test_suite_reports) 
      {
         $total_suites++;

         foreach ($test_suite_reports as $test_case => $reports) 
         {
            $successful = 0;
            $failed = 0;

            $names = explode("\\", $test_case);

            $namesSuitesMenu[] = $names[1];

            $namesSuitessubmenu[] = $test_case;

            $total_cases++;

            $html_report .= ' <!-- Content Row -->
            <div id="card_tests' . $names[1] . $c .'" class="card_' . $names[1] . ' suites_test" style="display:none;">
               <div class="row" id = "card_' . $names[2] . '">
                  <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div  class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                           <h6 class="m-0 font-weight-bold text-primary">' . $names[2] . '</h6>
                        </div>
                           <!-- Card Body -->
                        <div class="card-body">
                           <table class="table table-borderless" style="margin: -0.5rem;">
                              <thead>
                                 <tr class ="border-bottom">
                                    <th scope="col">Class</th>
                                    <th scope="col">Asserts</th>
                                    <th scope="col"></th>
                                 </tr>
                              </thead>
                              <tbody><tr>';
            
            foreach ($reports as $test_function => $report) 
            {
               $html_report .= '<td>' . $test_function . '</td>';
               
               $total_tests++;

               if (isset($report['asserts'])) 
               {
                  foreach ($report['asserts'] as $assert_report) 
                  {
                     if ($assert_report['type'] == 'ERROR') 
                     {
                        $html_report .= '<td class ="text-danger">ERROR: ' . $assert_report['msg'] . '</td>';

                        $total_failed++;
                        $failed++;
                     } 
                     else if ($assert_report['type'] == 'OK') 
                     {
                        $html_report .= '<td class="text-success">OK: ' . $assert_report['msg'] . '</td>';

                        $total_successful++;
                        $successful++;
                     } 
                     else if ($assert_report['type'] == 'EXCEPTION') 
                     {
                        $html_report .= '<td class="text-primary">EXCEPTION: ' . $assert_report['msg'] . '</td>';
                     }

                     if (!empty($report['output'])) 
                     {
                        $html_report .= '<td class="text-secondary">OUTPUT: ' . $report['output'] . '</td>';
                     }
                     $html_report .= '</tr>';
                  }

                  $total_asserts++;
               }
            }
            $html_report .= '</tbody></table></div></div></div></div></div>';

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
            $c++;
         }
      }
     
      foreach ($namesSuitesMenu as $item) 
      {
         if ($h > 0 && $namesSuitesMenu[$h - 1] == $item) 
         {
            $menu_items .= '';
         }
         else
         {
            $menu_items .= '<li class="nav-item">
               <a id="' . $item . '" class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities_' . $item . '"
               aria-expanded="true" aria-controls="collapseUtilities">';

            $is_failed = self::is_faild($item, $total_cases_failed);

            if ($is_failed)
            {
               $menu_items .= '<i class="fas fa-times text-warning"></i> ';
            } 
            else 
            {
               $menu_items .= '<i class="fa fa-check text-success"></i> ';
            }

            $menu_items .= '<span>' . $item . '</span></a>
               <div id="collapseUtilities_' . $item . '" class="collapse" aria-labelledby="headingUtilities"
               data-parent="#accordionSidebar">
               <div id="collapse_' . $item . '" class="bg-white py-2 collapse-inner rounded">
               <h6 class="collapse-header">Test case:</h6>';

            $menu_items .=  self::names_tests($item, $namesSuitessubmenu);

            $menu_items .= '</div></div></li>';
         }
         $h++;
      }
     
      if (count($total_cases_failed) >= 1) 
      {
         $failed_cases = count($total_cases_failed);

         foreach ($total_cases_failed as $total_case_failed) {
            $names_failed = explode("\\", $total_case_failed['case']);

            $failed_Summ .= '<tr>
               <td>' . $names_failed[1] . '</td>
               <td>' . $names_failed[2] . '</td>
               <td class="text-right">' . $total_case_failed['case_successful'] . '</td>
               <td class="text-right">' . $total_case_failed['case_failed'] . '</td>
            </tr>';
         }
      } 
      else 
      {
         $failed_cases = 0;
      }

      if (count($total_cases_successful) >= 1) 
      {
         $successful_case = count($total_cases_successful);

         foreach ($total_cases_successful as $total_case_successful) 
         {
            $names_successful = explode("\\", $total_case_successful['case']);

            $succ_Summ .= '<tr>
               <td>' . $names_successful[1] . '</td>
               <td>' . $names_successful[2] . '</td>
               <td class="text-right">' . $total_case_successful["case_successful"] . '</td>
            </tr>';
         }
      } 
      else 
      {
         $successful_case = 0;
      }

      $content = new \CaboLabs\PhTest\PhTestHtmlTemplate;

      $render = $content->Html_template($total_suites, $total_cases, $failed_cases, $successful_case, $html_report, $test_time, $total_tests, $total_successful, $total_failed, $total_asserts, $failed_Summ, $succ_Summ, $menu_items);

      if ($path == './') 
      {
         $path = 'test_report.html';
      }

      file_put_contents($path, $render);
   }
  
   public function names_tests($item, $namesSuitessubmenu)
   {
      $menu_subitems = '';
      
      foreach ($namesSuitessubmenu as $submenu)
      {
         $suites = explode("\\", $submenu);
         if(in_array($item, $suites))
         {
            $menu_subitems .= '<a class="collapse-item" href="#">' . $suites[2] . '</a>'; 
         }
      }

      return $menu_subitems;
   }

   public function is_faild($item, $total_cases_failed)
   {
      foreach ($total_cases_failed as $suiteFaild)
      {
         $suites = explode("\\", $suiteFaild["case"]);
         if(array_search($item, $suites))
         {
            $faildSuite = true;
            break;
         }
         else
         {
            $faildSuite = false;
         }
      }
      
      return $faildSuite;
   }

   public function get_reports()
   {
      return $this->reports;
   }

   public function after_each_test($callback)
   {
      $this->after_each_test_function = $callback;
   }

   public function get_summary_report($test_time, $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful, $total_cases_failed, $total_cases_successful)
   {
      /**
       * render tables summary
       * @param String $row_separator, print "+--------------+--------------+--------------+" to table total Summary
       * @param String @$row_headers, header table total Summary
       * @param String $row_cells, data table total Summary (body)
       * @param int $gap_x, space on the x axis
       * @param String $joins, point joining both axes
       * @param String $axi_x, character for the x axis
       * @param String $axi_y, character for the y axis
       * @param String $column_width, default column width that will increase if any cell data is greater than this
       * @param Array $tableSummary, array with table data
       */
    
      $gap_x = 1;
      $joins = '+';
      $axi_x = '-';
      $axi_y = '|';
      $column_width = 10;

      //array with totals (suites,cases,tests)
      $tableSummary1 = [
         'Total suites'        => $total_suites,
         'Total test classes'  => $total_cases,
         'Total tests'         => $total_tests
      ];

      //array with Asserts totals
      $tableSummary2 = [
         'Asserts successful'  => $total_successful,
         'Asserts failed'      => $total_failed,
         'Total asserts'       => $total_asserts
      ];

      //generate table totals summary (suites,cases,tests)
      $summary1 = self::generate_table_summary_totales($column_width, $tableSummary1, $gap_x, $joins, $axi_x, $axi_y);

      //generate table totals asserts summary
      $summary2 = self::generate_table_summary_totales($column_width, $tableSummary2, $gap_x, $joins, $axi_x, $axi_y);

      echo PHP_EOL;

      echo 'Summary reports --> total time: '. $test_time .' μs'. PHP_EOL . PHP_EOL;
 
      //render table totals summary (suites,cases,tests)
      foreach ($summary1 as $table1) 
      {
         echo $table1 . PHP_EOL;
      }

      echo PHP_EOL;

       //render table totals asserts summary
      foreach ($summary2 as $table2)
      {
         echo $table2 . PHP_EOL;
      }

      echo PHP_EOL;
      echo PHP_EOL;

      //render table failed cases summary
      if (count($total_cases_failed) >= 1)
      {
         $failed_cases = count($total_cases_failed);

         echo 'Failed Summary: Cases failed ('. $failed_cases . ')'. PHP_EOL;

         $cases_failed_head = ['Suite' , 'Class', 'Failed', 'Successful'];
         
         //generate table failed cases summary
         $summary_cases_failed = self::generate_table_cases_summary(5, $cases_failed_head, $total_cases_failed, $gap_x, $joins, $axi_x, $axi_y);

         // render table cases failded summary
         foreach ($summary_cases_failed as $cases_failed) 
         {
            echo $cases_failed;
         }

         echo PHP_EOL;
      }
      else
      {
         $failed_cases = 0;
         
         echo 'Cases failed ('. $failed_cases . ')'. PHP_EOL;
      }

      echo PHP_EOL;

      //render table successful cases summary
      if (count($total_cases_successful) >= 1)
      {
         $successful_case = count($total_cases_successful);

         echo 'Successful Summary: Cases successful ('. $successful_case . ')'. PHP_EOL;

         $cases_success_head = ['Suite' , 'Class', 'Successful'];
         
         //generate table success cases summary
         $summary_cases_success = self::generate_table_cases_summary(5, $cases_success_head, $total_cases_successful, $gap_x, $joins, $axi_x, $axi_y);

         // render table cases success summary
         foreach ($summary_cases_success as $cases_success) 
         {
            echo $cases_success;
         }

         echo PHP_EOL;
      }
      else
      {
         $successful_case = 0;
         echo 'Cases Successful ('. $successful_case . ')'. PHP_EOL;
      }
   }

   public function generate_table_summary_totales($column_width, $tableSummary, $gap_x, $joins, $axi_x, $axi_y)
   {
      /**
       * generate summary table of totales
       * 
       */
      $row_separator = '';
      $row_headers = '';
      $row_cells = '';
   
      foreach ($tableSummary as $head => $row)
      {
         $length = strlen($head);

         if ($length > $column_width) 
         {
            $column_width = $length;
         }

         /* separator character is created line by line - adds spaces to the string and adds the number
          of characters of max width - space between the characters and finally the alignment that 
          the string will have*/
         $row_headers .= $axi_y . str_pad($head, ($gap_x * 2) + $column_width, ' ', STR_PAD_BOTH) . ' ';
         $row_cells .= $axi_y . str_pad($row, ($gap_x * 2) + $column_width, ' ', STR_PAD_LEFT) . ' ';

         //separator is created with the characters "+" and "-" the latter is repeated to form a line
         $row_separator .= $joins . str_repeat($axi_x, ($gap_x * 2) + $column_width) . $axi_x;
      }

      /* I end each line with the character "|" to close the table and the character "+" to join the
      intercepts of the x y axes */
      $row_headers .= $axi_y;

      $row_cells .= $axi_y;

      $row_separator .= $joins;
     
      return $dataTable = [
         '$row_separator1' => $row_separator,
         'row_headers'     => $row_headers,
         '$row_separator2' => $row_separator,
         '$row_cells'      => $row_cells,
         '$row_separator3' => $row_separator
      ];
   }

   public function generate_table_cases_summary($column_width, $arr_head, $arr_cases, $gap_x, $joins, $axi_x, $axi_y)
   {
      /**
       * generate summary table of successful summary and faild summary
       * 
       */

      $row_separator_cases = '';
      $row_headers = '';
      $row_cells = '';
      
      //make the width of the column ('Suite' , 'Class') adapted to the content
      for ($i=0; $i < count($arr_cases) ; $i++) 
      {
         $name_cas = explode("\\", $arr_cases[$i]['case']);
         $len1 = strlen($name_cas[2]);
         $len2 = strlen($name_cas[1]);

         if ($len1 > $column_width)
         {
            $column_width = $len1;
         }
         
         if ($len2 > $len1)
         {
            $column_width = $len2;
         }
      }

      //create the table header with its separators
      foreach ($arr_head as $head) 
      { 
         /* separator character is created line by line - adds spaces to the string and adds the number 
            of characters of max width - space between the characters and finally the alignment that 
            the string will have*/
         $row_headers .= $axi_y . ' ' . str_pad($head, ($gap_x * 2) + $column_width, ' ', STR_PAD_BOTH) . ' ';
         $row_separator_cases .= $joins . $axi_x. str_repeat($axi_x, ($gap_x * 2) + $column_width) . $axi_x;
      }

      $row_headers .= $axi_y;

      foreach ($arr_cases as $total_case)
      {
         $names_failed = explode("\\", $total_case['case']);

         /* separator character is created line by line - adds spaces to the string and adds the number
            of characters of max width - space between the characters and finally the alignment that
            the string will have*/
         $row_cells .= $axi_y . ' ' . str_pad($names_failed[1], ($gap_x * 2) + $column_width, ' ', STR_PAD_RIGHT) . ' ' .
            $axi_y . ' ' . str_pad($names_failed[2], ($gap_x * 2) + $column_width, ' ', STR_PAD_RIGHT) . ' ';

         // FIXME: to show a table should depend only on the data, the content to display shouldnt depend on the number of items, so the data array shouldn't have keys, just data.
         if (count($arr_head) == 4)
         {
            $row_cells .= $axi_y . ' ' . str_pad($total_case['case_failed'], ($gap_x * 2) + $column_width, ' ', STR_PAD_LEFT) . ' ' .
              $axi_y . ' ' . str_pad($total_case['case_successful'], ($gap_x * 2) + $column_width, ' ', STR_PAD_LEFT). ' ' . $axi_y . PHP_EOL;
         }
         else
         {
            $row_cells .= $axi_y . ' ' . str_pad($total_case['case_successful'], ($gap_x * 2) + $column_width, ' ', STR_PAD_LEFT). ' ' . $axi_y . PHP_EOL;
         }
      }

      $row_separator_cases .= $joins;
      
      return $summary_case = [
         'row_separator_cases1' => $row_separator_cases . PHP_EOL,
         'row_headers_failed'   => $row_headers . PHP_EOL,
         'row_separator_cases2' => $row_separator_cases . PHP_EOL,
         'row_cells_failed'     => $row_cells,
         'row_separator_cases3' => $row_separator_cases . PHP_EOL
      ];
   }
}

?>