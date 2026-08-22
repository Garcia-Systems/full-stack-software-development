<?php
namespace App\Http\Controllers; use App\Models\Membership; use App\Services\DashboardSummary; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request;
final class DashboardController { public function __invoke(Request $r,DashboardSummary $summary):JsonResponse{$org=$r->integer('organization_id');abort_unless(Membership::where(['user_id'=>$r->user()->id,'organization_id'=>$org,'status'=>'active'])->exists(),403);return response()->json(['data'=>$summary->get($org),'requestId'=>$r->attributes->get('request_id')]);}}
