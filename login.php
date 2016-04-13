<?php
    session_start();
    
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db("sra",$db);

    $uid = $_POST['uid'];
    $upass = $_POST['password'];
    
    $login_result = mysql_query("SELECT * FROM users WHERE uid='$uid' AND hash ='$upass'", $db);
    
    if(!$login_result || !$db)
        die("Cannot link database! ERROR:".mysql_error());
    else
    {
        if($row = mysql_fetch_array($login_result))
        {
            //uid will be used as user identification data in other webpages.
            $_SESSION['uid'] = $row['uid'];
            $_SESSION['uname'] = $row['uname'];
            if($_COOKIE['uid'] != $row['uid'])
                unset($_SESSION['list']);
            setcookie("uid", $row['uid'], time() + 3600);
            echo "Welcome, ".$row['uname']."!<br>";
            echo "This page will be redirect to home after 1 second.<br>";
            echo "<a href='home.php'>Click here if you cannot be auto redirect to homepage.</a>";
            ?>
            <script type="text/javascript">
                setTimeout('window.location.href="home.php"', 1000);
            </script>
            <?php
        }
        else
        {
            echo mysql_error()."</br>";
            echo'Failed to login. Clike <a href="javascript:history.back(-1)">HERE</a> to try again';
            exit;
        }
    }

?>