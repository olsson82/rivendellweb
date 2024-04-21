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
if (!$json_sett["admin"][$_COOKIE['username']]["groups"] == 1) {
    header('Location: '.DIR.'/login');
    exit();
}
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$services = $dbfunc->getServices();
$pagecode = "groups";
$page_vars = 'groups';
$page_title = $ml->tr('GROUPS');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="https://unpkg.com/huebee@2/dist/huebee.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="'.DIR.'/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="'.DIR.'/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="https://unpkg.com/huebee@2/dist/huebee.pkgd.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="'.DIR.'/assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/groups.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('GROUPS'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGERIVGROUPS'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('GROUPS'); ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">
                    <?= $ml->tr('AVGROUPS'); ?>
                </h5>
                <button data-bs-toggle="modal" data-bs-target="#add_window" class="btn btn-light-success">
                    <?= $ml->tr('ADDGROUP'); ?>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="groups_table">
                        <thead>
                            <tr>
                                <th>
                                    <?= $ml->tr('NAME') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('DESCRIPTION') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('STARTCART') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('ENDCART') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('ENFRANGE') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('MUSICREPORT') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('TRAFFICREPORT') ?>
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

    <div class="modal fade text-left" id="rename_window" data-bs-backdrop="static" role="dialog"
        aria-labelledby="RenameLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="RenameLabel">
                        <?= $ml->tr('RENAMEGROUP') ?>
                    </h4>
                    <button type="button" class="close" data-kt-rename-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="rename_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="re_groupname">
                                        <?= $ml->tr('GROUPNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="re_groupname" class="form-control" name="groupname">
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="re_groupid" name="groupid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-rename-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="edit_window" data-bs-backdrop="static" role="dialog"
        aria-labelledby="EditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="EditLabel">
                        <?= $ml->tr('EDITGROUP') ?>
                    </h4>
                    <button type="button" class="close" data-kt-edit-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="edit_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="groupname">
                                        <?= $ml->tr('GROUPNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="groupname" class="form-control" name="groupname" value=""
                                        READONLY>
                                </div>
                                <div class="col-md-4">
                                    <label for="groupdesc">
                                        <?= $ml->tr('GROUPDESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="groupdesc" class="form-control" name="groupdesc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="gimport">
                                        <?= $ml->tr('DEFGROUPIMPORT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="gimport" class="form-control" name="gimport" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="emailaddresses">
                                        <?= $ml->tr('NOTEMAILADDRESSES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="emailaddresses" class="form-control" name="emailaddresses"
                                        value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="carttype">
                                        <?= $ml->tr('DEFCARTTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="carttype" name="carttype" class="form-select">
                                        <option value="1" selected>
                                            <?= $ml->tr('AUDIO') ?>
                                        </option>
                                        <option value="2">
                                            <?= $ml->tr('MACRO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="cartstart">
                                        <?= $ml->tr('DEFCARTSTART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="cartstart" class="form-control" name="cartstart" min="1"
                                        value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="cartend">
                                        <?= $ml->tr('DEFCARTEND') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="cartend" class="form-control" name="cartend" min="1"
                                        value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="enfcart" name="enfcart" class='form-check-input'>
                                            <label for="enfcart">
                                                <?= $ml->tr('ENFCARTRANGE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="inctraffic" name="inctraffic"
                                                class='form-check-input'>
                                            <label for="inctraffic">
                                                <?= $ml->tr('INCTRAFFREP') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="incmusic" name="incmusic"
                                                class='form-check-input'>
                                            <label for="incmusic">
                                                <?= $ml->tr('INCMUSREP') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="enddatetime" name="enddatetime"
                                                class='form-check-input'>
                                            <label for="enddatetime">
                                                <?= $ml->tr('SETENDDATETIME') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="cutcreation">
                                        <?= $ml->tr('DAYSAFTERCREATIONCUT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="cutcreation" class="form-control" name="cutcreation"
                                        min="0" value="" DISABLED>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="purge" name="purge" class='form-check-input'>
                                            <label for="purge">
                                                <?= $ml->tr('PURGEEXPIREDCUTS') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="purgedays">
                                        <?= $ml->tr('PURGEAFTERDAYS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="purgedays" class="form-control" name="purgedays" min="0"
                                        value="" DISABLED>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="delempty" name="delempty"
                                                class='form-check-input' disabled>
                                            <label for="delempty">
                                                <?= $ml->tr('DELCARTIFEMPTY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="activeservice">
                                        <?= $ml->tr('ACTIVESERVICES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="activeservice" name="activeservice[]" class="form-select" multiple>
                                        <?php foreach ($services as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="color">
                                        <?= $ml->tr('COLOR') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" data-huebee='{ "notation": "hex", "saturations": 2 }' id="color"
                                        class="form-control color-input" name="color">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="groupid" name="groupid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-edit-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="add_window" data-bs-backdrop="static" role="dialog" aria-labelledby="AddLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="AddLabel">
                        <?= $ml->tr('ADDGROUP') ?>
                    </h4>
                    <button type="button" class="close" data-kt-add-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="add_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_allusr" name="allusergroup"
                                                class='form-check-input'>
                                            <label for="add_allusr">
                                                <?= $ml->tr('ENABLEGROUPALLUSR') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="add_groupname">
                                        <?= $ml->tr('GROUPNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="add_groupname" class="form-control" name="groupname"
                                        value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="add_groupdesc">
                                        <?= $ml->tr('GROUPDESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="add_groupdesc" class="form-control" name="groupdesc"
                                        value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="add_gimport">
                                        <?= $ml->tr('DEFGROUPIMPORT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="add_gimport" class="form-control" name="gimport" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="add_emailaddresses">
                                        <?= $ml->tr('NOTEMAILADDRESSES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="add_emailaddresses" class="form-control"
                                        name="emailaddresses" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="carttype">
                                        <?= $ml->tr('DEFCARTTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="add_carttype" name="carttype" class="form-select">
                                        <option value="1" selected>
                                            <?= $ml->tr('AUDIO') ?>
                                        </option>
                                        <option value="2">
                                            <?= $ml->tr('MACRO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="add_cartstart">
                                        <?= $ml->tr('DEFCARTSTART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="add_cartstart" class="form-control" name="cartstart"
                                        min="1" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="add_cartend">
                                        <?= $ml->tr('DEFCARTEND') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="add_cartend" class="form-control" name="cartend" min="1"
                                        value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_enfcart" name="enfcart"
                                                class='form-check-input'>
                                            <label for="add_enfcart">
                                                <?= $ml->tr('ENFCARTRANGE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_inctraffic" name="inctraffic"
                                                class='form-check-input'>
                                            <label for="add_inctraffic">
                                                <?= $ml->tr('INCTRAFFREP') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_incmusic" name="incmusic"
                                                class='form-check-input'>
                                            <label for="add_incmusic">
                                                <?= $ml->tr('INCMUSREP') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_enddatetime" name="enddatetime"
                                                class='form-check-input'>
                                            <label for="add_enddatetime">
                                                <?= $ml->tr('SETENDDATETIME') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="add_cutcreation">
                                        <?= $ml->tr('DAYSAFTERCREATIONCUT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="add_cutcreation" class="form-control" name="cutcreation"
                                        min="0" value="" DISABLED>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_purge" name="purge" class='form-check-input'>
                                            <label for="add_purge">
                                                <?= $ml->tr('PURGEEXPIREDCUTS') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="add_purgedays">
                                        <?= $ml->tr('PURGEAFTERDAYS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="add_purgedays" class="form-control" name="purgedays"
                                        min="0" value="" DISABLED>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="add_delempty" name="delempty"
                                                class='form-check-input' disabled>
                                            <label for="add_delempty">
                                                <?= $ml->tr('DELCARTIFEMPTY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="add_activeservice">
                                        <?= $ml->tr('ACTIVESERVICES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="add_activeservice" name="activeservice[]" class="form-select" multiple>
                                        <?php foreach ($services as $name) { ?>
                                            <option value="<?php echo $name; ?>">
                                                <?php echo $name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="add_color">
                                        <?= $ml->tr('COLOR') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" data-huebee='{ "notation": "hex", "saturations": 2 }'
                                        id="add_color" class="form-control color-input" name="color">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-kt-add-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>