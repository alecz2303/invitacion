<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name','slug','is_active'];

    public function users()  { return $this->hasMany(User::class); }
    public function events() { return $this->hasMany(Event::class); }
    public function invites(){ return $this->hasMany(Invite::class); }
}
