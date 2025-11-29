<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\AttendanceFactory::new();
    }

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(\App\Models\BreakTime::class);
    }

    public function modificationRequests()
    {
        return $this->hasMany(AttendanceModificationRequest::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'off_duty' => '勤務外',
            'working' => '出勤中',
            'on_break' => '休憩中',
            'clocked_out' => '退勤済',
            default => '不明',
        };
    }
}

