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
var dt1;
var dt2;
var groupnow;
var allgroups = 1;

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

function roworder(id, evname, order, up, type) {
    jQuery.ajax({
        type: "POST",
        async: false,
        url: HOST_URL + '/forms/events/roworder.php',
        data: {
            id: id,
            evname: evname,
            order: order,
            up: up,
            type: type
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            if (type == 0) {
                dt.ajax.reload();
            } else {
                dt1.ajax.reload();
            }

        }
    });
}

function imptrans(id, trans, type) {
    jQuery.ajax({
        type: "POST",
        async: false,
        url: HOST_URL + '/forms/events/editimptrans.php',
        data: {
            idet: id,
            trans: trans
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            if (type == 0) {
                dt.ajax.reload();
            } else {
                dt1.ajax.reload();
            }

        }
    });
}

function removeimp(id, type, row, name) {
    Swal.fire({
        text: TRAN_REMOVEIMP,
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
                url: HOST_URL + '/forms/events/removeimp.php',
                data: {
                    id: id,
                    type: type,
                    row: row,
                    name: name
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        if (type == 0) {
                            dt.ajax.reload();

                        } else {
                            dt1.ajax.reload();
                        }


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

function addimp(event, type, imptype) {
    theimptype = imptype;
    if (type == 1) {
        $('#cart_select').modal('show');
        $('#vttype1').val(type);
        $('#imptype1').val(imptype);

    } else if (type == 2) {
        $('#add_vt').modal('show');
        $('#vtnotes').val('');
        $('#vttype').val(type);
        $('#imptype').val(imptype);
        $('#addVTLabel').html(TRAN_VTHEAD);
        $('#vtnote').html(TRAN_VTNOTE);
    } else {
        $('#add_vt').modal('show');
        $('#vtnotes').val('');
        $('#vttype').val(type);
        $('#imptype').val(imptype);
        $('#addVTLabel').html(TRAN_MKHEAD);
        $('#vtnote').html(TRAN_MKHEAD);
    }
}

function addcarttoimp(cart, type) {
    jQuery.ajax({
        type: "POST",
        async: false,
        url: HOST_URL + '/forms/events/addimp.php',
        data: {
            idet: $("#idet1").val(),
            imptype: $("#imptype1").val(),
            vttype: $("#vttype1").val(),
            cart: cart,
            carttype: type
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            if (theimptype == 0) {
                dt.ajax.reload();
            } else {
                dt1.ajax.reload();
            }
            $('#cart_select').modal('hide');
        }
    });
}

function edimp(i) {
    $.ajax({
        url: HOST_URL + '/forms/events/editinfo.php',
        data: "id=" + i,
        dataType: 'json',
        success: function (data) {
            var type = data['TYPE'];
            var evtype = data['EVENT_TYPE'];
            var marker = data['MARKER_COMMENT'];
            $('#vtnotes').val(marker);
            $('#edid').val(i);
            if (evtype == '6') {
                $('#vtnote').html(TRAN_VTNOTE);
                $('#addVTLabel').html(TRAN_VTHEAD);
                $('#vttype').val('2');
                $('#vtedit').val('1');
                $('#imptype').val(type);
            } else {
                $('#vtnote').html(TRAN_MKHEAD);
                $('#addVTLabel').html(TRAN_MKHEAD);
                $('#vttype').val('3');
                $('#vtedit').val('1');
                $('#imptype').val(type);
            }
            $('#add_vt').modal('show');
        }
    });
}

var EVForm = $('#event_form').validate({
    rules: {
        schedstart: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        waitupto: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        byleast: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        impsched1: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        impsched2: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        artsep: {
            digits: true,
        },
        titsep: {
            digits: true,
        },
    },
    messages: {
        schedstart: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        waitupto: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        byleast: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        impsched1: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        impsched2: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        artsep: {
            pattern: TRAN_ONLYDIGITS
        },
        titsep: {
            pattern: TRAN_ONLYDIGITS
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
        $('#cuestartmillis').val(getMillisFromTime($('#schedstart').val()));
        $('#waituptomillis').val(getMillisFromTime($('#waitupto').val()));
        $('#byleastmillis').val(getMillisFromTime($('#byleast').val()));
        $('#impsched1millis').val(getMillisFromTime($('#impsched1').val()));
        $('#impsched2millis').val(getMillisFromTime($('#impsched2').val()));
        var dataString = $('#event_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/events/updateevent.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    Swal.fire({
                        text: TRAN_EVENTHASSAVED,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                } else {
                    Swal.fire({
                        text: TRAN_NOTPOSSIBLESAVETHEEVENT,
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

var VTForm = $('#vtmarker_form').validate({
    rules: {
        vtnotes: {
            required: true,
        },
    },
    messages: {
        vtnotes: {
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
        var dataString = $('#vtmarker_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/events/addimp.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    $("#vtmarker_form").trigger("reset");
                    if (theimptype == 0) {
                        dt.ajax.reload();
                    } else {
                        dt1.ajax.reload();
                    }
                    $('#add_vt').modal('hide');
                } else {
                    Swal.fire({
                        text: TRAN_NOTPOSSIBLEMARKER,
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


const Musthave2box = document.getElementById('musthave2');
const SelMusthave2Box = new Choices(Musthave2box, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const Musthavebox = document.getElementById('musthave');
const SelMusthaveBox = new Choices(Musthavebox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const Groupbox = document.getElementById('group');
const SelGroupBox = new Choices(Groupbox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const ImportCartbox = document.getElementById('importcart');
const SelImportCartBox = new Choices(ImportCartbox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const Inlinebox = document.getElementById('inline');
const SelInlineBox = new Choices(Inlinebox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

Inputmask({
    "mask": "99:99"
}).mask("#schedstart");

Inputmask({
    "mask": "99:99"
}).mask("#byleast");

Inputmask({
    "mask": "99:99"
}).mask("#impsched1");

Inputmask({
    "mask": "99:99"
}).mask("#impsched2");

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

    return pad(hrs) + ':' + pad(mins) + ':' + pad(secs) + '.' + pad(ms, 1);
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

var KTDatatablesServerSide = function () {
    var initDatatable = function () {


        dt = $("#preimp_table").DataTable({
            processing: true,
            scrollY: 600,
            scrollCollapse: true,
            paging: false,
            dom: "<'table-responsive'tr>",
            ordering: false,
            order: [
                [2, 'desc']
            ],
            stateSave: true,
            serverMethod: 'post',
            ajax: {
                url: HOST_URL + "/tables/event-preimporttable.php",
                data: function (d) {
                    d.evname = EV_ID;
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
                    data: 'CART_NUMBER'
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
                    data: 'TRANS_TYPE'
                },
                {
                    data: 'COUNT'
                },
                {
                    data: null
                },
            ],
            columnDefs: [

                {
                    targets: 0,
                    render: function (data, type, row) {
                        if (row.EVENT_TYPE == 0) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/sound.png" /></div> ` + data;
                        } else if (row.EVENT_TYPE == 6) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/microphone.png" /></div> `;
                        } else if (row.EVENT_TYPE == 2) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/settings.png" /></div> ` + data;
                        } else if (row.EVENT_TYPE == 1) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/notepad.png" /></div> `;
                        }
                    }
                },

                {
                    targets: 1,
                    render: function (data, type, row) {
                        if (row.EVENT_TYPE == 0) {
                            return data;
                        } else if (row.EVENT_TYPE == 6) {
                            return TRAN_TRACK;
                        } else if (row.EVENT_TYPE == 2) {
                            return data;
                        } else if (row.EVENT_TYPE == 1) {
                            return TRAN_MARKER;
                        }
                    }
                },

                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (data != 0) {
                            return getTimeFromMillis(data);
                        } else {
                            return "";
                        }
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {

                        if (row.EVENT_TYPE == 0) {
                            return data;
                        } else if (row.EVENT_TYPE == 6) {
                            return TRAN_VTRACK;
                        } else if (row.EVENT_TYPE == 2) {
                            return data;
                        } else if (row.EVENT_TYPE == 1) {
                            return TRAN_LNOTE;
                        }




                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (row.COUNT == 0) {
                            return TRAN_AUTO;
                        } else {
                            if (data == 0) {
                                return TRAN_PLAY;
                            } else if (data == 1) {
                                return TRAN_SEGUE;
                            } else {
                                return TRAN_STOP;
                            }

                        }
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return `<div class="btn-group mb-3" role="group">
                <a href="javascript:;" onclick="roworder('` + row.ID + `', '` + row.EVENT_NAME + `', '` + row.COUNT + `','1','0')" class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_MOVEUP + `"><i class="bi bi-arrow-up-circle"></i></a>
                <a href="javascript:;" onclick="roworder('` + row.ID + `', '` + row.EVENT_NAME + `', '` + row.COUNT + `','0', '0')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_MOVEDOWN + `"><i class="bi bi-arrow-down-circle"></i></a>                
            </div>`;
                    }
                },

                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var tranmen = '';
                        var roed = '';

                        if (row.COUNT != 0) {
                            tranmen = `<a href="javascript:;" onclick="imptrans('` + row.ID + `', '0', '` + row.TYPE + `')" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_PLAYTRANS + `"><i class="bi bi-play"></i></a>
                            <a href="javascript:;" onclick="imptrans('`+ row.ID + `', '1', '` + row.TYPE + `')" class="btn icon btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_SEGUETRANS + `"><i class="bi bi-arrows-angle-contract"></i></a>`;
                        }

                        if ((row.EVENT_TYPE != 0) && (row.EVENT_TYPE != 2)) {
                            roed = `<a href="javascript:;" onclick="edimp('` + row.ID + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_EDIT + `"><i class="bi bi-pencil"></i></a>`;
                        }


                        return `
                                    <div class="btn-group mb-3" role="group">
                                    `+ tranmen + `
                                    `+ roed + `
                                    <a href="javascript:;" onclick="removeimp('`+ row.ID + `', '` + row.TYPE + `','` + row.COUNT + `','` + row.EVENT_NAME + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVE + `"><i class="bi bi-x-square"></i></a>
                                </div>
                    `;
                    }
                },
            ],
        });
    }

    var initPostDatatable = function () {


        dt1 = $("#postimp_table").DataTable({
            processing: true,
            scrollY: 600,
            scrollCollapse: true,
            paging: false,
            dom: "<'table-responsive'tr>",
            ordering: false,
            order: [
                [2, 'desc']
            ],
            stateSave: true,
            serverMethod: 'post',
            ajax: {
                url: HOST_URL + "/tables/event-postimporttable.php",
                data: function (d) {
                    d.evname = EV_ID;
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
                    data: 'CART_NUMBER'
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
                    data: 'TRANS_TYPE'
                },
                {
                    data: 'COUNT'
                },
                {
                    data: null
                },
            ],
            columnDefs: [

                {
                    targets: 0,
                    render: function (data, type, row) {
                        if (row.EVENT_TYPE == 0) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/sound.png" /></div> ` + data;
                        } else if (row.EVENT_TYPE == 6) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/microphone.png" /></div> `;
                        } else if (row.EVENT_TYPE == 2) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/settings.png" /></div> ` + data;
                        } else if (row.EVENT_TYPE == 1) {
                            return `<div class="avatar me-3">
                            <img alt="Logo" src="`+HOST_URL+`/assets/static/images/event/notepad.png" /></div> `;
                        }
                    }
                },

                {
                    targets: 1,
                    render: function (data, type, row) {
                        if (row.EVENT_TYPE == 0) {
                            return data;
                        } else if (row.EVENT_TYPE == 6) {
                            return TRAN_TRACK;
                        } else if (row.EVENT_TYPE == 2) {
                            return data;
                        } else if (row.EVENT_TYPE == 1) {
                            return TRAN_MARKER;
                        }
                    }
                },

                {
                    targets: 2,
                    render: function (data, type, row) {
                        if (data != 0) {
                            return getTimeFromMillis(data);
                        } else {
                            return "";
                        }
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {

                        if (row.EVENT_TYPE == 0) {
                            return data;
                        } else if (row.EVENT_TYPE == 6) {
                            return TRAN_VTRACK;
                        } else if (row.EVENT_TYPE == 2) {
                            return data;
                        } else if (row.EVENT_TYPE == 1) {
                            return TRAN_LNOTE;
                        }




                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (row.COUNT == 0) {
                            return TRAN_AUTO;
                        } else {
                            if (data == 0) {
                                return TRAN_PLAY;
                            } else if (data == 1) {
                                return TRAN_SEGUE;
                            } else {
                                return TRAN_STOP;
                            }

                        }
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return `<div class="btn-group mb-3" role="group">
                <a href="javascript:;" onclick="roworder('` + row.ID + `', '` + row.EVENT_NAME + `', '` + row.COUNT + `','1','1')" class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_MOVEUP + `"><i class="bi bi-arrow-up-circle"></i></a>
                <a href="javascript:;" onclick="roworder('` + row.ID + `', '` + row.EVENT_NAME + `', '` + row.COUNT + `','0', '1')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_MOVEDOWN + `"><i class="bi bi-arrow-down-circle"></i></a>                
            </div>`;
                    }
                },

                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var tranmen = '';
                        var roed = '';

                        if (row.COUNT != 0) {
                            tranmen = `<a href="javascript:;" onclick="imptrans('` + row.ID + `', '0', '` + row.TYPE + `')" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_PLAYTRANS + `"><i class="bi bi-play"></i></a>
                            <a href="javascript:;" onclick="imptrans('`+ row.ID + `', '1', '` + row.TYPE + `')" class="btn icon btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_SEGUETRANS + `"><i class="bi bi-arrows-angle-contract"></i></a>`;
                        }

                        if ((row.EVENT_TYPE != 0) && (row.EVENT_TYPE != 2)) {
                            roed = `<a href="javascript:;" onclick="edimp('` + row.ID + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_EDIT + `"><i class="bi bi-pencil"></i></a>`;
                        }


                        return `
                                    <div class="btn-group mb-3" role="group">
                                    `+ tranmen + `
                                    `+ roed + `
                                    <a href="javascript:;" onclick="removeimp('`+ row.ID + `', '` + row.TYPE + `','` + row.COUNT + `','` + row.EVENT_NAME + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVE + `"><i class="bi bi-x-square"></i></a>
                                </div>
                    `;
                    }
                },
            ],
        });
    }

    var initCartLibrary = function () {
        dt2 = $("#cartadd_table").DataTable({
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
                url: HOST_URL + "/tables/event-implibrarydata.php",
                data: function (d) {
                    d.ausr = USERNAME;
                    d.all = allgroups;
                    d.groups = groupnow;
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
                        <a href="javascript:;" onclick="addcarttoimp('`+ row.NUMBER + `', '` + row.TYPE + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
                    }
                },
            ],
        });

    }

    const element1 = document.getElementById('cart_select');
    const modal1 = new bootstrap.Modal(element1);

    var initSelCartModalButtons = function () { 
        const cancelButton2 = element1.querySelector('[data-kt-cartsel-modal-action="cancel"]');
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
        const closeButton2 = element1.querySelector('[data-kt-cartsel-modal-action="close"]');
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

    const element2 = document.getElementById('add_vt');
    const modal2 = new bootstrap.Modal(element2);

    var initVTNoteModalButtons = function () { 
        const cancelButton2 = element2.querySelector('[data-kt-vtnote-modal-action="cancel"]');
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
        const closeButton2 = element2.querySelector('[data-kt-vtnote-modal-action="close"]');
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
            initPostDatatable();
            initCartLibrary();
            initSelCartModalButtons();
            initVTNoteModalButtons();
        }
    }

}();

$(document).ready(function () {

    KTDatatablesServerSide.init();

    if ($("#import_none").is(":checked")) {
        $('#impsched1').prop("disabled", true);
        $('#impsched2').prop("disabled", true);
        SelInlineBox.disable();
        SelGroupBox.disable();
        $('#artsep').prop("disabled", true);
        $('#titsep').prop("disabled", true);
        SelMusthaveBox.disable();
        SelMusthave2Box.disable();
        SelImportCartBox.disable();
    }

    if ($("#import_traffic").is(":checked")) {

        $("#impsched1").removeAttr('disabled');
        $("#impsched2").removeAttr('disabled');
        SelImportCartBox.enable();
        SelInlineBox.disable();
        SelGroupBox.disable();
        $('#artsep').prop("disabled", true);
        $('#titsep').prop("disabled", true);
        SelMusthaveBox.disable();
        SelMusthave2Box.disable();
    }

    if ($("#import_music").is(":checked")) {

        $("#impsched1").removeAttr('disabled');
        $("#impsched2").removeAttr('disabled');
        SelImportCartBox.enable();
        SelInlineBox.enable();
        SelGroupBox.disable();
        $('#artsep').prop("disabled", true);
        $('#titsep').prop("disabled", true);
        SelMusthaveBox.disable();
        SelMusthave2Box.disable();
    }

    if ($("#import_select").is(":checked")) {

        $('#impsched1').prop("disabled", true);
        $('#impsched2').prop("disabled", true);
        SelImportCartBox.disable();
        SelInlineBox.disable();
        SelGroupBox.enable();
        $("#artsep").removeAttr('disabled');
        $("#titsep").removeAttr('disabled');
        SelMusthaveBox.enable();
        SelMusthave2Box.enable();
    }

});


$('#import_none').click(function () {

    if ($("#import_none").is(":checked")) {
        $('#impsched1').prop("disabled", true);
        $('#impsched2').prop("disabled", true);
        SelInlineBox.disable();
        SelGroupBox.disable();
        $('#artsep').prop("disabled", true);
        $('#titsep').prop("disabled", true);
        SelMusthaveBox.disable();
        SelMusthave2Box.disable();
        SelImportCartBox.disable();
    }

});

$('#import_traffic').click(function () {

    if ($("#import_traffic").is(":checked")) {

        $("#impsched1").removeAttr('disabled');
        $("#impsched2").removeAttr('disabled');
        SelImportCartBox.enable();
        SelInlineBox.disable();
        SelGroupBox.disable();
        $('#artsep').prop("disabled", true);
        $('#titsep').prop("disabled", true);
        SelMusthaveBox.disable();
        SelMusthave2Box.disable();
    }

});

$('#import_music').click(function () {

    if ($("#import_music").is(":checked")) {

        $("#impsched1").removeAttr('disabled');
        $("#impsched2").removeAttr('disabled');
        SelImportCartBox.enable();
        SelInlineBox.enable();
        SelGroupBox.disable();
        $('#artsep').prop("disabled", true);
        $('#titsep').prop("disabled", true);
        SelMusthaveBox.disable();
        SelMusthave2Box.disable();
    }

});

$('#import_select').click(function () {

    if ($("#import_select").is(":checked")) {

        $('#impsched1').prop("disabled", true);
        $('#impsched2').prop("disabled", true);
        SelImportCartBox.disable();
        SelInlineBox.disable();
        SelGroupBox.enable();
        $("#artsep").removeAttr('disabled');
        $("#titsep").removeAttr('disabled');
        SelMusthaveBox.enable();
        SelMusthave2Box.enable();
    }

});

$('#autofill').click(function () {

    if ($("#autofill").is(":checked")) {
        $("#warnoverunder").removeAttr('disabled');
        if ($("#warnoverunder").is(":checked")) {
            $("#byleast").removeAttr('disabled');
        } else {
            $('#byleast').prop("disabled", true);
        }
    } else {
        $('#warnoverunder').prop("disabled", true);
    }

});

$('#warnoverunder').click(function () {
    if ($("#warnoverunder").is(":checked")) {
        $("#byleast").removeAttr('disabled');
    } else {
        $('#byleast').prop("disabled", true);
    }
});

$('#hard_wait').click(function () {
    if ($("#hard_wait").is(":checked")) {
        $("#waitupto").removeAttr('disabled');
    } else {
        $('#waitupto').prop("disabled", true);
    }
});

$('#hard_next').click(function () {
    if ($("#hard_next").is(":checked")) {
        $('#waitupto').prop("disabled", true);
    }
});

$('#hard_select_im').click(function () {
    if ($("#hard_select_im").is(":checked")) {
        $('#waitupto').prop("disabled", true);
    }
});

$('#hardtime').click(function () {
    if ($("#hardtime").is(":checked")) {
        $('#cueevent').prop("disabled", true);
        $('#schedstart').prop("disabled", true);
        $("#hard_select_im").removeAttr('disabled');
        $("#hard_next").removeAttr('disabled');
        $("#hard_wait").removeAttr('disabled');
    } else {
        $('#hard_select_im').prop("disabled", true);
        $('#hard_next').prop("disabled", true);
        $('#hard_wait').prop("disabled", true);
        $('#waitupto').prop("disabled", true);
        $("#cueevent").removeAttr('disabled');
    }
});

$('#cueevent').click(function () {
    if ($("#cueevent").is(":checked")) {
        $("#schedstart").removeAttr('disabled');
        $('#hardtime').prop("disabled", true);
        $('#hard_select_im').prop("disabled", true);
        $('#hard_next').prop("disabled", true);
        $('#hard_wait').prop("disabled", true);
        $('#waitupto').prop("disabled", true);
    } else {
        $('#schedstart').prop("disabled", true);
        $("#hardtime").removeAttr('disabled');
    }

});