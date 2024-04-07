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

let choices = document.querySelectorAll(".choices")
let initChoice
for (let i = 0; i < choices.length; i++) {
    if (choices[i].classList.contains("multiple-remove")) {
        initChoice = new Choices(choices[i], {
            delimiter: ",",
            editItems: true,
            maxItemCount: -1,
            removeItemButton: true,
        })
    } else {
        initChoice = new Choices(choices[i], {
            noResultsText: TRAN_SELECTNORESULTS,
            noChoicesText: TRAN_SELECTNOOPTIONS,
            itemSelectText: TRAN_SELECTPRESSSELECT,
        })
    }
}

function addcommand(i) {
    if (ALLOW_MOD == 1) {
        $('#isedit').val('0');
        $('#add_macro').modal('show');

    } else {
        Swal.fire({
            text: TRAN_NORIGHTS,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: TRAN_OK,
            customClass: {
                confirmButton: "btn fw-bold btn-primary"
            }
        });
    }
}

function editmacro(i, o) {
    if (ALLOW_MOD == 1) {
        $('#lineid').val(i);
        $('#oldcommand').val(o);
        $('#commando').val(o);
        $('#isedit').val('1');
        $('#add_macro').modal('show');

    } else {
        Swal.fire({
            text: TRAN_NORIGHTS,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: TRAN_OK,
            customClass: {
                confirmButton: "btn fw-bold btn-primary"
            }
        });
    }
}

function deletemacro(i, o) {
    if (ALLOW_MOD == 1 || ALLOW_DEL == 1) {
        Swal.fire({
            text: TRAN_REMOVEMACROCOMMAND,
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
                    url: HOST_URL + '/forms/library/removecommand.php',
                    data: {
                        cartid: CART_ID,
                        lineid: i,
                        macro: o
                    },
                    datatype: 'html',
                    success: function (data) {
                        var mydata = $.parseJSON(data);
                        var fel = mydata.error;
                        var kod = mydata.errorcode;
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
        Swal.fire({
            text: TRAN_NORIGHTS,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: TRAN_OK,
            customClass: {
                confirmButton: "btn fw-bold btn-primary"
            }
        });
    }
}

var MACROForm = $('#addmacro_form').validate({
    rules: {
        commando: {
            required: true,
        },
    },
    messages: {
        commando: {
            required: TRAN_NOTBEEMPTY
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
        var dataString = $('#addmacro_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/library/updatecommand.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    MACROForm.resetForm();
                    $('#add_macro').modal('hide');
                    dt.ajax.reload();
                } else {
                    Swal.fire({
                        text: TRAN_COMMANDONOTPOSSIBLE,
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

var UPDATEForm = $('#cart_form').validate({
    rules: {
        title: {
            required: true,
        },
    },
    messages: {
        title: {
            required: TRAN_NOTBEEMPTY
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
        var dataString = $('#cart_form').serialize();
        if (ALLOW_MOD == 1) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/library/updatemacro.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        Swal.fire({
                            text: TRAN_CARTSAVED,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
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
        } else {
            Swal.fire({
                text: TRAN_NORIGHTS,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: TRAN_OK,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary"
                }
            });
        }
    }
});

var KTDatatablesServerSide = function () {
    var initDatatable = function () {
        dt = $("#macro_table").DataTable({
            processing: true,
            scrollY: "500px",
            scrollCollapse: true,
            paging: false,
            dom: "<'table-responsive'tr>",
            ordering: false,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: {
                url: HOST_URL + "/tables/macro-table.php",
                data: function (d) {
                    d.cartid = CART_ID;
                }
            },
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
                    data: 'ID'
                },
                {
                    data: 'MACRO'
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
                <a href="javascript:;" onclick="editmacro('`+ row.ID + `','` + row.MACRO + `')" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_EDITCOMMAND + `"><i class="bi bi-pencil"></i></a>
                <a href="javascript:;" onclick="deletemacro('`+ row.ID + `','` + row.MACRO + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_REMOVECOMMAND + `"><i class="bi bi-x-square"></i></a>
            </div>
                    `;
                    }
                },
            ],
        });
    }

    const element1 = document.getElementById('add_macro');
    const modal1 = new bootstrap.Modal(element1);

    var initAddMacroButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-addmacro-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSECOMMANDOWINDOW,
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
        const closeButton2 = element1.querySelector('[data-kt-addmacro-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSECOMMANDOWINDOW,
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
            initAddMacroButtons();
        }
    }
}();

KTDatatablesServerSide.init();