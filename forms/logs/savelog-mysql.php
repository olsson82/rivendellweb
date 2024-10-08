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
$rd_username = $_COOKIE["username"];
$rd_password = $functions->loadPass($rd_username);
$rd_web_api = $_COOKIE["rdWebAPI"];
$logname = $_POST['logname'];
$description = $_POST['description'];
$service = $_POST['service'];
$lockguid = $logedit_data[$logname]['LOCK_GUID'];

if (isset($_POST['startdateac'])) {
    $startdate = $_POST['logstartdate'];
} else {
    $startdate = null;
}
if (isset($_POST['enddateac'])) {
    $enddate = $_POST['logenddate'];
} else {
    $enddate = null;
}
if (isset($_POST['removedateac'])) {
    $purgedate = $_POST['logremovedate'];
} else {
    $purgedate = null;
}
if (isset($_POST['autorefresh'])) {
    $autorefresh = 'Y';
} else {
    $autorefresh = 'N';
}
$linequantity = count($logedit_data[$logname]['LOGLINES']);

foreach ($logedit_data[$logname]['LOGLINES'] as $lines) {
    $logedit_data[$logname]['LOGLINES'][$lines['ID']]['LINE_ID'] = $logedit_data[$logname]['LOGLINES'][$lines['ID']]['COUNT'];
}

$parameters = array(
    'NAME' => $logname,
    'SERVICE' => $service,
    'DESCRIPTION' => $description,
    'AUTO_REFRESH' => $autorefresh,
    'START_DATE' => $startdate,
    'END_DATE' => $enddate,
    'PURGE_DATE' => $purgedate,
    'NEXT_ID' => $linequantity,
    'LOCK_GUID' => $lockguid
);

