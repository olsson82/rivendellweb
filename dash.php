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
$pagecode = "dash";
$page_vars = 'dash';
$page_title = $ml->tr('DASHBOARD');
$page_css = '<link rel="stylesheet" href="'.DIR.'/assets/compiled/css/iconly.css">
<link rel="stylesheet" href="'.DIR.'/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css">';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

            <div class="page-heading">
                <h3><?= $ml->tr('WELCOMEADMIN {{' . $fullname . '}}'); ?></h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 ">
                                                <div class="avatar avatar-xl bg-primary me-3">
                                                    <span class="avatar-content"><i
                                                            class="fa fa-music fa-4x"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">
                                                    <?= $ml->tr('INLIBRARY'); ?>
                                                </h6>
                                                <h6 class="font-extrabold mb-0">
                                                    <?php echo $dbfunc->getTotLibrary(); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 ">
                                                <div class="avatar avatar-xl bg-success me-3">
                                                    <span class="avatar-content"><i
                                                            class="fa fa-layer-group fa-4x"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">
                                                    <?= $ml->tr('GROUPS'); ?>
                                                </h6>
                                                <h6 class="font-extrabold mb-0">
                                                    <?php echo $dbfunc->getTotGroups(); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 ">
                                                <div class="avatar avatar-xl bg-info me-3">
                                                    <span class="avatar-content"><i
                                                            class="fa fa-clipboard-list fa-4x"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">
                                                    <?= $ml->tr('SCHEDULERCODES'); ?>
                                                </h6>
                                                <h6 class="font-extrabold mb-0">
                                                    <?php echo $dbfunc->getTotSched(); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-4 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 ">
                                                <div class="avatar avatar-xl bg-danger me-3">
                                                    <span class="avatar-content"><i class="fa fa-list fa-4x"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                <h6 class="text-muted font-semibold">
                                                    <?= $ml->tr('LOGS'); ?>
                                                </h6>
                                                <h6 class="font-extrabold mb-0">
                                                    <?php echo $dbfunc->getTotLogs(); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><?= $ml->tr('VOICETRACKS'); ?></h4>
                                    </div>
                                    <div class="card-body">
                                        <?php $logs = $dbfunc->getVoicetrackWorks($selectedService);
                                        if ($dbfunc->getVoicetrackJobs($selectedService) > 0) { ?>
                                            <div id="voicetracSlides" class="carousel slide" data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php $i = 0;
                                                    foreach ($logs as $log) { 
                                                        $needsrecord = $log['SCHEDULED_TRACKS'] - $log['COMPLETED_TRACKS'];
                                                        ?>
                                                        <div class="carousel-item <?php if ($i == 0) {
                                                            echo "active";
                                                        } ?>">
                                                            <h4><?php echo $log['NAME']; ?></h4>
                                                            <P><?= $ml->trp('SINGVOICETRACK {{'.$needsrecord.'}}', 'PLURVOICETRACK {{'.$needsrecord.'}}'); ?></P>
                                                            <P><a href="<?php echo DIR; ?>/logs/voicetrack/<?php echo $log['NAME']; ?>" class="btn btn-danger rounded-pill"><?= $ml->tr('FIXITNOW'); ?></a></P>
                                                        </div>
                                                    <?php $i++; } ?>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                        <h4><?= $ml->tr('NOVOICELOGSRECNEED') ?></h4>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div> <?php if (isset($json_sett["newsmess"]) && $json_sett["newsmess"] != "") { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><?= $ml->tr('MESSFROMADMINS {{'.APPNAME.'}}'); ?></h4>
                                    </div>
                                    <div class="card-body">
                                        <?php echo nl2br($json_sett["newsmess"]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>   <?php } ?>                     
                    </div>                    
                </section>
            </div>

            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>