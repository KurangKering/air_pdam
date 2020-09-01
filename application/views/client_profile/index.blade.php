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
					Profile Ku
				</h1>
			</div>
			<div class="col-sm-6" style="text-align: right">
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
					<div class="card-header p-2">
						<ul class="nav nav-pills">
							<li class="nav-item"><a class="nav-link " href="#data-perusahaan" data-toggle="tab">Data Perusahaan</a></li>
							<li class="nav-item"><a class="nav-link active" href="#data-pengguna" data-toggle="tab">Data Pengguna</a></li>
						</ul>
					</div>
					<div class="card-body">
						<div class="tab-content">
							<div class=" tab-pane" id="data-perusahaan">
								<form class="form-horizontal" enctype="multipart/form-data" id="form-data-perusahaan" method="POST" >
									<div class="card card-outline card-secondary">
										<div class="card-body">
											<div class="form-group row">
												<label class="col-sm-4 col-form-label">Nama Perusahaan</label>
												<div class="col-sm-8">
													<input type="text" class="form-control" required id="input-nama_perusahaan" name="input-nama_perusahaan" placeholder=""
													value="{{ $client['nama_perusahaan'] }}">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-sm-4 col-form-label">Periode Mulai</label>
												<div class="col-sm-4">
													<select name="input-periode_bulan" id="input-periode_bulan" name="input-periode_bulan" class="form-control" required>
														@foreach (hBUlan() as $k => $bulan)
														<option 

														@if($client['periode_bulan'] == $k)
														selected
														@endif
														value="{{ $k }}">{{ $bulan }}</option>
														@endforeach
													</select>
												</div>
												<div class="col-sm-4">
													<input type="number" class="form-control" required id="input-periode_tahun" name="input-periode_tahun" placeholder="" value="{{ $client['periode_tahun'] }}">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-4 col-form-label">Meteran Mulai</label>
												<div class="col-sm-8">
													<input type="number" class="form-control" required id="input-meteran_akhir" name="input-meteran_akhir" placeholder="" value="{{ $client['meteran_akhir'] }}">
												</div>
											</div>
											
											

											
											

											<div class="form-group row">
												<label for="input-kap_mesin_produksi" class="col-sm-4 col-form-label">Kapasitas Mesin Produksi (TON)</label>
												<div class="col-sm-4">
													<input type="number" class="form-control" required id="input-kap_mesin_produksi" name="input-kap_mesin_produksi" placeholder="" value="{{ $client['kap_mesin_produksi'] }}">
												</div>
												<div class="col-sm-4">
													
													<select  id="input-satuan_kap_mesin_produksi" name="input-satuan_kap_mesin_produksi" class="form-control" required>
														@foreach (hSatuanKapMesinProduksi() as $k => $v)
														<option 
														@if ($k == $client['satuan_kap_mesin_produksi'])
														selected
														@endif
														value="{{ $k }}">{{ $v }}</option>

														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="card card-outline card-secondary">
										<div class="card-header">
											<label for="input-kap_mesin_produksi" class="col-sm-12 col-form-label text-center">Kapasitas Produksi</label>
										</div>
										<div class="card-body">
											<div class="form-group row">
												
												<div class="col-sm-3">
													<label for="input-kap_mesin_produksi" class="col-12 col-form-label text-center">Produksi (TBS/Ton/Hari)</label>
													<div class="col-12">
														<input type="number" class="form-control" required id="input-kap_prod_produksi" name="input-kap_prod_produksi" placeholder="" value="{{ $client['kap_prod_produksi'] }}">	
													</div>
												</div>
												<div class="col-sm-3">
													<label for="input-kap_mesin_produksi" class="col-12 col-form-label text-center">Operasional (Jam/Hari)</label>
													<div class="col-12">
														<input type="number" class="form-control" required id="input-kap_prod_operasional" name="input-kap_prod_operasional" placeholder="" value="{{ $client['kap_prod_operasional'] }}">	
													</div>
												</div>
												<div class="col-sm-3">
													<label for="input-kap_mesin_produksi" class="col-12 col-form-label text-center">Hari Operasional (Perbulan)</label>
													<div class="col-12">
														<input type="number" class="form-control" required id="input-kap_prod_hari_operasional" name="input-kap_prod_hari_operasional" placeholder="" value="{{ $client['kap_prod_hari_operasional'] }}">	
													</div>
												</div>
												<div class="col-sm-3">
													<label for="input-kap_mesin_produksi" class="col-12 col-form-label text-center">Jumlah Produksi (Perbulan)</label>
													<div class="col-12">
														<input type="number" class="form-control" required id="input-kap_prod_jumlah_produksi" name="input-kap_prod_jumlah_produksi" placeholder="" value="{{ $client['kap_prod_jumlah_produksi'] }}">	
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card card-outline card-secondary">
										<div class="card-header">
											<label for="input-kap_mesin_produksi" class="col-sm-12 col-form-label text-center">Water Meter</label>
										</div>
										<div class="card-body">
											<div class="form-group row">
												
												<div class="col-sm-6">
													<label for="input-kap_mesin_produksi" class="col-12 col-form-label text-center">No Seri</label>
													<div class="col-12">
														<input type="text" class="form-control" required id="input-water_meter_no_seri" name="input-water_meter_no_seri" placeholder="" value="{{ $client['water_meter_no_seri'] }}">	
													</div>
												</div>
												<div class="col-sm-6">
													<label for="input-kap_mesin_produksi" class="col-12 col-form-label text-center">Kondisi (Baik/Rusak)</label>
													<div class="col-12">
														<select name="input-water_meter_kondisi" id="input-water_meter_kondisi" name="input-water_meter_kondisi" class="form-control" required>
															@foreach (hKondisiWaterMeter() as $k => $v)
															<option 

															@if ($k == $client['water_meter_kondisi'])
															selected
															@endif
															value="{{ $k }}">{{ $v }}</option>
															@endforeach
														</select>
													</div>
												</div>

											</div>

										</div>

									</div>
									<button type="submit" class="btn btn-success float-right" style="">
										<i class="fas fa-save"></i> Simpan Data Perusahaan
									</button>
								</form>
							</div>
							<div class=" active tab-pane" id="data-pengguna">
								<div id="html-pengguna">
									<form class="form-horizontal" enctype="multipart/form-data" id="form-data-pengguna" method="POST" >
										<div class="card card-outline card-secondary">
											<div class="card-body">
												<div class="form-group row">
													<label class="col-sm-4 col-form-label">Nama Pengguna</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" required id="input-nama_pengguna" name="input-nama_pengguna" placeholder=""
														value="{{ $pengguna['nama'] }}">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-4 col-form-label">Email</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" required id="input-email" name="input-email" placeholder=""
														value="{{ $pengguna['email'] }}">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-4 col-form-label">Username</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" required id="input-username" name="input-username" placeholder=""
														value="{{ $pengguna['username'] }}">
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-4 col-form-label">Password</label>
													<div class="col-sm-8">
														
														<div class="input-group">
															
															<input type="password" class="form-control"  id="input-password" name="input-password" placeholder="Kosongkan jika tidak ingin merubah password"
															>
															<div class="input-group-prepend">
																<button type="button" class="" id="btn-show" onclick="togglePassword()"><i class="fas fa-eye"></i></button>
															</div>
														</div>
													</div>
												</div>

											</div>
										</div>
										<button type="submit" class="btn btn-success float-right" style="">
											<i class="fas fa-save"></i> Simpan Data Pengguna
										</button>
									</form>
									
									
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('js-export')
@endsection
@section('js-inline')
<script>
	let $formDataPerusahaan = null;
	let $formDataPengguna = null;
	$(function() {
		$formDataPerusahaan = $("#form-data-perusahaan");
		$formDataPengguna = $("#form-data-pengguna");

		$formDataPerusahaan.submit(function(event) {
			event.preventDefault();
			$("body").loading('start');
			$(this).attr('disabled', true);
			data = new FormData($formDataPerusahaan[0]);
			axios.post('{{ base_url('client-profile/updateDataPerusahaan') }}', data)
			.then(res => {
				data = res.data;
				if (data.success) {
					Swal.fire({
						title: 'Sukses!',
						text: 'Berhasil merubah data perusahaan.',
						icon: 'success',
						timer: 1500,
						showConfirmButton: false,

					})
				}

			})
			.catch(err => {
				
			})
			.then(() => {
				$(this).attr('disabled', false);
				$("body").loading('stop');
			});
		});

		$formDataPengguna.submit(function(event) {
			event.preventDefault();
			$("body").loading('start');
			$(this).attr('disabled', true);
			data = new FormData($formDataPengguna[0]);
			axios.post('{{ base_url('client-profile/updateDataPengguna') }}', data)
			.then(res => {
				data = res.data;
				if (data.success) {
					Swal.fire({
						title: 'Sukses!',
						text: 'Berhasil merubah data pengguna.',
						icon: 'success',
						timer: 1500,
						showConfirmButton: false,

					})
				} else {
					Swal.fire({
						title: 'Gagal!',
						text: 'Gagal merubah data pengguna.',
						icon: 'warning',
						timer: 1500,
						showConfirmButton: false,

					})
				}

			})
			.catch(err => {
				
			})
			.then(() => {
				$(this).attr('disabled', false);
				$("body").loading('stop');
			});
		});
	});

	function togglePassword() {
		let inputPassword = document.getElementById("input-password");
		if (inputPassword.type === "password") {
			inputPassword.type = "text";
		} else {
			inputPassword.type = "password";

		}
	}
</script>
@endsection