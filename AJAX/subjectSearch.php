<?php
    session_start();
    
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db('sra');
    
    $search_request = $_GET['keyword'];
    $request = strtoupper($search_request);
    $result = mysql_query("SELECT sid, sname, description FROM subject WHERE sid LIKE '%$request%' OR sname LIKE '%$request%'", $db);
    
    if(mysql_errno() == 0)
    {
        if(mysql_num_rows($result) == 0)
            echo "<div style='width:100%; text-align:center'>No subject found, try another?</div>";
        else{
            $i=1;
            while($row = mysql_fetch_array($result)){
                ?>
                <div class="search-item">
                <?php
                echo "<div class='search-item-title'>".$i.". <a href='details.php?s=".$row['sid']."'>".$row['sid']."</a>: ".ucfirst(strtolower($row["sname"]))."</div>";
                $i++;
                ?>
                <div class="search-item-add"><div style="padding: 20px 0;" id="btnAdd<?=$row['sid']?>" onclick="add('<?=$row['sid']?>')">add</div></div>
                <div class="search-item-detail"><div style="padding: 20px 0;" onclick="window.location.href='details.php?s=<?php echo $row['sid']?>'">detail</div></div>
                <div style="clear: both"></div>
                </div>
                <?php
            }
        }
    }
    else
    {
        echo mysql_error();
        exit;
    }
?>