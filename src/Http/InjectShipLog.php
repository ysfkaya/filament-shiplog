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
        if ($request->header('X-Inertia')) {
            return false;
        }

        return ! $request->ajax()
            && ! $request->wantsJson()
            && $response->isSuccessful()
            && $this->isHtml($response);
    }

    /**
     * Responses built from a view often have no Content-Type yet, so only an
     * explicitly non-HTML type rules a response out.
     */
    protected function isHtml(Response $response): bool
    {
        $type = $response->headers->get('Content-Type');

        return blank($type) || str_contains($type, 'text/html');
    }
}
