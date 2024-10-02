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
let wavesurfer, record
let scrollingWaveform = false
var lineid;
var vtgroup;
var cartid;
var logname;
var username;
let newblob;

const createWaveSurfer = () => {
    if (wavesurfer) {
        wavesurfer.destroy()
    }
    wavesurfer = WaveSurfer.create({
        container: '#mic',
        waveColor: 'rgb(200, 0, 200)',
        progressColor: 'rgb(100, 0, 100)',
    })

    record = wavesurfer.registerPlugin(WaveSurfer.Record.create({ scrollingWaveform, renderRecordedAudio: false }))

    record.on('record-end', (blob) => {
        const container = document.querySelector('#recordings')
        const recordedUrl = URL.createObjectURL(blob)

        const wavesurfer = WaveSurfer.create({
            container,
            waveColor: 'rgb(200, 100, 0)',
            progressColor: 'rgb(100, 50, 0)',
            url: recordedUrl,
        })
        let xrid = Math.floor((Math.random() * 100) + 1);
        const button = container.appendChild(document.createElement('button'))
        button.textContent = TRAN_PLAY
        button.className = "btn btn-success"
        button.onclick = () => wavesurfer.playPause()
        wavesurfer.on('pause', () => (button.textContent = TRAN_PLAY))
        wavesurfer.on('play', () => (button.textContent = TRAN_PAUSE))
        const buttonsave = container.appendChild(document.createElement('button'))
        buttonsave.textContent = TRAN_SAVEREQ
        buttonsave.className = "btn btn-warning"
        buttonsave.id = xrid
        buttonsave.onclick = () => convertBlobToAudioBuffer(blob, lineid, vtgroup, xrid)
        const link = container.appendChild(document.createElement('a'))
        Object.assign(link, {
            href: recordedUrl,
            className: "btn btn-primary",
            download: 'recording.' + blob.type.split(';')[0].split('/')[1] || 'webm',
            textContent: TRAN_DOWN_REQ,
        })
    })
    pauseButton.style.display = 'none'
    recButton.textContent = TRAN_RECORD

    record.on('record-progress', (time) => {
        updateProgress(time)
    })

}

const progress = document.querySelector('#progress')
const updateProgress = (time) => {
    const formattedTime = [
        Math.floor((time % 3600000) / 60000),
        Math.floor((time % 60000) / 1000),
    ]
        .map((v) => (v < 10 ? '0' + v : v))
        .join(':')
    progress.textContent = formattedTime
}

const pauseButton = document.querySelector('#pause')
pauseButton.onclick = () => {
    if (record.isPaused()) {
        record.resumeRecording()
        pauseButton.textContent = TRAN_PAUSE
        return
    }

    record.pauseRecording()
    pauseButton.textContent = TRAN_RESUME
}


const recButton = document.querySelector('#record')

