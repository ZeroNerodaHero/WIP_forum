<?php
    include_once("../adminPowerV2/login.php");
    function newUser($username,$password,$email,$promo){
        global $conn;
        $usrId = rand()<<32|rand();
        $promoCredit = ($promo != NULL && $promo=="test10"?10:0);
        $que = "INSERT INTO advertManager(username,password,email,userId,credits)
                VALUES('$username','$password','$email',$usrId,$promoCredit)";
        myQuery($conn,$que);
    }
    function checkUnique($toCheck,$value){
        global $conn;
        $que = "SELECT * FROM advertManager WHERE $toCheck='$value'";
        $res = $conn->query($que);
        return ($res && $res->num_rows > 0)?1:0;
    }
?>
