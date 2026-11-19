<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportvisit extends CI_Controller {

    public $dir_v = 'reportvisit/';

    public function __construct() {
        parent::__construct();
        $this->m_auth->check_login();
        $this->m_auth->check_akses();
        $this->load->model('M_reportvisit');
    }

    public function index() {
        $data['css'] = array(
            'lib/datatables/dataTables.bootstrap.min.css',
            'lib/datatables/fixedColumns.bootstrap.min.css',
            'lib/datepicker/datepicker.min.css',
            'lib/select/component-chosen.min.css',
        );
        $data['js'] = array(
            'lib/datatables/datatables.min.js',
            'lib/datatables/dataTables.bootstrap.min.js',
            'lib/datatables/dataTables.fixedColumns.min.js',
            'lib/sweetalert/sweetalert2.all.min.js',
            'lib/datepicker/datepicker.min.js',
            'lib/select/chosen.jquery.min.js',
            'lib/mask/jquery.mask.min.js',
            'src/js/admin/reportvisit.js',
        );
        $data['panel'] = '<i class="fa fa-clipboard-list"></i> &nbsp;<b>Laporan Kunjungan</b>';
        $this->l_skin->main($this->dir_v.'view', $data);
    }

    /**
     * DataTables JSON source — filter by date range
     */
    public function table() {
        $date_from = trim($this->input->get('date_from'));
        $date_to   = trim($this->input->get('date_to'));

        if (empty($date_from) || empty($date_to)) {
            echo json_encode(array(
                'draw'            => intval($this->input->get('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => array(),
            ));
            exit();
        }

        $date_from_db = $this->format_date_db($date_from);
        $date_to_db   = $this->format_date_db($date_to);

        $rows = $this->M_reportvisit->get_report($date_from_db, $date_to_db);

        $draw = intval($this->input->get('draw'));
        $data = array();
        $i    = 1;
        $grand_total = 0;

        foreach ($rows->result() as $row) {
            $jml = intval($row->jumlah);
            $grand_total += $jml;
            $data[] = array(
                'DT_RowId' => $row->tgl,
                '0'        => $i++,
                '1'        => !empty($row->tgl) ? date('d/m/Y', strtotime($row->tgl)) : '-',
                '2'        => $jml,
            );
        }

        echo json_encode(array(
            'draw'            => $draw,
            'recordsTotal'    => $rows->num_rows(),
            'recordsFiltered' => $rows->num_rows(),
            'data'            => $data,
            'grand_total'     => $grand_total,
        ));
        exit();
    }

    /**
     * Ambil daftar pasien berdasarkan tanggal (via AJAX)
     */
    public function patient_list() {
        $date = trim((string) $this->input->get('date'));
        if (empty($date)) {
            echo 'Tanggal tidak valid';
            return;
        }

        $date_db = $this->format_date_db($date);
        $result  = $this->M_reportvisit->get_patients_by_date($date_db);

        $rows = array();
        foreach ($result->result() as $row) {
            $row->umur          = $this->hitung_umur($row->patient_bod, $row->trans_doc);
            $row->diagnosa_list = $this->split_diagnosa($row->diagnosa);
            $rows[] = $row;
        }

        $data['rows'] = $rows;
        $data['tgl']  = $date;
        $this->load->view($this->dir_v.'patient_list', $data);
    }

    /**
     * Download detail pasien per tanggal (Excel)
     */
    public function download_patient_list() {
        $date = trim((string) $this->input->get('date'));

        if (empty($date)) {
            show_404();
        }

        $date_db = $this->format_date_db($date);
        $rows    = $this->M_reportvisit->get_patients_by_date($date_db);

        $filename = 'Detail_Kunjungan_' . str_replace('/', '-', $date) . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
              <head><meta charset="UTF-8">
              <style>td { mso-number-format:"\@"; }</style>
              </head><body>';
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background:#2d6a4f;color:#fff;">';
        echo '<th>No</th>';
        echo '<th>No. RM</th>';
        echo '<th>Nama Pasien</th>';
        echo '<th>JK</th>';
        echo '<th>Umur</th>';
        echo '<th>Perusahaan</th>';
        echo '<th>Telepon</th>';
        echo '<th>Dokter</th>';
        echo '<th>Diagnosa</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $no = 1;

        foreach ($rows->result() as $row) {
            $age  = $this->hitung_umur($row->patient_bod, $row->trans_doc);
            $dgn  = $this->split_diagnosa($row->diagnosa);
            $cell = '-';
            if (!empty($dgn)) {
                foreach ($dgn as $k => $item) {
                    $dgn[$k] = '• ' . htmlspecialchars((string) $item);
                }
                $cell = implode('<br />', $dgn);
            }

            echo '<tr>';
            echo '<td style="vertical-align:top;text-align:center;">' . $no++ . '</td>';
            echo '<td style="vertical-align:top;">' . htmlspecialchars((string) $row->trans_patient_code) . '</td>';
            echo '<td style="vertical-align:top;">' . htmlspecialchars((string) $row->patient_name) . '</td>';
            echo '<td style="vertical-align:top;text-align:center;">' . ($row->patient_gender === 'L' ? 'Laki-laki' : ($row->patient_gender === 'P' ? 'Perempuan' : '-')) . '</td>';
            echo '<td style="vertical-align:top;text-align:center;">' . ($age !== '' ? $age . ' Thn' : '-') . '</td>';
            echo '<td style="vertical-align:top;">' . (htmlspecialchars((string) $row->trans_patient_company) ?: '-') . '</td>';
            echo '<td style="vertical-align:top;">' . (htmlspecialchars((string) $row->trans_patient_phone) ?: '-') . '</td>';
            echo '<td style="vertical-align:top;">' . (htmlspecialchars((string) $row->doct_name) ?: '-') . '</td>';
            echo '<td style="vertical-align:top;">' . $cell . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</body></html>';
    }

    /**
     * Download Excel
     */
    public function download_excel() {
        $date_from = trim($this->input->get('date_from'));
        $date_to   = trim($this->input->get('date_to'));

        if (empty($date_from) || empty($date_to)) {
            show_404();
        }

        $date_from_db = $this->format_date_db($date_from);
        $date_to_db   = $this->format_date_db($date_to);

        $rows = $this->M_reportvisit->get_report($date_from_db, $date_to_db);

        $filename = 'Laporan_Kunjungan_' . $date_from . '_' . $date_to . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
              <head><meta charset="UTF-8">
              <style>td { mso-number-format:"\@"; }</style>
              </head><body>';
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background:#2d6a4f;color:#fff;">';
        echo '<th>No</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Jumlah Kunjungan</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $no = 1;
        $grand_total = 0;

        foreach ($rows->result() as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . (!empty($row->tgl) ? date('d/m/Y', strtotime($row->tgl)) : '-') . '</td>';
            echo '<td style="text-align:center;">' . intval($row->jumlah) . '</td>';
            echo '</tr>';
            $grand_total += intval($row->jumlah);
        }

        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr style="font-weight:700;">';
        echo '<td colspan="2" style="text-align:right;">Total</td>';
        echo '<td style="text-align:center;">' . $grand_total . '</td>';
        echo '</tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</body></html>';
    }

    /**
     * Pecah teks diagnosa (pemisah newline) menjadi array
     */
    private function split_diagnosa($text) {
        if (empty($text)) return array();
        $items = preg_split('/\r\n|\r|\n/', $text);
        $result = array();
        foreach ($items as $item) {
            $item = trim($item);
            if ($item !== '') $result[] = $item;
        }
        return $result;
    }

    /**
     * Hitung umur (tahun) pada tanggal referensi
     */
    private function hitung_umur($bod, $ref_date = null) {
        if (empty($bod) || $bod === '0000-00-00') return '';
        $birth = date_create($bod);
        $ref   = !empty($ref_date) ? date_create($ref_date) : date_create('now');
        if (!$birth || !$ref) return '';
        return $ref->diff($birth)->y;
    }

    /**
     * Konversi tanggal dd/mm/yyyy ke yyyy-mm-dd
     */
    private function format_date_db($date) {
        if (empty($date)) return null;
        $clean = str_replace('/', '-', $date);
        $timestamp = strtotime($clean);
        if (!$timestamp) return null;
        return date('Y-m-d', $timestamp);
    }
}
