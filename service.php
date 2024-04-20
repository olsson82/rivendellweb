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
$groupinfo = $dbfunc->getGroupNames();
$rivhost = $dbfunc->getRivHosts();
$templates = $dbfunc->getImpTemp();
$id = $_GET['id'];
$pagecode = "services";
$page_vars = 'service';
$page_title = $ml->tr('EDITSERVICE');
$page_css = '<link rel="stylesheet" href="' . DIR . '/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="' . DIR . '/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="' . DIR . '/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="' . DIR . '/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="' . DIR . '/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="' . DIR . '/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="' . DIR . '/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="' . DIR . '/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="' . DIR . '/assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="' . DIR . '/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="' . DIR . '/assets/static/js/service.js"></script>';

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('EDITSERVICE'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('MANAGESERVICEHERE {{' . $id . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/services">
                                <?= $ml->tr('RIVSERVICES'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('EDITSERVICE'); ?>
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
                            <?= $ml->tr('SERVICEINFO'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="service_form" action="#" method="get">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="servicename">
                                            <?= $ml->tr('SERVICENAME') ?>
                                        </label>
                                        <input type="text" name="servicename" id="servicename" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "NAME"); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="descr">
                                            <?= $ml->tr('SERVICEDESC') ?>
                                        </label>
                                        <input type="text" name="descr" id="descr" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "DESCRIPTION"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="programc">
                                            <?= $ml->tr('PROGRAMCODE') ?>
                                        </label>
                                        <input type="text" name="programc" id="programc" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "PROGRAM_CODE"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="lognametemp">
                                            <?= $ml->tr('LOGNAMETEMPLATE') ?>
                                        </label>
                                        <input type="text" name="lognametemp" id="lognametemp" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "NAME_TEMPLATE"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="logdesctemp">
                                            <?= $ml->tr('LOGDESCTEMPLATE') ?>
                                        </label>
                                        <input type="text" name="logdesctemp" id="logdesctemp" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "DESCRIPTION_TEMPLATE"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="bypass">
                                            <?= $ml->tr('BYPASSGRID') ?>
                                        </label>
                                        <select id="bypass" name="bypass" class="choices form-select">
                                            <option value="N" <?php if ($info->getServiceInfo($id, "BYPASS_MODE") == 'N') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('NO') ?>
                                            </option>
                                            <option value="Y" <?php if ($info->getServiceInfo($id, "BYPASS_MODE") == 'Y') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('YES') ?>
                                            </option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="inline">
                                            <?= $ml->tr('INLINEEVENTSTART') ?>
                                        </label>
                                        <select id="inline" name="inline" class="form-select" <?php if ($info->getServiceInfo($id, "BYPASS_MODE") == 'Y') {
                                                echo "DISABLED";
                                            } ?>>
                                            <option value="0" <?php if ($info->getServiceInfo($id, "SUB_EVENT_INHERITANCE") == '0') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('FROMRELATIVEPOS') ?>
                                            </option>
                                            <option value="1" <?php if ($info->getServiceInfo($id, "SUB_EVENT_INHERITANCE") == '1') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('FROMSCHEDULERFILE') ?>
                                            </option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="vtgroup">
                                            <?= $ml->tr('VOICETRACKGROUP') ?>
                                        </label>
                                        <select id="vtgroup" name="vtgroup" class="choices form-select">
                                            <?php foreach ($groupinfo as $groupdata) { ?>
                                                <option value="<?php echo $groupdata; ?>" <?php if ($info->getServiceInfo($id, "TRACK_GROUP") == $groupdata) {
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
                                        <label for="autospot">
                                            <?= $ml->tr('AUTOSPOTGROUP') ?>
                                        </label>
                                        <select id="autospot" name="autospot" class="choices form-select">
                                            <?php foreach ($groupinfo as $groupdata) { ?>
                                                <option value="<?php echo $groupdata; ?>" <?php if ($info->getServiceInfo($id, "AUTOSPOT_GROUP") == $groupdata) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $groupdata; ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="chainto" name="chainto" class="form-check-input"
                                                <?php if ($info->getServiceInfo($id, "CHAIN_LOG") == 'Y') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="chainto">
                                                <?= $ml->tr('INSERTCHANTO') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="autorefresh" name="autorefresh"
                                                class="form-check-input" <?php if ($info->getServiceInfo($id, "AUTO_REFRESH") == 'Y') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="autorefresh">
                                                <?= $ml->tr('AUTOREFRESHDEFAULT') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="autodelete" name="autodelete"
                                                class="form-check-input" <?php if ($info->getServiceInfo($id, "DEFAULT_LOG_SHELFLIFE") != '-1') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="autodelete">
                                                <?= $ml->tr('SETLOGSTOAUTODELETE') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="autodeletedays">
                                            <?= $ml->tr('AUTODELETEDAYS') ?>
                                        </label>
                                        <input type="text" name="autodeletedays" id="autodeletedays"
                                            class="form-control" value="<?php if ($info->getServiceInfo($id, "DEFAULT_LOG_SHELFLIFE") != '-1') {
                                                echo $info->getServiceInfo($id, "DEFAULT_LOG_SHELFLIFE");
                                            } ?>" <?php if ($info->getServiceInfo($id, "DEFAULT_LOG_SHELFLIFE") == '-1') {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="daysaftertype">
                                            <?= $ml->tr('DAYSAFTERTYPE') ?>
                                        </label>
                                        <select id="daysaftertype" name="daysaftertype" class="form-select" <?php if ($info->getServiceInfo($id, "DEFAULT_LOG_SHELFLIFE") == '-1') {
                                                    echo "DISABLED";
                                                } ?>>
                                            <option value="0" <?php if ($info->getServiceInfo($id, "LOG_SHELFLIFE_ORIGIN") == '0') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('AIRDATELOG') ?>
                                            </option>
                                            <option value="1" <?php if ($info->getServiceInfo($id, "LOG_SHELFLIFE_ORIGIN") == '1') {
                                                echo "SELECTED";
                                            } ?>>
                                                <?= $ml->tr('CREATIONLOG') ?>
                                            </option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="purgeelr" name="purgeelr"
                                                class="form-check-input" <?php if ($info->getServiceInfo($id, "ELR_SHELFLIFE") != '-1') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="purgeelr">
                                                <?= $ml->tr('PURGEELR') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="elrdays">
                                            <?= $ml->tr('DAYSAFTERAIRING') ?>
                                        </label>
                                        <input type="text" name="elrdays" id="elrdays" class="form-control" value="<?php if ($info->getServiceInfo($id, "ELR_SHELFLIFE") != '-1') {
                                            echo $info->getServiceInfo($id, "ELR_SHELFLIFE");
                                        } ?>" <?php if ($info->getServiceInfo($id, "ELR_SHELFLIFE") == '-1') {
                                            echo "DISABLED";
                                        } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="musimpmark" name="musimpmark"
                                                class="form-check-input" <?php if ($info->getServiceInfo($id, "INCLUDE_MUS_IMPORT_MARKERS") == 'Y') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="musimpmark">
                                                <?= $ml->tr('INCMUSIMPFINISH') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <input type="checkbox" id="trafimpmark" name="trafimpmark"
                                                class="form-check-input" <?php if ($info->getServiceInfo($id, "INCLUDE_TFC_IMPORT_MARKERS") == 'Y') {
                                                    echo "CHECKED";
                                                } ?>>
                                            <label for="trafimpmark">
                                                <?= $ml->tr('INCTRAFIMPFINISH') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-2">
                                <label for="enabledhosts" class="form-label">
                                    <?= $ml->tr('ENABLEDHOSTS') ?>
                                </label>
                                <select id="enabledhosts" name="enabledhosts[]" class="choices form-select" multiple>
                                    <?php foreach ($rivhost as $hosts) { ?>
                                        <option value="<?php echo $hosts['NAME']; ?>" <?php if ($info->getServiceHost($id, $hosts['NAME']) == $hosts['NAME']) {
                                               echo "SELECTED";
                                           } ?>>
                                            <?php echo $hosts['NAME']; ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="divider">
                                <div class="divider-text">
                                    <?= $ml->tr('TRAFFICDATAIMPORT') ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="importpath">
                                            <?= $ml->tr('IMPPATH') ?>
                                        </label>
                                        <input type="text" name="importpath" id="importpath" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_PATH"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="preimpcom">
                                            <?= $ml->tr('PREIMPCOMMAND') ?>
                                        </label>
                                        <input type="text" name="preimpcom" id="preimpcom" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_PREIMPORT_CMD"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="insertmarkerstring">
                                            <?= $ml->tr('INSERTMARKERSTRING') ?>
                                        </label>
                                        <input type="text" name="insertmarkerstring" id="insertmarkerstring"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LABEL_CART"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="insertvtstring">
                                            <?= $ml->tr('INSETRVTSTRING') ?>
                                        </label>
                                        <input type="text" name="insertvtstring" id="insertvtstring"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_TRACK_STRING"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="imptemplate">
                                            <?= $ml->tr('IMPORTTEMPLATE') ?>
                                        </label>
                                        <select id="imptemplate" name="imptemplate" class="form-select">
                                        <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") { ?>
                                            <?php foreach ($templates as $temp) { ?>
                                                <option value="<?php echo $temp['NAME']; ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") == $temp['NAME']) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $temp['NAME']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="cust">
                                                <?= $ml->tr('CUSTBRACKIMP') ?>
                                            </option>
                                            <?php } else { ?>
                                                <?php foreach ($templates as $temp) { ?>
                                                <option value="<?php echo $temp['NAME']; ?>">
                                                    <?php echo $temp['NAME']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="cust" SELECTED>
                                                <?= $ml->tr('CUSTBRACKIMP') ?>
                                            </option>

                                                <?php } ?>                                          
                                        

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                    <a href="javascript:;" onclick="copyCustom('<?php echo $id; ?>', 1)" class="btn btn-light-danger"><?= $ml->tr('COPYTOCUSTOM'); ?></a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfccartof">
                                            <?= $ml->tr('CARTOFFSET') ?>
                                        </label>
                                        <input type="text" name="tfccartof" id="tfccartof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_CART_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfccartlength">
                                            <?= $ml->tr('CARTLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="tfccartlength" id="tfccartlength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_CART_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfctitof">
                                            <?= $ml->tr('TITLEOFFSET') ?>
                                        </label>
                                        <input type="text" name="tfctitof" id="tfctitof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_TITLE_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfctitlength">
                                            <?= $ml->tr('TITLELENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="tfctitlength" id="tfctitlength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_TITLE_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfchourof">
                                            <?= $ml->tr('STARTHOUROFFSET') ?>
                                        </label>
                                        <input type="text" name="tfchourof" id="tfchourof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_HOURS_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfchourslength">
                                            <?= $ml->tr('STARTHOURLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="tfchourslength" id="tfchourslength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_HOURS_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcminof">
                                            <?= $ml->tr('STARTMINUTESOFFSET') ?>
                                        </label>
                                        <input type="text" name="tfcminof" id="tfcminof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_MINUTES_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcminlength">
                                            <?= $ml->tr('STARTMINUTESLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="tfcminlength" id="tfcminlength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_MINUTES_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcsecof">
                                            <?= $ml->tr('STARTSECONDSOFFSET') ?>
                                        </label>
                                        <input type="text" name="tfcsecof" id="tfcsecof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_SECONDS_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcseclength">
                                            <?= $ml->tr('STARTSECONDSLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="tfcseclength" id="tfcseclength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_SECONDS_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfclenhoof">
                                            <?= $ml->tr('LENGTHHOUROF') ?>
                                        </label>
                                        <input type="text" name="tfclenhoof" id="tfclenhoof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LEN_HOURS_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcleholength">
                                            <?= $ml->tr('LENGTHHOURLENG') ?>
                                        </label>
                                        <input type="text" name="tfcleholength" id="tfcleholength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LEN_HOURS_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfclenminof">
                                            <?= $ml->tr('LENGTHMINOF') ?>
                                        </label>
                                        <input type="text" name="tfclenminof" id="tfclenminof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LEN_MINUTES_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcleminlength">
                                            <?= $ml->tr('LENGTHMINLENG') ?>
                                        </label>
                                        <input type="text" name="tfcleminlength" id="tfcleminlength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LEN_MINUTES_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfclensecof">
                                            <?= $ml->tr('LENGTHSECOF') ?>
                                        </label>
                                        <input type="text" name="tfclensecof" id="tfclensecof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LEN_SECONDS_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcleseclength">
                                            <?= $ml->tr('LENGTHSECLENG') ?>
                                        </label>
                                        <input type="text" name="tfcleseclength" id="tfcleseclength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_LEN_SECONDS_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcdataof">
                                            <?= $ml->tr('GLOBUNIQUEOF') ?>
                                        </label>
                                        <input type="text" name="tfcdataof" id="tfcdataof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_DATA_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcdatalength">
                                            <?= $ml->tr('GLOBUNIQUELENG') ?>
                                        </label>
                                        <input type="text" name="tfcdatalength" id="tfcdatalength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_DATA_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfceventof">
                                            <?= $ml->tr('EVENTIDOF') ?>
                                        </label>
                                        <input type="text" name="tfceventof" id="tfceventof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_EVENT_ID_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfceventlength">
                                            <?= $ml->tr('EVENTIDLENG') ?>
                                        </label>
                                        <input type="text" name="tfceventlength" id="tfceventlength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_EVENT_ID_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcanncof">
                                            <?= $ml->tr('ANNCTOF') ?>
                                        </label>
                                        <input type="text" name="tfcanncof" id="tfcanncof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_ANNC_TYPE_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="tfcannclength">
                                            <?= $ml->tr('ANNCTLENG') ?>
                                        </label>
                                        <input type="text" name="tfcannclength" id="tfcannclength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "TFC_ANNC_TYPE_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "TFC_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="divider">
                                <div class="divider-text">
                                    <?= $ml->tr('MUSICDATAIMPORT') ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="importpath_mus">
                                            <?= $ml->tr('IMPPATH') ?>
                                        </label>
                                        <input type="text" name="importpath_mus" id="importpath_mus"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_PATH"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="preimpcom_mus">
                                            <?= $ml->tr('PREIMPCOMMAND') ?>
                                        </label>
                                        <input type="text" name="preimpcom_mus" id="preimpcom_mus" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_PREIMPORT_CMD"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="insertmarkerstring_mus">
                                            <?= $ml->tr('INSERTMARKERSTRING') ?>
                                        </label>
                                        <input type="text" name="insertmarkerstring_mus" id="insertmarkerstring_mus"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LABEL_CART"); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="insertvtstring_mus">
                                            <?= $ml->tr('INSETRVTSTRING') ?>
                                        </label>
                                        <input type="text" name="insertvtstring_mus" id="insertvtstring_mus"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TRACK_STRING"); ?>">
                                    </div>
                                </div>
                                <div class="form-group my-2">
                                    <label for="instraficbreak_mus" class="form-label">
                                        <?= $ml->tr('INSERTTRAFFICBREAKSTRING') ?>
                                    </label>
                                    <input type="text" name="instraficbreak_mus" id="instraficbreak_mus"
                                        class="form-control"
                                        value="<?php echo $info->getServiceInfo($id, "MUS_BREAK_STRING"); ?>">
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="imptemplate_mus">
                                            <?= $ml->tr('IMPORTTEMPLATE') ?>
                                        </label>
                                        <select id="imptemplate_mus" name="imptemplate_mus" class="form-select">
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") { ?>
                                            <?php foreach ($templates as $temp) { ?>
                                                <option value="<?php echo $temp['NAME']; ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") == $temp['NAME']) {
                                                       echo "SELECTED";
                                                   } ?>>
                                                    <?php echo $temp['NAME']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="cust">
                                                <?= $ml->tr('CUSTBRACKIMP') ?>
                                            </option>
                                            <?php } else { ?>
                                                <?php foreach ($templates as $temp) { ?>
                                                <option value="<?php echo $temp['NAME']; ?>">
                                                    <?php echo $temp['NAME']; ?>
                                                </option>
                                            <?php } ?>
                                            <option value="cust" SELECTED>
                                                <?= $ml->tr('CUSTBRACKIMP') ?>
                                            </option>

                                                <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                    <a href="javascript:;" onclick="copyCustom('<?php echo $id; ?>', 2)" class="btn btn-light-danger"><?= $ml->tr('COPYTOCUSTOM'); ?></a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="muscartof">
                                            <?= $ml->tr('CARTOFFSET') ?>
                                        </label>
                                        <input type="text" name="muscartof" id="muscartof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_CART_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="muscartlength">
                                            <?= $ml->tr('CARTLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="muscartlength" id="muscartlength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_CART_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mustitof">
                                            <?= $ml->tr('TITLEOFFSET') ?>
                                        </label>
                                        <input type="text" name="mustitof" id="mustitof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TITLE_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mustitlength">
                                            <?= $ml->tr('TITLELENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="mustitlength" id="mustitlength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TITLE_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mushourof">
                                            <?= $ml->tr('STARTHOUROFFSET') ?>
                                        </label>
                                        <input type="text" name="mushourof" id="mushourof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_HOURS_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mushourslength">
                                            <?= $ml->tr('STARTHOURLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="mushourslength" id="mushourslength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_HOURS_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musminof">
                                            <?= $ml->tr('STARTMINUTESOFFSET') ?>
                                        </label>
                                        <input type="text" name="musminof" id="musminof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_MINUTES_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musminlength">
                                            <?= $ml->tr('STARTMINUTESLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="musminlength" id="musminlength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_MINUTES_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mussecof">
                                            <?= $ml->tr('STARTSECONDSOFFSET') ?>
                                        </label>
                                        <input type="text" name="mussecof" id="mussecof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_SECONDS_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musseclength">
                                            <?= $ml->tr('STARTSECONDSLENGTHIMP') ?>
                                        </label>
                                        <input type="text" name="musseclength" id="musseclength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_SECONDS_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="muslenhoof">
                                            <?= $ml->tr('LENGTHHOUROF') ?>
                                        </label>
                                        <input type="text" name="muslenhoof" id="muslenhoof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LEN_HOURS_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musleholength">
                                            <?= $ml->tr('LENGTHHOURLENG') ?>
                                        </label>
                                        <input type="text" name="musleholength" id="musleholength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LEN_HOURS_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="muslenminof">
                                            <?= $ml->tr('LENGTHMINOF') ?>
                                        </label>
                                        <input type="text" name="muslenminof" id="muslenminof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LEN_MINUTES_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musleminlength">
                                            <?= $ml->tr('LENGTHMINLENG') ?>
                                        </label>
                                        <input type="text" name="musleminlength" id="musleminlength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LEN_MINUTES_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="muslensecof">
                                            <?= $ml->tr('LENGTHSECOF') ?>
                                        </label>
                                        <input type="text" name="muslensecof" id="muslensecof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LEN_SECONDS_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musleseclength">
                                            <?= $ml->tr('LENGTHSECLENG') ?>
                                        </label>
                                        <input type="text" name="musleseclength" id="musleseclength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_LEN_SECONDS_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musdataof">
                                            <?= $ml->tr('GLOBUNIQUEOF') ?>
                                        </label>
                                        <input type="text" name="musdataof" id="musdataof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_DATA_OFFSET"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musdatalength">
                                            <?= $ml->tr('GLOBUNIQUELENG') ?>
                                        </label>
                                        <input type="text" name="musdatalength" id="musdatalength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_DATA_LENGTH"); ?>" <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                    echo "DISABLED";
                                                } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="museventof">
                                            <?= $ml->tr('EVENTIDOF') ?>
                                        </label>
                                        <input type="text" name="museventof" id="museventof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_EVENT_ID_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="museventlength">
                                            <?= $ml->tr('EVENTIDLENG') ?>
                                        </label>
                                        <input type="text" name="museventlength" id="museventlength"
                                            class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_EVENT_ID_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musanncof">
                                            <?= $ml->tr('ANNCTOF') ?>
                                        </label>
                                        <input type="text" name="musanncof" id="musanncof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_ANNC_TYPE_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="musannclength">
                                            <?= $ml->tr('ANNCTLENG') ?>
                                        </label>
                                        <input type="text" name="musannclength" id="musannclength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_ANNC_TYPE_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "MUS_IMPORT_TEMPLATE") != "") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mustransof">
                                            <?= $ml->tr('TRANTYPEOFFSET') ?>
                                        </label>
                                        <input type="text" name="mustransof" id="mustransof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TRANS_TYPE_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "BYPASS_MODE") != "Y") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mustranslength">
                                            <?= $ml->tr('TRANTYPELENGTH') ?>
                                        </label>
                                        <input type="text" name="mustranslength" id="mustranslength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TRANS_TYPE_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "BYPASS_MODE") != "Y") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mustimeof">
                                            <?= $ml->tr('TIMETYPEOFFSET') ?>
                                        </label>
                                        <input type="text" name="mustimeof" id="mustimeof" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TIME_TYPE_OFFSET"); ?>"
                                            <?php if ($info->getServiceInfo($id, "BYPASS_MODE") != "Y") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mustimelength">
                                            <?= $ml->tr('TIMETYPELENGTH') ?>
                                        </label>
                                        <input type="text" name="mustimelength" id="mustimelength" class="form-control"
                                            value="<?php echo $info->getServiceInfo($id, "MUS_TIME_TYPE_LENGTH"); ?>"
                                            <?php if ($info->getServiceInfo($id, "BYPASS_MODE") != "Y") {
                                                echo "DISABLED";
                                            } ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group my-2 d-flex justify-content-end">
                                <input type="hidden" name="service" id="service" value="<?php echo $id; ?>">
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
                            <?= $ml->tr('AUTOFILLCARTS') ?>
                        </h5>
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#cart_select" class="btn btn-light-danger"><?= $ml->tr('ADDCART'); ?></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="autofill_table">
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
                                            <?= $ml->tr('ARTIST') ?>
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
                        <table class="table" id="addtofill_table">
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


</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>