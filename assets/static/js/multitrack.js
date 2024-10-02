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
let multitrack;
var ee;
var tr1segstart;
var tr2segstart;
var tr3segstart;
var tr1lineid;
var tr2lineid;
var tr3lineid;
var tra1end;
var tra2end;
var tra3end;
var thelog;

function doMultitrack(lineid, logname) {
    var tracks;
    var roword;
    var totrows;
    var abow;
    var below;
    var begincount;
    var addnext = 0;
    var i = 0;
    var isdone = 0;
    var cartnomb;
    var nextstart;
    var segstartpoint;
    var theTracks = Array();
    thelog = logname;

    $("#multitrack_voice").modal("show");
    $('#markerbody').preloader({
        text: TRAN_LOADINGAUDIO,
    });

    $.ajax({
        type: "POST",
        url: HOST_URL + '/forms/voicetrack/rowinfo.php',
        data: {
            lineid: lineid,
            logname: logname
        },
        async: false,
        dataType: 'json',
        success: function (data) {
            roword = data['COUNT'];
        }
    });

    if (roword > 0) {
        totrows = 3;
        abow = roword - 1;
        below = roword + 1;
        begincount = abow;
    } else {
        begincount = roword;
        totrows = 2;
        below = roword + 1;
    }

    while (begincount <= below) {
        var scount
        var cutname;
        var dtype;
        var currsegstartpoint;
        var currfadeuppoint;
        var currfadedownpoint;
        var currsegendpoint;
        var nexsegstart;
        var nextcutseguestartpoint;
        var lineid;
        var artist;
        var title;
        var endpoint;
        var print;

        $.ajax({
            type: "POST",
            async: false,
            url: HOST_URL + '/forms/voicetrack/rowinfocount.php',
            data: {
                count: begincount,
                logname: logname
            },
            dataType: 'json',
            success: function (data) {
                dtype = data['TYPE'];
                currsegstartpoint = data['SEGUE_START_POINT'];
                currsegendpoint = data['SEGUE_END_POINT'];
                currfadeuppoint = data['FADEUP_POINT'];
                currfadedownpoint = data['FADEDOWN_POINT'];
                lineid = data['LINE_ID'];
                artist = data['ARTIST'];
                title = data['TITLE'];
                if (dtype == 0) {
                    cartnomb = data['CART_NUMBER'];
                    addnext = 1;
                } else {
                    addnext = 0;
                }

            }
        });
        if (addnext == 1) {
            var dragg = true;
            var endtime = 0;
            var starttime;
            var cutseguestartpoint;
            $.ajax({
                data: "id=" + cartnomb,
                url: HOST_URL + '/forms/rdcatch/getcut.php',
                dataType: 'json',
                async: false,
                success: function (dat) {
                    cutname = dat['CUT_NAME'];
                    cutseguestartpoint = dat['SEGUE_START_POINT'];
                    endpoint = dat['END_POINT'];
                    if (artist == '' || artist == null || artist == "undefined") {
                        print = title;
                    } else {
                        print = title + ' - ' + artist;
                    }
                    if (i == 0) {
                        endtime = endpoint;
                        tra1end = endtime;
                        tr1lineid = lineid;
                        dragg = false;
                        nextstart = endtime;
                        nexsegstart = currsegstartpoint;
                        segstartpoint = currsegstartpoint;
                        nextcutseguestartpoint = cutseguestartpoint;
                        if (currfadeuppoint > 0 && currfadedownpoint <= 0) {
                            currfadeuppoint = currfadeuppoint / 1000;
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: 0,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                    fadeIn: {
                                        shape: "linear",
                                        duration: currfadeuppoint
                                    }
                                });
                        } else if (currfadeuppoint < 0 && currfadedownpoint > 0) {
                            var calfadeend = endpoint - currfadedownpoint;
                            currfadedownpoint = calfadeend / 1000;
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: 0,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                    fadeIn: {
                                        shape: "linear",
                                        duration: currfadeuppoint
                                    },
                                    fadeOut: {
                                        shape: "linear",
                                        duration: currfadedownpoint
                                    }
                                });
                        } else if (currfadeuppoint > 0 && currfadedownpoint > 0) {
                            currfadeuppoint = currfadeuppoint / 1000;
                            var calfadeend = endpoint - currfadedownpoint;
                            currfadedownpoint = calfadeend / 1000;
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: 0,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                    fadeOut: {
                                        shape: "linear",
                                        duration: currfadedownpoint
                                    }
                                });
                        } else {
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: 0,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                });
                        }

                    } else {
                        if (i == 1) {
                            tr2lineid = lineid;
                            tra2end = endtime;
                        } else {
                            tra3end = endtime;
                            tr3lineid = lineid;
                        }
                        dragg = true;
                        if (nexsegstart > 0) {
                            if (i == 2) {
                                starttime = nextstart - nexsegstart;
                                starttime = starttime / 1000;
                            } else {
                                starttime = nexsegstart / 1000;
                                nextstart = nexsegstart + endtime;
                            }

                        } else {
                            if (nextcutseguestartpoint > 0) {
                                starttime = nextcutseguestartpoint / 1000;
                                nextstart = nextcutseguestartpoint + endtime;
                            } else {
                                starttime = nextstart / 1000;
                                nextstart = nextstart + endtime;
                            }
                        }
                        nexsegstart = currsegstartpoint;
                        nextcutseguestartpoint = cutseguestartpoint;
                        if (currfadeuppoint > 0 && currfadedownpoint <= 0) {
                            currfadeuppoint = currfadeuppoint / 1000;
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: starttime,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                    fadeIn: {
                                        shape: "linear",
                                        duration: currfadeuppoint
                                    }
                                },

                            );
                        } else if (currfadeuppoint <= 0 && currfadedownpoint > 0) {
                            var calfadeend = endpoint - currfadedownpoint;
                            currfadedownpoint = calfadeend / 1000;
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: starttime,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                    fadeOut: {
                                        shape: "linear",
                                        duration: currfadedownpoint
                                    }
                                },

                            );
                        } else if (currfadeuppoint > 0 && currfadedownpoint > 0) {
                            currfadeuppoint = currfadeuppoint / 1000;
                            var calfadeend = endpoint - currfadedownpoint;
                            currfadedownpoint = calfadeend / 1000;
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: starttime,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    },
                                    fadeIn: {
                                        shape: "linear",
                                        duration: currfadeuppoint
                                    },
                                    fadeOut: {
                                        shape: "linear",
                                        duration: currfadedownpoint
                                    }
                                },

                            );
                        } else {
                            theTracks.push(
                                {
                                    src: HOST_URL + '/forms/library/export.php?cutname=' + cutname + '&mp3=0',
                                    name: print,
                                    start: starttime,
                                    states: {
                                        cursor: true,
                                        fadein: true,
                                        fadeout: true,
                                        select: true,
                                        shift: dragg,
                                    }
                                },

                            );
                        }
                    }
                }
            });
            i++;
            begincount = begincount + 1;
        }

    }

    multitrack = WaveformPlaylist.init({
        samplesPerPixel: 5000,
        waveHeight: 130,
        container: document.getElementById("multitrack"),
        state: 'cursor',
        colors: {
            waveOutlineColor: '#707273',
            timeColor: 'grey',
            fadeColor: 'black'
        },
        fadeType: "linear",
        timescale: true,
        controls: {
            show: true, //whether or not to include the track controls
            width: 200, //width of controls in pixels
            widgets: {
                muteOrSolo: false,
                volume: false,
                stereoPan: false,
                collapse: false,
                remove: false,
            }
        },
        seekStyle: 'line',
        zoomLevels: [500, 1000, 3000, 5000],
        isAutomaticScroll: true,
    });

    multitrack.load(theTracks).then(function () {
        $('#markerbody').preloader('remove');
    });

    ee = multitrack.getEventEmitter();
    var $container = $("body");

    function toggleActive(node) {
        var active = node.parentNode.querySelectorAll('.active');
        var i = 0, len = active.length;

        for (; i < len; i++) {
            active[i].classList.remove('active');
        }

        node.classList.toggle('active');
    }

    $container.on("click", ".btn-play", function () {
        ee.emit("play");
    });

    $container.on("click", ".btn-stop", function () {
        isLooping = false;
        ee.emit("stop");
    });

    $container.on("click", ".btn-pause", function () {
        isLooping = false;
        ee.emit("pause");
    });

    $container.on("click", ".btn-rewind", function () {
        isLooping = false;
        ee.emit("rewind");
    });

    $container.on("click", ".btn-fast-forward", function () {
        isLooping = false;
        ee.emit("fastforward");
    });

    $container.on("click", ".btn-zoom-in", function () {
        ee.emit("zoomin");
    });

    $container.on("click", ".btn-zoom-out", function () {
        ee.emit("zoomout");
    });

    $container.on("click", ".btn-cursor", function () {
        ee.emit("statechange", "cursor");
        toggleActive(this);
    });

    $container.on("click", ".btn-shift", function () {
        ee.emit("statechange", "shift");
        toggleActive(this);
    });

    $container.on("click", ".btn-fadein", function () {
        ee.emit("fadetype", "linear");
        ee.emit("statechange", "fadein");
        toggleActive(this);
    });

    $container.on("click", ".btn-fadeout", function () {
        ee.emit("fadetype", "linear");
        ee.emit("statechange", "fadeout");
        toggleActive(this);
    });

}

