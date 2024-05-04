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
$cdesc = $_POST['cdesc'];
$coutcue = $_POST['coutcue'];
$ciscicode = $_POST['ciscicode'];
$cisrc = $_POST['cisrc'];
$cart = $_POST['idet'];
$cutid = $_POST['cutid'];
$cutnumber = substr($cutid, strpos($cutid, "_") + 1);

if ($info->getCartInfo($cart, "USE_WEIGHTING") == 'N') {
    $useorder = 1;
} else {
    $useorder = 0;
}

if (isset($_POST['evergreen'])) {
    $evergreen = 1;
} else {
    $evergreen = 0;
}
$weight = $_POST['weight'];
if (isset($_POST['airenable'])) {
    $airenable = 1;
} else {
    $airenable = 0;
}
if (isset($_POST['airdaypartactive'])) {
    $airdaypartactive = 1;
} else {
    $airdaypartactive = 0;
}
if ($airenable == 1) {
    $adstart = $_POST['adstart'];
    $adstart = strtotime($adstart);
    $adstart = date('Y-m-d\TH:i:s', $adstart);
    $adend = $_POST['adend'];
    $adend = strtotime($adend);
    $adend = date('Y-m-d\TH:i:s', $adend);
} else {
    $adstart = "";
    $adend = "";
}
if ($airdaypartactive == 1) {
    $adaystart = $_POST['adaystart'];
    $adayend = $_POST['adayend'];
}

if (isset($_POST['daymon'])) {
    $daymon = 1;
} else {
    $daymon = 0;
}
if (isset($_POST['daytue'])) {
    $daytue = 1;
} else {
    $daytue = 0;
}
if (isset($_POST['daywed'])) {
    $daywed = 1;
} else {
    $daywed = 0;
}
if (isset($_POST['daythu'])) {
    $daythu = 1;
} else {
    $daythu = 0;
}
if (isset($_POST['dayfri'])) {
    $dayfri = 1;
} else {
    $dayfri = 0;
}
if (isset($_POST['daysat'])) {
    $daysat = 1;
} else {
    $daysat = 0;
}
if (isset($_POST['daysun'])) {
    $daysun = 1;
} else {
    $daysun = 0;
}

if ($useorder == 1) {
    if (!$dbfunc->updateCutOrder($cutid, $weight)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
    }
}

if (!$functions->rd_edit_cut($cart, $cutnumber, $evergreen, $cdesc, $coutcue, $cisrc, $ciscicode, $adstart, $adend, $daymon, $daytue, $daywed, $daythu, $dayfri, $daysat, $daysun, $adaystart, $adayend, $weight, $useorder)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    $echodata = ['error' => 'false', 'errorcode' => '0'];
    echo json_encode($echodata);
}