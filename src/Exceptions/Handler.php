<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/9/19
 * Time: 11:01 AM
 */

namespace Tech\APIHelper\Exceptions;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;
use Tech\APIHelper\Resources\Formatter;
use Tech\APIHelper\Exceptions\FatalErrorException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class ExceptionHandler
 * @package Tech\APIHelper\\Exceptions\Handler
 */
class Handler extends ExceptionHandler
{
    /**
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * The list of handle exceptions.
     *
     * @var array
     */
    protected $handels = [
        "BadMethodCallException",
        "Tech\APIHelper\Exceptions\DataBaseException",
        "Tech\APIHelper\Exceptions\NotFoundException",
        "Tech\APIHelper\Exceptions\ValidationException",
        "Tech\APIHelper\Exceptions\FatalErrorException",
        "Tech\APIHelper\Exceptions\TokenInvalidException",
        "Tech\APIHelper\Exceptions\NotFoundHttpException",
        "Tech\APIHelper\Exceptions\UnauthorizedException",
        "Tech\APIHelper\Exceptions\BadMethodCallException",
        "Tech\APIHelper\Exceptions\DeleteResourceNotFound",
        "Tech\APIHelper\Exceptions\UpdateResourceNotFound",
        "Tech\APIHelper\Exceptions\MethodNotAllowedHttpException",
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @throws DataBaseException
     * @throws \Tech\APIHelper\Exceptions\FatalErrorException
     * @throws \Tech\APIHelper\Exceptions\NotFoundHttpException
     * @throws \Tech\APIHelper\Exceptions\UnauthorizedException
     * @throws \Tech\APIHelper\Exceptions\MethodNotAllowedHttpException
     * @return \Illuminate\Http\Response|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if (Config::get("api.should_report")) :
            if ($exception instanceof NotFoundHttpException) :
                throw new \Tech\APIHelper\Exceptions\NotFoundHttpException();
            elseif ($exception instanceof MethodNotAllowedHttpException) :
                throw new \Tech\APIHelper\Exceptions\MethodNotAllowedHttpException();
            elseif ($exception instanceof QueryException) :
                throw new DataBaseException($exception->getMessage());
            elseif ($exception instanceof FatalThrowableError) :
                throw new FatalErrorException($exception->getMessage());
            elseif ($exception instanceof UnauthorizedException) :
                throw new \Tech\APIHelper\Exceptions\UnauthorizedException($exception->getMessage());
            endif;
            return $this->handel($request, $exception);
        endif;

        return parent::render($request, $exception);
    }

    /**
     * @param $request
     * @param $exception
     * @return mixed
     */
    private function handel($request, $exception)
    {
        $class = get_class($exception);
        if (in_array($class, $this->handels)) :
            $code = $exception->getCode();
            $message = $exception->getMessage();
            return response()->json($this->make($message, $code));
        endif;

        return parent::render($request, $exception);
    }

    /**
     * @param $exception
     * @param $code
     * @return array
     */
    private function make($exception, $code)
    {
        return Formatter::factory()->makeErrorException($exception, $code);
    }
}