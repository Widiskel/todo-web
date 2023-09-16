<?php

namespace App\Http;
use App\Models\FormPengajuanKredit;
use DateTime;

class Helper
{
    public static function success($message = 'Success',$data = [], $statusCode = 200)
    {
        return response()->json([
            'rc' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error($message = 'Terjadi Kesalahan', $data = [], $statusCode = 422)
    {
        return response()->json([
            'rc' => 'failed',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

   
}
