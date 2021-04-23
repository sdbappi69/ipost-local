<?php
namespace App\Http\Traits;

use App\MailQueue;
use App\PickingTask;
use App\OrderProduct;
use App\SubOrder;
use App\Merchant;
use App\User;

trait MailsTrait {

    public function setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc) {
    	$mail = new MailQueue();
    	$mail->source = $source;
    	$mail->mail_type = $mail_type;
    	$mail->ref_id = $ref_id;
    	$mail->ref_table = $ref_table;
    	$mail->to = $to;
    	$mail->subject = $subject;
    	$mail->body = $body;
    	$mail->cc = $cc;
    	$mail->bcc = $bcc;
    	$mail->created_by = $user_id;
    	$mail->updated_by = $user_id;
		$mail->save();
        return $mail->id;
    }

    // Return Complete
    public function mailReturnComplete($product_unique_id){

        $data = OrderProduct::select('order_product.id', 'm.name', 'm.email')
                            ->join('order AS o', 'o.id', '=', 'order_product.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('order_product.product_unique_id', $product_unique_id)
                            ->first();

        if(count($data) > 0){

            $source = 'system';
            $mail_type = 'Return Complete';
            $ref_id = $data->id;
            $ref_table = 'order_product';
            $to = $data->email;
            $subject = 'Return Complete';
            $cc = '';
            $bcc = '';
            $user_id = auth()->user()->id;

            $body = $product_unique_id;

            return $this->setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc);

        }else{
            return 0;
        }

    }

    // Pertial Picked
    public function mailPertialPicked($product_unique_id){
        
        $data = OrderProduct::select('order_product.id', 'm.name', 'm.email')
                            ->join('order AS o', 'o.id', '=', 'order_product.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('order_product.product_unique_id', $product_unique_id)
                            ->first();

        if(count($data) > 0){

            $source = 'system';
            $mail_type = 'Pertial Product Picked';
            $ref_id = $data->id;
            $ref_table = 'order_product';
            $to = $data->email;
            $subject = 'Pertial Product Picked';
            $cc = '';
            $bcc = '';
            $user_id = auth()->user()->id;

            $body = $product_unique_id;

            return $this->setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc);

        }else{
            return 0;
        }

    }

    // Full Picked
    public function mailFullPicked($product_unique_id){

        $data = OrderProduct::select('order_product.id', 'm.name', 'm.email')
                            ->join('order AS o', 'o.id', '=', 'order_product.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('order_product.product_unique_id', $product_unique_id)
                            ->first();

        if(count($data) > 0){

            $source = 'system';
            $mail_type = 'Full Product Picked';
            $ref_id = $data->id;
            $ref_table = 'order_product';
            $to = $data->email;
            $subject = 'Full Product Picked';
            $cc = '';
            $bcc = '';
            $user_id = auth()->user()->id;

            $body = $product_unique_id;

            return $this->setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc);

        }else{
            return 0;
        }
    }

    // Full Delivered
    public function mailFullDelivered($unique_suborder_id){
        
        $data = SubOrder::select('sub_orders.id', 'm.name', 'm.email')
                            ->join('order AS o', 'o.id', '=', 'sub_orders.order_id')
                            ->join('stores AS s', 's.id', '=', 'o.store_id')
                            ->join('merchants AS m', 'm.id', '=', 's.merchant_id')
                            ->where('sub_orders.unique_suborder_id', $unique_suborder_id)
                            ->first();

        if(count($data) > 0){

            $source = 'system';
            $mail_type = 'Product Delivered';
            $ref_id = $data->id;
            $ref_table = 'sub_orders';
            $to = $data->email;
            $subject = 'Product Delivered';
            $cc = '';
            $bcc = '';
            $user_id = auth()->user()->id;

            $body = $unique_suborder_id;

            return $this->setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc);

        }else{
            return 0;
        }

    }

    // New User
    public function mailNewUser($user_id, $password){

        $data = User::whereStatus(true)->where('id', $user_id)->first();

        if(count($data) > 0){

            $source = 'system';
            $mail_type = 'New User';
            $ref_id = $data->id;
            $ref_table = 'users';
            $to = $data->email;
            $subject = 'User Credentials';
            $cc = '';
            $bcc = '';
            $user_id = auth()->user()->id;

            $body = $password;

            return $this->setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc);

        }else{
            return 0;
        }

    }

    // Edit User
    public function mailEditUser($user_id, $password){
        
        $data = User::whereStatus(true)->where('id', $user_id)->first();

        if(count($data) > 0){

            $source = 'system';
            $mail_type = 'Edit User';
            $ref_id = $data->id;
            $ref_table = 'users';
            $to = $data->email;
            $subject = 'User Credentials';
            $cc = '';
            $bcc = '';
            $user_id = auth()->user()->id;

            $body = $password;

            return $this->setMail($user_id, $source, $mail_type, $ref_id, $ref_table, $to, $subject, $body, $cc, $bcc);

        }else{
            return 0;
        }

    }
    
}
