<!DOCTYPE html>
<html>
    <?php
        include_once("reuse.php");
    ?>

    <body>
        <h1>Create Board</h1>
        
        <ul>
            <li> Don't add / to the thing. No need. </li>
            <li> Use the pinned post as a way to post rules and stuff. yea </li>
        </ul> <br>

        <form action="updateBoard.php" method="post">
            Board Name: <input type="text" name="boardName" class="tit"> <br>
            Description: <br>
                <textarea name="descript" rows="2" cols="100" ></textarea>
            <br>
            Post Title: <input type="text" name="pinTit" class="tit"> <br>
            Pinned Post: <br>
                <textarea name="pinned" rows="6" cols="100" ></textarea>
            <br>
            Password: <input type="text" name="password">
            <input type="submit" value="Post">    
        </form>

        <br> 
    </body>
</html> 
