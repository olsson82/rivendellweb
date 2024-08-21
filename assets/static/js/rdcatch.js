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
var dt3;
var allgroups = 1;
var groupnow;
var editmodal;
var cartdata;
var librarytype = 2;
var catchtype;
var sampleOne = ["32000", "44100", "48000"];
var sampleTwo = ["16000", "22050", "32000", "44100", "48000"];
var bitOne = ["32", "48", "56", "64", "80", "96", "112", "128", "160", "192", "224", "256", "320", "384"];
var bitTwo = ["32", "40", "48", "56", "64", "80", "96", "112", "128", "160", "192", "224", "256", "320", "VBR"];

$('#for_format').change(function () {
    var selectedCategory = $('#for_format').val();
    if (selectedCategory == '5') {
        $('#for_quality').removeAttr('disabled');
        $("#for_quality").attr({
            "max": 10,
            "min": -1
        });
    } else {
        $('#for_quality').attr('disabled', 'disabled');
    }
    if (selectedCategory != "") {

        $('#for_samplerate').find('option').remove();
        $('#for_bitrate').find('option').remove();

        var sizeList = [];

        if (selectedCategory == '2') {
            $('#for_bitrate').removeAttr('disabled');
            for (var i = 0; i <= sampleTwo.length; i++) {
                var sampTwo = sampleTwo[i];
                $('#for_samplerate').append($("<option></option>").attr("value", sampTwo).text(sampTwo));
            }
            for (var i = 0; i <= bitOne.length; i++) {
                var biOne = bitOne[i];
                $('#for_bitrate').append($("<option></option>").attr("value", biOne).text(biOne));
            }
        }
        else {
            if (selectedCategory == '3') {
                $('#for_bitrate').removeAttr('disabled');
                for (var i = 0; i <= bitTwo.length; i++) {
                    var biTwo = bitTwo[i];
                    $('#for_bitrate').append($("<option></option>").attr("value", biTwo).text(biTwo));
                }
            } else {
                $('#for_bitrate').attr('disabled', 'disabled');
            }

            for (var i = 0; i <= sampleOne.length; i++) {
                var sampOne = sampleOne[i];
                $('#for_samplerate').append($("<option></option>").attr("value", sampOne).text(sampOne));
            }
        }
    } else {

        $("#for_bitrate").empty();
        $('#for_bitrate').append($("<option></option>").attr("value", "").text(TRAN_SELECTBITRATE));
        $("#for_samplerate").empty();
        $('#for_samplerate').append($("<option></option>").attr("value", "").text(TRAN_SELECTSAMPLERATE));
    }
});

$('#for_bitrate').change(function () {
    var selectedBitrate = $('#for_bitrate').val();
    if (selectedBitrate == 'VBR') {
        $('#for_quality').removeAttr('disabled');
        $("#for_quality").attr({
            "max": 9,
            "min": 0
        });
    } else {
        $('#for_quality').attr('disabled', 'disabled');
    }
});

