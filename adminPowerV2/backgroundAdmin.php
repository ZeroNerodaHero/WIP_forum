<?php
    include_once("login.php");

    $typeCode = $_POST["typeCode"];

    if($typeCode == 0){
        $passwd = $_POST["passwd"];

        if($passwd == $admin_ppassword || $passwd == $mod_ppassword){
            generalSideBar();
        } else{
            echo "Wrong Password";
        }
    } 
    else if($typeCode == 1){
        $title = $_POST["title"];
        $content= $_POST["content"];
        postNews($title,$content);
    }
    else if($typeCode == 2){
        $board = $_POST["board"];
        $descript= $_POST["descript"];
        $title = $_POST["title"];
        $postContent = $_POST["pinnedContent"];
        createBoard($board,$descript,$title,$postContent);
    }
    else if($typeCode == 3){
        $pageType = $_POST["pageType"];
        displayDeleteStuff($pageType,$_POST["board"],$_POST["threadId"],$_POST["isAnote"]);
    }
    else if($typeCode == 4){
        //ban list
        generateBanList();
    }
    else if($typeCode == 5){
        //banned words
        generateBadWordList();
    }
    else if($typeCode == 6){
        //advert manager
        generateAdvert();
    }
    else if($typeCode == 7){
    }
    // etc ...
    else if($typeCode == 8){
        //delete Stuff
        $opt = $_POST["opt"];            
        $board= $_POST["board"];            
        $tId= $_POST["tId"];            
        $isAnote = $_POST["isAnote"];
        $pId=$_POST["pId"];
        $rId=$_POST["rpId"];
        deleteStuff($opt,$board,$tId,$isAnote,$pId,$rId);
    }
    else if($typeCode == 9){
        //ban user
        $reason=$_POST["reason"];
        $board= $_POST["board"];            
        $tId= $_POST["tId"];            
        $pId=$_POST["pId"];
        $isAnote=$_POST["isAnote"];
//echo "$reason $board $tId $pId $isAnote \n";
        
        if(empty($_POST["rId"])){
            banUsrFromPost($reason,$board,$tId,$pId,$isAnote);
        }else{
            banUsrFromPost($reason,$board,$tId,$pId,$isAnote,$_POST["rId"]);
        }
    }
    else if($typeCode == 10){
        $usrId=$_POST["usrId"];
        unBanUsr($usrId);
    } 
    else if($typeCode == 11){
        $word = $_POST["word"];
        updateBannedWord(1,$word);
    }
    else if($typeCode == 12){
        $word = $_POST["word"];
        updateBannedWord(0,$word);
    } else if($typeCode == 13){
        $id = $_POST["id"];
        $opt = $_POST["opt"];
        $value = $_POST["value"];
        $oldValue = $_POST["oldValue"];
        updateAdvert($id,$opt,$value,$oldValue);
    } else if($typeCode == 14){
        $imgLnk = $_POST["imgLnk"];
        $siteLnk = $_POST["siteLnk"];
        $points = $_POST["points"];
        $boardsLimited = $_POST["boardsLimited"];
        addAdvert($imgLnk,$siteLnk,$points,$boardsLimited);
    } else if($typeCode == 99){
        echo returnLog();
    }
    function postNews($title,$msg){
        global $conn;
        $title = addslashes($title);

        $msg = nl2br($msg);
        $msg = addslashes($msg);

        $que = "INSERT INTO frontNews(news_title,post_time,news_content) 
                VALUES('$title',CURRENT_TIMESTAMP(),'$msg')";
        myQuery($conn,$que);
    }
    function createBoard($boardName,$boardDescript,$pinnedTit,$pinnedPost){
        global $conn,$connBoards;
        /************************************************/
        //create board threads
        $que = "CREATE TABLE " . $boardName . "Threads (
                    title varchar(300),
                    threadId int NOT NULL AUTO_INCREMENT,
                    time TIMESTAMP,
                    tags VARCHAR(50) NULL,
                    postCnt int NOT NULL DEFAULT 0,
                    acclaim VARCHAR(999) NULL,
                    newTag int NOT NULL DEFAULT 0,
                    PRIMARY KEY(threadId)
                )";
myQuery($connBoards,$que);
        
        $que = "INSERT INTO ". $boardName. "Threads (title,tags)
            VALUES('$pinnedTit','pin')";
