<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
final class Organization extends Model { protected $fillable=['name','project_limit']; protected function casts(): array{return ['project_limit'=>'integer'];} public function customers():HasMany{return $this->hasMany(Customer::class);} public function projects():HasMany{return $this->hasMany(Project::class);} public function tickets():HasMany{return $this->hasMany(Ticket::class);} }
