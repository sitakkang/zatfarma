<style>
    .history-patient-summary {
        background: #f5f8fa;
        border-radius: 6px;
        margin-bottom: 14px;
        padding: 12px 16px;
    }
    .history-patient-meta {
        color: #667085;
        font-size: 12px;
        margin-top: 4px;
    }
    #history_visits_table {
        table-layout: fixed;
        width: 100% !important;
    }
    #history_visits_table th,
    #history_visits_table td {
        overflow-wrap: anywhere;
        vertical-align: top;
        white-space: normal;
    }
    .history-cell-title {
        color: #327b5d;
        font-size: 11px;
        font-weight: 700;
        margin: 0 0 4px;
        text-transform: uppercase;
    }
    .history-cell-block + .history-cell-block {
        border-top: 1px solid #e8eeeb;
        margin-top: 8px;
        padding-top: 8px;
    }
    .history-muted {
        color: #7b8794;
    }
</style>

<div class="history-patient-summary">
    <strong><?= htmlspecialchars($patient->patient_name ?: '-', ENT_QUOTES, 'UTF-8') ?></strong>
    <div class="history-patient-meta">
        Kode: <?= htmlspecialchars($patient->patient_code ?: '-', ENT_QUOTES, 'UTF-8') ?>
        &nbsp; | &nbsp; KTP: <?= htmlspecialchars($patient->patient_ktp ?: '-', ENT_QUOTES, 'UTF-8') ?>
        &nbsp; | &nbsp; Perusahaan: <?= htmlspecialchars($patient->patient_company ?: '-', ENT_QUOTES, 'UTF-8') ?>
    </div>
</div>

<?php if (empty($visits)): ?>
    <div class="alert alert-info" style="margin:0;">
        <i class="fa fa-info-circle"></i> Belum ada riwayat kunjungan aktif untuk pasien ini.
    </div>
<?php else: ?>
    <table id="history_visits_table" class="ds-table">
        <thead>
            <tr>
                <th style="width:13%;">Tanggal</th>
                <th style="width:30%;">Anamnesa</th>
                <th style="width:45%;">Diagnosa &amp; Terapi</th>
                <th style="width:12%;">Dokter</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($visits as $visit): ?>
            <tr>
                <td>
                    <strong><?= !empty($visit->trans_doc) ? date('d/m/Y', strtotime($visit->trans_doc)) : '-' ?></strong>
                </td>
                <td>
                    <?php if (empty($visit->anamnesa)): ?>
                        <span class="history-muted">Tidak ada data anamnesa.</span>
                    <?php else: ?>
                        <?php foreach ($visit->anamnesa as $anamnesa): ?>
                            <div class="history-cell-block">
                                <?php if (!empty($anamnesa->anm_note)): ?>
                                    <div><strong>Keluhan:</strong> <?= nl2br(htmlspecialchars($anamnesa->anm_note, ENT_QUOTES, 'UTF-8')) ?></div>
                                <?php endif; ?>
                                <div style="font-size:12px;margin-top:4px;">
                                    Suhu: <?= htmlspecialchars($anamnesa->anm_temp ?: '-', ENT_QUOTES, 'UTF-8') ?> |
                                    Nadi: <?= htmlspecialchars($anamnesa->anm_pulse ?: '-', ENT_QUOTES, 'UTF-8') ?> |
                                    Respirasi: <?= htmlspecialchars($anamnesa->anm_respirasi ?: '-', ENT_QUOTES, 'UTF-8') ?><br>
                                    Tekanan darah: <?= htmlspecialchars($anamnesa->anm_blood_press ?: '-', ENT_QUOTES, 'UTF-8') ?> |
                                    Tinggi: <?= htmlspecialchars($anamnesa->anm_height ?: '-', ENT_QUOTES, 'UTF-8') ?> cm |
                                    Berat: <?= htmlspecialchars($anamnesa->anm_weight ?: '-', ENT_QUOTES, 'UTF-8') ?> kg
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="history-cell-block">
                        <div class="history-cell-title">Diagnosa</div>
                        <?php if (empty($visit->diagnoses)): ?>
                            <span class="history-muted">Tidak ada diagnosa.</span>
                        <?php else: ?>
                            <ul style="margin:0;padding-left:18px;">
                                <?php foreach ($visit->diagnoses as $diagnosis): ?>
                                    <li>
                                        <?= htmlspecialchars($diagnosis->trans_dgn_name ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                        <?php if (!empty($diagnosis->trans_dgn_note)): ?>
                                            <div class="history-muted"><?= htmlspecialchars($diagnosis->trans_dgn_note, ENT_QUOTES, 'UTF-8') ?></div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="history-cell-block">
                        <div class="history-cell-title">Obat</div>
                        <?php if (empty($visit->medicines)): ?>
                            <span class="history-muted">Tidak ada obat tercatat.</span>
                        <?php else: ?>
                            <ul style="margin:0;padding-left:18px;">
                                <?php foreach ($visit->medicines as $medicine): ?>
                                    <li>
                                        <?= htmlspecialchars($medicine->trans_obat_name ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                        <?= !empty($medicine->trans_obat_pulv_id) ? ' (Racikan)' : '' ?>
                                        <span class="history-muted">
                                            — <?= htmlspecialchars($medicine->trans_obat_dosis ?: '-', ENT_QUOTES, 'UTF-8') ?>,
                                            <?= intval($medicine->trans_obat_qty ?: 0) ?> <?= htmlspecialchars($medicine->trans_obat_satuan ?: '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <?php
                    $sksWithTherapy = array_filter($visit->sks, function ($sks) {
                        return !empty($sks->terapi);
                    });
                    ?>
                    <?php if (!empty($sksWithTherapy)): ?>
                        <div class="history-cell-block">
                            <div class="history-cell-title">Terapi</div>
                            <?php foreach ($sksWithTherapy as $sks): ?>
                                <div><?= nl2br(htmlspecialchars($sks->terapi, ENT_QUOTES, 'UTF-8')) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($visit->sks)): ?>
                        <div class="history-cell-block">
                            <div class="history-cell-title">Surat Keterangan Sakit (SKS)</div>
                            <?php foreach ($visit->sks as $sks): ?>
                                <div>
                                    <strong>No. SKS:</strong>
                                    <?= htmlspecialchars($sks->docnumb ?: '-', ENT_QUOTES, 'UTF-8') ?><br>
                                    <strong>Istirahat:</strong>
                                    <?= !empty($sks->datefrom) && strtotime($sks->datefrom) !== false ? date('d/m/Y', strtotime($sks->datefrom)) : '-' ?>
                                    &ndash;
                                    <?= !empty($sks->dateto) && strtotime($sks->dateto) !== false ? date('d/m/Y', strtotime($sks->dateto)) : '-' ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($visit->doctor_name ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
