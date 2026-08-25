<?php

namespace Ysfkaya\ShipLog\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\ShipLogManager;
use Ysfkaya\ShipLog\Support\Authorizer;
use Ysfkaya\ShipLog\Support\Settings;

class FeedController
{
    public function __invoke(Request $request, ShipLogManager $manager, Authorizer $authorizer): JsonResponse
    {
        abort_unless($authorizer->canView(), 403);

        $releases = $manager->releases();

        $perPage = max(1, min(100, (int) $request->integer('per_page', app(Settings::class)->perPage)));
        $cursor = max(0, $request->integer('cursor'));

        $page = $releases->slice($cursor, $perPage);
        $next = $cursor + $page->count();

        return response()->json([
            'releases' => $page
                ->map(fn (Release $release): array => $release->toArray())
                ->values()
                ->all(),
            'next' => $next < $releases->count() ? $next : null,
            'total' => $releases->count(),
        ]);
    }
}
