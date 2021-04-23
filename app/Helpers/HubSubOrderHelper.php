<?php

function hubAllStatus() {

    $sub_order_status = \App\Status::select('id','title')->pluck('title','id')->toArray();

    return $sub_order_status;

}

function hubWhereInStatus($sub_order_status){

    switch ($sub_order_status) {
        case "1":
            $whereIn = array(2);
            break;
        case "2":
            $whereIn = array(3, 4);
            break;
        case "3":
            $whereIn = array(5, 9);
            break;
        case "4":
            $whereIn = array(7, 8);
            break;
        case "5":
            $whereIn = array(10, 11, 15, 16);
            break;
        case "6":
            $whereIn = array(6, 12);
            break;
        case "7":
            $whereIn = array(13);
            break;
        case "8":
            $whereIn = array(18, 19, 20, 21);
            break;
        case "9":
            $whereIn = array(22, 26, 27);
            break;
        case "10":
            $whereIn = array(28, 29);
            break;
        case "11":
            $whereIn = array(30);
            break;
        case "12":
            $whereIn = array(31, 32);
            break;
        case "13":
            $whereIn = array(38, 39, 41, 42, 43, 44, 45);
            break;
        case "14":
            $whereIn = array(33);
            break;
        case "15":
            $whereIn = array(34);
            break;
        case "16":
            $whereIn = array(35);
            break;
        case "17":
            $whereIn = array(36);
            break;
        case "18":
            $whereIn = array(37);
            break;
        case "19":
            $whereIn = array(40);
            break;
        case "20":
            $whereIn = array(0);
            break;
        case "21":
            $whereIn = array(47);
            break;
        case "22":
            $whereIn = array(41);
            break;
        case "23":
            $whereIn = array(42);
            break;
        case "24":
            $whereIn = array(43);
            break;
        case "25":
            $whereIn = array(45);
            break;
        case "26":
            $whereIn = array(46);
            break;
        default:
            $whereIn = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48);
    }

    return $whereIn;

}

function hubWhereInStatusText($sub_order_status){

    switch ($sub_order_status) {
        case "1":
            $whereIn = array(hubGetRealStatus(2));
            break;
        case "2":
            $whereIn = array(hubGetRealStatus(3), hubGetRealStatus(4));
            break;
        case "3":
            $whereIn = array(hubGetRealStatus(5), hubGetRealStatus(9));
            break;
        case "4":
            $whereIn = array(hubGetRealStatus(7), hubGetRealStatus(8));
            break;
        case "5":
            $whereIn = array(hubGetRealStatus(10), hubGetRealStatus(11), hubGetRealStatus(15), hubGetRealStatus(16));
            break;
        case "6":
            $whereIn = array(hubGetRealStatus(6), hubGetRealStatus(12));
            break;
        case "7":
            $whereIn = array(hubGetRealStatus(13));
            break;
        case "8":
            $whereIn = array(hubGetRealStatus(18), hubGetRealStatus(19), hubGetRealStatus(20), hubGetRealStatus(21));
            break;
        case "9":
            $whereIn = array(hubGetRealStatus(22), hubGetRealStatus(26), hubGetRealStatus(27));
            break;
        case "10":
            $whereIn = array(hubGetRealStatus(28), hubGetRealStatus(29));
            break;
        case "11":
            $whereIn = array(hubGetRealStatus(30));
            break;
        case "12":
            $whereIn = array(hubGetRealStatus(31), hubGetRealStatus(32));
            break;
        case "13":
            $whereIn = array(hubGetRealStatus(38), hubGetRealStatus(39), hubGetRealStatus(41), hubGetRealStatus(42), hubGetRealStatus(43), hubGetRealStatus(44), hubGetRealStatus(45));
            break;
        case "14":
            $whereIn = array(hubGetRealStatus(33));
            break;
        case "15":
            $whereIn = array(hubGetRealStatus(34));
            break;
        case "16":
            $whereIn = array(hubGetRealStatus(35));
            break;
        case "17":
            $whereIn = array(hubGetRealStatus(36));
            break;
        case "18":
            $whereIn = array(hubGetRealStatus(37));
            break;
        case "19":
            $whereIn = array(hubGetRealStatus(40));
            break;
        case "20":
            $whereIn = array('Inactive');
            break;
        case "21":
            $whereIn = array(hubGetRealStatus(47));
            break;
        case "22":
            $whereIn = array(hubGetRealStatus(41));
            break;
        case "23":
            $whereIn = array(hubGetRealStatus(42));
            break;
        case "24":
            $whereIn = array(hubGetRealStatus(43));
            break;
        case "25":
            $whereIn = array(hubGetRealStatus(45));
            break;
        case "26":
            $whereIn = array(hubGetRealStatus(46));
            break;
        default:
            $whereIn = array('Unknown');
    }

    return $whereIn;

}

