<?php
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
require $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
if (!$user->is_logged_in()) {
    header('Location: ' . DIR . '/login');
    exit();
}
if (!$json_sett["usrsett"][$_COOKIE['username']]["rdcatch"] == 1) {
    header('Location: ' . DIR . '/login');
    exit();
}
$services = $dbfunc->getServices();
$hosts = $dbfunc->getHosts();
$feeds = $dbfunc->getFeeds();
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$groupinfo = $dbfunc->getUserGroup($username);
$pagecode = "rdcatch";
$page_vars = 'rdcatch';
$page_title = $ml->tr('RDCATCH');
$page_css = '<link rel="stylesheet" href="' . DIR . '/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="' . DIR . '/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="' . DIR . '/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="' . DIR . '/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="' . DIR . '/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="' . DIR . '/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="' . DIR . '/assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="' . DIR . '/assets/static/js/rdcatch.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('RDCATCH'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGERDCATCHEVENT'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('RDCATCH'); ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Basic Tables start -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">
                    <?= $ml->tr('AVRDCATCHEVENTS'); ?>
                </h5>
                <div class="btn-group mb-1">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle me-1" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= $ml->tr('ADD'); ?>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="javascript:;"
                                onclick="add(1)"><?= $ml->tr('MACROCART'); ?></a>
                            <a class="dropdown-item" href="javascript:;" onclick="add(5)"><?= $ml->tr('UPLOAD'); ?></a>
                            <a class="dropdown-item" href="javascript:;"
                                onclick="add(4)"><?= $ml->tr('DOWNLOAD'); ?></a>
                            <a class="dropdown-item" href="javascript:;"
                                onclick="add(2)"><?= $ml->tr('SWITCHEVENT'); ?></a>
                            <a class="dropdown-item" href="javascript:;" onclick="add(3)"><?= $ml->tr('PLAYOUT'); ?></a>
                            <a class="dropdown-item" href="javascript:;" onclick="add(0)"><?= $ml->tr('RECORDING'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="rdcatch_table">
                        <thead>
                            <tr>
                                <th>
                                    <?= $ml->tr('ISACTIVE') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('HOST') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('DESCRIPTION') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('START') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('END') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('SOURCE') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('SUN') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('MON') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('TUE') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('WED') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('THU') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('FRI') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('SAT') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('ONESHOT') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('ACTION') ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

    <div class="modal fade text-left" id="recording_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="RecordingLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="RecordingLabel">
                        <?= $ml->tr('CATCHRECEDIT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdrec-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="recording_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('GENERALSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="eventactive_rec" name="eventactive"
                                                class='form-check-input'>
                                            <label for="eventactive_rec">
                                                <?= $ml->tr('CEVENTACTIVE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_rec">
                                        <?= $ml->tr('LOCATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="location_rec" name="location" class="form-select">
                                        <?php foreach ($hosts as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="audio_rec">
                                        <?= $ml->tr('RECORDINGPORT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="audio_rec" name="audioport" class="form-select">
                                        <option value=""><?= $ml->tr('SELECTAPORT') ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="desc_rec">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="desc_rec" class="form-control" name="desc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="source_rec">
                                        <?= $ml->tr('SOURCE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="source_rec" name="source" class="form-select">
                                        <option value=""><?= $ml->tr('SELECTAPORT') ?></option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcartbuttrec" data-bs-stacked-modal="#macro_select"
                                        class="btn btn-info">
                                        <?= $ml->tr('SELECTCART') ?>
                                    </a>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcutbuttrec" data-bs-stacked-modal="#cut_select"
                                        style="display: none;" class="btn btn-warning">
                                        <?= $ml->tr('SELECTCUT') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="dest_rec">
                                        <?= $ml->tr('DESTINATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="dest_rec" class="form-control" name="dest" value=""
                                        readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="channels_rec">
                                        <?= $ml->tr('AUDIOCHANNELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="channels_rec" name="channels" class="form-select">
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>                                       
                                    </select>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="autotrim_rec" name="autotrim" class='form-check-input'>
                                            <label for="autotrim_rec">
                                                <?= $ml->tr('AUTOTRIM') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="trimlevel_rec">
                                        <?= $ml->tr('AUTOTRIMLEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="trimlevel_rec" class="form-control" min="-99" max="-1"
                                        name="trimlevel" value="<?php echo $json_sett["autotrim"]; ?>">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="normalize_rec" name="normalize" class='form-check-input'>
                                            <label for="normalize_rec">
                                                <?= $ml->tr('NORMALIZE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="normlevel_rec">
                                        <?= $ml->tr('NORMALIZELEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="normlevel_rec" class="form-control" min="-99" max="-1"
                                        name="normlevel" value="<?php echo $json_sett["normalize"]; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="startdateoffset_rec">
                                        <?= $ml->tr('STARTDATEOFFSET') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="startdateoffset_rec" class="form-control" min="0"
                                        name="startdateoffset" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="enddateoffset_rec">
                                        <?= $ml->tr('ENDDATEOFFSET') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="enddateoffset_rec" class="form-control" min="0"
                                        name="enddateoffset" value="0">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="oneshot_rec" name="oneshot"
                                                class='form-check-input'>
                                            <label for="oneshot_rec">
                                                <?= $ml->tr('ONESHOT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('STARTPARAMETERS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" name="startopt"
                                            id="hardtime_rec" checked>
                                        <label class="form-check-label" for="hardtime_rec">
                                        <?= $ml->tr('USEHARDTIME') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_rec">
                                        <?= $ml->tr('RECSTARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="start_rec" class="fltpick form-control" name="start" value="00:00:00">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" name="startopt"
                                            id="gpi_rec">
                                        <label class="form-check-label" for="gpi_rec">
                                        <?= $ml->tr('USEGPI') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gpistart_rec">
                                        <?= $ml->tr('WINDOWSTARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="gpistart_rec" class="fltpick form-control" name="gpistart" value="00:00:00">
                                </div>
                                <div class="col-md-4">
                                    <label for="gpistartend_rec">
                                        <?= $ml->tr('WINDOWENDTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="gpistartend_rec" class="fltpick form-control" name="gpiend" value="00:00:00">
                                </div>
                                <div class="col-md-4">
                                    <label for="gpimatrix_rec">
                                        <?= $ml->tr('GPIMATRIX') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" min="0" max="7" id="gpimatrix_rec" class="form-control"
                                        name="gpimatrix" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="gpiline_rec">
                                        <?= $ml->tr('GPILINE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" min="1" id="gpiline_rec" class="form-control"
                                        name="gpiline" value="1">
                                </div>
                                <div class="col-md-4">
                                    <label for="startdelay_rec">
                                        <?= $ml->tr('STARTDELAY') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="startdelay_rec" class="fltpick form-control" name="startdelay" value="00:00:00">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="multiplerec_rec" name="multiplerec"
                                                class='form-check-input'>
                                            <label for="multiplerec_rec">
                                                <?= $ml->tr('ALLOWMULTIPLEREC') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ENDPARAMETERS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" name="endopt"
                                            id="hardtimeend_rec" checked>
                                        <label class="form-check-label" for="hardtimeend_rec">
                                        <?= $ml->tr('USEHARDTIME') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="recendtime_rec">
                                        <?= $ml->tr('RECORDENDTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="recendtime_rec" class="fltpick form-control" name="recendtime" value="00:00:00">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" name="endopt"
                                            id="lengthend_rec">
                                        <label class="form-check-label" for="lengthend_rec">
                                        <?= $ml->tr('USELENGTH') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="recendtimelength_rec">
                                        <?= $ml->tr('RECORDLENGTH') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="recendtimelength_rec" class="fltpick form-control" name="recendtimelength" value="00:00:00">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" name="endopt"
                                            id="gpiend_rec">
                                        <label class="form-check-label" for="gpiend_rec">
                                        <?= $ml->tr('USEGPI') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gpiendstart_rec">
                                        <?= $ml->tr('WINDOWSTARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="gpiendstart_rec" class="fltpick form-control" name="gpiendstart" value="00:00:00">
                                </div>
                                <div class="col-md-4">
                                    <label for="gpiend_rec">
                                        <?= $ml->tr('WINDOWENDTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="gpiendend_rec" class="fltpick form-control" name="gpiendend" value="00:00:00">
                                </div>
                                <div class="col-md-4">
                                    <label for="gpimatrixend_rec">
                                        <?= $ml->tr('GPIMATRIX') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" min="0" max="7" id="gpimatrixend_rec" class="form-control"
                                        name="gpimatrixend" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="gpilineend_rec">
                                        <?= $ml->tr('GPILINE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" min="1" id="gpilineend_rec" class="form-control"
                                        name="gpilineend" value="1">
                                </div>
                                <div class="col-md-4">
                                    <label for="maxreclength_rec">
                                        <?= $ml->tr('MAXRECORDLENGTH') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="maxreclength_rec" class="fltpick form-control" name="maxreclength" value="00:00:00">
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIVEDAYS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="mon_rec" name="monday" class='form-check-input'>
                                            <label for="mon_rec">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_rec" name="tuesday"
                                                class='form-check-input'>
                                            <label for="tue_rec">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="wed_rec" name="wednesday"
                                                class='form-check-input'>
                                            <label for="wed_rec">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="thu_rec" name="thursday"
                                                class='form-check-input'>
                                            <label for="thu_rec">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="fri_rec" name="friday" class='form-check-input'>
                                            <label for="fri_rec">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sat_rec" name="saturday"
                                                class='form-check-input'>
                                            <label for="sat_rec">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sun_rec" name="sunday" class='form-check-input'>
                                            <label for="sun_rec">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="recid" name="catchid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rdrec-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="playout_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="PlayoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="PlayoutLabel">
                        <?= $ml->tr('CATCHPLAYOUTEDIT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdplay-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="playout_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('GENERALSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="eventactive_play" name="eventactive"
                                                class='form-check-input'>
                                            <label for="eventactive_play">
                                                <?= $ml->tr('CEVENTACTIVE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_play">
                                        <?= $ml->tr('LOCATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="location_play" name="location" class="form-select">
                                        <?php foreach ($hosts as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="audio_play">
                                        <?= $ml->tr('PLAYOUTPORT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="audio_play" name="audioport" class="form-select">
                                        <option value=""><?= $ml->tr('SELECTAPORT') ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_play">
                                        <?= $ml->tr('STARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="start_play" class="fltpick form-control" name="start" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="desc_play">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="desc_play" class="form-control" name="desc" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcartbuttplay" data-bs-stacked-modal="#macro_select"
                                        class="btn btn-info">
                                        <?= $ml->tr('SELECTCART') ?>
                                    </a>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcutbuttplay" data-bs-stacked-modal="#cut_select"
                                        style="display: none;" class="btn btn-warning">
                                        <?= $ml->tr('SELECTCUT') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="dest_play">
                                        <?= $ml->tr('DESTINATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="dest_play" class="form-control" name="dest" value=""
                                        readonly>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="oneshot_play" name="oneshot"
                                                class='form-check-input'>
                                            <label for="oneshot_play">
                                                <?= $ml->tr('ONESHOT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIVEDAYS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="mon_play" name="monday" class='form-check-input'>
                                            <label for="mon_play">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_play" name="tuesday"
                                                class='form-check-input'>
                                            <label for="tue_play">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="wed_play" name="wednesday"
                                                class='form-check-input'>
                                            <label for="wed_play">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="thu_play" name="thursday"
                                                class='form-check-input'>
                                            <label for="thu_play">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="fri_play" name="friday" class='form-check-input'>
                                            <label for="fri_play">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sat_play" name="saturday"
                                                class='form-check-input'>
                                            <label for="sat_play">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sun_play" name="sunday" class='form-check-input'>
                                            <label for="sun_play">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="playid" name="catchid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rdplay-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="switch_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="SwitchLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="SwitchLabel">
                        <?= $ml->tr('CATCHSWITCHEDIT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdswitch-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" autocomplete="off" id="switch_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('GENERALSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="eventactive_switch" name="eventactive"
                                                class='form-check-input'>
                                            <label for="eventactive_switch">
                                                <?= $ml->tr('CEVENTACTIVE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_switch">
                                        <?= $ml->tr('LOCATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="location_switch" name="location" class="form-select">
                                        <?php foreach ($hosts as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_switch">
                                        <?= $ml->tr('STARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="start_switch" class="fltpick form-control" name="start" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="desc_switch">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="desc_switch" class="form-control" name="desc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="matrix_switch">
                                        <?= $ml->tr('SWITCHMATRIX') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="matrix_switch" name="matrix" class="form-select">
                                        <option value=""><?= $ml->tr('SELECTASWITCH') ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="output_switch">
                                        <?= $ml->tr('SWITCHOUTPUT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="output_switch" name="output" class="form-select">
                                        <option value=""><?= $ml->tr('SELECTOUTPUT') ?></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="input_switch">
                                        <?= $ml->tr('SWITCHINPUT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="input_switch" name="input" class="form-select">
                                        <option value=""><?= $ml->tr('SELECTINPUT') ?></option>
                                        <option value="0"><?= $ml->tr('OFF') ?></option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="oneshot_switch" name="oneshot"
                                                class='form-check-input'>
                                            <label for="oneshot_macro">
                                                <?= $ml->tr('ONESHOT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIVEDAYS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="mon_sw" name="monday" class='form-check-input'>
                                            <label for="mon_sw">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_sw" name="tuesday" class='form-check-input'>
                                            <label for="tue_sw">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="wed_sw" name="wednesday"
                                                class='form-check-input'>
                                            <label for="wed_sw">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="thu_sw" name="thursday" class='form-check-input'>
                                            <label for="thu_sw">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="fri_sw" name="friday" class='form-check-input'>
                                            <label for="fri_sw">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sat_sw" name="saturday" class='form-check-input'>
                                            <label for="sat_sw">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sun_sw" name="sunday" class='form-check-input'>
                                            <label for="sun_sw">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="switchid" name="catchid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rdswitch-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" id="subbut_chain" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="upload_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="UploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="UploadLabel">
                        <?= $ml->tr('CATCHEEDITUPLOAD') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdup-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="upload_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('GENERALSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="eventactive_upload" name="eventactive"
                                                class='form-check-input'>
                                            <label for="eventactive_upload">
                                                <?= $ml->tr('CEVENTACTIVE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_upload">
                                        <?= $ml->tr('LOCATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="location_upload" name="location" class="form-select">
                                        <?php foreach ($hosts as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_upload">
                                        <?= $ml->tr('STARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="start_upload" class="fltpick form-control" name="start" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="desc_upload">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="desc_upload" class="form-control" name="desc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="feed_upload">
                                        <?= $ml->tr('RSSFEED') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="feed_upload" name="feed" class="form-select">
                                        <?php foreach ($feeds as $name) { ?>
                                            <option value="-1"><?= $ml->tr('NONE') ?></option>
                                            <option value="<?php echo $name['ID']; ?>">
                                                <?php echo $name['KEY_NAME']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="url_upload">
                                        <?= $ml->tr('URL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="url_upload" class="form-control" name="url" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="usrn_upload">
                                        <?= $ml->tr('USERNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="usrn_upload" class="form-control" name="username" value=""
                                        disable>
                                </div>
                                <div class="col-md-4">
                                    <label for="pass_upload">
                                        <?= $ml->tr('PASSWORD') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="password" id="pass_upload" class="form-control" name="password"
                                        value="" disable>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcartbutt_up" data-bs-stacked-modal="#macro_select"
                                        class="btn btn-info">
                                        <?= $ml->tr('SELECTCART') ?>
                                    </a>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcutbutt_up" data-bs-stacked-modal="#cut_select"
                                        style="display: none;" class="btn btn-warning">
                                        <?= $ml->tr('SELECTCUT') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="source_upload">
                                        <?= $ml->tr('SOURCE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="source_upload" class="form-control" name="source" value=""
                                        readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="for_format">
                                        <?= $ml->tr('EXPORTAUDIOFORMAT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="for_format" name="audioformat" class="form-select">
                                        <option value="0">
                                            <?= $ml->tr('PCM16WAV') ?>
                                        </option>
                                        <option value="7">
                                            <?= $ml->tr('PCM24WAV') ?>
                                        </option>
                                        <option value="2">
                                            <?= $ml->tr('MPEGL2') ?>
                                        </option>
                                        <option value="3">
                                            <?= $ml->tr('MPEGL3') ?>
                                        </option>
                                        <option value="4">
                                            <?= $ml->tr('FLAC') ?>
                                        </option>
                                        <option value="5">
                                            <?= $ml->tr('OGGVORBIS') ?>
                                        </option>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="for_channels">
                                        <?= $ml->tr('AUDIOCHANNELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="for_channels" name="audiochannels" class="form-select">
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="for_samplerate">
                                        <?= $ml->tr('SAMPERATE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="for_samplerate" name="samplerate" class="form-select">
                                        <option value="32000">32000</option>
                                        <option value="44100">44100</option>
                                        <option value="48000">48000</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="for_bitrate">
                                        <?= $ml->tr('BITRATE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="for_bitrate" name="audiobitrate" class="form-select" DISABLED>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="for_quality">
                                        <?= $ml->tr('QUALITY') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" min="0" max="9" id="for_quality" class="form-control"
                                        name="audioquality" value="0" DISABLED>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="normalize_upload" name="normalize"
                                                class='form-check-input'>
                                            <label for="normalize_upload">
                                                <?= $ml->tr('NORMALIZE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="normlevel_upload">
                                        <?= $ml->tr('NORMALIZELEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="normlevel_upload" class="form-control" min="-99" max="-1"
                                        name="normlevel" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="exportme_upload" name="exportmeta"
                                                class='form-check-input'>
                                            <label for="exportme_upload">
                                                <?= $ml->tr('EXPORTLIBRARYMETADATA') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="oneshot_upload" name="oneshot"
                                                class='form-check-input'>
                                            <label for="oneshot_upload">
                                                <?= $ml->tr('ONESHOT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="dayoffset_upload">
                                        <?= $ml->tr('EVENTOFFSETDAYS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="dayoffset_upload" class="form-control" min="-30" max="30"
                                        name="dayoffset" value="">
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIVEDAYS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="mon_up" name="monday" class='form-check-input'>
                                            <label for="mon_up">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_up" name="tuesday" class='form-check-input'>
                                            <label for="tue_up">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="wed_up" name="wednesday"
                                                class='form-check-input'>
                                            <label for="wed_up">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="thu_up" name="thursday" class='form-check-input'>
                                            <label for="thu_up">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="fri_up" name="friday" class='form-check-input'>
                                            <label for="fri_up">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sat_up" name="saturday" class='form-check-input'>
                                            <label for="sat_up">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sun_up" name="sunday" class='form-check-input'>
                                            <label for="sun_up">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="upid" name="catchid" value="">
                        <input type="hidden" id="filpa_up" name="filpa" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rdup-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="download_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="DownloadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="DownloadLabel">
                        <?= $ml->tr('CATCHEDITDOWNLOAD') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rddown-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="download_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('GENERALSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="eventactive_down" name="eventactive"
                                                class='form-check-input'>
                                            <label for="eventactive_down">
                                                <?= $ml->tr('CEVENTACTIVE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_down">
                                        <?= $ml->tr('LOCATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="location_down" name="location" class="form-select">
                                        <?php foreach ($hosts as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_down">
                                        <?= $ml->tr('STARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="start_down" class="fltpick form-control" name="start" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="desc_down">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="desc_down" class="form-control" name="desc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="url_down">
                                        <?= $ml->tr('URL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="url_down" class="form-control" name="url" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="usrn_down">
                                        <?= $ml->tr('USERNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="usrn_down" class="form-control" name="username" value=""
                                        disable>
                                </div>
                                <div class="col-md-4">
                                    <label for="pass_down">
                                        <?= $ml->tr('PASSWORD') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="password" id="pass_down" class="form-control" name="password" value=""
                                        disable>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcartbutt" data-bs-stacked-modal="#macro_select"
                                        class="btn btn-info">
                                        <?= $ml->tr('SELECTCART') ?>
                                    </a>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" id="selcutbutt" data-bs-stacked-modal="#cut_select"
                                        style="display: none;" class="btn btn-warning">
                                        <?= $ml->tr('SELECTCUT') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="dest_down">
                                        <?= $ml->tr('DESTINATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="dest_down" class="form-control" name="dest" value=""
                                        readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="channels_down">
                                        <?= $ml->tr('AUDIOCHANNELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="channels_down" name="channels" class="form-select">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="autotrim_down" name="autotrim"
                                                class='form-check-input'>
                                            <label for="autotrim_down">
                                                <?= $ml->tr('AUTOTRIM') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="trimlevel_down">
                                        <?= $ml->tr('AUTOTRIMLEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="trimlevel_down" class="form-control" min="-99" max="-1"
                                        name="trimlevel" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="normalize_down" name="normalize"
                                                class='form-check-input'>
                                            <label for="normalize_down">
                                                <?= $ml->tr('NORMALIZE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="normlevel_down">
                                        <?= $ml->tr('NORMALIZELEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="normlevel_down" class="form-control" min="-99" max="-1"
                                        name="normlevel" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="updlib_down" name="updatelib"
                                                class='form-check-input'>
                                            <label for="updlib_down">
                                                <?= $ml->tr('UPDATELIBRARYMETADATA') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="oneshot_down" name="oneshot"
                                                class='form-check-input'>
                                            <label for="oneshot_down">
                                                <?= $ml->tr('ONESHOT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="dayoffset_down">
                                        <?= $ml->tr('EVENTOFFSETDAYS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="dayoffset_down" class="form-control" min="-30" max="30"
                                        name="dayoffset" value="">
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIVEDAYS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="mon_dow" name="monday" class='form-check-input'>
                                            <label for="mon_dow">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_dow" name="tuesday" class='form-check-input'>
                                            <label for="tue_dow">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="wed_dow" name="wednesday"
                                                class='form-check-input'>
                                            <label for="wed_dow">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="thu_dow" name="thursday"
                                                class='form-check-input'>
                                            <label for="thu_dow">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="fri_dow" name="friday" class='form-check-input'>
                                            <label for="fri_dow">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sat_dow" name="saturday"
                                                class='form-check-input'>
                                            <label for="sat_dow">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sun_dow" name="sunday" class='form-check-input'>
                                            <label for="sun_dow">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="dowid" name="catchid" value="">
                        <input type="hidden" id="filpa_down" name="filpa" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rddown-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="macro_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="MacroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="MacroLabel">
                        <?= $ml->tr('CATCHEDITCARTEV') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdmacro-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" autocomplete="off" id="macro_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('GENERALSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="eventactive_macro" name="eventactive"
                                                class='form-check-input'>
                                            <label for="eventactive_macro">
                                                <?= $ml->tr('CEVENTACTIVE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="location_macro">
                                        <?= $ml->tr('LOCATION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="location_macro" name="location" class="form-select">
                                        <?php foreach ($hosts as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_macro">
                                        <?= $ml->tr('STARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="start_macro" class="fltpick form-control" name="start" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" data-bs-stacked-modal="#macro_select" class="btn btn-info">
                                        <?= $ml->tr('SELECTCART') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="desc_macro">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="desc_macro" class="form-control" name="desc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="cart_macro">
                                        <?= $ml->tr('CARTNUMBER') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="cart_macro" class="form-control" name="cart" value=""
                                        readonly>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="oneshot_macro" name="oneshot"
                                                class='form-check-input'>
                                            <label for="oneshot_macro">
                                                <?= $ml->tr('ONESHOT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIVEDAYS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="mon_mac" name="monday" class='form-check-input'>
                                            <label for="mon_mac">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_mac" name="tuesday" class='form-check-input'>
                                            <label for="tue_mac">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="wed_mac" name="wednesday"
                                                class='form-check-input'>
                                            <label for="wed_mac">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="thu_mac" name="thursday"
                                                class='form-check-input'>
                                            <label for="thu_mac">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="fri_mac" name="friday" class='form-check-input'>
                                            <label for="fri_mac">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sat_mac" name="saturday"
                                                class='form-check-input'>
                                            <label for="sat_mac">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="sun_mac" name="sunday" class='form-check-input'>
                                            <label for="sun_mac">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="macid" name="catchid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rdmacro-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" id="subbut_chain" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="macro_select" data-bs-backdrop="static" role="dialog"
        aria-labelledby="selCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="selCartLabel">
                        <?= $ml->tr('ADDFROMLIBRARY') ?>
                    </h4>
                    <button type="button" class="close" data-kt-macsel-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <select id="selectGroup" class="choices form-select">
                        <option value="allgroups">
                            <?= $ml->tr('ALLGROUPS') ?>
                        </option>
                        <?php foreach ($groupinfo as $ugrp) { ?>
                            <option value="<?php echo $ugrp; ?>">
                                <?php echo $ugrp; ?>
                            </option>
                        <?php } ?>

                    </select>
                    <div class="table-responsive">
                        <table class="table" id="macroadd_table">
                            <thead>
                                <tr>
                                    <th>
                                        <?= $ml->tr('CART') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('GROUP') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('LENGTH') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('TITLE') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('ARTIST') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('ACTION') ?>
                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="idet" id="idet1" value="<?php echo $id; ?>">
                    <input type="hidden" name="vttype" id="vttype1" value="">
                    <input type="hidden" name="imptype" id="imptype1" value="">
                    <button type="button" class="btn btn-light-secondary" data-kt-macsel-modal-action="close">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">
                            <?= $ml->tr('CLOSE') ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="cut_select" data-bs-backdrop="static" role="dialog"
        aria-labelledby="selCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="selCartLabel">
                        <?= $ml->tr('SELECTCUT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-cutsel-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table" id="cutssel_table">
                            <thead>
                                <tr>
                                    <th>
                                        <?= $ml->tr('CART') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </th>
                                    <th>
                                        <?= $ml->tr('ACTION') ?>
                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-kt-cutsel-modal-action="close">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">
                            <?= $ml->tr('CLOSE') ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>