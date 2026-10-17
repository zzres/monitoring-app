<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Probe extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'target', 'check_interval_minutes', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function latestCheck()
    {
        return $this->hasOne(Check::class)->latestOfMany('checked_at');
    }

     public function isDue(): bool
    {
        if (! $this->is_active) {
            return true;
        }

        $lastCheck = $this->latestCheck;

        if ($lastCheck === null) {
            return true;
        }

        return $lastCheck->checked_at
            ->copy()
            ->addMinutes($this->check_interval_minutes)
            ->subSeconds(30)
            ->lte(now());
    }
}
