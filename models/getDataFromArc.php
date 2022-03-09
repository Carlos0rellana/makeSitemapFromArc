<?php

    function getResultListFromArc($queryArray) {
        $curl = curl_init();
        curl_setopt_array($curl,$queryArray);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function makeArrayQuerie($apiQueryUrl,$key=false){
        require ROOT_DIR.'/config/dataConection.php';
        if(!$key){$key=$pass;}
        return array(
          CURLOPT_URL => $apiUrl.$apiQueryUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array('Authorization: Bearer '.$key),
        );
    }

    function getSearchList($site,$start,$end=false,$canonical_website=false,$from=0){
        $end= !$end?$start:$end;
        $canonical=$canonical_website?'+AND+canonical_website:'.$site:'';
        $site='website='.$site;
        $type='+AND+type:story';
        $dateRange='publish_date:%5B'.$start.'+TO+'.$end.'%5D';
        $sourceData='&_sourceInclude=headlines,websites,publish_date,promo_items,last_updated_date';
        $query='content/v4/search/published?'.$site.'&q='.$dateRange.$canonical.$type.$sourceData.'&size=100&from='.$from;
        return getResultListFromArc(makeArrayQuerie($query));
    }

?>