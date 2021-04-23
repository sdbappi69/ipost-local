<?php

/**
 * API Routes are goes here
 */
// Route::group(['namespace' => 'Api'], function() {
//
//     Route::resource('api/charge-calculator', 'ChargeCalculatorController');
//
// });


//Route::post('api/charge-calculator', 'Api\ChargeCalculatorController@index');

/**By Johnny**/
Route::post('api/charge-calculator', 'Api\ChargeCalculatorController@chargeCalculator');

/**rider registration by johnny**/
Route::group(['prefix' => 'api/v2'], function () {
    Route::group(['namespace' => 'ApiV2'], function () {
        Route::post('rider-registration', 'RiderRegistrationController@index');
        Route::post('rider-registration-submit', 'RiderRegistrationController@storeRider');
        Route::post('rider-otp-verify', 'RiderRegistrationController@otpVerify');
    });
});


Route::group(['prefix' => 'api/v1'], function () {
    Route::group(['namespace' => 'Api'], function () {
        // masud merchant login
        Route::post('/merchant/login', 'Merchant\MerchantApiLoginController@login');
        //end masud
        Route::post('/rider/login', 'UsersApiLoginController@login');

        /**
         * Client
         */
        Route::post('/client/registration', 'ClientController@registration');
        Route::post('/client/login', 'ClientController@login');
        Route::post('/client/order/history/{id}', 'ClientController@order_history');
        Route::post('/user/order/history/{id}', 'ClientController@open_order_history');

        Route::resource('/client', 'ClientController');

        /**
         * Open Calculator
         */
        Route::get('charge/calculator/{product_category_id}/{pickup_zone_id}/{delivery_zone_id}/{quantity}/{width}/{height}/{length}', 'ChargeCalculatorController@open');

        /**
         * Product Categories
         */
        Route::get('product/categories/', 'ProductCategoriesController@index');

        /**
         * Product Location
         */
        Route::get('location/divisions/{country_id}', 'LocationController@states');
        Route::get('location/cities/{division_id}', 'LocationController@cities');
        Route::get('location/zones/{city_id}', 'LocationController@zones');
        //Route::get('location/countries', 'LocationController@countries');
    });
});

Route::group(['prefix' => 'api/v1', 'middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'Api'], function () {
        // masud 
        Route::group(['namespace' => 'Merchant'], function () {

            // Product
            Route::post('merchant/product/submit-product', 'ProductController@store_product');
            Route::post('merchant/product/{product_id}/update', 'ProductController@update_product');
            Route::post('merchant/product/{product_id}/destroy', 'ProductController@destroy_product');


            // Route::post('merchant/location-and-store-list', 'LocationController@location');
            Route::post('merchant/resource', 'ResourceController@resource');
            Route::post('merchant/submit-order', 'MerchantOrderController@store_order');
            Route::post('merchant/order/{order_id}/edit', 'MerchantOrderController@edit_order');
            Route::post('merchant/order/{order_id}/update', 'MerchantOrderController@update_order');
            Route::post('merchant/order/{order_id}/view', 'MerchantOrderController@view_order');
            Route::post('merchant/order/list', 'MerchantOrderController@index_order');
            Route::post('merchant/order/draft', 'MerchantOrderController@draft_order');
            Route::post('merchant/order/approve', 'MerchantOrderController@draft_submit');

            Route::post('merchant/charge-calculator', 'ChargeCalculatorController@charge_calculator');

            // Route::post('merchant/product/submit-product', function(){
            //     return 'Mahfuz';
            // });

            Route::post('merchant/dashboard', 'DashboardController@index');
        });
        //end Masud
        Route::resource('/users', 'UsersApiController');

        /**
         * Picking
         */
        Route::post('/tasks/reconciliation/{consignment_unique_id}', 'TaskController@reconciliation_req');
        Route::post('/tasks/completed/all', 'TaskController@picking_delivery_completed');
        Route::post('/tasks/picking', 'TaskController@index');
        Route::post('/tasks/{id}/{type}', 'TaskController@show');
        Route::post('/tasks/picking/{id}/{type}/start', 'TaskController@do_start_picking');
        Route::post('/tasks/picking/{id}/{type}/complete', 'TaskController@do_complete_picking');
        Route::post('/tasks/picking/{task_id}/verify', 'TaskController@picking_verify');


        // Route::resource('/tasks', 'TaskController');

        /**
         * Delivery
         */
        Route::post('/delivery/tasks', 'DeliveryController@index');
        Route::post('/delivery/tasks/{id}', 'DeliveryController@show');
        Route::post('/delivery/tasks/{id}/start', 'DeliveryController@do_start_delivery');
        Route::post('/delivery/tasks/{id}/complete', 'DeliveryController@do_complete_delivery');
        Route::post('/delivery/tasks/{task_id}/verify', 'DeliveryController@delivery_verify');
        Route::post('/delivery/tasks/{task_id}/completed', 'DeliveryController@delivery_complete');
        Route::resource('/delivery', 'DeliveryController');


        Route::post('/location', 'LocationController@store');

        Route::post('/reasons', 'ReasonController@index');
    });
});

// Rider New API
Route::group(['prefix' => 'api/v2'], function () {
    Route::group(['namespace' => 'ApiV2'], function () {
        Route::post('/rider/login', 'UsersApiLoginController@login');
        Route::post('zpay-ipn', 'ZpayController@zpay_ipn');

        /**reset password by johnny**/
        Route::post('/forget-password', 'UsersApiLoginController@forgetPassword');
        Route::post('/new-password', 'UsersApiLoginController@newPassword');
    });
});

