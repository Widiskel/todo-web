<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DailyTask extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $fillable = [];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value);
    }

}
