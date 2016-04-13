<?php
    session_start();
    if(!isset($_SESSION['uid'])){
         header('Location:index.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="util/jquery.min.js"></script>
        <script type="text/javascript">
            function selectAll(){
                var checkBox = document.getElementById("selectAllcbx");
                var checkBoxAll = document.getElementsByClassName("select-item-cbx");
                for(var i = 0; i<checkBoxAll.length; i++){
                    checkBoxAll[i].checked = checkBox.checked;
                }
                countCheck();
            }
            
            function countCheck()
            {
                var checkBoxAll = document.getElementsByClassName("select-item-cbx");
                var checkCount = 0;
                for(var i = 0; i<checkBoxAll.length; i++){
                    if(checkBoxAll[i].checked)
                        checkCount++;
                }
                document.getElementById('select-count').innerHTML = checkCount;
            }
            
            function loadList()
            {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function()
                {
                    if(xhr.readyState == 4 && xhr.status == 200)
                    {
                        document.getElementById('wishListTable').innerHTML = xhr.responseText;
                    }
                }
                xhr.open("GET","AJAX/loadList.php",true);
                xhr.send();
            }
            
            function deleteSubject(subjectCode)
        	{
        	    var xhr = new XMLHttpRequest();
        	    xhr.onreadystatechange = function()
        	    {
        	        if(xhr.readyState == 4 && xhr.status == 200)
        	        {
        	            if(xhr.responseText == "deleted")
        	                alert("Success!");
        	            else if(xhr.responseText == "Not in list")
        	                alert("Not in list");
    	                loadList()
        	        }
        	    }
        	    xhr.open("POST", "AJAX/deleteFromWish.php", true);
        	    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        	    xhr.send("s="+subjectCode);
        	}
        	
        	//Load the wishing list when the page is loaded.
        	$(document).ready(function(){
        	    loadList();
        	});
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
                <h1>Wishing List</h1>
                <div class="list-layout">
                    <form name="wishListForm" id="wishListForm" action="timetable.php" method="POST">
                        <div class="list-main">
                            <table class="list-table" cellspacing="10">
                                <thead>
                                    <tr>
                                        <th class="select-all">
                                            <input class="select-all-cbx" id="selectAllcbx" type="checkbox" onclick="selectAll()"/>
                                        </th>
                                        <th class="list-subject-code">Subject Code</th>
                                        <th class="list-subject-name">Subject Name</th>
                                        <th class="list-subject-group">Subject Group</th>
                                        <th class="list-component-code">Component Code</th>
                                        <th class="list-day-of-week">Day of Week</th>
                                        <th class="list-start-time">Start Time</th>
                                        <th class="list-end-time">End Time</th>
                                        <th class="list-venue">Venue</th>
                                        <th class="list-staff">Teaching Staff</th>
                                        <th class="list-remark">Remark</th>
                                        <th class="list-delete">Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="wishListTable">
                                    <tr>
                                        <td colspan="12">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="select-operation">
                            <div class="select-amount">
                                <span class="txt">Select</span>
                                <span id="select-count" style="font-weight:bold;">0</span>
                                <span class="txt">Subjects</span>
                            </div>
                            <div style="padding-top:10px;" class="btn-area">
                                <button class="sButton" onclick="wishListForm.submit()">
                                Check & Generate Timetable
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>