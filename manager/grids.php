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
if (!$info->checkusrRights('MODIFY_TEMPLATE_PRIV')) {
    header('Location: ' . DIR . '/login');
    exit();
}
$activeService = $_COOKIE['serviceName'];
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$grid = $logfunc->getGrid($activeService);
$clocks = $logfunc->getRivendellClocks($activeService);
$pagecode = "grids";
$page_vars = 'grids';
$page_title = $ml->tr('GRIDS');
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
<script src="' . DIR . '/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="' . DIR . '/assets/static/js/grids.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('GRIDS'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGEGRIDS'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('GRIDS'); ?>
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
                    <?= $ml->tr('SCHEDULER'); ?>
                </h5>
                <h6 class="card-subtitle">
                    <?= $ml->tr('FORSERVICE {{' . $selectedService . '}}'); ?>
                </h6>
                <div data-kt-library-table-toolbar="base">
                    <button onclick="saveGrid('<?php echo $activeService; ?>')" class="btn btn-light-warning">
                        <?= $ml->tr('SAVEGRIDLAYOUT'); ?>
                    </button>
                    <button onclick="LoadLayout('<?php echo $activeService; ?>')" class="btn btn-light-info">
                        <?= $ml->tr('SELLAYOUTGRID'); ?>
                    </button>
                    <button onclick="clearAll('<?php echo $activeService; ?>')" class="btn btn-light-danger">
                        <?= $ml->tr('CLEARALLGRID'); ?>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <?php
                                $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
                                foreach ($days as $day) {
                                    ?>
                                    <th>
                                        <?php if ($day == 'mon') {
                                            echo $ml->tr('MONDAY');
                                        } else if ($day == 'tue') {
                                            echo $ml->tr('TUESDAY');
                                        } else if ($day == 'wed') {
                                            echo $ml->tr('WEDNESDAY');
                                        } else if ($day == 'thu') {
                                            echo $ml->tr('THURSDAY');
                                        } else if ($day == 'fri') {
                                            echo $ml->tr('FRIDAY');
                                        } else if ($day == 'sat') {
                                            echo $ml->tr('SATURDAY');
                                        } else if ($day == 'sun') {
                                            echo $ml->tr('SUNDAY');
                                        } ?>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $clockNo = 0;
                            $runsNo = -1;
                            $runsNo2 = -1;
                            $rowlines = array(0, 24, 48, 72, 96, 120, 144, 1, 25, 49, 73, 97, 121, 145, 2, 26, 50, 74, 98, 122, 146, 3, 27, 51, 75, 99, 123, 147, 4, 28, 52, 76, 100, 124, 148, 5, 29, 53, 77, 101, 125, 149, 6, 30, 54, 78, 102, 126, 150, 7, 31, 55, 79, 103, 127, 151, 8, 32, 56, 80, 104, 128, 152, 9, 33, 57, 81, 105, 129, 153, 10, 34, 58, 82, 106, 130, 154, 11, 35, 59, 83, 107, 131, 155, 12, 36, 60, 84, 108, 132, 156, 13, 37, 61, 85, 109, 133, 157, 14, 38, 62, 86, 110, 134, 158, 15, 39, 63, 87, 111, 135, 159, 16, 40, 64, 88, 112, 136, 160, 17, 41, 65, 89, 113, 137, 161, 18, 42, 66, 90, 114, 138, 162, 19, 43, 67, 91, 115, 139, 163, 20, 44, 68, 92, 116, 140, 164, 21, 45, 69, 93, 117, 141, 165, 22, 46, 70, 94, 118, 142, 166, 23, 47, 71, 95, 119, 143, 167);
                            foreach ($rowlines as $rowline) {

                                $runsNo++;
                                $runsNo2++;
                                $class = 'btn-light';
                                $display = 'none';
                                $color = 'white';
                                $data = '---';
                                $display = 'block';
                                $color = $grid[$rowline]['COLOR'];
                                $data = $grid[$rowline]['SHORT_NAME'];
                                $style = 'background-color: ' . $color;
                                if ($runsNo == 0) {
                                    echo "<tr>";
                                }
                                ?>
                                <td><a href="javascript:;" style="background: <?php echo $color; ?>;"
                                        id="clocklink_<?php echo $rowline; ?>" class="btn btn-sm <?php echo $class; ?>"
                                        onclick="selectclock(<?php echo $rowline; ?>,'<?php echo $grid[$rowline]['CLOCK_NAME']; ?>')"><span
                                            class="fs-6 font-bold">
                                            <?php echo sprintf('%02d', $clockNo) . '-' . sprintf('%02d', $clockNo + 1); ?>
                                        </span>
                                        <span id="clockname_<?php echo $rowline; ?>" class="fs-6">
                                            <?php echo $data ?>
                                        </span></a>

                                </td>
                                <?php if ($runsNo2 == 6) {
                                    $clockNo++;
                                    echo "</tr>";
                                }
                                if ($runsNo >= 6) {
                                    $runsNo = -1;
                                }
                                if ($runsNo2 >= 6) {
                                    $runsNo2 = -1;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
    <div class="modal fade text-left" id="clock_modal" data-bs-backdrop="static" role="dialog"
        aria-labelledby="clockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="clockLabel">
                        <?= $ml->tr('ASSIGNCLOCK') ?>
                    </h4>
                    <button type="button" class="close" data-kt-clock-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <P>
                            <?= $ml->tr('SELECTCLOCKHOUR') ?>
                        </P>
                        <div class="table-responsive">
                            <table class="table table-lg">
                                <thead>
                                    <tr>
                                        <th>
                                            <?= $ml->tr('NAME') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('CODE') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('SELECT') ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clocks as $clock) { ?>
                                        <tr>
                                            <td>
                                                <P style="color:<?php echo $clock['COLOR']; ?>">
                                                    <?php echo $clock['NAME']; ?>
                                                </P>
                                            </td>
                                            <td>
                                                <?php echo $clock['SHORT_NAME']; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group mb-3" role="group">
                                                    <button
                                                        onclick="addclock('<?php echo $clock['NAME']; ?>', '<?php echo $clock['SHORT_NAME']; ?>', '<?php echo $clock['COLOR']; ?>')"
                                                        type="button" class="btn btn-success">
                                                        <?= $ml->tr('ADD') ?>
                                                    </button>
                                                    <button type="button"
                                                        onclick="addallclock('<?php echo $clock['NAME']; ?>', '<?php echo $clock['SHORT_NAME']; ?>', '<?php echo $clock['COLOR']; ?>', '<?php echo $activeService; ?>')"
                                                        class="btn btn-danger">
                                                        <?= $ml->tr('ADDONALL') ?>
                                                    </button>
                                                </div>
                                            </td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-kt-clock-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="save_grid" data-bs-backdrop="static" role="dialog" aria-labelledby="SaveLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="SaveLabel">
                        <?= $ml->tr('SAVEGRIDLAYOUT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-savelayout-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="save_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <P><?= $ml->tr('SAVEGRIDLAYOUTINFO') ?></P>
                                <div class="col-md-4">
                                    <label for="layoutname">
                                        <?= $ml->tr('LAYOUTNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="layoutname" class="form-control" name="layoutname">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="serviceid" id="serviceid">
                        <button type="button" class="btn btn-light-secondary" data-kt-savelayout-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="layout_select" data-bs-backdrop="static" role="dialog"
        aria-labelledby="layoutSelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="layoutSelLabel">
                        <?= $ml->tr('SELLAYOUTGRID') ?>
                    </h4>
                    <button type="button" class="close" data-kt-gridlayout-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">


                    <div class="table-responsive">
                        <table class="table" id="gridlayout_table">
                            <thead>
                                <tr>
                                    <th>
                                        <?= $ml->tr('LAYOUTNAME') ?>
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
                    <button type="button" class="btn btn-light-secondary" data-kt-gridlayout-modal-action="close">
                        <?= $ml->tr('CLOSE') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="layoutedit_select" data-bs-backdrop="static" role="dialog"
        aria-labelledby="layoutEdLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="layoutEdLabel">
                        <?= $ml->tr('EDITGRIDLAYOUT') ?>
                    </h4>
                    <button type="button" class="close" data-kt-gridlayoutedit-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">


                    <div class="table-responsive">
                        <table class="table" id="gridedit_table">
                            <thead>
                                <tr>
                                    <?php
                                    $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
                                    foreach ($days as $day) {
                                        ?>
                                        <th>
                                            <?php if ($day == 'mon') {
                                                echo $ml->tr('MONDAY');
                                            } else if ($day == 'tue') {
                                                echo $ml->tr('TUESDAY');
                                            } else if ($day == 'wed') {
                                                echo $ml->tr('WEDNESDAY');
                                            } else if ($day == 'thu') {
                                                echo $ml->tr('THURSDAY');
                                            } else if ($day == 'fri') {
                                                echo $ml->tr('FRIDAY');
                                            } else if ($day == 'sat') {
                                                echo $ml->tr('SATURDAY');
                                            } else if ($day == 'sun') {
                                                echo $ml->tr('SUNDAY');
                                            } ?>
                                        </th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $clockNo = 0;
                            $runsNo = -1;
                            $runsNo2 = -1;
                            $rowlines = array(0, 24, 48, 72, 96, 120, 144, 1, 25, 49, 73, 97, 121, 145, 2, 26, 50, 74, 98, 122, 146, 3, 27, 51, 75, 99, 123, 147, 4, 28, 52, 76, 100, 124, 148, 5, 29, 53, 77, 101, 125, 149, 6, 30, 54, 78, 102, 126, 150, 7, 31, 55, 79, 103, 127, 151, 8, 32, 56, 80, 104, 128, 152, 9, 33, 57, 81, 105, 129, 153, 10, 34, 58, 82, 106, 130, 154, 11, 35, 59, 83, 107, 131, 155, 12, 36, 60, 84, 108, 132, 156, 13, 37, 61, 85, 109, 133, 157, 14, 38, 62, 86, 110, 134, 158, 15, 39, 63, 87, 111, 135, 159, 16, 40, 64, 88, 112, 136, 160, 17, 41, 65, 89, 113, 137, 161, 18, 42, 66, 90, 114, 138, 162, 19, 43, 67, 91, 115, 139, 163, 20, 44, 68, 92, 116, 140, 164, 21, 45, 69, 93, 117, 141, 165, 22, 46, 70, 94, 118, 142, 166, 23, 47, 71, 95, 119, 143, 167);
                            foreach ($rowlines as $rowline) {

                                $runsNo++;
                                $runsNo2++;
                                $class = 'btn-light';
                                $display = 'none';
                                $color = 'white';
                                $data = '---';
                                $display = 'block';
                                $style = 'background-color: ' . $color;
                                if ($runsNo == 0) {
                                    echo "<tr>";
                                }
                                ?>
                                <td><a href="javascript:;" style="background: <?php echo $color; ?>;"
                                        id="edclocklink_<?php echo $rowline; ?>" class="btn btn-sm <?php echo $class; ?>"
                                        onclick="replacelayouthour(<?php echo $rowline; ?>)" data-bs-stacked-modal="#layoutclock_modal"><span
                                            class="fs-6 font-bold">
                                            <?php echo sprintf('%02d', $clockNo) . '-' . sprintf('%02d', $clockNo + 1); ?>
                                        </span>
                                        <span id="edclockname_<?php echo $rowline; ?>" class="fs-6">
                                            <?php echo $data ?>
                                        </span></a>

                                </td>
                                <?php if ($runsNo2 == 6) {
                                    $clockNo++;
                                    echo "</tr>";
                                }
                                if ($runsNo >= 6) {
                                    $runsNo = -1;
                                }
                                if ($runsNo2 >= 6) {
                                    $runsNo2 = -1;
                                }
                            } ?>
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-kt-gridlayoutedit-modal-action="close">
                        <?= $ml->tr('CLOSE') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="layoutclock_modal" data-bs-backdrop="static" role="dialog"
        aria-labelledby="layoutclockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="layoutclockLabel">
                        <?= $ml->tr('ASSIGNCLOCK') ?>
                    </h4>
                    <button type="button" class="close" data-kt-clocklayout-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <P>
                            <?= $ml->tr('SELECTCLOCKHOUR') ?>
                        </P>
                        <div class="table-responsive">
                            <table class="table table-lg">
                                <thead>
                                    <tr>
                                        <th>
                                            <?= $ml->tr('NAME') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('CODE') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('SELECT') ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clocks as $clock) { ?>
                                        <tr>
                                            <td>
                                                <P style="color:<?php echo $clock['COLOR']; ?>">
                                                    <?php echo $clock['NAME']; ?>
                                                </P>
                                            </td>
                                            <td>
                                                <?php echo $clock['SHORT_NAME']; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group mb-3" role="group">
                                                    <button
                                                        onclick="addlayoutclock('<?php echo $clock['NAME']; ?>', '<?php echo $clock['SHORT_NAME']; ?>', '<?php echo $clock['COLOR']; ?>')"
                                                        type="button" class="btn btn-success">
                                                        <?= $ml->tr('ADD') ?>
                                                    </button>
                                                    <button type="button"
                                                        onclick="layoutaddall('<?php echo $clock['NAME']; ?>', '<?php echo $clock['SHORT_NAME']; ?>', '<?php echo $clock['COLOR']; ?>')"
                                                        class="btn btn-danger">
                                                        <?= $ml->tr('ADDONALL') ?>
                                                    </button>
                                                </div>
                                            </td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-kt-clocklayout-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>



</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>