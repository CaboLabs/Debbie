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

            echo '├── Test case: '. $test_case .' (test count: '. count($reports) .')'. PHP_EOL;
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
      global $html_report, $content, $total_suites, $total_cases, $total_tests, $total_asserts, $total_failed, $total_successful;

      $total_cases_failed = $total_cases_successful = [];
      
      $html_report = '<h1>Test report<h1>';

      $item3 = "";
      $item4 = "";
      
      foreach ($this->reports as $i => $test_suite_reports)
      {
         $html_report .= '<ul>';
         $total_suites ++;

         foreach ($test_suite_reports as $test_case => $reports)
         {
            $successful = 0;
            $failed = 0;
            
            $html_report .= '<ul>';
            $html_report .= '<li class="container"><p>Test case: '. $test_case .'</p>';

            $total_cases ++;

            foreach ($reports as $test_function => $report)
            {
               $html_report .= '<ul>';
               $html_report .= '<li class="container" style="margin-top: 10px;"><p>Test: '. $test_function .'</p>';

               $total_tests ++;

               if (isset($report['asserts']))
               {
                  foreach ($report['asserts'] as $assert_report)
                  {
                     if ($assert_report['type'] == 'ERROR')
                     {
                        $html_report .= '<li><p style="color:red">ERROR: '. $assert_report['msg'] .'</p></li>';

                        $total_failed ++;
                        $failed ++;
                     }
                     else if ($assert_report['type'] == 'OK')
                     {
                        $html_report .= '<li><p style="color:green">OK: '. $assert_report['msg'] .'</p></li>';

                        $total_successful ++;
                        $successful ++;
                     }
                     else if ($assert_report['type'] == 'EXCEPTION')
                     {
                        $html_report .= '<li><p style="color:blue">EXCEPTION: '. $assert_report['msg'] .'</p></li>';
                     }

                     if (!empty($report['output']))
                     {
                        $html_report .= '<li><p style="color:gray">OUTPUT: '. $report['output'] .'</p></li>';
                     }
                  }

                  $total_asserts ++;
               }

               $html_report .= '</li>';
               $html_report .= '</ul>';
            }

            $html_report .= '</li><br>';
            $html_report .= '</ul>';

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

         $html_report .= '</ul><br>';
      }

      if (count($total_cases_failed) >= 1)
      {
         $failed_cases = count($total_cases_failed);

         $item3 .= "<h1>Failed Summary:</h1> 
            <table>
               <tr>
               <th>Suite</th>
               <th>Class</th>
               <th>Successful</th>
               <th>Failed</th>
               </tr>";
         
         foreach ($total_cases_failed as $total_case_failed)
         {
            $names_failed = explode("\\", $total_case_failed['case']);

            $item3 .= "<tr>
               <td>". $names_failed[1] ."</td>
               <td>". $names_failed[2] ."</td>
               <td>". $total_case_failed['case_successful'] ."</td>
               <td>". $total_case_failed['case_failed'] ."</td>
            </tr>";
         }

         $item3 .="</table>";
      }
      else
      {
         $failed_cases = 0;
      }

      if (count($total_cases_successful) >= 1)
      {
         $successful_case = count($total_cases_successful);

         $item4 .= "<h1>Successful Summary:</h1>
            <table>
               <tr>
                  <th>Suite</th>
                  <th>Class</th>
                  <th>Successful</th>
               </tr>";

         foreach ($total_cases_successful as $total_case_successful)
         {
            $names_successful = explode("\\", $total_case_successful['case']);

            $item4 .= "<tr>
               <td>". $names_successful[1] ."</td>
               <td>". $names_successful[2] ."</td>
               <td>". $total_case_successful["case_successful"] ."</td>
            </tr>";
         }

         $item4 .= "</table>";
      }
      else
      {
         $successful_case = 0;
      }

      //css provisional
      $content = <<< EOD
         <!DOCTYPE html>
         <html lang="en">
         <head>
         <meta charset="UTF-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>Document</title>
         </head>
         <style>
         body{
            font-size: 10px;
            line-height: 35px;
         }
         ul,
         li {
         list-style: none;
         margin: 0;
         padding: 0;
         }
         ul {
         padding-left: 1em;
         }
         li {
         padding-left: 1em;
         border: 1px dotted black;
         border-width: 0 0 1px 1px;
         }
         li.container {
         border-bottom: 0px;
         }
         li.empty {
         font-style: italic;
         color: silver;
         border-color: silver;
         }
         li p {
         margin: 0;
         background: white;
         position: relative;
         top: 0.5em;
         }
         li ul {
         border-top: 1px dotted black;
         margin-left: -1em;
         padding-left: 2em;
         }
         ul li:last-child ul {
            border-left: 1px solid white;
            margin-left: -17px;
         }

         .grid-container {
            display: grid;
            gap: 10px;
            padding: 10px;
         }
         .grid-item {
            padding: 20px;
            border: 1px dotted black;
            text-align: center;
         }
          
         .item1 {
            grid-column: 1;
            grid-row: 1;
         }
         .item2 {
            grid-column: 2;
            grid-row: 1;
         }
         .item3 {
            grid-column: 3;
            grid-row: 1;
         }
         .item4 {
            grid-column: 4;
            grid-row: 1;
         }
         table, th, td{
            border: 1px solid gray;
            border-collapse: collapse;
            font-size: 18px;
            padding: 5px;
         }
         td {
            text-align: right;
         }
         </style><body>

         <div class="grid-container">
         <div class="grid-item item1">
            <h1>Total suites: $total_suites </h1>
         </div>
         <div class="grid-item item2">
            <h1>Total tests cases: $total_cases </h1>
         </div>
         <div class="grid-item item3">
            <h1>Cases failed: $failed_cases</h1>
         </div>
         <div class="grid-item item4">
            <h1>Cases successful: $successful_case</h1>
         </div> 
         </div>

         $html_report

         <h1>Total Summary:</h1>
         <h2>total time: $test_time μs</h2>
         <table>
            <tr>
               <th>Total suites</th>
               <th>Total test classes</th>
               <th>Total tests</th>
               <th>Asserts successful</th>
               <th>Asserts failed</th>
               <th>Total asserts</th>
            </tr>
            <tr>
               <td>$total_suites</td>
               <td>$total_cases</td>
               <td>$total_tests</td> 
               <td>$total_successful</td>
               <td>$total_failed</td>
               <td>$total_asserts</td>
            </tr>
         </table>
         <br> 
         $item3
         <br>
         $item4
         <br>
         </body></html>
      EOD;
      // end css provisional

      if ($path == './')
      {
         $path = 'test_report.html';
      }

      file_put_contents($path, $content);
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
         'row_headers_failed'  => $row_headers . PHP_EOL,
         'row_separator_cases2' => $row_separator_cases . PHP_EOL,
         'row_cells_failed'    => $row_cells,
         'row_separator_cases3' => $row_separator_cases . PHP_EOL
      ];
   }
}

?>