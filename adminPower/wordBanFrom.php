<?php
    include_once("login.php");

    $redirect = "wordBan.php";

    if(empty($_POST["pword"]) || $_POST["pword"] != $mod_ppassword){
        echo "Only Admins can delete things";
    }
    else {
        $que =""; 
        if(!empty($_GET["deleteWord"])){
            $wd = $_GET["deleteWord"];
            $que = "DELETE FROM badWord WHERE word='$wd'";
        } else{
            $wd = $_POST["newWord"];
            $que = "INSERT INTO badWord(word) VALUE('$wd')";
        }
        //echo $que . "<br>";
        myQuery($conn,$que);
    }

    echo "redirecting to <a href='$redirect'> ... </a>";
    echo '
        <script>
        setTimeout(function(){
        location="'.$redirect.'";
        }, 5000);
        </script> ';

?>
