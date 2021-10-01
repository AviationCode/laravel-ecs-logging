<?php

namespace AviationCode\EcsLogging\Http\Middleware;

use AviationCode\EcsLogging\Tracing\Correlate;
use Closure;
use Illuminate\Http\Response;

class CorrelationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($correlationId = $request->header(Correlate::headerName())) {
            Correlate::setGenerator(fn () => $correlationId);
        }

        /** @var Response $response */
        $response = $next($request);

        $response->header(Correlate::headerName(), Correlate::id());

        return $response;
    }
}
