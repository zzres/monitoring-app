<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    public $timestamps = false;

    protected $fillable = ['probe_id', 'is_up', 'response_time_ms', 'status_code', 'error_message', 'checked_at'];

    protected $casts = [
        'is_up' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function probe()
    {
        return $this->belongsTo(Probe::class);
    }
}
