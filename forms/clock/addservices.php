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
$clockid = $_POST["clockid"];
$services = $_POST["services"];
$colors = $_POST["colors"];
$oldcolor = $_POST["oldclockcolor"];
$oldclockcode = $_POST['oldclockcode'];
$ccode = $_POST["ccode"];
$usernotes = $_POST["usernotes"];
$totaldata = count($services);
$ok = 0;
$notok = 0;
if (!$logfunc->removeClockPerms($clockid)) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    foreach ($services as $item) {
        if (!$logfunc->addClockPerms($clockid, $item)) {
            $notok = $notok + 1;
        } else {
            $ok = $ok + 1;
        }
    }
}

if ($ok == $totaldata) {
    if (!$logfunc->updateClockColour($clockid, $colors)) {
        $echodata = ['error' => 'true', 'errorcode' => '2'];
        echo json_encode($echodata);
    } else {
        foreach ($grids_data as $lines) {
            foreach ($lines['LAYOUT'] as $line) {
                foreach ($line['HRIDDATA'] as $data) {
                    if ($data['COLOR'] == $oldcolor) {
                        $grids_data[$lines['SERVICE']]['LAYOUT'][$line['LAYOUTNAME']]['HRIDDATA'][$data['HOUR']]['COLOR'] = $colors;
                    }
                }
            }
        }

        if (!$logfunc->updateClockData($clockid, $ccode, $usernotes)) {
            $echodata = ['error' => 'true', 'errorcode' => '3'];
            echo json_encode($echodata);
        } else {
            foreach ($grids_data as $lines) {
                foreach ($lines['LAYOUT'] as $line) {
                    foreach ($line['HRIDDATA'] as $data) {
                        if ($data['SHOR_NAME'] == $oldclockcode) {
                            $grids_data[$lines['SERVICE']]['LAYOUT'][$line['LAYOUTNAME']]['HRIDDATA'][$data['HOUR']]['SHOR_NAME'] = $ccode;
                        }
                    }
                }
            }
            $jsonData = json_encode($grids_data, JSON_PRETTY_PRINT);
            if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/grids.json', $jsonData)) {
                $echodata = ['error' => 'true', 'errorcode' => '1'];
                echo json_encode($echodata);
            } else {
                $echodata = ['error' => 'false', 'errorcode' => '0'];
                echo json_encode($echodata);
            }
        }
    }
}