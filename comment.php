<?php
    session_start();

    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db('sra');
    
    $sid = $_POST['sid'];
    $content = $_POST['comment'];
    $rate = $_POST['rate'];
    $uid = $_SESSION['uid'];
    
    date_default_timezone_set("Asia/Hong_Kong");
    $time = date("Y-m-d H:i:s");
    
    $isCRTwice = false;
    $isSQLError = false;
    function sql_error()
    {
        echo mysql_error()."<br/>";
        echo'We are sorry to tell you there is something wrong with database<br/>';
        echo'Click <a href="javascript:history.back(-1);">HERE</a> to try again';
    }
    
    function duplicate()
    {
        echo "You cannot comment or rate same subject twice.<br/>\n";
        echo "This page will be redirect to home after 3 second.<br/>\n";
        echo "<a href='home.html'>Click here if you cannot be auto redirect to homepage.</a>";
        echo "<script type='text/javascript'>";
        echo "setTimeout('window.location.href=\"home.html\"', 3000);";
        echo "</script>";
    }
    
    if(!$db)
        die("Cannot link to database! ERROR: ".mysql_error());
    else
    {
        mysql_select_db("sra", $db);
        
        //Comment
        $check_query = mysql_query("SELECT sid, uid FROM comment WHERE sid='$sid' AND uid='$uid' LIMIT 1", $db);
        if (!mysql_fetch_array($check_query))
        {
            $sql = "INSERT INTO comment (sid, uid, content, time) VALUES('$sid', '$uid', '$content', '$time')";
            if(!mysql_query($sql, $db))
                $isSQLError = true;
        }
        else
            $isCRTwice = true;
            
        //Rate
        $check_query = mysql_query("SELECT sid, uid FROM rate WHERE sid='$sid' AND uid='$uid' LIMIT 1", $db);
        if (!mysql_fetch_array($check_query))
        {
            $sql = "INSERT INTO rate (sid, uid, rate) VALUES('$sid', '$uid', '$rate')";
            if(mysql_query($sql, $db))
            {
                header("Location: details.php?s=".$sid);
                exit;
            }
            else
                $isSQLError = true;
        }
        else
            $isCRTwice = true;
    }
    
    if($isSQLError)
        sql_error();
    if($isCRTwice)
        duplicate();
    

?>