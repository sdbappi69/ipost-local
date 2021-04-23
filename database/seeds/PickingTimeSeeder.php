<?php

use Illuminate\Database\Seeder;

class PickingTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('picking_time_slots')->delete();

        $slot = array();
        $mydays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    	foreach ($mydays as $day) {
    		$slot[] = array('day' => $day, 'start_time' => '09:00 AM', 'end_time' => '01:00 PM');
    	}

        \Illuminate\Support\Facades\DB::table('picking_time_slots')->insert( $slot );
    }
}
