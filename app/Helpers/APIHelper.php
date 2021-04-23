<?php
/**
* API helper
*/
namespace App\Helpers;

use DB;
use App\UserType;
use App\Country;
use App\State;
use App\City;
use App\Zone;
use App\PickingLocations;
use App\PickingTimeSlot;
use App\ProductCategory;

class APIHelper
{
   /**
   * Get user type table data by id
   * @parm $id
   * @return array user type data
   */
   public static function get_user_type_by_id( $id = null )
   {
      return UserType::find($id);
   }

   /**
   * Get country data by id
   * @parm $id
   * @return array country data
   */
   public static function get_country_by_id( $id = null )
   {
      return Country::find($id);
   }

   /**
   * Get state data by id
   * @parm $id
   * @return array state data
   */
   public static function get_state_by_id( $id = null )
   {
      return State::find($id);
   }

   /**
   * Get city data by id
   * @parm $id
   * @return array city data
   */
   public static function get_city_by_id( $id = null )
   {
      return City::find($id);
   }

   /**
   * Get zone data by id
   * @parm $id
   * @return array zone data
   */
   public static function get_zone_by_id( $id = null )
   {
      return Zone::find($id);
   }

   /**
   * Get pickup_location data by id
   * @parm $id
   * @return array pickup_location data
   */
   public static function get_pickup_location_by_id( $id = null )
   {
      return PickingLocations::find($id);
   }

   /**
   * Get picking_time_slot data by id
   * @parm $id
   * @return array picking_time_slot data
   */
   public static function get_picking_time_slot_by_id( $id = null )
   {
      return PickingTimeSlot::find($id);
   }

   /**
   * Get product_category data by id
   * @parm $id
   * @return array product_category data
   */
   public static function get_product_category_by_id( $id = null )
   {
      return ProductCategory::find($id);
   }

}
