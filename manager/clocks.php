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
if (!$info->checkusrRights('MODIFY_TEMPLATE_PRIV')) {
    header('Location: '.DIR.'/login');
    exit();
}

$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$cutinfo = $dbfunc->getCutInfo($_GET['id']);
$groupinfo = $dbfunc->getUserGroup($username);
$pagecode = "clocks";
$page_vars = 'clocks';
$page_title = $ml->tr('CLOCKS');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="https://unpkg.com/huebee@2/dist/huebee.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="'.DIR.'/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="'.DIR.'/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="https://unpkg.com/huebee@2/dist/huebee.pkgd.min.js"></script>

<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="'.DIR.'/assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/clocks.js"></script>';
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('CLOCKS'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('ALLCLOCKSSERVICE'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('CLOCKS'); ?>
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
                    <?= $ml->tr('AVCLOCKS'); ?>
                </h5>
                <h6 class="card-subtitle">
                    <?= $ml->tr('FORSERVICE {{' . $selectedService . '}}'); ?>
                </h6>
                <div class="d-flex justify-content-end align-items-center d-none"
                    data-kt-clocks-table-select="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-clocks-table-select="selected_count"></span>
                        <?= $ml->tr('SELECTED'); ?>
                    </div>
                    <button type="button" class="btn btn-danger" data-kt-clocks-table-select="delete_selected">
                        <?= $ml->tr('DELSELECTED'); ?>
                    </button>
                </div>
                <div data-kt-clocks-table-toolbar="base">
                    <button data-bs-toggle="modal" data-bs-target="#add_clock" class="btn btn-light-warning">
                        <?= $ml->tr('ADDCLOCK'); ?>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="clocks_table">
                        <thead>
                            <tr>
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" id="checkall" type="checkbox"
                                            data-kt-check="true" data-kt-check-target="#clocks_table .form-check-input"
                                            value="1" />
                                    </div>
                                </th>
                                <th>
                                    <?= $ml->tr('NAME') ?>
                                </th>
                                <th>
                                    <?= $ml->tr('CODE') ?>
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
    <div class="modal fade text-left" id="rename_clock" data-bs-backdrop="static" role="dialog"
        aria-labelledby="ClockRenameLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="ClockRenameLabel">
                        <?= $ml->tr('RENAMECLOCK') ?>
                    </h4>
                    <button type="button" class="close" data-kt-clockrename-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="rename_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="reclockname">
                                        <?= $ml->tr('NEWCLOCKNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="reclockname" class="form-control" name="name" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="oldname" id="reoldname" value="">
                        <button type="button" class="btn btn-light-secondary" data-kt-clockrename-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('RENAMECLOCK') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="add_clock" data-bs-backdrop="static" role="dialog"
        aria-labelledby="ClockAddLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="ClockAddLabel">
                        <?= $ml->tr('ADDCLOCK') ?>
                    </h4>
                    <button type="button" class="close" data-kt-clockadd-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="add_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="clockname">
                                        <?= $ml->tr('CLOCKNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="clockname" class="form-control" name="name" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="ccode">
                                        <?= $ml->tr('CLOCKCODE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="ccode" class="form-control" name="ccode" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="colors">
                                        <?= $ml->tr('CLOCKCOLOR') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input data-huebee='{ "notation": "hex", "saturations": 2 }' type="text" id="colors"
                                        class="form-control color-input" name="colors" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="usernotes">
                                        <?= $ml->tr('USERNOTES') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <textarea name="usernotes" id="usernotes" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="service" value="<?php echo $_COOKIE['serviceName'] ?>">
                        <input type="hidden" name="artiststep" value="15">
                        <button type="button" class="btn btn-light-secondary" data-kt-clockadd-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('ADDCLOCK') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>