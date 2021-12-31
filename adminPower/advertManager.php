 <!DOCTYPE html>
<html>
    <?php 
        include_once("reuse.php");
    ?>
	<?php
		$que = "SELECT * FROM peepoAds";
		$res = $conn->query($que);

		echo "<table>";       
		echo "<tr><th>ID</th><th>linkToImg</th><th>img</th><th>linkToSite</th>
			<th>totalLoads</th><th>totalClicks</th><th>maxPoints</th>
			<th>boardLimited</th><th>dateAdded</th></tr>";

		if($res->num_rows > 0){
			while($row = $res->fetch_assoc()){
				$id = $row["id"];
				$linkToImg = $row[ "linkToImg" ];
				$linkToSite = $row[ "linkToSite" ];
				$totalLoads= $row[ "totalLoads" ];
				$totalClicks = $row[ "totalClicks" ];
				$maxPoints = $row[ "maxPoints" ];
				$boardLimited= $row[ "boardLimited" ];
				$dateAdded = $row[ "dateAdded" ];

				echo "<form action='updateAdvert.php?op=0&id=".$id.
						"' method='post' id=editForm'$id'></form>";
				
				echo "<tr>
				<th>$id</th>
				<th><input type='text' name='linkToImg' 
					value='$linkToImg' form=editForm'$id' size=10></th>
				<th><img src='$linkToImg'></th>
				<th><input type='text' name='linkToSite' 
					value='$linkToSite' form=editForm'$id' size=10></th>
				<th>$totalLoads</th>
				<th>$totalClicks</th>
				<th><input type='text' name='maxPoints' 
					value='$maxPoints' form=editForm'$id' size=5></th>
				<th><input type='text' name='boardLimited' 
					value='$boardLimited' form=editForm'$id' size=10></th>
				<th>$dateAdded</th>
				<th>
					<input type='text' name='pword' size=4 form=editForm'$id'><br>
					<input type='submit' name='submit' 
				     	value='Update' form=editForm'$id' ></th>
				<th>
					<form action='updateAdvert.php?op=1&post=$id' method='post'>
						<input type='text' name='pword' size=4><br>
						<input type='submit' value='Delete'> 
					</form>
				</th>
				</tr>";
			}
		}
		echo "</table>";
	?>
	<br><hr><br>
	<div id=form>  
		<form action="updateAdvert.php?op=2" method='post'>
			linkToImg: <input type='text' name='linkToImg'><br>
			linkToSite: <input type='text' name='linkToSite'><br>
			maxPoints: <input type='text' name='maxPoints'><br>
			boardLimited: <input type='text' name='boardLimited'><br>
			Password<input type='text' name='pword'><br>
			<input type='submit' value='->'> 
		</form>
	</div>
</html> 
