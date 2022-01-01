 <!DOCTYPE html>
<html>
    <?php 
        include_once("reuse.php");
    ?>
	<?php
		$pword = $_POST['pword'];
		$descript = $_POST['descript'];
		$board = $_GET['board'];

		if(!empty($pword) && $pword = $admin_ppassword){
			$que = "UPDATE boards
					SET descript = '".$descript."'
					WHERE boardName='".$board."'";
			echo $que . "<br>";	
			myQuery($conn,$que);
		}
	?>


	<script>
		setTimeout(function(){
			location="deleteStuff.php";
			}, 500); 
	</script>
</html> 
