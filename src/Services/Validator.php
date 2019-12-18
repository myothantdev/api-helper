<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/16/19
 * Time: 8:06 PM
 */

namespace Tech\APIHelper\Services;

use Tech\APIHelper\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator as BaseValidator;

/**
 * Class Validator
 * @package Tech\APIHelper\Services
 */
class Validator
{
    /**
     * @param array $data
     * @param array $rules
     * @return bool
     * @throws ValidationException
     */
    public function validate(array $data, array $rules)
    {
        $validator = BaseValidator::make($data, $rules);

        if ($validator->fails()) :
            throw new ValidationException($validator->errors());
        endif;

        return true;
    }
}