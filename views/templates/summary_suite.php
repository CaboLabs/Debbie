<div id="card_summary_<?=$suite?>" class="card_summary_suites" style="display:none">
  <!-- Total suites Card Example -->
  <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Total classes
              </div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$totalTestSuites?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total tests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                Total tests
              </div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$class?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clipboard-list fa-2x text-gray-300"></i></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- test failed Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-danger shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                Asserts failed
              </div>
              <div class="row no-gutters align-items-center">
                <div class="col-auto">
                  <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?=$fail?></div>
                </div>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-times-circle fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- test successful Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                Asserts successful</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$success?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>