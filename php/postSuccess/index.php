<?php
    include_once("../../adminPower/login.php");
    include_once("integration.php");
    $posterId = getUsrID();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../../css/postSuccess.css">
        <link rel="icon" href="../../../res/icon/icon_0.png">
		<script type="text/javascript" src="../../css/jscrap.js"></script>
        <title>FUNCEL.XYZ</title>
    </head>

    <body class=postSuccessBody>

    <?php
        $redirect = "php/?page=".$_GET['page'];

        $isThread = empty($_GET["TID"]);

        $board = $_GET["page"];
        $time = 5000;

        if(checkBan($posterId)){
            $time = 5000;
            printPage("You're banned",true);
        }
        //new thread
        else if($isThread && testString($_POST["title"],301) && 
                testString($_POST["content"],7500)){
            manageNewThread($_POST["title"],$_POST["content"],$board);
        } 
        //new post
        else if(!($isThread) && testString($_POST["content"],7500)){
            manageNewPost($_POST["content"],$board,$_GET["TID"]);
        } else{
            $ppstr = "uh oh u might have typed or did something wrong";
            printPage($ppstr,true);
            $time = 5000;
        }

        function testString($strr,$maxLen){
	    if(empty($strr) || strlen($strr) > $maxLen)
	        return false;

            $lenTit = strlen($strr);
	    $i = 0;
	    for(; $i < $lenTit; $i++){
	        if($strr[$i] != ' ') break;
	    }
	    return $lenTit !=  $i;
	}

        function manageNewPost($postContent,$board,$TID){
            $newTable = $board."_".$TID;
            $redirect = "/php/?page=".$_GET['page'];
            $redirect = "&TID=".$TID;

            postComment($board,$TID,$postContent);
            //bumpThread($board,$TID);
            //swaped for update
        }

        function manageNewThread($postTitle,$postContent,$board){
            global $connBoards,$maxThreads;

            $postTitle = addslashes($postTitle); 
            if(!textVerify($postTitle)){
                printPage("YOU SAID BAD WORD!!!",true);
                return;
            }
            $postTitle = parseTitle($postTitle);
            $postContent = addslashes($postContent); 

            $que = "INSERT INTO ". $board. "Threads (title)
                VALUES('$postTitle')";
            myQuery($connBoards,$que);
 
            $threadId = $connBoards->insert_id;
            $newTable = $board . "_" . $threadId;
            $redirect = "/php/?page=".$_GET['page'];
            $redirect .= "&TID=".$threadId;

            //create post table and post
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
	        //echo "diff ". $diff . "<br>";

                while($row = $res->fetch_assoc()){
		    if($diff <= 0) continue;
                    if($row['tags'] == 'pin') continue;
		    echo "current row ".$row["threadId"];

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
            $content = str_replace(">","&gt;",$content);

            $content = postParser($content) . "<br>";
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

        //what I need to do here is make sure that no more than two newlines
        //are used, no bed werd, uh oh stinky and new lines < 30
        function textVerify($content){
            $ws = 0; $we = 0;
            $clen = strlen($content);
            $retStr = $content;

            //counts new lines
            $cntNL = 0;
            while($ws < $clen){
                while($we < $clen && $content[$we] != ' ' 
                      && $content[$we] != "\n" &&
                      $content[$we] != "\r" && $content[$we] != "\r\n" &&
                      $content[$we] != "\n\r"){
                        $we++;
                }

                //check is it is a bad word
                $word = substr($content,$ws,$we-$ws);
                if(isBadWord($word)) return false;
                
                if($content[$we] == "\n" || $content[$we] == "\r\n" || 
                   $content[$we] == "\n\r"){
                        $cntNL++;
                }
                $ws = ++$we;
            }
            return $cntNL < 30; 
        }

        //essentially posts 
        //this uses a terrible way to parse the text
        //first i got and find cases of "##"
        //then i use it to find "[?]()
        //it is slow of course
        //optimize
        function postParser($content){
            $ws = 0; $we = 0;
            $clen = strlen($content);
            $retStr = $content;

            //counts new lines
            $cntNL = 0;

            while($ws < $clen){
                //this is used for pink text
                if($content[$ws] == "~"){
                    while($we < $clen && $content[$we] != "\n" &&
                      $content[$we] != "\r" && $content[$we] != "\r\n" &&
                      $content[$we] != "\n\r"){
                        $we++;      
                    }
                    //>hello -> <div style="color:pink">hello</div>
                    $tobeReplace = substr($content,$ws,$we-$ws);
                    $retStr = str_replace($tobeReplace,
                        //"<div style='color:pink'>".$tobeReplace."</div>",$retStr);
                        "<span style='color:pink'>".$tobeReplace."</span>",$retStr);
                        //problem here is that there is an extra return. 
                        //hopefully the users are not retarded
                    $ws = ++$we;
                    continue;
                }

                //i canot understadn what character html uses for newlines wtf
                //i do have a problem where the skips for characters are wrong
                while($we < $clen && $content[$we] != ' ' && $content[$we] != "\n" &&
                      $content[$we] != "\r" && $content[$we] != "\r\n" &&
                      $content[$we] != "\n\r"){
                        $we++;
                }
                //check if string is special case
                $word = substr($content,$ws,$we-$ws);

                $wlen = strlen($word);
                //dont forget escape character after anything wtf
                if($wlen > 2 && $word[0] == "#" && $word[1] == "#"){
                    $goTo= substr($word,2);
                    $replace = "<a href='javascript:jumpPost(\"pd$goTo\")'>" .
                        $word . "</a>";

                    $retStr = str_replace($word,$replace,$retStr);
                    
                    $ws = ++$we;
                    continue;
                }

                //[tag](URI){option}

                $option = 0; $cpo = 0;
                $leftChar = array('[','(','{');
                $rightChar = array(']',')','}');
                $pos = array(-1,-1,-1,-1,-1,-1);
                //search and find next `
                for($i = $ws; $i < $we; $i++,$option++){
                    while($i < $we && $content[$i] != $leftChar[$option]){
                        $i++;
                    }
                    if($content[$i] == $leftChar[$option])
                        $pos[$cpo++] = ++$i;
                    while($i < $we && $content[$i] != $rightChar[$option]){
                        $i++;
                    } 
                    if($content[$i] == $rightChar[$option])
                        $pos[$cpo++] = $i;
                }
                if($pos[0] != -1 && $pos[1] != -1 && 
                        $pos[2] != -1 && $pos[3] != -1){
                    $tmpTYPE = substr($content,$pos[0], $pos[1]-$pos[0]);
                    $tmpLNK = substr($content,$pos[2], $pos[3]-$pos[2]);

                    $tmpOPT = ""; 
                    if($pos[5] != -1){
                        $tmpOPT= substr($content,$pos[4],$pos[5]-$pos[4]);
                    } else{
                        //have to set pos[5] as pos[3] bc the str replace
                        //changes
                        $pos[5] = $pos[3];
                    }

                    $newStr = substr($content,$ws,$we-$ws);

                    if($tmpTYPE == "IMG"){
                        $newStr = integrate_IMG($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "LNK"){
                        $newStr = integrate_LNK($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "YTB"){
                        $newStr = integrate_YTB($tmpLNK,$tmpOPT);
                    } else if($tmpTYPE == "TXT"){
                        $newStr = integrate_TXT($tmpLNK,$tmpOPT);
                    }

                    //so what happens here is bc the way 
                    //i made this is that the endpoints are not
                    //counted...have to add 2
                    //old ver that replaces the whole word
                    $findStr = substr($content,$pos[0]-1,$pos[5]-$pos[0]+2); 

                    $retStr = str_replace($findStr,$newStr,$retStr);
                }
                //what is wrong with this. i dont rememberdoing it like this
                $ws = ++$we;
            }
            return $retStr;
        }

        function parseTitle($title){
            $retStr = $title;
            $retStr = str_replace("<","&lt;",$retStr);
            $retStr = str_replace(">","&gt;",$retStr);
            return $retStr;
        }
        function printPage($msg,$error=false,$redirect=""){
            global $totalBury;
            if($redirect == ""){
                $redirect = "/php/?page=".$_GET['page'];
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
		    $_POST["content"] . "</div></div>";
            }
            
            echo '
                <script>
                    setTimeout(function(){
                    location="'.$redirect.'";
                    }, 500000);

                </script> ';

        }
        class myPair{
            public $first;
            public $second;
        }
    ?>
    </body>
</html>
