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
$location = $_POST['location'];
$start = $_POST['start'];
$desc = $_POST['desc'];
$feed = $_POST['feed'];
$url = $_POST['url'];
$username = $_POST['username'];
$password = $_POST['password'];
$source = $_POST['source'];
$audioformat = $_POST['audioformat'];
$audiochannels = $_POST['audiochannels'];
$samplerate = $_POST['samplerate'];
$audiobitrate = $_POST['audiobitrate'];
$audioquality = $_POST['audioquality'];
$filpa = $_POST['filpa'];
$dayoffset = $_POST['dayoffset'];

if ($audioformat == 0 || $audioformat == 7 || $audioformat == 4) {
    $audiobitrate = 0;
    $audioquality = 0;
}
if ($audioformat == 3 && $audiobitrate == 'VBR') {
    $audiobitrate = 0;
} else if ($audioformat == 3 && $audiobitrate != 'VBR') {
    $audiobitrate = $audiobitrate * 1000;
}
if ($audioformat == 2) {
    $audioquality = 0;
}
if ($audioformat == 5) {
    $audiobitrate = 0;
}

if (isset($password) && $password != $filpa) {
    $password = base64_encode($password);
}

if (isset($_POST['eventactive'])) {
    $eventactive = 'Y';
} else {
    $eventactive = 'N';
}

if (isset($_POST['normalize'])) {
    $normalize = $_POST['normlevel'] * 100;
} else {
    $normalize = '0';
}
if (isset($_POST['exportmeta'])) {
    $exportmeta = 'Y';
} else {
    $exportmeta = 'N';
}

if (isset($_POST['oneshot'])) {
    $oneshot = 'Y';
} else {
    $oneshot = 'N';
}

if (isset($_POST['monday'])) {
    $monday = 'Y';
} else {
    $monday = 'N';
}
if (isset($_POST['tuesday'])) {
    $tuesday = 'Y';
} else {
    $tuesday = 'N';
}
if (isset($_POST['wednesday'])) {
    $wednesday = 'Y';
} else {
    $wednesday = 'N';
}
if (isset($_POST['thursday'])) {
    $thursday = 'Y';
} else {
    $thursday = 'N';
}
if (isset($_POST['friday'])) {
    $friday = 'Y';
} else {
    $friday = 'N';
}
if (isset($_POST['saturday'])) {
    $saturday = 'Y';
} else {
    $saturday = 'N';
}
if (isset($_POST['sunday'])) {
    $sunday = 'Y';
} else {
    $sunday = 'N';
}

if (!$dbfunc->AddCatcUpload($eventactive, $location, $source, $sunday, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $desc, $start, $normalize, $audioformat, $samplerate, $audiochannels, $audiobitrate, $audioquality, $dayoffset, $oneshot, $url, $username, $password, $exportmeta, $feed)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    $echodata = ['error' => 'false', 'errorcode' => '0'];
    echo json_encode($echodata);
}