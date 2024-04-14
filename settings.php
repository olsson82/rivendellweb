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

if (!$json_sett["admin"][$_COOKIE['username']]["settings"] == 1) {
    header('Location: '.DIR.'/login');
    exit();
}

function timezone_list()
{
    static $timezones = null;

    if ($timezones === null) {
        $timezones = [];
        $offsets = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new DateTimeZone($timezone));
            $offsets[] = $offset = $now->getOffset();
            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
        }

        array_multisort($offsets, $timezones);
    }

    return $timezones;
}

function format_GMT_offset($offset)
{
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function format_timezone_name($name)
{
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
}

$tzlist = timezone_list();


$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$pagecode = "settings";
$page_vars = 'settings';
$page_title = $ml->tr('SETTINGS');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/choices.js/public/assets/styles/choices.css">
<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/table-datatable-jquery.css">';
$plugin_js = '<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<script src="'.DIR.'/assets/extensions/flatpickr/flatpickr.min.js"></script>
<script src="'.DIR.'/assets/extensions/jquery-loading/jquery.loading.min.js"></script>
<script src="'.DIR.'/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/settings.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('SETTINGS'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('SETSETTINGSFOR {{' . SYSTIT . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('SETTINGS'); ?>
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
                            <?= $ml->tr('SYSTEMSETTINGS'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form class="form form-horizontal" id="install_form" method="post">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="sys_name">
                                            <?= $ml->tr('SYSTEMNAME'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="sys_name" class="form-control" name="sys_name"
                                            value="<?php echo $json_sett["sysname"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMNAMEINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="urladd">
                                            <?= $ml->tr('SYSTEMNAME'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="urladd" class="form-control" name="urladd"
                                            value="<?php echo $json_sett["sysurl"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMURLINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="time_zone">
                                            <?= $ml->tr('SYSTEMTIME'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="time_zone" name="time_zone" class="choices form-select">
                                            <?php foreach ($tzlist as $key => $timezone) {
                                                if ($key == $json_sett["timezone"]) {
                                                    echo '<option value="' . $key . '" SELECTED>' . $timezone . '</option>';
                                                } else {
                                                    echo '<option value="' . $key . '">' . $timezone . '</option>';
                                                }
                                            } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMTIMEINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="def_lang">
                                            <?= $ml->tr('SYSTEMDEFAULTLANGUAGE'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="def_lang" name="def_lang" class="choices form-select">
                                            <option value="en_US" <?php if ($json_sett["deflang"] == 'en_US') {
                                                echo 'SELECTED';
                                            } ?>>
                                                <?= $ml->tr('ENGLISH'); ?>
                                            </option>
                                            <option value="sv_SE" <?php if ($json_sett["deflang"] == 'sv_SE') {
                                                echo 'SELECTED';
                                            } ?>>
                                                <?= $ml->tr('SWEDISH'); ?>
                                            </option>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMDEFAULTLANGUAGEINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pass_reset">
                                            <?= $ml->tr('SYSTEMPASSRESET'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="pass_reset" name="pass_reset" class="choices form-select">
                                            <option value="1" <?php if ($json_sett["usereset"] == '1') {
                                                echo 'SELECTED';
                                            } ?>>
                                                <?= $ml->tr('YES'); ?>
                                            </option>
                                            <option value="0" <?php if ($json_sett["usereset"] == '0') {
                                                echo 'SELECTED';
                                            } ?>>
                                                <?= $ml->tr('NO'); ?>
                                            </option>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMPASSRESETINFO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="autotrim">
                                            <?= $ml->tr('AUTOTRIMLEVEL'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="autotrim" class="form-control" name="autotrim"
                                            value="<?php echo $json_sett["autotrim"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('AUTOTRIMLEVELTEXT'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="normalize">
                                            <?= $ml->tr('NORMALIZELEVEL'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="normalize" class="form-control" name="normalize"
                                            value="<?php echo $json_sett["normalize"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('NORMALIZELEVELTEXT'); ?>
                                            </small></p>
                                    </div>
                                    <div class="divider">
                                        <div class="divider-text">
                                            <?= $ml->tr('SMTPSETTINGS'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="smtp_server">
                                            <?= $ml->tr('SYSTEMSMPTSERVER'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="smtp_server" class="form-control" name="smtp_server"
                                            value="<?php echo $json_sett["smtpserv"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMSMPTSERVERINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pass_reset">
                                            <?= $ml->tr('LOGINTOSMTPSERVER'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="smtp_login" name="smtp_login" class="choices form-select">
                                            <option value="1" <?php if ($json_sett["smtplogin"] == '1') {
                                                echo 'SELECTED';
                                            } ?>><?= $ml->tr('YES'); ?>
                                            </option>
                                            <option value="0" <?php if ($json_sett["smtplogin"] == '0') {
                                                echo 'SELECTED';
                                            } ?>><?= $ml->tr('NO'); ?>
                                            </option>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('LOGINTOSMTPSERVERNEEDTO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pass_reset">
                                            <?= $ml->tr('SMTPENCRYPTION'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="smtp_enc" name="smtp_enc" class="choices form-select">
                                            <option value="1" <?php if ($json_sett["smtpenc"] == '1') {
                                                echo 'SELECTED';
                                            } ?>><?= $ml->tr('SMTPENCRYPTIONTLS'); ?>
                                            </option>
                                            <option value="0" <?php if ($json_sett["smtpenc"] == '0') {
                                                echo 'SELECTED';
                                            } ?>><?= $ml->tr('SMTPENCRYPTIONSTARTTLS'); ?>
                                            </option>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SMTPENCRYPTIONNEEDTOUSE'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="smtp_port">
                                            <?= $ml->tr('SYSTEMSMPTPORT'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="smtp_port" class="form-control" name="smtp_port"
                                            value="<?php echo $json_sett["port"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMSMPTPORTINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="smtp_usr">
                                            <?= $ml->tr('SYSTEMSMPTUSERNAME'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="smtp_usr" class="form-control" name="smtp_usr"
                                            value="<?php echo $json_sett["smtpusr"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMSMPTUSERNAMEINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="smtp_pass">
                                            <?= $ml->tr('SYSTEMSMPTPASSWORD'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="password" id="smtp_pass" class="form-control" name="smtp_pass"
                                            value="<?php echo $json_sett["smtppass"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMSMPTPASSWORDINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="smtp_from">
                                            <?= $ml->tr('SYSTEMSMPTFROM'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="email" id="smtp_from" class="form-control" name="smtp_from"
                                            value="<?php echo $json_sett["smtpfrom"]; ?>">
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMSMPTFROMINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            <?= $ml->tr('SAVE'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>