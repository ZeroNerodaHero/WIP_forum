<head>
    <title>REDIRECTING</title>
    <link rel="icon" href="/res/icon/icon_0.png">
    <meta name="description" content="FUNCEL.XYZ is an anonymous text-board.">

    <style>
        body{
            background-color: #ffe0e0;
            display: grid;
            justify-content: center;
            align-content: center;
            margin-bottom: 30%;
            font-size: 24px;
            text-align: center;
            padding: 1% 2%;
        } 
        #redirectEncap{
            border: 2px black solid; 
            margin: 0% 20% 0% 20%;
            padding: 7px 0px;
            background-color: #00000012;
        }
        #displayLink{
            word-break: break-all;
        }
        #link{
            margin: 7px 9px;
        }
        #disclaimer{
            font-size: 0.9rem; 
        }
    </style>
</head>
<?php
    $lnk = $_GET["link"];
    if($lnk != NULL){
        echo "
            <div id=redirectEncap>
                <div id=message>
                    Auto Redirecting to <div id=displayLink><b>$lnk</b></div> in 
                    <span id=timeCnter>15</span> seconds...
                </div>
                <div id=link>
                    <a href='$lnk'>[QUICK REDIRECT]</a>
                </div>
                <div id=disclaimer>
                    Websites and groups linked here does not mean funcel.xyz supports
                    them or what not.
                </div>
            </div>

            <script>
                setInterval(function(){
                    document.getElementById('timeCnter').innerText -=1;
                },1000);
                setTimeout(function(){
                    window.location = '$lnk';
                },15000);
            </script>
        ";
    } else {
        echo"
            <div id=redirectEncap>
                <div id=message>
                    Error
                </div>
            </div>";
    }
?>
