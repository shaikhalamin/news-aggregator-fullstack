<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // $this->reportable(function (Throwable $e) {
        //     //
        // });

        $this->renderable(function (Throwable $e, $request) {

            if ($e instanceof NotFoundHttpException) {
                $message = 'Resource not found!';

                return response()->json(['message' => $message, 'errors' => []], $e->getStatusCode());
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                $message = $e->getMessage();

                return response()->json(['message' => $message, 'errors' => []], $e->getStatusCode());
            }

            if ($e instanceof QueryException) {
                $message = $e->getMessage();

                return response()->json(['message' => $message, 'errors' => []], 500);
            }

            if ($e instanceof ValidationException) {
                $message = $e->getMessage();
                $errors = $e->errors();

                return response()->json(['message' => $message, 'errors' => $errors], $e->status);
            }

            if ($e instanceof AuthenticationException) {
                $message = $e->getMessage();
                $errors = [];
                return response()->json(['message' => $message, 'errors' => $errors], 401);
            }

            if ($e instanceof HttpException) {
                $message = $e->getMessage() ? $e->getMessage() : 'Something went wrong';

                return response()->json(['message' => $message, 'errors' => []], $e->getStatusCode());
            }

            if ($e instanceof \Exception) {
                $message = $e->getMessage() ? $e->getMessage() : 'Something went wrong';

                return response()->json(['message' => $message, 'errors' => []], 500);
            }
        });
    }
}
