<?php
    session_start();
    if(isset($_SESSION['uid']))
    {
        header("Location: /home.php");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="util/md5.js"></script>
    <script type="text/javascript" src="util/jquery.min.js"></script>
    <script type="text/javascript">
        function check(){
            var uname = document.getElementById("uid").value;
            var pwd = document.getElementById("password");
            
            //check if username & password is blank or not. 
            if(/^ *$/.test(uname))
                return false;
            if(/^ *$/.test(pwd.value))
                return false;
            
            if(pwd.value.length <= 32)
                pwd.value = md5(pwd.value);
                return true;
        }
        
        function login(){
            if(check())
            {
                document.getElementById("login").className = "sButton-disabled";
                loginForm.submit();
            }
        }
        
        //Assign the Enter key to login (Very convenient!)
        $(document).ready(function(){
            $("#password").on("keydown",function(event){
               if(event.keyCode==13)
                   login();
            });
        })
    </script>
</head>

<body>
<div class="container" style="background-color: whitesmoke">
   <div class="header">
        <!--<h1 style="padding: 0px;">SR Assistant</h1>-->
        <img style="padding: 0px;margin-up:0px;padding-up:0px;" src="img/title.png"></img>
    </div>
    <div class="content" id="indexC">
        <div class="intro">
            <h2>Introduction</h2><br>
            <ul>
                <li>During the subject registration period, PolyU students always worry about a sequence of problems: </li>
            <ul><br>
            <li>Which subjects are popular?</li>
            <li>Is this subject easy to get a good grade?</li>
            <li>I want to take more than one subject. Will it cause any time crash?</li>
            <li>Do my friends also prefer this subject?</li>
            <br></ul>
            <li>The subject registration assistant aims to solve these problems with one website and provide PolyU students with a better subject registration experience. </li>
            </ul>
        </div>
        <div class="sign">
            <div class = "logo">
                <img src="img/coffee.png"/>
            </div>
            <form name="loginForm" action="login.php" method="POST">
                <div class = "login">
                    <div class="field">
                        <div class="input">StudentID: </div>
                        <input type="text" name="uid" id="uid" required pattern="^\d{8}[A-z]$"/>
                    </div>
                    <div class="field">
                            <div class="input">Password: </div>
                        <input type="password" name="password" id="password"/>
                    </div>
                    <div class="buttongroup">
                        <a class="sButton" id="login" onclick="login()">Log In</a>
                        <a class="sButton" id="signUp" href='signupForm.php'>Sign Up</a>
                    </div>
                </div>
            </form>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div class="footer">Credits:&nbsp&nbsp&nbspJoe Wang, Travis Peng & Yuki Chen</div>
</div>
</body>
</html>
