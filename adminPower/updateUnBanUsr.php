<p> UNBAN USER </p>
<?php
    include_once("login.php");

    $usr = $_GET['ip'];
    $redirect = "banList.php";

    if(empty($_POST["pword"]) || $_POST["pword"] != $mod_ppassword){
        echo "Only Admins can delete things";
    }
    else {
        $que = "DELETE FROM ipBans WHERE ip=".$usr;
        //echo $que; 
        $res = myQuery($conn,$que);
    }

    echo "redirecting to <a href='$redirect'> ... </a>";
    echo '
        <script>
        setTimeout(function(){
        location="'.$redirect.'";
        }, 5000);
        </script> ';

?>
