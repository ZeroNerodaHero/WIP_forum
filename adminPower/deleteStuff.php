 <!DOCTYPE html>
<html>
    <?php
        include_once("reuse.php");
    ?>

    <body>
        <h1>Moderating Board</h1>
        
        <p>
            DOES THE GUI LOOK NICE?
            <ul>
                <li> <a href="deleteStuff.php"> GO BACK </a> </li>
            </ul>
            <div>-------------------------------------------<br></div>
        </p>

    <?php 
        ini_set("display_errors",1);
        ini_set("display_startup_errors",1);
        error_reporting(E_ALL);

        //ex url = ...?board=...&tid=...
        //can exist or not
        //i have not check if the tid and board really exist but i think
        //i dont care bc retards
        $redirect = $_SERVER['REQUEST_URI'];
        $deleteRed = "deleteContent.php";

        if(!empty($_GET["board"]) && !empty($_GET["TID"])){
            //case 1 navigating thread content
            $curBoard = $_GET["board"];
            $curTID = $_GET["TID"];
            $deleteRed .= "?board=".$curBoard."&TID=".$curTID;
            $banGen = "banUsr.php?board=".$curBoard."&TID=".$curTID;

            $que = "SELECT * FROM ".$curBoard."_".$curTID;
            $res = $connBoards->query($que);

            if($res->num_rows > 0){
                echo "<table>";       
                echo "<th>CONTENT</th><th>PID</th><th>TIME</th>
                      <th>DELETE</th><th>BAN(reason/pword)</th>";
                while($row = $res->fetch_assoc()){
                    echo "<tr>";
                    $rcontent=$row["content"];
                    $rpid=$row["postId"];
                    $rtime=$row["time"];
                    $deletePg=$deleteRed."&PID=".$rpid;
                    $banPg =$banGen . "&PID=".$rpid;

                    echo "<th><div class=usrComment>".$rcontent."</div></th>
                        <th>".$rpid."</th>
                        <th>".$rtime."</th> ";
                    echo '<th> <form action='.$deletePg.' method="post">
                            <input type="text" name="pword" size="5"> 
                            <input type="submit" value="X"/> </form>
                         </th>';
                    echo '<th><form action='.$banPg.' method="post">
                            <input type="text" name="reason" size="5">
                            <input type="text" name="pword" size="5"> 
                            <input type="submit" value="!"/> </form>
                         </th>';
                    echo "</tr>";
                }
                echo "</table>";
            } else{
                echo "NADA<br>";
            }
        } else if(!empty($_GET["board"])){
            //case 2 navigating board threads
            $curBoard = $_GET["board"];
            $que = "SELECT * FROM ".$curBoard."Threads ORDER BY time DESC";
            $res = $connBoards->query($que);
            $deleteRed .= "?board=".$curBoard;

            if($res->num_rows > 0){
                echo "<table>";       
                echo "<th>TITLE</th><th>TID</th><th>TIME</th>
                      <th>TAGS</th><th>GOTO</th><th>DELETE</th>";
                while($row = $res->fetch_assoc()){
                    echo "<tr>";
                    $rtitle = $row["title"];
                    $rtid = $row["threadId"];
                    $rtime = $row["time"];
                    $rtags = $row["tags"];
                    $tmp = $redirect . "&TID=".$rtid;
                    $deletePg = $deleteRed . "&TID=".$rtid;

                    echo "<th><div class=usrComment>".$rtitle."</div></th>
                          <th>".$rtid."</th><th>".$rtime."</th>
                          <th>".$rtags."</th><th><a href='$tmp'> > </a></th>";
                    echo '<th> <form action='.$deletePg.' method="post">
                            <input type="text" name="pword" size="5"> 
                            <input type="submit" value="X"/> </form>
                         </th>';
                    echo "</tr>";
                }
                echo "</table>";       
            } else{
                echo "NADA<br>";
            }
        } else {
            //case 3 navigating board
            $que = "SELECT * FROM boards";
            $res = $conn->query($que);

            if($res->num_rows > 0){
                echo "<table>";
                echo "<th>Board Name</th><th>TYPE</th><th>GOTO</th>
                      <th>EDIT DESC(NEW DESC/pword)</th>";
                while($row = $res->fetch_assoc()){
                    echo "<tr>";
                    $bname = $row["boardName"];
					$descript = $row["descript"];
					$lnkToGo = "updateBoardDesc.php?board=".$bname;
                    $tmp = $redirect."?board=".$bname;
                    echo "
                        <th>".$bname."</th>
                        <th>".$row["typeOfBoard"]."</th>
                        <th><a href=".$tmp."> > </a></th>"; 

                    echo '<th> <form action='.$lnkToGo.' method="post">
                            <input type="text" name="descript" size="25"
								value="'.$descript.'">
                            <input type="text" name="pword" size="5"> 
                            <input type="submit" value="O"/> </form>
                         </th>';

                    echo "</tr>";

                }
                echo "</table>";
            } 
            else{
                echo "no boards?<br>";
            }
        }
    ?>
    <p>
        penispenis
    </p>
    </body>
</html> 
