<?php
namespace Tualo\Office\OSRM;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class TSP {
    public static function TSP(){
        /*
        $sql = '
        create table if not exists orms_nodes (
            id varchar(36) primary key,lon decimal(15,13),lat decimal(15,13)
        )';

        $sql = 'create table if not exists
                orms_nodes_map 
            (
                from_id varchar(36),
                to_id varchar(36),
                type varchar(10),

                primary key (from_id,to_id,type),
                from_lon decimal(15,13),
                from_lat decimal(15,13),
                to_lon decimal(15,13),
                to_lat decimal(15,13),

                distance decimal(15,5),
                duration decimal(15,5)
            ) ';
        

        $sql = '
        insert into orms_nodes_map 
        (from_id,from_lon,from_lat,to_id,to_lon,to_lat,type)
        select 
            a.id from_id,
            a.lon from_lon,
            a.lat from_lat,
            b.id to_id,
            b.lon to_lon,
            b.lat to_lat ,
            "bike"
        from orms_nodes a join orms_nodes b on true
        on duplicate key update from_lon=values(from_lon)
        ';
        */
        $list = $db->direct('select * from orms_nodes_map where distance is null');
        foreach($list as $item){
            $route = OsrmHelper::route(
                $item['type'],
                $item['from_lon'],
                $item['from_lat'],
                $item['to_lon'],
                $item['to_lat'],
                $steps=false
            );
            $item['distance']=9999999;
            $item['duration']=9999999;
            
            if ($route['code']=='Ok'){
                $item['distance'] = $route['routes'][0]['distance'];
                $item['duration'] = $route['routes'][0]['duration'];
            }

            $sql = '
            update 
                orms_nodes_map 
            set 
                distance={distance}, 
                duration={duration}
            where 
                from_id={from_id} and 
                to_id={to_id} and type={type}
            ';
            $db->direct($sql,$item);
        }

    }

    public static function bySV(){
        $sql='
        insert into orms_nodes (id,lon,lat)   
        select 
sv_normalized_address.id,
json_value(nominatim_json,"$[0].lon") lon,
json_value(nominatim_json,"$[0].lat") lat

from sv_daten 
join sv_normalized_address_queries on sv_normalized_address_queries.id = sv_daten.normalized_key
join sv_normalized_address on sv_normalized_address.id = sv_normalized_address_queries.sv_normalized_address

where sv_daten.gepl_zustellung=curdate() + interval - 4 day and sv_daten.sort_district = "ZT5702054_V_0070324"

group by 
sv_normalized_address.id';


    }
}
