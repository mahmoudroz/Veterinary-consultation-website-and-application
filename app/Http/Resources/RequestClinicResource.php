<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestClinicResource extends JsonResource
{

    public function toArray($request)
    {
        Carbon::setlocale( app()->getLocale() );
        $paid = '';
        if ( $this['paid'] == 0 ){
            $paid = __('clinic.unpaid');
        }elseif ( $this['paid'] == 1 ) {
            $paid = __('clinic.paid');
        }else{
            $this['paid'] = 0;
        }
        //RETURN STATUS NAME
        $statusName = '';
        if($this->status == 0)
            $statusName = __('user.new');
        elseif($this->status == 1)
            $statusName = __('user.suspended');
        elseif($this->status == 2)
            $statusName = __('user.finished');
        elseif($this->status == 3)
            $statusName = __('user.canceled');
        elseif($this->status == 4)
            $statusName = __('user.current');
        #####################  START GENDER #######################
        $gender = '';
        if ( $this['gender'] == 0 ){
            $gender = __('clinic.Female');
        }elseif ( $this['gender'] == 1 ){
            $gender =  __('clinic.Male');
        }else {
            $gender = $this['gender'];
        }
        #####################   END GENDER  #######################
        $pet = (object) [
            'id'                =>$this->pet->id,
            'user_id'           =>$this->pet->user_id,
            'name'              =>$this->pet->name ?? '0',
            'image'             =>$this->pet->image_path,
            'type_id'           =>$this->pet->type_id ?? 0,
            'type'              =>$this->pet->petType->name,
             ];
        return [
            'id'                            => $this['id'],
            'date'                          => $this['date'] ?? '00/00/0000',
            'time'                          => Carbon::parse( $this->clinicDay->times[0]->from )->translatedFormat('H:i:s a') ?? '00:00:00 am',
            'requestType'                   => $this->requestType['name'] ,
            'pet_id'                        => $this['pet_id'] ?? 0,
            'pet'                           => $pet,
            'notes'                         => $this['details'] ?? "0",
            'images'                        => RequestImagesClinicResource::collection( $this['images'] ),
            'clinic_report'                 => $this['clinic_report'] ?? "0",
            'accepted'                      => $this['accepted'] ?? 0,
            'status'                        => $this['status'],
            'statusName'                    => $statusName,
            'paid'                          => $this['paid'],
            'address'                       => $this['address'] ?? "0",
            'lat'                           => $this['lat'] ?? "0",
            'lng'                           => $this['lng'] ?? "0",
//            'clinicDay'                     => $this['clinicDay'],
//            'date'                          =>  Carbon::parse($this['created_at'])->format('d/m/Y') ?? '00/00/0000',
//            'time'                          =>  Carbon::parse($this['created_at'])->translatedFormat('H:i:s a') ?? '00:00:00 am',
//            'updated_at'                    =>  Carbon::parse($this['updated_at'])->format('d/m/Y') ?? '00/00/0000',
            'type_id'                       => $this['type_id'] ?? 0,
//            'specialization_id'             => $this['specialization_id'] ?? 0,
//            'specialization'                => $this['specialization'],


        ];
    }
}
