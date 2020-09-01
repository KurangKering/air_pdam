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
					Daftar Pengguna
				</h1>
			</div>
			<div class="col-sm-6" style="text-align: right">
				<button type="button" class="btn btn-primary ml-2" id="btn-tambah-pengguna">Tambah</button>
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
							<table id="table-pengguna" class="table table-hover table-outline table-vcenter text-nowrap card-table">
								<thead>
									<tr>
										<th>Id</th>
										<th>Nama</th>
										<th>Username</th>
										<th>Role</th>
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
<div class="modal fade" id="modalPengguna" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog"  role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-title-pengguna">Tambah Pengguna</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="modal-content">
					<form onsubmit="return false">
						<input type="hidden" id="id-pengguna" value="">
						<div class="form-group" id="group-role">
							<label for="message-text" class="col-form-label">Role:</label>
							<select name="input-role_id" id="input-role_id" class="form-control custom-select">
								<option value="0">Pilih Role</option>
								@foreach ($data['roles'] as $k => $role)
								@if ($role->id != 3) 
								<option value="{{ $role->id }}">{{ $role->nama }}</option>
								@endif
								@endforeach 
							</select>
							<div class="error"></div>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Nama:</label>
							<input type="text"  id="input-nama" class="form-control"></input>
							<div class="error"></div>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Email:</label>
							<input type="text"  id="input-email" class="form-control"></input>
							<div class="error"></div>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Username:</label>
							<input type="text"  id="input-username" class="form-control"></input>
							<div class="error"></div>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Password:</label>
							<input type="password"  id="input-password" class="form-control"></input>
							<div class="error"></div>
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" id="btn-submit-pengguna" class="btn btn-primary">Save changes</button>
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
	let $tablePengguna = null;
	let $modalPengguna = null;
	let $btnTambahPengguna = null;
	let $btnSubmitPengguna = null;
	let inputNama = null;
	let inputEmail = null;
	let inputUsername = null;
	let inputPassword = null;
	let $idPengguna = null;
	$(function() {
		$modalPengguna = $("#modalPengguna");
		$btnTambahPengguna = $("#btn-tambah-pengguna");
		$btnSubmitPengguna = $("#btn-submit-pengguna");
		$inputRoleId = $("#input-role_id");
		$inputNama = $("#input-nama");
		$inputEmail = $("#input-email");
		$inputProvinsi = $("#input-email");
		$inputUsername = $("#input-username");
		$inputPassword = $("#input-password");
		$idPengguna = $("#id-pengguna");
		$tablePengguna = $('#table-pengguna').DataTable({ 
			"bAutoWidth": false ,
			"processing": true, 
			"serverSide": true, 
			"order": [], 
			"ajax": {
				"url": '<?php echo base_url('pengguna/getData'); ?>',
				"type": "POST",
			},
			"columns": [
			{"data": "id"},
			{"data": "nama"},
			{"data": "username"},
			{"data": "role_nama"},
			{"data": "action"},
			],
			'columnDefs': [
			{
				"targets": 0,
				"orderable" : false,
			},
			{
				"targets": 4,
				"className": "text-center",
				"searchable" : false,
				"orderable" : false,
				"className" : 'action-no-wrap',
			}],
		});
		$btnTambahPengguna.click(function() {
			clearData();
			clearError();
			$("#modal-title-pengguna").text("Tambah Pengguna");
			$modalPengguna.modal("show");
		})
		$btnSubmitPengguna.click(function(e) {
			// $(this).attr('disabled', true);
			formData = {
				nama: $inputNama.val(),
				email: $inputEmail.val(),
				username: $inputUsername.val(),
				password: $inputPassword.val(),
				role_id: $inputRoleId.val(),
				id: $idPengguna.val(),
			};
			data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')
			var url = "";
			if (formData.id) {
				url = '{{ base_url('pengguna/update') }}';
			} else {
				url = '{{ base_url('pengguna/insert') }}'
			}
			axios.post(url, data)
			.then(res => {
				data = res.data;
				clearError();
				if (data.success == 0) {
					$btnSubmitPengguna.attr('disabled', false);
					$.each(data.messages, function(key, value) {
						$('#'+key).addClass('is-invalid');
						$('#'+key).parent('.form-group').find('.error').html(value);
					});
				} else if (data.success == 1){
					toggleModal($modalPengguna, false).done(function() {
						$tablePengguna.ajax.reload();
						$btnSubmitPengguna.attr('disabled', false);
					});
				}
			})
			.catch(err => {
				$(this).attr('disabled', false);
			})
			.then(() => {
				$btnSubmitPengguna.attr('disabled', false);
				$(this).attr('disabled', false);
				

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
			$inputRoleId.val(data.role_id);
			$inputNama.val(data.nama);
			$inputEmail.val(data.email);
			$inputUsername.val(data.username);
			$inputPassword.val("");
			$idPengguna.val(data.id);
			$("#modal-title-pengguna").text("Ubah Data Pengguna");
			if (data.role_id == 3) {

				$("#group-role").hide();
			} else {
				$("#group-role").show();

			}
			$modalPengguna.modal("show");
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
				axios.post("{{ base_url("pengguna/delete") }}", data)
				.then((res) => {
					Swal.fire({
						title: 'Deleted!',
						text: 'Your file has been deleted.',
						icon: 'success',
						timer: 500,
						showConfirmButton: false,
					})
					.then(() => {
						$tablePengguna.ajax.reload();
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
		$inputNama.val("");
		$inputEmail.val("");
		$inputUsername.val("");
		$inputPassword.val("");
		$inputRoleId.prop('selectedIndex',0);
	}
	function clearError() {
		$(".error").html("");
		$(".is-invalid").removeClass('is-invalid');
	}
</script>
@endsection