function saveTracksData() {
    var trackdatan = multitrack.getInfo();
    var track0start = Math.trunc(trackdatan.tracks[0].start * 1000);
    var track0end = Math.trunc(trackdatan.tracks[0].end * 1000);
    var track1start = Math.trunc(trackdatan.tracks[1].start * 1000);
    var track1end = Math.trunc(trackdatan.tracks[1].end * 1000);
    var track2start = Math.trunc(trackdatan.tracks[2].start * 1000);
    var track2end = Math.trunc(trackdatan.tracks[2].end * 1000);
    var fadein0 = trackdatan.tracks[0].fadeIn?.duration;
    if (typeof fadein0 === "undefined") {
        fadein0 = -1;        
    } else {
        fadein0 = Math.trunc(trackdatan.tracks[0].fadeIn.duration * 1000);
        console.log("Track 1 Fadein " + fadein0);
    }
    var fadein1 = trackdatan.tracks[1].fadeIn?.duration;
    if (typeof fadein1 === "undefined") {
        fadein1 = -1;
    } else {
        fadein1 = Math.trunc(trackdatan.tracks[1].fadeIn.duration * 1000);
    }
    var fadein2 = trackdatan.tracks[2].fadeIn?.duration;
    if (typeof fadein2 === "undefined") {
        fadein2 = -1;        
    } else {
        fadein2 = Math.trunc(trackdatan.tracks[2].fadeIn.duration * 1000);
    }
    var fadeout0 = trackdatan.tracks[0].fadeOut?.duration;
    if (typeof fadeout0 === "undefined") {
        fadeout0 = -1;
    } else {
        fadeout0 = Math.trunc(trackdatan.tracks[0].fadeOut.duration * 1000);
        fadeout0 = track0end - fadeout0;        
    }
    var fadeout1 = trackdatan.tracks[1].fadeOut?.duration;
    if (typeof tfadeout1 === "undefined") {
        fadeout1 = -1;
    } else {
        fadeout1 = Math.trunc(trackdatan.tracks[1].fadeOut.duration * 1000);
        fadeout1 = track1end - fadeout1;
    }
    var fadeout2 = trackdatan.tracks[2].fadeOut?.duration;
    if (typeof fadeout2 === "undefined") {
        fadeout2 = -1;
    } else {
        fadeout2 = Math.trunc(trackdatan.tracks[2].fadeOut.duration * 1000);
        fadeout2 = track2end - fadeout2;
    }

    if (track2start < track1start) {
        track2start = track1start;
    }

    console.log("Track 1 Start " + track0start + " End " + track0end + " Lineid: " + tr1lineid);
    console.log("Track 2 Start " + track1start + " End " + track1end + " Lineid: " + tr2lineid);
    console.log("Track 3 Start " + track2start + " End " + track2end + " Lineid: " + tr3lineid);

    jQuery.ajax({
        type: "POST",
        url: HOST_URL + '/forms/voicetrack/savetracksdata.php',
        data: {
            lineid1: tr1lineid,
            lineid2: tr2lineid,
            lineid3: tr3lineid,
            tr1start: track0start,
            tr2start: track1start,
            tr3start: track2start,
            tr1end: track0end,
            tr2end: track1end,
            tr3end: track2end,
            seg1end: tra1end,
            seg2end: tra2end,
            seg3end: tra3end,
            fadein1: fadein0,
            fadein2: fadein1,
            fadein3: fadein2,
            fadeout1: fadeout0,
            fadeout2: fadeout1,
            fadeout3: fadeout2,
            logname: thelog
        },
        datatype: 'html',
        success: function (data) {
            var mydata = $.parseJSON(data);
            var fel = mydata.error;
            var kod = mydata.errorcode;
            if (fel == "false") {
                $('#multitrack_voice').modal('hide');
                ee.emit("clear");

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

const element3 = document.getElementById('multitrack_voice');
const modal3 = new bootstrap.Modal(element3);

var initMultitrackVoiceButtons = function () {
    const cancelButton2 = element3.querySelector('[data-kt-multitrack-modal-action="cancel"]');
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
                ee.emit("clear");
            }
        });
    });
    const closeButton2 = element3.querySelector('[data-kt-multitrack-modal-action="close"]');
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
                ee.emit("clear");

            }
        });
    });
}

initMultitrackVoiceButtons();

