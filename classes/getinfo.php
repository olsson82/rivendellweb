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
class Getinfo
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

        public function getCartInfo($cartid, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM CART WHERE NUMBER = :number');

                $stmt->execute(['number' => $cartid]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row[$type];
        }

        public function getClockInfo($clockname, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM CLOCKS WHERE NAME = :name');

                $stmt->execute(['name' => $clockname]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row[$type];
        }

        public function getLogInfo($log, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM LOGS WHERE NAME = :name');

                $stmt->execute(['name' => $log]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row[$type];
        }

        public function getEventInfo($eventname, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM EVENTS WHERE NAME = :name');

                $stmt->execute(['name' => $eventname]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row[$type];
        }

        public function getGroupInfo($groupname, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM GROUPS WHERE NAME = :name');

                $stmt->execute(['name' => $groupname]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row[$type];
        }

        public function getCartSchedCode($cartid, $code)
        {
                $stmt = $this->_db->prepare('SELECT * FROM CART_SCHED_CODES WHERE CART_NUMBER = :name AND SCHED_CODE = :sched');

                $stmt->execute([
                        'name' => $cartid,
                        'sched' => $code
                ]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['SCHED_CODE'];
        }

        public function getEventService($eventid, $service)
        {
                $stmt = $this->_db->prepare('SELECT * FROM EVENT_PERMS WHERE SERVICE_NAME = :name AND EVENT_NAME = :sched');

                $stmt->execute([
                        'name' => $service,
                        'sched' => $eventid
                ]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['SERVICE_NAME'];
        }

        public function getServiceClockInfo($clock, $service)
        {
                $stmt = $this->_db->prepare('SELECT * FROM CLOCK_PERMS WHERE CLOCK_NAME = :name AND SERVICE_NAME = :service');

                $stmt->execute([
                        'name' => $clock,
                        'service' => $service
                ]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['SERVICE_NAME'];
        }

        public function getUserEmail($username)
        {
                $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :name');

                $stmt->execute(['name' => $username]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['EMAIL_ADDRESS'];
        }

        public function getUserFullName($username)
        {
                $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :name');

                $stmt->execute(['name' => $username]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['FULL_NAME'];
        }

        public function getUserPhoneNumber($username)
        {
                $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :name');

                $stmt->execute(['name' => $username]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['PHONE_NUMBER'];
        }

        public function checkDBVers()
        {
                $stmt = $this->_db->prepare('SELECT * FROM VERSION');

                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row['DB'];
        }

        public function checkusrRights($right)
        {
                $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :name');

                $stmt->execute(['name' => $_COOKIE['username']]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row[$right] == 'Y') {
                        return true;
                } else {
                        return false;
                }
        }

        public function checkIfCartOwner($cart) {
                $stmt = $this->_db->prepare('SELECT * FROM CART WHERE NUMBER = :name');

                $stmt->execute(['name' => $cart]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['OWNER'] != null) {
                        return true;
                } else {
                        return false;
                } 
        }

        public function checkIfLogLocked($log) {
                $stmt = $this->_db->prepare('SELECT * FROM LOGS WHERE NAME = :name');

                $stmt->execute(['name' => $log]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['LOCK_USER_NAME'] != null) {
                        return true;
                } else {
                        return false;
                }
        }

        public function checkMacroNormal($cart, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM CART WHERE NUMBER = :thetype');
                $stmt->execute(['thetype' => $cart]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['TYPE'] == $type) {
                        return true;
                } else {
                        return false;
                }
        }

        public function getCutInfo($cut, $type)
        {
                $stmt = $this->_db->prepare('SELECT * FROM CUTS WHERE CUT_NAME = :number');

                $stmt->execute(['number' => $cut]);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row[$type];
        }

        public function checkLogin()
        {
                if (isset($_COOKIE["loggedin"]) && $_COOKIE["loggedin"] === true) {
                        return true;
                } else {
                        return false;
                }
        }
}