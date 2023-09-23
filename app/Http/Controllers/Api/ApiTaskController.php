<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Helper;
use Illuminate\Support\Facades\Validator;

class ApiTaskController extends Controller
{
    public function get_task(Request $request)
    {
        $task = Task::with('dailyTasks')
            ->withCount([
                'dailyTasks',
                'dailyTasks as completed_daily_tasks' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->where('user_id', auth()->user()->id)
            ->whereDate('to', '>', today())
            ->get();
        return Helper::success('Berhasil mendapatkan data task', $task);
    }
    public function get_all_task(Request $request)
    {
        $task = Task::with('dailyTasks')
            ->withCount([
                'dailyTasks',
                'dailyTasks as completed_daily_tasks' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->where('user_id', auth()->user()->id)
            ->get();
        return Helper::success('Berhasil mendapatkan data task', $task);
    }

    public function delete_task(Task $task)
    {
        $task->delete();
        return Helper::success('Task berhasil dihapus', []);
    }

    public function add_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:' . Task::class,
            'description' => 'required|string',
            'from' => 'required|date_format:Y-m-d',
            'to' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return Helper::error($validator->errors()->first());
        } else {
            $validator = Validator::make($request->all(), [
                'to' => 'required|date_format:Y-m-d|after:' . $request->from,
            ]);

            if ($validator->fails()) {
                return Helper::error($validator->errors()->first());
            }
        }

        try {
            DB::beginTransaction();

            $task = Task::create([
                'name' => $request->name,
                'description' => $request->description,
                'from' => $request->from,
                'to' => $request->to,
                'user_id' => auth()->user()->id,
            ]);

            $numberOfDays = $task->from->diffInDays($task->to) + 1;

            for ($i = 0; $i < $numberOfDays; $i++) {
                DailyTask::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'date' => $task->from,
                    'task_id' => $task->id,
                    'user_id' => auth()->user()->id,
                ]);
                $task->from->addDay();
            }

            DB::commit();
            return Helper::success('Task Berhasil Ditambahkan', [], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Helper::error('Terjadi Kesalahan', [], 500);
        }
    }
    public function edit_task(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tasks,name,' . $task->id,
            'description' => 'required|string',
            'to' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return Helper::error($validator->errors()->first());
        } else {
            $validator = Validator::make($request->all(), [
                'to' => 'required|date_format:Y-m-d|after:' . $request->from,
            ]);

            if ($validator->fails()) {
                return Helper::error($validator->errors()->first());
            }
        }

        try {
            DB::beginTransaction();

            $task->update([
                'name' => $request->name,
                'description' => $request->description,
                'to' => $request->to,
            ]);
            $task->from = Carbon::createFromFormat('Y-m-d', $task->from);
            $task->save();

            $numberOfDays = $task->from->diffInDays($task->to);

            DailyTask::where(function ($query) use ($task) {
                $query->where('date', '<', $task->from->toDateString())->orwhere('date', '>', $task->to->toDateString());
            })
                ->where('task_id', $task->id)
                ->where('user_id', auth()->user()->id)
                ->delete();

            for ($i = 0; $i < $numberOfDays + 1; $i++) {
                $dailyTask = DailyTask::firstOrNew([
                    'task_id' => $task->id,
                    'date' => $task->from->toDateString(),
                    'user_id' => auth()->user()->id,
                ]);
                $dailyTask->name = $request->name;
                $dailyTask->description = $request->description;
                $dailyTask->date = $task->from;
                $dailyTask->user_id = auth()->user()->id;
                $dailyTask->save();
                $task->from->addDay();
            }

            $data = $task
                ->with('dailyTasks')
                ->withCount([
                    'dailyTasks',
                    'dailyTasks as completed_daily_tasks' => function ($query) {
                        $query->where('status', 1);
                    },
                ])
                ->first();

            DB::commit();
            return Helper::success('Task Berhasil Diperbarui', $data, 200);
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return Helper::error('Terjadi Kesalahan', [], 500);
        }
    }
}
