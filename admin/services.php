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
if (!$json_sett["admin"][$_COOKIE['username']]["services"] == 1) {
    header('Location: ' . DIR . '/login');
    exit();
}
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$users = $dbfunc->getUsers();
$services = $dbfunc->getServices();
$pagecode = "services";
$page_vars = 'services';
$page_title = $ml->tr('RIVSERVICES');
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
$page_js = '<script src="' . DIR . '/assets/static/js/services.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('RIVSERVICES'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('RIVSERVICESMANAGE'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('RIVSERVICES'); ?>
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
                    <?= $ml->tr('AVALIBLESERVICES'); ?>
                </h5>
                <button data-bs-toggle="modal" data-bs-target="#add_window" class="btn btn-light-success">
                    <?= $ml->tr('ADDSERVICE'); ?>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="services_table">
                        <thead>
                            <tr>
                                <th>
                                    <?= $ml->tr('NAME') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('DESCRIPTION') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('PGMCODE') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('TRACKGROUP') ?>
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
    <div class="modal fade text-left" id="add_window" data-bs-backdrop="static" role="dialog" aria-labelledby="AddLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="AddLabel">
                        <?= $ml->tr('ADDSERVICE') ?>
                    </h4>
                    <button type="button" class="close" data-kt-add-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="add_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="service_name">
                                        <?= $ml->tr('SERVICENAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="service_name" class="form-control" name="service_name">
                                </div>
                                <div class="col-md-4">
                                    <label for="basedon">
                                        <?= $ml->tr('BASEONSERVICE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="basedon" name="basedon" class="choices form-select">
                                        <?php foreach ($services as $service) { ?>
                                            <option value="<?php echo $service; ?>">
                                                <?php echo $service; ?>
                                            </option>
                                        <?php } ?>
                                        <option value="non">
                                            <?= $ml->tr('NONBRACKET') ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-kt-add-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-success ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>