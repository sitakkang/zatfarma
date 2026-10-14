var tabel_history_medical;

$(document).ready(function () {
    tabel_history_medical = $('#tabel_history_medical').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        data: [],
        columns: [
            { data: '0' },
            { data: '1' },
            { data: '2' },
            { data: '3' },
            { data: '4' },
            { data: '5' },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '<button type="button" class="ds-btn-action ds-btn-green history-open-btn" data-id="' +
                        row.DT_RowId + '" style="padding:5px 12px;"><i class="fa fa-notes-medical"></i> Riwayat</button>';
                }
            }
        ],
        language: {
            processing: 'Memuat data...',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Belum ada data',
            zeroRecords: 'Pasien tidak ditemukan',
            emptyTable: 'Masukkan kode pasien, nomor KTP, atau nama lalu klik Cari',
            paginate: { previous: '&laquo;', next: '&raquo;' }
        },
        order: [[2, 'asc']],
        autoWidth: false
    });

    function searchPatients() {
        var code = $('#history_code').val().trim();
        var ktp = $('#history_ktp').val().trim();
        var name = $('#history_name').val().trim();
        if (code === '' && ktp === '' && name === '') {
            if (typeof notifNo === 'function') {
                notifNo('Masukkan kode pasien, nomor KTP, atau nama pasien.');
            } else {
                window.alert('Masukkan kode pasien, nomor KTP, atau nama pasien.');
            }
            return;
        }

        $.ajax({
            url: site_url + 'history_medical/table',
            type: 'GET',
            dataType: 'json',
            data: { code: code, ktp: ktp, name: name },
            success: function (response) {
                tabel_history_medical.clear().rows.add(response.data || []).draw();
            },
            error: function () {
                if (typeof notifNo === 'function') {
                    notifNo('Pencarian pasien gagal. Silakan coba kembali.');
                } else {
                    window.alert('Pencarian pasien gagal. Silakan coba kembali.');
                }
            }
        });
    }

    $('#history_search_btn').on('click', function () {
        searchPatients();
    });

    $('#history_search_form input').on('keypress', function (event) {
        if (event.which === 13) {
            event.preventDefault();
            searchPatients();
        }
    });

    $('#history_reset_btn').on('click', function () {
        $('#history_search_form')[0].reset();
        tabel_history_medical.clear().draw();
    });

    $(document).on('click', '.history-open-btn', function () {
        var patientId = $(this).data('id');
        $('#MyModalTitle').html('<i class="fa fa-notes-medical"></i> Riwayat Rekam Medis Pasien');
        $('#MyModalContent').html('<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin"></i> Memuat riwayat...</div>');
        $('#MyModalFooter').html('');
        $('.modal-dialog').removeClass('modal-sm modal-md').addClass('ds-modal modal-lg');
        $('#MyModal').modal('show');

        $.ajax({
            url: site_url + 'history_medical/patient_history',
            type: 'GET',
            data: { id: patientId },
            success: function (html) {
                $('#MyModalContent').html(html);
                var $visitsTable = $('#history_visits_table');
                if ($visitsTable.length) {
                    $visitsTable.DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50], [10, 25, 50]],
                        paging: true,
                        searching: true,
                        ordering: false,
                        info: true,
                        autoWidth: false,
                        language: {
                            processing: 'Memuat data...',
                            search: 'Cari:',
                            lengthMenu: 'Tampilkan _MENU_ kunjungan',
                            info: 'Menampilkan _START_–_END_ dari _TOTAL_ kunjungan',
                            infoEmpty: 'Belum ada riwayat kunjungan',
                            zeroRecords: 'Tidak ada kunjungan yang cocok',
                            emptyTable: 'Belum ada riwayat kunjungan',
                            paginate: { previous: '&laquo; Sebelumnya', next: 'Berikutnya &raquo;' }
                        }
                    });
                    $visitsTable.DataTable().columns.adjust();
                }
            },
            error: function () {
                $('#MyModalContent').html('<div class="alert alert-danger">Riwayat pasien gagal dimuat. Silakan coba kembali.</div>');
            }
        });
    });

    $('#MyModal').on('hidden.bs.modal', function () {
        $('.modal-dialog').removeClass('modal-lg');
    });
});
