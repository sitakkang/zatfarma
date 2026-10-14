<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_history_medical extends CI_Model {

    public function search_patients($code, $ktp, $name) {
        if ($code === '' && $ktp === '' && $name === '') {
            return array();
        }

        $this->db->select('id_patient, patient_code, patient_name, patient_ktp, patient_nik, patient_gender, patient_bod, patient_company, patient_job, patient_address, patient_phone, patient_status');
        $this->db->from('ms_patient');
        $this->db->group_start();

        if ($code !== '') {
            $this->db->or_like('patient_code', $code);
        }
        if ($ktp !== '') {
            $this->db->or_like('patient_ktp', $ktp);
            $this->db->or_like('patient_nik', $ktp);
        }
        if ($name !== '') {
            $this->db->or_like('patient_name', $name);
        }

        $this->db->group_end();
        $this->db->order_by('patient_name', 'ASC');
        return $this->db->get()->result();
    }

    public function get_patient($patient_id) {
        return $this->db->get_where('ms_patient', array(
            'id_patient' => intval($patient_id)
        ))->row();
    }

    public function get_visits($patient_id) {
        $this->db->select('visit.id_visit, visit.trans_patient_code, visit.trans_doc, visit.trans_insert_dt, visit.trans_vst_type, visit.trans_doct_by, doctor.fullname AS doctor_name, record.id_medical_record');
        $this->db->from('trans_visit AS visit');
        $this->db->join('trans_medical_record AS record', 'record.visit_id = visit.id_visit AND record.mrd_status = 1', 'left');
        $this->db->join('conf_users AS doctor', 'visit.trans_doct_by = doctor.id_user', 'left');
        $this->db->where('visit.patient_id', intval($patient_id));
        $this->db->where('visit.trans_status', 1);
        $this->db->order_by('visit.trans_doc', 'DESC');
        $this->db->order_by('visit.id_visit', 'DESC');
        return $this->db->get()->result();
    }

    public function get_diagnoses($medical_record_ids) {
        if (empty($medical_record_ids)) {
            return array();
        }
        $this->db->where_in('medical_record_id', $medical_record_ids);
        $this->db->where('trans_dgn_status', 1);
        $this->db->order_by('id_trans_dgn', 'ASC');
        return $this->db->get('trans_diagnosa')->result();
    }

    public function get_medicines($medical_record_ids) {
        if (empty($medical_record_ids)) {
            return array();
        }
        $this->db->where_in('medical_record_id', $medical_record_ids);
        $this->db->where('trans_obat_status', 1);
        $this->db->order_by('id_trans_obat', 'ASC');
        return $this->db->get('trans_obat')->result();
    }

    public function get_anamnesa($medical_record_ids) {
        if (empty($medical_record_ids)) {
            return array();
        }
        $this->db->where_in('medical_record_id', $medical_record_ids);
        $this->db->where('anm_status', 1);
        $this->db->order_by('id_trans_anm', 'ASC');
        return $this->db->get('trans_anamnesa')->result();
    }

    public function get_sks($visit_ids) {
        if (empty($visit_ids)) {
            return array();
        }
        $this->db->where_in('visit_id', $visit_ids);
        $this->db->order_by('docdate', 'DESC');
        return $this->db->get('sks')->result();
    }
}
