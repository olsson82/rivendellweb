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
            result  = mydata.translated;
        }
    });
    return result;
}

jQuery.validator.addMethod("noSpace", function(value, element) { 
    return value.indexOf(" ") < 0 && value != ""; 
  }, TRAN_NOSPACEALLOWED);


function checkLock(id) {
    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/logs/checklock.php',
        data: {
            id: id
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                location.href=HOST_URL + '/logedit/logs/log/'+id;          

           } else {
            Swal.fire({
                text: TRAN_LOGBEINGEDIT,
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

$("#checkall").on("click", function (e) {
    if ($(this).is(":checked")) {
        dt.rows().select();
        $(".checked-rows-table-check").prop("checked", true);
    } else {
        dt.rows().deselect();
        $(".checked-rows-table-check").prop("checked", false);
    }
});

function delLog(id) {
   var trans = tr('REMOVELOG {{' + id + '}}');
    if (ALLOW_DEL == 0) {
        Swal.fire({
            text: TRAN_NORIGHTS,
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
                    url: HOST_URL + '/forms/logs/dellog.php',
                    data: {
                        idet: id
                    },
                    datatype: 'html',
                    success: function (data) {
                        var mydata = $.parseJSON(data);
                        var fel = mydata.error;
                        var kod = mydata.errorcode;
                        var logname = mydata.logname;
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

var addLogForm = $('#addlog_form').validate({
    rules: {
        logname: {
            required: true,
            remote: HOST_URL + "/validation/checknewlogname.php",
            noSpace: true
        },


    },
    messages: {
        logname: {
            required: TRAN_LOGNAMENOTEMPTY
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
        var dataString = $('#addlog_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/addlog.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var thelogname = mydata.logname;
                if (fel == "false") {
                    dt.ajax.reload();
                    addLogForm.resetForm();
                    $('#add_log').modal('hide');
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
        dt = $("#logs_table").DataTable({
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
                url: HOST_URL + "/tables/logs-table.php",
                data: function (d) {
                    d.servicename = SERVICENAME;
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
                    data: 'name'
                },
                {
                    data: 'name'
                },
                {
                    data: 'description'
                },
                {
                    data: 'auto_refresh'
                },
                {
                    data: 'music_merged'
                },
                {
                    data: 'traffic_merged'
                },
                {
                    data: 'scheduled'
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

                        if (row.scheduled > 0 && row.completed < row.scheduled) {
                            return `<a href="javascript:;" onclick="checkLock('` + row.name + `')" class="text-danger">` + data + `</a>`;
                        } else {
                            return `<a href="javascript:;" onclick="checkLock('` + row.name + `')" class="text-success">` + data + `</a>`;
                        }



                    }
                },

                {
                    targets: 6,
                    render: function (data, type, row) {

                        if (row.scheduled > 0 && row.completed < row.scheduled) {
                            return '<span class="badge bg-danger">' + row.completed + '/' + row.scheduled + '</span>';
                        } else if (row.scheduled > 0 && row.scheduled == row.completed) {
                            return '<span class="badge bg-success">' + row.completed + '/' + row.scheduled + '</span>';
                        } else {
                            return '<span class="badge bg-primary">' + row.completed + '/' + row.scheduled + '</span>';
                        }

                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var vtrack;
                        if (ALLOW_VOICE == 0) {
                            vtrack = 'javascript:;';
                        } else {
                            vtrack = HOST_URL+ '/logedit/logs/voicetrack/' + row.name;
                        }
                        return `
                        <div class="btn-group mb-3" role="group">
                                    <a href="`+ vtrack + `" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_VOICETRACKER + `"><i class="bi bi-mic"></i></a>
                                    <a href="javascript:;" onclick="delLog('` + row.name + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVELOG + `"><i class="bi bi-x-square"></i></a>
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
        const container = document.querySelector('#logs_table');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const deleteSelected = document.querySelector('[data-kt-logs-table-select="delete_selected"]');
        checkboxes.forEach(c => { 
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        deleteSelected.addEventListener('click', function () { 

            if (ALLOW_DEL == 0) {
                Swal.fire({
                    text: TRAN_NORIGHTS,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: TRAN_OK,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                });
            } else {

                Swal.fire({
                    text: TRAN_DELETEMARKED,
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
                                url: HOST_URL + '/forms/library/delmultiplemusic.php',
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
                                        Swal.fire({
                                            text: TRAN_MARKEDDELETED,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: TRAN_OK,
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then(function (result) {
                                            if (result.isConfirmed) { 
                                                dt.ajax.reload();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            text: TRAN_DELMARKEDLOGSNOT,
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

                        } else {
                            Swal.fire({
                                text: TRAN_DELMARKEDLOGSNOTSEL,
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
            }
        });
    }

    var toggleToolbars = function () {
        const container = document.querySelector('#logs_table');
        const toolbarBase = document.querySelector('[data-kt-logs-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-logs-table-select="selected"]');
        const selectedCount = document.querySelector('[data-kt-logs-table-select="selected_count"]');
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

    const element1 = document.getElementById('add_log');
    const modal1 = new bootstrap.Modal(element1);

    var initAddLogButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-addlog-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEADDLOG,
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

        const closeButton2 = element1.querySelector('[data-kt-addlog-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEADDLOG,
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
            initToggleToolbar();
            toggleToolbars();
            initAddLogButtons();
        }
    }
}();



KTDatatablesServerSide.init();