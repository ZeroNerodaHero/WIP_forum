<?php 
    include_once("reuse.php");
    
    $que = "SELECT * FROM boards";
    $resb = $conn->query($que);

    if($resb->num_rows > 0){
        while($row = $resb->fetch_assoc()){
            $boardName = $row["boardName"];
            echo "$boardName good<br>";

            $que = "SHOW COLUMNS FROM ".$boardName."Threads LIKE 'postCnt'";
            $resExist = $connBoards->query($que);             
            

            if($resExist->num_rows == 0){
                $que = "ALTER TABLE $boardName"."Threads  
                        ADD postCnt int not null default 0 
                        AFTER tags";
                myQuery($connBoards,$que);
            }
            $que = "SELECT * FROM $boardName"."Threads";
            $resThreads = $connBoards->query($que);
echo "$que --$resThreads->num_rows<br>";

            if($resThreads->num_rows > 0){
                while($rowThread = $resThreads->fetch_assoc()){
                    $TIDiQ = $rowThread["threadId"];
                    $TIDoldTime = $rowThread["time"];

                    $que = "SELECT * FROM $boardName"."_"."$TIDiQ";
                    $resCnt = $connBoards->query($que);

                    $TIDcnt = $resCnt->num_rows;

                    $que = "UPDATE $boardName"."Threads
                            SET postCnt = $TIDcnt, time='$TIDoldTime'
                            WHERE threadId = $TIDiQ";
echo "$que <br>";
                    myQuery($connBoards,$que);
                }
            }
        }
    }
?>


