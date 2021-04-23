<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

/* Note :
      Author : MMM
      There is a directory in app/Http/extraRoute
      where extra route file created
      customer suppport is belong there
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    } else {
        return view('auth.login');
    }
});

Route::get('/clearallcache', function () {

    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('clear-compiled');

    echo "ok";

});

Route::get('/tm_try', function () {

    Artisan::call('command:task_manage');

    echo "ok";

});

Route::get('/otp','ApiV3\TaskController@otp'); // test function for QA

Route::get('/AgingData', function () {
    Artisan::call('command:Aging');
});
Route::get('/TatData', function () {
    Artisan::call('command:Tat');
});
        
    Route::get('suborder-details/{unique_suborder_id}','Order\SubOrderDetailsController@subOrderDetails');

Route::get('/jsonview', function () {

    $sub_order_notes = array(
        'picked' => '0000-00-00 00:00:00',
        'delivered' => '0000-00-00 00:00:00',
        'pickup_requested' => '0000-00-00 00:00:00',
        'delivery_requested' => '0000-00-00 00:00:00',
        'raked_on_pickup' => '0000-00-00 00:00:00',
        'raked_on_destination' => '0000-00-00 00:00:00',
        'tat' => 'null',
        'pickup_aging' => 'null',
        'delivery_aging' => 'null',
        'delivery_attempt_aging' => 'null',
        'trip_aging' => 'null',
        'latest_picking_attempt' => '0000-00-00 00:00:00',
        'latest_picking_reason' => 'null',
        'latest_delivery_attempt' => '0000-00-00 00:00:00',
        'latest_delivery_reason' => 'null'
    );

    return json_encode($sub_order_notes);

});

Route::get('/test', 'TestController@index');

Route::auth();

Route::get('/home', 'HomeController@index');

Route::post('/fetch-locations', function (\Illuminate\Http\Request $request) {
    $modelName = 'App\\' . $request->input('search_model');
    return $modelName::where($request->input('attr_name'), $request->input('search_id'))->select('name', 'id')->get();
});

Route::group(['middleware' => ['auth']], function () {
    
    Route::get('/hub-manager-nav', 'NavController@hubManager');
    Route::get('/head-account-nav', 'NavController@headAccountManager');

    Route::get('/dashboard-export/{type}', 'HomeController@dashboardexport');

    Route::group(['namespace' => 'Settings'], function () {

        Route::resource('/settings', 'SettingsController');
        Route::resource('/country', 'CountryController');
        Route::resource('/state', 'StateController');
        Route::resource('/city', 'CityController');
        Route::resource('/zone', 'ZoneController');
        Route::resource('/picking-time', 'PickingTimeController');
    });

    Route::group(['namespace' => 'Vehicle'], function () {

        Route::resource('/vehicle-type', 'VehicleTypeController');
        Route::resource('/vehicle', 'VehicleController');
        Route::resource('/driver', 'DriverController');
    });

    Route::group(['namespace' => 'Profile'], function () {

        Route::resource('/profile', 'ProfileController');
        Route::post('/profile-photo/{id}', 'ProfileController@updatePhoto');
        Route::post('/change-password/{id}', 'ProfileController@updatePass');
    });

    /** Johnny **/
    Route::group(['namespace' => 'Rider'], function () {
        Route::resource('/rider', 'RiderController');
        Route::get('/rider-profile-update-request', 'RiderController@updateRequests');
    });

    Route::group(['namespace' => 'User'], function () {

        Route::resource('/user', 'UserController');
        Route::get('/users/data', 'UserController@userList');
        Route::post('/users/storedata', 'UserController@storeUserList');
        Route::get('reference/{user_type_id}', 'UserController@references');

        Route::resource('/role', 'RoleController');
        Route::resource('/permission', 'PermissionController');
    });
    /// masud start
    Route::post('/merchant/create-user', 'Merchant\UserController@create_user');
    Route::put('/merchant/edit-user', 'Merchant\UserController@edit_user');
    //end masud
    Route::group(['namespace' => 'Merchant'], function () {

        Route::resource('/merchant', 'MerchantController');
        Route::get('/merchantexport/{type}', 'MerchantController@merchantexport');
        Route::get('/merchants/data', 'MerchantController@merchantList');

        Route::resource('/store', 'StoreController');
        Route::get('/stores/data', 'StoreController@storeList');

        Route::resource('/warehouse', 'WarehouseController');
        // Route::get('/warehouses/data', 'WarehouseController@warehouseList');

        Route::post('/warehouses/data', 'WarehouseController@warehouseList');

        Route::resource('/merchant-order', 'MerchantOrderController');
    });

    Route::group(['namespace' => 'Charge'], function () {

        Route::resource('/charge', 'ChargeController');
        Route::resource('/charge-model', 'ChargeModelController');
        Route::get('chargecod/create', 'ChargeController@createcod');
        Route::get('chargecod/{id}/edit', 'ChargeController@editcod');
        Route::get('bulk_charge/{order_id}/{width}/{height}/{length}/{weight}/{unit_price}/{pickup_location_id}', 'ChargeController@bulk_charge');

        // New
        Route::get('product-category-charge/{category_id}', 'NewChargeController@view');
        Route::get('charge-remove/{charge_id}', 'NewChargeController@delete');
        Route::post('product-category-charge-submit', 'NewChargeController@save');
        Route::get('product-category-approval', 'NewChargeController@categoryApproval');

        //By Johnny
        Route::get('product-category-charge/v2/{category_id}', 'v2\NewChargeController@view');
        Route::post('product-category-charge-submit/v2', 'v2\NewChargeController@save');
        Route::post('product-category-charge-update/v2', 'v2\NewChargeController@update');
        Route::get('charge-remove/v2/{charge_id}', 'v2\NewChargeController@remove');
        Route::get('product-category-charge-approval/v2', 'v2\NewChargeController@approveCharge');
        Route::get('category-charge-approved/v2/{id}', 'v2\NewChargeController@approved');
        Route::post('category-charge-approve-all/v2/', 'v2\NewChargeController@approvedAll');
        
        // Discount
        Route::resource('/discount', 'DiscountController');

        //price-approval
        Route::get('price-approval', 'NewChargeController@priceApproval');
        Route::get('approvePrice/{id}', 'NewChargeController@approvePrice');

    });

    Route::group(['namespace' => 'Product'], function () {

        Route::resource('/product', 'ProductController');
        Route::post('/bulkproduct', 'ProductController@bulkproduct');
        Route::get('pick-up-slot/{day}', 'ProductController@pick_up_slot');
        Route::resource('/product-category', 'ProductCategoryController');

        // Discount Reset
        // Route::get('discount_reset/{order_id}', 'ProductController@discount_reset');
    });

    Route::group(['namespace' => 'Order'], function () {

        Route::resource('/merchant-orderv2', 'MerchantOrderControllerV2');

        Route::resource('/merchant-order', 'MerchantOrderController');
        Route::get('/merchant-orderexport/{type}', 'MerchantOrderController@orderexport');
        Route::get('/merchant-order-draft', 'MerchantOrderController@draft');
        Route::get('/merchant-order-draftv2', 'MerchantOrderControllerV2@draft');

        Route::get('/merchant-order-bulk', 'BulkOrderController@bulk');
        Route::post('/merchant-order-bulk-submit', 'BulkOrderController@bulk_submit');

        Route::post('/merchant-order-draft-submit', 'MerchantOrderController@draft_submit');
        Route::post('/merchant-order-draft-remove', 'MerchantOrderController@draft_remove');
        Route::resource('/verify-order', 'VerifyOrderController');
        Route::resource('/hub-order', 'HubOrderController');
        Route::get('/hub-orderexport/{type}', 'HubOrderController@orderexport');

        Route::resource('/order', 'OrderController');
        Route::get('/orderexport/{type}', 'OrderController@orderexport');
        Route::get('/order-draftv2', 'OrderDraftController@draft');

        Route::resource('/assign-pickup', 'AssignPickupController');
        Route::resource('/queued-picked', 'QueuedPickedController');

        Route::resource('/receive-picked', 'ReceivePickedController');
        Route::get('/total-receive-picked', 'ReceivePickedController@totalReceived');
        Route::post('/receive-picked-bulk', 'ReceivePickedController@bulk_verify');
        Route::get('addsuborder/{unique_order_id}', 'ReceivePickedController@addsuborder');
        Route::get('update_product_suborder/{product_unique_id}/{suborder_id}', 'ReceivePickedController@update_product_suborder');

        Route::resource('/queued-shipping', 'QueuedShippingController');
        // Route::resource('/transfer-suborder', 'QueuedShippingController');
        Route::resource('/hub-receive', 'HubReceiveController');
        Route::resource('/assign-delivery', 'AssignDeliveryController');
        Route::resource('/transfer-product', 'TransferProductController');
        Route::resource('/receive-transferd', 'TransferProductReceive');
        Route::resource('/accept-picked', 'AcceptPickedController');
        Route::resource('/accept-suborder', 'AcceptSuborderController');

        Route::resource('/return-pickup', 'ReturnPickupController');
        Route::resource('/return-delivery', 'ReturnDeliveryController');
        
        Route::get('receive-prodcut','HubReceiveController@receiveProduct');
        Route::post('received-varified-suborder','HubReceiveController@receivedAndVarified');
        Route::post('receive-reject-suborder','HubReceiveController@receiveAndReject');
    });

    Route::group(['namespace' => 'Hub'], function () {

        Route::resource('/hub', 'HubController');
        Route::get('office-delivery-list', 'DeliveryOfficeController@index');
        Route::get('delivery-from-office/{id}', 'DeliveryOfficeController@delivery');
        Route::post('confirm-office-delivery/{id}', 'DeliveryOfficeController@confirmDelivery');
        
    });

    Route::group(['namespace' => 'Trip'], function () {

        Route::resource('/trip', 'TripController');
        Route::post('/trip_load/{id}', 'TripController@tripLoad');
        Route::get('/trip_start/{id}', 'TripController@tripStart');
        Route::get('/trip_end/{id}', 'TripController@tripEnd');
        Route::get('/on_trip/{id}', 'TripController@onTrip');
        Route::get('/trip_cancel/{id}', 'TripController@tripCancel');

        Route::resource('/transfer', 'TransferController');

        // TripConsignment
        Route::get('/tripconsignments-trip', 'TripController@trip');
        Route::post('/tripconsignments-trip-submit', 'TripController@trip_submit');
        Route::post('/update-tripconsignments-trip-submit', 'TripController@trip_submit_load');
        Route::get('/remove-from-trip/{trip_id}/{sub_order_id}', 'TripController@remove_from_trip');

        Route::post('/add-trip-cart', 'CartTripController@add_trip_cart');
        Route::post('/add-bulk-trip-cart', 'CartTripController@add_bulk_trip_cart');
        Route::get('/remove-trip-cart/{unique_suborder_id}', 'CartTripController@remove_trip_cart');

        Route::get('/triprunsheet/{id}', 'TripController@trip_run_sheet');

        Route::resource('/trip-map', 'TripMapController');
        Route::get('/trip-map/{start_hub_id}/{end_hub_id}', 'TripMapController@edit');
    });

    Route::group(['namespace' => 'Reconciliation'], function () {

        // Route::resource('/reconciliation', 'ReconciliationController');
        Route::get('/reconciliation/{id}/{type}', 'ReconciliationController@index');
        Route::get('/reconciliation-picking-done/{id}', 'ReconciliationController@picking_done');
        Route::get('/reconciliation-delivery-done/{id}', 'ReconciliationController@delivery_done');
        //new
        Route::post('/reconciliation-update-picking', 'ReconciliationController@update_picking');
        Route::post('/reconciliation-update-delivery', 'ReconciliationController@update_delivery');
        Route::get('/consignments-details/{id}/{type}/{details}', 'ReconciliationController@index');
    });

    Route::get('location/states/{country_id}', 'LocationController@states');
    Route::get('location/cities/{state_id}', 'LocationController@cities');
    Route::get('location/zones/{city_id}', 'LocationController@zones');
    Route::get('location/zonebound/{lat}/{lng}', 'LocationController@zonebound');
    Route::group(['namespace' => 'Vehicle'], function () {
        Route::get('vehicle/vehicle/{vehicle_type_id}', 'VehicleController@vehicles');
        // Route::get('trip/vehicle/vehicle/{vehicle_type_id}', 'VehicleController@vehicles');
    });

    /**
     * Ware House Management
     */
    Route::group(['namespace' => 'Shelf'], function () {
        Route::resource('/shelf', 'ShelfController');
        Route::get('/shelfdata/products', 'ShelfController@product_lists')->name('shelf_products');
    });

    Route::group(['namespace' => 'Rack'], function () {
        Route::resource('/rack', 'RackController');
        Route::get('/rackdata/products', 'RackController@product_lists')->name('rack_products');
    });

    Route::get('/maps/{latitude}/{longitude}', 'MapsController@index');
    Route::get('/suborder-invoice/{unique_suborder_id}', 'Consignments\ConsignmentsControllerAll@suborder_invoice');
    Route::get('/common-awb-single/{sub_order_id}', 'Consignments\ConsignmentsControllerAll@common_awb_single');
    Route::get('/common-invoice-single/{sub_order_id}', 'Consignments\ConsignmentsControllerAll@common_invoice_single');
    Route::get('/common-awb-multi/{consignment_id}', 'Consignments\ConsignmentsControllerAll@common_awb_multi');
    Route::get('/common-invoice-multi/{consignment_id}', 'Consignments\ConsignmentsControllerAll@common_invoice_multi');

    /// masud start
    Route::post('/merchant/create-user', 'Merchant\UserController@create_user');
    Route::put('/merchant/edit-user', 'Merchant\UserController@edit_user');

    /// Consignments

    Route::group(['namespace' => 'Consignments'], function () {
        // Consignment pick up
        Route::get('/consignments-pick-up', 'ConsignmentsPickUpController@pick_up');
        Route::post('/consignments-pick-up-submit', 'ConsignmentsPickUpController@pick_up_submit');

        Route::post('/add-pickup-cart', 'CartPickUpController@add_pickup_cart');
        Route::post('/add-bulk-pickup-cart', 'CartPickUpController@add_bulk_pickup_cart');
        Route::get('/remove-pickup-cart/{unique_suborder_id}', 'CartPickUpController@remove_pickup_cart');

        // Consignment pick up
        Route::get('/consignments-return', 'ConsignmentsReturnController@return_con');
        Route::post('/consignments-return-submit', 'ConsignmentsReturnController@return_submit');
        Route::post('/update-consignments-return-submit', 'ConsignmentsReturnController@return_submit_load');

        Route::post('/add-return-cart', 'CartReturnController@add_return_cart');
        Route::post('/add-bulk-return-cart', 'CartReturnController@add_bulk_return_cart');
        Route::get('/remove-return-cart/{unique_suborder_id}', 'CartReturnController@remove_return_cart');

        // Consignment delivery
        Route::get('/consignments-delivery', 'ConsignmentsDeliveryController@delivery');
        Route::post('/consignments-delivery-submit', 'ConsignmentsDeliveryController@delivery_submit');

        Route::post('/add-delivery-cart', 'CartDeliveryController@add_delivery_cart');
        Route::post('/add-bulk-delivery-cart', 'CartDeliveryController@add_bulk_delivery_cart');
        Route::get('/remove-delivery-cart/{unique_suborder_id}', 'CartDeliveryController@remove_delivery_cart');

        // all consignment
        Route::get('/consignments-all', 'ConsignmentsControllerAll@all_pick_up_cl');
        Route::get('/consignments/{id}', 'ConsignmentsControllerAll@view_consignment');
        Route::get('/consignments-all-invoice/{id}/{type}', 'ConsignmentsControllerAll@all_invoice');
        Route::get('/consignments-start/{id}', 'ConsignmentsControllerAll@start_consignments');
        Route::get('/consignments-cancel/{id}', 'ConsignmentsControllerAll@cancel_consignments');
        Route::get('/consignments-edit/{id}', 'ConsignmentsControllerAll@edit_consignments');
        Route::get('/consignments-followup/{id}', 'ConsignmentsControllerAll@followup_consignments');

        Route::get('/con-sms-test', 'ConsignmentsControllerAll@testStartCon');

    });
     // masud end
    // Accounts
    Route::group(['namespace' => 'Accounts'], function () {
        //Collected Cash Amount for hub manager
        Route::get('/collected-cash-amount','CollectedCashAmountController@index');
        Route::get('/collected-cash-amount-export/{type}','CollectedCashAmountController@export');
        Route::post('/collected-cash-amount/accumulated','CollectedCashAmountController@collectedCashAccumulated');
        Route::get('/accumulated-collected-cash','CollectedCashAmountController@accumulatedCash');
        //Collected Cash Amount for head of account manager
        Route::get('/accumulated-collected-cash-confirm','CollectedCashAmountController@accumulatedCashConfirm');
        Route::post('/accumulated-collected-cash-confirm','CollectedCashAmountController@accumulatedConfirmed');
        //HOA accumulated merchant checkout
        Route::get('collected-cash-merchant','CollectedCashAmountController@getLists');
        Route::get('collected-cash-merchant-export/{type}','CollectedCashAmountController@getListsExport');
        Route::post('collected-cash-merchant','CollectedCashAmountController@merchantAccumulatedCash');
        //HOA accumulated merchant checkout confirm
        Route::get('collected-cash-merchant-confirm','CollectedCashAmountController@accumulatedMerchantCashConfirm');
        Route::get('collected-cash-merchant-confirm-export/{type}','CollectedCashAmountController@accumulatedMerchantCashConfirmExport');
        Route::post('collected-cash-merchant-confirm','CollectedCashAmountController@merchantCashConfirm');
        //HOA accumulated merchant checkout history
        Route::get('collected-cash-merchant-final','CollectedCashAmountController@merchantCashFinalLists');

        Route::get('collection-cash-details/{id}','CollectedCashAmountController@collectionCashDetails');

        // Cash collection
        Route::get('/cash-collection', 'CashCollectionController@index');
        Route::post('/cash-collection-submit', 'CashCollectionController@cash_collection_submit');
        // tranfer to vault
        Route::get('/transfer-to-vault', 'TransferToVaultController@index');
        Route::post('/transfer-to-vault-submit', 'TransferToVaultController@transfer_to_vault_submit');
        // vault history
        Route::get('/vault-list', 'VaultController@index');
        Route::post('/vault-approval-submit', 'VaultController@vault_approval_submit');
        // transfer to bank
        Route::get('/transfer-to-bank', 'TransferToBankController@index');
        Route::post('/transfer-to-bank-submit', 'TransferToBankController@transfer_to_bank_submit');

        // bank history
        Route::get('/bank-list', 'BankController@index');
        Route::post('/bank-approval-submit', 'BankController@bank_approval_submit');

        // bank transfer canceled
        Route::get('/bank-canceled', 'BankController@transfer_canceled');
        Route::post('/bank-cancel-submit', 'BankController@bank_cancel_submit');

        // manage valut
        Route::get('/manage-vault', 'VaultController@manage_vault');

        // manage checkout
        Route::get('/manage-checkout', 'BankController@manage_checkout');

        // upload bank doc
        Route::post('/upload-bank-doc', 'BankController@upload_bank_doc');

        // bank transaction id
        Route::post('/set-bank-transaction-id', 'BankController@set_bank_transaction_id');

        // merchant checkout
        Route::get('/create-merchant-checkout', 'MerchantCheckoutController@index');
        Route::post('/merchant-checkout-submit', 'MerchantCheckoutController@checkout_submit');
        Route::get('/get-merchant-account', 'MerchantCheckoutController@merchant_bank_account');

        // Manage merchant check out
        Route::get('/manage-merchant-checkout', 'MerchantCheckoutController@manage_merchant_checkout');
        Route::post('/upload-bank-doc-merchant-checkout', 'MerchantCheckoutController@upload_bank_doc');
        Route::post('/merchant-checkout-transaction-id', 'MerchantCheckoutController@set_bank_transaction_id');
        Route::get('/view-merchant-invoice/{invoice_no}/{id}', 'MerchantCheckoutController@invoice_pdf');
        Route::get('/get-merchant-discount', 'MerchantCheckoutController@get_merchant_discount');
        Route::post('/discount-merchant-checkout', 'MerchantCheckoutController@discount_merchant_checkout');

        // Head of accounts
        Route::get('/manage-vault-accounts', 'HeadOfAccountsController@manage_vault');
        Route::get('/manage-checkout-accounts', 'HeadOfAccountsController@manage_checkout');
        Route::get('/cancel-checkout', 'HeadOfAccountsController@cancel_checkout');

        // merchant Bill
        Route::get('/create-merchant-bill', 'MerchantBillController@index');
        Route::post('/merchant-bill-submit', 'MerchantBillController@bill_submit');

        // Manage merchant Bill
        Route::get('/manage-merchant-bill', 'MerchantBillController@manage_merchant_bill');
        Route::get('/merchant-bill-invoice/{invoice_no}/{id}', 'MerchantBillController@invoice_pdf');
        Route::post('/merchant-bill-transaction-id', 'MerchantBillController@set_bank_transaction_id');

        Route::post('/upload-bank-doc-merchant-bill', 'MerchantBillController@upload_bank_doc');
        Route::get('/merchant-invoice-details/{invoice_no}', 'MerchantBillController@invoice_details');

        Route::get('/get-bill-discount', 'MerchantBillController@get_bill_discount');
        Route::post('/discount-merchant-bill', 'MerchantBillController@discount_merchant_bill');
        Route::get('/get-bill-charge', 'MerchantBillController@get_bill_charge');
        Route::post('/charge-merchant-bill', 'MerchantBillController@charge_merchant_bill');

    });

    Route::resource('vault', 'VaultController');
    Route::resource('bank', 'BankController');
    Route::resource('bank-accounts', 'BankAccountsController');
    Route::resource('hub-bank-accounts', 'HubBankAccountsController');
    Route::get('get-store-for-merchant-bank-accounts', 'MerchantBankAccountsController@get_store_for_merchant_bank_accounts');
    Route::resource('merchant-bank-accounts', 'MerchantBankAccountsController');


    Route::resource('recon-on', 'ReconONController');

    // Reporting
    Route::group(['namespace' => 'Report'], function () {

        Route::get('/order-reassign', 'CooReportController@index');
        Route::get('/order-reassign-export/{type}', 'CooReportController@orderreassignexport');

        Route::get('/order-unattempted', 'CooReportController@orderunattempted');
        Route::get('/order-unattempted-export/{type}', 'CooReportController@orderunattemptedexport');

        Route::get('/order-completed', 'CooReportController@ordercompleted');
        Route::get('/order-completed-export/{type}', 'CooReportController@ordercompletedexport');

        Route::get('/new-merchants', 'CooReportController@newmerchants');
        Route::get('/new-merchants-export/{type}', 'CooReportController@newmerchantsexport');

        Route::get('/inactive-merchants', 'CooReportController@merchantsorder');
        Route::get('/inactive-merchants-export/{type}', 'CooReportController@merchantsorderexport');

        Route::get('/delivery-revenue', 'CooReportController@deliveryrevenue');
        Route::get('/delivery-revenue-export/{type}', 'CooReportController@deliveryrevenueexport');

        Route::get('/tat/delivery', 'TatController@delivery');
        Route::get('/tat/deliveryexport/{type}', 'TatController@deliveryexport');

        Route::get('/tat/return', 'TatController@returnorder');
        Route::get('/tat/returnexport/{type}', 'TatController@returnorderexport');

        Route::get('/tat/tit', 'TatController@tit');
        Route::get('/tat/titexport/{type}', 'TatController@titexport');

        Route::get('/aging/pickup', 'AgingController@pickup');
        Route::get('/aging/pickupexport/{type}', 'AgingController@pickupexport');

        Route::get('/aging/delivery', 'AgingController@delivery');
        Route::get('/aging/deliveryexport/{type}', 'AgingController@deliveryexport');

        Route::get('/aging/return', 'AgingController@returnaging');
        Route::get('/aging/returnexport/{type}', 'AgingController@returnexport');

        Route::get('/aging/trip', 'AgingController@trip');
        Route::get('/aging/tripexport/{type}', 'AgingController@tripexport');

        Route::get('/aging/attempt', 'AgingController@attempt');
        Route::get('/aging/attemptexport/{type}', 'AgingController@attemptexport');


        //Operational Reports
        Route::get('/operation/sales', 'OperationalController@sales');
        Route::get('/operation/salesexport/{type}', 'OperationalController@salesexport');

        Route::get('/operation/failed-pickup', 'OperationalController@pickup');
        Route::get('/operation/failed-pickupexport/{type}', 'OperationalController@pickupexport');

        Route::get('/operation/failed-delivery', 'OperationalController@delivery');
        Route::get('/operation/failed-deliveryexport/{type}', 'OperationalController@deliveryexport');

        Route::get('/operation/failed-return', 'OperationalController@return');
        Route::get('/operation/failed-returnexport/{type}', 'OperationalController@returnexport');

        Route::get('/operation/intransit', 'OperationalController@intransit');
        Route::get('/operation/intransitexport/{type}', 'OperationalController@intransitexport');

        Route::get('/operation/quantitative', 'OperationalController@quantitative');
        Route::get('/operation/quantitativeexport/{type}', 'OperationalController@quantitativeexport');

    });

});

