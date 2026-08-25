<?php

namespace Ysfkaya\ShipLog\Http;

use Illuminate\Http\JsonResponse;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\ShipLogManager;
use Ysfkaya\ShipLog\Support\Authorizer;

class FeedController
{
    public function __invoke(ShipLogManager $manager, Authorizer $authorizer): JsonResponse
    {
        abort_unless($authorizer->canView(), 403);

        return response()->json([
            'releases' => $manager->releases()
                ->map(fn (Release $release): array => $release->toArray())
                ->all(),
        ]);
    }
}
