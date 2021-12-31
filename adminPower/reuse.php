 <!DOCTYPE html>
<html>
	
	<?php 
       	include_once("login.php");
    	echo '
            <head>
                <title>Admin</title>
                <link rel="stylesheet" href="../css/adminstyle.css">
                <link rel="icon" href="../../res/icon/icon_0.png">
            </head>';
        
        echo '
            <div class=topTools>
                <a href="adminPostNews.php" class=alink>Post News</a> | 
                <a href="createBoard.php" class=alink>Create Board</a> | 
                <a href="deleteStuff.php" class=alink>Delete Stuff</a> | 
                <a href="banList.php" class=alink>Ban List</a> </li> | 
                <a href="wordBan.php" class=alink>Banned Words List</a> |
                <a href="advertManager.php" class=alink>advertManager</a>
            </div>';
    ?>
</html> 