function hubGetStatus($sub_order_status){
    $hub_status_id = $sub_order_status;
    $all_sub_order_status = hubAllStatus();
    return $all_sub_order_status["$hub_status_id"];
}

function hubGetRealStatus($sub_order_status){
    $data = DB::table('status')->where('code', $sub_order_status)->first();
    return $data->title;
}

function hubGetRealStatusId($sub_order_status){
    $data = DB::table('status')->where('title', $sub_order_status)->first();
    return $data->code;
}

function countStatus($sub_order_group, $start_date, $end_date){

    $whereInStatusText = hubWhereInStatusText($sub_order_group);
    
    return $count = DB::table('sub_orders')->where('sub_orders.status', '!=', 0)
                    ->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id')
                    ->WhereIn('order_logs.text',$whereInStatusText)
                    ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                    ->where(function($query) {
                        $query->where('sub_orders.destination_hub_id', '=', auth()->user()->reference_id);
                        $query->orWhere('sub_orders.source_hub_id', '=', auth()->user()->reference_id);
                        $query->orWhere('sub_orders.next_hub_id', '=', auth()->user()->reference_id);
                    })
                    ->groupBy('sub_orders.id')->count();

}

function countStatusAll($sub_order_group, $start_date, $end_date){

    $whereInStatusText = hubWhereInStatusText($sub_order_group);
    
    return $count = DB::table('sub_orders')->where('sub_orders.status', '!=', 0)
                    ->leftJoin('order_logs','order_logs.sub_order_id','=','sub_orders.id')
                    ->WhereIn('order_logs.text',$whereInStatusText)
                    ->WhereBetween('order_logs.created_at', array($start_date.' 00:00:01',$end_date.' 23:59:59'))
                    ->groupBy('sub_orders.id')->count();

}

// function timeDifference( $start, $end ){
//     // return $start;
//     $uts['start']      =    strtotime( $start );
//     $uts['end']        =    strtotime( $end );
//     if( $uts['start']!==-1 && $uts['end']!==-1 )
//     {
//         if( $uts['end'] >= $uts['start'] )
//         {
//             $diff    =    $uts['end'] - $uts['start'];
//             if( $years=intval((floor($diff/31104000))) )
//                 $diff = $diff % 31104000;
//             if( $months=intval((floor($diff/2592000))) )
//                 $diff = $diff % 2592000;
//             if( $days=intval((floor($diff/86400))) )
//                 $diff = $diff % 86400;
//             if( $hours=intval((floor($diff/3600))) )
//                 $diff = $diff % 3600;
//             if( $minutes=intval((floor($diff/60))) )
//                 $diff = $diff % 60;
//             $diff    =    intval( $diff );
//             // return( array('years'=>$years,'months'=>$months,'days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
//             $text = '';
//             if($years != 0){
//                 $text = $text.$years.' Years ';
//             }
//             if($months != 0){
//                 $text = $text.$months.' Months ';
//             }
//             if($days != 0){
//                 $text = $text.$days.' Days ';
//             }
//             if($hours != 0){
//                 $text = $text.$hours.' Hours ';
//             }
//             if($minutes != 0){
//                 $text = $text.$minutes.' Minutes ';
//             }
//             if($diff != 0){
//                 $text = $text.$diff.' Seconds';
//             }

//             return $text;
//         }
//         else
//         {
//             return "Ending date/time is earlier than the start date/time";
//         }
//     }
//     else
//     {
//         return "Invalid date/time data detected";
//     }
// }

function timeDifference( $start, $end ){
    // return $start;
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );
            $text = '';
            if($hours != 0){
                $text = $text.sprintf("%02d", $hours).':';
            }else{
                $text = $text.'00:';
            }
            if($minutes != 0){
                $text = $text.sprintf("%02d", $minutes).':';
            }else{
                $text = $text.'00:';
            }
            if($diff != 0){
                $text = $text.sprintf("%02d", $diff);
            }else{
                $text = $text.'00';
            }

            return $text;
        }
        else
        {
            return "Ending date/time is earlier than the start date/time";
        }
    }
    else
    {
        return "Invalid date/time data detected";
    }
}
