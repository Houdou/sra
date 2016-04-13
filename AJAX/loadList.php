<?php
session_start();

$servername = getenv('IP');
$username = "houdou";
$password = "SRA";

$db = mysql_connect($servername, $username, $password);
mysql_select_db('sra');

require_once('../util/func.php');

//$_SESSION['list'] is an array storing all the subject selected
if(count($_SESSION['list']) > 0)
foreach($_SESSION['list'] as $sid)
{
    $sid = substr($sid, 0, 16);//In case that there is some invalid subject code in list
    $result = mysql_query("Select * from subject inner join schedule on subject.sid=schedule.sid where schedule.sid="."'".$sid."'", $db);
    $rowNumber = mysql_num_rows($result);
    if($rowNumber > 0)
    {
        if($row = mysql_fetch_array($result))
        {
            //Timeslot is a customized time data for our website, details explain can be found in report.
            $time = convertTimeSlot($row['timeslot']);
            ?>
            <tr><td colspan=12><hr style="border: 0px; height:1px; box-shadow:1px 1px 0px 0px rgba(100,100,100,0.3);"/></td></tr>
            <tr>
                <td rowspan="<?=$rowNumber?>"><input class="select-item-cbx" name="sl<?=$sid?>" id="sl<?=$sid?>" type="checkbox" onclick="countCheck()"></td>
                <td rowspan="<?=$rowNumber?>" class="list-subject-code"><?="<a href='details.php?s=".$row['sid']."'>".$row['sid']."</a>"?></td>
                <td rowspan="<?=$rowNumber?>" class="list-subject-name"><?=$row['sname']?></td>
                <td rowspan="<?=$rowNumber?>" class="list-subject-group"><?=$row['sgroup']?></td>
                <td class="list-component-code">
                    <?php if($time[3]=="0")
                            echo '<input type="radio"'. ((substr($row['type'],5)=="1")?'checked="checked"':'') .' name="'.$sid.'-'.substr($row['type'],0,3).'" value="'.$row['type'].'"/>';
                    echo $row['type'];?></td>
                <td class="list-day-of-week"><?=$time[0]?></td>
                <td class="list-start-time"><?=$time[1]?></td>
                <td class="list-end-time"><?=$time[2]?></td>
                <td class="list-venue"><?=$row['venue']?></td>
                <td class="list-staff"><?=$row['teachstaff']?></td>
                <td class="list-remark"></td>
                <td rowspan="<?=$rowNumber?>" class="list-delete"><button type="button" class="sButton" onclick="deleteSubject('<?=$sid?>')">Delete</button></td>
            </tr>
            <?php
        }
        while($row = mysql_fetch_array($result))
        {
            $time = convertTimeSlot($row['timeslot']);
            ?>
            <tr>
                <td class="list-component-code">
                    <?php if($time[3]=="0")
                            echo '<input type="radio"'. ((substr($row['type'],5)=="1")?'checked="checked"':'') .' name="'.$sid.'-'.substr($row['type'],0,3).'" value="'.$row['type'].'"/>';
                    echo $row['type'];?></td>
                <td class="list-day-of-week"><?=$time[0]?></td>
                <td class="list-start-time"><?=$time[1]?></td>
                <td class="list-end-time"><?=$time[2]?></td>
                <td class="list-venue"><?=$row['venue']?></td>
                <td class="list-staff"><?=$row['teachstaff']?></td>
                <td class="list-remark"></td>
            </tr>
            <?php
        }
    }
    else
        echo "<td colspan=12>Your wish list is empty, why not add some?</td>";
}
else
    echo "<td colspan=12>Your wish list is empty, why not add some?</td>";
?>