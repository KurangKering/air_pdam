<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Carbon;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\CodeigniterAdapter;

class Transaksi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function forward($client_id)
    {
        $transaksi        = $this->M_Transaksi->getLast($client_id);
        $transaksi->biaya = hRupiah($transaksi->biaya);
        $detail           = $transaksi->detailTransaksi;

        $this->load->library('TrackTransaksi', ['client_id' => $client_id]);
        $track_transaksi = $this->tracktransaksi->generate();
        $current         = $track_transaksi['current'];

        $this->response['rincian_pembayaran'] = $this->calculateTahapPembayaran($transaksi->id);
        $this->response['transaksi']          = $transaksi;
        $this->response['detail']             = $detail;
        $this->response['status']             = $transaksi->status_transaksi_id;
        $this->response['client']             = $transaksi->dataClient;
        $this->response['current']            = $current;
        $this->response['status_readable']    = $transaksi->dataStatusTransaksi->status;
        return view('transaksi.forward', $this->response);
    }
    public function action_forward()
    {
        $post            = $this->input->post();
        $transaksi       = $this->M_Transaksi->getLast($post['client_id']);
        $status_sekarang = $transaksi->status_transaksi_id;
        $post_status     = -1;
        $post_data       = array();
        if ($status_sekarang == StatusTransaksi::INPUT_DATA) {
            if ($post['is_verif'] == 1) {
                $post_status                   = StatusTransaksi::MENUNGGU_PEMBAYARAN;
                $post_data['waktu_verifikasi'] = Carbon::now();

                $client                = $this->M_Client->findOrFail($post['client_id']);
                $client->meteran_akhir = $transaksi->meteran_akhir;
                $client->save();

                $bulan_pembayaran = $transaksi->periode->copy()->modify('first day of next month');
                $waktu_input      = $transaksi->waktu_input;
                $config           = $this->M_Config->first();
                $batas_input      = $bulan_pembayaran->copy()->addDays($config->batas_input);

                if ($waktu_input < $batas_input) {
                    $post_data['waktu_mulai_pembayaran'] = $post_data['waktu_verifikasi'];
                } else {
                    $post_data['waktu_mulai_pembayaran'] = $batas_input;

                }
            } else {
                $post_status = StatusTransaksi::GAGAL_VERIFIKASI;
            }

        } elseif ($status_sekarang == StatusTransaksi::KONFIRMASI_PEMBAYARAN) {
            if ($post['is_verif'] == 1) {
                $post_status        = StatusTransaksi::TRANSAKSI_BERHASIL;
                $rincian_pembayaran = $this->calculateTahapPembayaran($transaksi->id);
                $post_data['biaya'] = $rincian_pembayaran['total_biaya'];
            } else {
                $post_status = StatusTransaksi::GAGAL_PEMBAYARAN;
            }
        }

        $post_data['status_transaksi_id'] = $post_status;

        DB::beginTransaction();
        try {
            $update_transaksi = DB::table('transaksi')
            ->where('id', $transaksi->id)->update($post_data);
            $post_status = array
            (
                'transaksi_id' => $transaksi->id,
                'status_id'    => $post_data['status_transaksi_id'],
                'waktu'        => Carbon::now(),
            );
            $insert_status = DB::table('transaksi_status')->insert($post_status);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            die();
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function index()
    {
        $clients = $this->M_Client->with('lastTransaksi')->get();
        $clients->each(function ($q) {
            if (($q->lastTransaksi)) {
                $q->periode_readablee = $q->lastTransaksi->periode_readable;
            } else {
                $q->periode_readablee = $q->periode_readable;
            }
        });
        $this->response['clients'] = $clients;
        return view('transaksi.index', $this->response);
    }
    public function tambah()
    {
        $client_id = $this->uri->segment(3);
        $this->load->library('TrackTransaksi', ['client_id' => $client_id]);
        $track_transaksi   = $this->tracktransaksi->generate();
        $keterangan_client = $track_transaksi['keterangan_periode'];

        if (!in_array($keterangan_client, [KeteranganPeriode::BELUM_PERIODE_INI, KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA])) {
            echo "<script>alert('maaf tidak boleh masuk')</script>";
            die();
        }

        $next                = $track_transaksi['next'];
        $next_month          = $next['bulan'];
        $next_year           = $next['tahun'];
        $total_days          = cal_days_in_month(CAL_GREGORIAN, $next_month, $next_year);
        $next['jumlah_hari'] = $total_days;
        for ($i = 1; $i <= $total_days; $i++) {
            $next['data_tanggal'][] = "{$i}-{$next_month}-{$next_year}";
        }
        $data_status                               = StatusTransaksi::TRANSAKSI_BERHASIL;
        $this->response['data']['status']          = $data_status;
        $this->response['data']['next']            = $next;
        $this->response['data']['data_perusahaan'] = $track_transaksi['client'];
        return view('transaksi.tambah', $this->response);
    }
    public function ubah($client_id)
    {
        $format_transaction                         = $this->formatLastTransaction($client_id);
        $last_transaction                           = $format_transaction['last_transaction'];
        $data_transaction                           = $this->M_Transaksi->findOrFail($last_transaction['id']);
        $last_transaction['file_meteran']           = $data_transaction->file_meteran;
        $last_transaction['file_pembayaran']        = $data_transaction->file_pembayaran;
        $data_status                                = $this->M_Status->pluck('status', 'id');
        $this->response['data']['daftar_status']    = $data_status;
        $this->response['data']['last_transaction'] = $last_transaction;
        $this->response['data']['data_perusahaan']  = $format_transaction['data_perusahaan'];
        return view('transaksi.ubah', $this->response);
    }
    public function getDataForUbahMenu($format_json = true, $transaksi_id = false)
    {
        $transaksi_id            = $this->input->post('transaksi_id');
        $data_detail_transaction = $this->M_DetailTransaksi->where('transaksi_id', $transaksi_id)->get();
        $data_detail_transaction->each(function ($q) {
            $q->tanggal = indoDate($q->tanggal, 'd-m-Y');
        });
        if ($format_json) {
            $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data_detail_transaction));
        } else {
            return $data_detail_transaction;
        }
    }
    public function getFormPemakaian()
    {
        $client_id = $this->input->post('id');
        $this->getJsonTambahTransaksi($client_id);
    }
    private function getJsonTambahTransaksi($client_id)
    {
        $this->load->library('TrackTransaksi', ['client_id' => $client_id]);
        $track_transaksi   = $this->tracktransaksi->generate();
        $keterangan_client = $track_transaksi['keterangan_periode'];

        if (!in_array($keterangan_client, [KeteranganPeriode::BELUM_PERIODE_INI, KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA])) {
            echo "<script>alert('maaf tidak boleh masuk')</script>";
            die();
        }
        $next                = $track_transaksi['next'];
        $next_month          = $next['bulan'];
        $next_year           = $next['tahun'];
        $total_days          = cal_days_in_month(CAL_GREGORIAN, $next_month, $next_year);
        $next['jumlah_hari'] = $total_days;
        for ($i = 1; $i <= $total_days; $i++) {
            $next['data_tanggal'][] = "{$i}-{$next_month}-{$next_year}";
        }

        $next                = $track_transaksi['next'];
        $next_month          = $next['bulan'];
        $next_year           = $next['tahun'];
        $total_days          = cal_days_in_month(CAL_GREGORIAN, $next_month, $next_year);
        $next['jumlah_hari'] = $total_days;
        for ($i = 1; $i <= $total_days; $i++) {
            $next['data_tanggal'][] = "{$i}-{$next_month}-{$next_year}";
        }
        $response['next']                  = $next;
        $response['next']['meteran_akhir'] = $track_transaksi['client']->meteran_akhir;
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }
    public function getData()
    {
        $this->generateJsonDataTable();
    }
    private function generateJsonDataTable()
    {
        $dt = new Datatables(new CodeigniterAdapter);
        $dt->query('
            SELECT tr.id, tr.periode, tr.biaya, tr.meteran_awal, tr.meteran_akhir,
            cl.nama_perusahaan,
            st.status,
            st.id as status_id,
            cl.id as client_id
            FROM transaksi AS tr JOIN status st ON tr.status_transaksi_id = st.id
            JOIN client AS cl ON tr.client_id = cl.id
            ');
        $dt->edit('status', function ($data) {
            $status = strtoupper($data['status']);

            $html          = $status;
            $linkToForward = base_url("transaksi/forward/{$data['client_id']}");

            if (in_array($data['status_id'], [1, 4])) {
                $html = "
                <button type=\"button\" class=\"btn  btn-sm btn-outline-info\" onclick=\"location.href='{$linkToForward}'\">{$status}</button>
                ";
            }
            return $html;
        });
        $dt->edit('periode', function ($data) {
            return indoDate($data['periode'], 'F Y');
        });
        $dt->edit('biaya', function ($data) {
            return $data['biaya'] == null ? '-' : hRupiah($data['biaya']);
        });
        $dt->add('action', function ($data) {
            $html = '';

            $html .= "
            <button type=\"button\" class=\"btn  btn-sm btn-outline-primary\" onClick=\"showDetaiTransaksi('{$data['id']}',1)\">
            <i class=\"fas fa-eye\"></i> Detail</button>
            ";

            $html .= "
            <button type=\"button\" class=\"btn  btn-sm btn-outline-warning\" onClick=\"showModal('{$data['id']}',1)\">
            <i class=\"fas fa-edit\"></i> Ubah</button>
            ";

            $html .= "
            <button type=\"button\" class=\"btn  btn-sm btn-outline-danger\" onClick=\"showModal('{$data['id']}',2)\">
            <i class=\"fas fa-trash\"></i> Hapus</button>";
            return $html;});
        echo $dt->generate();
    }

    public function insert()
    {
        $this->insertOutputJson();
    }
    private function insertOutputJson()
    {
        $auth = $this->auth;
        $this->load->library('form_validation');
        $post                     = $this->input->post();
        $config                   = $this->M_Config->first();
        $total_days               = count($post['input-tanggal']);
        $meteran_awal             = $post['input-meter_awal'][0];
        $post['meteran_awal']     =  (int) $meteran_awal;
        $meteran_akhir            = $post['input-meter_akhir'][$total_days - 1];
        $post['meteran_akhir']    = (int) $meteran_akhir;
        $pemakaian                = $meteran_akhir - $meteran_awal;
        $post_tahun               = $post['input-periode_tahun'];
        $post_bulan               = $post['input-periode_bulan'];
        $post['periode']          = Carbon::createFromFormat('Y-m', "{$post_tahun}-{$post_bulan}")->endOfMonth()->format('Y-m-d');
        $post['harga_per_watt']   = $config->harga_per_watt;
        $post['waktu_input'] = Carbon::createFromFormat('d-m-Y H:i:s', $post['input-waktu_input']);
        $post['waktu_verifikasi'] = Carbon::createFromFormat('d-m-Y H:i:s', $post['input-waktu_verifikasi']);
        $post['waktu_mulai_pembayaran'] = Carbon::createFromFormat('d-m-Y H:i:s', $post['input-waktu_mulai_pembayaran']);
        $post['biaya'] = $post['input-biaya'];
        $post['input-status'] = StatusTransaksi::TRANSAKSI_BERHASIL;
        $post_transaction         = array
        (
            'client_id'           => $post['input-client_id'],
            'periode'             => $post['periode'],
            'biaya'               => $post['biaya'],
            'harga_per_watt'      => $post['harga_per_watt'],
            'meteran_awal'        => $post['meteran_awal'],
            'meteran_akhir'       => $post['meteran_akhir'],
            'waktu_input'         => $post['waktu_input'],
            'waktu_verifikasi'    => $post['waktu_verifikasi'],
            'waktu_mulai_pembayaran'    => $post['waktu_mulai_pembayaran'],
            'status_transaksi_id' => $post['input-status'],
        );
        DB::beginTransaction();
        try {
            $transaksi_id            = DB::table('transaksi')->insertGetId($post_transaction);
            $post_transaction_status = array
            (
                'transaksi_id' => $transaksi_id,
                'status_id'    => $post['input-status'],
                'waktu'        => Carbon::now(),
            );
            $insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
            $post_detail_transaction   = array();
            foreach ($post['input-tanggal'] as $k => $v) {
                $tanggal                   = $post['input-tanggal'][$k];
                $tanggal                   = Carbon::createFromFormat('d-m-Y', $tanggal);
                $post_detail_transaction[] = array
                (
                    'transaksi_id' => $transaksi_id,
                    'tanggal'      => $tanggal->format('Y-m-d'),
                    'awal'         => $post['input-meter_awal'][$k],
                    'akhir'        => $post['input-meter_akhir'][$k],
                    'pemakaian'    => $post['input-pemakaian'][$k],
                );
            }
            DB::table('detail_transaksi')->insert($post_detail_transaction);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function update()
    {
        $this->updateOutputJson();
    }
    private function updateOutputJson()
    {
        $auth = $this->auth;
        $this->load->library('form_validation');
        $post                   = $this->input->post();
        $config                 = $this->M_Config->first();
        $transaksi_id           = $this->input->post('input-transaksi_id');
        $total_days             = count($post['input-tanggal']);
        $meteran_awal           = $post['input-meter_awal'][0];
        $post['meteran_awal']   = $meteran_awal;
        $meteran_akhir          = $post['input-meter_akhir'][$total_days - 1];
        $post['meteran_akhir']  = $meteran_akhir;
        $pemakaian              = $meteran_akhir - $meteran_awal;
        $post['biaya']          = $config->harga_per_watt * $pemakaian;
        $post_tahun             = $post['input-periode_tahun'];
        $post_bulan             = $post['input-periode_bulan'];
        $post['periode']        = Carbon::createFromFormat('Y-m', "{$post_tahun}-{$post_bulan}")->endOfMonth()->format('Y-m-d');
        $post['harga_per_watt'] = $config->harga_per_watt;
        $post_transaction       = array
        (
            'biaya'               => $post['biaya'],
            'meteran_awal'        => $post['meteran_awal'],
            'meteran_akhir'       => $post['meteran_akhir'],
            'status_transaksi_id' => $post['input-status'],
        );
        DB::beginTransaction();
        try {
            $post_transaction_status = array
            (
                'transaksi_id' => $transaksi_id,
                'status_id'    => $post['input-status'],
                'waktu'        => Carbon::now(),
            );
            $data_transaction = $this->M_Transaksi->findOrFail($transaksi_id);
            // $data_transaction_status = $this->M_TransaksiStatus->where('transaksi_id', $transaksi_id)
            // ->orderBy('id', 'desc')->first();
            // if ($data_transaction_status) {
            //     if ($data_transaction_status->status_id != $post['input-status']) {
            //         $insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
            //     }
            // } else {
            //     $insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
            // }
            if ($data_transaction->status_transaksi_id != $post['input-status']) {
                if ($post['input-status'] < StatusTransaksi::MENUNGGU_PEMBAYARAN) {
                    $post_transaction['waktu_verifikasi'] = null;
                } else {
                    if ($data_transaction->waktu_verifikasi != null) {
                        $post_transaction['waktu_verifikasi'] = $data_transaction->waktu_verifikasi;
                    } else {
                        $post_transaction['waktu_verifikasi'] = Carbon::now();
                    }
                }
            }
            $update = DB::table('transaksi')->where('id', $transaksi_id)->update($post_transaction);
            foreach ($post['input-tanggal'] as $k => $v) {
                $tanggal                 = $post['input-tanggal'][$k];
                $tanggal                 = Carbon::createFromFormat('d-m-Y', $tanggal);
                $id_detail_transaction   = $post['input-detail_transaksi_id'][$k];
                $post_detail_transaction = array
                (
                    'awal'      => $post['input-meter_awal'][$k],
                    'akhir'     => $post['input-meter_akhir'][$k],
                    'pemakaian' => $post['input-pemakaian'][$k],
                );
                DB::table('detail_transaksi')->where('id', $id_detail_transaction)->update($post_detail_transaction);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->response['success'] = false;
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function delete()
    {
        $id = $this->input->post('id');
        $this->deleteOutputJson($id);
    }
    private function deleteOutputJson($id)
    {
        $data_transaction        = $this->M_Transaksi->findOrFail($id);
        $data_detail_transaction = $data_transaction->dataTransaksiStatus()->get();
        $data_detail_transaction->each(function ($q) {
            $q->delete();
        });
        $delete_data = $data_transaction->delete();
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function detail()
    {
        $id = $this->input->post('client_id');
        $this->getDetailOutputJson($id);
    }
    private function getDetailOutputJson($id)
    {
        $data   = $this->M_Transaksi->with('dataClient')->findOrFail($id);
        $output = array
        (
            'data_perusahaan' => array
            (
                'nama_perusahaan' => $data->dataClient->nama_perusahaan,
            ),
            'periode'         => indoDate($data->periode->format('Y-m-d'), 'F Y'),
            'biaya'           => $data->biaya,
            'harga_per_watt'  => $data->harga_per_watt,
            'meteran_awal'    => $data->meteran_awal,
            'meteran_akhir'   => $data->meteran_akhir,
            'tahapan'         => $data->tahapan,
        );
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output));
    }
    public function formatLastTransaction($clientId)
    {
        $last_transaction = $this->M_Transaksi->getLast($clientId);

        $output              = array();
        $output['is_ada']    = (bool) $last_transaction;
        $output['client_id'] = $clientId;
        $dataClient          = null;
        if ($last_transaction != null) {
            $dataClient = $last_transaction->dataClient;
        } else {
            $dataClient = $this->M_Client->findOrFail($clientId);
        }

        $output['data_perusahaan'] = array
        (
            'client_id'       => $dataClient->id,
            'nama_perusahaan' => $dataClient->nama_perusahaan,
            'meteran_akhir'   => $dataClient->meteran_akhir,
        );
        if ($last_transaction) {
            $now                        = Carbon::now();
            $biaya_transaksi            = $last_transaction->biaya;
            $periode_transaksi          = new Carbon($last_transaction->periode);
            $status_transaksi           = $last_transaction->status_transaksi_id;
            $periode_selanjutnya        = $periode_transaksi->copy()->modify('last day of next month');
            $batas_periode_selanjutnya  = $periode_selanjutnya->copy()->modify('last day of next month');
            $output['last_transaction'] = array
            (
                'id'             => $last_transaction->id,
                'bulan'          => $periode_transaksi->format('n'),
                'bulan_huruf'    => hBulan($periode_transaksi->format('n')),
                'tahun'          => $periode_transaksi->format('Y'),
                'biaya'          => $biaya_transaksi,
                'status'         => $status_transaksi,
                'status_message' => $last_transaction->dataStatusTransaksi->status,
            );
            $output['next_transaction'] = array
            (
                'bulan'       => $periode_selanjutnya->format('n'),
                'bulan_huruf' => hBulan($periode_selanjutnya->format('n')),
                'tahun'       => $periode_selanjutnya->format('Y'),
            );

            if ($now > $periode_transaksi && $now < $periode_selanjutnya) {
                if ($status_transaksi == StatusTransaksi::TRANSAKSI_BERHASIL) {
                    $output['status']   = 'success';
                    $output['messsage'] = "Periode " . indoDate($periode_transaksi->format('Y-m-d'), 'F Y') . " telah lunas";
                } else {
                    $output['status']   = 'pending_periode_ini';
                    $output['messsage'] = 'Anda belum menyelesaikan transaksi untuk periode ini';
                }
            } else if ($now > $periode_transaksi && $now > $periode_selanjutnya) {
                if ($status_transaksi == StatusTransaksi::TRANSAKSI_BERHASIL) {
                    if ($now < $batas_periode_selanjutnya) {
                        $output['status']  = 'belum_periode_ini';
                        $output['message'] = "Anda belum melakukan transaksi untuk periode {$periode_selanjutnya->format('F Y')}";
                    } else {
                        $output['status']  = 'belum_periode_sebelumnya';
                        $output['message'] = "Anda belum melakukan transaksi untuk periode {$periode_selanjutnya->format('F Y')}";
                    }
                } else {
                    $output['status']  = 'pending_periode_sebelumnya';
                    $output['message'] = "Anda belum menyelesaikan transaksi untuk periode {$periode_transaksi->format('F Y')}";
                }
            }
        } else {
            $periode_selanjutnya        = $dataClient->periode->modify('last day of this month');
            $output['next_transaction'] = array
            (
                'bulan'       => $periode_selanjutnya->format('n'),
                'bulan_huruf' => hBulan($periode_selanjutnya->format('n')),
                'tahun'       => $periode_selanjutnya->format('Y'),
            );
            $output['status']  = 'empty';
            $output['message'] = 'Belum ada transaksi';
        }
        return $output;
    }
    public function jGetFormatLastTransaction()
    {
        $client_id           = $this->input->post('client_id');
        $output              = $this->formatLastTransaction($client_id);
        $html                = view('transaksi.history-content', $output, true);
        $output['html']      = $html;
        $output['client_id'] = $client_id;
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output));
    }
    public function getDetailTransaksi()
    {
        $transaksi_id   = $this->input->post('transaksi_id');
        $transaksi      = $this->M_Transaksi->with('dataClient', 'dataStatusTransaksi', 'detailTransaksi')->where(['id' => $transaksi_id])->first()->toArray();
        $html           = view('transaksi.detail', compact('transaksi'), true);
        $output['html'] = $html;

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output));

    }
    public function calculateTahapPembayaran($transaksi_id)
    {
        $data_transaction = $this->M_Transaksi->with('dataStatusTransaksi', 'dataClient', 'detailTransaksi')->findOrFail($transaksi_id);
        if ($data_transaction->waktu_pembayaran == null) {
            return null;
        }
        $client = $data_transaction->dataClient;

        $tanggal_mulai_periode  = $data_transaction->periode->copy()->modify('first day of next month');
        $waktu_input            = $data_transaction->waktu_input;
        $waktu_verifikasi       = $data_transaction->waktu_verifikasi;
        $waktu_mulai_pembayaran = $data_transaction->waktu_mulai_pembayaran;
        $waktu_pembayaran       = $data_transaction->waktu_pembayaran;
        $config                 = $this->M_Config->first();
        $tanggal_batas_input    = $tanggal_mulai_periode->copy()->addDays($config->batas_input);
        $now                    = $data_transaction->waktu_pembayaran;
        $jumlah_hari_bayar      = $now->diffInDays($waktu_mulai_pembayaran);
        $jumlah_pemakaian       = $data_transaction->meteran_akhir - $data_transaction->meteran_awal;
        $biaya_bersih           = $jumlah_pemakaian * $config->harga_per_watt;
        $denda_input            = $tanggal_batas_input < $waktu_input ? ($biaya_bersih * $config->denda_input) : 0;
        $kali_denda_bayar       = round($jumlah_hari_bayar / $config->batas_bayar);
        $denda_bayar            = ($config->denda_bayar * $biaya_bersih) * $kali_denda_bayar;
        $biaya_seluruhnya       = $biaya_bersih + $denda_input + $denda_bayar;
        $output                 = array(
            'success'                 => 1,
            'client_id'               => $client->id,
            'periode'                 => $tanggal_mulai_periode->copy()->modify('last day of previous month'),
            'waktu_input'             => $waktu_input,
            'waktu_verifikasi'        => $waktu_verifikasi,
            'waktu_mulai_pembayaran'  => $waktu_mulai_pembayaran,
            'waktu_pembayaran'        => $waktu_pembayaran,
            'tanggal_batas_input'     => $tanggal_batas_input,
            'jumlah_hari_bayar'       => $jumlah_hari_bayar,
            'jumlah_pemakaian'        => $jumlah_pemakaian,
            'biaya_bersih'            => $biaya_bersih,
            'denda_input'             => $denda_input,
            'jumlah_kali_denda_bayar' => $kali_denda_bayar,
            'denda_bayar'             => $denda_bayar,
            'total_biaya'             => $biaya_seluruhnya,
            'persen_denda_input'      => $config->denda_input,
            'persen_denda_bayar'      => $config->denda_bayar,
            'harga_per_watt'          => $config->harga_per_watt,
            'hari_batas_input'        => $config->batas_input,
            'hari_batas_bayar'        => $config->batas_bayar,
        );
        return $output;
    }

    public function getBiayaTransaksi()
    {
        $post = $this->input->post();
        $client_id = $post['client_id'];
        $this->load->library('TrackTransaksi', ['client_id' => $client_id]);
        $track_transaksi = $this->tracktransaksi->generate();
        $periode = $track_transaksi['next']['periode'];
        $bulan_pembayaran = $periode->copy()->modify('first day of next month');
        $waktu_input      = Carbon::now();
        $waktu_verifikasi = Carbon::now();
        $config           = $this->M_Config->first();
        $batas_bayar      = $bulan_pembayaran->copy()->addDays($config->batas_input-1);
        $tanggal_batas_input    = $periode->copy()->addDays($config->batas_input-1);

        if ($waktu_input < $batas_bayar) {
            $waktu_bayar = $waktu_input;
        } else {
            $waktu_bayar = $batas_bayar;
        }
        $meteran_akhir = $post['meteran_akhir'];
        $meteran_awal = $post['meteran_awal'];
        $jumlah_hari_bayar      = $waktu_input->diffInDays($waktu_bayar);
        $jumlah_pemakaian       = $meteran_akhir - $meteran_awal;
        $biaya_bersih           = $jumlah_pemakaian * $config->harga_per_watt;
        $denda_input            = $tanggal_batas_input < $waktu_input ? ($biaya_bersih * $config->denda_input) : 0;
        $kali_denda_bayar       = round($jumlah_hari_bayar / $config->batas_bayar);
        $denda_bayar            = ($config->denda_bayar * $biaya_bersih) * $kali_denda_bayar;
        $biaya_seluruhnya       = $biaya_bersih + $denda_input + $denda_bayar;

        $output = [
            'waktu_input' => $waktu_input->format('d-m-Y H:i:s'),
            'waktu_verifikasi' => $waktu_verifikasi->format('d-m-Y H:i:s'),
            'waktu_bayar' => $waktu_bayar->format('d-m-Y H:i:s'),
            'biaya_bersih' => hRupiah($biaya_bersih),
            'denda_input' => hRupiah($denda_input),
            'denda_bayar' => hRupiah($denda_bayar),
            'biaya_seluruhnya' => hRupiah($biaya_seluruhnya),
            'biaya_seluruhnya_angka' => ($biaya_seluruhnya),
        ];

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($output));
    }
}
/* End of file Transaksi.php */
/* Location: ./application/controllers/Transaksi.php */
