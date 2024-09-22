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
class Log
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

    public function getGrid($service)
    {

        $grid = array();

        $sql = "SELECT * FROM SERVICE_CLOCKS grid LEFT JOIN CLOCKS clk ON grid.CLOCK_NAME=clk.NAME WHERE grid.SERVICE_NAME LIKE :services ORDER BY grid.HOUR";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([':services' => $service]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $stmt->fetch()) {

            $hour = $row['HOUR'];
            $grid[$hour] = $row;

        }


        return $grid;

    }

    public function getRivendellClocks($service)
    {

        $clocks = array();

        $sql = 'SELECT `CLOCK_NAME` FROM `CLOCK_PERMS`
                WHERE `SERVICE_NAME` = :service
                ORDER BY `CLOCK_NAME` ASC';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':service', $service);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {

            while ($row = $stmt->fetch())
                $clocks[] = $row['CLOCK_NAME'];

            $stmt = NULL;

            $clockNames = join(',', array_fill(0, count($clocks), '?'));
            $sql = 'SELECT `NAME`, `SHORT_NAME`, `COLOR` FROM `CLOCKS`
                WHERE `NAME` IN (' . $clockNames . ')';

            $stmt = $this->_db->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute($clocks);

            $clocks = array();

            while ($row = $stmt->fetch())
                $clocks[$row['NAME']] = $row;

        }


        return $clocks;

    }

    public function getRivendellEvents($serviceName)
    {

        $events = array();

        $sql = 'SELECT * FROM `EVENTS` grid LEFT JOIN `EVENT_PERMS` clk ON grid.NAME=clk.EVENT_NAME WHERE clk.SERVICE_NAME LIKE :services ORDER BY grid.NAME ASC';

        $results = $this->_db->prepare($sql);
        $results->execute([':services' => $serviceName]);
        $results->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $results->fetch())
            $events[$row['NAME']] = $row;


        return $events;

    }

    public function getRules($clockName)
    {

        $events = array();
        $sql = "SELECT * FROM RULE_LINES WHERE `CLOCK_NAME` = '" . $clockName . "' ORDER BY `CODE` ASC";
        $results = $this->_db->query($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $results->fetch())
            $events[] = $row;

        $results = NULL;



        return $events;

    }

    public function isValidClock($clocks, $name)
    {
        $valid = FALSE;

        if (isset($clocks[$name]))
            $valid = TRUE;
        return $valid;
    }

    public function getClock($clocks, $clockName)
    {

        $events = array();

        if ($this->isValidClock($clocks, $clockName)) {

            $sql = "SELECT * FROM CLOCK_LINES WHERE CLOCK_NAME LIKE '" . $clockName . "' ORDER BY `START_TIME` ASC";

            $results = $this->_db->query($sql);
            $results->setFetchMode(PDO::FETCH_ASSOC);

            while ($row = $results->fetch())
                $events[] = $row;

            $results = NULL;

        }
        return $events;

    }

    public function clockExists($name)
    {

        $exists = false;

        $sql = 'SELECT `NAME` AS `CLOCK_COUNT` FROM `CLOCKS` WHERE `NAME` = ?';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(1, $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0)
            $exists = true;

        return $exists;

    }

    public function checkRemoveExistEvent($name)
    {



        $sql = 'SELECT * FROM `CLOCK_LINES` WHERE `EVENT_NAME` = :evname';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':evname', $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {
            return false;

        } else {
            return true;
        }



    }

    public function removeClock($clockname)
    {
        //First clear grid
        $newname = "";
        $sql = 'UPDATE `SERVICE_CLOCKS` SET `CLOCK_NAME` = :newName
                WHERE `CLOCK_NAME` = :oldname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $newname);
        $stmt->bindParam(':oldname', $clockname);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            $sql = "DELETE FROM CLOCK_LINES WHERE CLOCK_NAME = '$clockname'";

            if ($this->_db->query($sql) === FALSE) {
                return false;
            } else {
                $sql = "DELETE FROM CLOCK_PERMS WHERE CLOCK_NAME = '$clockname'";

                if ($this->_db->query($sql) === FALSE) {
                    return false;
                } else {
                    $sql = "DELETE FROM CLOCKS WHERE NAME = '$clockname'";

                    if ($this->_db->query($sql) === FALSE) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }
    }

    public function clearClockGrid($servicename)
    {
        for ($rowruns = 0; $rowruns < 168; $rowruns++) {
            $clockname = "";
            $sql = 'UPDATE `SERVICE_CLOCKS` SET `CLOCK_NAME` = :newName
                WHERE `SERVICE_NAME` = :serviceName AND `HOUR` = :hoUR';
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':newName', $clockname);
            $stmt->bindParam(':serviceName', $servicename);
            $stmt->bindParam(':hoUR', $rowruns);
            $stmt->execute();
        }
        return true;
    }

    public function setAllClockGrid($servicename, $clockname)
    {
        for ($rowruns = 0; $rowruns < 168; $rowruns++) {
            $sql = 'UPDATE `SERVICE_CLOCKS` SET `CLOCK_NAME` = :newName
                WHERE `SERVICE_NAME` = :serviceName AND `HOUR` = :hoUR';
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':newName', $clockname);
            $stmt->bindParam(':serviceName', $servicename);
            $stmt->bindParam(':hoUR', $rowruns);
            $stmt->execute();
        }
        return true;
    }

    public function renameClockGrid($oldName, $newName, $servicename, $hour)
    {
        if ($oldName == "") {
            $sql = 'UPDATE `SERVICE_CLOCKS` SET `CLOCK_NAME` = :newName
                WHERE `SERVICE_NAME` = :serviceName AND `HOUR` = :hoUR';
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':newName', $newName);
            $stmt->bindParam(':serviceName', $servicename);
            $stmt->bindParam(':hoUR', $hour);

            if ($stmt->execute() === FALSE) {
                return false;
            } else {
                return true;
            }
        } else {

            $sql = 'UPDATE `SERVICE_CLOCKS` SET `CLOCK_NAME` = :newName
                WHERE `CLOCK_NAME` = :oldName AND `SERVICE_NAME` = :serviceName AND `HOUR` = :hoUR';
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':newName', $newName);
            $stmt->bindParam(':oldName', $oldName);
            $stmt->bindParam(':serviceName', $servicename);
            $stmt->bindParam(':hoUR', $hour);

            if ($stmt->execute() === FALSE) {
                return false;
            } else {
                return true;
            }

        }

    }

    public function renameEvent($name, $oldname)
    {
        $sql = 'UPDATE `CLOCK_LINES` SET `EVENT_NAME` = :newName
                WHERE `EVENT_NAME` = :oldName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $name);
        $stmt->bindParam(':oldName', $oldname);
        $stmt->execute();

        $sql1 = 'UPDATE `EVENT_PERMS` SET `EVENT_NAME` = :newName
                WHERE `EVENT_NAME` = :oldName';
        $stmt1 = $this->_db->prepare($sql1);
        $stmt1->bindParam(':newName', $name);
        $stmt1->bindParam(':oldName', $oldname);
        $stmt1->execute();

        $sql2 = 'UPDATE `EVENT_LINES` SET `EVENT_NAME` = :newName
                WHERE `EVENT_NAME` = :oldName';
        $stmt2 = $this->_db->prepare($sql2);
        $stmt2->bindParam(':newName', $name);
        $stmt2->bindParam(':oldName', $oldname);
        $stmt2->execute();

        $sql3 = 'UPDATE `EVENTS` SET `NAME` = :newName
                WHERE `NAME` = :oldName';
        $stmt3 = $this->_db->prepare($sql3);
        $stmt3->bindParam(':newName', $name);
        $stmt3->bindParam(':oldName', $oldname);

        if ($stmt3->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function addEvent($name)
    {


        $sql = 'INSERT INTO `EVENTS` (`NAME`)
                VALUES (:name)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
            return false;
        } else {
            return true;
        }

    }

    public function addEventPerms($name, $service)
    {


        $sql = 'INSERT INTO `EVENT_PERMS` (`EVENT_NAME`, `SERVICE_NAME`)
                VALUES (:name, :servicename)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':servicename', $service);

        if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
            return false;
        } else {
            return true;
        }

    }

    public function removeEvent($name)
    {

        $sql = "DELETE FROM EVENT_PERMS WHERE EVENT_NAME = '$name'";
        $this->_db->query($sql);
        $sql2 = "DELETE FROM EVENT_LINES WHERE EVENT_NAME = '$name'";
        $this->_db->query($sql2);
        $sql3 = "DELETE FROM EVENTS WHERE NAME = '$name'";

        if ($this->_db->query($sql3) === FALSE) {
            return false;
        } else {
            return true;
        }


    }

    public function removeEventPerms($name)
    {

        $sql = "DELETE FROM EVENT_PERMS WHERE EVENT_NAME = '$name'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            return true;
        }


    }
    public function addClock($name, $shortName, $colour, $artistSeparation, $remarks)
    {

        if (substr($colour, 0, 1) != '#')
            $colour = '#' . $colour;

        $sql = 'INSERT INTO `CLOCKS` (`NAME`, `SHORT_NAME`, `ARTISTSEP`, `COLOR`, `REMARKS`)
                VALUES (:name, :shortName, :artistSeparation, :colour, :remarks)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':shortName', $shortName);
        $stmt->bindParam(':artistSeparation', $artistSeparation);
        $stmt->bindParam(':colour', $colour);
        $stmt->bindParam(':remarks', $remarks);

        if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
            return false;
        } else {
            return true;
        }

    }

    public function clockCodeExists($name)
    {

        $exists = false;

        $sql = 'SELECT `NAME` AS `CLOCK_COUNT` FROM `CLOCKS` WHERE `SHORT_NAME` = ?';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(1, $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0)
            $exists = true;

        return $exists;

    }

    public function saveEvent($clock, $event, $start, $length)
    {
        $sql = 'INSERT INTO `CLOCK_LINES` (`CLOCK_NAME`, `EVENT_NAME`, `START_TIME`, `LENGTH`)
                VALUES (:name, :evname, :starttime, :lengths)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':name', $clock);
        $stmt->bindParam(':evname', $event);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':lengths', $length);

        if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
            return false;
        } else {
            return true;
        }

    }

    public function eventUpdate($name, $cue, $hardtime, $hardtype, $firsttrans, $defaulttans, $autofill, $autofillunder, $import, $impstart, $impend, $nested, $schedgroup, $artist, $title, $have1, $have2, $color, $notes)
    {

        $sql = 'UPDATE `EVENTS` SET `PREPOSITION` = :preposition, `TIME_TYPE` = :timetype, `GRACE_TIME` = :gracetime, `FIRST_TRANS_TYPE` = :firsttrans, `DEFAULT_TRANS_TYPE` = :defaulttrans, `USE_AUTOFILL` = :autofill, `AUTOFILL_SLOP` = :autofillslop, `IMPORT_SOURCE` = :impsource, `START_SLOP` = :startslop, `END_SLOP` = :endslop, `NESTED_EVENT` = :nestedev, `SCHED_GROUP` = :schedgroup, `ARTIST_SEP` = :artistep, `TITLE_SEP` = :titlesep, `HAVE_CODE` = :havecode, `HAVE_CODE2` = :havecode2, `COLOR` = :color, `REMARKS` = :remarks 
                WHERE `NAME` = :evname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':preposition', $cue);
        $stmt->bindParam(':timetype', $hardtime);
        $stmt->bindParam(':gracetime', $hardtype);
        $stmt->bindParam(':firsttrans', $hardtime);
        $stmt->bindParam(':defaulttrans', $defaulttans);
        $stmt->bindParam(':autofill', $autofill);
        $stmt->bindParam(':autofillslop', $autofillunder);
        $stmt->bindParam(':impsource', $import);
        $stmt->bindParam(':startslop', $impstart);
        $stmt->bindParam(':endslop', $impend);
        $stmt->bindParam(':nestedev', $nested);
        $stmt->bindParam(':schedgroup', $schedgroup);
        $stmt->bindParam(':artistep', $artist);
        $stmt->bindParam(':titlesep', $title);
        $stmt->bindParam(':havecode', $have1);
        $stmt->bindParam(':havecode2', $have2);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':remarks', $notes);
        $stmt->bindParam(':evname', $name);
        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function editimptrans($id, $trans)
    {

        $sql = 'UPDATE `EVENT_LINES` SET `TRANS_TYPE` = :trans 
                WHERE `ID` = :ido';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':trans', $trans);
        $stmt->bindParam(':ido', $id);
        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function editvpnote($id, $note)
    {

        $sql = 'UPDATE `EVENT_LINES` SET `MARKER_COMMENT` = :comm 
                WHERE `ID` = :ido';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':comm', $note);
        $stmt->bindParam(':ido', $id);
        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function addcartimp($event, $cart, $ctype, $type, $imp)
    {
        $sql = 'SELECT * FROM `EVENT_LINES` WHERE `TYPE` = :thetype AND `EVENT_NAME` = :evname';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thetype', $imp);
        $stmt->bindParam(':evname', $event);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totrows = $stmt->rowCount();
        $stmt = NULL;
        if ($type == 1) {
            if ($ctype == 1) {
                $eventtype = 0;
            } else {
                $eventtype = 2;
            }
            $trans = 0;
            $sql = 'INSERT INTO `EVENT_LINES` (`EVENT_NAME`, `TYPE`, `COUNT`, `EVENT_TYPE`,  `CART_NUMBER`, `TRANS_TYPE`)
                VALUES (:event, :imp, :totrows, :evtype, :cartno, :trans)';

            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':event', $event);
            $stmt->bindParam(':imp', $imp);
            $stmt->bindParam(':totrows', $totrows);
            $stmt->bindParam(':evtype', $eventtype);
            $stmt->bindParam(':cartno', $cart);
            $stmt->bindParam(':trans', $trans);

            if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function addvtnote($event, $note, $type, $imp)
    {
        $sql = 'SELECT * FROM `EVENT_LINES` WHERE `TYPE` = :thetype AND `EVENT_NAME` = :evname';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thetype', $imp);
        $stmt->bindParam(':evname', $event);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totrows = $stmt->rowCount();
        $stmt = NULL;

        if ($type == 2) {
            $eventtype = 6;
            $trans = 0;
            $sql = 'INSERT INTO `EVENT_LINES` (`EVENT_NAME`, `TYPE`, `COUNT`, `EVENT_TYPE`, `TRANS_TYPE`, `MARKER_COMMENT`)
                VALUES (:event, :imp, :totrows, :evtype, :trans, :note)';

            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':event', $event);
            $stmt->bindParam(':imp', $imp);
            $stmt->bindParam(':totrows', $totrows);
            $stmt->bindParam(':evtype', $eventtype);
            $stmt->bindParam(':trans', $trans);
            $stmt->bindParam(':note', $note);

            if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
                return false;
            } else {
                return true;
            }
        } else if ($type == 3) {
            $eventtype = 1;
            $trans = 0;
            $sql = 'INSERT INTO `EVENT_LINES` (`EVENT_NAME`, `TYPE`, `COUNT`, `EVENT_TYPE`, `TRANS_TYPE`, `MARKER_COMMENT`)
                VALUES (:event, :imp, :totrows, :evtype, :trans, :note)';

            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':event', $event);
            $stmt->bindParam(':imp', $imp);
            $stmt->bindParam(':totrows', $totrows);
            $stmt->bindParam(':evtype', $eventtype);
            $stmt->bindParam(':trans', $trans);
            $stmt->bindParam(':note', $note);

            if ($stmt->execute() === FALSE || $stmt->rowCount() != 1) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function removeEvenetLine($id, $row, $type, $evname)
    {
        $sql = 'SELECT * FROM `EVENT_LINES` WHERE `TYPE` = :thetype AND `EVENT_NAME` = :evname';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thetype', $type);
        $stmt->bindParam(':evname', $evname);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totrows = $stmt->rowCount();
        $stmt = NULL;

        $lastrow = $totrows - 1;

        $i = $row + 1;

        $sql = "DELETE FROM EVENT_LINES WHERE ID = '$id'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            while ($i <= $lastrow) {


                $sqlcheck = 'SELECT * FROM `EVENT_LINES`
                    WHERE `EVENT_NAME` = :evname AND `COUNT` = :countrow AND `TYPE` = :thetype';

                $stmtc = $this->_db->prepare($sqlcheck);
                $stmtc->bindParam(':evname', $evname);
                $stmtc->bindParam(':countrow', $i);
                $stmtc->bindParam(':thetype', $type);
                $stmtc->setFetchMode(PDO::FETCH_ASSOC);
                $stmtc->execute();
                $oldid = "";
                while ($row = $stmtc->fetch()) {
                    $newspot = $i - 1;
                    $sql1 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                    $stmt1 = $this->_db->prepare($sql1);
                    $stmt1->bindParam(':newplace', $newspot);
                    $stmt1->bindParam(':theid', $row['ID']);
                    $stmt1->execute();
                }
                $i++;
            }

            return true;
        }


    }

    public function sortEvent($id, $evname, $order, $dir, $type)
    {

        $sql = 'SELECT * FROM `EVENT_LINES` WHERE `TYPE` = :thetype AND `EVENT_NAME` = :evname';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':thetype', $type);
        $stmt->bindParam(':evname', $evname);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totrows = $stmt->rowCount();
        $stmt = NULL;

        if ($dir == 1) {
            if ($order == 0) {
                return false;
            } else {
                $newplace = $order - 1;
                $sqlcheck = 'SELECT * FROM `EVENT_LINES`
                WHERE `EVENT_NAME` = :evname AND `COUNT` = :countrow AND `TYPE` = :thetype';

                $stmtc = $this->_db->prepare($sqlcheck);
                $stmtc->bindParam(':evname', $evname);
                $stmtc->bindParam(':countrow', $newplace);
                $stmtc->bindParam(':thetype', $type);
                $stmtc->setFetchMode(PDO::FETCH_ASSOC);
                $stmtc->execute();
                $oldid = "";
                while ($row = $stmtc->fetch()) {
                    $sql1 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                    $stmt1 = $this->_db->prepare($sql1);
                    $stmt1->bindParam(':newplace', $totrows);
                    $stmt1->bindParam(':theid', $row['ID']);
                    $stmt1->execute();
                    $oldid = $row['ID'];
                }
                $stmtc = NULL;

                $sql2 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                $stmt2 = $this->_db->prepare($sql2);
                $stmt2->bindParam(':newplace', $newplace);
                $stmt2->bindParam(':theid', $id);
                $stmt2->execute();
                $sql3 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                $stmt3 = $this->_db->prepare($sql3);
                $stmt3->bindParam(':newplace', $order);
                $stmt3->bindParam(':theid', $oldid);
                if ($stmt3->execute() === FALSE) {
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $lastrow = $totrows - 1;

            if ($order == $lastrow) {
                return false;
            } else {
                $newplace = $order + 1;
                $sqlcheck = 'SELECT * FROM `EVENT_LINES`
                WHERE `EVENT_NAME` = :evname AND `COUNT` = :countrow AND `TYPE` = :thetype';

                $stmtc = $this->_db->prepare($sqlcheck);
                $stmtc->bindParam(':evname', $evname);
                $stmtc->bindParam(':countrow', $newplace);
                $stmtc->bindParam(':thetype', $type);
                $stmtc->setFetchMode(PDO::FETCH_ASSOC);
                $stmtc->execute();
                $oldid = "";
                while ($row = $stmtc->fetch()) {
                    $sql1 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                    $stmt1 = $this->_db->prepare($sql1);
                    $stmt1->bindParam(':newplace', $totrows);
                    $stmt1->bindParam(':theid', $row['ID']);
                    $stmt1->execute();
                    $oldid = $row['ID'];
                }
                $stmtc = NULL;
                $sql2 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                $stmt2 = $this->_db->prepare($sql2);
                $stmt2->bindParam(':newplace', $newplace);
                $stmt2->bindParam(':theid', $id);
                $stmt2->execute();
                $sql3 = 'UPDATE `EVENT_LINES` SET `COUNT` = :newplace WHERE `ID` = :theid';
                $stmt3 = $this->_db->prepare($sql3);
                $stmt3->bindParam(':newplace', $order);
                $stmt3->bindParam(':theid', $oldid);
                if ($stmt3->execute() === FALSE) {
                    return false;
                } else {
                    return true;
                }
            }
        }



    }

    public function updateEvent($event, $start, $length, $eventid)
    {

        $sql = 'UPDATE `CLOCK_LINES` SET `EVENT_NAME` = :newName, `START_TIME` = :starttime, `LENGTH` = :lengths
                WHERE `ID` = :id';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $event);
        $stmt->bindParam(':starttime', $start);
        $stmt->bindParam(':lengths', $length);
        $stmt->bindParam(':id', $eventid);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }


    }

    public function removeClockEvent($id)
    {

        $sql = "DELETE FROM CLOCK_LINES WHERE ID = '$id'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            return true;
        }


    }

    public function removeClockPerms($name)
    {

        $sql = "DELETE FROM CLOCK_PERMS WHERE CLOCK_NAME = '$name'";

        if ($this->_db->query($sql) === FALSE) {
            return false;
        } else {
            return true;
        }


    }

    public function updateClockRules($clockname, $code, $max, $min, $not, $or, $or2)
    {

        $sql = 'UPDATE `RULE_LINES` SET `MAX_ROW` = :maxrow, `MIN_WAIT` = :minwait, `NOT_AFTER` = :notafter, `OR_AFTER` = :orafter, `OR_AFTER_II` = :orafter2
                WHERE `CLOCK_NAME` = :clockname AND `CODE` = :codes';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':maxrow', $max);
        $stmt->bindParam(':minwait', $min);
        $stmt->bindParam(':notafter', $not);
        $stmt->bindParam(':orafter', $or);
        $stmt->bindParam(':orafter2', $or2);
        $stmt->bindParam(':clockname', $clockname);
        $stmt->bindParam(':codes', $code);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }


    }

    public function renameClockRules($oldName, $newName)
    {

        $sql = 'UPDATE `RULE_LINES` SET `CLOCK_NAME` = :newName
                WHERE `CLOCK_NAME` = :oldName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $newName);
        $stmt->bindParam(':oldName', $oldName);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }


    }

    public function renameClockLines($oldName, $newName)
    {

        $sql = 'UPDATE `CLOCK_LINES` SET `CLOCK_NAME` = :newName
                WHERE `CLOCK_NAME` = :oldName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $newName);
        $stmt->bindParam(':oldName', $oldName);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function renameClock($oldName, $newName)
    {

        $sql = 'UPDATE `CLOCKS` SET `NAME` = :newName WHERE `NAME` = :oldName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $newName);
        $stmt->bindParam(':oldName', $oldName);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function renameClockPerms($oldName, $newName)
    {

        $sql = 'UPDATE `CLOCK_PERMS` SET `CLOCK_NAME` = :newName
                WHERE `CLOCK_NAME` = :oldName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newName', $newName);
        $stmt->bindParam(':oldName', $oldName);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function addClockRules($clockname)
    {
        $sql = 'SELECT * FROM SCHED_CODES';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totalCount = $stmt->rowCount();
        if ($totalCount > 0) {
            $sql = 'INSERT INTO `RULE_LINES` (`CLOCK_NAME`, `CODE`)
                  VALUES ';

            $i = 1;

            while ($row = $stmt->fetch()) {

                $code = $row['CODE'];

                $sql .= "('$clockname','$code')";

                if ($i == $totalCount) {
                    $sql .= ";";
                } else {
                    $sql .= ",";
                }

                $i++;

            }

            $stmt = $this->_db->prepare($sql);

            if ($stmt->execute() === FALSE || $stmt->rowCount() < 1) {
                return false;

            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function copyClock($oldname, $name, $code)
    {
        $sql = 'SELECT * FROM CLOCKS WHERE `NAME` = :oldName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':oldName', $oldname);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $totalCount = $stmt->rowCount();
        if ($totalCount > 0) {
            $sql = 'INSERT INTO `CLOCKS` (`NAME`, `SHORT_NAME`, `ARTISTSEP`, `COLOR`, `REMARKS`)
                VALUES (:name, :shortName, :artistSeparation, :colour, :remarks)';
            while ($row = $stmt->fetch()) {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':shortName', $code);
                $stmt->bindParam(':artistSeparation', $row['ARTISTSEP']);
                $stmt->bindParam(':colour', $row['COLOR']);
                $stmt->bindParam(':remarks', $row['REMARKS']);

                if ($stmt->execute() === FALSE) {
                    return false;
                } else {
                    return true;
                }

            }
        } else {
            return false;
        }

    }

    public function copyClockRules($sourceName, $copyName)
    {

        $sql = 'SELECT * FROM RULE_LINES WHERE `CLOCK_NAME` = :sourceName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':sourceName', $sourceName);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $totalCount = $stmt->rowCount();
        if ($totalCount > 0) {

            $sql = 'INSERT INTO `RULE_LINES` (`CLOCK_NAME`, `CODE`, `MAX_ROW`, `MIN_WAIT`, `NOT_AFTER`, `OR_AFTER`, `OR_AFTER_II`)
                  VALUES ';

            $i = 1;

            while ($row = $stmt->fetch()) {

                $code = $row['CODE'];
                $max_row = $row['MAX_ROW'];
                $min_wait = $row['MIN_WAIT'];
                $not_after = $row['NOT_AFTER'];
                $or_after = $row['OR_AFTER'];
                $or_after2 = $row['OR_AFTER_II'];
                $sql .= "('$copyName','$code','$max_row','$min_wait','$not_after','$or_after','$or_after2')";

                if ($i == $totalCount) {
                    $sql .= ";";
                } else {
                    $sql .= ",";
                }

                $i++;

            }

            $stmt = $this->_db->prepare($sql);

            if ($stmt->execute() === FALSE || $stmt->rowCount() < 1) {
                return false;

            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    public function copyClockEvents($sourceName, $copyName)
    {

        $sql = 'SELECT * FROM CLOCK_LINES WHERE `CLOCK_NAME` = :sourceName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':sourceName', $sourceName);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $totalCount = $stmt->rowCount();
        if ($totalCount > 0) {

            $sql = 'INSERT INTO `CLOCK_LINES` (`CLOCK_NAME`, `EVENT_NAME`, `START_TIME`, `LENGTH`)
                  VALUES ';

            $i = 1;


            while ($row = $stmt->fetch()) {

                $ev_name = $row['EVENT_NAME'];
                $start_time = $row['START_TIME'];
                $length = $row['LENGTH'];
                $sql .= "('$copyName','$ev_name','$start_time','$length')";

                if ($i == $totalCount) {
                    $sql .= ";";
                } else {
                    $sql .= ",";
                }

                $i++;

            }

            $stmt = $this->_db->prepare($sql);

            if ($stmt->execute() === FALSE || $stmt->rowCount() < 1) {
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }

    }

    public function copyClockPerms($sourceName, $copyName)
    {

        $sql = 'SELECT * FROM CLOCK_PERMS WHERE `CLOCK_NAME` = :sourceName GROUP BY SERVICE_NAME';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':sourceName', $sourceName);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $totalCount = $stmt->rowCount();
        if ($totalCount > 0) {

            $sql = 'INSERT INTO `CLOCK_PERMS` (`CLOCK_NAME`, `SERVICE_NAME`)
                  VALUES ';

            $i = 1;


            while ($row = $stmt->fetch()) {

                $svc_name = $row['SERVICE_NAME'];
                $sql .= "('$copyName','$svc_name')";

                if ($i == $totalCount) {
                    $sql .= ";";
                } else {
                    $sql .= ",";
                }

                $i++;

            }

            $stmt = $this->_db->prepare($sql);

            if ($stmt->execute() === FALSE || $stmt->rowCount() < 1) {
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }

    }



    public function addClockPerms($name, $service)
    {

        $sql = 'INSERT INTO `CLOCK_PERMS` (`CLOCK_NAME`, `SERVICE_NAME`)
                VALUES (:name, :service)';

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':service', $service);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }



    }

    public function updateClockColour($clockName, $newColour)
    {

        $sql = 'UPDATE `CLOCKS` SET `COLOR` = :newColour WHERE `NAME` = :clockName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':newColour', $newColour);
        $stmt->bindParam(':clockName', $clockName);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function updateClockData($clockName, $clockcode, $remarks)
    {

        $sql = 'UPDATE `CLOCKS` SET `SHORT_NAME` = :shortName, `REMARKS` = :remarks WHERE `NAME` = :clockName';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':shortName', $clockcode);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':clockName', $clockName);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function updateLogData($logdata) {

        $sql = 'UPDATE `LOGS` SET `SERVICE` = :service, `DESCRIPTION` = :descr, `AUTO_REFRESH` = :autof, `START_DATE` = :sdate, `END_DATE` = :edate, `PURGE_DATE` = :pdate, `NEXT_ID` = :nid WHERE `NAME` = :logname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':service', $logdata['SERVICE']);
        $stmt->bindParam(':descr', $logdata['DESCRIPTION']);
        $stmt->bindParam(':autof', $logdata['AUTO_REFRESH']);
        $stmt->bindParam(':sdate', $logdata['START_DATE']);
        $stmt->bindParam(':edate', $logdata['END_DATE']);
        $stmt->bindParam(':pdate', $logdata['PURGE_DATE']);
        $stmt->bindParam(':nid', $logdata['NEXT_ID']);
        $stmt->bindParam(':logname', $logdata['NAME']);
        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function updateLogLine($lines)
    {

        $sql = 'UPDATE `LOG_LINES` SET `LINE_ID` = :lineid, `COUNT` = :count, `TYPE` = :type, `SOURCE` = :source, `START_TIME` = :starttime, `GRACE_TIME` = :grace,
        `CART_NUMBER` = :cart, `TIME_TYPE` = :timetype, `TRANS_TYPE` = :transtype, `START_POINT` = :startpoint, `END_POINT` = :endpoint, `FADEUP_POINT` = :fadeup,
        `FADEUP_GAIN` = :fadeupg, `FADEDOWN_POINT` = :fdownpoint, `FADEDOWN_GAIN` = :fdowngain, `SEGUE_START_POINT` = :segstartpoint, `SEGUE_END_POINT` = :segendpoint,
        `SEGUE_GAIN` = :seggain, `DUCK_UP_GAIN` = :duckupgain, `DUCK_DOWN_GAIN` = :duckdowngain, `COMMENT` = :comment, `LABEL` = :label, `ORIGIN_USER` = :originuser,
        `ORIGIN_DATETIME` = :origindatetime, `EVENT_LENGTH` = :evlength, `LINK_EVENT_NAME` = :linkevname, `LINK_START_TIME` = :linkstarttime, `LINK_LENGTH` = :linklength,
        `LINK_START_SLOP` = :linkstartslop, `LINK_END_SLOP` = :linkendslop, `LINK_ID` = :linkid, `LINK_EMBEDDED` = :linkembedded, `EXT_START_TIME` = :extstarttime,
        `EXT_LENGTH` = :extlength, `EXT_CART_NAME` = :extcartname, `EXT_DATA` = :extdata, `EXT_EVENT_ID` = :extevid, `EXT_ANNC_TYPE` = :extannctype WHERE `ID` = :loglineid';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':lineid', $lines['LINE_ID']);
        $stmt->bindParam(':count', $lines['COUNT']);
        $stmt->bindParam(':type', $lines['TYPE']);
        $stmt->bindParam(':source', $lines['SOURCE']);
        $stmt->bindParam(':starttime', $lines['START_TIME']);
        $stmt->bindParam(':grace', $lines['GRACE_TIME']);
        $stmt->bindParam(':cart', $lines['CART_NUMBER']);
        $stmt->bindParam(':timetype', $lines['TIME_TYPE']);
        $stmt->bindParam(':transtype', $lines['TRANS_TYPE']);
        $stmt->bindParam(':startpoint', $lines['START_POINT']);
        $stmt->bindParam(':endpoint', $lines['END_POINT']);
        $stmt->bindParam(':fadeup', $lines['FADEUP_POINT']);
        $stmt->bindParam(':fadeupg', $lines['FADEUP_GAIN']);
        $stmt->bindParam(':fdownpoint', $lines['FADEDOWN_POINT']);
        $stmt->bindParam(':fdowngain', $lines['FADEDOWN_GAIN']);
        $stmt->bindParam(':segstartpoint', $lines['SEGUE_START_POINT']);
        $stmt->bindParam(':segendpoint', $lines['SEGUE_END_POINT']);
        $stmt->bindParam(':seggain', $lines['SEGUE_GAIN']);
        $stmt->bindParam(':duckupgain', $lines['DUCK_UP_GAIN']);
        $stmt->bindParam(':duckdowngain', $lines['DUCK_DOWN_GAIN']);
        $stmt->bindParam(':comment', $lines['COMMENT']);
        $stmt->bindParam(':label', $lines['LABEL']);
        $stmt->bindParam(':originuser', $lines['ORIGIN_USER']);
        $stmt->bindParam(':origindatetime', $lines['ORIGIN_DATETIME']);
        $stmt->bindParam(':evlength', $lines['EVENT_LENGTH']);
        $stmt->bindParam(':linkevname', $lines['LINK_EVENT_NAME']);
        $stmt->bindParam(':linkstarttime', $lines['LINK_START_TIME']);
        $stmt->bindParam(':linklength', $lines['LINK_LENGTH']);
        $stmt->bindParam(':linkstartslop', $lines['LINK_START_SLOP']);
        $stmt->bindParam(':linkendslop', $lines['LINK_END_SLOP']);
        $stmt->bindParam(':linkid', $lines['LINK_ID']);
        $stmt->bindParam(':linkembedded', $lines['LINK_EMBEDDED']);
        $stmt->bindParam(':extstarttime', $lines['EXT_START_TIME']);
        $stmt->bindParam(':extlength', $lines['EXT_LENGTH']);
        $stmt->bindParam(':extcartname', $lines['EXT_CART_NAME']);
        $stmt->bindParam(':extdata', $lines['EXT_DATA']);
        $stmt->bindParam(':extevid', $lines['EXT_EVENT_ID']);
        $stmt->bindParam(':extannctype', $lines['EXT_ANNC_TYPE']);
        $stmt->bindParam(':loglineid', $lines['ID']);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function saveNewLine($lines)
    {

        $sql = 'INSERT INTO `LOG_LINES` (`LOG_NAME`, `LINE_ID`, `COUNT`, `TYPE`, `SOURCE`, `START_TIME`, `GRACE_TIME`, `CART_NUMBER`, `TIME_TYPE`, `TRANS_TYPE`,
        `START_POINT`, `END_POINT`, `FADEUP_POINT`, `FADEUP_GAIN`, `FADEDOWN_POINT`, `FADEDOWN_GAIN`, `SEGUE_START_POINT`, `SEGUE_END_POINT`, `SEGUE_GAIN`, `DUCK_UP_GAIN`,
        `DUCK_DOWN_GAIN`, `COMMENT`, `LABEL`, `ORIGIN_USER`, `ORIGIN_DATETIME`, `EVENT_LENGTH`, `LINK_EVENT_NAME`, `LINK_START_TIME`, `LINK_LENGTH`, `LINK_START_SLOP`,
        `LINK_END_SLOP`, `LINK_ID`, `LINK_EMBEDDED`, `EXT_START_TIME`, `EXT_LENGTH`, `EXT_CART_NAME`, `EXT_DATA`, `EXT_EVENT_ID`, `EXT_ANNC_TYPE`)
                VALUES (:logname, :lineid, :count, :type, :source, :starttime, :grace, :cart, :timetype, :transtype, :startpoint, :endpoint, :fadeup,
        :fadeupg, :fdownpoint, :fdowngain, :segstartpoint, :segendpoint, :seggain, :duckupgain, :duckdowngain, :comment, :label, :originuser, :origindatetime, :evlength,
        :linkevname, :linkstarttime, :linklength, :linkstartslop, :linkendslop, :linkid, :linkembedded, :extstarttime, :extlength, :extcartname, :extdata,
        :extevid, :extannctype)';        
        
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':logname', $lines['LOG_NAME']);
        $stmt->bindParam(':lineid', $lines['LINE_ID']);
        $stmt->bindParam(':count', $lines['COUNT']);
        $stmt->bindParam(':type', $lines['TYPE']);
        $stmt->bindParam(':source', $lines['SOURCE']);
        $stmt->bindParam(':starttime', $lines['START_TIME']);
        $stmt->bindParam(':grace', $lines['GRACE_TIME']);
        $stmt->bindParam(':cart', $lines['CART_NUMBER']);
        $stmt->bindParam(':timetype', $lines['TIME_TYPE']);
        $stmt->bindParam(':transtype', $lines['TRANS_TYPE']);
        $stmt->bindParam(':startpoint', $lines['START_POINT']);
        $stmt->bindParam(':endpoint', $lines['END_POINT']);
        $stmt->bindParam(':fadeup', $lines['FADEUP_POINT']);
        $stmt->bindParam(':fadeupg', $lines['FADEUP_GAIN']);
        $stmt->bindParam(':fdownpoint', $lines['FADEDOWN_POINT']);
        $stmt->bindParam(':fdowngain', $lines['FADEDOWN_GAIN']);
        $stmt->bindParam(':segstartpoint', $lines['SEGUE_START_POINT']);
        $stmt->bindParam(':segendpoint', $lines['SEGUE_END_POINT']);
        $stmt->bindParam(':seggain', $lines['SEGUE_GAIN']);
        $stmt->bindParam(':duckupgain', $lines['DUCK_UP_GAIN']);
        $stmt->bindParam(':duckdowngain', $lines['DUCK_DOWN_GAIN']);
        $stmt->bindParam(':comment', $lines['COMMENT']);
        $stmt->bindParam(':label', $lines['LABEL']);
        $stmt->bindParam(':originuser', $lines['ORIGIN_USER']);
        $stmt->bindParam(':origindatetime', $lines['ORIGIN_DATETIME']);
        $stmt->bindParam(':evlength', $lines['EVENT_LENGTH']);
        $stmt->bindParam(':linkevname', $lines['LINK_EVENT_NAME']);
        $stmt->bindParam(':linkstarttime', $lines['LINK_START_TIME']);
        $stmt->bindParam(':linklength', $lines['LINK_LENGTH']);
        $stmt->bindParam(':linkstartslop', $lines['LINK_START_SLOP']);
        $stmt->bindParam(':linkendslop', $lines['LINK_END_SLOP']);
        $stmt->bindParam(':linkid', $lines['LINK_ID']);
        $stmt->bindParam(':linkembedded', $lines['LINK_EMBEDDED']);
        $stmt->bindParam(':extstarttime', $lines['EXT_START_TIME']);
        $stmt->bindParam(':extlength', $lines['EXT_LENGTH']);
        $stmt->bindParam(':extcartname', $lines['EXT_CART_NAME']);
        $stmt->bindParam(':extdata', $lines['EXT_DATA']);
        $stmt->bindParam(':extevid', $lines['EXT_EVENT_ID']);
        $stmt->bindParam(':extannctype', $lines['EXT_ANNC_TYPE']);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function updateSchedTracks($sched, $log)
    {

        $sql = 'UPDATE `LOGS` SET `SCHEDULED_TRACKS` = :sched WHERE `NAME` = :logs';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':sched', $sched);
        $stmt->bindParam(':logs', $log);

        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }

    }

    public function updateLogModified($date, $log) {

        $sql = 'UPDATE `LOGS` SET `MODIFIED_DATETIME` = :datetim WHERE `NAME` = :logname';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':datetim', $date);
        $stmt->bindParam(':logname', $log);
        if ($stmt->execute() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    public function getClockHourName($service, $hour)
    {

        $clockname = '';
        $sql = "SELECT * FROM SERVICE_CLOCKS WHERE `SERVICE_NAME` = '" . $service . "' AND `HOUR` = '" . $hour . "'";
        $results = $this->_db->query($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $results->fetch())
            $clockname = $row['CLOCK_NAME'];

        $results = NULL;



        return $clockname;

    }

    public function getShortClockName($clock)
    {

        $shotname = '';
        $sql = "SELECT * FROM CLOCKS WHERE `NAME` = '" . $clock . "'";
        $results = $this->_db->query($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $results->fetch())
            $shotname = $row['SHORT_NAME'];

        $results = NULL;



        return $shotname;

    }

    public function getClockColor($clock)
    {

        $color = '';
        $sql = "SELECT * FROM CLOCKS WHERE `NAME` = '" . $clock . "'";
        $results = $this->_db->query($sql);
        $results->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $results->fetch())
            $color = $row['COLOR'];

        $results = NULL;



        return $color;

    }
    public function updateClockGrid($servicename, $hour, $clockname)
    {
            $sql = 'UPDATE `SERVICE_CLOCKS` SET `CLOCK_NAME` = :newName
                WHERE `SERVICE_NAME` = :serviceName AND `HOUR` = :hoUR';
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':newName', $clockname);
            $stmt->bindParam(':serviceName', $servicename);
            $stmt->bindParam(':hoUR', $hour);
            $stmt->execute();
        return true;
    }

}