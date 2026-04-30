<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceUtf8Response
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));

        $textualTypes = [
            'text/html',
            'text/plain',
            'text/css',
            'application/json',
            'application/javascript',
            'application/xml',
            'text/xml',
        ];

        if ($contentType === '') {
            $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        } else {
            foreach ($textualTypes as $type) {
                if (str_starts_with($contentType, $type) && ! str_contains($contentType, 'charset=')) {
                    $response->headers->set('Content-Type', $type.'; charset=UTF-8');
                    break;
                }
            }
        }

        return $response;
    }
}
