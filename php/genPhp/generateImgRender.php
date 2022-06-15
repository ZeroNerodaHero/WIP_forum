<?php
    //generate the boards 
    //overlay the image on top
    function genImgThread($board,$TID){
        global $threadContent,$connBoards;
        //connect to mySql -> get all the images
        //in the while loop -> print
        $que = "SELECT * FROM ".$board."_".$TID."_imgs";
        
        echo "<div id=imgRenderCont>";
        echo "<div id=imgContLayer>";

        $res = $connBoards->query($que);
        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                echo "<img class='imgLayer' src='".$row["imgLnk"]."'>";
            }
        }
        echo "</div>";

        /*------------------------------------*/

        echo "<div id=imgCommentCont>
                <div id=commentToggleCont>
                    <a href=javascript:toggleAnote()>toggle</a>
                </div>
                <div id=usrCommentCont>
                    <div id=usrCommentTitle>
                        Comment:
                    </div>
                    <div id=usrTextConstraint>
                        <textarea id='usrCommentText' name='usrCommentText'
                         rows='5'></textarea><br>
                    </div>
                    <div id=usrCommentSubmitCont>
                        <button type='submit' id=usrCommentSubmit>Post</button>
                    </div>
                </div>

                <div id=otherCommentCont>
                </div>
              </div>";
        echo "</div>";

        echo " <canvas id=lowerCanvas class=imgRenderCanvas></canvas>
               <canvas id=highlightCanvas class=imgRenderCanvas></canvas>
               <canvas id=upperCanvas class=imgRenderCanvas></canvas>";
        echo "<script> 
                //imgRenderInit('$board',$TID); 

                //for bad img size 
                //behavior happens bc of some reason.
                //only a temp solution
                setTimeout(() => {
                    imgRenderInit('$board',$TID); 
                }, 100);

              </script>";
    }
/* mySql table -> imgLink
 * pageNum
 * imgLink
 */
/* mySql table -> imgComment
 * pId
 * userId
 * position as a string a,b,c,d
 * comment
 * time
 * create table imgRenderTest( postId int NOT NULL AUTO_INCREMENT, userID long, sx float, sy float, ex float, ey float, comment varchar(5000), time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, primary key(postId));
 */
?>
