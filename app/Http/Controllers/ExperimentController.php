<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\SubOrder;
use App\Http\Traits\LogsTrait;
use App\Order;
use App\Http\Traits\AjkerDealTrait;
use DB;

class ExperimentController extends Controller
{

	use LogsTrait;
    use AjkerDealTrait;

    public function goToReturnPanel()
    {
        // $parent_sub_order_ids = array(
        //     'DBCXHKS901', 'DBCTHMVX01', 'DBBZWXZ603', 'DBCABM2401', 'DBCCN47801', 'DBCCCDNZ01', 'DBCEJOQU01',
        //     'DBCEKSW001', 'DBCOBT1801', 'DBCSRSU101', 'DBCUMSWX01', 'DBCUALNV01', 'DBCUBE3401', 'DBCNAKOP01',
        //     'DBCVJLMZ01', 'DBCVIMQ301', 'DBCVDZ1301', 'DBCVCJQ801', 'DBCWFLP501', 'DBCWDTZ301', 'DBCWCSUV01',
        //     'DBCWGN1401', 'DBCWAG0901', 'DBCWEKPZ01', 'DBCXBVY801', 'DBCXX23601', 'DBCNCOQU01', 'DBCXDEMN01',
        //     'DBCXXY0501', 'DBCYBEK901', 'DBCNNY0101', 'DBCNLZ6801', 'DBCYIOY701', 'DBCYJLX301', 'DBCYGU0401'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCYUWX701', 'DBCZCFR901', 'DBCZDE0301', 'DBCNIQR701', 'DBCZJQU801', 'DBCZKW5701', 'DBCZDKLW01',
        //     'DBCZCFR801', 'DBCZNST001', 'DBCZKLM701', 'DBCZDHQU01', 'DBCZJT5601', 'DBCZGK3801', 'DBCZDKY601',
        //     'DBCZBLTW01', 'DBCZGLR901', 'DBCZEF0801', 'DBCZN58901', 'DBCZERV401', 'DBC0AKPU01', 'DBC0CV2901',
        //     'DBC0AJP701', 'DBC0BKW901', 'DBCMKNO501', 'DBC0IN0201', 'DBC0GV1201', 'DBCMPUV001', 'DBCMFLVY01',
        //     'DBC0EKRY01', 'DBC0OU3501', 'DBC1IMU801', 'DBC1BKXY01', 'DBCMBRZ001', 'DBCMASVX01', 'DBC1IRU601'
        // );

        // $parent_sub_order_ids = array(
        //     'DBC1DKQ901', 'DBCMEJVY01', 'DBC2LNS401', 'DBC2BORZ01', 'DBC2CNY001', 'DBC2MN4901', 'DBC2DKV701',
        //     'DBC2AMRS01', 'DBC2DIX001', 'DBC2MW5701', 'DBC2Y15601', 'DBCMLWY401', 'DBC2CLY101', 'DBC2AQZ401',
        //     'DBCMACK801', 'DBCMPWXZ01', 'DBC3CFS501', 'DBC3BIS501', 'DBC3DPV601', 'DBCMEPR901', 'DBC3MSV601',
        //     'DBC3HLMO01', 'DBC3CHY901', 'DBC3BM0101', 'DBC3HLZ701', 'DBC3DMQV01', 'DBC3MW3701', 'DBC3DR4901',
        //     'DBC3AQ1701', 'DBC3TW4601', 'DBC3HM6801', 'DBC3CQU901', 'DBCMDIW201', 'DBCMAHL501', 'DBCMRVX201'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCMIJQ101', 'DBCMQYZ801', 'DBC3PRS901', 'DBC3JLMS01', 'DBC3TVZ701', 'DBC3BFZ701', 'DBCLAEQ001',
        //     'DBCLCDRV01', 'DBCLIR6801', 'DBCLAV0501', 'DBCLHPZ901', 'DBCLFUV601', 'DBCLJNT701', 'DBCLGKT101',
        //     'DBCLSY7801', 'DBCJGN2801', 'DBCKGWZ201', 'DBCLEILP01', 'DBCLJR4901', 'DBCMFKO801', 'DBCMCGHS01',
        //     'DBCMCFJQ01', 'DBCMACLR01', 'DBCMJR5701', 'DBCMAG2901', 'DBCMBV1601', 'DBCMP14901', 'DBCMCFHZ01',
        //     'DBCLABGK01', 'DBCNJNO001', 'DBCNG67801', 'DBCNAUZ201', 'DBCNADGJ01', 'DBCNAERZ01', 'DBCNBY1501'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCNCEL401', 'DBCNW06801', 'DBCOBINZ01', 'DBCOY25801', 'DBCOG04501', 'DBCOBPV701', 'DBCODPVZ01',
        //     'DBCOCDV701', 'DBCLQZ6801', 'DBCLOX3801', 'DBCOGKUV01', 'DBCOAMY301', 'DBCOIRZ601', 'DBCPHL2701',
        //     'DBCPEJY301', 'DBCPFP1201', 'DBCPFMZ201', 'DBCLCHMY01', 'DBCPAKNV01', 'DBCPAN1301', 'DBCPBUW501',
        //     'DBCPEMTY01', 'DBCPCKV501', 'DBCLDJ0601', 'DBCPBMV901', 'DBCQKS2701', 'DBCKHLY701', 'DBCQKST201',
        //     'DBCKAOU901', 'DBCRJPY801', 'DBCKFVWX01', 'DBCKV34501', 'DBCRHY6901', 'DBCRBFZ801', 'DBCRDFVY01'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCRAHNQ01', 'DBCRAKUZ01', 'DBCRDU5601', 'DBCRGPVX01', 'DBCRAPQ601', 'DBCRFJO501', 'DBCRHMS601',
        //     'DBCRPV6801', 'DBCRJPT801', 'DBCRL14601', 'DBCRAE6801', 'DBCRAKUX01', 'DBCRDKU301', 'DBCRPS3601',
        //     'DBCRKY2501', 'DBCRCFW301', 'DBCRIX0101', 'DBCRAFMQ01', 'DBCRKTZ601', 'DBCRPQX501', 'DBCRBEWZ01',
        //     'DBCRKPU801', 'DBCRDOU601', 'DBCRKW2801', 'DBCRCQU101', 'DBCKFJP301', 'DBCKRY5601', 'DBCREGQ401',
        //     'DBCRBDR001', 'DBCSHN1301', 'DBCSIQV801', 'DBCSGKN001', 'DBCSPQ4901', 'DBCKDKR801', 'DBCKEK3501'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCTCHT301', 'DBCTBFUZ01', 'DBCTCNQ901', 'DBCTJMPS01', 'DBCKBS2601', 'DBCKESY801', 'DBCTJNY201',
        //     'DBCTMTU601', 'DBCKGKW401', 'DBCJU06701', 'DBCUKNZ101', 'DBCUDFT701', 'DBCUBUX801', 'DBCUCLT201',
        //     'DBCUCDJS01', 'DBCJPTW301', 'DBCUTY8901', 'DBCJU13401', 'DBCVDI5801', 'DBCVHUV301', 'DBCVOWZ201',
        //     'DBCVGHT101', 'DBCJKW0301', 'DBCVDU1501', 'DBCVDGK701', 'DBCVUY6801', 'DBCVDLX201', 'DBCVADS401',
        //     'DBCVBY2501', 'DBCJFIV001', 'DBCVCPU501', 'DBCVBHT701', 'DBCWBL7901', 'DBCWBDH001', 'DBCWAFI901'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCWHJMX01', 'DBCWHOR701', 'DBCWELSY01', 'DBCWOT5601', 'DBCWADZ601', 'DBCJS23501', 'DBCJSU1201',
        //     'DBCJJMP701', 'DBCJD05701', 'DBCJFNQ501', 'DBCJBFHQ01', 'DBCJFGKV01', 'DBCIIMS001', 'DBCIBHO501',
        //     'DBCIP04501', 'DBCIEIY901', 'DBCIGM6701', 'DBCIMOQ001', 'DBCICDT901', 'DBBXBCY201', 'DBCISXZ001',
        //     'DBCIAPY901', 'DBB0PXZ901', 'DBB0HKU501', 'DBCIGKW501', 'DBCIGOZ201', 'DBB0DFNW01', 'DBBZAY4802',
        //     'DBBZHJ5602', 'DBCINS0101', 'DBCIOX1401', 'DBB0BN1401', 'DBB0BEVY01', 'DBCHLT4501', 'DBB1M68901'
        // );

        // $parent_sub_order_ids = array(
        //     'DBB1FR2801', 'DBB1UZ6901', 'DBB1HTXY01', 'DBB1BGP301', 'DBCHCEO901', 'DBCHHST101', 'DBCAABQ901',
        //     'DBCAAB4901', 'DBCAT13501', 'DBCAFHL101', 'DBCAJKNZ01', 'DBCADIN801', 'DBCAANV601', 'DBCHHPSU01',
        //     'DBCHDFHR01', 'DBCHIJQW01', 'DBCAJMQ601', 'DBCBINS701', 'DBCBRUZ501', 'DBCBFOVY01', 'DBCHBU2901',
        //     'DBCCFKM001', 'DBCHGR1201', 'DBCCEJM901', 'DBCCCGN001', 'DBCCJPYZ01', 'DBCCAEV301', 'DBCCL57901',
        //     'DBCDT78901', 'DBCDSXZ001', 'DBCDACKP01', 'DBCDILMR01', 'DBCDOSV301', 'DBCDADMQ01', 'DBCDAO7801'
        // );

        // $parent_sub_order_ids = array(
        //     'DBCHDGX601', 'DBCEU02601', 'DBCHAOT901', 'DBCELOV801', 'DBCHEZ7801', 'DBCEI05901', 'DBCEASW801',
        //     'DBCEMOWX01', 'DBCERTY201', 'DBCEDGR201', 'DBCEAL2401', 'DBCEEKUX01', 'DBCEMRX701', 'DBCEJVZ901',
        //     'DBCEHP6701', 'DBCEDFNQ01', 'DBCEFHX601', 'DBCEBDKN01', 'DBCFDPS901', 'DBCFKQ0801', 'DBCFHM0601',
        //     'DBCFEN0601', 'DBCFGORV01', 'DBCFAGVY01', 'DBCFHSY901', 'DBCFHPXY01', 'DBCFLRT501', 'DBCFBJR801',
        //     'DBCFN14801', 'DBCGRY1801', 'DBCGFOY001', 'DBCGBCFG01', 'DBCGEFS001', 'DBCGILQY01', 'DBCGCK3601'
        // );

		// $sub_orders = array();

        foreach ($parent_sub_order_ids as $parent_sub_order_id) {
            $parent_sub_order = SubOrder::where('unique_suborder_id', $parent_sub_order_id)->first();
            if(count($parent_sub_order) > 0){
            	if($parent_sub_order->parent_sub_order_id == 0){
            		if(count($parent_sub_order->child_sub_orders) > 0){
            			foreach ($parent_sub_order->child_sub_orders as $child_sub_order) {
	            			$sub_order = $child_sub_order;
	            			break;
	            		}
            		}else{
            			$sub_order = $parent_sub_order;
            		}
            	}
            	// $sub_orders[] = $sub_order;
            	if($sub_order->return == 1){

        			// Update Sub-Order Status
                	$this->suborderStatus($sub_order->id, 26); // Full order racked at Destination Hub

        		}
            }
        }

        return 'done';

    }

