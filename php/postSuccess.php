<!DOCTYPE html>
<html>
    <body>

    <h>
        POST SUCCESSFULL;
    </h>

    <p id="redirect">
        REDIRECTING BACK IN 5 sec.
    </p>

    <?php
        error_reporting(-1);
        ini_set('display_errors',1);

        include_once("../adminPower/login.php");

        //$redirect = "frontpage.php?page=".$_GET['page']."&TID=".$_GET["TID"];
        $redirect = "frontpage.php?page=".$_GET['page'];

        $isThread = empty($_GET["TID"]);

        $board = $_GET["page"];

        //new thread
        if($isThread && !empty($_POST["title"]) && !empty($_POST["content"]) &&
            strlen($_POST["title"]) < 300 && strlen($_POST["content"]) < 7500){
            $postTitle = addslashes($_POST["title"]); 
            $postContent = addslashes($_POST["content"]); 

            $que = "INSERT INTO ". $board. "Threads (title)
                VALUES('$postTitle')";
            myQuery($connBoards,$que);
 
            $threadId = $connBoards->insert_id;
            $newTable = $board . "_" . $threadId;
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
        //new post
        else if(!($isThread) && !empty($_POST["content"]) &&
                strlen($_POST["content"]) < 7500){
            $postContent = addslashes($_POST["content"]); 
            $newTable = $board."_".$_GET["TID"];
            $redirect .= "&TID=".$_GET["TID"];
//echo $postContent . "<br>";
            postComment($newTable,$postContent);
            bumpThread($board,$_GET["TID"]);
        } else{
            echo strlen($_POST["title"]). " " . strlen($_POST["content"]); 
            echo "<p> uh oh u might have typed or did something wrong </p>";
        }

        echo '
            <script>
                setTimeout(function(){
                location="'.$redirect.'";
                }, 5000);

            </script> 
            <a href="'.$redirect.'">GO BACK</a>';

        function postComment($threadTable,$content){
            global $connBoards;
            $content = postParser($content) . "<br>";
            $content = nl2br($content);
            $usrIP = ip2long(getusrIP());
            $que = "INSERT INTO ". $threadTable . "( content,ip) 
                    VALUES( '$content',";
            if(!empty($usrIP)) $que .= $usrIP.")";
            else $que .= "NULL)";

            myQuery($connBoards,$que);
        }
        function bumpThread($board,$tid){
            global $connBoards;
            $que = "UPDATE ".$board."Threads SET time=CURRENT_TIMESTAMP
                    WHERE threadId='$tid'";
            myQuery($connBoards,$que);
        }

        //essentially posts 
        function postParser($content){
            $ws = 0; $we = 0;
            $clen = strlen($content);
            $retStr = $content;
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
                echo $ws ." to " .$we . " " . $word . "<br>";

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
                    echo "<br>";
                    echo $pos[($option * 2)] . " ---- " . $pos[($option * 2+1)] . "<br>";
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
                    $retStr = str_replace($retStr,substr($content,$ws,$we-$ws),
                                            $newStr);

                }

                
            
                //what is wrong with this. i dont rememberdoing it like this
                $ws = ++$we;
            }
            echo "returns" .$retStr . "<br>";
            return $retStr;
        }
        class myPair{
            public $first;
            public $second;
        }
    ?>
    </body>
</html>
