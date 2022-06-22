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

    function newPostChecker($newResponse){
        $errorChecker = 0;
        $usrId = getUsrID();
        if(!testString($newResponse,3000)){
            $errorChecker = 1;
        } else if(!textVerify($newResponse)){
            $errorChecker = 2;
        } else if(checkBan($usrId)){
            $errorChecker = 3;
        } else if(usrCanPost($usrId) != NULL){
            $errorChecker = 4;
        }
        return $errorChecker;
    }
    function errorDisplay($errorCode){
        if($errorCode== 1){
            echo "Error: Comment too long";
        }
        else if($errorCode== 2){
            echo "Error: Stop saying the n-word";
        }
        else if($errorCode== 3){
            echo "Error: You are BANNED. WACKED.";
            checkBan(getUsrId(),true);
        }
        else if($errorCode== 4){
            echo "Error: Give it a minute. Posting too fast.";
        }
    }
?>
