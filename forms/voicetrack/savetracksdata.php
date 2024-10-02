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
$lineid1 = $_POST["lineid1"];
$lineid2 = $_POST["lineid2"];
$lineid3 = $_POST["lineid3"];
$tr1start = $_POST["tr1start"];
$tr2start = $_POST["tr2start"];
$tr3start = $_POST["tr3start"];
$tr1end = $_POST["tr1end"];
$tr2end = $_POST["tr2end"];
$tr3end = $_POST["tr3end"];
$seg1end = $_POST["seg1end"];
$seg2end = $_POST["seg2end"];
$seg3end = $_POST["seg3end"];
$fadein1 = $_POST["fadein1"];
$fadein2 = $_POST["fadein2"];
$fadein3 = $_POST["fadein3"];
$fadeout1 = $_POST["fadeout1"];
$fadeout2 = $_POST["fadeout2"];
$fadeout3 = $_POST["fadeout3"];
$logname = $_POST["logname"];

//Start of next is always the track above.
//Last track start - voicetrack start
for ($i = 0; $i <= 2; $i++) {
    if ($i == 0) {
        //Store voicetrack start
        if (!$dbfunc->updateTrackData($lineid1, $tr2start, $seg1end, $logname)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
        } else {
            if (isset($fadein1) && $fadein1 > 0)  {
                if (!$dbfunc->updateFadeData($lineid1, $fadein1, '1', $logname)) {
                    $echodata = ['error' => 'true', 'errorcode' => '1'];
                    echo json_encode($echodata);
                }
            }
            if (isset($fadeout1) && $fadeout1 > 0)  {
                if (!$dbfunc->updateFadeData($lineid1, $fadeout1, '0', $logname)) {
                    $echodata = ['error' => 'true', 'errorcode' => '1'];
                    echo json_encode($echodata);
                }
            }
        }
    } else if ($i == 1) {
        $segstarttrack = $tr3start - $tr2start;
        if (!$dbfunc->updateTrackData($lineid2, $segstarttrack, $seg2end, $logname)) {
            $echodata = ['error' => 'true', 'errorcode' => '1'];
            echo json_encode($echodata);
        }
        if (isset($fadein2) && $fadein2 > 0)  {
            if (!$dbfunc->updateFadeData($lineid2, $fadein2, '1', $logname)) {
                $echodata = ['error' => 'true', 'errorcode' => '1'];
                echo json_encode($echodata);
            }
        }
        if (isset($fadeout2) && $fadeout2 > 0)  {
            if (!$dbfunc->updateFadeData($lineid2, $fadeout2, '0', $logname)) {
                $echodata = ['error' => 'true', 'errorcode' => '1'];
                echo json_encode($echodata);
            }
        }
    } else if ($i == 2) {
        if (isset($fadein3) && $fadein3 > 0)  {
            if (!$dbfunc->updateFadeData($lineid3, $fadein3, '1', $logname)) {
                $echodata = ['error' => 'true', 'errorcode' => '1'];
                echo json_encode($echodata);
            }
        }
        if (isset($fadeout3) && $fadeout3 > 0)  {
            if (!$dbfunc->updateFadeData($lineid2, $fadeout3, '0', $logname)) {
                $echodata = ['error' => 'true', 'errorcode' => '1'];
                echo json_encode($echodata);
            }
        }
    }
}
$echodata = ['error' => 'false', 'errorcode' => '0'];
echo json_encode($echodata);