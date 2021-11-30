 <!DOCTYPE html>
<html>
    <?php 
        include_once("reuse.php");
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
