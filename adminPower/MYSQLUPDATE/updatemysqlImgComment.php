<?php 
    include_once("../reuse.php");
    
    $que = "SELECT * FROM boards";
    $resb = $conn->query($que);

    if($resb->num_rows > 0){
        while($row = $resb->fetch_assoc()){
            $boardName = $row["boardName"];
            $que = "SELECT * FROM ".$boardName."Threads";
            $resT = $connBoards->query($que);

            if($resT->num_rows > 0){
                while($row = $resT->fetch_assoc()){
                    $threadId = $row["threadId"];
                    $annoteTag = $row["newTag"];
                    if($annoteTag != 1) continue;

                    $selector=$boardName."_".$threadId."_comments";
                    $que = "SHOW COLUMNS FROM ".$selector." LIKE 'responseStr'";
                    $resExist = $connBoards->query($que);             

                    if($resExist->num_rows == 0){
                        $que = "ALTER TABLE $selector
                                ADD responseStr MEDIUMTEXT NULL
                                AFTER comment";
                        myQuery($connBoards,$que);
                    }
                    $que = "SHOW COLUMNS FROM ".$selector." LIKE 'responseCnt'";
                    $resExist = $connBoards->query($que);             

                    if($resExist->num_rows == 0){
                        $que = "ALTER TABLE $selector
                                ADD responseCnt int not null default 0
                                AFTER responseStr";
                        myQuery($connBoards,$que);
                    }
                          
                }
            }
        }
    }
?>


