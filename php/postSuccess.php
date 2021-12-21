<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../css/postSuccess.css">
        <link rel="icon" href="../../res/icon/icon_0.png">
        <title>FUNCEL.XYZ</title>
    </head>

    <body class=postSuccessBody>

    <?php
//debug
/*
error_reporting(-1);
ini_set('display_errors',1);
*/
        include_once("../adminPower/login.php");

        $redirect = "php/?page=".$_GET['page'];

        $isThread = empty($_GET["TID"]);

        $board = $_GET["page"];
        $time = 5000;

        if(checkBan(ip2long(getusrIP()))){
            $time = 5000;
            printPage("You're banned");
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
            $postContent = addslashes($postContent); 
            $newTable = $board."_".$TID;
            $redirect = "/php/?page=".$_GET['page'];
            $redirect = "&TID=".$TID;

            postComment($board,$TID,$postContent);
            bumpThread($board,$TID);
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

            //deletes tables when > 15
            if(!empty($res) && $res->num_rows > $maxThreads){
                while($row = $res->fetch_assoc()){
                    if($row['tags'] == 'pin') continue;

                    $que = "DROP TABLE ".$board."_".$row["threadId"];
                    myQuery($connBoards,$que);

                    //literal fucking dogshit code
                    $que = "DELETE FROM ".$board."Threads 
                            WHERE threadId=".$row["threadId"];
                    myQuery($connBoards,$que);
                    break;
                }
            }
        }

        //make error thing
        function postComment($page,$TID,$content){
            global $connBoards;
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
            //$usrIP = ip2long(getusrIP());
            $usrIP = getusrIP();
            $que = "INSERT INTO ". $threadTable . "(time,content,ip) 
                    VALUES( CURRENT_TIMESTAMP,'$content',";
            if(!empty($usrIP)) $que .= $usrIP.")";
            else $que .= "NULL)";

            myQuery($connBoards,$que);
            printPage("POST SUCCESSFUL!",false,$redirect);
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
                while($we < $clen && $content[$we] != ' ' && $content[$we] != "\n" &&
                      $content[$we] != "\r" && $content[$we] != "\r\n" &&
                      $content[$we] != "\n\r"){
                        $we++;
                }

                //check is it is a bad word
                $word = substr($content,$ws,$we-$ws);
                if(isBadWord($word)) return false;
                
                if($content[$we] != "\n" && $content[$we] != "\r" && $content[$we] != "\r\n" &&
                   $content[$we] != "\n\r"){
                        $cntNL++;
                }
                $ws = ++$we;
            }
            //return $cntNL < 50; 
            return true; 
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
                    $replace = "<a href=\'javascript:jumpPost(\"pd$goTo\")\'>" .
                        $word . "</a>";

                    $retStr = str_replace($word,$replace,$retStr);
                    
                    $ws = ++$we;
                    continue;
                }

                //[tag](URI){option}
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
                    $pos[$cpo++] = ++$i;
                    while($i < $we && $content[$i] != $rightChar[$option]){
                        $i++;
                    } 
                    $pos[$cpo++] = $i;
                }
                if($pos[0] != -1 && $pos[1] != -1 && $pos[2] != -1 && $pos[3] != -1){
                    $tmpTYPE = substr($content,$pos[0], $pos[1]-$pos[0]);
                    $tmpLNK = substr($content,$pos[2], $pos[3]-$pos[2]);
                    $newStr = substr($content,$ws,$we-$ws);

                    if($tmpTYPE == "IMG"){
                        $newStr = '<img src='.$tmpLNK.'>';
                        
                    } else if($tmpTYPE == "LNK"){
                        $newStr = '<a href='.$tmpLNK.'>'.$tmpLNK.'</a>';
                    } else if($tmpTYPE == "YTB"){
						$youtubeRegex = "/https:\/\/www.youtube.com\/watch\?v=/i";
						$youtubeId = preg_replace($youtubeRegex, "", $tmpLNK);
						$newStr = '<div><iframe width="420" height="315" src="https://www.youtube.com/embed/'.$youtubeId.'" allowfullscreen></iframe> </div>';
					}

                    //so what happens here is bc the way i made this is that the endpoints are not
                    //counted...have to add 2
                    //old ver that replaces the whole word
                    //$retStr = str_replace(substr($content,$ws,$we-$ws), $newStr,$retStr);
                    $retStr = str_replace(substr($content,$pos[0]-1,$pos[3]-$pos[0]+2), $newStr,$retStr);
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
            echo "<div class=postSuccessHeader><mark id=".$messageError."> "
					. $msg ."</mark></div>";
            
            echo '<p class=buryQuote> 
                    You have been blessed with Bury#'.$buryPic.'.<br><br>
                    <a href="'.$redirect.'">
                        <img src = "../res/buries/bury_'.$buryPic.'.png" 
                        id=postSuccessIMG>
                    </a><br></p>';

            echo '
                <p id="postSuccessRedirect">
                    REDIRECTING BACK IN 5 sec . . . 
                    <a href="'.$redirect.'" class=redLink>GO BACK</a>
                </p> ';
            
            echo '
                <script>
                    setTimeout(function(){
                    location="'.$redirect.'";
                    }, 50000);

                </script> ';

        }
        class myPair{
            public $first;
            public $second;
        }
    ?>
    </body>
</html>
