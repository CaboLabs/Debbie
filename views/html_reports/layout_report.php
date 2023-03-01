<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Test summary</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.css" integrity="sha512-gOfBez3ehpchNxj4TfBZfX1MDLKLRif67tFJNLQSpF13lXM1t9ffMNCbZfZNBfcN2/SaWvOf+7CvIHtQ0Nci2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" integrity="sha512-Mk4n0eeNdGiUHlWvZRybiowkcu+Fo2t4XwsJyyDghASMeFGH6yUXcdDI3CKq12an5J8fq4EFzRVRdbjerO3RmQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <?=$this->section('content')?>
    <!-- End of Main Content -->
  </div>
  <!-- End of Content Wrapper -->
  <!-- Bootstrap core JavaScript-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.2.1/js/bootstrap.bundle.js" integrity="sha512-4WQnCRyZ0CILKrMrO1P40yJrObxaNBOuImuSCFRV47/CWYh3ISyVPmqZnhiZ4OmhHstEj+QaoMDpQo5SnOXDAw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- Core plugin JavaScript-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" integrity="sha512-0QbL0ph8Tc8g5bLhfVzSqxe9GERORsKhIn1IrpxDAgUsbBGz/V7iSav2zzW325XGd1OMLdL4UiqRJj702IeqnQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.js" integrity="sha512-M82XdXhPLLSki+Ld1MsafNzOgHQB3txZr8+SQlGXSwn6veeqtYhPLbQeAfk9Y/Q9/gXcfa1jWT4YYUeoei6Uow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js" integrity="sha512-+QnjQxxaOpoJ+AAeNgvVatHiUWEDbvHja9l46BHhmzvP0blLTXC4LsvwDVeNhGgqqGQYBQLFhdKFyjzPX6HGmw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js" integrity="sha512-+QnjQxxaOpoJ+AAeNgvVatHiUWEDbvHja9l46BHhmzvP0blLTXC4LsvwDVeNhGgqqGQYBQLFhdKFyjzPX6HGmw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Custom scripts for all pages

  NOTE: this path is relative to this file using __DIR__ so it should work when running from other cli's
        if the path is relative, it will be relative to the running script, if cli.php is on a different
        folder than this package folder, then it won't find the js file. -->

  <script src="<?= __DIR__ ?>/../assets/js/views/test_report_index.js"></script>
</body>

</html>