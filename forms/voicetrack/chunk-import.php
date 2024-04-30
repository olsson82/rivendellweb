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
$fileId = $_POST['dzuuid'];
$chunkTotal = $_POST['dztotalchunkcount'];
$username = $_COOKIE['username'];
$password = $function->loadPass($username);
$fullname = $_COOKIE['fullname'];
$_RDWEB_API = $_COOKIE['rdWebAPI'];
$audiochannels = $_POST['audiochannels'];
$autotrim = $_POST['autotrim'];
$trimlevel = $_POST['trimlevel'];
$normalize = $_POST['normalize'];
$normalizelevel = $_POST['normalizelevel'];
$musicgroup = $_POST['musicgroup'];
$cart = $_POST['cartid'];
$logname = $_POST['logname'];
$lineid = $_POST['lineid'];
$wehavesched = 0;
if ($autotrim == 1) {
    $dotrim = $trimlevel;
} else {
    $dotrim = 0;
}

if ($normalize == 1) {
    $donormalize = $normalizelevel;
} else {
    $donormalize = 0;
}

$targetPath = '/tmp/';
$fileType = $_POST['fileName'];
$realfile = $targetPath . $fileId . '.' . $fileType;

$returnResponse = function ($info = null, $filelink = null, $status = "error") {
    die(
        json_encode(
            array(
                "status" => $status,
                "info" => $info,
                "file_link" => $filelink
            )
        )
    );
};
for ($i = 1; $i <= $chunkTotal; $i++) {

    $temp_file_path = realpath("{$targetPath}{$fileId}-{$i}.{$fileType}") or $returnResponse($ml->tr('IMPLOSTUP'));

    $chunk = file_get_contents($temp_file_path);
    if (empty($chunk))
        $returnResponse($ml->tr('IMPCHUNKEMP'));

    file_put_contents("{$targetPath}{$fileId}.{$fileType}", $chunk, FILE_APPEND | LOCK_EX);

    unlink($temp_file_path);
    if (file_exists($temp_file_path))
        $returnResponse($ml->tr('IMPTEMPNOTDEL'));

}

if (file_exists($realfile)) {
    if ($cart == 0) {
        $cart = $functions->rd_add_cart($cart, $musicgroup);
        $dbfunc->rd_updateVTCart($logname, $lineid, $cart, $username);
    }
    if (!$functions->rd_cart_exists($cart)) {
        $functions->rd_add_cart($cart, $musicgroup);
        $functions->rd_add_cut($cart);
    } else {
        if (!$functions->rd_cut_count($cart)) {
            $functions->rd_add_cut($cart);
        }
    }

    $date = date("D M d, Y G:i");
    $artist = $fullname;
    $title = $ml->tr('VOICETRACK');
    $comment = $ml->tr('IMPRECBY') . " " . $username . " " . $ml->tr('IMPON') . " " . $date . " " . $ml->tr('IMPFORLOG') . " " . $logname;
    $functions->rd_edit_VTcart($cart, $artist, $title, $comment, $logname);
    $ch = curl_init();
    $parameters = array(
        'COMMAND' => '2',
        'LOGIN_NAME' => $username,
        'PASSWORD' => $password,
        'CART_NUMBER' => $cart,
        'CUT_NUMBER' => '1',
        'CREATE' => '1',
        'GROUP_NAME' => $musicgroup,
        'CHANNELS' => $audiochannels,
        'NORMALIZATION_LEVEL' => $donormalize,
        'USE_METADATA' => '0',
        'AUTOTRIM_LEVEL' => $dotrim,
        'TITLE' => $title,
    );
    $files = array('FILENAME' => $realfile);
    $postfields = $functions->curl_custom_postfields($ch, $parameters, $files);
    curl_setopt($ch, CURLOPT_URL, $_RDWEB_API);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    if (preg_match('/ResponseCode>200</', $result, $matches)) {
        unlink($realfile);
        $returnResponse(null, null, "success");
    }
}


