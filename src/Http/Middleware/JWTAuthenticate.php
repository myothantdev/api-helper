<?php

namespace Tech\APIHelper\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tech\APIHelper\Exceptions\UnauthorizedException;

/**
 * Class JWTAuthenticate
 * @package Tech\APIHelper\Http\Middleware
 */
class JWTAuthenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->authenticate($request);
        } catch (\Exception $exception) {
            throw new UnauthorizedException($exception->getMessage());
        }

        return $next($request);
    }
}
