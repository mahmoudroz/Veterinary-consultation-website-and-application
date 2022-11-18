<?php

function changeLang(){
    if(request()->segment(1) == "en"){
       $url = url()->current();
       $replace = str_replace('/'.request()->segment(1) , '/ar' , $url);
       return $replace;
   }

   if(request()->segment(1) == "ar"){
       $url = url()->current();
       $replace = str_replace('/'.request()->segment(1) , '/en' , $url);
       return $replace;
   }
}


?>
