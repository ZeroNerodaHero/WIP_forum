<?php
    include_once("login.php");

    $typeCode = $_POST["typeCode"];

    if($typeCode == 0){
        $passwd = $_POST["passwd"];

        if($passwd == $admin_ppassword || $passwd == $mod_ppassword){
            echo "Login Success";
            generalSideBar();
        } else{
            echo "Wrong Password";
        }
    } 
    else if($typeCode == 1){
    }
    else if($typeCode == 2){
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
        $reason="WTF";
        $board= $_POST["board"];            
        $tId= $_POST["tId"];            
        $pId=$_POST["pId"];
        
        if(empty($_POST["rpId"])){
            banUsrFromPost($reason,$board,$tId,$pId);
        }else{
            banUsrFromPost($reason,$board,$tId,$pId,$_POST["rpId"]);
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
    }
    function banUsrFromPost($reason,$board,$tId,$pId,$rId=NULL,$time="1 0:0:0"){
        global $connBoards;
        $usrId = NULL;
        if($rId==NULL){
            //normal
            $que = "SELECT ip FROM $board"."_$tId
                    WHERE postId = $pId";
            $res = $connBoards->query($que);

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $usrId=$row["ip"];
                }
            }
        } else{
            //resposne
            $que = "SELECT responseStr FROM $board"."_$tid"."_comments
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
        banUsr($usrId,$reason,$time); 
    }

    function generalSideBar(){
        $optionAr = array("Post News","Create Board",
                          "Delete Stuff","Ban List",
                          "Banned Words","Advert Manager",
                          "Update MySql");
        $count = count($optionAr);

        echo "<div id=adminContent><div id=adminSelector>";
        for($i = 0; $i < $count; $i++){
            echo "<div class=adminOption onclick='showSelected(".($i+1).")'>".
                $optionAr[$i]."</div>";
        }
        echo "</div><div id=selectedBody></div></div>";
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
                echo "<tr><th>Thread Id</th><th>Title</th><th>Time</th>";
                echo "<th>Tags</th></tr>";
                while($row = $res->fetch_assoc()){
                    $tId = $row["threadId"];
                    $title = $row["title"];
                    $time = $row["time"];
                    $tags = $row["newTag"];
                    $scriptTXT = "javascript:deleteStuff('".$board."',".
                                    $tId.",".($tags&1?"1":"0").")";
                    $redirect= "javascript:renderDelete(3,'".$board."',$tId".
                                ($tags&1 ? ",1":"").")";
                    echo "<tr><th>$tId</th><th>$title</th><th>$time</th>".
                        "<th>$tags</th>
                        <th class=noBreak><a href=\"".$scriptTXT."\">Delete</a></th>
                        <th><a href=\"".$redirect."\"> >> </a></th></tr>";
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
                        <th class=noBreak><input type=text id=banReason>
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
                    $banRef = "javascript:uhOhBan('$board',$threadId,$pId)";

                    echo "<tr><th>$pId</th>
                        <th class=noBreak>".timeRegFormat($time)."</th>
                        <th>$content</th>
                        <th class=noBreak>(".number_format($sx,2).",".
                            number_format($sy,2).") to (".
                            number_format($ex,2).",".number_format($ey,2).")</th>
                        <th>$responseCnt</th>
                        <th class=noBreak><a href=\"$deleteRef\">Delete</a></th>
                        <th class=noBreak><input type=text id=banReason>
                            <a href=\"$banRef\">Ban</a></tr>";
                    if($responseCnt != 0){
                        echo "<tr><th class=emptyEntry></th>
                            <th>PostId</th><th>Time</th><th>Content</th></tr><tr></tr>";
                        $jsonObj = json_decode($responseStr);
                        $responseAr = $jsonObj->data;
                        foreach($responseAr as $obj){
                            $deleteRef = "javascript:deleteStuff('$board',$threadId,1,$pId,$obj[0])";
                            $banRef= "javascript:uhOhBan('$board',$threadId,$pId,$obj[0])";
                            echo "<tr><th class=emptyEntry></th>
                                <th>$obj[0]</th>
                                <th>$obj[2]</th><th>$obj[3]</th>
                                <th class=noBreak><a href=\"$deleteRef\">Delete</a></th>
                                <th class=noBreak><input type=text id=banReason>
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
                <th>Link to Site</th>
                <th class=noBreak>Point System(loads+click/max)</th>
                <th class=noBreak>Boards Limited</th>
                <th>Date Added</th>
                <tr>";
            while($row = $res->fetch_assoc()) {
                $id = $row["id"];
                $linktoImg = $row["linkToImg"];
                $linktoSite= $row["linkToSite"];
                $totalLoads=$row["totalLoads"];
                $totalClicks=$row["totalClicks"];
                $maxPoints=$row["maxPoints"];
                $boardLimited=$row["boardLimited"];
                $dateAdded=$row["dateAdded"];
                echo "<tr><th>$id</th><th><img src=$linktoImg></th>
                    <th>$linktoSite <a href='$linktoSite'>[Link]</a></th>
                    <th>$totalLoads + $totalCLicks / $maxPoints</th>
                    <th>$boardLimited</th>
                    <th>$dateAdded</th></tr>";
            }
            echo "</table>";
        }

    }

    
?>