    public function ajkerDealOrderIdUpdate(Request $request){

        if($request->from_date){
            $from_date = $request->from_date;
        }else{
            $from_date = date('Y-m-d');
        }

        if($request->to_date){
            $to_date = $request->to_date;
        }else {
            $to_date = date('Y-m-d');
        }

        $orders = Order::where('store_id', 83)->WhereBetween('created_at', array($from_date.' 00:00:01',$to_date.' 23:59:59'))->get();

        if(count($orders) > 0){

            $ajker_deal_orders = array();
            foreach ($orders as $order) {
                $ajker_deal_orders[] = array('unique_order_id' => $order->unique_order_id, 'merchant_order_id' => $order->merchant_order_id);
            }

            if(count($ajker_deal_orders) > 0){
                return $this->ajkerDealOrderUpdate($ajker_deal_orders);
            }else{
                return 'AjkerDeal orders not found.';
            }

        }else{
            return 'No orders found.';
        }

    }

    public function goToPickupCancel(){

        $sub_orders = array('DBEZSWY401', 'DBEZSWY401', 'DBEZSWY401', 'DBEZSWY401', 'DBEZSWY401', 'DBEZSWY401', 'DBEZSWY401');

        try {

            DB::beginTransaction();

                foreach ($sub_orders as $unique_suborder_id) {
                    $sub_order = SubOrder::where('unique_suborder_id', $unique_suborder_id)->first();
                    // Update Sub-Order Status
                    $this->suborderStatus($sub_order->id, 13); // Pickup order Cancelled
                }

            DB::commit();

            return 'Success';

        } catch (Exception $e) {
            DB::rollback();

            return 'Failed';
        }

    }

