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
var hourid;
var oldclockname;
var clockid;
var color;
var shortname;

function selectclock(i, o) {
    hourid = i;
    oldclockname = o;
    $("#clock_modal").modal("show");
}

function addclock(i, p, q) {
    clockid = i;
    color = q;
    shortname = p;

    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/grids/saveclockgrid.php',
        data: {
            new: i,
            old: oldclockname,
            clock: hourid
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                $("#clock_modal").modal("hide");
                $("#clocklink_" + hourid).css('background-color', color);
                $("#clockname_" + hourid).html(shortname);

            } else {
                Swal.fire({
                    text: TRAN_NOTSAVED,
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

function addallclock(i, p, q, z) {
    clockid = i;
    color = q;
    shortname = p;

    Swal.fire({
        text: TRAN_SETALLGRID,
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
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/grids/saveallclockgrids.php',
                data: {
                    new: i,
                    old: oldclockname,
                    service: z
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        $("#clock_modal").modal("hide");
                        for (rowruns = 0; rowruns < 168; rowruns++) {
                            $("#clocklink_" + rowruns).css('background-color', color);
                            $("#clockname_" + rowruns).html(shortname);
                        }


                    }
                }
            });
        }
    });


}

function clearAll(i) {
    Swal.fire({
        text: TRAN_CLEARALL,
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
            jQuery.ajax({
                type: "POST",
                url: HOST_URL + '/forms/grids/clearallclockgrids.php',
                data: {
                    service: i
                },
                datatype: 'html',
                success: function (data) {
                    var mydata = $.parseJSON(data);
                    var fel = mydata.error;
                    var kod = mydata.errorcode;
                    if (fel == "false") {
                        for (rowruns = 0; rowruns < 168; rowruns++) {
                            $("#clocklink_" + rowruns).css('background-color', "");
                            $("#clockname_" + rowruns).html("");
                        }


                    }
                }
            });
        }
    });
}

const element = document.getElementById('clock_modal');
const modal = new bootstrap.Modal(element);

var initGridModalButtons = function () {
    const cancelButton = element.querySelector('[data-kt-clock-modal-action="cancel"]');
    cancelButton.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSECLOCKS,
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
                modal.hide();
            }
        });
    });
    const closeButton = element.querySelector('[data-kt-clock-modal-action="close"]');
    closeButton.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSECLOCKS,
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
                modal.hide();

            }
        });
    });
}

initGridModalButtons();