myQuery($connBoards,$que);

        $newTable = $boardName . "_1";

        /************************************************/

        $que = "INSERT INTO boards(typeOfBoard,boardName,descript)
                VALUES(\"shit\",'$boardName','$boardDescript')";
myQuery($conn,$que);

        /************************************************/

        //create post table and post
        $que = "CREATE TABLE " . $newTable . "(
                    postId int NOT NULL AUTO_INCREMENT,
                    time TIMESTAMP,
                    content varchar(7500),
                    ip bigint,
                    PRIMARY KEY(postId)
                )";
myQuery($connBoards,$que);

        $usrIP = getUsrID(); 
        echo "ip is " .$usrIP . " and " . empty($usrIP) ."<br>";
        $que = "INSERT INTO ". $newTable . "(content,ip) VALUES( '$pinnedPost',";
        if(!empty($usrIP)) $que .= $usrIP.")";
        else $que .= "NULL)";
myQuery($connBoards,$que);
adminLog("Created a Board called $boardName");
    }
    function banUsrFromPost($reason,$board,$tId,$pId,$isAnote,$rId=NULL,$time="1 0:0:0"){
        global $connBoards;
        $usrId = NULL;
        if($rId==NULL){
            $schema = "ip";
            $que = "SELECT ip FROM $board"."_$tId
                    WHERE postId = $pId";
            if($isAnote){
                $schema = "userId";
                $que = "SELECT userId FROM $board"."_$tId"."_comments
                        WHERE postId = $pId";
            }

            $res = $connBoards->query($que);

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $usrId=$row[$schema];
                }
            }
        } else{
            //resposne
            $que = "SELECT responseStr FROM $board"."_$tId"."_comments
                    WHERE postId = $pId";
            $res = $connBoards->query($que);

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $responseObj=json_decode($row["responseStr"])->data;

                    foreach($responseObj as $val){
                        if($val[0] == $rId){
                            $usrId=$val[1];
                        }
                    }
                }
            }
        }
        if($usrId != NULL) banUsr($usrId,$reason,$time); 
    }

    function generalSideBar(){
        $optionAr = array("Post News","Create Board",
                          "Delete Stuff","Ban List",
                          "Banned Words","Advert Manager",
                          "Update MySql");
        $count = count($optionAr);

        echo "<div id=adminContent><div id=adminSelector>
            <div id=adminOptCont>";
        for($i = 0; $i < $count; $i++){
            echo "<div class=adminOption onclick='showSelected(".($i+1).")'>".
                $optionAr[$i]."</div>";
        }
        echo "</div><hr>
            <div id=adminLogCont>
                <div id=adminLogTit><u>LOG</u></div>
                <div id=adminLog><pre>ERROR LOG NOT LOADED</pre></div>
            </div></div>";
        echo "<div id=selectedCont>
                <div id=selectedBody>
                    Login Success...<br>
                    <img src='../res/emotes/emote_2.png' style='width: 100%'><br>
                    Welcum
                </div></div></div>";
    }

    function displayDeleteStuff($pageType,$board = -1,$threadId = -1,$isAnote=0){
        global $conn,$connBoards;
        //pageType -
        //0 - show boards
        //1 - show board threads
        //2 - show threadComments
        if($pageType == 0){
            echo "<h1>MODERATION</h1>";
            $que = "SELECT * FROM boards";
            $res = $conn->query($que);

            if($res->num_rows > 0){
                echo "<table>";       
                echo "<tr><th>Board</th><th>Type</th><th>Description</th></tr>";
                while($row = $res->fetch_assoc()){
                    $board=$row["boardName"];
                    $type=$row["typeOfBoard"];
                    $descript=$row["descript"];
                    $redirect = "javascript:renderDelete(3,'".$board."')";
                    echo "<tr><th>$board</th>
                          <th>$type</th><th>$descript</th>
                          <th><a href=\"".$redirect."\"> >> </a></tr>";
                }
                echo "</table>";       
            }
        } 
        else if($pageType == 1){
            echo "<h1>MODERATING Board:$board </h1>";
            echo "<div class=backLink>
                <a href=\"javascript:renderDelete(3)\"> Go Back</a></div>";
            $que = "SELECT * FROM ".$board."Threads";
            $res = $connBoards->query($que);

            if($res->num_rows > 0){
                echo "<table>";       
                echo "<tr><th class=noBreak>Thread Id</th><th>Title</th>
                    <th class=noBreak>Time</th><th class=noBreak>Tags</th></tr>";
                while($row = $res->fetch_assoc()){
                    $tId = $row["threadId"];
                    $title = $row["title"];
                    $time = $row["time"];
                    $tags = $row["newTag"];
                    $scriptTXT = "javascript:deleteStuff('".$board."',".
                                    $tId.",".($tags&1?"1":"0").")";
                    $redirect= "javascript:renderDelete(3,'".$board."',$tId".
                                ($tags&1 ? ",1":"").")";
                    echo "<tr><th>$tId</th><th>$title</th>
                        <th class=noBreak>".timeRegFormat($time)."</th>".
                        "<th>$tags</th>
                        <th class=noBreak><a href=\"".$scriptTXT."\">Delete</a></th>
                        <th><a href=\"".$redirect."\" class=noBreak> >> </a></th></tr>";
                }
                echo "</table>";       
            }
        }
        else if($pageType == 2 && $isAnote == false){
            echo "<h1>MODERATING Board:$board Thread:$threadId</h1>";
            echo "<div class=backLink>
                <a href=\"javascript:renderDelete(3,'$board')\"> Go Back</a></div>";
            $que = "SELECT * FROM ".$board."_".$threadId;
            $res = $connBoards->query($que);

            if($res->num_rows > 0){
                echo "<table>";       
                echo "<tr><th>PostId</th><th>Time</th><th>Content</th></tr>";
                
                while($row = $res->fetch_assoc()){
                    $pId = $row["postId"];
                    $time = $row["time"];
                    $content = $row["content"];
                    $uID = $row["ip"];

                    $deleteRef = "javascript:deleteStuff('$board',$threadId,0,$pId)";
                    $banRef= "javascript:uhOhBan('$board',$threadId,$pId)";

                    echo "<tr><th>$pId</th><th>$time</th>
                        <th class=constrainedBox>$content</th>
                        <th class=noBreak><a href=\"$deleteRef\">Delete</a></th>
                        <th class=noBreak><input type=text id=banReason_$pId>
                            <a href=\"$banRef\">Ban</a></th></tr>";
                }
                echo "</table>";
            }
        }
        else if($pageType == 2 && $isAnote == true){
            echo "<h1>MODERATING Board:$board Thread:$threadId</h1>";
            echo "<div class=backLink>
                <a href=\"javascript:renderDelete(3,'$board')\"> Go Back</a></div>";
            $que = "SELECT * FROM ".$board."_".$threadId."_comments";
            $res = $connBoards->query($que);
            if($res->num_rows > 0){
                echo "<table>";       
                echo "<tr><th class=noBreak>PostId</th><th>Time</th>
                    <th class=noBreak>Content</th>
                    <th>Positions</th><th class=noBreak>Response Count</th></tr>";
                while($row = $res->fetch_assoc()){
                    $pId = $row["postId"];
                    $time = $row["time"];
                    $content = $row["comment"];
                    $uID = $row["userID"];

                    $sx=$row["sx"]; $sy=$row["sy"]; $ex=$row["ex"]; $ey=$row["ey"];

                    $responseCnt = $row["responseCnt"];
                    $responseStr = $row["responseStr"];

                    $deleteRef = "javascript:deleteStuff('$board',$threadId,1,$pId)";
                    $banRef = "javascript:uhOhBan('$board',$threadId,$pId,1)";

                    echo "<tr><th>$pId</th>
                        <th class=noBreak>".timeRegFormat($time)."</th>
                        <th>$content</th>
                        <th class=noBreak>(".number_format($sx,2).",".
                            number_format($sy,2).") to (".
                            number_format($ex,2).",".number_format($ey,2).")</th>
                        <th>$responseCnt</th>
                        <th class=noBreak><a href=\"$deleteRef\">Delete</a></th>
                        <th class=noBreak><input type=text id=banReason_$pId>
                            <a href=\"$banRef\">Ban</a></tr>";
                    if($responseCnt != 0){
                        echo "<tr><th class=emptyEntry></th>
                            <th>PostId</th><th>Time</th><th>Content</th></tr><tr></tr>";
                        $jsonObj = json_decode($responseStr);
                        $responseAr = $jsonObj->data;
                        foreach($responseAr as $obj){
                            $rId = $obj[0];
                            $deleteRef = "javascript:deleteStuff('$board',$threadId,1,$pId,$rId)";
                            $banRef= "javascript:uhOhBan('$board',$threadId,$pId,1,$rId)";
                            echo "<tr><th class=emptyEntry></th>
                                <th>$obj[0]</th>
                                <th>$obj[2]</th><th>$obj[3]</th>
                                <th class=noBreak><a href=\"$deleteRef\">Delete</a></th>
                                <th class=noBreak><input type=text id=banReason_$pId"."_$rId>
                                    <a href=\"$banRef\">Ban</a></tr>";
                        }
                    }
                } 
                echo "</table>";
            } else{
                echo "EMPTY POST";
            }
        }
    }
    function deleteStuff($opt,$board,$tId,$isAnote,$pId,$rId){
        global $connBoards;
        if($opt == 0 || $opt == 1){
            $que = "DELETE FROM ".$board."Threads
                    WHERE threadId=".$tId;
            myQuery($connBoards,$que);
            
            if($opt==0){
                $que = "DROP TABLE ".$board."_".$tId;
                myQuery($connBoards,$que);
            } else{
                $que = "DROP TABLE ".$board."_".$tId."_comments";
                myQuery($connBoards,$que);
                $que = "DROP TABLE ".$board."_".$tId."_imgs";
                myQuery($connBoards,$que);
            }
        }
        else if($opt == 2){
            $que = "DELETE FROM ".$board."_".$tId."
                    WHERE postId=".$pId;
            myQuery($connBoards,$que);
        } 
        else if($opt == 3){
            $que = "DELETE FROM ".$board."_".$tId."_comments
                    WHERE postId=".$pId;
            myQuery($connBoards,$que);
        } 
        else if($opt == 4){
            $que = "SELECT responseStr,responseCnt FROM ".$board."_".$tId."_comments
                    WHERE postId=".$pId;
            $res = $connBoards->query($que);
            if($res->num_rows > 0){
                $responseObj = NULL;
                $responseCnt = 0;
                while($row = $res->fetch_assoc()) {
                    $responseObj = json_decode($row["responseStr"])->data;
                    $responseCnt = $row["responseCnt"]-1;
                }

                $newResponse = "{\"data\":[";
                if($responseCnt != 0){
                    $isFirst = 1;
                    foreach($responseObj as $val){
                        if($val[0] != $rId){
                            if(!$isFirst) $newResponse .= ',';
                            $isFirst = 0;
                            $newResponse .= "[$val[0],$val[1],\"$val[2]\",\"$val[3]\"]";
                        }
                    }
                    $newResponse .= "]}";
                } else{
                    $newResponse = "";
                }

                $newResponse = addSlashes($newResponse);
                $que = "UPDATE ".$board."_".$tId."_comments
                        SET responseStr='$newResponse',
                            responseCnt=".$responseCnt."
                        WHERE postId=$pId";
                myQuery($connBoards,$que);
            }
        }
    }
    function generateBanList(){
        global $conn;
        echo "banlist";
        $que = "SELECT * from ipBans";
        $res = $conn->query($que);
        if($res->num_rows > 0){
            echo "<table>";
            echo "<tr><th>usrId</th><th>Reason</th><th>Time</th>
                <th>Expire</th><tr>";
            while($row = $res->fetch_assoc()) {
                $usrId = $row["ip"];
                $reason = $row["reason"];
                $time= $row["time"];
                $expire= $row["expire"];
                $unBanRef = "javascript:unBanUsr('$usrId')";
                echo "<tr><th>$usrId</th><th>$reason</th><th>$time</th>
                    <th>$expire</th>
                    <th><a href=\"$unBanRef\">Unban</a><tr>";
            }
            echo "</table>";
        } else{
            echo "EMPTY";
        }
    }
    function generateBadWordList(){
        global $conn;
        $que = "SELECT * from badWord";
        $res = $conn->query($que);
        if($res->num_rows > 0){
            echo "<table>";
            echo "<tr><th>Word</th><tr>";
            while($row = $res->fetch_assoc()) {
                $word= $row["word"];
                $deleteWord = "javascript:deleteWord('$word')";
                echo "<tr><th>$word</th>
                    <th><a href=\"$deleteWord\">Delete</a></th><tr>";
            }
            echo "</table>";
        } else{
            echo "EMPTY";
        }
        echo "<br>ADD A WORD<br><input type=text id=newBannedWord><br>
            <button type=submit onclick='addWord()'>Add</button>";
    }
    function updateBannedWord($code,$word){
        global $conn;

        $que = NULL;
        if($code == 0){
            $que = "INSERT INTO badWord(word) VALUE('$word')";
        } else{
            $que = "DELETE FROM badWord WHERE word = '$word'";
        }
        if($que != NULL)
            myQuery($conn,$que);
    }
    function generateAdvert(){
        global $conn;
        $que = "SELECT * from peepoAds";
        echo $que;
        $res = $conn->query($que);
        if($res->num_rows > 0){
            echo "<table>";
            echo "<tr><th class=noBreak>id</th>
                <th>Image</th>
                <th>Ad Info</th>
                <th class=noBreak>Point System (loads+click/max)</th>
                <th class=noBreak>Boards Limited</th>
                <th>Date Added</th>
                <tr>";
            while($row = $res->fetch_assoc()) {
                $id = $row["id"];
                $linktoImg = $row["linkToImg"];
                $linktoSite= $row["linkToSite"];
                $totalLoads=$row["totalLoads"];
                $totalClicks=$row["totalClicks"];
                if($totalClicks == NULL) $totalClicks = "ERROR";
                $maxPoints=$row["maxPoints"];
                $boardLimited=$row["boardLimited"];
                $dateAdded=timeRegFormat($row["lastTime"]);
                echo "<tr><th>$id</th><th><img src=$linktoImg></th>
                    <th>
                        <div class=advertHeader><u>LINK TO IMAGE</u></div>
                        <div class=adlinkImg id=adlinkImg_$id>$linktoImg</div>
                        <input type=text id=chngImgLnk_$id value=$linktoImg>
                        <button onclick=updateAdvert($id,0)>Update Img</button>
                        <hr>
                        <div class=advertHeader><u>LINK TO SITE</u></div>
                        <span id=adlinkSite_$id>$linktoSite</span>
                        <a href='$linktoSite'>[Link]</a><br>
                        <input type=text id=chngSiteLnk_$id value=$linktoSite>
                        <button onclick=updateAdvert($id,1)>Update Link</button>
                        <hr>
                    </th>
                    <th>
                        <div>$totalLoads + $totalCLicks / 
                            <span id=maxPoint_$id>$maxPoints<span></div>
                        <div>
                            <input size=4 id=chngMaxPoint_$id>
                            <button onclick=updateAdvert($id,2)>Update</button>
                        </div>
                    </th>
                    <th>$boardLimited</th>
                    <th>$dateAdded</th></tr>";
            }
            echo "</table>";
        }
        echo "<br><div id=addAdvertCenter><div id=addAdvertCont>
            <b>ADD ADVERT</b><hr><br>
            Image Link: <input id=imageLnk><br>
            Link To Site: <input id=siteLnk><br>
            Max Points: <input id=points><br>
            Boards Limited: <input id=boardsLimited><br>
            <button id=submitNewAdvert onclick=addAdvert()>Add Advert
                </button></div></div>";
    }
    function updateAdvert($id,$opt,$value,$oldValue){
        global $conn;
        $que = "UPDATE peepoAds SET ";
        $log = "Changed ad_$id's ";
        if($opt==0){
            $que .= "linkToImg='$value' ";
            $log .= "Image Link ";
        } 
        else if($opt==1){
            $que .= "linkToSite='$value' ";
            $log .= "Site Link ";
        }
        else if($opt==2){
            $value += $oldValue;
            $que .= "maxPoints='$value' ";
            $log .= "Max Points ";
        }
        $que .= "WHERE id=$id";
        $log .= "from ".$oldValue." to ".$value;

        if(myQuery($conn,$que)){
            adminLog($log);
        }
    }
    function addAdvert($imgLnk,$siteLnk,$points){
        global $conn;
        $que = "INSERT INTO peepoAds(linkToImg,linkToSite,maxPoints)
                VALUE('$imgLnk','$siteLnk','$points')";
        myQuery($conn,$que);
    }
?>
