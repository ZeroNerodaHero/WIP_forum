<!DOCTYPE html>
<html>
    <?php
        include_once("reuse.php");
    ?>

    <body>
    <?php 
        ini_set("display_errors",1);
        ini_set("display_startup_errors",1);
        error_reporting(E_ALL);

        $boardName = $_POST["boardName"];
        $pinnedTit = addslashes($_POST["pinTit"]);
        $pinnedPost = addslashes($_POST["pinned"]);
        $ppassword = $_POST["password"];
        if($boardName && $pinnedTit && $pinnedPost && $ppassword && 
           $ppassword==$admin_ppassword){
            /************************************************/
            //create board threads
            $que = "CREATE TABLE " . $boardName . "Threads (
                        title varchar(300),
                        threadId int NOT NULL AUTO_INCREMENT,
                        time TIMESTAMP,
                        tags VARCHAR(50) NULL,
                        PRIMARY KEY(threadId)
                    )";

            echo $que . "<br>";
myQuery($connBoards,$que);
            
            $que = "INSERT INTO ". $boardName. "Threads (title,tags)
                VALUES('$pinnedTit','pin')";
            echo $que . "<br>";
myQuery($connBoards,$que);

            $threadId = $connBoards->insert_id;
            $newTable = $boardName . "_" . $threadId;

            /************************************************/

            $que = "INSERT INTO boards(typeOfBoard,boardName)
                    VALUES(\"shit\",'$boardName')";
            echo $que . "<br>";
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
            echo $que . "<br>";
 myQuery($connBoards,$que);

            $usrIP = ip2long(getusrIP()); 
            echo "ip is " .$usrIP . " and " . empty($usrIP) ."<br>";
            $que = "INSERT INTO ". $newTable . "(
                        content,ip) VALUES( '$pinnedPost',";
            if(!empty($usrIP)) $que .= $usrIP.")";
            else $que .= "NULL)";

            echo $que . "<br>";
 myQuery($connBoards,$que);


        }
        ?>
    </body>
</html> 