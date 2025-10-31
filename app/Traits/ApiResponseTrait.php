<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    public function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public function error(string $message = 'Something went wrong', int $code = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Wrap a paginated or simple collection using a resource and append success message.
     */
    public function successCollection($collection, string $resourceClass, string $message = 'Success', int $code = 200)
    {
        /** @var JsonResource $resource */
        $resource = $resourceClass::collection($collection)
            ->additional([
                'success' => true,
                'message' => $message,
            ]);

        return $resource->response()->setStatusCode($code);
    }

    /**
     * Wrap a single resource with success structure
     */
    public function successResource($resourceModel, string $resourceClass, string $message = 'Success', int $code = 200)
    {
        return (new $resourceClass($resourceModel))
            ->additional([
                'success' => true,
                'message' => $message,
            ])
            ->response()
            ->setStatusCode($code);
    }

    public function successData(array $data, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

}
