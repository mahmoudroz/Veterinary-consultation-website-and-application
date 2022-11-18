<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatClinicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // RETURN IMAGE AND NAME OF USER WHO CHAT WITH CURRENT CLINIC
        $user = User::find( $this['user_id'] );

        // RETURN LAST MESSAGE
//        $last_message = $this->messages->last();
//        $last_message = MessageResource::make($last_message);

        return [
            'id'                => $this['id'],
            'user_name'         => $user['name'],
            'user_id'           => $user['id'],
            'user_image'        => ($user->type == 1) ? $user['image_clinic'] : $user['image_path'],
            'request_id'        => $this['request_id'],
            'messages'          => MessageResource::collection( $this['messages'] ),
        ];
    }
}
