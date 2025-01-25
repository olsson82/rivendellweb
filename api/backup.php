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
$backupfolder = LOCAL_PATH_ROOT . "/data/backups/";
$backuptype = $json_sett["backups"]["autotype"];
$olderthan = $json_sett["backups"]["olderthan"];
$user = $DBO['username'];
$password = $DBO['password'];
$host = $DBO['hostname'];
$database = $DBO['database'];
$filenamerivsys = 'RivWebBroad-' . date('Y-m-d') . '-' . time() . '.zip';
$fileName = $DBO['database'] . '-' . date('Y-m-d') . '-' . time() . '.sql';
$fileNameZip = $DBO['database'] . '-' . date('Y-m-d') . '-' . time() . '.zip';
$dir = $backupfolder . $fileName;
$dir2 = LOCAL_PATH_ROOT . '/data/settings.json';
$dir3 = LOCAL_PATH_ROOT . '/data/grids.json';
$created = date("Y-m-d H:i:s");
$error = 0;

$files = glob(dirname(__FILE__) . $backupFolder . '*');
foreach ($files as $file) {
    if ($olderthan == 0) {
        if (is_file($file) && time() - filemtime($file) >= 60 * 60 * 24 * 7)
        unlink($file);
    } else if ($olderthan == 1) {
        if (is_file($file) && time() - filemtime($file) >= 30 * 24 * 60 * 60)
        unlink($file);
    }
    
}

if ($backuptype == 0) {
    /******************************
     * DO ONLY RIVENDELL DATABASE *
     ******************************/
    $cmd = "mysqldump -h {$host} -u {$user} --password={$password} {$database} > {$backupfolder}{$fileName}";
    exec($cmd);
    $zip = new ZipArchive();
    $zip_name = $backupfolder . 'backup-' . $fileNameZip;
    if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
        $error = 1;
    }
    $zip->addFile($dir, $fileName);
    $zip->close();
    unlink($dir);
    $json_sett["backups"]['backdata'][$fileNameZip]["backname"] = $fileNameZip;
    $json_sett["backups"]['backdata'][$fileNameZip]["created"] = $created;
    $json_sett["backups"]['backdata'][$fileNameZip]["includes"] = 0;
    $jsonsettings = json_encode($json_sett, JSON_UNESCAPED_SLASHES);
    if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/settings.json', $jsonsettings)) {
        $error = 1;
    }
} else if ($backuptype == 1) {
    /***********************************
     * DO ONLY RIVENDELL WEB BROADCAST *
     ***********************************/
    $zip = new ZipArchive();
    $zip_name = $backupfolder . 'backup-' . $filenamerivsys;
    if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
        $error = 1;
    }
    $zip->addFile($dir2, 'settings.json');
    if (file_exists($dir3)) {
        $zip->addFile($dir3, 'grids.json');
    }
    $zip->close();
    $json_sett["backups"]['backdata'][$filenamerivsys]["backname"] = $filenamerivsys;
    $json_sett["backups"]['backdata'][$filenamerivsys]["created"] = $created;
    $json_sett["backups"]['backdata'][$filenamerivsys]["includes"] = 1;
    $jsonsettings = json_encode($json_sett, JSON_UNESCAPED_SLASHES);
    if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/settings.json', $jsonsettings)) {
        $error = 1;
    }
} else {
    /**********
     * DO ALL *
     **********/
    $cmd = "mysqldump -h {$host} -u {$user} --password={$password} {$database} > {$backupfolder}{$fileName}";
    exec($cmd);
    $zip = new ZipArchive();
    $zip_name = $backupfolder . 'backup-' . $fileNameZip;
    if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
        $error = 1;
    }
    $zip->addFile($dir, $fileName);
    $zip->addFile($dir2, 'settings.json');
    if (file_exists($dir3)) {
        $zip->addFile($dir3, 'grids.json');
    }
    $zip->close();
    unlink($dir);
    $json_sett["backups"]['backdata'][$fileNameZip]["backname"] = $fileNameZip;
    $json_sett["backups"]['backdata'][$fileNameZip]["created"] = $created;
    $json_sett["backups"]['backdata'][$fileNameZip]["includes"] = 2;
    $jsonsettings = json_encode($json_sett, JSON_UNESCAPED_SLASHES);
    if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/settings.json', $jsonsettings)) {
        $error = 1;
    }
}