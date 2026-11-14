<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'v1/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'The requested resource was not found.',
                'errors' => ['resource' => ['The requested record does not exist or is unavailable.']],
            ], 404);
        });
        $exceptions->render(function (InvalidSignatureException $exception, Request $request) {
            if ($request->is('app/deposit/upi/*')) {
                Log::warning('UPI app payment route rejected an invalid signed URL.', [
                    'route' => $request->route()?->getName(),
                    'path' => $request->path(),
                    'deposit_id' => $request->route('deposit'),
                    'query_parameter_names' => array_keys($request->query()),
                    'expires' => $request->query('expires'),
                    'signature_present' => $request->filled('signature'),
                    'request_host' => $request->getHost(),
                    'configured_host' => parse_url((string) config('app.url'), PHP_URL_HOST),
                ]);
            }

            return null;
        });
    })->create();
