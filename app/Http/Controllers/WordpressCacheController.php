<?php

namespace App\Http\Controllers;

use App\Services\Wordpress\WordpressCacheInvalidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WordpressCacheController extends Controller
{
    public function __invoke(Request $request, WordpressCacheInvalidator $cacheInvalidator): JsonResponse
    {
        $configuredToken = (string) config('wordpress.invalidation.token', '');
        $requestToken = (string) $request->header('X-Wordpress-Cache-Token', '');

        abort_if($configuredToken === '', Response::HTTP_SERVICE_UNAVAILABLE, 'WordPress cache invalidation is not configured.');
        abort_unless(hash_equals($configuredToken, $requestToken), Response::HTTP_UNAUTHORIZED);

        $payload = $request->validate([
            'post_id' => ['nullable', 'integer'],
            'post_type' => ['nullable', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:200'],
        ]);

        $version = $cacheInvalidator->invalidatePostCaches(
            slug: $payload['slug'] ?? null,
            postType: $payload['post_type'] ?? null,
        );

        return response()->json([
            'status' => 'ok',
            'version' => $version,
        ]);
    }
}