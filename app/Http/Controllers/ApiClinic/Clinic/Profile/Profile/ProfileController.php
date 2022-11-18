<?php

namespace App\Http\Controllers\ApiClinic\Clinic\Profile\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClinicResource;
use App\Models\ClinicImage;
use App\Models\ClinicSpecialization;
use App\Models\Specialization;
use App\Models\User;
use App\Models\WeekDay;
use App\Traits\backendTraits;
use App\Traits\HandleApiJsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use backendTraits;
    use HandleApiJsonResponseTrait;
    ####################################   START SHOW    ###################################
    public function show(): \Illuminate\Http\JsonResponse
    {
         try{
             $id = auth()->user()->id;
             $clinic = User::with(['specializations' =>
                 function($q){
                     $q->with('specialization');
                 },'clinicImages',
                 'clinicDays' => function($q){
                     $q->with('times','day');
                 }])
                 ->find($id);
             $clinic = new ClinicResource($clinic);
             return $this->success($clinic);
         }catch (\Exception $ex){
             return $this->errorUnExpected($ex);
         }
    }
    ####################################     END SHOW    ###################################
    ####################################     START EDIT  ###################################
    public function edit(): \Illuminate\Http\JsonResponse
    {
        try{
            $id = auth()->user()->id;
             $clinic = User::with(['specializations' =>
                function($q){
                    $q->with('specialization');
                },'clinicImages',
                'clinicDays' => function($q){
                    $q->with('times','day');
                }])
                ->find($id);
            $specializations = Specialization::select('id','name_'.app()->getLocale().' as name')->get();
            $days = WeekDay::select('id','name_'.app()->getLocale().' as name')->get();
            //$clinicSpecializations = ClinicSpecialization::where('clinic_id', $id )->with('specialization')->get();
            return $this->success( [ 'clinic' => New ClinicResource($clinic) , 'specializations' => $specializations , 'days' => $days ] );
        }catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    ####################################     END EDIT    ###################################
    ####################################   START UPDATE  ###################################
    public function update(Request $request){
        try{
            return $request->images[0];
            $clinic = User::find(auth()->user()->id);
             if ( $this->validateUpdateProfile($request) ){
                 return $this->validateUpdateProfile($request);
             }
            $data = [
                'name'              => $request['name'],
                'phone'             => $request['phone'],
                'country_code'      => $request['country_code'],
                'email'             => $request['email'],
                'registration_number'=>$request['registration_number'],
                'consultation_fees' =>$request['consultation_fees'],
                'address'           => $request['address'],
                'details'           => $request['details'],
                'status_online'     => $request['status_online'],
                'lat'               => $request['lat'],
                'lng'               => $request['lng'],
            ];
            if ($request->image) {
                if ($clinic->image != null) {
                    Storage::disk('uploads')->delete('/clinics_images/' . $clinic->image);
                }
                $data['image'] = $this->upploadImage($request->image, '/uploads/clinics_images/');
            }
            $clinic->update($data);
            ########################## START UPDATE SPECIALIZATION  ###################
            $this->updateSpecialization( $request['clinic_specializations'] );
            ##########################   END UPDATE SPECIALIZATION  ###################
            ##########################   START UPDATE IMAGES CLINIC ###################
            $this->updateImagesClinic( $request['images'] , $request['id_images_delete'] );
            ##########################    END UPDATE IMAGES CLINIC  ###################
            return $this->success( __('clinic.success') );
        }catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    ###########################   START VALIDATE UPDATE PROFILE   ##########################
    public function validateUpdateProfile($request){
        $validator = Validator::make(
            $request->all(),
            [
                'name'                => 'required|string',
                'phone'               => ['required'],
                'country_code'        => 'required',
                'email'               => ['required', 'email'],
                'registration_number' => 'required',
                'consultation_fees'   => 'required|integer',
                'address'             => 'required',
                'details'             => 'required',
                'lat'                 => 'required',
                'lng'                 => 'required',
                'image'               => 'nullable',
                'clinic_specializations'=>'required|array',
                'clinic_specializations.*'=> 'required|integer|exists:specializations,id',
                'images'              => 'nullable|array',
                'image.*'             => 'image',
                'id_images_delete'    => 'array|nullable',
                'id_images_delete.*'  => 'nullable|integer',

            ]
        );
        if($validator->fails()){
            return $this->errorValidate($validator);
        }
    }
    ###########################    END VALIDATE UPDATE PROFILE    ##########################
    ############################        START SPECIALIZATION      ##########################
    public function updateSpecialization($clinic_specializations){
        $id = auth()->user()->id;
         ClinicSpecialization::where('clinic_id', $id)->delete();
            foreach($clinic_specializations as $clinic_specialization){
                ClinicSpecialization::create([
                    'clinic_id'         => $id,
                    'specialization_id' => $clinic_specialization
                ]);
            }
    }
    ############################        END SPECIALIZATION      ############################
    ########################      START  UPDATE IMAGES CLINIC      #########################
    public function updateImagesClinic($images ,$id_images_delete ){
        $id = auth()->user()->id;
        if ( $images ) {
            foreach ( $images as $image ) {
                $imageName = $this->upploadImage($image, 'uploads/clinics_images/');
                ClinicImage::create([
                    'clinic_id'     => $id,
                    'image'         => $imageName,
                ]);
            }
        }
        if( $id_images_delete ){
            $old_images = ClinicImage::where('clinic_id' , $id)->whereIn( 'id' , $id_images_delete )->get();
            foreach ($old_images as $old_image){
                if ($old_image->image != null) {
                    Storage::disk('uploads')->delete('/clinics_images/' . $old_image->image);
                }
                $old_image->delete();
            }
        }
    }
    ########################       END UPDATE IMAGES CLINIC     ############################
    ####################################    END UPDATE   ###################################
    ############################        START CHANGE PASSWORD ##############################
    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'old_password'            => 'required|max:255',
                    'password'                => 'required|min:6|max:255',
                    'confirm_password'        => 'required|max:255|same:password'
                ]
            );
            if($validator->fails()){
                return $this->errorValidate($validator);
            }
            $id = auth()->user()->id;
            $clinic = User::find($id);
            if(!$clinic){
              return $this->errorNotFound();
            }
            if( ! Hash::check( request('old_password') , auth()->user()->password ) ){
                return $this->error( __('clinic.Information Error') );
            }
            $clinic->update([
                'password'  => Hash::make($request->password)
            ]);
            return $this->success(__('clinic.success'));
        }catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    ############################        END CHANGE PASSWORD  ###############################
    ############################        START DELETE ACCOUNT  ##############################
    public function deleteAccount(): \Illuminate\Http\JsonResponse
    {
        try {
            $id = auth()->user()->id;
            $clinic = User::find($id);
            if(!$clinic){
                return $this->errorNotFound();
            }
            $clinic->update([
                'active' => -1
            ]);
            return $this->success(__('clinic.success'));
        }catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    ############################          END DELETE ACCOUNT      ##########################
    ############################       START CHANGE ONLINE STATUS ##########################
    public function changeStatusOnline(){
        $clinic = User::find( auth()->user()->id );
        $clinic->update([
            'status_online' =>  ! $clinic['status_online']
        ]);
        return $this->success(__('clinic.success'));
    }
    ############################        END CHANGE ONLINE STATUS  ##########################

}