// Route::get('/mailview/successpickup', 'MailViewController@successpickup');
// Route::get('/mailview/failpickup', 'MailViewController@failpickup');
// Route::get('/mailview/successdelivery', 'MailViewController@successdelivery');
// Route::get('/mailview/faildelivery', 'MailViewController@faildelivery');
// Route::get('/mailview/successreturn', 'MailViewController@successreturn');

// Route::get('/mailview/successpickup', function(){
//     // \Illuminate\Support\Facades\Artisan::call('email:daily');
//     Artisan::call('email:daily');
//     return 'Ok';
// });
// Route::get('/mailview/failpickup', 'MailViewController@failpickup');
// Route::get('/mailview/successdelivery', 'MailViewController@successdelivery');
// Route::get('/mailview/faildelivery', 'MailViewController@faildelivery');
// Route::get('/mailview/successreturn', 'MailViewController@successreturn');

# Consignment V2
// Route::get('/v2consignment', function(){
//     return 'Ok';
// });
Route::group(['prefix' => 'v2consignment', 'middleware' => 'auth'], function () {
    Route::group(['namespace' => 'ConsignmentsV2'], function () {

        Route::get('/', 'ConsignmentController@index');
        Route::get('/export/{type}', 'ConsignmentController@consignmentexport');
        Route::get('/{id}', 'ConsignmentController@show');
        Route::get('/reconciliation/done/{id}', 'ConsignmentController@reconciliationDone');
        Route::get('/reconciliation/{id}', 'ConsignmentController@reconciliation');
        Route::post('/reconcile', 'ConsignmentController@reconcile');
        Route::post('/bulkreconcile', 'ConsignmentController@bulkreconcile');

    });
});

