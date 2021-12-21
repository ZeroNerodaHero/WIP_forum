<?php
	$reLnk = '/php';
	$navStr = "<a href='$reLnk' id=bheaderLnk>FUNCEL.XYZ</a>";

	if(empty($_GET["page"]) || $_GET["page"] == "news"){
		$newsPg = 0;
		if(!empty($_GET["newsNo"])) $newsPg=$_GET["newsNo"];

		$leftN=max($newsPg-1,0);
		$rightN=min($newsPg+1,$maxNews);

		echo " <div id=newsNavCont> <span class=newsNavPart>".
			(($newsPg != 0) ? 
				"<a href='?newsNo=".$leftN."' class=newsNavLink> <<< </a>" :
				" <<< " ).
			"</span> <span class=newsNavPart>" .
			(($newsPg+1 < $maxNews) ? 
				"<a href='?newsNo=".$rightN."' class=newsNavLink> >>> </a>" :
				" >>> ").
			"</span> </div> ";
	} else {
		$pageN = $_GET["page"];
		$reLnk .= "?page=".$pageN;
		$navStr .= " > <a href='$reLnk' id=bheaderLnk>".$pageN."</a>";

		if(!empty($_GET["TID"])){
			$threadN = $_GET["TID"];
			$reLnk .= "&TID=".$threadN;

			$que = "SELECT title FROM ".$pageN."Threads where threadId='$threadN'";
			$res = $connBoards->query($que);

			$theadTitle = "ERROR: THREAD DOESN'T EXIST";
			if(!empty($res) && $res->num_rows > 0){
				while($row = $res->fetch_assoc()){
					$threadTitle = $row["title"];
				}
			}

			$navStr .= " > <a href='$reLnk' id=bheaderLnk>".
						" ThreadNo".$threadN." : ".$threadTitle."</a>";

		}
	} 
	echo $navStr;
?>
