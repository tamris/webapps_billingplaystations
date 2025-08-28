<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playstation extends Model
{
    protected $fillable = ['code','name','status','price_per_hour'];

    // helper badge
    public function statusBadge(): string {
        return match($this->status){
            'available'   => 'success',
            'in_use'      => 'warning',
            'maintenance' => 'secondary',
        };
    }
    public function sessions(){ return $this->hasMany(PlaySession::class); }
}
