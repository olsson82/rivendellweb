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
$hosts = $dbfunc->getHosts();
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$groupinfo = $dbfunc->getUserGroup($username);
$pagecode = "rdcatch";
$page_vars = 'rdcatch';
$page_title = $ml->tr('RDCATCH');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="'.DIR.'/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="'.DIR.'/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="'.DIR.'/assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/rdcatch.js"></script>';
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
                <form class="form form-horizontal" id="macro_form" action="#">
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
                                    <input type="text" id="start_macro" class="form-control" name="start" value="">
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
                                    <input type="text" id="cart_macro" class="form-control" name="cart" value="" readonly>
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
                                            <input type="checkbox" id="mon_mac" name="monday"
                                                class='form-check-input'>
                                            <label for="mon_mac">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="tue_mac" name="tuesday"
                                                class='form-check-input'>
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
                                            <input type="checkbox" id="fri_mac" name="friday"
                                                class='form-check-input'>
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
                                            <input type="checkbox" id="sun_mac" name="sunday"
                                                class='form-check-input'>
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
    

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>