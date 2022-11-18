<?php

namespace App\Traits;


Trait  HandleApiJsonResponseTrait
{
    ###############################  START ERROR VALIDATE #############################
    public function errorValidate($validator):\Illuminate\Http\JsonResponse{
        return response()->json([
            'status' => false,
            'msg'    => $validator->errors()->first(),
            'data'   => (object)[],
        ],404);
    }
    ###############################  END ERROR VALIDATE   #############################
    ###############################    START NOT FOUND    #############################
    public function errorNotFound(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => false,
            'msg'    => __('clinic.Not Found'),
            'data'   => (object)[],
        ],404);
    }
    ###############################    END NOT FOUND      #############################
    ###############################    START SUCCESS      #############################
    public function success($data): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => true,
            'msg'    => "success",
            'data'   => (object)$data,
        ],200);
    }
    ###############################    END SUCCESS        #############################
    ###############################    START UNEXPECTED   #############################
    public function errorUnExpected($ex): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => false,
            'msg'    => $ex->getMessage(),
            'data'   => (object)[]
        ],404);
    }
    ###############################    END UNEXPECTED     #############################
    ###############################    START ERROR        #############################
    public function error($message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => false,
            'msg'    => $message,
            'data'   => (object)[],
        ],404);
    }
    ###############################    END ERROR          #############################

}
