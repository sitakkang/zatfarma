<input type="hidden" id="skbs_patient_id" value="<?= intval($patient->id_patient) ?>">

<div class="ds-form-group">
    <label>Nomor SKBS <span class="text-danger">*</span></label>
    <input type="text" id="skbs_docnumb" class="form-control"
           value="<?= htmlspecialchars($default_skbs_docnumb, ENT_QUOTES, 'UTF-8') ?>"
           placeholder="<?= htmlspecialchars($default_skbs_docnumb, ENT_QUOTES, 'UTF-8') ?>"
           maxlength="100" autocomplete="off">
</div>

<div class="row" style="margin: 0 -6px;">
    <div class="col-md-6" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Nama Pasien</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($patient->patient_name, ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>
    </div>
    <div class="col-md-6" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>NIK</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($patient->patient_nik, ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>
    </div>
</div>

<div class="ds-form-group">
    <label>Dokter Pemeriksa <span class="text-danger">*</span></label>
    <?php
    $user_level = $this->session->userdata('sess_level');
    $sess_id = $this->session->userdata('sess_id');
    ?>
    <select id="skbs_doct_id" class="form-control autocomplete">
        <option value="">-- Pilih --</option>
        <?php foreach ($dokter_list as $doctor): ?>
            <option value="<?= intval($doctor->id_user) ?>" <?= ($user_level == 3 && $doctor->id_user == $sess_id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($doctor->fullname, ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="row" style="margin: 0 -6px;">
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Hasil <span class="text-danger">*</span></label>
            <select id="skbs_result" class="form-control" onchange="togglePatientSkbsFields(this)">
                <option value="">-- Pilih --</option>
                <option value="FIT">FIT</option>
                <option value="FIT DENGAN CATATAN">FIT dengan Catatan</option>
                <option value="UNFIT">UNFIT</option>
                <option value="KETERANGAN">Keterangan</option>
            </select>
        </div>
    </div>
    <div class="col-md-8" style="padding: 0 6px; display:none;" id="skbs_desc_wrap">
        <div class="ds-form-group">
            <label>Keterangan</label>
            <input type="text" id="skbs_desc" class="form-control" placeholder="Keterangan hasil">
        </div>
    </div>
</div>

<div class="row" style="margin: 0 -6px;">
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Tekanan Darah (TD)</label>
            <input type="text" id="skbs_td" class="form-control" placeholder="cth: 120/80">
        </div>
    </div>
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Tinggi Badan (TB)</label>
            <input type="text" id="skbs_tb" class="form-control" placeholder="cth: 165">
        </div>
    </div>
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Berat Badan (BB)</label>
            <input type="text" id="skbs_bb" class="form-control" placeholder="cth: 65">
        </div>
    </div>
</div>

<div class="row" style="margin: 0 -6px;">
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Buta Warna (BW)</label>
            <input type="text" id="skbs_bw" class="form-control" placeholder="cth: Normal / Parsial">
        </div>
    </div>
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Visus Kanan (R)</label>
            <input type="text" id="skbs_r" class="form-control" placeholder="cth: 6/6">
        </div>
    </div>
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Visus Kiri (L)</label>
            <input type="text" id="skbs_l" class="form-control" placeholder="cth: 6/6">
        </div>
    </div>
</div>

<div class="row" style="margin: 0 -6px;">
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Visus Koreksi (R)</label>
            <input type="text" id="skbs_koreksi_r" class="form-control" placeholder="cth: 6/6">
        </div>
    </div>
    <div class="col-md-4" style="padding: 0 6px;">
        <div class="ds-form-group">
            <label>Visus Koreksi (L)</label>
            <input type="text" id="skbs_koreksi_l" class="form-control" placeholder="cth: 6/6">
        </div>
    </div>
    <div class="col-md-4" style="padding: 0 6px; display:none;" id="skbs_note_wrap">
        <div class="ds-form-group">
            <label>Catatan</label>
            <input type="text" id="skbs_note" class="form-control" placeholder="Catatan">
        </div>
    </div>
</div>

<script>
function togglePatientSkbsFields(el) {
    document.getElementById('skbs_desc_wrap').style.display = el.value === 'KETERANGAN' ? '' : 'none';
    document.getElementById('skbs_note_wrap').style.display = el.value === 'FIT DENGAN CATATAN' ? '' : 'none';
}
</script>
