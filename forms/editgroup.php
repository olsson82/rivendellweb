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
$groupid = $_POST['groupid'];
$groupdesc = $_POST['groupdesc'];
$gimport = $_POST['gimport'];
$emailaddresses = $_POST['emailaddresses'];
$carttype = $_POST['carttype'];
$cartstart = $_POST['cartstart'];
$cartend = $_POST['cartend'];
$color = $_POST['color'];
$activeservice = $_POST['activeservice'];
if (isset($activeservice)) {
    $wehaveservice = 1;
} else {
    $wehaveservice = 0;
}
$serviceold = $dbfunc->getServicesGroup($groupid);
if (isset($_POST['enfcart'])) {
    $enfcart = 'Y';
} else {
    $enfcart = 'N'; 
}
if (isset($_POST['inctraffic'])) {
    $inctraffic = 'Y';
} else {
    $inctraffic = 'N'; 
}
if (isset($_POST['incmusic'])) {
    $incmusic = 'Y';
} else {
    $incmusic = 'N'; 
}
if (isset($_POST['enddatetime'])) {
    $cutcreation = $_POST['cutcreation'];
} else {
    $cutcreation = '-1'; 
}
if (isset($_POST['purge'])) {
    $purgedays = $_POST['purgedays'];
} else {
    $purgedays = '-1'; 
}
if (isset($_POST['delempty'])) {
    $delempty = 'Y';
} else {
    $delempty = 'N'; 
}

if ($wehaveservice == 0) {
    if (!$dbfunc->clearServiceGroup($groupid)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
        exit();
    }
}

if ($wehaveservice == 1) {
    foreach ($serviceold as $scode) {
        if (!$dbfunc->removeOldServiceGroup($groupid, $scode)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
            exit();
        }
    }

    foreach ($activeservice as $scode) {
        if (!$dbfunc->addNewServiceGroup($groupid, $scode)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
            exit();
        }
    }

    
}

if (!$dbfunc->updateGroupInfo($groupid, $groupdesc, $gimport, $emailaddresses, $carttype, $cartstart, $cartend, $color, $enfcart, $inctraffic, $incmusic, $cutcreation, $purgedays, $delempty)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    $echodata = ['error' => 'false', 'errorcode' => '0'];
    echo json_encode($echodata);
}

