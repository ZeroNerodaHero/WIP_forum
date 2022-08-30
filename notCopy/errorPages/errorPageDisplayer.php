<head>
    <title>ERROR</title>
    <link rel="icon" href="/res/icon/icon_0.png">
    <meta name="description" content="FUNCEL.XYZ is an anonymous text-board.">

    <style>
        body{
            background-color:#ff0000;
            color: red;
        }
        a{
            color: #ff8080;
        }
        #allContent{
            text-align:center;
            margin: 6% 5% 0% 5%;
            background-color:#413838;
            padding: 7px 0px 13px 0px;
            border: 2px black solid;
        }
        #topMsg{
            font-size: 3rem;
        }
        #errorImg{
            width: 95%;
            border-top: 4px white solid;
            border-bottom: 4px white solid;
        }
        #botMsg{
            font-size: 2rem;
        }
    </style>
</head>

<?php

function generateErrorPage($topText,$botText,$photo="/res/errorPages/errorPic.gif"){
    echo "
        <div id=allContent>
            <div id=topMsg>
                <b>$topText</b>
            </div>
            <a href='/php'>
                <img src='$photo' id=errorImg>
            </a>
            <div id=botMsg>
                $botText
                <a href='\'> [ RETURN ]</a>
            </div>
        </div>
    ";        
}

?>
