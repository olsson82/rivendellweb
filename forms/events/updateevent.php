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
$colors = $_POST["colors"];
if (isset($_POST["cueevent"])) {
    $preposition = $_POST["schedstart"];
} else {
    $preposition = '-1';
}
if (isset($_POST["hardtime"])) {
    $hardtime = 1;
} else {
    $hardtime = 0;
}
if ($_POST["hardselect"] == 0) {
    $gracetime = 0;
} else if ($_POST["hardselect"] == 1) {
    $gracetime = '-1';
} else {
    $gracetime = $_POST["waituptomillis"];
}
$services = $_POST["services"];
$firstcart = $_POST["firstcart"];
$importcart = $_POST["importcart"];
if (isset($_POST["autofill"])) {
    $autofill = 'Y';
} else {
    $autofill = 'N';
}
if (isset($_POST["warnoverunder"])) {
    $warnoverunder = $_POST["byleastmillis"];
} else {
    $warnoverunder = '-1';
}

$importopt = $_POST["importopt"];
$impsched1 = $_POST["impsched1millis"];
$impsched2 = $_POST["impsched2millis"];
if ($_POST["inline"] == '0') {
    $inline = '';
} else {
    $inline = $_POST["inline"];
}

$group = $_POST["group"];
$artsep = $_POST["artsep"];
$titsep = $_POST["titsep"];
if ($_POST["musthave"] == '0') {
    $musthave = '';
} else {
    $musthave = $_POST["musthave"];
}
if ($_POST["musthave2"] == '0') {
    $musthave2 = '';
} else {
    $musthave2 = $_POST["musthave2"];
}
$name = $_POST["eventid"];
$notes = $_POST["usrnotes"];
$ok = 0;
$notok = 0;
$totaldata = count($services);
if (!$logfunc->removeEventPerms($name)) {
    $echodata = ['error' => 'true', 'errorcode' => '2'];
    echo json_encode($echodata);
} else {
    foreach ($services as $item) {
        if (!$logfunc->addEventPerms($name, $item)) {
            $notok = $notok + 1;
        } else {
            $ok = $ok + 1;
        }
    }
}

if ($ok == $totaldata) {

    if (!$logfunc->eventUpdate($name, $preposition, $hardtime, $gracetime, $firstcart, $importcart, $autofill, $warnoverunder, $importopt, $impsched1, $impsched2, $inline, $group, $artsep, $titsep, $musthave, $musthave2, $colors, $notes)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
        exit();
    } else {
        $echodata = ['error' => 'false', 'errorcode' => '0'];
        echo json_encode($echodata);
        exit();
    }

} else {
    $echodata = ['error' => 'true', 'errorcode' => '3'];
    echo json_encode($echodata);
    exit();
}