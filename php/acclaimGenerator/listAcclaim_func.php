<?php
    include_once("/var/www/html/adminPowerV2/login.php");
    function genEmoteList(){
        global $conn;
        $emoteQUE = "SELECT * FROM emotes";
        $emoteRES = $conn->query($emoteQUE);
        $emoteList = array("NULL");
        if($emoteRES->num_rows > 0){
            while($row = $emoteRES->fetch_assoc()){
                $emoteList[] = $row["filePATH"];
            }
        }
        return $emoteList;
    }
    function genAcclaim($acclaimStr,$emoteList=NULL){
        if($emoteList == NULL){
            $emoteList = genEmoteList();
        }
        //example str = "0:100,1:1,2:3,...
        $length = strlen($acclaimStr);
        //echo "<span class=acclaimList>";
        echo "<span class=acclaimList style=".  ($length == 0 ? 
            "'background-color: #aaaaaa00;margin-right:0px'":"''").">";
        for($i = 0; $i < $length; $i++){
                $key=0;$value = 0;
                while($i < $length && $acclaimStr[$i] != ":"){
                    $key = $key*10 + (int)$acclaimStr[$i];
                    $i++;
                } $i++;
                while($i < $length && $acclaimStr[$i] != ","){
                    $value = $value*10 + (int)$acclaimStr[$i];
                    $i++;
                } 
                $emotePATH = $emoteList[$key];
                echo "<img src='../../res/emotes/$emotePATH' class=threadEmote 
                    onmouseover=expandEmote(this) onmouseout=deflateEmote(this)>
                    <span class=emote_counter>$value</span>";
        } 
        echo "</span>";
    }
?>
