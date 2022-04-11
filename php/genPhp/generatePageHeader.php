<?php
    include_once("/var/www/html/adminPower/login.php");
    $descript = "FUNCEL.XYZ is an anonymous text-board. ";
    $tabTitle = "[news]";

    $threadTitle = "ERROR: THREAD DOESN'T EXIST";
    $pgN = 0;

    // threadType tells waht type
    // 0:text, 1:anote
    $threadType = 0;

    $boardPageName = "news";
    if(!empty($_GET["page"]))
        $boardPageName = $_GET["page"];

    $que = "SELECT * FROM boards";;
    $res = $conn->query($que);
    $allBoards = array();
    while($row = $res->fetch_assoc())
        $allBoards[] = $row;


    //if page is board = 0
    //if page is thread = 1
    $typeOfPage=($boardPageName != "news" && !empty($_GET["TID"]));
    $threadID = ($typeOfPage == 1) ? $_GET["TID"] : -1;
    $boardThreads = array();
    if($boardPageName != "news"){
        //first the pin then everything else bc no need for double for loops
        $que = "SELECT * FROM ".$boardPageName."Threads WHERE tags='pin'";
        $res = $connBoards->query($que);

        //never have to check this again? or use null
	if($res->num_rows > 0){
            while($row = $res->fetch_assoc())
                $boardThreads[] = $row; 
        }
        $que = "SELECT * FROM ".$boardPageName."Threads WHERE tags IS NULL 
                ORDER BY time DESC";
        $res = $connBoards->query($que);

        //never have to check this again? or use null
	if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                if($typeOfPage && $threadID == $row["threadId"]){
                    $threadType = $row["newTag"];
                }
                $boardThreads[] = $row; 
            }
        }
    }
    $threadContent = array();
    $threadContentSize = 0; 
    if($typeOfPage == 1 && $threadType == 0){
        $que = "SELECT * FROM ".$boardPageName."_".$threadID;
        $res = $connBoards->query($que);
        
	if($res->num_rows > 0){
            while($row = $res->fetch_assoc())
                $threadContent[] = $row; 
        }
        $threadContentSize = sizeof($threadContent); 
    } else if( $typeOfPage == 1 && $threadType == 1){
        $que = "SELECT postId FROM ".$boardPageName."_".$threadID."_comments";
        $res = $connBoards->query($que);
        $threadContentSize = $res->num_rows;
    }
    /*
     *  allBoards -> done here to get the board descript and also used in nav
     *  boardThreads -> need here 
     *  threadContent -> need here and the recent
     *  ???threads from other done with js? since doesn't change without 
     *      user touch
     */
    if($boardPageName != "news"){
	foreach($allBoards as $row){
            if($row["boardName"] == $boardPageName){
	        $descript .= $row["descript"];
                break;
            }
	}
	$tabTitle = "[".$boardPageName."]";

        //essentially is a thread
	if($typeOfPage == 1 && $boardThreads != NULL){
	    foreach($boardThreads as $row){
                if($row["threadId"] == $threadID){
                    $threadTitle = $row["title"];
	            $tabTitle .= " ".$row["title"];
                }
	    }
        } else {
            $tabTitle .= " Threads";
        }
    } else{
        $descript .= "Welcome.";
    }
    echo "<title>$tabTitle : FUNCEL.XYZ</title>";
    echo "<meta name='description' content='$descript'>";
?>

