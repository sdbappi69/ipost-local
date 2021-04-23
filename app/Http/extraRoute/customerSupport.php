<?php
//Inquiry
Route::post('inquiry/get-caller-info','InquiryController@get_caller_details');
Route::get('inquiry/mark-as-solved/{inquiry_id}','InquiryController@set_status');
Route::post('inquiry-send-email/{inquiry_id}','InquiryController@send_email');
Route::get('inquiry/export-xls','InquiryController@export_xls');
Route::resource('inquiry','InquiryController');
//End inquiry
Route::get('complain/mark-as-solved/{complain_id}','ComplainController@mark_as_solved');
Route::post('complain-send-email/{complain_id}','ComplainController@send_email');
Route::get('complain/export-xls','ComplainController@export_xls');
Route::resource('complain','ComplainController');
Route::resource('inquiry-status','InquiryStatusController');
Route::resource('mail-groups','MailGroupController');
Route::resource('source-of-info','SourceOfInformationController');
Route::resource('unique-head','UniqueHeadController');
Route::resource('query','QueryController');
Route::resource('reaction','ReactionController');
        // Order List
Route::get('order-cs/{type}','OrderController@orderexport');
Route::resource('order-cs','OrderController');
        // End Order list
        // Feedback
Route::get('feedback/export-xls','FeedbackController@export_xls');
Route::get('feedback/collected','FeedbackController@collected_feedback');
Route::resource('feedback','FeedbackController');
        // End Feedback