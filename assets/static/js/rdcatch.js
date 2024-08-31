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
var isedit = 0;
var allgroups = 1;
var groupnow;
var editmodal;
var cartdata;
var station;
var matrix;
var singleFetch;
var singleFetchIn;
var singleFetchOut;
var singleFetchPlay;
var singleFetchRec;
var singleFetchRecSource;
var librarytype = 2;
var catchtype;
var sampleOne = ["32000", "44100", "48000"];
var sampleTwo = ["16000", "22050", "32000", "44100", "48000"];
var bitOne = ["32", "48", "56", "64", "80", "96", "112", "128", "160", "192", "224", "256", "320", "384"];
var bitTwo = ["32", "40", "48", "56", "64", "80", "96", "112", "128", "160", "192", "224", "256", "320", "VBR"];

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
station = $("#location_switch").val();


const singfets = document.getElementById('matrix_switch');
singleFetch = new Choices(singfets, {
    allowHTML: false,
    searchPlaceholderValue: TRAN_SEARCHSWITCH,
});

const singfetsoutdrop = document.getElementById('output_switch');
singleFetchOut = new Choices(singfetsoutdrop, {
    allowHTML: false,
    searchPlaceholderValue: TRAN_SEARCHOUTPUT,
});

const singfetindrop = document.getElementById('input_switch');
singleFetchIn = new Choices(singfetindrop, {
    allowHTML: false,
    searchPlaceholderValue: TRAN_SEARCHINPUT,
});

const singfetplayrop = document.getElementById('audio_play');
singleFetchPlay = new Choices(singfetplayrop, {
    allowHTML: false,
    searchPlaceholderValue: TRAN_SEARCHPORT,
});

const singfetrecrop = document.getElementById('audio_rec');
singleFetchRec = new Choices(singfetrecrop, {
    allowHTML: false,
    searchPlaceholderValue: TRAN_SEARCHPORT,
});

const singfetrecsourcerop = document.getElementById('source_rec');
singleFetchRecSource = new Choices(singfetrecsourcerop, {
    allowHTML: false,
    searchPlaceholderValue: TRAN_SEARCHPORT,
});


singleFetch.setChoices(function () {
    return fetch(
        HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
    )
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            return data.switches.map(function (matrix) {
                return { label: matrix.NAME, value: matrix.MATRIX };
            });
        });
});

$('#location_play').change(function () {
    station = $("#location_play").val();
    singleFetchPlay.removeActiveItems();
    singleFetchPlay.clearInput();
    singleFetchPlay.clearChoices();

    singleFetchPlay.setChoices(function () {
        return fetch(
            HOST_URL + '/forms/rdcatch/playout.php?station=' + station,
        )
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                return data.playouts.map(function (play) {
                    var pnumb = String(play.PORT_NUMBER_VIS);
                    return { label: pnumb, value: play.CHANNEL };
                });
            });
    });
});

$('#location_rec').change(function () {
    station = $("#location_rec").val();
    singleFetchRec.removeActiveItems();
    singleFetchRec.clearInput();
    singleFetchRec.clearChoices();

    singleFetchRec.setChoices(function () {
        return fetch(
            HOST_URL + '/forms/rdcatch/playout.php?station=' + station,
        )
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                return data.records.map(function (rec) {
                    var pnumb = String(rec.PORT_NUMBER_VIS);
                    return { label: pnumb, value: rec.CHANNEL };
                });
            });
    });
});

$('#audio_rec').change(function () {
    station = $("#location_rec").val();
    var thechan = $("#audio_rec").val();
    $.ajax({
        url: HOST_URL + '/forms/rdcatch/getsource.php',
        data: "station=" + station + "&channel=" + thechan,
        dataType: 'json',
        success: function (data) {
            matrix = data['SWITCH_MATRIX'];
            singleFetchRecSource.removeActiveItems();
            singleFetchRecSource.clearInput();
            singleFetchRecSource.clearChoices();
            singleFetchRecSource.setChoices(function () {
                return fetch(
                    HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
                )
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        return data.inputs.map(function (inp) {
                            return { label: inp.NAME, value: inp.NUMBER };
                        });
                    });
            });
        }
    });

});

$('#location_switch').change(function () {
    station = $("#location_switch").val();
    singleFetch.removeActiveItems();
    singleFetch.clearInput();
    singleFetch.clearChoices();
    singleFetchOut.removeActiveItems();
    singleFetchIn.removeActiveItems();
    singleFetchOut.clearInput();
    singleFetchOut.clearChoices();
    singleFetchIn.clearInput();
    singleFetchIn.clearChoices();

    singleFetch.setChoices(function () {
        return fetch(
            HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
        )
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                return data.switches.map(function (matrix) {
                    return { label: matrix.NAME, value: matrix.MATRIX };
                });
            });
    });
});

