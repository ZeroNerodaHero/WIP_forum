<?php
        include_once("reuse.php");
?>

<?php

    echo " <h1> BAN LIST </h1>";

    $que = "SELECT * FROM ipBans";
    $res = $conn->query($que);
    $unBan = "updateUnBanUsr.php?ip=";

    echo "<table><tr><th>IP</th><th>REASON</th><th>START</th><th>END</th></tr>";
    if(!empty($res) && $res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $unBanPg=$unBan . $row["ip"];
            echo "<tr><th>".$row["ip"]."</th><th>".$row["reason"]."</th>
                  <th>".$row["time"]."</th>  <th>".$row["expire"]."</th>
                  <th>
                        <form action=".$unBanPg." method='post'>
                        <input type='text' name='pword' size='5'> 
                        <input type='submit' value='X'/> </form>
                  </th>
                </tr> "; 
        }
    }
    echo "</table>";
?>
