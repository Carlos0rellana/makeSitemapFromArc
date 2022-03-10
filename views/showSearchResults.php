<?php
    //var_dump($argv);
    parse_str($argv[1], $params);
    parse_str($argv[2], $params);
    var_dump($params);
    define('ROOT_DIR', dirname(__FILE__,2));
    require_once ROOT_DIR.'/controllers/getDataArc.php';
    if(isset($_GET["start"]) && isset($_GET["site"])){
        autoBucle($_GET["site"],$_GET["start"]);
    }elseif(array_key_exists('start',$params) && array_key_exists('site',$params)){
        autoBucle($params["site"],$params["start"]);
    }
?>