$('#matrix_switch').change(function () {
    matrix = $("#matrix_switch").val();
    singleFetchOut.removeActiveItems();
    singleFetchIn.removeActiveItems();
    singleFetchOut.clearInput();
    singleFetchOut.clearChoices();
    singleFetchIn.clearInput();
    singleFetchIn.clearChoices();
    singleFetchOut.setChoices(function () {
        return fetch(
            HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
        )
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                return data.outputs.map(function (output) {
                    return { label: output.NAME, value: output.NUMBER };
                });
            });
    });
    singleFetchIn.setChoices(function () {
        return fetch(
            HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
        )
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                return data.inputs.map(function (input) {
                    return { label: input.NAME, value: input.NUMBER };
                });
            });
    });
    singleFetchIn.setChoices(function () {
        return [{ value: '0', label: TRAN_OFF }]
    });
});




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
                } else if (type == 3) {
                    $('#dest_play').val(data['CUT_NAME']);
                } else if (type == 0) {
                    $('#dest_rec').val(data['CUT_NAME']);
                }
            }
        });
        if (type == 4) {
            $("#selcutbutt").show();
            $("#selcartbutt").hide();
        } else if (type == 5) {
            $("#selcutbutt_up").show();
            $("#selcartbutt_up").hide();
        } else if (type == 3) {
            $("#selcutbuttplay").show();
            $("#selcartbuttplay").hide();
        } else if (type == 0) {
            $("#selcutbuttrec").show();
            $("#selcartbuttrec").hide();
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
    } else if (type == 3) {
        $("#dest_play").val(cutname);
        $("#selcutbuttplay").hide();
        $("#selcartbuttplay").show();
    } else if (type == 0) {
        $("#dest_rec").val(cutname);
        $("#selcutbuttrec").hide();
        $("#selcartbuttrec").show();
    }
}

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

function remove(id, desc) {
    var trans = tr('REMOVECATCHWARN {{' + desc + '}}');

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
                url: HOST_URL + '/forms/rdcatch/remove.php',
                data: {
                    catchid: id
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

function add(type) {
    catchtype = type;
    isedit = 0;
    if (type == 1) {
        librarytype = 2;
        dt2.ajax.reload();
        $("#macro_form").trigger("reset");
        $('#macro_edit').modal('show');
    } else if (type == 4) {
        librarytype = 1;
        dt2.ajax.reload();
        $("#download_form").trigger("reset");
        $("#selcutbutt").hide();
        $("#selcartbutt").show();
        $("#trimlevel_down").val("-35");
        $("#normlevel_down").val("-13");
        $("#dayoffset_down").val("0");
        $('#download_edit').modal('show');
    } else if (type == 5) {
        librarytype = 1;
        dt2.ajax.reload();
        $("#upload_form").trigger("reset");
        $("#selcutbutt_up").hide();
        $("#selcartbutt_up").show();
        $("#normlevel_upload").val("-13");
        $("#dayoffset_upload").val("0");
        $('#upload_edit').modal('show');
    } else if (type == 2) {
        $("#switch_form").trigger("reset");
        singleFetch.setChoices(function () {
            return fetch(
                HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
            )
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    return data.switches.map(function (matrix) {
                        return { label: matrix.NAME, value: matrix.MATRIX };
                    });
                });
        });
        $('#switch_edit').modal('show');
    } else if (type == 3) {
        librarytype = 1;
        dt2.ajax.reload();
        $("#playout_form").trigger("reset");
        station = $("#location_play").val();
        singleFetchPlay.removeActiveItems();
        singleFetchPlay.clearInput();
        singleFetchPlay.clearChoices();

        singleFetchPlay.setChoices(function () {
            return fetch(
                HOST_URL + '/forms/rdcatch/playout.php?station=' + station,
            )
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    return data.playouts.map(function (play) {
                        var pnumb = String(play.PORT_NUMBER_VIS);
                        return { label: pnumb, value: play.CHANNEL };
                    });
                });
        });

        $('#playout_edit').modal('show');
    } else if (type == 0) {
        librarytype = 1;
        dt2.ajax.reload();
        $("#recording_form").trigger("reset");
        station = $("#location_rec").val();
        singleFetchRec.removeActiveItems();
        singleFetchRec.clearInput();
        singleFetchRec.clearChoices();

        singleFetchRec.setChoices(function () {
            return fetch(
                HOST_URL + '/forms/rdcatch/playout.php?station=' + station,
            )
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    return data.records.map(function (rec) {
                        var pnumb = String(rec.PORT_NUMBER_VIS);
                        return { label: pnumb, value: rec.CHANNEL };
                    });
                });
        });

        $('#normlevel_rec').prop("disabled", true);
        $('#trimlevel_rec').prop("disabled", true);
        $("#start_rec").removeAttr('disabled');
        $('#gpistart_rec').prop("disabled", true);
        $('#gpistartend_rec').prop("disabled", true);
        $('#gpimatrix_rec').prop("disabled", true);
        $('#gpiline_rec').prop("disabled", true);
        $('#startdelay_rec').prop("disabled", true);
        $('#multiplerec_rec').prop("disabled", true);
        $("#recendtime_rec").removeAttr('disabled');
        $('#recendtimelength_rec').prop("disabled", true);
        $('#gpiendstart_rec').prop("disabled", true);
        $('#gpiendend_rec').prop("disabled", true);
        $('#gpimatrixend_rec').prop("disabled", true);
        $('#gpilineend_rec').prop("disabled", true);
        $('#maxreclength_rec').prop("disabled", true);
        $('#recording_edit').modal('show');
    }
}

