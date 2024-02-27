<?php $this->layout('layout_report') ?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="test_report.html">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">PhTest</div>
  </a>
  <!-- Divider -->
  <hr class="sidebar-divider my-0">
  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a id="dashboard" class="nav-link" href="#">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>
  <!-- Divider -->
  <hr class="sidebar-divider">
  <!-- Heading -->
  <div class="sidebar-heading">
    Test suites
  </div>
  <!-- Nav Item -->
  <?=$menu_items?>
</ul>
<!-- End of Sidebar -->
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
  <!-- Main Content -->
  <div id="content">
    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2">
        <h1 class="h2 text-gray-800"></h1>
      </div>
    </nav>
    <!-- End of Topbar -->
    <!-- Begin Page Content -->
    <div class="container-fluid">
      <h2 id="title_suite"></h2>
      <!-- Content Row -->
      <div id="headCardSummary" class="row">
        <!-- Total suites Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                    Total suites
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?=$total_suites?>
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Total tests cases Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                    Total tests cases
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?=$total_cases?>
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Cases failed Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                    Cases failed
                  </div>
                  <div class="row no-gutters align-items-center">
                    <div class="col-auto">
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                        <?=$failed_cases?>
                      </div>
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
        <!-- Cases successful Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                    Cases successful
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?=$successful_case?>
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="Card_suites">
        <?=$html_report?>
      </div>
      <div id="cardSummaryTables" class="row">
        <div class="col-xl-12 col-lg-12">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-success">Total time:
                <?=$test_time?> μs
              </h6>
            </div>
            <!-- Card Body -->
            <div class="card-body table-responsive">
              <br>
              <h6 class="m-0 font-weight-bold text-primary">Total Summary</h6><br>
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Total suites</th>
                    <th scope="col">Total test classes</th>
                    <th scope="col">Total tests</th>
                    <th scope="col">Asserts successful</th>
                    <th scope="col">Asserts failed</th>
                    <th scope="col">Total asserts</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-right">
                      <?=$total_suites?>
                    </td>
                    <td class="text-right">
                      <?=$total_cases?>
                    </td>
                    <td class="text-right">
                      <?=$total_tests?>
                    </td>
                    <td class="text-right">
                      <?=$total_successful?>
                    </td>
                    <td class="text-right">
                      <?=$total_failed?>
                    </td>
                    <td class="text-right">
                      <?=$total_asserts?>
                    </td>
                  </tr>
                </tbody>
              </table>
              <h6 class="m-0 font-weight-bold text-primary">Failed Summary</h6><br>
              <?=$failed_Summ?>
              <br>
              <h6 class="m-0 font-weight-bold text-primary">Successful Summary</h6><br>
              <?=$succ_Summ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>