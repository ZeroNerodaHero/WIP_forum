 <!DOCTYPE html>
<html>
    <head>
    <title>PeePo</title>
    </head>

    <body>

        <h1>Running PeePo</h1>
        
        <form action="frontpage.php" method="post">
            Title <input type="text" name="msg_post[]">
            Message <input type="text" name="msg_post[]">
            <input type="submit" value="->">
        </form>
        <br> 
        <?php 
            $msg_post = $_POST["msg_post"];
            echo $msg_post[0]. "<br>" . $msg_post[1];

            $servername = "localhost";
            $user = "eve";
            $pass = "PrinzKai177Kai";
            $conn = mysqli_connect($servername,$user,$pass);
            if($conn) echo "connected to mysql";
        ?>
    </body>
</html> 
