 <!DOCTYPE html>
<html>
    <head>
    <title>Create Board</title>

    <style>
        .tit{
            width: 40%;
        }
        
    </style>
    </head>

    <body>
        <h1>Create Board</h1>
        
        <p>
            <ul>
                <li> Don't add / to the thing. No need.
                <li> Use the pinned post as a way to post rules and stuff. yea
            </ul>
        </p>

        <form action="createBoard.php" method="post">
            Board Name: <input type="text" name="boardName" class="tit"> <br>
            Post Title: <input type="text" name="pinTit" class="tit"> <br>
            Pinned Post: <br>
                <textarea name="pinned" rows="6" cols="100" ></textarea>
            <br>
            Password: <input type="text" name="password">
            <input type="submit" value="Post">    
        </form>

        <br> 
    <?php 
        ini_set("display_errors",1);
        ini_set("display_startup_errors",1);
        error_reporting(E_ALL);

        include_once("login.php");

        $boardName = $_POST["boardName"];
        $pinnedTit = addslashes($_POST["pinTit"]);
        $pinnedPost = addslashes($_POST["pinned"]);
        $ppassword = $_POST["password"];
        if($boardName && $pinnedTit && $pinnedPost && $ppassword && 
           $ppassword==$admin_ppassword){
            /************************************************/
            //create board threads
            $que = "CREATE TABLE " . $boardName . "Threads (
                        title varchar(200),
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
                        content varchar(2000),
                        ip int,
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
