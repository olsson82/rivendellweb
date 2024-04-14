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


$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];
if (!isset($json_sett["admin"][$_COOKIE['username']]["username"])) {
    header('Location: '.DIR.'/login');
    exit();
}
$pagecode = "admindash";
$critical = FALSE;
$update = FALSE;
$url = "https://raw.githubusercontent.com/olsson82/rivendellweb/main/version.csv";
$fp = @fopen($url, 'r') or print ('UPDATE SERVER OFFLINE');
$read = fgetcsv($fp);
fclose($fp);
if ($read[0] > VERS && $read[2] == "1") {
    $critical = TRUE;
}
if ($read[0] > VERS) {
    $update = TRUE;
}

$page_vars = 'admindash';
$page_title = $ml->tr('ADMINDASH');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/iconly.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.css">';
$plugin_js = '<script src="'.DIR.'/assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="'.DIR.'/assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="'.DIR.'/assets/extensions/sweetalert2/sweetalert2.min.js"></script>';
$page_js = '<script src="'.DIR.'/assets/static/js/admindash.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

            <div class="page-heading">
                <h3><?= $ml->tr('ADMINDASHFOR {{' . SYSTIT . '}}'); ?></h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><?= $ml->tr('WELCOMEADMIN {{' . $fullname . '}}'); ?></h4>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($dbversold == 1) { ?>
                                    <div class="alert alert-danger">
                            <h4 class="alert-heading"><?= $ml->tr('WRONGRIVDBVERSION'); ?>
                                        </h4>
                                        <p>
                                            <?= $ml->tr('USEWRONGDB1 {{' . SYSTIT . '}}'); ?>
                                        </p>
                                        <p>
                                            <?= $ml->tr('USEWRONGDB2'); ?>
                                        </p>
                                        <p>
                                            <?= $ml->tr('USEWRONGDB3 {{' . $info->checkDBVers() . '}} {{' . DBOK . '}}'); ?>
                                        </p>
                                    </div><?php } ?>
                                        <P><?= $ml->tr('WELCOMEINFO {{' . SYSTIT . '}}'); ?></P>
                                        <P><?= $ml->tr('WELCOMEINFO1'); ?></P>
                                        <P><?= $ml->tr('WELCOMEINFO2'); ?></P>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><?= $ml->tr('UPDATES'); ?></h4>
                                    </div>
                                    <div class="card-body">
                                 <?php   if ($critical) { ?>
                                    <H4><?= $ml->tr('CRITICALUP'); ?></H4>
                                    <P><?= $ml->tr('VERSNEW {{' . $read[0] . '}}'); ?></P>
                                    <P><?= $ml->tr('OLDVERS {{' . VERS . '}}'); ?></P>
                                    <P><?= $ml->tr('VERSIONNAME {{' . $read[1] . '}}'); ?></P>
                                    <P><?= $ml->tr('RIVDBVERSINFUP {{' . $read[3] . '}}'); ?></P>
                                    <a href="<?php echo $read[3]; ?>" target="_blank" class="btn btn-danger rounded-pill"><?= $ml->tr('GETITHERE'); ?></a>
                                        <?php } else if ($update){ ?>
                                    <H4><?= $ml->tr('NONCRITICALUP'); ?></H4>
                                    <P><?= $ml->tr('VERSNEW {{' . $read[0] . '}}'); ?></P>
                                    <P><?= $ml->tr('OLDVERS {{' . VERS . '}}'); ?></P>
                                    <P><?= $ml->tr('VERSIONNAME {{' . $read[1] . '}}'); ?></P>
                                    <P><?= $ml->tr('RIVDBVERSINFUP {{' . $read[3] . '}}'); ?></P>
                                    <a href="<?php echo $read[4]; ?>" target="_blank" class="btn btn-success rounded-pill"><?= $ml->tr('GETITHERE'); ?></a>
                                    <?php } else { ?>
                                        <H4><?= $ml->tr('NOUPDATES'); ?></H4>
                                    <?php } ?>                                        
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <?php if ($json_sett["admin"][$_COOKIE['username']]["message"] == 1) { ?> 
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><?= $ml->tr('SETUSERMESSAGE'); ?>
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="usermess_form" class="form form-vertical">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="form-group mb-3">
                                                        <label for="usrmess" class="form-label"><?= $ml->tr('MESSAGEONDASH'); ?>
                                                        </label>
                                                        <textarea class="form-control" name="usrmess" id="usrmess" rows="3"><?php echo $json_sett["newsmess"]; ?></textarea>
                                                    </div>
                                                    <div class="col-12 d-flex justify-content-end">
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
                        </div>   <?php } ?>                     
                    </div>                    
                </section>
            </div>

            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>