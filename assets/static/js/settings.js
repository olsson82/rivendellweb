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

$('#install_form').validate({
    rules: {
        sys_name: {
            required: true,
        },
        urladd: {
            required: true,
            url: true
        },
        admin_usr: {
            required: true,
        },
        time_zone: {
            required: true,
        },
        def_lang: {
            required: true,
        },
        pass_reset: {
            required: true,
        },
        autotrim: {
            required: true,
        },
        normalize: {
            required: true,
        },
        smtp_server: {
            required: true,
        },
        smtp_login: {
            required: true,
        },
        smtp_enc: {
            required: true,
        },
        smtp_port: {
            required: true,
        },
        smtp_usr: {
            required: true,
        },
        smtp_pass: {
            required: true,
        },
        smtp_from: {
            required: true,
            email: true
        },
    },
    messages: {
        sys_name: {
            required: TRAN_NOTBEEMPTY,
        },
        urladd: {
            required: TRAN_NOTBEEMPTY,
            url: TRAN_CORRURLNEEDS
        },
        time_zone: {
            required: TRAN_NOTBEEMPTY,
        },
        def_lang: {
            required: TRAN_NOTBEEMPTY,
        },
        pass_reset: {
            required: TRAN_NOTBEEMPTY,
        },
        autotrim: {
            required: TRAN_NOTBEEMPTY,
        },
        normalize: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_server: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_login: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_enc: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_port: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_usr: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_pass: {
            required: TRAN_NOTBEEMPTY,
        },
        smtp_from: {
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
        var dataString = $('#install_form').serialize();
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/settings.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    Swal.fire({
                        text: TRAN_SYSSETTSAVED,
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