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
var dt2;
var allgroups = 1;
var groupnow;
var editmodal;

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

$('#selectGroup').on('change', function (e) {

    if ($('#selectGroup').val() == 'allgroups') {
        allgroups = 1;
        dt2.ajax.reload();
    } else {
        allgroups = 0;
        groupnow = $('#selectGroup').val();
        dt2.ajax.reload();
    }
});

function addcart(cart, title) {
    $("#cart_macro").val(cart);
    $("#desc_macro").val(title);
    editmodal.hide();
}

function getTimeFromMillis(millis) {

    var minutes = '' + Math.floor(millis / 60000);
    millis = millis % 60000;

    while (minutes.length < 2)
        minutes = '0' + minutes;

    var seconds = '' + Math.floor(millis / 1000);
    if (seconds < 0)
        seconds = seconds * -1;

    while (seconds.length < 2)
        seconds = '0' + seconds;

    millis = millis % 1000;

    var tenths = Math.floor(millis / 100);

    if (tenths < 0)
        tenths = tenths * -1;

    return minutes + ':' + seconds + '.' + tenths;

}

function edit(type, id) {

    $.ajax({
        url: HOST_URL + '/forms/rdcatch/getdata.php',
        data: "id=" + id,
        dataType: 'json',
        success: function (data) {
            if (type == 1) {
                $('#macid').val(id);
                if (data['IS_ACTIVE'] == 'Y') {
                    $("#eventactive_macro").prop('checked', true);
                } else {
                    $("#eventactive_macro").prop('checked', false);
                }
                $('#location_macro').val(data['STATION_NAME']);
                $('#start_macro').val(data['START_TIME']);
                $('#desc_macro').val(data['DESCRIPTION']);
                $('#cart_macro').val(data['MACRO_CART']);
                $('#cart_macro').val(data['MACRO_CART']);
                if (data['ONE_SHOT'] == 'Y') {
                    $("#oneshot_macro").prop('checked', true);
                } else {
                    $("#oneshot_macro").prop('checked', false);
                }
                if (data['SUN'] == 'Y') {
                    $("#sun_mac").prop('checked', true);
                } else {
                    $("#sun_mac").prop('checked', false);
                }
                if (data['MON'] == 'Y') {
                    $("#mon_mac").prop('checked', true);
                } else {
                    $("#mon_mac").prop('checked', false);
                }
                if (data['TUE'] == 'Y') {
                    $("#tue_mac").prop('checked', true);
                } else {
                    $("#tue_mac").prop('checked', false);
                }
                if (data['WED'] == 'Y') {
                    $("#wed_mac").prop('checked', true);
                } else {
                    $("#wed_mac").prop('checked', false);
                }
                if (data['THU'] == 'Y') {
                    $("#thu_mac").prop('checked', true);
                } else {
                    $("#thu_mac").prop('checked', false);
                }
                if (data['FRI'] == 'Y') {
                    $("#fri_mac").prop('checked', true);
                } else {
                    $("#fri_mac").prop('checked', false);
                }
                if (data['SAT'] == 'Y') {
                    $("#sat_mac").prop('checked', true);
                } else {
                    $("#sat_mac").prop('checked', false);
                }
                $('#macro_edit').modal('show');
            }
        }
    });
}

$('#macro_form').validate({
    rules: {
        location: {
            required: true,
        },
        start_macro: {
            required: true,
        },
        desc_macro: {
            required: true,
        },
        cart_macro: {
            required: true,
        },
    },
    messages: {
        location: {
            required: TRAN_NOTBEEMPTY,
        },
        start_macro: {
            required: TRAN_NOTBEEMPTY,
        },
        desc_macro: {
            required: TRAN_NOTBEEMPTY,
        },
        cart_macro: {
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
        var dataString = $('#macro_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/rdcatch/macroedit.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    $('#macro_edit').modal('hide');
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

var KTDatatablesServerSide = function () {
    var initDatatable = function () {
        dt = $("#rdcatch_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/rdcatch-table.php",
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
                    data: 'IS_ACTIVE'
                },
                {
                    data: 'STATION_NAME'
                },
                {
                    data: 'DESCRIPTION'
                },
                {
                    data: 'START_TIME'
                },
                {
                    data: 'END_TIME'
                },
                {
                    data: 'MACRO_CART'
                },
                {
                    data: 'SUN'
                },
                {
                    data: 'MON'
                },
                {
                    data: 'TUE'
                },
                {
                    data: 'WED'
                },
                {
                    data: 'THU'
                },
                {
                    data: 'FRI'
                },
                {
                    data: 'SAT'
                },
                {
                    data: 'ONE_SHOT'
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
                                    <a href="javascript:;" onclick="edit('`+ row.TYPE + `', '`+ row.ID + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDIT + `"><i class="bi bi-pencil"></i></a>
                                </div>
                        `;
                    }
                },
            ],
        });

    }

    const element1 = document.getElementById('macro_edit');
    const modal1 = new bootstrap.Modal(element1);

    var initMacroEditModalButtons = function () {
        const cancelButton1 = element1.querySelector('[data-kt-rdmacro-modal-action="cancel"]');
        cancelButton1.addEventListener('click', e => {
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
        const closeButton2 = element1.querySelector('[data-kt-rdmacro-modal-action="close"]');
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
            initMacroEditModalButtons();
        }
    }
}();

var KTDatatablesServerSideLibrary = function () {
    var initDatatableLibrary = function () {
        dt2 = $("#macroadd_table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ordering: true,
            autoWidth: false,
            order: [
                [2, 'desc']
            ],
            stateSave: true,
            serverMethod: 'post',
            ajax: {
                url: HOST_URL + "/tables/rdcatch-librarydata.php",
                data: function (d) {
                    d.ausr = USERNAME;
                    d.all = allgroups;
                    d.groups = groupnow;
                    d.thetype = 2;
                }
            },
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
                    data: 'NUMBER'
                },
                {
                    data: 'GROUP_NAME'
                },
                {
                    data: 'AVERAGE_LENGTH'
                },
                {
                    data: 'TITLE'
                },
                {
                    data: 'ARTIST'
                },
                {
                    data: null
                },
            ],
            columnDefs: [

                {
                    targets: 1,
                    render: function (data, type, row) {
                        return '<P style="color:' + row.COLOR + '">' + data + '</p>';
                    }
                },

                {
                    targets: 2,
                    render: function (data, type, row) {
                        return getTimeFromMillis(data);
                    }
                },


                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                        <a href="javascript:;" onclick="addcart('`+ row.NUMBER + `', '` + row.TITLE + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
                    }
                },
            ],
        });

    }

    const element4 = document.getElementById('macro_select');
    const modal4 = new bootstrap.Modal(element4);

    var initSelCartModalButtons = function () {
        const cancelButton2 = element4.querySelector('[data-kt-macsel-modal-action="cancel"]');
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
                    editmodal.hide();
                }
            });
        });
        const closeButton2 = element4.querySelector('[data-kt-macsel-modal-action="close"]');
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
                    editmodal.hide();

                }
            });
        });
    }

    return {
        init: function () {
            initDatatableLibrary();
            initSelCartModalButtons();
        }
    }
}();

$(document).ready(function () {
KTDatatablesServerSide.init();
KTDatatablesServerSideLibrary.init();
});

$("#start_macro").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i:S",
    enableSeconds: true,
    time_24hr: true
});