<style>
    #newUsrPrompt{
        top: 30%;
        left: 50%;
        transform: translate(-50%,-30%);
        width: 30em;
        border: 1px solid black;
        background-color: #b3b3b3e8;
        position: fixed;
        z-index: 5;

        width: 40%;
        
        margin: 5px;
        padding: 5px;
    }

    #nup_topBar{
        font-size: 1.3rem;
        background-color: red;
    

        /* top right bot left */
        padding: 2px 5px 0px 5px;
        margin: -5px -5px 0px -5px ;
    }
    #nup_topBarTitle{
        
    }
    #nup_topBarClose{
        float: right; 
    }
    #nup_hbar{
        margin-top: 0px;
    }
    .nup_subText{
        text-align:center;
    }
    .nup_img{
        text-align:center;
        width:100%;
    } 
    .nup_large{
        text-align:center;
        font-size: 2rem;
        color: #ea82df;
        text-shadow: -1px 1px 0px #000000
    }
         
    #nup_content{
        padding: 3px;
    }
</style>

<script>
    function closeNUP(){
        document.getElementById("newUsrPrompt").style.display="none";
    }
</script>

<span id=newUsrPrompt>
    <div id = nup_topBar>
        <span id= nup_topBarTitle>
            <b>WELCOME NEW USER(or OLD USER)!!!</b>
        </span>

        <span id= nup_topBarClose>
            <a href="javascript:closeNUP()">X</a>
        </span>
    </div>
    <hr id=nup_hbar>

    <div id = nup_content>
        First off, welcome to FUNCEL.XYZ. I HOPE YOU ENJOY YOUR TIME HERE. 
        <br> <br> 
        Before I let you off, I noticed that this site is really badly designed.
        Many people who tried to use this site said it looked autistic and retarded.
        So I made this prompt in hopes that you can navigate this site a little bit
        easier. 
        <br> <br>
        On your right, just below this text is the board where all the content
        is at. 
        <br> <br>
        On your top-left, there is a navigation tab that allows you to switch to a 
        different board. 
        <br> <br>
        But before you leave, here are some other features that may be helpful navigating
        the website:
        <ol>
            <li>If you click the header element(The thing that says WELCOME TO FUNCEL.XYZ)
                you can return back to the board if you are on a thread or not the first page.
                <br>
                <br><img class=nup_img src="../res/funcelNUP/Header.png"><br>
                <i class=nup_subText>It will light up pink when you can do it.</i>
            </li>
            <li>If you scroll down a little bit, you can see a pinkbar. That pinkbar also serves 
                as a navigation device.
                <br><img class=nup_img src="../res/funcelNUP/boardHeader.png"><br>
                <i class=nup_subText>
                    Here is displays the frontPage, the board meta, and a thread</i>
            </li>
            <li>This feature often blends in with the surrounding, but there is a small little
                bar that lets you go from the top of the board and down to the bottom.
                <br><img class=nup_img src="../res/funcelNUP/scroll.png"><br>
                <i class=nup_subText>SCROLL BAR</i>
            </li>
            <li>Some posts have ##. ## is meant to be an @user. If you click on it, it redirects
                you to the post that it is being referenced. If you want to reference a post,
                click the PID(post id).
                <br><img class=nup_img src="../res/funcelNUP/postRef.png"><br>
                <i class=nup_subText>pic rel</i>
            </li>
        </ol>
        <div class=nup_large>Have fun. </div>
    </div>
</span>
