<?php
    $sid = $_GET['s'];
    
    session_start();

    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";

    $db = mysql_connect($servername, $username, $password);
    mysql_select_db('sra');
    
    $count = mysql_query("SELECT rate, COUNT(rate) as count FROM rate where sid='$sid' group by rate", $db);
    
    
    if(!$count || !$db)
    die("Cannot link database! ERROR:".mysql_error());
    else
    {
        $rates = array();
        while($row = mysql_fetch_array($count, MYSQL_ASSOC))
        {
            array_push($rates, $row);
        }
        echo json_encode($rates);
    }
?>