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
$schedcodesold = $dbfunc->getCartSchedulerCodes($number);
$number = $_POST['number'];
$group = $_POST['group'];
$title = $_POST['title'];
$artist = $_POST['artist'];
$album = $_POST['album'];
$year = $_POST['year'];
$record = $_POST['record'];
$client = $_POST['client'];
$agency = $_POST['agency'];
$publisher = $_POST['publisher'];
$composer = $_POST['composer'];
$userdef = $_POST['userdef'];
$usagecode = $_POST['usagecode'];
$shedcodes = $_POST['schedcodes'];
$beatmin = $_POST['beatmin'];
$songid = $_POST['songid'];
$conductor = $_POST['conductor'];
if (isset($_POST['enflength'])) {
    $enflength = 1;
} else {
    $enflength = 0;
}
$frlength = $_POST['enfrangecorr'];
$asynchronous = "0";
$notes = $_POST['notes'];
if (isset($shedcodes)) {
    $wehavesched = 1;
} else {
    $wehavesched = 0;
}

if ($wehavesched == 0) {
    if (!$dbfunc->clearSchedCodesCart($number)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
        exit();
    }
}

if ($wehavesched == 1) {
    foreach ($schedcodesold as $scode) {
        if (!$functions->rd_unassign_sched($number, $scode['sched'])) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
            exit();
        }
    }

    foreach ($shedcodes as $item) {
        if (!$functions->rd_assign_sched($number, $item)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
            exit();
        }
    }
}

if (!$functions->rd_edit_cart($number, $group, $album, $year, $record, $client, $agency, $publisher, $composer, $userdef, $usagecode, $enflength, $frlength, $asynchronous, $artist, $title, $notes)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    if ($usagecode == 0) {
        if (!$dbfunc->resetUsageCode($number)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
        } else {
            $echodata = ['error' => 'false', 'errorcode' => '0'];
            echo json_encode($echodata);
        }
    } else {
        $echodata = ['error' => 'false', 'errorcode' => '0'];
        echo json_encode($echodata);
    }
}