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
$username = $_POST['confirm_username'];
$password = $_POST['password'];
$token = $_POST['token'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/includes/mail/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/mail/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/mail/src/SMTP.php';

if (!isset($reset_data[$token]['token'])) {
    $echodata = ['error' => 'true', 'errorcode' => '1'];
    echo json_encode($echodata);
} else {
    $checktime = strtotime($reset_data[$token]['added']);
    if (time() - $checktime > 15 * 60) {
        unset($reset_data[$token]);
        $final_data = json_encode($reset_data, JSON_PRETTY_PRINT);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/reset.json', $final_data);
        $echodata = ['error' => 'true', 'errorcode' => '1'];
        echo json_encode($echodata);
    } else {


        if ($token == $reset_data[$token]['token']) {
            if (base64_encode($username) == $reset_data[$token]['usrn']) {
                if ($user->checkUser($username)) {
                    if ($user->changePass($password, $username)) {
                        unset($reset_data[$token]);
                        $final_data = json_encode($reset_data, JSON_PRETTY_PRINT);
                        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/data/reset.json', $final_data);
                        $email = $user->getUserEmail($username);
                        $fullname = $user->getUserFullName($email);
                        $message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/includes/mailtemp/pass-reset.html');
                        $message = str_replace('%imglogo%', DIR . '/assets/static/images/rivlogo/rdairplay-128x128.png', $message);
                        $message = str_replace('%passreset%', $ml->tr('PASSRESETED'), $message);
                        $message = str_replace('%hello%', $ml->tr('HELLONAME {{' . $fullname . '}}'), $message);
                        $message = str_replace('%someone%', $ml->tr('SOMEONECHANGEDPASS {{' . APPNAME . '}}'), $message);
                        $message = str_replace('%loginurl%', DIR . '/login', $message);
                        $message = str_replace('%loginaccount%', $ml->tr('LOGINTOYOURACCOUNT'), $message);
                        $message = str_replace('%footernote%', $ml->tr('SENTFROM {{' . APPNAME . '}}'), $message);
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = SMTPSERV;
                        $mail->Port = SMTPPORT;
                        if ($json_sett["smtplogin"] == 1) {
                            $mail->SMTPAuth = true;
                            $mail->Username = SMTPUSER;
                            $mail->Password = SMTPPASS;
                            if ($json_sett["smtpenc"] == 1) {
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                            } else {
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            }            
                        }
                        $mail->setFrom(SMTPFROM, APPNAME);
                        $mail->addAddress($email, $fullname);
                        $mail->Subject = $ml->tr('PASSRESETED');
                        $mail->CharSet = 'utf-8';
                        $mail->IsHTML(true);
                        $mail->msgHTML($message);
                        if (!$mail->send()) {
                            $echodata = ['error' => 'true', 'errorcode' => '1'];
                            echo json_encode($echodata);
                        } else {
                            $echodata = ['error' => 'false', 'errorcode' => '0'];
                            echo json_encode($echodata);
                        }
                    } else {
                        $echodata = ['error' => 'false', 'errorcode' => '0'];
                        echo json_encode($echodata);
                    }
                } else {
                    $echodata = ['error' => 'false', 'errorcode' => '0'];
                    echo json_encode($echodata);
                }
            } else {
                $echodata = ['error' => 'false', 'errorcode' => '0'];
                echo json_encode($echodata);
            }

        } else {
            $echodata = ['error' => 'false', 'errorcode' => '0'];
            echo json_encode($echodata);
        }
    }
}