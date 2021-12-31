 <!DOCTYPE html>
<html>
    <?php 
        include_once("reuse.php");
    ?>
	<?php
		$pword= $_POST[ "pword" ];
		$linkToImg = $_POST[ "linkToImg" ];
		$linkToSite = $_POST[ "linkToSite" ];
		$maxPoints = $_POST[ "maxPoints" ];
		$boardLimited= $_POST[ "boardLimited" ];

		if(!empty($pword) || $pword == $admin_ppassword){
			if($_GET["op"] == 0){
				$id = $_GET["id"];
				updateAdvert($id,$linkToImg,$linkToSite,$maxPoints,$boardLimited);
			} else if($_GET["op"] == 1){
				deleteAdvert($_GET["post"]);
			} else{
				$res = addAdvert($linkToImg,$linkToSite,$maxPoints,$boardLimited);	
				if($res){
					echo "inserted<br>";
				} else{
					echo "failed inserted<br>";
				}
			}
		} else{
			echo "NO PASSWORD OR WRONG PASSWORD<br>";
		}

		function updateAdvert($id,$linkToImg,$linkToSite,$maxPoints,$boardLimited){
			if( empty($linkToImg) || empty($linkToSite) || empty($maxPoints) ){
				return false;
			}
			global $conn;
			$que = "UPDATE peepoAds 
					SET linkToImg = '$linkToImg',
						linkToSite = '$linkToSite',
						maxPoints = $maxPoints,
						boardLimited = ".($boardLimited == NULL?"NULL":$boardLimited)."
					WHERE id=$id";
			echo $que;
			myQuery($conn,$que);
		}

		function deleteAdvert($id){
			global $conn;
			$que = "DELETE FROM peepoAds where id=".$id;
			echo $que;
			myQuery($conn,$que);
		}

		function addAdvert($linkToImg,$linkToSite,$maxPoints,$boardLimited){
			global $conn;
			if( empty($linkToImg) || empty($linkToSite) || empty($maxPoints) ){
				return false;
			}

			$que = "INSERT INTO peepoAds(linkToImg,linkToSite,maxPoints,boardLimited)
					VALUE('$linkToImg','$linkToSite','$maxPoints','$boardLimited')";
			myQuery($conn,$que);
			echo $que . "<br>";
			return true;
		}
	?>
</html> 
