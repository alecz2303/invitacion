<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'tenant_id','event_id','guest_name','seats','status','confirmed_at'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function event(){ return $this->belongsTo(Event::class); }
    public function rsvps(){ return $this->hasMany(Rsvp::class); }
}
