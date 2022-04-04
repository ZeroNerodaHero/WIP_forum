<?php
    //generate the boards 
    //overlay the image on top
    function genImg($board,$TID){
        global $threadContent,$connBoards;
        //connect to mySql -> get all the images
        //in the while loop -> print
        $que = "SELECT * FROM ".$board."_".$TID."_ImgLnks";
        
        echo "<div id=imgRenderCont>";
        echo "<span id=imgRenderLayer>
                <img class=imgRenderImage src='../res/random/imgTest/p_0.jpeg'>
                <img class=imgRenderImage src='../res/random/imgTest/p_1.jpeg'>
                <img class=imgRenderImage src='../res/random/imgTest/p_2.jpeg'>
                <img class=imgRenderImage src='../res/random/imgTest/p_3.jpeg'>
                <img class=imgRenderImage src='../res/random/imgTest/p_4.jpeg'>
              </span>";
        echo "<span id=imgCommentCont>
                <div id=usrCommentCont>
                    <div id=usrCommentTitle>
                        Comment:
                    </div>
                    <div id=usrTextConstraint>
                        <textarea id='usrCommentText' name='usrCommentText'
                         rows='5'></textarea><br>
                    </div>
                    <div id=usrCommentSubmitCont>
                        <button type='submit' id=usrCommentSubmit>
                        Post</button>
                    </div>
                </div>
                <span id=otherCommentCont>
                    <div class=imgComment>
                    good
                    </div>
                </span>
              </span>";
        echo "</div>";

        echo " <canvas id=lowerCanvas class=imgRenderCanvas></canvas>
               <canvas id=highlightCanvas class=imgRenderCanvas></canvas>
               <canvas id=upperCanvas class=imgRenderCanvas></canvas>";
    
        /*
                <img class=imgRenderImage src='../res/emotes/emote_0.png'>
                <img class=imgRenderImage src='../res/emotes/emote_1.png'>
                <img class=imgRenderImage src='../res/emotes/emote_2.png'>

         */
    }
/* mySql table -> imgLink
 * pageNum
 * imgLink
 */
    function genOverlayBot($query){
        //connect to other table where all the rectangles are at
        //generate only the rectangles and then call when usr touches

    }
/* mySql table -> imgComment
 * pId
 * userId
 * position as a string a,b,c,d
 * comment
 * time
 * create table imgRenderTest( postId int NOT NULL AUTO_INCREMENT, userID long, sx float, sy float, ex float, ey float, comment varchar(5000), time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, primary key(postId));
 */
?>
