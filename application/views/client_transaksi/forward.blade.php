@extends('layouts.layout')
@section('css-inline')
<style>
	* {
		margin: 0;
		padding: 0
	}

	html {
		height: 100%
	}

	#grad1 {
		background-color: : #9C27B0;
		background-image: linear-gradient(120deg, #FF4081, #81D4FA)
	}

	#msform {
		text-align: center;
		position: relative;
		margin-top: 20px
	}

	

	#msform fieldset {
		background: white;
		border: 0 none;
		border-radius: 0.5rem;
		box-sizing: border-box;
		width: 100%;
		margin: 0;
		padding-bottom: 20px;
		position: relative
	}

	#msform fieldset:not(:first-of-type) {
		display: none
	}

	#msform fieldset .form-card {
		text-align: left;
		color: #9E9E9E
	}

	#msform input,
	#msform textarea {
		padding: 0px 8px 4px 8px;
		border: none;
		border-bottom: 1px solid #ccc;
		border-radius: 0px;
		margin-bottom: 25px;
		margin-top: 2px;
		width: 100%;
		box-sizing: border-box;
		font-family: montserrat;
		color: #2C3E50;
		font-size: 16px;
		letter-spacing: 1px
	}

	#msform input:focus,
	#msform textarea:focus {
		-moz-box-shadow: none !important;
		-webkit-box-shadow: none !important;
		box-shadow: none !important;
		border: none;
		font-weight: bold;
		border-bottom: 2px solid skyblue;
		outline-width: 0
	}

	#msform .action-button {
		width: 100px;
		background: skyblue;
		font-weight: bold;
		color: white;
		border: 0 none;
		border-radius: 0px;
		cursor: pointer;
		padding: 10px 5px;
		margin: 10px 5px
	}

	#msform .action-button:hover,
	#msform .action-button:focus {
		box-shadow: 0 0 0 2px white, 0 0 0 3px skyblue
	}

	#msform .action-button-previous {
		width: 100px;
		background: #616161;
		font-weight: bold;
		color: white;
		border: 0 none;
		border-radius: 0px;
		cursor: pointer;
		padding: 10px 5px;
		margin: 10px 5px
	}

	#msform .action-button-previous:hover,
	#msform .action-button-previous:focus {
		box-shadow: 0 0 0 2px white, 0 0 0 3px #616161
	}

	select.list-dt {
		border: none;
		outline: 0;
		border-bottom: 1px solid #ccc;
		padding: 2px 5px 3px 5px;
		margin: 2px
	}

	select.list-dt:focus {
		border-bottom: 2px solid skyblue
	}

	.card {
		z-index: 0;
		border: none;
		border-radius: 0.5rem;
		position: relative
	}

	.fs-title {
		font-size: 25px;
		color: #2C3E50;
		margin-bottom: 10px;
		font-weight: bold;
		text-align: left
	}

	#progressbar {
		margin-bottom: 30px;
		overflow: hidden;
		color: lightgrey
	}

	#progressbar .active {
		color: #000000
	}

	#progressbar li {
		list-style-type: none;
		font-size: 12px;
		width: 33.33%;
		float: left;
		position: relative
	}

	#progressbar #input-data:before {
		font-family: FontAwesome;
		content: "\f044"
	}

	#progressbar #input-pembayaran:before {
		font-family: FontAwesome;
		content: "\f09d"
	}

	#progressbar #selesai:before {
		font-family: FontAwesome;
		content: "\f11e"
	}

	

	#progressbar li:before {
		width: 50px;
		height: 50px;
		line-height: 45px;
		display: block;
		font-size: 18px;
		color: #ffffff;
		background: lightgray;
		border-radius: 50%;
		margin: 0 auto 10px auto;
		padding: 2px
	}

	#progressbar li:after {
		content: '';
		width: 100%;
		height: 2px;
		background: lightgray;
		position: absolute;
		left: 0;
		top: 25px;
		z-index: -1
	}

	#progressbar li.active:before,
	#progressbar li.active:after {
		background: skyblue
	}

	.radio-group {
		position: relative;
		margin-bottom: 25px
	}

	.radio {
		display: inline-block;
		width: 204;
		height: 104;
		border-radius: 0;
		background: lightblue;
		box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
		box-sizing: border-box;
		cursor: pointer;
		margin: 8px 2px
	}

	.radio:hover {
		box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.3)
	}

	.radio.selected {
		box-shadow: 1px 1px 2px 2px rgba(0, 0, 0, 0.1)
	}

	.fit-image {
		width: 100%;
		object-fit: cover
	}
</style>
@endsection
@section('css-export')


@endsection
@section('css-inline')
@parent
<style>
	.bersinar {
		background-color: red;
	}
	
</style>
@endsection
@section('content')
@php
$statusInput = array(null, StatusTransaksi::GAGAL_VERIFIKASI);
$statusPembayaran = array(StatusTransaksi::MENUNGGU_PEMBAYARAN, StatusTransaksi::GAGAL_PEMBAYARAN);
$statusSelesai = array(StatusTransaksi::TRANSAKSI_BERHASIL);
@endphp
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		
	</div><!-- /.container-fluid -->
</section>
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div id="msform">

							<ul id="progressbar">
								<li  @if (
									in_array($tahapan, $statusInput) || 
									in_array($tahapan, $statusPembayaran) || 
									in_array($tahapan, $statusSelesai)  ) 
									class="active" 
									@endif  id="input-data"><strong>Input Data Meteran</strong>
								</li>
								<li  @if (
									in_array($tahapan, $statusPembayaran) || 
									in_array($tahapan, $statusSelesai) ) 
									class="active"
									@endif id="input-pembayaran"><strong>Input Bukti Pembayaran</strong>
								</li>
								<li @if (
									in_array($tahapan, $statusSelesai) ) 
									class="active" 
									@endif id="selesai"><strong>Transaksi Selesai</strong>
								</li>
							</ul> 
						</div>
					</div>
				</div>
				

				@if (in_array($tahapan, $statusInput))
				@include('client_transaksi.forward.input-data')
				@elseif (in_array($tahapan, $statusPembayaran))
				@include('client_transaksi.forward.input-pembayaran')

				@endif
			</div>
			
		</div>
	</div>
</section>



@endsection
@section('js-export')
@parent
@endsection
@section('js-inline')

@endsection