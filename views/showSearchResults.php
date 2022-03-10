<?php
    //var_dump($argv);
    define('ROOT_DIR', dirname(__FILE__,2));
    require_once ROOT_DIR.'/controllers/getDataArc.php';
    if(isset($_GET["start"]) && isset($_GET["site"])){
        autoBucle($_GET["site"],$_GET["start"]);
    }
    if(count($argv)>1){
        parse_str($argv[1], $site);
        parse_str($argv[2], $date);
        autoBucle($site["site"],$date["start"]);
    }
?>