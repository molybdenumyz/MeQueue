<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/29
 * Time: 下午11:48
 */

namespace App\Services\Contracts;


interface TokenServiceInterface
{
    function hasToken(int $userId,int $client):bool;

    function makeToken(int $userId,string $ip,int $client):string;
    //内部是调用create和update

    function isTokenExpire(string $tokenStr):bool;

    function destoryToken(int $userId,int $client);

    function getUserIdByToken(string $tokenStr):int;
}