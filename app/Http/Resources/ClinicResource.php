<?php

namespace App\Http\Resources;

use App\Models\ClinicRate;
use Illuminate\Http\Resources\Json\JsonResource;

class ClinicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $specializations = $this->specializations()->select('id','clinic_id','specialization_id')->with('specialization')->get();

        $clinicDays = $this->clinicDays()->select('id','clinic_id','day_id')->with(['day', 'times'=>function($q){
            return $q->select('id','clinic_day_id','from','to');
        }])->get();

        $count_clinic_rate = ClinicRate::where('clinic_id' , $this->id)->count();

        return [
            'id'                     =>$this->id,
            'image'                  =>$this->image_clinic?? 'default.png',
            'name'                   =>$this->name?? '',
            'rate'                   =>$this->rate,
            'count_clinic_rate'     => $count_clinic_rate,
            'consultation_fees'      => $this->consultation_fees,
            'details'                =>$this->details?? 'details',
            'phone'                  =>$this->phone?? '000',
            'country_code'           =>$this->country_code?? '000',
            'email'                  =>$this->email?? '',
            'registration_number'    =>$this->registration_number?? '0000',
            'address'                =>$this->address?? 'address',
            'lat'                    =>$this->lat?? '0',
            'lng'                    =>$this->lng?? '0',
            'specializations'        =>$specializations,
            'clinic_images'          =>  ImageClinicResource::collection($this->clinicImages),
            'clinic_days'            =>$clinicDays,
            'status_online'          =>$this->status_online?? 0,
            'fcm_token'              =>$this->fcm_token?? '',
            'mobile_id'              =>$this->mobile_id ?? 00000,
            'active'                 =>$this->active,
            'notifiable'             =>$this->notifiable,
            'locale'                 =>$this->locale,
            //'recommendations'        =>$recommendations,
        ];
    }
}
