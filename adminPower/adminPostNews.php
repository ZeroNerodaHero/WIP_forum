 <!DOCTYPE html>
<html>
    <?php 
        include_once("reuse.php");
    ?>
    <body>
        <h1>Ur Posting News</h1>
        
        <form action="updateNews.php" method="post">
            Title: <input type="text" name="title" class="tit"> <br>
            Message: <br>
                <textarea name="content" rows="6" cols="100" ></textarea>
            <br>
            Password: <input type="text" name="password">
            <input type="submit" value="Post">    
        </form>

        <br> 
    </body>
</html> 
