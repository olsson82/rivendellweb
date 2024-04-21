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
var groupnow;
var allgroups = 1;
var editids_arr = [];

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

$("#checkall").on("click", function (e) {
    if ($(this).is(":checked")) {
        dt.rows().select();
        $(".checked-rows-table-check").prop("checked", true);
    } else {
        dt.rows().deselect();
        $(".checked-rows-table-check").prop("checked", false);
    }
});


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

$('#selectGroup').change(function () {
    if ($("#selectGroup").val() == "allgroups") {
        allgroups = 1;
        Cookies.set('groupsel', '1', { expires: 7 });
        dt.ajax.reload();
    } else {
        allgroups = 0;
        groupnow = $("#selectGroup").val();
        Cookies.set('groupsel', groupnow, { expires: 7 });
        dt.ajax.reload();
    }
});


const groupselbox = document.getElementById('selectGroup');
selgrouplibrary = new Choices(groupselbox, {
    noResultsText: TRAN_SELECTNORESULTS,
    noChoicesText: TRAN_SELECTNOOPTIONS,
    itemSelectText: TRAN_SELECTPRESSSELECT,
});

if (Cookies.get('groupsel') === undefined || Cookies.get('groupsel') === null) {
    allgroups = 1;
    Cookies.set('groupsel', '1', { expires: 7 });
    selgrouplibrary.setChoiceByValue("allgroups");
}


if (Cookies.get('groupsel') == 1) {
    allgroups = 1;
    selgrouplibrary.setChoiceByValue("allgroups");
} else if (Cookies.get('groupsel') != 1) {
    allgroups = 0;
    groupnow = Cookies.get('groupsel');
    selgrouplibrary.setChoiceByValue(groupnow);
}


Dropzone.autoDiscover = false;
var myDropzone = new Dropzone("div#dropzone_upload", {
    url: HOST_URL + "/forms/library/chunk-upload.php",
    parallelUploads: 1,
    parallelChunkUploads: true,
    retryChunks: true,
    retryChunksLimit: 3,
    forceChunking: true,
    chunkSize: 1000000,
    maxFiles: 200,
    chunking: true,
    acceptedFiles: ".mp3,.wav",
    maxFilesize: 500,
    chunksUploaded: function (file, done) {
        let currentFile = file;
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/library/chunk-import.php',
            data: {
                dzuuid: currentFile.upload.uuid,
                dztotalchunkcount: currentFile.upload.totalChunkCount,
                fileName: currentFile.name.substr((currentFile.name.lastIndexOf('.') + 1)),
                audiochannels: $('#audiochannels').val(),
                autotrim: $('#autotrim').val(),
                trimlevel: $('#trimlevel').val(),
                normalize: $('#normalize').val(),
                normalizelevel: $('#normalizelevel').val(),
                musicgroup: $('#musicgroup').val(),
                schedcodes: $('#schedcodes').val(),
            },
            datatype: 'html',
            success: function (data) {
                myDropzone.removeFile(file);
                dt.ajax.reload();
            },
            error: function (msg) {
                currentFile.accepted = false;
                myDropzone._errorProcessing([currentFile], msg.responseText);
            }
        });
    },
});

function delCart(id) {
    var trans = tr('REMOVECART {{' + id + '}}');
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
                    url: HOST_URL + '/forms/library/delmusic.php',
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

                        } else if (fel == "true" && kod == '2') {

                            var trans2 = tr('BELONGTOLOG {{' + logname + '}}');
                            Swal.fire({
                                text: trans2,
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
                });


            } else if (result.dismiss === 'cancel') {
                var trans3 = tr('NOTDELETED {{' + id + '}}');
                Swal.fire({
                    text: trans3,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: TRAN_OK,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                });
            }
        });
    }
}

