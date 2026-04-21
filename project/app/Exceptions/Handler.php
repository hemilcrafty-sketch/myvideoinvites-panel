<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Ensure validation failures return JSON for AJAX / JSON clients (e.g. offer package modal).
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException && ($request->expectsJson() || $request->ajax())) {
            return response()->json([
                'message' => $e->getMessage() ?: __('The given data was invalid.'),
                'errors'  => $e->errors(),
            ], $e->status);
        }

        return parent::render($request, $e);
    }
}
