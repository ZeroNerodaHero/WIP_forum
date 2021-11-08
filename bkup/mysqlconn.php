 <!DOCTYPE html>
<html>
    <head>
    <title>PeePo</title>
    </head>

    <body>

        <h1>Running PeePo</h1>
        
        <br> 
        <?php 
            ini_set("display_errors",1);
            ini_set("display_startup_errors",1);
            error_reporting(E_ALL);

            $servername = "localhost";
            $user = "eve";
            $pass = "PrinzKai177Kai";
            $data = "testDB";
            $conn = mysqli_connect($servername,$user,$pass,$data) 
                or die("can't connect to mySql");
            if($conn -> connect_errno){
                echo "can't connect";
                exit();
            }

            $que = "INSERT INTO connectToMySQL(connectTime) VALUES(CURRENT_TIMESTAMP)";
            /*
            if($conn->query($que)){
                echo "<br>added visit " . $conn->insert_id;
            } else{
                echo "<br>what happened?";
            }
             */
                
            $que = "SELECT * FROM connectToMySQL";
            $res = $conn->query($que);

            if($res->num_rows > 0){
                echo $res->num_rows . "<br>";
                while($row = $res->fetch_assoc()) {
                    echo "connected at ". $row["connectTime"] . "<br>";
                }
            }
        ?>
        <p> 
            sql is wtf
        </p>
    </body>
</html> 
