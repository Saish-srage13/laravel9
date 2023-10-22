<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\ApiController;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $ex)
    {
        $apiController = new ApiController;

        if ($ex instanceof NotFoundHttpException) {
            return $apiController->respondNotFound('Not Found');
        } elseif ($ex instanceof \Dotenv\Exception\ValidationException && $ex->getResponse()) {
            $status = Response::HTTP_BAD_REQUEST;
            $ex = new \Dotenv\Exception\ValidationException('HTTP_BAD_REQUEST', $status, $ex);
        } elseif ($ex instanceof \Illuminate\Database\QueryException) {
            return $apiController->respondInternalServerError($ex->getMessage());
        }  elseif ($ex instanceof ValidationException) {
            return $apiController->respondValidationFailed($ex->validator->errors()->all(), $ex->getMessage());
        } elseif ($ex instanceof AuthenticationException) {
            return $apiController->respondUnauthorized();
        }elseif ($ex instanceof FatalError) {
            return $apiController->respondInternalServerError($ex->getMessage());
        } elseif ($ex) {
            if (method_exists($ex, 'render')) {
                return $ex->render();
            }
            return $apiController->respondInternalServerError($ex->getMessage());
            // return parent::render($request, $ex);
        }

        return $apiController->status($status)
            ->respond([
                'status' => false,
                'data' => ["errors" => [$ex->getMessage()]],
                'message' => $ex->getMessage(),
            ]);
    }
}
