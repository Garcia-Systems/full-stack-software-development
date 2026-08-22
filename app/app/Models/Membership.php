<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
final class Membership extends Model { protected $fillable=['user_id','organization_id','role','status']; }
