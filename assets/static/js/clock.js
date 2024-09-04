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
var rulename;
var evcolor;
var transtype;
var evname;
var evid;
var doedit = 0;
var editmodal;

Inputmask({
    "mask": "99:99.9"
}).mask("#starttime");

Inputmask({
    "mask": "99:99.9"
}).mask("#endtime");

function tr(translate) {
    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/jstrans.php',
        data: {
            translate: translate
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            var translated = mydata.translated;
            if (fel == "false") {
                return translated;

            }
        }
    });
}

jQuery.validator.addMethod("noSpace", function(value, element) { 
    return value.indexOf(" ") < 0 && value != ""; 
  }, TRAN_NOSPACEALLOWED);

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
const orafter = document.getElementById('orafter');
joorafter = new Choices(orafter, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const orafter2 = document.getElementById('orafter2');
joorafter2 = new Choices(orafter2, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const notafter = document.getElementById('notafter');
jonotafter = new Choices(notafter, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const eventsel = document.getElementById('event');
eventselbox = new Choices(eventsel, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
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

function editshed(code, clock) {
    $.ajax({
        url: HOST_URL + '/forms/clock/loadschedrules.php',
        data: "id=" + code + "&clock=" + clock,
        dataType: 'json',
        success: function (data) {
            rulename = code;
            $('#ClockRuleLabel').html(TRAN_EDRULETIT + ' ' + code);
            $('#shedrulecode').val(code);
            $('#maxrow').val(data['MAX_ROW']);
            $('#minwait').val(data['MIN_WAIT']);
            jonotafter.removeActiveItems();
            joorafter.removeActiveItems();
            joorafter2.removeActiveItems();
            jonotafter.setChoiceByValue(data['NOT_AFTER']);
            joorafter.setChoiceByValue(data['OR_AFTER']);
            joorafter2.setChoiceByValue(data['OR_AFTER_II']);
        }
    });
}

function clone(event, edit) {
    if (edit == 1) {
        doedit = 1;
    } else {
        doedit = 0;
    }
    evid = event;
    $.ajax({
        url: HOST_URL + '/forms/clock/loadclockevent.php',
        data: "id=" + event,
        dataType: 'json',
        success: function (data) {
            if (doedit == 0) {
                var start = data['START_TIME'] + data['LENGTH'];
                var end = start + data['LENGTH'];
                eventselbox.setChoiceByValue(data['EVENT_NAME']);
                $('#starttime').val(getTimeFromMillis(start));
                $('#endtime').val(getTimeFromMillis(end));
                $('#addevent_clock').modal('show');
            } else {
                var end = data['START_TIME'] + data['LENGTH'];
                eventselbox.setChoiceByValue(data['EVENT_NAME']);
                $('#starttime').val(getTimeFromMillis(data['START_TIME']));
                $('#endtime').val(getTimeFromMillis(end));
                $('#addevent_clock').modal('show');
            }
        }
    });
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

function geteventdata(event) {
    $.ajax({
        url: HOST_URL + '/forms/clock/loadeventdata.php',
        data: "id=" + event,
        dataType: 'json',
        success: function (data) {

            evcolor = data['COLOR'];
            evname = data['NAME'];
            var trans = data['FIRST_TRANS_TYPE'];

            if (trans == 0) {
                transtype = TRAN_PLAY;
            } else if (trans == 1) {
                transtype = TRAN_SEGUE
            } else if (trans == 2) {
                transtype = TRAN_STOP
            }
        }
    });
}

function deleteevent(eventid) {
    Swal.fire({
        text: TRAN_REMOVEEVENT,
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
                url: HOST_URL + '/forms/clock/removeclockev.php',
                data: {
                    eventid: eventid,
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

function getlength(start, stop) {
    sum = stop - start;
    return sum;
}

$('#service_form').validate({
    rules: {
        'services[]': {
            required: true,
        },
        colors: {
            required: true,
        },
        ccode: {
            required: true,
            maxlength: 3,
            noSpace: true,
            remote: {
                url: HOST_URL + "/validation/checkclockcodeupdate.php",
                type: "post",
                data: {
                    oldclockcode: function () {
                        return $("#oldclockcode").val();
                    }
                }
            }
        },


    },
    messages: {
        'services[]': {
            required: TRAN_SERVICEREQUIRED
        },
        colors: {
            required: TRAN_COLORREQ
        },
        ccode: {
            required: TRAN_CLOCKCODEREQ,
            maxlength: TRAN_CLOCLCODEMAX3
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
        var dataString = $('#service_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/clock/addservices.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    $('#service_clock').modal('hide');
                } else {
                    if (kod == 1) {
                        Swal.fire({
                            text: TRAN_NOTPOSSIBLETOSAVESERVICECLOCK,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if (kod == 2) {
                        Swal.fire({
                            text: TRAN_NOTPOSSIBLECOLORCLOCK,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if (kod == 3) {
                        Swal.fire({
                            text: TRAN_NOTPOSSCLOCKSETT,
                            icon: "error",
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
            }
        });
    }
});

$('#saveas_form').validate({
    rules: {
        name: {
            required: true,
            remote: HOST_URL + "/validation/checkclocknamenew.php",
            noSpace: true
        },
        ccode: {
            required: true,
            remote: HOST_URL + "/validation/checkclockcodenew.php",
            maxlength: 3,
            noSpace: true
        },


    },
    messages: {
        name: {
            required: TRAN_CLOCKNAMENOTEMPTY
        },
        ccode: {
            required: TRAN_CLOCKCODEREQ,
            maxlength: TRAN_CLOCLCODEMAX3
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
        var dataString = $('#saveas_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/clock/saveasclock.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var theclockname = mydata.clockname;
                if (fel == "false") {
                    location.href = HOST_URL + "/manager/clock/" + theclockname;
                } else {
                    if (kod == 1) {
                        Swal.fire({
                            text: TRAN_NOTPOSSIBLESAVEASCLOCK,
                            icon: "error",
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
            }
        });
    }
});

$('#rule_form').validate({
    rules: {
        maxrow: {
            required: true,
        },
        minwait: {
            required: true,
        },


    },
    messages: {
        maxrow: {
            required: TRAN_NOTBEEMPTY
        },
        minwait: {
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
        var dataString = $('#rule_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/clock/editschedrules.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var theclockname = mydata.clockname;
                if (fel == "false") {
                    $("#" + rulename + "_MAX").html($("#maxrow").val());
                    $("#" + rulename + "_MIN").html($("#minwait").val());
                    if ($("#notafter").val() == 0) {
                        $("#" + rulename + "_NOTA").html("");
                    } else {
                        $("#" + rulename + "_NOTA").html($("#notafter").val());
                    }
                    if ($("#orafter").val() == 0) {
                        $("#" + rulename + "_OA").html("");
                    } else {
                        $("#" + rulename + "_OA").html($("#orafter").val());
                    }
                    if ($("#orafter2").val() == 0) {
                        $("#" + rulename + "_OA2").html("");
                    } else {
                        $("#" + rulename + "_OA2").html($("#orafter2").val());
                    }
                    editmodal.hide();
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

$.validator.addMethod("biggerThanField", function (value, element, param) {

    var val_a = $('#starttime').val();

    return this.optional(element)
        || (value > val_a);
}, TRAN_ENDSTARTNOTCLOCK);

$('#addevent_form').validate({
    rules: {
        event: {
            required: true,
        },
        starttime: {
            required: true,
            pattern: '([0-5][0-9]):([0-5][0-9])\.([0-9])',
            remote: {
                url: HOST_URL + '/validation/checkstart.php',
                type: "post",
                data: {
                    clockid: function () {
                        return $("#clockid").val();
                    },
                    timemillis: function () {
                        return getMillisFromTime($("#starttime").val());
                    },
                    edit: function () {
                        return doedit;
                    }
                }
            }
        },
        endtime: {
            required: true,
            pattern: '([0-5][0-9]):([0-5][0-9])\.([0-9])',
            biggerThanField: true
        },


    },
    messages: {
        event: {
            required: TRAN_SELCLOCKEVENT
        },
        starttime: {
            required: TRAN_SELCLOCKSTART,
            pattern: TRAN_CORRTIMEFORMATCLOCK
        },
        endtime: {
            required: TRAN_SELCLOCKEND,
            pattern: TRAN_CORRTIMEFORMATCLOCK
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
        $("#millisstart").val(getMillisFromTime($("#starttime").val()));
        $("#millisend").val(getMillisFromTime($("#endtime").val()));
        if (doedit == 1) {
            $("#eventid").val(evid);
            $("#editevent").val("1");
        } else {
            $("#editevent").val("0");
        }
        geteventdata($("#event").val());
        var dataString = $('#addevent_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/clock/addeventclock.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    dt.ajax.reload();
                    doedit = 0;
                    $("#starttime").val("");
                    $("#endtime").val("");
                    $("#addevent_form").trigger("reset");
                    $('#addevent_clock').modal('hide');
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


        dt = $("#clock_table").DataTable({
            processing: true,
            scrollY: 600,
            scrollCollapse: true,
            paging: false,
            dom: "<'table-responsive'tr>",
            ordering: false,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: {
                url: HOST_URL + "/tables/clock-table.php",
                data: function (d) {
                    d.clock = CLOCK_ID;
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
                    data: 'START_TIME'
                },
                {
                    data: 'LENGTH'
                },
                {
                    data: 'LENGTH'
                },
                {
                    data: 'FIRST_TRANS_TYPE'
                },
                {
                    data: 'EVENT_NAME'
                },
                {
                    data: null
                },
            ],
            columnDefs: [

                {
                    targets: 0,
                    render: function (data, type, row) {


                        return getTimeFromMillis(data);




                    }
                },

                {
                    targets: 1,
                    render: function (data, type, row) {
                        var returntime;

                        returntime = row.START_TIME + data;

                        return getTimeFromMillis(returntime);




                    }
                },



                {
                    targets: 2,
                    render: function (data, type, row) {


                        return getTimeFromMillis(data);




                    }
                },
                {
                    targets: 3,
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
                                    <a href="javascript:;" onclick="clone('`+ row.ID + `', 1)" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDITEVENT + `"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="clone('`+ row.ID + `', 0)" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_CLONEEVENT + `"><i class="bi bi-copy"></i></a>
                                    <a href="javascript:;" onclick="deleteevent('`+ row.ID + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVEEVENT + `"><i class="bi bi-x-square"></i></a>
                                </div>
                    `;
                    }
                },
            ],

            rowCallback: function (row, data, index) {
                $(row).find('td:eq(5)').css('background-color', data.COLOR);

            },

        });
    }

    const element1 = document.getElementById('saveas_clock');
    const modal1 = new bootstrap.Modal(element1);

    var initSaveAsClockButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-clocksave-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSESAVEASWINDOW,
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
        const closeButton2 = element1.querySelector('[data-kt-clocksave-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSESAVEASWINDOW,
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

    const element3 = document.getElementById('service_clock');
    const modal3 = new bootstrap.Modal(element3);

    var initServiceClockButtons = function () {
        const cancelButton2 = element3.querySelector('[data-kt-service-modal-action="cancel"]');
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
        const closeButton2 = element3.querySelector('[data-kt-service-modal-action="close"]');
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

    const element4 = document.getElementById('schedrules_clock');
    const modal4 = new bootstrap.Modal(element4);

    var initSchedulerClockButtons = function () {
        const cancelButton2 = element4.querySelector('[data-kt-scheduler-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSESCHEDULERWINDOW,
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
                }
            });
        });
        const closeButton2 = element4.querySelector('[data-kt-scheduler-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSESCHEDULERWINDOW,
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

                }
            });
        });
    }

    const element5 = document.getElementById('edit_sched');
    const modal5 = new bootstrap.Modal(element5);

    var initRuleClockButtons = function () {
        const cancelButton2 = element5.querySelector('[data-kt-rule-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEEDITRULEWINDOW,
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
        const closeButton2 = element5.querySelector('[data-kt-rule-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEEDITRULEWINDOW,
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

    const element6 = document.getElementById('addevent_clock');
    const modal6 = new bootstrap.Modal(element6);

    var initEventClockButtons = function () {
        const cancelButton2 = element6.querySelector('[data-kt-addevent-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEEVENTCLOCKWINDOW,
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
                    modal6.hide();
                }
            });
        });
        const closeButton2 = element6.querySelector('[data-kt-addevent-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEEVENTCLOCKWINDOW,
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
                    modal6.hide();

                }
            });
        });
    }

    return {
        init: function () {
            initDatatable();
            initSaveAsClockButtons();
            initServiceClockButtons();
            initSchedulerClockButtons();
            initRuleClockButtons();
            initEventClockButtons();


        }
    }

}();

KTDatatablesServerSide.init();