recButton.onclick = () => {
    if (record.isRecording() || record.isPaused()) {
        record.stopRecording()
        recButton.textContent = TRAN_RECORD
        pauseButton.style.display = 'none'
        return
    }

    recButton.disabled = true
    record.startRecording(record.startMic()).then(() => {
        recButton.textContent = TRAN_STOP
        recButton.disabled = false
        pauseButton.style.display = 'inline'
    })
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

function recordvoice(i, o, u, z, w) {
    if (ALLOW_CREATE == 1) {
        lineid = i;
        vtgroup = o;
        cartid = u;
        logname = z;
        username = w;
        createWaveSurfer()
        $("#record_voice").modal("show");
    } else {
        Swal.fire({
            text: TRAN_NORIGHTS,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: TRAN_OK,
            customClass: {
                confirmButton: "btn fw-bold btn-primary"
            }
        });
    }
}

function uploadvoice(i, o, u, z, w) {
    if (ALLOW_CREATE == 1) {
        lineid = i;
        vtgroup = o;
        cartid = u;
        logname = z;
        username = w;
        $("#upload_voice").modal("show");
    } else {
        Swal.fire({
            text: TRAN_NORIGHTS,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: TRAN_OK,
            customClass: {
                confirmButton: "btn fw-bold btn-primary"
            }
        });
    }
}

Dropzone.autoDiscover = false;
var myDropzone = new Dropzone("#dropzone_upload", {
    url: HOST_URL + "/forms/voicetrack/chunk-upload.php",
    parallelUploads: 1,
    parallelChunkUploads: true,
    retryChunks: true,
    retryChunksLimit: 3,
    forceChunking: true,
    chunkSize: 1000000,
    maxFiles: 1,
    chunking: true,
    acceptedFiles: ".mp3,.wav",
    maxFilesize: 500,
    chunksUploaded: function (file, done) {
        let currentFile = file;
        jQuery.ajax({
            type: "POST",
            url: HOST_URL + '/forms/voicetrack/chunk-import.php',
            data: {
                dzuuid: currentFile.upload.uuid,
                dztotalchunkcount: currentFile.upload.totalChunkCount,
                fileName: currentFile.name.substr((currentFile.name.lastIndexOf('.') + 1)),
                audiochannels: $('#audiochannels').val(),
                autotrim: $('#autotrim').val(),
                trimlevel: $('#trimlevel').val(),
                normalize: $('#normalize').val(),
                normalizelevel: $('#normalizelevel').val(),
                musicgroup: vtgroup,
                logname: logname,
                cartid: cartid,
                lineid: lineid
            },
            datatype: 'html',
            success: function (data) {
                myDropzone.removeFile(file);
                $.ajax({
                    type: "POST",
                    url: HOST_URL + '/forms/voicetrack/rowinfo.php',
                    data: {
                        lineid: lineid,
                        logname: logname
                    },
                    dataType: 'json',
                    success: function (data) {
                        $("#cart_" + lineid).html(data['CART_NUMBER']);
                        $("#artist_" + lineid).html(data['ARTIST']);
                        $("#title_" + lineid).html(data['TITLE']);
                        $("#length_" + lineid).html(getTimeFromMillis(data['AVERAGE_LENGTH']));
                        if (ALLOW_MULTITRACK == '1') {
                        $("#buttons_" + lineid).html(`<div class="btn-group mb-3" role="group"
                        aria-label="`+ TRAN_VOICETRACKER + `">
                        <button type="button"
                            onclick="recordvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_RECORD + `"
                            class="btn btn-danger"><i
                                class="bi bi-mic"></i></button>
                        <button type="button"
                            onclick="uploadvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_UPLOAD + `"
                            class="btn btn-warning"><i
                                class="bi bi-cloud-upload"></i></button>
                        <button type="button"
                                onclick="doMultitrack(`+ lineid + `,'` + logname + `')"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="`+ TRAN_MULTITRACKEDITOR + `"
                                class="btn btn-success"><i
                                    class="bi bi-music-note-list"></i></button>
                    </div>`);
                } else {
                    $("#buttons_" + lineid).html(`<div class="btn-group mb-3" role="group"
                        aria-label="`+ TRAN_VOICETRACKER + `">
                        <button type="button"
                            onclick="recordvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_RECORD + `"
                            class="btn btn-danger"><i
                                class="bi bi-mic"></i></button>
                        <button type="button"
                            onclick="uploadvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_UPLOAD + `"
                            class="btn btn-warning"><i
                                class="bi bi-cloud-upload"></i></button>
                    </div>`);
                }
                        $("#upload_voice").modal("hide");
                    }
                });
            },
            error: function (msg) {
                currentFile.accepted = false;
                myDropzone._errorProcessing([currentFile], msg.responseText);
            }
        });

    },
});

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

function getWavBytes(buffer, options) {
    const type = options.isFloat ? Float32Array : Uint16Array
    const numFrames = buffer.byteLength / type.BYTES_PER_ELEMENT

    const headerBytes = getWavHeader(Object.assign({}, options, { numFrames }))
    const wavBytes = new Uint8Array(headerBytes.length + buffer.byteLength);

    // prepend header, then add pcmBytes
    wavBytes.set(headerBytes, 0)
    wavBytes.set(new Uint8Array(buffer), headerBytes.length)

    return wavBytes
}

function getWavHeader(options) {
    const numFrames = options.numFrames
    const numChannels = options.numChannels || 2
    const sampleRate = options.sampleRate || 44100
    const bytesPerSample = options.isFloat ? 4 : 2
    const format = options.isFloat ? 3 : 1

    const blockAlign = numChannels * bytesPerSample
    const byteRate = sampleRate * blockAlign
    const dataSize = numFrames * blockAlign

    const buffer = new ArrayBuffer(44)
    const dv = new DataView(buffer)

    let p = 0

    function writeString(s) {
        for (let i = 0; i < s.length; i++) {
            dv.setUint8(p + i, s.charCodeAt(i))
        }
        p += s.length
    }

    function writeUint32(d) {
        dv.setUint32(p, d, true)
        p += 4
    }

    function writeUint16(d) {
        dv.setUint16(p, d, true)
        p += 2
    }

    writeString('RIFF')
    writeUint32(dataSize + 36)
    writeString('WAVE')
    writeString('fmt ')
    writeUint32(16)
    writeUint16(format)
    writeUint16(numChannels)
    writeUint32(sampleRate)
    writeUint32(byteRate)
    writeUint16(blockAlign)
    writeUint16(bytesPerSample * 8)
    writeString('data')
    writeUint32(dataSize)

    return new Uint8Array(buffer)
}

function convertAudioBufferToBlob(audioBuffer) {
    var channelData = [],
        totalLength = 0,
        channelLength = 0;

    for (var i = 0; i < audioBuffer.numberOfChannels; i++) {
        channelData.push(audioBuffer.getChannelData(i));
        totalLength += channelData[i].length;
        if (i == 0) channelLength = channelData[i].length;
    }

    const interleaved = new Float32Array(totalLength);

    for (
        let src = 0, dst = 0;
        src < channelLength;
        src++, dst += audioBuffer.numberOfChannels
    ) {
        for (var j = 0; j < audioBuffer.numberOfChannels; j++) {
            interleaved[dst + j] = channelData[j][src];
        }
    }

    const wavBytes = getWavBytes(interleaved.buffer, {
        isFloat: true,
        numChannels: audioBuffer.numberOfChannels,
        sampleRate: 48000,
    });
    const wav = new Blob([wavBytes], { type: "audio/wav" });
    return wav;
}

function convertBlobToAudioBuffer(myBlob, rdline, rdgroup, idnomb) {
    const audioContext = new AudioContext();
    const fileReader = new FileReader();
    $("#"+idnomb).prop("disabled",true);

    fileReader.onloadend = () => {

        let myArrayBuffer = fileReader.result;

        audioContext.decodeAudioData(myArrayBuffer, (audioBuffer) => {

            let blob = convertAudioBufferToBlob(audioBuffer);
            importToCart(blob, rdline, rdgroup, idnomb)
        });
    };
    fileReader.readAsArrayBuffer(myBlob);
}

function importToCart(thefile, rdline, rdgroup, idnomb) {
    var fd = new FormData();
    fd.append("audio_data", thefile, rdline);
    fd.append("line", rdline);
    fd.append("cart", cartid);
    fd.append("group", rdgroup);
    fd.append("logname", logname);
    fd.append("username", username);
    fd.append("audiochannels", $("#audiochannels_rec").val());
    fd.append("autotrim", $("#autotrim_rec").val());
    fd.append("trimlevel", $("#trimlevel_rec").val());
    fd.append("normalize", $("#normalize_rec").val());
    fd.append("normalizelevel", $("#normalizelevel_rec").val());

    jQuery.ajax({
        type: "POST",
        url: HOST_URL + "/forms/voicetrack/importvoicetrack.php",
        data: fd,
        async: false,
        success: function () {
            $("#record_voice").modal("hide");
            $("#"+idnomb).prop("disabled",false);
            $("#recordings").empty();
            $.ajax({
                type: "POST",
                url: HOST_URL + '/forms/voicetrack/rowinfo.php',
                data: {
                    lineid: lineid,
                    logname: logname
                },
                dataType: 'json',
                success: function (data) {
                    $("#cart_" + lineid).html(data['CART_NUMBER']);
                    $("#artist_" + lineid).html(data['ARTIST']);
                    $("#title_" + lineid).html(data['TITLE']);
                    $("#length_" + lineid).html(getTimeFromMillis(data['AVERAGE_LENGTH']));
                    if (ALLOW_MULTITRACK == '1') {
                    $("#buttons_" + lineid).html(`<div class="btn-group mb-3" role="group"
                        aria-label="`+ TRAN_VOICETRACKER + `">
                        <button type="button"
                            onclick="recordvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_RECORD + `"
                            class="btn btn-danger"><i
                                class="bi bi-mic"></i></button>
                        <button type="button"
                            onclick="uploadvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_UPLOAD + `"
                            class="btn btn-warning"><i
                                class="bi bi-cloud-upload"></i></button>
                            <button type="button"
                                onclick="doMultitrack(`+ lineid + `,'` + logname + `')"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="`+ TRAN_MULTITRACKEDITOR + `"
                                class="btn btn-success"><i
                                    class="bi bi-music-note-list"></i></button>
                    </div>`);
                    } else {
                        $("#buttons_" + lineid).html(`<div class="btn-group mb-3" role="group"
                        aria-label="`+ TRAN_VOICETRACKER + `">
                        <button type="button"
                            onclick="recordvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_RECORD + `"
                            class="btn btn-danger"><i
                                class="bi bi-mic"></i></button>
                        <button type="button"
                            onclick="uploadvoice(`+ lineid + `,'` + VT_GROUP + `','` + data['CART_NUMBER'] + `', '` + logname + `', '` + VT_USERNAME + `')"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="`+ TRAN_UPLOAD + `"
                            class="btn btn-warning"><i
                                class="bi bi-cloud-upload"></i></button>
                    </div>`); 
                    }
                }
            });


        },
        cache: false,
        contentType: false,
        processData: false
    });
}

const element1 = document.getElementById('upload_voice');
const modal1 = new bootstrap.Modal(element1);

var initUploadVoiceButtons = function () {
    const cancelButton2 = element1.querySelector('[data-kt-upload-modal-action="cancel"]');
    cancelButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSEUPLOADWINDOWVOICE,
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
    const closeButton2 = element1.querySelector('[data-kt-upload-modal-action="close"]');
    closeButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSEUPLOADWINDOWVOICE,
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

const element2 = document.getElementById('record_voice');
const modal2 = new bootstrap.Modal(element2);

var initRecordVoiceButtons = function () {
    const cancelButton2 = element2.querySelector('[data-kt-record-modal-action="cancel"]');
    cancelButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSERECORDWINDOWVOICE,
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
                wavesurfer.destroy();
            }
        });
    });
    const closeButton2 = element2.querySelector('[data-kt-record-modal-action="close"]');
    closeButton2.addEventListener('click', e => {
        e.preventDefault();

        Swal.fire({
            text: TRAN_CLOSERECORDWINDOWVOICE,
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
                wavesurfer.destroy();

            }
        });
    });
}

initUploadVoiceButtons();
initRecordVoiceButtons();