<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaySession extends Model
{
    protected $table = 'ps_sessions'; // penting!
    protected $fillable = [
        'playstation_id',
        'started_at',
        'ended_at',
        'duration_minutes',
        'total_price',
        'status'
    ];

    public function playstation()
    {
        return $this->belongsTo(Playstation::class);
    }
}
