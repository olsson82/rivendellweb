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
var editmodal;
var therowplace;
var rowcol;
var allservice = 1;
var allgroups = 1;
var groupnow;
var servicename;

var timeoutInMiliseconds = 1800000;
var timeoutInMilisecondsClose = 60000;
var timeoutId;
var timeoutIdClose;

function startCloseTime() {
    timeoutIdClose = window.setTimeout(doLogout, timeoutInMilisecondsClose)
}

function doLogout() {
    $.ajax({
        url: HOST_URL + '/forms/logs/unlocklog.php',
        data: "log=" + LOG_ID + "&lockcode=" + LOCK_CODE,
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                location.href = HOST_URL + '/logedit/logs';
            }
        }
    });
}

function startTimer() {
    timeoutId = window.setTimeout(doInactive, timeoutInMiliseconds)
}

function doInactive() {
    window.clearTimeout(timeoutId);
    $('#still_edit').modal('show');
    startCloseTime();


}

function resetTimer() {
    window.clearTimeout(timeoutId);
    window.clearTimeout(timeoutIdClose);
    $('#still_edit').modal('hide');
    startTimer();
}

function setupTimers() {
    document.addEventListener("mousemove", resetTimer, false);
    document.addEventListener("mousedown", resetTimer, false);
    document.addEventListener("keypress", resetTimer, false);
    document.addEventListener("touchmove", resetTimer, false);

    startTimer();
}

function removeLogLine(rowid, count) {
    if (ALLOW_REMOVEFROM == 0) {
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
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/removelogline.php',
            data: {
                rowid: rowid,
                log: LOG_ID,
                countplace: count
            },
            datatype: 'html',
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    dt.ajax.reload();

                }
            }
        });
    }
}

function updateLock() {
    $.ajax({
        url: HOST_URL + '/forms/logs/updatelock.php',
        data: "log=" + LOG_ID + "&lockcode=" + LOCK_CODE,
    });
}

var interval = setInterval(function () { updateLock(); }, 60000);

Inputmask({
    "mask": "99:99:99.9"
}).mask("#startattime_logchain");

Inputmask({
    "mask": "99:99"
}).mask("#waitupto_logchain");

Inputmask({
    "mask": "99:99:99.9"
}).mask("#startattime_cart");

Inputmask({
    "mask": "99:99"
}).mask("#waitupto_cart");

Inputmask({
    "mask": "99:99:99.9"
}).mask("#startattime_voice");

Inputmask({
    "mask": "99:99"
}).mask("#waitupto_voice");

Inputmask({
    "mask": "99:99:99.9"
}).mask("#startattime_marker");

Inputmask({
    "mask": "99:99"
}).mask("#waitupto_marker");