function edit(type, id) {
    catchtype = type;
    isedit = 1;
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
            } else if (type == 2) {

                $('#switchid').val(id);
                station = data['STATION_NAME'];
                matrix = data['CHANNEL'];
                var forinput = data['SWITCH_INPUT'];
                var foroutput = data['SWITCH_OUTPUT'];
                singleFetch.removeActiveItems();
                singleFetch.clearInput();
                singleFetch.clearChoices();
                singleFetchOut.removeActiveItems();
                singleFetchIn.removeActiveItems();
                singleFetchOut.clearInput();
                singleFetchOut.clearChoices();
                singleFetchIn.clearInput();
                singleFetchIn.clearChoices();

                singleFetch.setChoices(function () {
                    return fetch(
                        HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
                    )
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            return data.switches.map(function (matrixs) {
                                if (matrixs.MATRIX == matrix) {
                                    return { label: matrixs.NAME, value: matrixs.MATRIX, selected: true };
                                } else {
                                    return { label: matrixs.NAME, value: matrixs.MATRIX };
                                }
                            });
                        });
                });

                singleFetchOut.setChoices(function () {
                    return fetch(
                        HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
                    )
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            return data.outputs.map(function (output) {
                                if (output.NUMBER == foroutput) {
                                    return { label: output.NAME, value: output.NUMBER, selected: true };
                                } else {
                                    return { label: output.NAME, value: output.NUMBER };
                                }

                            });
                        });
                });
                singleFetchIn.setChoices(function () {
                    return fetch(
                        HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
                    )
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            return data.inputs.map(function (input) {
                                if (input.NUMBER == forinput) {
                                    return { label: input.NAME, value: input.NUMBER, selected: true };
                                } else {
                                    return { label: input.NAME, value: input.NUMBER };
                                }

                            });
                        });
                });
                singleFetchIn.setChoices(function () {
                    if (forinput == 0) {
                        return [{ value: '0', label: TRAN_OFF, selected: true }]
                    } else {
                        return [{ value: '0', label: TRAN_OFF }]
                    }

                });

                if (data['IS_ACTIVE'] == 'Y') {
                    $("#eventactive_switch").prop('checked', true);
                } else {
                    $("#eventactive_switch").prop('checked', false);
                }
                $('#location_switch').val(data['STATION_NAME']);
                $('#start_switch').val(data['START_TIME']);
                $('#desc_switch').val(data['DESCRIPTION']);
                if (data['ONE_SHOT'] == 'Y') {
                    $("#oneshot_switch").prop('checked', true);
                } else {
                    $("#oneshot_switch").prop('checked', false);
                }
                if (data['SUN'] == 'Y') {
                    $("#sun_sw").prop('checked', true);
                } else {
                    $("#sun_sw").prop('checked', false);
                }
                if (data['MON'] == 'Y') {
                    $("#mon_sw").prop('checked', true);
                } else {
                    $("#mon_sw").prop('checked', false);
                }
                if (data['TUE'] == 'Y') {
                    $("#tue_sw").prop('checked', true);
                } else {
                    $("#tue_sw").prop('checked', false);
                }
                if (data['WED'] == 'Y') {
                    $("#wed_sw").prop('checked', true);
                } else {
                    $("#wed_sw").prop('checked', false);
                }
                if (data['THU'] == 'Y') {
                    $("#thu_sw").prop('checked', true);
                } else {
                    $("#thu_sw").prop('checked', false);
                }
                if (data['FRI'] == 'Y') {
                    $("#fri_sw").prop('checked', true);
                } else {
                    $("#fri_sw").prop('checked', false);
                }
                if (data['SAT'] == 'Y') {
                    $("#sat_sw").prop('checked', true);
                } else {
                    $("#sat_sw").prop('checked', false);
                }

                $('#switch_edit').modal('show');
            } else if (type == 3) {

                $('#playid').val(id);
                station = data['STATION_NAME'];
                var forplayout = data['CHANNEL'];
                librarytype = 1;
                dt2.ajax.reload();
                singleFetchPlay.removeActiveItems();
                singleFetchPlay.clearInput();
                singleFetchPlay.clearChoices();

                singleFetchPlay.setChoices(function () {
                    return fetch(
                        HOST_URL + '/forms/rdcatch/playout.php?station=' + station,
                    )
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            return data.playouts.map(function (play) {
                                if (play.CHANNEL == forplayout) {
                                    var pnumb = String(play.PORT_NUMBER_VIS);
                                    return { label: pnumb, value: play.CHANNEL, selected: true };
                                } else {
                                    var pnumb = String(play.PORT_NUMBER_VIS);
                                    return { label: pnumb, value: play.CHANNEL };
                                }

                            });
                        });
                });

                if (data['IS_ACTIVE'] == 'Y') {
                    $("#eventactive_play").prop('checked', true);
                } else {
                    $("#eventactive_play").prop('checked', false);
                }
                $('#location_play').val(data['STATION_NAME']);
                $('#start_play').val(data['START_TIME']);
                $('#desc_play').val(data['DESCRIPTION']);
                $('#dest_play').val(data['CUT_NAME']);
                if (data['ONE_SHOT'] == 'Y') {
                    $("#oneshot_play").prop('checked', true);
                } else {
                    $("#oneshot_play").prop('checked', false);
                }
                if (data['SUN'] == 'Y') {
                    $("#sun_play").prop('checked', true);
                } else {
                    $("#sun_play").prop('checked', false);
                }
                if (data['MON'] == 'Y') {
                    $("#mon_play").prop('checked', true);
                } else {
                    $("#mon_play").prop('checked', false);
                }
                if (data['TUE'] == 'Y') {
                    $("#tue_play").prop('checked', true);
                } else {
                    $("#tue_play").prop('checked', false);
                }
                if (data['WED'] == 'Y') {
                    $("#wed_play").prop('checked', true);
                } else {
                    $("#wed_play").prop('checked', false);
                }
                if (data['THU'] == 'Y') {
                    $("#thu_play").prop('checked', true);
                } else {
                    $("#thu_play").prop('checked', false);
                }
                if (data['FRI'] == 'Y') {
                    $("#fri_play").prop('checked', true);
                } else {
                    $("#fri_play").prop('checked', false);
                }
                if (data['SAT'] == 'Y') {
                    $("#sat_play").prop('checked', true);
                } else {
                    $("#sat_play").prop('checked', false);
                }
                $('#playout_edit').modal('show');

            } else if (type == 0) {
                $('#recid').val(id);
                station = data['STATION_NAME'];
                librarytype = 1;
                dt2.ajax.reload();
                var forplayout = data['CHANNEL'];
                singleFetchRec.removeActiveItems();
                singleFetchRec.clearInput();
                singleFetchRec.clearChoices();

                singleFetchRec.setChoices(function () {
                    return fetch(
                        HOST_URL + '/forms/rdcatch/playout.php?station=' + station,
                    )
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            return data.records.map(function (rec) {
                                if (rec.CHANNEL == forplayout) {
                                    var pnumb = String(rec.PORT_NUMBER_VIS);
                                    return { label: pnumb, value: rec.CHANNEL, selected: true };
                                } else {
                                    var pnumb = String(rec.PORT_NUMBER_VIS);
                                    return { label: pnumb, value: rec.CHANNEL };
                                }
                            });
                        });
                });

                var theinput = data['SWITCH_INPUT'];
                $.ajax({
                    url: HOST_URL + '/forms/rdcatch/getsource.php',
                    data: "station=" + station + "&channel=" + forplayout,
                    dataType: 'json',
                    success: function (data) {
                        matrix = data['SWITCH_MATRIX'];
                        singleFetchRecSource.removeActiveItems();
                        singleFetchRecSource.clearInput();
                        singleFetchRecSource.clearChoices();
                        singleFetchRecSource.setChoices(function () {
                            return fetch(
                                HOST_URL + '/forms/rdcatch/switch.php?station=' + station + '&matrix=' + matrix,
                            )
                                .then(function (response) {
                                    return response.json();
                                })
                                .then(function (data) {
                                    return data.inputs.map(function (inp) {
                                        if (inp.NUMBER == theinput) {
                                            return { label: inp.NAME, value: inp.NUMBER, selected: true };
                                        } else {
                                            return { label: inp.NAME, value: inp.NUMBER };
                                        }

                                    });
                                });
                        });
                    }
                });

                if (data['IS_ACTIVE'] == 'Y') {
                    $("#eventactive_rec").prop('checked', true);
                } else {
                    $("#eventactive_rec").prop('checked', false);
                }
                $('#location_rec').val(data['STATION_NAME']);
                $('#desc_rec').val(data['DESCRIPTION']);
                $('#dest_rec').val(data['CUT_NAME']);
                $('#channels_rec').val(data['CHANNELS']);
                if (data['TRIM_THRESHOLD'] == '0') {
                    $("#autotrim_rec").prop('checked', false);
                    $('#trimlevel_rec').val('-35');
                    $('#trimlevel_rec').prop("disabled", true);
                } else {
                    $("#autotrim_rec").prop('checked', true);
                    $("#trimlevel_rec").removeAttr('disabled');
                    //Find better solution to convert to correct value
                    var autotrimval = data['TRIM_THRESHOLD'] - (data['TRIM_THRESHOLD'] * 2);
                    autotrimval = autotrimval / 100;
                    $('#trimlevel_rec').val(autotrimval);
                }
                if (data['NORMALIZE_LEVEL'] == '0') {
                    $("#normalize_rec").prop('checked', false);
                    $('#normlevel_rec').val('-35');
                    $('#normlevel_rec').prop("disabled", true);
                } else {
                    $("#normalize_rec").prop('checked', true);
                    $("#normlevel_rec").removeAttr('disabled');
                    var normalizlevel = data['NORMALIZE_LEVEL'] / 100;
                    $('#normlevel_rec').val(normalizlevel);
                }
                $('#startdateoffset_rec').val(data['STARTDATE_OFFSET']);
                $('#enddateoffset_rec').val(data['ENDDATE_OFFSET']);
                if (data['ONE_SHOT'] == 'Y') {
                    $("#oneshot_rec").prop('checked', true);
                } else {
                    $("#oneshot_rec").prop('checked', false);
                }
                if (data['START_TYPE'] == 0) {
                    $("#hardtime_rec").prop("checked", true);
                    $("#start_rec").removeAttr('disabled');
                    $('#start_rec').val(data['START_TIME']);
                    $('#gpistart_rec').prop("disabled", true);
                    $('#gpistartend_rec').prop("disabled", true);
                    $('#gpimatrix_rec').prop("disabled", true);
                    $('#gpiline_rec').prop("disabled", true);
                    $('#startdelay_rec').prop("disabled", true);
                    $('#multiplerec_rec').prop("disabled", true);
                } else if (data['START_TYPE'] == 1) {
                    $("#gpi_rec").prop("checked", true);
                    $("#gpistart_rec").removeAttr('disabled');
                    $("#gpistartend_rec").removeAttr('disabled');
                    $("#gpimatrix_rec").removeAttr('disabled');
                    $("#gpiline_rec").removeAttr('disabled');
                    $("#startdelay_rec").removeAttr('disabled');
                    $("#multiplerec_rec").removeAttr('disabled');
                    $('#start_rec').prop("disabled", true);
                    $('#gpistart_rec').val(data['START_TIME']);
                    const milliseconds = (h, m, s) => (((h * 3600) + (+m * 60) + +s) * 1000);
                    const time = data['START_TIME'];
                    const timeParts = time.split(":");
                    var result = milliseconds(timeParts[0], timeParts[1], timeParts[2]);
                    $('#gpistartend_rec').val(msToTime(result + data['START_LENGTH']));
                    $('#gpimatrix_rec').val(data['START_MATRIX']);
                    $('#gpiline_rec').val(data['START_LINE']);
                    $('#startdelay_rec').val(msToTime(data['START_OFFSET']));
                    if (data['ALLOW_MULTI_RECS'] == 'Y') {
                        $("#multiplerec_rec").prop('checked', true);
                    } else {
                        $("#multiplerec_rec").prop('checked', false);
                    }
                }
                if (data['END_TYPE'] == 0) {
                    $("#hardtimeend_rec").prop("checked", true);
                    $("#recendtime_rec").removeAttr('disabled');
                    $('#recendtime_rec').val(data['END_TIME']);
                    $('#recendtimelength_rec').prop("disabled", true);
                    $('#gpiendstart_rec').prop("disabled", true);
                    $('#gpiendend_rec').prop("disabled", true);
                    $('#gpimatrixend_rec').prop("disabled", true);
                    $('#gpilineend_rec').prop("disabled", true);
                    $('#maxreclength_rec').prop("disabled", true);
                } else if (data['END_TYPE'] == 2) {
                    $("#lengthend_rec").prop("checked", true);
                    $('#recendtime_rec').prop("disabled", true);
                    $('#recendtimelength_rec').removeAttr('disabled');
                    $('#recendtimelength_rec').val(msToTime(data['LENGTH']));
                    $('#gpiendstart_rec').prop("disabled", true);
                    $('#gpiendend_rec').prop("disabled", true);
                    $('#gpimatrixend_rec').prop("disabled", true);
                    $('#gpilineend_rec').prop("disabled", true);
                    $('#maxreclength_rec').prop("disabled", true);
                } else if (data['END_TYPE'] == 1) {
                    $("#gpiend_rec").prop("checked", true);
                    $('#recendtime_rec').prop("disabled", true);
                    $('#recendtimelength_rec').prop("disabled", true);
                    $('#gpiendstart_rec').removeAttr('disabled');
                    $('#gpiendend_rec').removeAttr('disabled');
                    $('#gpimatrixend_rec').removeAttr('disabled');
                    $('#gpilineend_rec').removeAttr('disabled');
                    $('#maxreclength_rec').removeAttr('disabled');
                    $('#gpiendstart_rec').val(data['END_TIME']);
                    const milliseconds = (h, m, s) => (((h * 3600) + (+m * 60) + +s) * 1000);
                    const time = data['END_TIME'];
                    const timeParts = time.split(":");
                    var result = milliseconds(timeParts[0], timeParts[1], timeParts[2]);
                    $('#gpiendend_rec').val(msToTime(result + data['END_LENGTH']));
                    $('#gpimatrixend_rec').val(data['END_MATRIX']);
                    $('#gpilineend_rec').val(data['END_LINE']);
                    $('#maxreclength_rec').val(msToTime(data['MAX_GPI_REC_LENGTH']));
                }
                if (data['SUN'] == 'Y') {
                    $("#sun_rec").prop('checked', true);
                } else {
                    $("#sun_rec").prop('checked', false);
                }
                if (data['MON'] == 'Y') {
                    $("#mon_rec").prop('checked', true);
                } else {
                    $("#mon_rec").prop('checked', false);
                }
                if (data['TUE'] == 'Y') {
                    $("#tue_rec").prop('checked', true);
                } else {
                    $("#tue_rec").prop('checked', false);
                }
                if (data['WED'] == 'Y') {
                    $("#wed_rec").prop('checked', true);
                } else {
                    $("#wed_rec").prop('checked', false);
                }
                if (data['THU'] == 'Y') {
                    $("#thu_rec").prop('checked', true);
                } else {
                    $("#thu_rec").prop('checked', false);
                }
                if (data['FRI'] == 'Y') {
                    $("#fri_rec").prop('checked', true);
                } else {
                    $("#fri_rec").prop('checked', false);
                }
                if (data['SAT'] == 'Y') {
                    $("#sat_rec").prop('checked', true);
                } else {
                    $("#sat_rec").prop('checked', false);
                }
                $('#recording_edit').modal('show');


            }
        }
    });
}

