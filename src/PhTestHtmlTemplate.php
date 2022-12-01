<?php

namespace CaboLabs\PhTest;

/**
 * 
 * this class renders the html when the tests are run.
 */

class PhTestHtmlTemplate {

  public function Html_template($total_suites, $total_cases, $failed_cases, $successful_case, $html_report, $test_time, $total_tests, $total_successful, $total_failed, $total_asserts, $failed_Summ, $succ_Summ, $name_test_cases)
  {
    global $content;

    $content = <<< EOD
      <!DOCTYPE html>
      <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">
      
            <title>Test summary</title>
      
            <!-- Custom fonts for this template-->
            <link href="assets/bootstrap-sb-admin-2/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">      
            <!-- Custom styles for this template-->
            <link href="assets/bootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
      
        </head>
      
        <body id="page-top">
      
            <!-- Page Wrapper -->
            <div id="wrapper">
      
                <!-- Sidebar -->
                <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      
                    <!-- Sidebar - Brand -->
                    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                        <img width="150" src="assets/images/amplify.png">
                    </a>
      
                    <!-- Divider -->
                    <hr class="sidebar-divider my-0">
      
                    <!-- Nav Item - Dashboard -->
                    <li class="nav-item active">
                        <a class="nav-link" href="test_report.html">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span></a>
                    </li>
      
                    <!-- Divider -->
                    <hr class="sidebar-divider">
      
                    <!-- Heading -->
                    <div class="sidebar-heading">
                      Test case
                    </div>
      
                    <!-- Nav Item -->
                    $name_test_cases
      
                    <!-- Divider -->
                    <hr class="sidebar-divider my-0"><br>
      
                    <!-- Heading -->
                    <div class="sidebar-heading">
                      Report summary
                    </div>
      
                    <li class="nav-item">
                      <a class="nav-link collapsed" href="#" 
                          aria-expanded="true" aria-controls="collapseTwo">
                          <i class="fas fa-clipboard-list"></i>
                          <span>Total summary</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link collapsed" href="#" 
                          aria-expanded="true" aria-controls="collapseTwo">
                          <i class="fas fa-clipboard-list"></i>
                          <span>Failed Summary</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link collapsed" href="#" 
                          aria-expanded="true" aria-controls="collapseTwo">
                          <i class="fas fa-clipboard-list"></i>
                          <span>Successful Summary</span>
                      </a>
                    </li>
                </ul>
                <!-- End of Sidebar -->
      
                <!-- Content Wrapper -->
                <div id="content-wrapper" class="d-flex flex-column">
                  <!-- Main Content -->
                  <div id="content">
      
                      <!-- Topbar -->
                      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2">
                          <h1 class="h2 text-gray-800">Test reports</h1>
                        </div>                                    
                      </nav>
                      <!-- End of Topbar -->
      
                      <!-- Begin Page Content -->
                      <div class="container-fluid">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2">
                          <h4 class="h4 text-gray-800">Summary</h4>
                        </div>
                          <!-- Content Row -->
                          <div class="row">
      
                              <!-- Total suites Card Example -->
                              <div class="col-xl-3 col-md-6 mb-4">
                                  <div class="card border-left-primary shadow h-100 py-2">
                                      <div class="card-body">
                                          <div class="row no-gutters align-items-center">
                                              <div class="col mr-2">
                                                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total suites</div>
                                                  <div class="h5 mb-0 font-weight-bold text-gray-800">$total_suites</div>
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
                                                    Total tests cases</div>
                                                  <div class="h5 mb-0 font-weight-bold text-gray-800">$total_cases</div>
                                              </div>
                                              <div class="col-auto">
                                                <i class="fas fa-project-diagram fa-2x text-gray-300"></i></i>
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
                                                          <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">$failed_cases</div>
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
                                                    Cases successful</div>
                                                  <div class="h5 mb-0 font-weight-bold text-gray-800">$successful_case</div>
                                              </div>
                                              <div class="col-auto">
                                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Content Row -->
                          <div class="row">
                              <div class="col-xl-12 col-lg-12">
                                  <div class="card shadow mb-4">
                                      <!-- Card Header - Dropdown -->
                                            $html_report
                                  </div>
                              </div>
                          </div>
      
                          <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card shadow mb-4">
                                  <!-- Card Header - Dropdown -->
                                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                      <h6 class="m-0 font-weight-bold text-success">Total time:  $test_time μs</h6>
                                  </div>
                                  <!-- Card Body -->
                                  <div class="card-body">
                                    <br><h6 class="m-0 font-weight-bold text-primary">Total Summary</h6><br>
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
                                          <td class="text-right">$total_suites</td>
                                          <td class="text-right">$total_cases</td>
                                          <td class="text-right">$total_tests</td> 
                                          <td class="text-right">$total_successful</td>
                                          <td class="text-right">$total_failed</td>
                                          <td class="text-right">$total_asserts</td>
                                        </tr>
                                      </tbody>
                                    </table>
      
                                    <h6 class="m-0 font-weight-bold text-primary">Failed Summary</h6><br>
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th scope="col">Suite</th>
                                          <th scope="col">Class</th>
                                          <th class="text-right" scope="col">Successful</th>
                                          <th class="text-right" scope="col">Failed</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        $failed_Summ
                                      </tbody>
                                    </table>
      
                                    <br><h6 class="m-0 font-weight-bold text-primary">Successful Summary</h6><br>
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th scope="col">Suite</th>
                                          <th scope="col">Class</th>
                                          <th class="text-right" scope="col">Successful</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        $succ_Summ
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                            </div>
                          </div>
                        </div>
      
                  </div>
                </div>
                <!-- End of Main Content -->
      
            </div>
            <!-- End of Content Wrapper -->
      
            <!-- Bootstrap core JavaScript-->
            <script src="assets/bootstrap-sb-admin-2/js/jquery/jquery.min.js"></script>
            <script src="assets/bootstrap-sb-admin-2/js/bootstrap.bundle.min.js"></script>
      
            <!-- Core plugin JavaScript-->
            <script src="assets/bootstrap-sb-admin-2/js/jquery-easing/jquery.easing.min.js"></script>
      
            <!-- Custom scripts for all pages-->
            <script src="assets/bootstrap-sb-admin-2/js/sb-admin-2.min.js"></script>
      
      
        </body>
      </html>
     EOD;

     return $content;
  }

}