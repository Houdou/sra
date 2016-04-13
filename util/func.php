<?php
function convertTimeslot($timeslot)
    {
        //timeslot data will in the form that:
        //"WWWWWWHHHHHHHHHHHHHC"
        //1-6: Weekday, 1 is marked
        //7-19: Hour slot, from 08:30 to 22:20, 1 bit per hour
        //20: compulsory or not
        
        $compulsory = substr($timeslot, 19);
        $timeslot = substr($timeslot, 0, 19);
        $day = substr($timeslot, 0, 6);
        $day = strpos($day, "1");
        $time = substr($timeslot, 6);
        $startTime = strpos($time, "1");
        $endTime = strrpos($time, "1");
        switch($day)
        {
            case 0:
                $day = "Monday";
                break;
            case 1:
                $day = "Tuesday";
                break;
            case 2:
                $day = "Wednesday";
                break;
            case 3:
                $day = "Thursday";
                break;
            case 4:
                $day = "Friday";
                break;
            case 5:
                $day = "Saturday";
                break;
            default:
                break;
        }
        
        $startTime += 8;
        $startTime = substr("00" . $startTime, -2);
        $startTime .= ":30";
        $endTime += 9;
        $endTime = substr("00" . $endTime, -2);
        $endTime .= ":20";
        
        $time = array($day, $startTime, $endTime, $compulsory);
        
        return $time;
    }
?>