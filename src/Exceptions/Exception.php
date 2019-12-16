<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/9/19
 * Time: 3:31 PM
 */

namespace Tech\APIHelper\Exceptions;

use Throwable;
use Exception as BaseException;
use Tech\APIHelper\Resources\Manifest;
use Illuminate\Support\Facades\Config;

/**
 * Class Exception
 * @package Tech\APIHelper\Exceptions
 */
class Exception extends BaseException
{
    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $key = $this->getKey();

        if (empty($message)) :
            $message = Manifest::get("message", $key);
        endif;

        if (empty($code)) :
            $code = Manifest::get("code", $key);
        endif;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    protected function getKey()
    {
        $parts = explode("\\", get_class($this));
        return end($parts);
    }
}