$.validator.addMethod("biggerWindowStartGpiEnd", function (value, element, param) {

    var val_a = $('#gpistart_rec').val();

    return this.optional(element) || value >= val_a;
}, TRAN_GPINOTENDBEFOREBEGINSTART);

$.validator.addMethod("recordNotEndBefore", function (value, element, param) {

    var val_a = $('#start_rec').val();

    return this.optional(element) || value >= val_a;
}, TRAN_RECNOTENDBEFOREBEGIN);

$.validator.addMethod("recordLengthNotBe", function (value, element, param) {

    return this.optional(element) || value != param;
}, TRAN_RECLENGTHPARANOTBE);

$.validator.addMethod("biggerWindowEndGpiEnd", function (value, element, param) {
    if ($("#hardtime_rec").is(":checked")) {
        var val_a = $('#start_rec').val();
    } else if ($("#gpi_rec").is(":checked")) {
        var val_a = $('#gpistart_rec').val();
    }    

    return this.optional(element)
        || value >= val_a;
}, TRAN_GPIENDNOTENDBEFOREBEGINSTART);

$.validator.addMethod("biggerWindowEndTimeGpiEnd", function (value, element, param) {

    var val_a = $('#gpiendstart_rec').val();

    return this.optional(element) || value >= val_a;
}, TRAN_GPIENDNOTENDBEFOREBEGINSTART);

