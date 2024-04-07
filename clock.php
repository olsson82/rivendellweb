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
if (!$info->checkusrRights('MODIFY_TEMPLATE_PRIV')) {
    header('Location: index.php');
    exit();
}

$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$clockid = $_GET['id'];
$clocks = $logfunc->getRivendellClocks($_COOKIE['serviceName']);
$rulesclock = $logfunc->getRules($clockid);
$clockEvents = $logfunc->getClock($clocks, $clockid);
$events = $logfunc->getRivendellEvents($_COOKIE['serviceName']);
$schedCodes = $dbfunc->getSchedulerCodes();
$pagecode = "clocks";
$page_vars = 'clock';
$page_title = $ml->tr('EDITCLOCK');
$page_css = '<link rel="stylesheet" href="assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="https://unpkg.com/huebee@2/dist/huebee.min.css">
<link rel="stylesheet" href="assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="./assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js "></script>
<script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="https://unpkg.com/huebee@2/dist/huebee.pkgd.min.js"></script>

<script src="assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="assets/extensions/inputmask/jquery.inputmask.js"></script>
<script src="assets/static/js/pages/datatables.js"></script>';
$page_js = '<script src="assets/static/js/clock.js"></script>';

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>
                                <?= $ml->tr('EDITCLOCK'); ?>
                            </h3>
                            <p class="text-subtitle text-muted">
                                <?= $ml->tr('EDITCLOCKHOUR'); ?>
                            </p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dash.php">
                                            <?= $ml->tr('DASHBOARD'); ?>
                                        </a></li>
                                    <li class="breadcrumb-item"><a href="clocks.php">
                                            <?= $ml->tr('CLOCKS'); ?>
                                        </a></li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <?= $ml->tr('EDITCLOCK'); ?>
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
                                <?php echo $info->getClockInfo($clockid, 'NAME'); ?>
                            </h5>
                            <h6 class="card-subtitle">
                                <?= $ml->tr('CODE') ?>
                                <?php echo $info->getClockInfo($clockid, 'SHORT_NAME'); ?>
                            </h6>
                            <div class="d-flex justify-content-end align-items-center d-none"
                                data-kt-clocks-table-select="selected">
                                <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-clocks-table-select="selected_count"></span>
                                    <?= $ml->tr('SELECTED'); ?>
                                </div>
                                <button type="button" class="btn btn-danger"
                                    data-kt-clocks-table-select="delete_selected">
                                    <?= $ml->tr('DELSELECTED'); ?>
                                </button>
                            </div>
                            <div data-kt-clocks-table-toolbar="base">

                                <button data-bs-toggle="modal" data-bs-target="#schedrules_clock"
                                    class="btn btn-light-info">
                                    <?= $ml->tr('SCHEDULERRULES'); ?>
                                </button>
                                <button style="background-color: <?php echo $info->getClockInfo($clockid, 'COLOR'); ?>"
                                    id="id_color" data-bs-toggle="modal" data-bs-target="#color_clock"
                                    class="btn btn-light-secondary">
                                    <?= $ml->tr('COLOR'); ?>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#service_clock"
                                    class="btn btn-light-danger">
                                    <?= $ml->tr('SERVICES'); ?>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#saveas_clock"
                                    class="btn btn-light-warning">
                                    <?= $ml->tr('SAVEAS'); ?>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#addevent_clock"
                                    class="btn btn-light-primary">
                                    <?= $ml->tr('ADDEVENT'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="clock_table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <?= $ml->tr('START') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('END') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('LENGTH') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('TRANS') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('EVENT') ?>
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
                                                <?= $ml->tr('START') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('END') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('LENGTH') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('TRANS') ?>
                                            </th>
                                            <th>
                                                <?= $ml->tr('EVENT') ?>
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

                </section>
                <div class="modal fade text-left" id="saveas_clock" data-bs-backdrop="static" role="dialog"
                    aria-labelledby="ClockSaveLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header  bg-warning">
                                <h4 class="modal-title white" id="ClockSaveLabel">
                                    <?= $ml->tr('SAVECLOCKAS') ?>
                                </h4>
                                <button type="button" class="close" data-kt-clocksave-modal-action="cancel"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form class="form form-horizontal" id="saveas_form" action="#">
                                <div class="modal-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="newcname">
                                                    <?= $ml->tr('CLOCKNAME') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="newcname" class="form-control" name="name"
                                                    value="<?php echo $info->getClockInfo($clockid, 'NAME'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="newccode">
                                                    <?= $ml->tr('CLOCKCODE') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="newccode" class="form-control" name="ccode"
                                                    value="<?php echo $info->getClockInfo($clockid, 'SHORT_NAME'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="service" value="<?php echo $_COOKIE['serviceName'] ?>">
                                    <input type="hidden" id="oldclockid" name="clockid" value="<?php echo $clockid; ?>">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-kt-clocksave-modal-action="close">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">
                                            <?= $ml->tr('CLOSE') ?>
                                        </span>
                                    </button>
                                    <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('SAVEAS') ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="color_clock" data-bs-backdrop="static" role="dialog"
                    aria-labelledby="ClockColorLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header  bg-success">
                                <h4 class="modal-title white" id="ClockColorLabel">
                                    <?= $ml->tr('CLOCKCOLOR') ?>
                                </h4>
                                <button type="button" class="close" data-kt-color-modal-action="cancel"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form class="form form-horizontal" id="color_form" action="#">
                                <div class="modal-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="colors">
                                                    <?= $ml->tr('CLOCKCOLOR') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input data-huebee='{ "notation": "hex", "saturations": 2 }' type="text"
                                                    id="colors" class="form-control color-input" name="colors"
                                                    value="<?php echo $info->getClockInfo($clockid, 'COLOR'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="service" value="<?php echo $_COOKIE['serviceName'] ?>">
                                    <input type="hidden" id="clockidcolor" name="clockid"
                                        value="<?php echo $clockid; ?>">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-kt-color-modal-action="close">
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
                <div class="modal fade text-left" id="service_clock" data-bs-backdrop="static" role="dialog"
                    aria-labelledby="ClockServiceLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header  bg-danger">
                                <h4 class="modal-title white" id="ClockServiceLabel">
                                    <?= $ml->tr('ENABLEDSERVICES') ?>
                                </h4>
                                <button type="button" class="close" data-kt-service-modal-action="cancel"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form class="form form-horizontal" id="service_form" action="#">
                                <div class="modal-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="servicess">
                                                    <?= $ml->tr('SERVICES') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select id="servicess" name="services[]" class="choices form-select"
                                                    multiple>
                                                    <?php foreach ($serviceNames as $name) { ?>
                                                        <option value="<?php echo $name; ?>" <?php if ($info->getServiceClockInfo($clockid, $name) == $name) { ?>SELECTED <?php } ?>>
                                                            <?php echo $name; ?>
                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="service" value="<?php echo $_COOKIE['serviceName'] ?>">
                                    <input type="hidden" id="clockidservice" name="clockid"
                                        value="<?php echo $clockid; ?>">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-kt-service-modal-action="close">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">
                                            <?= $ml->tr('CLOSE') ?>
                                        </span>
                                    </button>
                                    <input type="submit" class="btn btn-danger ms-1" value="<?= $ml->tr('SAVE') ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade text-left" id="schedrules_clock" data-bs-backdrop="static" role="dialog"
                    aria-labelledby="ClockSchedLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-full" role="document">
                        <div class="modal-content">
                            <div class="modal-header  bg-info">
                                <h4 class="modal-title white" id="ClockSchedLabel">
                                    <?= $ml->tr('SCHEDULERRULES') ?>
                                </h4>
                                <button type="button" class="close" data-kt-scheduler-modal-action="cancel"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <?= $ml->tr('CODE') ?>
                                                </th>
                                                <th scope="col">
                                                    <?= $ml->tr('MAXINROW') ?>
                                                </th>
                                                <th scope="col">
                                                    <?= $ml->tr('MINWAIT') ?>
                                                </th>
                                                <th scope="col">
                                                    <?= $ml->tr('NOTSCHEDULERAFTER') ?>
                                                </th>
                                                <th scope="col">
                                                    <?= $ml->tr('ORAFTER') ?>
                                                </th>
                                                <th scope="col">
                                                    <?= $ml->tr('ORAFTER') ?>
                                                </th>
                                                <th scope="col">
                                                    <?= $ml->tr('ACTION') ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rulesclock as $rule) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $rule['CODE']; ?>
                                                    </td>
                                                    <td id="<?php echo $rule['CODE']; ?>_MAX">
                                                        <?php if ($rule['MAX_ROW'] == "") {
                                                            echo "0";
                                                        } else {
                                                            echo $rule['MAX_ROW'];
                                                        } ?>
                                                    </td>
                                                    <td id="<?php echo $rule['CODE']; ?>_MIN">
                                                        <?php if ($rule['MIN_WAIT'] == "") {
                                                            echo "0";
                                                        } else {
                                                            echo $rule['MIN_WAIT'];
                                                        } ?>
                                                    </td>
                                                    <td id="<?php echo $rule['CODE']; ?>_NOTA">
                                                        <?php echo $rule['NOT_AFTER']; ?>
                                                    </td>
                                                    <td id="<?php echo $rule['CODE']; ?>_OA">
                                                        <?php echo $rule['OR_AFTER']; ?>
                                                    </td>
                                                    <td id="<?php echo $rule['CODE']; ?>_OA2">
                                                        <?php echo $rule['OR_AFTER_II']; ?>
                                                    </td>
                                                    <td><button
                                                            onclick="editshed('<?php echo $rule['CODE']; ?>','<?php echo $rule['CLOCK_NAME']; ?>')"
                                                            data-bs-stacked-modal="#edit_sched"
                                                            class="btn btn-light-danger">
                                                            <?= $ml->tr('EDIT'); ?>
                                                        </button></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary"
                                    data-kt-scheduler-modal-action="close">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">
                                        <?= $ml->tr('CLOSE') ?>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade text-left" id="edit_sched" data-bs-backdrop="static" role="dialog"
                    aria-labelledby="ClockRuleLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header  bg-primary">
                                <h4 class="modal-title white" id="ClockRuleLabel">
                                    <?= $ml->tr('EDITSCHEDRULE') ?>
                                </h4>
                                <button type="button" class="close" data-kt-rule-modal-action="cancel"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form class="form form-horizontal" id="rule_form" action="#">
                                <div class="modal-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="maxrow">
                                                    <?= $ml->tr('MAXINROW') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="number" id="maxrow" min="0" max="99" class="form-control"
                                                    name="maxrow" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="minwait">
                                                    <?= $ml->tr('MINWAIT') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="number" id="minwait" min="0" max="99" class="form-control"
                                                    name="minwait" value="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="notafter">
                                                    <?= $ml->tr('NOTSCHEDULERAFTER') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select id="notafter" name="notafter" class="form-select">
                                                    <option value="0">
                                                        <?= $ml->tr('NONE') ?>
                                                    </option>
                                                    <?php foreach ($schedCodes as $scode) { ?>
                                                        <option value="<?php echo $scode['code']; ?>">
                                                            <?php echo $scode['code']; ?>
                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="orafter">
                                                    <?= $ml->tr('ORAFTER') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select id="orafter" name="orafter" class="form-select">
                                                    <option value="0">
                                                        <?= $ml->tr('NONE') ?>
                                                    </option>
                                                    <?php foreach ($schedCodes as $scode) { ?>
                                                        <option value="<?php echo $scode['code']; ?>">
                                                            <?php echo $scode['code']; ?>
                                                        </option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="orafter2">
                                                    <?= $ml->tr('ORAFTER') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select id="orafter2" name="orafter2" class="form-select">
                                                    <option value="0">
                                                        <?= $ml->tr('NONE') ?>
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
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" id="shedrulecode" name="shedrulecode" value="">
                                    <input type="hidden" id="clockidsched" name="clockid"
                                        value="<?php echo $clockid; ?>">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-kt-rule-modal-action="close">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">
                                            <?= $ml->tr('CLOSE') ?>
                                        </span>
                                    </button>
                                    <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="addevent_clock" data-bs-backdrop="static" role="dialog"
                    aria-labelledby="ClockAddEventLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header  bg-primary">
                                <h4 class="modal-title white" id="ClockAddEventLabel">
                                    <?= $ml->tr('EVENT') ?>
                                </h4>
                                <button type="button" class="close" data-kt-addevent-modal-action="cancel"
                                    aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <form class="form form-horizontal" id="addevent_form" action="#">
                                <div class="modal-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="event">
                                                    <?= $ml->tr('SELECTEVENT') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <select id="event" name="event" class="form-select">
                                                    <?php foreach ($events as $listevent) { ?>
                                                        <option value="<?php echo $listevent['NAME']; ?>">
                                                            <?php echo $listevent['NAME']; ?> -
                                                            <?php if ($listevent['FIRST_TRANS_TYPE'] == 0) {
                                                                echo $ml->tr('PLAY');
                                                            } else if ($listevent['FIRST_TRANS_TYPE'] == 1) {
                                                                echo $ml->tr('SEGUE');
                                                            } else if ($listevent['FIRST_TRANS_TYPE'] == 2) {
                                                                echo $ml->tr('STOP');
                                                            } ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="starttime">
                                                    <?= $ml->tr('STARTTIME') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="starttime" class="form-control" name="starttime"
                                                    value="">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="endtime">
                                                    <?= $ml->tr('ENDTIME') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <input type="text" id="endtime" class="form-control" name="endtime"
                                                    value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="service" value="<?php echo $_COOKIE['serviceName'] ?>">
                                    <input type="hidden" id="millisstart" name="millisstart" value="">
                                    <input type="hidden" id="millisend" name="millisend" value="">
                                    <input type="hidden" id="clockid" name="clockid" value="<?php echo $clockid; ?>">
                                    <input type="hidden" id="eventid" name="eventid" value="">
                                    <input type="hidden" id="editevent" name="editevent" value="0">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-kt-addevent-modal-action="close">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">
                                            <?= $ml->tr('CLOSE') ?>
                                        </span>
                                    </button>
                                    <input type="submit" class="btn btn-primary ms-1" value="<?= $ml->tr('SAVE') ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>

            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>