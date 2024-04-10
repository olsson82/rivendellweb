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
if (!$json_sett["admin"][$_COOKIE['username']]["users"] == 1) {
    header('Location: index.php');
    exit();
}
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$pagecode = "users";
$page_vars = 'users';
$page_title = $ml->tr('USERS');
$page_css = '<link rel="stylesheet" href="assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="./assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="assets/static/js/users.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('USERS'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('ADMINUSERSIN {{' . SYSTIT . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dash.php">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('USERS'); ?>
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
                    <?= $ml->tr('AVUSERS'); ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="users_table">
                        <thead>
                            <tr>
                                <th>
                                    <?= $ml->tr('USERNAME') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('FULLNAME') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('EMAIL') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('PHONE') ?>
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
    <div class="modal fade text-left" id="user_window" data-bs-backdrop="static" role="dialog"
        aria-labelledby="UserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="UserLabel">
                        <?= $ml->tr('USRRDRIGHTS') ?>
                    </h4>
                    <button type="button" class="close" data-kt-user-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="user_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('PRODUCTIONRIGHTS') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="createcarts">
                                        <?= $ml->tr('CREATECARTS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="createcarts" name="createcarts" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="modifycarts">
                                        <?= $ml->tr('MODIFYCARTS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="modifycarts" name="modifycarts" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="deletecarts">
                                        <?= $ml->tr('DELETECARTS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="deletecarts" name="deletecarts" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="editaudio">
                                        <?= $ml->tr('EDITAUDIO') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="editaudio" name="editaudio" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="deditnetcatch">
                                        <?= $ml->tr('EDITNETCATCH') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="deditnetcatch" name="deditnetcatch" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="voicetracklogs">
                                        <?= $ml->tr('VOICETRACKLOGS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="voicetracklogs" name="voicetracklogs" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="allowweb">
                                        <?= $ml->tr('ALLOWWEBGET') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="allowweb" name="allowweb" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('TRAFFICRIGHTS') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="createlog">
                                        <?= $ml->tr('CREATELOG') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="createlog" name="createlog" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="deletelog">
                                        <?= $ml->tr('DELETELOG') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="deletelog" name="deletelog" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="modifytemp">
                                        <?= $ml->tr('MODIFYTEMPLATE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="modifytemp" name="modifytemp" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="delreportdata">
                                        <?= $ml->tr('DELREPORTDATA') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="delreportdata" name="delreportdata" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ONAIRRIGHTS') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="playoutlogs">
                                        <?= $ml->tr('PLAYOUTLOGS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="playoutlogs" name="playoutlogs" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="addlogitems">
                                        <?= $ml->tr('ADDLOGITEMS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="addlogitems" name="addlogitems" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="rearrlogitems">
                                        <?= $ml->tr('REARRLOGITEMS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="rearrlogitems" name="rearrlogitems" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="dellogitems">
                                        <?= $ml->tr('DELLOGITEMS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="dellogitems" name="dellogitems" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="confsyspanel">
                                        <?= $ml->tr('CONFSYSTEMPANELS') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="confsyspanel" name="confsyspanel" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('PODCASTINGRIGHTS') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="createpodcast">
                                        <?= $ml->tr('CREATEPODCAST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="createpodcast" name="createpodcast" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="editpodcast">
                                        <?= $ml->tr('EDITPODCAST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="editpodcast" name="editpodcast" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="delpodcast">
                                        <?= $ml->tr('DELPODCAST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="delpodcast" name="delpodcast" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="allweblog">
                                        <?= $ml->tr('ALLWEBLOGIN') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="allweblog" name="allweblog" class="form-select">
                                        <option value="Y" selected>
                                            <?= $ml->tr('YES') ?>
                                        </option>
                                        <option value="N">
                                            <?= $ml->tr('NO') ?>
                                        </option>
                                    </select>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="theuser" name="theuser" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-user-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>