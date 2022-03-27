<?php
    function genAcclaim($acclaimStr){
        //example str = "0:100,1:1,2:3,...
        $length = strlen($acclaimStr);
        echo "<span class=acclaimList".
            ($length == 0 ? " style='background-color:#aaaaaa00'":"").">";
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

            echo "<img src='../../res/emotes/emote_$key.png' class=threadEmote 
                onmouseover=expandEmote(this) onmouseout=deflateEmote(this)>
                <span class=emote_counter>$value</span>";
        } 
        echo "</span>";
    }
?>
