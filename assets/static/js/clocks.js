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

$("#checkall").on("click", function (e) {
    if ($(this).is(":checked")) {
        dt.rows().select();
        $(".checked-rows-table-check").prop("checked", true);
    } else {
        dt.rows().deselect();
        $(".checked-rows-table-check").prop("checked", false);
    }
});

function rename(oldname) {
    $('#reoldname').val(oldname);
    $('#rename_clock').modal('show');
}

$('#rename_form').validate({
    rules: {
        name: {
            required: true,
            remote: HOST_URL + "/validation/checkclocknamenew.php",
            noSpace: true
        },
        
    
    },
    messages: {
        name: {
            required: TRAN_CLOCKNAMENOTEMPTY
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
            url: HOST_URL + '/forms/clocks/renameclock.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    $('#rename_clock').modal('hide');
                    dt.ajax.reload();
                } else {
                    if (kod == 1) {
                        Swal.fire({
                            text: TRAN_CLOCKNAMEEXIST,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 2) {
                        Swal.fire({
                            text: TRAN_CLOCKRULESNOTPOSS,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 3) {
                        Swal.fire({
                            text: TRAN_CLOCKLINESNOTPOSS,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 4) {
                        Swal.fire({
                            text: TRAN_CLOCKSERVICESNOTPOSS,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 5) {
                        Swal.fire({
                            text: TRAN_CLOCKNAMENOTPOSSCHANGE,
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

$('#add_form').validate({
    rules: {
        name: {
            required: true,
            remote: HOST_URL + "/validation/checkclocknamenew.php",
            noSpace: true
        },
        ccode: {
            required: true,
            remote: HOST_URL + "/validation/checkclockcodenew.php",
            noSpace: true,
            maxlength: 3,
        },
        colors: {
            required: true,
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
        colors: {
            required: TRAN_COLORREQ
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
            url: HOST_URL + '/forms/clocks/addclock.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var theclockname = mydata.clockname;
                if (fel == "false") {
                    location.href = HOST_URL + "/manager/clock/"+theclockname;
                } else {
                    if (kod == 1) {
                        Swal.fire({
                            text: TRAN_CLOCKNAMEEXIST,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });                        
                    } else if(kod == 2) {
                        Swal.fire({
                            text: TRAN_CLOCKCODEEXIST,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 3) {
                        Swal.fire({
                            text: TRAN_CLOCKNOTPOSSIBLE,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 4) {
                        Swal.fire({
                            text: TRAN_CLOCKSERVNOTPOSSIBLE,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else if(kod == 5) {
                        Swal.fire({
                            text: TRAN_CLOCKRULENOTPOSSIBLE,
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

function delClock(id) {
    var trans = tr('ABOUTREMOVECLOCK {{' + id + '}}');
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
                url: HOST_URL + '/forms/clocks/removeclock.php',
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



var KTDatatablesServerSide = function () {
    var initDatatable = function () {
        dt = $("#clocks_table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
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
            serverMethod: 'post',
            ajax: {
                url: HOST_URL + "/tables/clocks-data.php",
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
                    data: 'NAME'
                },
                {
                    data: 'NAME'
                },
                {
                    data: 'SHORT_NAME'
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


                        return '<a href="'+ HOST_URL +'/manager/clock/' + row.NAME + '" style="color:' + row.COLOR + ';" class="text-hover-primary mb-1">' + data + '</a>';




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
                                    <a href="`+ HOST_URL +`/manager/clock/`+ row.NAME + `" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDITCLOCK + `"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="rename('` + row.NAME + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_RENAMECLOCK + `"><i class="bi bi-fonts"></i></a>
                                    <a href="javascript:;" onclick="delClock('` + row.NAME + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_REMOVECLOCK + `"><i class="bi bi-x-square"></i></a>
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
        const container = document.querySelector('#clocks_table');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const deleteSelected = document.querySelector('[data-kt-clocks-table-select="delete_selected"]');
        checkboxes.forEach(c => { 
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        deleteSelected.addEventListener('click', function () {
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
                            url: HOST_URL + '/forms/clocks/delmultipleclocks.php',
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
                                }

                            }
                        });

                    } else {
                        Swal.fire({
                            text: TRAN_DELETEMARKEDNOTSELECTED,
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
        const container = document.querySelector('#clocks_table');
        const toolbarBase = document.querySelector('[data-kt-clocks-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-clocks-table-select="selected"]');
        const selectedCount = document.querySelector('[data-kt-clocks-table-select="selected_count"]');
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

const element3 = document.getElementById('add_clock');
const modal3 = new bootstrap.Modal(element3);

var initAddClockButtons = function () { 
    const cancelButton2 = element3.querySelector('[data-kt-clockadd-modal-action="cancel"]');
    cancelButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSEADDCLOCK,
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
    const closeButton2 = element3.querySelector('[data-kt-clockadd-modal-action="close"]');
    closeButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSEADDCLOCK,
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

const element4 = document.getElementById('rename_clock');
const modal4 = new bootstrap.Modal(element4);

var initRenameClockButtons = function () {
    const cancelButton2 = element4.querySelector('[data-kt-clockrename-modal-action="cancel"]');
    cancelButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSERENAMECLOCKWINDOW,
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
    const closeButton2 = element4.querySelector('[data-kt-clockrename-modal-action="close"]');
    closeButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSERENAMECLOCKWINDOW,
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


    return {
        init: function () {
            initDatatable();
            initToggleToolbar();
            toggleToolbars();
            initAddClockButtons();
            initRenameClockButtons();


        }
    }
}();

KTDatatablesServerSide.init();