var KTDatatablesServerSide = function () {
    var initDatatable = function () {
        dt = $("#library_table").DataTable({
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
                url: HOST_URL + "/tables/library-table.php",
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
                    data: 'NUMBER'
                },
                {
                    data: 'GROUP_NAME'
                },
                {
                    data: 'TITLE'
                },
                {
                    data: 'ARTIST'
                },
                {
                    data: 'AVERAGE_LENGTH'
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

                        if (typeof (row.OWNER) != "undefined" && row.OWNER != null) {

                            if (ALLOW_MOD == 1) {

                                return data;

                            } else {
                                return data;
                            }

                        } else {
                            if (ALLOW_MOD == 1) {

                                if (row.TYPE == 2) {
                                    return '<a href="'+HOST_URL +'/library/carts/cart/macro/' + row.NUMBER + '" class="text-gray-800 text-hover-primary mb-1">' + data + '</a>';
                                } else {

                                    return '<a href="'+HOST_URL +'/library/carts/cart/audio/' + row.NUMBER + '" class="text-gray-800 text-hover-primary mb-1">' + data + '</a>';
                                }

                            } else {
                                return data;
                            }
                        }


                    }
                },

                {
                    targets: 2,
                    render: function (data, type, row) {

                        return '<P style="color:' + row.COLOR + '">' + data + '</p>';

                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return getTimeFromMillis(data);
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return data;
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return data;
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        var URL;
                        if (typeof (row.OWNER) != "undefined" && row.OWNER != null) {
                            if (ALLOW_MOD == 1) {

                                URL = 'javascript:;';

                            } else {
                                URL = 'javascript:;';
                            }
                        } else {
                            if (ALLOW_MOD == 1) {

                                if (row.TYPE == 2) {
                                    URL = HOST_URL +'/library/carts/cart/macro/' + row.NUMBER;
                                } else {

                                    URL = HOST_URL +'/library/carts/cart/audio/' + row.NUMBER;
                                }

                            } else {
                                URL = 'javascript:;';
                            }
                        }
                        return `
                        <div class="btn-group mb-3" role="group">
                                    <a href="`+ URL + `" class="btn icon btn-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="javascript:;" onclick="delCart(` + row.NUMBER + `)" class="btn icon btn-danger"><i class="bi bi-x-square"></i></a>
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
        const container = document.querySelector('#library_table');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const deleteSelected = document.querySelector('[data-kt-library-table-select="delete_selected"]');
        const editSelected = document.querySelector('[data-kt-library-table-select="edit_selected"]');

        checkboxes.forEach(c => {
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        editSelected.addEventListener('click', function () {

            if (ALLOW_MOD == 0) {
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
                editids_arr = [];
                $("input:checkbox[name=deletethis]:checked").each(function () {
                    editids_arr.push($(this).val());
                });
                if (editids_arr.length > 0) {
                    $('#multi_edit').modal('show');
                } else {
                    Swal.fire({
                        text: TRAN_SELECTTOMULTIEDIT,
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
                                        if (kod == '1') {
                                            Swal.fire({
                                                text: TRAN_SOMECARTSNOTDELETED,
                                                icon: "error",
                                                buttonsStyling: false,
                                                confirmButtonText: TRAN_OK,
                                                customClass: {
                                                    confirmButton: "btn fw-bold btn-primary"
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                text: TRAN_SOMEAREVOICETRACKS,
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
                                text: TRAN_DELETEMARKEDNOTSELECTED,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: TRAN_OK,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }


                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: TRAN_NONDELETE,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: TRAN_OK,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            }
                        });
                    }
                });
            }
        });
    }

    var toggleToolbars = function () {
        const container = document.querySelector('#library_table');
        const toolbarBase = document.querySelector('[data-kt-library-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-library-table-select="selected"]');
        const selectedCount = document.querySelector('[data-kt-library-table-select="selected_count"]');
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

    const element2 = document.getElementById('import_music');
    const modal2 = new bootstrap.Modal(element2);
    const element3 = document.getElementById('add_cart');
    const modal3 = new bootstrap.Modal(element3);
    const element4 = document.getElementById('multi_edit');
    const modal4 = new bootstrap.Modal(element4);

    var initImportModalButtons = function () {
        const cancelButton2 = element2.querySelector('[data-kt-import-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_YESCANCEL,
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
        const closeButton2 = element2.querySelector('[data-kt-import-modal-action="close"]');
        closeButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_YESCANCEL,
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

    var initAddModalButtons = function () {
        const cancelButton2 = element3.querySelector('[data-kt-add-modal-action="cancel"]');
        cancelButton2.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSEADDCART,
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
                text: TRAN_CLOSEADDCART,
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

    var initMultiModalButtons = function () {
        const cancelButton2 = element4.querySelector('[data-kt-multiedit-modal-action="cancel"]');
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
                    modal4.hide();
                }
            });
        });
        const closeButton2 = element4.querySelector('[data-kt-multiedit-modal-action="close"]');
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
            initImportModalButtons();
            initAddModalButtons();
            initMultiModalButtons();
        }
    }
}();


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

function msConversion(millis) {
    let sec = Math.floor(millis / 1000);
    let hrs = Math.floor(sec / 3600);
    sec -= hrs * 3600;
    let min = Math.floor(sec / 60);
    sec -= min * 60;

    sec = '' + sec;
    sec = ('00' + sec).substring(sec.length);

    if (hrs > 0) {
        min = '' + min;
        min = ('00' + min).substring(min.length);
        return hrs + ":" + min + ":" + sec;
    }
    else {
        return min + ":" + sec;
    }
}

$("#multieditSave").click(function () {
    $.ajax({
        url: HOST_URL + '/forms/library/multieditcart.php',
        type: 'post',
        data: {
            request: 2,
            edit_arr: editids_arr,
            title: $("#title").val(),
            artist: $("#artist").val(),
            year: $("#year").val(),
            songid: $("#songid").val(),
            album: $("#album").val(),
            record: $("#record").val(),
            client: $("#client").val(),
            agency: $("#agency").val(),
            publisher: $("#publisher").val(),
            composer: $("#composer").val(),
            conductor: $("#conductor").val(),
            usrdef: $("#usrdef").val(),
            usagecode: $("#usagecode").val(),
            group: $("#group").val(),
            schedcodes: $("#schedcodes").val(),
        },
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                $('#multi_edit').modal('hide');
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
});


$('#add_cart_form').validate({
    rules: {
        cartgroup: {
            required: true,
        },
        carttype: {
            required: true
        },
    },
    messages: {
        cartgroup: {
            required: TRAN_SELCARTGROUP
        },
        carttype: {
            required: TRAN_SELCARTTYPE
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
        var dataString = $('#add_cart_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/library/addcart.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var cart = mydata.cart;
                if (fel == "false") {
                    if ($("#carttype").val() == 'audio') {
                        location.href = HOST_URL + "/library/carts/cart/audio/" + cart;
                    } else {
                        location.href = HOST_URL + "/library/carts/cart/macro/" + cart;
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

KTDatatablesServerSide.init();