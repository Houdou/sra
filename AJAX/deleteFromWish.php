<?php
    session_start();
    
    if(!isset($_SESSION['list']))
        $_SESSION['list'] = array();
    $list = $_SESSION['list'];
    
    $newSubj = $_POST['s'];
    $isInList = false;
    
    sort($list);
    
    $index = -1;
    foreach($list as $subject)
    {
        $index++;
        if($subject == $newSubj)
        {
            $isInList = true;
            break;
        }
    }
            
    if($isInList)
    {
        array_splice($list, $index, 1);
        echo "deleted";
    }
    else
    {
        echo "Not in list";
    }
    
    $_SESSION['list'] = $list;
?>