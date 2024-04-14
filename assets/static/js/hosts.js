var dt;

function editHost(host) {
    $.ajax({
        url: HOST_URL + '/forms/loadhost.php',
        data: "id=" + host,
        dataType: 'json',
        success: function (data) {
            $('#hostid').val(host);
            $('#hostname').val(data['NAME']);
            $('#shortname').val(data['SHORT_NAME']);
            $('#descr').val(data['DESCRIPTION']);
            $('#defuser').val(data['DEFAULT_NAME']);
            $('#ipadd').val(data['IPV4_ADDRESS']);
            $('#repedit').val(data['REPORT_EDITOR_PATH']);
            $('#webbrow').val(data['BROWSER_PATH']);
            $("#edit_window").modal("show");
        }
    });
}

var EditForm = $('#edit_form').validate({
    rules: {
        shortname: {
            required: true,
        },
        descr: {
            required: true,
        },
        defuser: {
            required: true,
        },
        ipadd: {
            required: true,
        },
    },
    messages: {
        shortname: {
            required: TRAN_NOTBEEMPTY,
        },
        descr: {
            required: TRAN_NOTBEEMPTY,
        },
        defuser: {
            required: TRAN_NOTBEEMPTY,
        },
        ipadd: {
            required: TRAN_NOTBEEMPTY,
        },
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('parsley-error');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function () {
        var dataString = $('#edit_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/edithost.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    EditForm.resetForm();
                    dt.ajax.reload();
                    $('#edit_window').modal('hide');
                } else {
                    Swal.fire({
                        text: TRAN_BUG,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            }
        });
    }
});

var KTDatatablesServerSide = function () {
    var initDatatable = function () {
        dt = $("#host_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/hosts-table.php",
            language: {
                "emptyTable": TRAN_TABLENODATA,
                "info": TRAN_TABLESHOWS + " _START_ " + TRAN_TABLETO + " _END_ " + TRAN_TABLETOTAL + " _TOTAL_ " + TRAN_TABLEROWS,
                "infoEmpty": TRAN_TABLESHOWS + " 0 " + TRAN_TABLETO + " 0 " + TRAN_TABLETOTAL + " 0 " + TRAN_TABLEROWS,
                "infoFiltered": "(" + TRAN_TABLEFILTERED + " _MAX_ " + TRAN_TABLEROWS + ")",
                "infoThousands": " ",
                "lengthMenu": TRAN_TABLESHOW+ " _MENU_ " +TRAN_TABLEROWS,
                "loadingRecords": TRAN_TABLELOADING,
                "processing": TRAN_TABLEWORKING,
                "search": TRAN_TABLESEARCH,
                "zeroRecords": TRAN_TABLENORESULTS,
                "thousands": " ",
                "paginate": {
                    "first": TRAN_TABLEFIRST,
                    "last": TRAN_TABLELAST,
                    "next": TRAN_TABLENEXT,
                    "previous": TRAN_TABLEPREV
                },
                "select": {
                    "rows": {
                        "1": "1 " + TRAN_TABLESELECTED,
                        "_": "%d " + TRAN_TABLESELECTED
                    }
                },
                "aria": {
                    "sortAscending": ": " + TRAN_TABLENSORTRISE,
                    "sortDescending": ": " + TRAN_TABLENSORTFALL
                }
            },
            columns: [
                {
                    data: 'NAME'
                },
                {
                    data: 'DESCRIPTION'
                },
                {
                    data: 'IPV4_ADDRESS'
                },
                {
                    data: 'DEFAULT_NAME'
                },
                {
                    data: 'SHORT_NAME'
                },
                {
                    data: null
                },
            ],
            columnDefs: [
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                        <div class="btn-group mb-3" role="group">
                                    <a href="javascript:;" onclick="editHost('` + row.NAME + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDIT + `"><i class="bi bi-pencil"></i></a>
                                </div>
                        `;
                    }
                },
            ],
        });

    }

    const element1 = document.getElementById('edit_window');
    const modal1 = new bootstrap.Modal(element1);

    var initEditModalButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-edit-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSETHEWINDOW,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: TRAN_YES,
                cancelButtonText: TRAN_NO,
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    modal1.hide();
                }
            });
        });
        const closeButton2 = element1.querySelector('[data-kt-edit-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSETHEWINDOW,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: TRAN_YES,
                cancelButtonText: TRAN_NO,
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    modal1.hide();

                }
            });
        });
    }    

    return {
        init: function () {
            initDatatable();
            initEditModalButtons();
        }
    }
}();

KTDatatablesServerSide.init();