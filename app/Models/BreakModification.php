<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakModification extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_modification_request_id',
        'requested_break_start',
        'requested_break_end',
    ];

    protected $casts = [
        'requested_break_start' => 'datetime',
        'requested_break_end' => 'datetime',
    ];

    public function modificationRequest()
    {
        return $this->belongsTo(AttendanceModificationRequest::class);
    }
}

