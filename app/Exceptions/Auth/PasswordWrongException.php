<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午9:52
 */

namespace App\Exceptions\Auth;


use App\Exceptions\BaseException;

class PasswordWrongException extends BaseException
{
    protected $code = 20002;
    protected $data = "Password Wrong";
}