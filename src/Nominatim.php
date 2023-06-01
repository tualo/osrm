<?php
namespace Tualo\Office\OSRM;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Nominatim {

    public static function geocode(){
        if(!defined("NOMINATIM_URL")){
            throw new \Exception("Die API steht derzeit nicht zur Verfügung (NOMINATIM_URL)");
        }else{

            
            $url = NOMINATIM_URL."search?";
            $url .= "&street=".urlencode($_REQUEST['street']);
            $url .= "&city=".urlencode($_REQUEST['city']);
            $url .= "&postalcode=".urlencode($_REQUEST['postalcode']);

            $url .= "&format=json";
            $url .= "&extratags=1";
            $url .= "&namedetails=1";
            $url .= "&polygon_geojson=1";

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
                App::result('debug_response',$data);
                $json = json_decode($data,true);
                if (is_null($json)) throw new Exception("Die API steht derzeit nicht zur Verfügung (JSON Error)");
                if (!is_array($json)) throw new Exception("Die API steht derzeit nicht zur Verfügung (JSON not an Array)");
                //if (count($json)>0){
                    App::result('success',true);
                    App::result('json',$json);
                //}

            }else{
                throw new App("Die API steht derzeit nicht zur Verfügung (".$httpCode.")");
            }

        }
    }

}