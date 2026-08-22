<?php
namespace App\Http\Controllers;
use App\Models\Customer; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request;
final class CustomerController extends Controller { public function index(Request $request):JsonResponse{return response()->json(['customers'=>Customer::where('organization_id',$request->integer('organization_id',1))->withCount(['projects','tickets'])->orderBy('id')->get()]);} }