Route::group(['prefix' => 'api/v2', 'middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'ApiV2'], function () {

        // Route::post('/rider/tasks', function() {
        //     return 0;
        // });

        Route::post('/rider/tasks', 'TaskController@tasks_list');
        Route::post('/rider/task/start', 'TaskController@task_start');
        Route::post('/rider/task', 'TaskController@task_detail');
        Route::post('/rider/task/submit', 'TaskController@task_submit');
        Route::post('/rider/reasons', 'ReasonController@index');
        Route::post('/rider/tasks/reconciliation', 'TaskController@reconciliation_req');
        Route::post('/rider/location', 'TaskController@location');
        Route::post('/rider/location/verify', 'TaskController@location_verify');
        Route::post('/rider/consignments', 'TaskController@consignments');

        /**reset password by johnny**/
        Route::post('reset-password', 'UsersApiLoginController@resetPassword');
        Route::post('profile-update-request', 'RiderRegistrationController@updateProfile');
    });
});

/** api version 3 by johnny**/
Route::group(['prefix' => 'api/v3', 'middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'ApiV3'], function () {
        Route::post('charge-calculator', 'ChargeCalculatorController@index');

        Route::post('/rider/online-status', 'UsersApiLoginController@online_status');
        
        Route::post('/rider/tasks', 'TaskController@tasks_list');
        Route::post('/rider/task/start', 'TaskController@task_start');
        Route::post('/rider/task/details', 'TaskController@task_detail');
        Route::post('/rider/task/submit', 'TaskController@task_submit');
        Route::post('/rider/resources', 'ReasonController@index');
        Route::post('/rider/reasons', 'ReasonController@reasons');
        Route::post('/rider/tasks/reconciliation', 'TaskController@reconciliation_req');
        Route::post('/rider/location', 'TaskController@location');
        Route::post('/rider/consignments', 'TaskController@consignments');
        Route::post('/rider/history', 'TaskController@history');

        Route::post('reset-password', 'UsersApiLoginController@resetPassword');
        Route::post('profile-update-request', 'RiderRegistrationController@updateProfile');

        // Notification
        Route::post('/rider/pull-notification', 'OrderNotificationController@pullNotification');
        Route::post('/rider/decline-notification', 'OrderNotificationController@declineNotification');
        Route::post('/rider/accept-notification', 'OrderNotificationController@acceptNotification');
    });
});

Route::group(['prefix' => 'api/v3'], function () {
    Route::group(['namespace' => 'ApiV3'], function () {
        Route::post('/rider/login', 'UsersApiLoginController@login');

        Route::post('/forget-password', 'UsersApiLoginController@forgetPassword');
        Route::post('/new-password', 'UsersApiLoginController@newPassword');
        
        Route::post('rider-registration', 'RiderRegistrationController@index');
        Route::post('rider-registration-submit', 'RiderRegistrationController@storeRider');
        Route::post('rider-otp-verify', 'RiderRegistrationController@otpVerify');
    });
});


Route::group(['prefix' => 'lpapi/merchant'], function () {
    Route::group(['namespace' => 'LpApi\Merchant'], function () {

        Route::post('/login', 'LoginController@login');

        // Product
        Route::post('/product/submit-product', 'ProductController@store_product');
        Route::post('/product/{product_id}/update', 'ProductController@update_product');
        Route::post('/product/{product_id}/destroy', 'ProductController@destroy_product');


        // Route::post('merchant/location-and-store-list', 'LocationController@location');
        Route::post('/resource', 'ResourceController@resource');
        Route::post('/submit-order', 'MerchantOrderController@store_order');
        Route::post('/submit-order-bulk', 'MerchantOrderController@store_order_bulk');
        Route::post('/order/{order_id}/edit', 'MerchantOrderController@edit_order');
        Route::post('/order/{order_id}/update', 'MerchantOrderController@update_order');
        Route::post('/order/{order_id}/view', 'MerchantOrderController@view_order');
        Route::post('/order/list', 'MerchantOrderController@index_order');
        Route::post('/order/draft', 'MerchantOrderController@draft_order');
        Route::post('/order/approve', 'MerchantOrderController@draft_submit');

        Route::post('/charge-calculator', 'ChargeCalculatorController@charge_calculator');

    });
});

Route::group(['prefix' => 'api/v2'], function () {
    Route::group(['namespace' => 'ApiV2'], function () {
        // Charge Calculator iPost
        Route::post('charge-calculator', 'ChargeCalculatorController@index');

        // masud merchant login
        Route::post('/merchant/login', 'Merchant\MerchantApiLoginController@login');
    });
});

Route::group(['prefix' => 'api/v2', 'middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'ApiV2'], function () {
        Route::group(['namespace' => 'Merchant'], function () {

            Route::post('merchant/resource', 'ResourceController@resource');
            Route::post('merchant/submit-order', 'MerchantOrderController@store_order');
            Route::post('merchant/view-order', 'MerchantOrderController@view_order');

            Route::post('merchant/charge-calculator', 'ChargeCalculatorController@charge_calculator');

            Route::post('merchant/submit-custom-order', 'MerchantCustomOrderController@store_order');
            
            /* create by johnny */
            Route::post('merchant/pickup-location', 'ResourceController@storePickupLocation');

        });
    });
});
