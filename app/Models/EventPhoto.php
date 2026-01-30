<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    protected $fillable = ['tenant_id','event_id','path','sort'];

    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function event(){ return $this->belongsTo(Event::class); }
}
