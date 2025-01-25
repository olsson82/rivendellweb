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
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/settings.json')) {
    header('Location: ' . DIR . '/login');
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

$ini_file = "/etc/rd.conf";
if (!is_readable($ini_file)) {
    $noini = 1;
} else {
    $noini = 0;
}

$tzlist = timezone_list();
session_start();
$install = $_GET['install'];
$errors = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Rivendell Web Broadcast</title>
    <link rel="shortcut icon" href="AppImages/favicon.ico" />
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="assets/extensions/choices.js/public/assets/styles/choices.css">
    <link rel="stylesheet" href="assets/extensions/sweetalert2/sweetalert2.min.css">
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <nav class="navbar navbar-light">
        <div class="container d-block">
            <a class="navbar-brand ms-4" href="javascript:;">
                <img src="assets/static/images/rivlogo/rdairplay-128x128.png">
            </a>
        </div>
    </nav>

    <?php if ($install == 2) {

        ?>
        <div class="container">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="card-title">Rivendell Web Broadcast Installer - Installation</h4>
                </div>
                <div class="card-body">
                    <P>Now you are going to setup the system, all fields needs to be setup.</P>
                    <P>You need an email address with smtp settings for the email functions to work with the system.</P>
                    <P>Also to have access to the admin function in the system, you need to enter your regular username in
                        Rivendell Radio Automation. NOT the admin username. Only enter one now, you can add more later.</P>
                    <form class="form form-horizontal" id="install_form">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="sys_name">System Name</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="sys_name" class="form-control" name="sys_name"
                                        placeholder="Rivendell Web Broadcast">
                                    <p><small class="text-muted">The name of your radio station for example.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="urladd">System URL</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="urladd" class="form-control" name="urladd"
                                        placeholder="http://localhost">
                                    <p><small class="text-muted">For testing, you can use localhost, on live use an reverse
                                            proxy with https support.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="admin_usr">Admin user</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="admin_usr" class="form-control" name="admin_usr"
                                        placeholder="A normal user">
                                    <p><small class="text-muted">Enter the username of one to get access to admin rights in
                                            system. You can add more later.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="time_zone">Timezone</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="time_zone" name="time_zone" class="choices form-select">
                                        <?php foreach ($tzlist as $key => $timezone) {
                                            echo '<option value="' . $key . '">' . $timezone . '</option>';
                                        } ?>
                                    </select>
                                    <p><small class="text-muted">The timezone you will use in the system.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="def_lang">Default Language</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="def_lang" name="def_lang" class="choices form-select">
                                        <option value="en_US">English</option>
                                        <option value="sv_SE">Swedish</option>
                                    </select>
                                    <p><small class="text-muted">The language that will be used by default.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="back_type">
                                        Backup Type
                                    </label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="back_type" name="back_type" class="choices form-select">
                                        <option value="0">
                                            Only Rivendell Database
                                        </option>
                                        <option value="1">
                                            Only Rivendell Web Broadcast
                                        </option>
                                        <option value="2" SELECTED>
                                            Everything
                                        </option>
                                    </select>
                                    <p><small class="text-muted">
                                            When you run backup with cron job, what type will be used.
                                        </small></p>
                                </div>
                                <div class="col-md-4">
                                        <label for="back_older">
                                        Remove backup older than
                                        </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select id="back_older" name="back_older" class="choices form-select">
                                        <option value="0" SELECTED>
                                        One week
                                            </option>
                                            <option value="1">
                                            One month
                                            </option>
                                        </select>
                                        <p><small class="text-muted">
                                        Will remove backups that pass this creation time.
                                            </small></p>
                                    </div>
                                <div class="col-md-4">
                                    <label for="pass_reset">Use Password Reset</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="pass_reset" name="pass_reset" class="choices form-select">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <p><small class="text-muted">Allow users to reset password if they forgot it.</small>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label for="autotrim">Autotrim Level</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="autotrim" class="form-control" name="autotrim" placeholder="-35">
                                    <p><small class="text-muted">The default autotrim level to use.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="normalize">Normalize Level</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="normalize" class="form-control" name="normalize"
                                        placeholder="-13">
                                    <p><small class="text-muted">The default normalize level to use.</small></p>
                                </div>
                                <div class="divider">
                                    <div class="divider-text">SMTP Settings</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="smtp_server">SMTP Server</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="smtp_server" class="form-control" name="smtp_server"
                                        placeholder="smtp.server.com">
                                    <p><small class="text-muted">Your SMTP server for sending mails.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="pass_reset">Login to SMTP Server</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="smtp_login" name="smtp_login" class="choices form-select">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    <p><small class="text-muted">Do you need to login to your smtp server?</small>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label for="pass_reset">SMTP Encryption</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <select id="smtp_enc" name="smtp_enc" class="choices form-select">
                                        <option value="1">TLS</option>
                                        <option value="0">STARTTLS</option>
                                    </select>
                                    <p><small class="text-muted">What type of login encryption do you need ?</small>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label for="smtp_port">SMTP Port</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="smtp_port" class="form-control" name="smtp_port"
                                        placeholder="587">
                                    <p><small class="text-muted">Your SMTP port. Ex: 587</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="smtp_usr">SMTP Username</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="smtp_usr" class="form-control" name="smtp_usr"
                                        placeholder="user@name.com">
                                    <p><small class="text-muted">Your username to login to smtp server.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="smtp_pass">SMTP Password</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="password" id="smtp_pass" class="form-control" name="smtp_pass"
                                        placeholder="Your password">
                                    <p><small class="text-muted">Password to SMTP server.</small></p>
                                </div>
                                <div class="col-md-4">
                                    <label for="smtp_from">From email</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    <input type="text" id="smtp_from" class="form-control" name="smtp_from"
                                        placeholder="your@email.com">
                                    <p><small class="text-muted">Email to use with sending.</small></p>
                                </div>
                                <div class="col-sm-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Install</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } else if ($install == 1) { ?>
            <div class="container">
                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="card-title">Rivendell Web Broadcast Installer - Pre Check</h4>
                    </div>
                    <div class="card-body">
                        <P>We are doing som checks on the system. If everything seems good, you can start install.</P>
                        <P>The data folder need to have the rights to write and read. Also check in the documentation that you
                            have
                            installed everything that is required for the system to work. If there are an error, please fix it
                            on your server and reload this page.</P>
                        <form class="form form-horizontal" id="install_check">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="phpvers">PHP Version</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                    <?php $php_version = phpversion();
                                    if ($php_version < 7) {
                                        $errors = 1; ?>
                                            <a href="javascript:;" class="btn icon btn-danger"><i class="bi bi-x"></i></a>
                                    <?php } else { ?>
                                            <a href="javascript:;" class="btn icon btn-success"><i class="bi bi-check"></i></a>
                                    <?php } ?>
                                        <p><small class="text-muted">Must be version 7 or better.</small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="writeable">Writeable</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <?php
                                        $is_writable = file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/dummy.txt', "hello");
                                        $php_version = phpversion();
                                        if ($is_writable > 0) {
                                            unlink($_SERVER['DOCUMENT_ROOT'] . '/data/dummy.txt'); ?>
                                            <a href="javascript:;" class="btn icon btn-success"><i class="bi bi-check"></i></a>
                                    <?php } else {
                                            $errors = 1; ?>
                                            <a href="javascript:;" class="btn icon btn-danger"><i class="bi bi-x"></i></a>
                                    <?php } ?>
                                        <p><small class="text-muted">The data folder needs to be writeable.</small></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="session">Cookies and Session</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <?php
                                        $_SESSION['rivwebbroad_sessions_work'] = 1;
                                        if (empty($_SESSION['rivwebbroad_sessions_work']) || empty($_COOKIE['installtest'])) {
                                            $errors = 1;
                                            ?>
                                            <a href="javascript:;" class="btn icon btn-danger"><i class="bi bi-x"></i></a>
                                    <?php } else { ?>
                                            <a href="javascript:;" class="btn icon btn-success"><i class="bi bi-check"></i></a>
                                    <?php } ?>
                                        <p><small class="text-muted">Cookies and Session needs to work on your server.</small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="rivendell">Rivendell</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <?php
                                        if ($noini == 1) {
                                            $errors = 1;

                                            ?>
                                            <a href="javascript:;" class="btn icon btn-danger"><i class="bi bi-x"></i></a>
                                    <?php } else { ?>
                                            <a href="javascript:;" class="btn icon btn-success"><i class="bi bi-check"></i></a>
                                    <?php } ?>
                                        <p><small class="text-muted">Rivendell Radio Automation needs to be installed.</small>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="rewrite">Apache Mod Rewrite</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <?php
                                        if (function_exists('apache_get_modules')) {
                                            $modules = apache_get_modules();
                                            if (in_array('mod_rewrite', $modules)) { ?>
                                                <a href="javascript:;" class="btn icon btn-success"><i class="bi bi-check"></i></a>
                                        <?php } else {
                                                $errors = 1; ?>
                                                <a href="javascript:;" class="btn icon btn-danger"><i class="bi bi-x"></i></a>
                                        <?php }
                                        } else {
                                            $errors = 1; ?>
                                            <a href="javascript:;" class="btn icon btn-danger"><i class="bi bi-x"></i></a>
                                    <?php } ?>
                                        <p><small class="text-muted">Apache needs to have mod_rewrite enable.</small></p>
                                    </div>


                                    <div class="col-sm-12 d-flex justify-content-end">
                                    <?php if ($errors == 0) { ?>
                                            <a href="installer.php?install=2" class="btn btn-success">Next ></a>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    <?php } else { ?>
        <?php $expire = time() + (3600 * 1);
        setcookie('installtest', 'true', $expire, '/'); ?>
            <div class="container">
                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="card-title">Rivendell Web Broadcast Installer</h4>
                    </div>
                    <div class="card-body">
                        <P>Welcome to the Rivendell Web Broadcast Installer. This guide will help you install Rivendell Web
                            Broadcast on your Rivendell Machine.</P>
                        <P>You can only install this on a computer or server that have Rivendell Radio Automation installed. If
                            it not installed, please install Rivendell Radio Automation first.</P>
                        <P>The installation is in two steps, first it will check your system, so it works with Rivendell Web
                            Broadcast, then you are going to enter some information for the system.</P>
                        <P><B>Please be adviced that this system is NOT developed by the people that develop Rivendell Radio
                                Automation. You should read the documentation before you start. Also you use it as your own
                                risk. Always test if first before use it on a production machine.</B></P>
                        <P>When you are ready to install, click on next.</P>
                        <P class="col-sm-12 d-flex justify-content-end"><a target="_blank"
                                href="https://olsson82.github.io/rivwebdoc/" class="btn btn-info">Documentation</a> <a
                                href="installer.php?install=1" class="btn btn-success">Next ></a></P>

                    </div>
                </div>
            </div>

    <?php } ?>



    <script src="assets/extensions/jquery/jquery.min.js"></script>
    <script src="assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
    <script src="assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <script src="assets/static/js/installer.js"></script>

</body>

</html>