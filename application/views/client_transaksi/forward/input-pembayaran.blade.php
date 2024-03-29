<form id="form-transaksi" enctype="multipart/form-data" method="POST">
	<div class="card">

		<div class="card-body">
			<table class="table">
				<tr>
					<th>Nama Perusahaan</th>
					<td>:</td>
					<td><span class="set-data-span" id="nama_perusahaan"></span></td>
				</tr>
				<tr>
					<th>Periode</th>
					<td>:</td>
					<td><span  class="set-data-span" id="periode"></span></td>
				</tr>
				<tr>
					<th>Tanggal Input</th>
					<td>:</td>
					<td><span  class="set-data-span" id="waktu_input"></span></td>
				</tr>
				<tr>
					<th>Tanggal Konfirmasi</th>
					<td>:</td>
					<td><span  class="set-data-span" id="waktu_verifikasi"></span></td>
				</tr>

				<tr>
					<th>Biaya Pemakaian</th>
					<td>:</td>
					<td><span class="set-data-span" id="biaya_bersih"></span></td>
				</tr>
				<tr>
					<th>Denda Telat Input</th>
					<td>:</td>
					<td><span class="set-data-span" id="denda_input"></span></td>
				</tr>
				<tr>
					<th>Denda Telat Bayar</th>
					<td>:</td>
					<td><span class="set-data-span" id="denda_bayar"></span></td>
				</tr>
				<tr>
					<th>Total yang harus dibayar</th>
					<td>:</td>
					<td><span  class="set-data-span" id="total_biaya"></span></td>
				</tr>
			</table>
		</div>
		<!-- /.card-body -->
	</div>

	<div class="card">
		<div class="card-body">
			<div class="form-group">
				<label class="form-label">Scan Pembayaran
					@if (isset($current['file_pembayaran_fullpath']))
					| <a href="{{ $current['file_pembayaran_fullpath'] }}" data-fancybox data-caption="File Pembayaran" 
					id="btn-show-file-meteran"
					>Lihat File</a>
					@endif


				</label>
				<input type="file" class="form-control"

				@if ($current['file_pembayaran_fullpath'] == NULL)
				required
				@endif

				name="file_pembayaran" id="file_pembayaran">
			</div>
		</div>
	</div>
	<div class="form-group">
		<button id="btn-submit" class="btn btn-primary" type="submit">Submit</button>
	</div>
</form>

@section('js-export')

@endsection
@section('js-inline')
<script>
	let $btnSubmit = null;
	let $formTransaksi = null;
	$(function() {

		$formTransaksi = $("#form-transaksi");

		let formData = {
			xxx : 'xxx',
		}
		data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
		$("body").loading('start');

		axios.post('{{ base_url('client-transaksi/generateTahapPembayaran') }}', data)
		.then(res => {

			data = res.data;
			console.log(data);
			$(".set-data-span").each(function(index, el) {
				var nama_variable = $(el).attr('id');
				$("#"+nama_variable).text(data[nama_variable]);
			});

			$("body").loading('stop');
			


		})
		.catch(err => {
			$("body").loading('stop');

		})


		$("#form-transaksi").submit(function(event) {
			event.preventDefault();
			$("body").loading('start');
			$(this).attr('disabled', true);
			$("#btn-submit").attr('disabled', true);

			data = new FormData($formTransaksi[0]);

			axios.post('{{ base_url('client-transaksi/actionPembayaran') }}', data)
			.then(res => {

				if (!res.data.success) 
				{
					swal({
						icon : 'warning',
						title : 'Gagal',
						text : 'Gagal',
						buttons : false,
						timer : 1500,
					})
					$("body").loading('stop');

				} else
				{
					window.location.href = "{{ base_url('client-transaksi') }}";  
				}

				$("#btn-submit").attr('disabled', false);

			})
			.catch(err => {
				$("body").loading('stop');
				$(this).attr('disabled', false);

				$("#btn-submit").attr('disabled', false);

			});
		});


	});



</script>
@endsection