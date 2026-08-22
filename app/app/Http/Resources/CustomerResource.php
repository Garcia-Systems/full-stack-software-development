<?php
namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
final class CustomerResource extends JsonResource { public function toArray(Request $r):array{return ['id'=>$this->id,'name'=>$this->name,'email'=>$this->email,'isActive'=>(bool)$this->is_active];} }
