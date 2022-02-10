<?php
    include_once("/var/www/html/adminPower/login.php");
    $descript = "FUNCEL.XYZ is an anonymous text-board. ";
    $tabTitle = "[news]";

    $threadTitle = "ERROR: THREAD DOESN'T EXIST";

    $que = "SELECT * FROM boards";
    $allBoards = $conn->query($que);

    $boardPageName = "news";
    if(!empty($_GET["page"]))
        $boardPageName = $_GET["page"];

    //if page is board = 0
    //if page is thread = 1
    $typeOfPage=($boardPageName != "news" && !empty($_GET["TID"]));
    $threadID = ($typeOfPage == 1) ? $_GET["TID"] : -1;
    $boardThreads = NULL;
    if($boardPageName != "news"){
        $que = "SELECT * FROM ".$boardPageName."Threads";
        $res = $connBoards->query($que);

        //never have to check this again? or use null
	if($res->num_rows > 0){
            $boardThreads = $res; 
        }
    }

    $threadContent = NULL;
    if($typeOfPage == 1){
        $que = "SELECT * FROM ".$boardPageName."_".$threadID;
        $res = $connBoards->query($que);
        
	if($res->num_rows > 0){
            $threadContent = $res; 
        }
    }
    /*
     *  allBoards -> done here to get the board descript and also used in nav
     *  boardThreads -> need here 
     *  threadContent -> need here and the recent
     *  ???threads from other done with js? since doesn't change without 
     *      user touch
     */
    if($boardPageName != "news"){
	while($row = $allBoards->fetch_assoc()){
            if($row["boardName"] == $boardPageName){
	        $descript .= $row["descript"];
            }
	}
	$tabTitle = "[".$boardPageName."]";

        //essentially is a thread
	if($typeOfPage == 1 && $boardThreads != NULL){
	    while($row = $boardThreads->fetch_assoc()){
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

