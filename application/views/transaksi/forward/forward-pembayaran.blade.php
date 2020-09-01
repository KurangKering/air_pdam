@section('css-inline')
@parent

@endsection
<div class="card">
	
	<div class="card-body content-area">
		

		<table class="table">
			<col width="20%">
			<col width="2%">

			<tr>
				<th>Nama Perusahaan</th>
				<td>:</td>
				<td><span id="nama_perusahaan">{{  $client['nama_perusahaan'] }}</span></td>
			</tr>
			<tr>
				<th>Periode Transaksi</th>
				<td>:</td>
				<td><span id="bulan_terakhir">{{  $current['bulan_huruf'] }}</span> <span id="tahun_terakhir">{{  $current['tahun'] }}</span></td>
			</tr>

			<tr>
				<th>Status Transaksi</th>
				<td>:</td>
				<td><span id="status_terakhir">{{  $status_readable }}</span></td>
			</tr>
		</table>

	</div>
</div>

<div class="card ">
	<div class="card-header">
		<h4>Rincian Transaksi</h4>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="card-body " style="border-right: blue 1px solid;">
				<table class="table table-striped">
					<tr>
						<th>Tanggal Input</th>
						<td>:</td>
						<td><span  class="set-data-span" id="waktu_input">{{ $rincian_pembayaran['waktu_input']->copy()->format('d-m-Y') }}</span></td>
					</tr>
					<tr>
						<th>Tanggal Konfirmasi</th>
						<td>:</td>
						<td><span  class="set-data-span" id="waktu_verifikasi">{{ $rincian_pembayaran['waktu_verifikasi']->format('d-m-Y') }}</span></td>
					</tr>
					<tr>
						<th>Tanggal Bayar</th>
						<td>:</td>
						<td><span  class="set-data-span" id="waktu_pembayaran">{{ $rincian_pembayaran['waktu_pembayaran']->format('d-m-Y') }}</span></td>
					</tr>

					<tr>
						<th>Total Pemakaian</th>
						<td>:</td>
						<td><span class="set-data-span" id="total_pemakaian">{{ ($rincian_pembayaran['jumlah_pemakaian']) . " Watt" }}</span></td>
					</tr>
					<tr>
						<th>Biaya Pemakaian</th>
						<td>:</td>
						<td><span class="set-data-span" id="biaya_bersih">{{ hRupiah($rincian_pembayaran['biaya_bersih']) }}</span></td>
					</tr>
					<tr>
						<th>Denda Telat Input</th>
						<td>:</td>
						<td><span class="set-data-span" id="denda_input">{{ hRupiah($rincian_pembayaran['denda_input']) }}</span></td>
					</tr>
					<tr>
						<th>Denda Telat Bayar</th>
						<td>:</td>
						<td><span class="set-data-span" id="denda_bayar">{{ hRupiah($rincian_pembayaran['denda_bayar']) }}</span></td>
					</tr>

					<tr>
						<th>Total Transaksi</th>
						<td>:</td>
						<td><span id="biaya">{{  hRupiah($rincian_pembayaran['total_biaya']) }}</span></td>
					</tr>

				</table>

			</div>
		</div>
		<div class="col-md-6">
			<div class="card-body">
				<div class="img-pembayaran" style="text-align: center">
					<a href="{{ $transaksi->file_pembayaran_fullpath }}" data-fancybox data-caption="File Pembayaran" 
						id="btn-show-file-meteran">
						<img src="{{ $transaksi->file_pembayaran_fullpath }}" width="100%" height="100%" alt=""/>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="card ">
	<div class="card-header">
		<h4>Bukti Pembayaran</h4>
	</div>
	
</div>

<div class="card">
	
	<div class="card-body content-area" style="text-align: center">
		
		<input type="hidden" name="client_id" value="{{ $client->id }}">
		<button class="btn btn-primary btn-verifikasi" data-is-verif="1" type="button">Konfirmasi Pembayaran</button>
		<button class="btn btn-warning btn-verifikasi" data-is-verif="-1" type="button">Upload Ulang Bukti Pembayaran</button>


	</div>
</div>


@section('js-inline')
<script>
	$(".btn-verifikasi").click(function(event) {

		let is_verif = $(this).attr('data-is-verif');
		let client_id = $("input[name='client_id']").val();
		let formData = {
			is_verif: is_verif,
			client_id: client_id,
		};
		data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');

		let pesan_pembuka = null;
		let pesan_berhasil = null;
		let title = null
		if (is_verif == 1) {
			title = 'Konfirmasi Pembayaran';
			pesan_pembuka = "Yakin ingin konfirmasi pembayaran data ini ?";
			pesan_berhasil = "Berhasil konfirmasi pembyaran data";
		} else {
			title = 'Tolak Pembayaran';
			pesan_pembuka = "Yakin ingin menolak pembayaran data ini ?";
			pesan_berhasil = "Berhasil menolak pembayaran data";
		}

		Swal.fire({
			title: title,
			text: pesan_pembuka,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yakin!'
		}).then((result) => {
			if (result.value) {
				$("body").loading('start');

				axios.post("{{ base_url("transaksi/action_forward") }}", data)
				.then((res) => {

					data = res.data;
					if (data.success) {
						location.href = '{{ base_url('transaksi') }}';
					} else {
						
						$("body").loading('stop');
					}
					console.log(data);
				})
				.catch(err => {
					$("body").loading('stop');

				});
			}
		});

		
	});
</script>
@endsection