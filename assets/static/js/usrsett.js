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
$.validator.addMethod("passwordFormatCheck", function (value, element) {
    return this.optional(element) || /^(?=.*\d)(?=.*[A-Z])(?=.*\W).*$/i.test(value);
}, TRAN_PASSMUSTHAVESPECIALS);

$('#pass_form').validate({
    rules: {
        current_password: {
            required: true,
        },
        password: {
            required: true,
            minlength: 5,
            passwordFormatCheck: true
        },
        confirm_password: {
            required: true,
            passwordFormatCheck: true,
            equalTo: "#password"
        },
    },
    messages: {
        current_password: {
            required: TRAN_NOTBEEMPTY,
        },
        password: {
            required: TRAN_NOTBEEMPTY,
            minlength: TRAN_PASSCHARMIN
        },
        confirm_password: {
            required: TRAN_NOTBEEMPTY,
            equalTo: TRAN_PASSWORDSNEEDMATCH,
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
        var dataString = $('#pass_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/updpass.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    Swal.fire({
                        text: TRAN_PASSHASCHANGE,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    });
                } else {
                    if (kod == '1') {
                        Swal.fire({
                            text: TRAN_CURRPASSNOTCORR,
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

$('#user_form').validate({
    rules: {
        fullname: {
            required: true,
        },
        email: {
            required: true,
            email: true
        },
        phone: {
            required: true,
        },
    },
    messages: {
        fullname: {
            required: TRAN_NOTBEEMPTY,
        },
        email: {
            required: TRAN_NOTBEEMPTY,
            email: TRAN_CORREMAILNEEDS
        },
        phone: {
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
        var dataString = $('#user_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/upduser.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                var kod = mydata.errorcode;
                if (fel == "false") {
                    Swal.fire({
                        text: TRAN_USERINFOUPDATED,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: TRAN_OK,
                        customClass: {
                            confirmButton: "btn btn-success"
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
    }
});