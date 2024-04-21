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

const groupBox = document.getElementById('activegroups');
const SelGroupBox = new Choices(groupBox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

const serviceBox = document.getElementById('activeservice');
const SelServiceBox = new Choices(serviceBox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

function editperms(user) {
    $.ajax({
        url: HOST_URL + '/forms/loaduserservice.php',
        data: "id=" + user,
        dataType: 'json',
        success: function (data) {
            var i;
            SelServiceBox.removeActiveItems();
            for (i = 0; i < data.length; i++) {
                SelServiceBox.setChoiceByValue(data[i]);
            }
        }
    });

    $.ajax({
        url: HOST_URL + '/forms/loadusergroup.php',
        data: "id=" + user,
        dataType: 'json',
        success: function (data) {
            var i;
            SelGroupBox.removeActiveItems();
            for (i = 0; i < data.length; i++) {
                SelGroupBox.setChoiceByValue(data[i]);
            }
        }
    });
    $('#userid').val(user);
    $("#permission_window").modal("show");
}

function editRights(user) {
    $.ajax({
        url: HOST_URL + '/forms/loaduser.php',
        data: "id=" + user,
        dataType: 'json',
        success: function (data) {
            $('#theuser').val(user);
            $('#createcarts').val(data['CREATE_CARTS_PRIV']);
            $('#modifycarts').val(data['MODIFY_CARTS_PRIV']);
            $('#deletecarts').val(data['DELETE_CARTS_PRIV']);  
            $('#editaudio').val(data['EDIT_AUDIO_PRIV']);  
            $('#deditnetcatch').val(data['EDIT_CATCHES_PRIV']);  
            $('#voicetracklogs').val(data['VOICETRACK_LOG_PRIV']);  
            $('#allowweb').val(data['WEBGET_LOGIN_PRIV']);  
            $('#createlog').val(data['CREATE_LOG_PRIV']); 
            $('#deletelog').val(data['DELETE_LOG_PRIV']); 
            $('#modifytemp').val(data['MODIFY_TEMPLATE_PRIV']); 
            $('#delreportdata').val(data['DELETE_REC_PRIV']); 
            $('#playoutlogs').val(data['PLAYOUT_LOG_PRIV']); 
            $('#addlogitems').val(data['ADDTO_LOG_PRIV']); 
            $('#rearrlogitems').val(data['ARRANGE_LOG_PRIV']); 
            $('#dellogitems').val(data['REMOVEFROM_LOG_PRIV']);  
            $('#confsyspanel').val(data['CONFIG_PANELS_PRIV']);  
            $('#createpodcast').val(data['ADD_PODCAST_PRIV']);  
            $('#editpodcast').val(data['EDIT_PODCAST_PRIV']);  
            $('#delpodcast').val(data['DELETE_PODCAST_PRIV']);   
            $('#allweblog').val(data['ENABLE_WEB']);
            $("#user_window").modal("show");                
        }
    });
}

var AddForm = $('#add_form').validate({
    rules: {
        user_name: {
            required: true,
            remote: HOST_URL + "/validation/checkusername.php",
        },
        fullname: {
            required: true,
        },
        email: {
            required: true,
            remote: HOST_URL + "/validation/checkemail.php",
            email: true
        },
    },
    messages: {
        user_name: {
            required: TRAN_NOTBEEMPTY
        },
        fullname: {
            required: TRAN_NOTBEEMPTY
        },
        email: {
            required: TRAN_NOTBEEMPTY,
            email: TRAN_CORREMAILNEEDS
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
            url: HOST_URL + '/forms/adduser.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    AddForm.resetForm();
                    dt.ajax.reload();
                    $('#add_window').modal('hide');
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

$('#user_form').validate({
    rules: {
        createcarts: {
            required: true,
        },
    },
    messages: {
        createcarts: {
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
        var dataString = $('#user_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/editrivrights.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    $('#user_window').modal('hide');
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
        dt = $("#users_table").DataTable({
            searchDelay: 500,
            processing: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            ajax: HOST_URL + "/tables/users-table.php",
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
                    data: 'LOGIN_NAME'
                },
                {
                    data: 'FULL_NAME'
                },
                {
                    data: 'EMAIL_ADDRESS'
                },
                {
                    data: 'PHONE_NUMBER'
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
                                    <a href="`+HOST_URL+`/admin/users/user/` + row.LOGIN_NAME + `" class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDITUSER + `"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="editRights('` + row.LOGIN_NAME + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_EDITRIVRIGHTS + `"><i class="bi bi-universal-access"></i></a>
                                    <a href="javascript:;" onclick="editperms('` + row.LOGIN_NAME + `')" class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="`+ TRAN_GROUPSERVICEPERMS + `"><i class="bi bi-database-lock"></i></a>
                                </div>
                        `;
                    }
                },
            ],
        });

    }

const element1 = document.getElementById('user_window');
const modal1 = new bootstrap.Modal(element1);

var initUserRightsModalButtons = function () {
    const cancelButton2 = element1.querySelector('[data-kt-user-modal-action="cancel"]');
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
    const closeButton2 = element1.querySelector('[data-kt-user-modal-action="close"]');
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

const element2 = document.getElementById('permission_window');
const modal2 = new bootstrap.Modal(element2);

var initPermsModalButtons = function () {
    const cancelButton2 = element2.querySelector('[data-kt-perms-modal-action="cancel"]');
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
    const closeButton2 = element2.querySelector('[data-kt-perms-modal-action="close"]');
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

const element3 = document.getElementById('add_window');
const modal3 = new bootstrap.Modal(element3);

var initPermsModalButtons = function () {
    const cancelButton2 = element3.querySelector('[data-kt-add-modal-action="cancel"]');
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
    const closeButton2 = element3.querySelector('[data-kt-add-modal-action="close"]');
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
            initDatatable();
            initUserRightsModalButtons();
            initPermsModalButtons();
        }
    }
}();

KTDatatablesServerSide.init();