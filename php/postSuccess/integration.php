<?php
function integrate_IMG($tmpLNK,$option=""){
    return '<img src='.$tmpLNK.'>';
}
function integrate_LNK($tmpLNK,$option=""){
    return '<a href='.$tmpLNK.'>'.$tmpLNK.'</a>';
}
function integrate_YTB($tmpLNK,$option=""){
    $youtubeRegex = "/https:\/\/www.youtube.com\/watch\?v=/i";
    $youtubeId = preg_replace($youtubeRegex, "", $tmpLNK);
    $newStr = '<div><iframe width="420" height="315" src="https://www.youtube.com/embed/'.$youtubeId.'" allowfullscreen> </iframe> </div>';
    return $newStr;
}

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
            //is a css property
            if(!$isProperty){
                if(validProperty($curProperty,$curValue)){
                    $style .= $curProperty.":".$curValue.";";
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
    echo "new style = " .$style . "<br>";
    return $newStr;
}

function validProperty($property,$value){
    $property = strtolower($property);
    /*
    if($property == "size"){
        value 
        return $value > 
    }
     */
    return 
        ($property == "color");
}

function validTag($tag){
    $tag = strtolower($tag);
    return ($tag == "b") ||
        ($tag == "i");
}
?>
