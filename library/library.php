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
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$usrgroups = $dbfunc->getUserGroup($username);
$schedCodes = $dbfunc->getSchedulerCodes();
$pagecode = "library";
$page_vars = 'library';
$page_title = $ml->tr('LIBRARY');
$page_css = '<link rel="stylesheet" href="' . DIR . '/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="' . DIR . '/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="' . DIR . '/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="' . DIR . '/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="' . DIR . '/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="' . DIR . '/assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="' . DIR . '/assets/static/js/library.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('LIBRARY'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('ALLCARTSONE'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('LIBRARY'); ?>
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
                    <?= $ml->tr('CARTS'); ?>
                </h5>
                <div class="d-flex justify-content-end align-items-center d-none"
                    data-kt-library-table-select="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-library-table-select="selected_count"></span>
                        <?= $ml->tr('SELECTED'); ?>
                    </div>
                    <button type="button" class="btn btn-danger" data-kt-library-table-select="delete_selected">
                        <?= $ml->tr('DELSELECTED'); ?>
                    </button>
                    <button type="button" class="btn btn-primary" data-kt-library-table-select="edit_selected">
                        <?= $ml->tr('MULTIEDITCARTS'); ?>
                    </button>
                </div>
                <div data-kt-library-table-toolbar="base">
                    <?php if ($info->checkusrRights('CREATE_CARTS_PRIV')) { ?>
                        <button data-bs-toggle="modal" data-bs-target="#import_music" class="btn btn-light-success">
                            <?= $ml->tr('IMPORTMUSIC'); ?>
                        </button>
                        <button data-bs-toggle="modal" data-bs-target="#add_cart" class="btn btn-light-warning">
                            <?= $ml->tr('ADDCART'); ?>
                        </button>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="selectGroup"><?= $ml->tr('GROUP') ?></label>
                            <select id="selectGroup" class="form-select">
                                <option value="allgroups">
                                    <?= $ml->tr('ALLGROUPS') ?>
                                </option>
                                <?php foreach ($usrgroups as $ugrp) { ?>
                                    <option value="<?php echo $ugrp; ?>">
                                        <?php echo $ugrp; ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="selectScheduler"><?= $ml->tr('SCHEDULERCODE') ?></label>
                            <select id="selectScheduler" class="form-select">
                                <option value="allsched">
                                    <?= $ml->tr('ALL') ?>
                                </option>
                                <?php foreach ($schedCodes as $scode) { ?>
                                    <option value="<?php echo $scode['code']; ?>">
                                        <?php echo $scode['code']; ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="library_table">
                        <thead>
                            <tr>
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" id="checkall" type="checkbox"
                                            data-kt-check="true" data-kt-check-target="#library_table .form-check-input"
                                            value="1" />
                                    </div>
                                </th>
                                <th>
                                    <?= $ml->tr('CART') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('GROUP') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('TITLE') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('ARTIST') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('AVERAGELENGTH') ?>
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
    <div class="modal fade text-left" id="multi_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="multiEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="multiEditLabel">
                        <?= $ml->tr('MULTIEDITCARTS') ?>
                    </h4>
                    <button type="button" class="close" data-kt-multiedit-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="multi_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="title">
                                        <?= $ml->tr('TITLE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="title" class="form-control" name="title" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="artist">
                                        <?= $ml->tr('ARTIST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="artist" class="form-control" name="artist" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="year">
                                        <?= $ml->tr('YEARRELEASED') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="year" class="form-control" name="year" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="songid">
                                        <?= $ml->tr('SONGID') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="songid" class="form-control" name="songid" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="album">
                                        <?= $ml->tr('ALBUM') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="album" class="form-control" name="album" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="record">
                                        <?= $ml->tr('RECORDLABEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="record" class="form-control" name="record" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="client">
                                        <?= $ml->tr('CLIENT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="client" class="form-control" name="client" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="agency">
                                        <?= $ml->tr('AGENCY') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="agency" class="form-control" name="agency" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="publisher">
                                        <?= $ml->tr('PUBLISHER') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="publisher" class="form-control" name="publisher" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="composer">
                                        <?= $ml->tr('COMPOSER') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="composer" class="form-control" name="composer" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="conductor">
                                        <?= $ml->tr('CONDUCTOR') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="conductor" class="form-control" name="conductor" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="usrdef">
                                        <?= $ml->tr('USERDEFINED') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="usrdef" class="form-control" name="usrdef" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="usagecode">
                                        <?= $ml->tr('USAGE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="usagecode" name="usagecode" class="choices form-select">
                                        <option value="non" SELECTED>
                                            <?= $ml->tr('UNCHANGEBRACK') ?>
                                        </option>
                                        <option value="0">
                                            <?= $ml->tr('FEATURE') ?>
                                        </option>
                                        <option value="1">
                                            <?= $ml->tr('THEMEOPEN') ?>
                                        </option>
                                        <option value="2">
                                            <?= $ml->tr('THEMECLOSE') ?>
                                        </option>
                                        <option value="3">
                                            <?= $ml->tr('THEMEOPENCLOSE') ?>
                                        </option>
                                        <option value="4">
                                            <?= $ml->tr('BACKGROUND') ?>
                                        </option>
                                        <option value="5">
                                            <?= $ml->tr('COMJINGPROMO') ?>
                                        </option>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="schedcodes">
                                        <?= $ml->tr('SCHEDULERCODES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="schedcodes" name="schedcodes[]" class="choices form-select" multiple>
                                        <?php foreach ($schedCodes as $scode) { ?>
                                            <option value="<?php echo $scode['code']; ?>">
                                                <?php echo $scode['code']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="group">
                                        <?= $ml->tr('GROUP') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="group" name="group" class="choices form-select">
                                        <option value="non" SELECTED>
                                            <?= $ml->tr('UNCHANGEBRACK') ?>
                                        </option>
                                        <?php foreach ($usrgroups as $groupdata) { ?>
                                            <option value="<?php echo $groupdata; ?>">
                                                <?php echo $groupdata; ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-kt-multiedit-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <button type="button" id="multieditSave" class="btn btn-primary ms-1">
                            <?= $ml->tr('SAVE') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="import_music" data-bs-backdrop="static" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="myModalLabel33">
                        <?= $ml->tr('IMPORTMUSIC') ?>
                    </h4>
                    <button type="button" class="close" data-kt-import-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="alert alert-primary dropzone needsclick" id="dropzone_upload">
                                    <div class="dz-message needsclick">
                                        <h4 class="alert-heading">
                                            <?= $ml->tr('DROPZONEDROP') ?>
                                        </h4>
                                        <SPAN class="note needsclick">
                                            <?= $ml->tr('DROPZONEDROPMAX') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="musicgroup">
                                        <?= $ml->tr('GROUP') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="musicgroup" name="musicgroup" class="choices form-select">
                                        <?php foreach ($usrgroups as $ugrp) { ?>
                                            <option value="<?php echo $ugrp; ?>">
                                                <?php echo $ugrp; ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="schedcodess">
                                        <?= $ml->tr('SCHEDULERCODES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="schedcodess" name="schedcodes[]" class="choices form-select" multiple>
                                        <?php foreach ($schedCodes as $scode) { ?>
                                            <option value="<?php echo $scode['code']; ?>">
                                                <?php echo $scode['code']; ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="audiochannels">
                                        <?= $ml->tr('AUDIOCHANNELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="audiochannels" name="audiochannels" class="choices form-select">
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>

                                    </select>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="autotrim" name="autotrim"
                                                class='form-check-input' checked>
                                            <label for="autotrim">
                                                <?= $ml->tr('AUTOTRIM') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="trimlevel">
                                        <?= $ml->tr('AUTOTRIMLEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="trimlevel" min="-99" max="0" class="form-control"
                                        name="trimlevel" value="<?php echo AUTOTRIM ?>">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="normalize" name="normalize"
                                                class='form-check-input' checked>
                                            <label for="normalize">
                                                <?= $ml->tr('NORMALIZE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="normalizelevel">
                                        <?= $ml->tr('NORMALIZELEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="normalizelevel" min="-30" max="0" class="form-control"
                                        name="normalizelevel" value="<?php echo NORMALIZE ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-kt-import-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="add_cart" data-bs-backdrop="static" role="dialog"
        aria-labelledby="addcartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-danger">
                    <h4 class="modal-title white" id="addcartLabel">
                        <?= $ml->tr('ADDCART') ?>
                    </h4>
                    <button type="button" class="close" data-kt-add-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="add_cart_form" action="library.php">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="cartgroup">
                                        <?= $ml->tr('GROUP') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="cartgroup" name="cartgroup" class="choices form-select">
                                        <?php foreach ($usrgroups as $ugrp) { ?>
                                            <option value="<?php echo $ugrp; ?>">
                                                <?php echo $ugrp; ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="carttype">
                                        <?= $ml->tr('CARTTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="carttype" name="carttype" class="choices form-select">
                                        <option value="audio" selected>
                                            <?= $ml->tr('AUDIO') ?>
                                        </option>
                                        <option value="macro">
                                            <?= $ml->tr('MACRO') ?>
                                        </option>

                                    </select>
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
                        <input type="submit" class="btn btn-danger ms-1" value="<?= $ml->tr('ADDCART') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>