<?php

namespace Ysfkaya\ShipLog\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\HttpFoundation\Response;

/**
 * Drops the timeline into every HTML response, so a frontend built in React,
 * Vue or Svelte needs no Blade edits at all.
 */
class InjectShipLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $content = $response->getContent();

        if ($content === false || ! str_contains($content, '</body>')) {
            return $response;
        }

        $markup = Blade::render('<x-shiplog />');

        $response->setContent(substr_replace(
            $content,
            $markup . '</body>',
            strripos($content, '</body>'),
            strlen('</body>'),
        ));

        return $response;
    }

    protected function shouldInject(Request $request, Response $response): bool
    {
        return ! $request->ajax()
            && ! $request->wantsJson()
            && $response->isSuccessful()
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }
}
