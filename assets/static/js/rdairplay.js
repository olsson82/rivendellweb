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

function addmess(station) {
    jQuery.ajax({
        type: "POST",
        url: HOST_URL + "/forms/rdairplay/getmessage.php",
        data: {
            station: station
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var message = mydata.message;
            if (fel == "false") {
                $('#message').val(message);
                $('#idet').val(station);
                $('#add_message').modal('show');
            }
        }
    });
}

function edithost(station) {
    $.ajax({
        url: HOST_URL + '/forms/rdairplay/getstation.php',
        data: "id=" + station,
        dataType: 'json',
        success: function (data) {
            $('#rdairhost').val(station);
            $('#mansegue').val(data['SEGUE_LENGTH']);
            $('#forcsegue').val(data['TRANS_LENGTH']);
            $('#piecountlast').val(data['PIE_COUNT_LENGTH'] / 1000);
            $('#systempanels').val(data['STATION_PANELS']);
            $('#userpanels').val(data['USER_PANELS']);
            $('#piecountsto').val(data['PIE_COUNT_ENDPOINT']);
            $('#deftranstype').val(data['DEFAULT_TRANS_TYPE']);
            $('#defaultservice').val(data['DEFAULT_SERVICE']);
            $('#spacebar').val(data['BAR_ACTION']);
            if (data['FLASH_PANEL'] == 'Y') {
                $("#flashpanel").prop('checked', true);
            } else {
                $("#flashpanel").prop('checked', false);
            }
            if (data['PANEL_PAUSE_ENABLED'] == 'Y') {
                $("#buttonpause").prop('checked', true);
            } else {
                $("#buttonpause").prop('checked', false);
            }
            $('#labletemp').val(data['BUTTON_LABEL_TEMPLATE']);
            if (data['CHECK_TIMESYNC'] == 'Y') {
                $("#timesunc").prop('checked', true);
            } else {
                $("#timesunc").prop('checked', false);
            }
            if (data['SHOW_AUX_1'] == 'Y') {
                $("#aux1").prop('checked', true);
            } else {
                $("#aux1").prop('checked', false);
            }
            if (data['SHOW_AUX_2'] == 'Y') {
                $("#aux2").prop('checked', true);
            } else {
                $("#aux2").prop('checked', false);
            }
            if (data['CLEAR_FILTER'] == 'Y') {
                $("#clearcart").prop('checked', true);
            } else {
                $("#clearcart").prop('checked', false);
            }
            if (data['PAUSE_ENABLED'] == 'Y') {
                $("#enabpaused").prop('checked', true);
            } else {
                $("#enabpaused").prop('checked', false);
            }
            if (data['SHOW_COUNTERS'] == 'Y') {
                $("#extrabuttons").prop('checked', true);
            } else {
                $("#extrabuttons").prop('checked', false);
            }
            if (data['HOUR_SELECTOR_ENABLED'] == 'Y') {
                $("#showhour").prop('checked', true);
            } else {
                $("#showhour").prop('checked', false);
            }
            $('#preroll').val(data['AUDITION_PREROLL'] / 1000);
            $('#settings_window').modal('show');

        }
    });
}

$('#conf_form').validate({
    rules: {
        mansegue: {
            required: true,
        },
        forcsegue: {
            required: true,
        },
        piecountlast: {
            required: true,
        },
        systempanels: {
            required: true,
        },
        userpanels: {
            required: true,
        },
        labletemp: {
            required: true,
        },
        preroll: {
            required: true,
        },
    },
    messages: {
        mansegue: {
            required: TRAN_NOTBEEMPTY,
        },
        forcsegue: {
            required: TRAN_NOTBEEMPTY,
        },
        piecountlast: {
            required: TRAN_NOTBEEMPTY,
        },
        systempanels: {
            required: TRAN_NOTBEEMPTY,
        },
        userpanels: {
            required: TRAN_NOTBEEMPTY,
        },
        labletemp: {
            required: TRAN_NOTBEEMPTY,
        },
        preroll: {
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
        var dataString = $('#conf_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/rdairplay/rdairsettings.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    $('#settings_window').modal('hide');
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

$('#addmessage_form').validate({
    rules: {
        message: {
            required: true,
        },
    },
    messages: {
        message: {
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
        var dataString = $('#addmessage_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/rdairplay/addmessage.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    $('#add_message').modal('hide');
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
        dt = $("#rdairplay_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/rdairplay-table.php",
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
                    data: 'STATION'
                },
                {
                    data: 'DEFAULT_SERVICE'
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
                                    <a href="javascript:;" onclick="addmess('`+ row.STATION + `')" class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_ADDMESSAGE + `"><i class="bi bi-chat-right-text"></i></a>
                                    <a href="javascript:;" onclick="edithost('`+ row.STATION + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_CONFRDAIRPLAY + `"><i class="bi bi-pencil"></i></a>
                                </div>
                        `;
                    }
                },
            ],
        });

    }

    const element1 = document.getElementById('add_message');
    const modal1 = new bootstrap.Modal(element1);

    var initMessageModalButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-message-modal-action="cancel"]');
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
        const closeButton2 = element1.querySelector('[data-kt-message-modal-action="close"]');
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

    const element2 = document.getElementById('settings_window');
    const modal2 = new bootstrap.Modal(element2);

    var initAirSettingsModalButtons = function () {
        const cancelButton2 = element2.querySelector('[data-kt-rdairhost-modal-action="cancel"]');
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
        const closeButton2 = element2.querySelector('[data-kt-rdairhost-modal-action="close"]');
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
            initMessageModalButtons();
            initAirSettingsModalButtons();
        }
    }
}();

KTDatatablesServerSide.init();