$('#recording_form').validate({
    rules: {
        location: {
            required: true,
        },
        audioport: {
            required: true,
        },
        desc: {
            required: true,
        },
        source: {
            required: true,
        },
        dest: {
            required: true,
        },
        start: {
            required: {
                depends: function (element) {
                    return $("#hardtime_rec").is(":checked");
                }
            }
        },
        gpistart: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            }
        },
        gpiend: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            },
            biggerWindowStartGpiEnd: true
        },
        gpimatrix: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            },
            remote: {
                url: HOST_URL + '/validation/checkgpimatrix.php',
                type: "post",
                data: {
                    thestation: function () {
                        return station;
                    },
                    thefield: function () {
                        return 0;
                    },
                }
            }
        },
        gpiline: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            },
            remote: {
                url: HOST_URL + '/validation/checkgpiline.php',
                type: "post",
                data: {
                    thestation: function () {
                        return station;
                    },
                    thematrix: function () {
                        return $("#gpimatrix_rec").val();
                    },
                    thefield: function () {
                        return 0;
                    },
                }
            }
        },
        startdelay: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            }
        },
        recendtime: {
            required: {
                depends: function (element) {
                    return $("#hardtimeend_rec").is(":checked");
                }
            },
            recordNotEndBefore: true
        },
        recendtimelength: {
            required: {
                depends: function (element) {
                    return $("#lengthend_rec").is(":checked");
                }
            },
            recordLengthNotBe: "00:00:00"
        },
        gpiendstart: {
            required: {
                depends: function (element) {
                    return $("#gpiend_rec").is(":checked");
                }
            },
            biggerWindowEndGpiEnd: true
        },
        gpiendend: {
            required: {
                depends: function (element) {
                    return $("#gpiend_rec").is(":checked");
                }
            },
            biggerWindowEndTimeGpiEnd: true
        },
        gpimatrixend: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            },
            remote: {
                url: HOST_URL + '/validation/checkgpimatrix.php',
                type: "post",
                data: {
                    thestation: function () {
                        return station;
                    },
                    thefield: function () {
                        return 1;
                    },
                }
            }
        },

        gpilineend: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            },
            remote: {
                url: HOST_URL + '/validation/checkgpiline.php',
                type: "post",
                data: {
                    thestation: function () {
                        return station;
                    },
                    thematrix: function () {
                        return $("#gpimatrixend_rec").val();
                    },
                    thefield: function () {
                        return 1;
                    },
                }
            }
        },

        maxreclength: {
            required: {
                depends: function (element) {
                    return $("#gpi_rec").is(":checked");
                }
            },
            recordLengthNotBe: "00:00:00"
        },
    },
    messages: {
        location: {
            required: TRAN_NOTBEEMPTY,
        },
        audioport: {
            required: TRAN_NOTBEEMPTY,
        },
        desc: {
            required: TRAN_NOTBEEMPTY,
        },
        source: {
            required: TRAN_NOTBEEMPTY,
        },
        dest: {
            required: TRAN_NOTBEEMPTY,
        },
        start: {
            required: TRAN_NOTBEEMPTY,
        },
        gpistart: {
            required: TRAN_NOTBEEMPTY,
        },
        gpiend: {
            required: TRAN_NOTBEEMPTY,
        },
        gpimatrix: {
            required: TRAN_NOTBEEMPTY,
        },
        gpiline: {
            required: TRAN_NOTBEEMPTY,
        },
        startdelay: {
            required: TRAN_NOTBEEMPTY,
        },
        recendtime: {
            required: TRAN_NOTBEEMPTY,
        },
        gpiendstart: {
            required: TRAN_NOTBEEMPTY,
        },
        gpiendend: {
            required: TRAN_NOTBEEMPTY,
        },
        gpimatrixend: {
            required: TRAN_NOTBEEMPTY,
        },
        gpilineend: {
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
        var dataString = $('#recording_form').serialize();
        if (isedit == 1) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/recordingedit.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#recording_edit').modal('hide');
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
        } else {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/recordingadd.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#recording_edit').modal('hide');
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

    }
});

