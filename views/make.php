<?php
    //var_dump($argv);
    define('ROOT_DIR', dirname(__FILE__,2));
    require_once ROOT_DIR.'/controllers/getDataArc.php';
    if(isset($_GET["make"]) && isset($_GET["site"])){
        echo 'se tienen los parametros de creacion de sitemap index.';
        if($_GET["make"]=='sitemapIndex'){
            $site = $_GET["site"];
            $fileUrl = ROOT_DIR.'/sitemaps/'.$site.'/';
            print_r(makeASitemapIndex($fileUrl,$site));
        }
    }
?>