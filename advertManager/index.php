<!DOCTYPE html>
<html>
    <head>
        <title>Advert Manager</title>
        <link rel="stylesheet" href="advertManager.css">
        <link rel="icon" href="../../res/icon/icon_0.png">
        <script type="text/javascript" src="advertJS.js"></script>
    </head>

    <body>
        <div id=advertLoginFlexCont>
            <div id=advertLoginCont>
                <h1 id=loginHeader>
                    Advert Manager
                </h1>
                <div>
                    Please Login or Sign Up
                </div><hr>
            
                <div>
                    <form>
                        <div>
                            Username: 
                            <input id=username name=username><br><br>
                            Password: 
                            <input type='password' id=passwd name=passwd><br><br>
                            <button type='button' onclick='updateBody(0)'>Sign In</button>
                        </div>
                    </form>
                    <br>
                </div>
            </div>
        </div>
        <div id=externalLink>
            <a href="signUp.html">Sign Up</a> | 
            <a href="">Info</a> | 
            <a href="">Terms Of Service</a> | 
            <a href="">Back to Funcel</a>
        </div>
    </body>
</html>
