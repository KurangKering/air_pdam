<div class="card">
	
	<div class="card-body">
		

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
<div class="card">
	
	<div class="card-body">
		
		<div class="form-group">
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Meteran Awal</th>
						<th>Meteran Akhir</th>
						<th>Meteran Pemakaian</th>
					</tr>
				</thead>
				<tbody>
					
					@foreach ($detail as $k => $v)
					<tr>
						<td>{{ $v->tanggal_dmy }}</td>
						<td>{{ $v->awal }}</td>
						<td>{{ $v->akhir }}</td>
						<td>{{ $v->pemakaian }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>


	</div>
</div>
<div class="card">
	<div class="card-header">
		<h4>Bukti Meteran</h4>
	</div>
	<div class="card-body">
		<div class="img-verifikasi" style="text-align: center;">
			<a href="{{ $current['file_meteran_fullpath'] }}" data-fancybox data-caption="File Meteran" 
			id="btn-show-file-meteran">
			<img src="{{ $current['file_meteran_fullpath'] }}" alt=""/>
		</a>
	</div>
</div>
</div>
<div class="card">
	
	<div class="card-body" style="text-align: center;">
		
		<input type="hidden" name="client_id" value="{{ $client->id }}">
		<button class="btn btn-lg btn-outline-primary btn-verifikasi" data-is-verif="1" type="button"><i class="fas fa-check"></i></button>
		<button class="btn btn-lg btn-outline-danger btn-verifikasi" data-is-verif="-1" type="button"><i class="fas fa-times"></i></button>


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
			title = 'Konfirmasi data';
			pesan_pembuka = "Yakin ingin melanjutkan data ini ?";
			pesan_berhasil = "Berhasil konfirmasi data";
		} else {
			title = 'Tolak Konfirmasi data';
			pesan_pembuka = "Yakin ingin menolak data ini ?";
			pesan_berhasil = "Berhasil menolak data";
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