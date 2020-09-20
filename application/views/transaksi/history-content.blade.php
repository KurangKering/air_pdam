
@php
$arrBelum = [KeteranganPeriode::BELUM_PERIODE_INI, KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA];
$arrPending = ['pending_periode_ini','pending_periode_sebelumnya'];
$arrEmpty = ['empty'];
$arrSuccess = ['success'];
@endphp
<div class="row">
	<div class="col-12">
		<div class="card ">

			<div class="card-body">
				<table class="table  ">
					<col >
					<col width="1%">
					<col >
					<tr>
						<th>Nama Perusahaan</th>
						<td>:</td>
						<td>{{ $data_perusahaan['nama_perusahaan'] }}</td>
					</tr>
					<tr>
						<th>Meteran AKhir</th>
						<td>:</td>
						<td>{{ $data_perusahaan['meteran_akhir'] }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="card ">
			<div class="card-header">
				<div class="card-title">
					<h5>Data Transaksi Terakhir</h5>
				</div>
			</div>
			<div class="card-body">
				@if ($is_ada)
				<table class="table  ">
					<col >
					<col width="1%">
					<col >
					<tr>
						<th>Periode</th>
						<td>:</td>
						<td>{{ $last_transaction['bulan_huruf'] . " " . $last_transaction['tahun'] }}</td>
					</tr>
					<tr>
						<th>Status</th>
						<td>:</td>
						<td>{{ $last_transaction['status_message'] }}</td>
					</tr>
				</table>
				<div class="d-flex">
					<div class="ml-auto">


						@if (in_array($status, $arrPending) && 1 == 2)
						<button type="button" class="btn btn-info btn-update">Proses Transaksi</button>
						@endif
					</div>

				</div>
				@else 
				-
				@endif

			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card ">
			<div class="card-header">
				<div class="card-title">
					<h5>Data Transaksi Selanjutnya</h5>
				</div>
			</div>
			<div class="card-body">
				<table class="table  ">
					<col >
					<col width="1%">
					<col >
					<tr>
						<th>Periode</th>
						<td>:</td>
						<td>{{ $next_transaction['bulan_huruf'] . " ". $next_transaction['tahun'] }}</td>
					</tr>

				</table>
				@if (in_array($status, $arrBelum))
				<button type="button" class="btn btn-info btn-block" id="btn-tambah">Tambah Transaksi</button>
				@endif
				<div class="d-flex">
					<div class="ml-auto">
						

						
					</div>

				</div>
			</div>
		</div>
	</div>

</div>



<script>
	$(function() {
		$(".btn-update").click(function(e) {

			window.location.href= "{{ base_url('transaksi/ubah/').$client_id }}";
		});

		$("#btn-tambah").click(function(event) {
			window.location.href= "{{ base_url('transaksi/tambah/').$client_id }}";
		});
	});
</script>