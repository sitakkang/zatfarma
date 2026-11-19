<div class="ds-detail-wrap">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:8px;">
        <div>
            <strong style="font-size:14px;">Tanggal: <?= htmlspecialchars($tgl) ?></strong>
            <span style="font-size:12px;color:#999;margin-left:8px;">
                Total: <?= count($rows) ?> pasien
            </span>
        </div>
        <button type="button" class="ds-btn-action ds-btn-blue patient-list-download"
                data-tgl="<?= htmlspecialchars($tgl) ?>" style="padding:6px 16px;">
            <i class="fa fa-file-excel"></i> Download
        </button>
    </div>

    <?php if (count($rows) > 0): ?>
    <div class="ds-wide-table-wrap" style="margin:0;">
        <table class="ds-table" style="margin-top:0;">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="100">No. RM</th>
                    <th>Nama Pasien</th>
                    <th width="80">JK</th>
                    <th width="70">Umur</th>
                    <th width="160">Perusahaan</th>
                    <th width="110">Telepon</th>
                    <th width="140">Dokter</th>
                    <th width="220">Diagnosa</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($rows as $r): ?>
                <tr>
                    <td style="vertical-align:top;"><?= $no++ ?></td>
                    <td style="vertical-align:top;"><?= htmlspecialchars((string) $r->trans_patient_code) ?></td>
                    <td style="vertical-align:top;"><?= htmlspecialchars((string) $r->patient_name) ?></td>
                    <td style="vertical-align:top;"><?= $r->patient_gender === 'L' ? 'Laki-laki' : ($r->patient_gender === 'P' ? 'Perempuan' : '-') ?></td>
                    <td style="vertical-align:top;"><?= $r->umur !== '' ? htmlspecialchars((string) $r->umur) . ' Thn' : '-' ?></td>
                    <td style="vertical-align:top;"><?= htmlspecialchars((string) $r->trans_patient_company) ?: '-' ?></td>
                    <td style="vertical-align:top;"><?= htmlspecialchars((string) $r->trans_patient_phone) ?: '-' ?></td>
                    <td style="vertical-align:top;"><?= htmlspecialchars((string) $r->doct_name) ?: '-' ?></td>
                    <td style="vertical-align:top;">
                        <?php if (!empty($r->diagnosa_list)): ?>
                            <?php foreach ($r->diagnosa_list as $dgn): ?>
                            <div style="display:flex;align-items:flex-start;gap:4px;margin-bottom:2px;">
                                <i class="fa fa-caret-right" style="color:var(--ds-primary, #2d6a4f);margin-top:3px;flex:none;"></i>
                                <span><?= htmlspecialchars((string) $dgn) ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-muted"><i class="fa fa-info-circle"></i> Tidak ada data pasien.</p>
    <?php endif; ?>

</div>
