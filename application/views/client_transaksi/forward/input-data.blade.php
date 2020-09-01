<form id="form-transaksi" enctype="multipart/form-data" method="POST">
    <div class="card">
        <div class="card-body">
            <input type="hidden" name="flag">
            <input type="hidden" name="" value="{{ $current['bulan'] }}">
            <input type="hidden" name="" value="{{ $current['tahun'] }}">
            <div class="form-group">
                <label class="form-label">Nama Perusahaan</label>
                <input type="text" class="form-control" value="{{ $client['nama_perusahaan'] }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-label">Periode Bulan</label>
                <input type="text" class="form-control" name="" value="{{ $current['bulan_huruf'] }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-label">Periode Tahun</label>
                <input type="text" class="form-control" name="" value="{{ $current['tahun'] }}" disabled>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Tanggal</th>
                    <th>Meter Awal</th>
                    <th>Meter Akhir</th>
                    <th>Pemakaian</th>
                    {{-- <th>Action</th> --}}
                </tr>
                <tbody id="content-transaksi">
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Scan Meteran 
                    @if (isset($current['file_meteran_fullpath']))
                    | <a href="{{ $current['file_meteran_fullpath'] }}" data-fancybox data-caption="File Meteran" 
                    id="btn-show-file-meteran"
                    >Lihat File</a>
                    @endif
                </label>
                <input type="file" class="form-control" 
                @if (isset($current['file_meteran_fullpath']) && $current['file_meteran_fullpath'] == NULL)
                required
                @endif
                name="file_meteran" id="file_meteran">
            </div>
        </div>
    </div>
    <div class="form-group">
        <button id="btn-submit" class="btn btn-primary" type="submit">Submit</button>
    </div>
</form>
@section('js-export')
@parent
@endsection
@section('js-inline')
<script>
    let $inputMeteranAwal = null;
    let $inputMeteranAkhir = null;
    let $inputPemakaian = null;
    let $tombolLibur = null;
    let contohni = null;
    let $btnSubmit = null;
    let $btnShowFileMeteran = null;
    $(function() {
        $inputMeteranAwal = $(".input-meteran-awal");
        $inputMeteranAkhir = $(".input-meteran-akhir");
        $inputPemakaian = $(".input-pemakaian");
        $tombolLibur = $(".tombol-libur");
        $btnSubmit = $("#btn-submit");
        $formTransaksi = $("#form-transaksi");
        $btnShowFileMeteran = $("#btn-show-file-meteran");
        let formData = {
            xxx : 'xxx',
        }
        data = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
        $("body").loading('start');
        axios.post('{{ base_url('client-transaksi/generateTahapInput') }}', data)
        .then(res => {
            $("body").loading('stop');
            data = res.data;
            $("input[name='flag']").val(data.flag);
            const Container = new ContainerPemakaian();
            let currentNum = null;
            data.detail_transaksi.forEach((element,index) => {
                if (data.flag == 'tambah') {
                    if (index == 0) {
                        currentNum = element.awal;
                    }
                    element.awal = currentNum;
                    currentNum = currentNum + 1;
                    element.akhir = currentNum;
                }
                Container.addPemakaian(new Pemakaian(element.tanggal_dmy, element.awal, element.akhir, element.id));
            })
            const objRenderer = new Renderer(Container, "#content-transaksi");
            objRenderer.render();
        })
        .catch(err => {
            $("body").loading('stop');
        })
        $("#form-transaksi").submit(function(event) {
            event.preventDefault();
            $("body").loading('start');
            $(this).attr('disabled', true);
            data = new FormData($formTransaksi[0]);
            axios.post('{{ base_url('client-transaksi/actionInputData') }}', data)
            .then(res => {
                if (!res.data.success) 
                {
                    swal({
                        icon : 'warning',
                        title : 'Gagal',
                        text : 'Tidak ada Data',
                        buttons : false,
                        timer : 1500,
                    })
                    $("body").loading('stop');
                    $(this).attr('disabled', false);
                } else
                {
                    window.location.href = "{{ base_url('client-transaksi') }}"; 
                }
            })
            .catch(err => {
                $(this).attr('disabled', false);
                $("body").loading('stop');
            })
            .then(() => {
                $("body").loading('stop');

            });
        });
        // $btnShowFileMeteran.click(function(event) {
        //     event.preventDefault();
        //     let url_image = $(this).data('url-image');
        // });
    });
</script>
@endsection