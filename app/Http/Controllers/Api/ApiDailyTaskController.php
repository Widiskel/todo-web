<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use App\Http\Helper;

class ApiDailyTaskController extends Controller
{
    public function done_task(Request $request,DailyTask $task)
    {
        $task->status = 1;
        return Helper::success('Task telah diselesaikan',$task);
    }
    
}
