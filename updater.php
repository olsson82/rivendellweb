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
$error = 0;
$json_sett["jsonID"] = "AxZQ9f3fEUkLz25131";
$jsonsettings = json_encode($json_sett, JSON_UNESCAPED_SLASHES);

    if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/settings.json', $jsonsettings)) {
        $error = 1;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rivendell Web Broadcast Updater</title>
    <link rel="shortcut icon" href="<?php echo DIR; ?>/AppImages/favicon.ico" />
    <link rel="stylesheet" href="<?php echo DIR; ?>/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?php echo DIR; ?>/assets/compiled/css/app-dark.css">
</head>

<body>
    <script src="<?php echo DIR; ?>/assets/static/js/initTheme.js"></script>
    <nav class="navbar navbar-light">
        <div class="container d-block">
            <a class="navbar-brand ms-4" href="javascript:;">
                <img src="<?php echo DIR; ?>/assets/static/images/rivlogo/rdairplay-128x128.png">
            </a>
        </div>
    </nav>

            <div class="container">
                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="card-title">Rivendell Web Broadcast Updater</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error == 1) { ?>
                            <P>Ooops! Looks like the update was not possible to do. Please check file write in the data folder.</P>
                        <?php } else { ?>
                        <P>This update has moved RDCatch to regular user use. No longer need admin rights to use it.</P>
                        <P>You need to give the user right to use RDCatch function in user settings. You will find Special User Rights where you give your user rights to use RDCatch. You need also to give your self access to it.</P>
                        <P class="col-sm-12 d-flex justify-content-end"><a target="_blank"
                                href="https://olsson82.github.io/rivwebdoc/" class="btn btn-info">Documentation</a> <a
                                href="<?php echo DIR; ?>/dash" class="btn btn-success">Go to dashboard</a></P>
                                <?php } ?>

                    </div>
                </div>
            </div>



    <script src="<?php echo DIR; ?>/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?php echo DIR; ?>/assets/compiled/js/app.js"></script>

</body>

</html>