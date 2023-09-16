<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use App\Http\Helper;

class ApiDailyTaskController extends Controller
{
    public function get_daily_task(Request $request)
    {
        $task = DailyTask::where(function($q) use ($request){
            if($request->date){
                return $q->where('date',$request->date);
            }else{
                return $q->where('date',now()->toDateString());
            }
        })->get();
        return Helper::success('Berhasil mendapatkan data daily task',$task);
    } 
    public function done_task(Request $request,DailyTask $task)
    {
        $task->status = 1;
        return Helper::success('Task telah diselesaikan',$task);
    }
    
}