if (!$logfunc->updateLogData($parameters)) {
    $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'On save the log ' . $logname];
    echo json_encode($echodata);
} else {
    foreach ($logedit_data[$logname]['LOGLINES'] as $lines) {
        //Loop here and save log line thru Mysql
        if ($logedit_data[$logname]['LOGLINES'][$lines['ID']]['NEW_LINE'] == 'Y') {
            if (!$logfunc->saveNewLine($logedit_data[$logname]['LOGLINES'][$lines['ID']])) {
                $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'On save new line id ' . $lines['LINE_ID']];
                echo json_encode($echodata);
            } else {
                if ($logedit_data[$logname]['LOGLINES'][$lines['ID']]['TYPE'] == 6) {
                    $schedtrack = $info->getLogInfo($logname, "SCHEDULED_TRACKS") + 1;
                    if (!$logfunc->updateSchedTracks($schedtrack, $logname)) {
                        $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'On update scheduled tracks'];
                        echo json_encode($echodata);
                    }
                }
            }
        }
        if (!$logfunc->updateLogLine($logedit_data[$logname]['LOGLINES'][$lines['ID']])) {
            $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'On save line id ' . $lines['LINE_ID']];
            echo json_encode($echodata);
        }

    }

    unset($logedit_data[$logname]);
    if (!$logedit_data[$logname]) {
        $extra = array(
            'NAME' => $info->getLogInfo($logname, "NAME"),
            'SERVICE' => $info->getLogInfo($logname, "SERVICE"),
            'DESCRIPTION' => $info->getLogInfo($logname, "DESCRIPTION"),
            'AUTO_REFRESH' => $info->getLogInfo($logname, "AUTO_REFRESH"),
            'START_DATE' => $info->getLogInfo($logname, "START_DATE"),
            'END_DATE' => $info->getLogInfo($logname, "END_DATE"),
            'PURGE_DATE' => $info->getLogInfo($logname, "PURGE_DATE"),
            'NEXT_ID' => $info->getLogInfo($logname, "NEXT_ID"),
            'LOCK_GUID' => $lockguid
        );

        $groupSet = array();
        $timebefore = 0;
        $faketime = 0;
        $rowcount = 0;
        $sql = "SELECT * FROM LOG_LINES WHERE LOG_NAME = '$logname' ORDER BY COUNT ASC";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $cartno = $row['CART_NUMBER'];
            $sql2 = "SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.NUMBER = '$cartno'";
            $stmt1 = $db->prepare($sql2);
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $stmt1->execute();
            if ($stmt1->rowCount() == 1) {
                while ($row1 = $stmt1->fetch()) {
                    $groupname = $row1['GROUP_NAME'];
                    $title = $row1['TITLE'];
                    $artist = $row1['ARTIST'];
                    $averagelange = $row1['AVERAGE_LENGTH'];
                    $color = $row1['COLOR'];
                }
            } else {
                $groupname = "";
                $title = "";
                $artist = "";
                $averagelange = "0";
                $color = "";
            }

            if ($row['TYPE'] == 0) {
                if ($rowcount > 0) {
                    $timebefore = $timebefore + $averagelange;
                    $faketime = $timebefore - $averagelange;
                } else {
                    $timebefore = $averagelange;
                    $faketime = 0;
                }
            } else {
                $faketime = $timebefore;
            }

            $groupSet[$row['ID']] = array(
                'ID' => $row['ID'],
                'LOG_NAME' => $row['LOG_NAME'],
                'LINE_ID' => $row['LINE_ID'],
                'COUNT' => $row['COUNT'],
                'TYPE' => $row['TYPE'],
                'SOURCE' => $row['SOURCE'],
                'START_TIME' => $row['START_TIME'],
                'GRACE_TIME' => $row['GRACE_TIME'],
                'CART_NUMBER' => $row['CART_NUMBER'],
                'TIME_TYPE' => $row['TIME_TYPE'],
                'TRANS_TYPE' => $row['TRANS_TYPE'],
                'START_POINT' => $row['START_POINT'],
                'END_POINT' => $row['END_POINT'],
                'FADEUP_POINT' => $row['FADEUP_POINT'],
                'FADEUP_GAIN' => $row['FADEUP_GAIN'],
                'FADEDOWN_POINT' => $row['FADEDOWN_POINT'],
                'FADEDOWN_GAIN' => $row['FADEDOWN_GAIN'],
                'SEGUE_START_POINT' => $row['SEGUE_START_POINT'],
                'SEGUE_END_POINT' => $row['SEGUE_END_POINT'],
                'SEGUE_GAIN' => $row['SEGUE_GAIN'],
                'DUCK_UP_GAIN' => $row['DUCK_UP_GAIN'],
                'DUCK_DOWN_GAIN' => $row['DUCK_DOWN_GAIN'],
                'COMMENT' => $row['COMMENT'],
                'LABEL' => $row['LABEL'],
                'ORIGIN_USER' => $row['ORIGIN_USER'],
                'ORIGIN_DATETIME' => $row['ORIGIN_DATETIME'],
                'EVENT_LENGTH' => $row['EVENT_LENGTH'],
                'LINK_EVENT_NAME' => $row['LINK_EVENT_NAME'],
                'LINK_START_TIME' => $row['LINK_START_TIME'],
                'LINK_START_SLOP' => $row['LINK_START_SLOP'],
                'LINK_END_SLOP' => $row['LINK_END_SLOP'],
                'LINK_LENGTH' => $row['LINK_LENGTH'],
                'LINK_ID' => $row['LINK_ID'],
                'LINK_EMBEDDED' => $row['LINK_EMBEDDED'],
                'EXT_START_TIME' => $row['EXT_START_TIME'],
                'EXT_LENGTH' => $row['EXT_LENGTH'],
                'EXT_CART_NAME' => $row['EXT_CART_NAME'],
                'EXT_DATA' => $row['EXT_DATA'],
                'EXT_EVENT_ID' => $row['EXT_EVENT_ID'],
                'EXT_ANNC_TYPE' => $row['EXT_ANNC_TYPE'],
                'GROUP_NAME' => $groupname,
                'TITLE' => $title,
                'ARTIST' => $artist,
                'AVERAGE_LENGTH' => $averagelange,
                'COLOR' => $color,
                'FAKE_TIME' => $faketime,
                'NEW_LINE' => 'N',
            );
            $rowcount = $rowcount + 1;
        }

        $logedit_data[$logname] = $extra;
        $logedit_data[$logname]['LOGLINES'] = $groupSet;
        $jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
        if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData)) {
            $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'Not possible to save json file'];
            echo json_encode($echodata);
        } else {
            $date = date("Y-m-d H:i:s");
            if (!$logfunc->updateLogModified($date, $logname)) {
                $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'Not possible to update modified date time.'];
                echo json_encode($echodata);
            } else {
                $echodata = ['error' => 'false', 'errorcode' => '0'];
                echo json_encode($echodata);
            }

        }


    } else {
        $echodata = ['error' => 'true', 'errorcode' => '1', 'errormess' => 'Log not removed!'];
        echo json_encode($echodata);
    }

}