$('#url_down').keyup(function () {
    let url = $("#url_down").val();
    const okUrl = url.startsWith('http:') || url.startsWith('ftp:') || url.startsWith('sftp:') || url.startsWith('file:')

    if (okUrl == true) {
        $("#usrn_down").removeAttr('disabled');
        $("#pass_down").removeAttr('disabled');
    } else {
        $("#usrn_down").prop("disabled", true);
        $("#pass_down").prop("disabled", true);
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

function addcart(cart, title, type) {
    if (librarytype == 2) {
        editmodal.hide();
        $("#cart_macro").val(cart);
        $("#desc_macro").val(title);
    } else {
        editmodal.hide();
        cartdata = cart;
        dt3.ajax.reload();

        $.ajax({
            url: HOST_URL + '/forms/rdcatch/getcut.php',
            data: "id=" + cartdata,
            dataType: 'json',
            success: function (data) {
                if (type == 4) {
                    $('#dest_down').val(data['CUT_NAME']);
                } else if (type == 5) {
                    $('#source_upload').val(data['CUT_NAME']);
                }
            }
        });
        if (type == 4) {
            $("#selcutbutt").show();
            $("#selcartbutt").hide();
        } else if (type == 5) {
            $("#selcutbutt_up").show();
            $("#selcartbutt_up").hide();
        }

    }
}

function addcut(cutname, type) {
    editmodal.hide();
    if (type == 4) {
        $("#dest_down").val(cutname);
        $("#selcutbutt").hide();
        $("#selcartbutt").show();
    } else if (type == 5) {
        $("#source_upload").val(cutname);
        $("#selcutbutt_up").hide();
        $("#selcartbutt_up").show();
    }
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
    catchtype = type;
    $.ajax({
        url: HOST_URL + '/forms/rdcatch/getdata.php',
        data: "id=" + id,
        dataType: 'json',
        success: function (data) {
            if (type == 1) {
                librarytype = 2;
                dt2.ajax.reload();
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
            } else if (type == 4) {
                librarytype = 1;
                dt2.ajax.reload();
                $("#selcutbutt").hide();
                $("#selcartbutt").show();
                $('#dowid').val(id);
                if (data['IS_ACTIVE'] == 'Y') {
                    $("#eventactive_down").prop('checked', true);
                } else {
                    $("#eventactive_down").prop('checked', false);
                }
                $('#location_down').val(data['STATION_NAME']);
                $('#start_down').val(data['START_TIME']);
                $('#desc_down').val(data['DESCRIPTION']);
                $('#url_down').val(data['URL']);
                $('#usrn_down').val(data['URL_USERNAME']);
                $('#pass_down').val(data['URL_PASSWORD']);
                $('#filpa_down').val(data['URL_PASSWORD']);
                $('#dest_down').val(data['CUT_NAME']);
                $('#channels_down').val(data['CHANNELS']);
                if (data['TRIM_THRESHOLD'] == '0') {
                    $("#autotrim_down").prop('checked', false);
                    $('#trimlevel_down').val('-35');
                    $('#trimlevel_down').prop("disabled", true);
                } else {
                    $("#autotrim_down").prop('checked', true);
                    $("#trimlevel_down").removeAttr('disabled');
                    //Find better solution to convert to correct value
                    var autotrimval = data['TRIM_THRESHOLD'] - (data['TRIM_THRESHOLD'] * 2);
                    autotrimval = autotrimval / 100;
                    $('#trimlevel_down').val(autotrimval);
                }
                if (data['NORMALIZE_LEVEL'] == '0') {
                    $("#normalize_down").prop('checked', false);
                    $('#normlevel_down').val('-35');
                    $('#normlevel_down').prop("disabled", true);
                } else {
                    $("#normalize_down").prop('checked', true);
                    $("#normlevel_down").removeAttr('disabled');
                    var normalizlevel = data['NORMALIZE_LEVEL'] / 100;
                    $('#normlevel_down').val(normalizlevel);
                }
                if (data['ENABLE_METADATA'] == 'Y') {
                    $("#updlib_down").prop('checked', true);
                } else {
                    $("#updlib_down").prop('checked', false);
                }
                if (data['ONE_SHOT'] == 'Y') {
                    $("#oneshot_down").prop('checked', true);
                } else {
                    $("#oneshot_down").prop('checked', false);
                }
                $('#dayoffset_down').val(data['EVENTDATE_OFFSET']);
                if (data['SUN'] == 'Y') {
                    $("#sun_dow").prop('checked', true);
                } else {
                    $("#sun_dow").prop('checked', false);
                }
                if (data['MON'] == 'Y') {
                    $("#mon_dow").prop('checked', true);
                } else {
                    $("#mon_dow").prop('checked', false);
                }
                if (data['TUE'] == 'Y') {
                    $("#tue_dow").prop('checked', true);
                } else {
                    $("#tue_dow").prop('checked', false);
                }
                if (data['WED'] == 'Y') {
                    $("#wed_dow").prop('checked', true);
                } else {
                    $("#wed_dow").prop('checked', false);
                }
                if (data['THU'] == 'Y') {
                    $("#thu_dow").prop('checked', true);
                } else {
                    $("#thu_dow").prop('checked', false);
                }
                if (data['FRI'] == 'Y') {
                    $("#fri_dow").prop('checked', true);
                } else {
                    $("#fri_dow").prop('checked', false);
                }
                if (data['SAT'] == 'Y') {
                    $("#sat_dow").prop('checked', true);
                } else {
                    $("#sat_dow").prop('checked', false);
                }

                let url = $("#url_down").val();
                const okUrl = url.startsWith('http:') || url.startsWith('ftp:') || url.startsWith('sftp:') || url.startsWith('file:')

                if (okUrl == true) {
                    $("#usrn_down").removeAttr('disabled');
                    $("#pass_down").removeAttr('disabled');
                } else {
                    $("#usrn_down").prop("disabled", true);
                    $("#pass_down").prop("disabled", true);
                }
                $('#download_edit').modal('show');

            } else if (type == 5) {
                librarytype = 1;
                dt2.ajax.reload();
                $("#selcutbutt_up").hide();
                $("#selcartbutt_up").show();
                $('#upid').val(id);

                if (data['IS_ACTIVE'] == 'Y') {
                    $("#eventactive_upload").prop('checked', true);
                } else {
                    $("#eventactive_upload").prop('checked', false);
                }
                $('#location_upload').val(data['STATION_NAME']);
                $('#start_upload').val(data['START_TIME']);
                $('#desc_upload').val(data['DESCRIPTION']);
                $('#feed_upload').val(data['FEED_ID']);
                $('#url_upload').val(data['URL']);
                $('#usrn_upload').val(data['URL_USERNAME']);
                $('#pass_upload').val(data['URL_PASSWORD']);
                $('#filpa_up').val(data['URL_PASSWORD']);
                $('#source_upload').val(data['CUT_NAME']);

                $('#for_format').val(data['FORMAT']);
                $('#for_format').trigger('change');
                $('#for_channels').val(data['CHANNELS']);
                $('#for_samplerate').val(data['SAMPRATE']);
                if (data['FORMAT'] == 3) {
                    
                    if (data['BITRATE'] == 0) {
                    $('#for_bitrate').val('VBR');
                    $('#for_quality').removeAttr('disabled');
                    } else {
                        $('#for_bitrate').val(data['BITRATE'] / 1000);
                        $('#for_quality').attr('disabled', 'disabled');
                    }
                } else if (data['FORMAT'] == 2) {
                    $('#for_bitrate').val(data['BITRATE'] / 1000);
                }
                $('#for_quality').val(data['QUALITY']);
                

                if (data['NORMALIZE_LEVEL'] == '0') {
                    $("#normalize_upload").prop('checked', false);
                    $('#normlevel_upload').val('-35');
                    $('#normalize_upload').prop("disabled", true);
                } else {
                    $("#normalize_upload").prop('checked', true);
                    $("#normalize_upload").removeAttr('disabled');
                    var normalizlevel = data['NORMALIZE_LEVEL'] / 100;
                    $('#normlevel_upload').val(normalizlevel);
                }
                if (data['ENABLE_METADATA'] == 'Y') {
                    $("#exportme_upload").prop('checked', true);
                } else {
                    $("#exportme_upload").prop('checked', false);
                }

                if (data['ONE_SHOT'] == 'Y') {
                    $("#oneshot_upload").prop('checked', true);
                } else {
                    $("#oneshot_upload").prop('checked', false);
                }
                $('#dayoffset_upload').val(data['EVENTDATE_OFFSET']);
                if (data['SUN'] == 'Y') {
                    $("#sun_up").prop('checked', true);
                } else {
                    $("#sun_up").prop('checked', false);
                }
                if (data['MON'] == 'Y') {
                    $("#mon_up").prop('checked', true);
                } else {
                    $("#mon_up").prop('checked', false);
                }
                if (data['TUE'] == 'Y') {
                    $("#tue_up").prop('checked', true);
                } else {
                    $("#tue_up").prop('checked', false);
                }
                if (data['WED'] == 'Y') {
                    $("#wed_up").prop('checked', true);
                } else {
                    $("#wed_up").prop('checked', false);
                }
                if (data['THU'] == 'Y') {
                    $("#thu_up").prop('checked', true);
                } else {
                    $("#thu_up").prop('checked', false);
                }
                if (data['FRI'] == 'Y') {
                    $("#fri_up").prop('checked', true);
                } else {
                    $("#fri_up").prop('checked', false);
                }
                if (data['SAT'] == 'Y') {
                    $("#sat_up").prop('checked', true);
                } else {
                    $("#sat_up").prop('checked', false);
                }

                if ($('#feed_upload').val() == '-1') {
                    $("#url_upload").removeAttr('disabled');
                    $("#usrn_upload").removeAttr('disabled');
                    $("#pass_upload").removeAttr('disabled');
                    $("#for_format").removeAttr('disabled');
                    $("#for_channels").removeAttr('disabled');
                    $("#for_samplerate").removeAttr('disabled');
                    $("#for_bitrate").removeAttr('disabled');
                    $("#for_quality").removeAttr('disabled');
                    $("#normalize_upload").removeAttr('disabled');
                    $("#normlevel_upload").removeAttr('disabled');
                    $("#exportme_upload").removeAttr('disabled');
                } else {
                    $('#url_upload').prop("disabled", true);
                    $('#usrn_upload').prop("disabled", true);
                    $('#pass_upload').prop("disabled", true);
                    $('#for_format').prop("disabled", true);
                    $('#for_channels').prop("disabled", true);
                    $('#for_samplerate').prop("disabled", true);
                    $('#for_bitrate').prop("disabled", true);
                    $('#for_quality').prop("disabled", true);
                    $('#normalize_upload').prop("disabled", true);
                    $('#normlevel_upload').prop("disabled", true);
                    $('#exportme_upload').prop("disabled", true);
                }


                $('#upload_edit').modal('show');
            }
        }
    });
}

$('#upload_form').validate({
    rules: {
        location: {
            required: true,
        },
        start: {
            required: true,
        },
        desc: {
            required: true,
        },
        feed: {
            required: true,
        },
        source: {
            required: true,
        },
    },
    messages: {
        location: {
            required: TRAN_NOTBEEMPTY,
        },
        start: {
            required: TRAN_NOTBEEMPTY,
        },
        desc: {
            required: TRAN_NOTBEEMPTY,
        },
        feed: {
            required: TRAN_NOTBEEMPTY,
        },
        source: {
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
        var dataString = $('#upload_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/rdcatch/upedit.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    $('#upload_edit').modal('hide');
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

$('#download_form').validate({
    rules: {
        location: {
            required: true,
        },
        start: {
            required: true,
        },
        desc: {
            required: true,
        },
        url: {
            required: true,
        },
        dest: {
            required: true,
        },
    },
    messages: {
        location: {
            required: TRAN_NOTBEEMPTY,
        },
        start: {
            required: TRAN_NOTBEEMPTY,
        },
        desc: {
            required: TRAN_NOTBEEMPTY,
        },
        url: {
            required: TRAN_NOTBEEMPTY,
        },
        dest: {
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
        var dataString = $('#download_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/rdcatch/downedit.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    $('#download_edit').modal('hide');
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

$('#macro_form').validate({
    rules: {
        location: {
            required: true,
        },
        start: {
            required: true,
        },
        desc: {
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
        start: {
            required: TRAN_NOTBEEMPTY,
        },
        desc: {
            required: TRAN_NOTBEEMPTY,
        },
        cart: {
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
                    targets: 0,
                    render: function (data, type, row) {
                        if (row.TYPE == 4) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/down.png" /></div> ` + data;
                        } else if (row.TYPE == 5) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/upload.png" /></div> ` + data;
                        } else {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/settings.png" /></div> ` + data;
                        }
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        if (row.TYPE == 4 || row.TYPE == 5) {
                            return row.URL
                        } else {
                            return row.MACRO_CART
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
                                    <a href="javascript:;" onclick="edit('`+ row.TYPE + `', '` + row.ID + `')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
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

    const element2 = document.getElementById('download_edit');
    const modal2 = new bootstrap.Modal(element2);

    var initDownEditModalButtons = function () {
        const cancelButton1 = element2.querySelector('[data-kt-rddown-modal-action="cancel"]');
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
                    modal2.hide();
                }
            });
        });
        const closeButton2 = element2.querySelector('[data-kt-rddown-modal-action="close"]');
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

    const element6 = document.getElementById('upload_edit');
    const modal6 = new bootstrap.Modal(element6);

    var initUpEditModalButtons = function () {
        const cancelButton1 = element6.querySelector('[data-kt-rdup-modal-action="cancel"]');
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
                    modal6.hide();
                }
            });
        });
        const closeButton2 = element6.querySelector('[data-kt-rdup-modal-action="close"]');
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
                    modal6.hide();

                }
            });
        });
    }

    return {
        init: function () {
            initDatatable();
            initMacroEditModalButtons();
            initDownEditModalButtons();
            initUpEditModalButtons();
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
                            return `<a href="javascript:;" onclick="addcart('` + row.NUMBER + `', '` + row.TITLE + `', '` + catchtype + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
                        } else {
                            return `<a href="javascript:;" onclick="addcart('` + row.NUMBER + `', '` + row.TITLE + `', '` + catchtype + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
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

var KTDatatablesServerSideCuts = function () {
    var initDatatableCutsLibrary = function () {
        dt3 = $("#cutssel_table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ordering: true,
            autoWidth: false,
            order: [
                [1, 'desc']
            ],
            stateSave: true,
            serverMethod: 'post',
            ajax: {
                url: HOST_URL + "/tables/rdcatch-cuts-table.php",
                data: function (d) {
                    d.cart = cartdata;
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
                    data: 'CART_NUMBER'
                },
                {
                    data: 'DESCRIPTION'
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

                        return `<a href="javascript:;" onclick="addcut('` + row.CUT_NAME + `', '` + catchtype + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;


                    }
                },
            ],
        });

    }

    const element5 = document.getElementById('cut_select');
    const modal5 = new bootstrap.Modal(element5);

    var initSelCutModalButtons = function () {
        const cancelButton2 = element5.querySelector('[data-kt-cutsel-modal-action="cancel"]');
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
        const closeButton2 = element5.querySelector('[data-kt-cutsel-modal-action="close"]');
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
            initDatatableCutsLibrary();
            initSelCutModalButtons();
        }
    }
}();

$(document).ready(function () {
    KTDatatablesServerSide.init();
    KTDatatablesServerSideLibrary.init();
    KTDatatablesServerSideCuts.init();
});

$("#start_macro").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i:S",
    enableSeconds: true,
    time_24hr: true
});

$("#start_upload").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i:S",
    enableSeconds: true,
    time_24hr: true
});

