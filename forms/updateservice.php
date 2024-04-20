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
$service = $_POST["service"];
$descr = $_POST["descr"];
$programc = $_POST["programc"];
$lognametemp = $_POST["lognametemp"];
$logdesctemp = $_POST["logdesctemp"];
$bypass = $_POST["bypass"];
$inline = $_POST["inline"];
$vtgroup = $_POST["vtgroup"];
$autospot = $_POST["autospot"];
if (isset($_POST["chainto"])) {
    $chainto = 'Y';
} else {
    $chainto = 'N';
}
if (isset($_POST["autorefresh"])) {
    $autorefresh = 'Y';
} else {
    $autorefresh = 'N';
}
if (isset($_POST["autodelete"])) {
    $autodelete = $_POST["autodeletedays"];
} else {
    $autodelete = '-1';
}
$daysaftertype = $_POST["daysaftertype"];
if (isset($_POST["purgeelr"])) {
    $purgeelr = $_POST["elrdays"];
} else {
    $purgeelr = '-1';
}
if (isset($_POST["musimpmark"])) {
    $musimpmark = 'Y';
} else {
    $musimpmark = 'N';
}
if (isset($_POST["trafimpmark"])) {
    $trafimpmark = 'Y';
} else {
    $trafimpmark = 'N';
}
$enabledhosts = $_POST["enabledhosts"];
$importpath = $_POST["importpath"];
$preimpcom = $_POST["preimpcom"];
$insertmarkerstring = $_POST["insertmarkerstring"];
$insertvtstring = $_POST["insertvtstring"];
$imptemplate = $_POST["imptemplate"];
$importpath_mus = $_POST["importpath_mus"];
$preimpcom_mus = $_POST["preimpcom_mus"];
$insertmarkerstring_mus = $_POST["insertmarkerstring_mus"];
$insertvtstring_mus = $_POST["insertvtstring_mus"];
$imptemplate_mus = $_POST["imptemplate_mus"];
$instraficbreak_mus = $_POST["instraficbreak_mus"];

$tfccartof = $_POST["tfccartof"];
$tfccartlength = $_POST["tfccartlength"];
$tfctitof = $_POST["tfctitof"];
$tfctitlength = $_POST["tfctitlength"];
$tfchourof = $_POST["tfchourof"];
$tfchourslength = $_POST["tfchourslength"];
$tfcminof = $_POST["tfcminof"];
$tfcminlength = $_POST["tfcminlength"];
$tfcsecof = $_POST["tfcsecof"];
$tfcseclength = $_POST["tfcseclength"];
$tfclenhoof = $_POST["tfclenhoof"];
$tfcleholength = $_POST["tfcleholength"];
$tfclenminof = $_POST["tfclenminof"];
$tfcleminlength = $_POST["tfcleminlength"];
$tfclensecof = $_POST["tfclensecof"];
$tfcleseclength = $_POST["tfcleseclength"];
$tfcdataof = $_POST["tfcdataof"];
$tfcdatalength = $_POST["tfcdatalength"];
$tfceventof = $_POST["tfceventof"];
$tfceventlength = $_POST["tfceventlength"];
$tfcanncof = $_POST["tfcanncof"];
$tfcannclength = $_POST["tfcannclength"];
$muscartof = $_POST["muscartof"];
$muscartlength = $_POST["muscartlength"];
$mustitof = $_POST["mustitof"];
$mustitlength = $_POST["mustitlength"];
$mushourof = $_POST["mushourof"];
$mushourslength = $_POST["mushourslength"];
$musminof = $_POST["musminof"];
$musminlength = $_POST["musminlength"];
$mussecof = $_POST["mussecof"];
$musseclength = $_POST["musseclength"];
$muslenhoof = $_POST["muslenhoof"];
$musleholength = $_POST["musleholength"];
$muslenminof = $_POST["muslenminof"];
$musleminlength = $_POST["musleminlength"];
$muslensecof = $_POST["muslensecof"];
$musleseclength = $_POST["musleseclength"];
$musdataof = $_POST["musdataof"];
$musdatalength = $_POST["musdatalength"];
$museventof = $_POST["museventof"];
$museventlength = $_POST["museventlength"];
$musanncof = $_POST["musanncof"];
$musannclength = $_POST["musannclength"];
$mustransof = $_POST["mustransof"];
$mustranslength = $_POST["mustranslength"];
$mustimeof = $_POST["mustimeof"];
$mustimelength = $_POST["mustimelength"];

if (isset($enabledhosts)) {
    $hostadd = 1;
} else {
    $hostadd = 0;
}

if ($hostadd == 0) {
    if (!$dbfunc->removeAllServiceHost($service)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
        exit();
    }
} else {
    foreach ($enabledhosts as $hosts) {

        if (!$dbfunc->removeServiceHost($hosts, $service)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
            exit();
        }

        if (!$dbfunc->AddServiceHost($hosts, $service)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
            exit();
        }
    }
}

if ($imptemplate == "cust") {
    if (!$dbfunc->updateTFCData($service, $tfccartof, $tfccartlength, $tfctitof, $tfctitlength, $tfchourof, $tfchourslength, $tfcminof, $tfcminlength, $tfcsecof, $tfcseclength, $tfclenhoof, $tfcleholength, $tfclenminof, $tfcleminlength, $tfclensecof, $tfcleseclength, $tfcdataof, $tfcdatalength, $tfceventof, $tfceventlength, $tfcanncof, $tfcannclength)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
        exit();
    }
}

if ($imptemplate_mus == "cust") {
    if (!$dbfunc->updateMUSData($service, $muscartof, $muscartlength, $mustitof, $mustitlength, $mushourof, $mushourslength, $musminof, $musminlength, $mussecof, $musseclength, $muslenhoof, $musleholength, $muslenminof, $musleminlength, $muslensecof, $musleseclength, $musdataof, $musdatalength, $museventof, $museventlength, $musanncof, $musannclength, $mustransof, $mustranslength, $mustimeof, $mustimelength)) {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
        exit();
    }
}


if (!$dbfunc->updateService($service, $descr, $programc, $lognametemp, $logdesctemp, $bypass, $inline, $vtgroup, $autospot, $chainto, $autorefresh, $autodelete, $daysaftertype, $purgeelr, $musimpmark, $trafimpmark, $importpath, $preimpcom, $insertmarkerstring, $insertvtstring, $imptemplate, $importpath_mus, $preimpcom_mus, $insertmarkerstring_mus, $insertvtstring_mus, $imptemplate_mus, $instraficbreak_mus)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    $echodata = ['error' => 'false', 'errorcode' => '0'];
    echo json_encode($echodata);
}