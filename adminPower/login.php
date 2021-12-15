<?php
	include_once("commWserver.php");

    $data = "peepo";
    $conn = mysqli_connect($servername,$user,$pass,$data) 
        or die("can't connect to mySql");

    $boardData = "peepoPost";
    $connBoards = mysqli_connect($servername,$user,$pass,$boardData) 
        or die("can't connect to mySql");

    function myQuery($mcon,$que,$msg=""){
        if($mcon->query($que)){
            //echo "successfully added " .$msg;
        } else{
            echo "failed to add " . $que;
            echo "<br>";
        }
    }

    function getusrIP(){
        if(!empty($_SERVER['REMOTE_ADDR'])){
       	    return ip2long($_SERVER['REMOTE_ADDR']);
		}
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    	return 0;
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
		return 0;
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    function checkBan($usr_IP){
        global $conn;
        $curTime = date("Y-m-d H:i:s",time());
        
        //dev
        $que = "SELECT * FROM ipBans WHERE ip = '$usr_IP'";
        $res = $conn->query($que); 

        if(!empty($res) && $res->num_rows != 0){
            while($row = $res->fetch_assoc()){
                echo $curTime. " ::::: " . $row["expire"] . "<br>";
                if($curTime > $row['expire']){
                    $que = "DELETE FROM ipBans WHERE ip='$usr_IP'";
                    myQuery($conn,$que);
                    return false;
                } else{
                    echo "banned checkBan<br>";
                    return true;
                }
            }
        } 
        return false;
    }
    function banUsr($usr_IP,$reason,$expire_time){
        global $conn;
        $que = "INSERT INTO ipBans(ip,reason,expire)
                VALUES ('$usr_IP','$reason',
                ADDTIME(CURRENT_TIMESTAMP,'$expire_time'))";
        myQuery($conn,$que); 
    }
    function isBadWord($word){
        global $conn;

        $que = "SELECT * FROM badWord WHERE word='$word'";
        $res = $conn->query($que);

        return !empty($res) && $res->num_rows != 0; 
    }
?>
