<?php
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db('sra');
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="util/md5.js"></script>
        <script type="text/javascript" src="util/jquery.min.js"></script>
        <script type="text/javascript">
            function check(){
                var uname = document.getElementById("uname");
                var unameAlert = document.getElementById("nameAlert");
                var email = document.getElementById("email");
                var emailAlert = document.getElementById("emailAlert");
                var pwd1 = document.getElementById("password");
                var pwd2 = document.getElementById("password2");
                var pwdAlert = document.getElementById("pwdAlert");
                
                //check if username is blank.
                if(/^ *$/.test(uname.value))
                {
                    unameAlert.innerHTML = "Name cannot be blank";
                    unameAlert.style.color = "#F15C52";
                    unameAlert.style.verticalAlign = "bottom";
                    return false;
                }
                else
                {
                    //reset the label
                    unameAlert.innerHTML = "Name:";
                    unameAlert.style.color = "";
                    unameAlert.style.verticalAlign = "";
                }
                
                //check if email is blank. 
                if(/^ *$/.test(email.value))
                {
                    emailAlert.innerHTML = "Email cannot be blank";
                    emailAlert.style.color = "#F15C52";
                    emailAlert.style.verticalAlign = "bottom";
                    return false;
                }
                else
                {
                    //reset the label
                    emailAlert.innerHTML = "Email:";
                    emailAlert.style.color = "";
                    emailAlert.style.verticalAlign = "";
                }
                
                //Avoid hashing blank password
                if(!/^ *$/.test(pwd1.value))
                {
                    //check if two passwords are the same
                    if(pwd1.value == pwd2.value)
                    {
                        //reset the label
                        pwdAlert.innerHTML = "Confirm password:";
                        pwdAlert.style.color = "";
                        pwdAlert.style.verticalAlign = "";
                        
                        //Prevent the password from being hash twice.
                        if(pwd1.value.length < 32)
                        {
                            //MD5 encrypted security.
                            pwd1.value = md5(pwd1.value);
                            pwd2.value = md5(pwd2.value);
                        }
                    }
                    else
                    {
                        pwdAlert.innerHTML = "Two password do not match";
                        pwdAlert.style.color = "#F15C52";
                        pwdAlert.style.verticalAlign = "bottom";
                        return false;
                    }
                }
                else
                {
                    pwdAlert.innerHTML = "Password cannot be empty.";
                    pwdAlert.style.color = "#F15C52";
                    pwdAlert.style.verticalAlign = "bottom";
                    return false;
                }
            }
        </script>
    </head>
    <body style="background-color: #F8F8F8;">
        <div class="container">
            <div class="header-center" style="margin-top:5%;">
                <img src="img/signUp.png"></img>
            </div>
            <div class="register-form">
                <form action="signup.php" method="POST">
                    <div class="field-group">
                        <div class="field">
                            <label for="uid">
                                <div>Student ID:</div>
                            </label>
                            <input type="text" id="uid" name="uid" required pattern="^\d{8}[A-z]$" />
                        </div>
                        <div class="field">
                            <label id="nameAlert" for="name">
                                <div>Name: </div>
                            </label>
                            <input type="text" id="uname" name="name" required/>
                        </div>
                        <div class="field">
                            <label for="passowrd">
                                <div>Password: </div>
                            </label>
                            <input type="password" id="password" name="password" required/>
                        </div>
                        <div class="field">
                            <label id="pwdAlert" for="passowrd2">
                                <div>Confirm Password: </div>
                            </label>
                            <input type="password" id="password2" name="password2" required/>
                        </div>
                        <div class="field">
                            <label for="type">
                                <div>Gender: </div>
                            </label>
                            <div class="radio-group">
                                <div class="radio">
                                    <input type="radio" name="gender" value="M"/>
                                    Male</div>
                                <div class="radio">
                                    <input type="radio" name="gender" value="F"/>
                                    Female</div>
                            </div>
                        </div>
                        <div class="field">
                            <label id="emailAlert" for="remarks">
                                <div>Email: </div>
                            </label>
                            <input type="email" id="email" name="email" required/>
                        </div>
                        <div class="field">
                            <label for="department">
                                <div>Department: </div>
                            </label>
                            <select name="department" required>
                                <?php
                                    //Get the list of departments.
                                    $dept_query = mysql_query("SELECT code from dept", $db);
                                    while($row = mysql_fetch_array($dept_query))
                                    {
                                        echo "<option value='".$row['code']."'>".$row['code']."</option>\n";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br><br>
                    <input class="lButton" id="btnRegis" style="margin:0 160px;" type="submit" value="Register" onclick="return check()"/>
                </form>
            </div>
        </div>
    </body>
</html>