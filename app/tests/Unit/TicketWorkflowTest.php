<?php
namespace Tests\Unit;
use App\Exceptions\BusinessRuleViolation; use App\Models\Ticket; use App\Services\TicketWorkflow; use PHPUnit\Framework\TestCase;
final class TicketWorkflowTest extends TestCase { public function test_closed_is_terminal_without_touching_persistence():void{$ticket=new Ticket(['status'=>'closed']);$this->expectException(BusinessRuleViolation::class);(new TicketWorkflow)->transition($ticket,'open');} }