Route::group(['prefix' => 'operational-report'], function () {
    Route::group(['namespace' => 'OperationalReport'], function () {

        Route::get('/', 'ConsignmentController@index');
        Route::get('/export/{type}', 'ConsignmentController@consignmentexport');
        Route::get('/{id}', 'ConsignmentController@show');
        Route::get('/reconciliation/done/{id}', 'ConsignmentController@reconciliationDone');
        Route::get('/reconciliation/{id}', 'ConsignmentController@reconciliation');
        Route::post('/reconcile', 'ConsignmentController@reconcile');
        Route::post('/bulkreconcile', 'ConsignmentController@bulkreconcile');

    });
});

// Experiment
Route::get('experiment/go-to-return-panel', 'ExperimentController@goToReturnPanel');
Route::get('experiment/ajker-deal-order-id-update', 'ExperimentController@ajkerDealOrderIdUpdate');
Route::get('experiment/go-to-pickup-cancel', 'ExperimentController@goToPickupCancel');
Route::get('experiment/go-to-delivery-panel', 'ExperimentController@goToDeliveryPanel');
Route::get('experiment/update-suborder/{sub_order_id}/{status}', 'ExperimentController@updateSubOrderStatus');
Route::get('experiment/test-fcm/{sub_order_unique_id}', 'ExperimentController@testFcm');

Route::get('/awb/{unique_suborder_id}', 'Consignments\ConsignmentsControllerPublic@common_awb_single_public');

/**
 * Routes used by Risul Islam
 *
 * @author Risul Islam risul.islam@sslwireless.com/risul321@gmail.com
 **/

Route::get('gmap', function () {
    return view('gmapRnD');
});