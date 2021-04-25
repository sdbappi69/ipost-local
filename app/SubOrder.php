<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model {

    protected $table = 'sub_orders';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function products() {
        return $this->hasMany('App\OrderProduct');
    }

    public function product() {
//        return $this->belongsTo('App\OrderProduct', 'id', 'sub_order_id');
        return $this->belongsTo('App\OrderProduct', 'unique_suborder_id', 'product_unique_id');
    }

    public function order() {
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }

    public function source_hub() {
        return $this->belongsTo('App\Hub', 'source_hub_id', 'id');
    }

    public function destination_hub() {
        return $this->belongsTo('App\Hub', 'destination_hub_id', 'id');
    }

    public function next_hub() {
        return $this->belongsTo('App\Hub', 'next_hub_id', 'id');
    }

    public function current_hub() {
        return $this->belongsTo('App\Hub', 'current_hub_id', 'id');
    }

    public function responsible() {
        return $this->belongsTo('App\User', 'responsible_user_id', 'id');
    }

    public function deliveryman() {
        return $this->belongsTo('App\User', 'deliveryman_id', 'id');
    }

    public function suborder_status() {
        return $this->belongsTo('App\Status', 'sub_order_status', 'code');
    }

    public function suborder_last_status() {
        return $this->belongsTo('App\Status', 'sub_order_last_status', 'code');
    }

    public function history() {
        return $this->hasMany('App\OrderLog')->orderBy('id', 'desc');
    }

    public function dTask() {
        return $this->belongsTo('App\DeliveryTask', 'unique_suborder_id', 'unique_suborder_id');
    }

    public function child_sub_orders() {
        return $this->hasMany('App\SubOrder', 'parent_sub_order_id', 'id')->orderBy('id', 'desc');
    }

    public function parent_sub_order() {
        return $this->belongsTo('App\SubOrder', 'parent_sub_order_id', 'id');
    }

    public function allComplains() {
        return $this->hasMany('App\CustomerSupportModel\Complain', 'sub_order_id', 'suborder_id');
    }

    public function feedback() {
        return $this->hasOne('App\CustomerSupportModel\FeedBack', 'unique_suborder_id', 'unique_suborder_id');
    }

    public function deliveryTask() {
        return $this->hasOne(ConsignmentTask::class, 'sub_order_id', 'id')->whereIn('task_type_id', [2, 4, 6])->orderBy("id", 'desc');
    }

    public function picking_task() {
        return $this->hasOne(ConsignmentTask::class, 'sub_order_id', 'id')->whereIn('task_type_id', [1, 5])->orderBy("id", 'desc');
    }

    public function orderLogs() {
        return $this->hasMany(OrderLog::class, "sub_order_id", 'id');
    }

}
