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
     * @param Closure  $next
     * @param $guard
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            if (!empty($guard)) :
                auth()->shouldUse($guard);
            endif;
            $this->authenticate($request);
        } catch (\Exception $exception) {
            throw new UnauthorizedException($exception->getMessage());
        }

        return $next($request);
    }
}
