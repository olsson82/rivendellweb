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
$allscheds = $_POST['alls'];
$scheds = $_POST['scheds'];
$ausr = $_POST['ausr'];
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'];
$columnIndex = $_POST['order'][0]['column'];
$columnName = $_POST['columns'][$columnIndex]['data'];
$columnSortOrder = $_POST['order'][0]['dir'];
$searchValue = $_POST['search']['value'];

$searchArray = array();
$searchQuery = " ";
if ($searchValue != '') {
    if ($alla == 1 && $allscheds == 1) {
        $searchQuery = " AND usa.USER_NAME = :usernam AND (grid.NUMBER LIKE :number OR 
           grid.GROUP_NAME LIKE :group_name OR
           grid.TITLE LIKE :title OR 
           grid.ARTIST LIKE :artist ) ";
        $searchArray = array(
            'usernam' => $ausr,
            'number' => "%$searchValue%",
            'group_name' => "%$searchValue%",
            'title' => "%$searchValue%",
            'artist' => "%$searchValue%"
        );
    } else {
        if ($alla != 1 && $allscheds == 1) {
            $searchQuery = " AND grid.GROUP_NAME = :groupa AND (grid.NUMBER LIKE :number OR 
           grid.GROUP_NAME LIKE :group_name OR
           grid.TITLE LIKE :title OR 
           grid.ARTIST LIKE :artist ) ";
            $searchArray = array(
                'groupa' => $groups,
                'number' => "%$searchValue%",
                'group_name' => "%$searchValue%",
                'title' => "%$searchValue%",
                'artist' => "%$searchValue%"
            );
        } else if ($allscheds != 1 && $alla == 1) {
            $searchQuery = " AND sched.theschedcode = :schedd AND (grid.NUMBER LIKE :number OR 
           grid.GROUP_NAME LIKE :group_name OR
           grid.TITLE LIKE :title OR 
           grid.ARTIST LIKE :artist ) ";
            $searchArray = array(
                'schedd' => $scheds,
                'number' => "%$searchValue%",
                'group_name' => "%$searchValue%",
                'title' => "%$searchValue%",
                'artist' => "%$searchValue%"
            );
        } else if ($allscheds != 1 && $alla != 1) {
            $searchQuery = " AND grid.GROUP_NAME = :groupa AND sched.theschedcode = :schedd AND (grid.NUMBER LIKE :number OR 
           grid.GROUP_NAME LIKE :group_name OR
           grid.TITLE LIKE :title OR 
           grid.ARTIST LIKE :artist ) ";
            $searchArray = array(
                'groupa' => $groups,
                'schedd' => $scheds,
                'number' => "%$searchValue%",
                'group_name' => "%$searchValue%",
                'title' => "%$searchValue%",
                'artist' => "%$searchValue%"
            );
        }

    }
} else {

    if ($alla == 1 && $allscheds == 1) {
        $searchQuery = " AND usa.USER_NAME = :usernam ";
        $searchArray = array(
            'usernam' => $ausr
        );
    } else {
        if ($alla != 1 && $allscheds == 1) {
            $searchQuery = " AND grid.GROUP_NAME = :groupa ";
            $searchArray = array(
                'groupa' => $groups
            );
        } else if ($allscheds != 1 && $alla == 1) {
            $searchQuery = " AND sched.theschedcode = :schedd ";
            $searchArray = array(
                'schedd' => $scheds
            );
        } else if ($allscheds != 1 && $alla != 1) {
            $searchQuery = " AND sched.theschedcode = :schedd AND grid.GROUP_NAME = :groupa ";
            $searchArray = array(
                'schedd' => $scheds,
                'groupa' => $groups
            );
        }

    }

}

if ($alla == 1 && $allscheds == 1) {
    $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT CART_NUMBER, COUNT(*) AS schedcodes FROM CART_SCHED_CODES GROUP BY CART_NUMBER) sched ON grid.NUMBER = sched.CART_NUMBER LEFT JOIN USER_PERMS usa ON  grid.GROUP_NAME = usa.GROUP_NAME WHERE usa.USER_NAME = :usernam");
    $stmt->execute([':usernam' => $ausr]);
} else {
    if ($alla != 1 && $allscheds == 1) {
        $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER, COUNT(*) AS schedcodes FROM CART_SCHED_CODES GROUP BY CART_NUMBER) sched ON grid.NUMBER = sched.CART_NUMBER WHERE grid.GROUP_NAME = :groupname");
        $stmt->execute([':groupname' => $groups]);
    } else if ($allscheds != 1 && $alla == 1) {
        $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN CART_SCHED_CODES sched ON grid.NUMBER=sched.CART_NUMBER WHERE sched.SCHED_CODE = :schedcode");
        $stmt->execute([':schedcode' => $scheds]);
    } else if ($alla != 1 && $allscheds != 1) {
        $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN CART_SCHED_CODES sched ON grid.NUMBER=sched.CART_NUMBER WHERE sched.SCHED_CODE = :schedcode AND grid.GROUP_NAME = :groupname");
        $stmt->execute([':schedcode' => $scheds,
        ':groupname' => $groups]);
    }

}
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

if ($alla == 1 && $allscheds == 1) {
    $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER, COUNT(*) AS schedcodes FROM CART_SCHED_CODES GROUP BY CART_NUMBER) sched ON grid.NUMBER = sched.CART_NUMBER LEFT JOIN USER_PERMS usa ON  grid.GROUP_NAME = usa.GROUP_NAME WHERE 1 " . $searchQuery);
} else {
    if ($alla != 1 && $allscheds == 1) {
        $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER, COUNT(*) AS schedcodes FROM CART_SCHED_CODES GROUP BY CART_NUMBER) sched ON grid.NUMBER = sched.CART_NUMBER WHERE 1 " . $searchQuery);    
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) AS allcount FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER AS schedcodes FROM CART_SCHED_CODES) sched ON grid.NUMBER = sched.schedcodes WHERE 1 " . $searchQuery);
    }
}
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

if ($alla == 1 && $allscheds == 1) {
    $stmt = $db->prepare("SELECT grid.*, clk.*, sched.schedcodes, sched.theschedcode, usa.*  FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER, COUNT(*) AS schedcodes FROM CART_SCHED_CODES GROUP BY CART_NUMBER) sched ON grid.NUMBER = sched.CART_NUMBER LEFT JOIN USER_PERMS usa ON grid.GROUP_NAME = usa.GROUP_NAME WHERE 1 " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");
} else {
    if ($alla != 1 && $allscheds == 1) {
        $stmt = $db->prepare("SELECT grid.*, clk.*, sched.schedcodes, sched.theschedcode FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER, COUNT(*) AS schedcodes FROM CART_SCHED_CODES GROUP BY CART_NUMBER) sched ON grid.NUMBER = sched.CART_NUMBER WHERE 1 " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");    
    } else {
        $stmt = $db->prepare("SELECT grid.*, clk.*, sched.schedcodes, sched.theschedcode FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME LEFT JOIN (SELECT SCHED_CODE AS theschedcode, CART_NUMBER AS schedcodes FROM CART_SCHED_CODES) sched ON grid.NUMBER = sched.schedcodes WHERE 1 " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");
    }
    
}

foreach ($searchArray as $key => $search) {
    $stmt->bindValue(':' . $key, $search, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', (int) $row, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $rowperpage, PDO::PARAM_INT);
$stmt->execute();
$empRecords = $stmt->fetchAll();

$data = array();

foreach ($empRecords as $row) {
    $data[] = $row;
}

$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);

