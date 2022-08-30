<?php
    include_once("backgroundReuse.php");
    if(!empty($_POST["typeCode"])){
        $typeCode = $_POST["typeCode"];
        if($typeCode == 1){
            $username = $_POST["username"];
            $password = $_POST["password"];
            $usrAccount = accountExists($username,$password);
            if($usrAccount != NULL){
                setcookie("usrId", $usrAccount['userId'], time() + (86400 * 30));

                $newSessionId = rand();
                $que = "UPDATE advertManager
                        SET lastSessionId=$newSessionId
                        WHERE userId=".$usrAccount['userId'];
                myQuery($conn,$que);
                setcookie("newSessionId", $newSessionId, time() + (60*60));
                genMainPage($usrAccount);
            } else{
                genLoginPage("No User Found Please <a href='signUp.html'>Sign Up</a>");
            }
        } else {
            $usrId = $_COOKIE["usrId"];
            $usrSession = $_COOKIE["newSessionId"];
            $usrAccount=accountExistsSession($usrId,$usrSession);
            
            if($usrAccount == NULL){
                genLoginPage("Error: You have been logged out");
            } else if($typeCode== 2){
                echo genMyAdverts($usrId);
            } else if($typeCode== 3){
                echo genTransaction($usrAccount);
            } else if($typeCode== 4){
                echo genAddCredits($usrAccount);
            } else if($typeCode== 5){
                echo genAccountInfo($usrAccount);
            } else if($typeCode== 6){
                echo genMessages($usrAccount);
            } else if($typeCode== 7){
                genMainPage($usrAccount);
            } else if($typeCode== 10){
                $lnkToImg = $_POST["lnkToImg"];
                $lnkToSite= $_POST["lnkToSite"];
                $credits = $_POST["credits"];    
                if($usrAccount["credits"] < $credits){
                    echo '{"code":1}';
                } else if($usrAccount["verified"] != true){
                    echo '{"code":2}';
                } else{
                    addAdvert($usrAccount,$lnkToImg,$lnkToSite,$credits);
                    echo '{"code":0}';
                }
            } else if($typeCode== 11){
                $adId= $_POST["adId"];
                deleteAdvert($usrAccount,$adId);
            } else if($typeCode== 20){
                $oldPassword = $_POST["pword"];
                $newPassword = $_POST["newPWord"];
                if($usrAccount["password"]==$oldPassword){
                    updatePassword($usrAccount,$newPassword);
                    echo genAccountInfo($usrAccount,
                        "<div class=success>Password Has Been Updated</div><br>");
                } else{
                    echo genAccountInfo($usrAccount,
                        "<div class=failed>You entered the wrong password</div><br>");
                }
            } else if($typeCode== 24){
                $promoCode = $_POST["promoCode"];
                checkPromo($usrAccount,$promoCode);
            }
        }
    }
    function accountExistsSession($usrId,$usrSession){
        $que = "SELECT * FROM advertManager WHERE userId=$usrId &&
                lastSessionId=$usrSession";
        return returnAccount($que);
    }

    function accountExists($username,$password){
        $que = "SELECT * FROM advertManager WHERE username='$username'&&
                password='$password'";
        return returnAccount($que);
    }
    function returnAccount($que){
        global $conn;
        $res = $conn->query($que);
        $account = NULL;
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $account=$row;
            }
        }
        return $account;
    }
    function genMainPage($usrAccount,$opt=0,$etc=""){
        echo "
            <div id=advertMainBody>
                <div id=mainContent>
                    <div id=advertTopLeft>
                        <div id=advertHeader><u>
                            WELCOME TO ADVERT CONTROL PANEL
                        </u></div>
                        <div id=accountHeaderInfo>
                            Last Action: ".timeRegFormat($usrAccount["lastTime"])."
                            <br>Remaining Credits: ".$usrAccount["credits"]."
                        </div>
                    </div>
                    <div id=advertMainBody>
                    <div id=advertGridView>
                        <div id=addAdvertButton>
                            <div class=myAdvertTab onclick='updateBody(2)'>
                                My adverts
                            </div>
                            <div class=myAdvertTab onclick='updateBody(3)'>
                                Transactions
                            </div>
                            <div class=myAdvertTab onclick='updateBody(4)'>
                                Add Credits
                            </div>
                            <div class=myAdvertTab onclick='updateBody(5)'>
                                Account Info 
                            </div>
                        </div>
                        <div id=advertStuff>";
        if($opt==0) echo genMyAdverts($usrAccount["userId"]);
        else if($opt==4) echo genAddCredits($usrAccount,$etc);
                        echo "</div>
                    </div>
                    </div>
                </div>
            </div>";
    }
    function genPageHeader($txt){
        return "<div id=advertPageHeader><b>$txt</b></div><hr id=advertPageReturn>";
    }
    function genLoginPage($errorMsg=""){
        echo "<div id=advertLoginFlexCont><div id=advertLoginCont>
            <h1 id=loginHeader>Advert Manager</h1><div>Please Login or Sign Up".
            "<div class=errorMsg>".$errorMsg."</div>".
            "</div><hr><div><div>Username: <input id=username name=username><br><br>
            Password:<input type='password' id=passwd name=passwd><br><br>
            <button type='button' onclick='updateBody(0)'>Sign In</button>
            </div><br></div></div></div><div id=externalLink>
            <a href='signUp.html'>Sign Up</a> | 
            <a href=''>Info</a> | 
            <a href=''>Terms Of Service</a> | 
            <a href=''>Back to Funcel</a></div>";
    }
    function genMyAdverts($usrId){
        return genPageHeader("Your Adverts").genAddAdvert().getUsrAds($usrId);
    }
    function getUsrAds($usrId){
        global $conn;
        $que = "SELECT * FROM peepoAds WHERE uploaderId=$usrId ";
        $res = $conn->query($que);
        $ret = "";
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $ret .= genAdvertInfo($row["id"],$row["uploaderId"],
                    $row["linkToImg"],$row["linkToSite"],
                    $row["totalLoads"],$row["maxPoints"],
                    $row["uploadTime"],$row["lastTime"]);
            }
        }
        if($ret == "") $ret = "<div id=noAds>You have no Ads</div>";
        return "<div id=yourAdHeader>Your Ads</div>".$ret;
    }
    function genAdvertInfo($id,$uploaderId,$imgLnk,$siteLnk,$totalLoads,
                            $maxCredits,$uploadTime,$lastUpdate){
        return "<div class=displayAdvertCont>
            <div class=displayAdvertPreview>
                <img src='$imgLnk' class=displayAdvertImgPreview>
                <a href='$siteLnk' class=displayAdvertLinkPreview>LINK</a>
            </div>
            <div class=displayAdvertInfoCont>
                <div class=displayAdId><b>Id: </b>$id</div>
                <div class=displayTextRight><b>Upload Time:</b> $uploadTime</div>
                <div class=displayLastLoadTime><b>Last Load Time:</b> $lastUpdate</div>
                <div>
                    <div><b>Image Link:</b></div>
                    <div class=displayTextRight>$imgLnk</div>
                </div>
                <div>
                    <div><b>Site Link:</b></div>
                    <div class=displayTextRight>$siteLnk</div>
                </div>
                <div>
                    <div class=displayAdCredits>
                        <b>Credits:</b> $totalLoads / $maxCredits
                    </div>
                </div>
                <details class=displayLoadHistoryCont>
                    <summary class=loadHistoryHeaderCont>
                        <span class=loadHistoryHeader>&nbsp;Load History</span>
                    </summary>    
                    <div class=loadHistoryGraph>Place holder</div>
                </details>
                <br>
                <div class=deleteAdCont>
                    <div class=deleteAdButton onclick='deleteAd($id)'>
                        Delete
                    </div>
                </div>
            </div>
        </div>-";   
    }
    function genAddAdvert(){
        return "
            <details id=addAdvertCont open>
                <summary id=addAdHeader>
                    <span id=addAdHeaderText>Add Advertisement</span>
                </summary>
                <div id=addAdvertGrid>
                    <div id=addAdvertPreview>
                        <img src='../res/emotes/emote_1.png' id=addAdvertImgPreview>
                        <br><a href='' id=addAdvertLnkPreview>LINK</a>
                    </div> 
                    <div id=addAdvertInfo>
                        <div id=addAdvertInfoNotice>
                            *The preview will update with your input*
                        </div>
                        <div id=addAdvertInputCont>
                            <div><b>Link To Img:</b> </div>
                            <div class=inputRightEncap>
                                <input id=lnkToImg onfocusout='updatePreviewImg()'>
                            </div>
                            <div><b>Link To Site:</b> </div>
                            <div class=inputRightEncap>
                                <input id=lnkToSite onfocusout='updatePreviewLnk()'>
                            </div>
                            <div><b>Credits:</b> </div>
                            <div class=inputRightEncap>
                                0 / <input id=addAdCredits>
                            </div>
                        </div>
    
                        <div id=addDisclaimer><br>
                            By Submitting, the user has agreed to the premise that the 
                            advertisement will contain no NSFW content and general civility(no
                            explicit slurs like the gamer word and that is about it).
                            That is not to say the site has to be SFW, but that the images
                            may depict no NSFW content.<br>
                            Deleting ads also have a 10% penalty to prevent spammers.
                        </div><br>
                        <div id=submitNewAdCont>
                            <span id=submitButton onclick=newAd()>Post</span>
                        </div>
                    </div>
                </div>
            </details><hr>";
    }
    function addAdvert($usrAccount,$lnkToImg,$lnkToSite,$credits,
                        $lowerText="",$etcInfo=""){
        global $conn;
        $usrId=$usrAccount["userId"];
        if(empty($usrId)&& empty($lnkToImg)&& empty($lnkToSite)&& empty($credits)) return;
        
        $que = "INSERT INTO peepoAds(uploaderId,linkToImg,linkToSite,maxPoints,lowerText,etcInfo)
                VALUES($usrId,'$lnkToImg','$lnkToSite',$credits,'$lowerText','$etcInfo')";
        myQuery($conn,$que);
        $adId = $conn->insert_id;
        $usrAccount["credits"] -= $credits;

        updateUserCredits($usrAccount,$credits);
        updateTransactionHistory($usrAccount,$adId,1,$credits);
    }
    function deleteAdvert($usrAccount,$adId){
        global $conn;
        $usrId = $usrAccount["userId"];
        $nuCredit= $usrAccount["credits"];
        $refund=0;$original=0;
        $que= "SELECT * FROM peepoAds WHERE id=$adId && uploaderId=$usrId";
        $res = $conn->query($que);
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $original= $row["maxPoints"]-$row["totalLoads"];
                $refund= ceil($original*0.9);
            }
        }
        $que = "DELETE FROM peepoAds WHERE id=$adId";
        myQuery($conn,$que);

        $nuCredit+=$refund;

        $que = "UPDATE advertManager 
                SET credits=$nuCredit
                WHERE userId=$usrId";
        myQuery($conn,$que);
        updateTransactionHistory($usrAccount,$adId,0,$original);
    }
    //type tells us if 0-delete,1-add,3 change?
    function updateTransactionHistory($usrAccount,$adId,$type,$credits){
        global $conn;
        $newLog = array("time"=>timeRegFormat(time()),"adId"=>$adId,
                    "type"=>$type,"credits"=>$credits,
                    "accountCredits"=>$usrAccount["credits"]);

        $transactionHistory=$usrAccount["transactionHistory"];
        $historyObj = json_decode($transactionHistory,true);
        $historyObj["log"][] = $newLog;

        $que = "UPDATE advertManager
                SET transactionHistory='".json_encode($historyObj)."'
                WHERE userId=".$usrAccount['userId'];
        myQuery($conn,$que);
    }
    function updateUserCredits($usrAccount,$credits){
        global $conn;
        $que = "UPDATE advertManager
                SET credits=".$usrAccount["credits"]."
                WHERE userId=".$usrAccount["userId"];
        myQuery($conn,$que);
    }
    function genTransaction($usrAccount){
        $transactionObj = json_decode($usrAccount["transactionHistory"],true); 
        $transactionLog = array_reverse($transactionObj["log"]);
        $ret = "";
        $shade = 0;
        foreach($transactionLog as $logRow){
            $ret .="<div class='displayLogLine".($shade?" shadeLogLine":"")."'>";
            $ret .="<div class=displayLogTime>".$logRow["time"]."</div> ";
            $ret .= "<div class=displayLogHeader><b>Log:</b></div>";
            $ret .= "<div class=displayLogInfo>";
            if($logRow["type"] == 0){
                $ret .= "Deleted ad#".$logRow["adId"]." with ".$logRow["credits"].
                    " credits<br>Refund is ".ceil($logRow["credits"]*0.9)."<br>";
            } else if($logRow["type"] == 1){
                $ret .= "Added ad#".$logRow["adId"]." with ".$logRow["credits"].
                    " credits<br>";
            }
            $ret.= "Current Account Credits: ".
                $logRow["accountCredits"]."</div></div>";
            $shade ^= 1;
        }
        if($ret == "") $ret = "<div id=noAds>Empty Transaction History</div>";
        else{
            $ret ="<div class=displayLogLine>
                    <div class=displayLogTime><b>Time</b></div>
                    <div class=displayLogHeader><b>Log Message</b></div>
                </div>".$ret;
        }
        return genPageHeader("Transaction History").$ret;
    }
    function genAddCredits($usrAccount,$etc=""){
        return genPageHeader("Add Credits").$etc.
            "<div id=Paypalstuff></div>".
            "<div id=promoInputCont><b>Promo Code:</b><input id=promoCodeInput>
                <input type=submit id=promotInputSubmit onclick=addPromo()>
                <div id=promorInfo>*You can find these codes somewhere*</div>
            </div>";
    }
    function checkPromo($usrAccount,$promoCode){
        global $conn;
        $que = "SELECT * FROM promoCodes WHERE promoCode='$promoCode'";
        $res = $conn->query($que);
        $ret = "<div id=advertErrorMessage>
                <b>OY VEY<b>: CODE USED OR INVALID CODE</div>";
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $jsonObj = json_decode($usrAccount["etcInfo"],1);
                $promos= $jsonObj["promos"];
                $hasPromo = 0;
                if($promos != NULL){
                    foreach($promos as $code){
                        if($promoCode==$code) $hasPromo =1;
                    }
                } else{
                    if($jsonObj==NULL) $jsonObj = array();
                    $tmp = array("promos"=>array("noCode"));
                    $jsonObj = array_merge($jsonObj,array("promos"=>array("noCode")));
                }
                if(!$hasPromo){
                    $newUses = $row["usesLeft"]-1;
                    $que = "UPDATE promoCodes 
                            SET usesLeft=".($newUses)."
                            WHERE promoCode='$promoCode'";
                    myQuery($conn,$que);
                
                    $usrAccount["credits"] += $row["credits"];
                    $jsonObj["promos"][]=$promoCode;
                    $que = "UPDATE advertManager
                            SET credits=".($usrAccount["credits"]).",
                                etcInfo='".json_encode($jsonObj)."'
                            WHERE userId=".$usrAccount["userId"];
                    myQuery($conn,$que);
                    $ret = "<div id=advertSuccessMessage>
                            <b>BURY NICE</b>: YOU HAVE REDEEMED ".$row["credits"]." CREDITS
                            </div>";
                }
            }
        } 
        genMainPage($usrAccount,4,$ret);
    }
    function updatePassword($usrAccount,$newPassword){
        global $conn;
        $que = "UPDATE advertManager
                SET password=$newPassword
                WHERE userId=".$usrAccount["userId"];
        myQuery($conn,$que);
    }
    function genAccountInfo($usrAccount,$miscInfo=""){
        return genPageHeader("Account Info").
            ($miscInfo!="" ? "<div id=accountUpdatInfo>$miscInfo</div>":"").
            "<div id=displayUsrInfo>
                <div class=usrInfoLeft><b>Username:</b>
                    <span class=usrInfoRight>".$usrAccount["username"]."</span></div>".
                "<div class=usrInfoLeft><b>Email:</b>
                    <span class=usrInfoRight>".$usrAccount["email"]."</span></div>".
                "<div class=usrInfoLeft><b>UserId:</b>
                    <span class=usrInfoRight>".$usrAccount["userId"]."</span></div>
                <div id=changePWordCont>
                    <div id=changePWordHeader>
                        <b>Change Password</b>
                    </div>
                    <div id=oldPWordCont class=pwordInputCont>
                        <b>Old Password:</b>
                        <input type=password id=oldPword class=PWordChng
                            oninput=updatePWordCheck()>
                    </div>
                    <div id=newPWordCont class=pwordInputCont>
                        <b>New Password:</b>
                        <input type=password id=newPword class=PWordChng
                            oninput=updatePWordCheck()>
                    </div>
                    <div id=retypePWordCont class=pwordInputCont>
                        <b>Retype Password:</b>
                        <input type=password id=retypePword class=PWordChng
                            oninput=updatePWordCheck()>
                    </div>
                    <div id=changePWordButtonCont>
                        <input type=button id=changePWordButton onclick=changePWord() 
                        value=Change disabled>
                    </div>
                </div>
            </div>";
    }
    function genMessages($usrAccount){
        return genPageHeader("Messages").
            "";
    }
?>
