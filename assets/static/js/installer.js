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
            noResultsText: 'No results found',
            noChoicesText: 'No options avalible',
            itemSelectText: 'Press to select',
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
        smtp_login: {
            required: true,
        },
        smtp_enc: {
            required: true,
        },
        smtp_server: {
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
            required: 'This can not be empty!',
        },
        urladd: {
            required: 'This can not be empty!',
            url: 'You need to enter a correct url!'
        },
        admin_usr: {
            required: 'This can not be empty!',
        },
        time_zone: {
            required: 'This can not be empty!',
        },
        def_lang: {
            required: 'This can not be empty!',
        },
        pass_reset: {
            required: 'This can not be empty!',
        },
        autotrim: {
            required: 'This can not be empty!',
        },
        normalize: {
            required: 'This can not be empty!',
        },
        smtp_server: {
            required: 'This can not be empty!',
        },
        smtp_login: {
            required: 'This can not be empty!',
        },
        smtp_enc: {
            required: 'This can not be empty!',
        },
        smtp_port: {
            required: 'This can not be empty!',
        },
        smtp_usr: {
            required: 'This can not be empty!',
        },
        smtp_pass: {
            required: 'This can not be empty!',
        },
        smtp_from: {
            required: 'This can not be empty!',
            email: 'You need to enter a correct email address!'
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
            url: 'forms/installer.php',
            data: dataString,
            success: function (data) {
                var mydata = $.parseJSON(data);
                var fel = mydata.error;
                if (fel == "false") {
                    location.href = 'index.php';
                } else {
                    Swal.fire({
                        text: 'Not possible to install system on the server. Have you read in the wiki what you need to do ?',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });


                }
            }
        });
    }
});