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

function renameEvent(name) {
    $("#neweventname").val(name);
    $("#oldname").val(name);
    $('#rename_event').modal('show');
}

function delEvent(id) {
    var trans = tr('REMEVENTCORR {{' + id + '}}');
    var trans2 = tr('EVINCLOCKNOTREM {{' + id + '}}');
    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/events/removecheck.php',
        data: {
            name: id
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "true") {
                Swal.fire({
                    text: trans2,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: TRAN_OK,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                });
            } else {
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
                            url: HOST_URL + '/forms/events/removeevent.php',
                            data: {
                                idet: id
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

var RenameEventForm = $('#renameevent_form').validate({
    rules: {
        name: {
            required: true,
            remote: HOST_URL + "/validation/checkeventnamenew.php",
        },
    },
    messages: {
        name: {
            required: TRAN_EVNAMENOTEMPTY
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
        var dataString = $('#renameevent_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/events/renameevent.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    RenameEventForm.resetForm();
                    dt.ajax.reload();
                    $('#rename_event').modal('hide');
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


var addEventForm = $('#addevent_form').validate({
    rules: {
        name: {
            required: true,
            remote: HOST_URL + "/validation/checkeventnamenew.php",
        },
    },
    messages: {
        name: {
            required: TRAN_EVNAMENOTEMPTY
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
        var dataString = $('#addevent_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/events/addevent.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var theeventname = mydata.eventname;
                if (fel == "false") {
                    location.href = HOST_URL + "/event.php?id=" + theeventname;
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
        dt = $("#events_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            select: {
                style: 'multi',
                selector: 'td:first-child input[type="checkbox"]',
                className: 'row-selected'
            },
            order: [
                [1, 'desc']
            ],
            stateSave: true,
            ajax: {
                url: HOST_URL + "/tables/events-table.php",
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
                    data: 'NAME'
                },
                {
                    data: 'NAME'
                },
                {
                    data: 'FIRST_TRANS_TYPE'
                },
                {
                    data: 'GRACE_TIME'
                },
                {
                    data: null
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `
                            <div class="form-check form-check-sm">
                                <input class="form-check-input checked-rows-table-check" name="deletethis" id="delcheck_${data}" type="checkbox" value="${data}" />
                            </div>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return '<a href="event.php?id=' + row.NAME + '" style="color:' + row.COLOR + ';" class="text-hover-primary mb-1">' + data + '</a>';
                    }
                },

                {
                    targets: 2,
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
                    targets: 3,
                    render: function (data, type, row) {
                        if (row.TIME_TYPE == 1) {
                            if (data == 0) {
                                return TRAN_TIMED + " " + TRAN_NOW;
                            } else if (data == -1) {
                                return TRAN_TIMED + " " + TRAN_NEXT;
                            } else {
                                return TRAN_TIMED + " " + TRAN_WAIT + " " + getTimeFromMillis(data);
                            }
                        } else if (row.IMPORT_SOURCE == 3) {
                            if (row.USE_AUTOFILL == "Y") {
                                return TRAN_FILL + " " + TRAN_SCHEDULER;
                            } else {
                                return TRAN_SCHEDULER;
                            }
                        } else if (row.IMPORT_SOURCE == 2) {
                            if (row.USE_AUTOFILL == "Y") {
                                return TRAN_FILL + " " + TRAN_MUSIC;
                            } else {
                                return TRAN_MUSIC;
                            }
                        } else if (row.IMPORT_SOURCE == 1) {
                            if (row.USE_AUTOFILL == "Y") {
                                return TRAN_FILL + " " + TRAN_TRAFFIC;
                            } else {
                                return TRAN_TRAFFIC;
                            }
                        } else if (row.IMPORT_SOURCE == 0) {
                            if (row.USE_AUTOFILL == "Y") {
                                return TRAN_FILL + " " + TRAN_NONE;
                            } else {
                                return TRAN_NONE;
                            }
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
                                    <a href="event.php?id=`+ row.NAME + `" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDITEVENT + `"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="renameEvent('` + row.NAME + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_RENAMEEVENT + `"><i class="bi bi-fonts"></i></a>
                                    <a href="javascript:;" onclick="delEvent('` + row.NAME + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVEEVENT + `"><i class="bi bi-x-square"></i></a>
                                </div>
                        `;
                    }
                },
            ],
        });
        dt.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
        });

    }

    var initToggleToolbar = function () {
        const container = document.querySelector('#events_table');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const deleteSelected = document.querySelector('[data-kt-events-table-select="delete_selected"]');
        checkboxes.forEach(c => { 
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        deleteSelected.addEventListener('click', function () {

            Swal.fire({
                text: TRAN_REMOVESELECTEDEVENTS,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                confirmButtonText: TRAN_YES,
                cancelButtonText: TRAN_NO,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    var deleteids_arr = [];
                    $("input:checkbox[name=deletethis]:checked").each(function () {

                        deleteids_arr.push($(this).val());

                    });
                    if (deleteids_arr.length > 0) {
                        $.ajax({
                            url: HOST_URL + '/forms/events/removemultipleevents.php',
                            type: 'post',
                            data: {
                                request: 2,
                                deleteids_arr: deleteids_arr
                            },
                            success: function (data) {
                                var mydata = $.parseJSON(data);
                                var fel = mydata.error;
                                var kod = mydata.errorcode;
                                if (fel == "false") {
                                    dt.ajax.reload();
                                } else {
                                    if (kod == '1') {
                                        Swal.fire({
                                            text: TRAN_SOMENOTREMOVEEVENTS,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: TRAN_OK,
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary"
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            text: TRAN_SOMEEVENTSINCLOCKS,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: TRAN_OK,
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary"
                                            }
                                        });
                                    }
                                }

                            }
                        });

                    } else {
                        Swal.fire({
                            text: TRAN_SELECTEVENTSFIRST,
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
        });
    }

    var toggleToolbars = function () { 
        const container = document.querySelector('#events_table');
        const toolbarBase = document.querySelector('[data-kt-events-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-events-table-select="selected"]');
        const selectedCount = document.querySelector('[data-kt-events-table-select="selected_count"]');
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');
        let checkedState = false;
        let count = 0;
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    const element1 = document.getElementById('add_event');
    const modal1 = new bootstrap.Modal(element1);

    var initAddEventButtons = function () { 
        const cancelButton2 = element1.querySelector('[data-kt-addevent-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEADDEVENTWINDOW,
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
        const closeButton2 = element1.querySelector('[data-kt-addevent-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEADDEVENTWINDOW,
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

    const element2 = document.getElementById('rename_event');
    const modal2 = new bootstrap.Modal(element2);

    var initRenameEventButtons = function () {
        const cancelButton2 = element2.querySelector('[data-kt-renameevent-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSERENAMEEVENTWINDOW,
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
        const closeButton2 = element2.querySelector('[data-kt-renameevent-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSERENAMEEVENTWINDOW,
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
            initToggleToolbar();
            toggleToolbars();
            initAddEventButtons();
            initRenameEventButtons();
        }
    }
}();

KTDatatablesServerSide.init();