<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
final class Project extends Model { protected $fillable=['organization_id','customer_id','name','status']; public function organization():BelongsTo{return $this->belongsTo(Organization::class);} public function customer():BelongsTo{return $this->belongsTo(Customer::class);} public function tickets():HasMany{return $this->hasMany(Ticket::class);} }
