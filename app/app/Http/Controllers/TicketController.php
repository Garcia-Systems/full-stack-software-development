<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreTicketRequest; use App\Http\Requests\UpdateTicketRequest; use App\Models\Ticket; use App\Services\TicketWorkflow; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request;
final class TicketController extends Controller {
 public function index(Request $request):JsonResponse { $organization=$request->integer('organization_id',1); return response()->json(['tickets'=>Ticket::query()->where('organization_id',$organization)->with(['customer:id,name','project:id,name'])->orderBy('id')->get()]); }
 public function store(StoreTicketRequest $request):JsonResponse { $ticket=Ticket::create($request->validated()); return response()->json(['ticket'=>$ticket->load('customer','project')],201,['Location'=>"/api/tickets/{$ticket->id}"]); }
 public function show(Ticket $ticket):JsonResponse{return response()->json(['ticket'=>$ticket->load('customer','project')]);}
 public function update(UpdateTicketRequest $request,Ticket $ticket):JsonResponse{$ticket->update($request->validated());return response()->json(['ticket'=>$ticket->refresh()]);}
 public function destroy(Ticket $ticket):JsonResponse{$ticket->delete();return response()->json([],204);}
 public function transition(Request $request,Ticket $ticket,TicketWorkflow $workflow):JsonResponse{$data=$request->validate(['status'=>['required','in:open,in_progress,closed']]);return response()->json(['ticket'=>$workflow->transition($ticket,$data['status'])]);}
 public function controlledFailure():never{abort_unless(app()->environment('local','testing'),404);throw new \RuntimeException('Controlled Chapter 17 exception');}
}
