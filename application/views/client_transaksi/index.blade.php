@extends('layouts.layout')
@section('css-export')
<!-- DataTables -->
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>
					Daftar Transaksi Saya
				</h1>
			</div>
			<div class="col-sm-6" style="text-align: right">

				<button id="btn-cek-transaksi" class="btn 
				btn-outline-primary

				">Cek Transaksi Terakhir</button>

			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="table-transaksi" class="table table-hover table-outline table-vcenter text-nowrap card-table">
								<thead>
									<tr>
										<th>Id</th>
										<th>Perusahaan</th>
										<th>Periode</th>
										<th>Biaya</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade " id="modalCekTransaksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg"  role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-title-client">Transaksi Terakhir</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="modal-content">
					<form role="form">
						<div class="card-body">
							<div id="history-content">
							</div>
						</div>
						<!-- /.card-body -->
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade " id="modalDetailTransaksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg"  role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-title-client">Detail Transaksi</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="modal-content">
					<div id="content-detail">

					</div>
					<!-- /.card-body -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('js-export')
<!-- DataTables -->
<script src="{{ base_url('assets/template/utama/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ base_url('assets/template/utama/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ base_url('assets/template/utama/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ base_url('assets/template/utama/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endsection
@section('js-inline')
<script>
	let $tableTransaksi = null;
	let $modalCekTransaksi = null;
	let $modalDetailTransaksi = null;
	$(function() {
		$modalCekTransaksi = $("#modalCekTransaksi");
		$modalDetailTransaksi = $("#modalDetailTransaksi");
		$btnCekTransaksi = $("#btn-cek-transaksi");
		$tableTransaksi = $('#table-transaksi').DataTable({ 
			"bAutoWidth": false ,
			"processing": true, 
			"serverSide": true, 
			"order": [], 
			"ajax": {
				"url": '<?php echo base_url('client-transaksi/getData'); ?>',
				"type": "POST",
			},
			"columns": [
			{"data": "id"},
			{"data": "nama_perusahaan"},
			{"data": "periode"},
			{"data": "biaya"},
			{"data": "status"},
			{"data": "action"},
			],
			"order" : [],
			'columnDefs': [
			{
				"targets": 0,
				"orderable" : false,
			},
			{
				"targets": 5,
				"className": "text-center",
				"searchable" : false,
				"orderable" : false,
				"className" : 'action-no-wrap',
			}],
		});
		$btnCekTransaksi.click(function() {
			$("body").loading('start');
			let client_id = $("input[name='client_id']").val();
			let formData = {
				client_id: client_id,
			};
			data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
			axios.post("{{ base_url("client-transaksi/jGetFormatLastTransaction") }}", data)
			.then((res) => {
				$("body").loading('stop');
				data = res.data;
				$("#history-content").html(data.html);
				toggleModal($modalCekTransaksi, true);
			})
			.catch((error) => {
			});
		});
	});

	function showDetailTransaksi(transaksi_id) {
		$("body").loading('start');
		let formData = {
			transaksi_id: transaksi_id,
		};
		data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
		axios.post("{{ base_url("client-transaksi/getDetailTransaksi") }}", data)
		.then((res) => {
			$("body").loading('stop');
			data = res.data;
			$("#content-detail").html(data.html);
			toggleModal($modalDetailTransaksi, true);
		})
		.catch((error) => {
		});

	}
</script>
@endsection