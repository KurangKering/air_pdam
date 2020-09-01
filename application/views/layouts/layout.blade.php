@php
$CI =& get_instance();
$account = $CI->session->userdata('account');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Pajak | | | </title>
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ base_url('assets/template/utama/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ base_url('assets/plugins/fancybox/jquery.fancybox.min.css') }}">

  @yield('css-export')

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ base_url('assets/template/utama/dist/css/adminlte.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  @yield('css-inline')
  <style>
    .close:before {
      display: none;
    }
    
    .action-no-wrap {
      width: 1%;
      white-space: nowrap;
    }
    .loading-overlay {
      z-index: 99999999 !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li><a href="{{ base_url('logout') }}">Logout</a></li>
      </ul>
    </nav>
    <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="#" class="brand-link">
        <img src="{{ base_url('assets/template/utama/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
        style="opacity: .8">
        <span class="brand-text font-weight-light">Pajak 3</span>
      </a>
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <div class="elevation-2">

              <i class="fas fa-user-circle fa-2x"></i>
            </div>
          </div>
          <div class="info">
            <a href="#" class="d-block">{{ $account['nama'] }}</a>
          </div>
        </div>
        <!-- Sidebar Menu -->
        @include('layouts.navbar')
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      @yield('content')
    </div>
    <!-- /.content-wrapper -->
    <!-- Main Footer -->
    <footer class="main-footer"></footer>
  </div>
  <!-- ./wrapper -->
  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="{{ base_url('assets/template/utama/plugins/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ base_url('assets/template/utama/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ base_url('assets/plugins/axios.min.js') }}"></script>
  <script src="{{ base_url('assets/plugins/sweetalert2.all.min.js') }}"></script>
  <script src="{{ base_url('assets/plugins/jquery.loading.min.js') }}"></script>
  <script src="{{ base_url('assets/plugins/fancybox/jquery.fancybox.min.js') }}"></script>
  <script>
    function toggleModal(modal, state) {
      if(state == $('body').hasClass('modal-open')) {
        throw new Error(
          'Modal is already ' + (state ? 'shown' : 'hidden') + '!'
          );
      }
      
      var d = $.Deferred();
      
      modal
      .one(state ? 'shown.bs.modal' : 'hidden.bs.modal', d.resolve)
      .modal(state ? 'show' : 'hide');

      return d.promise();
    };
  </script>
  @yield('js-export')

  <!-- AdminLTE App -->
  <script src="{{ base_url('assets/template/utama/dist/js/adminlte.min.js') }}"></script>


  @yield('js-inline')
  <script>

    $("body").loading({
      stoppable: false,
      theme: "light",
      start: false,
    });

    function Pemakaian(tanggal,meter_awal,meter_akhir, id) {
      let _tanggal = tanggal;
      let _meter_awal = meter_awal;
      let _meter_akhir = meter_akhir;
      let _pemakaian = meter_akhir - meter_awal;
      let _id = id;

      function getId () {
        return _id;

      }
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
        if (getMeterAwal() <= parseInt(_meter_akhir)) {
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
        if ((getMeterAkhir() >= getMeterAwal())) {
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

        if (getMeterAwal() <= intMeterAkhir) {
          _meter_akhir = intMeterAkhir;
        }
        setPemakaian();

      }

      return {
        getId,
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

        let inputIdDetail = $("<input/>", {
          type : 'hidden',
          name : 'input-detail_transaksi_id[]',
          value : objPemakaian.getId(),
        });
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
          type : 'number',
          required : true,
          name : 'input-meter_awal[]',
          class : 'form-control',
          id : "input_meter_awal_"+index,
          value : objPemakaian.getMeterAwal(),
          readonly : true,
        });
        let kolomMeterAkhir = $("<td/>");
        let inputMeterAkhir = $("<input/>", {
          type : 'number',
          required : true,
          name : 'input-meter_akhir[]',
          class : 'form-control',
          id : "input_meter_akhir_"+index,
          value : objPemakaian.getMeterAkhir(),
        });
        let kolomPemakaian = $("<td/>");
        let inputPemakaian = $("<input/>", {
          type : 'number',
          required : true,
          name : 'input-pemakaian[]',
          class : 'form-control',
          id : "input_pemakaian_"+index,
          readonly : true,
          value : objPemakaian.getPemakaian()
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


        });

        elements.push({
          tanggal : inputTanggal.attr("id"),
          meter_awal : inputMeterAwal.attr("id"),
          meter_akhir : inputMeterAkhir.attr("id"),
          pemakaian : inputPemakaian.attr("id"),
        })

        let populate = tableRow.append(inputIdDetail)
        .append(kolomTanggal.append(inputTanggal))
        .append(kolomMeterAwal.append(inputMeterAwal))
        .append(kolomMeterAkhir.append(inputMeterAkhir))
        .append(kolomPemakaian.append(inputPemakaian));
        // .append(kolomAction.append(btnAction));

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
</body>
</html>
