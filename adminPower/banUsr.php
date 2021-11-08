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

        $ip=0;
    }

    echo "redirecting to <a href='$redirect'> ... </a>";
    echo '
        <script>
        setTimeout(function(){
        location="'.$redirect.'";
        }, 5000);
        </script> ';

?>
