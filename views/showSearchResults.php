<?php
    //var_dump($argv);
    parse_str($argv[1], $site);
    parse_str($argv[2], $date);
    var_dump($site);
    var_dump($date);
    define('ROOT_DIR', dirname(__FILE__,2));
    require_once ROOT_DIR.'/controllers/getDataArc.php';
    if(isset($_GET["start"]) && isset($_GET["site"])){
        autoBucle($_GET["site"],$_GET["start"]);
    }elseif(array_key_exists('start',$site) && array_key_exists('site',$date)){
        autoBucle($site["site"],$date["start"]);
    }
?>