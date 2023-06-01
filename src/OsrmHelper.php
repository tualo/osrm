<?php
namespace Tualo\Office\OSRM;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class OsrmHelper {

    public static function route($type,$from_lon,$from_lat,$to_lon,$to_lat,$steps=false){
        if(!defined("OSRM_URL")) throw new Exception("Die API steht derzeit nicht zur Verfügung (OSRM_URL)");
        $url = OSRM_URL.'/'.$type.'/'.$from_lon.','.$from_lat.';'.$to_lon.','.$to_lat.'?steps='.(($steps===true)?'true':'false');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        TualoApplication::result('url',$url);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode){
            TualoApplication::result('debug_response',$data);
            $json = json_decode($data,true);
            if (is_null($json)) throw new Exception("Die API steht derzeit nicht zur Verfügung (JSON Error)");
            if (!is_array($json)) throw new Exception("Die API steht derzeit nicht zur Verfügung (JSON not an Array)");
            return $json;
        }else{
            throw new Exception("Die API steht derzeit nicht zur Verfügung (".$httpCode.")");
        }
    }
}