$("#start_down").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i:S",
    enableSeconds: true,
    time_24hr: true
});

$('#feed_upload').change(function () {
    if ($('#feed_upload').val() == '-1') {
        $("#url_upload").removeAttr('disabled');
        $("#usrn_upload").removeAttr('disabled');
        $("#pass_upload").removeAttr('disabled');
        $("#for_format").removeAttr('disabled');
        $("#for_channels").removeAttr('disabled');
        $("#for_samplerate").removeAttr('disabled');
        $("#for_bitrate").removeAttr('disabled');
        $("#for_quality").removeAttr('disabled');
        $("#normalize_upload").removeAttr('disabled');
        $("#normlevel_upload").removeAttr('disabled');
        $("#exportme_upload").removeAttr('disabled');
    } else {
        $('#url_upload').prop("disabled", true);
        $('#usrn_upload').prop("disabled", true);
        $('#pass_upload').prop("disabled", true);
        $('#for_format').prop("disabled", true);
        $('#for_channels').prop("disabled", true);
        $('#for_samplerate').prop("disabled", true);
        $('#for_bitrate').prop("disabled", true);
        $('#for_quality').prop("disabled", true);
        $('#normalize_upload').prop("disabled", true);
        $('#normlevel_upload').prop("disabled", true);
        $('#exportme_upload').prop("disabled", true);
    }
});

$('#autotrim_down').click(function () {
    if ($("#autotrim_down").is(":checked")) {
        $("#trimlevel_down").removeAttr('disabled');
    } else {
        $('#trimlevel_down').prop("disabled", true);
    }
});
$('#normalize_down').click(function () {
    if ($("#normalize_down").is(":checked")) {
        $("#normlevel_down").removeAttr('disabled');
    } else {
        $('#normlevel_down').prop("disabled", true);
    }
});
$('#normalize_upload').click(function () {
    if ($("#normalize_upload").is(":checked")) {
        $("#normlevel_upload").removeAttr('disabled');
    } else {
        $('#normlevel_upload').prop("disabled", true);
    }
});