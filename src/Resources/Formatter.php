<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/7/19
 * Time: 9:28 PM
 */

namespace Tech\APIHelper\Resources;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Tech\APIHelper\Services\CommonService;

/**
 * Class Formatter
 * @package Tech\APIHelper\Service
 */
class Formatter
{
    /**
     * This variable is used to set data count
     * 
     * @var
     */
    protected $count;

    /**
     * @var
     */
    protected $total;

    /**
     * @var
     */
    protected $method;

    /**
     * @var
     */
    protected $offset;

    /**
     * @var
     */
    protected $message;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var int
     */
    protected $limit = 30;

    /**
     * @var int
     */
    protected $success = 1;

    /**
     * @var int
     */
    protected $status = 200;

    /**
     * @var
     */
    public static $instance;

    /**
     * Singleton
     * @return Formatter
     */
    public static function factory()
    {
        if (!(self::$instance instanceof self)) :
            self::$instance = new self();
            $method = self::$instance->getMethod();
            if ($method === 'post') :
                self::$instance->status = 201;
            endif;
        endif;

        return self::$instance;
    }

    /**
     * @param array $data
     * @return array
     */
    public function make(array $data)
    {
        $this->count = count($data);
        $response = $this->defaultFormat();

        if (!empty($meta = $this->meta)) :
            array_push($response, $meta);
        endif;

        $response["data"] = $data;
        return $response;
    }

    /**
     * @param string $token
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function authResponse(string $token, $data = [])
    {
        $response = $this->defaultFormat();
        $response["token"] = $this->tokenInfo($token);
        if (!empty($data)) :
            $response["data"] = $data;
        endif;

        return response()->json($response);
    }

    /**
     * @param $token
     * @return array
     */
    public function tokenInfo($token)
    {
        return [
            "type" => "Bearer",
            "access_token" => $token,
            "expired_at" => auth()->factory()->getTTL() * 60
        ];
    }

    /**
     * @return string
     */
    protected function getMethod()
    {
        $method = Request::method();
        $this->method = $method;
        return strtolower($method);
    }

    /**
     * @param null $offset
     * @param null $limit
     * @return $this
     */
    public function setMetaData($offset = null, $limit = null)
    {
        if (!is_null($limit))
            $this->limit = (int)$limit;

        if (!is_null($offset))
            $this->offset = (int)$offset;

        $this->meta = [
            "limit" => $this->limit,
            "offset" => $this->offset,
            "count" => $this->count,
            "total" => $this->total
        ];

        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount(int $count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return array
     */
    protected function defaultFormat()
    {
        return [
            "success" => $this->success,
            "status" => $this->status,
            "method" => $this->method
        ];
    }

    /**
     * @param $exception
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeErrorException($exception, $code)
    {
        $this->success = 0;
        $this->status = 500;
        if (max((int)$code, 0)) :
            $this->status = $code;
        endif;

        $response = $this->defaultFormat();
        $error = json_decode($exception);

        if (is_string($exception) && json_last_error() == JSON_ERROR_NONE) :
            $response["errors"] = $error;
        else :
            $response["errors"] = [
                "message" => $exception
            ];
        endif;

        return $response;
    }

    protected function __clone(){}

    protected function __construct(){}
}