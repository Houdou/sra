<?php
    session_start();
    
    if(!isset($_SESSION['list']))
        $_SESSION['list'] = array();
    $list = $_SESSION['list'];
    
    $newSubj = $_POST['s'];
    $isInList = false;
    foreach($list as $subject)
        if($subject == $newSubj)
            $isInList = true;
            
    if(!$isInList)
    {
        array_push($list, $newSubj);
        echo "Added";
    }
    else
    {
        echo "In list";
    }
    
    sort($list);//Make it ordered.
    $_SESSION['list'] = $list;
?>