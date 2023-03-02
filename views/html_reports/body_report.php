<?php
$c = 0;
foreach ($this->reports as $i => $test_suite_reports):

  foreach ($test_suite_reports as $test_case => $reports):
  
    $names = explode("\\", $test_case)?>

<!-- Content Row -->
<div id="card_tests' . $names[1] . $c . '" class="card_' . $names[1] . ' suites_test" style="display:none;">
  <div class="row" id="card_' . $names[2] . '">
    <div class="col-xl-12 col-lg-12">
      <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">' . $names[2] . '</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
          <table class="table table-borderless" style="margin: -0.5rem;">
            <thead>
              <tr class="border-bottom">
                <th scope="col">Class</th>
                <th scope="col">Asserts</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <?php foreach ($reports as $test_function => $report): ?>

                  <?php if (isset($report['asserts'])): ?>

                  <?php foreach  ($report['asserts'] as $assert_report): ?>

                  <td>' . $test_function . '</td>

                  <?php if ($assert_report['type'] == 'ERROR'): ?>

                  <td class="text-danger">ERROR: ' . $assert_report['msg'] . '</td>

                  <?php elseif ($assert_report['type'] == 'OK'): ?>

                  <td class="text-success">OK: ' . $assert_report['msg'] . '</td>

                  <?php elseif ($assert_report['type'] == 'EXCEPTION'): ?>

                  <td class="text-primary">EXCEPTION: ' . $assert_report['msg'] . '</td>

                  <?php endif; ?>

                  <?php if  (!empty($report['output'])): ?>

                  <td class="text-secondary">OUTPUT: ' . $report['output'] . '</td>

                  <?php endif; ?>
                <?php endforeach; ?>
              </tr>
              <?php else : ?>
                <td>' . $test_function . '</td>
                <td></td>
                <td></td>
                </tr>
              <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $c++; endforeach; ?>
<?php endforeach; ?>