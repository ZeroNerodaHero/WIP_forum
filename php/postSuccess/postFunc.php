<?php
    function testString($strr,$maxLen){
        if(empty($strr) || strlen($strr) > $maxLen)
            return false;

        $lenTit = strlen($strr);
        $i = 0;
        for(; $i < $lenTit; $i++){
            if($strr[$i] != ' ') break;
        }
        return $lenTit !=  $i;
    }

    //what I need to do here is make sure that no more than two newlines
    //are used, no bed werd, uh oh stinky and new lines < 30
    function textVerify($content){
        $ws = 0; $we = 0;
        $clen = strlen($content);
        $retStr = $content;

        //counts new lines
        $cntNL = 0;
        while($ws < $clen){
            while($we < $clen && $content[$we] != ' ' 
                  && $content[$we] != "\n" &&
                  $content[$we] != "\r" && $content[$we] != "\r\n" &&
                  $content[$we] != "\n\r"){
                    $we++;
            }

            //check is it is a bad word
            $word = substr($content,$ws,$we-$ws);
/*        
            $offsetBack = 0;
            $wordLen = $we-$ws;
            while($word[$wordLen-$offsetBack] > 
 */
            if(isBadWord($word)) return false;
            
            if($content[$we] == "\n" || $content[$we] == "\r\n" || 
               $content[$we] == "\n\r"){
                    $cntNL++;
            }
            $ws = ++$we;
        }
        return $cntNL < 30; 
    }

    function parseText($title){
        $retStr = $title;
        $retStr = str_replace("<","&lt;",$retStr);
        $retStr = str_replace(">","&gt;",$retStr);
        return $retStr;
    }
?>