var addVoiceForm = $('#addVoice_form').validate({
    rules: {
        startattime: {
            pattern: '([0-2][0-9]):([0-5][0-9]):([0-5][0-9])\.([0-9])',
        },
        waitupto: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        ifprevends: {
            required: true,
        },
        comment: {
            required: true,
        },
    },
    messages: {
        startattime: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        waitupto: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        ifprevends: {
            required: TRAN_NOTBEEMPTY
        },
        comment: {
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
        $("#starttimemillis_voice").val(getMillisFromTime($("#startattime_voice").val()));
        $("#waittimemillis_voice").val(getMillisFromTime($("#waitupto_voice").val()));
        var dataString = $('#addVoice_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/addmarker.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    addVoiceForm.resetForm();
                    dt.ajax.reload();
                    $('#add_voicetrack').modal('hide');
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

var addMarkerForm = $('#addMarker_form').validate({
    rules: {
        startattime: {
            pattern: '([0-2][0-9]):([0-5][0-9]):([0-5][0-9])\.([0-9])',
        },
        waitupto: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        ifprevends: {
            required: true,
        },
        comment: {
            required: true,
        },
        label: {
            required: true,
        },
    },
    messages: {
        startattime: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        waitupto: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        ifprevends: {
            required: TRAN_NOTBEEMPTY
        },
        comment: {
            required: TRAN_NOTBEEMPTY
        },
        label: {
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
        $("#starttimemillis_marker").val(getMillisFromTime($("#startattime_marker").val()));
        $("#waittimemillis_marker").val(getMillisFromTime($("#waitupto_marker").val()));
        var dataString = $('#addMarker_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/addmarker.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    addMarkerForm.resetForm();
                    dt.ajax.reload();
                    $('#add_marker').modal('hide');
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

var addLogChainForm = $('#logchain_form').validate({
    rules: {
        startattime: {
            pattern: '([0-2][0-9]):([0-5][0-9]):([0-5][0-9])\.([0-9])',
        },
        waitupto: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        ifprevends: {
            required: true,
        },
    },
    messages: {
        startattime: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        waitupto: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        ifprevends: {
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
        $("#starttimemillis_chain").val(getMillisFromTime($("#startattime_logchain").val()));
        $("#waittimemillis_chain").val(getMillisFromTime($("#waitupto_logchain").val()));
        var dataString = $('#logchain_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/addchain.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    addLogChainForm.resetForm();
                    dt.ajax.reload();
                    $('#add_logchain').modal('hide');
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

var addCartForm = $('#addCart_form').validate({
    rules: {
        startattime: {
            pattern: '([0-2][0-9]):([0-5][0-9]):([0-5][0-9])\.([0-9])',
        },
        waitupto: {
            pattern: '([0-5][0-9]):([0-5][0-9])',
        },
        ifprevends: {
            required: true,
        },
    },
    messages: {
        startattime: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        waitupto: {
            pattern: TRAN_NOTCORRECTTIMEFORM
        },
        ifprevends: {
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
        $("#starttimemillis_imp").val(getMillisFromTime($("#startattime_cart").val()));
        $("#waittimemillis_imp").val(getMillisFromTime($("#waitupto_cart").val()));
        var dataString = $('#addCart_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/addcart.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    addCartForm.resetForm();
                    dt.ajax.reload();
                    $('#add_cart').modal('hide');
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

$('#log_form').validate({
    rules: {
        description: {
            required: true,
        },
        logstartdate: {
            dateISO: true
        },
        logenddate: {
            dateISO: true
        },
        logremovedate: {
            dateISO: true
        },
    },
    messages: {
        description: {
            required: TRAN_NOTBEEMPTY
        },
        logstartdate: {
            dateISO: TRAN_DATEFORMAT
        },
        logenddate: {
            dateISO: TRAN_DATEFORMAT
        },
        logremovedate: {
            dateISO: TRAN_DATEFORMAT
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
        var dataString = $('#log_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/logs/savelog-mysql.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                var errormess = mydata.errormess;
                if (fel == "false") {
                    Swal.fire({
                        text: TRAN_LOGHASSAVED,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    dt.ajax.reload();
                } else {
                    console.log(errormess);
                    Swal.fire({
                        text: TRAN_NOTPOSSIBLESAVELOG,
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

$('#selectService').on('change', function (e) {

    if ($('#selectService').val() == 'allservices') {
        allservice = 1;
        dt1.ajax.reload();
    } else {
        allservice = 0;
        servicename = $('#selectService').val();
        dt1.ajax.reload();
    }
});

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

function addcart(cart, type, title, artist) {
    $("#cart_value").val(cart);
    $("#title_value").val(title);
    $("#artist_value").val(artist);
    $("#carttype_imp").val(type);
    $("#cartno_imp").val(cart);
    $("#subbut_cart").removeAttr('disabled');
    editmodal.hide();
}

function addchain(logname, description) {
    $("#logname_logchain").val(logname);
    $("#logdesc_logchain").val(description);
    $("#thelogname_chain").val(logname);
    $("#thelogdesc_chain").val(description);
    $("#subbut_chain").removeAttr('disabled');
    editmodal.hide();
}

function addtolog(log, type, rowplace, cartid) {

    if (ALLOW_ADDTO == 0) {
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

        if (type == 4) {
            $('#add_logchain').modal('show');
            $('#rowplace_chain').val(rowplace);
            $('#addcart_chain').val(log);
            $('#logChainLabel').html(TRAN_ADDCHAINTEXT);
            $('#iseditmode_chain').val('0');
            $('#subbut_chain').prop("disabled", true);
            therowplace = rowplace;
        }

        if (type == 40) {
            $.ajax({
                url: HOST_URL + '/forms/logs/cartinfo.php',
                data: "log=" + LOG_ID + "&cart=" + cartid,
                dataType: 'json',
                success: function (data) {
                    if (data['TIME_TYPE'] == 1) {
                        $("#startat_logchain").prop('checked', true);
                        $("#startattime_logchain").removeAttr('disabled');
                        $("#hard_select_logchain").removeAttr('disabled');
                        $("#hard_next_logchain").removeAttr('disabled');
                        $("#hard_wait_logchain").removeAttr('disabled');
                        $('#startattime_logchain').val(msToTime(data['START_TIME']));
                        if (data['GRACE_TIME'] > 0) {
                            $("#hard_wait_logchain").prop('checked', true);
                            $("#waitupto_logchain").removeAttr('disabled');
                            $('#waitupto_logchain').val(getMillisFromTime(data['GRACE_TIME']));
                        } else if (data['GRACE_TIME'] == -1) {
                            $('#waitupto_logchain').prop("disabled", true);
                            $('#waitupto_logchain').val('00:00');
                            $("#hard_next_logchain").prop('checked', true);
                        } else {
                            $('#waitupto_logchain').prop("disabled", true);
                            $('#waitupto_logchain').val('00:00');
                            $("#hard_select_logchain").prop('checked', true);
                        }
                    } else {
                        $("#startat_logchain").prop('checked', false);
                        $('#startattime_logchain').prop("disabled", true);
                        $('#startattime_logchain').val('00:00:00.0');
                        $('#hard_select_logchain').prop("disabled", true);
                        $('#hard_next_logchain').prop("disabled", true);
                        $('#hard_wait_logchain').prop("disabled", true);
                        $('#waitupto_logchain').prop("disabled", true);
                    }

                    $('#ifprevends_logchain').val(data['TRANS_TYPE']);
                    $('#ifprevends_logchain').trigger('change');
                    $("#logname_logchain").val(data['LABEL']);
                    $("#logdesc_logchain").val(data['COMMENT']);
                    $('#addcart_chain').val(log);
                    $('#rowplace_chain').val(rowplace);
                    $('#add_logchain').modal('show');
                    $('#iseditmode_chain').val('1');
                    $('#therowidno_chain').val(data['ID']);
                    $('#logChainLabel').html(TRAN_EDITLOGCHAINTEXT);
                    therowplace = rowplace;
                }
            });

        }

        if (type == 2) {
            $('#add_voicetrack').modal('show');
            $('#rowplace_voice').val(rowplace);
            $('#addcart_voice').val(log);
            $('#VoicetrackLabel').html(TRAN_ADDVOICETEXT);
            $('#iseditmode_voice').val('0');
            $('#isvoicetrack_voice').val('1');
            therowplace = rowplace;
        }

        if (type == 20) {
            $.ajax({
                url: HOST_URL + '/forms/logs/cartinfo.php',
                data: "log=" + LOG_ID + "&cart=" + cartid,
                dataType: 'json',
                success: function (data) {
                    if (data['TIME_TYPE'] == 1) {
                        $("#startat_voice").prop('checked', true);
                        $("#startattime_voice").removeAttr('disabled');
                        $("#hard_select_voice").removeAttr('disabled');
                        $("#hard_next_voice").removeAttr('disabled');
                        $("#hard_wait_voice").removeAttr('disabled');
                        $('#startattime_voice').val(msToTime(data['START_TIME']));
                        if (data['GRACE_TIME'] > 0) {
                            $("#hard_wait_voice").prop('checked', true);
                            $("#waitupto_voice").removeAttr('disabled');
                            $('#waitupto_voice').val(getMillisFromTime(data['GRACE_TIME']));
                        } else if (data['GRACE_TIME'] == -1) {
                            $('#waitupto_voice').prop("disabled", true);
                            $('#waitupto_voice').val('00:00');
                            $("#hard_next_voice").prop('checked', true);
                        } else {
                            $('#waitupto_voice').prop("disabled", true);
                            $('#waitupto_voice').val('00:00');
                            $("#hard_select_voice").prop('checked', true);
                        }
                    } else {
                        $("#startat_voice").prop('checked', false);
                        $('#startattime_voice').prop("disabled", true);
                        $('#startattime_voice').val('00:00:00.0');
                        $('#hard_select_voice').prop("disabled", true);
                        $('#hard_next_voice').prop("disabled", true);
                        $('#hard_wait_voice').prop("disabled", true);
                        $('#waitupto_voice').prop("disabled", true);
                    }

                    $('#ifprevends_voice').val(data['TRANS_TYPE']);
                    $('#ifprevends_voice').trigger('change');

                    $("#comment_voice").val(data['COMMENT']);
                    $('#addcart_voice').val(log);
                    $('#rowplace_voice').val(rowplace);
                    $('#add_voicetrack').modal('show');
                    $('#iseditmode_voice').val('1');
                    $('#isvoicetrack_voice').val('1');
                    $('#therowidno_voice').val(data['ID']);
                    $('#VoicetrackLabel').html(TRAN_EDITVOICETEXT);
                    therowplace = rowplace;
                }
            });

        }

        if (type == 3) {
            $('#add_marker').modal('show');
            $('#rowplace_marker').val(rowplace);
            $('#addcart_marker').val(log);
            $('#MarkerLabel').html(TRAN_ADDMARKERTEXT);
            $('#iseditmode_marker').val('0');
            $('#isvoicetrack_marker').val('0');
            therowplace = rowplace;
        }

        if (type == 30) {
            $.ajax({
                url: HOST_URL + '/forms/logs/cartinfo.php',
                data: "log=" + LOG_ID + "&cart=" + cartid,
                dataType: 'json',
                success: function (data) {
                    if (data['TIME_TYPE'] == 1) {
                        $("#startat_marker").prop('checked', true);
                        $("#startattime_marker").removeAttr('disabled');
                        $("#hard_select_marker").removeAttr('disabled');
                        $("#hard_next_marker").removeAttr('disabled');
                        $("#hard_wait_marker").removeAttr('disabled');
                        $('#startattime_marker').val(msToTime(data['START_TIME']));
                        if (data['GRACE_TIME'] > 0) {
                            $("#hard_wait_marker").prop('checked', true);
                            $("#waitupto_marker").removeAttr('disabled');
                            $('#waitupto_marker').val(getMillisFromTime(data['GRACE_TIME']));
                        } else if (data['GRACE_TIME'] == -1) {
                            $('#waitupto_marker').prop("disabled", true);
                            $('#waitupto_marker').val('00:00');
                            $("#hard_next_marker").prop('checked', true);
                        } else {
                            $('#waitupto_marker').prop("disabled", true);
                            $('#waitupto_marker').val('00:00');
                            $("#hard_select_marker").prop('checked', true);
                        }
                    } else {
                        $("#startat_marker").prop('checked', false);
                        $('#startattime_marker').prop("disabled", true);
                        $('#startattime_marker').val('00:00:00.0');
                        $('#hard_select_marker').prop("disabled", true);
                        $('#hard_next_marker').prop("disabled", true);
                        $('#hard_wait_marker').prop("disabled", true);
                        $('#waitupto_marker').prop("disabled", true);
                    }

                    $('#ifprevends_marker').val(data['TRANS_TYPE']);
                    $('#ifprevends_marker').trigger('change');

                    $("#comment_marker").val(data['COMMENT']);
                    $("#label_marker").val(data['LABEL']);
                    $('#addcart_marker').val(log);
                    $('#rowplace_marker').val(rowplace);
                    $('#add_marker').modal('show');
                    $('#iseditmode_marker').val('1');
                    $('#isvoicetrack_marker').val('0');
                    $('#therowidno_marker').val(data['ID']);
                    $('#MarkerLabel').html(TRAN_EDITMARKERTEXT);
                    therowplace = rowplace;
                }
            });

        }

        if (type == 1) {
            $('#add_cart').modal('show');
            $('#rowplace_imp').val(rowplace);
            $('#addcart_id').val(log);
            $('#CartLabel').html(TRAN_ADDCARTTEXT);
            $('#iseditmode_imp').val('0');
            $('#subbut_cart').prop("disabled", true);
            therowplace = rowplace;
        }

        if (type == 10) {
            $.ajax({
                url: HOST_URL + '/forms/logs/cartinfo.php',
                data: "log=" + LOG_ID + "&cart=" + cartid,
                dataType: 'json',
                success: function (data) {
                    if (data['TIME_TYPE'] == 1) {
                        $("#startat_cart").prop('checked', true);
                        $("#startattime_cart").removeAttr('disabled');
                        $("#hard_select_im").removeAttr('disabled');
                        $("#hard_next").removeAttr('disabled');
                        $("#hard_wait").removeAttr('disabled');
                        $('#startattime_cart').val(msToTime(data['START_TIME']));
                        if (data['GRACE_TIME'] > 0) {
                            $("#hard_wait").prop('checked', true);
                            $("#waitupto_cart").removeAttr('disabled');
                            $('#waitupto_cart').val(getMillisFromTime(data['GRACE_TIME']));
                        } else if (data['GRACE_TIME'] == -1) {
                            $('#waitupto_cart').prop("disabled", true);
                            $('#waitupto_cart').val('00:00');
                            $("#hard_next").prop('checked', true);
                        } else {
                            $('#waitupto_cart').prop("disabled", true);
                            $('#waitupto_cart').val('00:00');
                            $("#hard_select_im").prop('checked', true);
                        }
                    } else {
                        $("#startat_cart").prop('checked', false);
                        $('#startattime_cart').prop("disabled", true);
                        $('#startattime_cart').val('00:00:00.0');
                        $('#hard_select_im').prop("disabled", true);
                        $('#hard_next').prop("disabled", true);
                        $('#hard_wait').prop("disabled", true);
                        $('#waitupto_cart').prop("disabled", true);
                    }

                    $('#ifprevends_cart').val(data['TRANS_TYPE']);
                    $('#ifprevends_cart').trigger('change');

                    if (data['SEGUE_GAIN'] == 0) {
                        $("#nofadesegue_cart").prop('checked', true);
                    } else {
                        $("#nofadesegue_cart").prop('checked', false);
                    }

                    $("#cart_value").val(data['CART_NUMBER']);
                    $("#title_value").val(data['TITLE']);
                    $("#artist_value").val(data['ARTIST']);
                    $('#addcart_id').val(log);
                    $('#rowplace_imp').val(rowplace);
                    $('#cartno_imp').val(data['CART_NUMBER']);
                    $('#add_cart').modal('show');
                    $('#iseditmode_imp').val('1');
                    $('#therowidno_imp').val(data['ID']);
                    $('#CartLabel').html(TRAN_EDITCARTTEXT);
                    therowplace = rowplace;
                }
            });

        }

    }
}
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

function roworder(id, rowid, order, up, log) {
    if (ALLOW_ARRANGE == 0) {
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
        jQuery.ajax({
            type: "POST",
            async: false,
            url: HOST_URL + '/forms/logs/roworder.php',
            data: {
                id: id,
                rowid: rowid,
                order: order,
                up: up,
                log: LOG_ID
            },
            datatype: 'html',
            success: function (data) {
                var mydata = $.parseJSON(data);
                dt.ajax.reload();

            }
        });

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
        dt = $("#loglines_table").DataTable({
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
                url: HOST_URL + "/tables/loglines-table.php",
                data: function (d) {
                    d.logname = LOG_ID;
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
                    data: 'TRANS_TYPE'
                },
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
                    data: 'ARTIST'
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
                        if (row.TIME_TYPE == 1) {
                            return `<P style="color:#0054c2">S` + msToTime(data) + `</p>`;
                        } else {
                            return msToTime(row.FAKE_TIME)
                        }
                    }
                },
                {
                    targets: 1,
                    orderable: false,
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
                    targets: 2,
                    render: function (data, type, row) {
                        if (row.TYPE == 1) {
                            return TRAN_MARKERLABEL;
                        } else if (row.TYPE == 6) {
                            return TRAN_TRACKLABEL;
                        }
                        else if (row.TYPE == 5) {
                            return TRAN_LOGCHAIN;
                        } else {
                            return data;
                        }
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        if (row.TYPE == 0 || row.TYPE == 2) {
                            return '<P style="color:' + row.COLOR + '">' + data + '</p>';
                        } else {
                            return "";
                        }
                    }
                },

                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (row.TYPE == 0) {
                            if (data != 0) {
                                return getTimeFromMillis(data);
                            } else {
                                return "";
                            }
                        } else {
                            return "";
                        }
                    }
                },

                {
                    targets: 5,
                    render: function (data, type, row) {
                        if (row.TYPE == 1 || row.TYPE == 6 || row.TYPE == 5) {
                            return row.COMMENT;
                        } else if (row.TYPE == 0 || row.TYPE == 2) {
                            return data;
                        }
                    }
                },

                {
                    targets: 6,
                    render: function (data, type, row) {
                        if (row.TYPE == 1 || row.TYPE == 5) {
                            return row.LABEL;
                        } else if (row.TYPE == 6) {
                            return "";
                        } else if (row.TYPE == 0 || row.TYPE == 2) {
                            return data;
                        }
                    }
                },
                {
                    targets: 7,
                    orderable: false,
                    render: function (data, type, row) {
                        return `<div class="btn-group mb-3" role="group">
                <a href="javascript:;" onclick="roworder('` + row.ID + `', '` + row.LINE_ID + `', '` + row.COUNT + `','1')" class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                title="`+ TRAN_MOVEUP + `"><i class="bi bi-arrow-up-circle"></i></a>
                <a href="javascript:;" onclick="roworder('` + row.ID + `', '` + row.LINE_ID + `', '` + row.COUNT + `','0')" class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
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
                        var edittypes;
                        if (row.TYPE == 0 || row.TYPE == 2) {
                            edittypes = '10';
                        } else if (row.TYPE == 1) {
                            edittypes = '30';
                        } else if (row.TYPE == 6) {
                            edittypes = '20';
                        } else if (row.TYPE == 5) {
                            edittypes = '40';
                        }
                        return `
                        <div class="btn-group mb-3" role="group">
                        <button type="button" onclick="addtolog('`+ LOG_ID + `', '1','` + row.COUNT + `','0')" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top"
            title="`+ TRAN_ADDCART + `"><i class="bi bi-music-note"></i></button>
                        <button type="button" onclick="addtolog('`+ LOG_ID + `', '2','` + row.COUNT + `','0')" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
            title="`+ TRAN_ADDVOICETRACK + `"><i class="bi bi-mic"></i></button>
                        <button type="button" onclick="addtolog('`+ LOG_ID + `', '3','` + row.COUNT + `','0')" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
            title="`+ TRAN_ADDMARKER + `"><i class="bi bi-card-text"></i></button>
            <button type="button" onclick="addtolog('`+ LOG_ID + `', '4','` + row.COUNT + `','0')" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
            title="`+ TRAN_ADDLOGCHAIN + `"><i class="bi bi-link-45deg"></i></button>
            <button type="button" onclick="addtolog('`+ LOG_ID + `', '` + edittypes + `','` + row.COUNT + `','` + row.ID + `')" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
            title="`+ TRAN_EDIT + `"><i class="bi bi-pencil"></i></button>
            <button type="button" onclick="removeLogLine('`+ row.ID + `','` + row.COUNT + `',)" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top"
            title="`+ TRAN_DELLOGLINE + `"><i class="bi bi-x-square"></i></button>
                    </div>
                            `;
                    }
                },
            ],
        });
    }

    const element1 = document.getElementById('add_logchain');
    const modal1 = new bootstrap.Modal(element1);

    var initLogChainModalButtons = function () {
        const cancelButton2 = element1.querySelector('[data-kt-logchain-modal-action="cancel"]');
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
        const closeButton2 = element1.querySelector('[data-kt-logchain-modal-action="close"]');
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

    const element3 = document.getElementById('add_cart');
    const modal3 = new bootstrap.Modal(element3);

    var initAddCartModalButtons = function () {
        const cancelButton2 = element3.querySelector('[data-kt-addcart-modal-action="cancel"]');
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
        const closeButton2 = element3.querySelector('[data-kt-addcart-modal-action="close"]');
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

    const element5 = document.getElementById('add_marker');
    const modal5 = new bootstrap.Modal(element5);

    var initAddMarkerModalButtons = function () {
        const cancelButton2 = element5.querySelector('[data-kt-addmarker-modal-action="cancel"]');
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
                    modal5.hide();
                }
            });
        });
        const closeButton2 = element5.querySelector('[data-kt-addmarker-modal-action="close"]');
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
                    modal5.hide();

                }
            });
        });
    }

    const element6 = document.getElementById('add_voicetrack');
    const modal6 = new bootstrap.Modal(element6);

    var initAddVoiceModalButtons = function () {
        const cancelButton2 = element6.querySelector('[data-kt-addvoicetrack-modal-action="cancel"]');
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
                    modal6.hide();
                }
            });
        });
        const closeButton2 = element6.querySelector('[data-kt-addvoicetrack-modal-action="close"]');
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

    const cancelButtonClose = document.getElementById('kt_edit_log_cancel');

    var initCloseLogButton = function () {
        cancelButtonClose.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: TRAN_CLOSELOGLOCKPAGE,
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
                    $.ajax({
                        url: HOST_URL + '/forms/logs/unlocklog.php',
                        data: "log=" + LOG_ID + "&lockcode=" + LOCK_CODE,
                        success: function (data) {
                            var mydata = $.parseJSON(data);
                            var fel = mydata.error;
                            var kod = mydata.errorcode;
                            if (fel == "false") {
                                location.href = HOST_URL + '/logedit/logs';
                            }
                        }
                    });
                }
            });
        });
    }
    return {
        init: function () {
            initDatatable();
            initLogChainModalButtons();
            initAddCartModalButtons();
            initAddMarkerModalButtons();
            initAddVoiceModalButtons();
            initCloseLogButton();
        }
    }
}();

var KTDatatablesServerSideChain = function () {
    var initDatatableChain = function () {
        dt1 = $("#chain_table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [0, 'desc']
            ],
            stateSave: true,
            serverMethod: 'post',
            autoWidth: false,
            ajax: {
                url: HOST_URL + "/tables/logchain-data.php",
                data: function (d) {
                    d.username = USERNAME;
                    d.all = allservice;
                    d.servicename = servicename;
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
                    data: 'NAME'
                },
                {
                    data: 'DESCRIPTION'
                },
                {
                    data: 'SERVICE'
                },
                {
                    data: 'MUSIC_LINKED'
                },
                {
                    data: 'TRAFFIC_LINKED'
                },
                {
                    data: 'SCHEDULED_TRACKS'
                },
                {
                    data: null
                },
            ],
            columnDefs: [

                {
                    targets: 0,
                    render: function (data, type, row) {

                        if (row.SCHEDULED_TRACKS > 0 && row.COMPLETED_TRACKS < row.SCHEDULED_TRACKS) {
                            return `<a href="javascript:;" onclick="addchain('` + row.NAME + `', '` + row.DESCRIPTION + `')" class="text-danger">` + data + `</a>`;
                        } else {
                            return `<a href="javascript:;" onclick="addchain('` + row.NAME + `', '` + row.DESCRIPTION + `')" class="text-success">` + data + `</a>`;
                        }



                    }
                },

                {
                    targets: 5,
                    render: function (data, type, row) {

                        if (row.SCHEDULED_TRACKS > 0 && row.COMPLETED_TRACKS < row.SCHEDULED_TRACKS) {
                            return '<span class="badge bg-danger">' + row.COMPLETED_TRACKS + '/' + row.SCHEDULED_TRACKS + '</span>';
                        } else if (row.SCHEDULED_TRACKS > 0 && row.SCHEDULED_TRACKS == row.completed) {
                            return '<span class="badge bg-success">' + row.COMPLETED_TRACKS + '/' + row.SCHEDULED_TRACKS + '</span>';
                        } else {
                            return '<span class="badge bg-primary">' + row.COMPLETED_TRACKS + '/' + row.SCHEDULED_TRACKS + '</span>';
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
                        <a href="javascript:;" onclick="addchain('`+ row.NAME + `', '` + row.DESCRIPTION + `')" class="btn icon btn-primary"><i class="bi bi-plus-square"></i></a>`;
                    }
                },
            ],

        });
    }

    const element2 = document.getElementById('logchain_select');
    const modal2 = new bootstrap.Modal(element2);

    var initSelChainModalButtons = function () {
        const cancelButton2 = element2.querySelector('[data-kt-logsel-modal-action="cancel"]');
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
        const closeButton2 = element2.querySelector('[data-kt-logsel-modal-action="close"]');
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
            initDatatableChain();
            initSelChainModalButtons();
        }
    }
}();

var KTDatatablesServerSideLibrary = function () {
    var initDatatableLibrary = function () {
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
                        return `
                        <a href="javascript:;" onclick="addcart('`+ row.NUMBER + `', '` + row.TYPE + `', '` + row.TITLE + `', '` + row.ARTIST + `')" class="btn icon btn-info"><i class="bi bi-plus-square"></i></a>`;
                    }
                },
            ],
        });

    }

    const element4 = document.getElementById('cart_select');
    const modal4 = new bootstrap.Modal(element4);

    var initSelCartModalButtons = function () {
        const cancelButton2 = element4.querySelector('[data-kt-cartsel-modal-action="cancel"]');
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
        const closeButton2 = element4.querySelector('[data-kt-cartsel-modal-action="close"]');
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

$(document).ready(function () {
    KTDatatablesServerSide.init();
    KTDatatablesServerSideChain.init();
    KTDatatablesServerSideLibrary.init();

    setupTimers();
});


$('#startat_logchain').click(function () {

    if ($("#startat_logchain").is(":checked")) {
        $("#startattime_logchain").removeAttr('disabled');
        $("#hard_select_logchain").removeAttr('disabled');
        $("#hard_next_logchain").removeAttr('disabled');
        $("#hard_wait_logchain").removeAttr('disabled');
    } else {
        $('#startattime_logchain').prop("disabled", true);
        $('#hard_select_logchain').prop("disabled", true);
        $('#hard_next_logchain').prop("disabled", true);
        $('#hard_wait_logchain').prop("disabled", true);
        $('#waitupto_logchain').prop("disabled", true);
    }

});

$('#hard_wait_logchain').click(function () {

    if ($("#hard_wait_logchain").is(":checked")) {
        $("#waitupto_logchain").removeAttr('disabled');
    }

});

$('#hard_select_logchain').click(function () {

    if ($("#hard_select_logchain").is(":checked")) {
        $('#waitupto_logchain').prop("disabled", true);
    }

});

$('#hard_next_logchain').click(function () {

    if ($("#hard_next_logchain").is(":checked")) {
        $('#waitupto_logchain').prop("disabled", true);
    }

});

$("#logstartdate").flatpickr({
    dateFormat: "Y-m-d",
    time_24hr: true,
    locale: {
        firstDayOfWeek: 1,
        weekAbbreviation: "v",

        weekdays: {
            shorthand: [TRAN_SUN, TRAN_MON, TRAN_TUE, TRAN_WED, TRAN_THU, TRAN_FRI, TRAN_SAT],
            longhand: [
                TRAN_SUND,
                TRAN_MOND,
                TRAN_TUED,
                TRAN_WEDD,
                TRAN_THUD,
                TRAN_FRID,
                TRAN_SATD,
            ],
        },

        months: {
            shorthand: [
                TRAN_JAN,
                TRAN_FEB,
                TRAN_MAR,
                TRAN_APR,
                TRAN_MAY,
                TRAN_JUN,
                TRAN_JUL,
                TRAN_AUG,
                TRAN_SEP,
                TRAN_OCT,
                TRAN_NOV,
                TRAN_DEC,
            ],
            longhand: [
                TRAN_JANM,
                TRAN_FEBM,
                TRAN_MARM,
                TRAN_APRM,
                TRAN_MAYM,
                TRAN_JUNM,
                TRAN_JULM,
                TRAN_AUGM,
                TRAN_SEPM,
                TRAN_OCTM,
                TRAN_NOVM,
                TRAN_DECM,
            ],
        },
        rangeSeparator: " " + TRAN_TO + " ",
    }
});

$("#logenddate").flatpickr({
    dateFormat: "Y-m-d",
    time_24hr: true,
    locale: {
        firstDayOfWeek: 1,
        weekAbbreviation: "v",

        weekdays: {
            shorthand: [TRAN_SUN, TRAN_MON, TRAN_TUE, TRAN_WED, TRAN_THU, TRAN_FRI, TRAN_SAT],
            longhand: [
                TRAN_SUND,
                TRAN_MOND,
                TRAN_TUED,
                TRAN_WEDD,
                TRAN_THUD,
                TRAN_FRID,
                TRAN_SATD,
            ],
        },

        months: {
            shorthand: [
                TRAN_JAN,
                TRAN_FEB,
                TRAN_MAR,
                TRAN_APR,
                TRAN_MAY,
                TRAN_JUN,
                TRAN_JUL,
                TRAN_AUG,
                TRAN_SEP,
                TRAN_OCT,
                TRAN_NOV,
                TRAN_DEC,
            ],
            longhand: [
                TRAN_JANM,
                TRAN_FEBM,
                TRAN_MARM,
                TRAN_APRM,
                TRAN_MAYM,
                TRAN_JUNM,
                TRAN_JULM,
                TRAN_AUGM,
                TRAN_SEPM,
                TRAN_OCTM,
                TRAN_NOVM,
                TRAN_DECM,
            ],
        },
        rangeSeparator: " " + TRAN_TO + " ",
    }
});

$("#logremovedate").flatpickr({
    dateFormat: "Y-m-d",
    time_24hr: true,
    locale: {
        firstDayOfWeek: 1,
        weekAbbreviation: "v",

        weekdays: {
            shorthand: [TRAN_SUN, TRAN_MON, TRAN_TUE, TRAN_WED, TRAN_THU, TRAN_FRI, TRAN_SAT],
            longhand: [
                TRAN_SUND,
                TRAN_MOND,
                TRAN_TUED,
                TRAN_WEDD,
                TRAN_THUD,
                TRAN_FRID,
                TRAN_SATD,
            ],
        },

        months: {
            shorthand: [
                TRAN_JAN,
                TRAN_FEB,
                TRAN_MAR,
                TRAN_APR,
                TRAN_MAY,
                TRAN_JUN,
                TRAN_JUL,
                TRAN_AUG,
                TRAN_SEP,
                TRAN_OCT,
                TRAN_NOV,
                TRAN_DEC,
            ],
            longhand: [
                TRAN_JANM,
                TRAN_FEBM,
                TRAN_MARM,
                TRAN_APRM,
                TRAN_MAYM,
                TRAN_JUNM,
                TRAN_JULM,
                TRAN_AUGM,
                TRAN_SEPM,
                TRAN_OCTM,
                TRAN_NOVM,
                TRAN_DECM,
            ],
        },
        rangeSeparator: " " + TRAN_TO + " ",
    }
});

$('#startdateac').click(function () {

    if ($("#startdateac").is(":checked")) {
        $("#logstartdate").removeAttr('disabled');
    } else {
        $('#logstartdate').prop("disabled", true);
    }

});

$('#enddateac').click(function () {

    if ($("#enddateac").is(":checked")) {
        $("#logenddate").removeAttr('disabled');
    } else {
        $('#logenddate').prop("disabled", true);
    }

});

$('#removedateac').click(function () {

    if ($("#removedateac").is(":checked")) {
        $("#logremovedate").removeAttr('disabled');
    } else {
        $('#logremovedate').prop("disabled", true);
    }

});

$('#startat_cart').click(function () {

    if ($("#startat_cart").is(":checked")) {
        $("#startattime_cart").removeAttr('disabled');
        $("#hard_select_im").removeAttr('disabled');
        $("#hard_next").removeAttr('disabled');
        $("#hard_wait").removeAttr('disabled');
    } else {
        $('#startattime_cart').prop("disabled", true);
        $('#hard_select_im').prop("disabled", true);
        $('#hard_next').prop("disabled", true);
        $('#hard_wait').prop("disabled", true);
        $('#waitupto_cart').prop("disabled", true);
    }

});

$('#hard_wait').click(function () {

    if ($("#hard_wait").is(":checked")) {
        $("#waitupto_cart").removeAttr('disabled');
    }

});

$('#hard_select_im').click(function () {

    if ($("#hard_select_im").is(":checked")) {
        $('#waitupto_cart').prop("disabled", true);
    }

});

$('#hard_next').click(function () {

    if ($("#hard_next").is(":checked")) {
        $('#waitupto_cart').prop("disabled", true);
    }

});

$('#startat_marker').click(function () {

    if ($("#startat_marker").is(":checked")) {
        $("#startattime_marker").removeAttr('disabled');
        $("#hard_select_marker").removeAttr('disabled');
        $("#hard_next_marker").removeAttr('disabled');
        $("#hard_wait_marker").removeAttr('disabled');
    } else {
        $('#startattime_marker').prop("disabled", true);
        $('#hard_select_marker').prop("disabled", true);
        $('#hard_next_marker').prop("disabled", true);
        $('#hard_wait_marker').prop("disabled", true);
        $('#waitupto_marker').prop("disabled", true);
    }

});

$('#hard_wait_marker').click(function () {

    if ($("#hard_wait_marker").is(":checked")) {
        $("#waitupto_marker").removeAttr('disabled');
    }

});

$('#hard_select_marker').click(function () {

    if ($("#hard_select_marker").is(":checked")) {
        $('#waitupto_marker').prop("disabled", true);
    }

});

$('#hard_next_marker').click(function () {

    if ($("#hard_next_marker").is(":checked")) {
        $('#waitupto_marker').prop("disabled", true);
    }

});

$('#startat_voice').click(function () {

    if ($("#startat_voice").is(":checked")) {
        $("#startattime_voice").removeAttr('disabled');
        $("#hard_select_voice").removeAttr('disabled');
        $("#hard_next_voice").removeAttr('disabled');
        $("#hard_wait_voice").removeAttr('disabled');
    } else {
        $('#startattime_voice').prop("disabled", true);
        $('#hard_select_voice').prop("disabled", true);
        $('#hard_next_voice').prop("disabled", true);
        $('#hard_wait_voice').prop("disabled", true);
        $('#waitupto_voice').prop("disabled", true);
    }

});

$('#hard_wait_voice').click(function () {

    if ($("#hard_wait_voice").is(":checked")) {
        $("#waitupto_voice").removeAttr('disabled');
    }

});

$('#hard_select_voice').click(function () {

    if ($("#hard_select_voice").is(":checked")) {
        $('#waitupto_voice').prop("disabled", true);
    }

});

$('#hard_next_voice').click(function () {

    if ($("#hard_next_voice").is(":checked")) {
        $('#waitupto_voice').prop("disabled", true);
    }

});