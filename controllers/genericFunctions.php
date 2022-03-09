<?php

    function getLastDayMonth($yearAndMonth){
        $L = new DateTime( $yearAndMonth.'-01' ); 
        return intval($L->format('t'));
    }

    function writeFile($fileUrl,$data,$mode='w'){
        $fp = fopen($fileUrl,$mode);      
        fwrite($fp,$data);
        fclose($fp);
    }

    function createFolder($url){
        if (!file_exists($url)) {
            mkdir($url, 0777, true);
        }
    }

    function diffMonthDates($start,$end){
        $start = date_create($start);
        $end = date_create($end);
        $diff = date_diff($start, $end);
        return $diff->format('%y')*12 + $diff->format('%m');
    }

    //order Date array
   function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }

    function orderArrayDate($arr){
        usort($arr,"date_sort");
        return($arr);
    }

    function timeCount($sec){
        switch ($sec) {
            case $sec>60:
                $sec = ($sec/60).'min';
                break;
            case $sec>3600:
                $sec = ($sec/3600).'hrs';
                break;
            case $sec>86400:
                $sec = ($sec/86400).'días';
                break;
        }
        return $sec;
    }

?>