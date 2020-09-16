@extends('layouts.layout')
@section('css-export')
<style>
	.bersinar {
		background-color: red;
	}
</style>
@endsection
@section('content')
<div class="page-header">
	<h1 class="page-title">
		Tambah Transaksi
	</h1>
</div>
<div class="row row-cards row-deck">
	<div class="col-12">
		<div class="card">
			<!-- /.card-header -->
			<!-- form start -->
			<div class="card-body">
				<form enctype="multipart/form-data" id="form-transaksi" method="GET">
					<input name="input-client_id" type="hidden" value="{{ $data['data_perusahaan']['id'] }}">
					<input name="input-periode_bulan" type="hidden" value="{{ $data['next']['bulan'] }}">
					<input name="input-periode_tahun" type="hidden" value="{{ $data['next']['tahun'] }}">
					<input name="input-waktu_input" type="hidden" value="">
					<input name="input-waktu_verifikasi" type="hidden" value="">
					<input name="input-waktu_mulai_pembayaran" type="hidden" value="">
					<input name="input-biaya" type="hidden" value="">
					<div class="form-group">
						<label class="form-label">
							Nama Perusahaan
						</label>
						<input class="form-control" disabled="" name="input-nama_perusahaan" type="text" value="{{ $data['data_perusahaan']['nama_perusahaan'] }}">
					</input>
				</div>
				<div class="form-group">
					<label class="form-label">
						Periode Bulan
					</label>
					<input class="form-control" disabled="" name="" type="text" value="{{ $data['next']['bulan_huruf'] }}">
				</input>
			</div>
			<div class="form-group">
				<label class="form-label">
					Periode Tahun
				</label>
				<input class="form-control" disabled="" name="" type="text" value="{{ $data['next']['tahun'] }}">
			</input>
		</div>
		<div class="form-group">
			<label class="form-label">
				Scan Meteran
			</label>
			<input class="form-control" id="file_meteran" name="file_meteran" type="file">
		</input>
	</div>
	<table class="table table-bordered">
		<tr>
			<th>
				Tanggal
			</th>
			<th>
				Meter Awal
			</th>
			<th>
				Meter Akhir
			</th>
			<th>
				Pemakaian
			</th>
			<th>
				Action
			</th>
		</tr>
		<tbody id="content-transaksi">
		</tbody>
	</table>
	<table class="table table-bordered table-hover">
		<tr>
			<th width="50%">
				Tanggal Input
			</th>
			<td>
				<span class="set-data-span" id="waktu_input">
					: -
				</span>
			</td>
		</tr>
		<tr>
			<th width="50%">
				Tanggal Konfirmasi
			</th>
			<td>
				<span class="set-data-span" id="waktu_verifikasi">
					: -
				</span>
			</td>
		</tr>
		<tr>
			<th width="50%">
				Biaya Pemakaian
			</th>
			<td>
				<span class="set-data-span" id="biaya_bersih">
					: -
				</span>
			</td>
		</tr>
		<tr>
			<th width="50%">
				Denda Telat Input
			</th>
			<td>
				<span class="set-data-span" id="denda_input">
					: -
				</span>
			</td>
		</tr>
		<tr>
			<th width="50%">
				Denda Telat Bayar
			</th>
			<td>
				<span class="set-data-span" id="denda_bayar">
					: -
				</span>
			</td>
		</tr>
		<tr>
			<th width="50%">
				Total yang harus dibayar
			</th>
			<td>
				<span class="set-data-span" id="total_biaya">
					: -
				</span>
			</td>
		</tr>
	</table>
	<div class="form-group">
		<button class="btn btn-primary" id="btn-cek-biaya" type="button">
			Cek Biaya
		</button>
	</div>
	<div class="form-group">
		<label class="form-label">
			Scan Pembayaran
		</label>
		<input class="form-control" id="file_pembayaran" name="file_pembayaran" type="file">
	</input>
