<?php
function integrate_IMG($tmpLNK,$option=""){
    $retML = "";
    $curLNK = "";

    $tmpLNK .= ",";
    for($i = 0; $i < strlen($tmpLNK); $i++){
        if($tmpLNK[$i] == ','){
            $retML .= "<img src='" . $curLNK . "'>";
            $curLNK = "";
        }  else{
            $curLNK .= $tmpLNK[$i];
        }
    }
    return "<div style=\'display:inline-flex;flex-wrap:wrap;gap:6px;\'>" 
        . $retML . "</div>";
}

/* ------------------IMG ENDS----------------------*/

function integrate_LNK($tmpLNK,$option=""){
    $tmpLNK.=",";
    $retML="";
    $curLNK="";
    $LNKcnt=0;

    for($i = 0; $i < strlen($tmpLNK); $i++){
        if($tmpLNK[$i] == ',' && $curLNK != ''){
            if($LNKcnt++ > 0) $retML .= "<br>";
            $retML .= "<a href='" . $curLNK . "'>"
                    . $curLNK . "</a>";
            $curLNK = "";
        }  else{
            $curLNK .= $tmpLNK[$i];
        }
    }
    return ($LNKcnt > 1 ? "<br>":"").
        '<span class="linkLIST">'.$retML.'</span>'.
        ($LNKcnt > 1 ? "<br>":"");
}
/* ------------------LNK ENDS----------------------*/
function integrate_YTB($tmpLNK,$option=""){
    $youtubeRegex = "/https:\/\/www.youtube.com\/watch\?v=/i";
    $youtubeId = preg_replace($youtubeRegex, "", $tmpLNK);
    $newStr = '<div><iframe width="420" height="315" src="https://www.youtube.com/embed/'.$youtubeId.'" allowfullscreen> </iframe> </div>';
    return $newStr;
}
/* ------------------YTB ENDS----------------------*/
function integrate_TXT($tmpTXT,$option=""){
    //mysql call to check if the usrPoints is enough
    //inb4 cringe

    //read through options
    //ex options ie
    //  color:somethingsoemthing,type:bold...,
    //I think a hashmap or set would be better here
    //but PHP is hard and I don't think the N would
    //be too big here so no a big porblem
    /*
    $arrayOption = array(
        "style"=>array(
            "color","font","font-size"
        )
        "tag"=>array(
            "bold","italic"
        )
    );
     */
    $style ="";
    $curProperty = "";
    $curValue="";

    $tagLeft = $tagRight = "";
    //help so for loop is reusable
    $option .= ',';
    for($i = 0,$ws=0,$isProperty=true; $i < strlen($option); $i++){
        if($option[$i] == ":"){
            //switches to is value
            $isProperty = 0;
        } else if($option[$i] == ','){
            //to lower
            $curProperty = strtolower($curProperty);
            $curValue = strtolower($curValue);

            //is a css property
            if(!$isProperty){
                if(validProperty($curProperty,$curValue)){
                    $style .= convertProperty($curProperty).":".
                        convertValue($curProperty,$curValue).";";
                }
            }
            //is a html tag
            else if($isProperty){
                if(validTag($curProperty)){
                    $tagLeft = "<".$curProperty.">".$tagLeft;
                    $tagRight .= "</".$curProperty.">";
                }
            }
            $isProperty = 1;
            $curProperty = $curValue = "";
            //am i stupdi but the xor is a power level signifier
        } else{
            if($option[$i] !=  ' '){
                if($isProperty) $curProperty .= $option[$i];
                else $curValue .= $option[$i];
            }
        }
    }

    $newStr = $tagLeft."<span style=\'".$style."\'>".$tmpTXT .
            "</span>".$tagRight;
    return $newStr;
}
/* ------------------VIDEO ENDS----------------------*/
function integrate_VIDEO($tmpTXT,$option=""){
    //gets the .xxxx whatever that may be
    //can only support webm or mp4 
    //maybe .ogg too
    $s = strlen($tmpTXT)-1; 
    while($tmpTXT[$s] != '.') $s--; 

    $type = substr($tmpTXT,$s+1);
    if($type != "webm" && $type != "mp4" && $type != "ogg"){
        return "ERROR";
    }
    return "<video width='400' controls>
        <source src='$tmpTXT' type='video/$type'>
        You can't play this. Browser or something broken.></video> 
        <div class=videoRed><a href='$tmpTXT'>redirect: ".$tmpTXT.
        "</a></div>";
}



function convertProperty($property){
    $properList = array(
        "color" => "color",
        "size" => "font-size"
    );
    return $properList[$property];
}

function convertValue($property,$value){
    if($property == "size"){
        if($value == "xsmall")
            return "0.5rem";
        if($value == "small")
            return "0.75rem";
        if($value == "large")
            return "1.5rem";
        return "1.0rem";
    } 
    return $value;
}

function validProperty($property,$value){
    /*
    if($property == "size"){
        value 
        return $value > 
    }
     */
    return 
        ($property == "color") ||
        ($property == "size" && ($value == "xsmall" ||
            $value == "small" || $value == "large"));
}

function validTag($tag){
    return ($tag == "b") ||
        ($tag == "i");
}

?>

