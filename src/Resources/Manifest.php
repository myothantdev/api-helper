<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/14/19
 * Time: 2:11 PM
 */

namespace Tech\APIHelper\Resources;

use Tech\APIHelper\Exceptions\Exception;

class Manifest
{
    /**
     * @return Manifest
     */
    public static function get(string $function, string $key)
    {
        return (new self)->make($function, $key);
    }
    
    /**
     * @param string $function
     * @param string $key
     * @throws Exception
     */
    protected function make($function, $key)
    {
        if (method_exists($this, $function)) {
            $array = call_user_func([$this, $function]);
            if (!empty($array[$key])) :
                return $array[$key];
            endif;
        }

        return null;
    }

    /**
     * @return array
     */
    protected function message()
    {
        return [
            "TokenExpiredException" => "The requested token is expired",
            "TokenInvalidException" => "The requested token is invalid",
            "NotFoundHttpException" => "The requested route does not exist.",
            "NotFoundException" => "The requested resource ID does not exist.",
            "UnauthorizedException" => "The requested parameter is unauthorized",
            "DeleteResourceNotFound" => "The deleted resource ID does not exits.",
            "UpdateResourceNotFound" => "The updated resource ID does not exist.",
            "FatalErrorException" => "Internal server error, Please try again later.",
            "MethodNotAllowedHttpException" => "The requested method is not supported for this route."
        ];
    }

    /**
     * @return array
     */
    protected function code()
    {
        return [
            "DatabaseException" => 502,
            "NotFoundException" => 404,
            "FatalErrorException" => 500,
            "ValidationException" => 400,
            "BadMethodCallException" => 400,
            "UpdateResourceNotFound" => 404,
            "DeleteResourceNotFound" => 404,
            "NotFoundHttpException" => 404,
            "TokenExpiredException" => 401,
            "TokenInvalidException" => 401,
            "UnauthorizedException" => 403,
            "MethodNotAllowedHttpException" => 405
        ];
    }
}