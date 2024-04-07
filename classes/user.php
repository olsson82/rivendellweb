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
class User
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

    private function get_user_hash($username)
    {
        try {
            if ($this->_ignoreCase) {
                $stmt = $this->_db->prepare('SELECT PASSWORD, FULL_NAME LOGIN_NAME FROM USERS WHERE LOWER(LOGIN_NAME) = LOWER(:LOGIN_NAME) AND ENABLE_WEB="Y" ');
            } else {
                $stmt = $this->_db->prepare('SELECT PASSWORD, FULL_NAME, LOGIN_NAME FROM USERS WHERE LOGIN_NAME = :LOGIN_NAME AND ENABLE_WEB="Y" ');
            }
            $stmt->execute(['LOGIN_NAME' => $username]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            echo '<p class="bg-danger">' . $e->getMessage() . '</p>';
        }
    }

    public function isValidUsername($username)
    {
        if (strlen($username) < 3) {
            return false;
        }

        if (strlen($username) > 17) {
            return false;
        }

        if (!ctype_alnum($username)) {
            return false;
        }

        return true;
    }

    public function login($username, $password, $remember)
    {
        if (!$this->isValidUsername($username)) {
            return false;
        }

        if (strlen($password) < 3) {
            return false;
        }

        $row = $this->get_user_hash($username);

        if (base64_encode($password) == $row['PASSWORD']) {
            if ($remember == 1) {
                $expire = time() + (30 * 24 * 60 * 60);
                setcookie('loggedin', 'true', $expire, '/');
                setcookie('username', $row['LOGIN_NAME'], $expire, '/');
                setcookie('password', $row['PASSWORD'], $expire, '/');
                setcookie('fullname', $row['FULL_NAME'], $expire, '/');
                setcookie('rdWebAPI', 'http://localhost/rd-bin/rdxport.cgi', $expire, '/');
            } else {
                $expire = time() + (3600 * 4);
                setcookie('loggedin', 'true', $expire, '/');
                setcookie('username', $row['LOGIN_NAME'], $expire, '/');
                setcookie('password', $row['PASSWORD'], $expire, '/');
                setcookie('fullname', $row['FULL_NAME'], $expire, '/');
                setcookie('rdWebAPI', 'http://localhost/rd-bin/rdxport.cgi', $expire, '/');
            }
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $username;
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            $_SESSION['fullname'] = $row['FULL_NAME'];
            $_SESSION['rdWebAPI'] = 'http://localhost/rd-bin/rdxport.cgi';

            return true;
        }

        return false;
    }

    public function logout()
    {
        session_destroy();
        setcookie('loggedin', '', time() - 3600, '/');
        setcookie('username', '', time() - 3600, '/');
        setcookie('password', '', time() - 3600, '/');
        setcookie('fullname', '', time() - 3600, '/');
        setcookie('rdWebAPI', '', time() - 3600, '/');
    }

    public function is_logged_in()
    {
        if (isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] == true) {
            return true;
        }
    }

    public function getEmailConn($email)
    {

        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE EMAIL_ADDRESS = :emailadd');
        $stmt->execute([
            ':emailadd' => $email
        ]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $number_of_rows = $stmt->rowCount();

        if ($number_of_rows > 0) {
            return true;
        } else {
            return false;
        }


    }

    public function getUserFullName($email)
    {
        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE EMAIL_ADDRESS = :name');

        $stmt->execute(['name' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['FULL_NAME'];
    }

    public function getUserUserName($email)
    {
        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE EMAIL_ADDRESS = :name');

        $stmt->execute(['name' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['LOGIN_NAME'];
    }

    public function getUserEmail($username)
    {
        $stmt = $this->_db->prepare('SELECT * FROM USERS WHERE LOGIN_NAME = :name');

        $stmt->execute(['name' => $username]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['EMAIL_ADDRESS'];
    }

    public function checkUser($username)
    {

        $row = $this->get_user_hash($username);

        if ($username == $row['LOGIN_NAME']) {
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password, $username)
    {

        $row = $this->get_user_hash($username);

        if (base64_encode($password) == $row['PASSWORD']) {
            return true;
        } else {
            return false;
        }
    }

    public function changePass($password, $rd_username)
    {
        $newpass = base64_encode($password);
        $sql = 'UPDATE `USERS` SET `PASSWORD` = :passwo WHERE `LOGIN_NAME` = :loginname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':passwo', $newpass);
        $stmt->bindParam(':loginname', $rd_username);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function updateUserData($username, $fullname, $email, $phone)
    {
        $sql = 'UPDATE `USERS` SET `FULL_NAME` = :fullname, `EMAIL_ADDRESS` = :emailadd, `PHONE_NUMBER` = :phone  WHERE `LOGIN_NAME` = :loginname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':emailadd', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':loginname', $username);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }


}
