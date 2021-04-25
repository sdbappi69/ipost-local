<?php

if (!function_exists("getZoneBound")) {

    function getZoneBound($lat, $lng) {
        // $zone = DB::table('zone_maps')->where(DB::raw("ST_CONTAINS(ST_PolygonFromText(CONCAT('POLYGON((', zone_maps.coordinates, '))')), Point($lat, $lng))"), true)->join('zones', 'zone_maps.zone_id', '=', 'zones.id')->select('zone_maps.*', 'zones.*')->first();
        $zoneMap = App\ZoneMap::with('zone')
            ->where(DB::raw("ST_CONTAINS(ST_PolygonFromText(CONCAT('POLYGON((', zone_maps.coordinates, '))')), Point($lat, $lng))"), true)
            ->whereNotNull('coordinates')
            ->where('coordinates','!=','')
            ->whereHas('zone',function ($q){
                $q->whereStatus(1);
            })
            ->first();
        return $zoneMap != null ? $zoneMap->zone : null;
    }

}

if (!function_exists("lastDeniedUser")) {

    function lastDeniedUser($subOrderId) {
        $deniedUser = \App\TmTask::select('users.name')->join('users', 'users.id', '=', 'tm_tasks.user_id')
                ->where('tm_tasks.sub_order_id', $subOrderId)
                ->where('tm_tasks.status', 3)
                ->orderBy('tm_tasks.id', 'desc')
                ->first();
        if ($deniedUser) {
            return $deniedUser->name;
        }
        return null;
    }

}