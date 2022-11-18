<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;

Trait  backendTraits
{

    // save image
    function saveImage($photo,$folder){
        //save photo in folder
        $file_extension = $photo -> getClientOriginalExtension();
        $file_name = time().'.'.$file_extension;
        $path = $folder;
        $photo -> move($path,$file_name);
        return $file_name;
    }
    public function upploadImage($image,$folder)
    {
        $imageName = rand(1000000,100000000) .time().'.'.$image->extension();
        $image->move(public_path($folder),$imageName);
        return $imageName;
    }


    // save image by Image Intervention
    function imageInterve($image,$path){
        Image::make($image)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->save(public_path($path .$image->hashName()));
        $image = $image->hashName();
        return $image;
    }
}
