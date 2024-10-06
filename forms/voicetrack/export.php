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
$password = $functions->loadPass($username);
$fullname = $_COOKIE['fullname'];
$_RDWEB_API = $_COOKIE['rdWebAPI'];
$cutname = $_GET['cutname'];
$filepath = '/var/snd/'.$_GET['cutname'].'.wav';
$usemp3 = $_GET['mp3'];
$cartno =  substr($_GET['cutname'], 0, strpos($_GET['cutname'], "_"));
$cutname = substr($cutname, strpos($cutname, "_") + 1);
$startpoint = $_GET['start'];
$endpoint = $_GET['end'];

$ch = curl_init();

if ($usemp3 == 1) {
    $headers = array('Content-Type: audio/mpeg');
    $parameters = array(
        'COMMAND' => '1',
         'LOGIN_NAME' => $username,
         'PASSWORD' => $password,
         'CART_NUMBER' => $cartno,
         'CUT_NUMBER' => $cutname,
         'FORMAT' => '3',
         'CHANNELS' => '2',
         'SAMPLE_RATE' => '48000',
         'BIT_RATE' => '320',
         'QUALITY' => '0',
         'START_POINT' => $startpoint,
         'END_POINT' => $endpoint,
         'NORMALIZATION_LEVEL' => '0',
         'ENABLE_METADATA' => '0',         
     );
} else {
    $headers = array('Content-Type: audio/wav');
    $parameters = array(
         'COMMAND' => '1',
         'LOGIN_NAME' => $username,
         'PASSWORD' => $password,
         'CART_NUMBER' => $cartno,
         'CUT_NUMBER' => $cutname,
         'FORMAT' => '0',
         'CHANNELS' => '2',
         'SAMPLE_RATE' => '48000',
         'BIT_RATE' => '0',
         'QUALITY' => '0',
         'START_POINT' => $startpoint,
         'END_POINT' => $endpoint,
         'NORMALIZATION_LEVEL' => '0',
         'ENABLE_METADATA' => '0',
     ); 
}

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
if ($usemp3 == 1) {
    header('Content-Type: audio/mpeg');
    header("Content-Disposition:inline;filename=".$_GET['cutname'].".mp3");
} else {
    header('Content-Type: audio/wav');
    header("Content-Disposition:inline;filename=".$_GET['cutname'].".wav"); 
}