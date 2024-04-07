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
    header('Location: index.php');
    exit();
}

$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$cutinfo = $dbfunc->getCutInfo($_GET['id']);
$groupinfo = $dbfunc->getUserGroup($username);
$schedCodes = $dbfunc->getSchedulerCodes();
$id = $_GET['id'];
$pagecode = "library";

if (!$info->checkMacroNormal($id, 2)) {
    header('Location: library.php');
    exit();
}

$page_vars = 'macro';
$page_title = $ml->tr('VIEWMACRO');
$page_css = '<link rel="stylesheet" href="assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="./assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/timeline.min.js"></script>
<script src="assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="assets/static/js/macro.js"></script>';

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('VIEWMACRO'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGEMACROCART {{' . $id . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dash.php">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="library.php">
                                <?= $ml->tr('LIBRARY'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('VIEWMACRO'); ?>
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
                                            <input type="checkbox" id="exeasy" name="exeasy" value="1"
                                                class="form-check-input" <?php if ($info->getCartInfo($id, "ASYNCRONOUS") == 'Y') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="exeasy">
                                                <?= $ml->tr('EXECUTEASY') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="userdlogpad" name="userdlogpad" value="1"
                                                class="form-check-input" <?php if ($info->getCartInfo($id, "USE_EVENT_LENGTH") == 'Y') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="userdlogpad">
                                                <?= $ml->tr('USERDLOGPAD') ?>
                                            </label>
                                        </div>
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
                                        <input type="text" id="year" class="form-control" value="<?php $yearinfo = $info->getCartInfo($_GET["id"], "YEAR");
                                        if (isset($yearinfo) && $yearinfo != '') {
                                            echo date('Y', strtotime($info->getCartInfo($_GET["id"], "YEAR")));
                                        } ?>" name="year">
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
                                <label for="notes" class="form-label">
                                    <?= $ml->tr('NOTES') ?>
                                </label>
                                <textarea class="form-control" id="notes" name="notes"
                                    rows="3"><?php echo $info->getCartInfo($_GET["id"], "NOTES"); ?></textarea>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <input type="hidden" name="cartno"
                                    value="<?php echo $info->getCartInfo($id, "NUMBER"); ?>">
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
                            <?= $ml->tr('COMMANDOS') ?>
                        </h5>
                        <button onclick="addcommand('<?php echo $id; ?>')" class="btn btn-light-warning">
                            <?= $ml->tr('ADDCOMMAND'); ?>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="macro_table">
                                <thead>
                                    <tr>
                                        <th>
                                            <?= $ml->tr('LINE') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('COMMAND') ?>
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
                                            <?= $ml->tr('LINE') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('COMMAND') ?>
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
    <div class="modal fade text-left" id="add_macro" data-bs-backdrop="static" role="dialog"
        aria-labelledby="AddMacroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title white" id="AddMacroLabel">
                        <?= $ml->tr('ADDCOMMAND') ?>
                    </h4>
                    <button type="button" class="close" data-kt-addmacro-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="addmacro_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="commando">
                                        <?= $ml->tr('COMMAND') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="commando" class="form-control" name="commando" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" value="<?php echo $id; ?>">
                        <input type="hidden" name="oldcommand" id="oldcommand" value="">
                        <input type="hidden" name="lineid" id="lineid" value="">
                        <input type="hidden" name="isedit" id="isedit" value="0">
                        <button type="button" class="btn btn-light-secondary" data-kt-addmacro-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-info ms-1" value="<?= $ml->tr('ADDCOMMAND') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>