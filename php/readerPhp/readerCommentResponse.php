<?php
    include_once("../../adminPower/login.php");
    include_once("../postSuccess/postFunc.php");

    $board= $TID=$pId=$uId=$newResponse= NULL;
    $type = 0;

    //error of 
    //0 - fine
    //1 - too long
    //2 - said too much n words
    //
    $errorChecker = 0;
    if(!empty($_POST["responseComment"])){
        $type = 1;
        $newResponse = $_POST["responseComment"];
        $board=$_POST["board"];
        $TID=$_POST["tid"];
        $pId = $_POST["pId"];
        $uId = getUsrSafeHash(getUsrID(),$TID,8012921);

        if(!testString($newResponse,3000)){
            $errorChecker = 1;
        } else if(!textVerify($newResponse)){
            $errorChecker = 2;
        }

        $newResponse= str_replace("<","&lt;",$newResponse);
        $newResponse= str_replace(">","&gt;",$newResponse);
        //$newResponse= nl2br($newResponse);
        $newResponse= str_replace("\n","<br>",$newResponse);
        $newResponse= str_replace("\"","\\\"",$newResponse);
        //JSON doesn't like \
        $newResponse= str_replace("/","\\/;",$newResponse);
    } else{
        $board=$_GET["board"];
        $TID=$_GET["tid"];
        $pId = $_GET["pId"];
    }
    $selector = $board."_".$TID."_comments";

    $responseJSON = "";
    $responseCnt = 0;

    $que = "SELECT responseStr,responseCnt FROM $selector
            WHERE postId=".$pId;
    $res = $connBoards->query($que);
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $mysqlRes= $row["responseStr"];
            if($mysqlRes != NULL){
                $responseJSON .= $row["responseStr"];
                $responseCnt = $row["responseCnt"];
            }
        }
    }
    //echo $que;
    /*note here
     * Json format example
     * {
     *  'p0':['uid','time','content']...
     *
     *  be sure to a ,'pX':[...]
     *  here
     */

    /*
        $responseJSON = '{"data":[[0,7548481,"6/18/22-13:43","yea"]]}';
        $responseJSON = '{"data":[[0,7548481,"6/18/22-13:43","yea"],
                                  [1,7548481,"6/18/22-13:43","nooooo"]]}';
     */
    if($type){
        if($errorChecker != 0){
            if($errorChecker == 1){
                echo "Error: Comment too long";
            }
            else if($errorChecker == 2){
                echo "Error: Stop saying the n-word";
            }
        } else {
            $time = date( 'n/d/y-H:i');
            $newInsert = '['.$responseCnt.','.$uId.',"'.
                         $time.'","'.$newResponse.'"]';
            $responseStr = $responseJSON;
            //$responseStr = '{"data":[[0,7548481,"6/18/22-13:43","yea"]]}';
            $responseJSON = '{"data":['.$newInsert."]}";
            if(strlen($responseStr) > 0){
                $responseJSON= str_replace(']}',','.$newInsert."]}",
                                           $responseStr);
            } 
            //add slashes because slashes need slashes
            $responseJSON = addslashes($responseJSON);

            $que = "UPDATE $selector
                    SET responseStr='$responseJSON',
                        responseCnt=$responseCnt
                    WHERE postId=$pId";
            $res = $connBoards->query($que);

            $que = "SELECT postCnt FROM ".$board."Threads";
            $res = $connBoards->query($que);

            updatePostCnt($board,$TID);
            generalUsrUpdate();
        }
    }
    echo $responseJSON."";
?>
