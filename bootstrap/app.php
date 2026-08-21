<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Client API harus selalu dapat JSON, termasuk waktu error.
        // Default Laravel balikin halaman HTML, dan itu bikin Axios susah baca pesannya.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $pesan = $e->getPrevious() instanceof ModelNotFoundException
                    ? 'Data yang dicari tidak ditemukan.'
                    : 'Endpoint tidak ditemukan.';

                return response()->json(['message' => $pesan], 404);
            }
        });
    })->create();