    public function goToDeliveryPanel(){
        $sub_orders = array('DBGNAPU901','DBGNBCPY01','DBGNPT1401','DBGNBOU801','DBGNGIJW01','DBGNEKNY01','DBGNL57901','DBGNO36701','DBGNOU0301','DBGNAX2501','DBGNELT201','DBGNABY501','DBGNACFS01','DBGNO03901','DBGNHJR401','DBGNTX3501','DBGLX23702','DBGLIJL702','DBGMBKPR01','DBGLU13902','DBGLEGPV02','DBGLBU0702','DBGLBKU302','DBGLBN7902','DBGLNV0602','DBGMFOP101','DBGLTV5702','DBGLFXZ202','DBGLQT6902','DBGLEHX302','DBGMHLPR02','DBGMZ13802','DBGMCO0102','DBGMDIJT02','DBGMBYZ202','DBGLANO302','DBGLTW7902','DBGKFQV003','DBGJBGMV04','DBGJERW303','DBGMHIY401','DBGMEFR401');

        try {

            DB::beginTransaction();

                foreach ($sub_orders as $unique_suborder_id) {
                    $sub_order = SubOrder::where('unique_suborder_id', $unique_suborder_id)->first();

                    if(count($sub_order) > 0){
                        // if($sub_order->source_hub_id == $sub_order->destination_hub_id){
                            // Update Sub-Order Status
                            $this->suborderStatus($sub_order->id, 26); // Go to Delivery Panel
                        // }
                    }
                }

            DB::commit();

            return 'Success';

        } catch (Exception $e) {
            DB::rollback();

            return 'Failed';
        }
    }

    public function updateSubOrderStatus($sub_order_id, $status){
        return $this->suborderStatus($sub_order_id, $status);
    }

    public function testFcm($sub_order_unique_id){
        $sub_order = SubOrder::where('unique_suborder_id', $sub_order_unique_id)->first();
        return $this->fcm_task_req($sub_order->id);
    }

}
