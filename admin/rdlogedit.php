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
if (!$json_sett["admin"][$_COOKIE['username']]["hosts"] == 1) {
    header('Location: ' . DIR . '/login');
    exit();
}
$services = $dbfunc->getServices();
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$groupinfo = $dbfunc->getUserGroup($username);
$pagecode = "rdlogedit";
$page_vars = 'rdlogedit';
$page_title = $ml->tr('RDLOGEDIT');
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
$page_js = '<script src="' . DIR . '/assets/static/js/rdlogedit.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('RDLOGEDIT'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('RDLOGEDITMANAGE'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('RDLOGEDIT'); ?>
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
                    <?= $ml->tr('AVALIBLERDLOGEDITHOSTS'); ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="rdlogedit_table">
                        <thead>
                            <tr>
                                <th>
                                    <?= $ml->tr('HOST') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('DEFAULTTRANSTYPE') ?>
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
    <div class="modal fade text-left" id="settings_window" data-bs-backdrop="static" role="dialog"
        aria-labelledby="SettingsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="SettingsLabel">
                        <?= $ml->tr('CONFRDLOGEDIT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdlogedithost-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="conf_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('VOICETRACKERSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="maxrecord">
                                        <?= $ml->tr('MAXRECORDTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="maxrecord" class="form-control fltpick" name="maxrecord"
                                        value="00:00:00">
                                </div>
                                <div class="col-md-4">
                                    <label for="autotrim">
                                        <?= $ml->tr('AUTOTRIMLEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="autotrim" class="form-control" name="autotrim"
                                        value="<?php echo $json_sett["autotrim"]; ?>" min="-99" max="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="normalize">
                                        <?= $ml->tr('NORMALIZELEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="normalize" class="form-control" name="normalize"
                                        value="<?php echo $json_sett["normalize"]; ?>" min="-99" max="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="audiomargin">
                                        <?= $ml->tr('AUDIOMARGIN') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="audiomargin" class="form-control" name="audiomargin"
                                        value="2000" min="0" max="10000">
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
                                        <option value="2">
                                            <?= $ml->tr('PCM24WAV') ?>
                                        </option>
                                        <option value="1">
                                            <?= $ml->tr('MPEGL2') ?>
                                        </option>

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
                                    <label for="2startbutt">
                                        <?= $ml->tr('ENSECSTARTBUTTON') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="2startbutt" name="2startbutt" class="form-select">
                                        <option value="Y" SELECTED>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="waveform">
                                        <?= $ml->tr('WAVEFORMCAPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="waveform" class="form-control" name="waveform"
                                        value="%t - %a">
                                </div>
                                <div class="col-md-4">
                                    <label for="playstart">
                                        <?= $ml->tr('PLAYSTARTCART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <div class="input-group mb-3">
                                        <button class="btn btn-primary" type="button"
                                            id="playstart-button" onclick="add(1)" data-bs-stacked-modal="#macro_select"><?= $ml->tr('SELECTCART') ?></button>
                                        <input type="numbers" class="form-control" id="playstart" name="playstart">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="playend">
                                        <?= $ml->tr('PLAYENDCART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <div class="input-group mb-3">
                                        <button class="btn btn-primary" type="button"
                                            id="playend-button" onclick="add(2)" data-bs-stacked-modal="#macro_select"><?= $ml->tr('SELECTCART') ?></button>
                                        <input type="numbers" class="form-control" id="playend" name="playend">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="recordstart">
                                        <?= $ml->tr('RECORDSTARTCART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <div class="input-group mb-3">
                                        <button class="btn btn-primary" type="button"
                                            id="recordstart-button" onclick="add(3)" data-bs-stacked-modal="#macro_select"><?= $ml->tr('SELECTCART') ?></button>
                                        <input type="numbers" class="form-control" id="recordstart" name="recordstart">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="recordend">
                                        <?= $ml->tr('RECORDENDCART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <div class="input-group mb-3">
                                        <button class="btn btn-primary" type="button"
                                            id="recordend-button" onclick="add(4)" data-bs-stacked-modal="#macro_select"><?= $ml->tr('SELECTCART') ?></button>
                                        <input type="numbers" class="form-control" id="recordend" name="recordend">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="channels">
                                        <?= $ml->tr('AUDIOCHANNELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="channels" name="channels" class="form-select">
                                        <option value="1">
                                            1
                                        </option>
                                        <option value="2" SELECTED>
                                            2
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="defaulttrans">
                                        <?= $ml->tr('DEFAULTTRANSTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="defaulttrans" name="defaulttrans" class="form-select">
                                        <option value="0" SELECTED>
                                            <?= $ml->tr('PLAY') ?>
                                        </option>
                                        <option value="1">
                                            <?= $ml->tr('SEGUE') ?>
                                        </option>
                                        <option value="2">
                                            <?= $ml->tr('STOP') ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="rdlogedithost" name="rdlogedithost" value="">
                        <button type="button" class="btn btn-light-secondary"
                            data-kt-rdlogedithost-modal-action="close">
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
                            <?= $ml->tr('CLOSE') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>