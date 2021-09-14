 <!DOCTYPE html>
<html>
    <head>
    <title>Posting News</title>

    <style>
        .tit{
            width: 40%;
        }
        
    </style>
    </head>

    <body>
        <h1>Ur Posting News</h1>
        
        <form action="adminPostNews.php" method="post">
            Title: <input type="text" name="title" class="tit"> <br>
            Message: <br>
                <textarea name="content" rows="6" cols="100" ></textarea>
            <br>
            Password: <input type="text" name="password">
            <input type="submit" value="Post">    
        </form>

        <br> 
    <?php 
        include_once("login.php");
        ini_set("display_errors",1);
        ini_set("display_startup_errors",1);
        error_reporting(E_ALL);


        $title = $_POST["title"];
        $msg = $_POST["content"];
        $ppassword = $_POST["password"];

        if($title && $msg && $ppassword && $ppassword==$admin_ppassword){
            $que = "INSERT INTO frontNews(news_title,post_time,news_content) VALUES('$title',CURRENT_TIMESTAMP(),'$msg')";
            //echo $que . "<br><br>";
            if($conn->query($que)){
                    echo "successfully added " . $conn->insert_id;
                } else{
                    echo "failed to add";
                }
            }
        ?>
    </body>
</html> 
