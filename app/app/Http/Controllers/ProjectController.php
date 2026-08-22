<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreProjectRequest; use App\Models\Customer; use App\Services\CreateProject; use Illuminate\Http\JsonResponse;
final class ProjectController extends Controller { public function store(StoreProjectRequest $request,Customer $customer,CreateProject $operation):JsonResponse{$project=$operation->handle($customer,$request->validated('name'));return response()->json(['project'=>$project],201,['Location'=>"/api/projects/{$project->id}"]);} }