</div>
<div class="form-group">
	<button class="btn btn-primary" id="btn-submit" type="button">
		Submit
	</button>
</div>
</input>
</input>
</input>
</input>
</input>
</input>
</form>
</div>
</div>
</div>
</div>
@endsection
@section('js-export')

@endsection
@section('js-inline')
<script>
	let $inputMeteranAwal = null;
	let $inputMeteranAkhir = null;
	let $inputPemakaian = null;
	let $tombolLibur = null;
	let contohni = null;
	let $btnSubmit = null;
	$btnCekBiaya = null;

	$inputMeteranAwal = $(".input-meteran-awal");
	$inputMeteranAkhir = $(".input-meteran-akhir");
	$inputPemakaian = $(".input-pemakaian");
	$tombolLibur = $(".tombol-libur");
	$btnSubmit = $("#btn-submit");
	$btnCekBiaya = $("#btn-cek-biaya");
	$formTransaksi = $("#form-transaksi");

	let $clientId = $("input[name='input-client_id']").val();
	let formData = {
		id : $clientId,
	}
	data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')
	axios.post('{{ base_url('transaksi/getFormPemakaian') }}', data)
	.then(res => {

		data = res.data;


		const Container = new ContainerPemakaian();

		let meteranAkhir = data.next.meteran_akhir;
		let daftarTanggal = data.next.data_tanggal;

		daftarTanggal.forEach((element,index) => {
			let awal = "";
			if (index == 0) {
				awal = meteranAkhir;
			}
			Container.addPemakaian(new Pemakaian(element, awal));
		})

		const objRenderer = new Renderer(Container, "#content-transaksi");
		objRenderer.render();

		daftarTanggal.forEach((e,i) => {
			$("#input_meter_akhir_"+i).val(meteranAkhir + i + 1).trigger("change");
		})

	})
	.catch(err => {

	})



	$("#btn-submit").click(function(event) {
		event.preventDefault();
		$(this).attr('disabled', true);
		data = new FormData($formTransaksi[0]);

		axios.post('{{ base_url('transaksi/insert') }}', data)
		.then(res => {
			if (!res.data.success) 
			{
				swal({
					icon : 'warning',
					title : 'Gagal',
					text : 'Tidak ada ATK',
					buttons : false,
					timer : 1500,
				})
			} else
			{
				window.location.href = "{{ base_url('transaksi') }}";
			}

			$("#btn-submit").attr('disabled', false);

		})
		.catch(err => {
			$("#btn-submit").attr('disabled', false);

		});
	});
	$btnCekBiaya.click(function(event) {
		event.preventDefault();
		$btnCekBiaya.attr('disabled', true);

		var meteran_awal = $("input[name='input-meter_awal[]']").first().val();
		var meteran_akhir = $("input[name='input-meter_akhir[]']").last().val();

		let formData = {
			client_id : $clientId,
			meteran_awal : meteran_awal,
			meteran_akhir : meteran_akhir,
		}
		data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')


		axios.post('{{ base_url('transaksi/getBiayaTransaksi') }}', data)
		.then(res => {
			data = res.data;
			$("#waktu_input").text(data.waktu_input);
			$("#waktu_verifikasi").text(data.waktu_verifikasi);
			$("#biaya_bersih").text(data.biaya_bersih);
			$("#denda_input").text(data.denda_input);
			$("#denda_bayar").text(data.denda_bayar);
			$("#total_biaya").text(data.biaya_seluruhnya);

			$("input[name='input-waktu_input']").val(data.waktu_input);
			$("input[name='input-waktu_verifikasi']").val(data.waktu_verifikasi);
			$("input[name='input-waktu_mulai_pembayaran']").val(data.waktu_bayar);
			$("input[name='input-biaya']").val(data.biaya_seluruhnya_angka);

		})
		.catch(err => {
			$("#btn-submit").attr('disabled', false);

		})
		.then(() => {
			$btnCekBiaya.attr('disabled', false);
			

		});
	});
</script>
@endsection
