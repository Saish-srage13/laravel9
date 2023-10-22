<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $statusCode = Response::HTTP_OK;

    public function status($statusCode = Response::HTTP_OK)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respond($response)
    {
        return response()->json($response, $this->statusCode, [], JSON_UNESCAPED_SLASHES);
    }

    public function respondOk($data = [], $message = 'Data found.')
    {
        return $this
            ->status(Response::HTTP_OK)
            ->respond([
                'status' => true,
                'message' => $message,
                'data' => $data,
            ]);
    }

    public function respondNotFound($message = "Data not found.")
    {
        return $this
            ->status(Response::HTTP_NOT_FOUND)
            ->respond([
                'status' => false,
                'message' => $message,
                'data' => [],
            ]);
    }

    public function respondValidationFailed($errors = [], $message = "Invalid data.")
    {
        return $this
            ->status(Response::HTTP_BAD_REQUEST)
            ->respond([
                'status' => false,
                'message' => $message,
                'data' => [
                    'errors' => $errors
                ]
            ]);
    }

    public function respondBadRequest($message = "Bad request.")
    {
        return $this
            ->status(Response::HTTP_BAD_REQUEST)
            ->respond([
                'status' => false,
                'message' => $message
            ]);
    }

    public function respondInternalServerError($message = 'Something went wrong and we are on it.')
    {
        return $this
            ->status(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->respond([
                'status' => false,
                'message' => $message,
            ]);
    }

    public function respondWithPaginatedCollection($collection, $appends = [])
    {
        $response = array_merge([
            'listing' => $collection->getCollection(),
            'pagination' => [
                'current_count' => $collection->count(),
                'current_page' => $collection->currentPage(),
                'next_url' => $collection->nextPageUrl(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
                'total_pages' => ceil($collection->total() / $collection->perPage())
            ]
        ], $appends);

        return $this
            ->status(Response::HTTP_OK)
            ->respond($response);
    }
}
