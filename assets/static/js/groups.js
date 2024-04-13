/*********************************************************************************************************
 *                                        RIVENDELL WEB BROADCAST                                        *
 *    A WEB SYSTEM TO USE WITH RIVENDELL RADIO AUTOMATION: HTTPS://GITHUB.COM/ELVISHARTISAN/RIVENDELL    *
 *              THIS SYSTEM IS NOT CREATED BY THE DEVELOPER OF RIVENDELL RADIO AUTOMATION.               *
 * IT'S CREATED AS AN HELP TOOL ONLINE BY ANDREAS OLSSON AFTER HE FIXED BUGS IN AN OLD SCRIPT CREATED BY *
 *             BRIAN P. MCGLYNN : HTTPS://GITHUB.COM/BPM1992/RIVENDELL/TREE/RDWEB/WEB/RDPHP              *
 *        USE THIS SYSTEM AT YOUR OWN RISK. IT DO DIRECT MODIFICATION ON THE RIVENDELL DATABASE.         *
 *                 YOU CAN NOT HOLD US RESPONISBLE IF SOMETHING HAPPENDS TO YOUR SYSTEM.                 *
 *                   THE DESIGN IS DEVELOP BY SAUGI: HTTPS://GITHUB.COM/ZURAMAI/MAZER                    *
 *                                              MIT LICENSE                                              *
 *                                   COPYRIGHT (C) 2024 ANDREAS OLSSON                                   *
 *             PERMISSION IS HEREBY GRANTED, FREE OF CHARGE, TO ANY PERSON OBTAINING A COPY              *
 *             OF THIS SOFTWARE AND ASSOCIATED DOCUMENTATION FILES (THE "SOFTWARE"), TO DEAL             *
 *             IN THE SOFTWARE WITHOUT RESTRICTION, INCLUDING WITHOUT LIMITATION THE RIGHTS              *
 *               TO USE, COPY, MODIFY, MERGE, PUBLISH, DISTRIBUTE, SUBLICENSE, AND/OR SELL               *
 *                 COPIES OF THE SOFTWARE, AND TO PERMIT PERSONS TO WHOM THE SOFTWARE IS                 *
 *                       FURNISHED TO DO SO, SUBJECT TO THE FOLLOWING CONDITIONS:                        *
 *            THE ABOVE COPYRIGHT NOTICE AND THIS PERMISSION NOTICE SHALL BE INCLUDED IN ALL             *
 *                            COPIES OR SUBSTANTIAL PORTIONS OF THE SOFTWARE.                            *
 *              THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR               *
 *               IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,                *
 *              FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE              *
 *                AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER                 *
 *             LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,             *
 *             OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE             *
 *                                               SOFTWARE.                                               *
 *********************************************************************************************************/
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

function editgroup(name) {
    $.ajax({
        url: HOST_URL + '/forms/loadgroup.php',
        data: "id=" + name,
        dataType: 'json',
        success: function (data) {
            $('#groupid').val(name);
            $('#groupname').val(data['NAME']);
            $('#groupdesc').val(data['DESCRIPTION']);
            $('#gimport').val(data['DEFAULT_TITLE']);
            $('#emailaddresses').val(data['NOTIFY_EMAIL_ADDRESSES']);
            $('#carttype').val(data['DEFAULT_CART_TYPE']);
            $('#cartstart').val(data['DEFAULT_LOW_CART']);
            $('#cartend').val(data['DEFAULT_HIGH_CART']);
            $('#enfcart').val(data['ENFORCE_CART_RANGE']);
            $('#inctraffic').val(data['REPORT_TFC']);
            $('#incmusic').val(data['REPORT_MUS']);
            $('#color').val(data['COLOR']);
            if (data['DEFAULT_CUT_LIFE'] == '-1') {
                $('#cutcreation').prop("disabled", true);
                $("#enddatetime").prop("checked", false);
                $('#cutcreation').val('0');
            } else {
                $('#cutcreation').removeAttr('disabled');
                $("#enddatetime").prop("checked", true);
                $('#cutcreation').val(data['DEFAULT_CUT_LIFE']);
            }
            if (data['CUT_SHELFLIFE'] == '-1') {
                $('#purgedays').prop("disabled", true);
                $('#delempty').prop("disabled", true);
                $("#purge").prop("checked", false);
                $('#purgedays').val('0');
            } else {
                $('#purgedays').removeAttr('disabled');
                $('#delempty').removeAttr('disabled');
                $("#purge").prop("checked", true);
                $('#purgedays').val(data['CUT_SHELFLIFE']);
            }
            if (data['DELETE_EMPTY_CARTS'] == 'N') {
                $("#delempty").prop("checked", false);
            } else {
                $("#delempty").prop("checked", true);
            }
        }
    });

    $.ajax({
        url: HOST_URL + '/forms/loadaudioservice.php',
        data: "id=" + name,
        dataType: 'json',
        success: function (data) {
            var i;
            for (i = 0; i < data.length; i++) {
                $('#activeservice option[value=' + data[i] + ']').attr('selected', true);
            }
        }
    });


    $("#edit_window").modal("show");
}

