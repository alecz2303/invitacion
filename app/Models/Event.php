<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventPhoto;

class Event extends Model
{
    protected $fillable = [
        'tenant_id','type','title','celebrant_name','starts_at','venue',
        'dress_code','message','theme','maps_url','ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'theme' => 'array',
    ];

    public function photos()
    {
        return $this->hasMany(EventPhoto::class)->orderBy('sort')->latest();
    }

    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function invites(){ return $this->hasMany(Invite::class); }
}
