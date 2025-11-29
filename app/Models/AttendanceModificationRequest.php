<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceModificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_clock_in',
        'requested_clock_out',
        'requested_remarks',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'requested_clock_in' => 'datetime',
        'requested_clock_out' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function breakModifications()
    {
        return $this->hasMany(BreakModification::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => '承認待ち',
            'approved' => '承認済み',
            'rejected' => '却下',
            default => '不明',
        };
    }
}

