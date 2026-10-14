<?php
$age = '';
if (!empty($row->patient_bod)) {
    $birth_date = date_create($row->patient_bod);
    $reference_date = !empty($row->trans_doc) ? date_create($row->trans_doc) : date_create('now');
    if ($birth_date && $reference_date) {
        $age = date_diff($birth_date, $reference_date)->y;
    }
}
$visit_date = !empty($row->trans_doc) ? date('d/m/Y', strtotime($row->trans_doc)) : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resep - <?= htmlspecialchars($row->patient_name ?: 'Pasien', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        @page { size: A5 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: Arial, sans-serif; font-size: 12px; }
        .toolbar { margin: 12px auto; text-align: center; }
        .toolbar button { padding: 8px 18px; cursor: pointer; }
        .sheet { width: 100%; max-width: 148mm; margin: 0 auto; }
        .header { border-bottom: 2px solid #222; padding-bottom: 8px; text-align: center; }
        .header img { max-width: 55mm; max-height: 18mm; object-fit: contain; }
        .header h1 { margin: 4px 0 0; font-size: 17px; }
        .header p { margin: 3px 0 0; font-size: 10px; }
        .title { margin: 14px 0 9px; text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 2px; }
        .patient { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .patient td { padding: 3px 2px; vertical-align: top; }
        .patient .label { width: 85px; }
        .patient .separator { width: 12px; }
        .rx { margin: 10px 0; font-family: Georgia, serif; font-size: 34px; font-style: italic; }
        table.medicine { width: 100%; border-collapse: collapse; }
        table.medicine th, table.medicine td { border: 1px solid #555; padding: 6px; text-align: left; }
        table.medicine th { background: #f2f2f2; }
        .section { margin: 11px 0 5px; font-weight: bold; }
        .signature { margin: 28px 10px 0 auto; width: 45%; text-align: center; }
        .signature .space { height: 45px; }
        .signature strong { text-decoration: underline; }
        @media print {
            .toolbar { display: none; }
            .sheet { max-width: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Cetak Resep</button>
    </div>
    <main class="sheet">
        <header class="header">
            <img src="<?= base_url('img/zaffarma-login-logo.jpg') ?>" alt="Zaffarma">
            <h1>APOTEK ZAF FARMA</h1>
        </header>

        <div class="title">RESEP</div>
        <table class="patient">
            <tr>
                <td class="label">Tanggal</td><td class="separator">:</td>
                <td><?= htmlspecialchars($visit_date, ENT_QUOTES, 'UTF-8') ?></td>
                <td class="label">No. RM</td><td class="separator">:</td>
                <td><?= htmlspecialchars($row->trans_patient_code ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td class="label">Nama Pasien</td><td class="separator">:</td>
                <td><?= htmlspecialchars($row->patient_name ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td class="label">Umur</td><td class="separator">:</td>
                <td><?= $age !== '' ? intval($age) . ' tahun' : '-' ?></td>
            </tr>
            <tr>
                <td class="label">NIK</td><td class="separator">:</td>
                <td colspan="4"><?= htmlspecialchars($row->patient_nik ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>

        <div class="rx">R/</div>

        <?php if (!empty($obat)): ?>
            <div class="section">Obat</div>
            <table class="medicine">
                <thead>
                    <tr><th width="30">No</th><th>Nama Obat</th><th width="55">Jumlah</th><th width="115">Dosis</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($obat as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item->trans_obat_name ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= intval($item->trans_obat_qty) ?> <?= htmlspecialchars($item->trans_obat_satuan ?: '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item->trans_obat_dosis ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php foreach ($racikan as $index => $mix): ?>
            <div class="section">
                Racikan <?= $index + 1 ?>: <?= htmlspecialchars($mix->pulv_name ?: '', ENT_QUOTES, 'UTF-8') ?>
                — <?= intval($mix->pulv_qty) ?> bungkus
                (<?= htmlspecialchars($mix->pulv_dosis ?: '-', ENT_QUOTES, 'UTF-8') ?>)
            </div>
            <table class="medicine">
                <thead>
                    <tr><th width="30">No</th><th>Nama Obat</th><th width="55">Jumlah</th><th width="115">Dosis</th></tr>
                </thead>
                <tbody>
                    <?php
                    $items = isset($pulv_items[$mix->id_pulv]) ? $pulv_items[$mix->id_pulv] : array();
                    foreach ($items as $item_index => $item):
                    ?>
                    <tr>
                        <td><?= $item_index + 1 ?></td>
                        <td><?= htmlspecialchars($item->trans_obat_name ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= intval($item->trans_obat_qty) ?> <?= htmlspecialchars($item->trans_obat_satuan ?: '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item->trans_obat_dosis ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!empty($mix->pulv_notes)): ?>
                    <tr><td colspan="4"><strong>Catatan:</strong> <?= nl2br(htmlspecialchars($mix->pulv_notes, ENT_QUOTES, 'UTF-8')) ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <?php if (empty($obat) && empty($racikan)): ?>
            <p>Belum ada obat yang diresepkan untuk kunjungan ini.</p>
        <?php endif; ?>

        <div class="signature">
            <div>Dokter pemeriksa,</div>
            <div class="space"></div>
            <strong><?= htmlspecialchars($row->doct_name ?: ' ', ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
    </main>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
