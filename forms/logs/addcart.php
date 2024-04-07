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
$id = $_POST["idet"];
$cart = $_POST["cart"];
$carttype = $_POST["carttype"];
$rowplace = $_POST["rowplace"];
$transtype = $_POST["ifprevends"];
$iseditmode = $_POST["iseditmode"];
if (isset($_POST['startat'])) {
    $starttime = $_POST['starttimemillis'];
    $timetype = $_POST['hardselect'];
    $waitupto = $_POST['waittimemillis'];
    $timed = 1;
} else {
    $starttime = 0;
    $timetype = "-1";
    $waitupto = 0;
    $timed = 0;
}
if (isset($_POST['nofadesegue'])) {
    $seguegain = 0;
} else {
    $seguegain = -3000;
}

if ($timetype == 1) {
    $gracetime = '-1';
} else if ($timetype == 0) {
    $gracetime = '0';
} else if ($timetype == 2) {
    $gracetime = $waitupto;
} else {
    $gracetime = '0';
}

if ($iseditmode == 1) {
    $therowidno = $_POST['therowidno'];
    $logedit_data[$id]['LOGLINES'][$therowidno]['START_TIME'] = $starttime;
    $logedit_data[$id]['LOGLINES'][$therowidno]['GRACE_TIME'] = $gracetime;
    $logedit_data[$id]['LOGLINES'][$therowidno]['CART_NUMBER'] = $cart;
    $logedit_data[$id]['LOGLINES'][$therowidno]['TIME_TYPE'] = $timed;
    $logedit_data[$id]['LOGLINES'][$therowidno]['TRANS_TYPE'] = $transtype;
    $logedit_data[$id]['LOGLINES'][$therowidno]['SEGUE_GAIN'] = $seguegain;
    $logedit_data[$id]['LOGLINES'][$therowidno]['GROUP_NAME'] = $info->getCartInfo($cart, 'GROUP_NAME');
    $logedit_data[$id]['LOGLINES'][$therowidno]['TITLE'] = $info->getCartInfo($cart, 'TITLE');
    $logedit_data[$id]['LOGLINES'][$therowidno]['ARTIST'] = $info->getCartInfo($cart, 'ARTIST');
    $logedit_data[$id]['LOGLINES'][$therowidno]['AVERAGE_LENGTH'] = $info->getCartInfo($cart, 'AVERAGE_LENGTH');
    $logedit_data[$id]['LOGLINES'][$therowidno]['COLOR'] = $info->getGroupInfo($info->getCartInfo($cart, 'GROUP_NAME'), 'COLOR');

} else {
$nextid = $logedit_data[$id]['NEXT_ID'];
$logedit_data[$id]['NEXT_ID'] = $nextid + 1;

$tempid = "EE_" . $id . "_" . $nextid;

$countno = 0;
if ($rowplace == 'EE') {
    $countno = count($logedit_data[$id]['LOGLINES']);
} else if ($rowplace == 0) {
    $countno = 0;
    foreach ($logedit_data[$id]['LOGLINES'] as $lines) {
        $logedit_data[$id]['LOGLINES'][$lines['ID']]['COUNT'] = $logedit_data[$id]['LOGLINES'][$lines['ID']]['COUNT'] + 1;
    }

} else {
    $countno = $rowplace;
    $workfrom = $rowplace;
    foreach ($logedit_data[$id]['LOGLINES'] as $lines) {
        if ($workfrom == $logedit_data[$id]['LOGLINES'][$lines['ID']]['COUNT']) {
            $workfrom = $workfrom + 1;
            $logedit_data[$id]['LOGLINES'][$lines['ID']]['COUNT'] = $logedit_data[$id]['LOGLINES'][$lines['ID']]['COUNT'] + 1;
        }
        
    }
}

$groupSet = array();

$logedit_data[$id]['LOGLINES'][$tempid] = array(
    'ID' => $tempid,
    'LOG_NAME' => $id,
    'LINE_ID' => $nextid,
    'COUNT' => $countno,
    'TYPE' => 0,
    'SOURCE' => 0,
    'START_TIME' => $starttime,
    'GRACE_TIME' => $gracetime,
    'CART_NUMBER' => $cart,
    'TIME_TYPE' => $timed,
    'TRANS_TYPE' => $transtype,
    'START_POINT' => "-1",
    'END_POINT' => "-1",
    'FADEUP_POINT' => "-1",
    'FADEUP_GAIN' => 0,
    'FADEDOWN_POINT' => "-1",
    'FADEDOWN_GAIN' => 0,
    'SEGUE_START_POINT' => "-1",
    'SEGUE_END_POINT' => "-1",
    'SEGUE_GAIN' => $seguegain,
    'DUCK_UP_GAIN' => 0,
    'DUCK_DOWN_GAIN' => 0,
    'COMMENT' => "",
    'LABEL' => "",
    'ORIGIN_USER' => "",
    'ORIGIN_DATETIME' => null,
    'EVENT_LENGTH' => "-1",
    'LINK_EVENT_NAME' => "",
    'LINK_START_TIME' => 0,
    'LINK_START_SLOP' => 0,
    'LINK_END_SLOP' => 0,
    'LINK_LENGTH' => 0,
    'LINK_ID' => "-1",
    'LINK_EMBEDDED' => "N",
    'EXT_START_TIME' => null,
    'EXT_LENGTH' => "-1",
    'EXT_CART_NAME' => "",
    'EXT_DATA' => "",
    'EXT_EVENT_ID' => "",
    'EXT_ANNC_TYPE' => "",
    'GROUP_NAME' => $info->getCartInfo($cart, 'GROUP_NAME'),
    'TITLE' => $info->getCartInfo($cart, 'TITLE'),
    'ARTIST' => $info->getCartInfo($cart, 'ARTIST'),
    'AVERAGE_LENGTH' => $info->getCartInfo($cart, 'AVERAGE_LENGTH'),
    'COLOR' => $info->getGroupInfo($info->getCartInfo($cart, 'GROUP_NAME'), 'COLOR'),
);

uasort($logedit_data[$id]['LOGLINES'],function($a,$b){return $a['COUNT']-$b['COUNT'];});
}
foreach ($logedit_data[$id]['LOGLINES'] as $lines) {
    $logedit_data[$id]['LOGLINES'][$lines['ID']]['LINE_ID'] = $logedit_data[$id]['LOGLINES'][$lines['ID']]['COUNT'];
}
$jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    $echodata = ['error' => 'false', 'errorcode' => '0'];
    echo json_encode($echodata);
}