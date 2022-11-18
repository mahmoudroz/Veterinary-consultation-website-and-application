<?php

use App\Http\Controllers\ApiClinic\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'changeLanguage'], 'namespace' => 'ApiClinic'], function () {

    ################################ START AUTHENTICATION ###################################
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', [AuthenticationController::class, 'register']);
        Route::post('login', [AuthenticationController::class, 'login']);
        Route::post('check-phone', [AuthenticationController::class, 'checkPhone']);
        Route::post('forget-password', [AuthenticationController::class, 'forgetPassword']);

    });
    ################################ END AUTHENTICATION   ###################################
    ################################     START FOOTER     ###################################
    Route::group(['prefix' => 'footer', 'namespace' => 'Footer'], function () {
        Route::get('about-us', 'AboutUsController@getAboutUs');
        Route::get('contact-us', 'ContactUsController@getContactUs');
        Route::get('terms', 'TermController@getTerms');
    });
    ################################     END FOOTER       ###################################
    Route::group(['middleware' => ['clinic.auth:api'],], function () {
        Route::get('auth/logout', [AuthenticationController::class, 'logout']);
        ############################### START PROFILE ###############################
        Route::group(['prefix' => 'profile', 'namespace' => 'Clinic\Profile',], function () {
            ############################## START PROFILE #################################
            Route::group(['prefix' => 'profile', 'namespace' => 'Profile'], function () {
                Route::get('show', 'ProfileController@show');
                Route::post('change-password', 'ProfileController@changePassword');
                Route::get('delete-account', 'ProfileController@deleteAccount');
                Route::get('edit', 'ProfileController@edit');
                Route::post('update', 'ProfileController@update');
                Route::get('change-status-online', 'ProfileController@changeStatusOnline');
                ######################## START ADD CLINIC TIMES ###########################
                Route::post('add-clinic-times', 'ClinicTimesController@addClinicTimes');
                Route::get('delete-clinic-time/{time_id}', 'ClinicTimesController@deleteClinicTime');
                ######################## END ADD CLINIC TIMES   ###########################
            });
            ############################## END PROFILE ###################################
            ############################### START WITHDRAW ###############################
            Route::group(['prefix' => 'withdraw' , 'namespace' => 'Withdraw'], function (){
                Route::get('withdraw-show','WithdrawMoneyController@show');
                Route::post('withdraw-money','WithdrawMoneyController@withdrawMoney');
            });
            ############################## END WITHDRAW ##################################
            ############################## START REQUESTS ################################
            Route::group(['prefix' => 'requests' , 'namespace' => 'Requests' ], function (){
                ############################## START NEW REQUESTS ###############################
                Route::get('new-show','NewRequestController@show');
                Route::get('new-details/{id}','NewRequestController@details');
                Route::get('new-details-animal/{id}/{request_id}','NewRequestController@detailsAnimal');
                Route::get('new-accept/{id}','NewRequestController@accept');
                Route::get('new-reject/{id}','NewRequestController@reject');
                ############################## END NEW REQUESTS   ###############################
                ############################## START CURRENT REQUESTS ###########################
                Route::get('current-show','CurrentRequestController@show');
                Route::get('current-details/{id}','CurrentRequestController@details');
                Route::get('current-details-animal/{id}/{request_id}','CurrentRequestController@detailsAnimal');
                Route::post('current-add-report','CurrentRequestController@addReport');
                Route::get('current-save-report/{request_id}/{clinic_report}','CurrentRequestController@SaveReport');
                Route::get('current-add-medicine/{request_id}/{add_medicine}','CurrentRequestController@addMedicine');
                Route::get('current-delete-medicine/{request_id}/{delete_medicine}','CurrentRequestController@deleteMedicine');
                Route::get('move-to-previous-request/{request_id}','CurrentRequestController@moveToPreviousRequest');
                ############################## END CURRENT REQUESTS   ###########################
                ############################## START PREVIOUS REQUESTS ###############################
                Route::get('previous-show','PreviousRequestController@show');
                Route::get('previous-details/{id}','PreviousRequestController@details');
                Route::get('previous-details-animal/{id}/{request_id}','PreviousRequestController@detailsAnimal');
                ############################## END PREVIOUS REQUESTS   ###############################
            });
            ################################ END REQUESTS   ###############################
        });
        ###############################  END PROFILE  ###############################
        ############################### START REVIEWS ###############################
        Route::group(['prefix' => 'reviews', 'namespace' => 'Clinic\Reviews',], function () {
            Route::get('show','ReviewController@show');
        });
        ##############################  END REVIEWS #################################
        ############################ START NOTIFICATIONS #############################
        Route::group(['prefix' => 'profile/notifications', 'namespace' => 'Clinic\Notification',], function () {
            Route::get('show','NotificationController@show');
            Route::post('update-fcm-token','NotificationController@updateFcmToken');
        });
        ############################  END NOTIFICATIONS ##############################
        ############################     START CHATS    ##############################
        Route::group(['prefix' => 'profile/chats', 'namespace' => 'Clinic\Chat',], function () {
            Route::post('send-message' , 'ChatController@sendMessage');
            Route::get('my-chats' , 'ChatController@myChats');
            Route::post('get-chat' , 'ChatController@getChatByUserIdAndRequestIdAndClinicId');
//            Route::post('get-messages-by-sender-and-receiver' , 'ChatController@getMessagesBySenderAndReceiver');
        });
        ############################      END CHATS    ##############################

    });
});
