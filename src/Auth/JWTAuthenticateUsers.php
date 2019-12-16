<?php
/**
 * Created by PhpStorm.
 * User: myothantkyaw
 * Date: 12/11/19
 * Time: 10:08 PM
 */

namespace Tech\APIHelper\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tech\APIHelper\Resources\Formatter;
use Illuminate\Support\Facades\Validator;
use Tech\APIHelper\Exceptions\ValidationException;
use Tech\APIHelper\Exceptions\UnauthorizedException;

trait JWTAuthenticateUsers
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request->all());

        if ($token = $this->authenticate($request)) :
            return $this->response()->authResponse($token);
        endif;

        throw new UnauthorizedException();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) :
            throw new ValidationException($validator->errors());
        endif;

        $input = $request->all();
        $input["password"] = bcrypt($input["password"]);
        $user = $this->create($input);
        $token = $this->authenticate($request);

        return $this->response()->authResponse($token, $user);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        $format = $this->response()->make([
            "message" => "Logout Successfully"
        ]);

        return response()->json($format);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        return $this->response()->authResponse(auth()->refresh());
    }

    /**
     * @param $request
     * @return bool
     */
    public function authenticate($request)
    {
        return auth()->attempt($this->credential($request));
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function credential(Request $request)
    {
        return $request->only($this->getUser(), "password");
    }

    /**
     * @param $request
     * @throws ValidationException
     */
    protected function validateLogin($request)
    {
        $validator = Validator::make($request, [
            $this->getUser() => "required|string",
            "password" => "required|string"
        ]);

        if ($validator->fails())
            throw new ValidationException($validator->errors());
    }

    protected function currentUser()
    {
        if (!empty($user = auth()->user())) :
            $user = $this->response()->make($user->toArray());
            return response()->json($user);
        endif;
        throw new UnauthorizedException();
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        return Config::get("api.credential");
    }

    /**
     * @return Formatter
     */
    protected function response()
    {
        return Formatter::factory();
    }
}