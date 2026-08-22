<?php
namespace App\Models; use Illuminate\Foundation\Auth\User as Authenticatable; use Illuminate\Database\Eloquent\Relations\HasMany;
final class User extends Authenticatable { protected $fillable=['name','email','password']; protected $hidden=['password','remember_token']; protected function casts():array{return ['password'=>'hashed'];} public function memberships():HasMany{return $this->hasMany(Membership::class);} }
