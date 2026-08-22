<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
final class IdempotencyRequest extends Model { protected $guarded=[]; protected function casts():array{return ['response'=>'array'];} }
