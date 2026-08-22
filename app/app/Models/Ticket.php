<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class Ticket extends Model { protected $fillable=['organization_id','customer_id','project_id','subject','description','status','priority']; protected function casts():array{return ['version'=>'integer'];} public function organization():BelongsTo{return $this->belongsTo(Organization::class);} public function customer():BelongsTo{return $this->belongsTo(Customer::class);} public function project():BelongsTo{return $this->belongsTo(Project::class);} }
