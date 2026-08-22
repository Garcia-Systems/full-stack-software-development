<?php
namespace App\Services;
use App\Exceptions\BusinessRuleViolation; use App\Models\Ticket;
final class TicketWorkflow { private const ALLOWED=['open'=>['in_progress','closed'],'in_progress'=>['open','closed'],'closed'=>[]]; public function transition(Ticket $ticket,string $next):Ticket { if(!in_array($next,self::ALLOWED[$ticket->status]??[],true)) throw new BusinessRuleViolation("Ticket cannot move from {$ticket->status} to {$next}."); $ticket->status=$next; $ticket->version++; $ticket->save(); return $ticket->refresh(); } }
