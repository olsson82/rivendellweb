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
$cutinfo = $dbfunc->getEventData($_GET['id']);
$groupinfo = $dbfunc->getUserGroup($username);
$servicesUsr = $dbfunc->getUserService($username);
$schedCodes = $dbfunc->getSchedulerCodes();
$events = $logfunc->getRivendellEvents($_COOKIE['serviceName']);
$id = $_GET['id'];
$pagecode = "events";
$page_vars = 'event';
$page_title = $ml->tr('EVENT');
$page_css = '<link rel="stylesheet" href="assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="https://unpkg.com/huebee@2/dist/huebee.min.css">
<link rel="stylesheet" href="assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="./assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="https://unpkg.com/huebee@2/dist/huebee.pkgd.min.js"></script>
<script src="assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="assets/extensions/inputmask/jquery.inputmask.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/timeline.min.js"></script>
<script src="assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="assets/static/js/event.js"></script>';
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('EDITEVENT'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('EDITEVENTHERE {{' . $id . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dash.php">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="events.php">
                                <?= $ml->tr('EVENTS'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('EDITEVENT'); ?>
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
                            <?= $ml->tr('EVENTINFO'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="event_form" action="#" method="post">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="colors">
                                            <?= $ml->tr('COLOR') ?>
                                        </label>
                                        <input type="text" data-huebee='{ "notation": "hex", "saturations": 2 }'
                                            name="colors" id="colors" class="form-control color-input"
                                            value="<?php echo $info->getEventInfo($id, "COLOR"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="usrnotes">
                                            <?= $ml->tr('USERNOTES') ?>
                                        </label>
                                        <textarea class="form-control" id="usrnotes" name="usrnotes"
                                            rows="2"><?php echo $info->getEventInfo($id, "REMARKS"); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="cueevent" name="cueevent" value="1"
                                                class="form-check-input" <?php if ($info->getEventInfo($id, "PREPOSITION") != '-1') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="cueevent">
                                                <?= $ml->tr('CUETOEVENT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="schedstart">
                                            <?= $ml->tr('BEFORESCHEDSTART') ?>
                                        </label>
                                        <input type="text" name="schedstart" id="schedstart" class="form-control" value="<?php if ($info->getEventInfo($id, "PREPOSITION") == '-1') {
                                            echo "00:00";
                                        } else {
                                            echo $functions->msToHHMMSS($info->getEventInfo($id, "PREPOSITION"));
                                        } ?>" <?php if ($info->getEventInfo($id, "PREPOSITION") == '-1') {
                                             echo "DISABLED";
                                         } ?>>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('FIRSTCARTSTOP') ?>
                                            </small></p>
                                        <input type="hidden" name="cuestartmillis" id="cuestartmillis" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="hardtime" name="hardtime" value="1"
                                                class="form-check-input" <?php if ($info->getEventInfo($id, "TIME_TYPE") != '0') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="hardtime">
                                                <?= $ml->tr('USEHARDSTARTTIME') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-danger">
                                                    <input class="form-check-input" type="radio" value="0"
                                                        name="hardselect" id="hard_select_im" <?php if ($info->getEventInfo($id, "GRACE_TIME") == '0') {
                                                            echo 'checked="checked"';
                                                        }
                                                        if ($info->getEventInfo($id, "TIME_TYPE") == '0') {
                                                            echo "DISABLED";
                                                        } ?>>
                                                    <label class="form-check-label" for="hard_select_im">
                                                        <?= $ml->tr('STARTIMMEDIATLEY') ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-warning">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="hardselect" id="hard_next" <?php if ($info->getEventInfo($id, "GRACE_TIME") == '-1') {
                                                            echo 'checked="checked"';
                                                        } ?> <?php if ($info->getEventInfo($id, "TIME_TYPE") == '0') {
                                                              echo "DISABLED";
                                                          } ?>>
                                                    <label class="form-check-label" for="hard_next">
                                                        <?= $ml->tr('MAKENEXT') ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-primary">
                                                    <input class="form-check-input" type="radio" value="2"
                                                        name="hardselect" id="hard_wait" <?php if ($info->getEventInfo($id, "GRACE_TIME") > '0') {
                                                            echo 'checked="checked"';
                                                        } ?> <?php if ($info->getEventInfo($id, "TIME_TYPE") == '0') {
                                                              echo "DISABLED";
                                                          } ?>>
                                                    <label class="form-check-label" for="hard_wait">
                                                        <?= $ml->tr('WAITUPTO') ?>
                                                    </label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="waitupto">
                                            <?= $ml->tr('WAITUPTO') ?>
                                        </label>
                                        <input type="text" name="waitupto" id="waitupto" class="form-control" value="<?php if ($info->getEventInfo($id, "GRACE_TIME") > '0') {
                                            echo $functions->msToHHMMSS($info->getEventInfo($id, "GRACE_TIME"));
                                        } else {
                                            echo "00:00";
                                        } ?>" <?php if (!($info->getEventInfo($id, "GRACE_TIME") > '0')) {
                                             echo "DISABLED";
                                         } ?>>
                                        <input type="hidden" name="waituptomillis" id="waituptomillis" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="firstcart">
                                            <?= $ml->tr('FIRSTCARTHASA') ?>
                                        </label>
                                        <select id="firstcart" name="firstcart" class="choices form-select">
                                            <option value="0" <?php if ($info->getEventInfo($id, "FIRST_TRANS_TYPE") == '0') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('PLAY') ?>
                                            </option>
                                            <option value="1" <?php if ($info->getEventInfo($id, "FIRST_TRANS_TYPE") == '1') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('SEGUE') ?>
                                            </option>
                                            <option value="2" <?php if ($info->getEventInfo($id, "FIRST_TRANS_TYPE") == '2') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('STOP') ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="importcart">
                                            <?= $ml->tr('IMPORTEDCARTHAVE') ?>
                                        </label>
                                        <select id="importcart" name="importcart" class="form-select">
                                            <option value="0" <?php if ($info->getEventInfo($id, "DEFAULT_TRANS_TYPE") == '0') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('PLAY') ?>
                                            </option>
                                            <option value="1" <?php if ($info->getEventInfo($id, "DEFAULT_TRANS_TYPE") == '1') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('SEGUE') ?>
                                            </option>
                                            <option value="2" <?php if ($info->getEventInfo($id, "DEFAULT_TRANS_TYPE") == '2') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('STOP') ?>
                                            </option>

                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="autofill" name="autofill" value="1"
                                                class="form-check-input" <?php if ($info->getEventInfo($id, "USE_AUTOFILL") != 'N') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="autofill">
                                                <?= $ml->tr('USEAUTOFILL') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="warnoverunder" name="warnoverunder" value="1"
                                                class="form-check-input" <?php if ($info->getEventInfo($id, "AUTOFILL_SLOP") != '-1') {
                                                    echo "CHECKED";
                                                } ?> <?php if ($info->getEventInfo($id, "USE_AUTOFILL") == 'N') {
                                                      echo "DISABLED";
                                                  } ?>>
                                            <label for="warnoverunder">
                                                <?= $ml->tr('WARN') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="byleast">
                                            <?= $ml->tr('BYATLEAST') ?>
                                        </label>
                                        <input type="text" id="byleast" class="form-control" value="<?php if ($info->getEventInfo($id, "AUTOFILL_SLOP") == '-1') {
                                            echo "00:00";
                                        } else {
                                            echo $info->getEventInfo($id, "AUTOFILL_SLOP");
                                        } ?>" name="byleast" <?php if ($info->getEventInfo($id, "USE_AUTOFILL") == 'N' || $info->getEventInfo($id, "AUTOFILL_SLOP") == '-1') {
                                             echo "DISABLED";
                                         } ?>>
                                        <input type="hidden" name="byleastmillis" id="byleastmillis" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-danger">
                                                    <input class="form-check-input" type="radio" value="0"
                                                        name="importopt" id="import_none" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") == '0') {
                                                            echo 'CHECKED';
                                                        } ?>>
                                                    <label class="form-check-label" for="import_none">
                                                        <?= $ml->tr('NONE') ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-warning">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="importopt" id="import_traffic" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") == '1') {
                                                            echo 'CHECKED';
                                                        } ?>>
                                                    <label class="form-check-label" for="import_traffic">
                                                        <?= $ml->tr('FROMTRAFFIC') ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-primary">
                                                    <input class="form-check-input" type="radio" value="2"
                                                        name="importopt" id="import_music" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") == '2') {
                                                            echo 'CHECKED';
                                                        } ?>>
                                                    <label class="form-check-label" for="import_music">
                                                        <?= $ml->tr('FROMMUSIC') ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="d-inline-block me-2 mb-1">
                                                <div class="form-check form-check-info">
                                                    <input class="form-check-input" type="radio" value="3"
                                                        name="importopt" id="import_select" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") == '3') {
                                                            echo 'CHECKED';
                                                        } ?>>
                                                    <label class="form-check-label" for="import_select">
                                                        <?= $ml->tr('SELECTFROM') ?>
                                                    </label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="impsched1">
                                            <?= $ml->tr('IMPORTCARTSCHEDULED') ?>
                                        </label>
                                        <input type="text" id="impsched1" class="form-control"
                                            value="<?php echo $functions->msToHHMMSS($info->getEventInfo($id, "START_SLOP")); ?>"
                                            name="impsched1" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") == '0' || $info->getEventInfo($id, "IMPORT_SOURCE") == '3') {
                                                echo 'DISABLED';
                                            } ?>>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('PRIORTOSTARTEVENT') ?>
                                            </small></p>
                                        <input type="hidden" name="impsched1millis" id="impsched1millis" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="impsched2">
                                            <?= $ml->tr('IMPORTCARTSCHEDULED') ?>
                                        </label>
                                        <input type="text" id="impsched2" class="form-control"
                                            value="<?php echo $functions->msToHHMMSS($info->getEventInfo($id, "END_SLOP")); ?>"
                                            name="impsched2" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") == '0' || $info->getEventInfo($id, "IMPORT_SOURCE") == '3') {
                                                echo 'DISABLED';
                                            } ?>>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('AFTERENDOFEVENT') ?>
                                            </small></p>
                                        <input type="hidden" name="impsched2millis" id="impsched2millis" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="inline">
                                            <?= $ml->tr('IMPORTINLINETRAFFIC') ?>
                                        </label>
                                        <select id="inline" name="inline" class="form-select">
                                            <option value="0" <?php if ($info->getEventInfo($id, "NESTED_EVENT") == '') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('NONE') ?>
                                            </option>
                                            <?php foreach ($events as $listevent) { ?>
                                                <option value="<?php echo $listevent['NAME']; ?>" <?php if ($info->getEventInfo($id, "NESTED_EVENT") == $listevent['NAME']) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $listevent['NAME']; ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="group">
                                            <?= $ml->tr('SELECTFROM') ?>
                                        </label>
                                        <select id="group" name="group" class="form-select">
                                            <?php foreach ($groupinfo as $groupdata) {

                                                ?>
                                                <option value="<?php echo $groupdata; ?>" <?php if ($info->getEventInfo($id, "SCHED_GROUP") == $groupdata) {
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
                                        <label for="artsep">
                                            <?= $ml->tr('ARTISTSEPARATION') ?>
                                        </label>
                                        <input type="text" id="artsep" class="form-control" value="<?php if ($info->getEventInfo($id, "ARTIST_SEP") == '-1') {
                                            echo $ml->tr('NONE');
                                        } else {
                                            echo $info->getEventInfo($id, "ARTIST_SEP");
                                        } ?>" name="artsep" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") != '3') {
                                             echo 'DISABLED';
                                         } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="artsep">
                                            <?= $ml->tr('TITLESEPARATION') ?>
                                        </label>
                                        <input type="text" id="titsep" class="form-control" value="<?php if ($info->getEventInfo($id, "TITLE_SEP") == '-1') {
                                            echo $ml->tr('NONE');
                                        } else {
                                            echo $info->getEventInfo($id, "TITLE_SEP");
                                        } ?>" name="titsep" <?php if ($info->getEventInfo($id, "IMPORT_SOURCE") != '3') {
                                             echo 'DISABLED';
                                         } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musthave">
                                            <?= $ml->tr('MUSTHAVECODE') ?>
                                        </label>
                                        <select id="musthave" name="musthave" class="form-select">
                                            <option value="0" <?php if ($info->getEventInfo($id, 'HAVE_CODE') == '') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('NONE') ?>
                                            </option>
                                            <?php foreach ($schedCodes as $scode) { ?>
                                                <option value="<?php echo $scode['code']; ?>" <?php if ($info->getEventInfo($id, 'HAVE_CODE') == $scode['code']) {
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
                                        <label for="musthave2">
                                            <?= $ml->tr('ANDCODE') ?>
                                        </label>
                                        <select id="musthave2" name="musthave2" class="form-select">
                                            <option value="0" <?php if ($info->getEventInfo($id, 'HAVE_CODE2') == '') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('NONE') ?>
                                            </option>
                                            <?php foreach ($schedCodes as $scode) { ?>
                                                <option value="<?php echo $scode['code']; ?>" <?php if ($info->getEventInfo($id, 'HAVE_CODE2') == $scode['code']) {
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
                                        <label for="userdef">
                                            <?= $ml->tr('ASSIGNSERVICES') ?>
                                        </label>
                                        <select id="services" name="services[]" class="choices form-select" multiple>
                                            <?php foreach ($servicesUsr as $scode) { ?>
                                                <option value="<?php echo $scode; ?>" <?php if ($info->getEventService($id, $scode) == $scode) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $scode; ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <input type="hidden" id="evid" name="eventid" value="<?php echo $id; ?>">
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
                            <?= $ml->tr('PREIMPORTEVENTS') ?>
                        </h5>
                        <div class="btn-group mb-3" role="group">
                            <button type="button" onclick="addimp('<?php echo $id; ?>', '1','0')" class="btn btn-info"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDFROMLIBRARY') ?>"><i class="bi bi-music-note-list"></i></button>
                            <button type="button" onclick="addimp('<?php echo $id; ?>', '2','0')"
                                class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDVOICETRACK') ?>"><i class="bi bi-mic"></i></button>
                            <button type="button" onclick="addimp('<?php echo $id; ?>', '3','0')"
                                class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDLOGNOTE') ?>"><i class="bi bi-card-text"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="preimp_table">
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
                                            <?= $ml->tr('TRANSITION') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('ORDER') ?>
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
                                            <?= $ml->tr('TRANSITION') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('ORDER') ?>
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">
                            <?= $ml->tr('POSTIMPORTEVENTS') ?>
                        </h5>
                        <div class="btn-group mb-3" role="group">
                            <button type="button" onclick="addimp('<?php echo $id; ?>', '1','1')" class="btn btn-info"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDFROMLIBRARY') ?>"><i class="bi bi-music-note-list"></i></button>
                            <button type="button" onclick="addimp('<?php echo $id; ?>', '2','1')"
                                class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDVOICETRACK') ?>"><i class="bi bi-mic"></i></button>
                            <button type="button" onclick="addimp('<?php echo $id; ?>', '3','1')"
                                class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDLOGNOTE') ?>"><i class="bi bi-card-text"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="postimp_table">
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
                                            <?= $ml->tr('TRANSITION') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('ORDER') ?>
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
                                            <?= $ml->tr('TRANSITION') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('ORDER') ?>
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
    <div class="modal fade text-left" id="cart_select" data-bs-backdrop="static" role="dialog"
        aria-labelledby="selCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="selCartLabel">
                        <?= $ml->tr('ADDFROMLIBRARY') ?>
                    </h4>
                    <button type="button" class="close" data-kt-cartsel-modal-action="cancel" aria-label="Close">
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
                        <table class="table" id="cartadd_table">
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
                    <button type="button" class="btn btn-light-secondary" data-kt-cartsel-modal-action="close">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">
                            <?= $ml->tr('CLOSE') ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="add_vt" data-bs-backdrop="static" role="dialog" aria-labelledby="addVTLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="addVTLabel">
                        <?= $ml->tr('VOICETRACKMARKER') ?>
                    </h4>
                    <button type="button" class="close" data-kt-vtnote-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="vtmarker_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label id="vtnote" for="vtnotes">
                                        <?= $ml->tr('VOICETRACKNOTE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <textarea class="form-control" id="vtnotes" name="vtnotes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" value="<?php echo $id; ?>">
                        <input type="hidden" name="vttype" id="vttype" value="">
                        <input type="hidden" name="imptype" id="imptype" value="">
                        <input type="hidden" name="edid" id="edid" value="">
                        <input type="hidden" name="edit" id="vtedit" value="0">
                        <button type="button" class="btn btn-light-secondary" data-kt-vtnote-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                        <input type="submit" id="subbut_chain" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>