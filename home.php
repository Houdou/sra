<?php
    session_start();
    
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db('sra');
    
    if (!isset($_SESSION['uid'])){
         header('Location:index.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="util/jquery.min.js"></script>
        <script type="text/javascript" src="util/homeJQ.js"></script>
        <script type="text/javascript">
            function search()
            {
                var keyword = document.getElementById('keyword').value;
                //Prevent searching empty keyword.
                if(/^ *$/.test(keyword))
                    return false;
                collapseForm();
                
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function(){
                    if(xhr.status == 200)
                    {
                        var resultp = getNode('resultP', "div");
                        switch(xhr.readyState)
                        {
                            case 0:
                                break;
                            case 1:
                                resultp.innerHTML = "DB Connected.";
                                break;
                            case 2:
                                resultp.innerHTML = "Request sent.";
                                break;
                            case 3:
                                resultp.innerHTML = "Processing.";
                                break;
                            case 4:
                                resultp.innerHTML = xhr.responseText;
                                //Add hover display effect of Add and Details buttons
                                //This function is in util/homeJQ.js
                                addButton();
                                break;
                            default:
                                break;
                        }
                    }
                    if(xhr.status == 404)
                    {
                        document.body.appendChild(modifyText('resultP', "p", "Network Error. Try again later."));
                    }
                }
                xhr.open("GET","AJAX/subjectSearch.php?keyword="+keyword, true);
                xhr.send();
                return false;
            }
            
            function add(code)
        	{
        	    var xhr = new XMLHttpRequest();
        	    xhr.onreadystatechange = function()
        	    {
        	        if(xhr.readyState == 4 && xhr.status == 200)
        	        {
        	            if(xhr.responseText == "Added")
        	                alert("Success!");
        	            else if(xhr.responseText == "In list")
        	                alert("Already in list");
        	        }
        	    }
        	    xhr.open("POST", "AJAX/addToWish.php", true);
        	    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        	    xhr.send("s="+code);
        	}
            
            function getNode(id, tag)
            {
                var node = document.getElementById(id);
                if(!node)
                {
                    node = document.createElement(tag);
                    document.getElementById('content').appendChild(node);
                }
                node.id = id;
                return node;
            }
        </script>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <!--<h1 style="padding: 0px;">SR Assistant</h1>-->
                <img style="padding: 0px;margin-up:0px;padding-up:0px;" src="img/title.png"></img>
                <div class="user-info" style="height: 50px; padding:5px;float: right;font-family:Baskerville, Georgia, Cambria, Times, Times New Roman, serif;">
                    <div style="line-height: 32px;">Welcome, <?=$_SESSION['uname']?></div>
                    <a class="sButton" href="logout.php">Log out</a>
                </div>
            </div>
            <div class="menu-warpper">
                <div class="menu">
                    <div class="menu-item" style="padding:0; margin:0; height: 40px;">
                    </div>
                    <a class="menu-item" href="home.php">Home</a>
                    <a class="menu-item" href="wishingList.php">Wishing List</a>
                </div>
            </div>
            <div class="content" id="content">
                <div class="search">
                    <div class="search-form">
                        <input class="searchbox" type="text" id="keyword" name="keyword"
                        placeholder="Enter subject name or code" autocomplete="off" disableautocomplete />
                        <input class="searchbutton" type="submit" value="Search" onclick="return search();"/>
                        <div style="clear: both"></div>
                        <div hidden="hidden"><form><input type="text" name="sid"/></form></div>
                        <?php
                            echo "<div class='hiRateSB recom' id='hiRateSB'>\n";
                                echo "<h4 class='recom-head'>Rating ranking</h4>\n";
                                echo "<div class='recom-content' style='text-align:right'>\n";
                                $hirsb_query = mysql_query("select sid, format(avg(rate), 1) as avg from rate group by sid order by avg(rate) DESC LIMIT 3", $db);
                                while($sb = mysql_fetch_array($hirsb_query))
                                {
                                    echo "[<a href='details.php?s=".$sb['sid']."'>".$sb['sid']."</a>]"."<span style='vertical-align:bottom;'class='stars rating".round(((float)$sb['avg'])/0.5)*5 ."'><em></em></span>"."<br/>\n";
                                }
                                echo "</div>\n";
                            echo "</div>\n";
                        ?>
                        <?php
                            echo "<div class='newComm recom' id='newComm'>\n";
                                echo "<h4 class='recom-head'>Latest comments</h4>\n";
                                echo "<div class='recom-content'>\n";
                                $comm_query = mysql_query("Select * from comment order by time DESC LIMIT 3", $db);
                                while($sb = mysql_fetch_array($comm_query))
                                {
                                    //Trim the comment if it is too long
                                    if(strlen($sb['content']) > 50)
                                        $comment = substr($sb['content'], 0, 48)."...";
                                    else
                                        $comment = $sb['content'];
                                    echo "[<a href='details.php?s=".$sb['sid']."'>".$sb['sid']."</a>] ".$comment."<br/>\n";
                                }
                                echo "</div>\n";
                            echo "</div>\n";
                        ?>
                    </div>
                </div>
            </div>
            <div class="footer">Credits:&nbsp&nbsp&nbspJoe Wang, Travis Peng & Yuki Chen</div>
        </div>
    </body>
</html>