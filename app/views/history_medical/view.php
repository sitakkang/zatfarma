<div class="ds-page-wrap">
    <div class="ds-page-header">
        <div class="ds-page-header-start">
            <div class="ds-page-header-icon"><i class="fa fa-notes-medical"></i></div>
            <div>
                <h5 class="ds-page-header-title">Riwayat Rekam Medis</h5>
                <p class="ds-page-header-desc">Cari pasien untuk melihat riwayat medis per kunjungan</p>
            </div>
        </div>
    </div>

    <div class="ds-card" style="margin-bottom:16px;">
        <div class="ds-card-bd" style="padding:14px 20px;">
            <form id="history_search_form" onsubmit="return false;">
                <div class="row" style="margin:0 -6px;align-items:flex-end;">
                    <div class="col-md-3" style="padding:0 6px;">
                        <div class="ds-form-group" style="margin-bottom:0;">
                            <label style="font-size:12px;margin-bottom:4px;">KODE PASIEN / NO. RM</label>
                            <input type="text" id="history_code" class="form-control" maxlength="25" placeholder="Cari kode pasien" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3" style="padding:0 6px;">
                        <div class="ds-form-group" style="margin-bottom:0;">
                            <label style="font-size:12px;margin-bottom:4px;">NOMOR KTP</label>
                            <input type="text" id="history_ktp" class="form-control" maxlength="20" placeholder="Cari nomor KTP" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3" style="padding:0 6px;">
                        <div class="ds-form-group" style="margin-bottom:0;">
                            <label style="font-size:12px;margin-bottom:4px;">NAMA PASIEN</label>
                            <input type="text" id="history_name" class="form-control" maxlength="100" placeholder="Cari nama pasien" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3" style="padding:0 6px;display:flex;gap:6px;">
                        <button class="ds-btn-action ds-btn-green" id="history_search_btn" style="padding:7px 22px;">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        <button class="ds-btn-action" id="history_reset_btn" type="button" style="padding:7px 16px;">
                            <i class="fa fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card-bd" style="padding:18px 20px;">
            <div class="ds-wide-table-wrap" style="margin:0;">
                <table id="tabel_history_medical" class="ds-table" style="margin-top:0;">
                    <thead>
                        <tr>
                            <th width="45">No</th>
                            <th width="130">Kode Pasien</th>
                            <th>Nama Pasien</th>
                            <th width="170">Nomor KTP</th>
                            <th width="110">NIK</th>
                            <th width="160">Perusahaan</th>
                            <th width="120" class="text-center">Riwayat</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
