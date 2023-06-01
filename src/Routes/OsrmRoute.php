<?php
namespace Tualo\Office\OSRM\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class OsrmRoute implements IRoute{
    public static function register(){
        BasicRoute::add('/osrm/route',function($matches){
            try{
                $db = App::get('session')->getDB();
                App::contenttype('application/json');
                App::set('hlsJobDir',HLS_JOB_DIR);
                
                App::result('success',true);
            }catch(\Exception $e){
                App::result('msg', $e->getMessage());
            }
        },array('get','post'),true);
    }
}