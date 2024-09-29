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
var librarytype = 2;
var mactype = 1;
var bitOne = ["32", "48", "56", "64", "80", "96", "112", "128", "160", "192"];

$('#for_format').change(function () {
    var selectedCategory = $('#for_format').val();
    if (selectedCategory != "") {

        $('#for_bitrate').find('option').remove();

        var sizeList = [];

        if (selectedCategory == '1') {
            $('#for_bitrate').removeAttr('disabled');
            for (var i = 0; i <= bitOne.length; i++) {
                var biOne = bitOne[i];
                $('#for_bitrate').append($("<option></option>").attr("value", biOne).text(biOne));
            }
        } else {
            $("#for_bitrate").attr('disabled', true);
        }
    } else {

        $("#for_bitrate").empty();
        $("#for_bitrate").attr('disabled', true);
    }
});

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

function msToTime(s) {

    function pad(n, z) {
        z = z || 2;
        return ('00' + n).slice(-z);
    }

    var ms = s % 1000;
    s = (s - ms) / 1000;
    var secs = s % 60;
    s = (s - secs) / 60;
    var mins = s % 60;
    var hrs = (s - mins) / 60;

    return pad(hrs) + ':' + pad(mins) + ':' + pad(secs);
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

function getMillisFromTime(time) {

    time = time.split(':');
    var millis = 0;

    if (time.length == 2 && time[1].indexOf('.') != -1) {

        var temp = time[1].split('.');

        time[1] = temp[0];
        time.push(temp[1]);

    }

    if (time.length >= 2) {

        millis += time[0] * 60000;
        millis += time[1] * 1000;

    }

    if (time.length == 3)
        millis += time[2] * 100;

    return millis;

}

function add(type) {
    mactype = type;
}

function addcart(cart) {
    if (mactype == 1) {
        $("#playstart").val(cart);
    } else if (mactype == 2) {
        $("#playend").val(cart);
    } else if (mactype == 3) {
        $("#recordstart").val(cart);
    } else if (mactype == 4) {
        $("#recordend").val(cart);
    }
    editmodal.hide();
}

function edithost(station) {
    $("#conf_form").trigger("reset");
    $.ajax({
        url: HOST_URL + '/forms/rdlogedit/getstation.php',
        data: "id=" + station,
        dataType: 'json',
        success: function (data) {
            $('#rdlogedithost').val(station);
            $('#maxrecord').val(msToTime(data['MAXLENGTH']));
            if (data['TRIM_THRESHOLD'] != 0) {
                $('#autotrim').val(data['TRIM_THRESHOLD'] / 100); 
            } else {
                $('#autotrim').val(data['TRIM_THRESHOLD']);    
            }
            if (data['RIPPER_LEVEL'] != 0) {
                $('#normalize').val(data['RIPPER_LEVEL'] / 100); 
            } else {
                $('#normalize').val(data['RIPPER_LEVEL']);    
            }
            $('#audiomargin').val(data['TAIL_PREROLL']);
            $('#for_format').val(data['FORMAT']);
            $('#for_format').trigger('change');
            if (data['FORMAT'] == 1) {
                $('#for_bitrate').val(data['BITRATE'] / 1000);
            }
            
            $('#2startbutt').val(data['ENABLE_SECOND_START']);            
            $('#waveform').val(data['WAVEFORM_CAPTION']);
            if (data['START_CART'] != 0) {
                $('#playstart').val(data['START_CART']);
            } 
            if (data['END_CART'] != 0) {
                $('#playend').val(data['END_CART']);
            }                     
            if (data['REC_START_CART'] != 0) {
                $('#recordstart').val(data['REC_START_CART']);
            }              
            if (data['REC_END_CART'] != 0) {
                $('#recordend').val(data['REC_END_CART']);
            }           
            $('#channels').val(data['DEFAULT_CHANNELS']);            
            $('#defaulttrans').val(data['DEFAULT_TRANS_TYPE']);
            
            $('#settings_window').modal('show');

        }
    });
}

$('#conf_form').validate({
    rules: {
        maxrecord: {
            required: true,
        },
        autotrim: {
            required: true,
        },
        normalize: {
            required: true,
        },
        audiomargin: {
            required: true,
        },
        audioformat: {
            required: true,
        },
        waveform: {
            required: true,
        },
    },
    messages: {
        maxrecord: {
            required: TRAN_NOTBEEMPTY,
        },
        autotrim: {
            required: TRAN_NOTBEEMPTY,
        },
        normalize: {
            required: TRAN_NOTBEEMPTY,
        },
        audiomargin: {
            required: TRAN_NOTBEEMPTY,
        },
        audioformat: {
            required: TRAN_NOTBEEMPTY,
        },
        waveform: {
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
                url: HOST_URL + '/forms/rdlogedit/updatesettings.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#settings_window').modal('hide');
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
        dt = $("#rdlogedit_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/rdlogedit-table.php",
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
                    data: 'DEFAULT_TRANS_TYPE'
                },
                {
                    data: null
                },
            ],
            columnDefs: [
                
                {
                    targets: 1,
                    render: function (data, type, row) {

                        if (data == 0) {
                            return TRAN_PLAY;
                        } else if (data == 1) {
                            return TRAN_SEGUE;
                        } else {
                            return TRAN_STOP;
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
                                    <a href="javascript:;" onclick="edithost('`+ row.STATION + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_CONFRDLOGEDIT + `"><i class="bi bi-pencil"></i></a>
                                </div>
                        `;
                    }
                },
            ],
        });

    }

    const element1 = document.getElementById('settings_window');
    const modal1 = new bootstrap.Modal(element1);

    var initPanelSettingsModalButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-rdlogedithost-modal-action="cancel"]');
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
        const closeButton2 = element1.querySelector('[data-kt-rdlogedithost-modal-action="close"]');
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
            initPanelSettingsModalButtons();
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
                    d.thetype = librarytype;
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
                        if (librarytype == 2) {
                            return `<a href="javascript:;" onclick="addcart('` + row.NUMBER + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
                        } else {
                            return `<a href="javascript:;" onclick="addcart('` + row.NUMBER + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
                        }

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

KTDatatablesServerSide.init();
KTDatatablesServerSideLibrary.init();

$(".fltpick").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i:S",
    enableSeconds: true,
    time_24hr: true
});