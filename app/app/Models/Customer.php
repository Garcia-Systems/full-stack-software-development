<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
final class Customer extends Model { protected $fillable=['organization_id','name','email','is_active']; protected function casts():array{return ['is_active'=>'boolean'];} public function organization():BelongsTo{return $this->belongsTo(Organization::class);} public function projects():HasMany{return $this->hasMany(Project::class);} public function tickets():HasMany{return $this->hasMany(Ticket::class);} }