$('#playout_form').validate({
    rules: {
        location: {
            required: true,
        },
        audioport: {
            required: true,
        },
        start: {
            required: true,
        },
        desc: {
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
        audioport: {
            required: TRAN_NOTBEEMPTY,
        },
        start: {
            required: TRAN_NOTBEEMPTY,
        },
        desc: {
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
        var dataString = $('#playout_form').serialize();
        if (isedit == 1) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/playoutedit.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#playout_edit').modal('hide');
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
        } else {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/playoutadd.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#playout_edit').modal('hide');
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

    }
});

$('#switch_form').validate({
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
        matrix: {
            required: true,
        },
        output: {
            required: true,
        },
        input: {
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
        matrix: {
            required: TRAN_NOTBEEMPTY,
        },
        output: {
            required: TRAN_NOTBEEMPTY,
        },
        input: {
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
        var dataString = $('#switch_form').serialize();
        if (isedit == 1) {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/switchedit.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#switch_edit').modal('hide');
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
        } else {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/switchadd.php',
                data: dataString,
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    if (fel == "false") {
                        $('#switch_edit').modal('hide');
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

    }
});

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
        if (isedit == 1) {
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
        } else {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/upadd.php',
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
        if (isedit == 1) {
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
        } else {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/downadd.php',
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
        if (isedit == 1) {
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
        } else {
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/rdcatch/macroadd.php',
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
                        } else if (row.TYPE == 2) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/switch.png" /></div> ` + data;
                        } else if (row.TYPE == 3) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/sound.png" /></div> ` + data;
                        } else if (row.TYPE == 0) {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/rec-button.png" /></div> ` + data;
                        } else {
                            return `
                            <div class="avatar me-3">
                            <img alt="Logo" src="`+ HOST_URL + `/assets/static/images/event/settings.png" /></div> ` + data;
                        }
                    }
                },

                {
                    targets: 3,
                    render: function (data, type, row) {
                        if (row.TYPE == 0) {
                            if (row.START_TYPE == 0) {
                                return TRAN_HARD + ' ' + row.START_TIME;
                            } else if (row.START_TYPE == 1) {
                                const milliseconds = (h, m, s) => (((h * 3600) + (+m * 60) + +s) * 1000);
                                const time = row.START_TIME;
                                const timeParts = time.split(":");
                                var result = milliseconds(timeParts[0], timeParts[1], timeParts[2]);
                                var corrtime = msToTime(result + row.START_LENGTH);
                                return TRAN_GPI + ' ' + row.START_TIME + ',' + corrtime + ',' + msToTime(row.START_OFFSET);
                            }
                        } else {
                            return data;
                        }
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (row.TYPE == 0) {
                            if (row.END_TYPE == 0) {
                                return TRAN_HARD + ' ' + row.END_TIME;
                            } else if (row.END_TYPE == 1) {
                                const milliseconds = (h, m, s) => (((h * 3600) + (+m * 60) + +s) * 1000);
                                const time = row.END_TIME;
                                const timeParts = time.split(":");
                                var result = milliseconds(timeParts[0], timeParts[1], timeParts[2]);
                                var corrtime = msToTime(result + row.END_LENGTH);
                                return TRAN_GPI + ' ' + row.END_TIME + ',' + corrtime + ',' + msToTime(row.MAX_GPI_REC_LENGTH);
                            } else if (row.END_TYPE == 2) {
                                return TRAN_LENGTH + ' ' + msToTime(row.LENGTH);
                            }
                        } else {
                            return data;
                        }
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        if (row.TYPE == 4 || row.TYPE == 5) {
                            return row.URL
                        } else if (row.TYPE == 2) {
                            return row.NAME
                        } else if (row.TYPE == 3) {
                            return row.CUT_NAME
                        } else if (row.TYPE == 0) {
                            return row.IMPNAME
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
                                    <a href="javascript:;" onclick="remove('` + row.ID + `' ,'` + row.DESCRIPTION + `')" class="btn icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_REMOVE + `"><i class="bi bi-x-square"></i></a>
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

    const element7 = document.getElementById('switch_edit');
    const modal7 = new bootstrap.Modal(element7);

    var initSwitchEditModalButtons = function () {
        const cancelButton1 = element7.querySelector('[data-kt-rdswitch-modal-action="cancel"]');
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
                    modal7.hide();
                }
            });
        });
        const closeButton2 = element7.querySelector('[data-kt-rdswitch-modal-action="close"]');
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
                    modal7.hide();

                }
            });
        });
    }

    const element8 = document.getElementById('playout_edit');
    const modal8 = new bootstrap.Modal(element8);

    var initPlayEditModalButtons = function () {
        const cancelButton1 = element8.querySelector('[data-kt-rdplay-modal-action="cancel"]');
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
                    modal8.hide();
                }
            });
        });
        const closeButton2 = element8.querySelector('[data-kt-rdplay-modal-action="close"]');
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
                    modal8.hide();

                }
            });
        });
    }

    const element9 = document.getElementById('recording_edit');
    const modal9 = new bootstrap.Modal(element9);

    var initRecEditModalButtons = function () {
        const cancelButton1 = element9.querySelector('[data-kt-rdrec-modal-action="cancel"]');
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
                    modal9.hide();
                }
            });
        });
        const closeButton2 = element9.querySelector('[data-kt-rdrec-modal-action="close"]');
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
                    modal9.hide();

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
            initSwitchEditModalButtons();
            initPlayEditModalButtons();
            initRecEditModalButtons();
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

