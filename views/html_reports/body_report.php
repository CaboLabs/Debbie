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

           /* $html_report .= '<!-- Content Row -->
            <div id="card_tests' . $names[1] . $c . '" class="card_' . $names[1] . ' suites_test" style="display:none;">
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
               $total_tests++;

               if (isset($report['asserts']))
               {
                  foreach ($report['asserts'] as $assert_report)
                  {
                     $html_report .= '<td>' . $test_function . '</td>';
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
               else
               {
                  $html_report .= '<td>' . $test_function . '</td>';
                  $html_report .= '<td></td>';
                  $html_report .= '<td></td>';
                  $html_report .= '</tr>';
               }
            }
            $html_report .= '</tbody></table></div></div></div></div></div>';*/

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