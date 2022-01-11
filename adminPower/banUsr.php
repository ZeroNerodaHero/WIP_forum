<p> BAN USER </p>
<?php
    include_once("login.php");

    $cboard = $_GET["board"];
    $TID = $_GET["TID"];
    $PID = $_GET["PID"];
    $redirect = "deleteStuff.php?board=".$cboard;

    echo "OK: " . $cboard . " TID:" . $TID . " PID: " . $PID . "<br>";

    if(empty($_POST["reason"]) || empty($_POST["pword"]) || 
        $_POST["pword"] != $mod_ppassword){
        echo "Only Admins can delete things";
    }
    else {
        $que = "SELECT ip FROM ".$cboard."_".$TID." WHERE postId=".$PID;
        //echo $que; 
        $res = $connBoards->query($que);
        if(!empty($res) && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                echo "ID: " .$row["ip"] . "<br>";
                banUsr($row["ip"],$_POST["reason"],"1 0:0:0");
                //banUsr($row["ip"],$_POST["reason"],"0 0:0:0");
            }
        } else{
            echo "something went wrong lmao <br>";
        }
    }

    echo "redirecting to <a href='$redirect'> ... </a>";
    echo '
        <script>
        setTimeout(function(){
        location="'.$redirect.'";
        }, 5000);
        </script> ';

?>
