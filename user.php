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

if (!$json_sett["admin"][$_COOKIE['username']]["users"] == 1) {
    header('Location: '.DIR.'/login');
    exit();
}


$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
$id = $_GET['id'];
$userData = $dbfunc->getRivUser($id);
$pagecode = "users";
$page_vars = 'user';
$page_title = $ml->tr('EDITUSER');
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
$page_js = '<script src="'.DIR.'/assets/static/js/user.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <?= $ml->tr('EDITUSER'); ?>
                </h3>
                <p class="text-subtitle text-muted">
                    <?= $ml->tr('EDITUSERUSER {{' . $id . '}}'); ?>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/dash">
                                <?= $ml->tr('DASHBOARD'); ?>
                            </a></li>
                        <li class="breadcrumb-item"><a href="<?php echo DIR; ?>/admin/users">
                                <?= $ml->tr('USERS'); ?>
                            </a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $ml->tr('EDITUSER'); ?>
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
                            <?= $ml->tr('USERSETTINGS'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form class="form form-horizontal" id="user_form" method="post">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="fullname">
                                            <?= $ml->tr('FULLNAME'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="fullname" class="form-control" name="fullname"
                                            value="<?php echo $userData["FULL_NAME"]; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email">
                                            <?= $ml->tr('EMAIL'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="email" id="email" class="form-control" name="email"
                                            value="<?php echo $userData["EMAIL_ADDRESS"]; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="phone">
                                            <?= $ml->tr('PHONE'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="phone" class="form-control" name="phone"
                                            value="<?php echo $userData["PHONE_NUMBER"]; ?>">
                                    </div>
                                    <div class="divider">
                                        <div class="divider-text">
                                            <?= $ml->tr('ADMINRIGHTS'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="adminrights">
                                            <?= $ml->tr('GIVEADMINRIGHTS'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="adminrights" name="adminrights" class="choices form-select">
                                            <?php $isadmin = 0;
                                            if (isset($json_sett["admin"][$id])) {
                                                $isadmin = 1;
                                            } ?>
                                            <option value="1" <?php if ($isadmin == 1) {
                                                echo 'SELECTED';
                                            } ?>>
                                                <?= $ml->tr('YES'); ?>
                                            </option>
                                            <option value="0" <?php if ($isadmin == 0) {
                                                echo 'SELECTED';
                                            } ?>>
                                                <?= $ml->tr('NO'); ?>
                                            </option>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('GIVEADMINRIGHTSINFO'); ?>
                                            </small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="manageuser">
                                            <?= $ml->tr('MANAGEUSERS'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="manageuser" name="manageuser" class="choices form-select">
                                            <?php if ($isadmin == 1) { ?>
                                                <option value="1" <?php if ($json_sett["admin"][$id]["users"] == '1') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" <?php if ($json_sett["admin"][$id]["users"] == '0') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="1">
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" SELECTED>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('MANAGEUSERSINFO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="messages">
                                            <?= $ml->tr('SETMESSAGES'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="messages" name="messages" class="choices form-select">
                                            <?php if ($isadmin == 1) { ?>
                                                <option value="1" <?php if ($json_sett["admin"][$id]["message"] == '1') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" <?php if ($json_sett["admin"][$id]["message"] == '0') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="1">
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" SELECTED>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SETMESSAGESINFO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="messages">
                                            <?= $ml->tr('MANAGEHOSTS'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="hosts" name="hosts" class="choices form-select">
                                            <?php if ($isadmin == 1) { ?>
                                                <option value="1" <?php if ($json_sett["admin"][$id]["hosts"] == '1') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" <?php if ($json_sett["admin"][$id]["hosts"] == '0') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="1">
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" SELECTED>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('ALLOWUSRMANAGEHOST'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="modifygroups">
                                            <?= $ml->tr('MODIFYGROUPS'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="modifygroups" name="modifygroups" class="choices form-select">
                                            <?php if ($isadmin == 1) { ?>
                                                <option value="1" <?php if ($json_sett["admin"][$id]["groups"] == '1') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" <?php if ($json_sett["admin"][$id]["groups"] == '0') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="1">
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" SELECTED>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('MODIFYGROUPSINFO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="modifysched">
                                            <?= $ml->tr('MODIFYSCHEDCODES'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="modifysched" name="modifysched" class="choices form-select">
                                            <?php if ($isadmin == 1) { ?>
                                                <option value="1" <?php if ($json_sett["admin"][$id]["sched"] == '1') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" <?php if ($json_sett["admin"][$id]["sched"] == '0') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="1">
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" SELECTED>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('MODIFYSCHEDCODESINFO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="systemdata">
                                            <?= $ml->tr('SYSTEMDATA'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="systemdata" name="systemdata" class="choices form-select">
                                            <?php if ($isadmin == 1) { ?>
                                                <option value="1" <?php if ($json_sett["admin"][$id]["settings"] == '1') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" <?php if ($json_sett["admin"][$id]["settings"] == '0') {
                                                    echo 'SELECTED';
                                                } ?>>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="1">
                                                    <?= $ml->tr('YES'); ?>
                                                </option>
                                                <option value="0" SELECTED>
                                                    <?= $ml->tr('NO'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <p><small class="text-muted">
                                                <?= $ml->tr('SYSTEMDATAINFO'); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <input type="hidden" name="username" value="<?php echo $id; ?>">
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