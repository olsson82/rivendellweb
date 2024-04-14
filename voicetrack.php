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

$activeService = $_COOKIE['serviceName'];
$groupInfo = $dbfunc->getGroupInformation();
$vtInfo = $dbfunc->getVoicetrackInformation($activeService);
$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$logName = $_GET['log'];

$vtLower = $vtInfo[0]['default_low_cart'];
$vtUpper = $vtInfo[0]['default_high_cart'];
$vtGroup = $vtInfo[0]['group'];
$pagecode = "logs";
$page_vars = 'voicetrack';
$page_title = $ml->tr('VOICETRACKER');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="'.DIR.'/assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/record.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/voicetrack.js"></script>';

?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('VOICETRACKER'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('RECVOICEFORLOGHERE'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/logs">
                                <?= $ml->tr('LOGS'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('VOICETRACKER'); ?>
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
                            <?= $ml->tr('VOICETRACKS'); ?>
                        </h5>
                        <h6 class="card-subtitle">
                            <?= $ml->tr('FORVOICELOG {{' . $logName . '}}'); ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="hourTab" role="tablist">
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                ?>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link <?php if ($i == 0) {
                                        echo "active";
                                    } ?>" id="home-tab_<?php echo $i; ?>" data-bs-toggle="tab"
                                        href="#hour_<?php echo $i; ?>" role="tab" aria-controls="hour_<?php echo $i; ?>"
                                        aria-selected="<?php if ($i == 0) { ?>true<?php } ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php }
                            ?>
                        </ul>
                        <div class="tab-content" id="hourTabContent">
                            <?php
                            $hrtime = 0;
                            for ($i = 0; $i < 24; $i++) {
                                $hrtime = $i;
                                if ($hrtime == 0) {
                                    $logs = $dbfunc->getRivendellLog($logName, "00");
                                } else {
                                    $logs = $dbfunc->getRivendellLog($logName, $hrtime);
                                } ?>
                                <div class="tab-pane fade <?php if ($hrtime == 0) {
                                    echo "show active";
                                } ?>" id="hour_<?php echo $i; ?>" role="tabpanel"
                                    aria-labelledby="home-tab_<?php echo $i; ?>">

                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        <?= $ml->tr('STARTTIME') ?>
                                                    </th>
                                                    <th scope="col">
                                                        <?= $ml->tr('CARTNUMBER') ?>
                                                    </th>
                                                    <th scope="col">
                                                        <?= $ml->tr('GROUP') ?>
                                                    </th>
                                                    <th scope="col">
                                                        <?= $ml->tr('ARTIST') ?>
                                                    </th>
                                                    <th scope="col">
                                                        <?= $ml->tr('TITLE') ?>
                                                    </th>
                                                    <th scope="col">
                                                        <?= $ml->tr('LENGTH') ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($logs as $log) {

                                                    $type = $log['type'];
                                                    $cart = $log['cart'];
                                                    $group = $log['group'];
                                                    $artist = $log['artist'];
                                                    $title = $log['title'];
                                                    $comment = $log['comment'];
                                                    $count = $log['count'];
                                                    $lineid = $log['line_id'];
                                                    $label = $log['label'];
                                                    $color = $log['color'];
                                                    $length = $functions->msToHHMMSS($log['length']);
                                                    $startTime = $functions->msToHHMMSS_fromMID($log['start_time']);
                                                    if (($vtLower <= $cart) && ($cart <= $vtUpper)) {
                                                        $group = $vtGroup;
                                                    }
                                                    if ($type == 6) {
                                                        ?>
                                                        <tr id="vtrow_<?php echo $lineid; ?>">
                                                            <td>
                                                                <?php echo $startTime; ?>
                                                            </td>
                                                            <td id="cart_<?php echo $lineid; ?>">
                                                                <?= $ml->tr('NEW') ?>
                                                            </td>
                                                            <td id="buttons_<?php echo $lineid; ?>">
                                                                <div class="btn-group mb-3" role="group"
                                                                    aria-label="<?= $ml->tr('VOICETRACKER') ?>">
                                                                    <button type="button"
                                                                        onclick="recordvoice(<?php echo $lineid; ?>,'<?php echo $vtGroup; ?>','<?php echo $cart; ?>', '<?php echo $logName; ?>', '<?php echo $username; ?>')"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="<?= $ml->tr('RECORD') ?>" class="btn btn-danger"><i
                                                                            class="bi bi-mic"></i></button>
                                                                    <button type="button"
                                                                        onclick="uploadvoice(<?php echo $lineid; ?>,'<?php echo $vtGroup; ?>','<?php echo $cart; ?>', '<?php echo $logName; ?>', '<?php echo $username; ?>')"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="<?= $ml->tr('UPLOAD') ?>" class="btn btn-warning"><i
                                                                            class="bi bi-cloud-upload"></i></button>
                                                                </div>
                                                            </td>
                                                            <td id="artist_<?php echo $lineid; ?>"></td>
                                                            <td id="title_<?php echo $lineid; ?>"></td>
                                                            <td id="length_<?php echo $lineid; ?>"></td>
                                                        </tr>
                                                    <?php } else if ($type == 2) {
                                                        //Skip Macro Events
                                                    } else if ($type == 1) {
                                                        //Labels       ?>
                                                                <tr id="vtrow_<?php echo $lineid; ?>">
                                                                    <td>
                                                                <?php echo $startTime; ?>
                                                                    </td>
                                                                    <td>
                                                                <?= $ml->tr('MARKER') ?>
                                                                    </td>
                                                                    <td></td>
                                                                    <td>
                                                                <?php echo $comment; ?>
                                                                    </td>
                                                                    <td>
                                                                <?php echo $label; ?>
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                    <?php } else if ($group == $vtGroup) { ?>
                                                                    <tr id="vtrow_<?php echo $lineid; ?>">
                                                                        <td>
                                                                <?php echo $startTime; ?>
                                                                        </td>
                                                                        <td id="cart_<?php echo $lineid; ?>">
                                                                <?php echo $cart; ?>
                                                                        </td>
                                                                        <td id="buttons_<?php echo $lineid; ?>">
                                                                            <div class="btn-group mb-3" role="group"
                                                                                aria-label="<?= $ml->tr('VOICETRACKER') ?>">
                                                                                <button type="button"
                                                                                    onclick="recordvoice(<?php echo $lineid; ?>,'<?php echo $vtGroup; ?>','<?php echo $cart; ?>', '<?php echo $logName; ?>', '<?php echo $username; ?>')"
                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                    title="<?= $ml->tr('RECORD') ?>" class="btn btn-danger"><i
                                                                                        class="bi bi-mic"></i></button>
                                                                                <button type="button"
                                                                                    onclick="uploadvoice(<?php echo $lineid; ?>,'<?php echo $vtGroup; ?>','<?php echo $cart; ?>', '<?php echo $logName; ?>', '<?php echo $username; ?>')"
                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                    title="<?= $ml->tr('UPLOAD') ?>" class="btn btn-warning"><i
                                                                                        class="bi bi-cloud-upload"></i></button>
                                                                            </div>
                                                                        </td>
                                                                        <td id="artist_<?php echo $lineid; ?>">
                                                                <?php echo $artist; ?>
                                                                        </td>
                                                                        <td id="title_<?php echo $lineid; ?>">
                                                                <?php echo $title; ?>
                                                                        </td>
                                                                        <td id="length_<?php echo $lineid; ?>">
                                                                <?php echo $length; ?>
                                                                        </td>
                                                                    </tr>
                                                    <?php } else if ($cart != 0) { ?>
                                                                        <tr id="vtrow_<?php echo $lineid; ?>">
                                                                            <td>
                                                                <?php echo $startTime; ?>
                                                                            </td>
                                                                            <td>
                                                                <?php echo $cart; ?>
                                                                            </td>
                                                                            <td>
                                                                                <p style="color:<?php echo $color; ?>">
                                                                    <?php echo $group; ?>
                                                                                </p>
                                                                            </td>
                                                                            <td>
                                                                <?php echo $artist; ?>
                                                                            </td>
                                                                            <td>
                                                                <?php echo $title; ?>
                                                                            </td>
                                                                            <td>
                                                                <?php echo $length; ?>
                                                                            </td>
                                                                        </tr>
                                                        <?php
                                                    } else {
                                                    }

                                                }
                                                ?>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            <?php } ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="upload_voice" data-bs-backdrop="static" role="dialog"
        aria-labelledby="uploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header  bg-success">
                    <h4 class="modal-title white" id="uploadLabel">
                        <?= $ml->tr('UPLOAD') ?>
                    </h4>
                    <button type="button" class="close" data-kt-upload-modal-action="cancel" aria-label="Close">
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
                        <button type="button" class="btn btn-light-secondary" data-kt-upload-modal-action="close">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">
                                <?= $ml->tr('CLOSE') ?>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        <button class="btn btn-danger" id="record">
                            <?= $ml->tr('RECORD') ?>
                        </button>
                        <button id="pause" class="btn btn-warning" style="display: none;">
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

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>