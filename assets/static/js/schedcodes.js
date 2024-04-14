var dt;

function tr(translate) {
    var result = false;
    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/jstrans.php',
        async: false,
        data: {
            translate: translate
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            result = mydata.translated;
        }
    });
    return result;
}

function delCode(code) {
    var trans = tr('SCHEDWILLDEL {{' + code + '}}');
    Swal.fire({
        text: trans,
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: TRAN_YES,
        cancelButtonText: TRAN_NO,
        customClass: {
            confirmButton: "btn fw-bold btn-danger",
            cancelButton: "btn fw-bold btn-active-light-primary"
        }
    }).then(function (result) {
        if (result.value) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/removesched.php',
                data: {
                    idet: code,
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        dt.ajax.reload();
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
}

function editCode(code) {
    $.ajax({
        url: HOST_URL + '/forms/loadsched.php',
        data: "id=" + code,
        dataType: 'json',
        success: function (data) {
            $('#schedid').val(code);
            $('#desc').val(data['DESCRIPTION']);
            $("#edit_window").modal("show");
        }
    });
}

var AddForm = $('#add_form').validate({
    rules: {
        code: {
            required: true,
            remote: HOST_URL + "/validation/checknewschedcode.php",
            maxlength: 10
        },
        desc: {
            required: true,
        },
    },
    messages: {
        code: {
            required: TRAN_NOTBEEMPTY,
            maxlength: TRAN_SCHDECODETOLONG
        },
        desc: {
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
        var dataString = $('#add_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/addschedcode.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    AddForm.resetForm();
                    dt.ajax.reload();
                    $('#add_window').modal('hide');
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

var EditForm = $('#edit_form').validate({
    rules: {
        desc: {
            required: true,
        },
    },
    messages: {
        desc: {
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
            url: HOST_URL + '/forms/editsched.php',
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
        dt = $("#sched_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/sched-table.php",
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
                    data: 'CODE'
                },
                {
                    data: 'DESCRIPTION'
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
                                    <a href="javascript:;" onclick="editCode('` + row.CODE + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDIT + `"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="delCode('` + row.CODE + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVESCHEDCODE + `"><i class="bi bi-x-square"></i></a>
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

    const element2 = document.getElementById('add_window');
    const modal2 = new bootstrap.Modal(element2);

    var initAddModalButtons = function () {
        const cancelButton2 = element2.querySelector('[data-kt-add-modal-action="cancel"]');
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
                    modal2.hide();
                }
            });
        });
        const closeButton2 = element2.querySelector('[data-kt-add-modal-action="close"]');
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
                    modal2.hide();

                }
            });
        });
    }

    return {
        init: function () {
            initDatatable();
            initEditModalButtons();
            initAddModalButtons();
        }
    }
}();

KTDatatablesServerSide.init();