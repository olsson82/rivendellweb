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

$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$cutinfo = $dbfunc->getCutInfo($_GET['id']);
$groupinfo = $dbfunc->getUserGroup($username);
$schedCodes = $dbfunc->getSchedulerCodes();
$id = $_GET['id'];
$pagecode = "library";
$page_vars = 'cart';
$page_title = $ml->tr('CART');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="'.DIR.'/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="'.DIR.'/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="'.DIR.'/assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/record.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/timeline.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/cart.js"></script>';


if (!$info->checkMacroNormal($id, 1)) {
    header('Location: '.DIR.'/library/carts/cart/macro/'.$_GET['id']);
    exit();
}

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('VIEWCART'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGECART {{' . $id . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/library/carts">
                                <?= $ml->tr('LIBRARY'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('VIEWCART'); ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <?= $ml->tr('CARTINFO'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="cart_form" action="#" method="get">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="number">
                                            <?= $ml->tr('NUMBER') ?>
                                        </label>
                                        <input type="text" name="number" id="number" class="form-control"
                                            value="<?php echo $info->getCartInfo($id, "NUMBER"); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="avleng">
                                            <?= $ml->tr('AVERAGELENGTH') ?>
                                        </label>
                                        <input type="text" name="avleng" id="avleng" class="form-control" value=""
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="enflength" name="enflength" value="1"
                                                class="form-check-input">
                                            <label for="enflength">
                                                <?= $ml->tr('ENFORCELENGTH') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="frlength">
                                            <?= $ml->tr('FORCEDLENGTH') ?>
                                        </label>
                                        <input type="text" name="frlength" id="frlength" class="form-control" value=""
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="beatmin">
                                            <?= $ml->tr('BEATSPERMINUTE') ?>
                                        </label>
                                        <input type="text" name="beatmin" id="beatmin" class="form-control"
                                            value="<?php echo $info->getCartInfo($id, "BPM"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="group">
                                            <?= $ml->tr('GROUP') ?>
                                        </label>
                                        <select id="group" name="group" class="choices form-select">
                                            <?php foreach ($groupinfo as $groupdata) { ?>
                                                <option value="<?php echo $groupdata; ?>" <?php if ($info->getCartInfo($_GET["id"], "GROUP_NAME") == $groupdata) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $groupdata; ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="usagecode">
                                            <?= $ml->tr('USAGE') ?>
                                        </label>
                                        <select id="usagecode" name="usagecode" class="choices form-select">
                                            <option value="0" <?php if ($info->getCartInfo($_GET["id"], "USAGE_CODE") == 0) {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('FEATURE') ?>
                                            </option>
                                            <option value="1" <?php if ($info->getCartInfo($_GET["id"], "USAGE_CODE") == 1) {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('THEMEOPEN') ?>
                                            </option>
                                            <option value="2" <?php if ($info->getCartInfo($_GET["id"], "USAGE_CODE") == 2) {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('THEMECLOSE') ?>
                                            </option>
                                            <option value="3" <?php if ($info->getCartInfo($_GET["id"], "USAGE_CODE") == 3) {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('THEMEOPENCLOSE') ?>
                                            </option>
                                            <option value="4" <?php if ($info->getCartInfo($_GET["id"], "USAGE_CODE") == 4) {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('BACKGROUND') ?>
                                            </option>
                                            <option value="5" <?php if ($info->getCartInfo($_GET["id"], "USAGE_CODE") == 5) {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('COMJINGPROMO') ?>
                                            </option>

                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="schedcodes">
                                            <?= $ml->tr('SCHEDULERCODES') ?>
                                        </label>
                                        <select id="schedcodes" name="schedcodes[]" class="choices form-select"
                                            multiple>
                                            <?php foreach ($schedCodes as $scode) { ?>
                                                <option value="<?php echo $scode['code']; ?>" <?php if ($info->getCartSchedCode($id, $scode['code']) == $scode['code']) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $scode['code']; ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="title">
                                            <?= $ml->tr('TITLE') ?>
                                        </label>
                                        <input type="text" id="title" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "TITLE"); ?>"
                                            name="title">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="artist">
                                            <?= $ml->tr('ARTIST') ?>
                                        </label>
                                        <input type="text" id="artist" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "ARTIST"); ?>"
                                            name="artist">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="year">
                                            <?= $ml->tr('YEARRELEASED') ?>
                                        </label>
                                        <input type="text" id="year" class="form-control"
                                            value="<?php echo date('Y', strtotime($info->getCartInfo($_GET["id"], "YEAR"))); ?>"
                                            name="year">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="songid">
                                            <?= $ml->tr('SONGID') ?>
                                        </label>
                                        <input type="text" id="songid" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "SONG_ID"); ?>"
                                            name="songid">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="album">
                                            <?= $ml->tr('ALBUM') ?>
                                        </label>
                                        <input type="text" id="album" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "ALBUM"); ?>"
                                            name="album">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="record">
                                            <?= $ml->tr('RECORDLABEL') ?>
                                        </label>
                                        <input type="text" id="record" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "LABEL"); ?>"
                                            name="record">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="client">
                                            <?= $ml->tr('CLIENT') ?>
                                        </label>
                                        <input type="text" id="client" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "CLIENT"); ?>"
                                            name="client">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="agency">
                                            <?= $ml->tr('AGENCY') ?>
                                        </label>
                                        <input type="text" id="agency" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "AGENCY"); ?>"
                                            name="agency">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="publisher">
                                            <?= $ml->tr('PUBLISHER') ?>
                                        </label>
                                        <input type="text" id="publisher" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "PUBLISHER"); ?>"
                                            name="publisher">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="composer">
                                            <?= $ml->tr('COMPOSER') ?>
                                        </label>
                                        <input type="text" id="composer" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "COMPOSER"); ?>"
                                            name="composer">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="conductor">
                                            <?= $ml->tr('CONDUCTOR') ?>
                                        </label>
                                        <input type="text" id="conductor" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "CONDUCTOR"); ?>"
                                            name="conductor">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="userdef">
                                            <?= $ml->tr('USERDEFINED') ?>
                                        </label>
                                        <input type="text" id="userdef" class="form-control"
                                            value="<?php echo $info->getCartInfo($_GET["id"], "USER_DEFINED"); ?>"
                                            name="userdef">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-2">
                                <label for="schedcuts" class="form-label">
                                    <?= $ml->tr('SCHEDULECUTS') ?>
                                </label>
                                <select id="schedcuts" name="schedcuts" class="choices form-select">
                                            <option value="Y" <?php if ($info->getCartInfo($_GET["id"], "USE_WEIGHTING") == 'Y') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('SCHEDULECUTSW') ?>
                                            </option>
                                            <option value="N" <?php if ($info->getCartInfo($_GET["id"], "USE_WEIGHTING") == "N") {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('SCHEDULECUTSO') ?>
                                            </option>

                                        </select>
                            </div>
                            <div class="form-group my-2">
                                <label for="notes" class="form-label">
                                    <?= $ml->tr('NOTES') ?>
                                </label>
                                <textarea class="form-control" id="notes" name="notes"
                                    rows="3"><?php echo $info->getCartInfo($_GET["id"], "NOTES"); ?></textarea>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <input type="hidden" id="enfrangecorr" name="enfrangecorr" value="">
                                <button type="submit" class="btn btn-primary">
                                    <?= $ml->tr('SAVE') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">
                            <?= $ml->tr('CUTS') ?>
                        </h5>
                        <button onclick="addcut('<?php echo $id; ?>')" class="btn btn-light-warning">
                            <?= $ml->tr('ADDCUT'); ?>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="cuts_table">
                                <thead>
                                    <tr>
                                        <th>
                                            <?= $ml->tr('NAME') ?>
                                        </th>
                                        <?php if ($info->getCartInfo($_GET["id"], "USE_WEIGHTING") == 'Y') { ?>
                                            <th id="tabord1">
                                                <?= $ml->tr('WT') ?>
                                            </th>
                                        <?php } else { ?>
                                            <th id="tabord1">
                                                <?= $ml->tr('ORD') ?>
                                            </th>
                                        <?php } ?>
                                        <th>
                                            <?= $ml->tr('DESCRIPTION') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('LENGTH') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('LASTPLAYED') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('OFPLAYS') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('ACTION') ?>
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>
                                            <?= $ml->tr('NAME') ?>
                                        </th>
                                        <?php if ($info->getCartInfo($_GET["id"], "USE_WEIGHTING") == 'Y') { ?>
                                            <th id="tabord2">
                                                <?= $ml->tr('WT') ?>
                                            </th>
                                        <?php } else { ?>
                                            <th id="tabord2">
                                                <?= $ml->tr('ORD') ?>
                                            </th>
                                        <?php } ?>
                                        <th>
                                            <?= $ml->tr('DESCRIPTION') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('LENGTH') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('LASTPLAYED') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('OFPLAYS') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('ACTION') ?>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="record_voice" data-bs-backdrop="static" role="dialog"
        aria-labelledby="recordLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-danger">
                    <h4 class="modal-title white" id="recordLabel">
                        <?= $ml->tr('RECORD') ?>
                    </h4>
                    <button type="button" class="close" data-kt-record-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-4">
                                <label for="audiochannels_rec">
                                    <?= $ml->tr('AUDIOCHANNELS') ?>
                                </label>
                            </div>
                            <div class="col-md-8 form-group">
                                <select id="audiochannels_rec" name="audiochannels" class="choices form-select">
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>

                                </select>
                            </div>
                            <div class="col-12 col-md-8 offset-md-4 form-group">
                                <div class='form-check'>
                                    <div class="checkbox">
                                        <input type="checkbox" id="autotrim_rec" name="autotrim"
                                            class='form-check-input' checked>
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
                                <input type="number" id="trimlevel_rec" min="-99" max="0" class="form-control"
                                    name="trimlevel" value="-35">
                            </div>
                            <div class="col-12 col-md-8 offset-md-4 form-group">
                                <div class='form-check'>
                                    <div class="checkbox">
                                        <input type="checkbox" id="normalize_rec" name="normalize"
                                            class='form-check-input' checked>
                                        <label for="normalize_rec">
                                            <?= $ml->tr('NORMALIZE') ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="normalizelevel_rec">
                                    <?= $ml->tr('NORMALIZELEVEL') ?>
                                </label>
                            </div>
                            <div class="col-md-8 form-group">
                                <input type="number" id="normalizelevel_rec" min="-30" max="0" class="form-control"
                                    name="normalizelevel" value="-13">
                            </div>
                        </div>
                        <button class="btn btn-danger" id="recordrec">
                            <?= $ml->tr('RECORD') ?>
                        </button>
                        <button id="pauserec" class="btn btn-warning" style="display: none;">
                            <?= $ml->tr('PAUSE') ?>
                        </button>
                        <p id="progress">00:00</p>

                        <div id="mic" style="border: 1px solid #ddd; border-radius: 4px; margin-top: 1rem"></div>

                        <div id="recordings" style="margin: 1rem 0"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-kt-record-modal-action="close">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">
                            <?= $ml->tr('CLOSE') ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="import_cut" data-bs-backdrop="static" role="dialog" aria-labelledby="cutLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="cutLabel">
                        <?= $ml->tr('IMPORTAUDIO') ?>
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
                                            <?= $ml->tr('DROPZONEMAXONE') ?>
                                        </span>
                                    </div>
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
                        <input type="hidden" name="cartid"
                            value="<?php echo $info->getCartInfo($_GET["id"], "NUMBER"); ?>" id="cartid">
                        <input type="hidden" name="cutid" value="" id="cutid_imp">
                        <button type="button" class="btn btn-light-secondary" data-kt-import-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="export_cut" data-bs-backdrop="static" role="dialog"
        aria-labelledby="cutExportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="cutExportLabel">
                        <?= $ml->tr('EXPORTAUDIO') ?>
                    </h4>
                    <button type="button" class="close" data-kt-export-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="export_audio_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
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
                                    <input type="text" id="for_quality" class="form-control" name="audioquality"
                                        value="0" DISABLED>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="for_exportmeta" name="exportmeta"
                                                class='form-check-input' value="1" checked>
                                            <label for="for_exportmeta">
                                                <?= $ml->tr('EXPORTMETADATA') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="for_normalize" name="normalize"
                                                class='form-check-input' value="1" checked>
                                            <label for="for_normalize">
                                                <?= $ml->tr('NORMALIZE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="for_normalizelevel">
                                        <?= $ml->tr('NORMALIZELEVEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="number" id="for_normalizelevel" min="-30" max="0" class="form-control"
                                        name="normalizelevel" value="<?php echo NORMALIZE ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" value="<?php echo $id; ?>">
                        <input type="hidden" name="cutid" id="for_cutcart" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-export-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('EXPORT') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="cut_info" data-bs-backdrop="static" role="dialog"
        aria-labelledby="cutInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="cutInfoLabel">
                        <?= $ml->tr('CUTINFO') ?>
                    </h4>
                    <button type="button" class="close" data-kt-cutinfo-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="cut_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="cdesc">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="cdesc" class="form-control" name="cdesc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="coutcue">
                                        <?= $ml->tr('OUTCUE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="coutcue" class="form-control" name="coutcue" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="ciscicode">
                                        <?= $ml->tr('ISCI') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="ciscicode" class="form-control" name="ciscicode" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="cisrc">
                                        <?= $ml->tr('ISRC') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="cisrc" class="form-control" name="cisrc" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="csource">
                                        <?= $ml->tr('SOURCE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <P id="csource"></P>
                                </div>
                                <div class="col-md-4">
                                    <label for="cingest">
                                        <?= $ml->tr('INGEST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <P id="cingest"></P>
                                </div>
                                <div class="col-md-4">
                                    <label for="clastplayed">
                                        <?= $ml->tr('LASTPLAYED') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <P id="clastplayed"></P>
                                </div>
                                <div class="col-md-4">
                                    <label for="cofplays">
                                        <?= $ml->tr('OFPLAYS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <P id="cofplays"></P>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="evergreen" name="evergreen"
                                                class='form-check-input' value="1" checked>
                                            <label for="evergreen">
                                                <?= $ml->tr('CUTISEVERGREEN') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label id="cutwalab" for="weight">
                                        <?= $ml->tr('WEIGHT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="weight" class="form-control" name="weight" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="airenable" name="airenable"
                                                class='form-check-input' value="1" checked>
                                            <label for="airenable">
                                                <?= $ml->tr('AIRDATETIME') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="adstart">
                                        <?= $ml->tr('AIRDATESTART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="adstart" class="form-control" name="adstart" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="adend">
                                        <?= $ml->tr('AIRDATEEND') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="adend" class="form-control" name="adend" value="">
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="airdaypartactive" name="airdaypartactive"
                                                class='form-check-input' value="1" checked>
                                            <label for="airdaypartactive">
                                                <?= $ml->tr('DAYPARTENABLE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="adaystart">
                                        <?= $ml->tr('STARTTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="adaystart" class="form-control" name="adaystart" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="adayend">
                                        <?= $ml->tr('ENDTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="adayend" class="form-control" name="adayend" value="">
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('DAYSOFTHEWEEK') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="daymon" name="daymon" class='form-check-input'
                                                value="1" checked>
                                            <label for="daymon">
                                                <?= $ml->tr('MONDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="daytue" name="daytue" class='form-check-input'
                                                value="1" checked>
                                            <label for="daytue">
                                                <?= $ml->tr('TUESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="daywed" name="daywed" class='form-check-input'
                                                value="1" checked>
                                            <label for="daywed">
                                                <?= $ml->tr('WEDNESDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="daythu" name="daythu" class='form-check-input'
                                                value="1" checked>
                                            <label for="daythu">
                                                <?= $ml->tr('THURSDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="dayfri" name="dayfri" class='form-check-input'
                                                value="1" checked>
                                            <label for="dayfri">
                                                <?= $ml->tr('FRIDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="daysat" name="daysat" class='form-check-input'
                                                value="1" checked>
                                            <label for="daysat">
                                                <?= $ml->tr('SATURDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="daysun" name="daysun" class='form-check-input'
                                                value="1" checked>
                                            <label for="daysun">
                                                <?= $ml->tr('SUNDAY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" value="<?php echo $id; ?>">
                        <input type="hidden" name="cutid" id="cutid" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-cutinfo-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="audio_editor" data-bs-backdrop="static" role="dialog"
        aria-labelledby="EditMarkerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-info">
                    <h4 class="modal-title white" id="EditMarkerLabel">
                        <?= $ml->tr('EDITAUDIOMARKERS') ?>
                    </h4>
                    <button type="button" class="close" data-kt-editmarker-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-vertical" id="edit_audio_form" action="#">
                    <div class="modal-body">
                        <P>
                            <?= $ml->tr('EDITAUDIOHERE') ?>
                        </P>
                        <P>
                            <?= $ml->tr('EDITAUDIOHERECHANGE') ?>
                        </P>
                        <P>
                            <?= $ml->tr('WSHORTCUTS') ?>
                        </P>
                        <div id="editnow"></div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="for_zoom">
                                    <?= $ml->tr('ZOOM') ?>
                                </label>
                                <input id="for_zoom" type="range" min="1" max="1000" value="1" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn-group mb-3" role="group">
                                <a href="javascript:;" id="talkbutton" onclick="addMarker('1')"
                                    class="btn icon btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?= $ml->tr('TALKSTARTSTOP') ?>"><i class="bi bi-mic"></i></a>
                                <a href="javascript:;" id="seguebutton" onclick="addMarker('2')"
                                    class="btn icon btn-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?= $ml->tr('SEGUESTARTSTOP') ?>"><i
                                        class="bi bi-arrows-angle-contract"></i></a>
                                <a href="javascript:;" id="fadeupbutton" onclick="addMarker('3')"
                                    class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?= $ml->tr('FADEUP') ?>"><i class="bi bi-arrow-up-right-square"></i></a>
                                <a href="javascript:;" id="fadedownbutton" onclick="addMarker('4')"
                                    class="btn icon btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?= $ml->tr('FADEDOWN') ?>"><i class="bi bi-arrow-down-right-square"></i></a>
                                <a href="javascript:;" id="addhookbutton" onclick="addMarker('5')"
                                    class="btn icon btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?= $ml->tr('HOOKSTARTSTOP') ?>"><i class="bi bi-paperclip"></i></a>
                            </div>
                            <div class="btn-group mb-3" role="group">
                                <a href="javascript:;" id="play" class="btn icon btn-primary" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="<?= $ml->tr('PLAYPAUSE') ?>"><i
                                        class="bi bi-play"></i></a>
                                <a href="javascript:;" id="backward" class="btn icon btn-info" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="<?= $ml->tr('BACKWARD') ?>"><i
                                        class="bi bi-rewind"></i></a>
                                <a href="javascript:;" id="forward" class="btn icon btn-success"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?= $ml->tr('FORWARD') ?>"><i class="bi bi-fast-forward"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="cuestart" id="cuestart" value="-1">
                        <input type="hidden" name="cueend" id="cueend" value="-1">
                        <input type="hidden" name="talkstart" id="talkstart" value="-1">
                        <input type="hidden" name="talkend" id="talkend" value="-1">
                        <input type="hidden" name="fadeup" id="fadeup" value="-1">
                        <input type="hidden" name="fadedown" id="fadedown" value="-1">
                        <input type="hidden" name="seguestart" id="seguestart" value="-1">
                        <input type="hidden" name="segueend" id="segueend" value="-1">
                        <input type="hidden" name="hookstart" id="hookstart" value="-1">
                        <input type="hidden" name="hookend" id="hookend" value="-1">
                        <input type="hidden" name="cutname" id="thecutname" value="-1">
                        <button type="button" class="btn btn-light-secondary" data-kt-editmarker-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <button type="button" class="btn btn-info ms-1" data-kt-editmarker-modal-action="submit">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('SAVEMARKERS') ?>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>