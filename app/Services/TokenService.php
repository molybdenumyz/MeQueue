<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/29
 * Time: 下午11:47
 */

namespace App\Services;


use App\Common\Utils;
use App\Exceptions\Auth\UserNotExistException;
use App\Repository\Eloquent\TokenRepository;
use App\Repository\Eloquent\UserRepository;
use App\Services\Contracts\TokenServiceInterface;

class TokenService implements TokenServiceInterface
{
    private $userRepo;
    private $tokenRepo;

    private static $EXPIRE_TIME = 10800000; // 3小时

    public function __construct(UserRepository $userRepository, TokenRepository $tokenRepository)
    {
        $this->userRepo = $userRepository;
        $this->tokenRepo = $tokenRepository;
    }

    public function hasToken(int $userId, int $client): bool
    {
        $user = $this->userRepo->get($userId, ['id'])->first();

        if ($user == null) {
            throw new UserNotExistException();
        }

        $token = $this->tokenRepo->getByMult([
            'user_id' => $userId,
            'client' => $client
        ])->first();

        if ($token == null) {
            return false;
        } else {
            return true;
        }
    }

    private function createToken(int $userId, string $ip, int $client): string
    {
        $tokenStr = md5(uniqid());
        $time = Utils::createTimeStamp();
        $data = [
            'user_id' => $userId,
            'token' => $tokenStr,
            'client' => $client,
            'created_at' => $time,
            'updated_at' => $time,
            'expires_at' => $time + self::$EXPIRE_TIME,
            'ip' => $ip
        ];
        $this->tokenRepo->insert($data);
        return $tokenStr;
    }

    private function updateToken(int $userId, string $ip, int $client): string
    {
        $time = Utils::createTimeStamp();
        $tokenStr = md5(uniqid());
        $data = [
            'token' => $tokenStr,
            'updated_at' => $time,
            'expires_at' => $time + self::$EXPIRE_TIME,
            'ip' => $ip
        ];

        $this->tokenRepo->updateWhere([
            'user_id' => $userId,
            'client' => $client
        ], $data);

        return $tokenStr;
    }


    public function makeToken(int $userId, string $ip, int $client): string
    {
        $user = $this->userRepo->get($userId)->first();

        if ($user == null)
            throw new UserNotExistException();

        $token = $this->tokenRepo->getByMult([
            'user_id' => $userId,
            'client' => $client
        ])->first();

        if ($token == null) {
            return $this->createToken($userId, $ip, $client);
        } else {
            return $this->updateToken($userId, $ip, $client);
        }
    }

    public function isTokenExpire(string $tokenStr): bool
    {
        $time = Utils::createTimeStamp();

        $token = $this->tokenRepo->getBy('token', $tokenStr)->first();
        if ($token == null)
            throw new NeedLoginException();
        if ($token->expires_at < $time)
            return true;
        else
            return false;
    }

    public function destoryToken(int $userId, int $client)
    {
        $token = $this->tokenRepo->getByMult([
            'user_id' => $userId,
            'client' => $client
        ])->first();

        if ($token != null) {
            return $this->tokenRepo->updateWhere([
                'user_id' => $userId,
                'client' => $client
            ], ['token' => null]);
        }
        return -1;
    }

    public function getUserIdByToken(string $tokenStr): int
    {
        $token = $this->tokenRepo->getBy('token', $tokenStr)->first();

        $time = Utils::createTimeStamp();

        if ($token == null || $token->expireAt < $time) return -1;
        else return $token->user_id;
    }
}