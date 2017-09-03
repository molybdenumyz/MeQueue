<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Common\ValidationHelper;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email|max:100',
            'mobile' => 'required|mobile|max:45',
            'password' => 'required|min:6|max:20'
        ];

        ValidationHelper::validateCheck($request->all(), $rules);

        $userInfo = ValidationHelper::getInputData($request, $rules);

        // todo 添加相关激活逻辑

        $userId = $this->userService->register($userInfo);

        return response()->json([
            'code' => 0,
            'data' => [
                'user_id' => $userId
            ]
        ]);
    }

    public function login(Request $request)
    {
        $rules = [
            'identifier' => 'required|string',
            'password' => 'required|min:6|max:20',
            'client' => 'required|min:1|max:2' // 登录设备标识符
        ];

        ValidationHelper::validateCheck($request->all(), $rules);

        // 在此定制登录方式

        $identifier = $request->identifier;

        if (Utils::isEmail($identifier)) {
            $loginMethod = 'email';
        } else if (Utils::isMobile($identifier)) {
            $loginMethod = 'mobile';
        } else {
            $loginMethod = 'name';
        }

        $data = $this->userService
            ->loginBy($loginMethod, $identifier, $request->password, $request->ip(),$request->client);

        // 在下面定制要取出的字段

        return response()->json([
            'code' => 0,
            'data' => $data
        ]);
    }

    public function logout()
    {

    }
}
