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

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/settings.json')) {
  header('Location: installer.php');
  exit();
}
/******************************************************************************
 * THE SETTINGS ARE LOADED FROM JSON FILE. THIS CAN BE CHANGED IN THE SYSTEM. *
 ******************************************************************************/
$filepath = $_SERVER['DOCUMENT_ROOT'] . '/data/settings.json';
$json_string = file_get_contents($filepath);
$json_sett = json_decode($json_string, true);
/**********************************************************************
 * FOR RESET PASSWORD FUNCTION WE USE TO SAVE DATA IN JSON ENCRYPTED. *
 **********************************************************************/
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/reset.json')) {
  $reset_data = array();
} else {
  $filepath = $_SERVER['DOCUMENT_ROOT'] . '/data/reset.json';
  $json_string = file_get_contents($filepath);
  $reset_data = json_decode($json_string, true);
}
/**************************************************************
 * FOR TEMPORARY STORAGE OF LOG EDIT UNTIL SAVE TO RIVENDELL. *
 **************************************************************/
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json')) {
  $logedit_data = array();
} else {
  $filepath = $_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json';
  $json_string = file_get_contents($filepath);
  $logedit_data = json_decode($json_string, true);
}
ob_start();
session_start();
date_default_timezone_set($json_sett['timezone']);
define('DIR', $json_sett['sysurl']);
define('LOCAL_PATH_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('APPNAME', $json_sett['sysname']);
define('SMTPSERV', $json_sett['smtpserv']);
define('SMTPPORT', $json_sett['port']);
define('SMTPUSER', $json_sett['smtpusr']);
define('SMTPPASS', $json_sett['smtppass']);
define('SMTPFROM', $json_sett['smtpfrom']);
define('DEFAULTLANG', $json_sett['deflang']);
define('VERS', '0.2.9'); //DO NOT CHANGE THIS!
define('DBOK', '374'); //DO NOT CHANGE THIS!
define('SYSTIT', 'Rivendell Web Broadcast'); //DO NOT CHANGE THIS!
define('USERESET', $json_sett['usereset']);
define('AUTOTRIM', $json_sett['autotrim']);
define('NORMALIZE', $json_sett['normalize']);

/***************************************************
 * THE DATABASE INFORMATION GETS FROM RD.CONF FILE *
 ***************************************************/
function getDBDetails()
{
  $DBI = array();
  $ini_file = "/etc/rd.conf";
  if (!is_readable($ini_file)) {
    echo "$ini_file DOES NOT exist";
    exit(-1);
  }
  $ini_array = parse_ini_file($ini_file, true, INI_SCANNER_RAW);
  $DBI['username'] = $ini_array['mySQL']['Loginname'];
  $DBI['password'] = $ini_array['mySQL']['Password'];
  $DBI['hostname'] = $ini_array['mySQL']['Hostname'];
  $DBI['database'] = $ini_array['mySQL']['Database'];
  return $DBI;
}
$DBO = getDBDetails();
try {
  //create PDO connection
  $db = new PDO('mysql:host=' . $DBO['hostname'] . ';charset=utf8mb4;dbname=' . $DBO['database'], $DBO['username'], $DBO['password']);
  //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);//Suggested to uncomment on production websites
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Suggested to comment on production websites
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
  //show error
  echo '<p class="bg-danger">' . $e->getMessage() . '</p>';
  exit;
}
/***************************************************
 * THIS LOADS THE CLASSES FILES USED IN THE SYSTEM *
 ***************************************************/
include LOCAL_PATH_ROOT . '/classes/dbfunc.php';
include LOCAL_PATH_ROOT . '/classes/functions.php';
include LOCAL_PATH_ROOT . '/classes/getinfo.php';
include LOCAL_PATH_ROOT . '/classes/user.php';
include LOCAL_PATH_ROOT . '/classes/logmanager.php';
include LOCAL_PATH_ROOT . '/classes/multilang.php';
$dbfunc = new DBFunc($db);
$functions = new Functions($db);
$info = new Getinfo($db);
$user = new User($db);
$logfunc = new Log($db);
/**********************************************************************************
 * WE NEED TO CHECK SO THERE ARE NO LOGS THAT ARE LEFT IN THE LOG EDIT JSON FILE. *
 *             IF THERE ARE LOGS THAT NOT ARE IN USE, THEN REMOVE IT.             *
 **********************************************************************************/
 
 foreach ($logedit_data as $lines) {
  $field = $info->getLogInfo($lines['NAME'], "LOCK_DATETIME");
  $now = date('Y-m-d H:i:s', strtotime('-5 minutes'));
  if ($lines['LOCK_GUID'] != $info->getLogInfo($lines['NAME'], "LOCK_GUID") && strtotime($field) < strtotime($now)) {
    $code = $info->getLogInfo($lines['NAME'], "LOCK_GUID");
    $functions->removelock($lines['NAME'], $code);
    unset($logedit_data[$lines['NAME']]);
    $jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData);
  } else if($lines['LOCK_GUID'] == $info->getLogInfo($lines['NAME'], "LOCK_GUID") && strtotime($field) < strtotime($now)) {
    $code = $info->getLogInfo($lines['NAME'], "LOCK_GUID");
    $functions->removelock($lines['NAME'], $code);
    unset($logedit_data[$lines['NAME']]);
    $jsonData = json_encode($logedit_data, JSON_PRETTY_PRINT);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/logedit.json', $jsonData);
 }
} 
/**************************************************************************************
 *                  MULTILANGUAGE SUPPORT. LANGUAGE SAVED IN COOKIE.                  *
 * TO ADD UNTRANSLATED STRINGS TO LANGUAGE FILE, SET TO TRUE ON UNTRANSLATED_LOGGING. *
 *        THE SELECTED LANGUAGE FILE WILL GET UNTRANSLATED STRINGS IN COMMENT.        *
 **************************************************************************************/
$ml = new MultiLang($use_cookies = true, $untranslated_logging = false);
$ml->set_directory($_SERVER['DOCUMENT_ROOT'] . '/languages/');
$ml->setDefaultLang(DEFAULTLANG);
if (!isset($_COOKIE['lang'])) {
  $ml->setLanguage(DEFAULTLANG);
}
/*********************************************************************
 * THIS WILL WARN IF RIVENDELL NOT MATCH THE TESTED DATABASE VERSION *
 *********************************************************************/
$dbversold = 0;
if ($info->checkDBVers() > DBOK || $info->checkDBVers() < DBOK) {
  $dbversold = 1;
}
/**********************************************
 * LANGUAGE ARRAY FOR LANGUAGE SWITCH IN MENU *
 **********************************************/
$languagesArray = array(
  'en_US' => array(
    'text' => $ml->tr('ENGLISH'),
    'langcode' => 'en_US',
  ),
  'sv_SE' => array(
    'text' => $ml->tr('SWEDISH'),
    'langcode' => 'sv_SE',
  ),
);
