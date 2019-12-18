<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/8/19
 * Time: 9:43 PM
 */

namespace Tech\APIHelper\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Tech\APIHelper\Exceptions\ValidationException;

class CommonService
{
    /**
     * @param array $data
     * @return array
     */
    public function arrayStringSort(array $data)
    {
        $keys = array_map("strlen", array_keys($data));
        array_multisort($keys, SORT_ASC, $data);
        return $data;
    }

    /**
     * @param $allow
     * @param $params
     * @return array
     */
    public function paramsFilter($allow, $params)
    {
        $params = array_filter($params->only($allow, function ($value) {
            return !empty($value);
        }));

        return $params;
    }

    /**
     * @param $id
     * @throws ValidationException
     */
    public function id($id)
    {
        $validator = Validator::make(["id" => $id], [
            "id" => "required|integer"
        ]);

        if ($validator->fails()) :
            throw new ValidationException($validator->errors());
        endif;
    }
}