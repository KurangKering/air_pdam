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
				<form id="form-transaksi" enctype="multipart/form-data" method="GET">
					
					<input type="hidden" name="input-client_id" value="{{ $data['data_perusahaan']['client_id'] }}">
					<input type="hidden" name="input-periode_bulan" value="{{ $data['next_transaction']['bulan'] }}">
					<input type="hidden" name="input-periode_tahun" value="{{ $data['next_transaction']['tahun'] }}">
					<div class="form-group">
						<label class="form-label">Nama Perusahaan</label>
						<input type="text" class="form-control" name="input-nama_perusahaan" value="{{ $data['data_perusahaan']['nama_perusahaan'] }}" disabled>
					</div>
					<div class="form-group">
						<label class="form-label">Periode Bulan</label>
						<input type="text" class="form-control" name="" value="{{ $data['next_transaction']['bulan_huruf'] }}" disabled>
					</div>
					<div class="form-group">
						<label class="form-label">Periode Tahun</label>
						<input type="text" class="form-control" name="" value="{{ $data['next_transaction']['tahun'] }}" disabled>
					</div>
					<div class="form-group">
						<label class="form-label">Status Transaksi</label>
						<select name="input-status" id="status" class="form-control">
							@foreach ($data['status_transaksi'] as $k => $v)
							@if (in_array($k, [1,3,6]))
							<option value="{{ $k }}">{{ $v }}</option>

							@endif
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">Scan Meteran</label>
						<input type="file" class="form-control" name="file_meteran" id="file_meteran">
					</div>
					<div class="form-group">
						<label class="form-label">Scan Pembayaran</label>
						<input type="file" class="form-control" name="file_pembayaran" id="file_pembayaran">
					</div>

					<table class="table table-bordered">
						<tr>
							<th>Tanggal</th>
							<th>Meter Awal</th>
							<th>Meter Akhir</th>
							<th>Pemakaian</th>
							<th>Action</th>
						</tr>
						<tbody id="content-transaksi">

						</tbody>
					</table>

					<div class="form-group">
						<button id="btn-submit" class="btn btn-primary" type="submit">Submit</button>
					</div>

				</form>
			</div>
			<!-- /.card-body -->
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
	$(function() {

		$inputMeteranAwal = $(".input-meteran-awal");
		$inputMeteranAkhir = $(".input-meteran-akhir");
		$inputPemakaian = $(".input-pemakaian");
		$tombolLibur = $(".tombol-libur");
		$btnSubmit = $("#btn-submit");
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

			let meteranAkhir = data.next_transaction.meteran_akhir;
			let daftarTanggal = data.next_transaction.data_tanggal;

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
		







	});


	function Pemakaian(tanggal,meter_awal,meter_akhir) {
		let _tanggal = tanggal;
		let _meter_awal = meter_awal;
		let _meter_akhir = meter_akhir;
		let _pemakaian = meter_akhir - meter_awal;


		function getTanggal () {
			return _tanggal;
		}

		function getMeterAwal() {
			if (parseInt(_meter_awal) >= 0 ) {
				return parseInt(_meter_awal);
			}
			return null;
		}

		function getMeterAkhir () {
			if (getMeterAwal() && getMeterAwal() <= parseInt(_meter_akhir)) {
				return (_meter_akhir);
			}
			return null;
		}

		function getPemakaian () {
			if (parseInt(_pemakaian) >= 0 ) {
				return (_pemakaian);
			}
			return null;
		}

		function setPemakaian() {
			if (getMeterAwal() && (getMeterAkhir() >= getMeterAwal())) {
				console.log(getMeterAwal() != null);
				_pemakaian = getMeterAkhir() - getMeterAwal();
			} else {
				_pemakaian = "";
			}
		}
		function setTanggal(intTanggal) {
			_tanggal = intTanggal;
		}
		function setMeterAwal(intMeterAwal) {
			_meter_awal = intMeterAwal;
			if (this.getMeterAwal() > this.getMeterAkhir()) {
				this.setMeterAkhir(null);
			} else {
				setPemakaian();
			}
		}
		function setMeterAkhir(intMeterAkhir) {
			intMeterAkhir = parseInt(intMeterAkhir);

			if (getMeterAwal()  &&  intMeterAkhir && getMeterAwal() <= intMeterAkhir) {
				_meter_akhir = intMeterAkhir;
			}
			setPemakaian();

		}

		return {
			getTanggal,
			getMeterAwal,
			getMeterAkhir,
			getPemakaian,
			setTanggal,
			setMeterAwal,
			setMeterAkhir,

		}
	}

	function ContainerPemakaian() {
		let _daftar_libur = new Array();
		let _daftar_pemakaian = new Array();

		function getPemakaian(i) {
			return _daftar_pemakaian[i];
		}

		function setMeterAkhir(i, v) {

			getPemakaian(i).setMeterAkhir(v);
			if (typeof _daftar_pemakaian[i + 1] !== 'undefined') {
				getPemakaian(i+1).setMeterAwal(getPemakaian(i).getMeterAkhir());
			}

		}

		function getAllPemakaian() {
			return _daftar_pemakaian;
		}

		function addPemakaian(Pemakaian) {
			_daftar_pemakaian.push(Pemakaian);
		}

		function getAllDaftarLibur() {
			return _daftar_libur;
		}

		function addDaftarLibur(daftar) {
			if ($.inArray(daftar, _daftar_libur) == -1) {
				_daftar_libur.push(daftar);
			}
			_daftar_libur.sort((a,b) => a - b);
		}

		function removeDaftarLibur(daftar) {
			if ($.inArray(daftar, _daftar_libur) != -1) {
				let index = $.inArray(daftar, _daftar_libur);
				_daftar_libur.splice(index,1);
			}
			_daftar_libur.sort((a,b) => a - b);
		}

		return {
			getPemakaian,
			addPemakaian,
			getAllPemakaian,
			setMeterAkhir,
			addDaftarLibur,
			removeDaftarLibur,
			getAllDaftarLibur,
		};
	}

	function Renderer(AllData, divContent) {

		let elements = [];

		function singleRender(AllData,index) {
			objPemakaian = AllData.getPemakaian(index);

			let tableRow = $("<tr/>");
			let kolomTanggal = $("<td/>");
			let inputTanggal = $("<input/>", {
				type : 'text',
				name : 'input-tanggal[]',
				class : 'form-control',
				readonly : true,
				id : "input_tanggal_"+index,
				value : objPemakaian.getTanggal(),

			});
			let kolomMeterAwal = $("<td/>");
			let inputMeterAwal = $("<input/>", {
				type : 'text',
				name : 'input-meter_awal[]',
				class : 'form-control',
				id : "input_meter_awal_"+index,
				value : objPemakaian.getMeterAwal(),
				readonly : true,
			});
			let kolomMeterAkhir = $("<td/>");
			let inputMeterAkhir = $("<input/>", {
				type : 'text',
				name : 'input-meter_akhir[]',
				class : 'form-control',
				id : "input_meter_akhir_"+index,
				value : objPemakaian.getMeterAkhir(),
			});
			let kolomPemakaian = $("<td/>");
			let inputPemakaian = $("<input/>", {
				type : 'text',
				name : 'input-pemakaian[]',
				class : 'form-control',
				id : "input_pemakaian_"+index,
				readonly : true,
			});

			let kolomAction = $("<td>");
			let btnAction = $("<button/>", {
				type : 'button',
				text : 'libur',
				class : "btn btn-secondary",
				id : "btn_action_"+index,
				"data-libur" : "off",
			});


			inputMeterAkhir.change(function(event) {
				let objPemakaian = 
				AllData.setMeterAkhir(index,$(this).val());

				updateData();

			});

			btnAction.click(function(event) {

				let value = $(this).attr("data-libur");


				if (value == "on") {
					AllData.removeDaftarLibur(index);
					$(this).parents('tr:first').removeClass('bersinar');

					$(this).attr("data-libur", "off");
				} else {
					AllData.addDaftarLibur(index);
					$(this).parents('tr:first').addClass('bersinar')
					$(this).attr("data-libur", "on");
				}

				console.log(AllData.getAllDaftarLibur());

			});

			elements.push({
				tanggal : inputTanggal.attr("id"),
				meter_awal : inputMeterAwal.attr("id"),
				meter_akhir : inputMeterAkhir.attr("id"),
				pemakaian : inputPemakaian.attr("id"),
			})

			let populate = tableRow.append(kolomTanggal.append(inputTanggal))
			.append(kolomMeterAwal.append(inputMeterAwal))
			.append(kolomMeterAkhir.append(inputMeterAkhir))
			.append(kolomPemakaian.append(inputPemakaian))
			.append(kolomAction.append(btnAction));

			return populate;

		}

		function updateData() {

			AllData.getAllPemakaian().forEach((ee, index) => {
				$("#"+elements[index].tanggal).val(ee.getTanggal());
				$("#"+elements[index].meter_awal).val(ee.getMeterAwal());
				$("#"+elements[index].meter_akhir).val(ee.getMeterAkhir());
				$("#"+elements[index].pemakaian).val(ee.getPemakaian());

			})
		}

		function render() {

			AllData.getAllPemakaian().forEach((element, index) => {
				$(divContent).append(singleRender(AllData, index));
			})

		}



		return {
			render,
		}
	}

</script>
@endsection