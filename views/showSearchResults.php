<?php
    define('ROOT_DIR', dirname(__FILE__,2));
    require_once ROOT_DIR.'/controllers/getdataArc.php';
    if(isset($_GET["start"]) && isset($_GET["site"])){
        autoBucle($_GET["site"],$_GET["start"]);
    }
    
?>