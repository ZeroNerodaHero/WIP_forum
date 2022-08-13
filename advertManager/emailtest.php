<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';


accountVerification("billyz404meter@gmail.com","123joafojoo3ioifro3o23ofo");
accountVerification("funceldomain@gmail.com","123joafojoo3ioifro3o23ofo");
function accountVerification($email,$usrId){
    $mail = new PHPMailer(true);
    $verifyLnk = "https://funcel.xyz/advertManager/verify.php?vId=".$usrId;
    echo $verifyLnk;

    try {
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'funceldomain@gmail.com';             
        $mail->Password   = 'faqaxtrgzjebcrta';                  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('funceldomain@gmail.com', 'Me');
        $mail->addAddress($email, 'U');     
        $mail->addReplyTo('funceldomain@gmail.com', 'Reply Here: Human Response Incoming');

        //Content
        $mail->isHTML(true);                                 
        $mail->Subject = 'WELCOME PATRON OF FUNCELS';
        $mail->Body    = '
            <head>
                <style>
                    #allCont{
                        background-color:#ffe5fc;
                        text-align: center;
                    }
                    #foot{
                        font-size: 10px;
                    }
                    #topCont{
                        text-align: center;
                        background-color:#ea82df;
                        padding: 8px; margin: -8px; margin-bottom: 9px;
                        font-size: 43px;
                    }
                    #mainContent{ 
                        padding-left: 21px; 
                        padding-right: 21px; 
                        margin-bottom: 7px;
                    }
                </style>
            </head>
            <div id=allCont>
                <div id=topCont>
                    <div id=headerTxt><b>Please Verify Your Email</b></div>
                </div>
                <div id=mainContent>
                    <div>
                        Welcome anon to funcel.xyz!
                        Please Verify your email by clicking the link below. 
                        <div id=linkCont>
                            <div>
                                <a href="'.$verifyLnk.'" id=verifyLnk>Verify</a>
                            </div>
                            <div id=fullLinkCont>
                                Full Link: <a href="'.$verifyLnk.'" id=fullLnk>'.
                                    $verifyLnk.
                                '</a>
                            </div>
                            <div>Google thinks this is spam so links might not show</div>
                        </div>
                    </div>
                </div>
                <div><i>Welcome to the smallest internet <b>PSYOP</b></i></div>
                <img src="https://pbs.twimg.com/media/FX2Sh6LUIAE4gkt?format=jpg&name=large"
                    style="width:100%">
                <div id=foot>
                    Please do not reply back. But if you do have any questions, please 
                    reply to funceldomain@gmail.com or dm on discord: funcel.xyz_v0.69#7797
                </div>
            </div>';
        $mail->AltBody = "Why are you using outdated shit? 
            Verify with the link:<a href='$verifyLnk'>$verifyLnk</a>";

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
