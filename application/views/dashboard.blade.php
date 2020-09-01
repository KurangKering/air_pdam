@extends('layouts.layout')
@section('css-export')
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bbootstrap 4 -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<!-- iCheck -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<!-- JQVMap -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/jqvmap/jqvmap.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/dist/css/adminlte.min.css') }}">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/daterangepicker/daterangepicker.css') }}">
<!-- summernote -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/summernote/summernote-bs4.css') }}">
@endsection
@section('css-inline')

@endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Dashboard</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="callout callout-info">
					<h5><i class="fas fa-info"></i> Note:</h5>
					Selamat Datang.
				</div>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
@section('js-export')

<!-- jQuery UI 1.11.4 -->
<script src="{{ base_url('assets/template/utama/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
	$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ base_url('assets/template/utama/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ base_url('assets/template/utama/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ base_url('assets/template/utama/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ base_url('assets/template/utama/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ base_url('assets/template/utama/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ base_url('assets/template/utama/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ base_url('assets/template/utama/plugins/moment/moment.min.js') }}"></script>
<script src="{{ base_url('assets/template/utama/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ base_url('assets/template/utama/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ base_url('assets/template/utama/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ base_url('assets/template/utama/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
@endsection
@section('js-inline')
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ base_url('assets/template/utama/dist/js/pages/dashboard.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ base_url('assets/template/utama/dist/js/demo.js') }}"></script>
@endsection