$(".fltpick").flatpickr({
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

$('#autotrim_rec').click(function () {
    if ($("#autotrim_rec").is(":checked")) {
        $("#trimlevel_rec").removeAttr('disabled');
    } else {
        $('#trimlevel_rec').prop("disabled", true);
    }
});

$('#normalize_rec').click(function () {
    if ($("#normalize_rec").is(":checked")) {
        $("#normlevel_rec").removeAttr('disabled');
    } else {
        $('#normlevel_rec').prop("disabled", true);
    }
});

$('#hardtime_rec').click(function () {
    if ($("#hardtime_rec").is(":checked")) {
        $("#start_rec").removeAttr('disabled');

        $('#gpistart_rec').prop("disabled", true);
        $('#gpistartend_rec').prop("disabled", true);
        $('#gpimatrix_rec').prop("disabled", true);
        $('#gpiline_rec').prop("disabled", true);
        $('#startdelay_rec').prop("disabled", true);
        $('#multiplerec_rec').prop("disabled", true);
    } else {
        $('#start_rec').prop("disabled", true);
    }
});

$('#hardtimeend_rec').click(function () {
    if ($("#hardtimeend_rec").is(":checked")) {
        $("#recendtime_rec").removeAttr('disabled');

        $('#recendtimelength_rec').prop("disabled", true);
        $('#gpiendstart_rec').prop("disabled", true);
        $('#gpiendend_rec').prop("disabled", true);
        $('#gpimatrixend_rec').prop("disabled", true);
        $('#gpilineend_rec').prop("disabled", true);
        $('#maxreclength_rec').prop("disabled", true);
    } else {
        $('#recendtime_rec').prop("disabled", true);
    }
});

$('#lengthend_rec').click(function () {
    if ($("#lengthend_rec").is(":checked")) {
        $("#recendtimelength_rec").removeAttr('disabled');

        $('#recendtime_rec').prop("disabled", true);
        $('#gpiendstart_rec').prop("disabled", true);
        $('#gpiendend_rec').prop("disabled", true);
        $('#gpimatrixend_rec').prop("disabled", true);
        $('#gpilineend_rec').prop("disabled", true);
        $('#maxreclength_rec').prop("disabled", true);
    } else {
        $('#recendtimelength_rec').prop("disabled", true);
    }
});

$('#gpiend_rec').click(function () {
    if ($("#gpiend_rec").is(":checked")) {
        $("#gpiendstart_rec").removeAttr('disabled');
        $("#gpiendend_rec").removeAttr('disabled');
        $("#gpimatrixend_rec").removeAttr('disabled');
        $("#gpilineend_rec").removeAttr('disabled');
        $("#maxreclength_rec").removeAttr('disabled');

        $('#recendtimelength_rec').prop("disabled", true);
        $('#recendtime_rec').prop("disabled", true);
    } else {
        $('#gpiendstart_rec').prop("disabled", true);
        $('#gpiendend_rec').prop("disabled", true);
        $('#gpimatrixend_rec').prop("disabled", true);
        $('#gpilineend_rec').prop("disabled", true);
        $('#maxreclength_rec').prop("disabled", true);
    }
});

$('#gpi_rec').click(function () {
    if ($("#gpi_rec").is(":checked")) {
        $("#gpistart_rec").removeAttr('disabled');
        $("#gpistartend_rec").removeAttr('disabled');
        $("#gpimatrix_rec").removeAttr('disabled');
        $("#gpiline_rec").removeAttr('disabled');
        $("#startdelay_rec").removeAttr('disabled');
        $("#multiplerec_rec").removeAttr('disabled');

        $('#start_rec').prop("disabled", true);
    } else {
        $('#gpistart_rec').prop("disabled", true);
        $('#gpistartend_rec').prop("disabled", true);
        $('#gpimatrix_rec').prop("disabled", true);
        $('#gpiline_rec').prop("disabled", true);
        $('#startdelay_rec').prop("disabled", true);
        $('#multiplerec_rec').prop("disabled", true);
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