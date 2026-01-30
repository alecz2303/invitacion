<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $fillable = ['tenant_id','invite_id','response','user_agent','ip'];

    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function invite(){ return $this->belongsTo(Invite::class); }
}
