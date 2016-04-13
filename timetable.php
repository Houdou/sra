<?php
    session_start();
    
    if(!isset($_SESSION['uid'])){
         header('Location:index.php');
    }
    
    // DB connection
    $servername = getenv('IP');
    $username = "houdou";
    $password = "SRA";
    $database = "sra";
    $db = mysql_connect($servername, $username, $password);
    mysql_select_db($database);
    
    //Get all the subject user selected
    $subjectList = array();
    $subjectSelect = array();
    foreach($_POST as $subject => $value)
    {
        if($codeIndex = strpos($subject, "-"))//Option component will have a "-" in the key
        {
            //Mark the component selected
            //For example: $subject EIE1234-TUT => $value TUT002
            array_push($subjectSelect, array(substr($subject, 0, $codeIndex), $value));
        }
        else
        {
            //In the post data, selected subject will have a prefix "sl"
            array_push($subjectList, substr($subject,2));
        }
    }
    
    //Use implode to join the SQL conditions gracefully.
    $sql = "SELECT sid, type, timeslot FROM schedule WHERE sid='";
    $sql .= implode("' OR sid= '", $subjectList) . "'";
    $slot = mysql_query($sql, $db);
    
    if(mysql_errno() > 0)
    {
        echo mysql_error();
        exit;
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="util/jquery.min.js"></script>
        <script type="text/javascript" src="util/color.js"></script>
        <script type="text/javascript" src="util/md5.js"></script>
        <script type="text/javascript">
            <?php
                //After retrieving all the data,
                //send the data into JS Array to produce the timetable
                
                //ts_raw : Array of Array(subjectID, componentType, timeslot)
                echo "var ts_raw = Array(";
                $list = array();
                $flag = $row = mysql_fetch_array($slot, MYSQL_ASSOC);
                while($flag)
                {
                    array_push($list, $row);
                    echo "Array(". "'" . $row['sid'] . "','" . $row['type']. "'," . bindec($row['timeslot']);
                    if($row = mysql_fetch_array($slot, MYSQL_ASSOC))
                        echo "),";
                    else
                    {
                        $flag = false;
                        echo ")";
                    }
                }
                echo ");\n";
                
                //subjectSelect : Array of Array(subjectID, selection(ie. "TUT002"))
                echo "var subjectSelect = Array(";
                $subjectSelectArr = array();
                if(count($subjectSelect) > 0)
                {
                    foreach($subjectSelect as $select)
                    {
                        array_push($subjectSelectArr, "Array("."'".$select[0]."','".$select[1]."')");
                    }
                    $subjectSelectArrStr = implode(",",$subjectSelectArr);
                    echo $subjectSelectArrStr;
                }
                echo ");\n";
                
                // For debug
                // echo "/*\n";
                // print_r($list);
                // echo "*/\n";
            ?>
            var timeslot = Array();
            for(var i=0; i < ts_raw.length; i++)
            {
                //Fill the missing 0 after convertion
                var timeslotData = ("00000000000000000000"+ts_raw[i][2].toString(2)).substr(-20);
                timeslot.push(
                    Array(
                        ts_raw[i][0],                                   //0, Subject ID
                        ts_raw[i][1],                                   //1, Class type
                        timeslotData.substr(0,6).indexOf('1'),          //2, Weekday
                        timeslotData.substr(6).indexOf('1'),            //3, start time
                        timeslotData.substr(6,13).lastIndexOf('1'),     //4, end time
                        timeslotData.substr(19),                        //5, is selected
                        ts_raw[i][2] & 0b00000011111111111111,          //6, rawTS for crash check
                        false                                           //7, is crash
                        ));
            }
            console.log(timeslot);
            
            function check(){
                for(var i = 0; i < timeslot.length; i++)
                {
                    for(var j = i + 1; j < timeslot.length; j++)
                    {
                        if(timeslot[i][0] == timeslot[j][0])            //Skip if they are same subject
                            continue;
                        if((timeslot[i][2] == timeslot[j][2])           //In same day
                        && (timeslot[i][5]+timeslot[j][5] == "11")      //Both are selected
                        && (timeslot[i][6] & timeslot[j][6]) > 1)       //Time conflicts
                        {
                            //alert(timeslot[i][0]+":"+timeslot[i][1] +" crash with " + timeslot[j][0]+":"+timeslot[j][1]);
                            document.getElementById("crashMsg").innerHTML += timeslot[i][0]+":"+timeslot[i][1] +" crash with " + timeslot[j][0]+":"+timeslot[j][1] + "<br/>";
                            timeslot[i][7] = true;
                            timeslot[j][7] = true;
                        }
                    }
                }
            }
            
            function mark() {
                for(var i=0; i<subjectSelect.length; i++)
                {
                    var subject = subjectSelect[i][0];
                    var select = subjectSelect[i][1];
                    for(var j = 0; j < timeslot.length; j++)
                    {
                        if(timeslot[j][0] == subject && timeslot[j][1] == select)
                        {
                            timeslot[j][5] = "1";                      //mark selection
                            break;
                        }
                    }
                }
                
                check();
                
                for(var i=0; i<timeslot.length; i++)
                {
                    if(timeslot[i][5]=="0")//Mark only selected component
                        continue;
                    
                    var duration = timeslot[i][4] - timeslot[i][3] + 1;
                    //cellid : "WHH" W: weekday HH: hours
                    var cellid = timeslot[i][2] + ("00"+timeslot[i][3]).substr(-2);
                    var cell = document.getElementById("tc"+cellid);
                    if(!timeslot[i][7])//Normal component, no crash
                    {
                        //Fill content
                        cell.innerHTML = "<a href='details.php?s=" + timeslot[i][0] + "'>" + timeslot[i][0] + "</a><br/>" + timeslot[i][1];
                        //Expand the row
                        cell.parentNode.setAttribute("rowspan", duration);
                        //Hide following cell
                        for(var j = timeslot[i][3] + 1; j <= timeslot[i][4]; j++)
                        {
                            var deleteCellID = timeslot[i][2] + ("00"+j).substr(-2);
                            var deleteCell = document.getElementById("tc" + deleteCellID);
                            deleteCell.parentNode.setAttribute("hidden", "hidden");
                        }
                        
                        //Hash it subject id to get a random color.
                        //colorHash is in util/color.js
                        //It use subject id as the seed to get a MD5 hash string
                        //Trim a section and then map it to hue value,
                        //then convert it into RGB
                        //Therefore, a subject's all component will have same color
                        //, and all subject will have different color.
                        cell.parentNode.style.backgroundColor = colorHash(timeslot[i][0]);
                        cell.parentNode.style.boxShadow = "1px 2px 3px 2px rgba(30,30,30,0.1)";
                        cell.parentNode.style.color = "white";
                    }
                    else
                    {
                        var noSpan = true;
                        for(var j = timeslot[i][3]; j <= timeslot[i][4]; j++)
                        {
                            //Check if all the cell it need is available or not.
                            var deleteCellID = timeslot[i][2] + ("00"+j).substr(-2);
                            var deleteCell = document.getElementById("tc" + deleteCellID);
                            if(deleteCell.parentNode.getAttribute("hidden") == "hidden")
                            {
                                noSpan = false;
                                break;
                            }
                        }
                        
                        if(noSpan)//Draw it normally
                        {
                            cell.innerHTML += timeslot[i][0] + "<br/>" + timeslot[i][1] + "<br/>";
                            cell.parentNode.style.backgroundColor = "rgba(210,210,210,0.9)";
                            cell.className += " blink";//Blink effect is achieved by CSS3 animation
                            cell.parentNode.setAttribute("rowspan", duration);
                            for(var j = timeslot[i][3] + 1; j <= timeslot[i][4]; j++)
                            {
                                var hiddenCellID = timeslot[i][2] + ("00"+j).substr(-2);
                                var hiddenCell = document.getElementById("tc" + hiddenCellID);
                                hiddenCell.parentNode.setAttribute("hidden", "hidden");
                            }
                        }
                        else
                        {
                            j = timeslot[i][3];//Original start time cell
                            var hiddenCellID = timeslot[i][2] + ("00"+j).substr(-2);
                            var hiddenCell = document.getElementById("tc" + hiddenCellID);
                            
                            //If the start time cell is overlap by previous subject
                            if(hiddenCell.parentNode.getAttribute("hidden") == "hidden")
                            {
                                //Find the overlaping cell
                                while(hiddenCell.parentNode.getAttribute("hidden") == "hidden")
                                {
                                    j--;
                                    hiddenCellID = timeslot[i][2] + ("00"+j).substr(-2);
                                    hiddenCell = document.getElementById("tc" + hiddenCellID);
                                }
                                //Add the crashed subject information to that cell
                                hiddenCell.innerHTML += timeslot[i][0] + "<br/>" + timeslot[i][1] + "<br/>";
                                hiddenCell.parentNode.style.backgroundColor = "rgba(210,210,210,0.9)";
                                cell.className += " blink";//Make it blinking~
                            }
                            //Else its end time cell overlap with existing component
                            else
                            {
                                //Draw its start time cell
                                hiddenCell.innerHTML = timeslot[i][0] + "<br/>" + timeslot[i][1] + "<br/>";
                                hiddenCell.parentNode.style.backgroundColor = "rgba(210,210,210,0.9)";
                                hiddenCell.className += " blink";
                                //Add crash information to the overlaping cell
                                for(var j = timeslot[i][3] + 1; j <= timeslot[i][4]; j++)
                                {
                                    var hiddenCellID = timeslot[i][2] + ("00"+j).substr(-2);
                                    var hiddenCell = document.getElementById("tc" + hiddenCellID);
                                    hiddenCell.innerHTML += timeslot[i][0] + "<br/>" + timeslot[i][1] + "<br/>";
                                    hiddenCell.parentNode.style.backgroundColor = "rgba(210,210,210,0.9)";
                                    cell.className += " blink";
                                }
                            }
                        }
                        cell.style.color = "#F15C52";
                    }
                }
            }
            
            $(document).ready(function(){
                mark();
            })
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
                <p id="crashMsg" style="color:red;font-family:Baskerville, Cambria, serif; text-align:center"></p>
                <div class="timetable" id="timetable">
                    <table>
                        <!--Title-->
                        <tr>
                            <th><div class="timetable-time-head"></div></th>
                            <th><div class="timetable-days-head">Monday</div></th>
                            <th><div class="timetable-days-head">Tuesday</div></th>
                            <th><div class="timetable-days-head">Wednesday</div></th>
                            <th><div class="timetable-days-head">Thursday</div></th>
                            <th><div class="timetable-days-head">Friday</div></th>
                            <th><div class="timetable-days-head">Saturday</div></th>
                        </tr>
                        <!--0830-0930-->
                        <tr>
                            <th><div class="timetable-time-head">08:30</div></th>
                            <td><div id="tc000" class="timetable-cell"></div></td>
                            <td><div id="tc100" class="timetable-cell"></div></td>
                            <td><div id="tc200" class="timetable-cell"></div></td>
                            <td><div id="tc300" class="timetable-cell"></div></td>
                            <td><div id="tc400" class="timetable-cell"></div></td>
                            <td><div id="tc500" class="timetable-cell"></div></td>
                        </tr>
                        <!--0930-1030-->
                        <tr>
                            <th><div class="timetable-time-head">09:30</div></th>
                            <td><div id="tc001" class="timetable-cell"></div></td>
                            <td><div id="tc101" class="timetable-cell"></div></td>
                            <td><div id="tc201" class="timetable-cell"></div></td>
                            <td><div id="tc301" class="timetable-cell"></div></td>
                            <td><div id="tc401" class="timetable-cell"></div></td>
                            <td><div id="tc501" class="timetable-cell"></div></td>
                        </tr>
                        <!--1030-1130-->
                        <tr>
                            <th><div class="timetable-time-head">10:30</div></th>
                            <td><div id="tc002" class="timetable-cell"></div></td>
                            <td><div id="tc102" class="timetable-cell"></div></td>
                            <td><div id="tc202" class="timetable-cell"></div></td>
                            <td><div id="tc302" class="timetable-cell"></div></td>
                            <td><div id="tc402" class="timetable-cell"></div></td>
                            <td><div id="tc502" class="timetable-cell"></div></td>
                        </tr>
                        <!--1130-1230-->
                        <tr>
                            <th><div class="timetable-time-head">11:30</div></th>
                            <td><div id="tc003" class="timetable-cell"></div></td>
                            <td><div id="tc103" class="timetable-cell"></div></td>
                            <td><div id="tc203" class="timetable-cell"></div></td>
                            <td><div id="tc303" class="timetable-cell"></div></td>
                            <td><div id="tc403" class="timetable-cell"></div></td>
                            <td><div id="tc503" class="timetable-cell"></div></td>
                        </tr>
                        <!--1230-1330-->
                        <tr>
                            <th><div class="timetable-time-head">12:30</div></th>
                            <td><div id="tc004" class="timetable-cell"></div></td>
                            <td><div id="tc104" class="timetable-cell"></div></td>
                            <td><div id="tc204" class="timetable-cell"></div></td>
                            <td><div id="tc304" class="timetable-cell"></div></td>
                            <td><div id="tc404" class="timetable-cell"></div></td>
                            <td><div id="tc504" class="timetable-cell"></div></td>
                        </tr>
                        <!--1330-1430-->
                        <tr>
                            <th><div class="timetable-time-head">13:30</div></th>
                            <td><div id="tc005" class="timetable-cell"></div></td>
                            <td><div id="tc105" class="timetable-cell"></div></td>
                            <td><div id="tc205" class="timetable-cell"></div></td>
                            <td><div id="tc305" class="timetable-cell"></div></td>
                            <td><div id="tc405" class="timetable-cell"></div></td>
                            <td><div id="tc505" class="timetable-cell"></div></td>
                        </tr>
                        <!--1430-1530-->
                        <tr>
                            <th><div class="timetable-time-head">14:30</div></th>
                            <td><div id="tc006" class="timetable-cell"></div></td>
                            <td><div id="tc106" class="timetable-cell"></div></td>
                            <td><div id="tc206" class="timetable-cell"></div></td>
                            <td><div id="tc306" class="timetable-cell"></div></td>
                            <td><div id="tc406" class="timetable-cell"></div></td>
                            <td><div id="tc506" class="timetable-cell"></div></td>
                        </tr>
                        <!--1530-1630-->
                        <tr>
                            <th><div class="timetable-time-head">15:30</div></th>
                            <td><div id="tc007" class="timetable-cell"></div></td>
                            <td><div id="tc107" class="timetable-cell"></div></td>
                            <td><div id="tc207" class="timetable-cell"></div></td>
                            <td><div id="tc307" class="timetable-cell"></div></td>
                            <td><div id="tc407" class="timetable-cell"></div></td>
                            <td><div id="tc507" class="timetable-cell"></div></td>
                        </tr>
                        <!--1630-1730-->
                        <tr>
                            <th><div class="timetable-time-head">16:30</div></th>
                            <td><div id="tc008" class="timetable-cell"></div></td>
                            <td><div id="tc108" class="timetable-cell"></div></td>
                            <td><div id="tc208" class="timetable-cell"></div></td>
                            <td><div id="tc308" class="timetable-cell"></div></td>
                            <td><div id="tc408" class="timetable-cell"></div></td>
                            <td><div id="tc508" class="timetable-cell"></div></td>
                        </tr>
                        <!--1730-1830-->
                        <tr>
                            <th><div class="timetable-time-head">17:30</div></th>
                            <td><div id="tc009" class="timetable-cell"></div></td>
                            <td><div id="tc109" class="timetable-cell"></div></td>
                            <td><div id="tc209" class="timetable-cell"></div></td>
                            <td><div id="tc309" class="timetable-cell"></div></td>
                            <td><div id="tc409" class="timetable-cell"></div></td>
                            <td><div id="tc509" class="timetable-cell"></div></td>
                        </tr>
                        <!--1830-1930-->
                        <tr>
                            <th><div class="timetable-time-head">18:30</div></th>
                            <td><div id="tc010" class="timetable-cell"></div></td>
                            <td><div id="tc110" class="timetable-cell"></div></td>
                            <td><div id="tc210" class="timetable-cell"></div></td>
                            <td><div id="tc310" class="timetable-cell"></div></td>
                            <td><div id="tc410" class="timetable-cell"></div></td>
                            <td><div id="tc510" class="timetable-cell"></div></td>
                        </tr>
                        <!--1930-2030-->
                        <tr>
                            <th><div class="timetable-time-head">19:30</div></th>
                            <td><div id="tc011" class="timetable-cell"></div></td>
                            <td><div id="tc111" class="timetable-cell"></div></td>
                            <td><div id="tc211" class="timetable-cell"></div></td>
                            <td><div id="tc311" class="timetable-cell"></div></td>
                            <td><div id="tc411" class="timetable-cell"></div></td>
                            <td><div id="tc511" class="timetable-cell"></div></td>
                        </tr>
                        <!--2030-2130-->
                        <tr>
                            <th><div class="timetable-time-head">20:30</div></th>
                            <td><div id="tc012" class="timetable-cell"></div></td>
                            <td><div id="tc112" class="timetable-cell"></div></td>
                            <td><div id="tc212" class="timetable-cell"></div></td>
                            <td><div id="tc312" class="timetable-cell"></div></td>
                            <td><div id="tc412" class="timetable-cell"></div></td>
                            <td><div id="tc512" class="timetable-cell"></div></td>
                        </tr>
                        <!--2130-2230-->
                        <tr>
                            <th><div class="timetable-time-head">21:30</div></th>
                            <td><div id="tc013" class="timetable-cell"></div></td>
                            <td><div id="tc113" class="timetable-cell"></div></td>
                            <td><div id="tc213" class="timetable-cell"></div></td>
                            <td><div id="tc313" class="timetable-cell"></div></td>
                            <td><div id="tc413" class="timetable-cell"></div></td>
                            <td><div id="tc513" class="timetable-cell"></div></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>