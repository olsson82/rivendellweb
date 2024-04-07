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
$username = $_COOKIE['username'];
$password = $_COOKIE['password'];
$fullname = $_COOKIE['fullname'];
$_RDWEB_API = $_COOKIE['rdWebAPI'];
$cut = $_GET['cut'];
$format = $_GET['format'];
$channels = $_GET['channels'];
$sample = $_GET['sample'];
$bit = $_GET['bit'];
$qual = $_GET['qual'];
$meta = $_GET['meta'];
$norma = $_GET['norma'];
$norlev = $_GET['norlev'];
$cartno =  substr($cut, 0, strpos($cut, "_"));
$cutno = substr($cut, strpos($cut, "_") + 1);

$filename = $info->getCartInfo($cartno, 'ARTIST').' - '.$info->getCartInfo($cartno, 'TITLE');
$startpoint = $info->getCutInfo($cut, 'START_POINT');
$endpoint = $info->getCutInfo($cut, 'END_POINT');
$ch = curl_init();
if ($format == '2' || $format == '3') {
    $headers = array('Content-Type: audio/mpeg');
} else if ($format == '0' || $format == '7') {
    $headers = array('Content-Type: audio/wav');
} else if ($format == '4') {
    $headers = array('Content-Type: audio/flac');
} else if ($format == '5') {
    $headers = array('Content-Type: audio/ogg');
}
if ($norma == 0) {
    $norlev = 0;
}
if ($format == '0' || $format == '7' || $format == '4' || $format == '5') {
    $bit = 0;
} else if ($bit == 'VBR') {
    $bit = 0;
}
if ($format == '0' || $format == '7' || $format == '4' || $format == '2' || $format == '3' && $bit != 'VBR') {
    $qual = 0;
}
$parameters = array(
    'COMMAND' => '1',
    'LOGIN_NAME' => $username,
    'PASSWORD' => $password,
    'CART_NUMBER' => $cartno,
    'CUT_NUMBER' => $cutno,
    'FORMAT' => $format,
    'CHANNELS' => $channels,
    'SAMPLE_RATE' => $sample,
    'BIT_RATE' => $bit,
    'QUALITY' => $qual,
    'START_POINT' => $startpoint,
    'END_POINT' => $endpoint,
    'NORMALIZATION_LEVEL' => $norlev,
    'ENABLE_METADATA' => $meta,
);

$options = array(
    CURLOPT_URL => $_RDWEB_API,
    CURLOPT_HEADER => false,
    CURLOPT_POST => 1,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $parameters,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true
);
curl_setopt_array($ch, $options);
$result = curl_exec($ch);
curl_close($ch);
echo $result;
if ($format == '2' || $format == '3') {
    header('Content-Type: audio/mpeg');
    header("Content-Disposition: attachment; filename=".$filename.".mp3");
} else if ($format == '0' || $format == '7') {
    header('Content-Type: audio/wav');
    header("Content-Disposition: attachment; filename=".$filename.".wav");
} else if ($format == '4') {
    header('Content-Type: audio/flac');
    header("Content-Disposition: attachment; filename=".$filename.".flac");
} else if ($format == '5') {
    header('Content-Type: audio/ogg');
    header("Content-Disposition: attachment; filename=".$filename.".ogg");
}
