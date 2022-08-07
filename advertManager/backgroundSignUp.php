<?php
    include_once("backgroundReuse.php");
    if(!empty($_POST["username"])&& !empty($_POST["passwd"])&& !empty($_POST["email"])){
        newUser($_POST["username"],$_POST["passwd"],$_POST["email"],$_POST["promo"]);
    } 
    else if(!empty($_POST["isCheck"])){
        $toCheck = $_POST["toCheck"];
        $value = $_POST["value"];
        echo checkUnique($toCheck,$value);
    }
?>
