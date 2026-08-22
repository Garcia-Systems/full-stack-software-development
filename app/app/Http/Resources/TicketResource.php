<?php
namespace App\Http\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
final class TicketResource extends JsonResource { public function toArray(Request $r):array{return ['id'=>$this->id,'customerId'=>$this->customer_id,'subject'=>$this->subject,'description'=>$this->description,'status'=>$this->status,'priority'=>$this->priority,'version'=>$this->version,'createdAt'=>$this->created_at?->toISOString(),'customer'=>$this->whenLoaded('customer',fn()=>['id'=>$this->customer->id,'name'=>$this->customer->name])];} }
