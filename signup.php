<?php
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    
    function verify($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    //All fields except username is verified in form with JS.
    $uid = $_POST['uid'];
    $uname = verify($_POST['name']);
    $upass = $_POST['password'];
    $ugender = $_POST['gender'];
    $uemail = $_POST['email'];
    $udept = $_POST['department'];
    
    if(!$db)
        die("Cannot link database! ERRROR:".mysql_error());
    else
    {
        mysql_select_db("sra", $db);
        
        //Check if the student is already registered.
        $check_query = mysql_query("SELECT uid FROM users WHERE uid='$uid' LIMIT 1", $db);
        if(mysql_fetch_array($check_query))
        {
            echo 'Student ID: "',$uid,'" has been used. <a href="javascript:history.back(-1);">Back</a>';
            ?>
            <script type="text/javascript">
                setTimeout('window.location.href="index.php"', 1000);
            </script>
            <?php
            exit();
        }
        
        $sql = "INSERT INTO users (uid, uname, hash, sex, dept, email) VALUES('$uid', '$uname', '$upass', '$ugender', '$udept', '$uemail')";
        
        if(mysql_query($sql, $db))
        {
            echo 'Sign up succeed!<br/>Click <a href="index.php">HERE</a> to login if you cannot be redirect automatically.';
            ?>
            <script type="text/javascript">
                setTimeout('window.location.href="index.php"', 1000);
            </script>
            <?php
        }
        else
        {
            echo'Oops, there is something wrong with our database<br/>';
            echo mysql_error()."<br/>";
            echo'Click <a href="javascript:history.back(-1);">HERE</a> to try again.<br/>';
        }
    }
?>