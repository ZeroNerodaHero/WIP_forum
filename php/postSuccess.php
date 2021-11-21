<!DOCTYPE html>
<html>
    <body>

    <p id="redirect">
        REDIRECTING BACK IN 5 sec.
    </p>

    <?php
        error_reporting(-1);
        ini_set('display_errors',1);

        include_once("../adminPower/login.php");

        $redirect = "frontpage.php?page=".$_GET['page'];

        $isThread = empty($_GET["TID"]);

        $board = $_GET["page"];
        $time = 5000;

        if(checkBan(ip2long(getusrIP()))){
            $time = 5000;
            echo "you're banned<br>";
        }
        //new thread
        else if($isThread && !empty($_POST["title"]) && 
                !empty($_POST["content"]) && strlen($_POST["title"]) < 300 
                && strlen($_POST["content"]) < 7500){

            manageNewThread($_POST["title"],$_POST["content"],$board);
        } 
        //new post
        else if(!($isThread) && !empty($_POST["content"]) &&
                strlen($_POST["content"]) < 7500){

            manageNewPost($_POST["content"],$board,$_GET["TID"]);
        } else{
            echo strlen($_POST["title"]). " " . strlen($_POST["content"]); 
            echo "<p> uh oh u might have typed or did something wrong </p>";
            $time = 5000;
        }

        echo '
            <script>
                setTimeout(function(){
                location="'.$redirect.'";
                }, '.$time.');

            </script> 
            <a href="'.$redirect.'">GO BACK</a>';
        function manageNewPost($postContent,$board,$TID){
            $postContent = addslashes($postContent); 
            $newTable = $board."_".$TID;
            $redirect = "frontpage.php?page=".$_GET['page'];
            $redirect = "&TID=".$TID;

            postComment($newTable,$postContent);
            bumpThread($board,$TID);
        }

        function manageNewThread($postTitle,$postContent,$board){
            global $connBoards,$maxThreads;
            $postTitle = addslashes($postTitle); 
            if(!textVerify($postTitle)){
                echo "YOU SAID BAD WORD!!!!!<br>";
                return;
            }
            $postTitle = parseTitle($postTitle);
            $postContent = addslashes($postContent); 

            $que = "INSERT INTO ". $board. "Threads (title)
                VALUES('$postTitle')";
            myQuery($connBoards,$que);
 
            $threadId = $connBoards->insert_id;
            $newTable = $board . "_" . $threadId;
            $redirect = "frontpage.php?page=".$_GET['page'];
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
            postComment($newTable,$postContent);

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
        function postComment($threadTable,$content){
            global $connBoards;
            if(!textVerify($content)){
                echo "YOU SAID BAD WORD!!!!!<br>";
                return;
            }
            $content = postParser($content) . "<br>";
            $content = nl2br($content);
            $usrIP = ip2long(getusrIP());
            $que = "INSERT INTO ". $threadTable . "(time,content,ip) 
                    VALUES( CURRENT_TIMESTAMP,'$content',";
            if(!empty($usrIP)) $que .= $usrIP.")";
            else $que .= "NULL)";

            myQuery($connBoards,$que);
            echo "POST SUCCESSFUL!";
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


                //[tag](URI){option}
                //[tag](URI){option}

                $option = 0; $cpo = 0;
                $leftChar = array('[','(','{');
                $rightChar = array(']',')','}');
                $pos = array(-1,-1,-1,-1,-1,-1);
                //search and find next `
                // for for for
                // for
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
                    }
                    //$retStr = str_replace($retStr,substr($content,$ws,$we-$ws), $newStr);
                    $retStr = str_replace(substr($content,$ws,$we-$ws), $newStr,$retStr);

                }

                
            
                //what is wrong with this. i dont rememberdoing it like this
                $ws = ++$we;
            }
            echo "returns" .$retStr . "<br>";
            return $retStr;
        }

        function parseTitle($title){
            $retStr = $title;
            $retStr = str_replace("<","&lt;",$retStr);
            $retStr = str_replace(">","&gt;",$retStr);
            return $retStr;
        }
        class myPair{
            public $first;
            public $second;
        }
    ?>
    </body>
</html>
