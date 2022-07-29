<?php
    include_once("../../adminPower/login.php");
    include_once("integration.php");
    include_once("postFunc.php");
    $posterId = getUsrID();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../../css/postSuccess.css">
        <link rel="icon" href="../../../res/icon/icon_0.png">
	<script type="text/javascript" src="../../jscode/jscrap.js"></script>
        <title>FUNCEL.XYZ</title>
    </head>

    <body class=postSuccessBody>

    <?php
	$hasCaptcha = isset( $_POST["g-recaptcha-response"]);
        $responseKeys;
	if($hasCaptcha){
            $captcha = $_POST["g-recaptcha-response"];
	    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' 
	            . urlencode($captchaAPIkey) .  '&response=' . urlencode($captcha);
            $response = file_get_contents($url);
            $responseKeys = json_decode($response,true);
	}
        $isThread = empty($_GET["TID"]);
        $board = $_GET["page"];
        $TID = NULL;
        $redirect = "php/?page=".$board;

        if(!($isThread)){
            $TID = $_GET["TID"];
            $redirect .= "&TID=".$TID;
        }
        $time = 5000;
        $resetPostTime = usrCanPost($posterId);
        //echo "is ".($_POST["anote"]=="yes")."<br>";

        if(checkBan($posterId)){
            $time = 5000;
            printPage("You're banned",true);
        }
        else if($resetPostTime != NULL){
            printPage("You're posting too fast. Wait: ".$resetPostTime,true);
        }
	else if(!$devPuter && (!$hasCaptcha || !$responseKeys["success"])){
	    printPage("Bad Captcha ",true);
	}
        //new thread
        else if($isThread && testString($_POST["title"],301) && 
          testString($_POST["content"],7500)){
            $opt = 0; 
            if(!empty($_POST["anote"]) && ($_POST["anote"]=="yes"))
                $opt = 1;
            manageNewThread($_POST["title"],$_POST["content"],$board,$opt);
        } 
        //new post
        else if(!($isThread) && testString($_POST["content"],7500)){
            manageNewPost($_POST["content"],$board,$TID);
        } else{
            $ppstr = "uh oh u might have typed or did something wrong";
            printPage($ppstr,true);
            $time = 5000;
        }

        //wtf?
        function manageNewPost($postContent,$board,$TID){
            postComment($board,$TID,$postContent);
        }

        //type signifies 
        //0 - normal text
        //1 - anote thread
        function manageNewThread($postTitle,$postContent,$board,$type=0){
            global $connBoards;

            $postTitle = addslashes($postTitle); 
            if(!textVerify($postTitle)){
                printPage("YOU SAID BAD WORD!!!",true);
                return;
            }
            $postTitle = parseText($postTitle);
            $postContent = addslashes($postContent); 

            $que = "INSERT INTO ". $board. "Threads (title".
                ($type != 0 ? ",newTag":""). 
                ") VALUES('$postTitle'".
                ($type != 0 ? ",$type":"").")";
            myQuery($connBoards,$que);
 
            $threadId = $connBoards->insert_id;
            $redirect = "/php/?page=".$board;
            $redirect .= "&TID=".$threadId;

            if($type==0) manageNewTextThread($board,$threadId,$postContent);
            else if($type==1) manageNewAnoteThread($board,$threadId,$postContent);
        }
            
        function manageNewTextThread($board,$threadId,$postContent){
            global $connBoards,$maxThreads;
            //create post table and post
            $newTable = $board . "_" . $threadId;
            $que = "CREATE TABLE " . $newTable . "(
                        postId int NOT NULL AUTO_INCREMENT,
                        time TIMESTAMP,
                        content varchar(7500),
                        ip bigint,
                        PRIMARY KEY(postId)
                    )";
            myQuery($connBoards,$que);
            postComment($board,$threadId,$postContent);

            $que = "SELECT * FROM ". $board."Threads ORDER BY time ASC";
            $res = $connBoards->query($que);

            //deletes tables when > maxThreads
            if(!empty($res) && $res->num_rows > $maxThreads){
                $diff = max($res->num_rows-$maxThreads-1,0);

                while($row = $res->fetch_assoc()){
		    if($diff <= 0) continue;
                    if($row['tags'] == 'pin') continue;
		    //echo "current row ".$row["threadId"];

                    $que = "DROP TABLE ".$board."_".$row["threadId"];
                    myQuery($connBoards,$que);

                    //literal fucking dogshit code
                    $que = "DELETE FROM ".$board."Threads 
                            WHERE threadId=".$row["threadId"];
                    myQuery($connBoards,$que);
		    $diff--;
                }
            }
        }

        //make error thing
        function postComment($page,$TID,$content){
            global $connBoards,$posterId;

            $threadTable = $page . "_".$TID; 
            $redirect = "/php/?page=".$page."&TID=".$TID;
            if(!textVerify($content)){
                printPage("YOU SAID BAD WORD!!!",true,$redirect);
                return;
            }
            $content = str_replace("<","&lt;",$content);
            $content = str_replace("\t","&nbsp&nbsp&nbsp&nbsp;",$content);
            $content = str_replace(">","&gt;",$content);

            //original had a <br> here. Im not too sure why. 
            //when u have a <br> as a last child of a div or p
            //nothing happens.
            //$content = postParser($content) . "<br>";
            $content = postParser($content); 
            $content = nl2br($content);
            $content = addslashes($content); 
            $que = "INSERT INTO ". $threadTable . "(time,content,ip) 
                    VALUES( CURRENT_TIMESTAMP,'$content','$posterId')";

            myQuery($connBoards,$que);
            printPage("POST SUCCESSFUL!",false,$redirect);

            //update mysql variables
            updateUsrScore($posterId,10);
            updateUsrTime($posterId);
            updatePostCnt($page,$TID);
        }
        function bumpThread($board,$tid){
            global $connBoards;
            $que = "UPDATE ".$board."Threads SET time=CURRENT_TIMESTAMP
                    WHERE threadId='$tid'";
            myQuery($connBoards,$que);
        }

        //essentially posts 
        //this uses a terrible way to parse the text
        //first i got and find cases of "##"
        //then i use it to find "[?]()
        //it is slow of course
        //optimize
        function postParser($content){
            $ws = 0; 
            $clen = strlen($content);
            $retStr = $content;

            //counts new lines
            $cntNL = 0;

            while($ws < $clen){

                //this is used for pink text
                $we = 0;
                if($content[$ws] == "~"){
                    while($we < $clen && $content[$we] != "\n" &&
                      $content[$we] != "\r" && $content[$we] != "\r\n" &&
                      $content[$we] != "\n\r"){
                        $we++;      
                    }
                    //>hello -> <div style="color:pink">hello</div>
                    //-1 because this includes "/n"
                    $tobeReplace = substr($content,$ws,$we-$ws);
                    $retStr = str_replace($tobeReplace,
                        //"<div style='color:pink'>".$tobeReplace."</div>",$retStr);
                        "<span style='color:pink'>".$tobeReplace."</span>",$retStr);
                        //problem here is that there is an extra return. 
                        //hopefully the users are not retarded
                    $ws = ++$we;
                    continue;
                }
                //dont forget escape character after anything wtf
                if($ws+2 < $clen && $content[$ws] == "#" && $content[$ws+1] == "#"){
                    //plus 2 to skip over "##" -> we can assume does not hit max size
                    $we = $ws+2;
                    while($we < $clen && $content[$we] != "\n" &&
                      $content[$we] != "\r" && $content[$we] != "\r\n" &&
                      $content[$we] != "\n\r" && $content[$we] != " "){
                        $we++;
                    }
                    //
                    //the 2 here is the offset from ## and \n
                    $goTo= substr($content,$ws+2,($we-$ws-2));
                    $oldTXT = substr($content,$ws,($we-$ws));
                    $replace = "<a href='javascript:jumpPost(\"pd$goTo\")'>" .
                        "##".$goTo. "</a>";

                    $retStr = str_replace($oldTXT,$replace,$retStr);
                    
                    $ws = ++$we;
                    continue;
                }
                //i canot understadn what character html uses for newlines wtf
                //i do have a problem where the skips for characters are wrong
                $isComplete=0; $lastSet = -1;
                $pos = array(-1,-1,-1,-1,-1,-1);
                $sepType = array( "[", "]", "(", ")", "{", "}");

                $we = $ws;
                while($we < $clen && $content[$we] != "\n" &&
                  $content[$we] != "\r" && $content[$we] != "\r\n" &&
                  $content[$we] != "\n\r"){
                    $doubleBreak=0;
                    $isInArray = 0;

                    for($sepIt = 0; $sepIt < 6; $sepIt++){
                        //essentailly if char == one of the [](){} -> check if set
                        //if not set then set and break
                        if($content[$we] == $sepType[$sepIt] && $pos[$sepIt] == -1){
                            $isInArray = 1;
                            $pos[$sepIt]=$we;
                            $lastSet++;

                            //complete break bc meets }
                            if($sepId == 5){
                                $doubleBreak = 1;
                            }
                            break;
                        }
                    }
                    $we++;
                    if($doubleBreak) break;
                    if($isInArray) continue;
                    else if($lastSet & 1){
                        //essentailly if it is anything else(ie character or space)
                        //-> we need to make sure that it is between a [,(,{
                        //if it is even -> we can place a character of any type
                        break;
                    }
                }
                //[tag](URI){option}
                //lastSet max is 5 bc starts from -1
                //or if lastSet is odd
                $isComplete = ($lastSet == 5) || ($lastSet == 3);

                if($isComplete){
                    $tmpTYPE = substr($content,$pos[0]+1, $pos[1]-$pos[0]-1);
                    $tmpLNK = substr($content,$pos[2]+1, $pos[3]-$pos[2]-1);

                    $tmpOPT = ""; 
                    if($pos[5] != -1){
                        $tmpOPT= substr($content,$pos[4]+1,$pos[5]-$pos[4]-1);
                    } else{
                        //have to set pos[5] as pos[3] bc the str replace
                        //changes
                        //this is just optimzation so no extra if is needed
                        $pos[5] = $pos[3];
                    }
                    //echo "TYPE " .$tmpTYPE. "<br>LNK " .$tmpLNK. "<br>OPT " .$tmpOPT."<br>";

                    $newStr = substr($content,$ws,$we-$ws);

                    if($tmpTYPE == "IMG"){
                        $newStr = integrate_IMG($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "LNK"){
                        $newStr = integrate_LNK($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "YTB"){
                        $newStr = integrate_YTB($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "TXT"){
                        $newStr = integrate_TXT($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "VIDEO"){
                        $newStr = integrate_VIDEO($tmpLNK,$tmpOPT);
                    }

                    //this is used to detect if the next thing is a new line.
                    $newLineOffset = 1;
                    while($pos[5]+$newLineOffset < strlen($content) && 
                        ($content[$pos[5]+$newLineOffset] == "\r" || 
                        $content[$pos[5]+$newLineOffset] == "\n" ||
                        $content[$pos[5]+$newLineOffset] == "\n\r" || 
                        $content[$pos[5]+$newLineOffset] == "\r\n")){
                            $newLineOffset++;
                    }
                    $newLineOffset--;
                    //so what happens here is bc the way 
                    //i made this is that the endpoints are not
                    //counted...have to add 2
                    //old ver that replaces the whole word
                    $findStr = substr($content,$pos[0],$pos[5]-$pos[0]+1+$newLineOffset); 
                    $retStr = str_replace($findStr,$newStr,$retStr);
                }
                //what is wrong with this. i dont rememberdoing it like this
                $ws = ++$we;
            }
            return $retStr;
        }

        function printPage($msg,$error=false,$redirect=""){
            global $totalBury;
            if($redirect == ""){
                $redirect = "/php/?page=".$_GET['page'].
                    (empty($_GET['TID']) ? "":"&TID=".$_GET['TID']);
            }
            $buryPic = rand()%$totalBury;
	    $messageError = (!$error) ? "postFine" : "postBad";

	    echo "<div id=psEncap onclick='threadRedirect(\"$redirect\")'>";
            echo "<div class=postSuccessHeader><mark id=".$messageError."> "
	        . $msg ."</mark></div>";
            
            echo '<p class=buryQuote> 
                    You have been blessed with Bury#'.$buryPic.'.<br><br>
                    <a href="'.$redirect.'">
                        <img src = "../../res/buries/bury_'.$buryPic.'.png" 
                        id=postSuccessIMG>
                    </a><br></p>';

            echo '
                <p id="postSuccessRedirect">
                    REDIRECTING BACK IN 5 sec . . . 
                    <a href="'.$redirect.'" class=redLink>GO BACK</a>
                </p> ';
            echo "</div>";

	    if(!empty($_POST["content"])){
	        echo "<div id=conEncap>
	            Ur post if something goes wrong:<div id=conData>".
                    (!empty($_POST["title"]) ? $_POST["title"]."<br>---<br>" : "").
		    $_POST["content"] . "</div></div>";
            }
            
            echo '
                <script>
                    setTimeout(function(){
                    location="'.$redirect.'";
                    }, 500000);

                </script> ';

        }

        function manageNewAnoteThread($board,$TID,$commentStr){
            global $connBoards;

            $imgFormat = array(".apng",".gif",".jpg", ".jpeg",".png",".svg");

            $imgAr = array();
            $CLEN = strlen($commentStr); 
            for($i = 0; $i < $CLEN;$i++){
                $j = $i;
                $periodStart=0;
                while($j < $CLEN && $commentStr[$j] != ','){
                    $j++;
                    if($commentStr[$j] == '.'){
                        //you might wonder why that it starts at .
                        //instead of +1, bc i corner case where the 
                        //user doens't finish the link. not going to occur
                        $periodStart = $j;
                    }
                }
                //format to be working on later
                $cFormat = substr($commentStr,$periodStart,($j-$periodStart));
                array_push($imgAr,substr($commentStr,$i,($j-$i)));
                $i = $j;
            }

            //create new table of img link, page no
            $que = "CREATE TABLE ".$board."_".$TID."_imgs(
                        pgNo int auto_increment,
                        imgLnk varchar(400), 
                        primary key(pgNo))";
            myQuery($connBoards,$que);

            foreach($imgAr as $imgLnk){
                //echo $imgLnk . "<br>";
                $que = "INSERT INTO ".$board."_".$TID."_imgs(imgLnk)
                        VALUES('$imgLnk')";
                myQuery($connBoards,$que);
            }
            
            $que = "CREATE TABLE ".$board."_".$TID."_comments( 
                        postId int NOT NULL AUTO_INCREMENT, 
                        userID long, 
                        sx float, 
                        sy float, 
                        ex float, 
                        ey float, 
                        comment varchar(5000), 
                        responseStr MEDIUMTEXT NULL,
                        responseCnt int NOT NULL DEFAULT 0,
                        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                        primary key(postId))";
            myQuery($connBoards,$que);
            printPage("NEW ANOTE THREAD");
        }
    ?>
    </body>
</html>
