<?php

use App\Http\Controllers\REST\Errors;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => AuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (
                $exception instanceof MethodNotAllowedException
                || $exception instanceof MethodNotAllowedHttpException
                || $exception instanceof NotFoundHttpException
                || $exception instanceof NotFoundResourceException
            ) {
                // Custom 404 for api
                if ($request->is(['api', 'api/*'])) {
                    return (new Errors)
                        ->setInternal(false)
                        ->setMessage(404, "Endpoint or HTTP method not available")
                        ->sendError();
                }
            }
        });
    })->create();
