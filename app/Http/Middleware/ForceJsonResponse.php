<?php

namespace App\Http\Middleware;

use App\Http\Helper;
use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$response instanceof JsonResponse) {
            if($response->getStatusCode() != 200){
                $response = Helper::error($response->original['message'],[],$response->getStatusCode());
            }
            $response = response()->json($response->original);
        }

        return $response;
    }
}
