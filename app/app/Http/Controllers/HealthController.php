<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

final class HealthController extends Controller
{
    public function live(): JsonResponse
    {
        return response()->json([
            'status' => 'alive',
            'version' => config('app.version'),
        ]);
    }

    public function ready(): JsonResponse
    {
        try {
            DB::select('select 1');
            $key = 'health:readiness';
            Cache::put($key, 'ready', 10);
            if (Cache::get($key) !== 'ready') {
                throw new \RuntimeException('Cache round trip failed.');
            }
        } catch (Throwable $error) {
            report($error);

            return response()->json([
                'status' => 'not_ready',
                'version' => config('app.version'),
                'dependencies' => ['database' => 'unavailable', 'cache' => 'unknown'],
            ], 503);
        }

        return response()->json([
            'status' => 'ready',
            'version' => config('app.version'),
            'dependencies' => ['database' => 'available', 'cache' => 'available'],
        ]);
    }
}
