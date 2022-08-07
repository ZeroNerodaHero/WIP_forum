<?php
    include_once("backgroundReuse.php");
    $usrAccount=NULL;
    if(!empty($_POST["typeCode"])){
        $typeCode = $_POST["typeCode"];
        if($typeCode == 1){
            $username = $_POST["username"];
            $password = $_POST["password"];
            $tmpAccount = accountExists($username,$password);
            if($tmpAccount != NULL){
                $usrAccount = $tmpAccount;
                genMainPage();
                setcookie("usrId", $usrAccount['userId'], time() + (86400 * 30));
            } else{
                genLoginPage("No User Found Please <a href='signUp.html'>Sign Up</a>");
            }
        } else if($typeCode== 10){
            $lnkToImg = $_POST["lnkToImg"];
            $lnkToSite= $_POST["lnkToSite"];
            $credits = $_POST["credits"];    
            $usrId = $_COOKIE["usrId"];
            addAdvert($usrId,$lnkToImg,$lnkToSite,$credits);
        }
    }

    function accountExists($username,$password){
        global $conn;
        $que = "SELECT * FROM advertManager WHERE username='$username'&&
                password='$password'";
        $res = $conn->query($que);
        $account = NULL;
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $account=$row;
            }
        }
        return $account;
    }
    function genMainPage(){
        global $usrAccount;
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
                            <div class=myAdvertTab onclick='showMyAdverts()'>
                                My adverts
                            </div>
                            <div class=myAdvertTab onclick='showTransactions()'>
                                Transactions
                            </div>
                            <div class=myAdvertTab onclick='addCredits()'>
                                Add Credits
                            </div>
                            <div class=myAdvertTab onclick='showAccountInfo()'>
                                Account Info 
                            </div>
                            <div class=myAdvertTab onclick='showMyAdverts()'>
                                Messages
                            </div>
                        </div>
                        <div id=advertStuff>".
                        genAddAdvert().
                        "</div>
                    </div>
                    </div>
                </div>
            </div>";
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
    function getUsrAds($usrId){
        $que = "SELECT * FROM peepoAds WHERE uploaderId=$usrId ";
        $res = $conn->query($que);
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
            }
        }
    }
    function genAddAdvert(){
        return "
            <details id=addAdvertCont open>
                <summary id=addAdHeader>
                    <span id=addAdHeaderText>Add Advertisement</span>
                </summary>
                <div id=addAdvertGrid>
                    <div id=addAdvertPreview>
                        <img src='../res/emotes/emote_1.png' id=advertImgPreview>
                        <br><a href='' id=advertLnkPreview>LINK</a>
                    </div> 
                    <div id=addAdvertInfo>
                        <div id=addAdvertInfoNotice>
                            *The preview will update with your input*
                        </div>
                        <div id=addAdvertInputCont>
                            <div>Link To Img: </div>
                            <div class=inputRightEncap>
                                <input id=lnkToImg onfocusout='updatePreviewImg()'>
                            </div>
                            <div>Link To Site: </div>
                            <div class=inputRightEncap>
                                <input id=lnkToSite onfocusout='updatePreviewLnk()'>
                            </div>
                            <div>Credits: </div>
                            <div class=inputRightEncap>
                                0 / <input id=addAdCredits>
                            </div>
                        </div>
    
                        <div id=addDisclaimer><br>
                            By Submitting, the user has agreed to the premise that the 
                            advertisement will contain no NSFW content and general civility(no
                            explicit slurs like the gamer word and that is about it).
                            That is not to say the site has to be SFW, but that the images
                            may depict no NSFW content.
                        </div><br>
                        <div id=submitNewAdCont>
                            <span id=submitButton onclick=newAd()>Post</span>
                        </div>
                    </div>
                </div><hr>
            </details>";
    }
    function addAdvert($usrId,$lnkToImg,$lnkToSite,$credits,$lowerText="",$etcInfo=""){
        global $conn;
        if(empty($usrId)&& empty($lnkToImg)&& empty($lnkToSite)&& empty($credits)) return;
        
        $que = "INSERT INTO peepoAds(uploaderId,linkToImg,maxPoints,lowerText,etcInfo)
                VALUES($usrId,'$lnkToImg','$lnkToSite',$credits,'$lowerText','$etcInfo')";
        $que;
        myQuery($conn,$que);
    }
?>
