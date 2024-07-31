<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @return null|User
     */
    public function user(): ?User
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        return $currentUser;
    }

    /**
     * Respond with error.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondError(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'message'     => $message,
            'status_code' => Response::HTTP_BAD_REQUEST,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param $file
     * @param $directory
     * @return string|null
     */
    protected function storeFileToS3($file, $directory): ?string
    {
        if (!empty($file)) {
            $filePath = $file->store($directory, 's3');
            return config('config.assetsBaseUrl') . "{$filePath}";
        }

        return null;
    }
}
