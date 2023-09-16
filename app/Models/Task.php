<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = [];

    public function setFromAttribute($value)
    {
        $this->attributes['from'] = Carbon::parse($value);
    }

    public function setToAttribute($value)
    {
        $this->attributes['to'] = Carbon::parse($value);
    }

    public function dailyTasks()
    {
        return $this->hasMany(DailyTask::class);
    }
}
