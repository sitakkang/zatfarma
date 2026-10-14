<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History_medical extends CI_Controller {

    public $dir_v = 'history_medical/';

    public function __construct() {
        parent::__construct();
        $this->m_auth->check_login();
        $this->m_auth->check_akses();
        $this->load->model('M_history_medical');
    }

    public function index() {
        $data['css'] = array(
            'lib/datatables/dataTables.bootstrap.min.css',
            'lib/datatables/fixedColumns.bootstrap.min.css',
        );
        $data['js'] = array(
            'lib/datatables/datatables.min.js',
            'lib/datatables/dataTables.bootstrap.min.js',
            'lib/datatables/dataTables.fixedColumns.min.js',
            'src/js/admin/history_medical.js?v=' . filemtime(FCPATH . 'src/js/admin/history_medical.js'),
        );
        $data['panel'] = '<i class="fa fa-notes-medical"></i> &nbsp;<b>Riwayat Rekam Medis</b>';
        $this->l_skin->main($this->dir_v.'view', $data);
    }

    public function table() {
        $code = trim($this->input->get('code'));
        $ktp  = trim($this->input->get('ktp'));
        $name = trim($this->input->get('name'));
        $draw = intval($this->input->get('draw'));

        $rows = array();
        if ($code !== '' || $ktp !== '' || $name !== '') {
            $rows = $this->M_history_medical->search_patients($code, $ktp, $name);
        }

        $data = array();
        $number = 1;
        foreach ($rows as $row) {
            $data[] = array(
                'DT_RowId' => intval($row->id_patient),
                '0' => $number++,
                '1' => htmlspecialchars($row->patient_code ?: '-', ENT_QUOTES, 'UTF-8'),
                '2' => htmlspecialchars($row->patient_name ?: '-', ENT_QUOTES, 'UTF-8'),
                '3' => htmlspecialchars($row->patient_ktp ?: '-', ENT_QUOTES, 'UTF-8'),
                '4' => htmlspecialchars($row->patient_nik ?: '-', ENT_QUOTES, 'UTF-8'),
                '5' => htmlspecialchars($row->patient_company ?: '-', ENT_QUOTES, 'UTF-8'),
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'draw' => $draw,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data,
            )));
    }

    public function patient_history() {
        $patient_id = intval($this->input->get('id'));
        if ($patient_id < 1) {
            show_404();
        }

        $patient = $this->M_history_medical->get_patient($patient_id);
        if (!$patient) {
            show_404();
        }

        $visits = $this->M_history_medical->get_visits($patient_id);
        $medical_record_ids = array();
        $visit_ids = array();
        $record_to_visit = array();
        foreach ($visits as $visit) {
            $visit->diagnoses = array();
            $visit->medicines = array();
            $visit->anamnesa = array();
            $visit->sks = array();
            if (!empty($visit->id_medical_record)) {
                $medical_record_ids[] = intval($visit->id_medical_record);
                $record_to_visit[intval($visit->id_medical_record)] = intval($visit->id_visit);
            }
            $visit_ids[] = intval($visit->id_visit);
        }

        $visits_by_id = array();
        foreach ($visits as $visit) {
            $visits_by_id[intval($visit->id_visit)] = $visit;
        }

        foreach ($this->M_history_medical->get_diagnoses($medical_record_ids) as $diagnosis) {
            $record_id = intval($diagnosis->medical_record_id);
            if (isset($record_to_visit[$record_id])) {
                $visits_by_id[$record_to_visit[$record_id]]->diagnoses[] = $diagnosis;
            }
        }

        foreach ($this->M_history_medical->get_medicines($medical_record_ids) as $medicine) {
            $record_id = intval($medicine->medical_record_id);
            if (isset($record_to_visit[$record_id])) {
                $visits_by_id[$record_to_visit[$record_id]]->medicines[] = $medicine;
            }
        }

        foreach ($this->M_history_medical->get_anamnesa($medical_record_ids) as $anamnesa) {
            $record_id = intval($anamnesa->medical_record_id);
            if (isset($record_to_visit[$record_id])) {
                $visits_by_id[$record_to_visit[$record_id]]->anamnesa[] = $anamnesa;
            }
        }

        foreach ($this->M_history_medical->get_sks($visit_ids) as $sks) {
            $visit_id = intval($sks->visit_id);
            if (isset($visits_by_id[$visit_id])) {
                $visits_by_id[$visit_id]->sks[] = $sks;
            }
        }

        $data['patient'] = $patient;
        $data['visits'] = $visits;
        $this->load->view($this->dir_v.'patient_history', $data);
    }
}
