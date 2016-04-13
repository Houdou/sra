<?php
    require_once('util/func.php');
    session_start();
    
    if(!isset($_SESSION['uid']))
        header('Location:index.php');
        
    if(!isset($_GET['s']))
        header('Location:home.php');
    $sid = $_GET["s"];
    
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db('sra');
    
    $result = mysql_query("Select * from subject where sid="."'".$sid."'",$db);
    $row = mysql_fetch_array($result);
    if(!$row)
    {
        $row['sid'] = "Course does not exist. check subject code again";
        $row['description'] = "<a href='home.html'>Back to search page.</a>";
    }
    else
    {
        $timeslot_query = mysql_query("select * from schedule where sid='".$row['sid']."'", $db);
        //echo mysql_error();
        $website_query = mysql_query("select web from dept where code = '".$row['dept']."'", $db);
        
        $sid = $row['sid'];
        $sname = $row['sname'];
        
        if(preg_match("/^ *$/",$row['description']))
        {
            if($deptWeb = mysql_fetch_array($website_query))
                $row['description'] = '<br/>Currently, we do not have details of this subject.<br/>
                    You may refer to '.(($deptWeb['web'] != "")?('<a href="'.$deptWeb['web'].'">department syllabus website</a>'):('department syllabus website')).' for description.';
            else
                $row['description'] = "Details available later";
        }
        else
        {
            $row['description'] = nl2br($row['description']);
        }
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="util/jquery.min.js"></script>
        <script type="text/javascript" src="util/star-rating.js"></script>
        <script type="text/javascript">
        	function addSubject(subjectCode)
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
        	    xhr.send("s="+subjectCode);
        	}
        	
        	function check()
        	{
        	    RegExBlank = /^ *$/;
        	    if(RegExBlank.test(crForm.uid.value))
        	    {
        	        alert("You havn\'t login.");
        	        window.location.href="index.php";
        	        return false;
        	    }
        	    else if(RegExBlank.test(crForm.comment.value))
        	    {
        	        alert("Blank Comment.");
        	        return false;
        	    }
        	    else if(RegExBlank.test(crForm.rate.value))
        	    {
        	        alert("You havn\'t rate this subject.");
        	        return false;
        	    }
        	    else if(RegExBlank.test(crForm.sid.value))
        	    {
        	        alert("Invalid Access.");
        	        return false;
        	    }
        	    else return true;
        	}
        	
        	function rate()
        	{
        	    var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function()
                {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        var rates = JSON.parse(xmlhttp.responseText);
                        var totalRatesNumber = 0;
                        var totalRate = 0;
                        
                        for(var i = 0; i < rates.length; i++)
                        {
                            totalRatesNumber += parseInt(rates[i].count);
                            totalRate += parseInt(rates[i].rate) * parseInt(rates[i].count);
                        }
                        totalRate = totalRate / totalRatesNumber;
                        totalRateStr = parseInt(Math.round(totalRate/0.5))*5;
                        
                        document.getElementById("rated-people").innerHTML = totalRatesNumber;
                        document.getElementById("total-rate").innerHTML = (totalRatesNumber != 0)?totalRate.toFixed(1):"0.0";
                        document.getElementById("total-stars").className = "stars rating" + ((totalRatesNumber != 0)?totalRateStr:"0");
                        
                        if(rates.length > 0)
                        {
                            var j = 0;
                            for(var i = 1; i <= 5; i++)
                                document.getElementById("rp"+i).innerHTML = (rates[j].rate == i)?((rates[j++].count / totalRatesNumber * 100).toFixed(2) + "%"):"0%";
                        }
                    }
                };
                xmlhttp.open("GET","AJAX/ratings.php?s=" + <?="'".$sid."'"?> ,true);
                xmlhttp.send();
        	}
        	
        	$(document).ready(function(){
        	   rate();
        	});
        	
        	var link = document.URL;
        	console.log(link);
            function share() {
                document.getElementById("shareLink").innerHTML = 'Share this page by copying the URL:</p>'
                                                                + '<input class="field" style="padding: 5px;" id="urlShare" type="text" value="' + link +'"/>'
                                                                + '<button class="sButton" type="button" onclick="copyLink(link)">Copy</button>';
            }
            
            function copyLink(obj)
            {
                document.getElementById('urlShare').focus();
                document.getElementById('urlShare').select();
                document.execCommand('Copy', false, null);
                alert("The page link has been copied to the clipboard.");
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
            <div class="content">
                <div class="subject-info">
                    <h1>
                    <span class="subjectCode"><?=$sid;?></span><br>
                    <span class="subjectName"><?=$sname;?></span>
                    </h1>
                    <div class="subject-article">
                    <p>
                        <?php
                        echo $row['description']."<br/>";
                        echo "<br/>";
                        while($timeslot = mysql_fetch_array($timeslot_query))
                        {
                            $time = convertTimeslot($timeslot['timeslot']);
                            echo "<li>". $timeslot['type'] . ": " . $time[0] . ":  ". $time[1] . "-" .$time[2] . " at " .$timeslot['venue'] . "</li>\n";
                        }
                        ?>
                        </p>
                    </div>
                </div>
                <div class="rating">
                    <p>Overall Rating</p>
                    <div class="user-rating">
                       <span class="stars rating0" id="total-stars"><em></em></span>
                        <div></div>
                        <span class="rating-num" id="total-rate">-</span>
                        <div class="rating-people"><span id="rated-people">-</span> people rated</div>
                            <div>
                                <span class="stars rating50"><em></em></span>
                                <span class="power" style="width:20px" id="rp5">-%</span>
                            </div>
                            <div>
                                <span class="stars rating40"><em></em></span>
                                <span class="power" style="width:20px" id="rp4">-%</span>
                            </div>
                            <div>
                                <span class="stars rating30"><em></em></span>
                                <span class="power" style="width:20px" id="rp3">-%</span>
                            </div>
                            <div>
                                <span class="stars rating20"><em></em></span>
                                <span class="power" style="width:20px" id="rp2">-%</span>
                            </div>
                            <div>
                                <span class="stars rating10"><em></em></span>
                                <span class="power" style="width:20px" id="rp1">-%</span>
                            </div>
                            <br>
                        <div class="user-behaviors">
                            <button class="sButton" onclick="addSubject('<?=$sid?>')">Add to List</button>
                            <button class="sButton" onclick="share()">Share</button>
                            <p id="shareLink"></p>
                        </div>
                    </div>
                </div>
                <div class="comments">
                    <div class="user-comment-area">
                        <form name="crForm" action="comment.php" method="POST">
                            <div class="field">
                                <div>
                                    <h2>Give Comment:</h2><br>
                                    <div class="rating-you-stars">
                                        <div class="star" ><img id="star1" src="img/star-off.png">
                                        </img></div><div class="star" ><img id="star2" src="img/star-off.png">
                                        </img></div><div class="star" ><img id="star3" src="img/star-off.png">
                                        </img></div><div class="star" ><img id="star4" src="img/star-off.png">
                                        </img></div><div class="star" ><img id="star5" src="img/star-off.png">
                                        </img></div><span id="starValue"></span>
                                    </div>
                                </div>
                                <textarea rows="10" cols="100" name="comment"></textarea>
                                <input type="text" hidden="hidden" name="rate"/>
                                <input type="text" hidden="hidden" name="uid" value="<?=$_SESSION['uid']?>"/>
                                <input type="text" hidden="hidden" name="sid" value="<?=$_GET['s']?>"/>
                            </div>
                            <input class="sButton" type="submit" value="Submit" onclick="return check();" style="float:right;margin-right:270px;"/>
                        </form>
                    </div>
                    <div class="comment-list">
                        <?php
                            $commentResult = mysql_query("SELECT * FROM (rate INNER JOIN comment on rate.sid = comment.sid AND rate.uid=comment.uid) INNER JOIN users ON comment.uid=users.uid ".
                                "where comment.sid='".$row['sid']."'", $db);
                            while($commentRow = mysql_fetch_array($commentResult))
                            {
                                echo '<div class="comments-item">';
                                echo "<p><span style='font-size:1.25em'>".$commentRow['uname']."&nbsp&nbsp&nbsp&nbsp</span>\n";
                                echo "<span>".$commentRow['time']."</span>\n";
                                echo '<span class="stars rating'.$commentRow['rate'].'0"><em></em></span>';
                                echo "</p>";
                                echo "<p>".$commentRow['content']."</p>\n";
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="footer">Credits:&nbsp&nbsp&nbspJoe Wang, Travis Peng & Yuki Chen</div>
        </div>
    </body>
    
</html>