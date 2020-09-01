
<div class="card ">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table">
				<tbody>

					<tr>
						<th style="width: 50%">Nama Perusahaan</th>
						<td>{{ $transaksi['data_client']['nama_perusahaan'] }}</td>
					</tr>
					<tr>
						<th>Periode</th>
						<td>{{ $transaksi['periode_readable'] }}</td>
					</tr>
					<tr>
						<th>Status Transaksi</th>
						<td>{{ $transaksi['data_status_transaksi']['status'] }}</td>
					</tr>



				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-body">
		<table class="table">
			<thead>
				<tr>
					<th>Tanggal</th>
					<th>Meteran Awal</th>
					<th>Pemakaian</th>
					<th>Meteran Akhir</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($transaksi['detail_transaksi'] as $detail)
				<tr>
					<td>{{ $detail['tanggal_dmy'] }}</td>
					<td>{{ $detail['awal'] }}</td>
					<td>{{ $detail['pemakaian'] }}</td>
					<td>{{ $detail['akhir'] }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>




