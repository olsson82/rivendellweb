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
    header('Location: '.DIR.'/login');
    exit();
}
if (!$json_sett["admin"][$_COOKIE['username']]["hosts"] == 1) {
    header('Location: '.DIR.'/login');
    exit();
}
$services = $dbfunc->getServices();
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$pagecode = "rdairplay";
$page_vars = 'rdairplay';
$page_title = $ml->tr('RDAIRPLAY');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="'.DIR.'/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="'.DIR.'/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="'.DIR.'/assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/rdairplay.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('RDAIRPLAY'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGERDAIRPLAY'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('RDAIRPLAY'); ?>
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
                    <?= $ml->tr('AVALIBLERDAIRHOSTS'); ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="rdairplay_table">
                        <thead>
                            <tr>
                                <th>
                                    <?= $ml->tr('HOST') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('DEFSERVICE') ?>
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
                        <?= $ml->tr('CONFRDAIRPLAY') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rdairhost-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="conf_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('LOGSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="mansegue">
                                        <?= $ml->tr('MANSEGUE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="mansegue" class="form-control" name="mansegue" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="forcsegue">
                                        <?= $ml->tr('FORCSEGUE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="forcsegue" class="form-control" name="forcsegue" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="piecountlast">
                                        <?= $ml->tr('PIECOUNTLAST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="piecountlast" class="form-control" name="piecountlast"
                                        value="" min="0" max="60">
                                </div>
                                <div class="col-md-4">
                                    <label for="piecountsto">
                                        <?= $ml->tr('PIECOUNTSTO') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="piecountsto" name="piecountsto" class="form-select">
                                        <option value="0" selected>
                                            <?= $ml->tr('CARTEND') ?>
                                        </option>
                                        <option value="1">
                                            <?= $ml->tr('TRANSITION') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="deftranstype">
                                        <?= $ml->tr('DEFAULTTRANSTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="deftranstype" name="deftranstype" class="form-select">
                                        <option value="0" selected>
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
                                <div class="col-md-4">
                                    <label for="defaultservice">
                                        <?= $ml->tr('DEFSERVICE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="defaultservice" name="defaultservice" class="form-select">
                                        <?php foreach ($services as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('SOUNDPANELSETT') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="systempanels">
                                        <?= $ml->tr('SYSTEMPANELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="systempanels" class="form-control" name="systempanels"
                                        value="" min="0" max="50">
                                </div>
                                <div class="col-md-4">
                                    <label for="userpanels">
                                        <?= $ml->tr('USERPANELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="userpanels" class="form-control" name="userpanels" value=""
                                        min="0" max="50">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="flashpanel" name="flashpanel"
                                                class='form-check-input'>
                                            <label for="flashpanel">
                                                <?= $ml->tr('FLASHPANELBUTT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="buttonpause" name="buttonpause"
                                                class='form-check-input'>
                                            <label for="buttonpause">
                                                <?= $ml->tr('ENABLEBUTTONPAUSE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="labletemp">
                                        <?= $ml->tr('LABELTEMPLATE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="labletemp" class="form-control" name="labletemp" value="">
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('MISCSETTINGS') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="timesunc" name="timesunc"
                                                class='form-check-input'>
                                            <label for="timesunc">
                                                <?= $ml->tr('CHECKTIMESYNC') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="aux1" name="aux1"
                                                class='form-check-input'>
                                            <label for="aux1">
                                                <?= $ml->tr('SHOWAUX1') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="aux2" name="aux2"
                                                class='form-check-input'>
                                            <label for="aux2">
                                                <?= $ml->tr('SHOWAUX2') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="clearcart" name="clearcart"
                                                class='form-check-input'>
                                            <label for="clearcart">
                                                <?= $ml->tr('CLEARCARTSEARCH') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="enabpaused" name="enabpaused"
                                                class='form-check-input'>
                                            <label for="enabpaused">
                                                <?= $ml->tr('ENABLEPAUSEEVENT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="extrabuttons" name="extrabuttons"
                                                class='form-check-input'>
                                            <label for="extrabuttons">
                                                <?= $ml->tr('SHOWEXTRABUTTONS') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="showhour" name="showhour"
                                                class='form-check-input'>
                                            <label for="showhour">
                                                <?= $ml->tr('SHOWHOURSELECTOR') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="preroll">
                                        <?= $ml->tr('AUDITIONPREROLL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="preroll" class="form-control" name="preroll" value=""
                                        min="0" max="60">
                                </div>
                                <div class="col-md-4">
                                    <label for="spacebar">
                                        <?= $ml->tr('SPACEBARACTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="spacebar" name="spacebar" class="form-select">
                                        <option value="0" selected>
                                            <?= $ml->tr('NONE') ?>
                                        </option>
                                        <option value="1">
                                            <?= $ml->tr('STARTNEXT') ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="rdairhost" name="rdairhost" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rdairhost-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" id="subbut_chain" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="add_message" data-bs-backdrop="static" role="dialog"
        aria-labelledby="addMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="addMessageLabel">
                        <?= $ml->tr('ADDMESSAGE') ?>
                    </h4>
                    <button type="button" class="close" data-kt-message-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="addmessage_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="message">
                                        <?= $ml->tr('RDAIRPLAYMESS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="idet" name="idet" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-message-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" id="subbut_chain" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>