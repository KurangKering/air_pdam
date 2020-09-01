
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Login Sistem Pajak | | |</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ base_url('assets/template/utama/dist/css/adminlte.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <style>


    .main-head{
      height: 150px;
      background: #FFF;

    }
    
    .sidenav {
      height: 100%;
      background-color: #000;
      overflow-x: hidden;
      padding-top: 20px;
    }
    
    
    .main {
      padding: 0px 10px;
    }
    
    @media screen and (max-height: 450px) {
      .sidenav {padding-top: 15px;}
    }
    
    @media screen and (max-width: 450px) {
      .login-form{
        margin-top: 10%;
      }

      .register-form{
        margin-top: 10%;
      }
    }
    
    @media screen and (min-width: 768px){
      .main{
        margin-left: 40%; 
      }

      .sidenav{
        width: 40%;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
      }

      .login-form{
        margin-top: 80%;
      }

      .register-form{
        margin-top: 20%;
      }
    }
    
    
    .login-main-text{
      margin-top: 20%;
      padding: 60px;
      color: #fff;
    }
    
    .login-main-text h2{
      font-weight: 300;
    }
    
    .btn-black{
      background-color: #000 !important;
      color: #fff;
    }
  </style>
</head>
<body>
  <div class="sidenav">
   <div class="login-main-text">
    <h2>Sistem Perpajakan<br> Login Page</h2>
  </div>
</div>
<div class="main">
 <div class="col-md-6 col-sm-12">
  <div class="login-form">
    <form   method="post" id="form-login">
      <div class="form-group">
       <label>User Name</label>
       <input type="text" class="form-control" placeholder="User Name" id="username" name="username">
     </div>
     <div class="form-group">
       <label>Password</label>
       <input type="password" class="form-control" placeholder="Password" id="password" name="password">

     </div>

     <button type="submit" class="btn btn-black">Login</button>
   </form>
 </div>
</div>
</div>

<!-- jQuery -->
<script src="{{ base_url('assets/template/utama/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ base_url('assets/template/utama/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ base_url('assets/template/utama/dist/js/adminlte.min.js') }}"></script>
<script src="{{ base_url('assets/plugins/axios.min.js') }}"></script>
<script src="{{ base_url('assets/plugins/sweetalert2.all.min.js') }}"></script>
<script>
  $(function() {

    $username = $("#username");
    $password = $("#password");

    $("#form-login").submit(function(e) {

      e.preventDefault();

      $(this).find(':submit').attr('disabled','disabled');


      let post_data = {
        username: $username.val(),
        password: $password.val(),
      };

      post_data = Object.keys(post_data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(post_data[key])).join('&')

      axios.post("{{ base_url("auth/login") }}", post_data)
      .then((res) => {
        $(this).find(':submit').attr('disabled',false);


        response = res.data;

        if (response.success == 0) {
         Swal.fire({
          title: 'Gagal!',
          text: response.message,
          icon: 'error',
          timer: 1000,
          showConfirmButton: false,

        });
       } else if(response.success == 1) {

        window.location.href = "{{ base_url('') }}";
      }

    })
      .catch(() => {
        $(this).find(':submit').attr('disabled',false);


      })

    });

    $(".toggle-password").click(function() {

      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
  });
</script>
</body>
</html>
