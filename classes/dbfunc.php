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

    public function renameGroup($name, $oldname)
    {

        $sql = 'UPDATE `GROUPS` SET `NAME` = :newnames WHERE `NAME` = :oldname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newnames', $name);
        $stmt->bindParam(':oldname', $oldname);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            $sql = 'UPDATE `AUDIO_PERMS` SET `GROUP_NAME` = :newnames WHERE `GROUP_NAME` = :oldname';
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':newnames', $name);
            $stmt->bindParam(':oldname', $oldname);

            if ($stmt->execute() === FALSE) {
                return false;
            } else {
                $sql = 'UPDATE `CART` SET `GROUP_NAME` = :newnames WHERE `GROUP_NAME` = :oldname';
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':newnames', $name);
                $stmt->bindParam(':oldname', $oldname);

                if ($stmt->execute() === FALSE) {
                    return false;
                } else {
                    $sql = 'UPDATE `DROPBOXES` SET `GROUP_NAME` = :newnames WHERE `GROUP_NAME` = :oldname';
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindParam(':newnames', $name);
                    $stmt->bindParam(':oldname', $oldname);

                    if ($stmt->execute() === FALSE) {
                        return false;
                    } else {
                        $sql = 'UPDATE `REPORT_GROUPS` SET `GROUP_NAME` = :newnames WHERE `GROUP_NAME` = :oldname';
                        $stmt = $this->_db->prepare($sql);
                        $stmt->bindParam(':newnames', $name);
                        $stmt->bindParam(':oldname', $oldname);

                        if ($stmt->execute() === FALSE) {
                            return false;
                        } else {
                            $sql = 'UPDATE `USER_PERMS` SET `GROUP_NAME` = :newnames WHERE `GROUP_NAME` = :oldname';
                            $stmt = $this->_db->prepare($sql);
                            $stmt->bindParam(':newnames', $name);
                            $stmt->bindParam(':oldname', $oldname);

                            if ($stmt->execute() === FALSE) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }

                }
            }
        }

    }

    public function removeGroup($name)
    {

        $sql = "DELETE FROM AUDIO_PERMS WHERE GROUP_NAME = '$name'";
        $sql2 = "DELETE FROM DROPBOXES WHERE GROUP_NAME = '$name'";
        $sql3 = "DELETE FROM USER_PERMS WHERE GROUP_NAME = '$name'";
        $sql4 = "DELETE FROM GROUPS WHERE NAME = '$name'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            if ($this->_db->query($sql2) === FALSE) {
                return false;
            } else {
                if ($this->_db->query($sql3) === FALSE) {
                    return false;
                } else {
                    if ($this->_db->query($sql4) === FALSE) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

    }

    public function checkRemoveGroupCarts($name)
    {

        $sql = 'SELECT * FROM `CART` WHERE `GROUP_NAME` = :thename';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thename', $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt->rowCount();

    }

    public function getGroupNames()
    {

        $groups = array();

        $sql = 'SELECT `NAME` FROM `GROUPS`
                ORDER BY `NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $groups[] = $field;

        }

        $results = NULL;

        return $groups;

    }

    public function clearServiceUser($name)
    {
        $stmt1 = $this->_db->prepare('DELETE FROM USER_SERVICE_PERMS WHERE USER_NAME = :id');
        $stmt1->execute([
            ':id' => $name,
        ]);

        return true;
    }

    public function clearGroupUser($name)
    {
        $stmt1 = $this->_db->prepare('DELETE FROM USER_PERMS WHERE USER_NAME = :id');
        $stmt1->execute([
            ':id' => $name,
        ]);

        return true;
    }

    public function addUserService($name, $service)
    {

        $sql = 'INSERT INTO `USER_SERVICE_PERMS` (`USER_NAME`, `SERVICE_NAME`)
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

    public function addUserGroups($name, $group)
    {

        $sql = 'INSERT INTO `USER_PERMS` (`USER_NAME`, `GROUP_NAME`)
                VALUES (:gname, :sname)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':gname', $name);
        $stmt->bindParam(':sname', $group);

        if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
            return false;
        } else {
            return true;
        }

    }

    public function getSchedCodes()
    {

        $sched = array();
        $sql = 'SELECT * FROM `SCHED_CODES` ORDER BY `CODE` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $sched[] = $row;
        }

        return $sched;

    }

    public function getSchedCode($name)
    {

        $sched = array();
        $sql = 'SELECT * FROM `SCHED_CODES` WHERE `CODE` = :thename';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thename', $name);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $sched = $row;
        }

        return $sched;

    }

    public function updateSched($schedid, $scheddesc)
    {

        $sql = 'UPDATE `SCHED_CODES` SET `DESCRIPTION` = :descript WHERE `CODE` = :thename';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':descript', $scheddesc);
        $stmt->bindParam(':thename', $schedid);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getSchedCodeExist($name)
    {

        $stmt = $this->_db->prepare('SELECT * FROM SCHED_CODES WHERE CODE = :evname');
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

    public function addSchedCode($schedid, $scheddesc)
    {
        $sql = 'INSERT INTO `SCHED_CODES` (`CODE`, `DESCRIPTION`) VALUES (:thename, :descript)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thename', $schedid);
        $stmt->bindParam(':descript', $scheddesc);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function removeSched($name)
    {

        $sql = "DELETE FROM CART_SCHED_CODES WHERE SCHED_CODE = '$name'";
        $sql2 = "DELETE FROM DROPBOX_SCHED_CODES WHERE SCHED_CODE = '$name'";
        $sql3 = "UPDATE EVENTS SET HAVE_CODE = '' WHERE `HAVE_CODE` = '$name'";
        $sql4 = "UPDATE EVENTS SET HAVE_CODE2 = '' WHERE `HAVE_CODE2` = '$name'";
        $sql5 = "DELETE FROM SCHED_CODES WHERE CODE = '$name'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            if ($this->_db->query($sql2) === FALSE) {
                return false;
            } else {
                if ($this->_db->query($sql3) === FALSE) {
                    return false;
                } else {
                    if ($this->_db->query($sql4) === FALSE) {
                        return false;
                    } else {
                        if ($this->_db->query($sql5) === FALSE) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                }
            }
        }

    }

    public function getRivHosts()
    {

        $hosts = array();
        $sql = 'SELECT * FROM `STATIONS` ORDER BY `NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $hosts[] = $row;
        }

        return $hosts;

    }

    public function getHost($name)
    {

        $host = array();
        $sql = 'SELECT * FROM `STATIONS` WHERE `NAME` = :thename';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thename', $name);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $host = $row;
        }

        return $host;

    }

    public function getUsers()
    {

        $users = array();
        $notadmin = 'N';
        $sql = 'SELECT `LOGIN_NAME` FROM `USERS` WHERE `ADMIN_CONFIG_PRIV` = :notadmin
                ORDER BY `LOGIN_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':notadmin', $notadmin);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $users[] = $field;

        }

        $results = NULL;

        return $users;

    }

    public function updateHost($hostname, $shortname, $description, $defuser, $ipaddress, $report, $web)
    {

        $sql = 'UPDATE `STATIONS` SET `SHORT_NAME` = :short, `DESCRIPTION` = :descri, `DEFAULT_NAME` = :defname, `IPV4_ADDRESS` = :ipv4, `REPORT_EDITOR_PATH` = :report, `BROWSER_PATH` = :browspath WHERE `NAME` = :thename';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':short', $shortname);
        $stmt->bindParam(':descri', $description);
        $stmt->bindParam(':defname', $defuser);
        $stmt->bindParam(':ipv4', $ipaddress);
        $stmt->bindParam(':report', $report);
        $stmt->bindParam(':browspath', $web);
        $stmt->bindParam(':thename', $hostname);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getRivServices()
    {
        $services = array();
        $sql = 'SELECT * FROM `SERVICES` ORDER BY `NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $services[] = $row;
        }

        return $services;

    }

    public function getAutoFills($service)
    {
        $autoSet = array();
        $sql = 'SELECT * FROM `AUTOFILLS`
                WHERE `SERVICE` = :uname
                ORDER BY `CART_NUMBER` ASC';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':uname', $service);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            $sql2 = 'SELECT * FROM `CART`
                WHERE `NUMBER` = :service
                ORDER BY `NUMBER` ASC';

            $stmt = $this->_db->prepare($sql2);
            $stmt->bindParam(':service', $row['CART_NUMBER']);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            while ($row1 = $stmt->fetch()) {

                $autoSet[] = array(
                    'ID' => $row['ID'],
                    'NUMBER' => $row1['NUMBER'],
                    'TYPE' => $row1['TYPE'],
                    'GROUP_NAME' => $row1['GROUP_NAME'],
                    'TITLE' => $row1['TITLE'],
                    'ARTIST' => $row1['ARTIST'],
                    'AVERAGE_LENGTH' => $row1['AVERAGE_LENGTH'],
                    'FORCED_LENGTH' => $row1['FORCED_LENGTH'],
                    'SERVICE' => $row['SERVICE'],
                );

            }
        }
        return $autoSet;

    }

    public function getImpTemp()
    {

        $temp = array();
        $sql = 'SELECT * FROM `IMPORT_TEMPLATES` ORDER BY `NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {

            $temp[] = $row;
        }

        return $temp;

    }


    public function getImpTempImp($template)
    {

        $stmt = $this->_db->prepare('SELECT * FROM IMPORT_TEMPLATES WHERE NAME = :evname');
        $stmt->execute([
            ':evname' => $template,
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function getServiceData($service)
    {

        $stmt = $this->_db->prepare('SELECT * FROM SERVICES WHERE NAME = :evname');
        $stmt->execute([
            ':evname' => $service,
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function copyToCustom($service, $type, $temp)
    {
        $autoSet = array();
        $sql = 'SELECT * FROM `IMPORT_TEMPLATES`
                WHERE `NAME` = :uname';

        $results = $this->_db->prepare($sql);
        $results->bindParam(':uname', $temp);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        $row = $results->fetch();

        if ($type == 1) {

            $sql2 = 'UPDATE `SERVICES` SET `TFC_CART_OFFSET` = :coff, `TFC_CART_LENGTH` = :cleng, `TFC_TITLE_OFFSET` = :tittof, `TFC_TITLE_LENGTH` = :titleng, 
        `TFC_HOURS_OFFSET` = :hoff, `TFC_HOURS_LENGTH` = :hleng, `TFC_MINUTES_OFFSET` = :moff, `TFC_MINUTES_LENGTH` = :mleng, `TFC_SECONDS_OFFSET` = :soff, `TFC_SECONDS_LENGTH` = :sleng,
        `TFC_LEN_HOURS_OFFSET` = :lhoff, `TFC_LEN_HOURS_LENGTH` = :lhleng, `TFC_LEN_MINUTES_OFFSET` = :lmoff, `TFC_LEN_MINUTES_LENGTH` = :lmleng,
        `TFC_LEN_SECONDS_OFFSET` = :lsoff, `TFC_LEN_SECONDS_LENGTH` = :lsleng, `TFC_DATA_OFFSET` = :datoff, `TFC_DATA_LENGTH` = :datleng,
        `TFC_EVENT_ID_OFFSET` = :evoff, `TFC_EVENT_ID_LENGTH` = :evleng, `TFC_ANNC_TYPE_OFFSET` = :ancoff, `TFC_ANNC_TYPE_LENGTH` = :ancleng WHERE `NAME` = :thename';
            $stmt = $this->_db->prepare($sql2);
            $stmt->bindParam(':coff', $row['CART_OFFSET']);
            $stmt->bindParam(':cleng', $row['CART_LENGTH']);
            $stmt->bindParam(':tittof', $row['TITLE_OFFSET']);
            $stmt->bindParam(':titleng', $row['TITLE_LENGTH']);
            $stmt->bindParam(':hoff', $row['HOURS_OFFSET']);
            $stmt->bindParam(':hleng', $row['HOURS_LENGTH']);
            $stmt->bindParam(':moff', $row['MINUTES_OFFSET']);
            $stmt->bindParam(':mleng', $row['MINUTES_LENGTH']);
            $stmt->bindParam(':soff', $row['SECONDS_OFFSET']);
            $stmt->bindParam(':sleng', $row['SECONDS_LENGTH']);
            $stmt->bindParam(':lhoff', $row['LEN_HOURS_OFFSET']);
            $stmt->bindParam(':lhleng', $row['LEN_HOURS_LENGTH']);
            $stmt->bindParam(':lmoff', $row['LEN_MINUTES_OFFSET']);
            $stmt->bindParam(':lmleng', $row['LEN_MINUTES_LENGTH']);
            $stmt->bindParam(':lsoff', $row['LEN_SECONDS_OFFSET']);
            $stmt->bindParam(':lsleng', $row['LEN_SECONDS_LENGTH']);
            $stmt->bindParam(':datoff', $row['DATA_OFFSET']);
            $stmt->bindParam(':datleng', $row['DATA_LENGTH']);
            $stmt->bindParam(':evoff', $row['EVENT_ID_OFFSET']);
            $stmt->bindParam(':evleng', $row['EVENT_ID_LENGTH']);
            $stmt->bindParam(':ancoff', $row['ANNC_TYPE_OFFSET']);
            $stmt->bindParam(':ancleng', $row['ANNC_TYPE_LENGTH']);
            $stmt->bindParam(':thename', $service);

            if ($stmt->execute() === FALSE) {
                return false;
            } else {
                return true;
            }

        } else {
            $sql2 = 'UPDATE `SERVICES` SET `MUS_CART_OFFSET` = :coff, `MUS_CART_LENGTH` = :cleng, `MUS_TITLE_OFFSET` = :tittof, `MUS_TITLE_LENGTH` = :titleng, 
        `MUS_HOURS_OFFSET` = :hoff, `MUS_HOURS_LENGTH` = :hleng, `MUS_MINUTES_OFFSET` = :moff, `MUS_MINUTES_LENGTH` = :mleng, `MUS_SECONDS_OFFSET` = :soff, `MUS_SECONDS_LENGTH` = :sleng,
        `MUS_LEN_HOURS_OFFSET` = :lhoff, `MUS_LEN_HOURS_LENGTH` = :lhleng, `MUS_LEN_MINUTES_OFFSET` = :lmoff, `MUS_LEN_MINUTES_LENGTH` = :lmleng,
        `MUS_LEN_SECONDS_OFFSET` = :lsoff, `MUS_LEN_SECONDS_LENGTH` = :lsleng, `MUS_DATA_OFFSET` = :datoff, `MUS_DATA_LENGTH` = :datleng,
        `MUS_EVENT_ID_OFFSET` = :evoff, `MUS_EVENT_ID_LENGTH` = :evleng, `MUS_ANNC_TYPE_OFFSET` = :ancoff, `MUS_ANNC_TYPE_LENGTH` = :ancleng,
        `MUS_TRANS_TYPE_OFFSET` = :transoff, `MUS_TRANS_TYPE_LENGTH` = :transleng, `MUS_TIME_TYPE_OFFSET` = :timeoff, `MUS_TIME_TYPE_LENGTH` = :timeleng WHERE `NAME` = :thename';
            $stmt = $this->_db->prepare($sql2);
            $stmt->bindParam(':coff', $row['CART_OFFSET']);
            $stmt->bindParam(':cleng', $row['CART_LENGTH']);
            $stmt->bindParam(':tittof', $row['TITLE_OFFSET']);
            $stmt->bindParam(':titleng', $row['TITLE_LENGTH']);
            $stmt->bindParam(':hoff', $row['HOURS_OFFSET']);
            $stmt->bindParam(':hleng', $row['HOURS_LENGTH']);
            $stmt->bindParam(':moff', $row['MINUTES_OFFSET']);
            $stmt->bindParam(':mleng', $row['MINUTES_LENGTH']);
            $stmt->bindParam(':soff', $row['SECONDS_OFFSET']);
            $stmt->bindParam(':sleng', $row['SECONDS_LENGTH']);
            $stmt->bindParam(':lhoff', $row['LEN_HOURS_OFFSET']);
            $stmt->bindParam(':lhleng', $row['LEN_HOURS_LENGTH']);
            $stmt->bindParam(':lmoff', $row['LEN_MINUTES_OFFSET']);
            $stmt->bindParam(':lmleng', $row['LEN_MINUTES_LENGTH']);
            $stmt->bindParam(':lsoff', $row['LEN_SECONDS_OFFSET']);
            $stmt->bindParam(':lsleng', $row['LEN_SECONDS_LENGTH']);
            $stmt->bindParam(':datoff', $row['DATA_OFFSET']);
            $stmt->bindParam(':datleng', $row['DATA_LENGTH']);
            $stmt->bindParam(':evoff', $row['EVENT_ID_OFFSET']);
            $stmt->bindParam(':evleng', $row['EVENT_ID_LENGTH']);
            $stmt->bindParam(':ancoff', $row['ANNC_TYPE_OFFSET']);
            $stmt->bindParam(':ancleng', $row['ANNC_TYPE_LENGTH']);
            $stmt->bindParam(':transoff', $row['TRANS_TYPE_OFFSET']);
            $stmt->bindParam(':transleng', $row['TRANS_TYPE_LENGTH']);
            $stmt->bindParam(':timeoff', $row['TIME_TYPE_OFFSET']);
            $stmt->bindParam(':timeleng', $row['TIME_TYPE_LENGTH']);
            $stmt->bindParam(':thename', $service);

            if ($stmt->execute() === FALSE) {
                return false;
            } else {
                return true;
            }

        }

    }

    public function addAutofill($cart, $service)
    {
        $sql = 'INSERT INTO `AUTOFILLS` (`SERVICE`, `CART_NUMBER`) VALUES (:services, :cart)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':services', $service);
        $stmt->bindParam(':cart', $cart);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function removeAutofill($id)
    {

        $sql = "DELETE FROM AUTOFILLS WHERE ID = '$id'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function updateService($service, $descr, $programc, $lognametemp, $logdesctemp, $bypass, $inline, $vtgroup, $autospot, $chainto, $autorefresh, $autodelete, $daysaftertype, $purgeelr, $musimpmark, $trafimpmark, $importpath, $preimpcom, $insertmarkerstring, $insertvtstring, $imptemplate, $importpath_mus, $preimpcom_mus, $insertmarkerstring_mus, $insertvtstring_mus, $imptemplate_mus, $instraficbreak_mus)
    {
        if ($imptemplate == 'cust') {
            $imptemplate = "";
        }
        if ($imptemplate_mus == 'cust') {
            $imptemplate_mus = "";
        }
        $sql2 = 'UPDATE `SERVICES` SET `DESCRIPTION` = :descr, `PROGRAM_CODE` = :programc, `NAME_TEMPLATE` = :lognametemp, `DESCRIPTION_TEMPLATE` = :logdesctemp, 
        `BYPASS_MODE` = :bypass, `SUB_EVENT_INHERITANCE` = :inline, `TRACK_GROUP` = :vtgroup, `AUTOSPOT_GROUP` = :autospot, `CHAIN_LOG` = :chainto, `AUTO_REFRESH` = :autorefresh,
        `DEFAULT_LOG_SHELFLIFE` = :autodelete, `LOG_SHELFLIFE_ORIGIN` = :daysaftertype, `ELR_SHELFLIFE` = :purgeelr, `INCLUDE_MUS_IMPORT_MARKERS` = :musimpmark,
        `INCLUDE_TFC_IMPORT_MARKERS` = :trafimpmark, `TFC_PATH` = :importpath, `TFC_PREIMPORT_CMD` = :preimpcom, `TFC_LABEL_CART` = :insertmarkerstring,
        `TFC_TRACK_STRING` = :insertvtstring, `TFC_IMPORT_TEMPLATE` = :imptemplate, `MUS_PATH` = :importpath_mus, `MUS_PREIMPORT_CMD` = :preimpcom_mus,
        `MUS_LABEL_CART` = :insertmarkerstring_mus, `MUS_TRACK_STRING` = :insertvtstring_mus, `MUS_BREAK_STRING` = :instraficbreak_mus, `MUS_IMPORT_TEMPLATE` = :imptemplate_mus WHERE `NAME` = :thename';
        $stmt = $this->_db->prepare($sql2);
        $stmt->bindParam(':descr', $descr);
        $stmt->bindParam(':programc', $programc);
        $stmt->bindParam(':lognametemp', $lognametemp);
        $stmt->bindParam(':logdesctemp', $logdesctemp);
        $stmt->bindParam(':bypass', $bypass);
        $stmt->bindParam(':inline', $inline);
        $stmt->bindParam(':vtgroup', $vtgroup);
        $stmt->bindParam(':autospot', $autospot);
        $stmt->bindParam(':chainto', $chainto);
        $stmt->bindParam(':autorefresh', $autorefresh);
        $stmt->bindParam(':autodelete', $autodelete);
        $stmt->bindParam(':daysaftertype', $daysaftertype);
        $stmt->bindParam(':purgeelr', $purgeelr);
        $stmt->bindParam(':musimpmark', $musimpmark);
        $stmt->bindParam(':trafimpmark', $trafimpmark);
        $stmt->bindParam(':importpath', $importpath);
        $stmt->bindParam(':preimpcom', $preimpcom);
        $stmt->bindParam(':insertmarkerstring', $insertmarkerstring);
        $stmt->bindParam(':insertvtstring', $insertvtstring);
        $stmt->bindParam(':imptemplate', $imptemplate);
        $stmt->bindParam(':importpath_mus', $importpath_mus);
        $stmt->bindParam(':preimpcom_mus', $preimpcom_mus);
        $stmt->bindParam(':insertmarkerstring_mus', $insertmarkerstring_mus);
        $stmt->bindParam(':insertvtstring_mus', $insertvtstring_mus);
        $stmt->bindParam(':instraficbreak_mus', $instraficbreak_mus);
        $stmt->bindParam(':imptemplate_mus', $imptemplate_mus);
        $stmt->bindParam(':thename', $service);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function removeAllServiceHost($service)
    {

        $sql = "DELETE FROM SERVICE_PERMS WHERE SERVICE_NAME = '$service'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function removeServiceHost($host, $service)
    {

        $sql = "DELETE FROM SERVICE_PERMS WHERE STATION_NAME = '$host' AND SERVICE_NAME = '$service'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function AddServiceHost($host, $service)
    {
        try {

            $sql = "INSERT INTO SERVICE_PERMS (STATION_NAME, SERVICE_NAME) VALUES (:stat, :serv)";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':stat' => $host,
                ':serv' => $service,
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }

    }

    public function updateTFCData($service, $tfccartof, $tfccartlength, $tfctitof, $tfctitlength, $tfchourof, $tfchourslength, $tfcminof, $tfcminlength, $tfcsecof, $tfcseclength, $tfclenhoof, $tfcleholength, $tfclenminof, $tfcleminlength, $tfclensecof, $tfcleseclength, $tfcdataof, $tfcdatalength, $tfceventof, $tfceventlength, $tfcanncof, $tfcannclength)
    {

        try {

            $sql = "UPDATE SERVICES SET TFC_CART_OFFSET = :coff, TFC_CART_LENGTH = :cleng, TFC_TITLE_OFFSET = :tittof, TFC_TITLE_LENGTH = :titleng, 
            TFC_HOURS_OFFSET = :hoff, TFC_HOURS_LENGTH = :hleng, TFC_MINUTES_OFFSET = :moff, TFC_MINUTES_LENGTH = :mleng, TFC_SECONDS_OFFSET = :soff, TFC_SECONDS_LENGTH = :sleng,
            TFC_LEN_HOURS_OFFSET = :lhoff, TFC_LEN_HOURS_LENGTH = :lhleng, TFC_LEN_MINUTES_OFFSET = :lmoff, TFC_LEN_MINUTES_LENGTH = :lmleng,
            TFC_LEN_SECONDS_OFFSET = :lsoff, TFC_LEN_SECONDS_LENGTH = :lsleng, TFC_DATA_OFFSET = :datoff, TFC_DATA_LENGTH = :datleng,
            TFC_EVENT_ID_OFFSET = :evoff, TFC_EVENT_ID_LENGTH = :evleng, TFC_ANNC_TYPE_OFFSET = :ancoff, TFC_ANNC_TYPE_LENGTH = :ancleng WHERE NAME = :thename";

            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':coff' => $tfccartof,
                ':cleng' => $tfccartlength,
                ':tittof' => $tfctitof,
                ':titleng' => $tfctitlength,
                ':hoff' => $tfchourof,
                ':hleng' => $tfchourslength,
                ':moff' => $tfcminof,
                ':mleng' => $tfcminlength,
                ':soff' => $tfcsecof,
                ':sleng' => $tfcseclength,
                ':lhoff' => $tfclenhoof,
                ':lhleng' => $tfcleholength,
                ':lmoff' => $tfclenminof,
                ':lmleng' => $tfcminof,
                ':lsoff' => $tfclensecof,
                ':lsleng' => $tfcleseclength,
                ':datoff' => $tfcdataof,
                ':datleng' => $tfcdatalength,
                ':evoff' => $tfceventof,
                ':evleng' => $tfceventlength,
                ':ancoff' => $tfcanncof,
                ':ancleng' => $tfcannclength,
                ':thename' => $service,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateMUSData($service, $muscartof, $muscartlength, $mustitof, $mustitlength, $mushourof, $mushourslength, $musminof, $musminlength, $mussecof, $musseclength, $muslenhoof, $musleholength, $muslenminof, $musleminlength, $muslensecof, $musleseclength, $musdataof, $musdatalength, $museventof, $museventlength, $musanncof, $musannclength, $mustransof, $mustranslength, $mustimeof, $mustimelength)
    {

        try {

            $sql = "UPDATE SERVICES SET MUS_CART_OFFSET = :coff, MUS_CART_LENGTH = :cleng, MUS_TITLE_OFFSET = :tittof, MUS_TITLE_LENGTH = :titleng, 
            MUS_HOURS_OFFSET = :hoff, MUS_HOURS_LENGTH = :hleng, MUS_MINUTES_OFFSET = :moff, MUS_MINUTES_LENGTH = :mleng, MUS_SECONDS_OFFSET = :soff, MUS_SECONDS_LENGTH = :sleng,
            MUS_LEN_HOURS_OFFSET = :lhoff, MUS_LEN_HOURS_LENGTH = :lhleng, MUS_LEN_MINUTES_OFFSET = :lmoff, MUS_LEN_MINUTES_LENGTH = :lmleng,
            MUS_LEN_SECONDS_OFFSET = :lsoff, MUS_LEN_SECONDS_LENGTH = :lsleng, MUS_DATA_OFFSET = :datoff, MUS_DATA_LENGTH = :datleng,
            MUS_EVENT_ID_OFFSET = :evoff, MUS_EVENT_ID_LENGTH = :evleng, MUS_ANNC_TYPE_OFFSET = :ancoff, MUS_ANNC_TYPE_LENGTH = :ancleng,
            MUS_TRANS_TYPE_OFFSET = :transoff, MUS_TRANS_TYPE_LENGTH = :transleng, MUS_TIME_TYPE_OFFSET = :timeoff, MUS_TIME_TYPE_LENGTH = :timeleng WHERE NAME = :thename";

            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':coff' => $muscartof,
                ':cleng' => $muscartlength,
                ':tittof' => $mustitof,
                ':titleng' => $mustitlength,
                ':hoff' => $mushourof,
                ':hleng' => $mushourslength,
                ':moff' => $musminof,
                ':mleng' => $musminlength,
                ':soff' => $mussecof,
                ':sleng' => $musseclength,
                ':lhoff' => $muslenhoof,
                ':lhleng' => $musleholength,
                ':lmoff' => $muslenminof,
                ':lmleng' => $musleminlength,
                ':lsoff' => $muslensecof,
                ':lsleng' => $musleseclength,
                ':datoff' => $musdataof,
                ':datleng' => $musdatalength,
                ':evoff' => $museventof,
                ':evleng' => $museventlength,
                ':ancoff' => $musanncof,
                ':ancleng' => $musannclength,
                ':transoff' => $mustransof,
                ':transleng' => $mustranslength,
                ':timeoff' => $mustimeof,
                ':timeleng' => $mustimelength,
                ':thename' => $service,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getServiceNameNew($service)
    {

        $stmt = $this->_db->prepare('SELECT * FROM SERVICES WHERE NAME = :servname');
        $stmt->execute([
            ':servname' => $service
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

    public function copyAutoFill($service, $copy)
    {
        try {
            $sql = "INSERT INTO AUTOFILLS (SERVICE, CART_NUMBER)
        VALUES (:servicename, :cartnumber)";

            $stmt = $this->_db->prepare('SELECT * FROM AUTOFILLS WHERE SERVICE = :thename');
            $stmt->execute([
                ':thename' => $copy
            ]);
            $result = $stmt->fetchAll();
            foreach ($result as $row) {

                $stmt2 = $this->_db->prepare($sql);
                $stmt2->execute([
                    ':servicename' => $service,
                    ':cartnumber' => $row['CART_NUMBER'],
                ]);

            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function copyStation($service, $copy)
    {
        try {
            $sql = "INSERT INTO SERVICE_PERMS (STATION_NAME, SERVICE_NAME)
            VALUES (:statname, :servicename)";
            $stmt = $this->_db->prepare('SELECT * FROM SERVICE_PERMS WHERE SERVICE_NAME = :thename');
            $stmt->execute([
                ':thename' => $copy
            ]);
            $result = $stmt->fetchAll();
            foreach ($result as $row) {

                $stmt2 = $this->_db->prepare($sql);
                $stmt2->execute([
                    ':statname' => $row['STATION_NAME'],
                    ':servicename' => $service,
                ]);

            }

            return true;
        } catch (PDOException $e) {
            return false;
        }

    }

    public function addEmptyService($name, $logtemp, $logtempdescvar)
    {
        try {
            $sql = "INSERT INTO SERVICES (NAME, BYPASS_MODE, NAME_TEMPLATE, DESCRIPTION_TEMPLATE, CHAIN_LOG, SUB_EVENT_INHERITANCE, AUTO_REFRESH, DEFAULT_LOG_SHELFLIFE, LOG_SHELFLIFE_ORIGIN, ELR_SHELFLIFE, INCLUDE_IMPORT_MARKERS, INCLUDE_MUS_IMPORT_MARKERS, INCLUDE_TFC_IMPORT_MARKERS, TFC_IMPORT_TEMPLATE, MUS_IMPORT_TEMPLATE)
            VALUES (:thename, :bypass, :nametemp, :desctemp, :chainlog, :sub, :autoref, :deflshe, :logshelforg, :elr, :inimp, :inmus, :intfc, :tfcimp, :musimp)";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':thename' => $name,
                ':bypass' => 'N',
                ':nametemp' => $logtemp,
                ':desctemp' => $logtempdescvar,
                ':chainlog' => 'N',
                ':sub' => '0',
                ':autoref' => 'N',
                ':deflshe' => '-1',
                ':logshelforg' => '0',
                ':elr' => '-1',
                ':inimp' => 'Y',
                ':inmus' => 'Y',
                ':intfc' => 'Y',
                ':tfcimp' => 'Rivendell Standard Import',
                ':musimp' => 'Rivendell Standard Import',
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function notEmpty($value)
    {
        if (isset($value) && !empty($value) && $value != "") {
            return $value;
        } else {
            return null;
        }
    }

    public function copyNewService($name, $copyfrom, $logcopydesc)
    {
        try {
            $sql2 = "INSERT INTO SERVICES (NAME, DESCRIPTION, BYPASS_MODE, NAME_TEMPLATE, DESCRIPTION_TEMPLATE, PROGRAM_CODE, CHAIN_LOG, SUB_EVENT_INHERITANCE, TRACK_GROUP, AUTOSPOT_GROUP, AUTO_REFRESH, DEFAULT_LOG_SHELFLIFE, LOG_SHELFLIFE_ORIGIN, ELR_SHELFLIFE, INCLUDE_IMPORT_MARKERS, INCLUDE_MUS_IMPORT_MARKERS, INCLUDE_TFC_IMPORT_MARKERS, 
            TFC_PATH, TFC_PREIMPORT_CMD, TFC_IMPORT_TEMPLATE, TFC_LABEL_CART, TFC_TRACK_CART, TFC_BREAK_STRING, TFC_TRACK_STRING, TFC_CART_OFFSET, TFC_CART_LENGTH, TFC_TITLE_OFFSET, TFC_TITLE_LENGTH, 
            TFC_HOURS_OFFSET, TFC_HOURS_LENGTH, TFC_MINUTES_OFFSET, TFC_MINUTES_LENGTH, TFC_SECONDS_OFFSET, TFC_SECONDS_LENGTH, TFC_LEN_HOURS_OFFSET, TFC_LEN_HOURS_LENGTH, TFC_LEN_MINUTES_OFFSET, TFC_LEN_MINUTES_LENGTH,
            TFC_LEN_SECONDS_OFFSET, TFC_LEN_SECONDS_LENGTH, TFC_DATA_OFFSET, TFC_DATA_LENGTH, TFC_EVENT_ID_OFFSET, TFC_EVENT_ID_LENGTH, TFC_ANNC_TYPE_OFFSET, TFC_ANNC_TYPE_LENGTH, 
            MUS_PATH, MUS_PREIMPORT_CMD, MUS_IMPORT_TEMPLATE, MUS_LABEL_CART, MUS_TRACK_CART, MUS_BREAK_STRING, MUS_TRACK_STRING, MUS_CART_OFFSET, MUS_CART_LENGTH, MUS_TITLE_OFFSET, MUS_TITLE_LENGTH, 
            MUS_HOURS_OFFSET, MUS_HOURS_LENGTH, MUS_MINUTES_OFFSET, MUS_MINUTES_LENGTH, MUS_SECONDS_OFFSET, MUS_SECONDS_LENGTH, MUS_LEN_HOURS_OFFSET, MUS_LEN_HOURS_LENGTH, MUS_LEN_MINUTES_OFFSET, MUS_LEN_MINUTES_LENGTH,
            MUS_LEN_SECONDS_OFFSET, MUS_LEN_SECONDS_LENGTH, MUS_DATA_OFFSET, MUS_DATA_LENGTH, MUS_EVENT_ID_OFFSET, MUS_EVENT_ID_LENGTH, MUS_ANNC_TYPE_OFFSET, MUS_ANNC_TYPE_LENGTH,
            MUS_TRANS_TYPE_OFFSET, MUS_TRANS_TYPE_LENGTH, MUS_TIME_TYPE_OFFSET, MUS_TIME_TYPE_LENGTH)
            VALUES (:thename, :thedesc, :bypass, :nametemp, :desctemp, :progcode, :chainlog, :sub, :trackgr, :autospotgr, :autoref, :deflshe, :logshelforg, :elr, :inimp, :inmus, :intfc, 
            :tfcpath, :tfcpreimp, :tfcimp, :tfclabelcart, :tfctrackc, :tfcbreaks, :tfctracks, :tfccartof, :tfccartlen, :tfctitof, :tfctitleng, :tfchoff, :tfchleng, :tfcminoff, :tfcminleng, :tfcsecoff,
            :tfcsecleng, :tfclenhooff, :tfclenholeng, :tfclenminof, :tfclenminleng, :tfclensecof, :tfclensecleng, :tfcdataof, :tfcdataleng, :tfcevidof, :tfcevidleng, :tfcannof, :tfcannleng, :muspath, :muspreimp, :musimp,
            :muslabcart, :mustrackcart, :musbreak, :mustrackst, :muscartof, :muscartlen, :mustitof, :mustitleng, :mushoff, :mushleng, :musminoff, :musminleng, :mussecoff,
            :mussecleng, :muslenhooff, :muslenholeng, :muslenminof, :muslenminleng, :muslensecof, :muslensecleng, :musdataof, :musdataleng, :musevidof, :musevidleng, :musannof, :musannleng, :mustransoff, :mustransleng, :mustimeoff, :mustimeleng)";

            $stmt = $this->_db->prepare('SELECT * FROM SERVICES WHERE NAME = :thename');
            $stmt->execute([
                ':thename' => $copyfrom
            ]);
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                $stmt2 = $this->_db->prepare($sql2);
                $stmt2->execute([
                    ':thename' => $name,
                    ':thedesc' => $logcopydesc,
                    ':bypass' => $row['BYPASS_MODE'],
                    ':nametemp' => $this->notEmpty($row['NAME_TEMPLATE']),
                    ':desctemp' => $this->notEmpty($row['DESCRIPTION_TEMPLATE']),
                    ':progcode' => $this->notEmpty($row['PROGRAM_CODE']),
                    ':chainlog' => $row['CHAIN_LOG'],
                    ':sub' => $row['SUB_EVENT_INHERITANCE'],
                    ':trackgr' => $this->notEmpty($row['TRACK_GROUP']),
                    ':autospotgr' => $this->notEmpty($row['AUTOSPOT_GROUP']),
                    ':autoref' => $row['AUTO_REFRESH'],
                    ':deflshe' => $this->notEmpty($row['DEFAULT_LOG_SHELFLIFE']),
                    ':logshelforg' => $this->notEmpty($row['LOG_SHELFLIFE_ORIGIN']),
                    ':elr' => $this->notEmpty($row['ELR_SHELFLIFE']),
                    ':inimp' => $row['INCLUDE_IMPORT_MARKERS'],
                    ':inmus' => $row['INCLUDE_MUS_IMPORT_MARKERS'],
                    ':intfc' => $this->notEmpty($row['INCLUDE_TFC_IMPORT_MARKERS']),
                    ':tfcpath' => $this->notEmpty($row['TFC_PATH']),
                    ':tfcpreimp' => $this->notEmpty($row['TFC_PREIMPORT_CMD']),
                    ':tfcimp' => $this->notEmpty($row['TFC_IMPORT_TEMPLATE']),
                    ':tfclabelcart' => $this->notEmpty($row['TFC_LABEL_CART']),
                    ':tfctrackc' => $this->notEmpty($row['TFC_TRACK_CART']),
                    ':tfcbreaks' => $this->notEmpty($row['TFC_BREAK_STRING']),
                    ':tfctracks' => $this->notEmpty($row['TFC_TRACK_STRING']),
                    ':tfccartof' => $this->notEmpty($row['TFC_CART_OFFSET']),
                    ':tfccartlen' => $this->notEmpty($row['TFC_CART_LENGTH']),
                    ':tfctitof' => $this->notEmpty($row['TFC_TITLE_OFFSET']),
                    ':tfctitleng' => $this->notEmpty($row['TFC_TITLE_LENGTH']),
                    ':tfchoff' => $this->notEmpty($row['TFC_HOURS_OFFSET']),
                    ':tfchleng' => $this->notEmpty($row['TFC_HOURS_LENGTH']),
                    ':tfcminoff' => $this->notEmpty($row['TFC_MINUTES_OFFSET']),
                    ':tfcminleng' => $this->notEmpty($row['TFC_MINUTES_LENGTH']),
                    ':tfcsecoff' => $this->notEmpty($row['TFC_SECONDS_OFFSET']),
                    ':tfcsecleng' => $this->notEmpty($row['TFC_SECONDS_LENGTH']),
                    ':tfclenhooff' => $this->notEmpty($row['TFC_LEN_HOURS_OFFSET']),
                    ':tfclenholeng' => $this->notEmpty($row['TFC_LEN_HOURS_LENGTH']),
                    ':tfclenminof' => $this->notEmpty($row['TFC_LEN_MINUTES_OFFSET']),
                    ':tfclenminleng' => $this->notEmpty($row['TFC_LEN_MINUTES_LENGTH']),
                    ':tfclensecof' => $this->notEmpty($row['TFC_LEN_SECONDS_OFFSET']),
                    ':tfclensecleng' => $this->notEmpty($row['TFC_LEN_SECONDS_LENGTH']),
                    ':tfcdataof' => $this->notEmpty($row['TFC_DATA_OFFSET']),
                    ':tfcdataleng' => $this->notEmpty($row['TFC_DATA_LENGTH']),
                    ':tfcevidof' => $this->notEmpty($row['TFC_EVENT_ID_OFFSET']),
                    ':tfcevidleng' => $this->notEmpty($row['TFC_EVENT_ID_LENGTH']),
                    ':tfcannof' => $this->notEmpty($row['TFC_ANNC_TYPE_OFFSET']),
                    ':tfcannleng' => $this->notEmpty($row['TFC_ANNC_TYPE_LENGTH']),
                    ':muspath' => $this->notEmpty($row['MUS_PATH']),
                    ':muspreimp' => $this->notEmpty($row['MUS_PREIMPORT_CMD']),
                    ':musimp' => $this->notEmpty($row['MUS_IMPORT_TEMPLATE']),
                    ':muslabcart' => $this->notEmpty($row['MUS_LABEL_CART']),
                    ':mustrackcart' => $this->notEmpty($row['MUS_TRACK_CART']),
                    ':musbreak' => $this->notEmpty($row['MUS_BREAK_STRING']),
                    ':mustrackst' => $this->notEmpty($row['MUS_TRACK_STRING']),
                    ':muscartof' => $this->notEmpty($row['MUS_TRACK_STRING']),
                    ':muscartlen' => $this->notEmpty($row['MUS_CART_LENGTH']),
                    ':mustitof' => $this->notEmpty($row['MUS_TITLE_OFFSET']),
                    ':mustitleng' => $this->notEmpty($row['MUS_TITLE_LENGTH']),
                    ':mushoff' => $this->notEmpty($row['MUS_HOURS_OFFSET']),
                    ':mushleng' => $this->notEmpty($row['MUS_HOURS_LENGTH']),
                    ':musminoff' => $this->notEmpty($row['MUS_MINUTES_OFFSET']),
                    ':musminleng' => $this->notEmpty($row['MUS_MINUTES_LENGTH']),
                    ':mussecoff' => $this->notEmpty($row['MUS_SECONDS_OFFSET']),
                    ':mussecleng' => $this->notEmpty($row['MUS_SECONDS_LENGTH']),
                    ':muslenhooff' => $this->notEmpty($row['MUS_LEN_HOURS_OFFSET']),
                    ':muslenholeng' => $this->notEmpty($row['MUS_LEN_HOURS_LENGTH']),
                    ':muslenminof' => $this->notEmpty($row['MUS_LEN_MINUTES_OFFSET']),
                    ':muslenminleng' => $this->notEmpty($row['MUS_LEN_MINUTES_LENGTH']),
                    ':muslensecof' => $this->notEmpty($row['MUS_LEN_SECONDS_OFFSET']),
                    ':muslensecleng' => $this->notEmpty($row['MUS_LEN_SECONDS_LENGTH']),
                    ':musdataof' => $this->notEmpty($row['MUS_DATA_OFFSET']),
                    ':musdataleng' => $this->notEmpty($row['MUS_DATA_LENGTH']),
                    ':musevidof' => $this->notEmpty($row['MUS_EVENT_ID_OFFSET']),
                    ':musevidleng' => $this->notEmpty($row['MUS_EVENT_ID_LENGTH']),
                    ':musannof' => $this->notEmpty($row['MUS_ANNC_TYPE_OFFSET']),
                    ':musannleng' => $this->notEmpty($row['MUS_ANNC_TYPE_LENGTH']),
                    ':mustransoff' => $this->notEmpty($row['MUS_TRANS_TYPE_OFFSET']),
                    ':mustransleng' => $this->notEmpty($row['MUS_TRANS_TYPE_LENGTH']),
                    ':mustimeoff' => $this->notEmpty($row['MUS_TIME_TYPE_OFFSET']),
                    ':mustimeleng' => $this->notEmpty($row['MUS_TIME_TYPE_LENGTH']),
                ]);
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUsernameExist($username)
    {

        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :usrname');
        $stmt->execute([
            ':usrname' => $username
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

    public function getEmailExist($email)
    {

        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE EMAIL_ADDRESS = :eadd');
        $stmt->execute([
            ':eadd' => $email
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return false;
        } else {
            return true;
        }


    }

    public function updateCutOrder($cut, $order)
    {

        try {

            $sql = "UPDATE CUTS SET PLAY_ORDER = :pord WHERE CUT_NAME = :thename";

            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':pord' => $order,
                ':thename' => $cut,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateCartOrder($cart, $schedord)
    {

        try {

            $sql = "UPDATE CART SET USE_WEIGHTING = :sched WHERE NUMBER = :thenumber";

            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':sched' => $schedord,
                ':thenumber' => $cart,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRDPanels()
    {
        $rdpanel = array();
        $sql = 'SELECT * FROM `RDPANEL` ORDER BY `ID` ASC';
        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $rdpanel[] = $row;
        }
        return $rdpanel;
    }

    public function getRDPanelData($station)
    {

        $stmt = $this->_db->prepare('SELECT * FROM RDPANEL WHERE STATION = :id');
        $stmt->execute([':id' => $station]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;

    }

    public function updateRDPanel($station, $hostpanels, $userpanels, $defaultservice, $labletemp, $flash, $butpaus, $clear)
    {

        try {

            $sql = "UPDATE RDPANEL SET STATION_PANELS = :statpan, USER_PANELS = :usrpan, CLEAR_FILTER = :clrfilt, FLASH_PANEL = :flashpan, 
            PANEL_PAUSE_ENABLED = :panpause, BUTTON_LABEL_TEMPLATE = :butttemp, DEFAULT_SERVICE = :defserv WHERE STATION = :station";

            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                ':statpan' => $hostpanels,
                ':usrpan' => $userpanels,
                ':clrfilt' => $clear,
                ':flashpan' => $flash,
                ':panpause' => $butpaus,
                ':butttemp' => $labletemp,
                ':defserv' => $defaultservice,
                ':station' => $station,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRDCatchs()
    {
        $rdcatch = array();
        $sql = "SELECT rec.*, swit.NAME, imp.NAME AS IMPNAME FROM RECORDINGS rec LEFT JOIN MATRICES swit ON rec.STATION_NAME=swit.STATION_NAME AND rec.CHANNEL=swit.MATRIX LEFT JOIN INPUTS imp ON rec.STATION_NAME=imp.STATION_NAME AND swit.MATRIX=imp.MATRIX AND rec.SWITCH_INPUT=imp.NUMBER ORDER BY rec.ID ASC";
        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $rdcatch[] = $row;
        }
        return $rdcatch;
    }

    public function getHosts()
    {

        $hosts = array();

        $sql = 'SELECT `NAME` FROM `STATIONS`
                ORDER BY `NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            foreach ($row as $field)
                $hosts[] = $field;

        }

        $results = NULL;

        return $hosts;

    }

    public function getRDCatchData($id)
    {

        $stmt = $this->_db->prepare('SELECT * FROM RECORDINGS WHERE ID = :id');
        $stmt->execute([':id' => $id]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;
    }


    public function updateCatchMacro($isactive, $station, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $macro, $one, $id)
    {

        $sql = 'UPDATE `RECORDINGS` SET `IS_ACTIVE` = :isactive, `STATION_NAME` = :stationname, `SUN` = :sun, `MON` = :mon,
        `TUE` = :tue, `WED` = :wed, `THU` = :thu, `FRI` = :fri, `SAT` = :sat, `DESCRIPTION` = :descript,
        `START_TIME` = :starttime, `MACRO_CART` = :macrocart, `ONE_SHOT` = :oneshot WHERE `ID` = :idno';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':macrocart', $macro);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':idno', $id);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getRDCatchCutData($id)
    {

        $stmt = $this->_db->prepare('SELECT * FROM CUTS WHERE CART_NUMBER = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;
    }

    public function updateCatchDownload($isactive, $station, $cutname, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $treshold, $normlev, $evoff, $channels, $one, $url, $usrn, $urlpass, $metadata, $id)
    {

        $sql = 'UPDATE `RECORDINGS` SET `IS_ACTIVE` = :isactive, `STATION_NAME` = :stationname, `CUT_NAME` = :cutname, `SUN` = :sun, `MON` = :mon,
        `TUE` = :tue, `WED` = :wed, `THU` = :thu, `FRI` = :fri, `SAT` = :sat, `DESCRIPTION` = :descript,
        `START_TIME` = :starttime, `TRIM_THRESHOLD` = :treshold, `NORMALIZE_LEVEL` = :normlev, `EVENTDATE_OFFSET` = :evdateoff, 
        `CHANNELS` = :channels, `ONE_SHOT` = :oneshot, `URL` = :urls, `URL_USERNAME` = :urusr, `URL_PASSWORD` = :urlpass, `ENABLE_METADATA` = :enmeta WHERE `ID` = :idno';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':treshold', $treshold);
        $stmt->bindParam(':normlev', $normlev);
        $stmt->bindParam(':evdateoff', $evoff);
        $stmt->bindParam(':channels', $channels);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':urls', $url);
        $stmt->bindParam(':urusr', $usrn);
        $stmt->bindParam(':urlpass', $urlpass);
        $stmt->bindParam(':enmeta', $metadata);
        $stmt->bindParam(':idno', $id);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }
    public function getFeeds()
    {

        $feeds = array();

        $sql = 'SELECT * FROM `FEEDS`
                ORDER BY `KEY_NAME` ASC';

        $results = $this->_db->prepare($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);
        $results->execute();
        while ($row = $results->fetch()) {

            $feeds[] = $row;

        }

        return $feeds;

    }

    public function updateCatcUpload($isactive, $station, $cutname, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $normlev, $format, $sample, $channels, $bitrate, $quality, $evoff, $one, $url, $usrn, $urlpass, $metadata, $feed, $id)
    {

        $sql = 'UPDATE `RECORDINGS` SET `IS_ACTIVE` = :isactive, `STATION_NAME` = :stationname, `CUT_NAME` = :cutname, `SUN` = :sun, `MON` = :mon,
        `TUE` = :tue, `WED` = :wed, `THU` = :thu, `FRI` = :fri, `SAT` = :sat, `DESCRIPTION` = :descript,
        `START_TIME` = :starttime, `NORMALIZE_LEVEL` = :normlev, `FORMAT` = :format, `SAMPRATE` = :samprate, `CHANNELS` = :channels, 
        `BITRATE` = :bitrate, `QUALITY` = :quality, `EVENTDATE_OFFSET` = :evdateoff, 
        `ONE_SHOT` = :oneshot, `URL` = :urls, `URL_USERNAME` = :urusr, `URL_PASSWORD` = :urlpass, `ENABLE_METADATA` = :enmeta, `FEED_ID` = :feedid WHERE `ID` = :idno';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':normlev', $normlev);
        $stmt->bindParam(':format', $format);
        $stmt->bindParam(':samprate', $sample);
        $stmt->bindParam(':channels', $channels);
        $stmt->bindParam(':bitrate', $bitrate);
        $stmt->bindParam(':quality', $quality);
        $stmt->bindParam(':evdateoff', $evoff);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':urls', $url);
        $stmt->bindParam(':urusr', $usrn);
        $stmt->bindParam(':urlpass', $urlpass);
        $stmt->bindParam(':enmeta', $metadata);
        $stmt->bindParam(':feedid', $feed);
        $stmt->bindParam(':idno', $id);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function AddCatcUpload($isactive, $station, $cutname, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $normlev, $format, $sample, $channels, $bitrate, $quality, $evoff, $one, $url, $usrn, $urlpass, $metadata, $feed)
    {

        $channel = 0;
        $type = 5;
        $sql = 'INSERT INTO `RECORDINGS` (`IS_ACTIVE`, `STATION_NAME`, `TYPE`, `CHANNEL`, `CUT_NAME`, `SUN`, `MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `DESCRIPTION`,
        `START_TIME`, `NORMALIZE_LEVEL`, `FORMAT`, `SAMPRATE`, `CHANNELS`, `BITRATE`, `QUALITY`, `EVENTDATE_OFFSET`, `ONE_SHOT`, `URL`, `URL_USERNAME`, `URL_PASSWORD`, `ENABLE_METADATA`, `FEED_ID`)
                VALUES (:isactive, :stationname, :type, :channel, :cutname, :sun, :mon, :tue, :wed, :thu, :fri, :sat, :descript, :starttime, :normlev, :format, :samprate, :channels,
                :bitrate, :quality, :evdateoff, :oneshot, :urls, :urusr, :urlpass, :enmeta, :feedid)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':normlev', $normlev);
        $stmt->bindParam(':format', $format);
        $stmt->bindParam(':samprate', $sample);
        $stmt->bindParam(':channels', $channels);
        $stmt->bindParam(':bitrate', $bitrate);
        $stmt->bindParam(':quality', $quality);
        $stmt->bindParam(':evdateoff', $evoff);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':urls', $url);
        $stmt->bindParam(':urusr', $usrn);
        $stmt->bindParam(':urlpass', $urlpass);
        $stmt->bindParam(':enmeta', $metadata);
        $stmt->bindParam(':feedid', $feed);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function AddCatchDownload($isactive, $station, $cutname, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $treshold, $normlev, $evoff, $channels, $one, $url, $usrn, $urlpass, $metadata)
    {

        $channel = 0;
        $type = 4;
        $sql = 'INSERT INTO `RECORDINGS` (`IS_ACTIVE`, `STATION_NAME`, `TYPE`, `CHANNEL`, `CUT_NAME`, `SUN`, `MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `DESCRIPTION`,
        `START_TIME`, `TRIM_THRESHOLD`, `NORMALIZE_LEVEL`, `EVENTDATE_OFFSET`, `CHANNELS`, `ONE_SHOT`, `URL`, `URL_USERNAME`, `URL_PASSWORD`, `ENABLE_METADATA`)
                VALUES (:isactive, :stationname, :type, :channel, :cutname, :sun, :mon, :tue, :wed, :thu, :fri, :sat, :descript, :starttime, :treshold, :normlev, 
                :evdateoff, :channels, :oneshot, :urls, :urusr, :urlpass, :enmeta)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':treshold', $treshold);
        $stmt->bindParam(':normlev', $normlev);
        $stmt->bindParam(':evdateoff', $evoff);
        $stmt->bindParam(':channels', $channels);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':urls', $url);
        $stmt->bindParam(':urusr', $usrn);
        $stmt->bindParam(':urlpass', $urlpass);
        $stmt->bindParam(':enmeta', $metadata);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function AddCatchMacro($isactive, $station, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $macro, $one)
    {

        $channel = 0;
        $type = 1;
        $cut_name = "";
        $sql = 'INSERT INTO `RECORDINGS` (`IS_ACTIVE`, `STATION_NAME`, `TYPE`, `CHANNEL`, `CUT_NAME`, `SUN`, `MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `DESCRIPTION`,
        `START_TIME`, `MACRO_CART`, `ONE_SHOT`)
                VALUES (:isactive, :stationname, :type, :channel, :cutname, :sun, :mon, :tue, :wed, :thu, :fri, :sat, :descript, :starttime, 
                :macrocart, :oneshot)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':cutname', $cut_name);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':macrocart', $macro);
        $stmt->bindParam(':oneshot', $one);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function removeRDCatch($id)
    {
        $stmt1 = $this->_db->prepare('DELETE FROM RECORDINGS WHERE ID = :id');
        $stmt1->execute([
            ':id' => $id,
        ]);

        return true;
    }
    public function getSwitches($host)
    {

        $switch = array();
        $stmt = $this->_db->prepare('SELECT * FROM MATRICES WHERE STATION_NAME = :station AND INPUTS > 0 AND OUTPUTS > 0');
        $stmt->execute([':station' => $host]);
        $switch = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $switch;

    }

    public function getOutputs($matrix, $station)
    {

        $outputs = array();
        $stmt = $this->_db->prepare('SELECT * FROM OUTPUTS WHERE STATION_NAME = :station AND MATRIX = :matrix');
        $stmt->execute([
            ':station' => $station,
            ':matrix' => $matrix
        ]);
        $outputs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $outputs;

    }

    public function getInputs($matrix, $station)
    {

        $inputs = array();
        $stmt = $this->_db->prepare('SELECT * FROM INPUTS WHERE STATION_NAME = :station AND MATRIX = :matrix');
        $stmt->execute([
            ':station' => $station,
            ':matrix' => $matrix
        ]);
        $inputs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $inputs;

    }

    public function AddCatchSwitch($isactive, $station, $channel, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $switchin, $switchout, $one)
    {

        $type = 2;
        $cutname = "";
        $sql = 'INSERT INTO `RECORDINGS` (`IS_ACTIVE`, `STATION_NAME`, `TYPE`, `CHANNEL`, `CUT_NAME`, `SUN`, `MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `DESCRIPTION`,
        `START_TIME`, `SWITCH_INPUT`, `SWITCH_OUTPUT`, `ONE_SHOT`)
                VALUES (:isactive, :stationname, :type, :channel, :cutname, :sun, :mon, :tue, :wed, :thu, :fri, :sat, :descript, :starttime,
                :switchin, :switchout, :oneshot)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':switchin', $switchin);
        $stmt->bindParam(':switchout', $switchout);
        $stmt->bindParam(':oneshot', $one);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function EditCatchSwitch($isactive, $station, $channel, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $switchin, $switchout, $one, $id)
    {

        $sql = 'UPDATE `RECORDINGS` SET `IS_ACTIVE` = :isactive, `STATION_NAME` = :stationname, `CHANNEL` = :channel, `SUN` = :sun, `MON` = :mon,
        `TUE` = :tue, `WED` = :wed, `THU` = :thu, `FRI` = :fri, `SAT` = :sat, `DESCRIPTION` = :descript,
        `START_TIME` = :starttime, `SWITCH_INPUT` = :switchin, `SWITCH_OUTPUT` = :switchout, `ONE_SHOT` = :oneshot WHERE `ID` = :idno';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':switchin', $switchin);
        $stmt->bindParam(':switchout', $switchout);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':idno', $id);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getPlayoutPorts($station)
    {

        $playout = array();
        $stmt = $this->_db->prepare('SELECT * FROM DECKS WHERE STATION_NAME = :station AND CARD_NUMBER >= 0 AND PORT_NUMBER >= 0 AND CHANNEL > 128 AND CHANNEL <= 137');
        $stmt->execute([':station' => $station]);
        $i = 1;
        while ($row = $stmt->fetch()) {

            $playout[] = array(
                'ID' => $row['ID'],
                'STATION_NAME' => $row['STATION_NAME'],
                'CHANNEL' => $row['CHANNEL'],
                'CARD_NUMBER' => $row['CARD_NUMBER'],
                'STREAM_NUMBER' => $row['STREAM_NUMBER'],
                'PORT_NUMBER' => $row['PORT_NUMBER'],
                'MON_PORT_NUMBER' => $row['MON_PORT_NUMBER'],
                'DEFAULT_MONITOR_ON' => $row['DEFAULT_MONITOR_ON'],
                'PORT_TYPE' => $row['PORT_TYPE'],
                'DEFAULT_FORMAT' => $row['DEFAULT_FORMAT'],
                'DEFAULT_CHANNELS' => $row['DEFAULT_CHANNELS'],
                'DEFAULT_BITRATE' => $row['DEFAULT_BITRATE'],
                'DEFAULT_THRESHOLD' => $row['DEFAULT_THRESHOLD'],
                'SWITCH_STATION' => $row['SWITCH_STATION'],
                'SWITCH_MATRIX' => $row['SWITCH_MATRIX'],
                'SWITCH_OUTPUT' => $row['SWITCH_OUTPUT'],
                'SWITCH_DELAY' => $row['SWITCH_DELAY'],
                'PORT_NUMBER_VIS' => $i,
            );
            $i++;
        }

        return $playout;

    }

    public function AddCatchPlayout($isactive, $station, $channel, $cutname, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $one)
    {

        $type = 3;
        $sql = 'INSERT INTO `RECORDINGS` (`IS_ACTIVE`, `STATION_NAME`, `TYPE`, `CHANNEL`, `CUT_NAME`, `SUN`, `MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `DESCRIPTION`,
        `START_TIME`, `ONE_SHOT`)
                VALUES (:isactive, :stationname, :type, :channel, :cutname, :sun, :mon, :tue, :wed, :thu, :fri, :sat, :descript, :starttime,
                :oneshot)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':oneshot', $one);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function EditCatchPlayout($isactive, $station, $channel, $cutname, $sun, $mon, $tue, $wed, $thu, $fri, $sat, $desc, $start, $one, $id)
    {

        $sql = 'UPDATE `RECORDINGS` SET `IS_ACTIVE` = :isactive, `STATION_NAME` = :stationname, `CHANNEL` = :channel, `CUT_NAME` = :cutname, `SUN` = :sun, `MON` = :mon,
        `TUE` = :tue, `WED` = :wed, `THU` = :thu, `FRI` = :fri, `SAT` = :sat, `DESCRIPTION` = :descript,
        `START_TIME` = :starttime, `ONE_SHOT` = :oneshot WHERE `ID` = :idno';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $isactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':cutname', $cutname);
        $stmt->bindParam(':sun', $sun);
        $stmt->bindParam(':mon', $mon);
        $stmt->bindParam(':tue', $tue);
        $stmt->bindParam(':wed', $wed);
        $stmt->bindParam(':thu', $thu);
        $stmt->bindParam(':fri', $fri);
        $stmt->bindParam(':sat', $sat);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':oneshot', $one);
        $stmt->bindParam(':idno', $id);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function getRecordPorts($station)
    {

        $playout = array();
        $stmt = $this->_db->prepare('SELECT * FROM DECKS WHERE STATION_NAME = :station AND CARD_NUMBER >= 0 AND CHANNEL > 0 AND CHANNEL <= 9');
        $stmt->execute([':station' => $station]);
        $i = 1;
        while ($row = $stmt->fetch()) {

            $playout[] = array(
                'ID' => $row['ID'],
                'STATION_NAME' => $row['STATION_NAME'],
                'CHANNEL' => $row['CHANNEL'],
                'CARD_NUMBER' => $row['CARD_NUMBER'],
                'STREAM_NUMBER' => $row['STREAM_NUMBER'],
                'PORT_NUMBER' => $row['PORT_NUMBER'],
                'MON_PORT_NUMBER' => $row['MON_PORT_NUMBER'],
                'DEFAULT_MONITOR_ON' => $row['DEFAULT_MONITOR_ON'],
                'PORT_TYPE' => $row['PORT_TYPE'],
                'DEFAULT_FORMAT' => $row['DEFAULT_FORMAT'],
                'DEFAULT_CHANNELS' => $row['DEFAULT_CHANNELS'],
                'DEFAULT_BITRATE' => $row['DEFAULT_BITRATE'],
                'DEFAULT_THRESHOLD' => $row['DEFAULT_THRESHOLD'],
                'SWITCH_STATION' => $row['SWITCH_STATION'],
                'SWITCH_MATRIX' => $row['SWITCH_MATRIX'],
                'SWITCH_OUTPUT' => $row['SWITCH_OUTPUT'],
                'SWITCH_DELAY' => $row['SWITCH_DELAY'],
                'PORT_NUMBER_VIS' => $i,
            );
            $i++;
        }

        return $playout;

    }

    public function getDecksData($station, $channel)
    {

        $stmt = $this->_db->prepare('SELECT * FROM DECKS WHERE STATION_NAME = :station AND CHANNEL = :channel');
        $stmt->execute([':station' => $station,
        ':channel' => $channel]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        return $array;
    }

    public function AddCatchRecording($eventactive, $station, $audioport, $dest, $sunday, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $desc, $startopt, $thestart, $gpiend, $gpimatrix, $gpiline, $startdelay, $endopt, $theend, $gpiendend, $recendtimelength, $gpimatrixend, $gpilineend, $maxreclength, $source, $channels, $autotrim, $normalize, $startdateoffset, $enddateoffset, $multiplerecs, $oneshot)
    {

        $type = 0;
        $sql = 'INSERT INTO `RECORDINGS` (`IS_ACTIVE`, `STATION_NAME`, `TYPE`, `CHANNEL`, `CUT_NAME`, `SUN`, `MON`, `TUE`, `WED`, `THU`, `FRI`, `SAT`, `DESCRIPTION`,
        `START_TYPE`, `START_TIME`, `START_LENGTH`, `START_MATRIX`, `START_LINE`, `START_OFFSET`, `END_TYPE`, `END_TIME`, `END_LENGTH`, `LENGTH`, `END_MATRIX`, `END_LINE`,
        `MAX_GPI_REC_LENGTH`, `SWITCH_INPUT`, `CHANNELS`, `TRIM_THRESHOLD`, `NORMALIZE_LEVEL`, `STARTDATE_OFFSET`, `ENDDATE_OFFSET`, `ALLOW_MULT_RECS`, `ONE_SHOT`)
                VALUES (:isactive, :stationname, :type, :channel, :cutname, :sun, :mon, :tue, :wed, :thu, :fri, :sat, :descript, :startopt, :starttime,
                :gpiend, :startmatrix, :startline, :startoffset, :endtype, :endtime, :endlength, :lengths, :endmatrix, :endline, :maxgpirec,
                :switchinput, :channels, :trimtreshold, :normalize, :startdoffset, :enddoffset, :allowmultiple, :oneshot)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $eventactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':channel', $audioport);
        $stmt->bindParam(':cutname', $dest);
        $stmt->bindParam(':sun', $sunday);
        $stmt->bindParam(':mon', $monday);
        $stmt->bindParam(':tue', $tuesday);
        $stmt->bindParam(':wed', $wednesday);
        $stmt->bindParam(':thu', $thursday);
        $stmt->bindParam(':fri', $friday);
        $stmt->bindParam(':sat', $saturday);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':startopt', $startopt);
        $stmt->bindParam(':starttime', $thestart);
        $stmt->bindParam(':gpiend', $gpiend);
        $stmt->bindParam(':startmatrix', $gpimatrix);
        $stmt->bindParam(':startline', $gpiline);
        $stmt->bindParam(':startoffset', $startdelay);
        $stmt->bindParam(':endtype', $endopt);
        $stmt->bindParam(':endtime', $theend);
        $stmt->bindParam(':endlength', $gpiendend);
        $stmt->bindParam(':lengths', $recendtimelength);
        $stmt->bindParam(':endmatrix', $gpimatrixend);
        $stmt->bindParam(':endline', $gpilineend);
        $stmt->bindParam(':maxgpirec', $maxreclength);
        $stmt->bindParam(':switchinput', $source);
        $stmt->bindParam(':channels', $channels);
        $stmt->bindParam(':trimtreshold', $autotrim);
        $stmt->bindParam(':normalize', $normalize);
        $stmt->bindParam(':startdoffset', $startdateoffset);
        $stmt->bindParam(':enddoffset', $enddateoffset);
        $stmt->bindParam(':allowmultiple', $multiplerecs);
        $stmt->bindParam(':oneshot', $oneshot);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function EditCatchRecording($eventactive, $station, $audioport, $dest, $sunday, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $desc, $startopt, $thestart, $gpiend, $gpimatrix, $gpiline, $startdelay, $endopt, $theend, $gpiendend, $recendtimelength, $gpimatrixend, $gpilineend, $maxreclength, $source, $channels, $autotrim, $normalize, $startdateoffset, $enddateoffset, $multiplerecs, $oneshot, $id)
    {

        $sql = 'UPDATE `RECORDINGS` SET `IS_ACTIVE` = :isactive, `STATION_NAME` = :stationname, `CHANNEL` = :channel, `CUT_NAME` = :cutname, `SUN` = :sun, `MON` = :mon,
        `TUE` = :tue, `WED` = :wed, `THU` = :thu, `FRI` = :fri, `SAT` = :sat, `DESCRIPTION` = :descript, `START_TYPE` = :startopt, `START_TIME` = :starttime, 
        `START_LENGTH` = :gpiend, `START_MATRIX` = :startmatrix, `START_LINE` = :startline, `START_OFFSET` = :startoffset, `END_TYPE` = :endtype, 
        `END_TIME` = :endtime, `END_LENGTH` = :endlength, `LENGTH` = :lengths, `END_MATRIX` = :endmatrix, `END_LINE` = :endline, 
        `MAX_GPI_REC_LENGTH` = :maxgpirec, `SWITCH_INPUT` = :switchinput, `CHANNELS` = :channels, `TRIM_THRESHOLD` = :trimtreshold, `NORMALIZE_LEVEL` = :normalize, 
        `STARTDATE_OFFSET` = :startdoffset, `ENDDATE_OFFSET` = :enddoffset, `ALLOW_MULT_RECS` = :allowmultiple, `ONE_SHOT` = :oneshot WHERE `ID` = :idno';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':isactive', $eventactive);
        $stmt->bindParam(':stationname', $station);
        $stmt->bindParam(':channel', $audioport);
        $stmt->bindParam(':cutname', $dest);
        $stmt->bindParam(':sun', $sunday);
        $stmt->bindParam(':mon', $monday);
        $stmt->bindParam(':tue', $tuesday);
        $stmt->bindParam(':wed', $wednesday);
        $stmt->bindParam(':thu', $thursday);
        $stmt->bindParam(':fri', $friday);
        $stmt->bindParam(':sat', $saturday);
        $stmt->bindParam(':descript', $desc);
        $stmt->bindParam(':startopt', $startopt);
        $stmt->bindParam(':starttime', $thestart);
        $stmt->bindParam(':gpiend', $gpiend);
        $stmt->bindParam(':startmatrix', $gpimatrix);
        $stmt->bindParam(':startline', $gpiline);
        $stmt->bindParam(':startoffset', $startdelay);
        $stmt->bindParam(':endtype', $endopt);
        $stmt->bindParam(':endtime', $theend);
        $stmt->bindParam(':endlength', $gpiendend);
        $stmt->bindParam(':lengths', $recendtimelength);
        $stmt->bindParam(':endmatrix', $gpimatrixend);
        $stmt->bindParam(':endline', $gpilineend);
        $stmt->bindParam(':maxgpirec', $maxreclength);
        $stmt->bindParam(':switchinput', $source);
        $stmt->bindParam(':channels', $channels);
        $stmt->bindParam(':trimtreshold', $autotrim);
        $stmt->bindParam(':normalize', $normalize);
        $stmt->bindParam(':startdoffset', $startdateoffset);
        $stmt->bindParam(':enddoffset', $enddateoffset);
        $stmt->bindParam(':allowmultiple', $multiplerecs);
        $stmt->bindParam(':oneshot', $oneshot);
        $stmt->bindParam(':idno', $id);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function getMatrixCheckValidation($station, $matrix)
    {

        $stmt = $this->_db->prepare('SELECT * FROM GPIS WHERE STATION_NAME = :statname AND MATRIX = :matrix');
        $stmt->execute([
            ':statname' => $station,
            ':matrix' => $matrix
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return true;
        } else {
            return false;
        }


    }
    public function getMatrixLineCheckValidation($station, $matrix, $line)
    {

        $stmt = $this->_db->prepare('SELECT * FROM GPIS WHERE STATION_NAME = :statname AND MATRIX = :matrix AND NUMBER = :thenumb');
        $stmt->execute([
            ':statname' => $station,
            ':matrix' => $matrix,
            ':thenumb' => $line
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return true;
        } else {
            return false;
        }


    }
}