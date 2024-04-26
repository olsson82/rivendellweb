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
class Functions
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

    public function loadPass($username)
    {
        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :name');

        $stmt->execute(['name' => $username]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['PASSWORD'];
    }


    public function msToHHMMSS_fromMID($milliSeconds)
    {

        $seconds = $milliSeconds / 1000;

        return gmdate("H:i:s", $seconds);

    }

    public function msToHHMMSS($milliSeconds)
    {

        $seconds = $milliSeconds / 1000;

        if ($seconds > 3600) {
            return gmdate("H:i:s", $seconds);
        } else {
            return gmdate("i:s", $seconds);
        }

    }

    public function getDuration($millis)
    {

        //minutes
        $mins = 0;

        if ($millis >= 60000)
            $mins = (int) ($millis / 60000);

        while (strlen($mins) < 2)
            $mins = '0' . $mins;

        $millis = $millis - ($mins * 60000);

        //seconds
        $secs = 0;

        if ($millis >= 1000)
            $secs = (int) ($millis / 1000);

        while (strlen($secs) < 2)
            $secs = '0' . $secs;

        $millis = $millis - ($secs * 1000);


        $time = $mins . ':' . $secs;

        if ($millis > 0)
            $time .= '.' . (int) ($millis / 100);

        return $time;

    }

    public function curl_custom_postfields($ch, array $assoc = array(), array $files = array())
    {

        static $disallow = array("\0", "\"", "\r", "\n");

        foreach ($assoc as $k => $v) {
            $k = str_replace($disallow, "_", $k);
            $body[] = implode(
                "\r\n",
                array(
                    "Content-Disposition: form-data; name=\"{$k}\"",
                    "",
                    filter_var($v),
                )
            );
        }

        foreach ($files as $k => $v) {
            switch (true) {
                case false === $v = realpath(filter_var($v)):
                case !is_file($v):
                case !is_readable($v):
                    continue 2;
            }
            $data = file_get_contents($v);
            $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
            $k = str_replace($disallow, "_", $k);
            $v = str_replace($disallow, "_", $v);
            $body[] = implode(
                "\r\n",
                array(
                    "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
                    "Content-Type: application/octet-stream",
                    "",
                    $data,
                )
            );
        }

        do {
            $boundary = "---------------------" . md5(mt_rand() . microtime());
        } while (preg_grep("/{$boundary}/", $body));

        array_walk($body, function (&$part) use ($boundary) {
            $part = "--{$boundary}\r\n{$part}";
        });

        $body[] = "--{$boundary}--";
        $body[] = "";

        return @curl_setopt_array(
            $ch,
            array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => implode("\r\n", $body),
                CURLOPT_HTTPHEADER => array(
                    "Expect: 100-continue",
                    "Content-Type: multipart/form-data; boundary={$boundary}",
                ),
            )
        );
    }

    public function rd_cart_exists($cartNumber)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '7',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
            'INCLUDE_CUTS' => '1',
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return 0;
        } else {
            return 1;
        }

    }

    public function rd_cut_count($cartNumber)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '7',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
            'INCLUDE_CUTS' => '1',
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match_all('/<cutQuantity>([0-9]+)<\/cutQuantity>/', $result, $matches)) {
            $count = $matches[1][0];
            return $count;
        } else {
            return 0;
        }

    }

    public function rd_add_new_cart($carttype, $groupName)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '12',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'GROUP_NAME' => $groupName,
            'TYPE' => $carttype,
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            $cart = 0;
        } else if (preg_match_all('/<number>([0-9]+)<\/number>/', $result, $matches)) {
            $cart = $matches[1][0];
        }
        return $cart;
    }

    public function rd_add_cart($cartNumber, $groupName)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '12',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'GROUP_NAME' => $groupName,
            'TYPE' => 'audio',
            'CART_NUMBER' => $cartNumber,
        );

        if ($cartNumber == 0) {
            $parameters = array(
                'COMMAND' => '12',
                'LOGIN_NAME' => $rd_username,
                'PASSWORD' => $rd_password,
                'GROUP_NAME' => $groupName,
                'TYPE' => 'audio',
            );
        }

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            $cart = 0;
        } else if (preg_match_all('/<number>([0-9]+)<\/number>/', $result, $matches)) {
            $cart = $matches[1][0];
        }
        return $cart;
    }

    public function rd_add_cut($cartNumber)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '10',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return 0;
        } else {
            return 1;
        }
    }

    public function rd_assign_sched($cartNumber, $schedcode)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '25',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
            'CODE' => $schedcode,
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return false;
        } else {
            return true;
        }
    }

    public function rd_unassign_sched($cartNumber, $schedcode)
    {


        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '26',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
            'CODE' => $schedcode,
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return false;
        } else {
            return true;
        }
    }

    public function rd_edit_VTcart($cartNumber, $artist, $title, $comment, $log)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        $parameters = array(
            'COMMAND' => '14',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
            'ARTIST' => $artist,
            'TITLE' => $title,
            'OWNER' => $log,
            'USER_DEFINED' => $comment,
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return false;
        } else if (preg_match('/ResponseCode>409</', $result, $matches)) {
            return false;
        } else {
            return true;
        }

    }

    public function rd_edit_cart($cartNumber, $group, $album, $year, $record, $client, $agency, $publisher, $composer, $userdef, $usagecode, $enflength, $frlength, $asynchronous, $artist, $title, $comment)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");
        if ($enflength == 0) {
            $parameters = array(
                'COMMAND' => '14',
                'LOGIN_NAME' => $rd_username,
                'PASSWORD' => $rd_password,
                'CART_NUMBER' => $cartNumber,
                'GROUP_NAME' => $group,
                'ARTIST' => $artist,
                'TITLE' => $title,
                'YEAR' => $year,
                'LABEL' => $record,
                'CLIENT' => $client,
                'AGENCY' => $agency,
                'PUBLISHER' => $publisher,
                'COMPOSER' => $composer,
                'USER_DEFINED' => $userdef,
                'USAGE_CODE' => $usagecode,
                'ASYNCHRONOUS' => $asynchronous,
                'NOTES' => $comment,
            );
        } else {
            $parameters = array(
                'COMMAND' => '14',
                'LOGIN_NAME' => $rd_username,
                'PASSWORD' => $rd_password,
                'CART_NUMBER' => $cartNumber,
                'GROUP_NAME' => $group,
                'ARTIST' => $artist,
                'TITLE' => $title,
                'YEAR' => $year,
                'LABEL' => $record,
                'CLIENT' => $client,
                'AGENCY' => $agency,
                'PUBLISHER' => $publisher,
                'COMPOSER' => $composer,
                'USER_DEFINED' => $userdef,
                'USAGE_CODE' => $usagecode,
                'ENFORCE_LENGTH' => $enflength,
                'FORCED_LENGTH' => $frlength,
                'ASYNCHRONOUS' => $asynchronous,
                'NOTES' => $comment,
            );
        }
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return false;
        } else if (preg_match('/ResponseCode>409</', $result, $matches)) {
            return false;
        } else {
            return true;
        }
    }

    public function rd_edit_marker($cartno, $cutname, $cuestart, $cueend, $talkstart, $talkend, $fadeup, $fadedown, $seguestart, $segueend, $hookstart, $hookend)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");


        $parameters = array(
            'COMMAND' => '15',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartno,
            'CUT_NUMBER' => $cutname,
            'START_POINT' => $cuestart,
            'END_POINT' => $cueend,
            'FADEUP_POINT' => $fadeup,
            'FADEDOWN_POINT' => $fadedown,
            'SEGUE_START_POINT' => $seguestart,
            'SEGUE_END_POINT' => $segueend,
            'HOOK_START_POINT' => $hookstart,
            'HOOK_END_POINT' => $hookend,
            'TALK_START_POINT' => $talkstart,
            'TALK_END_POINT' => $talkend,
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return false;
        } else {
            return true;
        }
    }

    public function rd_edit_cut($cartNumber, $cutnumber, $evergreen, $cdesc, $coutcue, $cisrc, $ciscicode, $adstart, $adend, $daymon, $daytue, $daywed, $daythu, $dayfri, $daysat, $daysun, $adaystart, $adayend, $weight)
    {

        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array("Content-Type:multipart/form-data");


        $parameters = array(
            'COMMAND' => '15',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cartNumber,
            'CUT_NUMBER' => $cutnumber,
            'EVERGREEN' => $evergreen,
            'DESCRIPTION' => $cdesc,
            'OUTCUE' => $coutcue,
            'ISCI' => $ciscicode,
            'ISRC' => $cisrc,
            'START_DATETIME' => $adstart,
            'END_DATETIME' => $adend,
            'MON' => $daymon,
            'TUE' => $daytue,
            'WED' => $daywed,
            'THU' => $daythu,
            'FRI' => $dayfri,
            'SAT' => $daysat,
            'SUN' => $daysun,
            'START_DAYPART' => $adaystart,
            'END_DAYPART' => $adayend,
            'WEIGHT' => $weight,
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        curl_close($ch);

        if (preg_match('/ResponseCode>404</', $result, $matches)) {
            return false;
        } else {
            return true;
        }
    }

    public function addLogg($logname, $service)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '29',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'LOG_NAME' => $logname,
            'SERVICE_NAME' => $service,
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        if (preg_match('/ResponseCode>200</', $result, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public function locklog($log)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '34',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'OPERATION' => 'CREATE',
            'LOG_NAME' => $log,
            'LOCK_GUID' => ''
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        $sql = "SELECT LOCK_GUID
                FROM LOGS WHERE NAME = '$log'";
        $stmt = $this->_db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $lockgui = $row['LOCK_GUID'];
        }
        return $lockgui;

    }

    public function updateLock($log, $gui)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '34',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'OPERATION' => 'UPDATE',
            'LOG_NAME' => $log,
            'LOCK_GUID' => $gui
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return true;
    }

    public function removelock($log, $gui)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '34',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'OPERATION' => 'CLEAR',
            'LOG_NAME' => $log,
            'LOCK_GUID' => $gui
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return true;
    }

    public function deletelog($log)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '30',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'LOG_NAME' => $log,
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        if (preg_match('/ResponseCode>200</', $result, $matches)) {
            return true;
        } else {
            return false;
        }


    }

    public function deletecart($cart)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '13',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cart,
        );

        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        if (preg_match('/ResponseCode>200</', $result, $matches)) {
            return true;
        } else {
            return false;
        }


    }

    public function deletecut($cart, $cut)
    {
        $rd_username = $_COOKIE["username"];
        $rd_password = $this->loadPass($rd_username);
        $rd_web_api = $_COOKIE["rdWebAPI"];

        $cutsid = substr($cut, strpos($cut, '_') + 1);

        $ch = curl_init();
        $headers = array('Content-Type:multipart/form-data');
        $parameters = array(
            'COMMAND' => '11',
            'LOGIN_NAME' => $rd_username,
            'PASSWORD' => $rd_password,
            'CART_NUMBER' => $cart,
            'CUT_NUMBER' => $cutsid,
        );
        $options = array(
            CURLOPT_URL => $rd_web_api,
            CURLOPT_HEADER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        if (preg_match('/ResponseCode>200</', $result, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public function switchService($servicename)
    {
        if (isset($servicename)) {
            $expire = time() + (30 * 24 * 60 * 60);
            setcookie('serviceName', $servicename, $expire, '/');
            $_SESSION['serviceName'] = $servicename;

            return true;
        } else {
            return false;
        }

    }


}