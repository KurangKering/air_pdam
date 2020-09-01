<div class="card">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<th style="width:50%">Nama Perusahaan</th>
						<td>{{ $client['nama_perusahaan'] }}</td>
					</tr>
					<tr>
						<th>Meteran AKhir</th>
						<td>{{ $client['meteran_akhir'] }}</td>
					</tr>
					<tr>
						<th>Periode Akhir</th>
						<td>{{ $periode_terakhir_readable }}</td>
					</tr>
					<tr>
						<th>Status</th>
						<td>{{ $status_readable ?? "-" }}</td>
					</tr>
					@if ($next != NULL) 
					<tr>
						<th>Periode Selanjutnya</th>
						<td>{{ $next['periode_readable'] }}</td>
					</tr>
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
@php
$keterangan_belum = array(
	KeteranganPeriode::BELUM_PERIODE_INI,
	KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA,
);
@endphp
<div class="card ">
	<div class="card-body">
		
		@if (
			in_array($keterangan_periode, $keterangan_belum) || 
			(isset($current) && 
				in_array($current['status'], 
					[
						StatusTransaksi::GAGAL_VERIFIKASI,
						StatusTransaksi::MENUNGGU_PEMBAYARAN, 
						StatusTransaksi::GAGAL_PEMBAYARAN
					]
				)
			)
			)
			<button class="btn btn-block btn-primary btn-proses" type="button">Proses Transaksi</button>

			@endif
		</div>
	</div>
	<script>
		$(function() {
			$(".btn-proses").click(function(e) {

				window.location.href= "{{ base_url('client-transaksi/forward/') }}";
			});
		});
	</script>