<?php
    include_once("reuse.php");


    echo "<h1>Banned Words</h1>";

    $que = "SELECT * FROM badWord";
    $res = $conn->query($que);
    $unBan = "updateWordBanFrom.php?";

    echo "<div class=listTable><br>";
    echo "<table><tr><th>banned words</th></tr>";
    if(!empty($res) && $res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $unBanPg = $unBan . "deleteWord=" . $row["word"];
            echo "<tr><th>".$row["word"]."</th>
                  <th>
                        <form action=".$unBanPg." method='post'>
                        <input type='text' name='pword' size='5'> 
                        <input type='submit' value='X'/> </form>
                  </th>
                </tr> "; 
        }
    }
    echo "</table><br>";
    echo "</div>";


    echo "<div>ADD WORD:  
            <form action=".$unBan."add=1 method='post'>
            WORD: <input type='text' name='newWord' size='5'> 
            PASSWD: <input type='text' name='pword' size='5'> 
            <input type='submit' value='->'/> </form></div>";
?>
