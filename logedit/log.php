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
$groupinfo = $dbfunc->getUserGroup($username);
$servicesUsr = $dbfunc->getUserService($username);
$schedCodes = $dbfunc->getSchedulerCodes();
$events = $logfunc->getRivendellEvents($_COOKIE['serviceName']);
$id = $_GET['log'];
$lockguid = $functions->locklog($id);
$pagecode = "logs";
if ($lockguid != "") {
    if (isset($logedit_data[$id]) && $logedit_data[$id]['LOCK_GUID'] != $lockguid) {
        unset($logedit_data[$id]);
        $jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData);

        $extra = array(
            'NAME' => $info->getLogInfo($id, "NAME"),
            'SERVICE' => $info->getLogInfo($id, "SERVICE"),
            'DESCRIPTION' => $info->getLogInfo($id, "DESCRIPTION"),
            'AUTO_REFRESH' => $info->getLogInfo($id, "AUTO_REFRESH"),
            'START_DATE' => $info->getLogInfo($id, "START_DATE"),
            'END_DATE' => $info->getLogInfo($id, "END_DATE"),
            'PURGE_DATE' => $info->getLogInfo($id, "PURGE_DATE"),
            'NEXT_ID' => $info->getLogInfo($id, "NEXT_ID"),
            'LOCK_GUID' => $lockguid
        );

        $groupSet = array();
        $timebefore = 0;
        $faketime = 0;
        $rowcount = 0;

        $sql = "SELECT * FROM LOG_LINES WHERE LOG_NAME = '$id' ORDER BY COUNT ASC";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $cartno = $row['CART_NUMBER'];
            $sql2 = "SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.NUMBER = '$cartno'";
            $stmt1 = $db->prepare($sql2);
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $stmt1->execute();
            if ($stmt1->rowCount() == 1) {
                while ($row1 = $stmt1->fetch()) {
                    $groupname = $row1['GROUP_NAME'];
                    $title = $row1['TITLE'];
                    $artist = $row1['ARTIST'];
                    $averagelange = $row1['AVERAGE_LENGTH'];
                    $color = $row1['COLOR'];
                }
            } else {
                $groupname = "";
                $title = "";
                $artist = "";
                $averagelange = "0";
                $color = "";
            }

            if ($row['TYPE'] == 0) {
                if ($rowcount > 0) {
                    $timebefore = $timebefore + $averagelange;
                    $faketime = $timebefore - $averagelange;
                } else {
                    $timebefore = $averagelange;
                    $faketime = 0;
                }
            } else {
                $faketime = $timebefore;
            }

            $groupSet[$row['ID']] = array(
                'ID' => $row['ID'],
                'LOG_NAME' => $row['LOG_NAME'],
                'LINE_ID' => $row['LINE_ID'],
                'COUNT' => $row['COUNT'],
                'TYPE' => $row['TYPE'],
                'SOURCE' => $row['SOURCE'],
                'START_TIME' => $row['START_TIME'],
                'GRACE_TIME' => $row['GRACE_TIME'],
                'CART_NUMBER' => $row['CART_NUMBER'],
                'TIME_TYPE' => $row['TIME_TYPE'],
                'TRANS_TYPE' => $row['TRANS_TYPE'],
                'START_POINT' => $row['START_POINT'],
                'END_POINT' => $row['END_POINT'],
                'FADEUP_POINT' => $row['FADEUP_POINT'],
                'FADEUP_GAIN' => $row['FADEUP_GAIN'],
                'FADEDOWN_POINT' => $row['FADEDOWN_POINT'],
                'FADEDOWN_GAIN' => $row['FADEDOWN_GAIN'],
                'SEGUE_START_POINT' => $row['SEGUE_START_POINT'],
                'SEGUE_END_POINT' => $row['SEGUE_END_POINT'],
                'SEGUE_GAIN' => $row['SEGUE_GAIN'],
                'DUCK_UP_GAIN' => $row['DUCK_UP_GAIN'],
                'DUCK_DOWN_GAIN' => $row['DUCK_DOWN_GAIN'],
                'COMMENT' => $row['COMMENT'],
                'LABEL' => $row['LABEL'],
                'ORIGIN_USER' => $row['ORIGIN_USER'],
                'ORIGIN_DATETIME' => $row['ORIGIN_DATETIME'],
                'EVENT_LENGTH' => $row['EVENT_LENGTH'],
                'LINK_EVENT_NAME' => $row['LINK_EVENT_NAME'],
                'LINK_START_TIME' => $row['LINK_START_TIME'],
                'LINK_START_SLOP' => $row['LINK_START_SLOP'],
                'LINK_END_SLOP' => $row['LINK_END_SLOP'],
                'LINK_LENGTH' => $row['LINK_LENGTH'],
                'LINK_ID' => $row['LINK_ID'],
                'LINK_EMBEDDED' => $row['LINK_EMBEDDED'],
                'EXT_START_TIME' => $row['EXT_START_TIME'],
                'EXT_LENGTH' => $row['EXT_LENGTH'],
                'EXT_CART_NAME' => $row['EXT_CART_NAME'],
                'EXT_DATA' => $row['EXT_DATA'],
                'EXT_EVENT_ID' => $row['EXT_EVENT_ID'],
                'EXT_ANNC_TYPE' => $row['EXT_ANNC_TYPE'],
                'GROUP_NAME' => $groupname,
                'TITLE' => $title,
                'ARTIST' => $artist,
                'AVERAGE_LENGTH' => $averagelange,
                'COLOR' => $color,
                'FAKE_TIME' => $faketime,
                'NEW_LINE' => 'N',
            );
        }

        $logedit_data[$id] = $extra;
        $logedit_data[$id]['LOGLINES'] = $groupSet;
        $jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
        if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData)) {
            header('Location: ' . DIR . '/login');
            exit();
        }
    } else if (!$logedit_data[$id]) {
        //Store in json for temporary edit. On save it removes from temp json also on cancel.
        $extra = array(
            'NAME' => $info->getLogInfo($id, "NAME"),
            'SERVICE' => $info->getLogInfo($id, "SERVICE"),
            'DESCRIPTION' => $info->getLogInfo($id, "DESCRIPTION"),
            'AUTO_REFRESH' => $info->getLogInfo($id, "AUTO_REFRESH"),
            'START_DATE' => $info->getLogInfo($id, "START_DATE"),
            'END_DATE' => $info->getLogInfo($id, "END_DATE"),
            'PURGE_DATE' => $info->getLogInfo($id, "PURGE_DATE"),
            'NEXT_ID' => $info->getLogInfo($id, "NEXT_ID"),
            'LOCK_GUID' => $lockguid
        );

        $groupSet = array();
        $timebefore = 0;
        $faketime = 0;
        $rowcount = 0;
        $sql = "SELECT * FROM LOG_LINES WHERE LOG_NAME = '$id' ORDER BY COUNT ASC";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $cartno = $row['CART_NUMBER'];
            $sql2 = "SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.NUMBER = '$cartno'";
            $stmt1 = $db->prepare($sql2);
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $stmt1->execute();
            if ($stmt1->rowCount() == 1) {
                while ($row1 = $stmt1->fetch()) {
                    $groupname = $row1['GROUP_NAME'];
                    $title = $row1['TITLE'];
                    $artist = $row1['ARTIST'];
                    $averagelange = $row1['AVERAGE_LENGTH'];
                    $color = $row1['COLOR'];
                }
            } else {
                $groupname = "";
                $title = "";
                $artist = "";
                $averagelange = "0";
                $color = "";
            }

            if ($row['TYPE'] == 0) {
                if ($rowcount > 0) {
                    $timebefore = $timebefore + $averagelange;
                    $faketime = $timebefore - $averagelange;
                } else {
                    $timebefore = $averagelange;
                    $faketime = 0;
                }
            } else {
                $faketime = $timebefore;
            }

            $groupSet[$row['ID']] = array(
                'ID' => $row['ID'],
                'LOG_NAME' => $row['LOG_NAME'],
                'LINE_ID' => $row['LINE_ID'],
                'COUNT' => $row['COUNT'],
                'TYPE' => $row['TYPE'],
                'SOURCE' => $row['SOURCE'],
                'START_TIME' => $row['START_TIME'],
                'GRACE_TIME' => $row['GRACE_TIME'],
                'CART_NUMBER' => $row['CART_NUMBER'],
                'TIME_TYPE' => $row['TIME_TYPE'],
                'TRANS_TYPE' => $row['TRANS_TYPE'],
                'START_POINT' => $row['START_POINT'],
                'END_POINT' => $row['END_POINT'],
                'FADEUP_POINT' => $row['FADEUP_POINT'],
                'FADEUP_GAIN' => $row['FADEUP_GAIN'],
                'FADEDOWN_POINT' => $row['FADEDOWN_POINT'],
                'FADEDOWN_GAIN' => $row['FADEDOWN_GAIN'],
                'SEGUE_START_POINT' => $row['SEGUE_START_POINT'],
                'SEGUE_END_POINT' => $row['SEGUE_END_POINT'],
                'SEGUE_GAIN' => $row['SEGUE_GAIN'],
                'DUCK_UP_GAIN' => $row['DUCK_UP_GAIN'],
                'DUCK_DOWN_GAIN' => $row['DUCK_DOWN_GAIN'],
                'COMMENT' => $row['COMMENT'],
                'LABEL' => $row['LABEL'],
                'ORIGIN_USER' => $row['ORIGIN_USER'],
                'ORIGIN_DATETIME' => $row['ORIGIN_DATETIME'],
                'EVENT_LENGTH' => $row['EVENT_LENGTH'],
                'LINK_EVENT_NAME' => $row['LINK_EVENT_NAME'],
                'LINK_START_TIME' => $row['LINK_START_TIME'],
                'LINK_START_SLOP' => $row['LINK_START_SLOP'],
                'LINK_END_SLOP' => $row['LINK_END_SLOP'],
                'LINK_LENGTH' => $row['LINK_LENGTH'],
                'LINK_ID' => $row['LINK_ID'],
                'LINK_EMBEDDED' => $row['LINK_EMBEDDED'],
                'EXT_START_TIME' => $row['EXT_START_TIME'],
                'EXT_LENGTH' => $row['EXT_LENGTH'],
                'EXT_CART_NAME' => $row['EXT_CART_NAME'],
                'EXT_DATA' => $row['EXT_DATA'],
                'EXT_EVENT_ID' => $row['EXT_EVENT_ID'],
                'EXT_ANNC_TYPE' => $row['EXT_ANNC_TYPE'],
                'GROUP_NAME' => $groupname,
                'TITLE' => $title,
                'ARTIST' => $artist,
                'AVERAGE_LENGTH' => $averagelange,
                'COLOR' => $color,
                'FAKE_TIME' => $faketime,
                'NEW_LINE' => 'N',
            );
            $rowcount = $rowcount + 1;
        }

        $logedit_data[$id] = $extra;
        $logedit_data[$id]['LOGLINES'] = $groupSet;
        $jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
        if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData)) {
            header('Location: ' . DIR . '/login');
            exit();
        }

    }
}
$page_vars = 'log';
$page_title = $ml->tr('LOG');
$page_css = '<link rel="stylesheet" href="' . DIR . '/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="' . DIR . '/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="' . DIR . '/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="' . DIR . '/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="' . DIR . '/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="' . DIR . '/assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="' . DIR . '/assets/extensions/inputmask/jquery.inputmask.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/timeline.min.js"></script>
<script src="' . DIR . '/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="' . DIR . '/assets/static/js/log.js"></script>';

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('LOG'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('EDITLOGHERE {{' . $id . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/logedit/logs">
                                <?= $ml->tr('LOGS'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('EDITLOG'); ?>
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
                            <?= $ml->tr('LOGINFO'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="log_form" action="#" method="get">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="kt_service">
                                            <?= $ml->tr('SERVICE') ?>
                                        </label>
                                        <select id="kt_service" name="service" class="choices form-select">
                                            <?php foreach ($servicesUsr as $scode) { ?>
                                                <option value="<?php echo $scode; ?>" <?php if ($info->getLogInfo($id, "SERVICE") == $scode) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $scode; ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="description">
                                            <?= $ml->tr('DESCRIPTION') ?>
                                        </label>
                                        <textarea class="form-control" id="description" name="description"
                                            rows="2"><?php echo $info->getLogInfo($id, "DESCRIPTION"); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="startdateac" name="startdateac" value="1"
                                                class="form-check-input" <?php if ($info->getLogInfo($id, "START_DATE") != '') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="startdateac">
                                                <?= $ml->tr('STARTDATEENABLE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="logstartdate">
                                            <?= $ml->tr('STARTDATE') ?>
                                        </label>
                                        <input type="text" name="logstartdate" id="logstartdate" class="form-control"
                                            value="<?php echo $info->getLogInfo($id, "START_DATE");
                                            ?>" <?php if ($info->getLogInfo($id, "START_DATE") == '') {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="enddateac" name="enddateac" value="1"
                                                class="form-check-input" <?php if ($info->getLogInfo($id, "END_DATE") != '') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="enddateac">
                                                <?= $ml->tr('ENDDATEENABLE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="logenddate">
                                            <?= $ml->tr('ENDDATE') ?>
                                        </label>
                                        <input type="text" name="logenddate" id="logenddate" class="form-control" value="<?php echo $info->getLogInfo($id, "END_DATE");
                                        ?>" <?php if ($info->getLogInfo($id, "END_DATE") == '') {
                                            echo "DISABLED";
                                        } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="removedateac" name="removedateac" value="1"
                                                class="form-check-input" <?php if ($info->getLogInfo($id, "PURGE_DATE") != '') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="removedateac">
                                                <?= $ml->tr('REMOVEDATEENABLE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="logremovedate">
                                            <?= $ml->tr('REMOVEDATE') ?>
                                        </label>
                                        <input type="text" name="logremovedate" id="logremovedate" class="form-control"
                                            value="<?php echo $info->getLogInfo($id, "PURGE_DATE");
                                            ?>" <?php if ($info->getLogInfo($id, "PURGE_DATE") == '') {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-2">
                                <div class="form-check">
                                    <div class="checkbox">
                                        <input type="checkbox" id="autorefresh" name="autorefresh" value="1"
                                            class="form-check-input" <?php if ($info->getLogInfo($id, "AUTO_REFRESH") == 'Y') {
                                                echo "CHECKED";
                                            } ?>>
                                        <label for="autorefresh">
                                            <?= $ml->tr('ENABLEAUTOREF') ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <input type="hidden" id="logname" name="logname" value="<?php echo $id; ?>">
                                <a href="logs.php" id="kt_edit_log_cancel" class="btn btn-warning">
                                    <?= $ml->tr('CLOSE') ?>
                                </a>
                                <button type="submit" class="btn btn-danger">
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
                            <?= $ml->tr('LOGLINES') ?>
                        </h5>
                        <div class="btn-group mb-3" role="group">
                            <button type="button" onclick="addtolog('<?php echo $id; ?>', '1', 'EE', '0')"
                                class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDCART') ?>"><i class="bi bi-music-note"></i></button>
                            <button type="button" onclick="addtolog('<?php echo $id; ?>', '2', 'EE', '0')"
                                class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDVOICETRACK') ?>"><i class="bi bi-mic"></i></button>
                            <button type="button" onclick="addtolog('<?php echo $id; ?>', '3', 'EE', '0')"
                                class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDMARKER') ?>"><i class="bi bi-card-text"></i></button>
                            <button type="button" onclick="addtolog('<?php echo $id; ?>', '4', 'EE', '0')"
                                class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="<?= $ml->tr('ADDLOGCHAIN') ?>"><i class="bi bi-link-45deg"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="loglines_table">
                                <thead>
                                    <tr>
                                        <th>
                                            <?= $ml->tr('STARTTIME') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('TRANS') ?>
                                        </th>
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
                                            <?= $ml->tr('STARTTIME') ?>
                                        </th>
                                        <th>
                                            <?= $ml->tr('TRANS') ?>
                                        </th>
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

    <div class="modal fade text-left" id="add_voicetrack" data-bs-backdrop="static" role="dialog"
        aria-labelledby="VoicetrackLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-warning">
                    <h4 class="modal-title white" id="VoicetrackLabel">
                        <?= $ml->tr('ADDVOICETRACK') ?>
                    </h4>
                    <button type="button" class="close" data-kt-addvoicetrack-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="addVoice_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="startat_voice" name="startat"
                                                class='form-check-input' value="1">
                                            <label for="startat_voice">
                                                <?= $ml->tr('STARTAT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="startattime_voice">
                                        <?= $ml->tr('STARTATTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="startattime_voice" class="form-control" name="coutcue"
                                        value="00:00:00.0" DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="ifprevends_voice">
                                        <?= $ml->tr('TRANSITIONTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="ifprevends_voice" name="ifprevends" class="choices form-select">
                                        <option value="0">
                                            <?= $ml->tr('PLAY') ?>
                                        </option>
                                        <option value="1">
                                            <?= $ml->tr('SEGUE') ?>
                                        </option>
                                        <option value="2" selected>
                                            <?= $ml->tr('STOP') ?>
                                        </option>

                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIONIFPREVPLAY') ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-danger">
                                                <input class="form-check-input" type="radio" value="0" name="hardselect"
                                                    id="hard_select_voice" checked DISABLED>
                                                <label class="form-check-label" for="hard_select_voice">
                                                    <?= $ml->tr('STARTIMMEDIATLEY') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-warning">
                                                <input class="form-check-input" type="radio" value="1" name="hardselect"
                                                    id="hard_next_voice" DISABLED>
                                                <label class="form-check-label" for="hard_next_voice">
                                                    <?= $ml->tr('MAKENEXT') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-primary">
                                                <input class="form-check-input" type="radio" value="2" name="hardselect"
                                                    id="hard_wait_voice" DISABLED>
                                                <label class="form-check-label" for="hard_wait_voice">
                                                    <?= $ml->tr('WAITUPTO') ?>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <label for="waitupto_voice">
                                        <?= $ml->tr('WAITUPTO') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="waitupto_voice" class="form-control" name="waitupto"
                                        value="00:00" DISABLED>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('VOICETRACKINFORMATION') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="comment_voice">
                                        <?= $ml->tr('COMMENT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="comment_voice" class="form-control" name="comment" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" id="addcart_voice" value="<?php echo $id; ?>">
                        <input type="hidden" name="rowplace" id="rowplace_voice" value="">
                        <input type="hidden" name="starttimemillis" id="starttimemillis_voice" value="">
                        <input type="hidden" name="waittimemillis" id="waittimemillis_voice" value="">
                        <input type="hidden" name="therowidno" id="therowidno_voice" value="">
                        <input type="hidden" name="iseditmode" id="iseditmode_voice" value="0">
                        <input type="hidden" name="isvoicetrack" id="isvoicetrack_voice" value="1">
                        <button type="button" class="btn btn-light-secondary"
                            data-kt-addvoicetrack-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-warning ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="add_marker" data-bs-backdrop="static" role="dialog"
        aria-labelledby="MarkerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="MarkerLabel">
                        <?= $ml->tr('ADDMARKER') ?>
                    </h4>
                    <button type="button" class="close" data-kt-addmarker-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="addMarker_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="startat_marker" name="startat"
                                                class='form-check-input' value="1">
                                            <label for="startat_marker">
                                                <?= $ml->tr('STARTAT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="startattime_marker">
                                        <?= $ml->tr('STARTATTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="startattime_marker" class="form-control" name="coutcue"
                                        value="00:00:00.0" DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="ifprevends_marker">
                                        <?= $ml->tr('TRANSITIONTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="ifprevends_marker" name="ifprevends" class="choices form-select">
                                        <option value="0">
                                            <?= $ml->tr('PLAY') ?>
                                        </option>
                                        <option value="1" selected>
                                            <?= $ml->tr('SEGUE') ?>
                                        </option>
                                        <option value="2" selected>
                                            <?= $ml->tr('STOP') ?>
                                        </option>

                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIONIFPREVPLAY') ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-danger">
                                                <input class="form-check-input" type="radio" value="0" name="hardselect"
                                                    id="hard_select_marker" checked DISABLED>
                                                <label class="form-check-label" for="hard_select_marker">
                                                    <?= $ml->tr('STARTIMMEDIATLEY') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-warning">
                                                <input class="form-check-input" type="radio" value="1" name="hardselect"
                                                    id="hard_next_marker" DISABLED>
                                                <label class="form-check-label" for="hard_next_marker">
                                                    <?= $ml->tr('MAKENEXT') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-primary">
                                                <input class="form-check-input" type="radio" value="2" name="hardselect"
                                                    id="hard_wait_marker" DISABLED>
                                                <label class="form-check-label" for="hard_wait_marker">
                                                    <?= $ml->tr('WAITUPTO') ?>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <label for="waitupto_marker">
                                        <?= $ml->tr('WAITUPTO') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="waitupto_marker" class="form-control" name="waitupto"
                                        value="00:00" DISABLED>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('MARKERINFORMATION') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="comment_marker">
                                        <?= $ml->tr('COMMENT') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="comment_marker" class="form-control" name="comment" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="label_marker">
                                        <?= $ml->tr('LABEL') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="label_marker" class="form-control" name="label" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" id="addcart_marker" value="<?php echo $id; ?>">
                        <input type="hidden" name="rowplace" id="rowplace_marker" value="">
                        <input type="hidden" name="starttimemillis" id="starttimemillis_marker" value="">
                        <input type="hidden" name="waittimemillis" id="waittimemillis_marker" value="">
                        <input type="hidden" name="therowidno" id="therowidno_marker" value="">
                        <input type="hidden" name="iseditmode" id="iseditmode_marker" value="0">
                        <input type="hidden" name="isvoicetrack" id="isvoicetrack_marker" value="0">
                        <button type="button" class="btn btn-light-secondary" data-kt-addmarker-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" class="btn btn-success ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="add_cart" data-bs-backdrop="static" role="dialog" aria-labelledby="CartLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-info">
                    <h4 class="modal-title white" id="CartLabel">
                        <?= $ml->tr('ADDCART') ?>
                    </h4>
                    <button type="button" class="close" data-kt-addcart-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="addCart_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="startat_cart" name="startat"
                                                class='form-check-input' value="1">
                                            <label for="startat_cart">
                                                <?= $ml->tr('STARTAT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="startattime_cart">
                                        <?= $ml->tr('STARTATTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="startattime_cart" class="form-control" name="coutcue"
                                        value="00:00:00.0" DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="ifprevends_cart">
                                        <?= $ml->tr('TRANSITIONTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="ifprevends_cart" name="ifprevends" class="choices form-select">
                                        <option value="0">
                                            <?= $ml->tr('PLAY') ?>
                                        </option>
                                        <option value="1" selected>
                                            <?= $ml->tr('SEGUE') ?>
                                        </option>
                                        <option value="2" selected>
                                            <?= $ml->tr('STOP') ?>
                                        </option>

                                    </select>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="nofadesegue_cart" name="startat"
                                                class='form-check-input' value="1">
                                            <label for="nofadesegue">
                                                <?= $ml->tr('NOFADEONSEGUEOUT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIONIFPREVPLAY') ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-danger">
                                                <input class="form-check-input" type="radio" value="0" name="hardselect"
                                                    id="hard_select_im" checked DISABLED>
                                                <label class="form-check-label" for="hard_select_im">
                                                    <?= $ml->tr('STARTIMMEDIATLEY') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-warning">
                                                <input class="form-check-input" type="radio" value="1" name="hardselect"
                                                    id="hard_next" DISABLED>
                                                <label class="form-check-label" for="hard_next">
                                                    <?= $ml->tr('MAKENEXT') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-primary">
                                                <input class="form-check-input" type="radio" value="2" name="hardselect"
                                                    id="hard_wait" DISABLED>
                                                <label class="form-check-label" for="hard_wait">
                                                    <?= $ml->tr('WAITUPTO') ?>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <label for="waitupto_cart">
                                        <?= $ml->tr('WAITUPTO') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="waitupto_cart" class="form-control" name="waitupto"
                                        value="00:00" DISABLED>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('CARTINFORMATION') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" data-bs-stacked-modal="#cart_select" class="btn btn-info">
                                        <?= $ml->tr('SELECTCART') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="cart_value">
                                        <?= $ml->tr('CART') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="cart_value" class="form-control" name="cart_value" value=""
                                        DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="title_value">
                                        <?= $ml->tr('TITLE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="title_value" class="form-control" name="title_value" value=""
                                        DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="artist_value">
                                        <?= $ml->tr('ARTIST') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="artist_value" class="form-control" name="artist_value"
                                        value="" DISABLED>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" id="addcart_id" value="<?php echo $id; ?>">
                        <input type="hidden" name="cart" id="cartno_imp" value="">
                        <input type="hidden" name="carttype" id="carttype_imp" value="">
                        <input type="hidden" name="rowplace" id="rowplace_imp" value="">
                        <input type="hidden" name="starttimemillis" id="starttimemillis_imp" value="">
                        <input type="hidden" name="waittimemillis" id="waittimemillis_imp" value="">
                        <input type="hidden" name="therowidno" id="therowidno_imp" value="">
                        <input type="hidden" name="iseditmode" id="iseditmode_imp" value="0">
                        <button type="button" class="btn btn-light-secondary" data-kt-addcart-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" id="subbut_cart" class="btn btn-info ms-1" value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

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


    <div class="modal fade text-left" id="add_logchain" data-bs-backdrop="static" role="dialog"
        aria-labelledby="logChainLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="logChainLabel">
                        <?= $ml->tr('ADDLOGCHAIN') ?>
                    </h4>
                    <button type="button" class="close" data-kt-logchain-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <form class="form form-horizontal" id="logchain_form" action="#">
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <div class='form-check'>
                                        <div class="checkbox">
                                            <input type="checkbox" id="startat_logchain" name="startat"
                                                class='form-check-input' value="1">
                                            <label for="startat_logchain">
                                                <?= $ml->tr('STARTAT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="startattime_logchain">
                                        <?= $ml->tr('STARTATTIME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="startattime_logchain" class="form-control" name="coutcue"
                                        value="00:00:00.0" DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="ifprevends_logchain">
                                        <?= $ml->tr('TRANSITIONTYPE') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="ifprevends_logchain" name="ifprevends" class="choices form-select">
                                        <option value="0">
                                            <?= $ml->tr('PLAY') ?>
                                        </option>
                                        <option value="1" selected>
                                            <?= $ml->tr('SEGUE') ?>
                                        </option>
                                        <option value="2" selected>
                                            <?= $ml->tr('STOP') ?>
                                        </option>

                                    </select>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('ACTIONIFPREVPLAY') ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-danger">
                                                <input class="form-check-input" type="radio" value="0" name="hardselect"
                                                    id="hard_select_logchain" checked DISABLED>
                                                <label class="form-check-label" for="hard_select_logchain">
                                                    <?= $ml->tr('STARTIMMEDIATLEY') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-warning">
                                                <input class="form-check-input" type="radio" value="1" name="hardselect"
                                                    id="hard_next_logchain" DISABLED>
                                                <label class="form-check-label" for="hard_next_logchain">
                                                    <?= $ml->tr('MAKENEXT') ?>
                                                </label>
                                            </div>
                                        </li>
                                        <li class="d-inline-block me-2 mb-1">
                                            <div class="form-check form-check-primary">
                                                <input class="form-check-input" type="radio" value="2" name="hardselect"
                                                    id="hard_wait_logchain" DISABLED>
                                                <label class="form-check-label" for="hard_wait_logchain">
                                                    <?= $ml->tr('WAITUPTO') ?>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <label for="waitupto_logchain">
                                        <?= $ml->tr('WAITUPTO') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="waitupto_logchain" class="form-control" name="waitupto"
                                        value="00:00" DISABLED>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">
                                        <?= $ml->tr('LOGCHAININFO') ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 offset-md-4 form-group">
                                    <a href="javascript:;" data-bs-stacked-modal="#logchain_select"
                                        class="btn btn-primary">
                                        <?= $ml->tr('CHAINSELECTLOG') ?>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <label for="logname_logchain">
                                        <?= $ml->tr('LOGNAME') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="logname_logchain" class="form-control" name="logname"
                                        value="" DISABLED>
                                </div>
                                <div class="col-md-4">
                                    <label for="logdesc_logchain">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="logdesc_logchain" class="form-control" name="logdesc"
                                        value="" DISABLED>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idet" id="addcart_chain" value="<?php echo $id; ?>">
                        <input type="hidden" name="rowplace" id="rowplace_chain" value="">
                        <input type="hidden" name="starttimemillis" id="starttimemillis_chain" value="">
                        <input type="hidden" name="waittimemillis" id="waittimemillis_chain" value="">
                        <input type="hidden" name="thelogname" id="thelogname_chain" value="">
                        <input type="hidden" name="thelogdesc" id="thelogdesc_chain" value="">
                        <input type="hidden" name="therowidno" id="therowidno_chain" value="">
                        <input type="hidden" name="iseditmode" id="iseditmode_chain" value="0">
                        <button type="button" class="btn btn-light-secondary" data-kt-logchain-modal-action="close">
                            <?= $ml->tr('CLOSE') ?>
                        </button>
                        <input type="submit" id="subbut_chain" class="btn btn-primary ms-1"
                            value="<?= $ml->tr('SAVE') ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="logchain_select" data-bs-backdrop="static" role="dialog"
        aria-labelledby="selChainLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-primary">
                    <h4 class="modal-title white" id="selChainLabel">
                        <?= $ml->tr('ADDFROMLIBRARY') ?>
                    </h4>
                    <button type="button" class="close" data-kt-logsel-modal-action="cancel" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <select id="selectService" class="choices form-select">
                        <option value="allservices">
                            <?= $ml->tr('ALLSERVICES') ?>
                        </option>
                        <?php foreach ($servicesUsr as $userv) { ?>
                            <option value="<?php echo $userv; ?>">
                                <?php echo $userv; ?>
                            </option>
                        <?php } ?>

                    </select>
                    <div class="table-responsive">
                        <table class="table" id="chain_table">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <?= $ml->tr('LOGNAME') ?>
                                    </th>
                                    <th scope="col">
                                        <?= $ml->tr('DESCRIPTION') ?>
                                    </th>
                                    <th scope="col">
                                        <?= $ml->tr('SERVICE') ?>
                                    </th>
                                    <th scope="col">
                                        <?= $ml->tr('MUSICMERGED') ?>
                                    </th>
                                    <th scope="col">
                                        <?= $ml->tr('TRAFFICMERGED') ?>
                                    </th>
                                    <th scope="col">
                                        <?= $ml->tr('TRACKS') ?>
                                    </th>
                                    <th scope="col">
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
                    <input type="hidden" name="idet" id="idet2" value="<?php echo $id; ?>">
                    <input type="hidden" name="vttype" id="vttype2" value="">
                    <input type="hidden" name="imptype" id="imptype2" value="">
                    <button type="button" class="btn btn-light-secondary" data-kt-logsel-modal-action="close">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">
                            <?= $ml->tr('CLOSE') ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="still_edit" data-bs-backdrop="static" role="dialog"
        aria-labelledby="stillEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-danger">
                    <h4 class="modal-title white" id="stillEditLabel">
                        <?= $ml->tr('STILLEDITLOG') ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <p>
                        <?= $ml->tr('STILLEDITLOG1') ?>
                    </p>
                    <p>
                        <?= $ml->tr('STILLEDITLOG2') ?>
                    </p>
                    <p>
                        <?= $ml->tr('STILLEDITLOG3') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>