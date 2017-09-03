<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/30
 * Time: 下午3:20
 */

namespace App\Services;


use App\Common\Encrypt;
use App\Exceptions\Auth\PasswordWrongException;
use App\Exceptions\Auth\UserExistedException;
use App\Exceptions\Auth\UserNotExistException;
use App\Repository\Eloquent\UserRepository;
use App\Services\Contracts\UserServiceInterface;

class UserService implements UserServiceInterface
{
    private $userRepository;
    private $tokenService;

    public function __construct(UserRepository $userRepository, TokenService $tokenService)
    {
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
    }

    public function getRepository()
    {
        return $this->userRepository;
    }

    /**
     * 注册
     * @param array $userInfo 用户信息
     * @return int 新注册的用户的id
     */

    public function register(array $userInfo): int
    {
        // 在这里设置需要检测的字段

        $uniques = [
            'name', 'mobile', 'email'
        ];

        foreach ($uniques as $unique) {
            if ($this->userRepository->getBy($unique, $userInfo[$unique])->count() >= 1) {
                throw new UserExistedException($unique);
            }
        }

        if (!config('user.register_need_check')) {
            $userInfo['status'] = 1; // 直接设置成激活
        }

        $userInfo['password'] = Encrypt::encrypt($userInfo['password']); // 对密码加密

        return $this->userRepository->insertWithId($userInfo);
    }

    /**
     * 在开启了注册验证的条件下，用于激活注册的用户
     * @param int $userId
     * @return bool
     */
    public function active(int $userId): bool
    {
        if (!config('user.register_need_check')) {
            return true;
        }

        $user = $this->userRepository->get($userId, ['id', 'status']);

        if ($user == null) {
            throw new UserNotExistException();
        }

        if ($user->status == 1) {
            return true;
        } else if ($user->status == 0) {
            return $this->userRepository->update(['status' => 1], $userId) == 1;
        }

        return false;
    }

    /**
     * @param string $param 登录的方式，可选mobile,email，用于在数据库中指定字段
     * @param string $identifier 用户输入的值
     * @param string $password 密码
     * @param string $ip
     * @return string token值
     */

    public function loginBy(string $param, string $identifier, string $password, string $ip,int $client)
    {
        // 在这里修改需要获取的字段

        $user = $this->userRepository->getBy($param, $identifier, [
            'id', 'password','name','email','mobile'
        ])->first();

        if ($user == null) {
            throw new UserNotExistException();
        }

        // 检查密码

        if (!Encrypt::check($password, $user->password)) {
            throw new PasswordWrongException();
        }

        return [
            'user' => $user,
            'token' => $this->tokenService->makeToken($user->id, $ip,$client)
        ];
    }

    public function login(int $userId, string $ip,int $client): string
    {
        $user = $this->userRepository->get($userId, [
            'id', 'password','name','email','mobile'
        ]);
        if (!$this->isUserExist(['id' => $userId])) {
            throw new UserNotExistException();
        }

        return [
            'user' => $user,
            'token' => $this->tokenService->makeToken($userId, $ip,$client)
        ];
    }

    public function logout(int $userId,int $client)
    {
        $this->tokenService->destoryToken($userId,$client);
    }

    public function isUserExist(array $condition): bool
    {
        return $this->userRepository->getWhereCount($condition) == 1;
    }
}