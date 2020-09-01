<div class="card ">
	<div class="card-body">
		<table class="table  ">
			<col >
			<col width="1%">
			<col >
			<tr>
				<th>Nama Perusahaan</th>
				<td>:</td>
				<td>{{ $transaksi['data_client']['nama_perusahaan'] }}</td>
			</tr>
			<tr>
				<th>Periode</th>
				<td>:</td>
				<td>{{ $transaksi['periode_readable'] }}</td>
			</tr>
			<tr>
				<th>Status Transaksi</th>
				<td>:</td>
				<td>{{ $transaksi['data_status_transaksi']['status'] }}</td>
			</tr>
			

		</table>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<table class="table table-striped">
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
</div>




