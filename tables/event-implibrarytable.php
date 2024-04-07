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
$alla = $_POST['all'];
$groups = $_POST['groups'];
$ausr = $_POST['ausr'];
$datatable = array();
$jsonarray = array();

if ($alla == 1) {
    $stmtg = $db->prepare('SELECT * FROM USER_PERMS WHERE USER_NAME = :progid');
    $stmtg->execute([':progid' => $ausr]);
    $resultg = $stmtg->fetchAll();
    $jsonarray = array();
    foreach ($resultg as $rowg) {
        $stmt = $db->prepare('SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.GROUP_NAME = :grupp ORDER BY grid.GROUP_NAME ASC');

        $stmt->execute(['grupp' => $rowg['GROUP_NAME']]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $jsonarray[] = $row;
        }


    }
 
} else {
    $stmt = $db->prepare('SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.GROUP_NAME = :groupname');
    $stmt->execute([':groupname' => $groups]);
    $jsonarray = array();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $jsonarray = $data;
}

$datatable['data'] = $jsonarray; 
header('Content-Type: application/json; charset=utf-8');
$jsonData = json_encode($datatable, JSON_PRETTY_PRINT);
echo $jsonData;