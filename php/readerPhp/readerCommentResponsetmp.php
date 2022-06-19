<?php
    include_once("../../adminPower/login.php");

    $board=$_GET["board"];
    $TID=$_GET["tid"];
    $pId = $_GET["pId"];
    $type = 0;
    $newResponse = NULL;
    if(!empty($_POST["newResponse"])){
        $type = 1;
        $newResponse = $_POST["newResponse"];
        $board=$_POST["board"];
        $TID=$_POST["tid"];
        $pId = $_POST["pId"];
        echo "new";
    } else{
        echo "is not new post \n";
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
                $responseJSON .= $mysqlRes;
                $responseCnt = $row["responseCnt"];
            }
        }
    }
    echo $type."|||";
    //echo $que;
    /*note here
     * Json format example
     * {
     *  'p0':['uid','time','content']...
     *
     *  be sure to a ,'pX':[...]
     *  here
     */
    if($type){
        $time = date( 'n/d/y-H:i');
        $newInsert = "'post$responseCnt':[$uid,$time,'$newResponse']";
        if(strlen($responseStr) > 0){
            $responseStr = str_replace(']}',
                                       '],'.$newInsert."}",
                                       $responseStr);
        } else{
            $responseStr = "{".$newInsert."}";
        }
        $que = "UPDATE $selector
                SET responseStr='$responseStr'
                    responseCnt=$responseCnt
                WHERE postId=$pId";
        //$res = $connBoards->query($que);
    }            
    //echo $responseJSON."->".$responseCnt;
?>
