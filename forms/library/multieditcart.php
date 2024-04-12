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
$request = $_POST['request'];
$id = $_POST['edit_arr'];
$shedcodes = $_POST['schedcodes'];
if (isset($_POST['schedcodes'])) {
    $wehavesched = 1;
} else {
    $wehavesched = 0;
}
$totaldata = count($id);
$ok = 0;
$notok = 0;
if ($request == 2) {
    $edit_arr = array();
    if (isset($_POST['edit_arr'])) {
        $edit_arr = $_POST['edit_arr'];
        foreach ($edit_arr as $editid) {
            if (isset($_POST['title']) && $_POST['title'] != '') {
                if (!$dbfunc->setCartTitle($editid, $_POST['title'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['artist']) && $_POST['artist'] != '') {
                if (!$dbfunc->setCartArtist($editid, $_POST['artist'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['year']) && $_POST['year'] != '') {
                if (!$dbfunc->setCartYear($editid, $_POST['year'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['songid']) && $_POST['songid'] != '') {
                if (!$dbfunc->setSongID($editid, $_POST['songid'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['album']) && $_POST['album'] != '') {
                if (!$dbfunc->setCartAlbum($editid, $_POST['album'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['record']) && $_POST['record'] != '') {
                if (!$dbfunc->setCartRecordLabel($editid, $_POST['record'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['client']) && $_POST['client'] != '') {
                if (!$dbfunc->setCartClient($editid, $_POST['client'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['agency']) && $_POST['agency'] != '') {
                if (!$dbfunc->setCartAgency($editid, $_POST['agency'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['publisher']) && $_POST['publisher'] != '') {
                if (!$dbfunc->setCartPublisher($editid, $_POST['publisher'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['composer']) && $_POST['composer'] != '') {
                if (!$dbfunc->setCartComposer($editid, $_POST['composer'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['conductor']) && $_POST['conductor'] != '') {
                if (!$dbfunc->setConductor($editid, $_POST['conductor'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['usrdef']) && $_POST['usrdef'] != '') {
                if (!$dbfunc->setCartUserDefined($editid, $_POST['usrdef'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['usagecode']) && $_POST['usagecode'] != 'non') {
                if (!$dbfunc->setCartUsageCode($editid, $_POST['usagecode'])) {
                    $notok = $notok + 1;
                }
            }
            if (isset($_POST['group']) && $_POST['group'] != 'non') {
                if (!$dbfunc->setGroupName($editid, $_POST['group'])) {
                    $notok = $notok + 1;
                }
            }
            $schedcodesold = $dbfunc->getCartSchedulerCodes($editid);
            if ($wehavesched == 1) {
                foreach ($schedcodesold as $scode) {
                    if (!$functions->rd_unassign_sched($editid, $scode['sched'])) {
                        $notok = $notok + 1;
                    }
                }

                foreach ($shedcodes as $item) {
                    if (!$functions->rd_assign_sched($editid, $item)) {
                        $notok = $notok + 1;
                    }
                }
            }
        }
    }

    if ($notok == 0) {
        $echodata = ['error' => 'false', 'errorcode' => '0'];
        echo json_encode($echodata);
    } else {
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
    }

}