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
        echo '
            <script>
                setTimeout(function(){
                location="'.$redirect.'";
                }, 5000);

            </script> 
            <a href="'.$redirect.'">GO BACK</a>';

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
            echo $postContent . "<br>";
            postComment($newTable,$postContent);
            bumpThread($board,$_GET["TID"]);
        } else{
            echo strlen($_POST["title"]). " " . strlen($_POST["content"]); 
            echo "<p> uh oh u might have typed or did something wrong </p>";
        }

        function postComment($threadTable,$content){
            global $connBoards;
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
    ?>
    </body>
</html>
