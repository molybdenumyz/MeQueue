<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/28
 * Time: 下午9:55
 */

namespace App\Exceptions\Auth;


use App\Exceptions\BaseException;

class NeedLoginException extends BaseException
{
    protected $code = 20004;
    protected $data = "Need Login";
}