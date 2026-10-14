<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak SKS</title>
    <style>
        * { box-sizing: border-box; }
        body {
            color: #222;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.25;
            margin: 0;
            min-height: 277mm;
            padding: 0;
            position: relative;
        }
        .corner-accent {
            height: 42mm;
            left: -2mm;
            pointer-events: none;
            position: fixed;
            top: 0;
            width: 60mm;
            z-index: 0;
        }
        .print-controls {
            position: fixed;
            right: 12px;
            top: 12px;
            z-index: 2;
        }
        .page-content { padding: 24mm 25.4mm 18mm; position: relative; z-index: 1; }
        .kop { text-align: center; }
        .kop-instansi { font-size: 14pt; font-weight: 700; text-decoration: underline; }
        .kop-dokter, .kop-sip { font-size: 11pt; font-weight: 700; margin-top: 1px; }
        .kop-alamat { font-size: 12pt; margin-top: 1px; }
        .kop hr { border: 0; border-top: 1px solid #a9ad94; margin: 8px 0 0; }
        .judul { margin: 8pt 0 18pt; text-align: center; }
        .judul h1 {
            font-size: 12pt;
            margin: 0;
            font-weight: 700;
        }
        .nomor { font-size: 12pt; margin: 2px 0 0; }
        .pembuka { margin: 0 0 5pt; }
        .data { border-collapse: collapse; margin: 0; width: 100%; }
        .data td { padding: 1pt 0; vertical-align: top; }
        .data .label { padding-left: 5mm; width: 29mm; }
        .data .label-gender { white-space: nowrap; width: 36mm; }
        .data .pemisah { padding-left: 0; padding-right: 3px; width: 4mm; }
        .narasi { margin: 12pt 0 6pt; }
        .tanggal-istirahat { margin: 0 0 6pt; }
        .penutup { margin: 0; }
        .ttd { margin: 31px 0 0 auto; page-break-inside: avoid; text-align: center; width: 55mm; }
        .ttd-tempat, .ttd-peran, .ttd-nama, .ttd-spesialis, .ttd-sip { margin: 0; text-align: center; }
        .ttd-qr { margin: 0 auto 2mm; text-align: center; }
        .ttd-qr img { display: block; height: 24mm; margin: 0 auto; width: 24mm; }
        .ttd-stempel { color: #55789b; font-size: 15px; font-weight: 700; line-height: 1.05; margin: 0; text-align: center; }
        .ttd-spesialis { color: #55789b; font-size: 12px; }
        .ttd-nama { font-weight: 700; margin-top: 3px; }
        .ttd-sip { font-size: 12pt; }
        .btn-print {
            background: #222;
            border: 0;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            margin: 0 6px 12px 0;
            padding: 7px 15px;
        }
        @page { size: A4 portrait; margin: 0; }
        @media print {
            body { min-height: 0; padding: 0; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .corner-accent { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            .kop, .judul, .data, .ttd { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <svg class="corner-accent" viewBox="0 0 180 136" aria-hidden="true">
        <polygon points="0,0 180,0 0,136" fill="#BDD7EE"></polygon>
    </svg>
    <div class="print-controls no-print">
        <button class="btn-print" onclick="window.print()">Print / PDF</button>
        <button class="btn-print" onclick="window.close()">Tutup</button>
    </div>

    <div class="page-content">

    <?php
    $tanggalNumerik = function ($date) {
        return !empty($date) && strtotime($date) !== false ? date('j/n/Y', strtotime($date)) : '-';
    };
    $bulanIndo = array(
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    );
    $formatTanggal = function ($date) use ($bulanIndo) {
        if (empty($date) || strtotime($date) === false) {
            return '-';
        }
        $timestamp = strtotime($date);
        return date('j', $timestamp) . ' ' . $bulanIndo[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    };
    $angkaHari = array(
        1 => 'Satu', 2 => 'Dua', 3 => 'Tiga', 4 => 'Empat', 5 => 'Lima',
        6 => 'Enam', 7 => 'Tujuh', 8 => 'Delapan', 9 => 'Sembilan',
        10 => 'Sepuluh', 11 => 'Sebelas', 12 => 'Dua Belas',
        13 => 'Tiga Belas', 14 => 'Empat Belas', 15 => 'Lima Belas',
        16 => 'Enam Belas', 17 => 'Tujuh Belas', 18 => 'Delapan Belas',
        19 => 'Sembilan Belas', 20 => 'Dua Puluh', 21 => 'Dua Puluh Satu',
        22 => 'Dua Puluh Dua', 23 => 'Dua Puluh Tiga', 24 => 'Dua Puluh Empat',
        25 => 'Dua Puluh Lima', 26 => 'Dua Puluh Enam', 27 => 'Dua Puluh Tujuh',
        28 => 'Dua Puluh Delapan', 29 => 'Dua Puluh Sembilan',
        30 => 'Tiga Puluh', 31 => 'Tiga Puluh Satu',
    );
    $lama = 0;
    if (!empty($row->datefrom) && !empty($row->dateto)) {
        $lama = max(0, (int) floor((strtotime($row->dateto) - strtotime($row->datefrom)) / 86400) + 1);
    }
    $doctorName = !empty($row->fullname) ? trim($row->fullname) : '';
    $doctorLabel = $doctorName !== '' && stripos($doctorName, 'dr.') !== 0
        ? 'dr. ' . $doctorName
        : ($doctorName !== '' ? $doctorName : 'Dokter Pemeriksa');
    ?>

    <div class="kop">
        <div class="kop-instansi">PRAKTEK DOKTER UMUM</div>
        <div class="kop-dokter"><?= htmlspecialchars($doctorLabel, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="kop-sip">No SIP : <?= !empty($row->nip) ? htmlspecialchars($row->nip, ENT_QUOTES, 'UTF-8') : '-' ?></div>
        <div class="kop-alamat">
            Jl. Trans Sulawesi, Ds. Labota<br>
            Kec. Bahodopi, Kab. Morowali, Sulawesi Tengah<br>
            No. Telepon: 085609487216
        </div>
        <hr>
    </div>

    <div class="judul">
        <h1>SURAT KETERANGAN SAKIT</h1>
        <p class="nomor">Nomor: <?= htmlspecialchars($row->docnumb, ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <p class="pembuka">Dengan ini menerangkan bahwa berdasarkan hasil pemeriksaan yang telah dilakukan kepada pasien:</p>
    <table class="data">
        <tr>
            <td class="label">Nama</td><td class="pemisah">:</td>
            <td><?= htmlspecialchars($row->patient_name, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr>
            <td class="label label-gender">Jenis Kelamin</td><td class="pemisah">:</td>
            <td><?= $row->gender === 'L' ? 'Laki-laki' : ($row->gender === 'P' ? 'Perempuan' : '-') ?></td>
        </tr>
        <tr>
            <td class="label">Umur</td><td class="pemisah">:</td>
            <td><?= !empty($row->age) ? htmlspecialchars($row->age, ENT_QUOTES, 'UTF-8') . ' Tahun' : '-' ?></td>
        </tr>
        <tr>
            <td class="label">No. ID Card</td><td class="pemisah">:</td>
            <td><?= !empty($row->patient_nik_for_card) ? htmlspecialchars($row->patient_nik_for_card, ENT_QUOTES, 'UTF-8') : '-' ?></td>
        </tr>
        <tr>
            <td class="label">Pekerjaan</td><td class="pemisah">:</td>
            <td><?= !empty($row->patient_job) ? htmlspecialchars($row->patient_job, ENT_QUOTES, 'UTF-8') : '-' ?></td>
        </tr>
        <tr>
            <td class="label">Alamat</td><td class="pemisah">:</td>
            <td><?= !empty($row->alamat) ? nl2br(htmlspecialchars($row->alamat, ENT_QUOTES, 'UTF-8')) : '-' ?></td>
        </tr>
        <tr>
            <td class="label">Diagnosa</td><td class="pemisah">:</td>
            <td><?= !empty($row->diagnosa) ? nl2br(htmlspecialchars($row->diagnosa, ENT_QUOTES, 'UTF-8')) : '-' ?></td>
        </tr>
        <tr>
            <td class="label">Terapi</td><td class="pemisah">:</td>
            <td><?= !empty($row->terapi) ? nl2br(htmlspecialchars($row->terapi, ENT_QUOTES, 'UTF-8')) : '-' ?></td>
        </tr>
    </table>

    <p class="narasi">
        Diberikan istirahat sakit selama <strong><?= $lama ?> (<?= isset($angkaHari[$lama]) ? $angkaHari[$lama] : $lama ?>) hari</strong>
        terhitung mulai tanggal
    </p>
    <p class="tanggal-istirahat">
        <?= $tanggalNumerik($row->datefrom) ?> s.d tanggal <?= $tanggalNumerik($row->dateto) ?>
    </p>
    <p class="penutup">Demikian surat keterangan ini diberikan untuk diketahui dan dipergunakan seperlunya.</p>

    <div class="ttd">
        <p class="ttd-tempat">Labota, <?= $formatTanggal(!empty($row->docdate) ? $row->docdate : date('Y-m-d')) ?></p>
        <p class="ttd-peran">Dokter Pemeriksa</p>
        <p class="ttd-nama"><?= htmlspecialchars($doctorLabel, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="ttd-qr">
            <img src="<?= htmlspecialchars($qrcode, ENT_QUOTES, 'UTF-8') ?>" alt="QR Code verifikasi SKS">
        </div>
        <?php if (!empty($row->nip)): ?>
            <p class="ttd-sip">SIP : <?= htmlspecialchars($row->nip, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
    </div>
    <script>
        var sksDocumentTitle = document.title;
        window.addEventListener('beforeprint', function () {
            document.title = '';
        });
        window.addEventListener('afterprint', function () {
            document.title = sksDocumentTitle;
        });
    </script>
</body>
</html>
