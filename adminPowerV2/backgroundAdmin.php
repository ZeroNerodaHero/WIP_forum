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
    }
    else if($typeCode == 5){
    }
    else if($typeCode == 6){
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
            echo '<a href="javascript:renderDelete(3)"> Go Back</a>';
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
                        "<th>$tags</th><th><a href=\"".$scriptTXT."\">Delete</a></th>
                        <th><a href=\"".$redirect."\"> >> </a></th></tr>";
                }
                echo "</table>";       
            }
        }
        else if($pageType == 2 && $isAnote == false){
            echo "<a href=\"javascript:renderDelete(3,'$board')\"> Go Back</a>";
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

                    echo "<tr><th>$pId</th><th>$time</th><th>$content</th>
                        <th><a href=\"$deleteRef\">Delete</a></th><th>Ban</th></tr>";
                }
                echo "</table>";
            }
        }
        else if($pageType == 2 && $isAnote == true){
            echo "<a href=\"javascript:renderDelete(3,'$board')\"> Go Back</a>";
            $que = "SELECT * FROM ".$board."_".$threadId."_comments";
            $res = $connBoards->query($que);
            if($res->num_rows > 0){
                echo "<table>";       
                echo "<tr><th>PostId</th><th>Time</th><th>Content</th>
                      <th>Positions</th><th>Response Count</th></tr>";
                while($row = $res->fetch_assoc()){
                    $pId = $row["postId"];
                    $time = $row["time"];
                    $content = $row["comment"];
                    $uID = $row["userID"];

                    $sx=$row["sx"]; $sy=$row["sy"]; $ex=$row["ex"]; $ey=$row["ey"];

                    $responseCnt = $row["responseCnt"];
                    $responseStr = $row["responseStr"];

                    $deleteRef = "javascript:deleteStuff('$board',$threadId,1,$pId)";
                    echo "<tr><th>$pId</th><th>$time</th><th>$content</th>
                        <th>($sx,$sy) to ($ex,$ey)</th><th>$responseCnt</th>
                        <th><a href=\"$deleteRef\">Delete</a></th></tr>";
                    if($responseCnt != 0){
                        echo "<tr><th></th><th>PostId</th><th>Time</th>
                            <th>Content</th><tr>";
                        $jsonObj = json_decode($responseStr);
                        $responseAr = $jsonObj->data;
                        foreach($responseAr as $obj){
                            $deleteRef = "javascript:deleteStuff('$board',$threadId,1,$pId,$obj[0])";
                            echo "<tr><th></th><th>$obj[0]</th><th>$obj[1]</th>
                                <th>$obj[2]</th><th>$obj[3]</th><th>
                                <a href=\"$deleteRef\">Delete</a></tr>";
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
                    $responseCnt = $row["responseCnt"];
                }

                $newResponse = "{\"data\":[";
                $isFirst = 1;
                foreach($responseObj as $val){
                    if($val[0] != $rId){
                        if(!$isFirst) $newResponse .= ',';
                        $isFirst = 0;
                        $newResponse .= "[$val[0],$val[1],\"$val[2]\",\"$val[3]\"]";
                    }
                }
                $newResponse .= "]}";

                $newResponse = addSlashes($newResponse);
                $que = "UPDATE ".$board."_".$tId."_comments
                        SET responseStr='$newResponse',
                            responseCnt=".($responseCnt-1)."
                        WHERE postId=$pId";
                myQuery($connBoards,$que);
            }
        }
    }
?>