function renamegroup(name) {

    $.ajax({
        url: HOST_URL + '/forms/loadgroup.php',
        data: "id=" + name,
        dataType: 'json',
        success: function (data) {
            $('#re_groupid').val(name);
            $("#rename_window").modal("show");
        }
    });
}

function delgroup(id) {
    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/checkgroupcarts.php',
        async: false,
        data: {
            name: id
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            var carts = mydata.carts;
            if (fel == "true") {
                var trans = tr('CARTSWILLDELETE {{' + carts + '}} {{' + id + '}}');
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
                            url: HOST_URL + '/forms/removegroup.php',
                            data: {
                                idet: id,
                                remcarts: 1
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
            } else {
                var trans2 = tr('REMOVETHEGROUP {{' + id + '}}');
                Swal.fire({
                    text: trans2,
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
                            url: HOST_URL + '/forms/removegroup.php',
                            data: {
                                idet: id,
                                remcarts: 0
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
        }
    });
}

var RenameForm = $('#rename_form').validate({
    rules: {
        groupname: {
            required: true,
            remote: HOST_URL + "/validation/checknewgroupname.php",
            maxlength: 10
        },
    },
    messages: {
        groupname: {
            required: TRAN_NOTBEEMPTY,
            maxlength: TRAN_GROUPNAMELONG
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
        var dataString = $('#rename_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/renamegroup.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    RenameForm.resetForm();
                    dt.ajax.reload();
                    $('#rename_window').modal('hide');
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

var AddForm = $('#add_form').validate({
    rules: {
        groupname: {
            required: true,
            remote: HOST_URL + "/validation/checknewgroupname.php",
            maxlength: 10
        },
        groupdesc: {
            required: true,
        },
        gimport: {
            required: true,
        },
        cartstart: {
            required: true,
        },
        cartend: {
            required: true,
        },
    },
    messages: {
        groupname: {
            required: TRAN_NOTBEEMPTY,
            maxlength: TRAN_GROUPNAMELONG
        },
        groupdesc: {
            required: TRAN_NOTBEEMPTY
        },
        gimport: {
            required: TRAN_NOTBEEMPTY,
        },
        cartstart: {
            required: TRAN_NOTBEEMPTY,
        },
        cartend: {
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
            url: HOST_URL + '/forms/addgroup.php',
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
        groupdesc: {
            required: true,
        },
        gimport: {
            required: true,
        },
        cartstart: {
            required: true,
        },
        cartend: {
            required: true,
        },
    },
    messages: {
        groupdesc: {
            required: TRAN_NOTBEEMPTY
        },
        gimport: {
            required: TRAN_NOTBEEMPTY,
        },
        cartstart: {
            required: TRAN_NOTBEEMPTY,
        },
        cartend: {
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
            url: HOST_URL + '/forms/editgroup.php',
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

function addZeros(number) {
    var retstring = '' + number;
    var length = 6;
    while (retstring.length < length) {
        if (retstring.length == 5) {
            retstring = '0' + retstring;
        } else if (retstring.length == 4) {
            retstring = '00' + retstring;
        } else if (retstring.length == 3) {
            retstring = '000' + retstring;
        } else if (retstring.length == 2) {
            retstring = '0000' + retstring;
        } else if (retstring.length == 1) {
            retstring = '00000' + retstring;
        }
    }

    return retstring;
}

var KTDatatablesServerSide = function () {
    var initDatatable = function () {
        dt = $("#groups_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/groups-table.php",
            language: {
                "emptyTable": TRAN_TABLENODATA,
                "info": TRAN_TABLESHOWS + " _START_ " + TRAN_TABLETO + " _END_ " + TRAN_TABLETOTAL + " _TOTAL_ " + TRAN_TABLEROWS,
                "infoEmpty": TRAN_TABLESHOWS + " 0 " + TRAN_TABLETO + " 0 " + TRAN_TABLETOTAL + " 0 " + TRAN_TABLEROWS,
                "infoFiltered": "(" + TRAN_TABLEFILTERED + " _MAX_ " + TRAN_TABLEROWS + ")",
                "infoThousands": " ",
                "lengthMenu": TRAN_TABLESHOW + " _MENU_ " + TRAN_TABLEROWS,
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
                    data: 'DEFAULT_LOW_CART'
                },
                {
                    data: 'DEFAULT_HIGH_CART'
                },
                {
                    data: 'ENFORCE_CART_RANGE'
                },
                {
                    data: 'REPORT_MUS'
                },
                {
                    data: 'REPORT_TFC'
                },
                {
                    data: null
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row) {
                        if (row.DEFAULT_CART_TYPE == 1) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="assets/static/images/event/sound.png" /></div> <P style="color:` + row.COLOR + `">` + data + `</p>`;
                        } else if (row.DEFAULT_CART_TYPE == 2) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="assets/static/images/event/settings.png" /></div> <P style="color:` + row.COLOR + `">` + data + `</p>`;
                        }
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (data == 0) {
                            return TRAN_NONBRACKET;
                        } else {
                            return addZeros(data);
                        }
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        if (data == 0) {
                            return TRAN_NONBRACKET;
                        } else {
                            return addZeros(data);
                        }
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                        <div class="btn-group mb-3" role="group">
                                    <a href="javascript:;" onclick="editgroup('` + row.NAME + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDITGROUP + `"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="renamegroup('` + row.NAME + `')" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_RENAMEGROUP + `"><i class="bi bi-fonts"></i></a>
                                    <a href="javascript:;" onclick="delgroup('` + row.NAME + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVEGROUP + `"><i class="bi bi-x-square"></i></a>
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

    const element3 = document.getElementById('rename_window');
    const modal3 = new bootstrap.Modal(element3);

    var initRenameModalButtons = function () {
        const cancelButton2 = element3.querySelector('[data-kt-rename-modal-action="cancel"]');
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
                    modal3.hide();
                }
            });
        });
        const closeButton2 = element3.querySelector('[data-kt-rename-modal-action="close"]');
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
                    modal3.hide();

                }
            });
        });
    }

    return {
        init: function () {
            initDatatable();
            initEditModalButtons();
            initAddModalButtons();
            initRenameModalButtons();
        }
    }
}();

KTDatatablesServerSide.init();

$('#enddatetime').click(function () {

    if ($("#enddatetime").is(":checked")) {
        $("#cutcreation").removeAttr('disabled');
    } else {
        $('#cutcreation').prop("disabled", true);
    }

});

$('#purge').click(function () {

    if ($("#purge").is(":checked")) {
        $("#purgedays").removeAttr('disabled');
        $("#delempty").removeAttr('disabled');
    } else {
        $('#purgedays').prop("disabled", true);
        $('#delempty').prop("disabled", true);
    }

});

$('#add_enddatetime').click(function () {

    if ($("#add_enddatetime").is(":checked")) {
        $("#add_cutcreation").removeAttr('disabled');
    } else {
        $('#add_cutcreation').prop("disabled", true);
    }

});

$('#add_purge').click(function () {

    if ($("#add_purge").is(":checked")) {
        $("#add_purgedays").removeAttr('disabled');
        $("#add_delempty").removeAttr('disabled');
    } else {
        $('#add_purgedays').prop("disabled", true);
        $('#add_delempty').prop("disabled", true);
    }

});