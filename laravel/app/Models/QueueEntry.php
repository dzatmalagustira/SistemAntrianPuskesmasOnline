<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueEntry extends Model
{
    use HasFactory;

    protected $table = 'queues';

    protected $fillable = [
        'booking_id',
        'doctor_id',
        'user_id',
        'date',
        'number',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
