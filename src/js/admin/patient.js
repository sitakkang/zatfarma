var tabel_patient;
var url_ctrl = site_url+"patient/"; 
	
$(document).ready(function () {

    // --- Init Datepicker (xdsoft datetimepicker) ---
    $('.datepicker').datetimepicker({
        datepicker: true,
        timepicker: false,
        format: 'd/m/Y',
        closeOnDateSelect: true,
        scrollMonth: false,
        scrollInput: false,
    });

    $(".autocomplete").chosen(); 

    // --- DataTable (tanpa ajax — mulai kosong) ---
    tabel_patient = $('#tabel_patient').DataTable({
        processing: true,
        serverSide: false,
        scrollY: "500px",
        deferRender: true,
        scrollX: true,
        scrollCollapse: true,
        data: [],  // mulai tanpa data
        columns: [
            { data: '0', width: '40px' },
            { data: '1' },
            { data: '2' },
            { data: 'jk', className: 'text-center',
                render: function (data) {
                    if (data === 'L') return '<span class="badge badge-info">Laki-laki</span>';
                    if (data === 'P') return '<span class="badge badge-danger">Perempuan</span>';
                    return '<span class="badge badge-secondary">-</span>';
                }
            },
            { data: '3', className: 'text-center' },
            { data: '4' },
            { data: '5' },
            { data: '6', className: 'text-center' },
            { data: '7', className: 'text-center' },
            {
                data: null,
                width: '120px',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '<div class="ds-act-group">' +
                               '<button class="ds-act-btn ds-act-view view-row-btn" data-id="' + row.DT_RowId + '" title="Lihat Detail">' +
                                   '<i class="fa fa-eye"></i>' +
                               '</button>' +
                               '<button class="ds-act-btn ds-act-edit edit-row-btn" data-id="' + row.DT_RowId + '" title="Edit Data">' +
                                   '<i class="fa fa-pen"></i>' +
                               '</button>' +
                               '<button class="ds-act-btn ds-act-delete delete-row-btn" data-id="' + row.DT_RowId + '" title="Hapus">' +
                                   '<i class="fa fa-trash"></i>' +
                               '</button>' +
                           '</div>';
                }
            },
            {
                data: null,
                width: '130px',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '<button class="ds-btn-daftar sks-row-btn" data-id="' + row.DT_RowId + '" title="Tambah SKS">' +
                               '<i class="fa fa-file-text"></i> Tambah SKS' +
                           '</button>';
                }
            },
            {
                data: null,
                width: '130px',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '<button class="ds-btn-daftar skbs-row-btn" data-id="' + row.DT_RowId + '" title="Tambah SKBS">' +
                               '<i class="fa fa-heartbeat"></i> Tambah SKBS' +
                           '</button>';
                }
            },
            {
                data: null,
                width: '130px',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '<button class="ds-btn-daftar skmb-row-btn" data-id="' + row.DT_RowId + '" title="Tambah SKMB">' +
                               '<i class="fa fa-ambulance"></i> Tambah SKMB' +
                           '</button>';
                }
            },
            {
                data: null,
                width: '80px',
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return '<button class="ds-btn-daftar daftar-row-btn" data-id="' + row.DT_RowId + '" title="Daftarkan Pasien">' +
                               '<i class="fa fa-clipboard-list"></i> Daftar' +
                           '</button>';
                }
            },
        ],
        language: {
            processing: 'Memuat data...',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_ – _END_ dari _TOTAL_ data',
            infoEmpty: 'Belum ada data',
            zeroRecords: 'Belum ada data',
            emptyTable: 'Silakan lakukan pencarian terlebih dahulu',
            paginate: { previous: '&laquo;', next: '&raquo;' },
        },
        order: [[0, 'asc']],
        responsive: true,
        autoWidth: false,
    });

    // --- Fungsi pencarian ---
    function doSearch() {
        var rm   = $('#search_rm').val().trim();
        var ktp  = $('#search_ktp').val().trim();
        var nama = $('#search_nama').val().trim();

        tabel_patient.ajax.url(site_url + 'patient/table?rm=' + encodeURIComponent(rm) +
            '&ktp=' + encodeURIComponent(ktp) +
            '&nama=' + encodeURIComponent(nama)).load();
    }

    // --- Tombol Cari ---
    $('#search_btn').on('click', function () {
        doSearch();
    });

    // --- Enter pada field search ---
    $('#search_rm, #search_ktp, #search_nama').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            doSearch();
        }
    });

    // --- Helper: show modal with ds-modal class ---
    function showDsModal(title, contentHtml, footerHtml, size) {
        $('#MyModalTitle').html(title);
        $('#MyModalContent').html(contentHtml);
        $('#MyModalFooter').html(footerHtml);
        // Add ds-modal class + modal size
        $('.modal-dialog').addClass('ds-modal');
        if (size) {
            $('.modal-dialog').addClass(size);
        }
        // Init datepicker on modal content (xdsoft datetimepicker)
        // Simpan value sebelum init agar tidak hilang
        $('#MyModalContent .datepicker').each(function () {
            var val = $(this).val();
            $(this).datetimepicker({
                datepicker: true,
                timepicker: false,
                format: 'd/m/Y',
                closeOnDateSelect: true,
                scrollMonth: false,
                scrollInput: false,
            });
            if (val) $(this).val(val);
        });
        $('#MyModal').modal('show');
        $('.autocomplete').chosen();
    }

    // --- Tambah Pasien ---
    $('#add_btn').on('click', function () {
        $.get(site_url + 'patient/add', function (html) {
            showDsModal(
                '<i class="fa fa-plus-circle"></i> Tambah Pasien',
                html,
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                '<button type="button" class="ds-btn-action ds-btn-green" id="save_add_btn" style="padding:8px 22px">Simpan</button>',
                'modal-md'
            );
        });
    });

    $(document).on('click', '#save_add_btn', function () {
        var payload = {
            patient_name:        $('#patient_name').val(),
            patient_gender:      $('#patient_gender').val(),
            patient_nik:         $('#patient_nik').val(),
            patient_company:     $('#patient_company').val(),
            patient_job:  $('#patient_job').val(),
            patient_ktp:         $('#patient_ktp').val(),
            patient_bod:         $('#patient_bod').val(),
            patient_phone:       $('#patient_phone').val(),
            patient_address:     $('#patient_address').val(),
        };
        if($('#patient_name').val()==''){
			notifNo("Silahkan isi nama pasien");
            return false;
		}
        if($('#patient_gender').val()==''){
			notifNo("Silahkan pilih jenis kelamin");
            return false;
		}
        if($('#patient_bod').val()==''){
			notifNo("Silahkan isi tanggal lahir");
            return false;
		}
        if($('#patient_phone').val()==''){
			notifNo("Silahkan isi nomor telepon");
            return false;
		}
        if($('#patient_address').val()==''){
			notifNo("Silahkan isi alamat");
            return false;
		}
        $.post(site_url + 'patient/act_add', payload, function (res) {
            if (res.status == 1) {
                notifNo(res.notif);
            } else if (res.status == 2) {
                $('#MyModal').modal('hide');
                notifYesAuto(res.notif);
                if (res.patient_code) {
                    $('#search_rm').val(res.patient_code);
                    $('#search_ktp').val('');
                    $('#search_nama').val('');
                }
                doSearch();
            }
        }, 'json');
    });

    // --- Tambah SKS dari data pasien ---
    // Memakai form tambah SKS yang sudah ada (sks/add) dengan data pasien terisi otomatis
    $('#tabel_patient tbody').on('click', '.sks-row-btn', function () {
        var id = $(this).data('id');
        $.get(site_url + 'sks/add_from_patient/' + id, function (html) {
            showDsModal(
                '<i class="fa fa-file-text"></i> Tambah SKS',
                html,
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                '<button type="button" class="ds-btn-action ds-btn-green" id="save_sks_btn" style="padding:8px 22px">Simpan</button>',
                'modal-md'
            );
        });
    });

    $(document).on('click', '#save_sks_btn', function () {
        var payload = {
            docnumb:      $('#docnumb').val(),
            patient_id:   $('#patient_id').val(),
            patient_name: $('#patient_name').val(),
            company_name: $('#company_name').val(),
            age:          $('#age').val(),
            gender:       $('#gender').val(),
            alamat:       $('#alamat').val(),
            diagnosa:     $('#diagnosa').val(),
            terapi:       $('#terapi').val(),
            datefrom:     $('#datefrom').val(),
            dateto:       $('#dateto').val(),
            docdate:      $('#docdate').val(),
            doctby:       $('#doctby').val(),
        };
        if (!payload.docnumb || !payload.docnumb.trim()) {
            notifNo('Silahkan isi nomor SKS');
            return false;
        }
        if (/^\s*0+\s*\//.test(payload.docnumb)) {
            notifNo('Nomor SKS tidak boleh diawali angka 000');
            return false;
        }
        if ($('#patient_name').val() == '') {
            notifNo("Silahkan isi nama pasien");
            return false;
        }
        if ($('#age').val() == '') {
            notifNo("Silahkan isi umur pasien");
            return false;
        }
        if ($('#gender').val() == '') {
            notifNo("Silahkan pilih jenis kelamin pasien");
            return false;
        }
        if ($('#doctby').val() == '') {
            notifNo("Silahkan pilih dokter");
            return false;
        }
        if ($('#diagnosa').val() == '') {
            notifNo("Silahkan isi diagnosa");
            return false;
        }
        if ($('#alamat').val() == '') {
            notifNo("Silahkan isi alamat pasien");
            return false;
        }
        if ($('#datefrom').val() == '') {
            notifNo("Silahkan isi tanggal mulai");
            return false;
        }
        if ($('#dateto').val() == '') {
            notifNo("Silahkan isi tanggal selesai");
            return false;
        }
        $.post(site_url + 'sks/act_add', payload, function (res) {
            if (res.status == 1) {
                notifNo(res.notif);
            } else if (res.status == 2) {
                $('#MyModal').modal('hide');
                notifYesAuto(res.notif);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
            console.error('Gagal menyimpan SKS:', textStatus, errorThrown);
            notifNo('SKS gagal disimpan karena terjadi kesalahan pada server. Silakan coba kembali atau hubungi administrator.');
        });

    });

    $('#tabel_patient tbody').on('click', '.skbs-row-btn', function () {
        var id = $(this).data('id');
        $.get(url_ctrl + 'add_skbs_from_patient/' + id, function (html) {
            showDsModal(
                '<i class="fa fa-heartbeat"></i> Tambah SKBS',
                html,
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                '<button type="button" class="ds-btn-action ds-btn-green" id="save_patient_skbs_btn" style="padding:8px 22px">Simpan</button>',
                'modal-lg'
            );
        }).fail(function () {
            notifNo('Form SKBS gagal dimuat. Silakan coba kembali.');
        });
    });

    $(document).on('click', '#save_patient_skbs_btn', function () {
        var payload = {
            patient_id:       $('#skbs_patient_id').val(),
            skbs_doct_id:     $('#skbs_doct_id').val(),
            skbs_docnumb:     $('#skbs_docnumb').val(),
            skbs_result:      $('#skbs_result').val(),
            skbs_desc:        $('#skbs_desc').val(),
            skbs_note:        $('#skbs_note').val(),
            skbs_td:          $('#skbs_td').val(),
            skbs_tb:          $('#skbs_tb').val(),
            skbs_bb:          $('#skbs_bb').val(),
            skbs_bw:          $('#skbs_bw').val(),
            skbs_r:           $('#skbs_r').val(),
            skbs_l:           $('#skbs_l').val(),
            skbs_koreksi_r:   $('#skbs_koreksi_r').val(),
            skbs_koreksi_l:   $('#skbs_koreksi_l').val(),
        };
        if (!payload.skbs_docnumb || !payload.skbs_docnumb.trim()) {
            notifNo('Silakan isi nomor SKBS');
            return false;
        }
        if (/^\s*0+\s*\//.test(payload.skbs_docnumb)) {
            notifNo('Nomor SKBS tidak boleh diawali angka 000');
            return false;
        }
        if (!payload.skbs_doct_id) {
            notifNo('Silakan pilih dokter');
            return false;
        }
        if (!payload.skbs_result) {
            notifNo('Silakan pilih hasil SKBS');
            return false;
        }

        $.post(url_ctrl + 'act_add_skbs', payload, function (res) {
            if (res.status == 1) {
                notifNo(res.notif);
            } else {
                $('#MyModal').modal('hide');
                notifYesAuto(res.notif);
            }
        }, 'json').fail(function () {
            notifNo('SKBS gagal disimpan karena terjadi kesalahan pada server. Silakan coba kembali.');
        });
    });

    $('#tabel_patient tbody').on('click', '.skmb-row-btn', function () {
        var id = $(this).data('id');
        $.get(site_url + 'skmb/add_from_patient/' + id, function (html) {
            showDsModal(
                '<i class="fa fa-ambulance"></i> Tambah SKMB',
                html,
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                '<button type="button" class="ds-btn-action ds-btn-green" id="save_patient_skmb_btn" style="padding:8px 22px">Simpan</button>',
                'modal-md'
            );
        }).fail(function () {
            notifNo('Form SKMB gagal dimuat. Silakan coba kembali.');
        });
    });

    $(document).on('click', '#save_patient_skmb_btn', function () {
        var payload = {
            patient_id:        $('#patient_id').val(),
            docnumb:           $('#docnumb').val(),
            patient_name:      $('#patient_name').val(),
            nik:               $('#nik').val(),
            company_name:      $('#company_name').val(),
            pengantar:         $('#pengantar').val(),
            nik_pengantar:     $('#nik_pengantar').val(),
            company_pengantar: $('#company_pengantar').val(),
            hubungan:          $('#hubungan').val(),
            tgl_datang:        $('#tgl_datang').val(),
            jam:               $('#jam').val(),
            docdate:           $('#docdate').val(),
            doctby:            $('#doctby').val(),
        };
        if (!payload.docnumb || !payload.docnumb.trim() || /^\s*0+\s*\//.test(payload.docnumb)) {
            notifNo('Isi nomor SKMB yang valid (tidak boleh diawali 000)');
            return false;
        }
        if (!payload.pengantar || !payload.pengantar.trim()) {
            notifNo('Silakan isi nama pengantar');
            return false;
        }
        if (!payload.tgl_datang || !payload.jam || !payload.hubungan) {
            notifNo('Silakan lengkapi tanggal, jam, dan hubungan pengantar');
            return false;
        }
        if (!payload.doctby) {
            notifNo('Silakan pilih dokter');
            return false;
        }

        $.post(site_url + 'skmb/act_add', payload, function (res) {
            if (res.status == 1) {
                notifNo(res.notif);
            } else {
                $('#MyModal').modal('hide');
                notifYesAuto(res.notif);
            }
        }, 'json').fail(function () {
            notifNo('SKMB gagal disimpan karena terjadi kesalahan pada server. Silakan coba kembali.');
        });
    });

    // --- Lihat Detail Pasien ---
    $('#tabel_patient tbody').on('click', '.view-row-btn', function () {
        var id = $(this).data('id');
        $.get(site_url + 'patient/detail', { id: id }, function (html) {
            showDsModal(
                '<i class="fa fa-file-text-o"></i> Detail Pasien',
                html,
                '',  /* no footer — close button is in the modal header */
                'modal-md'
            );
        });
    });

    // --- Edit Pasien ---
    $('#tabel_patient tbody').on('click', '.edit-row-btn', function () {
        var id = $(this).data('id');
        $.get(site_url + 'patient/edit', { id: id }, function (html) {
            showDsModal(
                '<i class="fa fa-pencil"></i> Edit Pasien',
                html,
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                '<button type="button" class="ds-btn-action ds-btn-green" id="save_edit_btn" style="padding:8px 22px">Simpan Perubahan</button>',
                'modal-md'
            );
        });
    });

    $(document).on('click', '#save_edit_btn', function () {
        var payload = {
            id:                  $('#edit_id').val(),
            patient_name:        $('#edit_patient_name').val(),
            patient_gender:      $('#edit_patient_gender').val(),
            patient_nik:         $('#edit_patient_nik').val(),
            patient_company:     $('#edit_patient_company').val(),
            patient_job:  $('#edit_patient_job').val(),
            patient_ktp:         $('#edit_patient_ktp').val(),
            patient_bod:         $('#edit_patient_bod').val(),
            patient_phone:       $('#edit_patient_phone').val(),
            patient_address:     $('#edit_patient_address').val(),
        };
        if($('#edit_patient_name').val()==''){
			notifNo("Silahkan isi nama pasien");
            return false;
		}
        if($('#edit_patient_gender').val()==''){
			notifNo("Silahkan pilih jenis kelamin");
            return false;
		}
        if($('#edit_patient_bod').val()==''){
			notifNo("Silahkan isi tanggal lahir");
            return false;
		}
        if($('#edit_patient_phone').val()==''){
			notifNo("Silahkan isi nomor telepon");
            return false;
		}
        if($('#edit_patient_address').val()==''){
			notifNo("Silahkan isi alamat");
            return false;
		}
        $.post(site_url + 'patient/act_edit', payload, function (res) {
            if (res.status == 1) {
                notifNo(res.notif);
            } else if (res.status == 2) {
                $('#MyModal').modal('hide');
                notifYesAuto(res.notif);
                doSearch();
            }
        }, 'json');
    });

    // --- Daftar Pasien ---
    $('#tabel_patient tbody').on('click', '.daftar-row-btn', function () {
        var id = $(this).data('id');
        $.get(site_url + 'patient/daftar', { id: id }, function (html) {
            showDsModal(
                '<i class="fa fa-clipboard-list"></i> Pendaftaran Pasien',
                html,
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                '<button type="button" class="ds-btn-action ds-btn-green" id="save_daftar_btn" style="padding:8px 22px">Simpan Pendaftaran</button>',
                'modal-md'
            );
            // Init datetimepicker with time enabled for the daftar modal
            $('#MyModalContent .datetimepicker').datetimepicker({
                datepicker: true,
                timepicker: true,
                format: 'd/m/Y H:i',
                closeOnDateSelect: false,
                scrollMonth: false,
                scrollInput: false,
            });
        });
    });

    $(document).on('click', '#save_daftar_btn', function () {
        var id = $('#daftar_id').val();
        var trans_doc = $('#trans_doc').val();
        if (trans_doc == '') {
            notifNo('Silakan pilih tanggal dan jam pendaftaran');
            return false;
        }
        $.post(site_url + 'patient/act_daftar', { id: id, trans_doc: trans_doc }, function (res) {
            if (res.status == 1) {
                notifNo(res.notif);
            } else if (res.status == 2) {
                $('#MyModal').modal('hide');
                notifYesAuto(res.notif);
            }
        }, 'json');
    });

    // --- Hapus Pasien ---
    $('#tabel_patient tbody').on('click', '.delete-row-btn', function () {
        var id = $(this).data('id');
        swal({
            title: 'Nonaktifkan Pasien?',
            text: 'Data pasien akan dinonaktifkan (soft delete).',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Nonaktifkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
        }).then(function (result) {
            if (!result.value) return;
            $.post(site_url + 'patient/act_del', { id: id }, function (res) {
                if (res.status == 1) {
                    notifNo(res.notif);
                } else if (res.status == 2) {
                    notifYesAuto(res.notif);
                    doSearch();
                }
            }, 'json');
        });
    });

});
