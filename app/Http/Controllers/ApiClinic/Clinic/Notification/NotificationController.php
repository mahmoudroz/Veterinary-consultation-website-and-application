<?php

namespace App\Http\Controllers\ApiClinic\Clinic\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationClinicResource;
use App\Models\SentNotification;
use App\Models\User;
use App\Traits\HandleApiJsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use HandleApiJsonResponseTrait;
    ########################### START GET NOTIFICATION ###############################
    public function show(): \Illuminate\Http\JsonResponse
    {
        $send_notification = SentNotification::select( 'id' , 'created_at' , 'body_' . app()->getLocale() . ' as body' ,
            'title_' . app()->getLocale() . ' as title' , 'request_id' , 'status_request' )
            ->where('user_id' , '=' , auth()->user()->id)->orderBy('id', 'DESC')->paginate(10);
        return $this->success( [ 'notifications' => NotificationClinicResource::collection( $send_notification )->response()->getData(true) ] );
    }
    ###########################  END GET NOTIFICATION  ###############################
    ########################### START UPDATE FCM TOKEN ###############################
    public function updateFcmToken(Request $request)
    {
        $user_id = auth()->user()->id;
        $validator = Validator::make(
            $request->all(),
            [
                'fcm_token'   => 'nullable',
                'mobile_id'   => 'required|between:0,1'
            ]
        );
        if ($validator->fails()) {
            return $this->errorValidate( $validator );
        }
        $user = User::find( $user_id );

        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->mobile_id = $request->mobile_id;
            $user->save();
            return $this->success("fcm token has been updated");
        } else {
            return $this->error( __("clinic.error") );
        }
    }
    ###########################  END UPDATE FCM TOKEN  ###############################

}
