<?php
use App\Exceptions\BusinessRuleViolation;
use App\Http\Middleware\RequestId;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Session\Middleware\StartSession; use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse; use Illuminate\Cookie\Middleware\EncryptCookies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withCommands()
    ->withMiddleware(function (Middleware $middleware): void { $middleware->append(RequestId::class); $middleware->appendToGroup('api',[EncryptCookies::class,AddQueuedCookiesToResponse::class,StartSession::class,\App\Http\Middleware\ApiCors::class,\App\Http\Middleware\LabControls::class]); $middleware->alias(['api.auth'=>\App\Http\Middleware\ApiAuthentication::class]); })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (BusinessRuleViolation $e, Request $request) {
            return response()->json(['type' => 'business_rule', 'message' => $e->getMessage(), 'request_id' => $request->attributes->get('request_id')], 409);
        });
        $exceptions->render(function (QueryException $e, Request $request) {
            if (!$request->is('api/*')) return null;
            Log::error('persistence.failed', ['request_id' => $request->attributes->get('request_id'), 'exception' => get_class($e), 'message' => $e->getMessage()]);
            return response()->json(['type' => 'persistence_error', 'message' => 'Persistent state is temporarily unavailable.', 'request_id' => $request->attributes->get('request_id')], 503);
        });
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*') || $e instanceof HttpExceptionInterface || $e instanceof QueryException) return null;
            Log::error('request.unexpected', ['request_id' => $request->attributes->get('request_id'), 'exception' => get_class($e), 'message' => $e->getMessage()]);
            return response()->json(['type' => 'unexpected_error', 'message' => 'The server could not complete the request.', 'request_id' => $request->attributes->get('request_id')], 500);
        });
    })->create();
