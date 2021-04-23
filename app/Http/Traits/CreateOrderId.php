<?php
namespace App\Http\Traits;

use App\Order;
use Auth;

trait CreateOrderId {


    public function newOrderId() {

        $alphabet = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9");

        $yearCode = $this->yearCode($alphabet);
        $monthCode = $this->monthCode($alphabet);
        $dateCode = $this->dateCode($alphabet);

        return $unique_order_id = $this->unique_order_id($alphabet, $yearCode, $monthCode, $dateCode);

    }


    public function yearCode($alphabet) {

        $year = date('Y');

        $j = 0;
        for ($i=2017; $i <= 2052; $i++) { 
            if($i == $year){
                return $alphabet[$j];
            }
            $j++;
        }

    }

    public function monthCode($alphabet) {

        $month = date('m');

        $j = 0;
        for ($i=01; $i <= 12; $i++) { 
            if($i == $month){
                return $alphabet[$j];
            }
            $j++;
        }

    }

    public function dateCode($alphabet) {

        $day = date('d');

        $j = 0;
        for ($i=01; $i <= 31; $i++) { 
            if($i == $day){
                return $alphabet[$j];
            }
            $j++;
        }

    }

    public function unique_order_id($alphabet, $yearCode, $monthCode, $dateCode) {

        while(1)
        {
            $random = array_rand($alphabet,4);
            $randomString = "";
            foreach ($random as $key) {
                $randomString = $randomString.$alphabet[$key];
            }
            $proposed = $yearCode.$monthCode.$dateCode.$randomString;

            $count = Order::where('unique_order_id', $proposed)->WhereBetween('created_at',array(date('Y-m-d').' 00:00:01',date('Y-m-d').' 23:59:59'))->count();

            if($count == 0){
                return $proposed;
            }
        }
       
    }


}