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
					Daftar Transaksi
				</h1>
			</div>
			<div class="col-sm-6" style="text-align: right">
				<button  data-toggle="modal" data-target="#modalLookupClient" class="btn btn-primary">Cek Transaksi</button>
				<button  onclick="location.href='{{ base_url("transaksi/tambah") }}'" class="btn btn-primary">Tambah Transaksi</button>
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
<div class="modal fade" id="modalLookupClient">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Pilih Client</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table id="lookup" class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th>Nama Perusahaan</th>
							<th>Periode</th>
							<th>Meteran Akhir</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($clients as $client)
						<tr class="pilih" data-client_id="{{ $client->id }}" >
							<td>{{ $client->nama_perusahaan }}</td>
							<td>{{ $client->periode_readablee }}</td>
							<td>{{ $client->meteran_akhir }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="modal-footer justify-content-between">


			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalCekTransaksi">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Riwayat Transaksi</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer justify-content-normal">
				<button type="button" class="btn btn-secondary" id="btn-kembali">Kembali</button>

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailTransaksi">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Detail Transaksi</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer justify-content-between">

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
	let $modalTransaksi = null;
	let $modalCekTransaksi = null;
	let $btnTambahClient = null;
	let $btnSubmitClient = null;
	let $iNama = null;
	let $iUsername = null;
	let $iPassword = null;
	let $iProvinsi = null;
	let $iKota = null;
	let $idPengguna = null;
	let $modalLookupClient = null;
	let $modalDetailTransaksi = null;
	let $btnKembali = null;

	$(function() {

		$("#lookup").DataTable();

		$modalTransaksi = $("#modalTransaksi");
		$modalCekTransaksi = $("#modalCekTransaksi");
		$modalDetailTransaksi = $("#modalDetailTransaksi");

		$btnTambahClient = $("#btn-tambah-client");
		$btnCekTransaksi = $("#btn-cek-transaksi");
		$btnSubmitClient = $("#btn-submit-kota");
		$ikKota = $("#input-kota");
		$iNama = $("#input-nama");
		$iUsername = $("#input-username");
		$iPassword = $("#input-password");
		$iProvinsi = $("#input-provinsi");
		$iKota = $("#input-kota");
		$idPengguna = $("#id-pengguna");
		$modalLookupClient = $('#modalLookupClient');
		$btnKembali = $("#btn-kembali");

		$(document).on('click', '.pilih', function (e) {
			let client_id = $(this).data('client_id');
			$modalLookupClient.modal('hide');
			$('body').loading('start');

			if (client_id == "") {
				return;
			}
			let formData = {
				client_id: client_id,
			};
			data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');

			$("body").loading('start');
			axios.post("{{ base_url("transaksi/jGetFormatLastTransaction") }}", data)
			.then((res) => {

				data = res.data;
				$modalCekTransaksi.find(".modal-body").html(data.html);
				$("body").loading('stop');
				$modalCekTransaksi.modal("show");
			})
			.catch((error) => {

			});

		});

		$btnKembali.click(function(event) {
			$modalCekTransaksi.modal('hide');
			$modalLookupClient.modal('show');
		});
		$tableTransaksi = $('#table-transaksi').DataTable({ 
			"bAutoWidth": false ,
			"processing": true, 
			"serverSide": true, 
			"order": [], 
			"ajax": {
				"url": '<?php echo base_url('transaksi/getData'); ?>',
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
			$("#history-content").html('');
			$("#cek_client_id").prop('value', 0);
			$modalCekTransaksi.modal("show");
		})
		$btnTambahClient.click(function() {
			clearData();
			clearError();
			$("#modal-title-pengguna").text("Tambah Pengguna");
			$modalTransaksi.modal("show");
		})

		$btnSubmitClient.click(function(e) {
			$(this).attr('disabled', true);

			formData = {
				nama: $iNama.val(),
				username: $iUsername.val(),
				password: $iPassword.val(),
				provinsi: $iProvinsi.val(),
				kota: $iKota.val(),
				id: $idPengguna.val(),
			};
			data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')
			var url = "";
			if (formData.id) {
				url = '{{ base_url('transaksi/update') }}';
			} else {
				url = '{{ base_url('transaksi/store') }}'
			}
			axios.post(url, data)
			.then(res => {
				data = res.data;
				clearError();
				console.log(data);
				if (data.success == 0) {
					$btnSubmitClient.attr('disabled', false);

					$.each(data.messages, function(key, value) {

						$('#'+key).addClass('is-invalid');
						$('#'+key).parent('.form-group').find('.error').html(value);
					});
				} else if (data.success == 1){
					toggleModal($modalTransaksi, false).done(function() {
						$tableTransaksi.ajax.reload();
						$btnSubmitClient.attr('disabled', false);
					});



				}



			})
			.catch(err => {
				$(this).attr('disabled', true);

			});
		});


		

	});



	function showModal(id,opsi) {
		if (opsi == 1) {
			showEdit(id);
		} else if(opsi == 2) {
			showDelete(id);
		}
	}

	function showEdit(id) {
		let formData = {
			id: id,
		};
		clearError();
		clearData();
		data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')
		axios.post("{{ base_url("pengguna/detail") }}", data)
		.then((res) => {

			data = res.data;
			console.log(data);
			$iNama.val(data.nama);
			$iUsername.val(data.username);
			$iPassword.val("");

			
			$idPengguna.val(data.id);
			$("#modal-title-pengguna").text("Ubah Data Pengguna");

			$modalTransaksi.modal("show");
		})
	}

	function showDelete(id) {
		Swal.fire({
			title: 'Delete Data',
			text: "Yakin akan menghapus data ini ?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ya, Hapus!'
		}).then((result) => {
			if (result.value) {
				let formData = {
					id: id,
				};
				data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')
				axios.post("{{ base_url("transaksi/delete") }}", data)
				.then((res) => {
					Swal.fire({
						title: 'Deleted!',
						text: 'Your file has been deleted.',
						icon: 'success',
						timer: 500,
						showConfirmButton: false,

					})
					.then(() => {
						$tableTransaksi.ajax.reload();
					})
				})
				.catch((error) => {
					Swal.fire({
						title: 'Gagal!',
						text: 'Tidak dapat menghapus data ini !!!.',
						icon: 'error',
						timer: 1500	,
						showConfirmButton: false,

					})
				});


			}
		})
		;
	}




	function clearData() {
		$idPengguna.val("");
		$iNama.val("");
		$iUsername.val("");
		$iPassword.val("");
		$iProvinsi.prop('selectedIndex',0);
		$iKota.prop('selectedIndex',0);
	}
	function clearError() {
		$(".error").html("");
		$(".is-invalid").removeClass('is-invalid');
	}

	function showDetaiTransaksi(transaksi_id) {
		$("body").loading('start');
		let formData = {
			transaksi_id: transaksi_id,
		};
		data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
		axios.post("{{ base_url("transaksi/getDetailTransaksi") }}", data)
		.then((res) => {
			$("body").loading('stop');
			data = res.data;
			$modalDetailTransaksi.find(".modal-body").html(data.html);
			toggleModal($modalDetailTransaksi, true);
		})
		.catch((error) => {
		});

	}
	


</script>
@endsection