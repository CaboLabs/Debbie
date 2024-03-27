<div id="card_tests<?=$names[1] . $i?>" class="card_<?=$names[1]?> suites_test" style="display:none;">
  <div class="row row_testcases" id="card_<?=$names[2]?>">
    <div class="col-xl-12 col-lg-12">
      <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary"><?=$names[2]?></h6>
        </div>
        <!-- Card Body -->
        <div class="card-body table-responsive">
          <table class="table table-borderless" style="margin: -0.5rem;">
            <thead>
              <tr class="border-bottom">
                <th scope="col">Class</th>
                <th scope="col">Asserts</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reports as $test_function => $report): ?>
                <?php if (isset($report['asserts'])): ?>
                  <?php foreach ($report['asserts'] as $assert_report): ?>
                    <tr>
                      <td>
                        <?=$test_function?>
                      </td>
                      <?php if ($assert_report['type'] == 'ERROR'): ?>
                        <td class="text-danger">
                          ERROR: <pre><?=$assert_report['msg']?></pre>
                        </td>
                      <?php elseif ($assert_report['type'] == 'OK'): ?>
                        <td class="text-success">
                          OK: <pre><?=$assert_report['msg']?></pre>
                        </td>
                      <?php elseif ($assert_report['type'] == 'EXCEPTION'): ?>
                        <td class="text-primary">
                          EXCEPTION: <pre><?=$assert_report['msg'] ?></pre>
                        </td>
                      <?php endif; ?>
                      <td class="text-secondary">
                        <?php if  (!empty($report['output'])): ?>
                          OUTPUT: <pre><?=$report['output']?></pre>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>