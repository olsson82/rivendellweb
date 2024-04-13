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
class DBFunc
{
    private $_db;
    private $_ignoreCase;

    public function __construct($db)
    {
        $this->_db = $db;
        $this->_ignoreCase = false;
    }

    public function setIgnoreCase($sensitive)
    {
        $this->_ignoreCase = $sensitive;
    }

    public function getIgnoreCase()
    {
        return $this->_ignoreCase;
    }

    public function getCutData($cutid)
    {

        $stmt = $this->_db->prepare('SELECT * FROM CUTS WHERE CUT_NAME = :id');
        $stmt->execute([':id' => $cutid]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getLogLineData($lineid, $logname)
    {

        $stmt = $this->_db->prepare('SELECT * FROM LOG_LINES ll LEFT JOIN CART cc ON ll.CART_NUMBER = cc.NUMBER WHERE ll.LOG_NAME = :logname AND ll.LINE_ID = :lineid');
        $stmt->execute([
            ':logname' => $logname,
            ':lineid' => $lineid
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getCutInfoTable($cutid, $order)
    {

        $cutdata = array();
        if ($order == 1) {
            $sql = 'SELECT * FROM `CUTS`
            WHERE `CART_NUMBER` = :number ORDER BY CUT_NAME ASC';
        } else {
            $sql = 'SELECT * FROM `CUTS`
            WHERE `CART_NUMBER` = :number ORDER BY PLAY_ORDER ASC';
        }

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':number', $cutid);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $cutdata[] = array(
                'cutname' => $row['CUT_NAME'],
                'cartnumber' => $row['CART_NUMBER'],
                'evergreen' => $row['EVERGREEN'],
                'description' => $row['DESCRIPTION'],
                'outcue' => $row['OUTCUE'],
                'isrc' => $row['ISRC'],
                'isci' => $row['ISCI'],
                'recording' => $row['RECORDING_MBID'],
                'release' => $row['RELEASE_MBID'],
                'length' => $row['LENGTH'],
                'sha1' => $row['SHA1_HASH'],
                'origin' => $row['ORIGIN_DATETIME'],
                'startdate' => $row['START_DATETIME'],
                'enddate' => $row['END_DATETIME'],
                'sun' => $row['SUN'],
                'mon' => $row['MON'],
                'tue' => $row['TUE'],
                'wed' => $row['WED'],
                'thu' => $row['THU'],
                'fri' => $row['FRI'],
                'sat' => $row['SAT'],
                'sdaypart' => $row['START_DAYPART'],
                'edaypart' => $row['END_DAYPART'],
                'originname' => $row['ORIGIN_NAME'],
                'originlogin' => $row['ORIGIN_LOGIN_NAME'],
                'sourcehost' => $row['SOURCE_HOSTNAME'],
                'weight' => $row['WEIGHT'],
                'playorder' => $row['PLAY_ORDER'],
                'lastplaydate' => $row['LAST_PLAY_DATETIME'],
                'uploaddate' => $row['UPLOAD_DATETIME'],
                'playcounter' => $row['PLAY_COUNTER'],
                'localcounter' => $row['LOCAL_COUNTER'],
                'validity' => $row['VALIDITY'],
                'coding' => $row['CODING_FORMAT'],
                'samplerate' => $row['SAMPLE_RATE'],
                'bitrate' => $row['BIT_RATE'],
                'channels' => $row['CHANNELS'],
                'playgain' => $row['PLAY_GAIN'],
                'startpoint' => $row['START_POINT'],
                'endpoint' => $row['END_POINT'],
                'fadeuppoint' => $row['FADEUP_POINT'],
                'fadedownpoint' => $row['FADEDOWN_POINT'],
                'seguestart' => $row['SEGUE_START_POINT'],
                'segueend' => $row['SEGUE_END_POINT'],
                'seguegain' => $row['SEGUE_GAIN'],
                'hookstart' => $row['HOOK_START_POINT'],
                'hookend' => $row['HOOK_END_POINT'],
                'talkstart' => $row['TALK_START_POINT'],
                'talkend' => $row['TALK_END_POINT'],
            );
        }

        $stmt = NULL;

        return $cutdata;

    }

    public function getCutInfo($cutid)
    {

        $cutdata = array();

        $sql = 'SELECT * FROM `CUTS`
                WHERE `CART_NUMBER` = :number';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':number', $cutid);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $cutdata[] = array(
                'cutname' => $row['CUT_NAME'],
                'cartnumber' => $row['CART_NUMBER'],
                'evergreen' => $row['EVERGREEN'],
                'description' => $row['DESCRIPTION'],
                'outcue' => $row['OUTCUE'],
                'isrc' => $row['ISRC'],
                'isci' => $row['ISCI'],
                'recording' => $row['RECORDING_MBID'],
                'release' => $row['RELEASE_MBID'],
                'length' => $row['LENGTH'],
                'sha1' => $row['SHA1_HASH'],
                'origin' => $row['ORIGIN_DATETIME'],
                'startdate' => $row['START_DATETIME'],
                'enddate' => $row['END_DATETIME'],
                'sun' => $row['SUN'],
                'mon' => $row['MON'],
                'tue' => $row['TUE'],
                'wed' => $row['WED'],
                'thu' => $row['THU'],
                'fri' => $row['FRI'],
                'sat' => $row['SAT'],
                'sdaypart' => $row['START_DAYPART'],
                'edaypart' => $row['END_DAYPART'],
                'originname' => $row['ORIGIN_NAME'],
                'originlogin' => $row['ORIGIN_LOGIN_NAME'],
                'sourcehost' => $row['SOURCE_HOSTNAME'],
                'weight' => $row['WEIGHT'],
                'playorder' => $row['PLAY_ORDER'],
                'lastplaydate' => $row['LAST_PLAY_DATETIME'],
                'uploaddate' => $row['UPLOAD_DATETIME'],
                'playcounter' => $row['PLAY_COUNTER'],
                'localcounter' => $row['LOCAL_COUNTER'],
                'validity' => $row['VALIDITY'],
                'coding' => $row['CODING_FORMAT'],
                'samplerate' => $row['SAMPLE_RATE'],
                'bitrate' => $row['BIT_RATE'],
                'channels' => $row['CHANNELS'],
                'playgain' => $row['PLAY_GAIN'],
                'startpoint' => $row['START_POINT'],
                'endpoint' => $row['END_POINT'],
                'fadeuppoint' => $row['FADEUP_POINT'],
                'fadedownpoint' => $row['FADEDOWN_POINT'],
                'seguestart' => $row['SEGUE_START_POINT'],
                'segueend' => $row['SEGUE_END_POINT'],
                'seguegain' => $row['SEGUE_GAIN'],
                'hookstart' => $row['HOOK_START_POINT'],
                'hookend' => $row['HOOK_END_POINT'],
                'talkstart' => $row['TALK_START_POINT'],
                'talkend' => $row['TALK_END_POINT'],
            );
        }

        $stmt = NULL;

        return $cutdata;

    }



    public function getEventLineInfo($evid)
    {

        $stmt = $this->_db->prepare('SELECT * FROM EVENT_LINES WHERE ID = :id');
        $stmt->execute([':id' => $evid]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getEventData($eventid)
    {

        $stmt = $this->_db->prepare('SELECT * FROM EVENTS WHERE NAME = :evname');
        $stmt->execute([
            ':evname' => $eventid
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getClockEventData($eventid)
    {

        $stmt = $this->_db->prepare('SELECT * FROM CLOCK_LINES WHERE ID = :evname');
        $stmt->execute([
            ':evname' => $eventid
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getSchedRulesData($code, $clock)
    {

        $stmt = $this->_db->prepare('SELECT * FROM RULE_LINES WHERE CLOCK_NAME = :evname AND CODE = :codename');
        $stmt->execute([
            ':evname' => $clock,
            ':codename' => $code
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getClockEventExistTime($eventid, $starttime)
    {

        $stmt = $this->_db->prepare('SELECT * FROM CLOCK_LINES WHERE CLOCK_NAME = :evname AND START_TIME = :starttime');
        $stmt->execute([
            ':evname' => $eventid,
            ':starttime' => $starttime
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

    public function getCutLineColor($cutid, $day)
    {
        $color = "";
        $stmt = $this->_db->prepare('SELECT * FROM CUTS WHERE CUT_NAME = :evname');
        $stmt->execute([
            ':evname' => $cutid
        ]);
        $number_of_rows = $stmt->rowCount();
        while ($row = $stmt->fetch()) {
            if ($row['LENGTH'] == 0) {
                $color = '#6f0000';
            } else {
                if ($day == 0 && $row['SUN'] == 'N' || $day == 1 && $row['MON'] == 'N' || $day == 2 && $row['TUE'] == 'N' || $day == 3 && $row['WED'] == 'N' || $day == 4 && $row['THU'] == 'N' || $day == 5 && $row['FRI'] == 'N' || $day == 6 && $row['SAT'] == 'N') {
                    $color = '#6f0000';
                } else
                    if (isset($row['START_DATETIME']) && new DateTime() < new DateTime($row['START_DATETIME'])) {
                        $color = '#01f8f4';

                    } else
                        if (isset($row['END_DATETIME']) && new DateTime() > new DateTime($row['END_DATETIME'])) {
                            $color = '#6f0000';
                        } else
                            if (isset($row['START_DAYPART'])) {
                                if (new DateTime() < new DateTime($row['START_DAYPART']) || new DateTime() > new DateTime($row['END_DAYPART'])) {
                                    $color = '#6f0000';
                                }
                            }
            }
        }
        return $color;


    }

    public function getTotLibrary()
    {
        $stmt = $this->_db->prepare('SELECT * FROM CART');
        $stmt->execute();
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        return $number_of_rows;
    }

    public function getTotGroups()
    {
        $stmt = $this->_db->prepare('SELECT * FROM GROUPS');
        $stmt->execute();
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        return $number_of_rows;
    }

    public function getTotSched()
    {
        $stmt = $this->_db->prepare('SELECT * FROM SCHED_CODES');
        $stmt->execute();
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        return $number_of_rows;
    }

    public function getTotLogs()
    {
        $stmt = $this->_db->prepare('SELECT * FROM LOGS');
        $stmt->execute();
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        return $number_of_rows;
    }

    public function getVoicetrackWorks($service)
    {
        $works = array();
        $stmt = $this->_db->prepare('SELECT * FROM LOGS WHERE SERVICE = :services AND SCHEDULED_TRACKS > COMPLETED_TRACKS LIMIT 3');
        $stmt->execute([
            ':services' => $service
        ]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $stmt->fetch())
            $works[$row['NAME']] = $row;

        return $works;

    }

    public function getVoicetrackJobs($service)
    {
        $stmt = $this->_db->prepare('SELECT * FROM LOGS WHERE SERVICE = :services AND SCHEDULED_TRACKS > COMPLETED_TRACKS');
        $stmt->execute([
            ':services' => $service
        ]);
        $number_of_rows = $stmt->rowCount();
        return $number_of_rows;
    }

    public function getClockNameExist($clockname)
    {

        $stmt = $this->_db->prepare('SELECT * FROM CLOCKS WHERE NAME = :evname');
        $stmt->execute([
            ':evname' => $clockname
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

    public function getLogNameExist($logname)
    {

        $stmt = $this->_db->prepare('SELECT * FROM LOGS WHERE NAME = :logname');
        $stmt->execute([
            ':logname' => $logname
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }
    public function getEventNameExist($eventname)
    {

        $stmt = $this->_db->prepare('SELECT * FROM EVENTS WHERE NAME = :evname');
        $stmt->execute([
            ':evname' => $eventname
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

    public function getClockCodeExist($clockname)
    {

        $stmt = $this->_db->prepare('SELECT * FROM CLOCKS WHERE SHORT_NAME = :evname');
        $stmt->execute([
            ':evname' => $clockname
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }


    public function rd_updateVTCart($log, $line, $cart, $username)
    {
        $sql = "UPDATE LOG_LINES SET CART_NUMBER = '$cart', TYPE='0', ORIGIN_USER = '$username', SOURCE = '4' WHERE (LOG_LINES.LOG_NAME = '$log' AND LOG_LINES.LINE_ID = '$line')";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute();

        $sql = "SELECT COMPLETED_TRACKS FROM `LOGS` WHERE NAME='$log'";
        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $row = $stmt->fetch();
        $completed = $row['COMPLETED_TRACKS'];
        $completed++;

        $sql = "UPDATE LOGS SET COMPLETED_TRACKS='$completed' WHERE NAME='$log'";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();

    }

    public function getVoicetrackInformation($service)
    {


        $groupSet = array();
        $sql = "SELECT TRACK_GROUP FROM SERVICES WHERE NAME = '$service'";

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $row = $stmt->fetch();
        $trkGrp = $row['TRACK_GROUP'];

        $sql = "SELECT DEFAULT_LOW_CART, DEFAULT_HIGH_CART FROM GROUPS WHERE NAME = '$trkGrp'";

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $groupSet[] = array(
                'group' => $trkGrp,
                'default_low_cart' => $row['DEFAULT_LOW_CART'],
                'default_high_cart' => $row['DEFAULT_HIGH_CART'],
            );
        }


        return $groupSet;

    }

    public function getSchedulerCodes()
    {

        $schedSet = array();

        $sql = "SELECT CODE, DESCRIPTION
                FROM SCHED_CODES";

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $schedSet[] = array(
                'code' => $row['CODE'],
                'desc' => $row['DESCRIPTION'],
            );
        }

        return $schedSet;

    }

    public function clearSchedCodesCart($cartnumber)
    {
        $stmt1 = $this->_db->prepare('DELETE FROM CART_SCHED_CODES WHERE CART_NUMBER = :id');
        $stmt1->execute([
            ':id' => $cartnumber,
        ]);

        return true;
    }

    public function getCartSchedulerCodes($cartnumber)
    {

        $schedSet = array();

        $sql = "SELECT CART_NUMBER, SCHED_CODE
                FROM CART_SCHED_CODES WHERE CART_NUMBER = :cartnumb";

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':cartnumb', $cartnumber);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $schedSet[] = array(
                'number' => $row['CART_NUMBER'],
                'sched' => $row['SCHED_CODE'],
            );
        }

        return $schedSet;

    }
    public function getGroupInformation()
    {

        $groupSet = array();

        $sql = "SELECT NAME, DEFAULT_LOW_CART, DEFAULT_HIGH_CART, COLOR
                FROM GROUPS";

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $groupSet[] = array(
                'name' => $row['NAME'],
                'default_low_cart' => $row['DEFAULT_LOW_CART'],
                'default_high_cart' => $row['DEFAULT_HIGH_CART'],
                'color' => $row['COLOR'],
            );
        }

        return $groupSet;

    }

    public function getEventPostLines($event)
    {

        $groupSet = array();

        $sql = "SELECT ID, EVENT_NAME, TYPE, COUNT, EVENT_TYPE, CART_NUMBER, TRANS_TYPE, MARKER_COMMENT
                FROM EVENT_LINES WHERE EVENT_NAME = '$event' AND TYPE='1' ORDER BY COUNT ASC";



        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $cartno = $row['CART_NUMBER'];
            $sql2 = "SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.NUMBER = '$cartno'";
            $stmt1 = $this->_db->prepare($sql2);
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

            $groupSet[] = array(
                'ID' => $row['ID'],
                'EVENT_NAME' => $row['EVENT_NAME'],
                'TYPE' => $row['TYPE'],
                'COUNT' => $row['COUNT'],
                'EVENT_TYPE' => $row['EVENT_TYPE'],
                'CART_NUMBER' => $row['CART_NUMBER'],
                'TRANS_TYPE' => $row['TRANS_TYPE'],
                'MARKER_COMMENT' => $row['MARKER_COMMENT'],
                'GROUP_NAME' => $groupname,
                'TITLE' => $title,
                'ARTIST' => $artist,
                'AVERAGE_LENGTH' => $averagelange,
                'COLOR' => $color,
            );

        }

        return $groupSet;

    }

    public function getEventPreLines($event)
    {

        $groupSet = array();

        $sql = "SELECT ID, EVENT_NAME, TYPE, COUNT, EVENT_TYPE, CART_NUMBER, TRANS_TYPE, MARKER_COMMENT
                FROM EVENT_LINES WHERE EVENT_NAME = '$event' AND TYPE='0' ORDER BY COUNT ASC";



        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $cartno = $row['CART_NUMBER'];
            $sql2 = "SELECT * FROM CART grid LEFT JOIN GROUPS clk ON grid.GROUP_NAME=clk.NAME WHERE grid.NUMBER = '$cartno'";
            $stmt1 = $this->_db->prepare($sql2);
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

            $groupSet[] = array(
                'ID' => $row['ID'],
                'EVENT_NAME' => $row['EVENT_NAME'],
                'TYPE' => $row['TYPE'],
                'COUNT' => $row['COUNT'],
                'EVENT_TYPE' => $row['EVENT_TYPE'],
                'CART_NUMBER' => $row['CART_NUMBER'],
                'TRANS_TYPE' => $row['TRANS_TYPE'],
                'MARKER_COMMENT' => $row['MARKER_COMMENT'],
                'GROUP_NAME' => $groupname,
                'TITLE' => $title,
                'ARTIST' => $artist,
                'AVERAGE_LENGTH' => $averagelange,
                'COLOR' => $color,
            );

        }

        return $groupSet;

    }



    public function getRivendellLog($logname, $hour)
    {

        $logSet = array();
        $sql = "";

        $lowerMS = 0;
        $upperMS = 86400000;

        if ($hour) {
            $lowerMS = $hour * 3600 * 1000;
            $upperMS = $lowerMS + ((3600 * 1000) - 1);
        }


        $sql = "SELECT COUNT, CART.ARTIST, CART.TITLE, CART.GROUP_NAME, CART.AVERAGE_LENGTH, 
                ID, SOURCE, log.TYPE, START_TIME, LINE_ID,
                CART_NUMBER, COMMENT, log.LABEL, EVENT_LENGTH, LINK_EVENT_NAME, 
                LINK_START_TIME, LINK_LENGTH, EXT_START_TIME, EXT_CART_NAME, gr.COLOR
                FROM LOG_LINES log
                LEFT JOIN CART ON log.CART_NUMBER=CART.NUMBER
                LEFT JOIN GROUPS gr ON CART.GROUP_NAME=gr.NAME
                WHERE log.LOG_NAME='$logname' AND
                START_TIME BETWEEN $lowerMS AND $upperMS ORDER BY COUNT ASC";


        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();


        while ($row = $stmt->fetch()) {
            $logSet[] = array(
                'count' => $row['COUNT'],
                'line_id' => $row['LINE_ID'],
                'cart' => $row['CART_NUMBER'],
                'artist' => $row['ARTIST'],
                'title' => $row['TITLE'],
                'group' => $row['GROUP_NAME'],
                'length' => $row['AVERAGE_LENGTH'],
                'type' => $row['TYPE'],
                'comment' => $row['COMMENT'],
                'start_time' => $row['START_TIME'],
                'label' => $row['LABEL'],
                'color' => $row['COLOR'],
            );
        }


        return $logSet;

    }

    public function getRivendellLogs($service)
    {

        $logSet = array();

        $sql = 'SELECT `NAME`, `LOG_EXISTS`, `TYPE`, `SCHEDULED_TRACKS`, `COMPLETED_TRACKS`, `DESCRIPTION`, `MUSIC_LINKED`, `TRAFFIC_LINKED`, `AUTO_REFRESH` FROM `LOGS`
                WHERE `SERVICE` = :service
                ORDER BY `NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':service', $service);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $logSet[] = array(
                'name' => $row['NAME'],
                'description' => $row['DESCRIPTION'],
                'exists' => $row['LOG_EXISTS'],
                'music_merged' => $row['MUSIC_LINKED'],
                'traffic_merged' => $row['TRAFFIC_LINKED'],
                'auto_refresh' => $row['AUTO_REFRESH'],
                'type' => $row['TYPE'],
                'scheduled' => $row['SCHEDULED_TRACKS'],
                'completed' => $row['COMPLETED_TRACKS'],
                'service' => $service,
            );
        }

        return $logSet;

    }

    public function getRivendellLogsAll($username)
    {
        $logSet = array();
        $sql = 'SELECT `SERVICE_NAME` FROM `USER_SERVICE_PERMS`
                WHERE `USER_NAME` = :uname
                ORDER BY `SERVICE_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':uname', $username);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            $sql2 = 'SELECT `NAME`, `LOG_EXISTS`, `TYPE`, `SCHEDULED_TRACKS`, `COMPLETED_TRACKS`, `DESCRIPTION`, `MUSIC_LINKED`, `TRAFFIC_LINKED` FROM `LOGS`
                WHERE `SERVICE` = :service
                ORDER BY `NAME` ASC';

            $stmt = $this->_db->prepare($sql2);
            $stmt->bindParam(':service', $row['SERVICE_NAME']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            while ($row1 = $stmt->fetch()) {

                $logSet[] = array(
                    'name' => $row1['NAME'],
                    'description' => $row1['DESCRIPTION'],
                    'exists' => $row1['LOG_EXISTS'],
                    'music_merged' => $row1['MUSIC_LINKED'],
                    'traffic_merged' => $row1['TRAFFIC_LINKED'],
                    'type' => $row1['TYPE'],
                    'scheduled' => $row1['SCHEDULED_TRACKS'],
                    'completed' => $row1['COMPLETED_TRACKS'],
                    'service' => $row['SERVICE_NAME'],
                );

            }
        }
        return $logSet;

    }

    public function getUserGroup($username)
    {

        $groups = array();

        $sql = 'SELECT `GROUP_NAME` FROM `USER_PERMS`
                WHERE `USER_NAME` = :uname
                ORDER BY `GROUP_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':uname', $username);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $groups[] = $field;

        }

        $results = NULL;

        return $groups;

    }

    public function getUserService($username)
    {

        $service = array();

        $sql = 'SELECT `SERVICE_NAME` FROM `USER_SERVICE_PERMS`
                WHERE `USER_NAME` = :uname
                ORDER BY `SERVICE_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':uname', $username);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $service[] = $field;

        }

        $results = NULL;

        return $service;

    }

    public function updateMacroCart($number, $userdlogpad, $conductor, $songid, $group, $album, $year, $record, $client, $agency, $publisher, $composer, $userdef, $usagecode, $exeasy, $artist, $title, $notes)
    {

        if ($year == "") {
            $sql = 'UPDATE `CART` SET `GROUP_NAME` = :groupname, `TITLE` = :title, `ARTIST` = :artist, `ALBUM` = :album, `CONDUCTOR` = :conductor, `LABEL` = :labels, `CLIENT` = :clients, `AGENCY` = :agency, `PUBLISHER` = :publisher, `COMPOSER` = :composer, `USER_DEFINED` = :userdefined, `SONG_ID` = :songid, `USAGE_CODE` = :usagecode, `ASYNCRONOUS` = :asyncronous, `NOTES` = :notes, `USE_EVENT_LENGTH` = :evleng WHERE `NUMBER` = :numberCart';

        } else {
            $sql = 'UPDATE `CART` SET `GROUP_NAME` = :groupname, `TITLE` = :title, `ARTIST` = :artist, `ALBUM` = :album, `YEAR` = :years, `CONDUCTOR` = :conductor, `LABEL` = :labels, `CLIENT` = :clients, `AGENCY` = :agency, `PUBLISHER` = :publisher, `COMPOSER` = :composer, `USER_DEFINED` = :userdefined, `SONG_ID` = :songid, `USAGE_CODE` = :usagecode, `ASYNCRONOUS` = :asyncronous, `NOTES` = :notes, `USE_EVENT_LENGTH` = :evleng WHERE `NUMBER` = :numberCart';

        }
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':groupname', $group);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':artist', $artist);
        $stmt->bindParam(':album', $album);
        if ($year != "") {
            $stmt->bindParam(':years', $year);
        }
        $stmt->bindParam(':conductor', $conductor);
        $stmt->bindParam(':labels', $record);
        $stmt->bindParam(':clients', $client);
        $stmt->bindParam(':agency', $agency);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':composer', $composer);
        $stmt->bindParam(':userdefined', $userdef);
        $stmt->bindParam(':songid', $songid);
        $stmt->bindParam(':usagecode', $usagecode);
        $stmt->bindParam(':asyncronous', $exeasy);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':evleng', $userdlogpad);
        $stmt->bindParam(':numberCart', $number);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function updateMacro($cart, $macrostring)
    {

        $sql = 'UPDATE `CART` SET `MACROS` = :macString WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':macString', $macrostring);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function resetUsageCode($cart)
    {

        $sql = 'UPDATE `CART` SET `USAGE_CODE` = 0 WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setAUserservice($username)
    {

        $oneservice = 0;
        $sql = 'SELECT `SERVICE_NAME` FROM `USER_SERVICE_PERMS`
                WHERE `USER_NAME` = :uname
                ORDER BY `SERVICE_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':uname', $username);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $oneservice = $field;

        }

        $results = NULL;

        return $oneservice;

    }

    public function getRivUsers()
    {

        $users = array();
        $notadmin = 'N';
        $sql = 'SELECT * FROM `USERS` WHERE `ADMIN_CONFIG_PRIV` = :notadmin ORDER BY `LOGIN_NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':notadmin', $notadmin);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $users[] = $row;
        }

        return $users;

    }

    public function getRivUser($username)
    {

        $users = array();
        $notadmin = 'N';
        $sql = 'SELECT * FROM `USERS` WHERE `ADMIN_CONFIG_PRIV` = :notadmin AND `LOGIN_NAME` = :username';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':notadmin', $notadmin);
        $stmt->bindParam(':username', $username);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $users = $row;
        }

        return $users;

    }

    public function getRdAirplays()
    {
        $rdairplay = array();
        $sql = 'SELECT * FROM `RDAIRPLAY` ORDER BY `ID` ASC';
        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $rdairplay[] = $row;
        }
        return $rdairplay;
    }

    public function getServices()
    {

        $service = array();

        $sql = 'SELECT `NAME` FROM `SERVICES`
                ORDER BY `NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $service[] = $field;

        }

        $results = NULL;

        return $service;

    }

    public function getRDAirPlayData($station)
    {

        $stmt = $this->_db->prepare('SELECT * FROM RDAIRPLAY WHERE STATION = :id');
        $stmt->execute([':id' => $station]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function updateRDAirPlay($station, $mansegue, $forcsegue, $piecount, $pieto, $deftrans, $service, $syspan, $usrpan, $flash, $butpaus, $label, $timesync, $aux1, $aux2, $clear, $pauseevent, $hour, $extrabutton, $audition, $spacebar)
    {

        $sql = 'UPDATE `RDAIRPLAY` SET `SEGUE_LENGTH` = :mansegue, `TRANS_LENGTH` = :forcsegue, `PIE_COUNT_LENGTH` = :piecount, `PIE_COUNT_ENDPOINT` = :pieto,
        `DEFAULT_TRANS_TYPE` = :deftrans, `DEFAULT_SERVICE` = :services, `STATION_PANELS` = :syspan, `USER_PANELS` = :usrpan, `FLASH_PANEL` = :flash, `PANEL_PAUSE_ENABLED` = :butpaus,
        `BUTTON_LABEL_TEMPLATE` = :labels, `CHECK_TIMESYNC` = :timesync, `SHOW_AUX_1` = :aux1, `SHOW_AUX_2` = :aux2, `CLEAR_FILTER` = :clears, `PAUSE_ENABLED` = :pauseevent, `HOUR_SELECTOR_ENABLED` = :ahour,
        `SHOW_COUNTERS` = :extrabutton, `AUDITION_PREROLL` = :audition, `BAR_ACTION` = :spacebar WHERE `STATION` = :station';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':mansegue', $mansegue);
        $stmt->bindParam(':forcsegue', $forcsegue);
        $stmt->bindParam(':piecount', $piecount);
        $stmt->bindParam(':pieto', $pieto);
        $stmt->bindParam(':deftrans', $deftrans);
        $stmt->bindParam(':services', $service);
        $stmt->bindParam(':syspan', $syspan);
        $stmt->bindParam(':usrpan', $usrpan);
        $stmt->bindParam(':flash', $flash);
        $stmt->bindParam(':butpaus', $butpaus);
        $stmt->bindParam(':labels', $label);
        $stmt->bindParam(':timesync', $timesync);
        $stmt->bindParam(':aux1', $aux1);
        $stmt->bindParam(':aux2', $aux2);
        $stmt->bindParam(':clears', $clear);
        $stmt->bindParam(':pauseevent', $pauseevent);
        $stmt->bindParam(':ahour', $hour);
        $stmt->bindParam(':extrabutton', $extrabutton);
        $stmt->bindParam(':audition', $audition);
        $stmt->bindParam(':spacebar', $spacebar);
        $stmt->bindParam(':station', $station);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setConductor($cart, $conductor)
    {

        $sql = 'UPDATE `CART` SET `CONDUCTOR` = :conductor WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':conductor', $conductor);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setSongID($cart, $songid)
    {

        $sql = 'UPDATE `CART` SET `SONG_ID` = :songid WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':songid', $songid);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setGroupName($cart, $group)
    {

        $sql = 'UPDATE `CART` SET `GROUP_NAME` = :groupname WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':groupname', $group);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartTitle($cart, $title)
    {

        $sql = 'UPDATE `CART` SET `TITLE` = :title WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartArtist($cart, $artist)
    {

        $sql = 'UPDATE `CART` SET `ARTIST` = :artist WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':artist', $artist);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartAlbum($cart, $album)
    {

        $sql = 'UPDATE `CART` SET `ALBUM` = :album WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':album', $album);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartYear($cart, $year)
    {
        $dateform = $year . "-01-01";
        $sql = 'UPDATE `CART` SET `YEAR` = :years WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':years', $dateform);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartClient($cart, $client)
    {

        $sql = 'UPDATE `CART` SET `CLIENT` = :clients WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':clients', $client);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartAgency($cart, $agency)
    {

        $sql = 'UPDATE `CART` SET `AGENCY` = :agency WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':agency', $agency);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartPublisher($cart, $publisher)
    {

        $sql = 'UPDATE `CART` SET `PUBLISHER` = :publisher WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartComposer($cart, $composer)
    {

        $sql = 'UPDATE `CART` SET `COMPOSER` = :composer WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':composer', $composer);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartUserDefined($cart, $user)
    {

        $sql = 'UPDATE `CART` SET `USER_DEFINED` = :userdef WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':userdef', $user);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartRecordLabel($cart, $label)
    {

        $sql = 'UPDATE `CART` SET `LABEL` = :labels WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':labels', $label);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function setCartUsageCode($cart, $usage)
    {

        $sql = 'UPDATE `CART` SET `USAGE_CODE` = :uscode WHERE `NUMBER` = :numberCart';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':uscode', $usage);
        $stmt->bindParam(':numberCart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getRivGroups()
    {

        $groups = array();
        $sql = 'SELECT * FROM `GROUPS` ORDER BY `NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $groups[] = $row;
        }

        return $groups;

    }

    public function getRivGroup($name)
    {

        $group = array();
        $sql = 'SELECT * FROM `GROUPS` WHERE `NAME` = :thename';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thename', $name);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $group = $row;
        }

        return $group;

    }

    public function getServicesGroup($name)
    {

        $service = array();

        $sql = 'SELECT `SERVICE_NAME` FROM `AUDIO_PERMS` WHERE `GROUP_NAME` = :thename ORDER BY `SERVICE_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':thename', $name);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $service[] = $field;

        }

        $results = NULL;

        return $service;

    }

    public function clearServiceGroup($name)
    {
        $stmt1 = $this->_db->prepare('DELETE FROM AUDIO_PERMS WHERE GROUP_NAME = :id');
        $stmt1->execute([
            ':id' => $name,
        ]);

        return true;
    }

    public function removeOldServiceGroup($name, $service)
    {
        $stmt1 = $this->_db->prepare('DELETE FROM AUDIO_PERMS WHERE GROUP_NAME = :group AND SERVICE_NAME = :id');
        $stmt1->execute([
            ':group' => $name,
            ':id' => $service,
        ]);

        return true;
    }

    public function addNewServiceGroup($name, $service)
    {

        $sql = 'INSERT INTO `AUDIO_PERMS` (`GROUP_NAME`, `SERVICE_NAME`)
                VALUES (:gname, :sname)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':gname', $name);
        $stmt->bindParam(':sname', $service);

        if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
            return false;
        } else {
            return true;
        }

    }

    public function updateGroupInfo($groupid, $groupdesc, $gimport, $emailaddresses, $carttype, $cartstart, $cartend, $color, $enfcart, $inctraffic, $incmusic, $cutcreation, $purgedays, $delempty)
    {

        $sql = 'UPDATE `GROUPS` SET `DESCRIPTION` = :descript, `DEFAULT_CART_TYPE` = :carttype, `DEFAULT_LOW_CART` = :cartstart, `DEFAULT_HIGH_CART` = :cartend,
        `DEFAULT_CUT_LIFE` = :cutcreation, `CUT_SHELFLIFE` = :purgedays, `DELETE_EMPTY_CARTS` = :delempty, `DEFAULT_TITLE` = :gimport,
        `ENFORCE_CART_RANGE` = :enfcart, `REPORT_TFC` = :inctraffic, `REPORT_MUS` = :incmusic, `COLOR` = :color, `NOTIFY_EMAIL_ADDRESS` = :emailaddresses WHERE `NAME` = :thename';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':descript', $groupdesc);
        $stmt->bindParam(':carttype', $carttype);
        $stmt->bindParam(':cartstart', $cartstart);
        $stmt->bindParam(':cartend', $cartend);
        $stmt->bindParam(':cutcreation', $cutcreation);
        $stmt->bindParam(':purgedays', $purgedays);
        $stmt->bindParam(':delempty', $delempty);
        $stmt->bindParam(':gimport', $gimport);
        $stmt->bindParam(':enfcart', $enfcart);
        $stmt->bindParam(':inctraffic', $inctraffic);
        $stmt->bindParam(':incmusic', $incmusic);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':emailaddresses', $emailaddresses);
        $stmt->bindParam(':thename', $groupid);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function addGroupToUsers($name)
    {
        $notadmin = 'N';
        $sql = 'SELECT * FROM USERS WHERE `ADMIN_CONFIG_PRIV` = :notadmin';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':notadmin', $notadmin);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $totalCount = $stmt->rowCount();
        if ($totalCount > 0) {            
            while ($row = $stmt->fetch()) {
                $sql2 = 'INSERT INTO `USER_PERMS` (`USER_NAME`, `GROUP_NAME`)
                VALUES (:usrname, :groupname)';
                $stmt2 = $this->_db->prepare($sql2);
                $stmt2->bindParam(':usrname', $row['LOGIN_NAME']);
                $stmt2->bindParam(':groupname', $name);
                $stmt2->execute();
            }
            return true;
        } else {
            return false;
        }

    }

    public function addGroupInfo($groupid, $groupdesc, $gimport, $emailaddresses, $carttype, $cartstart, $cartend, $color, $enfcart, $inctraffic, $incmusic, $cutcreation, $purgedays, $delempty)
    {
        $sql = 'INSERT INTO `GROUPS` (`NAME`, `DESCRIPTION`, `DEFAULT_CART_TYPE`, `DEFAULT_LOW_CART`, `DEFAULT_HIGH_CART`, `DEFAULT_CUT_LIFE`, `CUT_SHELFLIFE`, `DELETE_EMPTY_CARTS`, `DEFAULT_TITLE`,
        `ENFORCE_CART_RANGE`, `REPORT_TFC`, `REPORT_MUS`, `COLOR`, `NOTIFY_EMAIL_ADDRESS`) VALUES (:thename, :descript, :carttype, :cartstart, :cartend, :cutcreation, :purgedays, :delempty, :gimport, :enfcart, :inctraffic, :incmusic, :color, :emailaddresses)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thename', $groupid);
        $stmt->bindParam(':descript', $groupdesc);
        $stmt->bindParam(':carttype', $carttype);
        $stmt->bindParam(':cartstart', $cartstart);
        $stmt->bindParam(':cartend', $cartend);
        $stmt->bindParam(':cutcreation', $cutcreation);
        $stmt->bindParam(':purgedays', $purgedays);
        $stmt->bindParam(':delempty', $delempty);
        $stmt->bindParam(':gimport', $gimport);
        $stmt->bindParam(':enfcart', $enfcart);
        $stmt->bindParam(':inctraffic', $inctraffic);
        $stmt->bindParam(':incmusic', $incmusic);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':emailaddresses', $emailaddresses);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getGroupNameExist($name)
    {

        $stmt = $this->_db->prepare('SELECT * FROM GROUPS WHERE NAME = :evname');
        $stmt->execute([
            ':evname' => $name
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

}