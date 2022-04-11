<?php 
    include_once("../reuse.php");
    
    $que = "SELECT * FROM boards";
    $resb = $conn->query($que);

    if($resb->num_rows > 0){
        while($row = $resb->fetch_assoc()){
            $boardName = $row["boardName"];
            echo "$boardName good<br>";

            $que = "SHOW COLUMNS FROM ".$boardName."Threads LIKE 'newTag'";
            $resExist = $connBoards->query($que);             
            

            if($resExist->num_rows == 0){
                $que = "ALTER TABLE $boardName"."Threads  
                        ADD newTag int not null default 0 
                        AFTER tags";
                myQuery($connBoards,$que);
            }
        }
    }
?>


