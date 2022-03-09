<?php
    ini_set('memory_limit', '-1');
    require ROOT_DIR.'/models/getDataFromArc.php';
    require ROOT_DIR.'/controllers/genericFunctions.php';

    $siteList = json_decode(file_get_contents(ROOT_DIR.'/db/blocks.json'),true);

    function getArticlesSearch($site,$start,$end=false,$canonical_website=false,$from=0){
        return getSearchList($site,$start,$end,$canonical_website,$from);
    }

    function bucleArticlesList($site,$start,$end=false,$canonical_website=false,$from=0){
        $result = json_decode(getArticlesSearch($site,$start,$end,$canonical_website,$from),true);

        if(!array_key_exists('error',$result)){
            if(array_key_exists('content_elements',$result) && count($result['content_elements'])>0){
                if($result['count']<=100){
                    return $result;
                }else{
                    $tempListArticles = $result['content_elements'];
                    $iteration = intval($result['count']/100);
                    for ($i=1; $i <= $iteration ; $i++) {
                        $next = $i * 100;
                        $tempResult = json_decode(getArticlesSearch($site,$start,$end,$canonical_website,$next),true);
                        if(array_key_exists('content_elements',$tempResult)){
                            $tempListArticles = array_merge($tempListArticles,$tempResult['content_elements']);
                        }
                    }
                    $result['content_elements'] = $tempListArticles;
                    return $result;
                }
            }
            return null;
        }
        return null;
    }

    function autoBucle($site,$start){
        $startTime = microtime(true);
        $fileUrl = ROOT_DIR.'/sitemaps/'.$site.'/';
        if(is_dir($fileUrl)){
            $listOfFiles = array_diff(scandir($fileUrl,1),['.','..','index.php','sitemap-index.xml']);
            if(count($listOfFiles)>0){
                $start = date('Y-m',strtotime(substr(reset($listOfFiles),0,7).'-01 + 1 month'));
            }
        }
        $year  = date('Y',strtotime($start));
        $month = date('m',strtotime($start));
        makeXmlByMonth($site,$year,$month);
        $time_elapsed_secs = microtime(true) - $startTime;
        print_r('mes-año consultado =>'.$year.'-'.$month.'<======>'.timeCount($time_elapsed_secs).' <= tiempo de autobucle.<br>');
    }


    function makeASitemapIndex($fileUrl,$site){
        if(is_dir($fileUrl.'sitemap-index.xml')){
            $xmlIndex = file_get_contents($fileUrl.'sitemap-index.xml');
            $dateNow = date('Y-m');
            if(strpos($xmlIndex,$dateNow.'.xml')!==false){
                return null;
            }
        }

        global $siteList;
        $listFiles = array_diff(scandir($fileUrl,1),['.','..','index.php','sitemap-index.xml']);
        $urlBase = $siteList['urlBase'];
        $xmlEncode = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlStart  = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xmlEnd    = '</sitemapindex>';
        $xmlContent   = '';
        foreach ($listFiles as $key => $value) {
            $xmlContent .= '<sitemap><loc>'.$urlBase.'/sitemaps/'.$site.'/'.$value.'</loc></sitemap>';
        }
        $xmlFull = $xmlEncode.$xmlStart.$xmlContent.$xmlEnd;
        writeFile($fileUrl.'sitemap-index.xml',$xmlFull,'w+');      
    }

    function makeXmlByMonth($site,$year,$month){
        $startTime = microtime(true);
        $yearMonth=$year.'-'.$month;
        $today = date('Y-m-d');
        $fileUrl = ROOT_DIR.'/sitemaps/'.$site.'/';
        if(!is_dir($fileUrl)){
            mkdir($fileUrl,0755,true);
        }
        if(!is_dir($fileUrl.'index.php')){
            writeFile($fileUrl.'index.php','<?php echo("A veces una hoja en blanco es la seguridad del alma."); ?>','w+');
        }
        
        $lastDay = getLastDayMonth($yearMonth);
        if( date($yearMonth) < $today){
            $tempContent = '';
            for ($i=$lastDay; $i > 0 ; $i--) { 
                $day = $i<10 ? '0'.$i:$i;
                if(date($yearMonth.'-'.$day) < $today){
                    $newsList = bucleArticlesList($site,$year.'-'.$month.'-'.$day);
                    if($newsList!==null){
                        foreach ($newsList['content_elements'] as $key => $value) {
                            $content = makeXmlArticleData($site,$value);
                            if($content!==null){
                                $tempContent .= $content;
                            }
                        }
                    }
                }
            }
            if(strlen($tempContent)>0){
                $header='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
                $footer='</urlset>'; 
                writeFile($fileUrl.$yearMonth.'.xml',$header.$tempContent.$footer,'w+');
                $time_elapsed_secs = microtime(true) - $startTime;
                makeASitemapIndex($fileUrl,$site);
                return true;
            }
            return false;
        }
        $time_elapsed_secs = microtime(true) - $startTime;
        print_r(timeCount($time_elapsed_secs).' <= tiempo de ejecución funcion sin pasar if.<br>');
        return null;
    }

    function makeXmlArticleData($site,$article,$config=false){
        global $siteList;
        $config = !$config?['changefreq'=>'always','priority'=>0.5]:$config;
        $siteUrl=$siteList['sites'][$site]['siteProperties']['websiteDomain'];
        if(array_key_exists('_id',$article) && array_key_exists('websites',$article)){
            $resultXml ='<url>';
            $resultXml.='<loc>'.$siteUrl.$article['websites'][$site]['website_url'].'</loc>';
            $resultXml.='<lastmod>'.$article['last_updated_date'].'</lastmod>';
            $resultXml.='<changefreq>'.$config['changefreq'].'</changefreq>';
            $resultXml.='<priority>'.$config['priority'].'</priority>';
            if( array_key_exists('promo_items',$article) && 
                array_key_exists('basic',$article['promo_items']) && 
                array_key_exists('additional_properties',$article['promo_items']['basic']) &&
                array_key_exists('resizeUrl',$article['promo_items']['basic']['additional_properties'])
            ){
                $pathImg=$article['promo_items']['basic']['additional_properties']['resizeUrl'];
                if(array_key_exists('caption',$article['promo_items']['basic']) && strlen($article['promo_items']['basic']['caption'])>0 ){
                    $imgDescription = $article['promo_items']['basic']['caption'];
                }else{
                    if(array_key_exists('alt_text',$article['promo_items']['basic']) && strlen($article['promo_items']['basic']['alt_text'])>0){
                        $imgDescription=$article['promo_items']['basic']['alt_text'];
                    }else{
                        $imgDescription = $article['headlines']['basic'];
                    }
                }
                $resultXml.='<image:image>';
                $resultXml.='<image:loc>'.$siteUrl.$pathImg.'</image:loc>';
                $resultXml.='<image:caption>';
                $resultXml.='<![CDATA[ '.$imgDescription.' ]]>';
                $resultXml.='</image:caption>';
                $resultXml.='</image:image>';
            }
            $resultXml.='</url>';
            return $resultXml;
        }
        return null;
    }

?>