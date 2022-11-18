<?php

namespace App\Http\Controllers\ApiClinic\Clinic\Profile\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetClinicResource;
use App\Http\Resources\RequestClinicResource;
use App\Models\Pet;
use App\Models\RequestMedicine;
use App\Traits\HandleApiJsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CurrentRequestController extends Controller
{
    use HandleApiJsonResponseTrait;
    public function show (): \Illuminate\Http\JsonResponse
    {
        try {
            $current_requests = \App\Models\Request::select('id', 'created_at' ,'type_id')
            ->where(
                ['accepted' => 1, 'status' => 4 , 'clinic_id' => auth()->user()->id])
                ->with(['requestType'])
                ->paginate(10);
            return $this->success( [ 'current_requests' =>  $current_requests ]);
        }catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    public function details($id): \Illuminate\Http\JsonResponse
    {
        try{
            $current_request = \App\Models\Request::with(['user','requestType','clinicDay'=> function($q)
            {
                $q->with(['times','day']);
            },
                'specialization','pet','images'])
            ->where('status' , 4 )
            ->where('clinic_id' , auth()->user()->id)->find($id);
            if ( !$current_request ){
                return $this->errorNotFound();
            }
            return $this->success( [ 'current_request' =>  new RequestClinicResource( $current_request ) ]);
        } catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    public function detailsAnimal($id ,$request_id): \Illuminate\Http\JsonResponse
    {
        try {
            if(!$request_id){
                return $this->errorNotFound();
            }
            $details_pet =  Pet::with(['petType', 'requests' => function( $q ){
                $q->with(['medicines','clinic'])->whereNotNull('clinic_report');
            } , 'vaccinations' => function( $q ){
                $q->with('vaccinationType');
            }])->find($id);
            if (!$details_pet){
                return $this->errorNotFound();
            }
            return $this->success( [ 'details_animal' =>  new PetClinicResource( $details_pet )  , 'request_id' => $request_id ]);
        } catch (\Exception $ex){
            return $this->errorUnExpected($ex);
        }
    }
    public function addReport(Request $request): \Illuminate\Http\JsonResponse
    {
       try{
           $validator = Validator::make( $request->all() , [
               'request_id'         => [ 'required' , 'integer' , 'exists:requests,id' ],
               'report'             => [ 'required' , 'string' , 'min:3' , 'max:3000' ],
               'medicines'          => [ 'required' , 'string' , 'min:3' , 'max:3000' ]
           ]);
           if( $validator -> fails() ){
               return $this->errorValidate($validator);
           }
           DB::beginTransaction();
           $current_request = \App\Models\Request::where('status' , 4)->where('clinic_id' , auth()->user()->id)->find($request -> request_id);
           if( ! $current_request ){
               return $this->error( __('clinic.error') );
           }
           $current_request['clinic_report'] = $request['report'];
           $current_request['status'] = 2;
           $current_request['accepted'] = 0;
           $current_request->save();
           $request_medicine = new RequestMedicine();
           $request_medicine['request_id']  = $request['request_id'];
           $request_medicine['medicine']    = $request['medicines'];
           $request_medicine->save();
           DB::commit();
           return $this->success( __('clinic.success') );
       } catch (\Exception $ex){
           DB::rollback();
           return $this->errorUnExpected( $ex );
       }
    }
}
