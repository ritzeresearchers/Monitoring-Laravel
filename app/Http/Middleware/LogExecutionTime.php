<?php

namespace App\Http\Middleware;

use \Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;

class LogExecutionTime
{
    public function handle($request, Closure $next)
    {
        // Start calculating execution time
        $start_time = microtime(true);

        // Process the request
        $response = $next($request);

        // Calculate the execution time
        $end_time = microtime(true);

        $execution_time = $end_time - $start_time;
        $request_name = $request->path();
        $exploded_path = explode('/',$request_name);

        // Store the information to the logs
        $category = match($exploded_path[0]){
            'admin' => 'admin',
            default => 'platform'
        };

        Log::channel('json')->info("execution_time",[
            'path' => $request_name,
            'category' => $category,
            'start_time' => Carbon::createFromFormat('U.u',$start_time,env('APP_TIMEZONE'))->format('Y-m-d H:i:s.u'),
            'end_time' => Carbon::createFromFormat('U.u',$end_time,env('APP_TIMEZONE'))->format('Y-m-d H:i:s.u'),
            'value' => $execution_time,
        ]);
        return $response;
        // Force dying to avoid double execution logging
        die;
    }
}