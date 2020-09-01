@extends('layouts.layout')
@section('css-export')
<link rel="stylesheet" href="{{ base_url("assets/modules/datatables/datatables.min.css") }}">
<link rel="stylesheet" href="{{ base_url("assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css") }}">
<link rel="stylesheet" href="{{ base_url("assets/modules/prism/prism.css") }}">


@endsection
@section('content')
<section class="section">
	<div class="section-header">
		<h1>Data Profile</h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item active"></div>
		</div>
	</div>

	<div class="row">
		

		<div class="col-md-12">
			<div class="card">
				
				<div class="card-body">
					<div class="col-md-6 offset-md-3">
						<form id="form-profile">
							
							<div class="form-group">
								<label for="message-text" class="col-form-label">Nama:</label>
								<input type="text" class="form-control" id="input-nama" placeholder="Nama" value="{{ $data['profile']->pengguna_nama }}">
								<div class="error"></div>

							</div>

							<div class="form-group">
								<label for="message-text" class="col-form-label">Username:</label>
								<input type="text" class="form-control" id="input-username" placeholder="username" value="{{ $data['profile']->pengguna_username }}">
								<div class="error"></div>

							</div>

							<div class="form-group">
								<label for="message-text" class="col-form-label">Password:</label>
								<input type="password" class="form-control" id="input-password" placeholder="Kosongkan jika tidak merubah Password">

								<div class="error"></div>

							</div>
							
							
							

							<div class="form-group row">
								<div class="col-md-3">

								</div>
								<div class="col-md-9">
									<button type="submit" class="btn btn-primary">Ubah</button>

								</div>
							</div>




						</form>
					</div>
				</div>
				
			</div>
		</div>


	</div>



</section>


@endsection
@section('js-export')


@endsection
@section('js-inline')
<script>
	let $iNama = null;
	let $iUsername = null;
	let $iPassword = null;
	$(function() {

		$iNama = $("#input-nama");
		$iUsername = $("#input-username");
		$iPassword = $("#input-password");


		$("#form-profile").submit(function(e) {
			e.preventDefault();
			$(this).find(':submit').attr('disabled','disabled');


			formData = {
				nama: $iNama.val(),
				username: $iUsername.val(),
				password: $iPassword.val(),
			};
			data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&')
			var url = '{{ base_url('profile/update') }}';


			axios.post(url, data)
			.then(res => {
				$(this).find(':submit').attr('disabled',false);

				data = res.data;
				if (data.success == 0) {

					$.each(data.messages, function(key, value) {

						$('#'+key).addClass('is-invalid');
						$('#'+key).parent('.form-group').find('.error').html(value);
					});
				} else if (data.success == 1){
					Swal.fire({
						title: 'Berhasil!',
						text: 'Berhasil merubah data profile.',
						icon: 'success',
						timer: 500,
						showConfirmButton: false,

					})
					.then(() => {
						location.reload();
					})



				}



			})
			.catch(err => {
				$(this).find(':submit').attr('disabled',false);


			});
		})
	});
</script>
@endsection