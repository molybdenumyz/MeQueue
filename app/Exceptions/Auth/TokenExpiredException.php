<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午9:57
 */

namespace App\Exceptions\Auth;


use App\Exceptions\BaseException;

class TokenExpiredException extends BaseException
{
    protected $code = 20005;
    protected $data = "Token Expired";
}