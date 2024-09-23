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
var hourid;
var oldclockname;
var clockid;
var color;
var shortname;
var layoutedit;
var serviceName = HOST_SERVICE;
var editmodal;
var layoutrow;

var elements = Array.prototype.slice.call(document.querySelectorAll("[data-bs-stacked-modal]"));
if (elements && elements.length > 0) {
    elements.forEach((element) => {
        if (element.getAttribute("data-kt-initialized") === "1") {
            return;
        }

        element.setAttribute("data-kt-initialized", "1");

        element.addEventListener("click", function (e) {
            e.preventDefault();

            const modalEl = document.querySelector(this.getAttribute("data-bs-stacked-modal"));

            if (modalEl) {
                editmodal = new bootstrap.Modal(modalEl);
                editmodal.show();
            }
        });
    });
}

function layoutaddall(m, n, o) {

    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/grids/savelayoutallclock.php',
        data: {
            name: m,
            short: n,
            color: o,
            row: layoutrow,
            service: serviceName,
            layout: layoutedit
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                for (rowruns = 0; rowruns < 168; rowruns++) {
                    $("#edclocklink_" + rowruns).css('background-color', o);
                    $("#edclockname_" + rowruns).html(n);
                }

                editmodal.hide();

            } else {
                Swal.fire({
                    text: TRAN_NOTSAVED,
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

function addlayoutclock(m, n, o) {

    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/grids/savelayoutclock.php',
        data: {
            name: m,
            short: n,
            color: o,
            row: layoutrow,
            service: serviceName,
            layout: layoutedit
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                editmodal.hide();
                $("#edclocklink_" + layoutrow).css('background-color', o);
                $("#edclockname_" + layoutrow).html(n);

            } else {
                Swal.fire({
                    text: TRAN_NOTSAVED,
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

function replacelayouthour(n) {
    layoutrow = n;
}

function editlayout(n) {
    $("#layout_select").modal("hide");
    layoutedit = n;
    jQuery.ajax({
        type: "GET",
        url: HOST_URL + '/forms/grids/getgridlayout.php',
        data: {
            layout: n,
            service: serviceName
        },
        datatype: 'json',
        success: function (results) {
            var obj = $.parseJSON(results);

            $.each(obj, function(key,value) {
                $("#edclocklink_" + value.HOUR).css('background-color', value.COLOR);
                $("#edclockname_" + value.HOUR).html(value.SHOR_NAME);
            });

            $("#layoutedit_select").modal("show");
        }
    });

}

function removelayout(n) {

    Swal.fire({
        text: TRAN_REMOVELAYOUTDESIGN,
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
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/grids/removelayout.php',
                data: {
                    layout: n,
                    service: serviceName
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

}

function replacegrid(n) {

    Swal.fire({
        text: TRAN_REPLACEGRIDLAYOUT,
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
            jQuery.ajax({
                type: "GET",
                url: HOST_URL + '/forms/grids/replacegridlayout.php',
                data: {
                    layout: n,
                    service: serviceName
                },
                datatype: 'json',
                success: function (results) {
                    var obj = $.parseJSON(results);

                    $.each(obj, function(key,value) {
                        $("#clocklink_" + value.HOUR).css('background-color', value.COLOR);
                        $("#clockname_" + value.HOUR).html(value.SHOR_NAME);
                    });

                    $("#layout_select").modal("hide");
                }
            });
        }
    });

}

function selectclock(i, o) {
    hourid = i;
    oldclockname = o;
    $("#clock_modal").modal("show");
}

function saveGrid(s) {
    $("#serviceid").val(s);
    $("#save_grid").modal("show");
}

function LoadLayout(s) {
    serviceName = s;
    dt.ajax.reload();
    $("#layout_select").modal("show");
}

function addclock(i, p, q) {
    clockid = i;
    color = q;
    shortname = p;

    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/grids/saveclockgrid.php',
        data: {
            new: i,
            old: oldclockname,
            clock: hourid
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                $("#clock_modal").modal("hide");
                $("#clocklink_" + hourid).css('background-color', color);
                $("#clockname_" + hourid).html(shortname);

            } else {
                Swal.fire({
                    text: TRAN_NOTSAVED,
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

function addallclock(i, p, q, z) {
    clockid = i;
    color = q;
    shortname = p;

    Swal.fire({
        text: TRAN_SETALLGRID,
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
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/grids/saveallclockgrids.php',
                data: {
                    new: i,
                    old: oldclockname,
                    service: z
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        $("#clock_modal").modal("hide");
                        for (rowruns = 0; rowruns < 168; rowruns++) {
                            $("#clocklink_" + rowruns).css('background-color', color);
                            $("#clockname_" + rowruns).html(shortname);
                        }


                    }
                }
            });
        }
    });


}

function clearAll(i) {
    Swal.fire({
        text: TRAN_CLEARALL,
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
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/grids/clearallclockgrids.php',
                data: {
                    service: i
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        for (rowruns = 0; rowruns < 168; rowruns++) {
                            $("#clocklink_" + rowruns).css('background-color', "");
                            $("#clockname_" + rowruns).html("");
                        }


                    }
                }
            });
        }
    });
}

var SaveLayout = $('#save_form').validate({
    rules: {
        layoutname: {
            required: true,
            remote: {
                url: HOST_URL + '/validation/checkgridlayoutname.php',
                type: "post",
                data: {
                    service: function () {
                        return $("#serviceid").val();
                    },
                }
            }
        },
    },
    messages: {
        layoutname: {
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
        var dataString = $('#save_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/grids/savegridlayout.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    $("#save_form").trigger("reset");
                    $('#save_grid').modal('hide');
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

const element = document.getElementById('clock_modal');
const modal = new bootstrap.Modal(element);

var initGridModalButtons = function () {
    const cancelButton = element.querySelector('[data-kt-clock-modal-action="cancel"]');
    cancelButton.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSECLOCKS,
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
                modal.hide();
            }
        });
    });
    const closeButton = element.querySelector('[data-kt-clock-modal-action="close"]');
    closeButton.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSECLOCKS,
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
                modal.hide();

            }
        });
    });
}

const element2 = document.getElementById('save_grid');
const modal2 = new bootstrap.Modal(element2);

var initGridLayoutModalButtons = function () {
    const cancelButton = element2.querySelector('[data-kt-savelayout-modal-action="cancel"]');
    cancelButton.addEventListener('click', e => {
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
    const closeButton = element2.querySelector('[data-kt-savelayout-modal-action="close"]');
    closeButton.addEventListener('click', e => {
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

var KTDatatablesServerSideLibrary = function () {
    var initDatatableLibrary = function () {
        dt = $("#gridlayout_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            autoWidth: false,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/grids-layout-table.php?service="+serviceName,
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
                    data: 'LAYOUTNAME'
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
                <a href="javascript:;" onclick="replacegrid('`+ row.LAYOUTNAME + `')" class="btn icon btn-info" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_SELLAYOUTGRID + `"><i class="bi bi-plus-square"></i></a>
                <a href="javascript:;" onclick="editlayout('`+ row.LAYOUTNAME + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_EDITGRIDLAYOUT + `"><i class="bi bi-pencil-square"></i></a>
                <a href="javascript:;" onclick="removelayout('`+ row.LAYOUTNAME + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_REMOVE + `"><i class="bi bi-x-square"></i></a>
            </div>`;

                    }
                },
            ],
        });

    }

    const element3 = document.getElementById('layout_select');
    const modal3 = new bootstrap.Modal(element3);

    var initSelLayoutModalButtons = function () {
        const cancelButton2 = element3.querySelector('[data-kt-gridlayout-modal-action="cancel"]');
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
        const closeButton2 = element3.querySelector('[data-kt-gridlayout-modal-action="close"]');
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
            initDatatableLibrary();
            initSelLayoutModalButtons();
        }
    }
}();

const element4 = document.getElementById('layoutedit_select');
const modal4 = new bootstrap.Modal(element4);

var initGridEditModalButtons = function () {
    const cancelButton = element4.querySelector('[data-kt-gridlayoutedit-modal-action="cancel"]');
    cancelButton.addEventListener('click', e => {
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
                modal4.hide();
                $("#layout_select").modal("show");
            }
        });
    });
    const closeButton = element4.querySelector('[data-kt-gridlayoutedit-modal-action="close"]');
    closeButton.addEventListener('click', e => {
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
                modal4.hide();
                $("#layout_select").modal("show");

            }
        });
    });
}

const element5 = document.getElementById('layoutclock_modal');
const modal5 = new bootstrap.Modal(element5);

var initGridClockEditModalButtons = function () {
    const cancelButton = element5.querySelector('[data-kt-clocklayout-modal-action="cancel"]');
    cancelButton.addEventListener('click', e => {
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
                editmodal.hide();
            }
        });
    });
    const closeButton = element5.querySelector('[data-kt-clocklayout-modal-action="close"]');
    closeButton.addEventListener('click', e => {
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
                editmodal.hide();

            }
        });
    });
}

initGridModalButtons();
initGridEditModalButtons();
initGridClockEditModalButtons();
initGridLayoutModalButtons();

$(document).ready(function () {
    KTDatatablesServerSideLibrary.init();
});
