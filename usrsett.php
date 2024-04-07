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

$username = $_COOKIE['username'];
$fullname = $_COOKIE['fullname'];

$pagecode = "usersett";
$page_vars = 'usrsett';
$page_title = $ml->tr('ACCINFO');
$page_css = '<link rel="stylesheet" href="assets/extensions/sweetalert2/sweetalert2.min.css">';
$plugin_js = '<script src="assets/extensions/jqueryvalidation/jquery.validate.min.js"></script>
<script src="assets/extensions/jqueryvalidation/additional-methods.min.js"></script>
<script src="assets/extensions/sweetalert2/sweetalert2.min.js"></script>';
$page_js = '<script src="assets/static/js/usrsett.js"></script>';
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/top.php'; ?>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3><?= $ml->tr('ACCINFO'); ?></h3>
                            <p class="text-subtitle text-muted"><?= $ml->tr('UPDATEACCOUNTINFO'); ?></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dash.php"><?= $ml->tr('DASHBOARD'); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?= $ml->tr('ACCINFO'); ?></li>
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
                                    <h5 class="card-title"><?= $ml->tr('CHANGEPASS'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <form action="#" id="pass_form" method="get">
                                        <div class="form-group my-2">
                                            <label for="current_password" class="form-label"><?= $ml->tr('CURRENTPASS'); ?></label>
                                            <input type="password" name="current_password" id="current_password"
                                                class="form-control" value="">
                                        </div>
                                        <div class="form-group my-2">
                                            <label for="password" class="form-label"><?= $ml->tr('NEWPASS'); ?></label>
                                            <input type="password" name="password" id="password" class="form-control" value="">
                                        </div>
                                        <div class="form-group my-2">
                                            <label for="confirm_password" class="form-label"><?= $ml->tr('CONFPASS'); ?></label>
                                            <input type="password" name="confirm_password" id="confirm_password"
                                                class="form-control" value="">
                                        </div>

                                        <div class="form-group my-2 d-flex justify-content-end">
                                            <input type="hidden" name="username" value="<?php echo $username;?>">
                                            <button type="submit" class="btn btn-danger"><?= $ml->tr('CHANGEPASS'); ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><?= $ml->tr('USERINFORMATION'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <form action="#" id="user_form" method="get">
                                        <div class="form-group my-2">
                                            <label for="fullname" class="form-label"><?= $ml->tr('FULLNAME'); ?></label>
                                            <input type="text" name="fullname" id="fullname" class="form-control" value="<?php echo $info->getUserFullName($username) ?>">
                                        </div>
                                        <div class="form-group my-2">
                                            <label for="email" class="form-label"><?= $ml->tr('EMAIL'); ?></label>
                                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $info->getUserEmail($username) ?>">
                                        </div>
                                        <div class="form-group my-2">
                                            <label for="phone" class="form-label"><?= $ml->tr('PHONE'); ?></label>
                                            <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $info->getUserPhoneNumber($username) ?>">
                                        </div>

                                        <div class="form-group my-2 d-flex justify-content-end">
                                        <input type="hidden" name="username" value="<?php echo $username;?>">
                                            <button type="submit" class="btn btn-primary"><?= $ml->tr('SAVE'); ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </section>
            </div>

            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/bottom.php'; ?>