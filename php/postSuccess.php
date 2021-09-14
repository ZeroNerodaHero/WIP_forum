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

            </script> ';

        $isThread = empty($_GET["TID"]);
        echo "is thread " .$isThread."<br>";

        $board = $_GET["page"];

        if($isThread && !empty($_POST["title"]) && !empty($_POST["content"])){
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
                        content varchar(2000),
                        ip int,
                        PRIMARY KEY(postId)
                    )";
            myQuery($connBoards,$que);
            postComment($newTable,$postContent);

        } 
        else if(!($isThread) && !empty($_POST["content"])){
            $postContent = addslashes($_POST["content"]); 
            $newTable = $board."_".$_GET["TID"];
            echo $postContent . "<br>";
            postComment($newTable,$postContent);
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
    ?>
    </body>
</html>
